<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Master data</p>
            <h1 class="text-2xl font-bold text-slate-900">Data Pasien</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <form method="GET" action="{{ route('patients.index') }}" class="flex w-full flex-col gap-3">
                    <div class="grid gap-3 lg:grid-cols-5 lg:items-end">
                        <label class="flex flex-col gap-1 lg:col-span-2">
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Cari</span>
                            <span class="flex w-full items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-600">
                                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none">
                                    <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                                    <path d="m20 20-3.5-3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                <input
                                    type="search"
                                    name="q"
                                    value="{{ $query }}"
                                    placeholder="Cari nama / nomor RM"
                                    class="w-full border-0 bg-transparent p-0 text-sm text-slate-700 placeholder:text-slate-400 focus:ring-0"
                                />
                            </span>
                        </label>

                        <label class="flex flex-col gap-1">
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Ruang Asal</span>
                            <select name="origin_room" class="rounded-lg border-slate-200 bg-white py-2.5 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                                <option value="" @selected($originRoom === '')>Semua</option>
                                <option value="IGD" @selected($originRoom === 'IGD')>IGD</option>
                                <option value="Bangsal" @selected($originRoom === 'Bangsal')>Bangsal</option>
                                <option value="Poli" @selected($originRoom === 'Poli')>Poli</option>
                            </select>
                        </label>

                        <label class="flex flex-col gap-1">
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Jenis Kelamin</span>
                            <select name="gender" class="rounded-lg border-slate-200 bg-white py-2.5 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                                <option value="" @selected($gender === '')>Semua</option>
                                <option value="L" @selected($gender === 'L')>Laki-laki</option>
                                <option value="P" @selected($gender === 'P')>Perempuan</option>
                            </select>
                        </label>

                        <label class="flex flex-col gap-1">
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Status Terakhir</span>
                            <select name="last_status" class="rounded-lg border-slate-200 bg-white py-2.5 text-sm focus:border-cyan-600 focus:ring-cyan-600">
                                <option value="" @selected($lastStatus === '')>Semua</option>
                                @foreach ($statusOptions as $status)
                                    <option value="{{ $status }}" @selected($lastStatus === $status)>{{ $status }}</option>
                                @endforeach
                            </select>
                        </label>

                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">&nbsp;</span>
                            <div class="flex items-center gap-2">
                                <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-950">Terapkan</button>
                                <a href="{{ route('patients.index') }}" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="mt-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead>
                        <tr class="text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            <th class="px-3 py-3">No. RM</th>
                            <th class="px-3 py-3">Nama</th>
                            <th class="px-3 py-3">Gender</th>
                            <th class="px-3 py-3">Umur</th>
                            <th class="px-3 py-3">Ruang Asal</th>
                            <th class="px-3 py-3">Status Terakhir</th>
                            <th class="px-3 py-3">Dibuat</th>
                            <th class="px-3 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse ($patients as $patient)
                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-3 py-3 font-semibold text-slate-900">{{ $patient->medical_record_number }}</td>
                                <td class="px-3 py-3">
                                    <a href="{{ route('patients.show', $patient) }}" class="font-semibold text-cyan-800 hover:underline">
                                        {{ $patient->name }}
                                    </a>
                                </td>
                                <td class="whitespace-nowrap px-3 py-3 text-slate-600">
                                    {{ $patient->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-3 text-slate-600">{{ $patient->age ?? '-' }}</td>
                                <td class="whitespace-nowrap px-3 py-3 text-slate-600">{{ $patient->origin_room ?? '-' }}</td>
                                <td class="whitespace-nowrap px-3 py-3 text-slate-600">{{ $patient->latestSurgeryRequest?->request_status ?? '-' }}</td>
                                <td class="whitespace-nowrap px-3 py-3 text-slate-600">{{ $patient->created_at?->format('d M Y') }}</td>
                                <td class="whitespace-nowrap px-3 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('patients.show', $patient) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Detail</a>
                                        <a href="{{ route('patients.edit', $patient) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Edit</a>
                                        <form method="POST" action="{{ route('patients.destroy', $patient) }}" onsubmit="return confirm('Hapus data pasien ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-100">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-3 py-10 text-center text-sm text-slate-500">
                                    Belum ada data pasien.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $patients->links() }}
            </div>
        </section>
    </div>
</x-app-layout>
