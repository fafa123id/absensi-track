<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
    ];

    public function departements()
    {
        return $this->hasMany(Departement::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
    public function wifis(){
        return $this->hasMany(WifiCompany::class);
    }
}
