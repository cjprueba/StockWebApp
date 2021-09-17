<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Gondola_Tiene_Sector extends Model
{
    protected $connection = 'retail';
    protected $table = 'gondola_tiene_sectores';
    public $timestamps = false;
      public static function guardar_referencia($datos){

        $user = auth()->user();
        
      
            //var_dump($datos['data']['Marcados']);
        try { 
            DB::connection('retail')->beginTransaction();
        /*  --------------------------------------------------------------------------------- */
  				Gondola_Tiene_Sector::insert(
            [
                'FK_GONDOLA' => $datos["FK_GONDOLA"],
                'FK_SECTOR' => $datos["FK_SECTOR"]
            ]
        	);
             DB::connection('retail')->commit();
             
           
        /*  --------------------------------------------------------------------------------- */
        }catch(Exception $ex){
            DB::connection('retail')->rollBack(); 
             return ["response"=>false,'statusText'=>$ex->getMessage()];
           
        }


    }
    public static function eliminar_referencia($datos){

        $user = auth()->user();
        
      
            //var_dump($datos['data']['Marcados']);
        try { 
            DB::connection('retail')->beginTransaction();
        /*  --------------------------------------------------------------------------------- */
                Gondola_Tiene_Sector::where(
            [
                'FK_GONDOLA' => $datos["FK_GONDOLA"],
                'FK_SECTOR' => $datos["FK_SECTOR"]
            ]
            )->delete();
             DB::connection('retail')->commit();
             
           
        /*  --------------------------------------------------------------------------------- */
        }catch(Exception $ex){
            DB::connection('retail')->rollBack(); 
             return ["response"=>false,'statusText'=>$ex->getMessage()];
           
        }


    }
}
