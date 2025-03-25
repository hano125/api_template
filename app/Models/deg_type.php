<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class deg_type extends Model
{
    protected $table = 'deg_types';
    protected $fillable = ["name", 'flag'];

    public function degAddresses()
    {
        return $this->hasMany(deg_addresse::class, 'deg_id');
    }
}
