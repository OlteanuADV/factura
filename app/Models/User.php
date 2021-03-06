<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable; //HasApiTokens, 

    public $table = 'users';

    protected $primaryKey = 'id';


    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public $timestamps = true;

    public function companies() {
        return $this->hasMany('App\Models\Companies', 'owner');
    }

    public function company() {
        return $this->hasOne('App\Models\Companies', 'id', 'last_company');
    }

    public function getCompaniesIdsAttribute()
    {
        return $this->companies()->pluck('id')->toArray();
    }
}
