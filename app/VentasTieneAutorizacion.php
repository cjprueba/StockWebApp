<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class VentasTieneAutorizacion extends Model
{
		protected $connection = 'retail';
	protected $table = 'ventas_tiene_autorizacion';

	       public static function guardar_referencia($data){

    	try {

	    	/*  --------------------------------------------------------------------------------- */

	    	$autorizacion = User_Supervisor::insertGetId([
	    		'FK_VENTA' => $data["FK_VENTA"],
	    		'FK_USER'=> $data["FK_USER"],
	    		'FK_USER_SUPERVISOR' => $data["FK_USER_SUPERVISOR"]
	    	]);


	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Usuario Supervisor: Éxito al guardar y confirmar.', ['VENTA' => $data["FK_VENTA"], 'FK_USER' => $autorizacion]);

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Usuario Supervisor: Error al guardar y confirmar.', ['VENTA' => $data["FK_VENTA"]]);

			/*  --------------------------------------------------------------------------------- */

		}
    }
    //
}
