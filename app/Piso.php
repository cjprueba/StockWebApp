<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Piso extends Model
{
	protected $connection = 'retail';
    protected $table = 'pisos';
    public $timestamps = false;
    //
}
