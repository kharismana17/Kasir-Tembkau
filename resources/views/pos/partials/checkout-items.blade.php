@php
    $totalItems = $totalItems ?? count($cart);
@endphp

@if(count($cart))
    @foreach($cart as $item)
        @php
            $isTembakauItem = (bool) ($item['is_tembakau'] ?? false);

            $itemUnitLabel = $isTembakauItem
                ? 'gram'
                : ($item['unit'] ?? 'pcs');

            $itemLineTotal = $isTembakauItem
                ? ($item['price'] * ($item['qty'] / 100))
                : ($item['price'] * $item['qty']);
        @endphp

        <div
            class="rounded-xl border border-[#E7E1D9] bg-white p-4 shadow-sm hover:border-[#B47727] transition">

            <div class="flex items-start justify-between gap-4">

                <div class="min-w-0 flex-1">

                    <h4 class="truncate text-base font-bold text-[#292522]">
                        {{ $item['name'] }}
                    </h4>

                    <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-[#6B4F3A]">

                        <span>
                            Qty :
                            <strong>{{ $item['qty'] }}</strong>
                            {{ $itemUnitLabel }}
                        </span>

                        @if(!empty($item['purchase_type']))
                            <span>•</span>

                            <span class="rounded-full bg-[#F4EFE6] px-2 py-1 text-xs font-semibold text-[#8A5B1E]">
                                {{ ucfirst($item['purchase_type']) }}
                            </span>
                        @endif

                    </div>

                    <div class="mt-2 text-sm text-[#8A5B1E]">

                        Rp {{ number_format($item['price'],0,',','.') }}

                        /

                        {{ $itemUnitLabel }}

                    </div>

                </div>

                <div class="text-right">

                    <p class="text-xs uppercase tracking-wide text-[#6B4F3A]">
                        Subtotal
                    </p>

                    <p class="mt-1 text-lg font-bold text-[#8A5B1E]">

                        Rp {{ number_format($itemLineTotal,0,',','.') }}

                    </p>

                </div>

            </div>

        </div>
    @endforeach
@else

    <div class="rounded-xl border border-[#E7E1D9] bg-white p-6 text-center">

        <svg class="mx-auto h-10 w-10 text-[#B47727]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1.8"
                  d="M3 3h2l2.4 12.2a2 2 0 002 1.6h7.8a2 2 0 001.9-1.4L21 8H6"/>
        </svg>

        <p class="mt-3 text-sm text-[#6B4F3A]">

            Belum ada produk di keranjang.

        </p>

    </div>

@endif