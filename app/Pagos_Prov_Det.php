<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Pagos_Prov_Det extends Model
{
    /*  --------------------------------------------------------------------------------- */

    protected $connection = 'retail';
    protected $table = 'pagos_prov_det';

    /*  --------------------------------------------------------------------------------- */

    public static function insertar($data){

    	try {
        
    	/*  --------------------------------------------------------------------------------- */

    	$pago = Pagos_Prov_Det::insertGetId([
    		'FK_PAGOS_PROV' => $data["FK_PAGOS_PROV"],
    		'PAGO' => $data["PAGO"],
    		'FK_DEUDA' => $data["FK_DEUDA"]
    	]);

    	/*  --------------------------------------------------------------------------------- */

    	Log::info('Pago Proveedor Det: Éxito al guardar.', ['PAGO PROV DET' => $pago, 'DEUDA' => $data["FK_DEUDA"], 'PAGO PROV' => $data["FK_PAGOS_PROV"]]);

    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Pago Proveedor Det: Error al guardar.', ['DEUDA' => $data["FK_DEUDA"], 'PAGO PROV' => $data["FK_PAGOS_PROV"]]);

			/*  --------------------------------------------------------------------------------- */

		}
    }
}
