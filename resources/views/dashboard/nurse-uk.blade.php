<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Koordinasi unit kamar operasi</p>
            <h1 class="text-2xl font-bold text-slate-900">Dashboard Perawat OK</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Checklist Aktif', 'value' => '12'],
                ['label' => 'Kamar Siap', 'value' => '04'],
                ['label' => 'Verifikasi Hari Ini', 'value' => '09'],
                ['label' => 'Kasus Prioritas', 'value' => '02'],
            ] as $stat)
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
                    @foreach ([
                        ['patient' => 'Siti Rahma', 'progress' => 'Lengkap', 'tone' => 'bg-emerald-100 text-emerald-700'],
                        ['patient' => 'Budi Santoso', 'progress' => '6/8 item', 'tone' => 'bg-amber-100 text-amber-700'],
                        ['patient' => 'Agus Wijaya', 'progress' => 'Perlu review', 'tone' => 'bg-rose-100 text-rose-700'],
                    ] as $item)
                        <div class="flex items-center justify-between rounded-lg bg-slate-50 p-4">
                            <span class="font-medium text-slate-900">{{ $item['patient'] }}</span>
                            <span class="rounded-md px-2.5 py-1 text-xs font-semibold {{ $item['tone'] }}">{{ $item['progress'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Status Kamar Operasi</h2>
                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    @foreach ([
                        ['room' => 'OK 1', 'status' => 'Steril'],
                        ['room' => 'OK 2', 'status' => 'Dipakai'],
                        ['room' => 'OK 3', 'status' => 'Persiapan'],
                        ['room' => 'OK 4', 'status' => 'Siap'],
                    ] as $room)
                        <div class="rounded-lg border border-slate-200 p-4">
                            <p class="font-semibold text-slate-900">{{ $room['room'] }}</p>
                            <p class="mt-2 text-sm text-slate-500">{{ $room['status'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
