<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Servicios extends Model
{

    protected $connection = 'retail';
    protected $table = 'servicios';
    public $timestamps = false;

    public static function servicios_pos($datos){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $codigo = $datos["codigo"];

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE CODIGO PRODUCTO O CODIGO INTERNO

        $producto = Servicios::
        ->select(DB::raw('DESCRIPCION'))
        ->where($filtro, '=', $codigo)
        ->where('PRODUCTOS_AUX.ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
        
        if (count($producto) > 0) {

            /*  --------------------------------------------------------------------------------- */

            // OBTENER GONDOLAS 

            $gondola = Gondola::obtener_gondolas_por_producto($codigo);
            
            $producto[0]["GONDOLAS"] = $gondola['gondolas'];
            $producto[0]["AUTODESCRIPCION"] = false;

            /*  --------------------------------------------------------------------------------- */

            // IMAGEN 

            $imagen = Imagen::obtenerImagen($codigo);

            /*  --------------------------------------------------------------------------------- */

            // RETORNAR VALOR 
            if(count($online)== 0){
               // var_dump("entre 1");

                return ["response" => true, "producto" => $producto[0], "online" => 0, "imagen" => $imagen["imagen"]];
            }else{
               // var_dump("entre 2");
                return ["response" => true, "producto" => $producto[0], "online" => $online[0], "imagen" => $imagen["imagen"]];
            }

            

            /*  --------------------------------------------------------------------------------- */

        } else {
            return ["response" => false];
        }

        /*  --------------------------------------------------------------------------------- */

    }
}
