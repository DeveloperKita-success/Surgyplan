<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-slate-500">Panduan & Referensi</p>
            <h1 class="text-2xl font-bold text-slate-900">Buku Pedoman</h1>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <!-- Upload Form -->
        <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold text-slate-900">Tambah Buku Pedoman</h2>
            <form method="POST" action="{{ route('guidelines.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="flex flex-col gap-2 sm:col-span-2">
                        <label for="title" class="text-sm font-semibold text-slate-700">Judul Pedoman <span class="text-rose-600">*</span></label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            placeholder="Contoh: Panduan ICD-10 Diagnosa"
                            value="{{ old('title') }}"
                            class="rounded-lg border-slate-200 bg-white focus:border-emerald-600 focus:ring-emerald-600"
                            required
                        />
                        @error('title')
                            <p class="text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="type" class="text-sm font-semibold text-slate-700">Tipe <span class="text-rose-600">*</span></label>
                        <input
                            type="text"
                            id="type"
                            name="type"
                            placeholder="Contoh: ICD-10, ICD-9-CM"
                            value="{{ old('type') }}"
                            class="rounded-lg border-slate-200 bg-white focus:border-emerald-600 focus:ring-emerald-600"
                            required
                        />
                        @error('type')
                            <p class="text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="file" class="text-sm font-semibold text-slate-700">File PDF</label>
                        <input
                            type="file"
                            id="file"
                            name="file"
                            accept=".pdf"
                            class="rounded-lg border-slate-200 bg-white focus:border-emerald-600 focus:ring-emerald-600"
                        />
                        <p class="text-xs text-slate-500">Format: PDF, Maksimal 10 MB</p>
                        @error('file')
                            <p class="text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2 sm:col-span-2">
                        <label for="description" class="text-sm font-semibold text-slate-700">Deskripsi</label>
                        <textarea
                            id="description"
                            name="description"
                            rows="3"
                            placeholder="Penjelasan singkat tentang pedoman ini"
                            class="rounded-lg border-slate-200 bg-white focus:border-emerald-600 focus:ring-emerald-600"
                        ></textarea>
                        @error('description')
                            <p class="text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">
                        Tambah Pedoman
                    </button>
                </div>
            </form>
        </section>

        <!-- Filter & Search -->
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <form method="GET" action="{{ route('guidelines.index') }}" class="flex w-full flex-col gap-3">
                    <div class="grid gap-3 lg:grid-cols-4 lg:items-end">
                        <label class="flex flex-col gap-1 lg:col-span-2">
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Cari Judul</span>
                            <span class="flex w-full items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-600">
                                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none">
                                    <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                                    <path d="m20 20-3.5-3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                                <input
                                    type="search"
                                    name="q"
                                    value="{{ $query }}"
                                    placeholder="Cari judul pedoman"
                                    class="w-full border-0 bg-transparent p-0 text-sm text-slate-700 placeholder:text-slate-400 focus:ring-0"
                                />
                            </span>
                        </label>

                        <label class="flex flex-col gap-1">
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Tipe</span>
                            <select name="type" class="rounded-lg border-slate-200 bg-white py-2.5 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                                <option value="" @selected($type === '')>Semua</option>
                                @foreach ($typeOptions as $typeOption)
                                    <option value="{{ $typeOption }}" @selected($type === $typeOption)>{{ $typeOption }}</option>
                                @endforeach
                            </select>
                        </label>

                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">&nbsp;</span>
                            <div class="flex items-center gap-2">
                                <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-950">Terapkan</button>
                                <a href="{{ route('guidelines.index') }}" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Guidelines Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead>
                        <tr class="text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            <th class="px-3 py-3">Judul</th>
                            <th class="px-3 py-3">Tipe</th>
                            <th class="px-3 py-3">Deskripsi</th>
                            <th class="px-3 py-3">Diupload Oleh</th>
                            <th class="px-3 py-3">Tanggal Upload</th>
                            <th class="px-3 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse ($guidelines as $guideline)
                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-3 py-3 font-semibold text-slate-900">{{ $guideline->title }}</td>
                                <td class="whitespace-nowrap px-3 py-3">
                                    <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">
                                        {{ $guideline->type }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-slate-600">
                                    <p class="line-clamp-2 max-w-xs">{{ $guideline->description ?? '-' }}</p>
                                </td>
                                <td class="whitespace-nowrap px-3 py-3 text-slate-600">{{ $guideline->uploadedBy?->name ?? '-' }}</td>
                                <td class="whitespace-nowrap px-3 py-3 text-slate-600">{{ $guideline->created_at?->format('d M Y') }}</td>
                                <td class="whitespace-nowrap px-3 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        @if ($guideline->file)
                                            <a
                                                href="{{ Storage::url($guideline->file) }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100"
                                            >
                                                Lihat
                                            </a>
                                        @endif
                                        <form method="POST" action="{{ route('guidelines.destroy', $guideline) }}" onsubmit="return confirm('Hapus pedoman ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-100">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-10 text-center text-sm text-slate-500">
                                    Belum ada buku pedoman.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $guidelines->links() }}
            </div>
        </section>
    </div>
</x-app-layout>
