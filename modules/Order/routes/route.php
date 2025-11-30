<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\OrderController;
use Modules\Order\Http\Controllers\AfterSaleController;

Route::prefix('order')->group(function(){

	Route::apiResource('order', OrderController::class);

    Route::apiResource('afterSale', AfterSaleController::class);
	//next
});
