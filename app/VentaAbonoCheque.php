<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class VentaAbonoCheque extends Model
{
    protected $connection = 'retail';
    protected $table = 'ventas_abono_cheque';

    public static function guardar_referencia($data, $id){

    	try {

    		/*  --------------------------------------------------------------------------------- */

    		if (is_array($data)) {

		    	if (count($data) > 0) {

		    		/*  --------------------------------------------------------------------------------- */

		    		// INSERTAR CHEQUE 

		    		foreach ($data as $key => $value) {
		    			$cheque = VentaAbonoCheque::insertGetId([
				    		'FK_BANCO' => $value["CODIGO_BANCO"],
				    		'FK_ABONO' => $id,
				    		'NUMERO' => $value["NUMERO"],
				    		'FECHA_COBRO' => $value["FECHA"],
				    		'MONTO' => Common::quitar_coma($value["IMPORTE"], 2),
				    		'FORMA' => $value["FORMA"],
				    		'MONEDA' => $value["MONEDA"]
				    	]);
		    		}

			    	/*  --------------------------------------------------------------------------------- */

		    		Log::info('Venta Cheque: Éxito al guardar.', ['ABONO' => $id, 'ID CHEQUE' => $cheque]);

		    		/*  --------------------------------------------------------------------------------- */

		    	}
		    	
    		}

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Venta Cheque: Error al guardar.', ['PAGO' => $id]);

			/*  --------------------------------------------------------------------------------- */

			return ["response" => false, "statusText" => "Error al guardar pago del cheque"];

			/*  --------------------------------------------------------------------------------- */
		}
    }
}
