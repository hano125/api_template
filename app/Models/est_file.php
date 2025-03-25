<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class est_file extends Model
{
    protected $table = 'est_files';
    protected $fillable = ['main_id', 'vacancy_file', 'majles_file', 'malia_file', 'file_back'];
}
