<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Stock;

class PrestamoProductoDet extends Model
{
    //
    protected $connection = 'retail';
    protected $table = 'prestamo_productos_det';
     public static function guardar($data, $id){

    	try {
       
    		/*  --------------------------------------------------------------------------------- */

    		if (is_array($data)) {

		    	if (count($data) > 0) {

		    		/*  --------------------------------------------------------------------------------- */

		    		// INSERTAR CHEQUE 

		    		foreach ($data as $key => $value) {
		    			// RESTAR STOCK 
		    			$lote=Stock::restar_stock_producto($value["CODIGO"],$value["CANTIDAD"]);
		    			/*  --------------------------------------------------------------------------------- */
						// VERIFICAR RESTA

		    			if($lote["response"]===true){
		    				/*  --------------------------------------------------------------------------------- */
							// RESTAR Y GUARDAR POR LOTE
		    				foreach ($lote["datos"] as $key => $value2) {
		    					# code...
		    					
		    					$stock = Stock::select(DB::raw('COSTO'))
						        ->where('id','=', $value2["id"])
						        ->get()->toArray();
						        

			    				$prestamo_producto_det = PrestamoProductoDet::insertGetId([
					    		'COD_PROD' => $value["CODIGO"],
					    		'CANTIDAD' => $value2["cantidad"],
					    		'COSTO' => Common::quitar_coma($stock[0]["COSTO"], 2),
					    		'COSTO_TOTAL' => Common::quitar_coma(($stock[0]["COSTO"]*$value2["cantidad"]), 2),
					    		'FK_PRESTAMO_PRODUCTOS' => $id,
					    		'FK_ID_LOTE' => $value2["id"],
					    		]);

		    				}

		    				
		    			}
		    			/*  --------------------------------------------------------------------------------- */

		    			

		    			/*  --------------------------------------------------------------------------------- */

		    			

				    	/*Stock::restar_stock_id_lote($value["LOTE_ID"], $value["CANTIDAD"]);*/

				    	
		    		}

			    	/*  --------------------------------------------------------------------------------- */
			    	


		    		Log::info('Prestamo de  Producto detalle: Éxito al guardar.', ['PRESTAMO PRODUCTO DET' => $prestamo_producto_det, 'PRESTAMO PRODUCTO ID:' => $id]);

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
