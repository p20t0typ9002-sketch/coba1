
<x-filament-panels::page>

    @php
        $laporan = $this->getLaporan();
        $ringkasan = $this->getRingkasan();
    @endphp

    {{-- Pilihan jenis laporan --}}
    <div class="flex flex-wrap gap-3">
        <x-filament::button
            wire:click="pilihJenis('peminjaman')"
            :color="$jenis === 'peminjaman' ? 'primary' : 'gray'"
        >
            Laporan Peminjaman
        </x-filament::button>

        <x-filament::button
            wire:click="pilihJenis('pengembalian')"
            :color="$jenis === 'pengembalian' ? 'primary' : 'gray'"
        >
            Laporan Pengembalian
        </x-filament::button>

        
<div class="ms-auto flex flex-wrap gap-2">
    <x-filament::button
        tag="a"
        :href="route('laporan.export.excel', [
            'jenis' => $jenis,
            'dari' => $dari,
            'sampai' => $sampai,
        ])"
        color="success"
        icon="heroicon-o-document-arrow-down"
    >
        Ekspor Excel
    </x-filament::button>

    <x-filament::button
        tag="a"
        :href="route('laporan.export.pdf', [
            'jenis' => $jenis,
            'dari' => $dari,
            'sampai' => $sampai,
        ])"
        color="danger"
        icon="heroicon-o-document"
    >
        Ekspor PDF
    </x-filament::button>
</div>

    </div>

    {{-- Filter tanggal --}}
    <x-filament::section heading="Filter Laporan">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

            <div>
                <label class="mb-2 block text-sm font-medium">
                    Tanggal Dari
                </label>

                <input
                    type="date"
                    wire:model.live="dari"
                    class="fi-input w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                >
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium">
                    Tanggal Sampai
                </label>

                <input
                    type="date"
                    wire:model.live="sampai"
                    min="{{ $dari }}"
                    class="fi-input w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900"
                >
            </div>

            <div class="flex items-end">
                <x-filament::button
                    color="gray"
                    outlined
                    wire:click="resetFilter"
                >
                    Reset Filter
                </x-filament::button>
            </div>

        </div>
    </x-filament::section>


<x-filament::section heading="Ringkasan Transaksi">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

        <div class="rounded-xl bg-primary-50 p-4 dark:bg-primary-500/10">
            <p class="text-sm font-medium text-primary-700 dark:text-primary-400">
                Total Transaksi
            </p>
            <p class="mt-2 text-3xl font-bold text-primary-700 dark:text-primary-400">
                {{ $ringkasan['total'] }}
            </p>
        </div>

        @if ($jenis === 'peminjaman')

            <div class="rounded-xl bg-warning-50 p-4 dark:bg-warning-500/10">
                <p class="text-sm font-medium text-warning-700 dark:text-warning-400">
                    Menunggu Persetujuan
                </p>
                <p class="mt-2 text-3xl font-bold text-warning-700 dark:text-warning-400">
                    {{ $ringkasan['menunggu'] }}
                </p>
            </div>

            <div class="rounded-xl bg-success-50 p-4 dark:bg-success-500/10">
                <p class="text-sm font-medium text-success-700 dark:text-success-400">
                    Sedang Dipinjam
                </p>
                <p class="mt-2 text-3xl font-bold text-success-700 dark:text-success-400">
                    {{ $ringkasan['dipinjam'] }}
                </p>
            </div>

            <div class="rounded-xl bg-gray-100 p-4 dark:bg-gray-800">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Selesai
                </p>
                <p class="mt-2 text-3xl font-bold text-gray-700 dark:text-gray-300">
                    {{ $ringkasan['selesai'] }}
                </p>
            </div>

        @else

            <div class="rounded-xl bg-warning-50 p-4 dark:bg-warning-500/10">
                <p class="text-sm font-medium text-warning-700 dark:text-warning-400">
                    Menunggu Pemeriksaan
                </p>
                <p class="mt-2 text-3xl font-bold text-warning-700 dark:text-warning-400">
                    {{ $ringkasan['diajukan'] }}
                </p>
            </div>

            <div class="rounded-xl bg-success-50 p-4 dark:bg-success-500/10">
                <p class="text-sm font-medium text-success-700 dark:text-success-400">
                    Diterima
                </p>
                <p class="mt-2 text-3xl font-bold text-success-700 dark:text-success-400">
                    {{ $ringkasan['diterima'] }}
                </p>
            </div>

            <div class="rounded-xl bg-danger-50 p-4 dark:bg-danger-500/10">
                <p class="text-sm font-medium text-danger-700 dark:text-danger-400">
                    Ditolak
                </p>
                <p class="mt-2 text-3xl font-bold text-danger-700 dark:text-danger-400">
                    {{ $ringkasan['ditolak'] }}
                </p>
            </div>

        @endif
    </div>
</x-filament::section>


    {{-- Tabel laporan --}}
    <x-filament::section>
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-lg font-bold">
                {{ $jenis === 'peminjaman'
                    ? 'Data Peminjaman'
                    : 'Data Pengembalian' }}
            </h2>

            <span class="text-sm text-gray-500">
                Total data: {{ $laporan->total() }} transaksi
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">

                @if($jenis === 'peminjaman')

                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800">
                            <th class="p-3 text-left">No.</th>
                            <th class="p-3 text-left">Kode</th>
                            <th class="p-3 text-left">Peminjam</th>
                            <th class="p-3 text-left">Tanggal Pinjam</th>
                            <th class="p-3 text-left">Rencana Kembali</th>
                            <th class="p-3 text-left">Daftar Alat</th>
                            <th class="p-3 text-left">Petugas</th>
                            <th class="p-3 text-left">Status</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($laporan as $index => $item)
                            <tr>
                                <td class="p-3">
                                    {{ $laporan->firstItem() + $index }}
                                </td>

                                <td class="p-3 font-medium">
                                    {{ $item->pinjam_code }}
                                </td>

                                <td class="p-3">
                                    {{ $item->user?->name ?? '-' }}
                                </td>

                                <td class="p-3 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}
                                </td>

                                <td class="p-3 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($item->tanggal_rencana_kembali)->format('d/m/Y') }}
                                </td>

                                <td class="p-3">
                                    @foreach($item->details as $detail)
                                        <div>
                                            {{ $detail->alat?->name ?? 'Alat tidak tersedia' }}
                                            ({{ $detail->jumlah }})
                                        </div>
                                    @endforeach
                                </td>

                                <td class="p-3">
                                    {{ $item->petugasPenyetuju?->name ?? '-' }}
                                </td>

                                <td class="p-3">
                                    {{ ucfirst($item->status) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-6 text-center text-gray-500">
                                    Belum ada data peminjaman.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                @else

                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800">
                            <th class="p-3 text-left">No.</th>
                            <th class="p-3 text-left">Kode Peminjaman</th>
                            <th class="p-3 text-left">Peminjam</th>
                            <th class="p-3 text-left">Tanggal Kembali</th>
                            <th class="p-3 text-left">Daftar Alat</th>
                            <th class="p-3 text-left">Petugas</th>
                            <th class="p-3 text-left">Status</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($laporan as $index => $item)
                            <tr>
                                <td class="p-3">
                                    {{ $laporan->firstItem() + $index }}
                                </td>

                                <td class="p-3 font-medium">
                                    {{ $item->peminjaman?->pinjam_code ?? '-' }}
                                </td>

                                <td class="p-3">
                                    {{ $item->peminjaman?->user?->name ?? '-' }}
                                </td>

                                <td class="p-3 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d/m/Y') }}
                                </td>

                                <td class="p-3">
                                    @foreach($item->details as $detail)
                                        <div>
                                            {{ $detail->detailPeminjaman?->alat?->name ?? 'Alat tidak tersedia' }}
                                            ({{ $detail->jumlah_kembali }})
                                        </div>
                                    @endforeach
                                </td>

                                <td class="p-3">
                                    {{ $item->petugasPenerima?->name ?? '-' }}
                                </td>

                                <td class="p-3">
                                    {{ ucfirst($item->status) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-6 text-center text-gray-500">
                                    Belum ada data pengembalian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                @endif
            </table>
        </div>

        <div class="mt-5">
            {{ $laporan->links() }}
        </div>
    </x-filament::section>

    <style>
        @media print {
            .fi-sidebar,
            .fi-topbar,
            .fi-breadcrumbs,
            button,
            input,
            nav {
                display: none !important;
            }

            .fi-main {
                padding: 0 !important;
            }

            body {
                background: white !important;
            }

            table {
                width: 100% !important;
                font-size: 10px !important;
            }

            th, td {
                padding: 6px !important;
            }

            @page {
                size: landscape;
                margin: 12mm;
            }
        }
    </style>

</x-filament-panels::page>
