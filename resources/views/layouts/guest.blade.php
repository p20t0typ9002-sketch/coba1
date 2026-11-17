<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Autentikasi' }} | Indotravi</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f5f7fa] font-sans text-slate-800 antialiased">

    <main class="grid min-h-screen lg:grid-cols-2">

        <!-- Bagian formulir -->
        <section class="flex items-center justify-center px-6 py-10 sm:px-12 lg:px-16">
            <div class="w-full max-w-md">

                <a href="/" class="mb-12 inline-flex items-center gap-2">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-xl text-white">
                        ✦
                    </span>
                    <span class="text-2xl font-bold tracking-tight text-slate-900">
                        My<span class="text-blue-600">pinjam.</span>
                    </span>
                </a>

                {{ $slot }}

                <p class="mt-12 text-center text-xs text-slate-400">
                    © {{ date('Y') }} Mypinjam. Sistem Manajemen Peminjaman Alat.
                </p>

            </div>
        </section>

        <!-- Bagian gambar -->
        <section class="relative hidden overflow-hidden bg-slate-900 lg:block">
            <img
                src="{{ asset('images/login.jpg') }}"
                alt="Pemandangan destinasi wisata Indonesia"
                class="absolute inset-0 h-full w-full object-cover"
            >

            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-900/10 to-slate-900/20"></div>

            <div class="absolute bottom-0 left-0 max-w-xl p-16 text-white">
                <span class="mb-5 inline-block rounded-full border border-white/30 bg-white/10 px-4 py-2 text-xs font-medium tracking-wider backdrop-blur-md">
                    SISTEM PEMINJAMAN DIGITAL
                </span>

                <h2 class="text-4xl font-semibold leading-tight xl:text-5xl">
                    Pinjam alat kebutuhanmu tanpa ribet.
                </h2>

                <p class="mt-5 max-w-md text-base leading-relaxed text-white/80">
                    Cek ketersediaan stok secara real-time, ajukan peminjaman dalam hitungan detik, 
                    dan pantau jadwal pengembalian dari satu tempat.
                </p>
            </div>
        </section>

    </main>

</body>
</html>
