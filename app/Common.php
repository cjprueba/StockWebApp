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

    public static function precio_candec_sin_letra($valor, $moneda){

        /*  --------------------------------------------------------------------------------- */

        // BUSCAR CANTIDAD DE DECIMALES DE MONEDA

        $candec = Parametro::candec($moneda);
                
        $cantidad_decimal = $candec["CANDEC"];

        /*  --------------------------------------------------------------------------------- */

        // DAR FORMATO

        $valor = Common::formato_precio($valor, $cantidad_decimal);

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return $valor;

        /*  --------------------------------------------------------------------------------- */

    }


    public static function calculo_iva($porcentaje, $total, $candec){

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $base5 = 0;
        $base10 = 0;
        $exenta = 0;
        $gravadas = 0;
        $impuesto = 0;
        $total = Common::quitar_coma($total, $candec);

        /*  --------------------------------------------------------------------------------- */

        // REVISAR IMPUESTO

        if ($porcentaje === 5) {

            /*  --------------------------------------------------------------------------------- */

            // BASE 5

            $base5 = $total / 21;
            $base5 = Common::quitar_coma($base5, $candec);

            /*  --------------------------------------------------------------------------------- */

        } else if ($porcentaje === 10) {

            /*  --------------------------------------------------------------------------------- */

            // BASE 10 

            $base10 = $total / 11;
            $base10 = Common::quitar_coma($base10, $candec);

            /*  --------------------------------------------------------------------------------- */

        } else {

            /*  --------------------------------------------------------------------------------- */

            // EXENTAS 

            $exenta = $total;

            /*  --------------------------------------------------------------------------------- */

        }

        /*  --------------------------------------------------------------------------------- */

        // IMPUESTO

        $impuesto = $base5 + $base10 + $exenta;

        /*  --------------------------------------------------------------------------------- */

        // GRAVADAS 

        $gravadas = $total - $impuesto;

        /*  --------------------------------------------------------------------------------- */

        //  RETORNAR VALOR

        return ["base5" => $base5, "base10" => $base10, "exentas" => $exenta, "gravadas" => $gravadas, "impuesto" => $impuesto];

        /*  --------------------------------------------------------------------------------- */

    }

}