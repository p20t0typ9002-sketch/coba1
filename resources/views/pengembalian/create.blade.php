
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Pengembalian</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">

    <div class="mx-auto max-w-4xl px-4 py-10">
        <div class="mb-6">
            <a href="{{ route('pengembalian.index') }}"
               class="text-sm font-medium text-blue-600 hover:underline">
                &larr; Kembali ke Riwayat
            </a>

            <h1 class="mt-4 text-3xl font-bold">Ajukan Pengembalian</h1>
            <p class="mt-2 text-slate-500">
                Peminjaman: {{ $peminjaman->pinjam_code }}
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-5 rounded-lg bg-red-100 p-4 text-red-800">
                <p class="mb-2 font-semibold">Periksa kembali data berikut:</p>
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('pengembalian.store', $peminjaman) }}"
              method="POST"
              class="space-y-6">
            @csrf

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="mb-5 text-lg font-bold">Informasi Pengembalian</h2>

                <label class="mb-2 block text-sm font-medium">
                    Tanggal Pengembalian
                </label>
                <input type="date"
                       name="tanggal_kembali"
                       value="{{ old('tanggal_kembali', now()->toDateString()) }}"
                       required
                       class="mb-5 w-full rounded-lg border border-slate-300 px-3 py-2">

                <label class="mb-2 block text-sm font-medium">
                    Catatan Umum
                </label>
                <textarea name="catatan"
                          rows="3"
                          class="w-full rounded-lg border border-slate-300 px-3 py-2"
                          placeholder="Catatan tambahan (opsional)">{{ old('catatan') }}</textarea>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="mb-2 text-lg font-bold">Daftar Alat</h2>
                <p class="mb-5 text-sm text-slate-500">
                    Isi jumlah yang ingin dikembalikan. Masukkan 0 jika alat belum dikembalikan.
                </p>

                @foreach ($details as $detail)
                    <div class="mb-5 rounded-lg border border-slate-200 p-4 last:mb-0">
                        <h3 class="font-semibold">
                            {{ $detail->alat?->name ?? 'Alat tidak tersedia' }}
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Dipinjam: {{ $detail->jumlah }} |
                            Belum dikembalikan: {{ $detail->sisa_pengembalian }}
                        </p>

                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-medium">
                                    Jumlah Dikembalikan
                                </label>
                                <input type="number"
                                       name="details[{{ $detail->id }}][jumlah_kembali]"
                                       value="{{ old("details.{$detail->id}.jumlah_kembali", 0) }}"
                                       min="0"
                                       max="{{ $detail->sisa_pengembalian }}"
                                       required
                                       class="w-full rounded-lg border border-slate-300 px-3 py-2">
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium">
                                    Kondisi Alat
                                </label>
                                <select name="details[{{ $detail->id }}][kondisi_alat]"
                                        required
                                        class="w-full rounded-lg border border-slate-300 px-3 py-2">
                                    <option value="baik">Baik</option>
                                    <option value="rusak_ringan">Rusak Ringan</option>
                                    <option value="rusak_berat">Rusak Berat</option>
                                    <option value="hilang">Hilang</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="mb-2 block text-sm font-medium">
                                Catatan Kondisi
                            </label>
                            <textarea name="details[{{ $detail->id }}][catatan]"
                                      rows="2"
                                      class="w-full rounded-lg border border-slate-300 px-3 py-2"
                                      placeholder="Catatan kondisi alat (opsional)">{{ old("details.{$detail->id}.catatan") }}</textarea>
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="submit"
                    class="w-full rounded-lg bg-blue-600 px-5 py-3 font-semibold text-white hover:bg-blue-700">
                Kirim Pengajuan Pengembalian
            </button>
        </form>
    </div>

</body>
</html>
