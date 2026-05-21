<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Perawat OK</p>
            <h1 class="text-2xl font-bold text-slate-900">Penjadwalan Pasien</h1>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{
        requestOptions: @js($requestOptions),
        roomOptions: @js($roomOptions),
        selectedRequestId: '{{ old('surgery_request_id') }}',
        selectedRoomId: '{{ old('operating_room_id') }}',
        requestDetail() {
            return this.requestOptions.find((item) => String(item.id) === String(this.selectedRequestId));
        },
        roomDetail() {
            return this.roomOptions.find((item) => String(item.id) === String(this.selectedRoomId));
        }
    }">
        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                <p class="font-semibold">Periksa kembali input penjadwalan.</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('nurse-ok.rooms.patient-scheduling.store') }}" class="space-y-6">
            @csrf

            <div class="grid gap-6 xl:grid-cols-5">
                <section class="xl:col-span-2 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="mb-4">
                        <h2 class="text-base font-semibold text-slate-900">Informasi Pasien</h2>
                        <p class="mt-1 text-sm text-slate-500">Pilih pasien yang sudah mendapatkan verifikasi.</p>
                    </div>

                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Pasien Terverifikasi</span>
                        <select
                            name="surgery_request_id"
                            x-model="selectedRequestId"
                            class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600"
                            required
                        >
                            <option value="">Pilih pasien terverifikasi</option>
                            @foreach ($verifiedRequests as $verifiedRequest)
                                <option value="{{ $verifiedRequest->id }}" @selected(old('surgery_request_id') == $verifiedRequest->id)>
                                    {{ $verifiedRequest->patient?->name ?? '-' }} - {{ optional($verifiedRequest->requested_date)->format('d/m/Y') ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50/70 p-4" x-show="requestDetail()" x-cloak>
                        <div class="mb-4 flex items-center gap-3 rounded-lg border border-emerald-200 bg-white px-3 py-2 text-emerald-800">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                    <path d="m9 11 3 3L22 4" />
                                </svg>
                            </span>
                            <div>
                                <p class="text-sm font-bold">Data pasien sudah diverifikasi</p>
                                <p class="text-xs font-medium text-emerald-700">Siap dilanjutkan ke penjadwalan kamar operasi.</p>
                            </div>
                        </div>

                        <dl class="space-y-2 text-sm">
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-slate-500">Nama Pasien</dt>
                                <dd class="font-medium text-slate-900" x-text="requestDetail()?.patient_name ?? '-'"></dd>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-slate-500">No. RM</dt>
                                <dd class="font-medium text-slate-900" x-text="requestDetail()?.patient_mrn ?? '-'"></dd>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-slate-500">Jenis Kelamin</dt>
                                <dd class="font-medium text-slate-900" x-text="requestDetail()?.patient_gender ?? '-'"></dd>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-slate-500">Asal Ruangan</dt>
                                <dd class="font-medium text-slate-900" x-text="requestDetail()?.patient_origin_room ?? '-'"></dd>
                            </div>
                            <div class="my-2 h-px bg-slate-200"></div>
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-slate-500">Dokter</dt>
                                <dd class="font-medium text-slate-900" x-text="requestDetail()?.doctor_name ?? '-'"></dd>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-slate-500">Tanggal Operasi</dt>
                                <dd class="font-medium text-slate-900" x-text="requestDetail()?.surgery_date ?? '-'"></dd>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-slate-500">Jam Operasi</dt>
                                <dd class="font-medium text-slate-900">
                                    <span x-text="requestDetail()?.start_time ?? '-'"></span>
                                    -
                                    <span x-text="requestDetail()?.end_time ?? '-'"></span>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <p class="mt-4 text-xs text-slate-500" x-show="!requestDetail()" x-cloak>
                        Pilih pasien terlebih dahulu untuk melihat detail verifikasi.
                    </p>
                </section>

                <section class="xl:col-span-3 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="mb-4">
                        <h2 class="text-base font-semibold text-slate-900">Pilih Kamar Operasi</h2>
                        <p class="mt-1 text-sm text-slate-500">Tentukan kamar operasi untuk pasien yang sudah diverifikasi.</p>
                    </div>

                    <label class="space-y-2">
                        <span class="text-sm font-medium text-slate-600">Kamar Operasi</span>
                        <select
                            name="operating_room_id"
                            x-model="selectedRoomId"
                            class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600"
                            required
                        >
                            <option value="">Pilih kamar operasi</option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->id }}" @selected(old('operating_room_id') == $room->id)>
                                    {{ $room->room_code }} - {{ $room->room_name }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <div class="mt-4 rounded-xl border border-cyan-100 bg-cyan-50/70 p-4" x-show="roomDetail()" x-cloak>
                        <dl class="grid gap-3 sm:grid-cols-2 text-sm">
                            <div>
                                <dt class="text-slate-500">Nama Kamar</dt>
                                <dd class="mt-1 font-semibold text-slate-900" x-text="roomDetail()?.room_name ?? '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-slate-500">Kode Kamar</dt>
                                <dd class="mt-1 font-semibold text-slate-900" x-text="roomDetail()?.room_code ?? '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-slate-500">Status</dt>
                                <dd class="mt-1 font-semibold capitalize text-slate-900" x-text="roomDetail()?.status ?? '-'"></dd>
                            </div>
                            <div>
                                <dt class="text-slate-500">Kapasitas</dt>
                                <dd class="mt-1 font-semibold text-slate-900" x-text="roomDetail()?.capacity ?? '-'"></dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-slate-500">Keterangan</dt>
                                <dd class="mt-1 font-medium text-slate-700" x-text="roomDetail()?.description || '-'"></dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-slate-500">Fasilitas</dt>
                                <dd class="mt-2">
                                    <template x-if="(roomDetail()?.facilities ?? []).length > 0">
                                        <div class="flex flex-wrap gap-2">
                                            <template x-for="facility in roomDetail().facilities" :key="facility">
                                                <span class="rounded-md bg-white px-2.5 py-1 text-xs font-semibold text-slate-700" x-text="facility"></span>
                                            </template>
                                        </div>
                                    </template>
                                    <p class="text-sm text-slate-600" x-show="(roomDetail()?.facilities ?? []).length === 0">Belum ada fasilitas terdaftar.</p>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50/70 p-4" x-show="requestDetail() && roomDetail()" x-cloak>
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <div>
                                <h3 class="text-sm font-bold text-emerald-900">Jadwal Operasi</h3>
                                <p class="mt-1 text-xs font-medium text-emerald-700">Tanggal dan jam mulai mengikuti data request pasien.</p>
                            </div>
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-emerald-700">Terverifikasi</span>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-3">
                            <div class="rounded-lg bg-white px-3 py-2">
                                <dt class="text-xs font-medium text-slate-500">Tanggal</dt>
                                <dd class="mt-1 text-sm font-semibold text-slate-900" x-text="requestDetail()?.surgery_date ?? '-'"></dd>
                            </div>
                            <div class="rounded-lg bg-white px-3 py-2">
                                <dt class="text-xs font-medium text-slate-500">Waktu Mulai</dt>
                                <dd class="mt-1 text-sm font-semibold text-slate-900" x-text="requestDetail()?.start_time ?? '-'"></dd>
                            </div>
                            <label class="rounded-lg bg-white px-3 py-2">
                                <span class="text-xs font-medium text-slate-500">Waktu Selesai</span>
                                <input type="time" name="end_time" value="{{ old('end_time') }}" class="mt-1 w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600" required>
                                @error('end_time')
                                    <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                                @enderror
                            </label>
                        </div>
                    </div>

                    <p class="mt-4 text-xs text-slate-500" x-show="!roomDetail()" x-cloak>
                        Pilih kamar operasi untuk melihat detail ruangan.
                    </p>
                </section>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('nurse-ok.rooms.index') }}" class="rounded-lg border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Batal
                </a>
                <button type="submit" class="rounded-lg bg-cyan-700 px-5 py-3 text-sm font-semibold text-white hover:bg-cyan-800">
                    Simpan Penjadwalan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
