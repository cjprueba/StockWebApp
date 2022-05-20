<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UsersTieneSucursales extends Model
{
    protected $connection = 'retail';
    protected $table = 'users_tiene_sucursales';
    public $timestamps = false;
    
    public static function guardar_sucursales($user,$sucursal, $aux){
    	try {

	    	/*  --------------------------------------------------------------------------------- */
            if ($aux==1) {
                $empleado = UsersTieneSucursales::where('ID_USER', '=', $user)->delete();
            }
	    	$userSucursales = UsersTieneSucursales::insertGetId([
	    		'ID_USER' => $user,
	    		'ID_SUCURSAL'=> $sucursal
	    	]);


	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Éxito al guardar.');

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Error al guardar.');

			/*  --------------------------------------------------------------------------------- */

		}
    }

    public static function obtener_user_sucursales($datos){
        $user = auth()->user();

        // OBTENER TODAS LAS SUCURSALES

        $sucursalesUser = UsersTieneSucursales::select('ID_SUCURSAL')
        					->where('ID_USER', '=', $datos) 
                            ->get()
                            ->toArray();
        // RETORNAR EL VALOR
       return $sucursalesUser;

        /*  --------------------------------------------------------------------------------- */
    }
}
