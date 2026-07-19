<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Admin\StockController;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

function assertTrue($condition, $message)
{
    echo ($condition ? "OK" : "FAIL") . ": $message\n";
    if (! $condition) {
        exit(1);
    }
}

$admin = User::where('email', 'admin@kasir.test')->first();

if (! $admin) {
    echo "Admin user not found. Run seeders first.\n";
    exit(1);
}

Auth::loginUsingId($admin->id);

$category = Category::updateOrCreate(
    ['slug' => 'phase4-test'],
    ['name' => 'Phase4 Test', 'description' => 'Kategori test untuk Phase 4']
);

$product = Product::updateOrCreate(
    ['sku' => 'OPNAME-TEST-' . uniqid()],
    [
        'category_id' => $category->id,
        'barcode' => null,
        'name' => 'Produk Stok Opname Test',
        'description' => 'Produk test untuk stok opname dan penyesuaian manual',
        'buy_price' => 5000,
        'sell_price' => 7000,
        'stock' => 100,
        'stock_min' => 10,
        'unit' => 'pcs',
        'stock_unit' => 'pcs',
        'selling_unit' => 'pcs',
        'is_active' => true,
    ]
);

if (empty($product->barcode)) {
    $product->barcode = Product::generateUniqueBarcode();
    $product->save();
}

$controller = new StockController();

$product->refresh();
assertTrue($product->stock === 100, 'Initial stock is 100');

// a. Stock opname menaikkan stok
$request = new Request();
$request->merge([
    'stock_physical' => [$product->id => 120],
    'opname_note' => 'Test opname naik',
]);
$request->server->set('REMOTE_ADDR', '127.0.0.1');
$controller->opnameStore($request);
$product->refresh();
assertTrue($product->stock === 120, 'Stock increased to 120 after opname');

$movement = StockMovement::where('product_id', $product->id)
    ->where('type', 'stock_adjustment')
    ->where('reference_type', 'stock_opname')
    ->where('change', 20)
    ->first();
assertTrue($movement !== null, 'Stock opname increase movement recorded');

$audit = AuditLog::where('action', 'stock_opname')
    ->where('auditable_type', Product::class)
    ->where('auditable_id', $product->id)
    ->where('description', 'like', '%Test opname naik%')
    ->first();
assertTrue($audit !== null, 'Stock opname audit log recorded');

// b. Stock opname menurunkan stok
$request = new Request();
$request->merge([
    'stock_physical' => [$product->id => 90],
    'opname_note' => 'Test opname turun',
]);
$request->server->set('REMOTE_ADDR', '127.0.0.1');
$controller->opnameStore($request);
$product->refresh();
assertTrue($product->stock === 90, 'Stock decreased to 90 after opname');

$movement = StockMovement::where('product_id', $product->id)
    ->where('type', 'stock_adjustment')
    ->where('reference_type', 'stock_opname')
    ->where('change', -30)
    ->first();
assertTrue($movement !== null, 'Stock opname decrease movement recorded');

// c. opname tanpa perubahan tidak membuat movement
$countBefore = StockMovement::where('product_id', $product->id)
    ->where('reference_type', 'stock_opname')
    ->count();

$request = new Request();
$request->merge([
    'stock_physical' => [$product->id => 90],
    'opname_note' => 'Test opname tanpa perubahan',
]);
$request->server->set('REMOTE_ADDR', '127.0.0.1');
$controller->opnameStore($request);
$countAfter = StockMovement::where('product_id', $product->id)
    ->where('reference_type', 'stock_opname')
    ->count();
assertTrue($countAfter === $countBefore, 'No stock movement created for unchanged opname');

// d. manual adjustment tambah stok
$request = new Request();
$request->merge([
    'action' => 'add',
    'amount' => 15,
    'note' => 'Test adjustment tambah',
]);
$request->server->set('REMOTE_ADDR', '127.0.0.1');
$controller->adjustStore($request, $product);
$product->refresh();
assertTrue($product->stock === 105, 'Stock increased to 105 after manual add');

$movement = StockMovement::where('product_id', $product->id)
    ->where('type', 'stock_adjustment')
    ->where('reference_type', 'manual_adjustment')
    ->where('change', 15)
    ->first();
assertTrue($movement !== null, 'Manual add stock movement recorded');

$adjustAudit = AuditLog::where('action', 'stock_adjustment')
    ->where('auditable_type', Product::class)
    ->where('auditable_id', $product->id)
    ->where('description', 'like', '%Test adjustment tambah%')
    ->first();
assertTrue($adjustAudit !== null, 'Manual adjustment audit log recorded');

// e. manual adjustment kurangi stok
$request = new Request();
$request->merge([
    'action' => 'reduce',
    'amount' => 5,
    'note' => 'Test adjustment kurang',
]);
$request->server->set('REMOTE_ADDR', '127.0.0.1');
$controller->adjustStore($request, $product);
$product->refresh();
assertTrue($product->stock === 100, 'Stock decreased to 100 after manual reduce');

$movement = StockMovement::where('product_id', $product->id)
    ->where('type', 'stock_adjustment')
    ->where('reference_type', 'manual_adjustment')
    ->where('change', -5)
    ->first();
assertTrue($movement !== null, 'Manual reduce stock movement recorded');

$adjustAudit = AuditLog::where('action', 'stock_adjustment')
    ->where('auditable_type', Product::class)
    ->where('auditable_id', $product->id)
    ->where('description', 'like', '%Test adjustment kurang%')
    ->first();
assertTrue($adjustAudit !== null, 'Manual adjustment audit log recorded for reduction');

// f. stok tidak boleh negatif
$request = new Request();
$request->merge([
    'action' => 'reduce',
    'amount' => 1000,
    'note' => 'Test adjustment negatif',
]);
$request->server->set('REMOTE_ADDR', '127.0.0.1');
$response = $controller->adjustStore($request, $product);
assertTrue(
    method_exists($response, 'getSession') && $response->getSession()->has('errors'),
    'Negative stock adjustment returns validation error'
);

// g. stock movement tercatat
$movementCount = StockMovement::where('product_id', $product->id)
    ->whereIn('reference_type', ['stock_opname', 'manual_adjustment'])
    ->count();
assertTrue($movementCount >= 4, 'Stock movements recorded for opname and manual adjustments');

// h. audit log tercatat
$auditCount = AuditLog::where('auditable_type', Product::class)
    ->where('auditable_id', $product->id)
    ->whereIn('action', ['stock_opname', 'stock_adjustment'])
    ->count();
assertTrue($auditCount >= 4, 'Audit log entries recorded for opname and adjustments');

echo "Phase4 automated test completed successfully.\n";
