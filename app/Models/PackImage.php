<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'pack_id'
    ];

    public function packs()
    {
        return $this->belongsTo(Pack::class, 'pack_id');
    }
}
