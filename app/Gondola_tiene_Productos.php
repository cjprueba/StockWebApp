<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\ProductosAux;

class Gondola_tiene_Productos extends Model
{
   	
   	protected $connection = 'retail';
    protected $table = 'gondola_tiene_productos';
    
   	public static function asignar_gondolas($codigo, array $gondolas ){

        /*  --------------------------------------------------------------------------------- */
         
        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 
         

        $user = auth()->user();

        //OBTENER ID DE PRODUCTOS_AUX

        $ID_PRODUCTO = ProductosAux::Select('ID')->where('CODIGO','=',$codigo)->where('ID_SUCURSAL','=',$user->id_sucursal)->limit(1)->get()->toArray();

        /*  --------------------------------------------------------------------------------- */

        // INSERTAR REFERENCIA 
        
        if ($gondolas === "null" || $gondolas===NULL) {
          return ['response' => false];
        }

        try {
        
          if (isset($gondolas[0])) {

            foreach ($gondolas as $key => $value) {

              /*  --------------------------------------------------------------------------------- */

              // INSERTAR GONDOLAS 
           

            	$gondola = Gondola_tiene_Productos::insertGetId([
                'FK_PRODUCTOS_AUX'=> $ID_PRODUCTO[0]["ID"],
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
        $ID_PRODUCTO=ProductosAux::Select('ID')->where('CODIGO','=',$codigo)->where('ID_SUCURSAL','=',$user->id_sucursal)->limit(1)->get()->toArray();
        
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $dia = date('Y-m-d H:i:s');
       
        if ($gondolas === "null" || $gondolas===[]) {
          Gondola_tiene_Productos::where('GONDOLA_COD_PROD', '=', $codigo)
              ->where('ID_SUCURSAL', '=', $user->id_sucursal)
              ->delete();

            DB::connection('retail')->table('PRODUCTOS_TIENE_SECCION')->where('COD_PROD', '=', $codigo)
              ->where('ID_SUCURSAL', '=', $user->id_sucursal)
              ->delete();

          return ['response' => false];
        }

        /*  --------------------------------------------------------------------------------- */

        // MODIFICAR REFERENCIA 
        
        try {
         
          if (count($gondolas[0]) > 0) {

              // ELIMINAR GONDOLAS NO ASIGNADAS Y PRODUCTOS DE PRODUCTOS_TIENE_SECCION PARA LAS GONDOLAS

            Gondola_tiene_Productos::where('GONDOLA_COD_PROD', '=', $codigo)
              ->where('ID_SUCURSAL', '=', $user->id_sucursal)
              ->delete();

            DB::connection('retail')->table('PRODUCTOS_TIENE_SECCION')->where('COD_PROD', '=', $codigo)
              ->where('ID_SUCURSAL', '=', $user->id_sucursal)
              ->delete();

            /*  --------------------------------------------------------------------------------- */

            foreach ($gondolas as $key => $value) {

              /*  --------------------------------------------------------------------------------- */

              // INSERTAR GONDOLAS 

              $gondola = Gondola_tiene_Productos::updateOrInsert(
                  ['FK_PRODUCTOS_AUX'=>$ID_PRODUCTO[0]["ID"] ,'GONDOLA_COD_PROD' => $codigo, 'ID_GONDOLA' => $value['ID']],
                  ['FECMODIF' => $dia, 'ID_SUCURSAL' => $user->id_sucursal]
              );
 
              /*  --------------------------------------------------------------------------------- */
              //AÑADIR SECCION AL PRODUCTO EN PRODUCTOS_TIENE_SECCION
              $gondola_tiene_seccion=DB::connection('retail')->table('GONDOLA_TIENE_SECCION')
              ->SELECT(DB::raw('GONDOLA_TIENE_SECCION.ID_SECCION AS ID_SECCION'))
              ->where('GONDOLA_TIENE_SECCION.ID_GONDOLA', '=', $value['ID'])
              ->where('GONDOLA_TIENE_SECCION.ID_SUCURSAL', '=', $user->id_sucursal)->get()->toArray();
              /*  --------------------------------------------------------------------------------- */
               if (count($gondola_tiene_seccion) > 0) {
                $c=0;
                foreach ($gondola_tiene_seccion as $key => $value2) {
                  /* var_dump($value2->ID_SECCION);*/
                  $c=$c+1;
                  if($c>=2){
                     $producto_seccion = DB::connection('retail')->table('PRODUCTOS_TIENE_SECCION')->where('COD_PROD', '=', $codigo)
                     ->where('ID_SUCURSAL', '=', $user->id_sucursal)->update(['SECCION' => $value2->ID_SECCION]);
                   }else{
                    $producto_seccion = DB::connection('retail')->table('PRODUCTOS_TIENE_SECCION')->insertGetId(
                    ['COD_PROD' => $codigo,
                     'SECCION' => $value2->ID_SECCION, 
                     'ID_SUCURSAL' => $user->id_sucursal]
                   );
                    
                   }
                  
                    
                }

               }
              /*  --------------------------------------------------------------------------------- */
              Log::info('Gondola Asignar: Éxito al modificar.', ['PRODUCTO' => $codigo, 'ID GONDOLA' => $value['ID']]);


              /*  --------------------------------------------------------------------------------- */

            }

            /*  --------------------------------------------------------------------------------- */

          } else {
              Gondola_tiene_Productos::where('GONDOLA_COD_PROD', '=', $codigo)
              ->where('ID_SUCURSAL', '=', $user->id_sucursal)
              ->delete();

            DB::connection('retail')->table('PRODUCTOS_TIENE_SECCION')->where('COD_PROD', '=', $codigo)
              ->where('ID_SUCURSAL', '=', $user->id_sucursal)
              ->delete();
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
