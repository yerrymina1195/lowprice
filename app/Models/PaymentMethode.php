<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PaymentMethode extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'type',
        'image'
    ];

    public static function validatedPaymentMethode($data, $payment_methodesId = null)
    {
        $rules = [
            'name' => [
                'required',
                'max:50',
                Rule::unique('payment_methodes')->ignore($payment_methodesId),
            ],
            'type' => [
                'required',
                'max:50',
                Rule::unique('payment_methodes')->ignore($payment_methodesId),
            ],
            'image' => 'nullable|image|max:2048',
        ];

        $messages = [
            'name.required' => 'Le nom du payment est obligatoire',
            'name.max' => 'Le nom du payment ne doit pas dépasser :max caractères',
            'name.unique' => 'Le nom du payment doit être unique',
            'type.required' => 'Le type du payment est obligatoire',
            'type.max' => 'Le type du payment ne doit pas dépasser :max caractères',
            'type.unique' => 'Le type du payment doit être unique',
            'image.image' => 'Le fichier doit être une image',
            'image.max' => 'La taille de l\'image ne doit pas dépasser 2 Mo',
        ];

        return Validator::make($data, $rules, $messages);
    }


    public function orders()
{
    return $this->hasMany(Order::class);
}
}
