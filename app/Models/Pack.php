<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Pack extends Model
{
    use HasFactory;

    protected $fillable= [
        'name',
        'description',
        'prix',
        'online',
    ];

    public static function validatedPack( $data, $packId = null)
    {
        $rules = [
            'name' => [
                'required','string',
                'max:50',
                Rule::unique('packs')->ignore($packId),
            ],
            'description'=> 'required|string|max:255',
            'prix'=> 'required|integer|min:0',
            'online'=> 'required|boolean'
        ];

        $messages = [
            'name.required' => 'le nom est obligatoire.',
            'name.string' => 'le nom doit etre en string.',
            'name.max' => 'le nom ne peut pas depassé 255 characters.',
            'description.required' => 'La description est obligatoire.',
            'description.string' => 'La description doit etre en  string.',
            'prix.required' => 'le prix est obligatoire.',
            'prix.integer' => 'le prix doit etre en chiffre.',
            'prix.min' => 'prix doit etre au moins 0.',
            'online.required'=>'le pack doit avoir un bouton online'
        ];
        return Validator::make($data, $rules, $messages);
    }

    public function images()
    {
        return $this->hasMany(PackImage::class);
    }
}
