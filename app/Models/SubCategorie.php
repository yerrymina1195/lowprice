<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


/**
 * @OA\Schema(
 *     schema="Subcategory",
 *     title="Subcategory",
 *     required={"id", "name", "categorie_id"},
 *     @OA\Property(property="id", type="integer", example="2"),
 *     @OA\Property(property="name", type="string", example="Automobile"),
 *     @OA\Property(property="category_id", type="integer", example="5")
 * )
 */
class SubCategorie extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'categorie_id',
    ];

    public function categories()
    {
        return $this->belongsTo(Categorie::class,'categorie_id');
    }



    public static function validatedSubCategory($data, $subcategorId = null)
    {
        $rules = [
            'name' => [
                'required',
                'max:50',
                Rule::unique('sub_categories')->ignore($subcategorId),
            ],
            'categorie_id' => 'required|exists:categories,id',
        ];

        $messages = [
            'name.required' => 'Le nom du categorie est obligatoire',
            'name.max' => 'Le nom du categorie ne doit pas dépasser :max caractères',
            'name.unique' => 'Le nom du categorie doit être unique',
            'categorie_Id.required' => 'Choisissez une catégorie',
            'categorie_Id.exists' => 'Cette catégorie n\'existe pas',
        ];

        return Validator::make($data, $rules, $messages);
    }
    public function produit()
    {
        return $this->hasMany(Produit::class);
    }
}
