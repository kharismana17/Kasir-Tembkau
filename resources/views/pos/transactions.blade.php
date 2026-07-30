@extends('layouts.cashier')

@section('title', 'Transaksi Saya')

@section('content')
  <div class="space-y-6">

    <section class="rounded-[28px] border border-[#E7E1D9] bg-[#FBF9F6] p-5 shadow-sm sm:p-6">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-[#B47727]">
            Transaction History
          </p>
          <h1 class="mt-2 text-2xl font-bold tracking-tight text-[#292522] sm:text-3xl">
            Transaksi Saya
          </h1>
          <p class="mt-2 text-sm text-[#6B4F3A]">
            Daftar transaksi penjualan yang dibuat oleh Anda.
          </p>
        </div>

        <div class="grid w-full gap-3 grid-cols-1 sm:grid-cols-2">
          <div class="rounded-2xl border border-[#E7E1D9] bg-white px-4 py-3 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">
              Rekam Jual
            </p>
            <p id="summaryTotalTransactions" class="mt-2 text-lg font-bold text-[#292522]">
              {{ $summary['total_transactions'] }}
            </p>
          </div>

          <div class="rounded-2xl border border-[#E7E1D9] bg-white px-4 py-3 shadow-sm">
            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">
              Status
            </p>
            <p class="mt-2 text-lg font-bold text-[#8A5B1E]">
              Aktif
            </p>
          </div>
        </div>
      </div>
    </section>

    <section class="rounded-[28px] border border-[#E7E1D9] bg-[#FBF9F6] p-5 shadow-sm sm:p-6">
      <div class="grid gap-4 lg:grid-cols-[1.6fr_1fr]">
        <div>
          <label for="transaction_search" class="mb-2 block text-sm font-semibold text-[#292522]">
            Cari Invoice atau Kasir
          </label>
          <input
            id="transaction_search"
            type="search"
            placeholder="Cari nomor invoice atau nama kasir..."
            class="w-full rounded-2xl border border-[#D8D3C9] bg-white px-4 py-3 text-sm text-[#292522] outline-none transition duration-200 hover:border-[#B8B1A4] focus:border-[#292522] focus:ring-4 focus:ring-[#292522]/10"
          >
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
          <div class="grid gap-3 sm:grid-cols-2">
            <div>
              <label for="transaction_from_date" class="mb-2 block text-sm font-semibold text-[#292522]">
                Dari
              </label>
              <input
                id="transaction_from_date"
                type="date"
                class="w-full rounded-2xl border border-[#D8D3C9] bg-white px-4 py-3 text-sm text-[#292522] outline-none transition duration-200 hover:border-[#B8B1A4] focus:border-[#292522] focus:ring-4 focus:ring-[#292522]/10"
              >
            </div>
            <div>
              <label for="transaction_to_date" class="mb-2 block text-sm font-semibold text-[#292522]">
                Sampai
              </label>
              <input
                id="transaction_to_date"
                type="date"
                class="w-full rounded-2xl border border-[#D8D3C9] bg-white px-4 py-3 text-sm text-[#292522] outline-none transition duration-200 hover:border-[#B8B1A4] focus:border-[#292522] focus:ring-4 focus:ring-[#292522]/10"
              >
            </div>
          </div>
          <div class="flex flex-nowrap gap-2 overflow-x-auto pb-1 lg:justify-end">
            <button type="button" data-payment-type="all" class="payment-filter-button whitespace-nowrap rounded-2xl bg-[#292522] px-4 py-2 text-xs font-bold text-white shadow-sm transition duration-200 hover:bg-[#3A352F]">
              Semua
            </button>
            <button type="button" data-payment-type="cash" class="payment-filter-button whitespace-nowrap rounded-2xl bg-[#F4EFE6] px-4 py-2 text-xs font-bold text-[#8A5B1E] shadow-sm transition duration-200 hover:bg-[#E8DFD0]">
              Cash
            </button>
            <button type="button" data-payment-type="non_cash" class="payment-filter-button whitespace-nowrap rounded-2xl bg-[#F4EFE6] px-4 py-2 text-xs font-bold text-[#8A5B1E] shadow-sm transition duration-200 hover:bg-[#E8DFD0]">
              Non Cash
            </button>
          </div>
        </div>
      </div>

      <div class="mt-4 flex flex-nowrap gap-2 overflow-x-auto pb-1 lg:flex-wrap lg:justify-end">
        <button type="button" data-filter="all" class="filter-button whitespace-nowrap rounded-2xl bg-[#292522] px-4 py-2 text-xs font-bold text-white shadow-sm transition duration-200 hover:bg-[#3A352F]">
          Semua
        </button>
        <button type="button" data-filter="today" class="filter-button whitespace-nowrap rounded-2xl bg-[#F4EFE6] px-4 py-2 text-xs font-bold text-[#8A5B1E] shadow-sm transition duration-200 hover:bg-[#E8DFD0]">
          Hari ini
        </button>
        <button type="button" data-filter="week" class="filter-button whitespace-nowrap rounded-2xl bg-[#F4EFE6] px-4 py-2 text-xs font-bold text-[#8A5B1E] shadow-sm transition duration-200 hover:bg-[#E8DFD0]">
          Minggu ini
        </button>
        <button type="button" data-filter="month" class="filter-button whitespace-nowrap rounded-2xl bg-[#F4EFE6] px-4 py-2 text-xs font-bold text-[#8A5B1E] shadow-sm transition duration-200 hover:bg-[#E8DFD0]">
          Bulan ini
        </button>
      </div>
    </section>

    <section class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
      <div class="rounded-[28px] border border-[#E7E1D9] bg-white p-5 shadow-sm">
        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Total Transaksi</p>
        <p id="summaryTotalTransactionsCard" class="mt-3 text-2xl font-bold text-[#292522]">{{ number_format($summary['total_transactions'], 0, ',', '.') }}</p>
      </div>
      <div class="rounded-[28px] border border-[#E7E1D9] bg-white p-5 shadow-sm">
        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Total Penjualan</p>
        <p id="summaryTotalSales" class="mt-3 text-2xl font-bold text-[#8A5B1E]">Rp {{ number_format($summary['total_sales'], 0, ',', '.') }}</p>
      </div>
      <div class="rounded-[28px] border border-[#E7E1D9] bg-white p-5 shadow-sm">
        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Total Item Terjual</p>
        <p id="summaryTotalItemsSold" class="mt-3 text-2xl font-bold text-[#292522]">{{ number_format($summary['total_items_sold'], 0, ',', '.') }}</p>
      </div>
      <div class="rounded-[28px] border border-[#E7E1D9] bg-white p-5 shadow-sm">
        <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Rata-rata Nilai Transaksi</p>
        <p id="summaryAverageTransaction" class="mt-3 text-2xl font-bold text-[#292522]">Rp {{ number_format($summary['average_transaction_value'], 0, ',', '.') }}</p>
      </div>
    </section>

    <section class="rounded-[28px] border border-[#E7E1D9] bg-white p-5 shadow-sm">
      <div class="flex items-center justify-between gap-3">
        <div>
          <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Ringkasan Pembayaran</p>
          <p class="mt-1 text-sm text-[#6B4F3A]">Total dan jumlah transaksi berdasarkan metode pembayaran</p>
        </div>
      </div>

      <div class="mt-4 grid gap-4 grid-cols-1 sm:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-[28px] border border-[#E7E1D9] bg-[#FBF9F6] p-5 shadow-sm">
          <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Total Cash</p>
          <p id="summaryTotalCash" class="mt-3 text-2xl font-bold text-[#292522]">Rp {{ number_format($summary['total_cash_sales'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-[28px] border border-[#E7E1D9] bg-[#FBF9F6] p-5 shadow-sm">
          <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Jumlah transaksi Cash</p>
          <p id="cashTransactionCount" class="mt-3 text-2xl font-bold text-[#292522]">{{ number_format($summary['cash_transaction_count'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-[28px] border border-[#E7E1D9] bg-[#FBF9F6] p-5 shadow-sm">
          <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Total Non Cash</p>
          <p id="summaryTotalNonCash" class="mt-3 text-2xl font-bold text-[#292522]">Rp {{ number_format($summary['total_non_cash_sales'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-[28px] border border-[#E7E1D9] bg-[#FBF9F6] p-5 shadow-sm">
          <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Jumlah transaksi Non Cash</p>
          <p id="nonCashTransactionCount" class="mt-3 text-2xl font-bold text-[#292522]">{{ number_format($summary['non_cash_transaction_count'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-[28px] border border-[#E7E1D9] bg-[#FBF9F6] p-5 shadow-sm">
          <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Grand Total</p>
          <p id="summaryGrandTotal" class="mt-3 text-2xl font-bold text-[#8A5B1E]">Rp {{ number_format($summary['grand_total'] ?? 0, 0, ',', '.') }}</p>
        </div>
      </div>
    </section>

    <section class="rounded-[28px] border border-[#E7E1D9] bg-[#FBF9F6] p-5 shadow-sm sm:p-6">
      <div class="flex items-center justify-between gap-3">
        <div>
          <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-[#6B4F3A]">Ringkasan Kategori</p>
          <p class="mt-1 text-sm text-[#6B4F3A]">Performa penjualan berdasarkan kategori produk</p>
        </div>
      </div>

      <div id="categorySummaryList" class="mt-4 space-y-3">
        @forelse($categories as $category)
          <div class="rounded-2xl border border-[#E7E1D9] bg-white px-4 py-4 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
              <div>
                <p class="text-sm font-bold text-[#292522]">{{ $category['category_name'] }}</p>
                <p class="mt-1 text-sm text-[#6B4F3A]">{{ number_format($category['total_items_sold'], 0, ',', '.') }} item terjual</p>
              </div>
              <p class="text-sm font-bold text-[#8A5B1E]">Rp {{ number_format($category['total_sales'], 0, ',', '.') }}</p>
            </div>
          </div>
        @empty
          <div class="rounded-2xl border border-dashed border-[#D8D3C9] bg-white px-4 py-6 text-center text-sm text-[#6B4F3A]">
            Belum ada data kategori untuk rentang filter ini.
          </div>
        @endforelse
      </div>
    </section>

    <div id="transactionsList" class="space-y-4">
      @include('pos.partials.transaction-list', ['transactions' => $transactions])
    </div>

  </div>

  <script>
    const filterButtons = Array.from(document.querySelectorAll('.filter-button'));
    const paymentFilterButtons = Array.from(document.querySelectorAll('.payment-filter-button'));
    const searchInput = document.getElementById('transaction_search');
    const startDateInput = document.getElementById('transaction_from_date');
    const endDateInput = document.getElementById('transaction_to_date');
    const transactionsList = document.getElementById('transactionsList');
    const categorySummaryList = document.getElementById('categorySummaryList');
    const summaryTotalTransactions = document.getElementById('summaryTotalTransactions');
    const summaryTotalTransactionsCard = document.getElementById('summaryTotalTransactionsCard');
    const summaryTotalSales = document.getElementById('summaryTotalSales');
    const summaryTotalItemsSold = document.getElementById('summaryTotalItemsSold');
    const summaryAverageTransaction = document.getElementById('summaryAverageTransaction');
    const summaryTotalCash = document.getElementById('summaryTotalCash');
    const cashTransactionCount = document.getElementById('cashTransactionCount');
    const summaryTotalNonCash = document.getElementById('summaryTotalNonCash');
    const nonCashTransactionCount = document.getElementById('nonCashTransactionCount');
    const summaryGrandTotal = document.getElementById('summaryGrandTotal');

    let activeFilter = 'all';
    let activePaymentFilter = 'all';
    let fromDate = '';
    let toDate = '';
    let searchValue = '';
    let refreshTimer = null;

    function formatCurrency(value) {
      return new Intl.NumberFormat('id-ID', {
        maximumFractionDigits: 0,
      }).format(value);
    }

    function setActiveFilter(filter) {
      activeFilter = filter;
      filterButtons.forEach((button) => {
        const isActive = button.dataset.filter === filter;
        button.classList.toggle('bg-[#292522]', isActive);
        button.classList.toggle('text-white', isActive);
        button.classList.toggle('bg-[#F4EFE6]', !isActive);
        button.classList.toggle('text-[#8A5B1E]', !isActive);
      });
    }

    function setActivePaymentFilter(filter) {
      activePaymentFilter = filter;
      paymentFilterButtons.forEach((button) => {
        const isActive = button.dataset.paymentType === filter;
        button.classList.toggle('bg-[#292522]', isActive);
        button.classList.toggle('text-white', isActive);
        button.classList.toggle('bg-[#F4EFE6]', !isActive);
        button.classList.toggle('text-[#8A5B1E]', !isActive);
      });
    }

    function updateSummary(summary) {
      if (summaryTotalTransactions) {
        summaryTotalTransactions.textContent = summary.total_transactions;
      }
      if (summaryTotalTransactionsCard) {
        summaryTotalTransactionsCard.textContent = new Intl.NumberFormat('id-ID').format(summary.total_transactions);
      }
      if (summaryTotalSales) {
        summaryTotalSales.textContent = 'Rp ' + formatCurrency(summary.total_sales);
      }
      if (summaryTotalItemsSold) {
        summaryTotalItemsSold.textContent = new Intl.NumberFormat('id-ID').format(summary.total_items_sold);
      }
      if (summaryAverageTransaction) {
        summaryAverageTransaction.textContent = 'Rp ' + formatCurrency(summary.average_transaction_value);
      }
      if (summaryTotalCash) {
        summaryTotalCash.textContent = 'Rp ' + formatCurrency(summary.total_cash_sales);
      }
      if (cashTransactionCount) {
        cashTransactionCount.textContent = new Intl.NumberFormat('id-ID').format(summary.cash_transaction_count);
      }
      if (summaryTotalNonCash) {
        summaryTotalNonCash.textContent = 'Rp ' + formatCurrency(summary.total_non_cash_sales);
      }
      if (nonCashTransactionCount) {
        nonCashTransactionCount.textContent = new Intl.NumberFormat('id-ID').format(summary.non_cash_transaction_count);
      }
      if (summaryGrandTotal) {
        summaryGrandTotal.textContent = 'Rp ' + formatCurrency(summary.grand_total);
      }
    }

    function updateCategories(categories) {
      if (!categorySummaryList) {
        return;
      }

      if (!categories.length) {
        categorySummaryList.innerHTML = '<div class="rounded-2xl border border-dashed border-[#D8D3C9] bg-white px-4 py-6 text-center text-sm text-[#6B4F3A]">Belum ada data kategori untuk rentang filter ini.</div>';
        return;
      }

      categorySummaryList.innerHTML = categories.map((category) => `
        <div class="rounded-2xl border border-[#E7E1D9] bg-white px-4 py-4 shadow-sm">
          <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <p class="text-sm font-bold text-[#292522]">${category.category_name}</p>
              <p class="mt-1 text-sm text-[#6B4F3A]">${new Intl.NumberFormat('id-ID').format(category.total_items_sold)} item terjual</p>
            </div>
            <p class="text-sm font-bold text-[#8A5B1E]">Rp ${formatCurrency(category.total_sales)}</p>
          </div>
        </div>
      `).join('');
    }

    async function loadTransactions(preserveScroll = true) {
      const scrollY = preserveScroll ? window.scrollY : 0;
      const params = new URLSearchParams({
        filter: activeFilter,
        payment_type: activePaymentFilter,
      });

      if (searchValue) {
        params.set('search', searchValue);
      }

      if (fromDate) {
        params.set('from_date', fromDate);
      }

      if (toDate) {
        params.set('to_date', toDate);
      }

      const response = await fetch(`/pos/transactions/data?${params.toString()}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      });

      if (!response.ok) {
        return;
      }

      const data = await response.json();
      updateSummary(data.summary);
      updateCategories(data.categories);

      if (transactionsList) {
        transactionsList.innerHTML = data.transactions_html;
      }

      if (preserveScroll) {
        window.scrollTo(0, scrollY);
      }
    }

    filterButtons.forEach((button) => {
      button.addEventListener('click', () => {
        setActiveFilter(button.dataset.filter);
        loadTransactions();
      });
    });

    paymentFilterButtons.forEach((button) => {
      button.addEventListener('click', () => {
        setActivePaymentFilter(button.dataset.paymentType);
        loadTransactions();
      });
    });

    if (searchInput) {
      searchInput.addEventListener('input', (event) => {
        searchValue = event.target.value.trim();
        window.clearTimeout(refreshTimer);
        refreshTimer = window.setTimeout(() => loadTransactions(), 250);
      });
    }

    if (startDateInput) {
      startDateInput.addEventListener('change', (event) => {
        fromDate = event.target.value;
        loadTransactions();
      });
    }

    if (endDateInput) {
      endDateInput.addEventListener('change', (event) => {
        toDate = event.target.value;
        loadTransactions();
      });
    }

    setActiveFilter(activeFilter);
    setActivePaymentFilter(activePaymentFilter);

    window.setInterval(() => {
      if (document.hidden) {
        return;
      }
      loadTransactions(true);
    }, 10000);
  </script>
@endsection