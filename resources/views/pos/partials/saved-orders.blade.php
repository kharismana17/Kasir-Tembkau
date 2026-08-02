@if ($savedOrders->isNotEmpty())
  <div class="space-y-3">
    @foreach ($savedOrders as $savedOrder)
      <div class="rounded-[22px] border border-[#E7E1D9] bg-white p-4 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <p class="text-sm font-bold text-[#292522]">Order #{{ $savedOrder->id }}</p>
            <p class="mt-2 text-sm text-[#6B4F3A]">Items: {{ $savedOrder->total_items }} — Rp {{ number_format($savedOrder->total, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-[#8A5B1E]">Tersimpan {{ $savedOrder->created_at->diffForHumans() }}</p>
          </div>

          <div class="flex flex-wrap gap-2">
            <form data-saved-order-action="load" method="POST" action="{{ route('saved-orders.load', ['savedOrder' => $savedOrder->id]) }}">
              @csrf
              <button type="submit" class="inline-flex items-center justify-center rounded-2xl border border-[#292522] bg-[#F4EFE6] px-4 py-3 text-sm font-bold text-[#292522] transition hover:bg-gray-100">Load</button>
            </form>

            <form data-saved-order-action="delete" method="POST" action="{{ route('saved-orders.delete', ['savedOrder' => $savedOrder->id]) }}">
              @csrf
              @method('DELETE')
              <button type="submit" class="inline-flex items-center justify-center rounded-2xl border border-[#292522] bg-[#F4EFE6] px-4 py-3 text-sm font-bold text-[#292522] transition hover:bg-gray-100">Delete</button>
            </form>
          </div>
        </div>
      </div>
    @endforeach
  </div>
@else
  <div class="rounded-2xl bg-[#FAF9F6] p-6 text-center">
    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-[#F4EFE6] text-[#B47727]">
      <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16" /></svg>
    </div>
    <p class="mt-3 text-sm text-[#6B4F3A]">Belum ada order tersimpan.</p>
  </div>
@endif
