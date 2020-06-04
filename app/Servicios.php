<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Servicios extends Model
{

    protected $connection = 'retail';
    protected $table = 'services';
    public $timestamps = false;

    public static function servicios_pos($datos){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $codigo = substr($datos["codigo"], 1);

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE CODIGO PRODUCTO O CODIGO INTERNO

        $servicio = Servicios::select(DB::raw('CODIGO, DESCRIPCION, MANUAL_DESCRIPCION, MANUAL_PRECIO, IVA'))
        ->where('CODIGO', '=', $codigo)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
        
        if (count($servicio) > 0) {

        	/*  --------------------------------------------------------------------------------- */

            return ["response" => true, "servicio" => $servicio[0]];

            /*  --------------------------------------------------------------------------------- */

        } else {

        	/*  --------------------------------------------------------------------------------- */

            return ["response" => false];

            /*  --------------------------------------------------------------------------------- */
        }

        /*  --------------------------------------------------------------------------------- */

    }
}
