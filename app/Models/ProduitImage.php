<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProduitImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'produit_id'
    ];

    public function produits()
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }
}
