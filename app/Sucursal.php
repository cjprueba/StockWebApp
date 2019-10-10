<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
     public static function mostrarSucursal($data)
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER TODAS LAS SUCURSALES

    	$sucursales = DB::connection('retail')
        ->table('sucursales')
        ->select(DB::raw('CODIGO, DESCRIPCION, RAZON_SOCIAL, DIRECCION, RUC'))
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($sucursales) {
        	return ['sucursales' => $sucursales];
        } else {
        	return ['sucursales' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function encontrarSucursal($data)
    {

    	/*  --------------------------------------------------------------------------------- */
    	   
    	// OBTENER TODAS LAS SUCURSALES

    	$sucursal = DB::connection('retail')
        ->table('sucursales')
        ->select(DB::raw('CODIGO, DESCRIPCION, RAZON_SOCIAL, DIRECCION, RUC'))
        ->where('CODIGO', '=', $data['codigoOrigen'])
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
        
        if (count($sucursal) > 0) {
            return ['sucursal' => $sucursal];
        } else {
            return ['sucursal' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
}
