<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Imagen extends Model
{
    
    protected $connection = 'retail';
	protected $table = 'imagenes';

	public static function obtenerImagen($codigo)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */
           
        // OBTENER EL PRODUCTO

        $imagen = Imagen::select(DB::raw('PICTURE'))
        ->where('COD_PROD', '=', $codigo)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
        
        if (count($imagen) > 0) {
            return ['imagen' => "data:image/jpg;base64,".base64_encode($imagen[0]->PICTURE)];
        } else {
            return ['imagen' => false];
        }

        /*  --------------------------------------------------------------------------------- */

    }
}
