<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Favorite extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'produit_id'
    ];

    public static function validatedFavorite($data)
    {
        $rules = [
            'produit_id' => 'required|exists:produits,id'
        ];

        $messages =[
            'produit_id.required'=>'produit_id est obligatoire pour un promo',
            'produit_id.exists'=>'ce promo est introuvable',
        ];

        return Validator::make($data, $rules, $messages);
    }

}
