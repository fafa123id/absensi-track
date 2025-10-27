<?php

// app/Models/UserPushDevice.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPushDevice extends Model
{
    protected $fillable = ['push_subscription_id','user_id','device_name','device_id','ip'];
}
