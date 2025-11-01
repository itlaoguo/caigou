<?php

use Illuminate\Support\Facades\Route;
use Modules\Purchase\Http\Controllers\PurchaseController;
use Modules\Purchase\Http\Controllers\PurchaseOrderController;

Route::prefix('purchase')->group(function(){
    
	Route::apiResource('purchase/order', PurchaseOrderController::class);
	//next

    Route::apiResource('purchase', PurchaseController::class);
});

