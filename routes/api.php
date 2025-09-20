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

// Rota para limpeza do inventário
Route::post('/inventory/clean', [App\Http\Controllers\InventoryController::class, 'cleanInventory']);

// Rota para registro de vendas
Route::post('/sale', [App\Http\Controllers\SaleController::class, 'store']);

// Rota para atualização de estoque - sales->status = pending
Route::post('/sale/process-pending', [App\Http\Controllers\SaleController::class, 'processPendingSales']);

// Rota para obter detalhes de uma venda específica
Route::get('/sale/{id}', [App\Http\Controllers\SaleController::class, 'show']);

// Rota para obter relatório de vendas
Route::get('/reports/sales', [App\Http\Controllers\ReportController::class, 'sales']);
