<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


/**
 * @OA\Schema(
 *     schema="Address",
 *     title="Address",
 *     required={"first_name", "last_name", "addresse", "telephone1", "zone"},
 *                 @OA\Property(property="id", type="integer", example="1"),
 *                 @OA\Property(property="first_name", type="string", example="test"),
 *                 @OA\Property(property="last_name", type="string", example="test"),
 *                 @OA\Property(property="zone", type="string", example="Dakar"),
 *                 @OA\Property(property="addresse", type="string", example="Hlm"),
 *                 @OA\Property(property="quartier", type="string", example="Hlm"),
 *                 @OA\Property(property="complement_addresse", type="string", example="centre ville"),
 *                 @OA\Property(property="telephone1", type="string", example="123456789"),
 *                 @OA\Property(property="telephone2", type="string", example="123456790"),
 *                 @OA\Property(property="created_at", type="string", example="2024-05-16T14:36:34.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", example="2024-05-16T14:36:34.000000Z")
 * )
 */
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


    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function  validatedAddresse($data, $addressID = null)
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
