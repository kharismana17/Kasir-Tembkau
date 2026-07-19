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

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private PaymentMethod $paymentMethod;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

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

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);

        $this->paymentMethod = PaymentMethod::create([
            'name' => 'Cash',
            'description' => 'Pembayaran tunai',
            'is_active' => true,
        ]);

        $this->category = Category::create([
            'name' => 'Produk',
            'slug' => 'produk',
            'description' => 'Kategori produk',
        ]);
    }

    public function test_admin_dashboard_shows_sales_metrics_and_best_selling_products(): void
    {
        $productA = Product::create([
            'category_id' => $this->category->id,
            'barcode' => 'PROD-A',
            'sku' => 'A001',
            'name' => 'Produk A',
            'buy_price' => 10000,
            'sell_price' => 15000,
            'stock' => 100,
            'stock_min' => 10,
            'unit' => 'pcs',
            'stock_unit' => 'pcs',
            'selling_unit' => 'pcs',
            'is_active' => true,
        ]);

        $productB = Product::create([
            'category_id' => $this->category->id,
            'barcode' => 'PROD-B',
            'sku' => 'B001',
            'name' => 'Produk B',
            'buy_price' => 20000,
            'sell_price' => 25000,
            'stock' => 80,
            'stock_min' => 10,
            'unit' => 'pcs',
            'stock_unit' => 'pcs',
            'selling_unit' => 'pcs',
            'is_active' => true,
        ]);

        $today = now();
        $twoDaysAgo = now()->subDays(2);

        $transactionA = Transaction::create([
            'invoice_no' => 'INV-001',
            'user_id' => $this->admin->id,
            'subtotal' => 45000,
            'discount' => 0,
            'total' => 45000,
            'payment_method_id' => $this->paymentMethod->id,
            'paid_amount' => 45000,
            'change_amount' => 0,
            'status' => 'completed',
            'notes' => 'Penjualan valid',
            'created_at' => $today,
            'updated_at' => $today,
        ]);

        TransactionItem::create([
            'transaction_id' => $transactionA->id,
            'product_id' => $productA->id,
            'qty' => 2,
            'price' => 15000,
            'discount' => 0,
            'subtotal' => 30000,
        ]);

        TransactionItem::create([
            'transaction_id' => $transactionA->id,
            'product_id' => $productB->id,
            'qty' => 1,
            'price' => 15000,
            'discount' => 0,
            'subtotal' => 15000,
        ]);

        $transactionB = Transaction::create([
            'invoice_no' => 'INV-002',
            'user_id' => $this->admin->id,
            'subtotal' => 25000,
            'discount' => 0,
            'total' => 25000,
            'payment_method_id' => $this->paymentMethod->id,
            'paid_amount' => 25000,
            'change_amount' => 0,
            'status' => 'voided',
            'notes' => 'Void transaksi',
            'created_at' => $today,
            'updated_at' => $today,
        ]);

        TransactionItem::create([
            'transaction_id' => $transactionB->id,
            'product_id' => $productB->id,
            'qty' => 1,
            'price' => 25000,
            'discount' => 0,
            'subtotal' => 25000,
        ]);

        $transactionC = Transaction::create([
            'invoice_no' => 'INV-003',
            'user_id' => $this->admin->id,
            'subtotal' => 30000,
            'discount' => 0,
            'total' => 30000,
            'payment_method_id' => $this->paymentMethod->id,
            'paid_amount' => 30000,
            'change_amount' => 0,
            'status' => 'completed',
            'notes' => 'Penjualan beberapa hari lalu',
            'created_at' => $twoDaysAgo,
            'updated_at' => $twoDaysAgo,
        ]);

        TransactionItem::create([
            'transaction_id' => $transactionC->id,
            'product_id' => $productB->id,
            'qty' => 2,
            'price' => 15000,
            'discount' => 0,
            'subtotal' => 30000,
        ]);

        $this->actingAs($this->admin);

        $response = $this->get('/admin');

        $response->assertStatus(200);
        $response->assertSeeText('Dashboard');
        $response->assertSeeText('Transaksi Hari Ini');
        $response->assertSeeText('Penjualan Hari Ini');
        $response->assertSeeText('Pendapatan Minggu Ini');
        $response->assertSeeText('Pendapatan Bulan Ini');

        $response->assertSeeText('Rp 45.000');
        $response->assertSeeText('Rp 75.000');

        $response->assertSeeText('Produk Terlaris');
        $response->assertSeeText('Produk A');
        $response->assertSeeText('Produk B');

        $response->assertSee('salesChart');
    }
}
