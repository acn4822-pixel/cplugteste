<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Inventory;
use App\Jobs\CleanOldInventoryRecords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class InventoryController extends Controller
{

    /**
     * Obter o status do inventário.
     */        
    public function index()
    {
        // Usa o Cache::remember() para armazenar a consulta por 60 minutos
        $inventorySummary = Cache::remember('inventory_summary', 60 * 60, function () {
            return DB::table('products')
                ->leftJoin('inventories', 'products.sku', '=', 'inventories.sku')
                ->select(
                    'products.sku',
                    'products.name',
                    DB::raw('COALESCE(SUM(inventories.quantity), 0) as total_quantity')
                )
                ->groupBy('products.sku', 'products.name')
                ->get();
        });

        return response()->json([
            'message' => 'Situação do estoque obtida com sucesso.',
            'data' => $inventorySummary
        ], 200);
    }

    /**
     * Criar novo registro de entrada no inventory.
     */
    public function store(Request $request)
    {

        // Cria um validador para verificar os dados
        $validator = Validator::make($request->all(), [
            'sku' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
        ]);

        // Se a validação falhar, retorna a resposta com as mensagens de erro
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro na validação.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Pega os dados validados
        $validated = $validator->validated();

        // Procura o produto pelo SKU
        $product = Product::where('sku', $validated['sku'])->first();

        // Se o produto não for encontrado, retorna um erro
        if (!$product) {
            return response()->json([
                'message' => 'O produto com este SKU não existe.'
            ], 404);
        }

        // Se o produto for encontrado, cria um novo registro na tabela 'inventory'
        Inventory::create([
            'sku' => $validated['sku'],
            'quantity' => $validated['quantity'],
            'product_id' => $product->id
        ]);

        // Apaga o cache
        Cache::forget('inventory_summary');

        // Sucesso
        return response()->json([
            'message' => 'Entrada de produto registrada com sucesso.',
            'data' => [
                'sku' => $validated['sku'],
                'quantity' => $validated['quantity']
            ]
        ], 201);
    }

    public function cleanInventory(Request $request)
    {
        // Dispara o job para ser executado
        //CleanOldInventoryRecords::dispatch();
        (new CleanOldInventoryRecords())->handle();

        Cache::forget('inventory_summary');
        
        return response()->json([
            'message' => 'Job de limpeza do inventory foi disparado.'
        ], 200);
    }
}