<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Address extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'first_name',
        'last_name',
        'addresse',
        'complement_addresse',
        'quartier',
        'zone',
        'telephone1',
        'telephone2',
        'user_id'
    ];


    public function users ()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public static function  validatedAddresse( $data, $addressID = null)
    {
        $rules = [
            'first_name' => 'required|string|between:2,100',
            'last_name' => 'required|string|between:2,100',
            'addresse' => 'required|string|max:100',
            'quartier' => 'nullable|string|max:100', 
            'zone' => 'required|string|max:100',
            'telephone1' => ['required', 'max:100', 'string', Rule::unique('addresses')->ignore($addressID)],
            'telephone2' => 'nullable|string|max:100',
            'complement_addresse' => 'nullable|string|max:100',
        ];
        $messages = [
            'fisrt_name.required' => 'Le prénom est obligatoire',
            'last_name.required' => 'Le nom de famille est obligatoire',
            'addresse.required' => 'L\'addresse est obligatoire',
            'zone.required' => 'La zone est obligatoire',
            'telephone1.required' => 'Le numéro de téléphone 1 est obligatoire',
            'telephone1.unique' => 'Le numéro de téléphone est déjà utilisé.'
        ];

        return Validator::make($data, $rules, $messages);

    }

    public function orders()
{
    return $this->hasMany(Order::class);
}
}
