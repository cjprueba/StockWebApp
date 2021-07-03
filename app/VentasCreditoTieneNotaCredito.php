<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class VentasCreditoTieneNotaCredito extends Model
{

	/*  --------------------------------------------------------------------------------- */

    // INICIAR LAS VARIABLES GLOBALES

    protected $connection = 'retail';
    protected $table = 'ventas_credito_tiene_nota_credito';
    
    /*  --------------------------------------------------------------------------------- */

    public static function guardar_referencia($data){

    	try {

	    	/*  --------------------------------------------------------------------------------- */
	    	
	    	$vt_nc = VentasCreditoTieneNotaCredito::insertGetId([
	    		'FK_VENTA' => $data["FK_VENTA"],
	    		'FK_NOTA_CREDITO' => $data["FK_NOTA_CREDITO"],
	    		'MONTO' => $data["MONTO"],
	    		'FK_MONEDA'=> $data["MONEDA"],
	    	]);

	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Venta Credito Tiene Nota Credito: Éxito al guardar.', ['VENTA' => $data["FK_VENTA"], 'FK_NOTA_CREDITO' => $vt_nc]);

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Venta Credito Tiene Nota de Credito: Error al guardar.', ['VENTA' => $data["FK_VENTA"]]);

			/*  --------------------------------------------------------------------------------- */

		}
    }

}
