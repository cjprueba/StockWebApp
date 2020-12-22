<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Gondola_tiene_Productos extends Model
{
   	
   	protected $connection = 'retail';
    protected $table = 'gondola_tiene_productos';
    
   	public static function asignar_gondolas($codigo, $gondolas){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INSERTAR REFERENCIA 
        
        if ($gondolas === "null") {
          return ['response' => false];
        }

        try {
        
          if (count($gondolas[0]) > 0) {

            foreach ($gondolas as $key => $value) {

              /*  --------------------------------------------------------------------------------- */

              // INSERTAR GONDOLAS 

            	$gondola = Gondola_tiene_Productos::insertGetId([
            		'GONDOLA_COD_PROD' => $codigo,
            		'ID_GONDOLA' => $value['ID'],
                'ID_SUCURSAL' => $user->id_sucursal
            	]);

              /*  --------------------------------------------------------------------------------- */

              Log::info('Gondola Asignar: Éxito al guardar.', ['PRODUCTO' => $codigo, 'ID' => $gondola]);

              /*  --------------------------------------------------------------------------------- */

            }

          } else {
            return ["response" => false];
          }

        } catch (Exception $e) {
          
          /*  --------------------------------------------------------------------------------- */

          // ERROR 

          Log::error('Gondola Asignar: Error al guardar.', ['PRODUCTO' => $codigo]);

          /*  --------------------------------------------------------------------------------- */

        }

        /*  --------------------------------------------------------------------------------- */

    }


    public static function modificar_asignar_gondolas($codigo, $gondolas){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $dia = date('Y-m-d H:i:s');
        
        if ($gondolas === "null") {
          return ['response' => false];
        }

        /*  --------------------------------------------------------------------------------- */

        // MODIFICAR REFERENCIA 
        
        try {
          
          if (count($gondolas[0]) > 0) {

            // ELIMINAR GONDOLAS NO ASIGNADAS 

            Gondola_tiene_Productos::where('GONDOLA_COD_PROD', '=', $codigo)
              ->where('ID_SUCURSAL', '=', $user->id_sucursal)
              ->delete();

            foreach ($gondolas as $key => $value) {

              /*  --------------------------------------------------------------------------------- */

              // INSERTAR GONDOLAS 

              $gondola = Gondola_tiene_Productos::updateOrInsert(
                  ['GONDOLA_COD_PROD' => $codigo, 'ID_GONDOLA' => $value['ID']],
                  ['FECMODIF' => $dia, 'ID_SUCURSAL' => $user->id_sucursal]
              );

              /*  --------------------------------------------------------------------------------- */

              Log::info('Gondola Asignar: Éxito al modificar.', ['PRODUCTO' => $codigo, 'ID GONDOLA' => $value['ID']]);

              /*  --------------------------------------------------------------------------------- */

            }

            /*  --------------------------------------------------------------------------------- */

          } else {
            return ["response" => false];
          }

        } catch (Exception $e) {
          
          /*  --------------------------------------------------------------------------------- */

          // ERROR 

          Log::error('Gondola Asignar: Error al modificar.', ['PRODUCTO' => $codigo]);

          /*  --------------------------------------------------------------------------------- */

        }

        /*  --------------------------------------------------------------------------------- */

    }

}
