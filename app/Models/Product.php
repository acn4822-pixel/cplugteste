<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;
    //
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'price',
    ];    
    
    public static function generateUniqueSku()
    {
        do {
            $sku = Str::random(12);
        } while (self::where('sku', $sku)->exists());

        return $sku;
    }    
}