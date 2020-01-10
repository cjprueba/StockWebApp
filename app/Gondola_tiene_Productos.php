<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gondola_tiene_Productos extends Model
{
   	
   	protected $connection = 'retail';
    protected $table = 'gondola_tiene_productos';
    
   	public static function asignar_gondolas($codigo, $gondolas){

        /*  --------------------------------------------------------------------------------- */

        // INSERTAR REFERENCIA 
        
        foreach ($gondolas as $key => $value) {
        	Gondola_tiene_Productos::insert([
        		'GONDOLA_COD_PROD' => $codigo,
        		'ID_GONDOLA' => $value['ID']
        	]);
        }

        /*  --------------------------------------------------------------------------------- */

    }

}
