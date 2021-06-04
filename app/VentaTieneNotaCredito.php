<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class VentaTieneNotaCredito extends Model
{
    protected $connection = 'retail';
    protected $table = 'ventas_tiene_nota_credito';

    public static function guardar_referencia($data){

    	try {

	    	/*  --------------------------------------------------------------------------------- */

	    	$vt_nc = VentaTieneNotaCredito::insertGetId([
	    		'FK_VENTA' => $data["FK_VENTA"],
	    		'FK_NOTA_CREDITO' => $data["FK_NOTA_CREDITO"],
	    		'ITEM'=>$data["ITEM"]
	    	]);

	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Venta Tiene Nota Credito: Éxito al guardar.', ['VENTA' => $data["FK_VENTA"], 'FK_NOTA_CREDITO' => $vt_nc]);

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Venta Tarjeta: Error al guardar.', ['VENTA' => $data["FK_VENTA"]]);

			/*  --------------------------------------------------------------------------------- */

		}
    }
}
