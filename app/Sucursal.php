<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{

    protected $connection = 'retail';
    protected $table = 'sucursales';
    const CREATED_AT='FECALTAS';
    const UPDATED_AT='FECMODIF';

    public static function guardarSucursales($datos){
        
        $user = auth()->user();
        $dia = date("Y-m-d");
        $hora = date("H:i:s");


        try {

            // CONTROLA QUE NO EXISTA PARA INSERTAR

            if ($datos['data']['existe']=== false){

                $sucursal=Sucursal::insertGetId(
                ['CODIGO'=> $datos['data']['codigo'], 
                'DESCRIPCION'=> $datos['data']['descripcion'],
                'RAZON_SOCIAL'=> $datos['data']['razon'],
                'RUC'=> $datos['data']['ruc'],
                'CIUDAD' => $datos['data']['ciudad'],
                'DIRECCION' => $datos['data']['direccion'],
                'TELEFONO' => $datos['data']['telefono'],
                'CR_DESCRIPCION' => $datos['data']['nombre'],
                'USER'=> $user->name,
                'FECALTAS'=> $dia,
                'HORALTAS'=> $hora]);

            }else{

                // ACTUALIZA LOS DATOS

                $sucursal=Sucursal::Where('CODIGO','=',$datos['data']['codigo'])
                    ->update(['DESCRIPCION'=> $datos['data']['descripcion'],
                    'RAZON_SOCIAL'=> $datos['data']['razon'],
                    'RUC' => $datos['data']['ruc'],
                    'CIUDAD' => $datos['data']['ciudad'],
                    'DIRECCION' => $datos['data']['direccion'],
                    'TELEFONO' => $datos['data']['telefono'],
                    'CR_DESCRIPCION' => $datos['data']['nombre'],
                    'USERM'=>$user->name,
                    'FECMODIF'=>$dia,
                    'HORMODIF'=>$hora]);
                }

            return ["response"=>true];

        }catch(Exception $ex){ 

             return ["response"=>false,'statusText'=>$ex->getMessage()];
        }
    }

    public static function eliminarSucursales($datos){

        $user = auth()->user();

        try {

            // ELIMINA SI EXISTE

            if ($datos['data']['existe']=== true){

                $sucursales=Sucursal::Where('CODIGO','=',$datos['data']['codigo'])->delete();

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

    public static function filtrar_sucursal($datos){

        $user = auth()->user();

        // OBTENER TODAS LAS SUCURSALES

        $sucursal = Sucursal::select(DB::raw('CODIGO, DESCRIPCION, RAZON_SOCIAL, DIRECCION, RUC, TELEFONO, CIUDAD, CR_DESCRIPCION')
                    )->Where('CODIGO','=',$datos['data'])->get()->toArray();

        // RETORNAR EL VALOR

       return ["sucursal"=>$sucursal];

        /*  --------------------------------------------------------------------------------- */

    }

    public static function filtrarSucursal($datos){

        $user = auth()->user();

        // OBTENER TODAS LAS SUCURSALES

        $sucursal = Sucursal::select(DB::raw('CODIGO, DESCRIPCION, RAZON_SOCIAL, DIRECCION, RUC, TELEFONO, CIUDAD, CR_DESCRIPCION')) 
                            ->get()
                            ->toArray();
        $Sucursal_creado = $user->id_sucursal;
        // RETORNAR EL VALOR

       return ["sucursal"=>$sucursal, "Sucursal_creado"=>$Sucursal_creado];

        /*  --------------------------------------------------------------------------------- */

    }

    public static function nuevaSucursal(){

        $user = auth()->user();

        //OBTENER EL ULTIMO CODIGO DE CLIENTE

        $sucursal = Sucursal::select('CODIGO')->orderby('CODIGO','DESC')->limit(1)
                        ->get()->toArray();


        // RETORNAR EL VALOR

        if ($sucursal) {
            return ['sucursal' => $sucursal];
        } else {
            return ['sucursal' => 0];
        }
    }

    public static function sucursalesDatatable($request){

        // INICIARA VARIABLES

        //global $search;

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 

                        0 => 'codigo', 
                        1 => 'descripcion',
                        2 => 'razon_social',
                        3 => 'direccion',
                        4 => 'ruc'
                        
                    );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE SUCURSALES ENCONTRADOS 

        $totalData = Sucursal::count();

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

            //  CARGAR TODAS LAS SUCURSALES ENCONTRADOS 

            $posts = Sucursal::select(DB::raw('CODIGO, DESCRIPCION, RAZON_SOCIAL, DIRECCION, RUC'))
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();

            /*  ************************************************************ */

        } else {

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            // CARGAR LAS SUCURSALES FILTRADAS EN DATATABLE

            $posts =Sucursal::select(DB::raw('CODIGO, DESCRIPCION, RAZON_SOCIAL, DIRECCION, RUC'))
                            ->where(function ($query) use ($search) {
                                $query->where('CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();


            // CARGAR LA CANTIDAD DE SUCURSALES FILTRADAS 

            $totalFiltered = Sucursal::where(function ($query) use ($search) {
                                $query->where('CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                             ->count();

            /*  ************************************************************ */  

        }

        /*  --------------------------------------------------------------------------------- */

        $data = array();

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts))
        {
            foreach ($posts as $post)
            {

             /*  --------------------------------------------------------------------------------- */

             // CARGAR EN LA VARIABLE 

                $nestedData['CODIGO'] = $post->CODIGO;
                $nestedData['DESCRIPCION'] = $post->DESCRIPCION;
                $nestedData['RAZON_SOCIAL'] = $post->RAZON_SOCIAL;
                $nestedData['DIRECCION'] = $post->DIRECCION;
                $nestedData['RUC'] = $post->RUC;


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

     public static function mostrarSucursal($data)
    {

    	/*  --------------------------------------------------------------------------------- */

        $user = auth()->user();

    	// OBTENER TODAS LAS SUCURSALES

    	$sucursales = Sucursal::select(DB::raw('CODIGO, DESCRIPCION, RAZON_SOCIAL, DIRECCION, RUC'))
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($sucursales) {
        	return ['sucursales' => $sucursales];
        } else {
        	return ['sucursales' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function encontrarSucursal($data)
    {

    	/*  --------------------------------------------------------------------------------- */
    	   
    	// OBTENER TODAS LAS SUCURSALES

    	$sucursal = Sucursal::select(DB::raw('CODIGO, DESCRIPCION, RAZON_SOCIAL, DIRECCION, CIUDAD, RUC'))
        ->where('CODIGO', '=', $data['codigoOrigen'])
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
        
        if (count($sucursal) > 0) {
            return ['sucursal' => $sucursal];
        } else {
            return ['sucursal' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
    
    public static function mostrar_sucursal($data){

        /*  --------------------------------------------------------------------------------- */

        $user = auth()->user();

        // OBTENER TODAS LAS SUCURSALES

        $sucursales = Sucursal::select(DB::raw('CODIGO, DESCRIPCION, RAZON_SOCIAL, DIRECCION, RUC'))
        ->where('CODIGO', '=', $user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($sucursales) {
            return ['sucursales' => $sucursales];
        } else {
            return ['sucursales' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
}
