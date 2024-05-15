<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Produit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'prix',
        'statut',
        'quantity',
        'categorie_id',
        'sub_categorie_id',
        'nouveaute'

    ];



    public static function validatedProduit($data, $produitId = null)
    {
        $rules = [
            'name' => [
                'required','string',
                'max:50',
                Rule::unique('produits')->ignore($produitId),
            ],
            'categorie_id' => 'required|exists:categories,id',
            'sub_categorie_id' => 'nullable|exists:sub_categories,id',
            'description'=> 'required|string|max:255',
            'prix'=> 'required|integer|min:0',
            'quantity' => 'required|integer|min:0',
            'statut'=> 'required|boolean',
            'nouveaute'=> 'required|boolean'
        ];

        $messages = [
            'name.required' => 'le nom est obligatoire.',
            'name.string' => 'le nom doit etre en string.',
            'name.max' => 'le nom ne peut pas depassé 255 characters.',
            'description.required' => 'La description est obligatoire.',
            'description.string' => 'La description doit etre en  string.',
            'statut.required' => 'le champ statut est obligatoire.',
            'statut.boolean' => 'statut doit etre boolean.',
            'nouveaute.required' => 'le champ nouveaute est obligatoire.',
            'nouveaute.boolean' => 'nouveaute doit etre boolean.',
            'prix.required' => 'le prix est obligatoire.',
            'prix.integer' => 'le prix doit etre en chiffre.',
            'prix.min' => 'prix doit etre au moins 0.',
            'quantity.required' => 'le champ quantité est obligatoire.',
            'quantity.integer' => 'le champ quantité doit etre un nombre.',
            'quantity.min' => 'The quantity must be at least 0.',
            'categorie_id.required' => 'l\'id du categorie est obligatoire.',
            'categorie_id.exists' => 'ce categorie est introuvable.',
            'sub_categorie_id.exists' => 'ce souscategorie est introuvable.',
        ];
        return Validator::make($data, $rules, $messages);
    }


    public function categories()
    {
        return $this->belongsTo(Categorie::class,'categorie_id');
    }


    public function subcategories()
    {
        return $this->belongsTo(SubCategorie::class,'sub_categorie_id');
    }

    public function images()
    {
        return $this->hasMany(ProduitImage::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function promos ()
    {
        $this->hasMany(PromoProduit::class);
    }

    public function favoritedByUsers()
{
    return $this->belongsToMany(User::class, 'favorites', 'produit_id', 'user_id')->withTimestamps();
}


public function items()
{
    return $this->hasMany(OrderItem::class);
}

public function premiereImage()
{
    return $this->hasOne(ProduitImage::class)->orderBy('id', 'asc');
}

}
