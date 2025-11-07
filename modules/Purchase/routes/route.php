<?php

use Illuminate\Support\Facades\Route;
use Modules\Purchase\Http\Controllers\PurchaseController;
use Modules\Purchase\Http\Controllers\PurchaseOrderController;

Route::prefix('purchase')->group(function(){

    Route::apiResource('purchase/order/shops', [PurchaseOrderController::class'shops']);

    Route::apiResource('purchase/order/render', PurchaseOrderController::class);

	Route::apiResource('purchase/order', PurchaseOrderController::class);
	//next

    Route::apiResource('purchase', PurchaseController::class);
});

