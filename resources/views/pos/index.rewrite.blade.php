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
            <p class="mt-2 text-2xl font-bold text-[#292522]">{{ $totalItems }}</p>
          </div>
          <div class="rounded-2xl border border-[#E7E1D9] bg-white p-4 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Subtotal</p>
            <p class="mt-2 text-2xl font-bold text-[#8A5B1E]">Rp {{ number_format($subtotal, 0, ',', '.') }}</p>
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
          <input id="barcode_input" type="text" placeholder="Scan barcode atau ketik lalu tekan Enter..." class="w-full rounded-2xl border border-[#D8D3C9] bg-white px-4 py-4 text-sm text-[#292522] outline-none transition duration-200 hover:border-[#B8B1A4] focus:border-[#292522] focus:ring-4 focus:ring-[#292522]/10" autocomplete="off" autofocus>
        </div>
        <div id="barcode_message" class="mt-3 hidden rounded-2xl p-3 text-sm"></div>
      </div>
    </section>

    <div class="grid gap-6 lg:grid-cols-[1.75fr_1.05fr]">
      <section class="rounded-[28px] border border-[#E7E1D9] bg-[#FBF9F6] p-6 shadow-sm">
        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-[#B47727]">Daftar Transaksi</p>
            <h2 class="mt-1 text-xl font-bold text-[#292522]">Item yang Masuk</h2>
          </div>
          <p class="text-sm text-[#6B4F3A]">Hanya daftar transaksi. Produk dari scan barcode.</p>
        </div>

        <div class="max-h-[calc(100vh-26rem)] overflow-y-auto space-y-4 pb-4 pr-2">
          @forelse ($cart as $item)
            @php
              $isTembakauItem = (bool) ($item['is_tembakau'] ?? false);
              $itemUnitLabel = $isTembakauItem ? 'gram' : ($item['unit'] ?? 'pcs');
              $itemLineTotal = $isTembakauItem
                ? $item['price'] * ($item['qty'] / 100)
                : $item['price'] * $item['qty'];
              $itemMinValue = $isTembakauItem ? 100 : 1;
              $itemStepValue = $isTembakauItem ? 100 : 1;
            @endphp

            <div class="rounded-[22px] border border-[#E7E1D9] bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-[#B47727] hover:bg-[#FCF8F2]" role="button" tabindex="0" data-item-edit-trigger data-item-name="{{ e($item['name']) }}" data-item-price="{{ number_format($item['price'], 0, ',', '.') }}" data-item-qty="{{ $item['qty'] }}" data-item-unit="{{ $itemUnitLabel }}" data-item-step="{{ $itemStepValue }}" data-item-min="{{ $itemMinValue }}" data-item-is-tembakau="{{ $isTembakauItem ? '1' : '0' }}" data-item-update-url="{{ route('pos.cart.update', ['product' => $item['product_id']]) }}">
              <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <p class="truncate text-sm font-bold text-[#292522]">{{ $item['name'] }}</p>
                  <p class="mt-1 text-xs text-[#6B4F3A]">Rp {{ number_format($item['price'], 0, ',', '.') }} / {{ $itemUnitLabel }}</p>
                </div>
                <form method="POST" action="{{ route('pos.cart.remove', ['product' => $item['product_id']]) }}" class="flex-shrink-0" data-item-delete-form>
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="text-xs font-bold text-[#8A5B1E] transition hover:text-[#B47727]">Hapus</button>
                </form>
              </div>

              <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="rounded-2xl bg-[#F4EFE6] px-3 py-2 text-sm font-semibold text-[#6B4F3A]">{{ $item['qty'] }} {{ $itemUnitLabel }}</div>
                <div class="text-right">
                  <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#8A5B1E]">Total</p>
                  <p class="mt-1 text-lg font-bold text-[#8A5B1E]">Rp {{ number_format($itemLineTotal, 0, ',', '.') }}</p>
                </div>
              </div>
            </div>
          @empty
            <div class="rounded-2xl bg-[#FAF9F6] p-6 text-center">
              <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-[#F4EFE6] text-[#B47727]">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h2l2.4 12.2a2 2 0 002 1.6h7.8a2 2 0 001.9-1.4L21 8H6" /></svg>
              </div>
              <p class="mt-3 text-sm text-[#6B4F3A]">Keranjang kosong. Scan barcode untuk memulai transaksi.</p>
            </div>
          @endforelse
        </div>
      </section>

      <section class="rounded-[28px] border border-[#E7E1D9] bg-[#FBF9F6] p-6 shadow-sm lg:sticky lg:top-24 lg:h-fit">
        <div class="flex h-full min-h-0 flex-col">
          <div class="mb-5">
            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-[#B47727]">Ringkasan</p>
            <h2 class="mt-1 text-xl font-bold text-[#292522]">Total Pembayaran</h2>
          </div>

          <div class="space-y-3">
            <div class="rounded-2xl bg-[#F4EFE6] p-4"><div class="flex items-center justify-between"><span class="text-sm font-semibold text-[#6B4F3A]">Jumlah Item</span><span class="text-lg font-bold text-[#292522]">{{ $totalItems }}</span></div></div>
            <div class="rounded-2xl bg-[#F4EFE6] p-4"><div class="flex items-center justify-between"><span class="text-sm font-semibold text-[#6B4F3A]">Total Gram Tembakau</span><span class="text-lg font-bold text-[#292522]">{{ $gramTotal }} gram</span></div></div>
            <div class="rounded-2xl bg-[#F4EFE6] p-4"><div class="flex items-center justify-between"><span class="text-sm font-semibold text-[#6B4F3A]">Subtotal</span><span class="text-lg font-bold text-[#8A5B1E]">Rp {{ number_format($subtotal, 0, ',', '.') }}</span></div></div>
            <div class="rounded-[22px] bg-[#292522] p-4 shadow-sm"><div class="flex items-center justify-between"><span class="text-sm font-semibold text-white">Grand Total</span><div class="flex items-center gap-1 text-2xl font-bold text-[#D89A5B]"><span class="text-lg">Rp</span><span>{{ number_format($grandTotal, 0, ',', '.') }}</span></div></div></div>
          </div>

          <div class="mt-auto border-t border-[#E7E1D9] pt-4">
            @if ($cartCount > 0)
              <div class="flex flex-col gap-3 sm:flex-row">
                <form action="{{ route('saved-orders.save') }}" method="POST" class="flex-1">@csrf<button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl border border-[#292522] bg-white px-4 py-3 text-sm font-bold text-[#292522] transition hover:bg-gray-100">Simpan</button></form>
                <a href="{{ route('pos.checkout.page') }}" class="inline-flex flex-1 items-center justify-center rounded-2xl bg-[#292522] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#302F2A]">Bayar</a>
              </div>
            @else
              <div class="flex flex-col gap-3 sm:flex-row">
                <button disabled class="inline-flex w-full items-center justify-center rounded-2xl border border-gray-300 bg-gray-200 px-4 py-3 text-sm font-bold text-gray-500 cursor-not-allowed">Simpan</button>
                <button disabled class="inline-flex w-full items-center justify-center rounded-2xl bg-gray-300 px-4 py-3 text-sm font-bold text-white cursor-not-allowed">Bayar</button>
              </div>
            @endif
          </div>
        </div>
      </section>
    </div>

    <div id="tembakauWeightModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/60 p-4">
      <div class="w-full max-w-md rounded-[28px] border border-[#E7E1D9] bg-[#FBF9F6] p-5 shadow-2xl sm:p-6">
        <div class="flex items-start justify-between gap-3">
          <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-[#B47727]">Tembakau</p>
            <h3 class="mt-1 text-xl font-bold text-[#292522]">Masukkan Berat</h3>
          </div>
          <button id="closeTembakauWeightModal" type="button" class="rounded-xl bg-[#F4EFE6] px-3 py-2 text-xs font-bold text-[#8A5B1E] transition hover:bg-[#E8DFD0]">Batal</button>
        </div>
        <div class="mt-5 space-y-4">
          <div>
            <p class="text-sm font-semibold text-[#292522]">Produk</p>
            <p id="tembakauProductName" class="mt-1 text-base font-bold text-[#292522]"></p>
          </div>
          <div>
            <p class="text-sm font-semibold text-[#292522]">Harga</p>
            <p id="tembakauProductPrice" class="mt-1 text-base font-semibold text-[#8A5B1E]"></p>
          </div>
          <div>
            <label for="tembakauWeightInput" class="mb-2 block text-sm font-semibold text-[#292522]">Berat</label>
            <div class="flex items-center gap-3">
              <input id="tembakauWeightInput" type="number" min="1" step="1" class="w-full rounded-2xl border border-[#D8D3C9] bg-white px-4 py-3 text-sm text-[#292522] outline-none transition duration-200 focus:border-[#292522] focus:ring-4 focus:ring-[#292522]/10" placeholder="Masukkan berat dalam gram">
              <span class="text-sm font-semibold text-[#6B4F3A]">gram</span>
            </div>
          </div>
          <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
            <button id="cancelTembakauWeightModal" type="button" class="rounded-2xl border border-[#D8D3C9] bg-white px-4 py-3 text-sm font-semibold text-[#6B4F3A] transition hover:bg-[#F4EFE6]">Batal</button>
            <button id="submitTembakauWeight" type="button" class="rounded-2xl bg-[#292522] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#3A352F]">Tambah</button>
          </div>
        </div>
      </div>
    </div>

    <div id="itemEditModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/60 p-4">
      <div class="w-full max-w-md rounded-[28px] border border-[#E7E1D9] bg-[#FBF9F6] p-5 shadow-2xl sm:p-6">
        <div class="flex items-start justify-between gap-3">
          <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-[#B47727]">Edit Item</p>
            <h3 class="mt-1 text-xl font-bold text-[#292522]">Ubah Qty / Berat</h3>
          </div>
          <button id="closeItemEditModal" type="button" class="rounded-xl bg-[#F4EFE6] px-3 py-2 text-xs font-bold text-[#8A5B1E] transition hover:bg-[#E8DFD0]">Batal</button>
        </div>
        <form id="itemEditForm" method="POST" class="mt-5 space-y-4">
          @csrf
          @method('PATCH')
          <div>
            <p class="text-sm font-semibold text-[#292522]">Nama Produk</p>
            <p id="itemEditName" class="mt-1 text-base font-bold text-[#292522]"></p>
          </div>
          <div>
            <p class="text-sm font-semibold text-[#292522]">Harga</p>
            <p id="itemEditPrice" class="mt-1 text-base font-semibold text-[#8A5B1E]"></p>
          </div>
          <div>
            <label id="itemEditLabel" for="itemEditQty" class="mb-2 block text-sm font-semibold text-[#292522]">Qty</label>
            <div class="flex items-center gap-2">
              <button type="button" id="itemEditMinus" class="flex h-11 w-11 items-center justify-center rounded-2xl border border-[#D8D3C9] bg-white text-xl font-bold text-[#292522] transition hover:bg-[#F4EFE6]">−</button>
              <input id="itemEditQty" name="qty" type="number" min="1" step="1" class="w-full rounded-2xl border border-[#D8D3C9] bg-white px-3 py-3 text-sm text-[#292522] outline-none transition duration-200 focus:border-[#292522] focus:ring-4 focus:ring-[#292522]/10">
              <button type="button" id="itemEditPlus" class="flex h-11 w-11 items-center justify-center rounded-2xl border border-[#D8D3C9] bg-white text-xl font-bold text-[#292522] transition hover:bg-[#F4EFE6]">+</button>
            </div>
            <p id="itemEditUnit" class="mt-2 text-sm text-[#6B4F3A]"></p>
          </div>
          <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
            <button type="button" id="cancelItemEditModal" class="rounded-2xl border border-[#D8D3C9] bg-white px-4 py-3 text-sm font-semibold text-[#6B4F3A] transition hover:bg-[#F4EFE6]">Batal</button>
            <button type="submit" class="rounded-2xl bg-[#292522] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#3A352F]">Update</button>
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
      tembakauWeightModal: document.getElementById('tembakauWeightModal'),
      tembakauProductName: document.getElementById('tembakauProductName'),
      tembakauProductPrice: document.getElementById('tembakauProductPrice'),
      tembakauWeightInput: document.getElementById('tembakauWeightInput'),
      cancelTembakauWeightModal: document.getElementById('cancelTembakauWeightModal'),
      closeTembakauWeightModal: document.getElementById('closeTembakauWeightModal'),
      submitTembakauWeight: document.getElementById('submitTembakauWeight'),
      itemEditModal: document.getElementById('itemEditModal'),
      itemEditForm: document.getElementById('itemEditForm'),
      itemEditName: document.getElementById('itemEditName'),
      itemEditPrice: document.getElementById('itemEditPrice'),
      itemEditQty: document.getElementById('itemEditQty'),
      itemEditLabel: document.getElementById('itemEditLabel'),
      itemEditUnit: document.getElementById('itemEditUnit'),
      itemEditMinus: document.getElementById('itemEditMinus'),
      itemEditPlus: document.getElementById('itemEditPlus'),
      closeItemEditModal: document.getElementById('closeItemEditModal'),
      cancelItemEditModal: document.getElementById('cancelItemEditModal'),
    };

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
      dom.barcodeInput.focus();
      dom.barcodeInput.select();
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

    function submitAddToCart(productId, qty = null) {
      const action = `{{ url('/pos/cart') }}/${productId}`;
      const form = buildForm(action, qty !== null ? { qty } : {});
      form.submit();
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

    function openCameraModal() {
      openModal(dom.cameraModal);
      if (dom.scannerLoader) dom.scannerLoader.classList.remove('hidden');
      if (dom.cameraStatus) dom.cameraStatus.textContent = 'Memuat daftar kamera...';
      loadCameras().then(() => startScanner()).catch((error) => {
        showMessage(error.message || 'Tidak dapat memuat kamera.', 'error');
        if (dom.scannerLoader) dom.scannerLoader.classList.add('hidden');
      });
    }

    function openTembakauModal(product) {
      state.currentTembakauProduct = product;
      if (dom.tembakauProductName) dom.tembakauProductName.textContent = product.name;
      if (dom.tembakauProductPrice) dom.tembakauProductPrice.textContent = `Rp ${Number(product.price).toLocaleString('id-ID')}`;
      if (dom.tembakauWeightInput) {
        dom.tembakauWeightInput.value = '';
        dom.tembakauWeightInput.focus();
      }
      openModal(dom.tembakauWeightModal);
    }

    function closeTembakauModal() {
      closeModal(dom.tembakauWeightModal);
      state.currentTembakauProduct = null;
      focusBarcode();
    }

    function submitTembakauWeight() {
      if (!state.currentTembakauProduct || !dom.tembakauWeightInput) return;
      const weight = Number(dom.tembakauWeightInput.value);
      if (!weight || weight <= 0) {
        showMessage('Masukkan berat tembakau yang valid.', 'error');
        dom.tembakauWeightInput.focus();
        return;
      }
      submitAddToCart(state.currentTembakauProduct.id, weight);
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
          headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        });
        const payload = await response.json();
        if (!response.ok || !payload.success) {
          throw new Error(payload.message || 'Produk tidak ditemukan');
        }
        if (payload.product.is_tembakau) {
          openTembakauModal(payload.product);
        } else {
          submitAddToCart(payload.product.id);
        }
      } catch (error) {
        showMessage(error.message || 'Gagal memproses barcode.', 'error');
        focusBarcode();
      }
    }

    function openItemEditModal(card) {
      if (!dom.itemEditModal || !dom.itemEditForm) return;
      const isTembakau = card.dataset.itemIsTembakau === '1';
      if (dom.itemEditName) dom.itemEditName.textContent = card.dataset.itemName || '';
      if (dom.itemEditPrice) dom.itemEditPrice.textContent = `Rp ${card.dataset.itemPrice || '0'}`;
      if (dom.itemEditQty) {
        dom.itemEditQty.value = card.dataset.itemQty || '1';
        dom.itemEditQty.min = card.dataset.itemMin || '1';
        dom.itemEditQty.step = card.dataset.itemStep || '1';
      }
      if (dom.itemEditLabel) dom.itemEditLabel.textContent = isTembakau ? 'Berat (gram)' : 'Qty';
      if (dom.itemEditUnit) dom.itemEditUnit.textContent = isTembakau ? 'Satuan: gram' : `Satuan: ${card.dataset.itemUnit || 'pcs'}`;
      dom.itemEditForm.action = card.dataset.itemUpdateUrl || '';
      openModal(dom.itemEditModal);
      if (dom.itemEditQty) {
        dom.itemEditQty.focus();
        dom.itemEditQty.select();
      }
    }

    function closeItemEditModal() {
      closeModal(dom.itemEditModal);
      focusBarcode();
    }

    function changeItemEditQty(delta) {
      if (!dom.itemEditQty) return;
      const step = Number(dom.itemEditQty.step || 1);
      const min = Number(dom.itemEditQty.min || 1);
      const value = Number(dom.itemEditQty.value || min) + delta * step;
      dom.itemEditQty.value = Math.max(min, value);
    }

    document.addEventListener('DOMContentLoaded', () => {
      focusBarcode();
      dom.openCameraScan?.addEventListener('click', openCameraModal);
      dom.closeCameraModal?.addEventListener('click', () => { closeModal(dom.cameraModal); stopScanner(); });
      dom.startCameraScan?.addEventListener('click', startScanner);
      dom.stopCameraScan?.addEventListener('click', stopScanner);
      dom.cancelTembakauWeightModal?.addEventListener('click', closeTembakauModal);
      dom.closeTembakauWeightModal?.addEventListener('click', closeTembakauModal);
      dom.submitTembakauWeight?.addEventListener('click', submitTembakauWeight);

      dom.barcodeInput?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
          event.preventDefault();
          processBarcode(dom.barcodeInput.value.trim());
        }
      });

      document.querySelectorAll('[data-item-edit-trigger]').forEach((card) => {
        card.addEventListener('click', (event) => {
          if (event.target.closest('[data-item-delete-form]')) return;
          openItemEditModal(card);
        });
        card.addEventListener('keydown', (event) => {
          if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            openItemEditModal(card);
          }
        });
      });
      document.querySelectorAll('[data-item-delete-form] button').forEach((button) => {
        button.addEventListener('click', (event) => event.stopPropagation());
      });

      dom.itemEditMinus?.addEventListener('click', () => changeItemEditQty(-1));
      dom.itemEditPlus?.addEventListener('click', () => changeItemEditQty(1));
      dom.closeItemEditModal?.addEventListener('click', closeItemEditModal);
      dom.cancelItemEditModal?.addEventListener('click', closeItemEditModal);
      dom.itemEditModal?.addEventListener('click', (event) => { if (event.target === dom.itemEditModal) closeItemEditModal(); });
      dom.cameraModal?.addEventListener('click', (event) => { if (event.target === dom.cameraModal) { closeModal(dom.cameraModal); stopScanner(); } });
      dom.tembakauWeightModal?.addEventListener('click', (event) => { if (event.target === dom.tembakauWeightModal) closeTembakauModal(); });

      document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
          if (dom.cameraModal && !dom.cameraModal.classList.contains('hidden')) { closeModal(dom.cameraModal); stopScanner(); }
          if (dom.tembakauWeightModal && !dom.tembakauWeightModal.classList.contains('hidden')) closeTembakauModal();
          if (dom.itemEditModal && !dom.itemEditModal.classList.contains('hidden')) closeItemEditModal();
        }
      });

      window.addEventListener('beforeunload', stopScanner);
    });
  </script>

@endsection
