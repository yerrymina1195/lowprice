<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PromoBanniere extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'taux',
        'image',
        'available'
    ];

    public static function validatedPromoBanniere($data, $PromoBanniereId= null )
    {
        $rules = [
            'title' => [
                'required','string',
                'max:50',
                Rule::unique('promo_bannieres')->ignore($PromoBanniereId),
            ],
            'taux' => 'required|integer|min:0|max:100',
            'image' => 'required|image|mimes:png,jpg,jpeg,webp',
            'available' => 'nullable'
        ];

        $messages = [
            'title.required' => 'le nom est obligatoire.',
            'title.string' => 'le nom doit etre en string.',
            'title.max' => 'le nom ne peut pas depassé 255 characters.',
            'taux.required' => 'le taux est obligatoire.',
            'taux.integer' => 'le taux doit etre en chiffre.',
            'taux.min' => 'taux doit etre au moins 0.',
            'taux.max' => 'taux doit etre au max 100.',
            'image.required' => 'image obligatoire'

        ];
        return Validator::make($data, $rules, $messages);
    }


    public function promos ()
    {
        $this->hasMany(PromoProduit::class);
    }

    
}
