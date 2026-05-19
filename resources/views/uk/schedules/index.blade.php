<x-app-layout>
    <x-slot name="header"><div><p class="text-sm font-medium text-slate-500">Perawat UK</p><h1 class="text-2xl font-bold text-slate-900">Jadwal Operasi</h1></div></x-slot>
    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-slate-500"><tr><th class="px-5 py-3">Pasien</th><th class="px-5 py-3">Dokter</th><th class="px-5 py-3">Kamar</th><th class="px-5 py-3">Tanggal</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($schedules as $schedule)
                    <tr><td class="px-5 py-4 font-medium">{{ $schedule->patient->name }}</td><td class="px-5 py-4">{{ $schedule->doctor->user->name }}</td><td class="px-5 py-4">{{ $schedule->operatingRoom->room_name }}</td><td class="px-5 py-4">{{ $schedule->surgery_date?->format('d M Y') }}</td></tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-8 text-center text-slate-500">Belum ada jadwal operasi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $schedules->links() }}</div>
</x-app-layout>
