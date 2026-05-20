<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Perawat UK</p>
            <h1 class="text-2xl font-bold text-slate-900">Edit Kamar Operasi</h1>
        </div>
    </x-slot>

    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <form method="POST" action="{{ route('nurse-uk.rooms.update', $room) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @include('nurse-uk.rooms._form')

            <div class="flex justify-end gap-3">
                <a href="{{ route('nurse-uk.rooms.show', $room) }}" class="rounded-lg border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
                <button type="submit" class="rounded-lg bg-cyan-700 px-5 py-3 text-sm font-semibold text-white hover:bg-cyan-800">Simpan Perubahan</button>
            </div>
        </form>
    </section>
</x-app-layout>
