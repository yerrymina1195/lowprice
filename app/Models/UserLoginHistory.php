<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLoginHistory extends Model
{
    use HasFactory;



    public $table = 'user_login_histories';

    public $fillable = [
        'user_id',
        'login_at',
        'logout_at',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
        'ip_address' => 'string',
        'user_agent' => 'string'
    ];

    public static array $rules = [
        'user_id' => 'required',
        'login_at' => 'nullable',
        'logout_at' => 'nullable',
        'ip_address' => 'required|string|max:255',
        'user_agent' => 'nullable|string|max:65535',
        'created_at' => 'nullable',
        'updated_at' => 'nullable'
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
