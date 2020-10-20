<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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

        // CONVERT IMAGE DEFAULT TO BLOB 

        $path = '../storage/app/imagenes/product.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $dataDefaultImage = file_get_contents($path);

        /*  --------------------------------------------------------------------------------- */

        // OBTENER EL PRODUCTO
        //var_dump($codigo);
        $imagen = Imagen::select('Picture')
        ->where('COD_PROD', '=', $codigo)
        ->get()
        ->toArray();
        //$imagen = Imagen::select(DB::raw('Picture'))
        //->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
       
        if (count($imagen) > 0) {
            //var_dump("entre aqui 1");
            return ['imagen' => "data:image/jpg;base64,".base64_encode($imagen[0]["Picture"])];
            //return ['imagen' => "data:image/jpg;base64,".base64_encode($imagen[0]->PICTURE)];
        } else {
            return ['imagen' => "data:image/jpg;base64,".base64_encode($dataDefaultImage)];
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function guardar($data){

        try {
            
            /*  --------------------------------------------------------------------------------- */

            // $imagen = Imagen::insertGetId([
            //     'COD_PROD' => $data["COD_PROD"],
            //     'CODIGO_INTERNO' => $data["CODIGO_INTERNO"],
            //     'PICTURE' => base64_decode($data["PICTURE"])
            // ]);

            $imagen = Imagen::updateOrInsert(
                  ['COD_PROD' => $data["COD_PROD"]],
                  ['CODIGO_INTERNO' => $data["CODIGO_INTERNO"], 'PICTURE' => base64_decode($data["PICTURE"])]
            );

            /*  --------------------------------------------------------------------------------- */

            Log::info('Imagen: Éxito al guardar.', ['PRODUCTO' => $data["COD_PROD"], 'ID' => $imagen]);

            /*  --------------------------------------------------------------------------------- */

        } catch (Exception $e) {

            /*  --------------------------------------------------------------------------------- */

            // ERROR 

            Log::error('Imagen: Error al guardar.', ['PRODUCTO' => $data["COD_PROD"]]);

            /*  --------------------------------------------------------------------------------- */

        }
    }

    public static function obtenerImagenURL($codigo)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CONVERT IMAGE DEFAULT TO BLOB 

        $filename = '../storage/app/public/imagenes/productos/'.$codigo.'.jpg';
                
        if(file_exists($filename)) {
            $imagen_producto = 'http://172.16.249.20:8080/storage/imagenes/productos/'.$codigo.'.jpg';
        } else {
            $imagen_producto = 'http://172.16.249.20:8080/storage/imagenes/productos/product.png';
        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
       
        return ['imagen' => "<img src='".$imagen_producto."'  class='card-img-top'>"];

        /*  --------------------------------------------------------------------------------- */

    }


}
