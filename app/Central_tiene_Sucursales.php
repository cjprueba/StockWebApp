<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Central_tiene_Sucursales extends Model
{
    
    protected $connection = 'retail';
    protected $table = 'central_tiene_sucursales';

    public static function comprobar_sucursal($data){

    	/*  --------------------------------------------------------------------------------- */

    	$user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */

    	// SUCURSAL TIENE

    	$central = Central_tiene_Sucursales::select('ID')
    			->where('FK_CENTRAL', '=', $data["central"])
                ->where('FK_SUCURSAL', '=', $data["sucursal"])
                ->get();

    	/*  --------------------------------------------------------------------------------- */

    	if (count($central) > 0) {
    		return ["response" => true];
    	} else {
    		return ["response" => false];
    	}

    	/*  --------------------------------------------------------------------------------- */
    }
}
