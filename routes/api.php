<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\InventoryController;
use App\Http\Controllers\API\ProductBatcheController;
use App\Http\Controllers\API\WarehouseController;
use App\Http\Controllers\API\StockInputVoucherController;
use App\Http\Controllers\API\StockOutputVoucherController;
use App\Http\Controllers\API\StorageImageController;
use App\Http\Controllers\API\UnitController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/auth/refresh', [AuthController::class, 'refresh'])->name('auth.refresh');

Route::middleware('auth:api')->group(function () {
	Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
	Route::get('/me', [AuthController::class, 'me'])->name('auth.me');

	// Role Routes	
	Route::apiResource('roles', RoleController::class);

	// Category Routes
	Route::apiResource('categories', CategoryController::class);

	// Units of Measure Routes
	Route::apiResource('units', UnitController::class);

	// Product Routes
	Route::apiResource('products', ProductController::class);
	Route::prefix('products') ->group(function () {
		Route::get('/{id}/batches', [ProductBatcheController::class, 'index'])->name('products.batches');
		Route::post('/check-duplicate', [ProductController::class, 'checkDuplicate'])->name('products.check_duplicate');
	});

	// Inventory Routes
	Route::apiResource('inventory', InventoryController::class);
	Route::get('/inventory/warehouse/{warehouse_id}', [InventoryController::class, 'byWarehouse'])->name('inventory.by_warehouse');
	Route::get('/inventory/product/{product_id}', [InventoryController::class, 'byProduct'])->name('inventory.by_product');
	Route::get('/inventory/low-stock', [InventoryController::class, 'lowStock'])->name('inventory.low_stock');
	Route::patch('/inventory/{id}/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');

	// Warehouse Routes
	Route::apiResource('warehouses', WarehouseController::class);
	Route::get('/warehouses/active', [WarehouseController::class, 'active'])->name('warehouses.active');

	// Stock Input Voucher Routes
	Route::apiResource('stock-input-vouchers', StockInputVoucherController::class);
	Route::get('/stock-input-vouchers/warehouse/{warehouse_id}', [StockInputVoucherController::class, 'byWarehouse'])->name('stock_input_vouchers.by_warehouse');
	Route::get('/stock-input-vouchers/status/{status_id}', [StockInputVoucherController::class, 'byStatus'])->name('stock_input_vouchers.by_status');
	Route::post('/stock-input-vouchers/{id}/submit', [StockInputVoucherController::class, 'submit'])->name('stock_input_vouchers.submit');
	Route::post('/stock-input-vouchers/{id}/approve', [StockInputVoucherController::class, 'approve'])->name('stock_input_vouchers.approve');
	Route::post('/stock-input-vouchers/{id}/receive', [StockInputVoucherController::class, 'receive'])->name('stock_input_vouchers.receive');
	Route::post('/stock-input-vouchers/{id}/reject', [StockInputVoucherController::class, 'reject'])->name('stock_input_vouchers.reject');

	// Stock Output Voucher Routes
	Route::apiResource('stock-output-vouchers', StockOutputVoucherController::class);
	Route::get('/stock-output-vouchers/warehouse/{warehouse_id}', [StockOutputVoucherController::class, 'byWarehouse'])->name('stock_output_vouchers.by_warehouse');
	Route::get('/stock-output-vouchers/status/{status_id}', [StockOutputVoucherController::class, 'byStatus'])->name('stock_output_vouchers.by_status');
	Route::post('/stock-output-vouchers/{id}/submit', [StockOutputVoucherController::class, 'submit'])->name('stock_output_vouchers.submit');
	Route::post('/stock-output-vouchers/{id}/approve', [StockOutputVoucherController::class, 'approve'])->name('stock_output_vouchers.approve');
	Route::post('/stock-output-vouchers/{id}/complete', [StockOutputVoucherController::class, 'complete'])->name('stock_output_vouchers.complete');
	Route::post('/stock-output-vouchers/{id}/reject', [StockOutputVoucherController::class, 'reject'])->name('stock_output_vouchers.reject');
	Route::post('/stock-output-vouchers/{voucherId}/items/{itemId}/cancel', [StockOutputVoucherController::class, 'cancelItem'])->name('stock_output_vouchers.cancel_item');

	// Route for get presigned url to upload image to s3
	Route::post('/storage/presigned-url', [StorageImageController::class, 'getPresignedUrl'])->name('storage.presigned_url');
});




