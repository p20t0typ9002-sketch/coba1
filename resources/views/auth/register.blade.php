<x-guest-layout>
    <div>
        <p class="text-sm font-medium text-blue-600">MULAI PETUALANGANMU</p>

        <h1 class="mt-3 text-3xl font-bold tracking-tight text-slate-900">
            Buat akun baru
        </h1>

        <p class="mt-2 text-sm text-slate-500">
            Bergabunglah dan temukan berbagai destinasi wisata Indonesia.
        </p>
    </div>

    @if ($errors->any())
        <div class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <label for="name" class="mb-2 block text-sm font-medium text-slate-700">
                Nama Lengkap
            </label>

            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                placeholder="Masukkan nama lengkap"
                class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-3.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
            >
        </div>

        <div>
            <label for="email" class="mb-2 block text-sm font-medium text-slate-700">
                Alamat Email
            </label>

            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="username"
                placeholder="nama@email.com"
                class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-3.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
            >
        </div>

        <div>
            <label for="password" class="mb-2 block text-sm font-medium text-slate-700">
                Password
            </label>

            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                placeholder="Minimal 8 karakter"
                class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-3.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
            >
        </div>

        <div>
            <label for="password_confirmation" class="mb-2 block text-sm font-medium text-slate-700">
                Konfirmasi Password
            </label>

            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="Ulangi password"
                class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-3.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
            >
        </div>

        <button
            type="submit"
            class="w-full rounded-xl bg-blue-600 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700"
        >
            Buat Akun
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-500">
        Sudah memiliki akun?
        <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700">
            Masuk di sini
        </a>
    </p>
</x-guest-layout>
