<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\ReportController;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class SalesReportSnapshotTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_report_uses_transaction_item_snapshots_for_deleted_products(): void
    {
        $transaction = Transaction::create([
            'invoice_no' => 'INV-SNAP-1',
            'user_id' => null,
            'subtotal' => 10000,
            'discount' => 0,
            'total' => 10000,
            'payment_method_id' => null,
            'paid_amount' => 10000,
            'change_amount' => 0,
            'status' => 'completed',
            'notes' => 'snapshot test',
        ]);

        TransactionItem::create([
            'transaction_id' => $transaction->id,
            'product_id' => null,
            'qty' => 2,
            'price' => 5000,
            'discount' => 0,
            'subtotal' => 10000,
            'buy_price' => 3000,
            'sell_price' => 5000,
            'product_name' => 'Produk telah dihapus',
            'product_unit' => 'pak',
            'product_category' => 'Lama',
            'product_barcode' => 'REMOVED-1',
        ]);

        $view = app(ReportController::class)->sales(new Request([
            'from' => now()->startOfMonth()->format('Y-m-d'),
            'to' => now()->format('Y-m-d'),
        ]));

        $this->assertSame('admin.reports.sales', $view->getName());

        $data = $view->getData();

        $this->assertSame(10000.0, round((float) $data['totalSales'], 2));
        $this->assertSame(6000.0, round((float) $data['totalCapital'], 2));
        $this->assertSame(4000.0, round((float) $data['totalProfit'], 2));
        $this->assertNotEmpty($data['bestSellingProducts']);
        $this->assertSame('Produk telah dihapus', $data['bestSellingProducts'][0]['product_name']);
        $this->assertSame('pak', $data['bestSellingProducts'][0]['product_unit']);
    }
}
