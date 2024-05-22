<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Schema(
 *     schema="Review",
 *     title="Review",
 *     required={"id", "user_id", "produit_id", "rating", "comment", "created_at", "updated_at"},
 *     @OA\Property(property="id", type="integer", format="int64", example="1"),
 *     @OA\Property(property="user_id", type="integer", example="1"),
 *     @OA\Property(property="produit_id", type="integer", example="29"),
 *     @OA\Property(property="rating", type="integer", example="4"),
 *     @OA\Property(property="comment", type="string", example="bvdjbjfbjkdbjkdbjfdbvjdbfd"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-05-16T15:23:17.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-05-16T15:23:17.000000Z")
 * )
 */

class Review extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'produit_id', 'rating', 'comment'];



    public static function validateReview($data)
    {
        $rules = [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:255',
            'produit_id' => 'required|exists:produits,id',

        ];

        $messages = [
            'rating.required' => 'La note est obligatoire',
            'rating.min' => 'La note doit être d\'au moins :min',
            'rating.max' => 'La note doit être d\'au moins :max',
            'product_id.exists' => 'Le produit spécifié n\'existe pas',
        ];

        return Validator::make($data, $rules, $messages);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }
}
