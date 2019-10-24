<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

use App\Parametro;

class Common 
{   
    

    public static function formato_precio($valor, $candec)
    {

        /*  --------------------------------------------------------------------------------- */

        // DAR FORMATO AL PRECIO DE ACUERDO A LA MONEDA

        return number_format($valor, $candec, '.', ',');

        /*  --------------------------------------------------------------------------------- */

    }

    public static function quitar_coma($valor, $candec)
    {

  		$dato = '';
        
        /*  --------------------------------------------------------------------------------- */

        // DAR FORMATO AL PRECIO DE ACUERDO A LA MONEDA

         $dato = str_replace(',', '', $valor);
        
        return number_format((float)$dato, $candec, '.', '');

        /*  --------------------------------------------------------------------------------- */

    }

    public static function precio_candec($valor, $moneda){

        /*  --------------------------------------------------------------------------------- */

        // BUSCAR CANTIDAD DE DECIMALES DE MONEDA

        $candec = Parametro::candec($moneda);
                
        $cantidad_decimal = $candec["CANDEC"];
        $descripcion_moneda = $candec["DESCRIPCION"];

        /*  --------------------------------------------------------------------------------- */

        // DAR FORMATO

        $valor = Common::formato_precio($valor, $cantidad_decimal);

        /*  --------------------------------------------------------------------------------- */

        // PONER MONEDA ADELANTE

        $valor = $descripcion_moneda.' '.$valor;

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return $valor;

        /*  --------------------------------------------------------------------------------- */

    }

}