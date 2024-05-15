<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'produit_id',
        'quantity',
        'subTotal'
    ];

    public static function validatedOrderItem($data)
    {
        $rules = [
            'order_id' => 'required',
            'produit_id' => 'required|exists:produits,id',
            'subTotal' => 'required|integer|min:0',
            'quantity' => 'required|integer|min:0',
        ];
    
        $messages = [
            'order_id.required' => 'Le champ order_id est obligatoire.',
            'produit_id.required' => 'Le champ produit_id est obligatoire.',
            'subTotal.required' => 'Le champ "sub total" est obligatoire.',
            'subTotal.integer' => 'Le champ "sub total" doit être un entier.',
            'subTotal.min' => 'Le champ "sub total" doit être d\'au moins :min.',
            'quantity.required' => 'Le champ quantité est obligatoire.',
            'quantity.integer' => 'Le champ quantité doit être un entier.',
            'quantity.min' => 'Le champ quantité doit être d\'au moins :min.',
        ];
    
        return Validator::make($data, $rules, $messages);
    }
    

    public function orders()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    public function produits()
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }
}
