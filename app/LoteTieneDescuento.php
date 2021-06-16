<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Stock;

class LoteTieneDescuento extends Model{
	
	/*  --------------------------------------------------------------------------------- */

    // INICIAR LAS VARIABLES GLOBALES

    protected $connection = 'retail';
    protected $table = 'lote_tiene_descuento';
    
    public static function guardarLoteDescuento($dato) {

        /*  --------------------------------------------------------------------------------- */

        	try {
       
           	DB::connection('retail')->beginTransaction();

           	// OBTENER LOS DATOS DEL USUARIO LOGUEADO 

           	$user = auth()->user();
         	$errorText = [];
    			$data = array();	
    			$datos = $dato["data"]["descuento"];
    			
           /*  --------------------------------------------------------------------------------- */

           // CARGAR DETALLE DE TRANSFERENCIA DET 

           	foreach ($datos as $key => $value) {
           		
               $loteCantidad = Stock::select('CANTIDAD')
	               ->where('ID','=', $value["LOTE_ID"])
	               ->get()
	               ->toArray();
               
               if ((float)$loteCantidad[0]["CANTIDAD"]> 0) {

               	$obtenerDescuento = LoteTieneDescuento::select('DESCUENTO')
	               	->where('FK_LOTE', '=', $value["LOTE_ID"])
	               	->get()
	               	->toArray();
               		
               	if(count($obtenerDescuento)==0){
	               			
		        			$inicio = date('Y-m-d', strtotime($value["FECHA_INICIO"]));
		       		 	$final = date('Y-m-d', strtotime($value["FECHA_FINAL"]));
		       		 	
	                  $loteDescuento = LoteTieneDescuento::insert([
	                  	'FK_LOTE' => $value["LOTE_ID"],
	                  	'DESCUENTO' => $value["DESCUENTO"],
	                  	'MOTIVO' => $value["MOTIVO"],
	                  	'FECHA_INICIO' => $inicio,
	                  	'FECHA_FIN' => $final
	                  ]);
	                 
               	}else{
               		$errorText['CODIGO'] = $value["CODIGO"].' ya tiene descuento de '.$obtenerDescuento[0]["DESCUENTO"]."%";
               		$data[] = $errorText;
               	}

               }

           }

           /*  --------------------------------------------------------------------------------- */


           DB::connection('retail')->commit();

           /*  --------------------------------------------------------------------------------- */

           // RETORNAR VALOR 
           if(count($data) > 0){
          	 return ["response" => true, "error"=>$data];
          	}

          	return ["response" => true];

           /*  --------------------------------------------------------------------------------- */

        } catch (Exception $e) {

            DB::connection('retail')->rollBack();
            throw $e;

        }   
    } 
}
