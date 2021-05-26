<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Parametro extends Model
{

    /*  --------------------------------------------------------------------------------- */

    // DEFINIR CONECCION Y TABLA 

    protected $connection = 'retail';
    protected $table = 'parametros';

    /*  --------------------------------------------------------------------------------- */
    
     public static function mostrarParametro()
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER LOS DATOS DEL USUARIO LOGUEADO 

    	$user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER PARAMETROS

    	$parametros = DB::connection('retail')
        ->table('parametros')
        ->select(DB::raw('parametros.MONEDA, MONEDAS.DESCRIPCION, MONEDAS.CANDEC, parametros.TAB_UNICA, parametros.ID_SUCURSAL'))
        ->join('MONEDAS', 'parametros.MONEDA', '=', 'MONEDAS.CODIGO')
        ->where('parametros.ID_SUCURSAL','=',$user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($parametros) {
        	return ['parametros' => $parametros];
        } else {
        	return ['parametros' => 0];
        }

        /*  --------------------------------------------------------------------------------- */
    }

    public static function tab_unica()
    {
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER PARAMETROS

        $parametros = DB::connection('retail')
        ->table('parametros')
        ->select(DB::raw('TAB_UNICA'))
        ->where('ID_SUCURSAL','=',$user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if (count($parametros) > 0) {
            return $parametros[0]->TAB_UNICA;
        } else {
            return 0;
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function candec($moneda){

        /*  --------------------------------------------------------------------------------- */

        // BUSCAR CANTIDAD DE DECIMALES DE MONEDA 

        $candec = DB::connection('retail')
        ->table('monedas')
        ->select(DB::raw('CANDEC, DESCRIPCION'))
        ->where('CODIGO','=', $moneda)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        if (count($candec) > 0){
            return ["CANDEC" => $candec[0]->CANDEC, "DESCRIPCION" => $candec[0]->DESCRIPCION];
        } else {
            return ["CANDEC" => 0, "DESCRIPCION" => 'N/A'];
        }

        /*  --------------------------------------------------------------------------------- */
    }

    public static function consultaPersonalizada($columnas){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER PARAMETROS

        $parametros = DB::connection('retail')
        ->table('PARAMETROS')
        ->select(DB::raw($columnas))
        ->where('ID_SUCURSAL','=',$user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if (count($parametros) > 0) {
            return $parametros[0];
        } else {
            return 0;
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function mayoristaCantidad()
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER PARAMETROS

        $parametros = Parametro::select(DB::raw('parametros.DESTINO'))
        ->where('parametros.ID_SUCURSAL','=',$user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($parametros) {
            return ['MAYORISTA' => $parametros[0]["DESTINO"]];
        } else {
            return ['MAYORISTA' => false];
        }

        /*  --------------------------------------------------------------------------------- */
    } 
    public static function obtener_Porcentaje_Stock_Minimo($id_sucursal){
            $Parametro=Parametro::Select(DB::raw('IFNULL(STOCK_MINIMO_PORCENTAJE,0) as PORCENTAJE'))
            ->where('ID_SUCURSAL','=',$id_sucursal)
            ->get()
            ->toArray();
            return $Parametro[0]['PORCENTAJE'];
    }   
}
