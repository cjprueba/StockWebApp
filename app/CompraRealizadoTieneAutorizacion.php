<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class CompraRealizadoTieneAutorizacion extends Model
{
    protected $connection = 'retail';
	protected $table = 'compras_tiene_autorizacion';
	public $timestamps = false;

	public static function guardar_referencia($data){

    	try {

	    	/*  --------------------------------------------------------------------------------- */

	    	$autorizacion = CompraRealizadoTieneAutorizacion::insertGetId([
	    		'FK_COMPRA' => $data["FK_COMPRA"],
	    		'FK_USER'=> $data["FK_USER"],
	    		'FK_USER_SUPERVISOR' => $data["FK_USER_SUPERVISOR"]
	    	]);


	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Usuario Supervisor: Éxito al guardar y confirmar.', ['COMPRA' => $data["FK_COMPRA"], 'FK_USER' => $autorizacion]);

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Usuario Supervisor: Error al guardar y confirmar.', ['COMPRA' => $data["FK_COMPRA"]]);

			/*  --------------------------------------------------------------------------------- */

		}
	}
}
