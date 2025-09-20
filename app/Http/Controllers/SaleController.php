<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{
 
    /**
     * Criar novo registro de venda e seus itens.
     */
    public function store(Request $request)
    {
        // Validação da Requisição
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.sku' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $items = $request->input('items');
        $updateInventory = $request->input('updateInventory');

        // Pré-verificação de Estoque e Existência
        foreach ($items as $item) {
            $product = Product::where('sku', $item['sku'])->first();

            if (!$product) {
                return response()->json([
                    'message' => 'O produto com o SKU ' . $item['sku'] . ' não existe.'
                ], 404);
            }

            $currentStock = DB::table('inventories')
                ->where('sku', $item['sku'])
                ->sum('quantity');

            if ($currentStock < $item['quantity']) {
                return response()->json([
                    'message' => 'Estoque insuficiente para o produto ' . $item['sku']
                ], 400);
            }
        }

        // Inicia a Transação e Salva os Dados
        $totalAmount = 0;
        $totalCost = 0;

        DB::beginTransaction();

        try {

            $sale = Sale::create([
                'total_amount' => 0,
                'total_cost' => 0,
                'total_profit' => 0,
                'status' => 'new'
            ]);

            foreach ($items as $item) {
                $product = Product::where('sku', $item['sku'])->first();

                // Registra o item da venda
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->sale_price,
                    'unit_cost' => $product->cost_price,
                ]);

                if(!is_null($updateInventory)) {
                    // Atualiza o registro de inventário em tempo real com quantidade negativa
                    Inventory::create([
                        'product_id' => $product->id,
                        'sku' => $item['sku'],
                        'quantity' => -$item['quantity']
                    ]);
                }

                // Calcula os totais
                $totalAmount += $item['quantity'] * $product->sale_price;
                $totalCost += $item['quantity'] * $product->cost_price;
            }

            // Atualiza a venda com os totais
            $sale->update([
                'total_amount' => $totalAmount,
                'total_cost' => $totalCost,
                'total_profit' => $totalAmount - $totalCost,
                'status' => !is_null($updateInventory)?'completed':'pending'
            ]);
           DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao processar a venda.',
                'error' => $e->getMessage()
            ], 500);
        }        

        return response()->json([
            'message' => 'Venda registrada com sucesso.',
            'total_amount' => $totalAmount,
            'total_cost' => $totalCost
        ], 201);
    } 
}
