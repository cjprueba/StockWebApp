<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{

    protected $connection = 'retail';
    protected $table = 'marca';

    public static function obtener_marcas($categoria)
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER TODAS LAS CATEGORIAS
        
    	$marca = Marca::select(DB::raw('CODIGO, DESCRIPCION'))
        ->leftjoin('LINEAS_TIENE_MARCAS', 'LINEAS_TIENE_MARCAS.FK_COD_MARCA', '=', 'MARCA.CODIGO')
        ->where('LINEAS_TIENE_MARCAS.FK_COD_LINEA_LINEAS_TIENE_MARCAS', '=', $categoria)
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
