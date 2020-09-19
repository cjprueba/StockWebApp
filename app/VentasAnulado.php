<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class VentasAnulado extends Model
{
    protected $connection = 'retail';
	protected $table = 'ventas_anulado';
	public $timestamps = false;

	public static function guardar_referencia($data){

    	try {

	    	/*  --------------------------------------------------------------------------------- */

	    	$anulado = VentasAnulado::insertGetId([
	    		'FK_VENTA' => $data["FK_VENTA"],
	    		'ANULADO' => $data["ANULADO"],
	    		'FECHA' => $data["FECHA"]
	    	]);

	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Venta Anulado: Éxito al guardar.', ['VENTA' => $data["FK_VENTA"], 'ID ANULADO' => $anulado]);

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Venta Anulado: Error al guardar.', ['VENTA' => $data["FK_VENTA"]]);

			/*  --------------------------------------------------------------------------------- */

		}
    }

     public static function anular_venta($id_venta, $fecha)
    {

    	/*  --------------------------------------------------------------------------------- */



    	/*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES



        /*  --------------------------------------------------------------------------------- */

        //ANULAR VENTA

        $stock = VentasAnulado::where('FK_VENTA','=', $id_venta)
        ->update(['ANULADO' => 1, 
        		  'FECHA' => $fecha]);

	    /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return ;

        /*  --------------------------------------------------------------------------------- */

    }

    public static function modificar_referencia($data){

    	try {

	    	/*  --------------------------------------------------------------------------------- */

	    	$anulado = VentasAnulado::
	    	where('FK_VENTA', '=', $data['FK_VENTA'])
	    	->update([
	    		'ANULADO' => $data["ANULADO"],
	    	]);

	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Venta Anulado Modificado: Éxito al guardar.', ['VENTA' => $data["FK_VENTA"], 'ID ANULADO' => $anulado]);

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Venta Anulado Modificado: Error al guardar.', ['VENTA' => $data["FK_VENTA"]]);

			/*  --------------------------------------------------------------------------------- */

		}
    }
}
