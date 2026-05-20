<?php

namespace App\Http\Controllers;

use App\Models\OperatingRoom;
use App\Models\Specialist;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UkOperatingRoomController extends Controller
{
    private const STATUSES = ['siap', 'dipakai', 'perawatan', 'nonaktif'];

    public function index(Request $request): View
    {
        $this->abortUnlessUkNurse();

        $query = $request->string('q')->trim()->toString();
        $status = $request->string('status')->trim()->toString();

        $rooms = OperatingRoom::query()
            ->with('specialist')
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

        return view('uk.rooms.index', [
            'rooms' => $rooms,
            'query' => $query,
            'activeStatus' => in_array($status, self::STATUSES, true) ? $status : '',
            'statuses' => self::STATUSES,
        ]);
    }

    public function create(): View
    {
        $this->abortUnlessUkNurse();

        return view('uk.rooms.create', [
            'specialists' => $this->specialists(),
            'statuses' => self::STATUSES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->abortUnlessUkNurse();

        $room = OperatingRoom::create($this->validatedPayload($request));

        return redirect()
            ->route('uk.rooms.show', $room)
            ->with('status', 'Kamar operasi berhasil ditambahkan.');
    }

    public function show(OperatingRoom $operatingRoom): View
    {
        $this->abortUnlessUkNurse();

        return view('uk.rooms.show', [
            'room' => $operatingRoom->load(['specialist', 'surgerySchedules.patient', 'surgerySchedules.doctor.user']),
        ]);
    }

    public function edit(OperatingRoom $operatingRoom): View
    {
        $this->abortUnlessUkNurse();

        return view('uk.rooms.edit', [
            'room' => $operatingRoom,
            'specialists' => $this->specialists(),
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(Request $request, OperatingRoom $operatingRoom): RedirectResponse
    {
        $this->abortUnlessUkNurse();

        $operatingRoom->update($this->validatedPayload($request, $operatingRoom));

        return redirect()
            ->route('uk.rooms.show', $operatingRoom)
            ->with('status', 'Kamar operasi berhasil diperbarui.');
    }

    public function destroy(OperatingRoom $operatingRoom): RedirectResponse
    {
        $this->abortUnlessUkNurse();

        if ($operatingRoom->surgerySchedules()->exists()) {
            return redirect()
                ->back()
                ->with('status', 'Kamar operasi tidak bisa dihapus karena sudah memiliki jadwal operasi.');
        }

        $operatingRoom->delete();

        return redirect()
            ->route('uk.rooms.index')
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
            'specialist_id' => ['nullable', 'exists:specialists,id'],
            'status' => ['required', Rule::in(self::STATUSES)],
        ]);
    }

    private function specialists()
    {
        return Specialist::query()
            ->orderBy('name')
            ->get();
    }

    private function abortUnlessUkNurse(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        abort_unless($user?->isUkNurse(), 403);
    }
}
