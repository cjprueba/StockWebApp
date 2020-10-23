<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class DevolucionProv extends Model
{

    protected $connection = 'retail';
    protected $table = 'devolucion_prov';

    public static function guardar($data){

    	try {
    		DB::connection('retail')->beginTransaction();

    		/*  --------------------------------------------------------------------------------- */

    		// OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        	$user = auth()->user();

        	/*  --------------------------------------------------------------------------------- */

    		$dia = date('Y-m-d H:i:s');
    		$codigo = DevolucionProv::ultimo_codigo();

    		/*  --------------------------------------------------------------------------------- */

	    	$devolucion = DevolucionProv::insertGetId([
	    		'CODIGO' => $codigo,
	    		'OBSERVACION' => $data["OBSERVACION"],
	    		'FK_PROVEEDOR' => $data["FK_PROVEEDOR"],
	    		'FK_MONEDA' => $data["FK_MONEDA"],
	    		'TOTAL' => $data["TOTAL"],
	    		'FECALTAS' => $dia,
	    		'FECMODIF' => $dia,
	    		'FK_USER_CR' => $user->id,
	    		'FK_USER_MD' => $user->id,
	    		'ID_SUCURSAL' => $user->id_sucursal
	    	]);

	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Devolucion Prov: Éxito al guardar.', ['DEVOLUCION PROV ID' => $devolucion, 'PROVEEDOR' => $data["FK_PROVEEDOR"]]);

	    	/*  --------------------------------------------------------------------------------- */
  			DB::connection('retail')->commit();
	    	return ["response" => true, "id" => $devolucion];

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {
    		 DB::connection('retail')->rollBack();

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Devolucion Prov: Error al guardar.', ['PROVEEDOR' => $data["FK_PROVEEDOR"]]);

			/*  --------------------------------------------------------------------------------- */

			return ["response" => false, "statusText" => "Error al guardar devolución proveedor"];

			/*  --------------------------------------------------------------------------------- */
		}
    }

    public static function ultimo_codigo() {

		/*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

		/*  --------------------------------------------------------------------------------- */

		// OBTENER ULTIMO CODIGO

		$codigo = DevolucionProv::select('CODIGO')
		->where(['ID_SUCURSAL' => $user->id_sucursal])
		->orderBy('CODIGO', 'desc')
		->limit(1)
		->get();

		/*  --------------------------------------------------------------------------------- */

		// RETORNAR VALOR 

		if (count($codigo) > 0) {
			return $codigo[0]['CODIGO'] + 1;
		} else {
			return 1;
		}

		/*  --------------------------------------------------------------------------------- */

	}
}
