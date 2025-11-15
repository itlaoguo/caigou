<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\OrderController;

Route::prefix('order')->group(function(){
    
	Route::apiResource('order', OrderController::class);
	//next
});
