<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'unit_cost',
    ];

    /**
     * Define a relação 'belongsTo' com o modelo Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}