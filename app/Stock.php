<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Lotes_tiene_TransferenciaDet;
use Illuminate\Support\Facades\Log;
use App\LoteUser;
use App\VentasDetTieneLotes;
use App\Ventas_det;
use App\NotaCreditoTieneLotes;

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
    	$diaHora = date("Y-m-d H:i:s");
    	$hora = date("H:i:s");

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
		        ->update(['CANTIDAD' => 0, 'USERM' => $user->name, 'FECMODIF' => $diaHora, 'HORMODIF' => $hora]);

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
		        ->update(['CANTIDAD' => DB::raw('CANTIDAD - '.$cantidad), 'USERM' => $user->name, 'FECMODIF' => $diaHora, 'HORMODIF' => $hora]);

	        	// $restar =Stock::where('COD_PROD','=', $codigo)
		        // ->where('LOTE','=', $stock[0]->LOTE)
		        // ->where('ID_SUCURSAL','=',$user->id_sucursal)
		        // ->decrement('CANTIDAD', $cantidad);

		        /*  --------------------------------------------------------------------------------- */

		        // CARGAR ARRAY 

		        $datos[] = array ("id" => $stock[0]->ID, "cantidad" => $cantidad);
		        
		        /*  --------------------------------------------------------------------------------- */

		        // CERAR CANTIDAD

		        $cantidad = 0;

		        /*  --------------------------------------------------------------------------------- */

	        }

	        /*  --------------------------------------------------------------------------------- */

	        // INSERTAR REFERENCIA USER 

    		LoteUser::guardar_referencia($user->id, 2, $stock[0]->ID, $diaHora);

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

    public static function sumar_stock_producto_devolucion($codigo, $cantidad, $id_ventas_det)
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER LOS DATOS DEL USUARIO LOGUEADO 

    	$user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

    	$diaHora = date('Y-m-d H:i:s');
    	$dia = date('Y-m-d');
    	$hora = date('H:i:s');

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LOTE NO ES NULO 

        if ($id_ventas_det === NULL) {
            $id_ventas_det = 0;
        }

        /*  --------------------------------------------------------------------------------- */

        if ($id_ventas_det > 0) {

        	/*  --------------------------------------------------------------------------------- */

        	$lotes_a_sumar = VentasDetTieneLotes::select(DB::raw('ID_LOTE, CANTIDAD'))
	        ->where('ID_VENTAS_DET','=',$id_ventas_det)
	        ->get();

        	/*  --------------------------------------------------------------------------------- */

        	// SUMAR ID LOTES 

        	foreach ($lotes_a_sumar as $key => $value) {
        		
        		if ($value->CANTIDAD >= $cantidad) {

        			/*  --------------------------------------------------------------------------------- */

        			Stock::where('ID','=', $value->ID_LOTE)
			        ->increment('CANTIDAD', $cantidad);

			        $cantidad = 0;

			        LoteUser::guardar_referencia($user->id, 3, $value->ID_LOTE, $diaHora);

    				/*  --------------------------------------------------------------------------------- */

        		} else {

        			Stock::where('ID','=', $value->ID_LOTE)
			        ->increment('CANTIDAD', $value->CANTIDAD);

			        $cantidad = $cantidad - $value->CANTIDAD;

			        /*  --------------------------------------------------------------------------------- */

			        // INSERTAR REFERENCIA USER 

    				LoteUser::guardar_referencia($user->id, 3, $value->ID_LOTE, $diaHora);

    				/*  --------------------------------------------------------------------------------- */

        		}

        		if ($cantidad === 0) {
        			break;
        		}

        	}

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

		        /*  --------------------------------------------------------------------------------- */

		        // OBTENER ID 

		        $id_lote = Stock::select('ID')
		        ->where('COD_PROD','=', $codigo)
		        ->where('LOTE','=', $lote)
		        ->where('ID_SUCURSAL','=',$user->id_sucursal)
		        ->limit(1)
		        ->get();

		        /*  --------------------------------------------------------------------------------- */

		        // INSERTAR REFERENCIA USER 

	    		LoteUser::guardar_referencia($user->id, 3, $id_lote[0]['ID'], $diaHora);

	    		/*  --------------------------------------------------------------------------------- */

	    	} else {

	    		/*  --------------------------------------------------------------------------------- */

	    		// INSERTAR NUEVO LOTE SI PRODUCTO NO POSEE NINGUN LOTE

	    		$id_lote = Stock::insert(
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

	    		// INSERTAR REFERENCIA USER 

	    		LoteUser::guardar_referencia($user->id, 1, $id_lote, $diaHora);

	    		/*  --------------------------------------------------------------------------------- */

	    	}

	        /*  --------------------------------------------------------------------------------- */

        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return ["response" => true];

        /*  --------------------------------------------------------------------------------- */

    }

    public static function sumar_stock_producto_nota_credito($codigo, $cantidad, $id_ventas_det, $id_nota_credito)
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER LOS DATOS DEL USUARIO LOGUEADO 

    	$user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

    	$diaHora = date('Y-m-d H:i:s');
    	$dia = date('Y-m-d');
    	$hora = date('H:i:s');

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LOTE NO ES NULO 

        if ($id_ventas_det === NULL) {
            $id_ventas_det = 0;
        }

        /*  --------------------------------------------------------------------------------- */

        if ($id_ventas_det > 0) {

        	/*  --------------------------------------------------------------------------------- */

        	$lotes_a_sumar = VentasDetTieneLotes::select(DB::raw('ventasdet_tiene_lotes.ID_LOTE, ventasdet_tiene_lotes.CANTIDAD, IFNULL(nota_credito_tiene_lote.CANTIDAD,0) AS CANTIDAD_DEVUELTA'))
        	->leftJoin('nota_credito_tiene_lote', function($join){
                                $join->on('nota_credito_tiene_lote.FK_VENTA_DET', '=', 'ventasdet_tiene_lotes.ID_VENTAS_DET')
                                     ->on('nota_credito_tiene_lote.ID_LOTE', '=', 'ventasdet_tiene_lotes.ID_LOTE');
        	})
	        ->where('ID_VENTAS_DET','=',$id_ventas_det)
	        ->get();

        	/*  --------------------------------------------------------------------------------- */

        	// SUMAR ID LOTES 

        	foreach ($lotes_a_sumar as $key => $value) {
        		
        		/*  --------------------------------------------------------------------------------- */

        		// RESTAR LA CANTIDAD QUE YA SE DEVOLVIO 

        		$value->CANTIDAD = $value->CANTIDAD - $value->CANTIDAD_DEVUELTA;

        		/*  --------------------------------------------------------------------------------- */
        		
        		if ($value->CANTIDAD >= $cantidad) {

        			/*  --------------------------------------------------------------------------------- */

        			Stock::where('ID','=', $value->ID_LOTE)
			        ->increment('CANTIDAD', $cantidad);

			        /*  --------------------------------------------------------------------------------- */

			        LoteUser::guardar_referencia($user->id, 3, $value->ID_LOTE, $diaHora);

    				/*  --------------------------------------------------------------------------------- */

    				NotaCreditoTieneLotes::guardar_referencia(
			            [
			                'FK_VENTA_DET' => $id_ventas_det,
			                'FK_NOTA_CREDITO_DET' => $id_nota_credito,
			                'ID_LOTE' => $value->ID_LOTE,
			                'CANTIDAD' => $cantidad
			            ]
			        );

    				/*  --------------------------------------------------------------------------------- */

    				$cantidad = 0;

    				/*  --------------------------------------------------------------------------------- */

        		} else if($value->CANTIDAD > 0) {

        			/*  --------------------------------------------------------------------------------- */

        			Stock::where('ID','=', $value->ID_LOTE)
			        ->increment('CANTIDAD', $value->CANTIDAD);

			        /*  --------------------------------------------------------------------------------- */

			        NotaCreditoTieneLotes::guardar_referencia(
			            [
			                'FK_VENTA_DET' => $id_ventas_det,
			                'FK_NOTA_CREDITO_DET' => $id_nota_credito,
			                'ID_LOTE' => $value->ID_LOTE,
			                'CANTIDAD' => $value->CANTIDAD
			            ]
			        );

    				/*  --------------------------------------------------------------------------------- */

			        $cantidad = $cantidad - $value->CANTIDAD;

			        /*  --------------------------------------------------------------------------------- */

			        // INSERTAR REFERENCIA USER 

    				LoteUser::guardar_referencia($user->id, 3, $value->ID_LOTE, $diaHora);

    				/*  --------------------------------------------------------------------------------- */

        		}

        		if ($cantidad === 0) {
        			break;
        		}

        	}

        	/*  --------------------------------------------------------------------------------- */
        	

        } 

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return ["response" => true];

        /*  --------------------------------------------------------------------------------- */

    }
    
    public static function sumar_stock_producto($codigo, $cantidad, $lote)
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER LOS DATOS DEL USUARIO LOGUEADO 

    	$user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

    	$diaHora = date('Y-m-d H:i:s');
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

	        // OBTENER ID 

	        $id_lote = Stock::select('ID')
	        ->where('COD_PROD','=', $codigo)
	        ->where('LOTE','=', $lote)
	        ->where('ID_SUCURSAL','=',$user->id_sucursal)
	        ->limit(1)
	        ->get();

	        /*  --------------------------------------------------------------------------------- */

	        // INSERTAR REFERENCIA USER 

    		LoteUser::guardar_referencia($user->id, 2, $id_lote[0]['ID'], $diaHora);

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

		        /*  --------------------------------------------------------------------------------- */

		        // OBTENER ID 

		        $id_lote = Stock::select('ID')
		        ->where('COD_PROD','=', $codigo)
		        ->where('LOTE','=', $lote)
		        ->where('ID_SUCURSAL','=',$user->id_sucursal)
		        ->limit(1)
		        ->get();

		        /*  --------------------------------------------------------------------------------- */

		        // INSERTAR REFERENCIA USER 

	    		LoteUser::guardar_referencia($user->id, 2, $id_lote[0]['ID'], $diaHora);

	    		/*  --------------------------------------------------------------------------------- */

	    	} else {

	    		/*  --------------------------------------------------------------------------------- */

	    		// INSERTAR NUEVO LOTE SI PRODUCTO NO POSEE NINGUN LOTE

	    		$id_lote = Stock::insert(
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

	    		// INSERTAR REFERENCIA USER 

	    		LoteUser::guardar_referencia($user->id, 1, $id_lote, $diaHora);

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

    	$diaHora = date('Y-m-d H:i:s');
    	$dia = date('Y-m-d');
    	$hora = date('H:i:s');

        /*  --------------------------------------------------------------------------------- */

        // SI CANTIDAD A SUMAR POSEE LOTE 

        $stock = Stock::where('ID','=', $id_lote)
        ->update(['FECMODIF' => date('Y-m-d'), 
        		  'CANTIDAD' => \DB::raw('CANTIDAD + '.$cantidad.'')]);

	    /*  --------------------------------------------------------------------------------- */

	    LoteUser::guardar_referencia($user->id, 2, $id_lote, $diaHora);

	    /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return true;

        /*  --------------------------------------------------------------------------------- */

    }

    public static function restar_stock_id_lote($id_lote, $cantidad)
    {

    	try {

	    	/*  --------------------------------------------------------------------------------- */

	    	// OBTENER LOS DATOS DEL USUARIO LOGUEADO 

	    	$user = auth()->user();

	    	/*  --------------------------------------------------------------------------------- */

	        // INICIAR VARIABLES

	    	$diaHora = date('Y-m-d H:i:s');
	    	$dia = date('Y-m-d');
	    	$hora = date('H:i:s');

	        /*  --------------------------------------------------------------------------------- */

	        // SI CANTIDAD A SUMAR POSEE LOTE 

	        $stock = Stock::where('ID','=', $id_lote)
	        ->update(['FECMODIF' => date('Y-m-d'), 
	        		  'CANTIDAD' => \DB::raw('CANTIDAD - '.$cantidad.'')]);

	        /*  --------------------------------------------------------------------------------- */

	        // REFERENCIA USER 

	        LoteUser::guardar_referencia($user->id, 2, $id_lote, $diaHora);

		    /*  --------------------------------------------------------------------------------- */

		    Log::info('Lote Restar: Éxito al modificar.', ['ID LOTE' => $id_lote, 'CANTIDAD' => $cantidad]);

		    /*  --------------------------------------------------------------------------------- */

	        // RETORNAR VALOR 

	        return true;

	        /*  --------------------------------------------------------------------------------- */

        } catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Lote Restar: Error al modificar.', ['ID LOTE' => $id_lote, 'CANTIDAD' => $cantidad]);

			/*  --------------------------------------------------------------------------------- */

			// RETORNAR 

			return ["response" => false, "statusText" => "No se pudo modificar el lote"];

			/*  --------------------------------------------------------------------------------- */
		}
    }

    public static function insetar_lote($codigo, $cantidad, $costo, $modo, $usere, $vencimiento)
    {

    	/*  --------------------------------------------------------------------------------- */

    	// INICIAR VARIABLES 

    	$diaHora = date("Y-m-d H:i:s");
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

	    // REVISAR VENCIMIENTO 

	    if ($vencimiento === 'N/A') {
	    	$vencimiento = '0000-00-00';
	    }

    	/*  --------------------------------------------------------------------------------- */

    	// MODOS
        // MODO 1 - COMPRA
        // MODO 2 - TRANSFERENCIA 
    	// MODO 5 - INVENTARIO

		$id = Stock::insertGetId(
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
			'USERE' => $usere,
			'FECHA_VENC' => $vencimiento
		]
		);

    	/*  --------------------------------------------------------------------------------- */

    	// INSERTAR REFERENCIA USER 

    	LoteUser::guardar_referencia($user->id, 1, $id, $diaHora);

    	/*  --------------------------------------------------------------------------------- */

    	// RETORNAR VALOR 

    	return ["lote" => $lote, "id" => $id];

    	/*  --------------------------------------------------------------------------------- */
    	
    }

    public static function ultimo_lote($codigo)
    {
    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER USUARIO 

    	$user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER ULTIMO LOTE 

    	$lote = Stock::select(DB::raw('LOTE'))
	    ->where('COD_PROD','=', $codigo)
	    ->where('ID_SUCURSAL','=',$user->id_sucursal)
	    ->orderBy('LOTE', 'desc')
	    ->limit(1)
	    ->get();

	    /*  --------------------------------------------------------------------------------- */

	    if (count($lote) > 0) {
	    	return $lote[0]["LOTE"];
	    } else {
	    	return 0;
	    }
	    
	    /*  --------------------------------------------------------------------------------- */
	    
    }


    public static function eliminar_lote_por_id($id)
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER LOS DATOS DEL USUARIO LOGUEADO 

    	$user = auth()->user();
    	$diaHora = date("Y-m-d H:i:s");

    	/*  --------------------------------------------------------------------------------- */

        // SI CANTIDAD A SUMAR POSEE LOTE 

        Stock::where('ID','=', $id)
        ->delete();

	    /*  --------------------------------------------------------------------------------- */

	    // REFERENCIA USER 

	    LoteUser::guardar_referencia($user->id, 3, $id, $diaHora);

	    /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return true;

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

    	$lote = Stock::select(DB::raw('LOTE, CANTIDAD_INICIAL, CANTIDAD, COSTO, SUBSTR(FECALTAS, 1,11) AS FECALTAS, SUBSTR(FECHA_VENC, 1,11) AS VENCIMIENTO'))
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

    public static function vencidos($request)
    {
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'C', 
                            1 => 'ID',
                            2 => 'CODIGO',
                            3 => 'LOTE',
                            4 => 'VENCIMIENTO',
                            5 => 'IMAGEN',
                            6 => 'ACCION',
                            7 => 'ESTATUS'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $dia = date('Y-m-d');
        $dias_filtro = date('Y-m-d', strtotime($dia. ' + 30 days'));
        $dias_warning = date('Y-m-d', strtotime($dia. ' + 10 days'));
        $c = 0;

        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE PRODUCTOS VENCIDOS QUE PASAN EL TIEMPO DE VENCIMIENTO

        $totalData = Stock::
        			leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'LOTES.COD_PROD')
        			->where('LOTES.ID_SUCURSAL','=', $user->id_sucursal)
        			->where('LOTES.CANTIDAD','>', 0)
        			->where('PRODUCTOS.VENCIMIENTO','=', 1)
        			->where('LOTES.FECHA_VENC','<=', $dias_filtro)
                    ->count();  
        
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $imagen_producto = '';

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE VALOR EN VARIABLE SEARCH

        if(empty($request->input('search.value')))
        {            

            /*  ************************************************************ */

            //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

            $posts = Stock::select(DB::raw('0 AS C, LOTES.COD_PROD, LOTES.LOTE, LOTES.FECHA_VENC, PRODUCTOS.DESCRIPCION, LOTES.CANTIDAD'))
        			->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'LOTES.COD_PROD')
        			->where('LOTES.CANTIDAD','>', 0)
        			->where('LOTES.ID_SUCURSAL','=', $user->id_sucursal)
        			->where('PRODUCTOS.VENCIMIENTO','=', 1)
        			->where('LOTES.FECHA_VENC','<=', $dias_filtro)
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();

            /*  ************************************************************ */

        } else {

            /*  ************************************************************ */

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            /*  ************************************************************ */

            // CARGAR LOS PRODUCTOS FILTRADOS EN DATATABLE

            $posts =  Stock::select(DB::raw('0 AS C, LOTES.COD_PROD, LOTES.LOTE, LOTES.FECHA_VENC, PRODUCTOS.DESCRIPCION, LOTES.CANTIDAD'))
        			->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'LOTES.COD_PROD')
        			->where('LOTES.ID_SUCURSAL','=', $user->id_sucursal)
        			->where('PRODUCTOS.VENCIMIENTO','=', 1)
        			->where('LOTES.CANTIDAD','>', 0)
        			->where('LOTES.FECHA_VENC','<=', $dias_filtro)
                            ->where(function ($query) use ($search) {
                                $query->where('LOTES.COD_PROD','LIKE',"%{$search}%")
                                      ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = Stock::
		        			leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'LOTES.COD_PROD')
		        			->where('LOTES.ID_SUCURSAL','=', $user->id_sucursal)
		        			->where('PRODUCTOS.VENCIMIENTO','=', 1)
		        			->where('LOTES.CANTIDAD','>', 0)
		        			->where('LOTES.FECHA_VENC','<=', $dias_filtro)  
                             ->where(function ($query) use ($search) {
                                $query->where('LOTES.COD_PROD','LIKE',"%{$search}%")
                                      ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"%{$search}%");
                             })
                             ->count();

            /*  ************************************************************ */  

        }

        $data = array();

        /*  --------------------------------------------------------------------------------- */

        // CONVERT IMAGE DEFAULT TO BLOB 

        $path = '../storage/app/imagenes/product.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $dataDefaultImage = file_get_contents($path);

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts))
        {
            foreach ($posts as $post)
            {

                /*  --------------------------------------------------------------------------------- */

                // BUSCAR IMAGEN

                $imagen = Imagen::select(DB::raw('PICTURE'))
                ->where('COD_PROD','=', $post->COD_PROD)
                ->get();
                
                /*  --------------------------------------------------------------------------------- */

                // CARGAR EN LA VARIABLE 

                $c = $c + 1;

                $nestedData['C'] = $c;
                $nestedData['CODIGO'] = $post->COD_PROD;
                $nestedData['DESCRIPCION'] = substr($post->DESCRIPCION, 0, 20).'...';
                $nestedData['LOTE'] = $post->LOTE;
                $nestedData['CANTIDAD'] = $post->CANTIDAD;
                $nestedData['VENCIMIENTO'] = substr($post->FECHA_VENC, 0, 11);


                
                
                $nestedData['ESTATUS'] = '<span class="badge badge-info">Cercano</span>';

                if ($post->FECHA_VENC <= $dias_warning && $post->FECHA_VENC > $dia) {
                	$nestedData['ESTATUS'] = '<span class="badge badge-warning">Por Vencer</span>';
                }

                if ($post->FECHA_VENC <= $dia) {
                	$nestedData['ESTATUS'] = '<span class="badge badge-danger">Vencido</span>';
                } 

                $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarDetalle' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a>";

                /*  --------------------------------------------------------------------------------- */

                // SI NO HAY IMAGEN CARGAR IMAGEN DEFAULT 
                
                if (count($imagen) > 0) {
                   foreach ($imagen as $key => $image) {
                        $imagen_producto = $image->PICTURE;
                    }
                } else {
                    $imagen_producto = $dataDefaultImage;
                }

                /*  --------------------------------------------------------------------------------- */

                $nestedData['IMAGEN'] = "<img src='data:image/jpg;base64,".base64_encode($imagen_producto)."' class='img-thumbnail' style='width:30px;height:30px;'>";

                /*  --------------------------------------------------------------------------------- */

                
                $data[] = $nestedData;

            }
        }
        
        /*  --------------------------------------------------------------------------------- */

        // PREPARAR EL ARRAY A ENVIAR 

        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );
        
        /*  --------------------------------------------------------------------------------- */

        // CONVERTIR EN JSON EL ARRAY Y ENVIAR 

        return $json_data; 

        /*  --------------------------------------------------------------------------------- */
    }

    public static function comprobar_si_hay_stock_producto($codigo)
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

        if ($stock[0]->CANTIDAD > 0) {
        	$valor = true;
        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return $valor;

        /*  --------------------------------------------------------------------------------- */

    }

    public static function terminados($request)
    {
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'COD_PROD',
                            1 => 'DESCRIPCION',
                            2 => 'STOCK',
                            3 => 'IMAGEN',
                            4 => 'ACCION'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $dia = date('Y-m-d');
        //$dia = '2020-07-06';

        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE PRODUCTOS VENCIDOS QUE PASAN EL TIEMPO DE VENCIMIENTO

        // $lotes_modificados = Ventas_det::
        // 			select(DB::raw('VENTASDET.COD_PROD, VENTASDET.ID_SUCURSAL'))
        // 			->whereRaw('(IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = VENTASDET.COD_PROD) AND (l.ID_SUCURSAL = VENTASDET.ID_SUCURSAL))),0)) = 0')
        // 			->where('VENTASDET.FECALTAS','=', '2020-05-22')
        // 			->where('VENTASDET.ID_SUCURSAL','=', $user->id_sucursal)
        // 			->groupBy('VENTASDET.COD_PROD')
        //             ->get();

        // $lotes_modificados = Stock::
        // 			select(DB::raw('LOTES.COD_PROD, LOTES.ID_SUCURSAL, SUM(LOTES.CANTIDAD) AS STOCK'))
        // 			->leftjoin('LOTES_USER', 'LOTES.ID', '=', 'LOTES_USER.FK_LOTE')
        // 			//->whereRaw('(IFNULL((SUM(LOTES.CANTIDAD)),0)) = 0')
        // 			->whereRaw('(IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = LOTES.COD_PROD) AND (l.ID_SUCURSAL = LOTES.ID_SUCURSAL))),0)) = 0')
        // 			//->havingRaw('(IFNULL((SUM(LOTES.CANTIDAD)),0)) = ?', ["0"])
        // 			->whereRaw('SUBSTR(LOTES_USER.FECHA, 1,11) = "2020-07-06"')
        // 			->where('LOTES.ID_SUCURSAL','=', $user->id_sucursal)
        // 			->groupBy('LOTES.COD_PROD')
        //             ->get();
                                


        // $totalData = Stock::
        // 			leftjoin('LOTES_USER', 'LOTES.ID', '=', 'LOTES_USER.FK_LOTE')
        // 			->whereRaw('(IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = LOTES.COD_PROD) AND (l.ID_SUCURSAL = LOTES.ID_SUCURSAL))),0)) = 0')
        // 			->whereRaw('SUBSTR(LOTES_USER.FECHA, 1,11) = "'.$dia.'"')
        // 			->where('LOTES.ID_SUCURSAL','=', $user->id_sucursal)
        // 			//->groupBy('LOTES.COD_PROD')
        //             ->count();  
        //var_dump($totalData);

                     $totalData = Stock::
        	leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'LOTES.COD_PROD')
        	->leftjoin('LOTES_USER', 'LOTES.ID', '=', 'LOTES_USER.FK_LOTE')
        	->whereRaw('(IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = LOTES.COD_PROD) AND (l.ID_SUCURSAL = LOTES.ID_SUCURSAL))),0)) = 0')
        	->whereRaw('SUBSTR(LOTES_USER.FECHA, 1,11) = "'.$dia.'"')
        	->where('LOTES.ID_SUCURSAL','=', $user->id_sucursal)
        	->groupBy('LOTES.COD_PROD')
        	->get();  

        	$totalData = count($totalData);
        //var_dump($totalData);
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $imagen_producto = '';

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE VALOR EN VARIABLE SEARCH

        if(empty($request->input('search.value')))
        {            

            /*  ************************************************************ */

            //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

            $posts = Stock::
        	select(DB::raw('LOTES.COD_PROD, PRODUCTOS.DESCRIPCION, SUM(LOTES.CANTIDAD) AS STOCK'))
        	->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'LOTES.COD_PROD')
        	->leftjoin('LOTES_USER', 'LOTES.ID', '=', 'LOTES_USER.FK_LOTE')
        	->whereRaw('(IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = LOTES.COD_PROD) AND (l.ID_SUCURSAL = LOTES.ID_SUCURSAL))),0)) = 0')
        	->whereRaw('SUBSTR(LOTES_USER.FECHA, 1,11) = "'.$dia.'"')
        	->where('LOTES.ID_SUCURSAL','=', $user->id_sucursal)
        	->groupBy('LOTES.COD_PROD')
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();

            /*  ************************************************************ */

        } else {

            /*  ************************************************************ */

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            /*  ************************************************************ */

            // CARGAR LOS PRODUCTOS FILTRADOS EN DATATABLE

            $posts =  Stock::
        	select(DB::raw('LOTES.COD_PROD, PRODUCTOS.DESCRIPCION, SUM(LOTES.CANTIDAD) AS STOCK'))
        	->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'LOTES.COD_PROD')
        	->leftjoin('LOTES_USER', 'LOTES.ID', '=', 'LOTES_USER.FK_LOTE')
        	->whereRaw('(IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = LOTES.COD_PROD) AND (l.ID_SUCURSAL = LOTES.ID_SUCURSAL))),0)) = 0')
        	->whereRaw('SUBSTR(LOTES_USER.FECHA, 1,11) = "'.$dia.'"')
        	->where('LOTES.ID_SUCURSAL','=', $user->id_sucursal)
        	->groupBy('LOTES.COD_PROD')
                            ->where(function ($query) use ($search) {
                                $query->where('LOTES.COD_PROD','LIKE',"%{$search}%")
                                      ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = Stock::
		        			leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'LOTES.COD_PROD')
        	->leftjoin('LOTES_USER', 'LOTES.ID', '=', 'LOTES_USER.FK_LOTE')
        	->whereRaw('(IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = LOTES.COD_PROD) AND (l.ID_SUCURSAL = LOTES.ID_SUCURSAL))),0)) = 0')
        	->whereRaw('SUBSTR(LOTES_USER.FECHA, 1,11) = "'.$dia.'"')
        	->where('LOTES.ID_SUCURSAL','=', $user->id_sucursal)
        	->groupBy('LOTES.COD_PROD')
                            ->where(function ($query) use ($search) {
                                $query->where('LOTES.COD_PROD','LIKE',"%{$search}%")
                                      ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                             ->count();

            /*  ************************************************************ */  

        }

        $data = array();

        /*  --------------------------------------------------------------------------------- */

        // CONVERT IMAGE DEFAULT TO BLOB 

        $path = '../storage/app/imagenes/product.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $dataDefaultImage = file_get_contents($path);

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts))
        {
            foreach ($posts as $post)
            {

                /*  --------------------------------------------------------------------------------- */

                // BUSCAR IMAGEN

                $imagen = Imagen::select(DB::raw('PICTURE'))
                ->where('COD_PROD','=', $post->COD_PROD)
                ->get();
                
                /*  --------------------------------------------------------------------------------- */

                // CARGAR EN LA VARIABLE 

                $nestedData['COD_PROD'] = $post->COD_PROD;
                $nestedData['DESCRIPCION'] = substr($post->DESCRIPCION, 0, 20).'...';
                $nestedData['STOCK'] = $post->STOCK;


                
                
                // $nestedData['ESTATUS'] = '<span class="badge badge-info">Cercano</span>';

                // if ($post->FECHA_VENC <= $dias_warning && $post->FECHA_VENC > $dia) {
                // 	$nestedData['ESTATUS'] = '<span class="badge badge-warning">Por Vencer</span>';
                // }

                // if ($post->FECHA_VENC <= $dia) {
                // 	$nestedData['ESTATUS'] = '<span class="badge badge-danger">Vencido</span>';
                // } 

                $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarDetalle' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a>";

                /*  --------------------------------------------------------------------------------- */

                // SI NO HAY IMAGEN CARGAR IMAGEN DEFAULT 
                
                if (count($imagen) > 0) {
                   foreach ($imagen as $key => $image) {
                        $imagen_producto = $image->PICTURE;
                    }
                } else {
                    $imagen_producto = $dataDefaultImage;
                }

                /*  --------------------------------------------------------------------------------- */

                $nestedData['IMAGEN'] = "<img src='data:image/jpg;base64,".base64_encode($imagen_producto)."' class='img-thumbnail' style='width:30px;height:30px;'>";

                /*  --------------------------------------------------------------------------------- */

                
                $data[] = $nestedData;

            }
        }
        
        /*  --------------------------------------------------------------------------------- */

        // PREPARAR EL ARRAY A ENVIAR 

        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );
        
        /*  --------------------------------------------------------------------------------- */

        // CONVERTIR EN JSON EL ARRAY Y ENVIAR 

        return $json_data; 

        /*  --------------------------------------------------------------------------------- */
    }

    public static function terminados_reporte($request)
    {
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'COD_PROD',
                            1 => 'DESCRIPCION',
                            2 => 'STOCK',
                            3 => 'IMAGEN',
                            4 => 'ACCION'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $dia = date('Y-m-d');
        $sucursal = $request->input('sucursal');
        $inicio = date('Y-m-d', strtotime($request->input('inicio')));
        $fin =  date('Y-m-d', strtotime($request->input('fin')));

        /*  --------------------------------------------------------------------------------- */

        $totalData = Stock::
        leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'LOTES.COD_PROD')
        ->leftjoin(DB::raw('(SELECT  FK_LOTE, MAX(FECHA) AS FECHA 
            FROM    lotes_user
            GROUP   BY FK_LOTE)
	               b'), 
	        function($join)
	        {
	           $join->on('LOTES.ID', '=', 'b.FK_LOTE');
	        })
        ->whereRaw('(IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = LOTES.COD_PROD) AND (l.ID_SUCURSAL = LOTES.ID_SUCURSAL))),0)) = 0')
        ->whereRaw('SUBSTR(b.FECHA, 1,11) >= "'.$inicio.'" and SUBSTR(b.FECHA, 1,11) <= "'.$fin.'"')
        ->where('LOTES.ID_SUCURSAL','=', $sucursal)
        ->groupBy('LOTES.COD_PROD')
        ->get();  

        $totalData = count($totalData);

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $imagen_producto = '';

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE VALOR EN VARIABLE SEARCH

        if(empty($request->input('search.value')))
        {            

            /*  ************************************************************ */

            //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

            $posts = Stock::
        	select(DB::raw('LOTES.COD_PROD, PRODUCTOS.DESCRIPCION, SUM(LOTES.CANTIDAD) AS STOCK, b.FECHA'))
        	->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'LOTES.COD_PROD')
        	->leftjoin(DB::raw('(SELECT  FK_LOTE, MAX(FECHA) AS FECHA 
            FROM    lotes_user
            GROUP   BY FK_LOTE)
	               b'), 
	        function($join)
	        {
	           $join->on('LOTES.ID', '=', 'b.FK_LOTE');
	        })
        	->whereRaw('(IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = LOTES.COD_PROD) AND (l.ID_SUCURSAL = LOTES.ID_SUCURSAL))),0)) = 0')
        	->whereRaw('SUBSTR(b.FECHA, 1,11) >= "'.$inicio.'" and SUBSTR(b.FECHA, 1,11) <= "'.$fin.'"')
        	->where('LOTES.ID_SUCURSAL','=', $sucursal)
        	->groupBy('LOTES.COD_PROD')
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();

            /*  ************************************************************ */

        } else {

            /*  ************************************************************ */

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            /*  ************************************************************ */

            // CARGAR LOS PRODUCTOS FILTRADOS EN DATATABLE

            $posts =  Stock::
        	select(DB::raw('LOTES.COD_PROD, PRODUCTOS.DESCRIPCION, SUM(LOTES.CANTIDAD) AS STOCK'))
        	->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'LOTES.COD_PROD')
        	->leftjoin(DB::raw('(SELECT  FK_LOTE, MAX(FECHA) AS FECHA 
            FROM    lotes_user
            GROUP   BY FK_LOTE)
	               b'), 
	        function($join)
	        {
	           $join->on('LOTES.ID', '=', 'b.FK_LOTE');
	        })
        	->whereRaw('(IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = LOTES.COD_PROD) AND (l.ID_SUCURSAL = LOTES.ID_SUCURSAL))),0)) = 0')
        	->whereRaw('SUBSTR(b.FECHA, 1,11) >= "'.$inicio.'" and SUBSTR(b.FECHA, 1,11) <= "'.$fin.'"')
        	->where('LOTES.ID_SUCURSAL','=', $sucursal)
        	->groupBy('LOTES.COD_PROD')
                            ->where(function ($query) use ($search) {
                                $query->where('LOTES.COD_PROD','LIKE',"%{$search}%")
                                      ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = Stock::
		        			leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'LOTES.COD_PROD')
        	->leftjoin(DB::raw('(SELECT  FK_LOTE, MAX(FECHA) AS FECHA 
            FROM    lotes_user
            GROUP   BY FK_LOTE)
	               b'), 
	        function($join)
	        {
	           $join->on('LOTES.ID', '=', 'b.FK_LOTE');
	        })
        	->whereRaw('(IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = LOTES.COD_PROD) AND (l.ID_SUCURSAL = LOTES.ID_SUCURSAL))),0)) = 0')
        	->whereRaw('SUBSTR(b.FECHA, 1,11) >= "'.$inicio.'" and SUBSTR(b.FECHA, 1,11) <= "'.$fin.'"')
        	->where('LOTES.ID_SUCURSAL','=', $sucursal)
        	->groupBy('LOTES.COD_PROD')
                            ->where(function ($query) use ($search) {
                                $query->where('LOTES.COD_PROD','LIKE',"%{$search}%")
                                      ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                             ->count();

            /*  ************************************************************ */  

        }

        $data = array();

        /*  --------------------------------------------------------------------------------- */

        // CONVERT IMAGE DEFAULT TO BLOB 

        $path = '../storage/app/imagenes/product.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $dataDefaultImage = file_get_contents($path);

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts))
        {
            foreach ($posts as $post)
            {

                /*  --------------------------------------------------------------------------------- */

                // BUSCAR IMAGEN

                $imagen = Imagen::select(DB::raw('PICTURE'))
                ->where('COD_PROD','=', $post->COD_PROD)
                ->get();
                
                /*  --------------------------------------------------------------------------------- */

                // CARGAR EN LA VARIABLE 

                $nestedData['COD_PROD'] = $post->COD_PROD;
                $nestedData['DESCRIPCION'] = substr($post->DESCRIPCION, 0, 20).'...';
                $nestedData['STOCK'] = $post->STOCK;
                $nestedData['FECHA'] = $post->FECHA;
                $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarDetalle' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a>";

                /*  --------------------------------------------------------------------------------- */

                // SI NO HAY IMAGEN CARGAR IMAGEN DEFAULT 
                
                if (count($imagen) > 0) {
                   foreach ($imagen as $key => $image) {
                        $imagen_producto = $image->PICTURE;
                    }
                } else {
                    $imagen_producto = $dataDefaultImage;
                }

                /*  --------------------------------------------------------------------------------- */

                $nestedData['IMAGEN'] = "<img src='data:image/jpg;base64,".base64_encode($imagen_producto)."' class='img-thumbnail' style='width:30px;height:30px;'>";

                /*  --------------------------------------------------------------------------------- */

                
                $data[] = $nestedData;

            }
        }
        
        /*  --------------------------------------------------------------------------------- */

        // PREPARAR EL ARRAY A ENVIAR 

        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );
        
        /*  --------------------------------------------------------------------------------- */

        // CONVERTIR EN JSON EL ARRAY Y ENVIAR 

        return $json_data; 

        /*  --------------------------------------------------------------------------------- */
    }

    /*  --------------------------------------------------------------------------------- */

    public static function obtener_stock($codigo)
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER LOS DATOS DEL USUARIO LOGUEADO 

    	$user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        $stock = Stock::select(DB::raw('IFNULL(SUM(CANTIDAD), 0) AS CANTIDAD'))
        ->where('COD_PROD','=', $codigo)
        ->where('ID_SUCURSAL','=',$user->id_sucursal)
        ->groupBy('COD_PROD')
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // CANTIDAD 

        $stock = count($stock) > 0 ? $stock[0]->CANTIDAD : 0;

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return ["response" => true, "stock" => $stock];

        /*  --------------------------------------------------------------------------------- */

    }
     public static function loteProducto($request) {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $codigo = $request->input('codigo');
        $proveedor = $request->input('proveedor');
        $moneda = $request->input('moneda');

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'COD_PROD', 
                            1 => 'COSTO',
                            2 => 'INICIAL',
                            3 => 'STOCK',
                            4 => 'VENCIMIENTO',
                            5 => 'LOTE',
                            6 => 'MONEDA',
                            7 => 'DECIMAL',
                            8 => 'DESCRIPCION',
                            9 => 'LOTE_ID'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = Compra::
                    leftJoin('COMPRASDET', function($join){
                        $join->on('COMPRAS.CODIGO', '=', 'COMPRASDET.CODIGO')
                             ->on('COMPRAS.ID_SUCURSAL', '=', 'COMPRASDET.ID_SUCURSAL');
                    })
                    ->leftJoin('LOTES', function($join){
                        $join->on('LOTES.COD_PROD', '=', 'COMPRASDET.COD_PROD')
                             ->on('LOTES.ID_SUCURSAL', '=', 'COMPRAS.ID_SUCURSAL')
                             ->on('LOTES.LOTE', '=', 'COMPRASDET.LOTE');
                    })

                    ->where([
                        'COMPRASDET.COD_PROD' => $codigo,
                        'COMPRAS.MONEDA' => $moneda,
                        'COMPRAS.ID_SUCURSAL' => $user->id_sucursal,
                    ])
                    ->where('LOTES.CANTIDAD', '>', 0)
                    ->count(); 

                    $totalData2=DB::connection('retail')->table('CONTEO_DET')->select(DB::raw('LOTES.COD_PROD, LOTES.COSTO, LOTES.CANTIDAD_INICIAL, LOTES.CANTIDAD, LOTES.FECHA_VENC, LOTES.LOTE, PRODUCTOS_AUX.MONEDA, MONEDAS.CANDEC, LOTES.ID AS LOTE_ID, PRODUCTOS.DESCRIPCION, PRODUCTOS.VENCIMIENTO'))
                    ->leftJoin('LOTE_TIENE_CONTEODET', 'LOTE_TIENE_CONTEODET.ID_CONTEO_DET', '=', 'CONTEO_DET.ID')
                    ->leftJoin('LOTES', function($join){
                        $join->on('LOTES.COD_PROD', '=', 'CONTEO_DET.COD_PROD')
                             ->on('LOTES.ID_SUCURSAL', '=', 'CONTEO_DET.ID_SUCURSAL')
                             ->on('LOTES.ID', '=', 'LOTE_TIENE_CONTEODET.ID_LOTE');
                    })
                    ->leftJoin('PRODUCTOS_AUX', function($join){
                        $join->on('PRODUCTOS_AUX.CODIGO', '=', 'CONTEO_DET.COD_PROD')
                             ->on('PRODUCTOS_AUX.ID_SUCURSAL', '=', 'CONTEO_DET.ID_SUCURSAL');
                    })
                    ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'CONTEO_DET.COD_PROD')
                    ->leftJoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'PRODUCTOS_AUX.MONEDA')
                    ->where([
                        'CONTEO_DET.COD_PROD' => $codigo,
                        'PRODUCTOS_AUX.MONEDA' => $moneda,
                        'CONTEO_DET.ID_SUCURSAL' => $user->id_sucursal,
                    ])
                    ->where('LOTES.CANTIDAD', '>', 0)
                    ->count();

                    $totalData3=DB::connection('retail')->table('TRANSFERENCIAS')->select(DB::raw('LOTES.COD_PROD, LOTES.COSTO, LOTES.CANTIDAD_INICIAL, LOTES.CANTIDAD, LOTES.FECHA_VENC, LOTES.LOTE, TRANSFERENCIAS.MONEDA_ENVIAR, MONEDAS.CANDEC, LOTES.ID AS LOTE_ID, PRODUCTOS.DESCRIPCION, PRODUCTOS.VENCIMIENTO'))
                     ->leftJoin('TRANSFERENCIAS_DET', function($join){
                        $join->on('TRANSFERENCIAS.CODIGO', '=', 'TRANSFERENCIAS_DET.CODIGO')
                             ->on('TRANSFERENCIAS.ID_SUCURSAL', '=', 'TRANSFERENCIAS_DET.ID_SUCURSAL');
                    })
                    ->leftJoin('Lote_tiene_TransferenciaDet', 'Lote_tiene_TransferenciaDet.ID_TRANSFERENCIA_DET', '=', 'TRANSFERENCIAS_DET.ID')
                    ->leftJoin('LOTES', function($join){
                        $join->on('LOTES.COD_PROD', '=', 'TRANSFERENCIAS_DET.CODIGO_PROD')
                             ->on('LOTES.ID_SUCURSAL', '=', 'TRANSFERENCIAS.SUCURSAL_DESTINO')
                             ->on('LOTES.ID', '=', 'Lote_tiene_TransferenciaDet.ID_LOTE');
                    })
                    ->leftJoin('PRODUCTOS_AUX', function($join){
                        $join->on('PRODUCTOS_AUX.CODIGO', '=', 'TRANSFERENCIAS_DET.CODIGO_PROD')
                             ->on('PRODUCTOS_AUX.ID_SUCURSAL', '=', 'TRANSFERENCIAS.SUCURSAL_DESTINO');
                    })
                    ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'TRANSFERENCIAS_DET.CODIGO_PROD')
                    ->leftJoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'TRANSFERENCIAS.MONEDA_ENVIAR')
                    ->where([
                        'TRANSFERENCIAS_DET.CODIGO_PROD' => $codigo,
                        'TRANSFERENCIAS.MONEDA_ENVIAR' => $moneda,
                        'TRANSFERENCIAS.SUCURSAL_DESTINO' => $user->id_sucursal,
                    ])
                    ->where('LOTES.CANTIDAD', '>', 0)
                    ->count();
                    $totalData=$totalData+$totalData2+$totalData3;

                
        
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE VALOR EN VARIABLE SEARCH

        if(empty($request->input('search.value')))
        {            

            /*  ************************************************************ */

            //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

            $posts = Compra::select(DB::raw('COMPRASDET.COD_PROD, LOTES.COSTO, LOTES.CANTIDAD_INICIAL, LOTES.CANTIDAD, LOTES.FECHA_VENC, LOTES.LOTE, COMPRAS.MONEDA, MONEDAS.CANDEC, LOTES.ID AS LOTE_ID, PRODUCTOS.DESCRIPCION, PRODUCTOS.VENCIMIENTO'))
                    ->leftJoin('COMPRASDET', function($join){
                        $join->on('COMPRAS.CODIGO', '=', 'COMPRASDET.CODIGO')
                             ->on('COMPRAS.ID_SUCURSAL', '=', 'COMPRASDET.ID_SUCURSAL');
                    })
                    ->leftJoin('LOTES', function($join){
                        $join->on('LOTES.COD_PROD', '=', 'COMPRASDET.COD_PROD')
                             ->on('LOTES.ID_SUCURSAL', '=', 'COMPRAS.ID_SUCURSAL')
                             ->on('LOTES.LOTE', '=', 'COMPRASDET.LOTE');
                    })
                    ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'COMPRASDET.COD_PROD')
                    ->leftJoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'COMPRAS.MONEDA')
                    ->where([
                        'COMPRASDET.COD_PROD' => $codigo,
                        'COMPRAS.MONEDA' => $moneda,
                        'COMPRAS.ID_SUCURSAL' => $user->id_sucursal,
                    ])
                    ->where('LOTES.CANTIDAD', '>', 0)
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
                          

                          $posts2 = DB::connection('retail')->table('CONTEO_DET')->select(DB::raw('LOTES.COD_PROD, LOTES.COSTO, LOTES.CANTIDAD_INICIAL, LOTES.CANTIDAD, LOTES.FECHA_VENC, LOTES.LOTE, PRODUCTOS_AUX.MONEDA, MONEDAS.CANDEC, LOTES.ID AS LOTE_ID, PRODUCTOS.DESCRIPCION, PRODUCTOS.VENCIMIENTO'))
                    ->leftJoin('LOTE_TIENE_CONTEODET', 'LOTE_TIENE_CONTEODET.ID_CONTEO_DET', '=', 'CONTEO_DET.ID')
                    ->leftJoin('LOTES', function($join){
                        $join->on('LOTES.COD_PROD', '=', 'CONTEO_DET.COD_PROD')
                             ->on('LOTES.ID_SUCURSAL', '=', 'CONTEO_DET.ID_SUCURSAL')
                             ->on('LOTES.ID', '=', 'LOTE_TIENE_CONTEODET.ID_LOTE');
                    })
                    ->leftJoin('PRODUCTOS_AUX', function($join){
                        $join->on('PRODUCTOS_AUX.CODIGO', '=', 'CONTEO_DET.COD_PROD')
                             ->on('PRODUCTOS_AUX.ID_SUCURSAL', '=', 'CONTEO_DET.ID_SUCURSAL');
                    })
                    ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'CONTEO_DET.COD_PROD')
                    ->leftJoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'PRODUCTOS_AUX.MONEDA')
                    ->where([
                     
                        'CONTEO_DET.COD_PROD' => $codigo,
                        'PRODUCTOS_AUX.MONEDA' => $moneda,
                        'CONTEO_DET.ID_SUCURSAL' => $user->id_sucursal,
                    ])
                    ->where('LOTES.CANTIDAD', '>', 0)
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();

                         $posts3=DB::connection('retail')->table('TRANSFERENCIAS')->select(DB::raw('LOTES.COD_PROD, LOTES.COSTO, LOTES.CANTIDAD_INICIAL, LOTES.CANTIDAD, LOTES.FECHA_VENC, LOTES.LOTE, TRANSFERENCIAS.MONEDA_ENVIAR as MONEDA, MONEDAS.CANDEC, LOTES.ID AS LOTE_ID, PRODUCTOS.DESCRIPCION, PRODUCTOS.VENCIMIENTO'))
                     ->leftJoin('TRANSFERENCIAS_DET', function($join){
                        $join->on('TRANSFERENCIAS.CODIGO', '=', 'TRANSFERENCIAS_DET.CODIGO')
                             ->on('TRANSFERENCIAS.ID_SUCURSAL', '=', 'TRANSFERENCIAS_DET.ID_SUCURSAL');
                    })
                    ->leftJoin('Lote_tiene_TransferenciaDet', 'Lote_tiene_TransferenciaDet.ID_TRANSFERENCIA_DET', '=', 'TRANSFERENCIAS_DET.ID')
                    ->leftJoin('LOTES', function($join){
                        $join->on('LOTES.COD_PROD', '=', 'TRANSFERENCIAS_DET.CODIGO_PROD')
                             ->on('LOTES.ID_SUCURSAL', '=', 'TRANSFERENCIAS.SUCURSAL_DESTINO')
                             ->on('LOTES.ID', '=', 'Lote_tiene_TransferenciaDet.ID_LOTE');
                    })
                    ->leftJoin('PRODUCTOS_AUX', function($join){
                        $join->on('PRODUCTOS_AUX.CODIGO', '=', 'TRANSFERENCIAS_DET.CODIGO_PROD')
                             ->on('PRODUCTOS_AUX.ID_SUCURSAL', '=', 'TRANSFERENCIAS.SUCURSAL_DESTINO');
                    })
                    ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'TRANSFERENCIAS_DET.CODIGO_PROD')
                    ->leftJoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'TRANSFERENCIAS.MONEDA_ENVIAR')
                    ->where([
                        'TRANSFERENCIAS_DET.CODIGO_PROD' => $codigo,
                        'TRANSFERENCIAS.MONEDA_ENVIAR' => $moneda,
                        'TRANSFERENCIAS.SUCURSAL_DESTINO' => $user->id_sucursal,
                    ])
                    ->where('LOTES.CANTIDAD', '>', 0)
                    ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
            /*  ************************************************************ */

        } else {

            /*  ************************************************************ */

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            /*  ************************************************************ */

            // CARGAR LOS PRODUCTOS FILTRADOS EN DATATABLE

            $posts =  Compra::select(DB::raw('COMPRASDET.COD_PROD, LOTES.COSTO, LOTES.CANTIDAD_INICIAL, LOTES.CANTIDAD, LOTES.FECHA_VENC, LOTES.LOTE, COMPRAS.MONEDA, MONEDAS.CANDEC, LOTES.ID AS LOTE_ID, PRODUCTOS.DESCRIPCION, PRODUCTOS.VENCIMIENTO'))
                    ->leftJoin('COMPRASDET', function($join){
                        $join->on('COMPRAS.CODIGO', '=', 'COMPRASDET.CODIGO')
                             ->on('COMPRAS.ID_SUCURSAL', '=', 'COMPRASDET.ID_SUCURSAL');
                    })
                    ->leftJoin('LOTES', function($join){
                        $join->on('LOTES.COD_PROD', '=', 'COMPRASDET.COD_PROD')
                             ->on('LOTES.ID_SUCURSAL', '=', 'COMPRAS.ID_SUCURSAL')
                             ->on('LOTES.LOTE', '=', 'COMPRASDET.LOTE');
                    })
                    ->leftJoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'COMPRAS.MONEDA')
                    ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'COMPRASDET.COD_PROD')
                    ->where([
                        'COMPRASDET.COD_PROD' => $codigo,
                        'COMPRAS.MONEDA' => $moneda,
                        'COMPRAS.ID_SUCURSAL' => $user->id_sucursal,
                    ])
                    ->where('LOTES.CANTIDAD', '>', 0)
                            ->where(function ($query) use ($search) {
                                $query->where('LOTES.LOTE','LIKE',"{$search}%")
                                      ->orWhere('LOTES.CANTIDAD', 'LIKE',"{$search}%");
                            })
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();


                    $posts2 = DB::connection('retail')->table('CONTEO_DET')->select(DB::raw('LOTES.COD_PROD, LOTES.COSTO, LOTES.CANTIDAD_INICIAL, LOTES.CANTIDAD, LOTES.FECHA_VENC, LOTES.LOTE, PRODUCTOS_AUX.MONEDA, MONEDAS.CANDEC, LOTES.ID AS LOTE_ID, PRODUCTOS.DESCRIPCION, PRODUCTOS.VENCIMIENTO'))
                    ->leftJoin('LOTE_TIENE_CONTEODET', 'LOTE_TIENE_CONTEODET.ID_CONTEO_DET', '=', 'CONTEO_DET.ID')
                    ->leftJoin('LOTES', function($join){
                        $join->on('LOTES.COD_PROD', '=', 'CONTEO_DET.COD_PROD')
                             ->on('LOTES.ID_SUCURSAL', '=', 'CONTEO_DET.ID_SUCURSAL')
                             ->on('LOTES.ID', '=', 'LOTE_TIENE_CONTEODET.ID_LOTE');
                    })
                    ->leftJoin('PRODUCTOS_AUX', function($join){
                        $join->on('PRODUCTOS_AUX.CODIGO', '=', 'CONTEO_DET.COD_PROD')
                             ->on('PRODUCTOS_AUX.ID_SUCURSAL', '=', 'CONTEO_DET.ID_SUCURSAL');
                    })
                    ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'CONTEO_DET.COD_PROD')
                    ->leftJoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'PRODUCTOS_AUX.MONEDA')
                    ->where([
                        'CONTEO_DET.COD_PROD' => $codigo,
                        'PRODUCTOS_AUX.MONEDA' => $moneda,
                        'CONTEO_DET.ID_SUCURSAL' => $user->id_sucursal,
                    ])
                    ->where('LOTES.CANTIDAD', '>', 0)
                     ->where(function ($query) use ($search) {
                                $query->where('LOTES.LOTE','LIKE',"{$search}%")
                                      ->orWhere('LOTES.CANTIDAD', 'LIKE',"{$search}%");
                            })
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();

                         $posts3=DB::connection('retail')->table('TRANSFERENCIAS')->select(DB::raw('LOTES.COD_PROD, LOTES.COSTO, LOTES.CANTIDAD_INICIAL, LOTES.CANTIDAD, LOTES.FECHA_VENC, LOTES.LOTE, TRANSFERENCIAS.MONEDA_ENVIAR as MONEDA, MONEDAS.CANDEC, LOTES.ID AS LOTE_ID, PRODUCTOS.DESCRIPCION, PRODUCTOS.VENCIMIENTO'))
                     ->leftJoin('TRANSFERENCIAS_DET', function($join){
                        $join->on('TRANSFERENCIAS.CODIGO', '=', 'TRANSFERENCIAS_DET.CODIGO')
                             ->on('TRANSFERENCIAS.ID_SUCURSAL', '=', 'TRANSFERENCIAS_DET.ID_SUCURSAL');
                    })
                    ->leftJoin('Lote_tiene_TransferenciaDet', 'Lote_tiene_TransferenciaDet.ID_TRANSFERENCIA_DET', '=', 'TRANSFERENCIAS_DET.ID')
                    ->leftJoin('LOTES', function($join){
                        $join->on('LOTES.COD_PROD', '=', 'TRANSFERENCIAS_DET.CODIGO_PROD')
                             ->on('LOTES.ID_SUCURSAL', '=', 'TRANSFERENCIAS.SUCURSAL_DESTINO')
                             ->on('LOTES.ID', '=', 'Lote_tiene_TransferenciaDet.ID_LOTE');
                    })
                    ->leftJoin('PRODUCTOS_AUX', function($join){
                        $join->on('PRODUCTOS_AUX.CODIGO', '=', 'TRANSFERENCIAS_DET.CODIGO_PROD')
                             ->on('PRODUCTOS_AUX.ID_SUCURSAL', '=', 'TRANSFERENCIAS.SUCURSAL_DESTINO');
                    })
                    ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'TRANSFERENCIAS_DET.CODIGO_PROD')
                    ->leftJoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'TRANSFERENCIAS.MONEDA_ENVIAR')
                    ->where([
                        'TRANSFERENCIAS_DET.CODIGO_PROD' => $codigo,
                        'TRANSFERENCIAS.MONEDA_ENVIAR' => $moneda,
                        'TRANSFERENCIAS.SUCURSAL_DESTINO' => $user->id_sucursal,
                    ])
                    ->where('LOTES.CANTIDAD', '>', 0)
                      ->where(function ($query) use ($search) {
                                $query->where('LOTES.LOTE','LIKE',"{$search}%")
                                      ->orWhere('LOTES.CANTIDAD', 'LIKE',"{$search}%");
                            })
                    ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = Compra::leftJoin('COMPRASDET', function($join){
                                $join->on('COMPRAS.CODIGO', '=', 'COMPRASDET.CODIGO')
                                     ->on('COMPRAS.ID_SUCURSAL', '=', 'COMPRASDET.ID_SUCURSAL');
                            })
                            ->leftJoin('LOTES', function($join){
                                $join->on('LOTES.COD_PROD', '=', 'COMPRASDET.COD_PROD')
                                     ->on('LOTES.ID_SUCURSAL', '=', 'COMPRAS.ID_SUCURSAL')
                                     ->on('LOTES.LOTE', '=', 'COMPRASDET.LOTE');
                            })
                            ->where([
                              
                                'COMPRASDET.COD_PROD' => $codigo,
                                'COMPRAS.MONEDA' => $moneda,
                                'COMPRAS.ID_SUCURSAL' => $user->id_sucursal,
                            ])
                            ->where('LOTES.CANTIDAD', '>', 0)
                            ->where(function ($query) use ($search) {
                                    $query->where('LOTES.LOTE','LIKE',"{$search}%")
                                    ->orWhere('LOTES.CANTIDAD', 'LIKE',"{$search}%");
                            })
                            ->count();

            $totalFiltered = $totalFiltered + count($posts2)+count($posts3);

            /*  ************************************************************ */  

        }

        $data = array();

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts))
        {
            foreach ($posts as $post)
            {

                /*  --------------------------------------------------------------------------------- */

                // CARGAR EN LA VARIABLE 

                $nestedData['COD_PROD'] = $post->COD_PROD;
                $nestedData['DESCRIPCION'] = $post->DESCRIPCION;
                $nestedData['COSTO'] = Common::formato_precio($post->COSTO, $post->CANDEC);
                $nestedData['INICIAL'] = $post->CANTIDAD_INICIAL;
                $nestedData['STOCK'] = $post->CANTIDAD;

                if ($post->VENCIMIENTO === 1) {
                    $nestedData['VENCIMIENTO'] = $post->FECHA_VENC;
                } else {
                    $nestedData['VENCIMIENTO'] = 'N/A';
                }
                
                $nestedData['LOTE'] = $post->LOTE;
                $nestedData['MONEDA'] = $post->MONEDA;
                $nestedData['DECIMAL'] = $post->CANDEC;
                $nestedData['LOTE_ID'] = $post->LOTE_ID;

                $data[] = $nestedData;

                /*  --------------------------------------------------------------------------------- */

            }

            
        }
        if(!empty($posts2)){
                foreach ($posts2 as  $post) {
                $nestedData['COD_PROD'] = $post->COD_PROD;
                $nestedData['DESCRIPCION'] = $post->DESCRIPCION;
                $nestedData['COSTO'] = Common::formato_precio($post->COSTO, $post->CANDEC);
                $nestedData['INICIAL'] = $post->CANTIDAD_INICIAL;
                $nestedData['STOCK'] = $post->CANTIDAD;

                if ($post->VENCIMIENTO === 1) {
                    $nestedData['VENCIMIENTO'] = $post->FECHA_VENC;
                } else {
                    $nestedData['VENCIMIENTO'] = 'N/A';
                }
                
                $nestedData['LOTE'] = $post->LOTE;
                $nestedData['MONEDA'] = $post->MONEDA;
                $nestedData['DECIMAL'] = $post->CANDEC;
                $nestedData['LOTE_ID'] = $post->LOTE_ID;

                $data[] = $nestedData;
                    # code...
                }
            }
            if(!empty($posts3)){
                foreach ($posts3 as  $post) {
                $nestedData['COD_PROD'] = $post->COD_PROD;
                $nestedData['DESCRIPCION'] = $post->DESCRIPCION;
                $nestedData['COSTO'] = Common::formato_precio($post->COSTO, $post->CANDEC);
                $nestedData['INICIAL'] = $post->CANTIDAD_INICIAL;
                $nestedData['STOCK'] = $post->CANTIDAD;

                if ($post->VENCIMIENTO === 1) {
                    $nestedData['VENCIMIENTO'] = $post->FECHA_VENC;
                } else {
                    $nestedData['VENCIMIENTO'] = 'N/A';
                }
                
                $nestedData['LOTE'] = $post->LOTE;
                $nestedData['MONEDA'] = $post->MONEDA;
                $nestedData['DECIMAL'] = $post->CANDEC;
                $nestedData['LOTE_ID'] = $post->LOTE_ID;

                $data[] = $nestedData;
                    # code...
                }
            }
        
        /*  --------------------------------------------------------------------------------- */

        // PREPARAR EL ARRAY A ENVIAR 

        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );
        
        /*  --------------------------------------------------------------------------------- */

        // CONVERTIR EN JSON EL ARRAY Y ENVIAR 

       return $json_data; 

        /*  --------------------------------------------------------------------------------- */

    }
}
