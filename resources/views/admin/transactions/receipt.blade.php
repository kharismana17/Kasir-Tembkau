<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk {{ $transaction->invoice_no }}</title>
    <style>
      body {
        margin: 0;
        padding: 0;
        font-family: Menlo, Monaco, Consolas, 'Courier New', monospace;
        background: #f8fafc;
        color: #111827;
      }

      .receipt {
        width: min(380px, calc(100% - 24px));
        margin: 24px auto;
        padding: 24px;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 24px 64px rgba(15, 23, 42, 0.08);
      }

      .receipt__header,
      .receipt__section,
      .receipt__footer {
        margin-bottom: 20px;
      }

      .receipt__title {
        margin: 0 0 6px;
        font-size: 1.05rem;
        font-weight: 700;
        letter-spacing: 0.04em;
      }

      .receipt__text {
        margin: 0;
        font-size: 0.95rem;
        line-height: 1.6;
      }

      .receipt__meta {
        margin-top: 16px;
        display: grid;
        gap: 8px;
      }

      .receipt__meta-item {
        display: flex;
        justify-content: space-between;
        font-size: 0.95rem;
        line-height: 1.4;
      }

      .receipt__divider {
        height: 1px;
        background: #e2e8f0;
        border: none;
        margin: 18px 0;
      }

      .receipt__table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.95rem;
      }

      .receipt__table th,
      .receipt__table td {
        padding: 6px 0;
        text-align: left;
      }

      .text-right {
        text-align: right;
      }

      .receipt__table th {
        font-size: 0.8rem;
        color: #475569;
        letter-spacing: 0.05em;
        text-transform: uppercase;
      }

      .receipt__line {
        display: flex;
        justify-content: space-between;
        margin-top: 10px;
        font-size: 0.95rem;
      }

      .receipt__total {
        font-weight: 700;
        font-size: 1rem;
        margin-top: 16px;
      }

      .receipt__print {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 14px 18px;
        border: none;
        border-radius: 12px;
        background: #16a34a;
        color: #ffffff;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        margin-top: 12px;
      }

      @media print {
        body {
          background: #ffffff;
        }

        .receipt {
          box-shadow: none;
          margin: 0;
          border-radius: 0;
          width: auto;
          padding: 0;
        }

        .receipt__print {
          display: none;
        }
      }
    </style>
  </head>
  <body>
    <div class="receipt">
      <div class="receipt__header">
        <p class="receipt__title">{{ $storeSetting?->store_name ?? 'Kasir Tembakau' }}</p>
        @if ($storeSetting?->address)
          <p class="receipt__text">{{ $storeSetting->address }}</p>
        @endif
        @if ($storeSetting?->phone)
          <p class="receipt__text">Telp: {{ $storeSetting->phone }}</p>
        @endif
      </div>

      <div class="receipt__section">
        <div class="receipt__meta">
          <div class="receipt__meta-item">
            <span>Invoice</span>
            <span>{{ $transaction->invoice_no }}</span>
          </div>
          <div class="receipt__meta-item">
            <span>Tanggal</span>
            <span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
          </div>
          <div class="receipt__meta-item">
            <span>Kasir</span>
            <span>{{ $transaction->user?->name ?? '-' }}</span>
          </div>
        </div>
      </div>

      <hr class="receipt__divider" />

      <table class="receipt__table">
        <thead>
          <tr>
            <th>Produk</th>
            <th class="text-right">Qty</th>
            <th class="text-right">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($transaction->items as $item)
            <tr>
              <td>{{ \Illuminate\Support\Str::limit($item->product?->name ?? 'Produk tidak ditemukan', 24) }}</td>
              <td class="text-right">
                {{ $item->qty }}{{ $item->product?->unit ? ' '.$item->product->unit : '' }}
              </td>
              <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            <tr>
              <td colspan="3" class="receipt__text">@ Rp {{ number_format($item->price, 0, ',', '.') }} x {{ $item->qty }}{{ $item->product?->unit ? ' '.$item->product->unit : '' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <hr class="receipt__divider" />

      <div class="receipt__line">
        <span>Subtotal</span>
        <span>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
      </div>
      <div class="receipt__line">
        <span>Diskon</span>
        <span>Rp {{ number_format($transaction->discount, 0, ',', '.') }}</span>
      </div>
      <div class="receipt__line receipt__total">
        <span>Total</span>
        <span>Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
      </div>
      <div class="receipt__line">
        <span>Metode</span>
        <span>{{ $transaction->paymentMethod?->name ?? '-' }}</span>
      </div>
      <div class="receipt__line">
        <span>Dibayar</span>
        <span>Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
      </div>
      <div class="receipt__line">
        <span>Kembali</span>
        <span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
      </div>

      <button type="button" class="receipt__print" onclick="window.print()">Cetak Struk</button>
    </div>
  </body>
</html>
