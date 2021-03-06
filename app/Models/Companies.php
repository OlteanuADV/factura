<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Companies extends Model
{
    use HasFactory;

    //get clients
    public function clients()
    {
        return $this->hasMany('App\Models\Clients', 'company_id');
    }
}
