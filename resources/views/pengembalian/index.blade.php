
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">

    <div class="mx-auto max-w-6xl px-4 py-10">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold">Riwayat Peminjaman</h1>
                <p class="mt-2 text-slate-500">
                    Lihat transaksi dan ajukan pengembalian alat.
                </p>
            </div>

            <a href="{{ route('home') }}"
               class="rounded-lg border border-slate-300 bg-white px-4 py-2 hover:bg-slate-50">
                Kembali ke Beranda
            </a>
        </div>

        @if (session('success'))
            <div class="mb-5 rounded-lg bg-green-100 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-5 rounded-lg bg-red-100 p-4 text-red-800">
                {{ session('error') }}
            </div>
        @endif

        @forelse ($peminjamans as $peminjaman)
            @php
                $adaPengajuan = $peminjaman->pengembalians
                    ->contains('status', 'diajukan');
            @endphp

            <div class="mb-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-4 border-b p-5">
                    <div>
                        <p class="text-sm text-slate-500">Kode Peminjaman</p>
                        <h2 class="text-lg font-bold">
                            {{ $peminjaman->pinjam_code }}
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Tanggal pinjam:
                            {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d M Y') }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <span class="rounded-full px-3 py-1 text-sm font-semibold
                            {{ $peminjaman->status === 'dipinjam' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $peminjaman->status === 'selesai' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $peminjaman->status === 'ditolak' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $peminjaman->status === 'menunggu' ? 'bg-yellow-100 text-yellow-700' : '' }}">
                            {{ ucfirst($peminjaman->status) }}
                        </span>

                        @if ($peminjaman->status === 'dipinjam' && ! $adaPengajuan)
                            <a href="{{ route('pengembalian.create', $peminjaman) }}"
                               class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                Ajukan Pengembalian
                            </a>
                        @elseif ($adaPengajuan)
                            <span class="rounded-lg bg-yellow-100 px-4 py-2 text-sm font-semibold text-yellow-800">
                                Menunggu Petugas
                            </span>
                        @endif
                    </div>
                </div>

                <div class="p-5">
                    <h3 class="mb-3 font-semibold">Daftar Alat</h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-slate-600">
                                <tr>
                                    <th class="p-3">Nama Alat</th>
                                    <th class="p-3">Jumlah Dipinjam</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($peminjaman->details as $detail)
                                    <tr class="border-b last:border-0">
                                        <td class="p-3">
                                            {{ $detail->alat?->name ?? 'Alat tidak tersedia' }}
                                        </td>
                                        <td class="p-3">
                                            {{ $detail->jumlah }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-slate-200 bg-white p-10 text-center">
                <h2 class="text-lg font-semibold">Belum Ada Peminjaman</h2>
                <p class="mt-2 text-slate-500">
                    Kamu belum memiliki transaksi peminjaman alat.
                </p>
            </div>
        @endforelse
    </div>

</body>
</html>
