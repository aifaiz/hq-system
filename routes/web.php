<?php

use App\Livewire\Agent;
use App\Livewire\Distributor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Agent\ToyyibPayController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('order/{refcode}', Agent\Product::class)->name('agent.product');
Route::get('order/{refcode}/product/{slug}', Agent\ViewProduct::class)->name('agent.product.view');
Route::get('order/{refcode}/cart', Agent\CartSummary::class)->name('agent.cart');
Route::get('order-completed/{refcode}/{billCode}', Agent\OrderCompleted::class)->name('agent.order.completed');

Route::group(['prefix'=>'toyyibpay'], function(){
    Route::get('agent/return', [ToyyibPayController::class, 'validatePayment'])->name('toyyibpay.agent.order.return');
    Route::get('agent/callback', [ToyyibPayController::class, 'validateCallback'])->name('toyyibpay.order.callback');
});

// Distributor Routes
Route::get('register-agent/{refcode}', Distributor\RegisterAgent::class)->name('distributor.reg.agent');
// Distributor Routes