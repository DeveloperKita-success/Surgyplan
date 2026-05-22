<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Koordinasi unit kamar operasi</p>
            <h1 class="text-2xl font-bold text-slate-900">Dashboard Perawat OK</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($stats as $stat)
                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm text-slate-500">{{ $stat['label'] }}</p>
                    <p class="mt-3 text-3xl font-bold text-slate-900">{{ $stat['value'] }}</p>
                </div>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-[1fr_1fr]">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Checklist Pasien</h2>
                <div class="mt-5 space-y-3">
                    @forelse ($checklistPatients as $item)
                        <div class="flex items-center justify-between rounded-lg bg-slate-50 p-4">
                            <span class="font-medium text-slate-900">{{ $item['patient'] }}</span>
                            <span class="rounded-md px-2.5 py-1 text-xs font-semibold {{ $item['tone'] }}">{{ $item['progress'] }}</span>
                        </div>
                    @empty
                        <div class="rounded-lg bg-slate-50 p-4 text-sm font-medium text-slate-500">
                            Belum ada checklist pasien aktif.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Status Kamar Operasi</h2>
                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    @forelse ($operatingRooms as $room)
                        <div class="rounded-lg border p-4 {{ $room['tone'] }}">
                            <p class="font-semibold text-slate-900">{{ $room['room'] }}</p>
                            <p class="mt-1 text-xs font-medium text-slate-500">{{ $room['name'] }}</p>
                            <p class="mt-2 text-sm font-semibold">{{ $room['status'] }}</p>
                        </div>
                    @empty
                        <div class="rounded-lg border border-slate-200 p-4 text-sm font-medium text-slate-500 sm:col-span-2">
                            Belum ada data kamar operasi.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
