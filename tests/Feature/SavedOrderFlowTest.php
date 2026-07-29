<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Role;
use App\Models\SavedOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SavedOrderFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_saved_order_can_be_saved_loaded_and_deleted(): void
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
            'email' => 'saved-order@example.com',
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
            'barcode' => 'SAVED-001',
            'sku' => 'SAVED-001',
            'name' => 'Produk Tersimpan',
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
                        'qty' => 2,
                        'unit' => $product->unit,
                        'is_tembakau' => false,
                    ],
                ],
            ]);

        $response = $this->post('/saved-orders');

        $response->assertRedirect(route('pos.index'));
        $response->assertSessionHas('success', 'Saved order berhasil disimpan.');

        $savedOrder = SavedOrder::where('user_id', $kasir->id)->latest()->firstOrFail();

        $this->assertDatabaseHas('saved_orders', [
            'id' => $savedOrder->id,
            'user_id' => $kasir->id,
        ]);

        $this->assertDatabaseHas('saved_order_items', [
            'saved_order_id' => $savedOrder->id,
            'product_id' => $product->id,
            'qty' => 2,
        ]);

        $this->assertSame([], session('cart', []));

        $loadResponse = $this->post("/saved-orders/{$savedOrder->id}/load");

        $loadResponse->assertRedirect(route('pos.index'));

        $cart = session('cart', []);
        $this->assertArrayHasKey($product->id, $cart);
        $this->assertSame($savedOrder->id, $cart[$product->id]['saved_order_id'] ?? null);

        $deleteResponse = $this->delete("/saved-orders/{$savedOrder->id}");

        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('saved_orders', ['id' => $savedOrder->id]);
    }
}
