
@extends('layouts.cashier')

@section('title', 'Saved Orders')

@section('content')
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Saved Orders</h1>
    <div class="space-y-3">
      @forelse($orders as $order)
        <div class="rounded p-4 border bg-white flex items-center justify-between">
          <div>
            <div class="text-sm font-semibold">Order #{{ $order->id }}</div>
            <div class="text-xs text-gray-500">Items: {{ $order->total_items }} — Rp {{ number_format($order->total,0,',','.') }}</div>
            <div class="text-xs text-gray-400">Saved: {{ $order->created_at->diffForHumans() }}</div>
          </div>
          <div class="flex gap-2">
            <form method="POST" action="{{ route('saved-orders.load', ['savedOrder' => $order->id]) }}">@csrf<button class="rounded px-3 py-2 bg-green-600 text-white">Load</button></form>
            <form method="POST" action="{{ route('saved-orders.delete', ['savedOrder' => $order->id]) }}">@csrf @method('DELETE')<button class="rounded px-3 py-2 bg-red-600 text-white">Delete</button></form>
          </div>
        </div>
      @empty
        <div class="text-sm text-gray-500">Belum ada saved order.</div>
      @endforelse
    </div>
  </div>

@endsection
