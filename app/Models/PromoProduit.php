<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class PromoProduit extends Model
{
    use HasFactory;
    protected $fillable = [
        'produit_id',
        'promobanniere_id',
        'prixpromo'
    ];


    public static function validatedPromoProduit($data)
    {
        $rules = [
            'produit_id' => 'required|exists:produits,id',
            'promobanniere_id' => 'required|exists:promo_bannieres,id',
            'prixpromo'=> 'required|integer|min:0',

        ];

        $messages =[
            'produit_id.required'=>'produit_id est obligatoire pour un promo',
            'produit_id.exists'=>'ce promo est introuvable',
            'promobanniere_id.required'=>'promobanniere_idest obligatoire pour creer un promo',
            'promobanniere_id.exists'=>'ce type de promo est introuvable',
            'prixpromo.required' => 'le prixpromo est obligatoire.',
            'prixpromo.integer' => 'le prixpromo doit etre en chiffre.',
            'prixpromo.min' => 'prixpromo doit etre au moins 0.',
        ];

        return Validator::make($data, $rules, $messages);
    }


    public function produits()
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

    public function promos()
    {
        return $this->belongsTo(PromoBanniere::class, 'promobanniere_id');
    }
}
