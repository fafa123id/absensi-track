<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UniqueIdentityQr extends Model
{
    protected $fillable = [
        'public_id',
        'unique_code',
        'user_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
