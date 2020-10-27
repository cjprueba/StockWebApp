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
        $autorizacion = User_Supervisor::select(DB::raw('ID,FK_USER, CODIGO'))->where('ID_SUCURSAL','=',$user->id_sucursal)->Where('CODIGO','=',$datos['codigo'])->get()->toArray();
        if(count($autorizacion)<=0){
           return ["response"=>false,"statusText"=>"Esta sucursal no posee este codigo de autorizacion! Consulte con su Gerente."];
        }
    
        // RETORNAR EL VALOR

       return ["response"=>true,"autorizacion"=>$autorizacion];

        /*  --------------------------------------------------------------------------------- */

    }
            public static function guardar_referencia($data){

    	try {

	    	/*  --------------------------------------------------------------------------------- */

	    	$autorizacion = User_Supervisor::insertGetId([
	    		'FK_VENTA' => $data["FK_VENTA"],
	    		'FK_USER'=> $data["FK_USER"],
	    		'FK_USER_SUPERVISOR' => $data["FK_USER_SUPERVISOR"]
	    	]);


	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Usuario Supervisor: Éxito al guardar y confirmar.', ['VENTA' => $data["FK_VENTA"], 'FK_USER' => $autorizacion]);

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Usuario Supervisor: Error al guardar y confirmar.', ['VENTA' => $data["FK_VENTA"]]);

			/*  --------------------------------------------------------------------------------- */

		}
    }
}
