<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class VentaCredito extends Model
{
    protected $connection = 'retail';
    protected $table = 'ventas_credito';
    public $timestamps = false;
    
    public static function guardar_referencia($data){

    	try {

	    	/*  --------------------------------------------------------------------------------- */

	    	$credito = VentaCredito::insertGetId([
	    		'FK_VENTA' => $data["FK_VENTA"],
	    		'MONTO' => $data["MONTO"],
	    		'MONEDA' => $data["MONEDA"],
	    		'DIAS_CREDITO' => $data["DIAS_CREDITO"],
	    		'FECHA_CREDITO_FIN' => $data["FECHA_CREDITO_FIN"],
	    		'SALDO' => $data["SALDO"],
	    	]);

	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Venta Crédito: Éxito al guardar.', ['VENTA' => $data["FK_VENTA"], 'ID CREDITO' => $credito]);

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Venta Crédito: Error al guardar.', ['VENTA' => $data["FK_VENTA"]]);

			/*  --------------------------------------------------------------------------------- */

		}
    }

    public static function obtener_creditos_cliente($codigo){

    	/*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

    	// OBTENER CREDITOS CLIENTES

    	$creditos_cliente = VentaCredito::select(DB::raw('VENTAS_CREDITO.FK_VENTA, VENTAS_CREDITO.SALDO'))
    	->leftjoin('VENTAS', 'VENTAS.ID', '=', 'VENTAS_CREDITO.FK_VENTA')
    	->where('VENTAS.CLIENTE', '=', $codigo)
    	->where('VENTAS_CREDITO.SALDO', '>', 'VENTAS_CREDITO.PAGO')
        ->where('VENTAS.ID_SUCURSAL', '=', $user->id_sucursal)
    	->get();

    	/*  --------------------------------------------------------------------------------- */

    	// RETORNAR 

    	if (count($creditos_cliente) > 0) {
    		return ['response' => true, 'creditos' => $creditos_cliente];
    	} else {
    		return ['response' => false, 'statusText' => 'No se han encontrado créditos'];
    	}

    	/*  --------------------------------------------------------------------------------- */

    }

        public static function obtener_credito_cliente($fk_venta){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER CREDITOS CLIENTES

        $credito_cliente = VentaCredito::select(DB::raw('VENTAS_CREDITO.FK_VENTA, VENTAS_CREDITO.SALDO'))
        ->leftjoin('VENTAS', 'VENTAS.ID', '=', 'VENTAS_CREDITO.FK_VENTA')
        ->where('VENTAS_CREDITO.SALDO', '>', 'VENTAS_CREDITO.PAGO')
        ->where('VENTAS.ID_SUCURSAL', '=', $user->id_sucursal)
        ->where('VENTAS.ID', '=', $fk_venta)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR 

        if (count($credito_cliente) > 0) {
            return ['response' => true, 'credito' => $credito_cliente];
        } else {
            return ['response' => false, 'statusText' => 'No se han encontrado créditos'];
        }

        /*  --------------------------------------------------------------------------------- */

    }
}
