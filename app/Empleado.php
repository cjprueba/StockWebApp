<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{   
    protected $connection = 'retail';

     public static function mostrarEmpleado($data)
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER TODOS LOS EMPLEADOS

    	$empleados = DB::connection('retail')
        ->table('empleados')
        ->select(DB::raw('CODIGO, NOMBRE, CI, DIRECCION, ID_SUCURSAL'))
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if (count($empleados) > 0) {
        	return ['empleados' => $empleados];
        } else {
        	return ['empleados' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function encontrarEmpleado($data)
    {

    	/*  --------------------------------------------------------------------------------- */
    	   
    	// OBTENER TODAS LAS SUCURSALES

    	$empleado = DB::connection('retail')
        ->table('empleados')
        ->select(DB::raw('ID, NOMBRE, CI, DIRECCION, ID_SUCURSAL'))
        ->where('ID', '=', $data['codigo'])
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
        
        if (count($empleado) > 0) {
            return ['empleado' => $empleado];
        } else {
            return ['empleado' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
}