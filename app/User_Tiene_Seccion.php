<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class User_Tiene_Seccion extends Model
{
    protected $connection = 'retail';
    protected $table = 'users_tiene_seccion';
    public $timestamps = false;
}
