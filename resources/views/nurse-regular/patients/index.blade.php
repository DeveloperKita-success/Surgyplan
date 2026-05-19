<x-app-layout>
    <x-slot name="header"><div><p class="text-sm font-medium text-slate-500">Perawat Reguler</p><h1 class="text-2xl font-bold text-slate-900">Data Pasien</h1></div></x-slot>
    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-slate-500"><tr><th class="px-5 py-3">No RM</th><th class="px-5 py-3">Nama</th><th class="px-5 py-3">Ruang Asal</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($patients as $patient)
                    <tr><td class="px-5 py-4">{{ $patient->medical_record_number }}</td><td class="px-5 py-4 font-medium">{{ $patient->name }}</td><td class="px-5 py-4">{{ $patient->origin_room }}</td></tr>
                @empty
                    <tr><td colspan="3" class="px-5 py-8 text-center text-slate-500">Belum ada pasien.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $patients->links() }}</div>
</x-app-layout>
