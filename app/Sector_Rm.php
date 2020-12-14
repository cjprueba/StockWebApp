<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sector_Rm extends Model{

    protected $connection = 'retail';
    protected $table = 'sectores_rm';
    public $timestamps = false;

    public static function nuevoSectorRM(){

        $user = auth()->user();

        //OBTENER EL ULTIMO ID

        $sector = Sector_Rm::select('ID')
                        ->orderby('ID','DESC')
                        ->limit(1)
                        ->get();

        // RETORNAR EL VALOR

        if (count($sector) > 0) {
           return ["sector" => $sector];
        } else {
            return 1;
        }
    }

    public static function eliminarSectorRM($datos){

        $user = auth()->user();
        
        try {

            // ELIMINA SI EXISTE

            if ($datos['data']['existe']=== true){

                $sector = Sector_Rm::Where('ID','=',$datos['data']['id'])
                ->where('DESCRIPCION','=', $datos['data']['descripcion'])
                ->where('DESC_CORTA', '=', $datos['data']['desc_corta'])
                ->delete();

            }else{

                // MUESTRA QUE NO EXISTE

                return ["response"=>false,'statusText'=> 'No se encontró la sector'];
            }

        }catch(Exception $ex){ 

                return ["response"=>false,'statusText'=>$ex->getMessage()];            
        
             }

        // RETORNAR EL VALOR

       return ["response"=>true];

    }

    public static function sectorRmDatatable($request){

        // INICIARA VARIABLES

        $user = auth()->user();

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                        0 => 'ID',
                        1 => 'DESCRIPCION',
                        2 => 'DESC_CORTA'
                    );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE SECTORES 

        $totalData = Sector_Rm::count();

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

            $posts = Sector_Rm::select(DB::raw('ID, DESCRIPCION, DESC_CORTA'))
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
        }else{

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            // CARGAR LOS FILTRADOS EN DATATABLE

            $posts = Sector_Rm::select(DB::raw('ID, DESCRIPCION, DESC_CORTA'))
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

            $totalFiltered = Sector_Rm::where(function ($query) use ($search) {
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

    public static function filtrarSectorRM($datos){

        $user = auth()->user();

        // OBTENER TODAS LOS CLIENTES

        $sector = Sector_Rm::select(DB::raw('ID, DESCRIPCION, DESC_CORTA'))
                    ->Where('ID','=',$datos['data'])
                    ->get()
                    ->toArray();
          
        // RETORNAR EL VALOR

        return ["sector" => $sector];

        /*  --------------------------------------------------------------------------------- */
    }

    public static function guardarSectorRM($datos){

    	try{

            DB::connection('retail')->beginTransaction();
          
            $user = auth()->user();
            $dia = date("Y-m-d H:i:s");


            if($datos['data']['existe'] == false){
                $sector = Sector_Rm::insertGetId([
                    'DESCRIPCION' => $datos['data']['descripcion'], 
                    'DESC_CORTA' => $datos['data']['desc_corta'], 
                    'USER' =>$user->id,
                    'FECALTAS' =>$dia
                ]);

            }else{

                // SI EXISTE ACTUALIZA LOS DATOS

                $sector = Sector_Rm::Where('ID','=',$datos['data']['id'])
                    ->update([
                        'DESCRIPCION' => $datos['data']['descripcion'], 
                        'DESC_CORTA' => $datos['data']['desc_corta']
                    ]); 
            }
            DB::connection('retail')->commit();

            return ["response" => true, "sector_id"=>$sector];

    	}catch (Exception $e){

    		DB::connection('retail')->rollBack();
            throw $e;
            return ["response"=>false,"statusText"=>"No se pudo guardar el sector!"];
    	}
      return ["response" => true];
    }
}
