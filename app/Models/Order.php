<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Order extends Model
{
    use HasFactory;


    protected $fillable = [

        'user_id',
        'paymentmethode_Id',
        'methodelivraison_id',
        'addresse_id',
        'statut',
        'prixTotal',
        'ispaid',
        'orderidentify'
    ];


    public static function validatedOrder($data)
    {
        $rules = [
            'paymentmethode_Id' => 'required|exists:payment_methodes,id',
            'methodelivraison_id' => 'required|exists:livraisons,id',
            'addresse_id' => 'required|exists:addresses,id',
            'prixTotal' => 'required|integer|min:0',
            'ispaid' => 'boolean',
            'statut' => 'string',
            'orderidentify' => 'string'
        ];

        $messages = [
            'paymentmethode_Id.required' => 'Le champ "méthode de paiement" est obligatoire.',
            'paymentmethode_Id.exists' => 'La méthode de paiement sélectionnée est invalide.',
            'methodelivraison_id.required' => 'Le champ "méthode de livraison" est obligatoire.',
            'methodelivraison_id.exists' => 'La méthode de livraison sélectionnée est invalide.',
            'addresse_id.required' => 'Le champ "adresse" est obligatoire.',
            'addresse_id.exists' => 'L\'adresse sélectionnée est invalide.',
            'prixTotal.required' => 'Le champ "prix total" est obligatoire.',
            'prixTotal.integer' => 'Le champ "prix total" doit être un entier.',
            'prixTotal.min' => 'Le champ "prix total" doit être d\'au moins :min.',
            'ispaid.boolean' => 'Le champ "payé" doit être vrai ou faux.',
            'statut.string' => 'Le champ "statut" doit être une chaîne de caractères.',
            'orderidentify.string' => 'Le champ "orderidentify" doit être une chaîne de caractères.',
        ];

        return Validator::make($data, $rules, $messages);
    }


    public function users()
    {
        return  $this->belongsTo(User::class, 'user_id');
    }
    public function paymentmethodes()
    {
        return $this->belongsTo(PaymentMethode::class, 'paymentmethode_Id');
    }
    public function methodelivraisons()
    {
        return $this->belongsTo(Livraison::class, 'methodelivraison_id');
    }
    public function addresses()
    {
        return  $this->belongsTo(Address::class, 'addresse_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }


    public function calculerSommePrixPanier()
    {
        $sommePrixPanier = 0;

        foreach ($this->items as $item) {
            $sommePrixPanier += $item->produits->prix * $item->quantity;
        }

        return $sommePrixPanier;
    }
}
