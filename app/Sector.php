<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
	protected $connection = 'retail';
    protected $table = 'sectores';
    public $timestamps = false;
    //
}
