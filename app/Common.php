<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

use App\Parametro;
use App\Stock;

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

    public static function comprobarCantidadLimiteStock($codigo, $cantidad){

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI SUPERA STOCK

        $stock = Stock::obtener_stock($codigo);

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $response = true;

        /*  --------------------------------------------------------------------------------- */

        if ($cantidad > $stock["stock"]) {
            $response = false;
        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR

        if ($response === true) {
            return ["response" => $response, "statusText" => 'La cantidad no supera el STOCK'];
        } else {
            return ["response" => $response, "statusText" => 'La cantidad supera el STOCK'];
        }
        

        /*  --------------------------------------------------------------------------------- */
    }

    public static function calculoPrecio($valor_precio, $valor_mayorista, $cantidad, $porcentaje, $linea, $marca) {

        /*  --------------------------------------------------------------------------------- */
        
        // USUARIO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        $tipo = 1;
        $precio_unit = 0.00;
        $precio = 0.00;
        $descuento_unit = 0.00;
        $descuento_categoria = 0;

        /*  --------------------------------------------------------------------------------- */

        // DESCUENTO CATEGORIA 

        $descuento_categoria = LineasDescuento::obtener_descuento($linea, $user->id_sucursal);

        /*  --------------------------------------------------------------------------------- */

        // DESCUENTO MANUAL POR PRODUCTO

        $descuento_marca = MarcaAux::obtener_descuento($marca, $user->id_sucursal);

        /*  --------------------------------------------------------------------------------- */

        // CALCULAR PRECIOS

        if ($porcentaje < 30) {

            // CALCULAR MAYORISTA

            $mayorista = Parametro::mayoristaCantidad();

            if ($cantidad >= $mayorista["MAYORISTA"] && $mayorista["MAYORISTA"] !== false) {

                //  PRECIO MAYORISTA
                
                $precio_unit = $valor_mayorista;
                $precio = Common::quitar_coma($valor_mayorista, 2) * Common::quitar_coma($cantidad, 2);
                $tipo = 2;

            } else if ($descuento_marca > 0) {

               // DESCUENTO MARCA

               $descuento_unit = (Common::quitar_coma($valor_precio, 2) * Common::quitar_coma($descuento_marca, 2)) / 100;
               $precio_unit = Common::quitar_coma($valor_precio, 2) - $descuento_unit;
               $precio = $precio_unit * $cantidad;
               $tipo = 4;

            } else if ($descuento_categoria > 0) {

               // DESCUENTO CATEGORIA

               $descuento_unit = (Common::quitar_coma($valor_precio, 2) * Common::quitar_coma($descuento_categoria, 2)) / 100;
               $precio_unit = Common::quitar_coma($valor_precio, 2) - $descuento_unit;
               $precio = $precio_unit * $cantidad;
               $tipo = 5;

            } else {

                //  PRECIO NORMAL 

                $precio_unit = $valor_precio;
                $precio = Common::quitar_coma($valor_precio, 2) * Common::quitar_coma($cantidad, 2);

            }

        } else {

            // CALCULAR DESCUENTO POR PRODUCTO

            $descuento_unit = (Common::quitar_coma($valor_precio, 2) * Common::quitar_coma($porcentaje, 2)) / 100;
            $precio_unit = Common::quitar_coma($valor_precio, 2) - $descuento_unit;
            $precio = $precio_unit * $cantidad;
            $tipo = 3;

        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return ["valor" => $precio, "tipo" => $tipo, "precio_unit" => $precio_unit, "descuento_unit" => $descuento_unit];

        /*  --------------------------------------------------------------------------------- */

    }

}