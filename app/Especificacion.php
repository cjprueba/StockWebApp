<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\EspecificacionAux;
class Especificacion extends Model
{
    //
    protected $connection = 'retail';
    protected $table = 'especificaciones';
     public static function obtener_avisos()
    {

        $user = auth()->user();
	    $dia = date('Y-m-d');
        /*  --------------------------------------------------------------------------------- */
     
        // OBTENER TODOS LOS DATOS DEL AVISO
        $Especificacion = Especificacion::select(DB::raw(
           'Especificaciones.ERRORES,
        	Especificaciones.CODIGO,
        	Especificaciones.FUNCIONES, 
        	Especificaciones.FECHAESPECIFICACION,
        	Especificaciones.VERSION,
            Especificaciones.FILTRAR,
            Especificaciones.ID_SUCURSAL'))
        ->Where('ESPECIFICACIONES.FECHAESPECIFICACION','=',$dia)
        ->get()
        ->toArray();

        if(count($Especificacion)<=0){
           return ["response"=>false];
        }
        if(($user->id_sucursal!=$Especificacion[0]["ID_SUCURSAL"]) && $Especificacion[0]["FILTRAR"]==1){
            return ["response"=>false];
        }

        $user_aviso=EspecificacionAux::select(DB::raw(
           'FK_USER,
            ACTIVO'))
        ->Where('FK_USER','=',$user->id)
        ->Where('CODIGO','=',$Especificacion[0]["CODIGO"])
        ->get()
        ->toArray();
        if(count($user_aviso)<=0){
          $avisos_users = EspecificacionAux::insertGetId([
                'FK_USER' => $user->id,
                'CODIGO' => $Especificacion[0]["CODIGO"],
                'FECHAESPECIFICACION' => $Especificacion[0]["FECHAESPECIFICACION"],
                'ACTIVO' => 0,
                'ID_SUCURSAL' => $user->id_sucursal
            ]);
          $activo=0;
        }else{
            $activo=$avisos_users[0]["ACTIVO"];
        }

        


        // RETORNAR EL VALOR

       return ["response"=>true,"aviso"=>$Especificacion, "activo"=>$activo];

        /*  --------------------------------------------------------------------------------- */

    }
    
}
