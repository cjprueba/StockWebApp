<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class SubCategoria extends Model
{

    /*  --------------------------------------------------------------------------------- */

    // INICIAR VARIABLES GLOBALES 

    protected $connection = 'retail';
    protected $table = 'SUBLINEAS';

    /*  --------------------------------------------------------------------------------- */

    public static function obtener_subCategorias($categoria)
    {
        
    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER TODAS LAS SUB CATEGORIAS

    	$subCategorias = SubCategoria::select(DB::raw('CODIGO, DESCRIPCION'))
        ->leftjoin('LINEAS_TIENE_SUBLINEAS', 'LINEAS_TIENE_SUBLINEAS.FK_COD_SUBLINEA', '=', 'SUBLINEAS.CODIGO')
        ->where('LINEAS_TIENE_SUBLINEAS.FK_COD_LINEA', '=', $categoria)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($subCategorias) {
        	return ['subCategorias' => $subCategorias];
        } else {
        	return ['subCategorias' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
}
