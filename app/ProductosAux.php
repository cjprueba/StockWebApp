<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ProductosAux extends Model
{
    //

    protected $connection = 'retail';
	protected $table = 'productos_aux';
	
}
