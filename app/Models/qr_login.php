<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class qr_login extends Model
{
     public $incrementing = false;
     protected $keyType = 'string';
     protected $primaryKey = 'id';
     protected $fillable = ['id','user_id','status', 'expires_at'];
}
