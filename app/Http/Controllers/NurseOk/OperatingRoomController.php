<?php

namespace App\Http\Controllers\NurseOk;

use App\Http\Controllers\Controller;
use App\Models\OperatingRoom;
use App\Models\SurgeryRequest;
use App\Models\SurgerySchedule;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OperatingRoomController extends Controller
{
    private const STATUSES = ['siap', 'dipakai', 'perawatan', 'nonaktif'];

    public function index(Request $request): View
    {
        $this->abortUnlessOkNurse();

        $query = $request->string('q')->trim()->toString();
        $status = $request->string('status')->trim()->toString();

        $rooms = OperatingRoom::query()
            ->when($query !== '', function ($builder) use ($query): void {
                $builder->where(function ($q) use ($query): void {
                    $q->where('room_code', 'like', "%{$query}%")
                        ->orWhere('room_name', 'like', "%{$query}%");
                });
            })
            ->when(in_array($status, self::STATUSES, true), fn ($builder) => $builder->where('status', $status))
            ->orderBy('room_name')
            ->paginate(10)
            ->withQueryString();

        return view('nurse-ok.rooms.index', [
            'rooms' => $rooms,
            'query' => $query,
            'activeStatus' => in_array($status, self::STATUSES, true) ? $status : '',
            'statuses' => self::STATUSES,
        ]);
    }

    public function create(): View
    {
        $this->abortUnlessOkNurse();

        return view('nurse-ok.rooms.create', [
            'statuses' => self::STATUSES,
        ]);
    }

    public function createPatientScheduling(): View
    {
        $this->abortUnlessOkNurse();

        $verifiedRequests = SurgeryRequest::query()
            ->with(['patient', 'requestedDoctor.user', 'okVerificationChecklist'])
            ->whereHas('okVerificationChecklist', function ($query): void {
                $query->where('operating_room_ready', true);
            })
            ->whereNotNull('requested_doctor_id')
            ->whereDoesntHave('surgerySchedules')
            ->orderByDesc('requested_date')
            ->get();

        $rooms = OperatingRoom::query()
            ->with('facilities')
            ->where('status', 'siap')
            ->where('capacity', '>', 0)
            ->orderBy('room_name')
            ->get();

        $requestOptions = $verifiedRequests->map(function (SurgeryRequest $request): array {
            return [
                'id' => $request->id,
                'patient_name' => $request->patient?->name ?? '-',
                'patient_mrn' => $request->patient?->medical_record_number ?? '-',
                'patient_gender' => $request->patient?->gender ?? '-',
                'patient_origin_room' => $request->patient?->origin_room ?? '-',
                'surgery_date' => optional($request->requested_date)->format('d M Y') ?? '-',
                'start_time' => $request->requested_start_time ? substr((string) $request->requested_start_time, 0, 5) : '-',
                'end_time' => $request->requested_end_time ? substr((string) $request->requested_end_time, 0, 5) : '-',
                'doctor_name' => $request->requestedDoctor?->user?->name ?? '-',
            ];
        })->values();

        $roomOptions = $rooms->map(function (OperatingRoom $room): array {
            return [
                'id' => $room->id,
                'room_code' => $room->room_code,
                'room_name' => $room->room_name,
                'status' => $room->status,
                'capacity' => $room->capacity,
                'description' => $room->description,
                'facilities' => $room->facilities
                    ->pluck('name')
                    ->filter()
                    ->values(),
            ];
        })->values();

        return view('nurse-ok.rooms.patient-scheduling.create', [
            'verifiedRequests' => $verifiedRequests,
            'rooms' => $rooms,
            'requestOptions' => $requestOptions,
            'roomOptions' => $roomOptions,
        ]);
    }

    public function storePatientScheduling(Request $request): RedirectResponse
    {
        $this->abortUnlessOkNurse();

        $validated = $request->validate([
            'surgery_request_id' => ['required', 'exists:surgery_requests,id'],
            'operating_room_id' => ['required', 'exists:operating_rooms,id'],
            'end_time' => ['required', 'date_format:H:i'],
        ]);

        $result = DB::transaction(function () use ($validated, $request): ?array {
            $selectedRequest = SurgeryRequest::query()
                ->with(['surgerySchedules', 'okVerificationChecklist'])
                ->lockForUpdate()
                ->findOrFail($validated['surgery_request_id']);

            if (! $selectedRequest->okVerificationChecklist || ! $selectedRequest->okVerificationChecklist->operating_room_ready) {
                return [
                    'field' => 'surgery_request_id',
                    'message' => 'Pasien belum lolos verifikasi Perawat OK untuk penjadwalan kamar.',
                ];
            }

            if ($selectedRequest->surgerySchedules->isNotEmpty()) {
                return [
                    'field' => 'surgery_request_id',
                    'message' => 'Pasien ini sudah memiliki jadwal operasi.',
                ];
            }

            if (! $selectedRequest->requested_doctor_id || ! $selectedRequest->requested_date || ! $selectedRequest->requested_start_time) {
                return [
                    'field' => 'surgery_request_id',
                    'message' => 'Data dokter, tanggal, atau jam mulai pada pengajuan belum lengkap untuk dibuatkan jadwal.',
                ];
            }

            if ($validated['end_time'] <= substr((string) $selectedRequest->requested_start_time, 0, 5)) {
                return [
                    'field' => 'end_time',
                    'message' => 'Waktu selesai harus lebih besar dari waktu mulai operasi.',
                ];
            }

            $room = OperatingRoom::query()
                ->lockForUpdate()
                ->findOrFail($validated['operating_room_id']);

            if ($room->capacity < 1) {
                return [
                    'field' => 'operating_room_id',
                    'message' => 'Kapasitas kamar operasi sudah penuh.',
                ];
            }

            SurgerySchedule::create([
                'surgery_request_id' => $selectedRequest->id,
                'patient_id' => $selectedRequest->patient_id,
                'doctor_id' => $selectedRequest->requested_doctor_id,
                'operating_room_id' => $room->id,
                'approved_by' => $request->user()->id,
                'surgery_date' => $selectedRequest->requested_date,
                'start_time' => $selectedRequest->requested_start_time,
                'end_time' => $validated['end_time'],
                'schedule_status' => 'scheduled',
            ]);

            $room->decrement('capacity');
            $room->refresh();

            if ($room->capacity < 1) {
                $room->update(['status' => 'dipakai']);
            }

            return null;
        });

        if ($result !== null) {
            return back()
                ->withErrors([$result['field'] => $result['message']])
                ->withInput();
        }

        return redirect()
            ->route('nurse-ok.schedules.index')
            ->with('status', 'Penjadwalan pasien berhasil disimpan.');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->abortUnlessOkNurse();

        $room = DB::transaction(function () use ($request): OperatingRoom {
            $validated = $this->validatedPayload($request);
            $room = OperatingRoom::create($this->roomPayload($validated));
            $this->syncFacilities($room, $validated['facilities'] ?? []);

            return $room;
        });

        return redirect()
            ->route('nurse-ok.rooms.show', $room)
            ->with('status', 'Kamar operasi berhasil ditambahkan.');
    }

    public function show(OperatingRoom $operatingRoom): View
    {
        $this->abortUnlessOkNurse();

        return view('nurse-ok.rooms.show', [
            'room' => $operatingRoom->load(['facilities', 'surgerySchedules.patient', 'surgerySchedules.doctor.user']),
        ]);
    }

    public function edit(OperatingRoom $operatingRoom): View
    {
        $this->abortUnlessOkNurse();

        return view('nurse-ok.rooms.edit', [
            'room' => $operatingRoom->load('facilities'),
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(Request $request, OperatingRoom $operatingRoom): RedirectResponse
    {
        $this->abortUnlessOkNurse();

        $room = DB::transaction(function () use ($request, $operatingRoom): OperatingRoom {
            $validated = $this->validatedPayload($request, $operatingRoom);
            $operatingRoom->update($this->roomPayload($validated));
            $this->syncFacilities($operatingRoom, $validated['facilities'] ?? []);

            return $operatingRoom;
        });

        return redirect()
            ->route('nurse-ok.rooms.show', $room)
            ->with('status', 'Kamar operasi berhasil diperbarui.');
    }

    public function destroy(OperatingRoom $operatingRoom): RedirectResponse
    {
        $this->abortUnlessOkNurse();

        if ($operatingRoom->surgerySchedules()->exists()) {
            return redirect()
                ->back()
                ->with('status', 'Kamar operasi tidak bisa dihapus karena sudah memiliki jadwal operasi.');
        }

        $operatingRoom->delete();

        return redirect()
            ->route('nurse-ok.rooms.index')
            ->with('status', 'Kamar operasi berhasil dihapus.');
    }

    private function validatedPayload(Request $request, ?OperatingRoom $operatingRoom = null): array
    {
        return $request->validate([
            'room_code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('operating_rooms', 'room_code')->ignore($operatingRoom),
            ],
            'room_name' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(self::STATUSES)],
            'facilities' => ['nullable', 'array'],
            'facilities.*.name' => ['nullable', 'string', 'max:255'],
            'facilities.*.description' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function roomPayload(array $validated): array
    {
        return [
            'room_code' => $validated['room_code'],
            'room_name' => $validated['room_name'],
            'capacity' => $validated['capacity'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
        ];
    }

    private function syncFacilities(OperatingRoom $room, array $facilities): void
    {
        $normalizedFacilities = collect($facilities)
            ->filter(fn (array $facility) => filled($facility['name'] ?? null))
            ->map(fn (array $facility) => [
                'name' => trim((string) $facility['name']),
                'description' => filled($facility['description'] ?? null) ? trim((string) $facility['description']) : null,
            ])
            ->values()
            ->all();

        $room->facilities()->delete();
        $room->facilities()->createMany($normalizedFacilities);
    }

    private function abortUnlessOkNurse(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        abort_unless($user?->isOkNurse(), 403);
    }
}
