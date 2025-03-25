<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class est_main extends Model
{
    protected $table = 'est_mains';
    protected $fillable = ['user_id', 'minstry', 'tshkeel', 'vacancy_deg_type', 'vacancy_deg_address', 'mkun', 'required_deg_type
    ', 'required_deg_address', 'certif', 'book_num', 'book_date', 'newly_deg_type', 'newly_deg_address', 'complate_flag', 'deflg', 'num_back', 'chek'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
