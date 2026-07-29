@extends('layouts.cashier')

@section('title', 'Checkout Pembayaran')

@section('content')
  @php
  $gramTotal = collect($cart ?? [])
      ->where('is_tembakau', true)
      ->sum('qty');

  $itemTotal = collect($cart ?? [])
      ->where('is_tembakau', false)
      ->sum('qty');

  $cartProductCount = collect($cart ?? [])->count();
  @endphp

  <div class="space-y-6">
    <section class="rounded-[28px] border border-[#E7E1D9] bg-[#FBF9F6] p-5 shadow-sm sm:p-6">
      <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-[#B47727]">
            Bayar
          </p>
          <h1 class="mt-2 text-2xl font-bold tracking-tight text-[#292522] sm:text-3xl">
            Checkout Pembayaran
          </h1>
          <p class="mt-2 text-sm text-[#6B4F3A]">
            Pastikan data transaksi sudah benar sebelum menyelesaikan pembayaran.
          </p>
        </div>

        <a href="{{ route('pos.index') }}" class="inline-flex items-center justify-center rounded-2xl bg-[#F4EFE6] px-4 py-3 text-sm font-bold text-[#8A5B1E] transition hover:bg-[#E8DFD0]">
          Kembali ke POS
        </a>
      </div>
    </section>

    <div class="grid gap-6 xl:grid-cols-[1fr_1fr]">
      <section class="rounded-[28px] border border-[#E7E1D9] bg-[#FBF9F6] p-5 shadow-sm sm:p-6">
        <div class="flex items-center justify-between gap-3">
          <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-[#B47727]">
              Ringkasan
            </p>
            <h2 class="mt-1 text-xl font-bold text-[#292522]">
              Detail Transaksi
            </h2>
          </div>
        </div>

        <div class="mt-5 space-y-3">
          <div class="flex items-center justify-between rounded-2xl bg-[#FAF9F6] px-4 py-3">
            <span class="text-sm font-bold text-[#6B4F3A]">Jumlah Item</span>
            <span class="text-right text-lg font-bold text-[#8A5B1E]">

                @if($gramTotal > 0)
                    <div>{{ number_format($gramTotal,0,',','.') }} gram</div>
                @endif

                @if($itemTotal > 0)
                    <div>{{ number_format($itemTotal,0,',','.') }} item</div>
                @endif

            </span>
          </div>

          <div class="flex items-center justify-between rounded-2xl bg-[#FAF9F6] px-4 py-3">
            <span class="text-sm font-bold text-[#6B4F3A]">Subtotal</span>
            <span class="text-lg font-bold text-[#8A5B1E]">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
          </div>

          <div class="flex items-center justify-between rounded-2xl bg-[#FAF9F6] px-4 py-3">
            <span class="text-sm font-bold text-[#6B4F3A]">Diskon</span>
            <span class="text-lg font-bold text-[#8A5B1E]">Rp {{ number_format($discount ?? 0, 0, ',', '.') }}</span>
          </div>

          <div class="flex items-center justify-between rounded-2xl bg-[#FAF9F6] px-4 py-3">
            <span class="text-sm font-bold text-[#6B4F3A]">Pajak</span>
            <span class="text-lg font-bold text-[#8A5B1E]">Rp {{ number_format($taxAmount, 0, ',', '.') }}</span>
          </div>

          <div class="rounded-2xl border border-[#E7E1D9] bg-[#292522] px-4 py-4 text-white shadow-sm">
            <div class="flex items-center justify-between gap-3">
              <span class="text-sm font-bold text-[#F4EFE6]">Grand Total</span>
              <span class="text-xl font-bold text-[#C68B59]">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
            </div>
          </div>
        </div>
      </section>

      <section class="rounded-[28px] border border-[#E7E1D9] bg-[#FBF9F6] p-5 shadow-sm sm:p-6">
        <div>
          <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-[#B47727]">
            Pembayaran
          </p>
          <h2 class="mt-1 text-xl font-bold text-[#292522]">
            Metode Pembayaran
          </h2>
        </div>

        <form method="POST" action="{{ route('pos.checkout') }}" class="mt-5 space-y-5">
          @csrf

          <div class="grid gap-3 sm:grid-cols-2">
            @foreach ($paymentMethods as $paymentMethod)
              <label class="cursor-pointer rounded-[24px] border border-[#E7E1D9] bg-white p-4 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <input
                  type="radio"
                  name="payment_method_id"
                  value="{{ $paymentMethod->id }}"
                  class="sr-only payment-method-radio"
                  data-type="{{ strtolower(str_contains($paymentMethod->name, 'qris') ? 'qris' : (str_contains(strtolower($paymentMethod->name), 'tunai') ? 'cash' : 'other')) }}"
                  {{ $loop->first ? 'checked' : '' }}
                >
                <div class="flex items-center justify-between gap-3">
                  <span class="text-sm font-bold text-[#292522]">{{ $paymentMethod->name }}</span>
                  <span class="rounded-full bg-[#F4EFE6] px-3 py-1 text-[11px] font-bold text-[#8A5B1E]">
                    {{ $paymentMethod->name }}
                  </span>
                </div>
              </label>
            @endforeach
          </div>

          <div id="cashFields" class="space-y-4">
            <div>
              <label for="paid_amount" class="mb-2 block text-sm font-bold text-[#17201C]">
                Uang Dibayar
              </label>
              <input
                id="paid_amount"
                name="paid_amount"
                type="number"
                min="0"
                value="{{ old('paid_amount') ?? $grandTotal }}"
                required
                class="w-full rounded-2xl border border-[#D8D3C9] bg-[#FAF9F6] px-4 py-3 text-sm text-[#17201C] outline-none transition focus:border-[#1B1B18] focus:bg-white focus:ring-4 focus:ring-[#1B1B18]/10"
                placeholder="Masukkan uang pembayaran"
              >
            </div>

            <div class="rounded-2xl bg-[#FAF9F6] px-4 py-3">
              <div class="flex items-center justify-between">
                <span class="text-sm font-bold text-[#6B4F3A]">Kembalian</span>
                <span id="changeAmount" class="text-lg font-bold text-[#8A5B1E]">Rp 0</span>
              </div>
            </div>
          </div>

          <div id="qrisCard" class="hidden rounded-[24px] border border-[#E7E1D9] bg-[#FAF9F6] p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
              <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-[#B47727]">
                  QRIS
                </p>
                <h3 class="mt-1 text-lg font-bold text-[#292522]">
                  Menunggu Pembayaran
                </h3>
              </div>
              <span class="rounded-full bg-[#F4EFE6] px-3 py-1 text-[11px] font-bold text-[#8A5B1E]">
                Status
              </span>
            </div>

            <div class="mt-4 flex items-center justify-center rounded-[24px] border border-dashed border-[#D8D3C9] bg-white p-6">
              <div class="flex h-40 w-40 items-center justify-center rounded-2xl bg-[#F4EFE6] text-center text-sm font-bold text-[#8A5B1E]">
                QR CODE
              </div>
            </div>

            <div class="mt-4 rounded-2xl bg-[#F4EFE6] px-4 py-3">
              <p class="text-sm font-bold text-[#8A5B1E]">Total Pembayaran</p>
              <p class="mt-1 text-lg font-bold text-[#292522]">Rp {{ number_format($grandTotal, 0, ',', '.') }}</p>
            </div>
          </div>

          <button type="submit" class="w-full rounded-2xl bg-[#1B1B18] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#302F2A] hover:shadow-md">
            Konfirmasi Pembayaran
          </button>
        </form>
      </section>
    </div>
  </div>

  <script>
    const radioButtons = document.querySelectorAll('.payment-method-radio');
    const cashFields = document.getElementById('cashFields');
    const qrisCard = document.getElementById('qrisCard');
    const paidAmountInput = document.getElementById('paid_amount');
    const changeAmount = document.getElementById('changeAmount');
    const grandTotal = Number({{ $grandTotal }});

    function formatRupiah(value) {
      return `Rp ${Number(value).toLocaleString('id-ID')}`;
    }

    function updatePaymentUI() {
      const selected = document.querySelector('.payment-method-radio:checked');
      const selectedType = selected ? selected.dataset.type : 'cash';

      if (selectedType === 'cash') {
        cashFields.classList.remove('hidden');
        qrisCard.classList.add('hidden');
        paidAmountInput.required = true;
        paidAmountInput.value = paidAmountInput.value || grandTotal;
      } else if (selectedType === 'qris') {
        cashFields.classList.add('hidden');
        qrisCard.classList.remove('hidden');
        paidAmountInput.required = false;
        paidAmountInput.value = 0;
      } else {
        cashFields.classList.add('hidden');
        qrisCard.classList.add('hidden');
        paidAmountInput.required = false;
        paidAmountInput.value = 0;
      }

      if (selectedType === 'cash') {
        const paid = Number(paidAmountInput.value || 0);
        const change = Math.max(paid - grandTotal, 0);
        changeAmount.textContent = formatRupiah(change);
      } else {
        changeAmount.textContent = formatRupiah(0);
      }
    }

    radioButtons.forEach((radio) => {
      radio.addEventListener('change', updatePaymentUI);
    });

    paidAmountInput.addEventListener('input', () => {
      if (document.querySelector('.payment-method-radio:checked')?.dataset.type === 'cash') {
        const paid = Number(paidAmountInput.value || 0);
        const change = Math.max(paid - grandTotal, 0);
        changeAmount.textContent = formatRupiah(change);
      }
    });

    updatePaymentUI();
  </script>
@endsection
