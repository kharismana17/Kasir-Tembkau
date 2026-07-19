@extends('layouts.admin')

@section('title', 'POS Kasir - Kasir Tembakau')

@section('content')

  <div class="space-y-8">

```
{{-- HEADER --}}
<div>
  <div class="flex items-center gap-3">

    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#1B1B18] text-[#D99A3D] shadow-sm">
      <svg
        class="h-5 w-5"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="1.8"
          d="M3 3h18v18H3V3zm4 4h10v3H7V7zm0 6h3v3H7v-3zm5 0h5v3h-5v-3z"
        />
      </svg>
    </div>

    <div>
      <p class="text-xs font-bold uppercase tracking-[0.25em] text-[#B47727]">
        Point of Sale
      </p>

      <h1 class="mt-1 text-2xl font-bold tracking-tight text-[#17201C] sm:text-3xl">
        Kasir
      </h1>
    </div>

  </div>

  <p class="mt-3 text-sm text-slate-500">
    Pilih produk untuk ditambahkan ke keranjang transaksi.
  </p>
</div>


{{-- FLASH MESSAGE --}}
@if (session('success'))
  <div class="rounded-2xl border border-[#D9C19D] bg-[#F4EFE6] p-4 text-sm font-medium text-[#8A5B1E]">
    {{ session('success') }}
  </div>
@endif

@if (session('error'))
  <div class="rounded-2xl border border-[#D9C19D] bg-[#F4EFE6] p-4 text-sm font-medium text-[#8A5B1E]">
    {{ session('error') }}
  </div>
@endif


{{-- BARCODE SCANNER --}}
<div class="rounded-3xl border border-[#D9C19D] bg-[#F4EFE6] p-6 shadow-sm">

  <div class="flex items-start gap-4">

    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#1B1B18] text-[#D99A3D]">

      <svg
        class="h-5 w-5"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="1.8"
          d="M4 6v12M7 6v12M10 6v12M14 6v12M17 6v12M20 6v12"
        />
      </svg>

    </div>

    <div>

      <h2 class="text-lg font-bold text-[#17201C]">
        Scan Barcode
      </h2>

      <p class="mt-2 text-sm text-slate-600">
        Gunakan barcode scanner atau ketik barcode secara manual untuk menambahkan produk ke keranjang.
      </p>

    </div>

  </div>


  <div class="mt-5">

    <label
      for="barcode_input"
      class="mb-2 block text-sm font-bold text-[#17201C]"
    >
      Barcode Produk
    </label>

    <input
      id="barcode_input"
      type="text"
      placeholder="Scan barcode atau ketik dan tekan Enter..."
      class="w-full rounded-2xl border border-[#D8D3C9] bg-white px-4 py-3 text-sm text-[#17201C] outline-none transition hover:border-[#B8B1A4] focus:border-[#1B1B18] focus:ring-4 focus:ring-[#1B1B18]/10"
      autocomplete="off"
      autofocus
    >

  </div>

  <div
    id="barcode_message"
    class="mt-3 hidden rounded-2xl p-3 text-sm"
  ></div>

</div>


{{-- MAIN CONTENT --}}
<div class="grid gap-6 xl:grid-cols-3">


  {{-- PRODUK --}}
  <section class="xl:col-span-2">

    <div class="mb-5 flex items-center justify-between">

      <div>

        <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#B47727]">
          Product Catalog
        </p>

        <h2 class="mt-1 text-xl font-bold text-[#17201C]">
          Produk
        </h2>

      </div>

      <span class="rounded-xl bg-[#F4EFE6] px-3 py-2 text-xs font-bold text-[#8A5B1E]">
        {{ $products->count() }} PRODUK
      </span>

    </div>


    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">

      @forelse ($products as $product)

        <div class="rounded-3xl border border-[#DED9D0] bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">

          <div class="mb-5 flex items-start justify-between gap-3">

            <div class="min-w-0">

              <h2 class="truncate font-bold text-[#17201C]">
                {{ $product->name }}
              </h2>

              <p class="mt-1 text-sm text-slate-500">
                {{ $product->sku }}
              </p>

            </div>

            <span class="shrink-0 rounded-full bg-[#F4EFE6] px-3 py-1 text-xs font-bold text-[#8A5B1E]">
              {{ $product->stock }} {{ $product->stockUnit() }}
            </span>

          </div>


          <div class="border-t border-[#EEEAE2] pt-4">

            <p class="text-xs font-bold uppercase tracking-[0.15em] text-slate-400">
              Harga
            </p>

            <p class="mt-1 text-lg font-bold text-[#8A5B1E]">
              Rp {{ number_format($product->sell_price, 0, ',', '.') }}
              <span class="text-xs font-semibold text-slate-500">
                / {{ $product->unit }}
              </span>
            </p>

          </div>


          <form
            method="POST"
            action="{{ route('pos.cart.add', $product->id) }}"
          >

            @csrf

            <button
              type="submit"
              class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-[#1B1B18] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#302F2A] hover:shadow-md"
            >

              <svg
                class="h-4 w-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 4v16m8-8H4"
                />
              </svg>

              Tambah ke Keranjang

            </button>

          </form>

        </div>

      @empty

        <div class="rounded-3xl border border-[#DED9D0] bg-white p-8 text-center sm:col-span-2 lg:col-span-3">

          <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#F4EFE6] text-[#B47727]">

            <svg
              class="h-7 w-7"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="1.8"
                d="M20 13V6a2 2 0 00-2-2h-3.5a2 2 0 01-1.4-.6l-.7-.7a2 2 0 00-1.4-.6H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0H4"
              />
            </svg>

          </div>

          <p class="mt-4 text-sm text-slate-500">
            Belum ada produk aktif dengan stok tersedia.
          </p>

        </div>

      @endforelse

    </div>

  </section>


  {{-- KERANJANG --}}
  <section class="rounded-3xl border border-[#DED9D0] bg-white p-6 shadow-sm">

    <div class="flex items-center justify-between">

      <div>

        <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#B47727]">
          Current Order
        </p>

        <h2 class="mt-1 text-xl font-bold text-[#17201C]">
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
            class="rounded-xl bg-[#F4EFE6] px-3 py-2 text-xs font-bold text-[#8A5B1E] transition hover:bg-[#E8DFD0]"
          >
            Kosongkan
          </button>

        </form>

      @endif

    </div>


    <div class="mt-6 space-y-4">

      @forelse ($cart as $item)

        <div class="rounded-2xl border border-[#DED9D0] bg-[#FAF9F6] p-4">

          <div class="flex items-start justify-between gap-3">

            <div class="min-w-0">

              <p class="truncate font-bold text-[#17201C]">
                {{ $item['name'] }}
              </p>

              <p class="mt-1 text-sm text-slate-500">
                Rp {{ number_format($item['price'], 0, ',', '.') }}
              </p>

            </div>


            <form
              method="POST"
              action="{{ route('pos.cart.remove', ['product' => $item['product_id']]) }}"
            >

              @csrf
              @method('DELETE')

              <button
                type="submit"
                class="text-xs font-bold text-[#8A5B1E] transition hover:text-[#B47727]"
              >
                Hapus
              </button>

            </form>

          </div>


          <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <div class="flex flex-wrap items-center gap-2">

              {{-- MINUS --}}
              <form
                method="POST"
                action="{{ route('pos.cart.update',['product' => $item['product_id']]) }}"
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
                  class="h-10 w-10 rounded-xl border border-[#D8D3C9] bg-white text-lg font-bold text-[#17201C] transition hover:bg-[#F4EFE6] disabled:cursor-not-allowed disabled:opacity-50"
                  @if(($item['is_tembakau'] ?? false) ? $item['qty'] <= 0.5 : $item['qty'] <= 1) disabled @endif
                >
                  −
                </button>

              </form>


              {{-- PLUS --}}
              <form
                method="POST"
                action="{{ route('pos.cart.update', ['product' => $item['product_id']]) }}"
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
                  class="h-10 w-10 rounded-xl border border-[#D8D3C9] bg-white text-lg font-bold text-[#17201C] transition hover:bg-[#F4EFE6]"
                >
                  +
                </button>

              </form>


              {{-- UPDATE QTY --}}
              <form
                method="POST"
                action="{{ route('pos.cart.update', ['product' => $item['product_id']]) }}"
                class="flex items-center gap-2"
              >

                @csrf
                @method('PATCH')

                <label
                  class="sr-only"
                  for="qty-{{ $item['product_id'] }}"
                >
                  Jumlah
                </label>

                <input
                  id="qty-{{ $item['product_id'] }}"
                  type="number"
                  name="qty"
                  value="{{ $item['qty'] }}"
                  min="{{ $item['unit'] === 'ons' ? '0.01' : '1' }}"
                  step="{{ $item['unit'] === 'ons' ? '0.01' : '1' }}"
                  inputmode="{{ $item['unit'] === 'ons' ? 'decimal' : 'numeric' }}"
                  class="w-20 rounded-xl border border-[#D8D3C9] bg-white px-3 py-2 text-sm text-[#17201C] outline-none focus:border-[#1B1B18] focus:ring-4 focus:ring-[#1B1B18]/10"
                >

                <span class="text-sm text-slate-600">
                  {{ $item['unit'] ?? '' }}
                </span>

                <button
                  type="submit"
                  class="rounded-xl bg-[#F4EFE6] px-3 py-2 text-xs font-bold text-[#8A5B1E] transition hover:bg-[#E8DFD0]"
                >
                  Update
                </button>

              </form>

            </div>


            <p class="shrink-0 font-bold text-[#8A5B1E]">
              Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}
            </p>

          </div>

        </div>

      @empty

        <div class="rounded-2xl bg-[#FAF9F6] p-6 text-center">

          <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-[#F4EFE6] text-[#B47727]">

            <svg
              class="h-6 w-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="1.8"
                d="M3 3h2l2.4 12.2a2 2 0 002 1.6h7.8a2 2 0 001.9-1.4L21 8H6"
              />
            </svg>

          </div>

          <p class="mt-3 text-sm text-slate-500">
            Keranjang masih kosong.
          </p>

        </div>

      @endforelse

    </div>


    {{-- CHECKOUT --}}
    <div class="mt-6 border-t border-[#EEEAE2] pt-6">

      <div class="flex items-center justify-between">

        <span class="text-sm font-bold text-slate-500">
          Subtotal
        </span>

        <span class="text-xl font-bold text-[#8A5B1E]">
          <span id="subtotal">
            Rp {{ number_format($subtotal, 0, ',', '.') }}
          </span>
        </span>

      </div>


      @if (count($cart) > 0)

        <form
          method="POST"
          action="{{ route('pos.checkout') }}"
          class="mt-6 space-y-4"
        >

          @csrf


          {{-- PAYMENT METHOD --}}
          <div>

            <label
              for="payment_method_id"
              class="mb-2 block text-sm font-bold text-[#17201C]"
            >
              Metode Pembayaran
            </label>

            <select
              id="payment_method_id"
              name="payment_method_id"
              required
              class="w-full rounded-2xl border border-[#D8D3C9] bg-[#FAF9F6] px-4 py-3 text-sm text-[#17201C] outline-none transition focus:border-[#1B1B18] focus:bg-white focus:ring-4 focus:ring-[#1B1B18]/10"
            >

              <option value="">
                Pilih metode pembayaran
              </option>

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
              <p class="mt-2 text-sm font-medium text-[#8A5B1E]">
                {{ $message }}
              </p>
            @enderror

          </div>


          {{-- PAID AMOUNT --}}
          <div>

            <label
              for="paid_amount"
              class="mb-2 block text-sm font-bold text-[#17201C]"
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
              class="w-full rounded-2xl border border-[#D8D3C9] bg-[#FAF9F6] px-4 py-3 text-sm text-[#17201C] outline-none transition focus:border-[#1B1B18] focus:bg-white focus:ring-4 focus:ring-[#1B1B18]/10"
              placeholder="Masukkan uang pembayaran"
            >

            @error('paid_amount')
              <p class="mt-2 text-sm font-medium text-[#8A5B1E]">
                {{ $message }}
              </p>
            @enderror

          </div>


          {{-- SUBMIT --}}
          <button
            type="submit"
            class="w-full rounded-2xl bg-[#1B1B18] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#302F2A] hover:shadow-md"
          >
            Simpan Transaksi
          </button>

        </form>

      @else

        <button
          type="button"
          disabled
          class="mt-6 w-full cursor-not-allowed rounded-2xl bg-[#EEEAE2] px-4 py-3 text-sm font-bold text-slate-400"
        >
          Checkout
        </button>

      @endif

    </div>

  </section>

</div>
```

  </div>

{{-- BARCODE SCRIPT --}}

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

        const response = await fetch(
          `{{ route('pos.scan-barcode') }}?barcode=${encodeURIComponent(barcode)}`
        );

        const data = await response.json();


        if (!data.success) {

          showMessage(
            data.message || 'Terjadi kesalahan.',
            'error'
          );

          barcodeInput.value = '';

          barcodeInput.focus();

          return;

        }


        const product = data.product;


        // TEMBAKAU
        if (product.is_tembakau) {

          let grams = prompt(
            'Masukkan berat (gram):',
            '100'
          );


          if (grams === null) {

            barcodeInput.value = '';

            barcodeInput.focus();

            return;

          }


          grams = grams.trim();


          if (
            !grams ||
            isNaN(grams) ||
            Number(grams) <= 0
          ) {

            showMessage(
              'Berat tidak valid.',
              'error'
            );

            barcodeInput.value = '';

            barcodeInput.focus();

            return;

          }


          const form = document.createElement('form');

          form.method = 'POST';

          form.action = `{{ url('/pos/cart') }}/${product.id}`;

          form.innerHTML =
            `@csrf` +
            `\n<input type="hidden" name="qty" value="${Number(grams)}">`;


          document.body.appendChild(form);

          form.submit();

          document.body.removeChild(form);

          return;

        }


        // NON-TEMBAKAU
        const form = document.createElement('form');

        form.method = 'POST';

        form.action = `{{ url('/pos/cart') }}/${product.id}`;

        form.innerHTML = `@csrf`;


        document.body.appendChild(form);

        form.submit();

        document.body.removeChild(form);


      } catch (error) {

        console.error('Error:', error);

        showMessage(
          'Terjadi kesalahan saat memproses barcode.',
          'error'
        );

        barcodeInput.value = '';

        barcodeInput.focus();

      }

    });


    function showMessage(text, type) {

      barcodeMessage.classList.remove(
        'hidden',
        'border-[#D9C19D]',
        'bg-[#F4EFE6]',
        'text-[#8A5B1E]'
      );


      barcodeMessage.classList.add(
        'border',
        'border-[#D9C19D]',
        'bg-[#F4EFE6]',
        'text-[#8A5B1E]'
      );


      barcodeMessage.textContent = text;


      setTimeout(() => {

        barcodeMessage.classList.add('hidden');

      }, 3000);

    }


    window.addEventListener('load', () => {

      barcodeInput.focus();

    });

  </script>

@endsection
