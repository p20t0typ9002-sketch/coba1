<x-guest-layout>
    <div>
        <p class="text-sm font-medium text-blue-600">SELAMAT DATANG KEMBALI</p>

        <h1 class="mt-3 text-3xl font-bold tracking-tight text-slate-900">
            Masuk ke akunmu
        </h1>

        <p class="mt-2 text-sm text-slate-500">
            Masuk untuk mulai meminjam dan memantau ketersediaan alat.
        </p>
    </div>

    @if (session('status'))
        <div class="mt-6 rounded-xl bg-green-50 p-4 text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
        @csrf

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
                autofocus
                autocomplete="username"
                placeholder="nama@email.com"
                class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-3.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
            >
        </div>

        <div>
            <div class="mb-2 flex items-center justify-between">
                <label for="password" class="block text-sm font-medium text-slate-700">
                    Password
                </label>

                <a href="{{ route('password.request') }}"
                   class="text-xs font-medium text-blue-600 hover:text-blue-700">
                    Lupa password?
                </a>
            </div>

            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Masukkan password"
                class="w-full rounded-xl border border-slate-200 bg-white/80 px-4 py-3.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
            >
        </div>

        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input
                type="checkbox"
                name="remember"
                class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
            >
            Ingat saya
        </label>

        <button
            type="submit"
            class="w-full rounded-xl bg-blue-600 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700"
        >
            Masuk ke Akun
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-500">
        Belum memiliki akun?
        <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-700">
            Daftar sekarang
        </a>
    </p>
</x-guest-layout>
