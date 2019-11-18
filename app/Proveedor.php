<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{

    protected $connection = 'retail';
    protected $table = 'proveedores';

     public static function obtener_proveedores()
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER TODOS LOS PROVEEDORES

    	$proveedores = Proveedor::select(DB::raw('CODIGO, NOMBRE AS DESCRIPCION'))
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($proveedores) {
        	return ['proveedores' => $proveedores];
        } else {
        	return ['proveedores' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
}
