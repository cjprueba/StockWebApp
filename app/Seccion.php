<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Gondola_Tiene_Seccion;
use App\User_Tiene_Seccion;

class Seccion extends Model
{
	 protected $connection = 'retail';
    protected $table = 'secciones';
    public $timestamps = false;

     public static function mostrarSeccion($data)
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER TODOS LOS EMPLEADOS

    	$secciones = Seccion::select(DB::raw('CODIGO, DESCRIPCION, ID_SUCURSAL'))
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if (count($secciones) > 0) {
        	return ['secciones' => $secciones];
        } else {
        	return ['secciones' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function encontrarSeccion($data)
    {

    	/*  --------------------------------------------------------------------------------- */
    	   
    	// OBTENER TODAS LAS SUCURSALES

    	$seccion = Seccion::select(DB::raw('ID, DESCRIPCION, ID_SUCURSAL'))
        ->where('ID', '=', $data['codigo'])
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
        
        if (count($seccion) > 0) {
            return ['seccion' => $seccion];
        } else {
            return ['seccion' => 0];
        }

        /*  --------------------------------------------------------------------------------- */
    }

    public static function secciones_datatable($request){

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 
        $user = auth()->user();
        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'ID', 
                            1 => 'CODIGO',
                            2 => 'DESCRIPCION'
                        );
        // CONTAR LA CANTIDAD DE SECCIONES ENCONTRADAS 
        $totalData = Seccion::
        where('ID_SUCURSAL','=', $user->id_sucursal)
        ->count();
        // INICIAR VARIABLES 

        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE VALOR EN VARIABLE SEARCH

        if(empty($request->input('search.value')))
        {            

            /*  ************************************************************ */

            //  CARGAR TODOS LAS SECCIONES ENCONTRADOS 

            $posts = Seccion::select(DB::raw('ID, CODIGO, DESCRIPCION'))
                         ->where('ID_SUCURSAL','=', $user->id_sucursal)
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
                        

            /*  ************************************************************ */

        } else {

            /*  ************************************************************ */

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            /*  ************************************************************ */

            // CARGAR LAS SECCIONES FILTRADOS EN DATATABLE

            $posts = Seccion::select(DB::raw('ID, CODIGO, DESCRIPCION'))
                            ->where('ID_SUCURSAL','=', $user->id_sucursal)
                            ->where(function ($query) use ($search) {
                                $query->where('CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE SECCIONES FILTRADOS 

            $totalFiltered = Seccion::where(function ($query) use ($search) {
                                $query->where('CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                             ->where('ID_SUCURSAL','=', $user->id_sucursal)
                             ->count();

            /*  ************************************************************ */  

        }

        $data = array();

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts))
        {
            foreach ($posts as $post)
            {

                /*  --------------------------------------------------------------------------------- */

                // CARGAR EN LA VARIABLE 
                $nestedData['ID'] = $post->ID;
                $nestedData['CODIGO'] = $post->CODIGO;
                $nestedData['DESCRIPCION'] = $post->DESCRIPCION;
                
                $data[] = $nestedData;

                /*  --------------------------------------------------------------------------------- */

            }
        }
        
        /*  --------------------------------------------------------------------------------- */

        // PREPARAR EL ARRAY A ENVIAR 

        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );
        
        /*  --------------------------------------------------------------------------------- */

        // CONVERTIR EN JSON EL ARRAY Y ENVIAR 
       return $json_data;
    }

    public static function nuevaSeccion(){
         $user = auth()->user();

        //OBTENER EL ULTIMO CODIGO DE LA SECCION
        $seccion = seccion::select(DB::raw('IFNULL(CODIGO,0) AS CODIGO'))
                        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
                        ->orderby('CODIGO','DESC')
                        ->limit(1)
                        ->get()
                        ->toArray();
        
        // RETORNAR EL VALOR
        if(count($seccion)>0){
            return ["seccion" => $seccion[0]["CODIGO"]];
        }else{

            return ["seccion" => 0];
        }

     }

     public static function filtrarSeccion($datos){


        $seccion=seccion::select(DB::raw('CODIGO, 
            DESCRIPCION'
            )
        )
        ->where('ID','=', $datos['data'])
        ->get();
            
            return['seccion'=> $seccion];
    }



    public static function guardarSeccion($datos){

        $user = auth()->user();
        try{
            DB::connection('retail')->beginTransaction();

            if($datos['data']['btn_guardar']==true){

                $seccion=seccion::insertGetId([
                    'CODIGO'=> $datos['data']['codigo'],
                    'DESCRIPCION'=> $datos['data']['descripcion'],
                    'ID_SUCURSAL'=>$user->id_sucursal]);

                DB::connection('retail')->commit();

                return['response'=>true];

            }else{
                $seccion = Seccion::Where('CODIGO','=',$datos['data']['codigo'])->where('ID_SUCURSAL', '=', $user->id_sucursal)
                    ->update([
                    'DESCRIPCION'=> $datos['data']['descripcion']]);
                DB::connection('retail')->commit();
                return['response'=>true];
            }
        }
        catch(Exception $ex){
            DB::connection('retail')->rollBack();
            if($ex->errorInfo[1]==1062){
                return ["response"=>false,'statusText'=>'¡Este Código de seccion ya fue registrado!'];
            }else{
                return ["response"=>false,'statusText'=>$ex->getMessage()];
            }
        }
    }

    public static function eliminarSeccion($datos){
        $user = auth()->user();

        if($datos['data']['btn_guardar']==false){

            
            
            $gondola_seccion = Seccion::verificarGondolaTieneSeccion($datos['data']['codigo']);
            
            if($gondola_seccion['response']==false){
                return $gondola_seccion;
            }


            
            $user_seccion = Seccion::verificarUserTieneSeccion($datos['data']['codigo']);

            if($user_seccion['response']==false){
                return $user_seccion;
            }

            $seccion = Seccion::where('CODIGO','=',$datos['data']['codigo'])
            				->where('ID_SUCURSAL', '=', $user->id_sucursal)->delete();

            return ['response'=>true];
        }else{
            return["response"=>false, 'statusText'=> 'No existe esta seccion '.$datos['data']['codigo']];
        }
    }

    public static function conseguir_id($codigo,$id_sucursal){
    	$id_seccion=Seccion::select('ID')
            		->where('CODIGO','=',$codigo)
            		->where('ID_SUCURSAL','=',$id_sucursal)
                    ->get()
                    ->toArray();
                    return $id_seccion[0]['ID'];

    }
    public static function verificarGondolaTieneSeccion($codigo){

        $user = auth()->user();

        $gondola = Gondola_Tiene_Seccion::select('ID')
        ->where('ID_SECCION', '=', $codigo)
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->limit(1)
        ->get();

         // RETORNAR EL VALOR

        if(count($gondola) > 0 && !empty($codigo)){

            return  ["response"=>false,'statusText'=> 'Existe una gondola cargado con esta seccion.'];
        } else {

            // MUESTRA QUE NO EXISTE

            return ["response"=>true];

        }
    }

    public static function verificarUserTieneSeccion($codigo){
    	$user = auth()->user();

        $userSeccion = User_Tiene_Seccion::select('FK_USER')
        ->where('FK_SECCION', '=', $codigo)
        ->limit(1)
        ->get();

         // RETORNAR EL VALOR

        if(count($userSeccion) > 0 && !empty($codigo)){

            return  ["response"=>false,'statusText'=> 'Existe un usuario cargado con esta seccion.'];
        } else {

            // MUESTRA QUE NO EXISTE

            return ["response"=>true];

        }
    }
}
