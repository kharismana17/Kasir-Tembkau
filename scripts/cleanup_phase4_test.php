<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;

$testCategory = Category::where('slug', 'phase4-test')->first();
if ($testCategory) {
    $products = Product::where('category_id', $testCategory->id)->get();

    foreach ($products as $product) {
        echo "Cleaning product {$product->id} {$product->name}\n";

        StockMovement::where('product_id', $product->id)
            ->whereIn('reference_type', ['stock_opname', 'manual_adjustment'])
            ->delete();

        AuditLog::where('auditable_type', Product::class)
            ->where('auditable_id', $product->id)
            ->whereIn('action', ['stock_opname', 'stock_adjustment'])
            ->delete();

        $product->delete();
    }

    $testCategory->delete();
    echo "Deleted test category and products.\n";
} else {
    echo "No Phase4 test category found.\n";
}
