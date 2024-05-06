<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SubCategorie extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
    ];

    public function categories()
    {
        return $this->belongsTo(Categorie::class,'category_id');
    }



    public static function validatedSubCategory($data, $subcategorId = null)
    {
        $rules = [
            'name' => [
                'required',
                'max:50',
                Rule::unique('sub_categories')->ignore($subcategorId),
            ],
            'category_id' => 'required|exists:categories,id',
        ];

        $messages = [
            'name.required' => 'Le nom du categorie est obligatoire',
            'name.max' => 'Le nom du categorie ne doit pas dépasser :max caractères',
            'name.unique' => 'Le nom du categorie doit être unique',
            'category_Id.required' => 'Choisissez une catégorie',
            'category_Id.exists' => 'Cette catégorie n\'existe pas',
        ];

        return Validator::make($data, $rules, $messages);
    }
    public function produit()
    {
        return $this->hasMany(Produit::class);
    }
}
