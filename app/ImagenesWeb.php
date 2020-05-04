<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImagenesWeb extends Model
{
    protected $connection = 'retail';
	protected $table = 'imagenes_web';

	public static function obtenerImagen($codigo)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
        $filename = 'SinImagen.png';

        /*  --------------------------------------------------------------------------------- */

        // CONVERT IMAGE DEFAULT TO BLOB 

        $path = '../storage/app/imagenes/product.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $dataDefaultImage = file_get_contents($path);

        /*  --------------------------------------------------------------------------------- */

        // OBTENER EL PRODUCTO
        
        $imagen = ImagenesWeb::select('PICTURE')
        ->where('COD_PROD', '=', $codigo)
        ->limit(1)
        ->get()
        ->toArray();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
       	
       	if (file_exists('images/'.$filename)) {
 			return true;
 		} else {
 			return false;
 		}

        if (count($imagen) > 0) {
            return ['imagen' => "data:image/jpg;base64,".base64_encode($imagen[0]["PICTURE"])];
        } else {
            return ['imagen' => "data:image/jpg;base64,".base64_encode($dataDefaultImage)];
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function obtenerImagenCarpeta($codigo)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 
       
        $address = 'http://131.196.192.165:8090/';

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
       	
       	if (file_exists('images/'.$codigo.'.jpg')) {
 			return $address.'images/'.$codigo.'.jpg';
 		} else {
 			return $address.'images/SinImagen.png';
 		}

        /*  --------------------------------------------------------------------------------- */

    }
}
