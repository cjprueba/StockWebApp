<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class User_Supervisor extends Model
{
	protected $connection = 'retail';
	protected $table = 'users_supervisores';

    //

     public static function obtener_autorizacion($datos)
    {
    	/*var_dump($datos);*/
        /*  --------------------------------------------------------------------------------- */
       $user = auth()->user();
        // OBTENER TODOS LOS DATOS DEL TALLE
        $autorizacion = User_Supervisor::select(DB::raw('FK_USER, CODIGO'))->where('ID_SUCURSAL','=',$user->id_sucursal)->Where('CODIGO','=',$datos['codigo'])->get()->toArray();
        if(count($autorizacion)<=0){
           return ["response"=>false,"StatusText"=>"Esta sucursal no posee este codigo de autorizacion! Consulte con su Gerente."];
        }
    
        // RETORNAR EL VALOR

       return ["response"=>true,"autorizacion"=>$autorizacion];

        /*  --------------------------------------------------------------------------------- */

    }
}
