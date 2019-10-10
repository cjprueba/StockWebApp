<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

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

}