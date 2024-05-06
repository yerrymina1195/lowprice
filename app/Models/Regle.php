<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Regle extends Model
{
    use HasFactory;



    protected $fillable = [
        'type',
        'name',
        'content',
    ];



    public static function validatedRegle($data, $regle_id= null)
    {
        $rules = [
            'name' => [
                'required',
                'max:50','string',
                Rule::unique('regles')->ignore($regle_id),
            ],
            'type' => [
                'required',
                'max:50',
                Rule::unique('regles')->ignore($regle_id),
            ],

            'content'=>'required|string'
        ];


        $messages = [
            'name.required' => 'le nom est obligatoire.',
            'name.string' => 'le nom doit etre en string.',
            'type.required' => 'Le type de livraison est obligatoire',
            'type.max' => 'Le type du livraison ne doit pas dépasser :max caractères',
            'content.required'=> 'le conontenu est obligatoire'
        ];
        return Validator::make($data, $rules, $messages);
    }
}
