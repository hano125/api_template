<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class certificate extends Model
{
    protected $table = 'certificates';
    protected $fillable = ["certificate_name", 'certificate_flag'];
    public $timestamps = true;
}
