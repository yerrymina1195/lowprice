<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Categorie extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
    ];


    public static function validatedCategory($data, $categorId = null)
    {
        $rules = [
            'name' => [
                'required',
                'max:50',
                Rule::unique('categories')->ignore($categorId),
            ],
            'image' => 'nullable|image|max:2048',
        ];

        $messages = [
            'name.required' => 'Le nom du categorie est obligatoire',
            'name.max' => 'Le nom du categorie ne doit pas dépasser :max caractères',
            'name.unique' => 'Le nom du categorie doit être unique',
            'image.image' => 'Le fichier doit être une image',
            'image.max' => 'La taille de l\'image ne doit pas dépasser 2 Mo',
        ];

        return Validator::make($data, $rules, $messages);
    }


    public function subcategories()
    {
        return $this->hasMany(SubCategorie::class);
    }
    public function produits()
    {
        return $this->hasMany(Produit::class);
    }
    
}
