<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PedidoDet extends Model
{
    protected $connection = 'retail';
	protected $table = 'pedidos_det';
    public $timestamps = false;
}
