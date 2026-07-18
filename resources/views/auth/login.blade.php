<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kasir Tembakau</title>
    @if (app()->environment('local') || app()->environment('development'))
      @vite(['resources/css/app.css'])
    @endif
    <style>
      body {
        font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: #eef5f0;
      }
      .form-container {
        max-width: 460px;
      }
      .brand-panel {
        background: linear-gradient(180deg, #27472b 0%, #1b3a24 100%);
      }
      @media (max-width: 768px) {
        .split-layout {
          flex-direction: column;
        }
      }
    </style>
  </head>
  <body class="min-h-screen bg-slate-50 text-slate-900">
    <div class="min-h-screen flex items-center justify-center px-4 py-8">
      <div class="w-full max-w-6xl rounded-3xl overflow-hidden shadow-2xl grid lg:grid-cols-2 bg-white">
        <div class="brand-panel hidden lg:flex flex-col justify-center px-12 py-16 text-white">
          <div class="space-y-8">
            <div>
              <p class="text-sm uppercase tracking-[0.3em] text-emerald-200">Kasir Tembakau</p>
              <h1 class="mt-4 text-4xl font-semibold">Kelola toko tembakau dengan tenang</h1>
            </div>
            <p class="max-w-md text-slate-200 leading-7">
              Sistem kasir sederhana untuk mengatur penjualan, stok, dan catatan transaksi. Cocok untuk toko tembakau dengan tampilan yang bersih dan akses mudah.
            </p>
          </div>
        </div>

        <div class="px-8 py-10 sm:px-12 sm:py-14">
          <div class="mb-8">
            <p class="text-sm text-emerald-600 font-semibold uppercase tracking-[0.25em]">Selamat datang</p>
            <h2 class="mt-4 text-3xl font-semibold text-slate-900">Masuk ke akun Anda</h2>
            <p class="mt-2 text-slate-500">Kelola penjualan dan stok toko dengan mudah.</p>
          </div>

          @if ($errors->has('login'))
            <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
              {{ $errors->first('login') }}
            </div>
          @endif

          <form method="POST" action="{{ route('login.post') }}" class="space-y-6">
            @csrf

            <div class="space-y-2">
              <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
              <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                required
                autofocus
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition hover:border-emerald-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
              >
            </div>

            <div class="space-y-2">
              <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
              <input
                id="password"
                name="password"
                type="password"
                required
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition hover:border-emerald-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
              >
            </div>

            <div class="flex items-center justify-between gap-4 text-sm text-slate-600">
              <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                Remember me
              </label>
            </div>

            <button
              type="submit"
              class="w-full rounded-2xl bg-emerald-700 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-500/15 transition hover:bg-emerald-800"
            >
              Masuk
            </button>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>
