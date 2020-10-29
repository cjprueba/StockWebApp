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
    protected $table = 'especificacion';
     public static function obtener_avisos()
    {
        try{
             DB::connection('retail')->beginTransaction();
        $user = auth()->user();
        $dia = date('Y-m-d');
        /*  --------------------------------------------------------------------------------- */
     
        // OBTENER TODOS LOS DATOS DEL AVISO
        $Especificacion = Especificacion::select(DB::raw(
           'ESPECIFICACION.ERRORES,
        	ESPECIFICACION.CODIGO,
        	ESPECIFICACION.FUNCIONES, 
        	ESPECIFICACION.FECHAESPECIFICACION,
        	ESPECIFICACION.VERSION,
            ESPECIFICACION.FILTRAR,
            ESPECIFICACION.ID_SUCURSAL'))
        ->Where('ESPECIFICACION.FECHAESPECIFICACION','=',$dia)
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
            $activo=$user_aviso[0]["ACTIVO"];
        }

        


        // RETORNAR EL VALOR
          DB::connection('retail')->commit();

       return ["response"=>true,"aviso"=>$Especificacion, "activo"=>$activo];
        }catch (Exception $e) {
               DB::connection('retail')->rollBack();
                return ["response"=>false];

        }

        

        /*  --------------------------------------------------------------------------------- */

    }
     public static function aceptar_terminos($datos)
    {
          try{
             $dia = date("Y-m-d H:i:s");

             $user = auth()->user();
               DB::connection('retail')->beginTransaction();

            $user_aviso=EspecificacionAux::where('CODIGO', $datos["codigo"])->where('FK_USER','=',$user->id)
            ->update(['ACTIVO'=>1,'FECHA_CONFIRMACION'=>$dia]);


                 DB::connection('retail')->commit();
                 return["response"=>true];
          }catch (Exception $e){
               DB::connection('retail')->rollBack();
                return ["response"=>false];
          }
    }

}
