<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PosController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CashierController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserPinController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\StockMovementController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\ReportController;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');


/*
|--------------------------------------------------------------------------
| POS / KASIR
|--------------------------------------------------------------------------
*/


Route::middleware(['auth', 'active', 'role:kasir'])->prefix('pos')->name('pos.')->group(function () {
    Route::get('/', [PosController::class, 'index'])
        ->name('index');

    Route::get('/transactions', [PosController::class, 'transactions'])
        ->name('transactions.index');

    Route::post('/transactions/{transaction}/void-request', [PosController::class, 'requestVoid'])
        ->name('transactions.void.request');

    Route::get('/scan-barcode', [PosController::class, 'scanBarcode'])
        ->name('scan-barcode');

    Route::post('/cart/{product}', [PosController::class, 'addToCart'])
        ->name('cart.add');

    Route::patch('/cart/{product}', [PosController::class, 'updateCart'])
        ->name('cart.update');

    Route::delete('/cart/{product}', [PosController::class, 'removeFromCart'])
        ->name('cart.remove');

    Route::delete('/cart', [PosController::class, 'clearCart'])
        ->name('cart.clear');

    Route::post('/checkout', [PosController::class, 'checkout'])
        ->name('checkout');
});


/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Cashier monitoring for admin
        Route::get('/cashiers', [CashierController::class, 'index'])
            ->name('cashiers.index');

        Route::get('/audit-logs', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])
            ->name('audit-logs.index');

        // User PIN management
        Route::get('/users/{user}/pin', [UserPinController::class, 'edit'])
            ->name('users.pin.edit');

        Route::post('/users/{user}/pin', [UserPinController::class, 'update'])
            ->name('users.pin.update');

        // Void requests handling
        Route::get('/voids', [\App\Http\Controllers\Admin\TransactionVoidController::class, 'index'])
            ->name('voids.index');

        Route::post('/voids/{transactionVoid}/approve', [\App\Http\Controllers\Admin\TransactionVoidController::class, 'approve'])
            ->name('voids.approve');

        Route::post('/voids/{transactionVoid}/reject', [\App\Http\Controllers\Admin\TransactionVoidController::class, 'reject'])
            ->name('voids.reject');


        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */

        Route::resource('categories', CategoryController::class)
            ->except(['show']);


        /*
        |--------------------------------------------------------------------------
        | Products
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/products/print',
            [ProductController::class, 'printAllBarcodes']
        )->name('products.print');

        Route::get(
            '/products/{product}/barcode',
            [ProductController::class, 'barcode']
        )->name('products.barcode');

        Route::get(
            '/products/{product}/print-barcode',
            [ProductController::class, 'printBarcode']
        )->name('products.print-barcode');


        Route::resource(
            'products',
            ProductController::class
        )->except(['show']);

        Route::get(
            '/admin/products/print-all-barcodes',
            [ProductController::class, 'printAllBarcodes']
        )->name('products.print-all-barcodes');

        /*
        |--------------------------------------------------------------------------
        | Stock
        |--------------------------------------------------------------------------
        */

        Route::get('/stock', [StockController::class, 'index'])
            ->name('stock.index');

        Route::get('/stock/opname', [StockController::class, 'opnameIndex'])
            ->name('stock.opname.index');

        Route::post('/stock/opname', [StockController::class, 'opnameStore'])
            ->name('stock.opname.store');

        Route::get('/stock/{product}/create', [StockController::class, 'create'])
            ->name('stock.create');

        Route::get('/stock/{product}/adjust', [StockController::class, 'adjustCreate'])
            ->name('stock.adjust.create');

        Route::post('/stock/{product}/adjust', [StockController::class, 'adjustStore'])
            ->name('stock.adjust.store');

        Route::post('/stock/{product}', [StockController::class, 'store'])
            ->name('stock.store');

        Route::get('/stock/movements', [StockMovementController::class, 'index'])
            ->name('stock.movements');


        /*
        |--------------------------------------------------------------------------
        | Payment Methods
        |--------------------------------------------------------------------------
        */

        Route::get('/payment-methods', [PaymentMethodController::class, 'index'])
            ->name('payment-methods.index');

        Route::get('/payment-methods/create', [PaymentMethodController::class, 'create'])
            ->name('payment-methods.create');

        Route::post('/payment-methods', [PaymentMethodController::class, 'store'])
            ->name('payment-methods.store');

        Route::get('/payment-methods/{paymentMethod}/edit', [PaymentMethodController::class, 'edit'])
            ->name('payment-methods.edit');

        Route::match(['put', 'patch'], '/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'update'])
            ->name('payment-methods.update');

        Route::patch('/payment-methods/{paymentMethod}/toggle-status', [PaymentMethodController::class, 'toggleStatus'])
            ->name('payment-methods.toggle-status');
        
        /*
        |--------------------------------------------------------------------------
        | Transactions
        |--------------------------------------------------------------------------
        */

        Route::get('/transactions', [TransactionController::class, 'index'])
            ->name('transactions.index');

        Route::get('/transactions/{transaction}/receipt', [TransactionController::class, 'receipt'])
            ->name('transactions.receipt');

        Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])
            ->name('transactions.show');


        /*
        |--------------------------------------------------------------------------
        | Reports
        |--------------------------------------------------------------------------
        */

        Route::get('/reports/sales', [ReportController::class, 'sales'])
            ->name('reports.sales');

        // Settings
        Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])
            ->name('settings.index');

        Route::post('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])
            ->name('settings.update');

        
    });