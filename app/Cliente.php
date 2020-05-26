<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cliente extends Model
{
    protected $connection = 'retail';
    protected $table = 'clientes';

    public static function cliente_datatable($request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
         
        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'CODIGO', 
                            1 => 'CI',
                         	2 => 'NOMBRE',
                         	3 => 'RUC',
                         	4 => 'DIRECCION',
                         	5 => 'CIUDAD',
                         	6 => 'TELEFONO',
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = Color::
        where('ID_SUCURSAL','=', $user->id_sucursal)
        ->count();

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

            $posts = Cliente::select(DB::raw('CODIGO, CI, NOMBRE, RUC, DIRECCION, CIUDAD, TELEFONO'))
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

            // CARGAR LOS PRODUCTOS FILTRADOS EN DATATABLE

            $posts = Cliente::select(DB::raw('CODIGO, CI, NOMBRE, RUC, DIRECCION, CIUDAD, TELEFONO'))
                            ->where('ID_SUCURSAL','=', $user->id_sucursal)
                            ->where(function ($query) use ($search) {
                                $query->where('CI','LIKE',"%{$search}%")
                                      ->orWhere('NOMBRE', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = Cliente::where(function ($query) use ($search) {
                                $query->where('CI','LIKE',"%{$search}%")
                                      ->orWhere('NOMBRE', 'LIKE',"%{$search}%");
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

                $nestedData['CODIGO'] = $post->CODIGO;
                $nestedData['CI'] = $post->CI;
                $nestedData['NOMBRE'] = $post->NOMBRE;
                $nestedData['RUC'] = $post->RUC;
                $nestedData['DIRECCION'] = $post->DIRECCION;
                $nestedData['CIUDAD'] = $post->CIUDAD;
                $nestedData['TELEFONO'] = $post->TELEFONO;

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

    public static function guardarClientes($datos){
        
        $user = auth()->user();
        $dia = date("Y-m-d");
        $hora = date("H:i:s");

        try {

            // CONTROLA QUE NO EXISTA PARA INSERTAR

            if ($datos['data']['existe']=== false){

                // GUARDA LOS DATOS

                $cliente = Cliente::insertGetId(
                ['CODIGO'=> $datos['data']['codigo'], 
                'CI' => $datos['data']['cedula'],
                'NOMBRE'=> $datos['data']['name'],
                'RUC'=> $datos['data']['ruc'],
                'DIRECCION' => $datos['data']['direccion'],
                'CIUDAD' => $datos['data']['ciudad'],
                'FEC_NAC'=> $datos['data']['nacimiento'],
                'TELEFONO' => $datos['data']['telefono'],
                'CELULAR' => $datos['data']['celular'],
                'EMAIL' => $datos['data']['email'],
                'TIPO' => $datos['data']['tipo'],
                'LIMITE_CREDITO' => $datos['data']['limite'],
                'USER'=> $user->name,
                'FECALTAS'=> $dia,
                'HORALTAS'=> $hora,
                'ID_SUCURSAL' => $user->id_sucursal]);

            }else{

                // ACTUALIZA LOS DATOS

                $cliente = Cliente::Where('CODIGO','=',$datos['data']['codigo'])->where('ID_SUCURSAL', '=', $user->id_sucursal)
                    ->update(['CI' => $datos['data']['cedula'],
                    'NOMBRE'=> $datos['data']['name'],
                    'RUC'=> $datos['data']['ruc'],
                    'DIRECCION' => $datos['data']['direccion'],
                    'CIUDAD' => $datos['data']['ciudad'],
                    'FEC_NAC'=> $datos['data']['nacimiento'],
                    'TELEFONO' => $datos['data']['telefono'],
                    'CELULAR' => $datos['data']['celular'],
                    'EMAIL' => $datos['data']['email'],
                    'TIPO' => $datos['data']['tipo'],
                    'LIMITE_CREDITO' => $datos['data']['limite'],
                    'USERM'=>$user->name,
                    'FECMODIF'=>$dia,
                    'HORMODIF'=>$hora]);
                }

            // ENVIA UNA RESPUESTA A LA FUNCION

            return ["response"=>true];

        // ERROR 
        
        }catch(Exception $ex){ 

            return ["response"=>false,'statusText'=>$ex->getMessage()];
        }
    }

    public static function eliminarClientes($datos){

        $user = auth()->user();

        try {

            // ELIMINA SI EXISTE

            if ($datos['data']['existe']=== true){

                $clientes= Cliente::Where('CODIGO','=',$datos['data']['codigo'])
                ->where('NOMBRE','=', $datos['data']['nombre'])
                ->where('ID_SUCURSAL', '=', $user->id_sucursal)->delete();

            }else{

                // MUESTRA QUE NO EXISTE

                return ["response"=>false,'statusText'=> 'No se encontró el cliente'];
            }

        }catch(Exception $ex){ 

                return ["response"=>false,'statusText'=>$ex->getMessage()];            
        
             }

        // RETORNAR EL VALOR

       return ["response"=>true];

    }

    public static function clientesDatatable($request){

        // INICIARA VARIABLES

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 

                        0 => 'id',
                        1 => 'nombre',
                        2 => 'codigo',
                        3 => 'ruc',
                        4 => 'direccion',
                        5 => 'ciudad',
                        
                    );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE CLIENTES ENCONTRADOS 

        $totalData = Cliente::count();

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

            //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

            $posts = Cliente::select(DB::raw('ID, NOMBRE, CODIGO, RUC, DIRECCION, CIUDAD'))
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();

            /*  ************************************************************ */

        } else {

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            // CARGAR LOS CLIENTES FILTRADOS EN DATATABLE

            $posts =Cliente::select(DB::raw('ID, NOMBRE, CODIGO, NOMBRE, RUC, DIRECCION, CIUDAD'))
                            ->where(function ($query) use ($search) {
                                $query->where('CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('NOMBRE', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();


            // CARGAR LA CANTIDAD DE CLIENTES FILTRADOS 

            $totalFiltered = Cliente::where(function ($query) use ($search) {
                                $query->where('CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('NOMBRE', 'LIKE',"%{$search}%");
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

             // CARGA EN LA VARIABLE 

                $nestedData['ID'] = $post->ID;
                $nestedData['NOMBRE'] = $post->NOMBRE;
                $nestedData['CODIGO'] = $post->CODIGO;
                $nestedData['RUC'] = $post->RUC;
                $nestedData['DIRECCION'] = $post->DIRECCION;
                $nestedData['CIUDAD'] = $post->CIUDAD;


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
}
