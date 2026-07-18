<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductBarcodeAndUnitTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private PaymentMethod $paymentMethod;
    private Category $tembakauCategory;
    private Category $pcsCategory;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create categories
        $this->tembakauCategory = Category::create([
            'name' => 'Tembakau',
            'slug' => 'tembakau',
            'description' => 'Produk Tembakau',
        ]);

        $this->pcsCategory = Category::create([
            'name' => 'Rokok Pcs',
            'slug' => 'rokok-pcs',
            'description' => 'Rokok Per Pcs',
        ]);

        // Create payment method
        $this->paymentMethod = PaymentMethod::create([
            'name' => 'Cash',
            'description' => 'Pembayaran Tunai',
            'is_active' => true,
        ]);
    }

    /**
     * TEST 1: Barcode harus unik
     * - Tidak boleh ada dua produk dengan barcode sama
     */
    public function test_barcode_must_be_unique(): void
    {
        $product1 = Product::create([
            'category_id' => $this->tembakauCategory->id,
            'barcode' => 'BARCODE123',
            'sku' => 'SKU001',
            'name' => 'Gudang Garam',
            'buy_price' => 15000,
            'sell_price' => 20000,
            'stock' => 1000,
            'stock_min' => 100,
            'stock_unit' => 'gram',
            'selling_unit' => 'ons',
            'unit' => 'ons',
            'is_active' => true,
        ]);

        // Try to create product dengan barcode yang sama
        $this->expectException(\Exception::class);
        
        Product::create([
            'category_id' => $this->tembakauCategory->id,
            'barcode' => 'BARCODE123',  // Barcode sama
            'sku' => 'SKU002',
            'name' => 'Djarum Black',
            'buy_price' => 16000,
            'sell_price' => 21000,
            'stock' => 800,
            'stock_min' => 100,
            'stock_unit' => 'gram',
            'selling_unit' => 'ons',
            'unit' => 'ons',
            'is_active' => true,
        ]);
    }

    /**
     * TEST 2: Pembelian Tembakau 100 Gram
     * - Qty dalam cart = 1 ons (100 gram)
     * - Harga per ons = Rp20,000
     * - Subtotal = Rp20,000
     * - Stock reduction = 100 gram
     * - Stock awal = 1000 gram, Stock akhir = 900 gram
     */
    public function test_tobacco_purchase_100_grams(): void
    {
        $product = Product::create([
            'category_id' => $this->tembakauCategory->id,
            'barcode' => 'BARCODE001',
            'sku' => 'GG001',
            'name' => 'Gudang Garam Surya',
            'buy_price' => 15000,
            'sell_price' => 20000,  // Rp20,000 per ons
            'stock' => 1000,        // 1000 gram
            'stock_min' => 100,
            'stock_unit' => 'gram',
            'selling_unit' => 'ons',
            'unit' => 'ons',
            'is_active' => true,
        ]);

        $this->actingAs($this->admin, 'api');

        // Simulate adding 1 ons (100 gram) to cart
        $cart = [
            $product->id => [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => 20000,
                'qty' => 1,  // 1 ons
                'unit' => 'ons',
                'is_tembakau' => true,
            ],
        ];

        session(['cart' => $cart]);

        // Checkout
        $response = $this->post('/pos/checkout', [
            'payment_method_id' => $this->paymentMethod->id,
            'paid_amount' => 20000,
        ]);

        $response->assertRedirect('/pos');
        $response->assertSessionHas('success', 'Transaksi berhasil disimpan.');

        // Verify transaction
        $transaction = Transaction::latest()->first();
        $this->assertNotNull($transaction);
        $this->assertEquals(20000, $transaction->subtotal);
        $this->assertEquals(20000, $transaction->total);

        // Verify transaction item
        $transactionItem = TransactionItem::where('transaction_id', $transaction->id)
            ->where('product_id', $product->id)
            ->first();
        $this->assertNotNull($transactionItem);
        $this->assertEquals(1, $transactionItem->qty);  // 1 ons
        $this->assertEquals(20000, $transactionItem->price);
        $this->assertEquals(20000, $transactionItem->subtotal);

        // Verify stock reduction (100 gram)
        $product->refresh();
        $this->assertEquals(900, $product->stock);  // 1000 - 100 = 900 gram

        // Verify stock movement
        $stockMovement = StockMovement::where('product_id', $product->id)
            ->where('reference_type', 'transaction')
            ->where('reference_id', $transaction->id)
            ->first();
        $this->assertNotNull($stockMovement);
        $this->assertEquals(-100, $stockMovement->change);  // Negative 100 gram
    }

    /**
     * TEST 3: Pembelian Tembakau 2.5 Ons = 250 Gram
     * - Qty dalam cart = 2.5 ons (250 gram)
     * - Harga per ons = Rp20,000
     * - Subtotal = Rp50,000
     * - Stock reduction = 250 gram
     */
    public function test_tobacco_purchase_2_5_ons(): void
    {
        $product = Product::create([
            'category_id' => $this->tembakauCategory->id,
            'barcode' => 'BARCODE002',
            'sku' => 'GG002',
            'name' => 'Gudang Garam Surya',
            'buy_price' => 15000,
            'sell_price' => 20000,
            'stock' => 1000,
            'stock_min' => 100,
            'stock_unit' => 'gram',
            'selling_unit' => 'ons',
            'unit' => 'ons',
            'is_active' => true,
        ]);

        $this->actingAs($this->admin, 'api');

        // Simulate adding 2.5 ons to cart
        $cart = [
            $product->id => [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => 20000,
                'qty' => 2.5,  // 2.5 ons
                'unit' => 'ons',
                'is_tembakau' => true,
            ],
        ];

        session(['cart' => $cart]);

        // Checkout
        $response = $this->post('/pos/checkout', [
            'payment_method_id' => $this->paymentMethod->id,
            'paid_amount' => 50000,
        ]);

        $response->assertRedirect('/pos');

        // Verify transaction
        $transaction = Transaction::latest()->first();
        $this->assertEquals(50000, $transaction->subtotal);  // 20000 * 2.5

        // Verify stock reduction (250 gram)
        $product->refresh();
        $this->assertEquals(750, $product->stock);  // 1000 - 250 = 750 gram
    }

    /**
     * TEST 4: Produk Non-Tembakau dengan Qty Integer
     * - Qty harus integer (tidak boleh decimal)
     * - Stock reduction = qty * 1
     */
    public function test_non_tobacco_purchase_with_integer_qty(): void
    {
        $product = Product::create([
            'category_id' => $this->pcsCategory->id,
            'barcode' => 'BARCODE003',
            'sku' => 'RP001',
            'name' => 'Rokok Kretek',
            'buy_price' => 50000,
            'sell_price' => 60000,
            'stock' => 100,  // 100 pcs
            'stock_min' => 10,
            'stock_unit' => 'pcs',
            'selling_unit' => 'pcs',
            'unit' => 'pcs',
            'is_active' => true,
        ]);

        $this->actingAs($this->admin, 'api');

        // Simulate adding 5 pcs to cart
        $cart = [
            $product->id => [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => 60000,
                'qty' => 5,  // 5 pcs (integer)
                'unit' => 'pcs',
                'is_tembakau' => false,
            ],
        ];

        session(['cart' => $cart]);

        // Checkout
        $response = $this->post('/pos/checkout', [
            'payment_method_id' => $this->paymentMethod->id,
            'paid_amount' => 300000,
        ]);

        $response->assertRedirect('/pos');

        // Verify transaction
        $transaction = Transaction::latest()->first();
        $this->assertEquals(300000, $transaction->subtotal);  // 60000 * 5

        // Verify stock reduction (5 pcs)
        $product->refresh();
        $this->assertEquals(95, $product->stock);  // 100 - 5 = 95 pcs
    }

    /**
     * TEST 5: Stock tidak boleh menjadi minus
     * - Jika stok tidak cukup, transaksi harus ditolak
     */
    public function test_stock_cannot_go_negative(): void
    {
        $product = Product::create([
            'category_id' => $this->tembakauCategory->id,
            'barcode' => 'BARCODE004',
            'sku' => 'GG004',
            'name' => 'Gudang Garam Surya',
            'buy_price' => 15000,
            'sell_price' => 20000,
            'stock' => 500,  // 500 gram
            'stock_min' => 100,
            'stock_unit' => 'gram',
            'selling_unit' => 'ons',
            'unit' => 'ons',
            'is_active' => true,
        ]);

        $this->actingAs($this->admin, 'api');

        // Simulate adding 7 ons (700 gram) to cart, but stock only 500 gram
        $cart = [
            $product->id => [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => 20000,
                'qty' => 7,  // 7 ons = 700 gram, but stock only 500 gram
                'unit' => 'ons',
                'is_tembakau' => true,
            ],
        ];

        session(['cart' => $cart]);

        // Checkout should fail
        $response = $this->post('/pos/checkout', [
            'payment_method_id' => $this->paymentMethod->id,
            'paid_amount' => 140000,
        ]);

        $response->assertStatus(422);
        $response->assertSessionHas('error', 'Stok produk Gudang Garam Surya tidak mencukupi');

        // Verify stock tidak berubah
        $product->refresh();
        $this->assertEquals(500, $product->stock);  // Tetap 500 gram

        // Verify tidak ada transaksi yang dibuat
        $this->assertEquals(0, Transaction::count());
    }

    /**
     * TEST 6: Barcode duplicate harus ditolak di validasi backend
     */
    public function test_barcode_duplicate_validation(): void
    {
        $this->actingAs($this->admin);

        // Create first product
        $response = $this->post('/admin/products', [
            'name' => 'Product 1',
            'sku' => 'SKU001',
            'barcode' => 'BARCODE123',
            'category_id' => $this->tembakauCategory->id,
            'buy_price' => 15000,
            'sell_price' => 20000,
            'stock' => 1000,
            'stock_min' => 100,
        ]);

        $response->assertRedirect('/admin/products');

        // Try to create second product with same barcode
        $response = $this->post('/admin/products', [
            'name' => 'Product 2',
            'sku' => 'SKU002',
            'barcode' => 'BARCODE123',  // Duplicate barcode
            'category_id' => $this->tembakauCategory->id,
            'buy_price' => 16000,
            'sell_price' => 21000,
            'stock' => 800,
            'stock_min' => 100,
        ]);

        $response->assertSessionHasErrors('barcode');
    }

    /**
     * TEST 7: Unit otomatis untuk kategori Tembakau
     * - stock_unit harus 'gram'
     * - selling_unit harus 'ons'
     */
    public function test_tobacco_units_auto_set(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post('/admin/products', [
            'name' => 'Gudang Garam',
            'sku' => 'GG999',
            'barcode' => 'GG999',
            'category_id' => $this->tembakauCategory->id,
            'buy_price' => 15000,
            'sell_price' => 20000,
            'stock' => 1000,
            'stock_min' => 100,
        ]);

        $product = Product::where('sku', 'GG999')->first();
        $this->assertNotNull($product);
        $this->assertEquals('gram', $product->stock_unit);
        $this->assertEquals('ons', $product->selling_unit);
    }

    /**
     * TEST 8: Unit otomatis untuk kategori non-Tembakau
     * - stock_unit harus 'pcs'
     * - selling_unit harus 'pcs'
     */
    public function test_non_tobacco_units_auto_set(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post('/admin/products', [
            'name' => 'Rokok Kretek',
            'sku' => 'RK999',
            'barcode' => 'RK999',
            'category_id' => $this->pcsCategory->id,
            'buy_price' => 50000,
            'sell_price' => 60000,
            'stock' => 100,
            'stock_min' => 10,
        ]);

        $product = Product::where('sku', 'RK999')->first();
        $this->assertNotNull($product);
        $this->assertEquals('pcs', $product->stock_unit);
        $this->assertEquals('pcs', $product->selling_unit);
    }
}
