<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class VentaRetencion extends Model
{
    protected $connection = 'retail';
    protected $table = 'ventas_retencion';

    public static function guardar_referencia($data){

    	try {

	    	/*  --------------------------------------------------------------------------------- */

	    	$retencion = VentaRetencion::insertGetId([
	    		'MONTO' => $data["MONTO"],
	    		'FK_VENTA' => $data["FK_VENTA"],
	    	]);

	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Venta Retencion: Éxito al guardar.', ['VENTA' => $data["FK_VENTA"], 'ID RETENCION' => $retencion]);

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Venta Retencion: Error al guardar.', ['VENTA' => $data["FK_VENTA"]]);

			/*  --------------------------------------------------------------------------------- */

		}
    }
}
