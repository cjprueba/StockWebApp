<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Empleado_Tiene_Gondola extends Model
{
    protected $connection = 'retail';
    protected $table = 'empleado_tiene_gondola';

    public static function asignar_gondola($datos, $fk_empleado){
    	try{
    		DB::connection('retail')->beginTransaction();
    		foreach ($datos as $key => $value) {
    		$gondola = Empleado_Tiene_Gondola::insertGetId([
    			'FK_GONDOLA'=>$value['ID'],
    			'FK_EMPLEADO'=>$fk_empleado]);
    		}

    	/*  --------------------------------------------------------------------------------- */

        Log::info('Gondola Asignar: Éxito al guardar empleado tiene gondola.', ['EMPLEADO' => $fk_empleado, 'GONDOLAS' => $datos]);
        DB::connection('retail')->commit();
	    }
	    catch(Exception $ex){
	        DB::connection('retail')->rollBack();
	        Log::info('Gondola Asignar: Error al guardar empleado tiene gondola.', ['EMPLEADO' => $fk_empleado, 'GONDOLAS' => $datos]);
	    }
	}
    public static function modificar_gondola($datos, $fk_empleado){
		try{
	    	DB::connection('retail')->beginTransaction();
	    	$gondola = Empleado_Tiene_Gondola::where('FK_EMPLEADO','=', $fk_empleado)->delete();

	    	if($datos!='null'||$datos!=[]){
	    		
	    			foreach ($datos as $key => $value) {

		    				$gondola = Empleado_Tiene_Gondola::insertGetId([
			    			'FK_GONDOLA'=>$value['ID'],
			    			'FK_EMPLEADO'=>$fk_empleado]);
	    			}
	    			Log::info('Gondola Asignar: Éxito al modificar empleado tiene gondola.', ['EMPLEADO' => $fk_empleado, 'GONDOLAS' => $datos]);
		    		DB::connection('retail')->commit();
	    		}
	    		
	    	}
    	catch(Exception $ex){
	        DB::connection('retail')->rollBack();
	        Log::info('Gondola Asignar: Error al modificar empleado tiene gondola.', ['EMPLEADO' => $fk_empleado, 'GONDOLAS' => $datos]);
	    }
    }
}
