<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Fpdf\Fpdf;

class VentaCupon extends Model
{
    //
        protected $connection = 'retail';
        protected $table = 'ventas_cupon';
        public static function guardar_referencia($data){

    	try {

	    	/*  --------------------------------------------------------------------------------- */

	    	$Cupon = VentaCupon::insertGetId([
	    		'FK_VENTA' => $data["FK_VENTA"],
	    		'FK_CUPON'=> $data["FK_CUPON"],
	    		'CUPON_IMPORTE' => $data["CUPON_IMPORTE"],
	    		'CUPON_PORCENTAJE'=>$data["CUPON_PORCENTAJE"],
	    		'CUPON_TIPO'=>$data["CUPON_TIPO"],
	    		'FK_USER'=>$data["FK_USER"],
	    		'MONEDA' => $data["MONEDA"],
	    		'FECALTAS'=>$data["FECALTAS"],
	    		'HORALTAS'=>$data["HORALTAS"]
	    	]);


	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Venta Cupon: Éxito al guardar.', ['VENTA' => $data["FK_VENTA"], 'ID CUPON' => $Cupon]);

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Venta Cupon: Error al guardar.', ['VENTA' => $data["FK_VENTA"]]);

			/*  --------------------------------------------------------------------------------- */

		}
    }
         public static function obtener_id_cupon_venta($venta_id){

             $cupon=VentaCupon::select(DB::raw('FK_CUPON'))
          ->where('FK_VENTA','=', $venta_id)
          ->get()->toArray();
         if(count($cupon)>0){
         	return $cupon[0]["FK_CUPON"];
         }else{
         	return 0;
         }
       
     }
}
