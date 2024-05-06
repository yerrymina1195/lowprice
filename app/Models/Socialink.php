<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Socialink extends Model
{
    use HasFactory;



    protected $fillable = [
        'name',
        'via'
    ];



    public static function validatedSocialink($data, $social_id= null)
    {
        $rules = [
            'name' => [
                'required',
                'max:50','string',
                Rule::unique('socialinks')->ignore($social_id),
            ],
            'via' => [
                'required',
                'string',
                'max:250',
            ],

        ];


        $messages = [
            'name.required' => 'le nom est obligatoire.',
            'name.string' => 'le nom doit etre en string.',
            'via.required' => 'Le via de livraison est obligatoire',
            'via.max' => 'Le type du livraison ne doit pas dépasser :max caractères',
            'via.string' => 'le via doit etre en string.',
          
        ];
        return Validator::make($data, $rules, $messages);
    }
}
