@extends('layouts.admin')

@section('title', 'POS Kasir - Kasir Tembakau')

@section('content')
  <div class="space-y-8">

    <div>
      <p class="text-sm uppercase tracking-[0.25em] text-emerald-600">
        Point of Sale
      </p>

      <h1 class="mt-2 text-3xl font-semibold text-slate-900">
        Kasir
      </h1>

      <p class="mt-2 text-slate-500">
        Pilih produk untuk ditambahkan ke keranjang transaksi.
      </p>
    </div>

    @if (session('success'))
      <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
        {{ session('success') }}
      </div>
    @endif

    @if (session('error'))
      <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
        {{ session('error') }}
      </div>
    @endif

    {{-- BARCODE SCANNER --}}
    <div class="rounded-3xl border border-emerald-200 bg-emerald-50 p-6 shadow-sm">
      <div>
        <h2 class="text-lg font-semibold text-slate-900">
          Scan Barcode
        </h2>

        <p class="mt-2 text-sm text-slate-600">
          Gunakan barcode scanner atau ketik barcode secara manual untuk menambahkan produk ke keranjang.
        </p>
      </div>

      <div class="mt-4">
        <label
          for="barcode_input"
          class="mb-2 block text-sm font-medium text-slate-700"
        >
          Barcode Produk
        </label>

        <input
          id="barcode_input"
          type="text"
          placeholder="Scan barcode atau ketik dan tekan Enter..."
          class="w-full rounded-2xl border border-emerald-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
          autocomplete="off"
          autofocus
        >
      </div>

      <div id="barcode_message" class="mt-3 hidden rounded-2xl p-3 text-sm"></div>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">

      {{-- PRODUK --}}
      <section class="xl:col-span-2">

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">

          @forelse ($products as $product)
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">

              <div class="mb-5 flex items-start justify-between gap-3">
                <div>
                  <h2 class="font-semibold text-slate-900">
                    {{ $product->name }}
                  </h2>

                  <p class="mt-1 text-sm text-slate-500">
                    {{ $product->sku }}
                  </p>
                </div>

                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                    {{ $product->stock }} {{ $product->stockUnit() }}
                  </span>
                </div>

                <form method="POST" action="{{ route('pos.cart.add', $product->id) }}">
                  @csrf

                  <button
                    type="submit"
                    class="mt-4 w-full rounded-2xl bg-emerald-700 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-800"
                  >
                    Rp {{ number_format($product->sell_price, 0, ',', '.') }} / {{ $product->unit }} + Tambah
                  </button>
                </form>

            </div>
          @empty
            <div class="rounded-3xl border border-slate-200 bg-white p-8 text-center sm:col-span-2 lg:col-span-3">
              <p class="text-sm text-slate-500">
                Belum ada produk aktif dengan stok tersedia.
              </p>
            </div>
          @endforelse

        </div>

      </section>

      {{-- KERANJANG --}}
      <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-lg font-semibold text-slate-900">
              Keranjang
            </h2>

            <p class="mt-1 text-sm text-slate-500">
              Produk yang dipilih
            </p>
          </div>

          @if (count($cart) > 0)
            <form
              method="POST"
              action="{{ route('pos.cart.clear') }}"
            >
              @csrf
              @method('DELETE')

              <button
                type="submit"
                class="text-sm font-semibold text-rose-600 hover:text-rose-700"
              >
                Kosongkan
              </button>
            </form>
          @endif
        </div>

        <div class="mt-6 space-y-4">

          @forelse ($cart as $item)
            <div class="rounded-2xl border border-slate-200 p-4">

              <div class="flex items-start justify-between gap-3">

                <div>
                  <p class="font-semibold text-slate-900">
                    {{ $item['name'] }}
                  </p>

                  <p class="mt-1 text-sm text-slate-500">
                    Rp {{ number_format($item['price'], 0, ',', '.') }}
                  </p>
                </div>

                <form
                  method="POST"
                  action="{{ route('pos.cart.remove', $item['product_id']) }}"
                >
                  @csrf
                  @method('DELETE')

                  <button
                    type="submit"
                    class="text-sm text-rose-600 hover:text-rose-700"
                  >
                    Hapus
                  </button>
                </form>

              </div>

              <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                <div class="flex items-center gap-2">
                  <form
                    method="POST"
                    action="{{ route('pos.cart.update', $item['product_id']) }}"
                  >
                    @csrf
                    @method('PATCH')

                    <input
                      type="hidden"
                      name="qty"
                      value="{{ $item['qty'] - (($item['is_tembakau'] ?? false) ? 0.5 : 1) }}"
                    >
                    <button
                      type="submit"
                      class="h-10 w-10 rounded-xl border border-slate-300 bg-slate-100 text-slate-700 hover:bg-slate-200 disabled:cursor-not-allowed disabled:opacity-50"
                      @if(($item['is_tembakau'] ?? false) ? $item['qty'] <= 0.5 : $item['qty'] <= 1) disabled @endif
                    >
                      -
                    </button>
                  </form>

                  <form
                    method="POST"
                    action="{{ route('pos.cart.update', $item['product_id']) }}"
                  >
                    @csrf
                    @method('PATCH')

                    <input
                      type="hidden"
                      name="qty"
                      value="{{ $item['qty'] + (($item['is_tembakau'] ?? false) ? 0.5 : 1) }}"
                    >
                    <button
                      type="submit"
                      class="h-10 w-10 rounded-xl border border-slate-300 bg-slate-100 text-slate-700 hover:bg-slate-200"
                    >
                      +
                    </button>
                  </form>

                  <form
                    method="POST"
                    action="{{ route('pos.cart.update', $item['product_id']) }}"
                    class="flex items-center gap-2"
                  >
                    @csrf
                    @method('PATCH')

                    <label class="sr-only" for="qty-{{ $item['product_id'] }}">Jumlah</label>
                    <input
                      id="qty-{{ $item['product_id'] }}"
                      type="number"
                      name="qty"
                      value="{{ $item['qty'] }}"
                      min="{{ $item['unit'] === 'ons' ? '0.01' : '1' }}"
                      step="{{ $item['unit'] === 'ons' ? '0.01' : '1' }}"
                      inputmode="{{ $item['unit'] === 'ons' ? 'decimal' : 'numeric' }}"
                      class="w-24 rounded-xl border border-slate-300 px-3 py-2 text-sm"
                    >

                    <span class="text-sm text-slate-600">
                      {{ $item['unit'] ?? '' }}
                    </span>

                    <button
                      type="submit"
                      class="rounded-xl bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200"
                    >
                      Update
                    </button>
                  </form>
                </div>

                <p class="font-semibold text-emerald-700">
                  Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}
                </p>

              </div>

            </div>
          @empty
            <div class="rounded-2xl bg-slate-50 p-6 text-center">
              <p class="text-sm text-slate-500">
                Keranjang masih kosong.
              </p>
            </div>
          @endforelse

        </div>

        <div class="mt-6 border-t border-slate-200 pt-6">

          <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-slate-500">
              Subtotal
            </span>

            <span class="text-xl font-semibold text-slate-900">
              <p id="subtotal">Rp {{ number_format($subtotal, 0, ',', '.') }}</p>
            </span>
          </div>

          @if (count($cart) > 0)
            <form
                method="POST"
                action="{{ route('pos.checkout') }}"
                class="mt-6 space-y-4"
            >
                @csrf

                <div>
                <label
                    for="payment_method_id"
                    class="mb-2 block text-sm font-medium text-slate-700"
                >
                    Metode Pembayaran
                </label>

                <select
                    id="payment_method_id"
                    name="payment_method_id"
                    required
                    class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                >
                    <option value="">Pilih metode pembayaran</option>

                    @foreach ($paymentMethods as $paymentMethod)
                    <option
                        value="{{ $paymentMethod->id }}"
                        {{ old('payment_method_id') == $paymentMethod->id ? 'selected' : '' }}
                    >
                        {{ $paymentMethod->name }}
                    </option>
                    @endforeach
                </select>

                @error('payment_method_id')
                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror
                </div>

                <div>
                <label
                    for="paid_amount"
                    class="mb-2 block text-sm font-medium text-slate-700"
                >
                    Uang Dibayar
                </label>

                <input
                    id="paid_amount"
                    name="paid_amount"
                    type="number"
                    min="0"
                    value="{{ old('paid_amount') }}"
                    required
                    class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                    placeholder="Masukkan uang pembayaran"
                >

                @error('paid_amount')
                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror
                </div>

                <button
                type="submit"
                class="w-full rounded-2xl bg-emerald-700 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-800"
                >
                Simpan Transaksi
                </button>
            </form>
            @else
            <button
                type="button"
                disabled
                class="mt-6 w-full cursor-not-allowed rounded-2xl bg-slate-200 px-4 py-3 text-sm font-semibold text-slate-500"
            >
                Checkout
            </button>
            @endif

        </div>

      </section>

    </div>

  </div>

  <script>
    const barcodeInput = document.getElementById('barcode_input');
    const barcodeMessage = document.getElementById('barcode_message');

    barcodeInput.addEventListener('keypress', async (e) => {
      if (e.key !== 'Enter') return;

      e.preventDefault();

      const barcode = barcodeInput.value.trim();

      if (!barcode) {
        showMessage('Barcode tidak boleh kosong.', 'error');
        return;
      }

      try {
        const response = await fetch(`{{ route('pos.scan-barcode') }}?barcode=${encodeURIComponent(barcode)}`);
        const data = await response.json();

        if (!data.success) {
          showMessage(data.message || 'Terjadi kesalahan.', 'error');
          barcodeInput.value = '';
          barcodeInput.focus();
          return;
        }

        // Product found, add to cart
        const product = data.product;

        // Submit form to add to cart
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('pos.cart.add', ':id') }}`.replace(':id', product.id);
        form.innerHTML = `@csrf`;

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);

        // The page will reload, so the input will be cleared and focused again after redirect

      } catch (error) {
        console.error('Error:', error);
        showMessage('Terjadi kesalahan saat memproses barcode.', 'error');
        barcodeInput.value = '';
        barcodeInput.focus();
      }
    });

    function showMessage(text, type) {
      barcodeMessage.classList.remove('hidden', 'border-emerald-200', 'bg-emerald-50', 'text-emerald-700', 'border-rose-200', 'bg-rose-50', 'text-rose-700');

      if (type === 'success') {
        barcodeMessage.classList.add('border-emerald-200', 'bg-emerald-50', 'text-emerald-700', 'border');
      } else {
        barcodeMessage.classList.add('border-rose-200', 'bg-rose-50', 'text-rose-700', 'border');
      }

      barcodeMessage.textContent = text;

      setTimeout(() => {
        barcodeMessage.classList.add('hidden');
      }, 3000);
    }

    // Keep focus on input if coming from redirect
    window.addEventListener('load', () => {
      barcodeInput.focus();
    });
  </script>
@endsection