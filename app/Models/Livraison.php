<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Livraison extends Model
{
    use HasFactory;

    protected $fillable= [
        'name',
        'type',
        'price',
        'ispublished'

    ];


    public static function validatedLivraison($data,$livraisonId=null)
    {
        $rules = [
            'name' => [
                'required',
                'max:50',
                Rule::unique('livraisons')->ignore($livraisonId),
            ],
            'type' => [
                'required',
                'max:50',
                Rule::unique('livraisons')->ignore($livraisonId),
            ],
            'price'=> 'required|integer',
            'ispublished'=> 'required|boolean'
        ];
        $messages = [
            'name.required' => 'le nom est obligatoire.',
            'name.string' => 'le nom doit etre en string.',
            'type.required' => 'Le type de livraison est obligatoire',
            'type.max' => 'Le type du livraison ne doit pas dépasser :max caractères',
            'price.required' => 'le prix est obligatoire.',
            'price.integer' => 'le prix doit etre en chiffre.',
            'price.min' => 'prix doit etre au moins 0.',
            'ispublished.required' => 'le champ ispublished est obligatoire.',
            'ispublished.boolean' => 'ispublished doit etre boolean.',
        ];
        return Validator::make($data, $rules, $messages);
    }


    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
