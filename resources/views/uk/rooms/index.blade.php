<x-app-layout>
    <x-slot name="header"><div><p class="text-sm font-medium text-slate-500">Perawat UK</p><h1 class="text-2xl font-bold text-slate-900">Kamar Operasi</h1></div></x-slot>
    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-slate-500"><tr><th class="px-5 py-3">Kode</th><th class="px-5 py-3">Nama Kamar</th><th class="px-5 py-3">Spesialis</th><th class="px-5 py-3">Status</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($rooms as $room)
                    <tr><td class="px-5 py-4">{{ $room->room_code }}</td><td class="px-5 py-4 font-medium">{{ $room->room_name }}</td><td class="px-5 py-4">{{ $room->specialist?->name ?? '-' }}</td><td class="px-5 py-4">{{ ucfirst($room->status) }}</td></tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-8 text-center text-slate-500">Belum ada kamar operasi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $rooms->links() }}</div>
</x-app-layout>
