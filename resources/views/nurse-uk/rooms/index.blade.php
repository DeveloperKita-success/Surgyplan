<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Perawat UK</p>
            <h1 class="text-2xl font-bold text-slate-900">Kamar Operasi</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <form method="GET" action="{{ route('nurse-uk.rooms.index') }}" class="grid gap-3 lg:grid-cols-[minmax(240px,1fr)_180px_auto]">
                <input
                    type="search"
                    name="q"
                    value="{{ $query }}"
                    placeholder="Cari kode atau nama kamar"
                    class="rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600"
                >
                <select name="status" class="rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                    <option value="">Semua status</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected($activeStatus === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-950">Terapkan</button>
                    <a href="{{ route('nurse-uk.rooms.index') }}" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
                </div>
            </form>

            <a href="{{ route('nurse-uk.rooms.create') }}" class="rounded-lg bg-cyan-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-cyan-800">
                Tambah Kamar
            </a>
        </div>

        <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-medium">Kode</th>
                            <th class="px-5 py-3 font-medium">Nama Kamar</th>
                            <th class="px-5 py-3 font-medium">Spesialis</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                            <th class="px-5 py-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($rooms as $room)
                            <tr>
                                <td class="px-5 py-4 font-semibold text-slate-900">{{ $room->room_code }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $room->room_name }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $room->specialist?->name ?? '-' }}</td>
                                <td class="px-5 py-4">
                                    <span class="rounded-md bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ ucfirst($room->status) }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('nurse-uk.rooms.show', $room) }}" class="rounded-md border border-slate-200 px-3 py-1.5 font-medium text-slate-700 hover:bg-slate-50">Detail</a>
                                        <a href="{{ route('nurse-uk.rooms.edit', $room) }}" class="rounded-md border border-cyan-200 px-3 py-1.5 font-medium text-cyan-700 hover:bg-cyan-50">Edit</a>
                                        <form method="POST" action="{{ route('nurse-uk.rooms.destroy', $room) }}" onsubmit="return confirm('Hapus kamar operasi ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-md border border-rose-200 px-3 py-1.5 font-medium text-rose-700 hover:bg-rose-50">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-slate-500">Belum ada kamar operasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{ $rooms->links() }}
    </div>
</x-app-layout>
