<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosQrisCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_qris_checkout_uses_total_as_paid_amount_and_zero_change(): void
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
            'name' => 'Kasir QRIS',
            'email' => 'kasir-qris@example.com',
            'password' => bcrypt('password'),
            'role_id' => $kasirRole->id,
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Kategori Test',
            'slug' => 'kategori-test-qris',
            'description' => 'Kategori tes',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'barcode' => 'QRS-001',
            'sku' => 'QRS-001',
            'name' => 'Produk QRIS',
            'buy_price' => 10000,
            'sell_price' => 25000,
            'stock' => 100,
            'stock_min' => 1,
            'unit' => 'pcs',
            'stock_unit' => 'pcs',
            'selling_unit' => 'pcs',
            'is_active' => true,
        ]);

        $paymentMethod = PaymentMethod::create([
            'name' => 'QRIS',
            'description' => 'Pembayaran QRIS',
            'is_active' => true,
        ]);

        $response = $this->actingAs($kasir)
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
            ])
            ->post('/pos/checkout', [
                'payment_method_id' => $paymentMethod->id,
            ]);

        $response->assertSessionHas('success', 'Transaksi berhasil disimpan.');

        $transaction = Transaction::latest()->first();

        $this->assertNotNull($transaction);
        $this->assertSame(25000, (int) $transaction->total);
        $this->assertSame(25000, (int) $transaction->paid_amount);
        $this->assertSame(0, (int) $transaction->change_amount);
        $this->assertSame('completed', $transaction->status);
    }
}
