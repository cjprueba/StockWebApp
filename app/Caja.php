<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Caja extends Model
{
	protected $connection = 'retail';
	protected $table = 'cajas';
  public $timestamps = false;

  public static function caja_obtener($datos){
  	/*var_dump($datos);*/
      /*  --------------------------------------------------------------------------------- */
     $user = auth()->user();
      // OBTENER TODOS LOS DATOS DEL TALLE
      $caja = Caja::select(DB::raw('CAJA, CANTIDAD_PERSONALIZADA, CANTIDAD_TICKET, RECARGAR'))->where('ID_SUCURSAL','=',$user->id_sucursal)->Where('IP','=',$datos['id'])->get()->toArray();
      if(count($caja)<=0){
         return ["response"=>false];
      }
  
      // RETORNAR EL VALOR

     return ["response"=>true,"caja"=>$caja];

      /*  --------------------------------------------------------------------------------- */
  }


  public static function asignar_Caja($datos){
    $user = auth()->user();
    $cajaSelec=$datos['data']['CODIGO'];
    try{
      DB::connection('retail')->beginTransaction();
      if($datos['data']['btn_asignar']==true){
        $caja=Caja::insertGetId([
          'IP'=>$datos['data']['IP_PC'],
          'CAJA'=>$cajaSelec,
          'ID_SUCURSAL'=>$user->id_sucursal,
          'CANTIDAD_PERSONALIZADA'=>$datos['data']['CANTIDAD_PERSONALIZADA'],
          'CANTIDAD_TICKET'=>$datos['data']['CANTIDAD_TICKET'],
          'RECARGAR'=>$datos['data']['RECARGAR']]);
        DB::connection('retail')->commit();
        return ["response"=>true];
      }else{
        $caja=Caja::where('ID_SUCURSAL','=',$user->id_sucursal)
          ->where('CAJA','=',$cajaSelec)
          ->update(['CANTIDAD_PERSONALIZADA'=>$datos['data']['CANTIDAD_PERSONALIZADA'],
          'CANTIDAD_TICKET'=>$datos['data']['CANTIDAD_TICKET'],
          'RECARGAR'=>$datos['data']['RECARGAR']]);
        DB::connection('retail')->commit();
        return['response'=>true];
      }
    }catch(Exception $ex){
      DB::connection('retail')->rollBack();
      if($ex->errorInfo[1]==1062){
          return ["response"=>false,'statusText'=>'¡Estas opciones ya fueron registrados!'];
      }else{
          return ["response"=>false,'statusText'=>$ex->getMessage()];
      }
    }

  }



  public static function quitar($datos){
    $user = auth()->user();
    $cajaSelec=$datos['data']['CODIGO'];
    if($datos['data']['btn_asignar']==false){
      $caja=Caja::where('CAJA','=',$cajaSelec)
                ->where('ID_SUCURSAL','=',$user->id_sucursal)
                ->delete();
      return ['response'=>true];
    }else{
      return ['response'=>false, 'statusText'=> 'No se encuntra ocupado esta caja '];
    }
  }



  public static function existe_Caja($datos){
    $user = auth()->user();
    $exiseCaja = Caja::select(DB::raw('CAJA'))
      ->where('ID_SUCURSAL','=',$user->id_sucursal)
      ->where('CAJA','=',$datos)
      ->limit(1)
      ->get();
    if(count($exiseCaja)>0 && !empty($datos)){
      return  ["response"=>false];
    }else{
      return ["response"=>true];
    }
  }
}
