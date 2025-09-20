<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rota para obter a situação do inventário
Route::get('/inventory', [App\Http\Controllers\InventoryController::class, 'index']);

// Rota para atualização do inventário
Route::post('/inventory', [App\Http\Controllers\InventoryController::class, 'store']);

// Rota para registro de vendas
Route::post('/sale', [App\Http\Controllers\SaleController::class, 'store']);