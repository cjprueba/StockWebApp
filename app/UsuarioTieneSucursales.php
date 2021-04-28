<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Sucursal;

class UsuarioTieneSucursales extends Model
{
	protected $connection = 'retail';
    protected $table = 'users_tiene_sucursales';
    public $timestamps = false;


    public static function obtener_sucursales()
    {
    	/*  --------------------------------------------------------------------------------- */
        $user = auth()->user();

        $sucursal = UsuarioTieneSucursales::select(DB::raw('SUCURSALES.CODIGO AS ID, SUCURSALES.DESCRIPCION'))
        ->leftjoin('SUCURSALES', 'SUCURSALES.CODIGO', '=', 'USERS_TIENE_SUCURSALES.ID_SUCURSAL')
        ->where('ID_USER', '=', $user->id)
        ->get();

         $sucursalActual=Sucursal::select(DB::raw('SUCURSALES.CODIGO AS ID, SUCURSALES.DESCRIPCION'))
         ->where('CODIGO', '=', $user->id_sucursal)
         ->get();
        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if (count($sucursal)>0) {
            return ['sucursal' => $sucursal, 'sucursal_actual'=>$sucursalActual];
        } else {
            return ['sucursal' => 0, 'sucursal_actual'=>0];
        }
    }

    public static function cambiar_user_sucursal($datos){
        $user= auth()->user();
        // var_dump($datos);
        $codSucursalSeleccionado=$datos['data']['seleccion_sucursal']['ID'];
        try{
            DB::connection('retail')->beginTransaction();
            $sucursal=User::where('id','=',$user->id)->update(['id_sucursal'=>$codSucursalSeleccionado]);
            DB::connection('retail')->commit();
            return["response"=>true];
        }catch(Exception $ex){
            DB::connection('retail')->rollBack(); 
            if($ex->errorInfo[1]===1062){
                return ["response"=>false,'statusText'=>'No se porque pero Error!!'];
            }else{
                return ["response"=>false,'statusText'=>$ex->getMessage()];
            }
        }
    }
}
