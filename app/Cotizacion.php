<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Common;

class Cotizacion extends Model
{

    public static function CALMONED($data)
    {

    	/*  --------------------------------------------------------------------------------- */

    	// DEVOLVER VALOR SI ES QUE LAS MONEDAS SON IGUALES

    	if ($data['monedaProducto'] === $data['monedaSistema']) {
    		return ["valor" => $data['precio']];
    	}

    	/*  --------------------------------------------------------------------------------- */

    	// INICIAR VARIABLES 

    	$hoy = date("Y-m-d");
    	$dia = date("d");
    	$mes = date("m");
    	$ano = date("Y");
    	$diaLetra = '';
    	$valor = 0;

    	/*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER COLUMNA DIA 

    	$diaLetra = 'CA'.$dia;

    	/*  --------------------------------------------------------------------------------- */

    	// COMPROBAR SI LA TABLA ES UNICA 

    	if($data['tab_unica'] === true) {

    		$cotizaciones = DB::connection('retail')
	        ->table('TABRELMON')
	        ->select(DB::raw(''.$diaLetra.' AS CAMBIO, FORMULA'))
	        ->where('CODMON', '=', $data['monedaProducto'])
	        ->where('CODMON1', '=', $data['monedaSistema'])
	        ->where('MES', '=', '0')
	        ->where('ANO', '=', $ano)
	        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
	        ->get();

    	} else {

    		$cotizaciones = DB::connection('retail')
	        ->table('TABRELMON')
	        ->select(DB::raw(''.$diaLetra.' AS CAMBIO, FORMULA'))
	        ->where('CODMON', '=', $data['monedaProducto'])
	        ->where('CODMON1', '=', $data['monedaSistema'])
	        ->where('MES', '=', $mes)
	        ->where('ANO', '=', $ano)
	        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
	        ->get();

    	}

    	/*  --------------------------------------------------------------------------------- */

    	// REALIZAR CALCULO 

    	if ($cotizaciones[0]->FORMULA === '*') {
    		$valor = Common::formato_precio(((float)$data['precio'] * (float)$cotizaciones[0]->CAMBIO), $data['decSistema']);
    	} else if ($cotizaciones[0]->FORMULA === '/') {
    		$valor = Common::formato_precio(((float)$data['precio'] / (float)$cotizaciones[0]->CAMBIO), $data['decSistema']);
    	} else if ($cotizaciones[0]->FORMULA === '+') {
    		$valor = Common::formato_precio(((float)$data['precio'] + (float)$cotizaciones[0]->CAMBIO), $data['decSistema']);
    	} else if ($cotizaciones[0]->FORMULA === '-') {
    		$valor = Common::formato_precio(((float)$data['precio'] - (float)$cotizaciones[0]->CAMBIO), $data['decSistema']);
    	}	

    	/*  --------------------------------------------------------------------------------- */
    	
    	// RETORNAR VALOR

    	return ["valor" => $valor];

    	/*  --------------------------------------------------------------------------------- */	
    	 
    }

}
