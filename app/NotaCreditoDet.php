<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotaCreditoDet extends Model
{

    protected $connection = 'retail';
	protected $table = 'nota_credito_det';
    public $timestamps = false;
}
