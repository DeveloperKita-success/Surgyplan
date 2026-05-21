<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Kontrol operasional dan data</p>
            <h1 class="text-2xl font-bold text-slate-900">Dashboard Admin</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section
            class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4"
            x-data="{
                onlineCount: 0,
                doctorOnline: 0,
                ukNurseOnline: 0,
                regularNurseOnline: 0,
                refresh() {
                    fetch('{{ route('admin.online-count') }}')
                        .then((response) => response.json())
                        .then((data) => {
                            this.onlineCount = data.online ?? 0;
                            this.doctorOnline = data.online_doctor ?? 0;
                            this.ukNurseOnline = data.online_uk_nurse ?? 0;
                            this.regularNurseOnline = data.online_regular_nurse ?? 0;
                        });
                },
                init() {
                    this.refresh();
                    setInterval(() => this.refresh(), 30000);
                }
            }"
            x-init="init()"
        >
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Pengguna Online (1 menit)</p>
                <p class="mt-3 text-3xl font-bold text-slate-900" x-text="onlineCount">0</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Dokter Aktif</p>
                <p class="mt-3 text-3xl font-bold text-slate-900" x-text="doctorOnline">0</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Perawat OK Aktif</p>
                <p class="mt-3 text-3xl font-bold text-slate-900" x-text="ukNurseOnline">0</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Perawat Reguler Aktif</p>
                <p class="mt-3 text-3xl font-bold text-slate-900" x-text="regularNurseOnline">0</p>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-lg font-semibold">Aktivitas Terbaru</h2>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($recentActiveUsers as $user)
                        <div class="px-5 py-4">
                            <p class="font-semibold text-slate-900">{{ $user['name'] }}</p>
                            <p class="text-sm text-slate-500">{{ $user['role'] }} · {{ $user['last_active'] }}</p>
                        </div>
                    @empty
                        <div class="px-5 py-4 text-sm text-slate-500">
                            Belum ada aktivitas pengguna.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Status Sistem</h2>
                <div class="mt-5 space-y-4">
                    @foreach ($systemStatus as $status)
                        <div class="flex items-center justify-between rounded-lg bg-slate-50 p-4">
                            <span class="font-medium text-slate-900">{{ $status['label'] }}</span>
                            <span class="text-sm font-semibold text-emerald-700">{{ $status['value'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
