<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Caja extends Model
{
	protected $connection = 'retail';
	protected $table = 'cajas';

     public static function caja_obtener($datos)
    {
    	/*var_dump($datos);*/
        /*  --------------------------------------------------------------------------------- */
       $user = auth()->user();
        // OBTENER TODOS LOS DATOS DEL TALLE
        $caja = Caja::select(DB::raw('CAJA'))->where('ID_SUCURSAL','=',$user->id_sucursal)->Where('IP','=',$datos['id'])->get()->toArray();
        if(count($caja)<=0){
           return ["response"=>false];
        }
    
        // RETORNAR EL VALOR

       return ["response"=>true,"caja"=>$caja];

        /*  --------------------------------------------------------------------------------- */

    }
}
