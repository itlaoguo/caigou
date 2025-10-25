<?php

use Illuminate\Support\Facades\Route;
use Modules\Purchase\Http\Controllers\PurchaseController;

Route::prefix('purchase')->group(function(){
    //next

    Route::apiResource('purchase', PurchaseController::class);
});
