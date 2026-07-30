@php
  $gramTotal = collect($cart)->where('is_tembakau', true)->sum('qty');
  $itemTotal = count($cart);
@endphp

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

  <div class="rounded-[22px] border border-[#E7E1D9] bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-[#B47727] hover:bg-[#FCF8F2]" role="button" tabindex="0" data-item-edit-trigger data-item-product-id="{{ $item['product_id'] }}" data-item-name="{{ e($item['name']) }}" data-item-price="{{ number_format($item['price'], 0, ',', '.') }}" data-item-qty="{{ $item['qty'] }}" data-item-unit="{{ $itemUnitLabel }}" data-item-step="{{ $itemStepValue }}" data-item-min="{{ $itemMinValue }}" data-item-is-tembakau="{{ $isTembakauItem ? '1' : '0' }}" data-item-update-url="{{ route('pos.cart.update', ['product' => $item['product_id']]) }}" data-item-sale-type="{{ $item['sale_type'] ?? '' }}" data-item-purchase-type="{{ $item['purchase_type'] ?? '' }}" data-item-input-method="{{ $item['input_method'] ?? '' }}" data-item-price-raw="{{ $item['price'] }}" data-item-wholesale-price="{{ $item['wholesale_price'] ?? 0 }}" data-item-wholesale-min-qty="{{ $item['wholesale_min_qty'] ?? 0 }}">
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
