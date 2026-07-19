<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\StockMovement;
use App\Models\TransactionVoid;
use App\Models\AuditLog;

echo "Starting cleanup of phase3 test data...\n";

$category = Category::where('slug', 'tembakau-test')->first();
if ($category) {
    echo "Found test category id={$category->id}, deleting related products...\n";
    $products = Product::where('category_id', $category->id)->get();
    foreach ($products as $product) {
        echo "Cleaning product {$product->id} {$product->name}\n";

        // Delete stock movements related to transactions with invoice TEST-% or reference_type void/transaction and reference_id in those transactions
        StockMovement::where('product_id', $product->id)
            ->where(function($q){
                $q->where('reference_type', 'transaction')
                  ->orWhere('reference_type', 'void')
                  ->orWhere('reference_type', 'manual');
            })->delete();

        // Delete transaction items for TEST invoices
        $txs = Transaction::where('invoice_no', 'like', 'TEST-%')->orWhere('invoice_no', 'like', 'TEST2-%')->get();
        foreach ($txs as $tx) {
            TransactionItem::where('transaction_id', $tx->id)->delete();
            TransactionVoid::where('transaction_id', $tx->id)->delete();
            AuditLog::where('auditable_type', Transaction::class)->where('auditable_id', $tx->id)->delete();
            echo "Deleting transaction {$tx->invoice_no}\n";
            $tx->delete();
        }

        // Delete any remaining stock movements for product
        StockMovement::where('product_id', $product->id)->delete();

        // Delete audit logs relating to product
        AuditLog::where('auditable_type', Product::class)->where('auditable_id', $product->id)->delete();

        $product->delete();
    }

    echo "Deleting test category...\n";
    $category->delete();
} else {
    echo "No test category found.\n";
}

// Delete generic audit logs created by test (actions containing 'Test')
AuditLog::where('description', 'like', '%Test%')->delete();
AuditLog::whereIn('action', ['approve_void','reject_void','request_void','create_transaction'])->delete();

// Delete any transaction voids with reason containing 'Test'
TransactionVoid::where('reason', 'like', '%Test%')->delete();

// Delete any products with name like 'Tembakau Test'
Product::where('name', 'like', 'Tembakau Test%')->delete();

echo "Cleanup completed.\n";
