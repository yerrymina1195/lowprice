<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="ProduiImage",
 *     title="Images",
 *     required={"id", "image", "produit_id", "created_at", "updated_at"},
 *     @OA\Property(property="id", type="integer", example="12"),
 *     @OA\Property(property="image", type="string", example="produits/velo.jpeg"),
 *     @OA\Property(property="produit_id", type="integer", example="29"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-05-16T14:36:34.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-05-16T14:36:34.000000Z"),
 * )
 */
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
