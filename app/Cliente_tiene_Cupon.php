<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Fpdf\Fpdf;

class Cliente_tiene_Cupon extends Model
{
    //
        protected $connection = 'retail';
        protected $table = 'cliente_tiene_cupon';
                public static function guardar_referencia($data){

    	try {

	    	/*  --------------------------------------------------------------------------------- */

	    	$Cliente_Cupon = Cliente_tiene_Cupon::insertGetId([
	    		'FK_VENTA' => $data["FK_VENTA"],
	    		'FK_CUPON'=> $data["FK_CUPON"],
	    		'FK_CLIENTE' => $data["FK_CLIENTE"],
	    		'MONEDA' => $data["MONEDA"],
	    	]);


	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Cliente Cupon: Éxito al guardar.', ['VENTA' => $data["FK_VENTA"], 'ID CUPON CLIENTE' => $Cliente_Cupon,'ID_CLIENTE'=>$data["FK_CLIENTE"]]);

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Cliente Cupon: Error al guardar.', ['VENTA' => $data["FK_VENTA"]]);

			/*  --------------------------------------------------------------------------------- */

		}
    }
}
