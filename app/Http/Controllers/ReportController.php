<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $query = Sale::query();

        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->query('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->query('end_date'));
        }

        if ($request->filled('product_sku')) {
            $query->whereHas('items.product', function ($productQuery) use ($request) {
                $productQuery->where('sku', $request->query('product_sku'));
            });
        }
        
        // Carrega Sale + Items + Product 
        /*
        $sales = Sale::with('items.product')->get();
        */
        
        // NOVO: Carrega apenas a coluna 'id' e 'sku' do produto
        $sales = $query->with(['items.product' => function ($query) {
            $query->select('id', 'sku');
        }])->get();

        return response()->json([
            'message' => 'Relatório de vendas gerado com sucesso.',
            'data' => $sales
        ], 200);
    }
}