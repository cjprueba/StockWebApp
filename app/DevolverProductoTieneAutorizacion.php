<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class DevolverProductoTieneAutorizacion extends Model
{
    protected $connection = 'retail';
	protected $table = 'devolucion_productos_tiene_autorizacion';
	public $timestamps = false;

	public static function guardar_referencia($data){
		try {

	    	/*  --------------------------------------------------------------------------------- */

	    	$autorizacion = DevolverProductoTieneAutorizacion::insertGetId([
	    		'FK_SALIDA_PRODUCTO' => $data["FK_SALIDA_PRODUCTO"],
	    		'FK_USER'=> $data["FK_USER"],
	    		'FK_USER_SUPERVISOR' => $data["FK_USER_SUPERVISOR"]
	    	]);


	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Usuario Supervisor: Éxito al guardar y confirmar.', ['PRODUCTO' => $data["FK_SALIDA_PRODUCTO"], 'FK_USER' => $autorizacion]);

	    	/*  --------------------------------------------------------------------------------- */

	    } catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Usuario Supervisor: Error al guardar y confirmar.', ['PRODUCTO' => $data["FK_SALIDA_PRODUCTO"]]);

			/*  --------------------------------------------------------------------------------- */
		}
	}
}
