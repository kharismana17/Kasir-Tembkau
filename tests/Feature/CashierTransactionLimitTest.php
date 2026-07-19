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

class CashierTransactionLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_cannot_checkout_after_daily_transaction_limit(): void
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

        $kasir = User::create([
            'name' => 'Kasir Test',
            'email' => 'kasir@example.com',
            'password' => bcrypt('password'),
            'role_id' => $kasirRole->id,
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Kategori Test',
            'slug' => 'kategori-test',
            'description' => 'Kategori tes',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'barcode' => 'TEST-001',
            'sku' => 'TEST-001',
            'name' => 'Produk Test',
            'buy_price' => 10000,
            'sell_price' => 20000,
            'stock' => 100,
            'stock_min' => 1,
            'unit' => 'pcs',
            'stock_unit' => 'pcs',
            'selling_unit' => 'pcs',
            'is_active' => true,
        ]);

        $paymentMethod = PaymentMethod::create([
            'name' => 'Cash',
            'description' => 'Pembayaran tunai',
            'is_active' => true,
        ]);

        $limit = config('kasir.daily_transaction_limit', 20);

        for ($i = 0; $i < $limit; $i++) {
            $transaction = Transaction::create([
                'invoice_no' => 'INV-' . sprintf('%03d', $i),
                'user_id' => $kasir->id,
                'subtotal' => 20000,
                'discount' => 0,
                'total' => 20000,
                'payment_method_id' => $paymentMethod->id,
                'paid_amount' => 20000,
                'change_amount' => 0,
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $product->id,
                'qty' => 1,
                'price' => 20000,
                'discount' => 0,
                'subtotal' => 20000,
            ]);
        }

        $this->actingAs($kasir)
            ->withSession([
                'cart' => [
                    $product->id => [
                        'product_id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->sell_price,
                        'qty' => 1,
                        'unit' => $product->unit,
                        'is_tembakau' => false,
                    ],
                ],
            ]);

        $response = $this->post('/pos/checkout', [
            'payment_method_id' => $paymentMethod->id,
            'paid_amount' => 20000,
        ]);

        $response->assertSessionHas('error', "Batas transaksi harian tercapai. Maksimal {$limit} transaksi per hari.");
    }

    public function test_cashier_can_checkout_when_daily_limit_not_reached(): void
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

        $kasir = User::create([
            'name' => 'Kasir Test',
            'email' => 'kasir2@example.com',
            'password' => bcrypt('password'),
            'role_id' => $kasirRole->id,
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Kategori Test',
            'slug' => 'kategori-test-2',
            'description' => 'Kategori tes',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'barcode' => 'TEST-002',
            'sku' => 'TEST-002',
            'name' => 'Produk Test B',
            'buy_price' => 10000,
            'sell_price' => 20000,
            'stock' => 100,
            'stock_min' => 1,
            'unit' => 'pcs',
            'stock_unit' => 'pcs',
            'selling_unit' => 'pcs',
            'is_active' => true,
        ]);

        $paymentMethod = PaymentMethod::create([
            'name' => 'Cash',
            'description' => 'Pembayaran tunai',
            'is_active' => true,
        ]);

        $this->actingAs($kasir)
            ->withSession([
                'cart' => [
                    $product->id => [
                        'product_id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->sell_price,
                        'qty' => 1,
                        'unit' => $product->unit,
                        'is_tembakau' => false,
                    ],
                ],
            ]);

        $response = $this->post('/pos/checkout', [
            'payment_method_id' => $paymentMethod->id,
            'paid_amount' => 20000,
        ]);

        $response->assertRedirect('/pos');
        $response->assertSessionHas('success', 'Transaksi berhasil disimpan.');

        $this->assertDatabaseHas('transactions', [
            'user_id' => $kasir->id,
            'total' => 20000,
            'status' => 'completed',
        ]);
    }
}
