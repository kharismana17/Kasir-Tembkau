<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Struk {{ $transaction->invoice_no }}</title>

  <style>
    :root {
      --ink: #292522;
      --muted: #8a8179;
      --soft-muted: #a3978d;
      --line: #e7e1d9;
      --cream: #f7f5f0;
      --white: #ffffff;
      --tobacco: #c68b59;
      --tobacco-dark: #6b4f3a;
      --success: #3f6b4a;
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      min-height: 100vh;
      padding: 24px 12px;
      font-family: Inter, ui-sans-serif, system-ui, -apple-system,
        BlinkMacSystemFont, "Segoe UI", sans-serif;
      background: var(--cream);
      color: var(--ink);
    }

    .receipt-wrapper {
      width: min(100%, 440px);
      margin: 0 auto;
    }

    .receipt {
      overflow: hidden;
      border: 1px solid var(--line);
      border-radius: 28px;
      background: var(--white);
      box-shadow: 0 24px 70px rgba(41, 37, 34, 0.10);
    }

    /* =====================================================
       HEADER
    ====================================================== */

    .receipt-header {
      padding: 32px 28px 26px;
      background: var(--ink);
      color: var(--white);
    }

    .brand-label {
      margin: 0 0 12px;
      font-size: 10px;
      font-weight: 700;
      letter-spacing: 0.25em;
      text-transform: uppercase;
      color: #c9c0b8;
    }

    .store-name {
      margin: 0;
      font-size: 22px;
      font-weight: 700;
      letter-spacing: -0.02em;
    }

    .store-info {
      margin-top: 14px;
      display: grid;
      gap: 4px;
      font-size: 12px;
      line-height: 1.6;
      color: #b8aea5;
    }

    /* =====================================================
       CONTENT
    ====================================================== */

    .receipt-content {
      padding: 28px;
    }

    .section-label {
      margin: 0 0 14px;
      font-size: 10px;
      font-weight: 700;
      letter-spacing: 0.2em;
      text-transform: uppercase;
      color: var(--soft-muted);
    }

    .meta {
      display: grid;
      gap: 12px;
    }

    .meta-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 20px;
      font-size: 13px;
    }

    .meta-row span:first-child {
      color: var(--muted);
    }

    .meta-row span:last-child {
      font-weight: 600;
      text-align: right;
      color: var(--ink);
    }

    .divider {
      height: 1px;
      margin: 24px 0;
      border: 0;
      background: var(--line);
    }

    /* =====================================================
       ITEMS
    ====================================================== */

    .items {
      display: grid;
      gap: 18px;
    }

    .item {
      display: grid;
      gap: 6px;
    }

    .item-main {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 16px;
    }

    .item-name {
      min-width: 0;
      font-size: 13px;
      font-weight: 600;
      line-height: 1.5;
      color: var(--ink);
    }

    .item-subtotal {
      flex-shrink: 0;
      font-size: 13px;
      font-weight: 700;
      color: var(--tobacco-dark);
    }

    .item-detail {
      font-size: 11px;
      color: var(--muted);
    }

    /* =====================================================
       SUMMARY
    ====================================================== */

    .summary {
      display: grid;
      gap: 12px;
    }

    .summary-row {
      display: flex;
      justify-content: space-between;
      gap: 20px;
      font-size: 13px;
    }

    .summary-row span:first-child {
      color: var(--muted);
    }

    .summary-row span:last-child {
      font-weight: 600;
      text-align: right;
    }

    .summary-total {
      margin-top: 8px;
      padding: 18px 0;
      border-top: 1px solid var(--line);
      border-bottom: 1px solid var(--line);
    }

    .summary-total .summary-row {
      font-size: 17px;
    }

    .summary-total span:first-child {
      font-weight: 700;
      color: var(--ink);
    }

    .summary-total span:last-child {
      font-size: 18px;
      font-weight: 800;
      color: var(--tobacco-dark);
    }

    /* =====================================================
       FOOTER
    ====================================================== */

    .receipt-footer {
      padding: 0 28px 28px;
    }

    .thank-you {
      margin: 0;
      text-align: center;
      font-size: 12px;
      line-height: 1.6;
      color: var(--muted);
    }

    .print-button {
      display: flex;
      width: 100%;
      align-items: center;
      justify-content: center;
      margin-top: 20px;
      padding: 14px 18px;
      border: 0;
      border-radius: 14px;
      background: var(--tobacco);
      color: var(--white);
      font-size: 13px;
      font-weight: 700;
      cursor: pointer;
      transition: 0.2s ease;
    }

    .print-button:hover {
      background: var(--tobacco-dark);
    }

    /* =====================================================
       RESPONSIVE
    ====================================================== */

    @media (max-width: 480px) {
      body {
        padding: 12px;
      }

      .receipt-header {
        padding: 26px 22px 22px;
      }

      .receipt-content {
        padding: 22px;
      }

      .receipt-footer {
        padding: 0 22px 22px;
      }

      .store-name {
        font-size: 20px;
      }
    }

    /* =====================================================
       PRINT
    ====================================================== */

    @media print {
      @page {
        margin: 0;
      }

      body {
        padding: 0;
        background: #ffffff;
      }

      .receipt-wrapper {
        width: 100%;
      }

      .receipt {
        border: 0;
        border-radius: 0;
        box-shadow: none;
      }

      .print-button {
        display: none;
      }

      .receipt-header {
        background: #ffffff;
        color: var(--ink);
        padding: 0 0 20px;
        border-bottom: 1px solid var(--line);
      }

      .brand-label {
        color: var(--muted);
      }

      .store-info {
        color: var(--muted);
      }

      .receipt-content {
        padding: 20px 0;
      }

      .receipt-footer {
        padding: 0;
      }
    }
  </style>
</head>

<body>

  <div class="receipt-wrapper">

    <div class="receipt">

      {{-- =====================================================
          HEADER
      ====================================================== --}}
      <header class="receipt-header">

        <p class="brand-label">
          Retail Management
        </p>

        <h1 class="store-name">
          {{ $storeSetting?->store_name ?? 'Kasir Tembakau' }}
        </h1>

        @if ($storeSetting?->address || $storeSetting?->phone)
          <div class="store-info">

            @if ($storeSetting?->address)
              <span>
                {{ $storeSetting->address }}
              </span>
            @endif

            @if ($storeSetting?->phone)
              <span>
                Telp: {{ $storeSetting->phone }}
              </span>
            @endif

          </div>
        @endif

      </header>


      {{-- =====================================================
          CONTENT
      ====================================================== --}}
      <main class="receipt-content">

        {{-- TRANSACTION INFO --}}
        <section>

          <p class="section-label">
            Transaction
          </p>

          <div class="meta">

            <div class="meta-row">
              <span>Invoice</span>
              <span>{{ $transaction->invoice_no }}</span>
            </div>

            <div class="meta-row">
              <span>Tanggal</span>
              <span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
            </div>

            <div class="meta-row">
              <span>Kasir</span>
              <span>{{ $transaction->user?->name ?? '-' }}</span>
            </div>

          </div>

        </section>


        <hr class="divider">


        {{-- ITEMS --}}
        <section>

          <p class="section-label">
            Detail Pesanan
          </p>

          <div class="items">

            @foreach ($transaction->items as $item)

              <div class="item">

                <div class="item-main">

                  <div class="item-name">
                    {{ \Illuminate\Support\Str::limit($item->product?->name ?? 'Produk tidak ditemukan', 32) }}
                  </div>

                  <div class="item-subtotal">
                    <span class="inline-flex items-center justify-end whitespace-nowrap text-right shrink-0 min-w-fit">
                      <span class="mr-1">Rp</span>
                      <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </span>
                  </div>

                </div>

                <div class="item-detail">
                  {{ $item->qty }}{{ $item->product?->unit ? ' '.$item->product->unit : '' }}
                  ×
                  <span class="inline-flex items-center justify-end whitespace-nowrap text-right shrink-0 min-w-fit">
                    <span class="mr-1">Rp</span>
                    <span>{{ number_format($item->price, 0, ',', '.') }}</span>
                  </span>
                </div>

              </div>

            @endforeach

          </div>

        </section>


        <hr class="divider">


        {{-- SUMMARY --}}
        <section>

          <p class="section-label">
            Ringkasan Pembayaran
          </p>

          <div class="summary">

            <div class="summary-row">
              <span>Subtotal</span>
              <span>
                <span class="inline-flex items-center justify-end whitespace-nowrap text-right shrink-0 min-w-fit">
                  <span class="mr-1">Rp</span>
                  <span>{{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                </span>
              </span>
            </div>

            <div class="summary-row">
              <span>Diskon</span>
              <span>
                <span class="inline-flex items-center justify-end whitespace-nowrap text-right shrink-0 min-w-fit">
                  <span class="mr-1">Rp</span>
                  <span>{{ number_format($transaction->discount, 0, ',', '.') }}</span>
                </span>
              </span>
            </div>

            <div class="summary-total">

              <div class="summary-row">
                <span>Total</span>
                <span>
                  <span class="inline-flex items-center justify-end whitespace-nowrap text-right shrink-0 min-w-fit">
                    <span class="mr-1">Rp</span>
                    <span>{{ number_format($transaction->total, 0, ',', '.') }}</span>
                  </span>
                </span>
              </div>

            </div>

            <div class="summary-row">
              <span>Metode</span>
              <span>
                {{ $transaction->paymentMethod?->name ?? '-' }}
              </span>
            </div>

            <div class="summary-row">
              <span>Dibayar</span>
              <span>
                <span class="inline-flex items-center justify-end whitespace-nowrap text-right shrink-0 min-w-fit">
                  <span class="mr-1">Rp</span>
                  <span>{{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
                </span>
              </span>
            </div>

            <div class="summary-row">
              <span>Kembali</span>
              <span>
                <span class="inline-flex items-center justify-end whitespace-nowrap text-right shrink-0 min-w-fit">
                  <span class="mr-1">Rp</span>
                  <span>{{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
                </span>
              </span>
            </div>

          </div>

        </section>

      </main>


      {{-- =====================================================
          FOOTER
      ====================================================== --}}
      <footer class="receipt-footer">

        <p class="thank-you">
          Terima kasih telah berbelanja.
          <br>
          Simpan struk ini sebagai bukti transaksi.
        </p>

        <button
          type="button"
          class="print-button"
          onclick="window.print()"
        >
          Cetak Struk
        </button>

      </footer>

    </div>

  </div>

</body>

</html>