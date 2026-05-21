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
        <span class="text-sm font-medium text-slate-600">Spesialis</span>
        <select name="specialist_id" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
            <option value="">Tanpa spesialis khusus</option>
            @foreach ($specialists as $specialist)
                <option value="{{ $specialist->id }}" @selected((string) old('specialist_id', $room->specialist_id ?? '') === (string) $specialist->id)>
                    {{ $specialist->name }}
                </option>
            @endforeach
        </select>
    </label>

    <label class="space-y-2">
        <span class="text-sm font-medium text-slate-600">Status</span>
        <select name="status" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600" required>
            @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(old('status', $room->status ?? 'siap') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </label>
</div>
