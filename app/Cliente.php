<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Venta;

class Cliente extends Model
{
    protected $connection = 'retail';
    protected $table = 'clientes';
    public $timestamps = false;
    
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

        $totalData = Cliente::
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

 public static function clientesDatatable($request){

        // INICIARA VARIABLES

        $user = auth()->user();

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                        0 => 'id',
                        1 => 'nombre',
                        2 => 'codigo',
                        3 => 'ci',
                        4 => 'ruc',
                        5 => 'telefono',
                        6 => 'razon_social',
                        7 => 'direccion',
                        8 => 'ciudad'
                    );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE CLIENTES ENCONTRADOS 

        $totalData = Cliente::select('id')->where('ID_SUCURSAL', '=', $user->id_sucursal)->count();

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

            $posts = Cliente::select(DB::raw('ID, NOMBRE, CODIGO, CI, RUC, TELEFONO, RAZON_SOCIAL, DIRECCION, CIUDAD'))
                         ->where('ID_SUCURSAL', '=', $user->id_sucursal)
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();

            /*  ************************************************************ */

        }else{

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            // CARGAR LOS CLIENTES FILTRADOS EN DATATABLE

            $posts =Cliente::select(DB::raw('ID, NOMBRE, CODIGO, CI, RUC, TELEFONO, RAZON_SOCIAL, DIRECCION, CIUDAD'))
                            ->where(function ($query) use ($search) {
                                $query->where('CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('NOMBRE', 'LIKE',"%{$search}%")
                                      ->orWhere('ID', 'LIKE',"%{$search}%")
                                      ->orWhere('RUC', 'LIKE',"%{$search}%")  
                                      ->orWhere('CI', 'LIKE',"%{$search}%")   
                                      ->orWhere('RAZON_SOCIAL', 'LIKE',"%{$search}%");
                            })
                            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            // CARGAR LA CANTIDAD DE CLIENTES FILTRADOS 

            $totalFiltered = Cliente::where(function ($query) use ($search) {
                                $query->where('CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('NOMBRE', 'LIKE',"%{$search}%")
                                      ->orWhere('ID', 'LIKE',"%{$search}%")
                                      ->orWhere('RUC', 'LIKE',"%{$search}%")                                       
                                      ->orWhere('CI', 'LIKE',"%{$search}%")                                          
                                      ->orWhere('RAZON_SOCIAL', 'LIKE',"%{$search}%");
                            })->where('ID_SUCURSAL', '=', $user->id_sucursal)
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

                $cliente = strtolower($post->NOMBRE);
                $nestedData['ID'] = $post->ID;
                $nestedData['NOMBRE'] = ucwords($cliente);
                $nestedData['CODIGO'] = $post->CODIGO;
                $nestedData['CI'] = $post->CI;
                $nestedData['RUC'] = $post->RUC;
                $nestedData['TELEFONO'] = $post->TELEFONO;
                $nestedData['RAZON_SOCIAL'] = $post->RAZON_SOCIAL;
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

    public static function filtrarClientes($datos){

        $user = auth()->user();

        // OBTENER TODAS LOS CLIENTES

        $cliente= Cliente::select(DB::raw('CLIENTES.CODIGO, 
                        CLIENTES.CI, 
                        CLIENTES.NOMBRE, 
                        CLIENTES.RUC, 
                        CLIENTES.DIRECCION, 
                        CLIENTES.CIUDAD, 
                        CLIENTES.FEC_NAC, 
                        CLIENTES.TELEFONO, 
                        CLIENTES.CELULAR, 
                        CLIENTES.EMAIL, 
                        CLIENTES.TIPO, 
                        CLIENTES.RAZON_SOCIAL,
                        CLIENTES.LIMITE_CREDITO,
                        CLIENTES.FK_EMPRESA,
                        CLIENTES.DIAS_CREDITO AS LIMITEDIA,
                        EMPRESAS.NOMBRE AS EMPRESA,
                        CLIENTES.CREDITO_DISPONIBLE'))
                    ->leftjoin('EMPRESAS', 'EMPRESAS.ID', '=', 'CLIENTES.FK_EMPRESA')
                    ->where('CLIENTES.ID_SUCURSAL', '=', $user->id_sucursal)
                    ->Where('CLIENTES.ID','=',$datos['data'])
                    ->get()
                    ->toArray();
          
        // RETORNAR EL VALOR

        return ["cliente" => $cliente];

        /*  --------------------------------------------------------------------------------- */
    }

    public static function guardarClientes($datos){
        
        $user = auth()->user();
        $dia = date("Y-m-d");
        $hora = date("H:i:s");

         
        try {

            // CONTROLA QUE NO EXISTA PARA INSERTAR

            if($datos['data']['existe']=== false){

                /*  --------------------------------------------------------------------------------- */

                // VERIFICAR RUC

                $ruc = Cliente::verificarRuc($datos['data']['ruc']);

                if($ruc['response'] == false){

                    return $ruc;
                }
                 /*  --------------------------------------------------------------------------------- */

                // VERIFICAR CI

                $ci = Cliente::verificarCI($datos['data']['cedula']);

                if($ci['response'] == false){

                    return $ci;
                }

                /*  --------------------------------------------------------------------------------- */

                // GUARDA LOS DATOS

                $codigo = Cliente::select('CODIGO')
                        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
                        ->orderby('CODIGO','DESC')
                        ->limit(1)
                        ->get()
                        ->toArray();

                $cliente = Cliente::insertGetId(
                ['CODIGO'=> $codigo["0"]["CODIGO"]+1, 
                'CI' => $datos['data']['cedula'],
                'NOMBRE'=> $datos['data']['name'],
                'RUC'=> $datos['data']['ruc'],
                'DIRECCION' => $datos['data']['direccion'],
                'RAZON_SOCIAL' => $datos['data']['razonSocial'],
                'CIUDAD' => $datos['data']['ciudad'],
                'TELEFONO' => $datos['data']['telefono'],
                'CELULAR' => $datos['data']['celular'],
                'EMAIL' => $datos['data']['email'],
                'TIPO' => $datos['data']['tipo'],
                'FK_EMPRESA' => $datos['data']['idEmpresa'],
                'LIMITE_CREDITO' => $datos['data']['limite'],
                'DIAS_CREDITO' => $datos['data']['diaLimite'],
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
                    'RAZON_SOCIAL' => $datos['data']['razonSocial'],
                    'CIUDAD' => $datos['data']['ciudad'],
                    'TELEFONO' => $datos['data']['telefono'],
                    'CELULAR' => $datos['data']['celular'],
                    'EMAIL' => $datos['data']['email'],
                    'TIPO' => $datos['data']['tipo'],
                    'LIMITE_CREDITO' => $datos['data']['limite'],
                    'DIAS_CREDITO' => $datos['data']['diaLimite'],
                    'FK_EMPRESA' => $datos['data']['idEmpresa'],
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
    
    public static function nuevoCliente(){

        $user = auth()->user();

        //OBTENER EL ULTIMO CODIGO DE CLIENTE

        $cliente = Cliente::select('CODIGO')
                        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
                        ->orderby('CODIGO','DESC')
                        ->limit(1)
                        ->get()
                        ->toArray();

        $limiteDia = Parametro::consultaPersonalizada('LIMITE_DIAS');

        // RETORNAR EL VALOR

        return ["cliente" => $cliente, "limite"=>$limiteDia];
    }

    public static function eliminarClientes($datos){

        $user = auth()->user();

        try {

            // ELIMINA SI EXISTE

            if ($datos['data']['existe']=== true){

                $venta = Cliente::existe_venta($datos['data']['codigo']);

                if($venta['response'] == false){

                    return $venta;
                }

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

    public static function existe_venta($codigo){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // VERIFICAR SI EXISTE 

        $cliente = DB::connection('retail')
        ->table('ventas')
        ->select(DB::raw(
                        'CODIGO'
                    ))
        ->where('ID_SUCURSAL','=', $user->id_sucursal)
        ->where('CLIENTE','=', $codigo)
        ->limit(1)
        ->get();

        /*  --------------------------------------------------------------------------------- */
        
        // RETORNAL VALOR 

        if(count($cliente) > 0){

            return ["response"=>false,'statusText'=> 'Existe una venta con este cliente'];
        } else {

            return ["response"=>true];
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function verificarRuc($data){

        /*  --------------------------------------------------------------------------------- */

        $user = auth()->user();
        $ruc = $data;

        //OBTENER EL ULTIMO CODIGO DE CLIENTE

        $cliente = Cliente::select(
                                    'CODIGO'
                                )
                        ->where('RUC', '=', $ruc)
                        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
                        ->limit(1)
                        ->get()
                        ->toArray();

        // RETORNAR EL VALOR

        if(count($cliente) > 0 && !empty($ruc)){

            return  ["response"=>false,'statusText'=> 'Ya existe un cliente con RUC: '.$data];
        } else {

            // MUESTRA QUE YA EXISTE

            return ["response"=>true];

        }

        /*  --------------------------------------------------------------------------------- */

    }
    
    public static function verificarCI($data){
        
        /*  --------------------------------------------------------------------------------- */
        $user = auth()->user();
        $ci = $data;

        //OBTENER EL ULTIMO CODIGO DE CLIENTE

        $cliente = Cliente::select(
                                    'CODIGO'
                                )
                        ->where('CI', '=', $ci)
                        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
                        ->limit(1)
                        ->get()
                        ->toArray();

        // RETORNAR EL VALOR

        if(count($cliente) > 0 && !empty($ci)){

             return  ["response"=>false,'statusText'=> 'Ya existe un cliente registrado con C.I: '.$data];
        } else {

            // MUESTRA QUE NO EXISTE

            return ["response"=>true];

        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function creditoCliente($datos){

        /*  --------------------------------------------------------------------------------- */

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODAS LOS CLIENTES

        $cliente= Cliente::select(DB::raw('LIMITE_CREDITO, DIAS_CREDITO, CREDITO_DISPONIBLE'))
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->Where('ID','=',$datos['data'])->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if(count($cliente) > 0){

            return ["response" => true, "cliente" => $cliente];

        } else {

           return ["response" => false];

        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function credito_cliente_datatable($request){

        /*  --------------------------------------------------------------------------------- */

        // INICIARA VARIABLES

        $user = auth()->user();

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 

                        0 => 'VENTAS.CLIENTE',
                        1 => 'CLIENTES.NOMBRE',
                        2 => 'CLIENTES.CELULAR',
                        3 => 'CLIENTES.TELEFONO',
                        4 => 'MONTO',
                    );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE CLIENTES ENCONTRADOS 

        $totalData = Venta::rightjoin('VENTAS_CREDITO', 'VENTAS_CREDITO.FK_VENTA', '=', 'VENTAS.ID')
                         ->where('VENTAS.ID_SUCURSAL', '=', $user->id_sucursal)
                         ->groupBy('VENTAS.CLIENTE')
                         ->get();


        $totalData = count($totalData);

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

            $posts = Venta::select(DB::raw('VENTAS.CLIENTE,  CLIENTES.NOMBRE, CLIENTES.CELULAR, CLIENTES.TELEFONO , SUM(VENTAS_CREDITO.MONTO) AS MONTO'))
                         ->rightjoin('VENTAS_CREDITO', 'VENTAS_CREDITO.FK_VENTA', '=', 'VENTAS.ID')
                         ->leftJoin('CLIENTES', function($join){
                            $join->on('CLIENTES.CODIGO', '=', 'VENTAS.CLIENTE')
                                 ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
                         })
                         ->where('VENTAS.ID_SUCURSAL', '=', $user->id_sucursal)
                         ->groupBy('VENTAS.CLIENTE')
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();

            /*  ************************************************************ */

        }else{

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            // CARGAR LOS CLIENTES FILTRADOS EN DATATABLE
            $posts =        Venta::select(DB::raw('VENTAS.CLIENTE,  CLIENTES.NOMBRE, CLIENTES.CELULAR, CLIENTES.TELEFONO , SUM(VENTAS_CREDITO.MONTO) AS MONTO'))
                            ->rightjoin('VENTAS_CREDITO', 'VENTAS_CREDITO.FK_VENTA', '=', 'VENTAS.ID')
                            ->leftJoin('CLIENTES', function($join){
                                $join->on('CLIENTES.CODIGO', '=', 'VENTAS.CLIENTE')
                                     ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
                            })
                            ->where(function ($query) use ($search) {
                                $query->where('VENTAS.CLIENTE','LIKE',"%{$search}%")
                                      ->orWhere('CLIENTES.NOMBRE', 'LIKE',"%{$search}%");
                            })
                            ->where('VENTAS.ID_SUCURSAL', '=', $user->id_sucursal)
                            ->groupBy('VENTAS.CLIENTE')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            // CARGAR LA CANTIDAD DE CLIENTES FILTRADOS 

            $totalFiltered = Venta::rightjoin('VENTAS_CREDITO', 'VENTAS_CREDITO.FK_VENTA', '=', 'VENTAS.ID')
                            ->leftJoin('CLIENTES', function($join){
                                $join->on('CLIENTES.CODIGO', '=', 'VENTAS.CLIENTE')
                                     ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
                            })
                            ->where(function ($query) use ($search) {
                                $query->where('VENTAS.CLIENTE','LIKE',"%{$search}%")
                                      ->orWhere('CLIENTES.NOMBRE', 'LIKE',"%{$search}%");
                            })
                            ->where('VENTAS.ID_SUCURSAL', '=', $user->id_sucursal)
                            ->groupBy('VENTAS.CLIENTE')
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

                $nestedData['CODIGO'] = $post->CLIENTE;
                $nestedData['NOMBRE'] = $post->NOMBRE;
                $nestedData['CELULAR'] = $post->CELULAR;
                $nestedData['TELEFONO'] = $post->TELEFONO;
                $nestedData['MONTO'] = $post->MONTO;

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

    public static function empresasDatatable($request){

        // INICIARA VARIABLES

        $user = auth()->user();

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                        0 => 'ID',
                        1 => 'NOMBRE'
                    );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE CLIENTES ENCONTRADOS 

        $totalData = DB::connection('retail')->table('empresas')->count();

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
            $posts = DB::connection('retail')->table('empresas')->select(DB::raw('ID, NOMBRE'))
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();

            /*  ************************************************************ */

        }else{

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            // CARGAR LOS CLIENTES FILTRADOS EN DATATABLE

            $posts = DB::connection('retail')->table('empresas')->select(DB::raw('ID, NOMBRE'))
                            ->where(function ($query) use ($search) {
                                $query->Where('NOMBRE', 'LIKE',"%{$search}%")
                                      ->orWhere('ID', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            // CARGAR LA CANTIDAD DE CLIENTES FILTRADOS 

            $totalFiltered = DB::connection('retail')->table('empresas')->where(function ($query) use ($search) {
                                $query->Where('NOMBRE', 'LIKE',"%{$search}%")
                                      ->orWhere('ID', 'LIKE',"%{$search}%");
                            })->count();

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
}
