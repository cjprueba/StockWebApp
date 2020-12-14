<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Registro_Maquina extends Model{

    protected $connection = 'retail';
    protected $table = 'registro_maquinas';
    public $timestamps = false;

    public static function ultimoCodigoSucursal($data){

        $user = auth()->user();

        //OBTENER EL ULTIMO ID 

        $sucursal = Registro_Maquina::select(DB::raw('count(ID_SUCURSAL_RM) AS CANTIDAD'))
        	->where('ID_SUCURSAL_RM', '=', $data['data'])
	        ->get()
	        ->toArray();

        // RETORNAR EL VALOR

        return ["sucursal" => $sucursal];
    }

    public static function ultimoCodigoSector($data){

        $user = auth()->user();

        //OBTENER EL ULTIMO ID

        $sector = Registro_Maquina::select(DB::raw('count(ID_SECTOR_RM) AS CANTIDAD'))
        	->where('ID_SECTOR_RM', '=', $data['data']['sector'])
        	->where('ID_SUCURSAL_RM', '=', $data['data']['sucursal'])
	        ->get()
	        ->toArray();

        // RETORNAR EL VALOR
      
        return ["sector" => $sector];
    }

    public static function ultimoRegistro(){

        $user = auth()->user();
        
        //OBTENER EL ULTIMO ID

        $registro = Registro_Maquina::select('ID')
	        ->orderBy('ID', 'DESC')
	        ->limit(1)
	        ->get();

        // RETORNAR EL VALOR
      
        if (count($registro) > 0) {
           return ["registro" => $registro];
        } else {
            return 1;
        }
    }
    public static function guardarMaquina($datos){

    	try{

            DB::connection('retail')->beginTransaction();
          
            $user = auth()->user();
            $dia = date("Y-m-d H:i:s");


            if($datos['data']['existe'] == false){
                $maquina = Registro_Maquina::insertGetId([
                    'ID_SUCURSAL_RM' => $datos['data']['idSucursal'], 
                    'ID_SECTOR_RM' => $datos['data']['idSector'], 
                    'CARACTERISTICAS' => $datos['data']['caracteristica'], 
                    'USUARIO' => $datos['data']['usuario'], 
                    'IP_RM' => $datos['data']['ip'], 
                    'NOMBRE' => $datos['data']['nombre'], 
                    'USER' =>$user->id,
                    'FECALTAS' =>$dia
                ]);

            }else{

                // SI EXISTE ACTUALIZA LOS DATOS

                $maquina = Registro_Maquina::Where('ID','=',$datos['data']['id'])
                    ->update([
                        'ID_SUCURSAL_RM' => $datos['data']['idSucursal'], 
	                    'ID_SECTOR_RM' => $datos['data']['idSector'], 
	                    'CARACTERISTICAS' => $datos['data']['caracteristica'], 
	                    'USUARIO' => $datos['data']['usuario'], 
	                    'IP_RM' => $datos['data']['ip'], 
	                    'NOMBRE' => $datos['data']['nombre']
                    ]); 
            }
            DB::connection('retail')->commit();

            return ["response" => true, "maquina_id"=>$maquina];

    	}catch (Exception $e){

    		DB::connection('retail')->rollBack();
            throw $e;
            return ["response"=>false,"statusText"=>"No se pudo guardar la máquina!"];
    	}
      return ["response" => true];
    }

    public static function eliminarMaquina($datos){

        $user = auth()->user();

        try {

            // ELIMINA SI EXISTE

            if ($datos['data']['existe']=== true){

                $maquina = Registro_Maquina::Where('ID','=',$datos['data']['id'])
                	->delete();

            }else{

                // MUESTRA QUE NO EXISTE

                return ["response"=>false,'statusText'=> 'No se encontró el registro'];
            }

        }catch(Exception $ex){ 

                return ["response"=>false,'statusText'=>$ex->getMessage()];            
        
             }

        // RETORNAR EL VALOR

       return ["response"=>true];

    }

    public static function registrosMaquinaDatatable($request){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
         
        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'REGISTRO_MAQUINAS.ID', 
                            1 => 'SUCURSALES_RM.DESCRIPCION',
                            2 => 'SECTORES_RM.DESCRIPCION',
                            3 => 'REGISTRO_MAQUINAS.CARACTERISTICAS',
                            4 => 'REGISTRO_MAQUINAS.USUARIO',
                            5 => 'REGISTRO_MAQUINAS.IP_RM',
                            6 => 'REGISTRO_MAQUINAS.NOMBRE'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD ENCONTRADA

        $totalData = Registro_Maquina::count();

        /*  --------------------------------------------------------------------------------- */

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

            //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

            $posts = Registro_Maquina::select(DB::raw('REGISTRO_MAQUINAS.ID AS ID, 
            				SUCURSALES_RM.DESCRIPCION AS SUCURSAL, 
            				SECTORES_RM.DESCRIPCION AS SECTOR, 
            				REGISTRO_MAQUINAS.CARACTERISTICAS AS CARACTERISTICAS, 
            				REGISTRO_MAQUINAS.USUARIO AS USUARIO, 
            				REGISTRO_MAQUINAS.IP_RM AS IP, 
            				REGISTRO_MAQUINAS.NOMBRE AS NOMBRE'))
            			 ->leftjoin('SECTORES_RM', 'SECTORES_RM.ID', '=', 'REGISTRO_MAQUINAS.ID_SECTOR_RM')
            			 ->leftjoin('SUCURSALES_RM', 'SUCURSALES_RM.ID', '=', 'REGISTRO_MAQUINAS.ID_SUCURSAL_RM')
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

            // CARGAR LOS PRODUCTOS FILTRADOS EN DATATABLE

            $posts = Registro_Maquina::select(DB::raw('REGISTRO_MAQUINAS.ID AS ID, 
            				SUCURSALES_RM.DESCRIPCION AS SUCURSAL, 
            				SECTORES_RM.DESCRIPCION AS SECTOR, 
            				REGISTRO_MAQUINAS.CARACTERISTICAS AS CARACTERISTICAS, 
            				REGISTRO_MAQUINAS.USUARIO AS USUARIO, 
            				REGISTRO_MAQUINAS.IP_RM AS IP, 
            				REGISTRO_MAQUINAS.NOMBRE AS NOMBRE'))
            			 ->leftjoin('SECTORES_RM', 'SECTORES_RM.ID', '=', 'REGISTRO_MAQUINAS.ID_SECTOR_RM')
            			 ->leftjoin('SUCURSALES_RM', 'SUCURSALES_RM.ID', '=', 'REGISTRO_MAQUINAS.ID_SUCURSAL_RM')
                         ->where(function ($query) use ($search) {
                                $query->where('REGISTRO_MAQUINAS.ID','LIKE',"%{$search}%")
                                      ->orWhere('REGISTRO_MAQUINAS.USUARIO', 'LIKE',"%{$search}%")
                                      ->orWhere('SUCURSALES_RM.DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = Registro_Maquina::leftjoin('SECTORES_RM', 'SECTORES_RM.ID', '=', 'REGISTRO_MAQUINAS.ID_SECTOR_RM')
            			 ->leftjoin('SUCURSALES_RM', 'SUCURSALES_RM.ID', '=', 'REGISTRO_MAQUINAS.ID_SUCURSAL_RM')
            			 ->where(function ($query) use ($search) {
                                $query->where('REGISTRO_MAQUINAS.ID','LIKE',"%{$search}%")
                                      ->orWhere('REGISTRO_MAQUINAS.USUARIO', 'LIKE',"%{$search}%")
                                      ->orWhere('SUCURSALES_RM.DESCRIPCION', 'LIKE',"%{$search}%");
                            })
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
                $nestedData['SUCURSAL'] = $post->SUCURSAL;
                $nestedData['SECTOR'] = $post->SECTOR;
                $nestedData['CARACTERISTICA'] = $post->CARACTERISTICAS;
                $nestedData['USUARIO'] = $post->USUARIO;
                $nestedData['IP'] = $post->IP;
                $nestedData['NOMBRE'] = $post->NOMBRE;
                
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

    public static function filtrarRegistro($datos){

        $user = auth()->user();

        // OBTENER TODAS LOS CLIENTES

        $registro = Registro_Maquina::select(DB::raw('ID AS ID, 
                        CARACTERISTICAS AS CARACTERISTICA, 
                        NOMBRE AS NOMBRE, 
                        USUARIO AS USUARIO, 
                        IP_RM AS IP, 
                        ID_SECTOR_RM AS SECTOR, 
                        ID_SUCURSAL_RM AS SUCURSAL'))
                    ->Where('ID','=',$datos['data'])
                    ->get()
                    ->toArray();
          
        // RETORNAR EL VALOR

        return ["registro" => $registro];

        /*  --------------------------------------------------------------------------------- */
    }
}
