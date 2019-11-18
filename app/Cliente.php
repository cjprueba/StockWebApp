<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $connection = 'retail';
    protected $table = 'clientes';
}
