<?php

namespace App\Jobs;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CleanOldInventoryRecords implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute o job.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            
            $query = DB::table('inventories');

            // Em modo de teste == true, ignora os 90 dias
            $testMode = true;
            // Se o modo de teste == false, filtra os registros com mais de 90 dias.
            if (!$testMode) {
                $cutoffDate = Carbon::now()->subDays(90);
                $query->where('created_at', '<', $cutoffDate);
            }

            // Obtém os registros que serão processados (precisamos dos IDs e da quantidade)
            $recordsToProcess = $query->get(['id', 'product_id', 'quantity']);
            $inventoryIds = $recordsToProcess->pluck('id');

            // Se houver registros a serem processados...
            if ($recordsToProcess->isNotEmpty()) {

                // Soma as quantidades por SKU
                $skuBalances = $recordsToProcess->groupBy('product_id')->map(function ($group) {
                    return $group->sum('quantity');
                });

                // Apaga os registros de forma segura usando os IDs que coletamos
                DB::table('inventories')->whereIn('id', $inventoryIds)->delete();
                
                // Cria os novos registros de saldo
                foreach ($skuBalances as $productId => $balance) {
                    if ($balance != 0) {
                        $product = \App\Models\Product::find($productId);
                        if ($product) {
                            \App\Models\Inventory::create([
                                'product_id' => $product->id,
                                'sku' => $product->sku,
                                'quantity' => $balance
                            ]);
                        }
                    }
                }
            }
        });
    }    
}