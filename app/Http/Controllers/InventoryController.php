<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InventoryController extends Controller
{
    /**
     * Store a new product entry in the inventory.
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

        // 1. Procura o produto pelo SKU
        $product = Product::where('sku', $validated['sku'])->first();

        // 2. Se o produto não for encontrado, retorna um erro
        if (!$product) {
            return response()->json([
                'message' => 'O produto com este SKU não existe.'
            ], 404);
        }

        // 3. Se o produto for encontrado, cria um novo registro na tabela 'inventory'
        Inventory::create([
            'sku' => $validated['sku'],
            'quantity' => $validated['quantity'],
            'product_id' => $product->id
        ]);

        return response()->json([
            'message' => 'Entrada de produto registrada com sucesso.',
            'data' => [
                'sku' => $validated['sku'],
                'quantity' => $validated['quantity']
            ]
        ], 201);
    }
}