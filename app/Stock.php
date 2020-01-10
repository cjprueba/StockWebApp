<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{

	/*  --------------------------------------------------------------------------------- */

	// INICIAR VARIABLES GLOBALES 

	protected $connection = 'retail';
	protected $table = 'lotes';
	public $timestamps = false;

	/*  --------------------------------------------------------------------------------- */

    public static function comprobar_stock_producto($codigo, $cantidad)
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER LOS DATOS DEL USUARIO LOGUEADO 

    	$user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

    	$valor = false;

        /*  --------------------------------------------------------------------------------- */

        $stock = Stock::select(DB::raw('SUM(CANTIDAD) AS CANTIDAD'))
        ->where('COD_PROD','=', $codigo)
        ->where('ID_SUCURSAL','=',$user->id_sucursal)
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

    	$datos = [];
    	$valor = false;

        /*  --------------------------------------------------------------------------------- */

        while ($cantidad > 0) {

        	$stock = Stock::select(DB::raw('ID, CANTIDAD, LOTE'))
	        ->where('COD_PROD','=', $codigo)
	        ->where('CANTIDAD','>', 0)
	        ->where('ID_SUCURSAL','=',$user->id_sucursal)
	        ->limit(1)
	        ->get();

	        /*  --------------------------------------------------------------------------------- */

	        // REVISAR SI STOCK ENCONTRO LOTES TODAVIA CON CANTIDAD 
	        // SINO ENCONTRO MAS STOCK SE TIENE EL WHILE 

	        if (count($stock) <= 0) {
	        	break;
	        }

	        /*  --------------------------------------------------------------------------------- */

	        // REVISAR SI CANTIDAD SUPERA A CANTIDAD LOTE 

	        if ($cantidad > $stock[0]->CANTIDAD) {

	        	/*  --------------------------------------------------------------------------------- */

	        	// PONER EN CERO E LOTE PORQUE SUPERO CANTIDAD 

	        	$restar = Stock::where('COD_PROD','=', $codigo)
		        ->where('LOTE','=', $stock[0]->LOTE)
		        ->where('ID_SUCURSAL','=',$user->id_sucursal)
		        ->update(['CANTIDAD' => 0]);

		        /*  --------------------------------------------------------------------------------- */

		        // RESTAR A CANTIDAD LO QUE SE RESTO DE LOTE 

		        $cantidad = $cantidad - $stock[0]->CANTIDAD;

		        /*  --------------------------------------------------------------------------------- */

		        // CARGAR ARRAY 

		        $datos[] = array ("id" => $stock[0]->ID, "cantidad" => $stock[0]->CANTIDAD);

		        /*  --------------------------------------------------------------------------------- */

	        } else {

	        	/*  --------------------------------------------------------------------------------- */

	        	// RESTAR CANTIDAD DE LOTE 

	        	$restar =Stock::where('COD_PROD','=', $codigo)
		        ->where('LOTE','=', $stock[0]->LOTE)
		        ->where('ID_SUCURSAL','=',$user->id_sucursal)
		        ->decrement('CANTIDAD', $cantidad);

		        /*  --------------------------------------------------------------------------------- */

		        // CARGAR ARRAY 

		        $datos[] = array ("id" => $stock[0]->ID, "cantidad" => $cantidad);
		        
		        /*  --------------------------------------------------------------------------------- */

		        // CERAR CANTIDAD

		        $cantidad = 0;

		        /*  --------------------------------------------------------------------------------- */

	        }

	        /*  --------------------------------------------------------------------------------- */

        }
        

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 
        // SI LA CANTIDAD ES CERO SIGNIFICA QUE PUDO GUARDAR TODOS LOS LOTES 
        // SI LA CANTIDAD ES MAYOR A CERO SIGNIFICA QUE NO PUDO GUARDAR TODA LA CANTIDAD

        if ($cantidad === 0) {
        	return ["response" => true, "datos" => $datos];
        } else {
        	return ["response" => false, "datos" => $datos, "restante" => $cantidad];
        }

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

        $stock = Stock::select(DB::raw('CANTIDAD, LOTE'))
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

	        $stock = Stock::table('lotes')
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

	        $stock = Stock::where('COD_PROD','=', $codigo)
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

    public static function sumar_stock_producto($codigo, $cantidad, $lote)
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER LOS DATOS DEL USUARIO LOGUEADO 

    	$user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

    	$dia = date('Y-m-d');
    	$hora = date('H:i:s');

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LOTE NO ES NULO 

        if ($lote === NULL) {
            $lote = 0;
        }

        /*  --------------------------------------------------------------------------------- */

        if ($lote > 0) {

        	/*  --------------------------------------------------------------------------------- */

        	// SI CANTIDAD A SUMAR POSEE LOTE 

        	$stock = Stock::where('COD_PROD','=', $codigo)
	        ->where('LOTE','=', $lote)
	        ->where('ID_SUCURSAL','=',$user->id_sucursal)
	        ->limit(1)
	        ->increment('CANTIDAD', $cantidad);

	        /*  --------------------------------------------------------------------------------- */

        } else {

        	/*  --------------------------------------------------------------------------------- */

        	// CONSEGUIR LOTE A SUMAR 

        	$lote = Stock::select(DB::raw('LOTE'))
	        ->where('COD_PROD','=', $codigo)
	        ->where('ID_SUCURSAL','=',$user->id_sucursal)
	        ->orderBy('LOTE', 'desc')
	        ->limit(1)
	        ->get();

        	/*  --------------------------------------------------------------------------------- */

        	// REVISAR SI ENCUENTRA ULTIMO LOTE 

        	if (count($lote) > 0) 
        	{

	        	$lote = $lote[0]->LOTE;

	        	$stock = Stock::where('COD_PROD','=', $codigo)
		        ->where('LOTE','=', $lote)
		        ->where('ID_SUCURSAL','=',$user->id_sucursal)
		        ->limit(1)
		        ->increment('CANTIDAD', $cantidad);

	    	} else {

	    		/*  --------------------------------------------------------------------------------- */

	    		// INSERTAR NUEVO LOTE SI PRODUCTO NO POSEE NINGUN LOTE

	    		Stock::insert(
				    [
				    	'COD_PROD' => $codigo, 
				    	'CANTIDAD_INICIAL' => $cantidad,
				    	'CANTIDAD' => $cantidad,
				    	'COSTO' => 0,
				    	'LOTE' => 1,
				    	'USER' => $user->name,
				    	'FECALTAS' => $dia,
				    	'HORALTAS' => $hora,
				    	'ID_SUCURSAL' => $user->id_sucursal
				    ]
				);

	    		/*  --------------------------------------------------------------------------------- */
	    	}

	        /*  --------------------------------------------------------------------------------- */

        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return true;

        /*  --------------------------------------------------------------------------------- */

    }


    public static function sumar_stock_id_lote($id_lote, $cantidad)
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER LOS DATOS DEL USUARIO LOGUEADO 

    	$user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

    	$dia = date('Y-m-d');
    	$hora = date('H:i:s');

        /*  --------------------------------------------------------------------------------- */

        // SI CANTIDAD A SUMAR POSEE LOTE 

        $stock = Stock::where('ID','=', $id_lote)
        ->update(['FECMODIF' => date('Y-m-d'), 
        		  'CANTIDAD' => \DB::raw('CANTIDAD + '.$cantidad.''), 
        		  'FK_USER_MD' => $user->id]);

	    /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return true;

        /*  --------------------------------------------------------------------------------- */

    }


    public static function insetar_lote($codigo, $cantidad, $costo, $modo, $usere)
    {

    	/*  --------------------------------------------------------------------------------- */

    	// INICIAR VARIABLES 

    	$dia = date("Y-m-d");
        $hora = date("H:i:s");

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER LOS DATOS DEL USUARIO LOGUEADO 

    	$user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */

    	// CONSEGUIR LOTE 

    	$lote = Stock::select(DB::raw('LOTE'))
	    ->where('COD_PROD','=', $codigo)
	    ->where('ID_SUCURSAL','=',$user->id_sucursal)
	    ->orderBy('LOTE', 'desc')
	    ->limit(1)
	    ->get();

	    /*  --------------------------------------------------------------------------------- */

	    // CALCULAR NUMERO LOTE 

	    if (count($lote) > 0) {
	    	$lote = $lote[0]->LOTE + 1;
	    } else {
	    	$lote = 1;
	    }

    	/*  --------------------------------------------------------------------------------- */

		Stock::insert(
		[
			'COD_PROD' => $codigo, 
			'CANTIDAD_INICIAL' => $cantidad,
			'CANTIDAD' => $cantidad,
			'COSTO' => $costo,
			'LOTE' => $lote,
			'USER' => $user->name,
			'FECALTAS' => $dia,
			'HORALTAS' => $hora,
			'ID_SUCURSAL' => $user->id_sucursal,
			'MODO' => $modo,
			'USERE' => $usere
		]
		);

    	/*  --------------------------------------------------------------------------------- */

    	// RETORNAR VALOR 

    	return $lote;

    	/*  --------------------------------------------------------------------------------- */
    	
    }

    public static function obtener_lotes_con_cantidad($codigo)
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER LOS DATOS DEL USUARIO LOGUEADO 

    	$user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */

    	// INICIAR VARIABLES 

    	$data = [];

    	/*  --------------------------------------------------------------------------------- */

    	// CONSEGUIR LOTE 

    	$lote = Stock::select(DB::raw('LOTE, CANTIDAD_INICIAL, CANTIDAD, COSTO, SUBSTR(FECALTAS, 1,11) AS FECALTAS'))
	    ->where('COD_PROD','=', $codigo["codigo"])
	    ->where('ID_SUCURSAL','=',$user->id_sucursal)
	    ->get();

	    /*  --------------------------------------------------------------------------------- */

	    // RETORNAR VALOR  

	    if (count($lote) > 0) {
	    	return ["lote" => $lote];
	    } else {
	    	return ["lote" => 0];
	    }

    	/*  --------------------------------------------------------------------------------- */
    	
    }
}
