<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class Gondola_Tiene_Seccion extends Model{
	protected $connection = 'retail';
    protected $table = 'gondola_tiene_seccion';
    public $timestamps = false;

    public static function asignar_seccion($Id_gondola, $Id_seccion, $Id_sucursal){
    	try{
    		DB::connection('retail')->beginTransaction();
    		$Seccion = Gondola_Tiene_Seccion::insertGetId([
    			'ID_GONDOLA'=>$Id_gondola,
    			'ID_SECCION'=>$Id_seccion,
    			'ID_SUCURSAL'=>$Id_sucursal]);
    		Log::info('Seccion Asignar: Éxito al guardar gondola tiene seccion.', ['GONDOLA' => $Id_gondola, 'SECCION' => $Id_seccion]);
        	DB::connection('retail')->commit();
    	}catch(Exception $ex){
	    	DB::connection('retail')->rollBack();
	        Log::info('Seccion Asignar: Error al guardar gondola tiene sección.', ['GONDOLA' => $Id_gondola, 'SECCION' => $Id_seccion]);
	    }
    }

    public static function modificar_seccion($Id_seccion, $Id_gondola, $Id_sucursal){
    	try{
    		DB::connection('retail')->beginTransaction();
    		if($Id_seccion=='null' || $Id_seccion==''){
                    gondola_tiene_seccion::where('ID_GONDOLA','=',$Id_gondola)->delete();
                }
                else{
                    $existeSeccion= gondola_tiene_seccion::select('ID_GONDOLA')
                    ->where('ID_GONDOLA','=', $Id_gondola)
                    ->get()
                    ->toArray();
                    if(count($existeSeccion)>0){
                        $gondola_seccion=gondola_tiene_seccion::where('ID_GONDOLA', '=', $Id_gondola )
                        ->update([
                        'ID_SECCION'=>$Id_seccion 
                        ]);
                        
                    }else{
                        Gondola_Tiene_Seccion::asignar_seccion($Id_gondola, $Id_seccion ,$Id_sucursal);
                    }
		    		DB::connection('retail')->commit();
    		}
    	}catch(Exception $ex){
	        DB::connection('retail')->rollBack();
	        Log::info('Seccion Asignar: Error al modificargondola tiene seccion.', ['GONDOLA' => $Id_gondola, 'SECCION' => $Id_seccion]);
	    }
    }
}
