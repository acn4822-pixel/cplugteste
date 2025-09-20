<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{

    /**
     * Obter o status do inventário.
     */        
    public function index()
    {
        $inventorySummary = DB::table('products')
            ->leftJoin('inventories', 'products.sku', '=', 'inventories.sku')
            ->select(
                'products.sku',
                'products.name',
                DB::raw('COALESCE(SUM(inventories.quantity), 0) as total_quantity')
            )
            ->groupBy('products.sku', 'products.name')
            ->get();

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

        // Sucesso
        return response()->json([
            'message' => 'Entrada de produto registrada com sucesso.',
            'data' => [
                'sku' => $validated['sku'],
                'quantity' => $validated['quantity']
            ]
        ], 201);
    }
}