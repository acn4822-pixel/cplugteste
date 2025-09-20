<?php

namespace App\Jobs;

use App\Models\Sale;
use App\Models\Inventory;
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
        
        Log::info("Encontradas {$pendingSales->count()} vendas pendentes para processar.");

        foreach ($pendingSales as $sale) {
            try {
                DB::transaction(function () use ($sale) {
                    foreach ($sale->items as $item) {
                        Inventory::create([
                            'product_id' => $item->product->id,
                            'sku' => $item->product->sku,
                            'quantity' => -$item->quantity
                        ]);
                    }

                    $sale->status = 'completed';
                    $sale->save();
                });
                Log::info("Venda ID: {$sale->id} processada com sucesso.");

            } catch (\Exception $e) {
                Log::error("Erro ao processar a venda ID: {$sale->id}. Erro: " . $e->getMessage());
            }
        }
                
    }
}