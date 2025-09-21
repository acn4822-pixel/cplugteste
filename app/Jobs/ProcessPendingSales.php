<?php

namespace App\Jobs;

use App\Models\Sale;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessPendingSales implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $pendingSales = Sale::where('status', 'pending')
                            ->with('items.product')
                            ->get();
        
        foreach ($pendingSales as $sale) {
            try {
                DB::transaction(function () use ($sale) {
                    foreach ($sale->items as $item) {

                        $product = Product::where('sku', $item->product->sku)->lockForUpdate()->first();

                        Inventory::create([
                            'product_id' => $item->product->id,
                            'sku' => $item->product->sku,
                            'quantity' => -$item->quantity
                        ]);
                    }

                    $sale->status = 'completed';
                    $sale->save();
                });

            } catch (\Exception $e) {
                Log::error("Erro ao processar a venda ID: {$sale->id}. Erro: " . $e->getMessage());
            }
        }
                
    }
}