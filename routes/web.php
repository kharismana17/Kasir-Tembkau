<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PosController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
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


Route::prefix('pos')->name('pos.')->group(function () {
    Route::get('/', [PosController::class, 'index'])
        ->name('index');

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

        Route::resource('products', ProductController::class)
            ->except(['show']);


        /*
        |--------------------------------------------------------------------------
        | Stock
        |--------------------------------------------------------------------------
        */

        Route::get('/stock', [StockController::class, 'index'])
            ->name('stock.index');

        Route::get('/stock/{product}/create', [StockController::class, 'create'])
            ->name('stock.create');

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
    });