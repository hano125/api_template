<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class deg_addresse extends Model
{
    protected $table = 'deg_addresses';
    protected $fillable = ["deg_address_name", 'deg_id ', "flag"];
    public $timestamps = false;


    // public function degType()
    // {
    //     return $this->belongsTo(deg_type::class, 'deg_id');
    // }
}
