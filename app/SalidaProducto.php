<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use  App\SalidaProductoDet;
Use App\Common;
use Illuminate\Support\Facades\Log;


class SalidaProducto extends Model
{
    //
    protected $connection = 'retail';
    protected $table = 'salida_productos';

    public static function salida($data) {

        try {
         DB::connection('retail')->beginTransaction();
            /*  --------------------------------------------------------------------------------- */

            // GUARDAR DEVOLUCION PROVEEDOR 

            $salidaProducto = SalidaProducto::guardar([
                'TIPO' => $data['data']['tipo'],
                'OBSERVACION' => $data['data']['observacion'],
                'FK_MONEDA' => $data['data']['moneda'],
                'TOTAL' => Common::quitar_coma($data['data']['total'], 2)
            ]);

            /*  --------------------------------------------------------------------------------- */

            // GUARDAR DEVOLUCION PROVEEDOR DETALLE 

            if ($salidaProducto["response"] === true) {
                SalidaProductoDet::guardar($data['data']['productos'], $salidaProducto["id"]);
            } else {
                return $devolucionProv;
            }

            /*  --------------------------------------------------------------------------------- */

            // RETORNAR VALOR 
            DB::connection('retail')->commit();
            return ["response" => true, "statusText" => "Se ha realizado con éxito la salida"];

            /*  --------------------------------------------------------------------------------- */

        } catch (Exception $e) {
            DB::connection('retail')->rollBack();
            /*  --------------------------------------------------------------------------------- */

            // ERROR 

            Log::error('Salida de producto: Error al guardar.');

            /*  --------------------------------------------------------------------------------- */

            return ["response" => false, "statusText" => "Error al guardar la salida de productos"];

            /*  --------------------------------------------------------------------------------- */
        }

    }
        public static function guardar($data){

    	try {
    	

    		/*  --------------------------------------------------------------------------------- */

    		// OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        	$user = auth()->user();

        	/*  --------------------------------------------------------------------------------- */

    		$dia = date('Y-m-d H:i:s');
    

    		/*  --------------------------------------------------------------------------------- */

	    	$salida = SalidaProducto::insertGetId([
	    		'OBSERVACION' => $data["OBSERVACION"],
	    		'TIPO' => $data["TIPO"],
	    		'FK_MONEDA' => $data["FK_MONEDA"],
	    		'TOTAL' => $data["TOTAL"],
	    		'FECALTAS' => $dia,
	    		'FECMODIF' => $dia,
	    		'FK_USER_CR' => $user->id,
	    		'FK_USER_MD' => $user->id,
	    		'ID_SUCURSAL' => $user->id_sucursal
	    	]);

	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Salida de producto: Éxito al guardar.', ['Salida de producto id:' => $salida]);

	    	/*  --------------------------------------------------------------------------------- */
  			
	    	return ["response" => true, "id" => $salida];

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {
    	

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Salida de productos: Error al guardar.');

			/*  --------------------------------------------------------------------------------- */

			return ["response" => false, "statusText" => "Error al guardar la salida de los productos"];

			/*  --------------------------------------------------------------------------------- */
		}
    }
}
