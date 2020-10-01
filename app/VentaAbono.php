<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class VentaAbono extends Model
{
    protected $connection = 'retail';
    protected $table = 'ventas_abono';
    public $timestamps = false;

    public static function insertar_abono($data){

    	try {

	    	/*  --------------------------------------------------------------------------------- */

	    	$abono = VentaAbono::insertGetId([
	    		'PAGO' => $data['PAGO'], 
                'MONEDA' => $data['MONEDA'], 
                'FECHA' => $data['FECHA'], 
                'SALDO' => $data['SALDO'],
                'VUELTO' => $data['VUELTO'],
                'FK_CLIENTE' => $data['FK_CLIENTE'], 
                'FK_USER' => $data['FK_USER'], 
                'FK_SUCURSAL' => $data['FK_SUCURSAL'], 
                'CAJA' => $data['CAJA'], 
	    	]);

	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Venta Abono: Éxito al guardar.', ['CLIENTE' => $data["FK_CLIENTE"], 'ID ABONO' => $abono]);

	    	/*  --------------------------------------------------------------------------------- */

	    	// RETORNAR 
	    	
	    	return ['valor' => $abono];

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Venta Abono: Error al guardar.', ['VENTA' => $data["FK_CLIENTE"]]);

			/*  --------------------------------------------------------------------------------- */

		}

    }
}
