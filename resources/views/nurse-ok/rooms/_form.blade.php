@if ($errors->any())
    <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
        <p class="font-semibold">Periksa kembali input kamar operasi.</p>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $facilityRows = old('facilities');

    if (! is_array($facilityRows)) {
        $facilityRows = isset($room) && $room?->exists
            ? $room->facilities->map(fn ($facility) => [
                'name' => $facility->name,
                'description' => $facility->description,
            ])->values()->all()
            : [];
    }

    if (empty($facilityRows)) {
        $facilityRows = [
            ['name' => '', 'description' => ''],
        ];
    }
@endphp

<div class="grid gap-4 md:grid-cols-2">
    <label class="space-y-2">
        <span class="text-sm font-medium text-slate-600">Kode Kamar</span>
        <input name="room_code" value="{{ old('room_code', $room->room_code ?? '') }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600" required>
    </label>

    <label class="space-y-2">
        <span class="text-sm font-medium text-slate-600">Nama Kamar</span>
        <input name="room_name" value="{{ old('room_name', $room->room_name ?? '') }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600" required>
    </label>

    <label class="space-y-2">
        <span class="text-sm font-medium text-slate-600">Kapasitas</span>
        <input type="number" min="1" name="capacity" value="{{ old('capacity', $room->capacity ?? '') }}" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600" required>
    </label>

    <label class="space-y-2">
        <span class="text-sm font-medium text-slate-600">Status</span>
        <select name="status" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600" required>
            @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(old('status', $room->status ?? 'siap') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </label>

    <label class="space-y-2 md:col-span-2">
        <span class="text-sm font-medium text-slate-600">Keterangan</span>
        <textarea name="description" rows="3" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">{{ old('description', $room->description ?? '') }}</textarea>
    </label>

    <div class="md:col-span-2 rounded-2xl border border-slate-200 bg-slate-50 p-4" x-data="{ facilities: @js($facilityRows) }">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-sm font-semibold text-slate-900">Fasilitas Ruang</p>
                <p class="text-xs text-slate-500">Tambahkan fasilitas yang tersedia di kamar operasi.</p>
            </div>
            <button type="button" @click="facilities.push({ name: '', description: '' })" class="rounded-lg bg-cyan-700 px-3 py-2 text-xs font-semibold text-white hover:bg-cyan-800">
                Tambah Fasilitas
            </button>
        </div>

        <div class="mt-4 space-y-3">
            <template x-for="(facility, index) in facilities" :key="index">
                <div class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
                    <div class="grid gap-3 md:grid-cols-[1fr_1.2fr_auto]">
                        <label class="space-y-2">
                            <span class="text-sm font-medium text-slate-600">Nama Fasilitas</span>
                            <input
                                type="text"
                                :name="'facilities[' + index + '][name]'"
                                x-model="facility.name"
                                class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600"
                                placeholder="Contoh: Monitor pasien"
                            >
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-medium text-slate-600">Keterangan</span>
                            <input
                                type="text"
                                :name="'facilities[' + index + '][description]'"
                                x-model="facility.description"
                                class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600"
                                placeholder="Contoh: Kondisi baik"
                            >
                        </label>

                        <div class="flex items-end">
                            <button type="button" @click="facilities.splice(index, 1)" class="rounded-lg border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-50">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
