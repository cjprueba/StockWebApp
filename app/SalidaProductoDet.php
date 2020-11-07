<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalidaProductoDet extends Model
{
    //
     protected $connection = 'retail';
    protected $table = 'salida_productos_det';
    public static function guardar($data, $id){

    	try {
       
    		/*  --------------------------------------------------------------------------------- */

    		if (is_array($data)) {

		    	if (count($data) > 0) {

		    		/*  --------------------------------------------------------------------------------- */

		    		// INSERTAR CHEQUE 

		    		foreach ($data as $key => $value) {

		    			$salida_producto_det = SalidaProductoDet::insertGetId([
				    		'COD_PROD' => $value["CODIGO"],
				    		'CANTIDAD' => $value["CANTIDAD"],
				    		'COSTO' => Common::quitar_coma($value["COSTO"], 2),
				    		'COSTO_TOTAL' => Common::quitar_coma($value["COSTO_TOTAL"], 2),
				    		'FK_SALIDA_PRODUCTOS' => $id,
				    		'FK_ID_LOTE' => $value["LOTE_ID"],
				    	]);

		    			/*  --------------------------------------------------------------------------------- */

		    			// RESTAR STOCK 

				    	Stock::restar_stock_id_lote($value["LOTE_ID"], $value["CANTIDAD"]);

				    	/*  --------------------------------------------------------------------------------- */

		    		}

			    	/*  --------------------------------------------------------------------------------- */
			    	


		    		Log::info('Salida Producto detalle: Éxito al guardar.', ['SALIDA PRODUCTO DET' => $salida_producto_det, 'SALIDA PRODUCTO ID:' => $id]);

		    		/*  --------------------------------------------------------------------------------- */

		    	}
		    	
    		}

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 
			

			Log::error('SALIDA PRODUCTO DET: Error al guardar.', ['SALIDA PRODUCTO ID:' => $id]);

			/*  --------------------------------------------------------------------------------- */

			return ["response" => false, "statusText" => "Error al guardar la salida del producto"];

			/*  --------------------------------------------------------------------------------- */
		}
    }
}
