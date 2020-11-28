<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Sector_Rm extends Model
{
    protected $connection = 'retail';
    protected $table = 'Sector_Rm';
    public $timestamps = false;
    public static function crear_sector($datos)
    {
    	try {
        DB::connection('retail')->beginTransaction();
      
        $user = auth()->user();
        $dia = date("Y-m-d H:i:s");
         $sector=Sector_Rm::insertGetId(
         ['DESCRIPCION'=> $datos['data']['DESCRIPCION'], 
         'DESC_CORTA'=> $datos['data']['DESC_CORTA'], 
         'USER'=>$user->id,
         'FECALTAS'=>$dia)];
            DB::connection('retail')->commit();

            return ["response"=>true,"sector_id"=>$sector];
    	} catch (Exception $e) {
    		 DB::connection('retail')->rollBack();
        throw $e;
         return ["response"=>false,"statusText"=>"No se pudo guardar el sector!"];
    	}
      
    }
    
}
