<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    public static function comprobar_stock_producto($codigo, $cantidad)
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER LOS DATOS DEL USUARIO LOGUEADO 

    	$user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

    	$valor = false;

        /*  --------------------------------------------------------------------------------- */

        $stock = DB::connection('retail')
        ->table('lotes')
        ->select(DB::raw('SUM(CANTIDAD) AS CANTIDAD'))
        ->where('COD_PROD','=', $codigo)
        ->where('ID_SUCURSAL','=',$user->id_sucursal)
        ->groupBy('COD_PROD')
        ->get();

        /*  --------------------------------------------------------------------------------- */

        if ($cantidad <= $stock[0]->CANTIDAD) {
        	$valor = true;
        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return $valor;

        /*  --------------------------------------------------------------------------------- */

    }

    public static function restar_stock_producto($codigo, $cantidad)
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER LOS DATOS DEL USUARIO LOGUEADO 

    	$user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

    	$valor = false;

        /*  --------------------------------------------------------------------------------- */

        while ($cantidad > 0) {

        	$stock = DB::connection('retail')
	        ->table('lotes')
	        ->select(DB::raw('CANTIDAD, LOTE'))
	        ->where('COD_PROD','=', $codigo)
	        ->where('CANTIDAD','>', 0)
	        ->where('ID_SUCURSAL','=',$user->id_sucursal)
	        ->limit(1)
	        ->get();

	        /*  --------------------------------------------------------------------------------- */

	        // REVISAR SI CANTIDAD SUPERA A CANTIDAD LOTE 

	        if ($cantidad > $stock[0]->CANTIDAD) {

	        	/*  --------------------------------------------------------------------------------- */

	        	// PONER EN CERO E LOTE PORQUE SUPERO CANTIDAD 

	        	$stock = DB::connection('retail')
		        ->table('lotes')
		        ->where('COD_PROD','=', $codigo)
		        ->where('LOTE','=', $stock[0]->LOTE)
		        ->where('ID_SUCURSAL','=',$user->id_sucursal)
		        ->update(['CANTIDAD' => 0]);

		        /*  --------------------------------------------------------------------------------- */

		        // RESTAR A CANTIDAD LO QUE SE RESTO DE LOTE 

		        $cantidad = $cantidad - $stock[0]->CANTIDAD;

		        /*  --------------------------------------------------------------------------------- */

	        } else {

	        	/*  --------------------------------------------------------------------------------- */

	        	// RESTAR CANTIDAD DE LOTE 

	        	$stock = DB::connection('retail')
		        ->table('lotes')
		        ->where('COD_PROD','=', $codigo)
		        ->where('LOTE','=', $stock[0]->LOTE)
		        ->where('ID_SUCURSAL','=',$user->id_sucursal)
		        ->decrement('CANTIDAD', $cantidad);

		        /*  --------------------------------------------------------------------------------- */

		        // CERAR CANTIDAD

		        $cantidad = 0;

		        /*  --------------------------------------------------------------------------------- */

	        }

	        /*  --------------------------------------------------------------------------------- */

        }
        

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return $stock;

        /*  --------------------------------------------------------------------------------- */

    }

    public static function restar_stock_producto_FK_CD($codigo, $cantidad)
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER LOS DATOS DEL USUARIO LOGUEADO 

    	$user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

    	$valor = false;
    	$lote = 0;
    	$cantidad_stock = 0;
    	$cantidad_a_restar = 0;

        /*  --------------------------------------------------------------------------------- */

        $stock = DB::connection('retail')
	    ->table('lotes')
	    ->select(DB::raw('CANTIDAD, LOTE'))
	    ->where('COD_PROD','=', $codigo)
	    ->where('CANTIDAD','>', 0)
	    ->where('ID_SUCURSAL','=',$user->id_sucursal)
	    ->limit(1)
	    ->get();

	    /*  --------------------------------------------------------------------------------- */

	    // CARGAR LOTE Y CANTIDAD

	    $lote = $stock[0]->LOTE;
	    $cantidad_stock = $stock[0]->CANTIDAD;
	    $cantidad_a_restar = $cantidad;
	    
	    /*  --------------------------------------------------------------------------------- */

	    // REVISAR SI CANTIDAD SUPERA A CANTIDAD LOTE 

	    if ($cantidad_a_restar > $cantidad_stock) {

	    	/*  --------------------------------------------------------------------------------- */

	     	// PONER EN CERO E LOTE PORQUE SUPERO CANTIDAD 

	        $stock = DB::connection('retail')
		    ->table('lotes')
		    ->where('COD_PROD','=', $codigo)
		    ->where('LOTE','=', $stock[0]->LOTE)
		    ->where('ID_SUCURSAL','=',$user->id_sucursal)
		    ->update(['CANTIDAD' => 0]);

		    /*  --------------------------------------------------------------------------------- */

		    // RESTAR A CANTIDAD LO QUE SE RESTO DE LOTE 

		    $cantidad_a_restar = $cantidad_a_restar - $cantidad_stock;

		    /*  --------------------------------------------------------------------------------- */

	        } else {

	        /*  --------------------------------------------------------------------------------- */

	        // RESTAR CANTIDAD DE LOTE 

	        $stock = DB::connection('retail')
		    ->table('lotes')
		    ->where('COD_PROD','=', $codigo)
		    ->where('LOTE','=', $stock[0]->LOTE)
		    ->where('ID_SUCURSAL','=',$user->id_sucursal)
		    ->decrement('CANTIDAD', $cantidad);

		    /*  --------------------------------------------------------------------------------- */

		    // CERAR CANTIDAD

		    $cantidad_a_restar = 0;

		    /*  --------------------------------------------------------------------------------- */

	    }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return ['cantidad' => $cantidad_a_restar, 'lote' => $lote];

        /*  --------------------------------------------------------------------------------- */

    }

}
