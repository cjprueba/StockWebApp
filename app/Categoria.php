<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{

    protected $connection = 'retail';
    protected $table = 'lineas';

     public static function obtener_categorias()
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER TODAS LAS CATEGORIAS

    	$categorias = Categoria::select(DB::raw('CODIGO, DESCRIPCION, ATRIBTELA, ATRIBCOLOR, ATRIBTALLE, ATRIBGENERO, ATRIBMARCA'))
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($categorias) {
        	return ['categorias' => $categorias];
        } else {
        	return ['categorias' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
}
