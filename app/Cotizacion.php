<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Common;
use App\Parametro;

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


    public static function cotizacion_dia($monedaSistema, $monedaEnviar)
    {

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

        // REVISAR SI MONEDAS SON IGUALES 

        if ($monedaSistema === $monedaEnviar) {
            return $cotizacion = 1;
        }

        /*  --------------------------------------------------------------------------------- */

        // OBTENER COLUMNA DIA 

        $diaLetra = 'CA'.$dia;

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI ES TAB UNICA 

        $tab_unica = Parametro::tab_unica();

        /*  --------------------------------------------------------------------------------- */

        // COMPROBAR SI LA TABLA ES UNICA 

        if($tab_unica === "SI") {

            $cotizaciones = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO'))
            ->where('CODMON', '=', $monedaEnviar)
            ->where('CODMON1', '=', $monedaSistema)
            ->where('MES', '=', '0')
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

        } else {

            $cotizaciones = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO'))
            ->where('CODMON', '=', $monedaEnviar)
            ->where('CODMON1', '=', $monedaSistema)
            ->where('MES', '=', $mes)
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

        }

        /*  --------------------------------------------------------------------------------- */
        
        // RETORNAR VALOR

        return (float)$cotizaciones[0]->CAMBIO;

        /*  --------------------------------------------------------------------------------- */    
         
    }

    public static function cotizacion_dia_monedas()
    {

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $hoy = date("Y-m-d");
        $dia = date("d");
        $mes = date("m");
        $ano = date("Y");
        $diaLetra = '';
        $valor = 0;

        $valor_guaranies = 0;
        $valor_dolares = 0;
        $valor_pesos = 0;
        $valor_reales = 0;


        $activar_guaranies = false;
        $activar_dolares = false;
        $activar_pesos = false;
        $activar_reales = false;

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER PARAMETROS 

        $parametros = Parametro::mostrarParametro();
        $tab_unica = $parametros['parametros'][0]->TAB_UNICA;
        $monedaSistema = $parametros['parametros'][0]->MONEDA;

        /*  --------------------------------------------------------------------------------- */

        // OBTENER COLUMNA DIA 

        $diaLetra = 'CA'.$dia;

        /*  --------------------------------------------------------------------------------- */

        // COMPROBAR SI LA TABLA ES UNICA 

        if($tab_unica === "SI") {

            $guaranies = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO'))
            ->where('CODMON', '=', 1)
            ->where('CODMON1', '=', $monedaSistema)
            ->where('MES', '=', '0')
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            $dolares = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO'))
            ->where('CODMON', '=', 2)
            ->where('CODMON1', '=', $monedaSistema)
            ->where('MES', '=', '0')
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            $pesos = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO'))
            ->where('CODMON', '=', 3)
            ->where('CODMON1', '=', $monedaSistema)
            ->where('MES', '=', '0')
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            $reales = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO'))
            ->where('CODMON', '=', 4)
            ->where('CODMON1', '=', $monedaSistema)
            ->where('MES', '=', '0')
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            /*  --------------------------------------------------------------------------------- */


        } else {

            $guaranies = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO'))
            ->where('CODMON', '=', 1)
            ->where('CODMON1', '=', $monedaSistema)
            ->where('MES', '=', $mes)
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            $dolares = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO'))
            ->where('CODMON', '=', 2)
            ->where('CODMON1', '=', $monedaSistema)
            ->where('MES', '=', $mes)
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            $pesos = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO'))
            ->where('CODMON', '=', 3)
            ->where('CODMON1', '=', $monedaSistema)
            ->where('MES', '=', $mes)
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            $reales = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO'))
            ->where('CODMON', '=', 4)
            ->where('CODMON1', '=', $monedaSistema)
            ->where('MES', '=', $mes)
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

        }

        /*  --------------------------------------------------------------------------------- */
        
        // REVISAR SI POSEEN COTIZACIONES 

            if (count($guaranies) <= 0) {
                $valor_guaranies = '';

                if ($monedaSistema !== 1) {
                    $activar_guaranies = true;
                }

            } else {
                $valor_guaranies = (float)$guaranies[0]->CAMBIO;
            }

            if (count($dolares) <= 0) {
                $valor_dolares = '';

                if ($monedaSistema !== 2) {
                    $activar_dolares = true;
                }

            } else {
                $valor_dolares = (float)$dolares[0]->CAMBIO;
            }

            if (count($pesos) <= 0) {
                $valor_pesos = '';

                if ($monedaSistema !== 3) {
                    $activar_pesos = true;
                }

            } else {
                $valor_pesos = (float)$pesos[0]->CAMBIO;
            }

            if (count($reales) <= 0) {
               $valor_reales = '';

               if ($monedaSistema !== 4) {
                    $activar_reales = true;
                }

            } else {
               $valor_reales = (float)$reales[0]->CAMBIO; 
            }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR
        
        return [
            'Guaranies' => $valor_guaranies, 
            'Dolares' => $valor_dolares,
            'Pesos' => $valor_pesos,
            'Reales' => $valor_reales,
            'activar_guaranies' => $activar_guaranies,
            'activar_dolares' => $activar_dolares,
            'activar_pesos' => $activar_pesos,
            'activar_reales' => $activar_reales 
        ];

        /*  --------------------------------------------------------------------------------- */    
         
    }

}
