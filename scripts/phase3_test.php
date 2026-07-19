<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\StockMovement;
use App\Models\TransactionVoid;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

echo "Starting Phase3 automated test...\n";

// Locate kasir and admin
$kasir = User::where('email', 'kasir1@kasir.test')->first();
$admin = User::where('email', 'admin@kasir.test')->first();

if (! $kasir || ! $admin) {
    echo "Required users not found. Ensure seeders ran.\n";
    exit(1);
}

echo "Found kasir: {$kasir->email}, admin: {$admin->email}\n";

// Set PIN for kasir and verify
$kasir->setPin('1234');
echo "Set PIN for kasir. checkPin('1234') => " . ($kasir->checkPin('1234') ? 'OK' : 'FAIL') . "\n";

// Create tembakau category
$category = Category::updateOrCreate(
    ['slug' => 'tembakau-test'],
    ['name' => 'Tembakau Test', 'description' => 'Kategori test']
);

// Create product
$product = Product::create([
    'category_id' => $category->id,
    'sku' => 'TMK-TEST-' . uniqid(),
    'barcode' => null,
    'name' => 'Tembakau Test',
    'description' => 'Produk tembakau untuk test',
    'buy_price' => 100,
    'sell_price' => 200, // per gram
    'stock' => 1000, // grams
    'stock_min' => 100,
    'unit' => 'gram',
    'stock_unit' => 'gram',
    'selling_unit' => 'gram',
    'is_active' => true,
]);

// Ensure barcode exists
if (empty($product->barcode)) {
    $product->barcode = Product::generateUniqueBarcode();
    $product->save();
}

echo "Created product {$product->name} (barcode: {$product->barcode}) with stock {$product->stock}\n";

// Simulate transaction by kasir: sell 100 grams
$qty = 100;
$subtotal = $product->sell_price * $qty;

$transaction = Transaction::create([
    'invoice_no' => 'TEST-' . now()->format('YmdHis') . '-' . strtoupper(substr(uniqid(),0,4)),
    'user_id' => $kasir->id,
    'subtotal' => $subtotal,
    'discount' => 0,
    'total' => $subtotal,
    'payment_method_id' => null,
    'paid_amount' => $subtotal,
    'change_amount' => 0,
    'status' => 'completed',
]);

TransactionItem::create([
    'transaction_id' => $transaction->id,
    'product_id' => $product->id,
    'qty' => $qty,
    'price' => $product->sell_price,
    'discount' => 0,
    'subtotal' => $subtotal,
]);

// Reduce stock
$product->decrement('stock', $qty);

StockMovement::create([
    'product_id' => $product->id,
    'change' => -$qty,
    'type' => 'stock_out',
    'reference_type' => 'transaction',
    'reference_id' => $transaction->id,
    'user_id' => $kasir->id,
    'note' => 'Stock out for test transaction ' . $transaction->invoice_no,
]);

AuditLog::create([
    'user_id' => $kasir->id,
    'action' => 'create_transaction',
    'auditable_type' => Transaction::class,
    'auditable_id' => $transaction->id,
    'description' => 'Test transaction created',
    'ip_address' => '127.0.0.1',
]);

echo "Transaction created: {$transaction->invoice_no} total={$transaction->total}. Product stock now: {$product->fresh()->stock}\n";

// Request void by kasir
$void = TransactionVoid::create([
    'transaction_id' => $transaction->id,
    'requested_by' => $kasir->id,
    'reason' => 'Test void request',
    'status' => 'void_requested',
]);

AuditLog::create([
    'user_id' => $kasir->id,
    'action' => 'request_void',
    'auditable_type' => Transaction::class,
    'auditable_id' => $transaction->id,
    'description' => 'Void requested in test',
    'ip_address' => '127.0.0.1',
]);

echo "Void requested id={$void->id} for transaction {$transaction->invoice_no}\n";

// Admin approve
Auth::loginUsingId($admin->id);

$controller = new App\Http\Controllers\Admin\TransactionVoidController();
$req = new Illuminate\Http\Request();
$controller->approve($req, $void);

$transaction->refresh();
$product->refresh();

echo "After approve: transaction status={$transaction->status}, product stock={$product->stock}\n";

// Check audit log for approve_void
$log = AuditLog::where('action', 'approve_void')->where('auditable_id', $transaction->id)->first();
echo "Approve audit log exists: " . ($log ? 'YES' : 'NO') . "\n";

// Now create another transaction and test reject flow
$transaction2 = Transaction::create([
    'invoice_no' => 'TEST2-' . now()->format('YmdHis') . '-' . strtoupper(substr(uniqid(),0,4)),
    'user_id' => $kasir->id,
    'subtotal' => 1000,
    'discount' => 0,
    'total' => 1000,
    'payment_method_id' => null,
    'paid_amount' => 1000,
    'change_amount' => 0,
    'status' => 'completed',
]);

TransactionItem::create([
    'transaction_id' => $transaction2->id,
    'product_id' => $product->id,
    'qty' => 10,
    'price' => $product->sell_price,
    'discount' => 0,
    'subtotal' => 1000,
]);

$product->decrement('stock', 10);

StockMovement::create([
    'product_id' => $product->id,
    'change' => -10,
    'type' => 'stock_out',
    'reference_type' => 'transaction',
    'reference_id' => $transaction2->id,
    'user_id' => $kasir->id,
    'note' => 'Stock out for test transaction 2 ' . $transaction2->invoice_no,
]);

$void2 = TransactionVoid::create([
    'transaction_id' => $transaction2->id,
    'requested_by' => $kasir->id,
    'reason' => 'Test void reject',
    'status' => 'void_requested',
]);

// Admin reject
$controller->reject($req, $void2);

$transaction2->refresh();
echo "After reject: transaction2 status={$transaction2->status}\n";

$log2 = AuditLog::where('action', 'reject_void')->where('auditable_id', $transaction2->id)->first();
echo "Reject audit log exists: " . ($log2 ? 'YES' : 'NO') . "\n";

echo "Phase3 automated test completed.\n";
