<?php

use App\Livewire\Agent;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('order/{refcode}', Agent\Product::class)->name('agent.product');
Route::get('order/{refcode}/cart', Agent\CartSummary::class)->name('agent.cart');