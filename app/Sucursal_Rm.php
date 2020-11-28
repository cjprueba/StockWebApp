<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Sucursal_Rm extends Model
{
	protected $connection = 'retail';
    protected $table = 'Sucursal_Rm';
    public $timestamps = false;
    //
        public static function crear_sucursal($datos)
    {
    	try {
        DB::connection('retail')->beginTransaction();
      
        $user = auth()->user();
        $dia = date("Y-m-d H:i:s");
         $Sucursal_Rm=Sucursal_Rm::insertGetId(
         ['DESCRIPCION'=> $datos['data']['DESCRIPCION'], 
         ['DESC_CORTA'=> $datos['data']['DESC_CORTA'], 
         'USER'=>$user->id,
         'FECALTAS'=>$dia);
            DB::connection('retail')->commit();

            return ["response"=>true,"sucursal_id"=>$Sucursal_Rm];
    	} catch (Exception $e) {
    		 DB::connection('retail')->rollBack();
        throw $e;
         return ["response"=>false,"statusText"=>"No se pudo guardar el Sucursal_Rm!"];
    	}
      
    }
}
