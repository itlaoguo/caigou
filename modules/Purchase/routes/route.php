<?php

use Illuminate\Support\Facades\Route;
use Modules\Purchase\Http\Controllers\PurchaseController;
use Modules\Purchase\Http\Controllers\PurchaseOrderController;

Route::prefix('purchase')->group(function(){

    Route::get('shops', [PurchaseOrderController::class,'shops']);
    Route::get('prepare', [PurchaseOrderController::class,'renderAndSplitPurchaseOrder']);

	Route::apiResource('order', PurchaseOrderController::class);

});

