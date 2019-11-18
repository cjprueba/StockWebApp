<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{

    protected $connection = 'retail';
    protected $table = 'marca';

    public static function obtener_marcas()
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER TODAS LAS CATEGORIAS

    	$marca = Marca::select(DB::raw('CODIGO, DESCRIPCION'))
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($marca) {
        	return ['marcas' => $marca];
        } else {
        	return ['marcas' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
}
