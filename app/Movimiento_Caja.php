<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Movimiento_Caja extends Model
{
    //
    	protected $connection = 'retail';
	protected $table = 'movimientos_cajas';
    public $timestamps = false;

        public static function guardar_movimiento($data){

    	try {
    		DB::connection('retail')->beginTransaction();
    		 $user = auth()->user();
             $dia = date("Y-m-d H:i:s");



	    	/*  --------------------------------------------------------------------------------- */

	    	$movimiento = Movimiento_Caja::insertGetId([
	    		'CAJA' => $data["data"]["CAJA"],
	    		'TIPO' => $data["data"]["TIPO"],
	    		'MONEDA' => $data["data"]["MONEDA"],
	    		'MOVIMIENTO' => $data["data"]["MEDIO"],
	    		'COMENTARIO' => $data["data"]["COMENTARIO"],
	    		'IMPORTE' => Common::quitar_coma($data["data"]["IMPORTE"]),
	    		'ID_SUCURSAL' => $user->id_sucursal,
	    		'FECALTAS' => $dia
	    	]);

	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Caja movimiento: Éxito al guardar movimiento .', ['MOVIMIENTO' => $movimiento, 'CAJA' => $data["data"]["CAJA"]]);
            DB::connection('retail')->commit();
            return["response"=>true];
	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Caja movimiento: Error al guardar movimiento .', ['CAJA' => $data["data"]["CAJA"]]);
			DB::connection('retail')->rollBack();
            return ["response"=>false,"statusText"=>"ERROR!! - Ha ocurrido un error al guardar el movimiento!!"];

			/*  --------------------------------------------------------------------------------- */

		}
    }
}
