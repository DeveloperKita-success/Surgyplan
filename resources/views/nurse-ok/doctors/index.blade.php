<x-app-layout>
    <x-slot name="header"><div><p class="text-sm font-medium text-slate-500">Perawat OK</p><h1 class="text-2xl font-bold text-slate-900">Dokter</h1></div></x-slot>
    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-slate-500"><tr><th class="px-5 py-3">Nama</th><th class="px-5 py-3">Gelar</th><th class="px-5 py-3">Spesialis</th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($doctors as $doctor)
                    <tr><td class="px-5 py-4 font-medium">{{ $doctor->user->name }}</td><td class="px-5 py-4">{{ $doctor->title }}</td><td class="px-5 py-4">{{ $doctor->specialist?->name ?? '-' }}</td></tr>
                @empty
                    <tr><td colspan="3" class="px-5 py-8 text-center text-slate-500">Belum ada dokter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $doctors->links() }}</div>
</x-app-layout>
