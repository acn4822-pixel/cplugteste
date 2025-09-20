<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Store a new product entry in the inventory.
     */
    public function store(Request $request)
    {
        // Lógica para registrar a entrada de um produto no estoque        
        return response()->json([
            'message' => 'Entrada de produto registrada com sucesso.',
            'data' => $request->all()
        ], 201);
    }
}
