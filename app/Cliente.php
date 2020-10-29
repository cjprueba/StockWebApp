<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Venta;
use App\VentaAbono;

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
                            7 => 'TIPO'
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

            $posts = Cliente::select(DB::raw('CODIGO, CI, NOMBRE, RUC, DIRECCION, CIUDAD, TELEFONO, TIPO, RETENTOR'))
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

            $posts = Cliente::select(DB::raw('CODIGO, CI, NOMBRE, RUC, DIRECCION, CIUDAD, TELEFONO, TIPO, RETENTOR'))
                            ->where('ID_SUCURSAL','=', $user->id_sucursal)
                            ->where(function ($query) use ($search) {
                                $query->where('CI','LIKE',"%{$search}%")
                                      ->orWhere('RUC', 'LIKE',"%{$search}%")
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
                                      ->orWhere('RUC', 'LIKE',"%{$search}%")
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
                $nestedData['NOMBRE'] = utf8_encode($post->NOMBRE);
                $nestedData['RUC'] = $post->RUC;
                $nestedData['DIRECCION'] = utf8_encode($post->DIRECCION);
                $nestedData['CIUDAD'] = utf8_encode($post->CIUDAD);
                $nestedData['TELEFONO'] = $post->TELEFONO;
                $nestedData['TIPO'] = $post->TIPO;
                $nestedData['RETENTOR'] = $post->RETENTOR;
                
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
                $nestedData['NOMBRE'] = ucwords(utf8_encode($cliente));
                $nestedData['CODIGO'] = $post->CODIGO;
                $nestedData['CI'] = utf8_encode($post->CI);
                $nestedData['RUC'] = utf8_encode($post->RUC);
                $nestedData['TELEFONO'] = utf8_encode($post->TELEFONO);
                $nestedData['RAZON_SOCIAL'] = utf8_encode($post->RAZON_SOCIAL);
                $nestedData['DIRECCION'] = utf8_encode($post->DIRECCION);
                $nestedData['CIUDAD'] = utf8_encode($post->CIUDAD);

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
                        CLIENTES.CREDITO_DISPONIBLE,
                        CLIENTES.RETENTOR'))
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

            /*  --------------------------------------------------------------------------------- */

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
                'ID_SUCURSAL' => $user->id_sucursal,
                'RETENTOR' => $datos['data']['retentor']]);

            }else{

                /*  --------------------------------------------------------------------------------- */

                // OBTENER ID CLIENTE 

                $id_cliente = (Cliente::id_cliente($datos['data']['codigo']))['ID_CLIENTE'];

                /*  --------------------------------------------------------------------------------- */

                // OBTENER SALDO

                $cliente = (Cliente::obtenerSaldo($id_cliente))["cliente"];

                /*  --------------------------------------------------------------------------------- */

                // RETORNAR SALDO 

                if ($cliente->SALDO > $datos['data']['limite']) {
                    return ["response" => false, "statusText" => "Error, tu limite no puede ser menor a tu saldo"];
                }

                /*  --------------------------------------------------------------------------------- */

                // MODFICAR CLIENTE 

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
                    'HORMODIF'=>$hora,
                    'RETENTOR' => $datos['data']['retentor']]);

                /*  --------------------------------------------------------------------------------- */

                // ACTUALIZAR CREDITO 

                Cliente::actualizarCredito($id_cliente);

                /*  --------------------------------------------------------------------------------- */

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

        $cliente= Cliente::select(DB::raw('IFNULL(LIMITE_CREDITO,0) AS LIMITE_CREDITO, IFNULL(DIAS_CREDITO,0) DIAS_CREDITO, IFNULL(CREDITO_DISPONIBLE,0) AS CREDITO_DISPONIBLE'))
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->where('CODIGO','=',$datos['data'])
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if(count($cliente) > 0){

            return ["response" => true, "cliente" => $cliente[0], "disponible"];

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
                        4 => 'DEUDA',
                    );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE CLIENTES ENCONTRADOS 

        $totalData = Venta::rightjoin('VENTAS_CREDITO', 'VENTAS_CREDITO.FK_VENTA', '=', 'VENTAS.ID')
                         ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'VENTAS.MONEDA')
                         ->where('VENTAS_CREDITO.SALDO', '>', 0)
                         ->where('VENTAS.ID_SUCURSAL', '=', $user->id_sucursal)
                         ->groupBy('VENTAS.CLIENTE', 'MONEDAS.CODIGO')
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

            $posts = Venta::select(DB::raw('VENTAS.CLIENTE,  CLIENTES.NOMBRE, CLIENTES.CELULAR, CLIENTES.TELEFONO, SUM(VENTAS_CREDITO.MONTO) AS MONTO, SUM(VENTAS_CREDITO.PAGO) AS PAGO, SUM(VENTAS_CREDITO.SALDO) AS DEUDA, VENTAS.MONEDA, MONEDAS.CANDEC'))
                         ->rightjoin('VENTAS_CREDITO', 'VENTAS_CREDITO.FK_VENTA', '=', 'VENTAS.ID')
                         ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'VENTAS.MONEDA')
                         ->leftJoin('CLIENTES', function($join){
                            $join->on('CLIENTES.CODIGO', '=', 'VENTAS.CLIENTE')
                                 ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
                         })
                         ->where('VENTAS_CREDITO.SALDO', '>', 0)
                         ->where('VENTAS.ID_SUCURSAL', '=', $user->id_sucursal)
                         ->groupBy('VENTAS.CLIENTE', 'MONEDAS.CODIGO')
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();

            /*  ************************************************************ */

        }else{

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            // CARGAR LOS CLIENTES FILTRADOS EN DATATABLE
            $posts =        Venta::select(DB::raw('VENTAS.CLIENTE,  CLIENTES.NOMBRE, CLIENTES.CELULAR, CLIENTES.TELEFONO, SUM(VENTAS_CREDITO.MONTO) AS MONTO, SUM(VENTAS_CREDITO.PAGO) AS PAGO, SUM(VENTAS_CREDITO.MONTO) AS DEUDA, VENTAS.MONEDA, MONEDA.CANDEC'))
                            ->rightjoin('VENTAS_CREDITO', 'VENTAS_CREDITO.FK_VENTA', '=', 'VENTAS.ID')
                            ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'VENTAS.MONEDA')
                            ->leftJoin('CLIENTES', function($join){
                                $join->on('CLIENTES.CODIGO', '=', 'VENTAS.CLIENTE')
                                     ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
                            })
                            ->where(function ($query) use ($search) {
                                $query->where('VENTAS.CLIENTE','LIKE',"%{$search}%")
                                      ->orWhere('CLIENTES.NOMBRE', 'LIKE',"%{$search}%");
                            })
                            ->where('VENTAS_CREDITO.SALDO', '>', 0)
                            ->where('VENTAS.ID_SUCURSAL', '=', $user->id_sucursal)
                            ->groupBy('VENTAS.CLIENTE', 'MONEDAS.CODIGO')
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
                            ->where('VENTAS_CREDITO.SALDO', '>', 0)
                            ->where('VENTAS.ID_SUCURSAL', '=', $user->id_sucursal)
                            ->groupBy('VENTAS.CLIENTE', 'VENTA.MONEDA')
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
                $nestedData['DEUDA'] = Common::precio_candec_sin_letra($post->DEUDA, $post->MONEDA);
                $nestedData['PAGO'] = Common::precio_candec_sin_letra($post->PAGO, $post->MONEDA);
                $nestedData['MONTO'] = Common::precio_candec_sin_letra($post->MONTO, $post->MONEDA);
                $nestedData['DEUDA_CRUDO'] = $post->DEUDA;
                $nestedData['MONEDA'] = $post->MONEDA;
                $nestedData['CANDEC'] = $post->CANDEC;
                $nestedData['ACCION'] = "&emsp;<a href='#' id='abonar' title='Detalle'><i class='fa fa-cash-register' aria-hidden='true'></i></a>
                    &emsp;<a href='#' id='imprimirTicket' title='Reporte'><i class='fa fa-file text-secondary' aria-hidden='true'></i></a>";

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

    public static function credito_cliente_datatable_detalle($request){

        /*  --------------------------------------------------------------------------------- */

        // INICIARA VARIABLES

        $user = auth()->user();

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                        0 => 'VENTAS.ID',
                        1 => 'VENTAS.FECALTAS',
                        2 => 'CLIENTES.CELULAR',
                        3 => 'CLIENTES.TELEFONO',
                        4 => 'DEUDA',
                    );
        
        /*  --------------------------------------------------------------------------------- */

        $codigo = $request->input('codigo');
        $dia = date("Y-m-d");

        // CONTAR LA CANTIDAD DE CLIENTES ENCONTRADOS 

        $totalData = Venta::rightjoin('VENTAS_CREDITO', 'VENTAS_CREDITO.FK_VENTA', '=', 'VENTAS.ID')
                         ->where('VENTAS.ID_SUCURSAL', '=', $user->id_sucursal)
                         ->where('VENTAS.CLIENTE', '=', $codigo)
                         // ->where('VENTAS_CREDITO.SALDO', '>', 0)
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

            $posts = Venta::select(DB::raw('VENTAS.ID,  CLIENTES.NOMBRE, CLIENTES.CELULAR, CLIENTES.TELEFONO , VENTAS_CREDITO.MONTO AS DEUDA, VENTAS.FECALTAS, VENTAS_CREDITO.FECHA_CREDITO_FIN, VENTAS_CREDITO.PAGO, VENTAS_CREDITO.SALDO, VENTAS.MONEDA'))
                         ->rightjoin('VENTAS_CREDITO', 'VENTAS_CREDITO.FK_VENTA', '=', 'VENTAS.ID')
                         ->leftJoin('CLIENTES', function($join){
                            $join->on('CLIENTES.CODIGO', '=', 'VENTAS.CLIENTE')
                                 ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
                         })
                         ->where('VENTAS.ID_SUCURSAL', '=', $user->id_sucursal)
                         ->where('VENTAS.CLIENTE', '=', $codigo)
                         // ->where('VENTAS_CREDITO.SALDO', '>', 0)
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();

            /*  ************************************************************ */

        }else{

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            // CARGAR LOS CLIENTES FILTRADOS EN DATATABLE
            $posts =        Venta::select(DB::raw('VENTAS.ID,  CLIENTES.NOMBRE, CLIENTES.CELULAR, CLIENTES.TELEFONO ,VENTAS_CREDITO.MONTO AS DEUDA, VENTAS.FECALTAS, VENTAS_CREDITO.FECHA_CREDITO_FIN, VENTAS_CREDITO.PAGO, VENTAS_CREDITO.SALDO, VENTAS.MONEDA'))
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
                            ->where('VENTAS.CLIENTE', '=', $codigo)
                            // ->where('VENTAS_CREDITO.SALDO', '>', 0)
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
                            ->where('VENTAS.CLIENTE', '=', $codigo)
                            // ->where('VENTAS_CREDITO.SALDO', '>', 0)
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

                $nestedData['CODIGO'] = $post->ID;
                $nestedData['DEUDA'] = Common::precio_candec($post->DEUDA, $post->MONEDA);
                $nestedData['VENCIMIENTO'] = $post->FECHA_CREDITO_FIN;
                $nestedData['FECALTAS'] = $post->FECALTAS;
                $nestedData['PAGO'] = Common::precio_candec($post->PAGO, $post->MONEDA);
                $nestedData['SALDO'] =  Common::precio_candec($post->SALDO, $post->MONEDA);

                if ($post->FECHA_CREDITO_FIN <= $dia) {
                    $nestedData['ESTATUS'] = 'table-danger';
                } else if ($post->SALDO > 0) {
                    $nestedData['ESTATUS'] = 'table-warning';
                } else {
                    $nestedData['ESTATUS'] = 'table-success';
                }
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

    public static function id_cliente($codigo){

        /*  --------------------------------------------------------------------------------- */

        // INICIARA VARIABLES

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER ID CLIENTE 

        $id = Cliente::select('ID')
        ->where('CODIGO', '=', $codigo)
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR ID CLIENTE 

        return ['ID_CLIENTE' => $id[0]->ID];

        /*  --------------------------------------------------------------------------------- */

    }

    public static function credito_cliente_datatable_abono($request){

        /*  --------------------------------------------------------------------------------- */

        // INICIARA VARIABLES

        $user = auth()->user();

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 

                        0 => 'VENTAS_ABONO.ID',
                        1 => 'VENTAS_ABONO.FECHA',
                        2 => 'VENTAS_ABONO.PAGO',
                        3 => 'VENTAS_ABONO.SALDO'
                    );
        
        /*  --------------------------------------------------------------------------------- */

        $codigo = $request->input('codigo');

        /*  --------------------------------------------------------------------------------- */

        // OBTENER ID CLIENTE 

        $id = (Cliente::id_cliente($codigo))['ID_CLIENTE'];

        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE CLIENTES ENCONTRADOS 

        $totalData = VentaAbono::where('VENTAS_ABONO.FK_CLIENTE', '=', $id)
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

        if(empty($request->input('search.value'))){            

            //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

            $posts = VentaAbono::select(DB::raw('VENTAS_ABONO.ID,  VENTAS_ABONO.FECHA, VENTAS_ABONO.PAGO, VENTAS_ABONO.SALDO, VENTAS_ABONO.MONEDA'))
                         ->where('VENTAS_ABONO.FK_CLIENTE', '=', $id)
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();

            /*  ************************************************************ */

        }else{

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            // CARGAR LOS CLIENTES FILTRADOS EN DATATABLE
            $posts =        VentaAbono::select(DB::raw('VENTAS_ABONO.ID,  VENTAS_ABONO.FECHA, VENTAS_ABONO.PAGO, VENTAS_ABONO.SALDO, VENTAS_ABONO.MONEDA'))
                            ->where('VENTAS_ABONO.FK_CLIENTE', '=', $id)
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            // CARGAR LA CANTIDAD DE CLIENTES FILTRADOS 

            $totalFiltered = Venta::where('VENTAS_ABONO.FK_CLIENTE', '=', $id)
                            ->where(function ($query) use ($search) {
                                $query->where('VENTAS_ABONO.PAGO','LIKE',"%{$search}%")
                                      ->orWhere('VENTAS_ABONO.FECHA', 'LIKE',"%{$search}%");
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

                $nestedData['CODIGO'] = $post->ID;
                $nestedData['FECHA'] = $post->FECHA;
                $nestedData['PAGO'] = Common::precio_candec($post->PAGO, $post->MONEDA);
                $nestedData['SALDO'] =  Common::precio_candec($post->SALDO, $post->MONEDA);

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

    public static function obtenerSaldo($cliente_id){

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $disponible = 0;

        /*  --------------------------------------------------------------------------------- */

        $cliente = Cliente::select(DB::raw('CLIENTES.LIMITE_CREDITO AS LIMITE, SUM(VENTAS_CREDITO.SALDO) AS SALDO'))
        ->leftJoin('VENTAS', function($join){
                            $join->on('VENTAS.CLIENTE', '=', 'CLIENTES.CODIGO')
                                 ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
        })
        ->leftjoin('VENTAS_CREDITO', 'VENTAS_CREDITO.FK_VENTA', '=', 'VENTAS.ID')
        ->where('CLIENTES.ID', '=', $cliente_id)
        ->where('VENTAS_CREDITO.SALDO', '>', 0)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        if($cliente[0]->SALDO === null) {
            $cliente[0]->SALDO = 0;
        }

        /*  --------------------------------------------------------------------------------- */

        return ["response" => true, "cliente" => $cliente[0], "statusText" => "Se obtuvo los datos de crédito"];

        /*  --------------------------------------------------------------------------------- */

    }

    public static function actualizarCredito($cliente_id){

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $cliente = (Cliente::obtenerSaldo($cliente_id))["cliente"];
        
        /*  --------------------------------------------------------------------------------- */

        // CALCULAR DISPONIBLE
       
        $disponible = $cliente->LIMITE - $cliente->SALDO;

         /*  --------------------------------------------------------------------------------- */

        $cliente = Cliente::where('ID', '=', $cliente_id)
        ->update([
            'CREDITO_DISPONIBLE' => $disponible
        ]);

        /*  --------------------------------------------------------------------------------- */

        return ["response" => true, "statusText" => "Se actualizo correctamente el credito"];

        /*  --------------------------------------------------------------------------------- */

    }
}
