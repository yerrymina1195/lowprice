<?php
namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'date_of_birth',
        'telephone', 
    ];
    const role_Admin = 'Admin';
    const role_User = 'User';

    const Roles = [
        self::role_Admin => 'Admin',
        self::role_User => 'User',
    ];


    public function isAdmin()
    {
        return $this->role === self::role_Admin;
    }
    public function isUser()
    {
        return $this->role === self::role_User;
    }
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }    

    public static function validateRegistration($data)
    {
        $rules = [
            'first_name' => 'required|string|between:2,100',
            'last_name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'date_of_birth' => 'required|date',
            'telephone' => 'required|string|unique:users',
        ];

        $messages = [
            'first_name.required' => 'Le prénom est obligatoire',
            'last_name.required' => 'Le nom de famille est obligatoire',
            'email.required' => 'L\'email est obligatoire',
            'password.required' => 'Le mot de passe est obligatoire',
            'date_of_birth.required' => 'La date de naissance est obligatoire',
            'telephone.required' => 'Le numéro de téléphone est obligatoire',
            'telephone.unique' => 'Le numéro de téléphone est déjà utilisé.'
        ];

        return Validator::make($data, $rules, $messages);
    }
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function favoriteProducts()
{
    return $this->belongsToMany(Produit::class, 'favorites', 'user_id', 'produit_id')->withTimestamps();
}


public function orders()
{
    return $this->hasMany(Order::class);
}

}