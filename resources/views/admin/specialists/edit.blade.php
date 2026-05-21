<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Data pendukung admin</p>
            <h1 class="text-2xl font-bold text-slate-900">Edit Spesialis</h1>
        </div>
    </x-slot>

    <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.specialists.update', $specialist) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <label class="space-y-2">
                <span class="text-sm font-semibold text-slate-700">Nama Spesialis</span>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $specialist->name) }}"
                    class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                    placeholder="Contoh: Bedah Umum"
                />
                @error('name')
                    <p class="text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </label>

            <label class="space-y-2">
                <span class="text-sm font-semibold text-slate-700">Deskripsi</span>
                <textarea
                    name="description"
                    rows="4"
                    class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                    placeholder="Catatan singkat tentang spesialis"
                >{{ old('description', $specialist->description) }}</textarea>
                @error('description')
                    <p class="text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </label>

            <div class="flex flex-wrap items-center justify-end gap-3">
                <a
                    href="{{ route('admin.specialists.show', $specialist) }}"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                >
                    Batal
                </a>
                <button
                    type="submit"
                    class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700"
                >
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </section>
</x-app-layout>
