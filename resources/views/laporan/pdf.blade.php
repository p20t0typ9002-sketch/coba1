
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #222;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        .info {
            text-align: center;
            margin-bottom: 20px;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #999;
            padding: 7px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #e8eef7;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
        }
    </style>
</head>
<body>

    <h2>
        LAPORAN {{ strtoupper($jenis) }}
    </h2>

    <div class="info">
        Periode:
        {{ $dari ? \Carbon\Carbon::parse($dari)->format('d/m/Y') : 'Semua tanggal' }}
        -
        {{ $sampai ? \Carbon\Carbon::parse($sampai)->format('d/m/Y') : 'Sekarang' }}
    </div>

    <table>
        <thead>
            @if ($jenis === 'peminjaman')
                <tr>
                    <th>No.</th>
                    <th>Kode Peminjaman</th>
                    <th>Peminjam</th>
                    <th>Tanggal Pinjam</th>
                    <th>Rencana Kembali</th>
                    <th>Daftar Alat</th>
                    <th>Petugas</th>
                    <th>Status</th>
                </tr>
            @else
                <tr>
                    <th>No.</th>
                    <th>Kode Peminjaman</th>
                    <th>Peminjam</th>
                    <th>Tanggal Kembali</th>
                    <th>Daftar Alat</th>
                    <th>Petugas</th>
                    <th>Status</th>
                </tr>
            @endif
        </thead>

        <tbody>
            @forelse ($baris as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>

                    @foreach ($item as $nilai)
                        <td>{{ $nilai }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $jenis === 'peminjaman' ? 8 : 7 }}"
                        style="text-align: center;">
                        Tidak ada data transaksi.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>
