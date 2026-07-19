<?php

namespace Tests\Feature;

use App\Models\CashierUnit;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCashierMonitoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cashier_monitoring_shows_today_unit_statistics(): void
    {
        $adminRole = Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Administrator',
        ]);

        $kasirRole = Role::create([
            'name' => 'Kasir',
            'slug' => 'kasir',
            'description' => 'Kasir',
        ]);

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Kategori Tes',
            'slug' => 'kategori-tes',
            'description' => 'Kategori untuk pengujian',
        ]);

        $unitA = CashierUnit::create([
            'name' => 'Unit 1',
            'code' => 'U1',
            'is_active' => true,
        ]);

        $cashier = User::create([
            'name' => 'Kasir 1',
            'email' => 'kasir1@example.com',
            'password' => bcrypt('password'),
            'role_id' => $kasirRole->id,
            'cashier_unit_id' => $unitA->id,
            'is_active' => true,
        ]);

        $paymentMethod = PaymentMethod::create([
            'name' => 'Cash',
            'description' => 'Pembayaran tunai',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'barcode' => 'P001',
            'sku' => 'P001',
            'name' => 'Produk Test',
            'buy_price' => 10000,
            'sell_price' => 40000,
            'stock' => 10,
            'stock_min' => 1,
            'unit' => 'pcs',
            'stock_unit' => 'pcs',
            'selling_unit' => 'pcs',
            'is_active' => true,
        ]);

        $transaction = Transaction::create([
            'invoice_no' => 'INV-100',
            'user_id' => $cashier->id,
            'subtotal' => 40000,
            'discount' => 0,
            'total' => 40000,
            'payment_method_id' => $paymentMethod->id,
            'paid_amount' => 40000,
            'change_amount' => 0,
            'status' => 'completed',
            'notes' => 'Transaksi kasir hari ini',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        TransactionItem::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'qty' => 1,
            'price' => 40000,
            'discount' => 0,
            'subtotal' => 40000,
        ]);

        Transaction::create([
            'invoice_no' => 'INV-101',
            'user_id' => $cashier->id,
            'subtotal' => 30000,
            'discount' => 0,
            'total' => 30000,
            'payment_method_id' => $paymentMethod->id,
            'paid_amount' => 30000,
            'change_amount' => 0,
            'status' => 'voided',
            'notes' => 'Transaksi void',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($admin);

        $response = $this->get(route('admin.cashiers.index'));

        $response->assertStatus(200);
        $response->assertSeeText('Monitoring Kasir');
        $response->assertSeeText('Total Transaksi Hari Ini');
        $response->assertSeeText('Unit 1');
        $response->assertSeeText('Kasir 1');
        $response->assertSeeText('Rp 40.000');
        $response->assertSeeText('1 transaksi');
        $response->assertSee('salesByUnitChart');
        $response->assertSee('transactionsByUnitChart');
    }
}
