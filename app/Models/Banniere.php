<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Banniere extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'pagelink',
        'image'
    ];




    public static function validatedBanniere($data, $bannier_id = null)
    {
        $rules = [
            'name' => [
                'required',
                'max:50',
                Rule::unique('bannieres')->ignore($bannier_id),
            ],
            'pagelink' =>'required|string|max:250',
            'image' => 'required|image|max:2048',
        ];

        $messages = [
            'name.required' => 'Le nom du banniere est obligatoire',
            'name.max' => 'Le nom du banniere ne doit pas dépasser :max caractères',
            'name.unique' => 'Le nom du banniere doit être unique',
            'pagelink.required' => 'Le pagelink du banniere est obligatoire',
            'pagelink.max' => 'Le pagelink du banniere ne doit pas dépasser :max caractères',
            'image.image' => 'Le fichier doit être une image',
            'image.required' => "l'image est obligatoire",
            'image.max' => 'La taille de l\'image ne doit pas dépasser 2 Mo',
        ];

        return Validator::make($data, $rules, $messages);
    }
}
