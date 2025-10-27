<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'scope',
        'start_date',
        'end_date',
        'status',
        'token'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function projectRoles()
    {
        return $this->hasMany(ProjectRole::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'project_user')
            ->withPivot('project_role_id')->withTimestamps();
    }
}