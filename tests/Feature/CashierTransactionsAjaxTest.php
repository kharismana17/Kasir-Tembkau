<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashierTransactionsAjaxTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_transactions_data_endpoint_returns_filtered_summary_and_categories(): void
    {
        $kasirRole = Role::create([
            'name' => 'Kasir',
            'slug' => 'kasir',
            'description' => 'Kasir',
        ]);

        $kasir = User::create([
            'name' => 'Kasir User',
            'email' => 'kasir@example.com',
            'password' => bcrypt('password'),
            'role_id' => $kasirRole->id,
            'is_active' => true,
        ]);

        $paymentMethod = PaymentMethod::create([
            'name' => 'Cash',
            'description' => 'Pembayaran tunai',
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Rokok',
            'slug' => 'rokok',
            'description' => 'Kategori rokok',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'barcode' => 'P001',
            'sku' => 'SKU-001',
            'name' => 'Djarum',
            'buy_price' => 10000,
            'sell_price' => 15000,
            'stock' => 100,
            'stock_min' => 10,
            'unit' => 'pcs',
            'stock_unit' => 'pcs',
            'selling_unit' => 'pcs',
            'is_active' => true,
        ]);

        $completedTransaction = Transaction::create([
            'invoice_no' => 'INV-001',
            'user_id' => $kasir->id,
            'subtotal' => 15000,
            'discount' => 0,
            'total' => 15000,
            'payment_method_id' => $paymentMethod->id,
            'paid_amount' => 15000,
            'change_amount' => 0,
            'status' => 'completed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        TransactionItem::create([
            'transaction_id' => $completedTransaction->id,
            'product_id' => $product->id,
            'qty' => 1,
            'price' => 15000,
            'discount' => 0,
            'subtotal' => 15000,
        ]);

        $voidedTransaction = Transaction::create([
            'invoice_no' => 'INV-002',
            'user_id' => $kasir->id,
            'subtotal' => 30000,
            'discount' => 0,
            'total' => 30000,
            'payment_method_id' => $paymentMethod->id,
            'paid_amount' => 30000,
            'change_amount' => 0,
            'status' => 'voided',
        ]);
        $voidedTransaction->forceFill([
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ])->save();

        TransactionItem::create([
            'transaction_id' => $voidedTransaction->id,
            'product_id' => $product->id,
            'qty' => 2,
            'price' => 15000,
            'discount' => 0,
            'subtotal' => 30000,
        ]);

        $this->actingAs($kasir);

        $response = $this->getJson('/pos/transactions/data?filter=today');

        $response->assertOk();
        $response->assertJsonPath('summary.total_transactions', 1);
        $response->assertJsonPath('summary.total_sales', 15000);
        $response->assertJsonPath('summary.total_items_sold', 1);
        $response->assertJsonMissingPath('transactions.1');
        $response->assertJsonPath('categories.0.category_name', 'Rokok');
    }
}
