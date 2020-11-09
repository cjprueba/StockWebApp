<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class DevolucionProvDet extends Model
{
    protected $connection = 'retail';
    protected $table = 'devolucion_prov_det';

    public static function guardar($data, $id){

    	try {
       
    		/*  --------------------------------------------------------------------------------- */

    		if (is_array($data)) {

		    	if (count($data) > 0) {

		    		/*  --------------------------------------------------------------------------------- */

		    		// INSERTAR CHEQUE 

		    		foreach ($data as $key => $value) {

		    			$devolucion_prov_det = DevolucionProvDet::insertGetId([
				    		'COD_PROD' => $value["CODIGO"],
				    		'CANTIDAD' => $value["CANTIDAD"],
				    		'COSTO' => Common::quitar_coma($value["COSTO"], 2),
				    		'COSTO_TOTAL' => Common::quitar_coma($value["COSTO"], 2),
				    		'FK_DEVOLUCION_PROV' => $id,
				    		'FK_ID_LOTE' => $value["LOTE_ID"],
				    	]);

		    			/*  --------------------------------------------------------------------------------- */

		    			// RESTAR STOCK 

				    	Stock::restar_stock_id_lote($value["LOTE_ID"], $value["CANTIDAD"]);

				    	/*  --------------------------------------------------------------------------------- */

		    		}

			    	/*  --------------------------------------------------------------------------------- */
			    	


		    		Log::info('Devolucion Prov Det: Éxito al guardar.', ['DEVOLUCION PROV DET' => $devolucion_prov_det, 'DEVOLUCION PROV' => $id]);

		    		/*  --------------------------------------------------------------------------------- */

		    	}
		    	
    		}

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 
			

			Log::error('Devolucion Prov Det: Error al guardar.', ['DEVOLUCION PROV ID' => $id]);

			/*  --------------------------------------------------------------------------------- */

			return ["response" => false, "statusText" => "Error al guardar devolución proveedor"];

			/*  --------------------------------------------------------------------------------- */
		}
    }
}
