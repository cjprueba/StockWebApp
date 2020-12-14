<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sucursal_Rm extends Model{
	protected $connection = 'retail';
    protected $table = 'sucursales_rm';
    public $timestamps = false;
    
    public static function guardarSucursalRM($datos){

    	try{

            DB::connection('retail')->beginTransaction();
          
            $user = auth()->user();
            $dia = date("Y-m-d H:i:s");
            
            if($datos['data']['existe'] == false){

                $sucursal_rm = Sucursal_Rm::insertGetId([
                    'DESCRIPCION' => $datos['data']['descripcion'], 
                    'DESC_CORTA' => $datos['data']['desc_corta'], 
                    'USER' =>$user->id,
                    'FECALTAS' =>$dia]
                );

            }else{
                
                // SI EXISTE ACTUALIZA LOS DATOS

                $sucursal_rm = Sucursal_Rm::Where('ID','=',$datos['data']['id'])
                    ->update([
                        'DESCRIPCION' => $datos['data']['descripcion'], 
                        'DESC_CORTA' => $datos['data']['desc_corta']
                    ]); 
            }

            DB::connection('retail')->commit();

            return ["response" => true, "sucursal_id" => $sucursal_rm];

    	}catch (Exception $e){

    		DB::connection('retail')->rollBack();
            throw $e;

            return ["response" => false, "statusText" => $ex->getMessage()];
    	}
    }

    public static function nuevaSucursalRM(){

       $user = auth()->user();

        //OBTENER EL ULTIMO ID

        $sucursal = Sucursal_Rm::select('ID')
                        ->orderby('ID','DESC')
                        ->limit(1)
                        ->get();

        // RETORNAR EL VALOR

        if (count($sucursal) > 0) {
           return ["sucursal" => $sucursal];
        } else {
            return 1;
        }
    }

    public static function eliminarSucursalRM($datos){

        $user = auth()->user();
        
        try {

            // ELIMINA SI EXISTE

            if ($datos['data']['existe']=== true){

                $sucursal = Sucursal_Rm::Where('ID','=',$datos['data']['id'])
                ->where('DESCRIPCION','=', $datos['data']['descripcion'])
                ->where('DESC_CORTA', '=', $datos['data']['desc_corta'])
                ->delete();

            }else{

                // MUESTRA QUE NO EXISTE

                return ["response"=>false,'statusText'=> 'No se encontró la sucursal'];
            }

        }catch(Exception $ex){ 

                return ["response"=>false,'statusText'=>$ex->getMessage()];            
        
             }

        // RETORNAR EL VALOR

       return ["response"=>true];

    }

    public static function sucursalRmDatatable($request){

        // INICIARA VARIABLES

        $user = auth()->user();

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                        0 => 'ID',
                        1 => 'DESCRIPCION',
                        2 => 'DESC_CORTA'
                    );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE SUCURSALES 

        $totalData = Sucursal_Rm::count();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE VALOR EN VARIABLE SEARCH

        if(empty($request->input('search.value'))){            

            //  CARGAR TODOS LOS ENCONTRADOS 

            $posts = Sucursal_Rm::select(DB::raw('ID, DESCRIPCION, DESC_CORTA'))
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
        }else{

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            // CARGAR LOS FILTRADOS EN DATATABLE

            $posts = Sucursal_Rm::select(DB::raw('ID, DESCRIPCION, DESC_CORTA'))
                            ->where(function ($query) use ($search) {
                                $query->Where('DESCRIPCION', 'LIKE',"%{$search}%")
                                      ->orWhere('DESC_CORTA', 'LIKE',"%{$search}%")
                                      ->orWhere('ID', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            // CARGAR LA CANTIDAD DE FILTRADOS 

            $totalFiltered = Sucursal_Rm::where(function ($query) use ($search) {
                                $query->Where('DESCRIPCION', 'LIKE',"%{$search}%")
                                      ->orWhere('DESC_CORTA', 'LIKE',"%{$search}%")
                                      ->orWhere('ID', 'LIKE',"%{$search}%");
                                    })
                                ->count();
        }

        /*  --------------------------------------------------------------------------------- */

        $data = array();

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts)){

            foreach ($posts as $post){

             /*  --------------------------------------------------------------------------------- */

             // CARGA EN LA VARIABLE 

                $nestedData['ID'] = $post->ID;
                $nestedData['DESCRIPCION'] = $post->DESCRIPCION;
                $nestedData['DESC_CORTA'] = $post->DESC_CORTA;

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

        /*  --------------------------------------------------------------------------------- */
        
    }

    public static function filtrarSucursalRM($datos){

        $user = auth()->user();

        // OBTENER TODAS LOS CLIENTES

        $sucursal = Sucursal_Rm::select(DB::raw('ID, DESCRIPCION, DESC_CORTA'))
                    ->Where('ID','=',$datos['data'])
                    ->get()
                    ->toArray();
          
        // RETORNAR EL VALOR

        return ["sucursal" => $sucursal];

        /*  --------------------------------------------------------------------------------- */
    }
}
