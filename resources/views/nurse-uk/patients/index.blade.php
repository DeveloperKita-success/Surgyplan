<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Master data</p>
            <h1 class="text-2xl font-bold text-slate-900">Data Patient</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif



        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <form method="GET" action="{{ route('nurse-uk.patients.index') }}" class="grid gap-3 lg:grid-cols-5 lg:items-end">
                <label class="space-y-1 lg:col-span-2">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Cari</span>
                    <input
                        type="search"
                        name="q"
                        value="{{ $query }}"
                        placeholder="Nama atau nomor RM"
                        class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600"
                    >
                </label>

                <label class="space-y-1">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Ruang Asal</span>
                    <select name="origin_room" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                        <option value="">Semua</option>
                        @foreach (['IGD', 'Bangsal', 'Poli'] as $room)
                            <option value="{{ $room }}" @selected($originRoom === $room)>{{ $room }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="space-y-1">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Jenis Kelamin</span>
                    <select name="gender" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                        <option value="">Semua</option>
                        @foreach (['Laki-laki', 'Perempuan'] as $option)
                            <option value="{{ $option }}" @selected($gender === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="space-y-1">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Status Terakhir</span>
                    <select name="last_status" class="w-full rounded-lg border-slate-200 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                        <option value="">Semua</option>
                        @foreach ($statusOptions as $status)
                            <option value="{{ $status }}" @selected($lastStatus === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </label>

                <div class="flex gap-2 lg:col-start-5">
                    <button type="submit" class="rounded-lg bg-cyan-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-cyan-800">Terapkan</button>
                    <a href="{{ route('nurse-uk.patients.index') }}" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-medium">No. RM</th>
                            <th class="px-5 py-3 font-medium">Nama</th>
                            <th class="px-5 py-3 font-medium">Jenis Kelamin</th>
                            <th class="px-5 py-3 font-medium">Umur</th>
                            <th class="px-5 py-3 font-medium">Ruang Asal</th>
                            <th class="px-5 py-3 font-medium">Status Terakhir</th>
                            <th class="px-5 py-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($patients as $patient)
                            <tr>
                                <td class="px-5 py-4 font-semibold text-slate-900">{{ $patient->medical_record_number }}</td>
                                <td class="px-5 py-4">
                                    <a href="{{ route('nurse-uk.patients.show', $patient) }}" class="font-semibold text-cyan-800 hover:underline">
                                        {{ $patient->name }}
                                    </a>
                                </td>
                                <td class="px-5 py-4 text-slate-600">{{ $patient->gender ?? '-' }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $patient->age ?? '-' }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $patient->origin_room ?? '-' }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $patient->latestSurgeryRequest?->request_status ? ucfirst($patient->latestSurgeryRequest->request_status) : '-' }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('nurse-uk.patients.show', $patient) }}" class="rounded-md border border-slate-200 px-3 py-1.5 font-medium text-slate-700 hover:bg-slate-50">Detail</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-slate-500">Belum ada data patient.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{ $patients->links() }}
    </div>
</x-app-layout>
