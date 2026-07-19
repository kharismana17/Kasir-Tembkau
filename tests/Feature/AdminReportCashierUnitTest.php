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

class AdminReportCashierUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_report_includes_cashier_and_unit_breakdown(): void
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

        $unitA = CashierUnit::create([
            'name' => 'Unit A',
            'code' => 'A',
            'is_active' => true,
        ]);

        $unitB = CashierUnit::create([
            'name' => 'Unit B',
            'code' => 'B',
            'is_active' => true,
        ]);

        $kasirA = User::create([
            'name' => 'Kasir A',
            'email' => 'kasir-a@example.com',
            'password' => bcrypt('password'),
            'role_id' => $kasirRole->id,
            'cashier_unit_id' => $unitA->id,
            'is_active' => true,
        ]);

        $kasirB = User::create([
            'name' => 'Kasir B',
            'email' => 'kasir-b@example.com',
            'password' => bcrypt('password'),
            'role_id' => $kasirRole->id,
            'cashier_unit_id' => $unitB->id,
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Kategori Test',
            'slug' => 'kategori-test',
            'description' => 'Kategori untuk pengujian',
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
            'sell_price' => 25000,
            'stock' => 100,
            'stock_min' => 1,
            'unit' => 'pcs',
            'stock_unit' => 'pcs',
            'selling_unit' => 'pcs',
            'is_active' => true,
        ]);

        $trxA = Transaction::create([
            'invoice_no' => 'INV-001',
            'user_id' => $kasirA->id,
            'subtotal' => 25000,
            'discount' => 0,
            'total' => 25000,
            'payment_method_id' => $paymentMethod->id,
            'paid_amount' => 25000,
            'change_amount' => 0,
            'status' => 'completed',
            'notes' => 'Transaksi Unit A',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        TransactionItem::create([
            'transaction_id' => $trxA->id,
            'product_id' => $product->id,
            'qty' => 1,
            'price' => 25000,
            'discount' => 0,
            'subtotal' => 25000,
        ]);

        $trxB = Transaction::create([
            'invoice_no' => 'INV-002',
            'user_id' => $kasirB->id,
            'subtotal' => 50000,
            'discount' => 0,
            'total' => 50000,
            'payment_method_id' => $paymentMethod->id,
            'paid_amount' => 50000,
            'change_amount' => 0,
            'status' => 'completed',
            'notes' => 'Transaksi Unit B',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        TransactionItem::create([
            'transaction_id' => $trxB->id,
            'product_id' => $product->id,
            'qty' => 2,
            'price' => 25000,
            'discount' => 0,
            'subtotal' => 50000,
        ]);

        $this->actingAs($admin);

        $response = $this->get(route('admin.reports.sales', [
            'from' => now()->format('Y-m-d'),
            'to' => now()->format('Y-m-d'),
        ]));

        $response->assertStatus(200);
        $response->assertSeeText('Laporan Per Kasir');
        $response->assertSeeText('Kasir A');
        $response->assertSeeText('Kasir B');
        $response->assertSeeText('Unit A');
        $response->assertSeeText('Unit B');
        $response->assertSeeText('Rp 25.000');
        $response->assertSeeText('Rp 50.000');
        $response->assertSeeText('Detail Aktivitas Kasir');
    }
}
