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
            $imagen_producto = ''.env("URL_FILE").'/storage/imagenes/productos/'.$codigo.'.jpg';
            $imagen_producto_external = ''.env("URL_FILE_EXTERNAL").'/storage/imagenes/productos/'.$codigo.'.jpg';
        } else {
            $imagen_producto = ''.env("URL_FILE").'/storage/imagenes/productos/product.png';
            $imagen_producto_external = ''.env("URL_FILE_EXTERNAL").'/storage/imagenes/productos/'.$codigo.'.jpg';
        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
       
        return ['imagen' => "<img src='".$imagen_producto."' id='myImg'  class='card-img-top'>", 'imagen_2' => "<img src='".$imagen_producto."' class='block' id='u255_img' width='100%'>", 'imagen_3' => "<img src='".$imagen_producto."' id='myImg'  style='width:80px;height:80px;'>", 'imagen_external' => "<img src='".$imagen_producto_external."' class='block' id='u255_img' width='100%'>"];

        /*  --------------------------------------------------------------------------------- */

    }


    public static function obtenerImagenURL_Empleado($codigo)
    {

        /*  --------------------------------------------------------------------------------- */

        /*  --------------------------------------------------------------------------------- */

        // CONVERT IMAGE DEFAULT TO BLOB 

        $filename = '../storage/app/public/imagenes/empleados/'.$codigo.'.jpg';
                
        if(file_exists($filename)) {
            $imagen_empleado = ''.env("URL_FILE").'/storage/imagenes/empleados/'.$codigo.'.jpg';
            $imagen_empleado_external = ''.env("URL_FILE_EXTERNAL").'/storage/app/public/imagenes/empleados/'.$codigo.'.jpg';
        } else {
            $imagen_empleado = ''.env("URL_FILE").'/storage/imagenes/empleados/empleado.png';
            $imagen_empleado_external = ''.env("URL_FILE_EXTERNAL").'/storage/app/public/imagenes/empleados/'.$codigo.'.jpg';
        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
       
        return ['imagen' => "<img src='".$imagen_empleado."' id='myImg'  class='card-img-top'>", 'imagen_2' => "<img src='".$imagen_empleado."' class='block' id='u255_img' width='100%'>", 'imagen_3' => "<img src='".$imagen_empleado."' id='myImg'  style='width:80px;height:80px;'>", 'imagen_external' => "<img src='".$imagen_empleado_external."' class='block' id='u255_img' width='100%'>"];

        /*  --------------------------------------------------------------------------------- */

    }


    public static function guardar_imagen($data){

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

    public static function guardar_imagen_storage($data){

        try {
            
            /*  --------------------------------------------------------------------------------- */

            $file = '../storage/app/public/imagenes/productos/'.$data["COD_PROD"].'.jpg';
            $handle=fopen($file, 'a+');
            fwrite($handle, base64_decode($data["PICTURE"]));

            /*  --------------------------------------------------------------------------------- */

            Log::info('Imagen: Éxito al guardar imagen en storage.', ['PRODUCTO' => $data["COD_PROD"]]);

            /*  --------------------------------------------------------------------------------- */

        } catch (Exception $e) {

            /*  --------------------------------------------------------------------------------- */

            // ERROR 

            Log::error('Imagen: Error al guardar.', ['PRODUCTO' => $data["COD_PROD"]]);

            /*  --------------------------------------------------------------------------------- */

        }
    }


    public static function guardar_imagen_empleado($data){

        try {
            
            /*  --------------------------------------------------------------------------------- */
            $file = '../storage/app/public/imagenes/empleados/'.$data['CODIGO'].'.jpg';
            $handle=fopen($file, 'a+');
            fwrite($handle, base64_decode($data["PICTURE"]));

            /*  --------------------------------------------------------------------------------- */

            Log::info('Imagen: Éxito al guardar imagen en storage.', ['EMPLEADO' => $data['CODIGO']]);

            /*  --------------------------------------------------------------------------------- */

        } catch (Exception $e) {

            /*  --------------------------------------------------------------------------------- */

            // ERROR 

            Log::error('Imagen: Error al guardar.', ['PRODUCTO' => $data["CODIGO"]]);

            /*  --------------------------------------------------------------------------------- */

        }
    }




    public static function obtenerLogoURL()
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER PARAMETRO

        $parametro = Parametro::select(DB::raw('NOMBRE_LOGO'))
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // LOGO

        $imagen = '../storage/app/public/imagenes/tiendas/'.$parametro[0]->NOMBRE_LOGO.'';
           
        if(!file_exists($imagen)) {
            $imagen = 0;
        } else {
            $imagen = "<img src='".''.env("URL_FILE").'/storage/imagenes/tiendas/'.$parametro[0]->NOMBRE_LOGO.''."' width='100%' >";
        } 


        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
       
        return ['imagen' => $imagen];

        /*  --------------------------------------------------------------------------------- */

    }
    public static function obtenerLogoDireccion()
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER PARAMETRO

        $parametro = Parametro::select(DB::raw('NOMBRE_LOGO'))
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // LOGO

        $imagen = '../storage/app/public/imagenes/tiendas/'.$parametro[0]->NOMBRE_LOGO.'';
           
        if(!file_exists($imagen)) {
            $imagen = 0;
            
        } 
            return ['imagen' => $imagen];
           /* $imagen = "<img src='".''.env("URL_FILE").'/storage/imagenes/tiendas/'.$parametro[0]->NOMBRE_LOGO.''."' width='100%' >";*/
        


        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
       
        /*return ['imagen' => $imagen];*/

        /*  --------------------------------------------------------------------------------- */

    }

        
}
