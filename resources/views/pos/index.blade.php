@extends('layouts.cashier')

@section('title', 'POS Kasir')

@section('content')

  @php
    $gramTotal = collect($cart)->where('is_tembakau', true)->sum('qty');
    $itemTotal = collect($cart)->where('is_tembakau', false)->sum('qty');
    $totalItems = count($cart);
  @endphp

  <div class="space-y-6">

    <section class="rounded-[28px] border border-[#E7E1D9] bg-[#FBF9F6] p-6 shadow-sm">
      <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
        <div class="max-w-2xl">
          <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-[#B47727]">Point of Sale</p>
          <h1 class="mt-2 text-3xl font-bold tracking-tight text-[#292522]">Kasir Toko Tembakau</h1>
          <p class="mt-2 text-sm leading-6 text-[#6B4F3A]">Semua produk ditambahkan melalui scan barcode. Tembakau membutuhkan input berat sebelum masuk ke keranjang.</p>
        </div>

        <div class="grid w-full gap-3 grid-cols-1 sm:grid-cols-2 lg:w-auto lg:grid-cols-2">
          <div class="rounded-2xl border border-[#E7E1D9] bg-white p-4 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Item dalam Keranjang</p>
            <p id="topSummaryItems" class="mt-2 text-2xl font-bold text-[#292522]">{{ $totalItems }}</p>
          </div>
          <div class="rounded-2xl border border-[#E7E1D9] bg-white p-4 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Subtotal</p>
            <p id="topSummarySubtotal" class="mt-2 text-2xl font-bold text-[#8A5B1E]">Rp {{ number_format($subtotal, 0, ',', '.') }}</p>
          </div>
        </div>
      </div>
    </section>

    <section class="rounded-[28px] border border-[#E7E1D9] bg-[#F4EFE6] p-6 shadow-sm">
      <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-center gap-4">
          <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#292522] text-[#C68B59]">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6v12M7 6v12M10 6v12M14 6v12M17 6v12M20 6v12" /></svg>
          </div>
          <div>
            <p class="text-sm font-bold text-[#292522]">Scan Barcode</p>
            <p class="mt-1 text-xs text-[#6B4F3A]">Scan atau ketik barcode dan tekan Enter. Jaga fokus input untuk transaksi cepat.</p>
          </div>
        </div>

        <button id="openCameraScan" type="button" class="inline-flex items-center justify-center rounded-2xl bg-[#292522] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#3A352F]">Scan dengan Kamera</button>
      </div>

      <div class="mt-6">
        <label for="barcode_input" class="mb-2 block text-sm font-semibold text-[#292522]">Barcode Produk</label>
        <div class="flex flex-col gap-3 sm:flex-row">
          <input id="barcode_input" name="barcode" type="text" placeholder="Scan barcode atau ketik lalu tekan Enter..." class="w-full rounded-2xl border border-[#D8D3C9] bg-white px-4 py-4 text-sm text-[#292522] outline-none transition duration-200 hover:border-[#B8B1A4] focus:border-[#292522] focus:ring-4 focus:ring-[#292522]/10" autocomplete="off" autofocus>
        </div>
        <div id="barcode_message" class="mt-3 hidden rounded-2xl p-3 text-sm"></div>
      </div>
    </section>

    <div class="grid gap-6 lg:grid-cols-[1.75fr_1.05fr] lg:min-h-[calc(100vh-170px)]">
      <section
      class="flex flex-col
            rounded-[28px]
            border border-[#E7E1D9]
            bg-[#FBF9F6]
            p-6
            shadow-sm
            h-[calc(100vh-180px)]">
        <div class="sticky top-0 z-10 mb-5 bg-[#FBF9F6] pb-4">
          <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-[#B47727]">Daftar Transaksi</p>
              <h2 class="mt-1 text-xl font-bold text-[#292522]">Item yang Masuk</h2>
            </div>
            <p class="text-sm text-[#6B4F3A]">Hanya daftar transaksi. Produk dari scan barcode.</p>
          </div>
        </div>

        <div id="cartItemsContainer"
          class="flex-1 overflow-y-auto pr-2 pb-4">
          @include('pos.partials.cart-items', ['cart' => $cart])
        </div>
      </section>

      <div class="space-y-6 lg:min-h-[calc(100vh-170px)] lg:flex lg:flex-col">
        <section class="rounded-[28px] border border-[#E7E1D9] bg-[#FBF9F6] p-6 shadow-sm">
          <div class="flex h-full min-h-0 flex-col">
            <div class="mb-5">
              <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-[#B47727]">Ringkasan</p>
              <h2 class="mt-1 text-xl font-bold text-[#292522]">Total Pembayaran</h2>
            </div>

            <div class="space-y-3">
              <div class="rounded-2xl bg-[#F4EFE6] p-4"><div class="flex items-center justify-between"><span class="text-sm font-semibold text-[#6B4F3A]">Jumlah Item</span><span id="summaryItems" class="text-lg font-bold text-[#292522]">{{ $totalItems }}</span></div></div>
              <div class="rounded-2xl bg-[#F4EFE6] p-4"><div class="flex items-center justify-between"><span class="text-sm font-semibold text-[#6B4F3A]">Total Gram Tembakau</span><span id="summaryGram" class="text-lg font-bold text-[#292522]">{{ $gramTotal }} gram</span></div></div>
              <div class="rounded-2xl bg-[#F4EFE6] p-4"><div class="flex items-center justify-between"><span class="text-sm font-semibold text-[#6B4F3A]">Subtotal</span><span id="summarySubtotal" class="text-lg font-bold text-[#8A5B1E]">Rp {{ number_format($subtotal, 0, ',', '.') }}</span></div></div>
              <div class="rounded-[22px] bg-[#292522] p-4 shadow-sm"><div class="flex items-center justify-between"><span class="text-sm font-semibold text-white">Grand Total</span><div class="flex items-center gap-1 text-2xl font-bold text-[#D89A5B]"><span class="text-lg">Rp</span><span id="summaryGrandTotal">{{ number_format($grandTotal, 0, ',', '.') }}</span></div></div></div>
            </div>

            <div class="mt-auto border-t border-[#E7E1D9] pt-4">
              <div class="flex flex-col gap-3 sm:flex-row">

                  <form id="saveOrderForm" action="{{ route('saved-orders.save') }}" method="POST" class="flex-1">
                      @csrf

                      <button
                          id="saveButton"
                          type="submit"
                          class="inline-flex w-full items-center justify-center rounded-2xl border px-4 py-3 text-sm font-bold transition border-[#292522] bg-white text-[#292522] hover:bg-gray-100">
                          Simpan
                      </button>

                  </form>

                  <a
                      id="openCheckoutButton"
                      href="{{ route('pos.checkout.page') }}"
                      class="inline-flex flex-1 items-center justify-center rounded-2xl px-4 py-3 text-sm font-bold text-white transition bg-[#292522] hover:bg-[#302F2A]">
                      Bayar
                  </a>

              </div>
            </div>
          </div>
        </section>

        <section class="rounded-[28px] border border-[#E7E1D9] bg-[#FBF9F6] p-6 shadow-sm">
          <div class="mb-5">
            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-[#B47727]">ORDER TERSIMPAN</p>
            <h2 class="mt-1 text-xl font-bold text-[#292522]">Order Tersimpan</h2>
          </div>

          <div id="savedOrdersContainer">
            @include('pos.partials.saved-orders', ['savedOrders' => $savedOrders])
          </div>
        </section>
      </div>
    </div>

    <div id="checkoutModal" class="fixed inset-0 z-[70] hidden items-center justify-center bg-black/60 p-0 sm:p-4">
      <div class="relative flex h-full w-full max-w-full flex-col overflow-hidden bg-white shadow-2xl sm:h-[90vh] sm:max-w-3xl lg:max-w-5xl sm:rounded-[28px]">
        <div class="sticky top-0 z-10 border-b border-[#E7E1D9] bg-white px-5 py-4 sm:px-6 sm:py-5">
          <div class="flex items-center justify-between gap-4">
            <div>
              <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-[#B47727]">Checkout</p>
              <h3 class="mt-1 text-xl font-bold text-[#292522]">Checkout Pembayaran</h3>
            </div>
            <button id="closeCheckoutModal" type="button" class="rounded-xl bg-[#F4EFE6] px-3 py-2 text-xs font-bold text-[#8A5B1E] transition hover:bg-[#E8DFD0]">X</button>
          </div>
        </div>
        <div class="flex-1 overflow-y-auto px-5 py-4 sm:px-6 sm:py-5">
          <div class="grid gap-5 md:grid-cols-[7fr_5fr]">
            <section class="rounded-xl border border-[#E7E1D9] bg-[#FBF9F6] p-4 shadow-sm">
              <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                  <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-[#B47727]">Produk</p>
                  <h2 class="mt-1 text-lg font-bold text-[#292522]">Daftar Item</h2>
                </div>
                <span class="text-sm font-semibold text-[#6B4F3A]">{{ $totalItems }} item</span>
              </div>
              <div id="checkoutItemsContainer" class="mt-4 space-y-3">
                  @include('pos.partials.checkout-items', [
                      'cart' => $cart,
                      'totalItems' => $totalItems,
                  ])
              </div>
            </section>

            <section class="rounded-xl border border-[#E7E1D9] bg-[#FBF9F6] p-4 shadow-sm">
              <div class="mb-4">
                <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-[#B47727]">Pembayaran</p>
                <h2 class="mt-1 text-lg font-bold text-[#292522]">Metode Pembayaran</h2>
              </div>

              <form id="checkoutForm" method="POST" action="{{ route('pos.checkout') }}" class="space-y-5">
                @csrf
                <input type="hidden" id="payment_method" name="payment_method" value="cash">
                <input type="hidden" id="checkoutTaxPercent" value="{{ $subtotal > 0 ? round(($taxAmount / $subtotal) * 100, 4) : 0 }}">
                <input type="hidden" id="checkoutDiscountPercent" value="{{ $subtotal > 0 ? round(($discount / $subtotal) * 100, 4) : 0 }}">

                <div id="checkoutTotalsPanel" class="rounded-2xl bg-white p-4 border border-[#E7E1D9] shadow-sm">
                  <div class="flex items-center justify-between gap-2">
                    <span class="text-sm text-[#6B4F3A]">Subtotal</span>
                    <span id="checkoutSubtotalText" class="text-sm font-semibold text-[#292522]">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                  </div>
                  <div class="mt-3 flex items-center justify-between gap-2">
                    <span class="text-sm text-[#6B4F3A]">Diskon</span>
                    <span id="checkoutDiscountText" class="text-sm font-semibold text-[#292522]">Rp {{ number_format($discount, 0, ',', '.') }}</span>
                  </div>
                  <div class="mt-3 flex items-center justify-between gap-2">
                    <span class="text-sm text-[#6B4F3A]">Pajak</span>
                    <span id="checkoutTaxText" class="text-sm font-semibold text-[#292522]">Rp {{ number_format($taxAmount, 0, ',', '.') }}</span>
                  </div>
                  <div class="mt-4 rounded-2xl bg-[#F4EFE6] p-4">
                    <div class="flex items-center justify-between gap-2">
                      <span class="text-sm font-semibold text-[#6B4F3A]">Grand Total</span>
                      <span id="checkoutGrandTotalText" class="text-lg font-bold text-[#8A5B1E]">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                    </div>
                  </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                  @foreach ($paymentMethods as $paymentMethod)
                    @php
                      $methodType = strtolower(str_contains($paymentMethod->name, 'qris') ? 'qris' : (str_contains(strtolower($paymentMethod->name), 'tunai') ? 'cash' : 'other'));
                    @endphp
                    <label class="payment-method-card cursor-pointer rounded-xl border border-[#E7E1D9] bg-white p-4 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md {{ $loop->first ? 'active' : '' }}" data-payment-type="{{ $methodType }}">
                      <input
                        type="radio"
                        name="payment_method_id"
                        value="{{ $paymentMethod->id }}"
                        class="sr-only payment-method-radio"
                        data-type="{{ $methodType }}"
                        {{ $loop->first ? 'checked' : '' }}
                      >
                      <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                          <span class="payment-method-check hidden h-8 w-8 items-center justify-center rounded-full bg-[#2E7D32] text-white text-sm font-bold">✓</span>
                          <span class="text-sm font-bold text-[#292522]">{{ $paymentMethod->name }}</span>
                        </div>
                        <span class="rounded-full bg-[#F4EFE6] px-3 py-1 text-[11px] font-bold text-[#8A5B1E]">
                          {{ $paymentMethod->name }}
                        </span>
                      </div>
                    </label>
                  @endforeach
                </div>

                <div id="cashFields" class="space-y-4">
                  <div id="paymentQuickActions" class="space-y-4">
                    <div class="grid gap-3 sm:grid-cols-2">
                      <button id="exactAmountBtn" type="button" class="rounded-2xl border border-[#D8D3C9] bg-white px-4 py-3 text-sm font-semibold text-[#292522] transition hover:bg-[#F4EFE6]">Uang Pas</button>
                    </div>
                    <div class="space-y-2">
                      <p class="text-sm font-semibold text-[#17201C]">Nominal Cepat</p>
                      <div id="quickAmountsContainer" class="flex flex-wrap gap-2"></div>
                    </div>
                  </div>

                  <div>
                    <label for="paid_amount" class="mb-2 block text-sm font-semibold text-[#17201C]">Uang Dibayar</label>
                    <input
                      id="paid_amount"
                      name="paid_amount"
                      type="number"
                      min="0"
                      value="{{ old('paid_amount') ?? $grandTotal }}"
                      required
                      class="w-full rounded-2xl border border-[#D8D3C9] bg-[#FAF9F6] px-4 py-4 text-sm text-[#17201C] outline-none transition duration-200 focus:border-[#1B1B18] focus:bg-white focus:ring-4 focus:ring-[#1B1B18]/10"
                      placeholder="Masukkan uang pembayaran"
                    >
                  </div>

                  <div class="rounded-2xl bg-[#FAF9F6] px-4 py-4">
                    <div class="flex items-center justify-between">
                      <span class="text-sm font-bold text-[#6B4F3A]">Kembalian</span>
                      <span id="changeAmount" class="text-lg font-bold text-[#8A5B1E]">Rp 0</span>
                    </div>
                  </div>
                </div>

                <div id="qrisCard" class="hidden rounded-xl border border-[#E7E1D9] bg-[#FAF9F6] p-5 shadow-sm">
                  <div class="flex items-center justify-between gap-3">
                    <div>
                      <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-[#B47727]">QRIS</p>
                      <h3 class="mt-1 text-lg font-bold text-[#292522]">Menunggu Pembayaran</h3>
                    </div>
                    <span class="rounded-full bg-[#F4EFE6] px-3 py-1 text-[11px] font-bold text-[#8A5B1E]">Status</span>
                  </div>

                  <div class="mt-4 flex items-center justify-center rounded-xl border border-dashed border-[#D8D3C9] bg-white p-6">
                    <div class="flex h-40 w-40 items-center justify-center rounded-2xl bg-[#F4EFE6] text-center text-sm font-bold text-[#8A5B1E]">QR CODE</div>
                  </div>

                  <div class="mt-4 rounded-2xl bg-[#F4EFE6] px-4 py-3">
                    <p class="text-sm font-bold text-[#8A5B1E]">Total Pembayaran</p>
                    <p class="mt-1 text-lg font-bold text-[#292522]">Rp {{ number_format($grandTotal, 0, ',', '.') }}</p>
                  </div>
                </div>
                <div class="sticky bottom-0 mt-6 border-t border-[#E7E1D9] bg-white pt-4">
                    <button
                        id="checkoutSubmitButton"
                        type="submit"
                        class="w-full rounded-2xl bg-[#292522] px-5 py-4 text-base font-bold text-white transition hover:bg-[#3A3532] disabled:cursor-not-allowed disabled:bg-gray-300">
                        Bayar Sekarang
                    </button>
                </div>
              </form>
            </section>
          </div>
        </div>

        <div class="sticky bottom-0 z-10 border-t border-[#E7E1D9] bg-white px-5 py-4 sm:px-6 sm:py-5">
          <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <p class="text-sm font-semibold text-[#6B4F3A]">Grand Total</p>
              <p class="text-2xl font-bold text-[#8A5B1E]">Rp {{ number_format($grandTotal, 0, ',', '.') }}</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row">
              <button id="closeCheckoutModal" type="button" class="rounded-2xl border border-[#D8D3C9] bg-white px-4 py-4 text-sm font-semibold text-[#6B4F3A] transition hover:bg-[#F4EFE6]">Batal</button>
              <button id="checkoutSubmitBtn" type="submit" form="checkoutForm" class="rounded-2xl bg-[#292522] px-4 py-4 text-sm font-bold text-white transition hover:bg-[#302F2A]">Bayar</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div id="productModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/60 p-4">
      <div class="w-full max-w-md rounded-[28px] border border-[#E7E1D9] bg-[#FBF9F6] p-5 shadow-2xl sm:p-6">
        <div class="flex items-start justify-between gap-3">
          <div>
            <p id="pmTitle" class="text-[11px] font-bold uppercase tracking-[0.28em] text-[#B47727]">Produk</p>
            <h3 id="pmHeader" class="mt-1 text-xl font-bold text-[#292522]">Tambah Produk</h3>
          </div>
          <button id="closeProductModal" type="button" class="rounded-xl bg-[#F4EFE6] px-3 py-2 text-xs font-bold text-[#8A5B1E] transition hover:bg-[#E8DFD0]">Batal</button>
        </div>

        <form id="productModalForm" method="POST" class="mt-5 space-y-4">
          @csrf
          <div id="pmProductNameWrap">
            <p class="text-sm font-semibold text-[#292522]">Nama Produk</p>
            <p id="pmProductName" class="mt-1 text-base font-bold text-[#292522]"></p>
            <input type="hidden" name="product_id" id="pmProductId">
            <input type="hidden" name="sale_type" id="pmSaleType">
            <input type="hidden" name="price" id="pmPriceHidden">
          </div>

          <div>
            <p class="text-sm font-semibold text-[#292522]">Kategori</p>
            <p id="pmCategory" class="mt-1 text-base text-[#292522]"></p>
          </div>

          <div>
            <p id="pmPrimaryPriceLabel" class="text-sm font-semibold text-[#292522]">Harga</p>
            <p id="pmProductPrice" class="mt-1 text-base font-semibold text-[#8A5B1E]"></p>
          </div>

          <div id="pmWholesalePriceWrap" class="hidden">
            <p class="text-sm font-semibold text-[#292522]">Harga Grosir per Gram</p>
            <p id="pmWholesalePrice" class="mt-1 text-base font-semibold text-[#8A5B1E]"></p>
          </div>

          <div id="pmWholesaleMinQtyWrap" class="hidden">
            <p class="text-sm font-semibold text-[#292522]">Minimal Pembelian Grosir (Gram)</p>
            <p id="pmWholesaleMinQty" class="mt-1 text-base text-[#292522]"></p>
          </div>

          <div id="pmStockWrap">
            <p class="text-sm font-semibold text-[#292522]">Stok Awal</p>
            <p id="pmStock" class="mt-1 text-base text-[#292522]"></p>
          </div>

          <div id="pmPurchaseTypeWrap" class="hidden">
            <p class="text-sm font-semibold text-[#292522]">Jenis Pembelian</p>
            <div class="mt-2 flex gap-3">
              <label class="inline-flex items-center gap-2"><input type="radio" name="purchase_type" value="pcs" class="pmPurchaseType"> <span class="text-sm">PCS</span></label>
              <label class="inline-flex items-center gap-2"><input type="radio" name="purchase_type" value="grosir" class="pmPurchaseType"> <span class="text-sm">Grosir</span></label>
            </div>
          </div>

          <div id="pmInputMethodWrap" class="hidden">
            <p class="text-sm font-semibold text-[#292522]">Metode Input</p>
            <div class="mt-2 flex gap-3">
              <label class="inline-flex items-center gap-2"><input type="radio" name="input_method" value="berat" class="pmInputMethod"> <span class="text-sm">Berat</span></label>
              <label class="inline-flex items-center gap-2"><input type="radio" name="input_method" value="nominal" class="pmInputMethod"> <span class="text-sm">Nominal</span></label>
            </div>
          </div>

          <div id="pmQtyWrap">
            <label id="pmQtyLabel" for="pmQty" class="mb-2 block text-sm font-semibold text-[#292522]">Qty</label>
            <div class="flex items-center gap-3">
              <button type="button" id="pmMinus" class="flex h-11 w-11 items-center justify-center rounded-2xl border border-[#D8D3C9] bg-white text-xl font-bold text-[#292522] transition hover:bg-[#F4EFE6]">−</button>
              <input id="pmQty" name="qty" type="number" min="1" step="1" class="w-full rounded-2xl border border-[#D8D3C9] bg-white px-3 py-3 text-sm text-[#292522] outline-none transition duration-200 focus:border-[#292522] focus:ring-4 focus:ring-[#292522]/10">
              <button type="button" id="pmPlus" class="flex h-11 w-11 items-center justify-center rounded-2xl border border-[#D8D3C9] bg-white text-xl font-bold text-[#292522] transition hover:bg-[#F4EFE6]">+</button>
            </div>
            <p id="pmUnit" class="mt-2 text-sm text-[#6B4F3A]"></p>
          </div>

          <div id="pmNominalWrap" class="hidden">
            <label for="pmNominal" class="mb-2 block text-sm font-semibold text-[#292522]">Nominal</label>
            <div class="flex items-center gap-3">
              <input id="pmNominal" type="number" min="1" step="1" class="w-full rounded-2xl border border-[#D8D3C9] bg-white px-3 py-3 text-sm text-[#292522] outline-none transition duration-200 focus:border-[#292522] focus:ring-4 focus:ring-[#292522]/10">
              <span class="text-sm font-semibold text-[#6B4F3A]">Rp</span>
            </div>
          </div>

          <div id="pmWholesaleMessage" class="hidden text-sm text-[#8A5B1E]"></div>

          <div>
            <p class="text-sm font-semibold text-[#292522]">Subtotal</p>
            <p id="pmSubtotal" class="mt-1 text-base font-bold text-[#8A5B1E]">Rp 0</p>
          </div>

          <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
            <button id="pmCancel" type="button" class="rounded-2xl border border-[#D8D3C9] bg-white px-4 py-3 text-sm font-semibold text-[#6B4F3A] transition hover:bg-[#F4EFE6]">Batal</button>
            <button id="pmSubmit" type="submit" class="rounded-2xl bg-[#292522] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#3A352F]">Tambah</button>
          </div>
        </form>
      </div>
    </div>

    <div id="cameraModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4">
      <div class="w-full max-w-2xl rounded-[28px] border border-[#E7E1D9] bg-[#FBF9F6] p-5 shadow-2xl sm:p-6">
        <div class="flex items-start justify-between gap-3">
          <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-[#B47727]">Camera Scanner</p>
            <h3 class="mt-1 text-xl font-bold text-[#292522]">Scan Barcode dengan Kamera</h3>
          </div>
          <button id="closeCameraModal" type="button" class="rounded-xl bg-[#F4EFE6] px-3 py-2 text-xs font-bold text-[#8A5B1E] transition hover:bg-[#E8DFD0]">Tutup</button>
        </div>
        <div class="mt-4 rounded-[24px] border border-[#E7E1D9] bg-white p-3">
          <div id="cameraPreview" style="width:100%; min-height:340px; border-radius:20px; overflow:hidden;"></div>
          <div id="scannerLoader" class="mt-3 hidden rounded-2xl bg-[#F4EFE6] px-3 py-2 text-sm font-semibold text-[#8A5B1E]">Mengakses kamera...</div>
          <p id="cameraStatus" class="mt-3 text-sm text-[#6B4F3A]">Siapkan kamera untuk memindai barcode secara realtime.</p>
        </div>
        <div class="mt-4">
          <label for="cameraSelect" class="mb-2 block text-sm font-semibold text-[#292522]">Pilih Kamera</label>
          <select id="cameraSelect" class="w-full rounded-2xl border border-[#D8D3C9] bg-white px-4 py-3 text-sm text-[#292522] outline-none transition duration-200 hover:border-[#B8B1A4] focus:border-[#292522] focus:ring-4 focus:ring-[#292522]/10"><option value="">Memuat kamera...</option></select>
        </div>
        <div class="mt-4 flex flex-col gap-3 sm:flex-row">
          <button id="startCameraScan" type="button" class="inline-flex flex-1 items-center justify-center rounded-2xl bg-[#292522] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#3A352F]">Buka Kamera</button>
          <button id="stopCameraScan" type="button" class="inline-flex flex-1 items-center justify-center rounded-2xl bg-[#F4EFE6] px-4 py-3 text-sm font-bold text-[#8A5B1E] transition hover:bg-[#E8DFD0]">Tutup Kamera</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
  <script>
    if (window.BarcodeScannerInitialized) {
      console.warn('[POS] Scanner script already loaded');
    }
    window.BarcodeScannerInitialized = true;

    const dom = {
      barcodeInput: document.getElementById('barcode_input'),
      barcodeMessage: document.getElementById('barcode_message'),
      openCameraScan: document.getElementById('openCameraScan'),
      cameraModal: document.getElementById('cameraModal'),
      cameraPreview: document.getElementById('cameraPreview'),
      cameraStatus: document.getElementById('cameraStatus'),
      scannerLoader: document.getElementById('scannerLoader'),
      cameraSelect: document.getElementById('cameraSelect'),
      closeCameraModal: document.getElementById('closeCameraModal'),
      startCameraScan: document.getElementById('startCameraScan'),
      stopCameraScan: document.getElementById('stopCameraScan'),
      checkoutModal: document.getElementById('checkoutModal'),
      openCheckoutButton: document.getElementById('openCheckoutButton'),
      closeCheckoutModal: document.getElementById('closeCheckoutModal'),
      productModal: document.getElementById('productModal'),
      productModalForm: document.getElementById('productModalForm'),
      pmTitle: document.getElementById('pmTitle'),
      pmHeader: document.getElementById('pmHeader'),
      closeProductModal: document.getElementById('closeProductModal'),
      pmProductName: document.getElementById('pmProductName'),
      pmProductPrice: document.getElementById('pmProductPrice'),
      pmCategory: document.getElementById('pmCategory'),
      pmProductId: document.getElementById('pmProductId'),
      pmSaleType: document.getElementById('pmSaleType'),
      pmPriceHidden: document.getElementById('pmPriceHidden'),
      pmPrimaryPriceLabel: document.getElementById('pmPrimaryPriceLabel'),
      pmWholesalePriceWrap: document.getElementById('pmWholesalePriceWrap'),
      pmWholesalePrice: document.getElementById('pmWholesalePrice'),
      pmWholesaleMinQtyWrap: document.getElementById('pmWholesaleMinQtyWrap'),
      pmWholesaleMinQty: document.getElementById('pmWholesaleMinQty'),
      pmStock: document.getElementById('pmStock'),
      pmSaleTypeRadios: document.querySelectorAll('input[name="pmSaleTypeDisplay"]'),
      pmPurchaseTypeWrap: document.getElementById('pmPurchaseTypeWrap'),
      pmInputMethodWrap: document.getElementById('pmInputMethodWrap'),
      pmQtyWrap: document.getElementById('pmQtyWrap'),
      pmQtyLabel: document.getElementById('pmQtyLabel'),
      pmQty: document.getElementById('pmQty'),
      pmMinus: document.getElementById('pmMinus'),
      pmPlus: document.getElementById('pmPlus'),
      pmUnit: document.getElementById('pmUnit'),
      pmNominalWrap: document.getElementById('pmNominalWrap'),
      pmNominal: document.getElementById('pmNominal'),
      pmWholesaleMessage: document.getElementById('pmWholesaleMessage'),
      pmSubtotal: document.getElementById('pmSubtotal'),
      pmCancel: document.getElementById('pmCancel'),
      pmSubmit: document.getElementById('pmSubmit'),
      cartItemsContainer: document.getElementById('cartItemsContainer'),
      summaryItems: document.getElementById('summaryItems'),
      summaryGram: document.getElementById('summaryGram'),
      summarySubtotal: document.getElementById('summarySubtotal'),
      checkoutItemsContainer: document.getElementById('checkoutItemsContainer'),
      summaryGrandTotal: document.getElementById('summaryGrandTotal'),
      checkoutSubtotalText: document.getElementById('checkoutSubtotalText'),
      checkoutTaxText: document.getElementById('checkoutTaxText'),
      checkoutDiscountText: document.getElementById('checkoutDiscountText'),
      checkoutGrandTotalText: document.getElementById('checkoutGrandTotalText'),
      qrisTotalText: document.getElementById('qrisTotalText'),
      paymentMethodHidden: document.getElementById('payment_method'),
      saveOrderForm: document.getElementById('saveOrderForm'),
      savedOrdersContainer: document.getElementById('savedOrdersContainer'),
      paymentMethodCards: document.querySelectorAll('.payment-method-card'),
      paymentMethodRadios: document.querySelectorAll('.payment-method-radio'),
      paidAmount: document.getElementById('paid_amount'),
      changeAmount: document.getElementById('changeAmount'),
      exactAmountBtn: document.getElementById('exactAmountBtn'),
      roundAmountBtn: document.getElementById('roundAmountBtn'),
      quickAmountsContainer: document.getElementById('quickAmountsContainer'),
      checkoutSubmitBtn: document.getElementById('checkoutSubmitBtn'),
      closeCheckoutModalBottom: document.getElementById('closeCheckoutModalBottom'),
      cashFields: document.getElementById('cashFields'),
      qrisCard: document.getElementById('qrisCard'),
      csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
    };

    let barcodeEnterLocked = false;
    function handleBarcodeEnter(event) {
      if (event.key === 'Enter' || event.keyCode === 13 || event.code === 'Enter') {
        event.preventDefault();
        if (barcodeEnterLocked) return;
        barcodeEnterLocked = true;
        processBarcode(dom.barcodeInput.value.trim());
        setTimeout(() => { barcodeEnterLocked = false; }, 150);
      }
    }

    const state = {
      html5QrCode: null,
      activeBarcode: null,
      currentTembakauProduct: null,
      isScanning: false,
    };

    function showMessage(message, type = 'info') {
      if (!dom.barcodeMessage) return;
      dom.barcodeMessage.textContent = message;
      dom.barcodeMessage.className = 'mt-3 rounded-2xl p-3 text-sm';
      if (type === 'error') {
        dom.barcodeMessage.classList.add('bg-red-50', 'text-red-700');
      } else if (type === 'success') {
        dom.barcodeMessage.classList.add('bg-green-50', 'text-green-700');
      } else {
        dom.barcodeMessage.classList.add('bg-[#F4EFE6]', 'text-[#8A5B1E]');
      }
    }

    function focusBarcode() {
      if (!dom.barcodeInput) return;
      dom.barcodeInput.value = '';
      dom.barcodeInput.focus();
      dom.barcodeInput.select();
    }

    function formatRp(value) {
      return `Rp ${Number(value || 0).toLocaleString('id-ID')}`;
    }

    function parseRp(value) {
      if (value == null) return 0;
      return Number(String(value).replace(/[\D]/g, '')) || 0;
    }

    function getGrandTotalValue() {
      return parseRp(dom.checkoutGrandTotalText?.textContent || dom.qrisTotalText?.textContent || 0);
    }

    function isQrisSelected() {
      return dom.paymentMethodHidden?.value === 'qris';
    }

    function setSelectedPaymentMethod(type) {
      if (!dom.paymentMethodHidden) return;
      dom.paymentMethodHidden.value = type;
      dom.paymentMethodCards?.forEach((card) => {
        const isActive = card.dataset.paymentType === type;
        card.classList.toggle('active', isActive);
        card.classList.toggle('border-[#B47727]', isActive);
        card.classList.toggle('bg-[#F4EFE6]', isActive);
        const check = card.querySelector('.payment-method-check');
        if (check) {
          check.classList.toggle('hidden', !isActive);
        }
        const radio = card.querySelector('.payment-method-radio');
        if (radio) {
          radio.checked = isActive;
        }
      });

      if (dom.cashFields) {
        dom.cashFields.classList.toggle('hidden', type !== 'cash');
      }
      if (dom.qrisCard) {
        dom.qrisCard.classList.toggle('hidden', type !== 'qris');
      }
      if (dom.paidAmount) {
        dom.paidAmount.readOnly = type !== 'cash';
        if (type !== 'cash') {
          dom.paidAmount.value = getGrandTotalValue();
        }
      }
      updatePaymentCalculation();
    }

    function updatePaymentCalculation() {
      const grandTotal = getGrandTotalValue();
      const paidValue = Number(dom.paidAmount?.value || 0);
      const paymentType = dom.paymentMethodHidden?.value || 'cash';
      const change = paidValue - grandTotal;
      const changeText = formatRp(Math.max(change, 0));

      if (dom.changeAmount) {
        dom.changeAmount.textContent = paymentType === 'cash' ? changeText : formatRp(0);
        dom.changeAmount.className = 'text-lg font-bold ' + (paymentType === 'cash' ? (change < 0 ? 'text-red-600' : 'text-[#8A5B1E]') : 'text-[#8A5B1E]');
      }

      const canPay = paymentType === 'cash' ? paidValue >= grandTotal && grandTotal > 0 : grandTotal > 0;
      if (dom.checkoutSubmitBtn) {
        dom.checkoutSubmitBtn.disabled = !canPay;
        dom.checkoutSubmitBtn.classList.toggle('opacity-50', !canPay);
        dom.checkoutSubmitBtn.classList.toggle('cursor-not-allowed', !canPay);
      }
    }

    function createQuickAmountButton(amount) {
      const button = document.createElement('button');
      button.type = 'button';
      button.className = 'rounded-2xl border border-[#D8D3C9] bg-white px-4 py-3 text-sm font-semibold text-[#292522] transition hover:bg-[#F4EFE6]';
      button.textContent = formatRp(amount);
      button.addEventListener('click', () => {
        if (!dom.paidAmount) return;
        dom.paidAmount.value = amount;
        updatePaymentCalculation();
      });
      return button;
    }

    function generateQuickAmounts() {
      if (!dom.quickAmountsContainer || !dom.paidAmount) return;
      const total = getGrandTotalValue();
      const roundTo = 10000;
      const roundedUp = Math.ceil(total / roundTo) * roundTo;
      const defaultAmounts = [total, roundedUp, roundedUp + roundTo, roundedUp + roundTo * 2];
      dom.quickAmountsContainer.innerHTML = '';
      defaultAmounts.forEach((amount) => {
        if (amount > 0) {
          dom.quickAmountsContainer.appendChild(createQuickAmountButton(amount));
        }
      });
    }

    function selectPaymentMethod(type) {
      setSelectedPaymentMethod(type);
    }

    function updateSummaryUI(summary) {
      if (dom.summaryItems) dom.summaryItems.textContent = summary.items ?? summary.item_count ?? 0;
      if (dom.summaryGram) dom.summaryGram.textContent = `${summary.gram ?? summary.total_gram ?? 0} gram`;
      if (dom.summarySubtotal) dom.summarySubtotal.textContent = `Rp ${Number(summary.subtotal ?? summary.subTotal ?? 0).toLocaleString('id-ID')}`;
      if (dom.summaryGrandTotal) dom.summaryGrandTotal.textContent = Number(summary.grandTotal ?? summary.grand_total ?? 0).toLocaleString('id-ID');
      if (dom.topSummaryItems) dom.topSummaryItems.textContent = summary.items ?? summary.item_count ?? 0;
      if (dom.topSummarySubtotal) dom.topSummarySubtotal.textContent = `Rp ${Number(summary.subtotal ?? summary.subTotal ?? 0).toLocaleString('id-ID')}`;
      updateCheckoutButtons(summary.items ?? 0);
    }

    function bindCartItemEvents() {
      if (!dom.cartItemsContainer || dom.cartEventsBound) return;

      dom._cartClickHandler = (event) => {
        const deleteButton = event.target.closest('[data-item-delete-form] button');
        if (deleteButton) {
          event.stopPropagation();
          return;
        }

        const card = event.target.closest('[data-item-edit-trigger]');
        if (!card) return;
        openEditFromCard(card);
      };

      dom._cartKeydownHandler = (event) => {
        if (event.key !== 'Enter' && event.key !== ' ') return;
        const card = event.target.closest('[data-item-edit-trigger]');
        if (!card) return;
        event.preventDefault();
        openEditFromCard(card);
      };

      dom.cartItemsContainer.addEventListener('click', dom._cartClickHandler);
      dom.cartItemsContainer.addEventListener('keydown', dom._cartKeydownHandler);
      dom.cartEventsBound = true;
    }

    function updateCheckoutButtons(cartCount) {

        const saveButton = document.getElementById('saveButton');
        const payButton = document.getElementById('openCheckoutButton');
        const checkoutSubmit = document.getElementById('checkoutSubmitButton');
        const checkoutSubmitBtn = document.getElementById('checkoutSubmitBtn');

        if (saveButton) {
            if (cartCount > 0) {
                saveButton.disabled = false;
                saveButton.removeAttribute('disabled');
                saveButton.classList.remove('cursor-not-allowed');
            } else {
                saveButton.disabled = true;
                saveButton.setAttribute('disabled', 'disabled');
                saveButton.classList.add('cursor-not-allowed');
            }
        }

        if (payButton) {
            if (cartCount > 0) {
                payButton.classList.remove('pointer-events-none');
                payButton.classList.remove('bg-gray-300');
                payButton.classList.add('bg-[#292522]');
                payButton.classList.add('hover:bg-[#302F2A]');
            } else {
                payButton.classList.add('pointer-events-none');
                payButton.classList.add('bg-gray-300');
                payButton.classList.remove('bg-[#292522]');
                payButton.classList.remove('hover:bg-[#302F2A]');
            }
        }

        if (checkoutSubmit) {
            checkoutSubmit.disabled = cartCount === 0;
            checkoutSubmit.classList.toggle('opacity-50', cartCount === 0);
            checkoutSubmit.classList.toggle('cursor-not-allowed', cartCount === 0);
        }

        if (checkoutSubmitBtn) {
            checkoutSubmitBtn.disabled = cartCount === 0;
            checkoutSubmitBtn.classList.toggle('opacity-50', cartCount === 0);
            checkoutSubmitBtn.classList.toggle('cursor-not-allowed', cartCount === 0);
        }
    }

    function updateCartUI(html, summary) {
      if (dom.cartItemsContainer) {
        dom.cartItemsContainer.innerHTML = html;
      }
      updateSummaryUI(summary);
      if (document.body) {
        document.body.dataset.cartCount = String(summary.items ?? 0);
      }
      dom.cartEventsBound = false;
      bindCartItemEvents();
      updateCheckoutButtons(summary.items ?? 0);
    }

    function updateCheckoutItems(html) {
      if (dom.checkoutItemsContainer) {
        dom.checkoutItemsContainer.innerHTML = html;
      }
    }

    function updateSavedOrders(html) {
      const container = dom.savedOrdersContainer || document.getElementById('savedOrdersContainer');
      if (container) {
        container.innerHTML = html;
        dom.savedOrdersContainer = container;
        dom.savedOrdersBound = false;
        bindSavedOrderEvents();
      }
    }

    function handleSavedOrderFormSubmit(event) {
      const form = event.target.closest('form[data-saved-order-action]');
      if (!form) return;
      event.preventDefault();

      const action = form.action;
      const formData = new FormData(form);
      formData.append('_token', dom.csrfToken);
      const overrideMethod = form.querySelector('input[name="_method"]')?.value?.toUpperCase();
      const formMethod = (form.method || 'POST').toUpperCase();
      const method = overrideMethod ? 'POST' : formMethod;

      if (overrideMethod && !formData.has('_method')) {
        formData.append('_method', overrideMethod);
      }

      fetch(action, {
        method,
        credentials: 'same-origin',
        headers: {
          'X-CSRF-TOKEN': dom.csrfToken,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        },
        body: formData,
      })
        .then(async (response) => {
          const contentType = response.headers.get('Content-Type') || '';
          let result = null;
          if (contentType.includes('application/json')) {
            result = await response.json();
          }
          if (!response.ok || !result?.success) {
            const message = result?.message || 'Terjadi kesalahan saat memproses order.';
            showMessage(message, 'error');
            focusBarcode();
            return;
          }

          if (result.cart_html) {
            updateCartUI(result.cart_html, result.summary ?? {});
          }
          if (result.checkout_html) {
            updateCheckoutItems(result.checkout_html);
          }
          if (result.saved_orders_html) {
            updateSavedOrders(result.saved_orders_html);
          }
          if (result.summary) {
            updateSummaryUI(result.summary);
          }
          updateCheckoutButtons(result.cart_count ?? 0);
          showMessage(result.message || 'Perubahan berhasil disimpan.', 'success');
          focusBarcode();
        })
        .catch((error) => {
          showMessage(error?.message || 'Terjadi kesalahan jaringan.', 'error');
          focusBarcode();
        });
    }

    function bindSavedOrderEvents() {
      if (!dom.savedOrdersContainer || dom.savedOrdersBound) return;
      dom.savedOrdersContainer.addEventListener('submit', handleSavedOrderFormSubmit);
      dom.savedOrdersBound = true;
    }

    function openModal(modal) {
      if (!modal) return;
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }

    function closeModal(modal) {
      if (!modal) return;
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }

    function buildForm(action, fields = {}) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = action;
      form.style.display = 'none';
      const token = document.createElement('input');
      token.type = 'hidden';
      token.name = '_token';
      token.value = '{{ csrf_token() }}';
      form.appendChild(token);
      Object.entries(fields).forEach(([name, value]) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
      });
      document.body.appendChild(form);
      return form;
    }

    async function submitAddToCart(productId, fields = {}, actionUrl = null) {
      if (!productId) {
        showMessage('ID produk tidak valid. Tidak dapat menambahkan ke keranjang.', 'error');
        return null;
      }

      if (!dom.csrfToken) {
        showMessage('Token CSRF tidak ditemukan.', 'error');
        return null;
      }

      const url = actionUrl || `{{ url('/pos/cart') }}/${productId}`;
      const formData = new FormData();
      formData.append('_token', dom.csrfToken);
      Object.entries(fields).forEach(([name, value]) => {
        formData.append(name, value);
      });

      try {
        const response = await fetch(url, {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'X-CSRF-TOKEN': dom.csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
          },
          body: formData,
        });

        let result = null;
        let errorMessage = 'Gagal menambahkan produk ke keranjang.';
        const contentType = response.headers.get('Content-Type') || '';

        if (contentType.includes('application/json')) {
          try {
            result = await response.json();
          } catch (jsonError) {
            errorMessage = 'Respon JSON tidak valid.';
          }
        } else {
          const text = await response.text();
          errorMessage = text || errorMessage;
        }

        if (!response.ok) {
          if (result && result.message) {
            errorMessage = result.message;
          }
          showMessage(errorMessage, 'error');
          focusBarcode();
          return null;
        }

        if (!result || !result.success) {
          errorMessage = result?.message || errorMessage;
          showMessage(errorMessage, 'error');
          focusBarcode();
          return null;
        }

        if (result.cart_html) {
          updateCartUI(result.cart_html, result.summary ?? {});
        }

        if (result.checkout_html && dom.checkoutItemsContainer) {
          dom.checkoutItemsContainer.innerHTML = result.checkout_html;
        }

        updateCheckoutButtons(result.cart_count ?? result.summary?.items ?? 0);
        updateCheckoutSummary(result.summary ?? {});

        showMessage(result.message || 'Produk berhasil ditambahkan ke keranjang.', 'success');
        return result;
      } catch (error) {
        showMessage(error?.message || 'Gagal menambahkan produk ke keranjang.', 'error');
        focusBarcode();
        return null;
      }
    }

    function resetScanModal() {
      state.isScanning = false;
      if (dom.scannerLoader) dom.scannerLoader.classList.add('hidden');
      if (dom.cameraStatus) dom.cameraStatus.textContent = 'Siapkan kamera untuk memindai barcode secara realtime.';
    }

    async function stopScanner() {
      if (!state.html5QrCode) return;
      try { await state.html5QrCode.stop(); } catch (e) {}
      try { await state.html5QrCode.clear(); } catch (e) {}
      state.html5QrCode = null;
      state.isScanning = false;
      resetScanModal();
    }

    async function startScanner() {
      if (!window.Html5Qrcode) { showMessage('Library scanner tidak tersedia.', 'error'); return; }
      if (state.html5QrCode) await stopScanner();
      state.html5QrCode = new window.Html5Qrcode('cameraPreview');
      const cameraId = dom.cameraSelect?.value || undefined;
      const constraints = cameraId ? { deviceId: { exact: cameraId } } : { facingMode: { ideal: 'environment' } };
      try {
        await state.html5QrCode.start(constraints, { fps: 10, qrbox: { width: 260, height: 170 }, aspectRatio: 1.0 }, async (decodedText) => {
          if (!decodedText || state.isScanning) return;
          state.isScanning = true;
          const barcode = decodedText.trim();
          if (!barcode) { state.isScanning = false; return; }
          if (dom.barcodeInput) dom.barcodeInput.value = barcode;
          showMessage('Barcode terbaca. Memproses...', 'success');
          await stopScanner();
          closeModal(dom.cameraModal);
          processBarcode(barcode);
        });
        if (dom.cameraStatus) dom.cameraStatus.textContent = 'Scanner aktif. Arahkan kamera ke barcode.';
        state.isScanning = true;
      } catch (error) {
        stopScanner();
        showMessage('Gagal mengakses kamera: ' + (error?.message || 'Unknown error'), 'error');
      }
    }

    async function loadCameras() {
      if (!window.Html5Qrcode) throw new Error('Library scanner tidak tersedia');
      const cameras = await window.Html5Qrcode.getCameras();
      if (!dom.cameraSelect) return cameras;
      dom.cameraSelect.innerHTML = '';
      if (!cameras || cameras.length === 0) {
        dom.cameraSelect.innerHTML = '<option value="">Tidak ada kamera terdeteksi</option>';
        throw new Error('Tidak ada kamera terdeteksi');
      }
      cameras.forEach((camera, index) => {
        const option = document.createElement('option');
        option.value = camera.id;
        option.textContent = camera.label || `Kamera ${index + 1}`;
        dom.cameraSelect.appendChild(option);
      });
      return cameras;
    }

    function updateCheckoutSummary(summary) {
        if (dom.checkoutSubtotalText) {
            dom.checkoutSubtotalText.textContent =
                'Rp ' + Number(summary.subtotal ?? 0).toLocaleString('id-ID');
        }

        if (dom.checkoutDiscountText) {
            dom.checkoutDiscountText.textContent =
                'Rp ' + Number(summary.discount ?? 0).toLocaleString('id-ID');
        }

        if (dom.checkoutTaxText) {
            dom.checkoutTaxText.textContent =
                'Rp ' + Number(summary.tax ?? 0).toLocaleString('id-ID');
        }

        if (dom.checkoutGrandTotalText) {
            dom.checkoutGrandTotalText.textContent =
                'Rp ' + Number(summary.grand_total ?? 0).toLocaleString('id-ID');
        }
    }

    function updateCheckoutModal(summary = {}) {
        const submitButton = document.getElementById('checkoutSubmitButton');
        const bottomSubmitButton = document.getElementById('checkoutSubmitBtn');
        if (submitButton) {
            const totalItem = Number(summary.items ?? 0);
            const hasItem = totalItem > 0;

            submitButton.disabled = !hasItem;
            submitButton.classList.toggle('bg-gray-300', !hasItem);
            submitButton.classList.toggle('cursor-not-allowed', !hasItem);
            submitButton.classList.toggle('hover:bg-gray-300', !hasItem);
            submitButton.classList.toggle('bg-[#292522]', hasItem);
            submitButton.classList.toggle('hover:bg-[#302F2A]', hasItem);
        }

        if (bottomSubmitButton) {
            const totalItem = Number(summary.items ?? 0);
            const hasItem = totalItem > 0;
            bottomSubmitButton.disabled = !hasItem;
            bottomSubmitButton.classList.toggle('opacity-50', !hasItem);
            bottomSubmitButton.classList.toggle('cursor-not-allowed', !hasItem);
            bottomSubmitButton.classList.toggle('bg-[#292522]', hasItem);
            bottomSubmitButton.classList.toggle('hover:bg-[#302F2A]', hasItem);
        }
    }

    function openCameraModal() {
      openModal(dom.cameraModal);
      if (dom.scannerLoader) dom.scannerLoader.classList.remove('hidden');
      if (dom.cameraStatus) dom.cameraStatus.textContent = 'Memuat daftar kamera...';
      loadCameras().then(() => startScanner()).catch((error) => {
        showMessage(error.message || 'Tidak dapat memuat kamera.', 'error');
        if (dom.scannerLoader) dom.scannerLoader.classList.add('hidden');
      });
    }

    function openProductModal({ product, mode = 'add', cartItem = null }) {
      if (!dom.productModal || !dom.productModalForm) return;
      const isEdit = mode === 'edit';

      dom.pmProductName.textContent = product.name || '';
      dom.pmCategory.textContent = product.category_name || '-';
      dom.pmProductId.value = product.id || '';
      dom.pmSaleType.value = product.sale_type ?? product.saleType ?? '';
      dom.pmPriceHidden.value = (cartItem && cartItem.price) ? cartItem.price : (product.price ?? product.sell_price ?? 0);
      dom.pmProductPrice.textContent = `Rp ${Number(dom.pmPriceHidden.value || 0).toLocaleString('id-ID')}`;

      // setup unit label
      const unitLabel = product.selling_unit ?? product.sellingUnit ?? 'pcs';
      dom.pmUnit.textContent = `Satuan: ${unitLabel}`;
      renderSaleTypeIndicator(dom.pmSaleType.value);

      // purchase type visibility (grosir support)
      const saleType = product.sale_type ?? product.saleType ?? '';
      const supportsGrosir = String(saleType).includes('grosir') || Number(product.wholesale_price ?? 0) > 0;
      dom.pmPurchaseTypeWrap.classList.toggle('hidden', !supportsGrosir);

      // input method visibility for gram types
      const isGram = String(saleType).includes('gram') || unitLabel === 'gram';
      dom.pmInputMethodWrap.classList.toggle('hidden', !isGram);

      // reset all radio states first
      dom.productModalForm.querySelectorAll('input[name="purchase_type"]').forEach((input) => input.checked = false);
      dom.productModalForm.querySelectorAll('input[name="input_method"]').forEach((input) => input.checked = false);

      // default purchase type
      const defaultPurchaseType = cartItem?.purchase_type || (supportsGrosir ? 'pcs' : null);
      if (defaultPurchaseType) {
        const el = dom.productModalForm.querySelector(`[name="purchase_type"][value="${defaultPurchaseType}"]`);
        if (el) el.checked = true;
      }

      if (isEdit && cartItem) {
        dom.pmQty.value = cartItem.qty ?? 1;
        if (cartItem?.input_method) {
          const el2 = dom.productModalForm.querySelector(`[name="input_method"][value="${cartItem.input_method}"]`);
          if (el2) el2.checked = true;
        }
        if (cartItem?.input_method === 'nominal') {
          dom.pmNominalWrap.classList.remove('hidden');
          dom.pmNominal.value = cartItem.subtotal ?? '';
          dom.pmQtyWrap.classList.add('hidden');
        } else {
          dom.pmNominalWrap.classList.add('hidden');
          dom.pmQtyWrap.classList.remove('hidden');
        }
        dom.pmSubmit.textContent = 'Update';
        dom.productModalForm.action = cartItem.updateUrl ?? dom.productModalForm.action;
        // attach PATCH method
        let methodInput = dom.productModalForm.querySelector('input[name="_method"]');
        if (!methodInput) {
          methodInput = document.createElement('input'); methodInput.type = 'hidden'; methodInput.name = '_method';
          dom.productModalForm.appendChild(methodInput);
        }
        methodInput.value = 'PATCH';
      } else {
        dom.pmQty.value = 1;
        dom.pmNominal.value = '';
        dom.pmSubmit.textContent = 'Tambah';
        dom.productModalForm.action = `{{ url('/pos/cart') }}/${product.id}`;
        const methodInput = dom.productModalForm.querySelector('input[name="_method"]'); if (methodInput) methodInput.remove();
      }

      dom.pmWholesaleMessage.classList.add('hidden');
      updateModalPriceFromPurchaseType();
      computeProductModalSubtotal();
      openModal(dom.productModal);
      // focus qty or nominal
      setTimeout(() => {
        if (!dom.pmQty) return;
        dom.pmQty.focus(); dom.pmQty.select();
      }, 50);
    }

    function resetProductModal() {
      if (!dom.productModalForm) return;
      dom.productModalForm.reset();
      dom.pmProductId.value = '';
      dom.pmSaleType.value = '';
      dom.pmPriceHidden.value = '';
      dom.pmProductName.textContent = '';
      dom.pmCategory.textContent = '';
      dom.pmProductPrice.textContent = '';
      dom.pmStock.textContent = '';
      dom.pmUnit.textContent = '';
      dom.pmWholesaleMessage.classList.add('hidden');
      dom.pmWholesalePriceWrap.classList.add('hidden');
      dom.pmWholesaleMinQtyWrap.classList.add('hidden');
      dom.pmNominalWrap.classList.add('hidden');
      dom.pmQtyWrap.classList.remove('hidden');
      dom.pmQty.value = 1;
      dom.pmNominal.value = '';
      dom.pmSubtotal.textContent = 'Rp 0';
      dom.pmSubmit.textContent = 'Tambah';
      dom.productModalForm.removeAttribute('action');
      const methodInput = dom.productModalForm.querySelector('input[name="_method"]');
      if (methodInput) methodInput.remove();
    }

    function closeProductModal() {
      closeModal(dom.productModal);
      resetProductModal();
      focusBarcode();
    }

    function renderSaleTypeIndicator(saleType) {
      dom.pmSaleTypeRadios.forEach((radio) => {
        radio.checked = radio.value === saleType;
      });
    }

    function updateModalPriceFromPurchaseType() {
      const saleType = dom.pmSaleType.value || '';
      const purchaseTypeEl = dom.productModalForm.querySelector('input[name="purchase_type"]:checked');
      const purchaseType = purchaseTypeEl ? purchaseTypeEl.value : null;
      const productPrice = Number(window.__currentScanProduct?.price || 0);
      const wholesalePrice = Number(window.__currentScanProduct?.wholesale_price || 0);
      const wholesalePricePerGram = Number(window.__currentScanProduct?.wholesale_price_per_gram || 0);
      const unitLabel = String(dom.pmUnit.textContent || 'pcs').replace('Satuan: ', '') || 'pcs';
      const selectedPrice = purchaseType === 'grosir' && wholesalePrice > 0 ? wholesalePrice : productPrice;
      const primaryDisplayPrice = String(saleType).includes('gram')
        ? Number(window.__currentScanProduct?.price_per_gram || 0)
        : selectedPrice;
      const formattedPrice = Number(primaryDisplayPrice || 0).toLocaleString('id-ID');

      dom.pmPriceHidden.value = selectedPrice;
      dom.pmProductPrice.textContent = `Rp ${formattedPrice}`;
      dom.pmPrimaryPriceLabel.textContent = String(saleType).includes('gram') ? 'Harga per Gram' : `Harga per ${unitLabel}`;

      if (String(saleType).includes('gram') && wholesalePricePerGram > 0) {
        dom.pmWholesalePriceWrap.classList.remove('hidden');
        dom.pmWholesalePrice.textContent = `Rp ${Number(wholesalePricePerGram).toLocaleString('id-ID')}`;
      } else {
        dom.pmWholesalePriceWrap.classList.add('hidden');
      }

      if (String(saleType).includes('grosir') && Number(window.__currentScanProduct?.wholesale_min_qty || 0) > 0) {
        dom.pmWholesaleMinQtyWrap.classList.remove('hidden');
        dom.pmWholesaleMinQty.textContent = `${Number(window.__currentScanProduct.wholesale_min_qty).toLocaleString('id-ID')} ${String(saleType).includes('gram') ? 'gram' : unitLabel}`;
      } else {
        dom.pmWholesaleMinQtyWrap.classList.add('hidden');
      }

      const stockValue = Number(window.__currentScanProduct?.stock || 0);
      dom.pmStock.textContent = `${stockValue.toLocaleString('id-ID')} ${String(saleType).includes('gram') ? 'gram' : unitLabel}`;
    }

    function computeProductModalSubtotal() {
      const price = Number(dom.pmPriceHidden.value || 0);
      const saleType = dom.pmSaleType.value || '';
      const isGram = String(saleType).includes('gram') || dom.pmUnit.textContent.includes('gram');
      const purchaseTypeEl = dom.productModalForm.querySelector('input[name="purchase_type"]:checked');
      const purchaseType = purchaseTypeEl ? purchaseTypeEl.value : null;
      const inputMethodEl = dom.productModalForm.querySelector('input[name="input_method"]:checked');
      const inputMethod = inputMethodEl ? inputMethodEl.value : 'berat';

      let qty = Number(dom.pmQty.value || 0);
      let subtotal = 0;

      if (isGram) {
        if (inputMethod === 'nominal') {
          const nominal = Number(dom.pmNominal.value || 0);
          subtotal = nominal;
          qty = price > 0 ? (nominal / price) * 100 : 0;
        } else {
          subtotal = price * (qty / 100);
        }
      } else {
        subtotal = price * qty;
      }

      dom.pmSubtotal.textContent = `Rp ${Number(subtotal || 0).toLocaleString('id-ID')}`;
      if (inputMethod === 'nominal' && dom.pmQty) {
        dom.pmQty.value = Math.round(qty);
      }
      return { qty, subtotal };
    }

    async function processBarcode(barcode) {
      if (!barcode) {
        showMessage('Barcode kosong.', 'error');
        focusBarcode();
        return;
      }
      showMessage('Mengirim barcode ke sistem...', 'info');
      try {
        const response = await fetch(`{{ route('pos.scan-barcode') }}?barcode=${encodeURIComponent(barcode)}`, {
          method: 'GET',
          credentials: 'same-origin',
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        });
        const payload = await response.json();
        if (!response.ok || !payload.success) {
          throw new Error(payload.message || 'Produk tidak ditemukan');
        }
        // store last scanned product metadata for modal logic
        window.__currentScanProduct = payload.product;
        const saleType = String(payload.product.sale_type || payload.product.saleType || '');
        const requiresModal = payload.product.is_tembakau || saleType.includes('gram') || saleType.includes('grosir');

        if (requiresModal) {
          openProductModal({ product: payload.product, mode: 'add' });
        } else {
          await submitAddToCart(payload.product.id, { qty: 1 });
        }
      } catch (error) {
        showMessage(error.message || 'Gagal memproses barcode.', 'error');
        focusBarcode();
      }
    }
    function openEditFromCard(card) {
      const product = {
        id: card.dataset.itemProductId || card.dataset.productId || null,
        name: card.dataset.itemName || card.dataset.itemName || '',
        price: Number(card.dataset.itemPriceRaw || card.dataset.itemPrice || 0),
        selling_unit: card.dataset.itemUnit || '',
        sale_type: card.dataset.itemSaleType || '',
      };

      // derive price_per_gram from stored price: assume stored price is per ons
      window.__currentScanProduct = {
        price: product.price,
        price_unit: 'ons',
        price_per_gram: product.price / 100,
        wholesale_price: Number(card.dataset.itemWholesalePrice || 0),
        wholesale_min_qty: Number(card.dataset.itemWholesaleMinQty || 0),
      };

      const cartItem = {
        qty: Number(card.dataset.itemQty || 1),
        purchase_type: card.dataset.itemPurchaseType || null,
        input_method: card.dataset.itemInputMethod || null,
        subtotal: null,
        updateUrl: card.dataset.itemUpdateUrl || null,
        price: Number(card.dataset.itemPriceRaw || 0),
        wholesale_price: Number(card.dataset.itemWholesalePrice || 0),
        wholesale_min_qty: Number(card.dataset.itemWholesaleMinQty || 0),
      };

      openProductModal({ product, mode: 'edit', cartItem });
    }

    document.addEventListener('DOMContentLoaded', () => {
      focusBarcode();
      dom.openCameraScan?.addEventListener('click', openCameraModal);
      dom.closeCameraModal?.addEventListener('click', () => { closeModal(dom.cameraModal); stopScanner(); focusBarcode(); });
      dom.startCameraScan?.addEventListener('click', startScanner);
      dom.stopCameraScan?.addEventListener('click', stopScanner);

      dom.barcodeInput?.addEventListener('keydown', handleBarcodeEnter);
      bindCartItemEvents();

      dom.saveOrderForm?.addEventListener('submit', async (event) => {
        event.preventDefault();
        const form = dom.saveOrderForm;
        if (!form) return;

        const action = form.action;
        const formData = new FormData(form);
        formData.append('_token', dom.csrfToken);

        try {
          const response = await fetch(action, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
              'X-CSRF-TOKEN': dom.csrfToken,
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json',
            },
            body: formData,
          });

          let result = null;
          const contentType = response.headers.get('Content-Type') || '';
          if (contentType.includes('application/json')) {
            result = await response.json();
          }

          if (!response.ok || !result?.success) {
            const errorMessage = result?.message || 'Gagal menyimpan pesanan.';
            showMessage(errorMessage, 'error');
            focusBarcode();
            return;
          }

          if (result.saved_orders_html) {
            updateSavedOrders(result.saved_orders_html);
          }

          if (result.cart_html) {
            updateCartUI(result.cart_html, result.summary ?? {});
          }

          if (result.checkout_html) {
            updateCheckoutItems(result.checkout_html);
          }

          if (result.summary) {
            updateSummaryUI(result.summary);
          }

          updateCheckoutButtons(result.cart_count ?? 0);
          showMessage(result.message || 'Pesanan berhasil disimpan', 'success');
          focusBarcode();
        } catch (error) {
          showMessage(error?.message || 'Gagal menyimpan pesanan.', 'error');
          focusBarcode();
        }
      });

      const initialCartCount = Number(document.body.dataset.cartCount || 0);
      updateCheckoutButtons(initialCartCount);
      bindSavedOrderEvents();

      // modal controls
      dom.closeProductModal?.addEventListener('click', closeProductModal);
      dom.pmCancel?.addEventListener('click', closeProductModal);
      dom.pmMinus?.addEventListener('click', () => { dom.pmQty.value = Math.max(Number(dom.pmQty.min || 1), Number(dom.pmQty.value || 0) - Number(dom.pmQty.step || 1)); computeProductModalSubtotal(); });
      dom.pmPlus?.addEventListener('click', () => { dom.pmQty.value = Number(dom.pmQty.value || 0) + Number(dom.pmQty.step || 1); computeProductModalSubtotal(); });
      dom.pmQty?.addEventListener('input', computeProductModalSubtotal);
      dom.pmNominal?.addEventListener('input', computeProductModalSubtotal);
      // change listeners for radios
      dom.productModalForm?.querySelectorAll('input[name="purchase_type"]').forEach((el) => el.addEventListener('change', (event) => {
        updateModalPriceFromPurchaseType();
        computeProductModalSubtotal();
      }));
      dom.productModalForm?.querySelectorAll('input[name="input_method"]').forEach((el) => el.addEventListener('change', (e) => {
        const val = e.target.value;
        if (val === 'nominal') {
          dom.pmNominalWrap.classList.remove('hidden');
          dom.pmQtyWrap.classList.add('hidden');
        } else {
          dom.pmNominalWrap.classList.add('hidden');
          dom.pmQtyWrap.classList.remove('hidden');
        }
        computeProductModalSubtotal();
      }));

      dom.paymentMethodCards?.forEach((card) => {
        card.addEventListener('click', () => {
          selectPaymentMethod(card.dataset.paymentType || 'cash');
        });
      });
      dom.paymentMethodRadios?.forEach((radio) => {
        radio.addEventListener('change', (event) => {
          selectPaymentMethod(event.target.dataset.type || 'cash');
        });
      });
      dom.exactAmountBtn?.addEventListener('click', () => {
        const total = getGrandTotalValue();
        if (!dom.paidAmount) return;
        dom.paidAmount.value = total;
        updatePaymentCalculation();
      });
      dom.roundAmountBtn?.addEventListener('click', () => {
        const total = getGrandTotalValue();
        const roundTo = 10000;
        if (!dom.paidAmount) return;
        dom.paidAmount.value = Math.ceil(total / roundTo) * roundTo;
        updatePaymentCalculation();
      });
      dom.paidAmount?.addEventListener('input', () => {
        if (dom.paymentMethodHidden?.value !== 'cash') return;
        updatePaymentCalculation();
      });

      // form submit will be normal POST/PATCH to server
      dom.productModalForm?.addEventListener('submit', async (e) => {
        const methodInput = dom.productModalForm.querySelector('input[name="_method"]');
        const isPatch = methodInput?.value?.toUpperCase() === 'PATCH';
        if (isPatch) {
          return; // preserve default update flow for cart item edits
        }

        e.preventDefault();

        const productId = dom.pmProductId.value;
        if (!productId) {
          showMessage('ID produk tidak valid. Tidak dapat menambahkan ke keranjang.', 'error');
          return;
        }

        const saleType = dom.pmSaleType.value || '';
        const isGram = String(saleType).includes('gram') || dom.pmUnit.textContent.includes('gram');
        const purchaseTypeEl = dom.productModalForm.querySelector('input[name="purchase_type"]:checked');
        const purchaseType = purchaseTypeEl ? purchaseTypeEl.value : null;
        const inputMethodEl = dom.productModalForm.querySelector('input[name="input_method"]:checked');
        const inputMethod = inputMethodEl ? inputMethodEl.value : 'berat';
        const qty = Number(dom.pmQty.value || 0);
        const nominal = Number(dom.pmNominal.value || 0);

        if (isGram) {
          if (inputMethod === 'nominal') {
            if (!nominal || nominal <= 0) { showMessage('Nominal harus lebih besar dari 0.', 'error'); return; }
          } else {
            if (!qty || qty <= 0) { showMessage('Berat harus lebih besar dari 0.', 'error'); return; }
          }
        } else {
          if (!qty || qty < 1) { showMessage('Qty minimal 1.', 'error'); return; }
        }

        if (purchaseType === 'grosir') {
          const min = Number(window.__currentScanProduct?.wholesale_min_qty || 0);
          const unitLabel = isGram ? 'gram' : 'pcs';
          const checkQty = isGram && inputMethod === 'nominal'
            ? computeProductModalSubtotal().qty
            : Number(dom.pmQty.value || 0);
          if (min > 0 && checkQty < min) {
            showMessage(`Minimal pembelian grosir adalah ${min} ${unitLabel}.`, 'error');
            return;
          }
        }

        const price = dom.pmPriceHidden.value;
        const fields = {
          qty: dom.pmQty.value || 1,
          price,
        };

        if (purchaseType !== null) fields.purchase_type = purchaseType;
        if (inputMethod !== null) fields.input_method = inputMethod;
        if (isGram && inputMethod === 'nominal') fields.qty = computeProductModalSubtotal().qty;

        const result = await submitAddToCart(productId, fields);
        if (result) {
          closeProductModal();
          if (dom.productModal) {
            dom.productModal.classList.add('hidden');
            dom.productModal.classList.remove('flex');
          }
        }
      });

      dom.productModal?.addEventListener('click', (event) => { if (event.target === dom.productModal) closeProductModal(); });
      dom.checkoutModal?.addEventListener('click', (event) => { if (event.target === dom.checkoutModal) closeModal(dom.checkoutModal); });
      dom.openCheckoutButton?.addEventListener('click', (event) => {
        event.preventDefault();
        generateQuickAmounts();
        setSelectedPaymentMethod(dom.paymentMethodHidden?.value || 'cash');
        openModal(dom.checkoutModal);
      });
      dom.closeCheckoutModal?.addEventListener('click', () => closeModal(dom.checkoutModal));
      dom.closeCheckoutModalBottom?.addEventListener('click', () => closeModal(dom.checkoutModal));

      document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
          if (dom.cameraModal && !dom.cameraModal.classList.contains('hidden')) { closeModal(dom.cameraModal); stopScanner(); focusBarcode(); }
          if (dom.productModal && !dom.productModal.classList.contains('hidden')) closeProductModal();
          if (dom.checkoutModal && !dom.checkoutModal.classList.contains('hidden')) closeModal(dom.checkoutModal);
        }
      });

      window.addEventListener('beforeunload', stopScanner);
    });
  </script>

@endsection
