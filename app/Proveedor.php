<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Common;
use App\Pagos_Prov;
use App\Pagos_Prov_Det;
use App\Compra;
use App\DevolucionProv;
use App\DevolucionProvDet;

class Proveedor extends Model
{

    protected $connection = 'retail';
    protected $table = 'proveedores';

     public static function obtener_proveedores()
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER TODOS LOS PROVEEDORES

    	$proveedores = Proveedor::select(DB::raw('CODIGO, NOMBRE AS DESCRIPCION'))
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($proveedores) {
        	return ['proveedores' => $proveedores];
        } else {
        	return ['proveedores' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
    public static function generarConsulta($datos) 
    {

        
         /*  --------------------------------------------------------------------------------- */

         // INCICIAR VARIABLES 

        $marcas[] = array();
        $categorias[] = array();
        $totales[] = array();
        $vales = array("v", "a", "l", "e", "%");
        $inicio = date('Y-m-d', strtotime($datos['Inicio']));
        $final = date('Y-m-d', strtotime($datos['Final']));
        $sucursal = $datos['Sucursal'];

        // CARGAR MARCAS 0 EN VENTAS

        //array_unshift($datos['Marcas'], 0);
        //var_dump($datos['Marcas']);
        /*  --------------------------------------------------------------------------------- */

        /*  *********** TODAS LAS VENTAS ENTRE LAS FECHAS INTERVALOS *********** */

        if ( $datos['AllBrand']) {
            $ventasdet = DB::connection('retail')->table('VENTASDET as v')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
             ->join('PRODUCTOS_AUX', 'PRODUCTOS_AUX.CODIGO', '=', 'v.COD_PROD')
            ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'PRODUCTOS.PROVEEDOR')
            ->leftjoin('LOTES',function($join){
             $join->on('v.LOTE','=','LOTES.LOTE')
             ->on('v.COD_PROD','=','LOTES.COD_PROD')
               ->on('LOTES.ID_SUCURSAL','=','v.ID_SUCURSAL');
         })
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->select(DB::raw('SUM(v.PRECIO) AS PRECIO'),
            DB::raw('SUM(v.CANTIDAD) AS VENDIDO'),
             DB::raw('SUM(LOTES.COSTO) AS COSTO'),
            DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = v.COD_PROD) AND (l.ID_SUCURSAL = v.ID_SUCURSAL))),0) AS STOCK'),
            DB::raw('PROVEEDORES.NOMBRE AS MARCA_NOMBRE'),
            DB::raw('LINEAS.DESCRIPCION AS LINEA_NOMBRE'),
            DB::raw('v.COD_PROD'),
            DB::raw('PRODUCTOS_AUX.PROVEEDOR AS MARCA'),
            DB::raw('PRODUCTOS.LINEA AS LINEA'))  
            ->whereBetween('v.FECALTAS', [$inicio , $final])
            ->where([
                ['v.ID_SUCURSAL', '=', $sucursal],
                ['v.ANULADO', '<>', 1],
                ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
            ])
            ->groupBy('v.COD_PROD')
            ->get()
            ->toArray(); 

            
        } else  {

            $ventasdet = DB::connection('retail')->table('VENTASDET as v')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
             ->leftjoin('LOTES',function($join){
             $join->on('v.LOTE','=','LOTES.LOTE')
             ->on('v.COD_PROD','=','LOTES.COD_PROD')
               ->on('LOTES.ID_SUCURSAL','=','v.ID_SUCURSAL');
         })
            ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'PRODUCTOS.PROVEEDOR')
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->select(DB::raw('SUM(v.PRECIO) AS PRECIO'),
            DB::raw('SUM(v.CANTIDAD) AS VENDIDO'),
             DB::raw('SUM(LOTES.COSTO) AS COSTO'),
            DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = v.COD_PROD) AND (l.ID_SUCURSAL = v.ID_SUCURSAL)) Group By v.COD_PROD),0) AS STOCK'),
            DB::raw('PROVEEDORES.NOMBRE AS MARCA_NOMBRE'),
            DB::raw('LINEAS.DESCRIPCION AS LINEA_NOMBRE'),
            DB::raw('v.COD_PROD'),
            DB::raw('PRODUCTOS.PROVEEDOR AS MARCA'),
            DB::raw('PRODUCTOS.LINEA AS LINEA'))  
            ->whereBetween('v.FECALTAS', [$inicio , $final])
            ->whereIn('PRODUCTOS.PROVEEDOR', $datos['Marcas'])
            ->where([
                ['v.ID_SUCURSAL', '=', $sucursal],
                ['v.ANULADO', '<>', 1],
                ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
            ])
            ->groupBy('v.COD_PROD')
            ->get()
            ->toArray(); 
        }

        /*  --------------------------------------------------------------------------------- */

        /*  *********** TODOS LOS DESCUENTOS GENERALES  *********** */

        $descuentos = DB::connection('retail')->table('VENTASDET as v')
        ->select(DB::raw('v.CODIGO'),
        DB::raw('substring(v.DESCRIPCION, 11, 15) AS PORCENTAJE'),
        DB::raw('v.CODIGO'),  
        DB::raw('v.CAJA'),
        DB::raw('v.ID_SUCURSAL'),
        DB::raw('v.ITEM'))  
        ->whereBetween('v.FECALTAS', [$inicio , $final])
        ->where([
            ['v.ID_SUCURSAL', '=', $sucursal],
            ['v.ANULADO', '<>', 1],
            ['v.DESCRIPCION', 'LIKE', 'DESCUENTO%'],
            ['v.COD_PROD', '=', 2],
        ])
        ->get(); 

       
        /*  --------------------------------------------------------------------------------- */

        foreach ($descuentos as $descuento) {

            /*  --------------------------------------------------------------------------------- */
            //REEMPLAZAR VALORES STRINGS EN LOS DESCUENTOS GENERALES
          
            $descuento->PORCENTAJE = str_replace($vales, "", $descuento->PORCENTAJE);
             //var_dump($descuento->PORCENTAJE);
            /*  *********** RECORRER LAS VENTAS CON LOS DESCUENTOS GENERALES *********** */

            if ( $datos['AllBrand']) {
                $ventas_con_descuentos = DB::connection('retail')->table('VENTASDET as v')
                ->select(DB::raw('v.COD_PROD'),
                DB::raw('v.PRECIO'),
                DB::raw('v.PRECIO_UNIT'),
                DB::raw('v.ITEM'))
                ->where([
                    ['v.ID_SUCURSAL', '=', $descuento->ID_SUCURSAL],
                    ['v.CODIGO', '=', $descuento->CODIGO],
                    ['v.CAJA', '=', $descuento->CAJA],
                    ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
                ])
                ->get();

            } else{

                $ventas_con_descuentos = DB::connection('retail')->table('VENTASDET as v')
                ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
                ->select(DB::raw('v.COD_PROD'),
                DB::raw('v.PRECIO'),
                DB::raw('v.PRECIO_UNIT'),
                DB::raw('v.ITEM'))  
                ->whereBetween('v.FECALTAS', [$inicio , $final])
                ->whereIn('PRODUCTOS.PROVEEDOR', $datos['Marcas'])
                
                ->where([
                    ['v.ID_SUCURSAL', '=', $descuento->ID_SUCURSAL],
                    ['v.CODIGO', '=', $descuento->CODIGO],
                    ['v.CAJA', '=', $descuento->CAJA],
                    ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
                ])
                ->get();

            }

            /*  --------------------------------------------------------------------------------- */

            /*  *********** EMPEZAR A MODIFICAR LOS VALORES DEL ARRAY *********** */

            foreach ($ventas_con_descuentos as $ventas_con_descuento) {
                if ($ventas_con_descuento->ITEM < $descuento->ITEM) {
                    $key = array_search($ventas_con_descuento->COD_PROD, array_column($ventasdet, 'COD_PROD'));
                    $ventasdet[$key]->PRECIO = (int)$ventasdet[$key]->PRECIO - (((int)$ventas_con_descuento->PRECIO * (float)$descuento->PORCENTAJE)/100);
                }
            }

            /*  --------------------------------------------------------------------------------- */
        }

        /*  --------------------------------------------------------------------------------- */

        unset($marcas[0]);
        unset($categorias[0]);
        unset($totales[0]);

        /*  --------------------------------------------------------------------------------- */

        // CREAR FILA PARA PRODUCTOS CON MARCAS INDEFINIDAS

        // $marcas[0]["CODIGO"] = 0;
        // $marcas[0]["MARCA"] = "INDEFINIDO";
        // $marcas[0]["TOTAL"] = 0;

        /*  --------------------------------------------------------------------------------- */
        foreach ($ventasdet as $key => $value) {


            /*  --------------------------------------------------------------------------------- */

            // CREAR ARRAY DE MARCAS

            if (array_key_exists($value->MARCA, $marcas))   {
                $marcas[$value->MARCA]["TOTAL"] += $value->PRECIO;
                $marcas[$value->MARCA]["COSTO"] += $value->COSTO;
                $marcas[$value->MARCA]["STOCK"] += $value->STOCK;
                $marcas[$value->MARCA]["VENDIDO"] += $value->VENDIDO;
            } else {
                $marcas[$value->MARCA]["CODIGO"] = $value->MARCA;
                $marcas[$value->MARCA]["MARCA"] = $value->MARCA_NOMBRE;
                $marcas[$value->MARCA]["STOCK"] = $value->STOCK;
                $marcas[$value->MARCA]["TOTAL"] = $value->PRECIO;
                $marcas[$value->MARCA]["COSTO"] = $value->COSTO;
                $marcas[$value->MARCA]["VENDIDO"] = $value->VENDIDO;
                $marcas[$value->MARCA]["STOCK_G"] = 0;
            }

             /*  --------------------------------------------------------------------------------- */

            // CREAR ARRAY DE CATEGORIAS

/*            if (array_key_exists($value->MARCA.''.$value->LINEA, $categorias))   {
                $categorias[$value->MARCA.''.$value->LINEA]["TOTAL"] += $value->PRECIO;
                $categorias[$value->MARCA.''.$value->LINEA]["STOCK"] += $value->STOCK;
                $categorias[$value->MARCA.''.$value->LINEA]["VENDIDO"] += $value->VENDIDO;
            } else {
                $categorias[$value->MARCA.''.$value->LINEA]["CODIGO"] = $value->LINEA;
                $categorias[$value->MARCA.''.$value->LINEA]["LINEA"] = $value->LINEA_NOMBRE;
                $categorias[$value->MARCA.''.$value->LINEA]["STOCK"] = $value->STOCK;
                $categorias[$value->MARCA.''.$value->LINEA]["TOTAL"] = $value->PRECIO;
                $categorias[$value->MARCA.''.$value->LINEA]["MARCA"] = $value->MARCA;
                $categorias[$value->MARCA.''.$value->LINEA]["VENDIDO"] = $value->VENDIDO;
                $categorias[$value->MARCA.''.$value->LINEA]["STOCK_G"] = 0;
            }*/

            /*  --------------------------------------------------------------------------------- */

        }

        /*  --------------------------------------------------------------------------------- */

        // BUSCAR STOCK GENERAL DE TODAS CATEGORIAS

        $stockGeneral = DB::connection('retail')
            ->table('LOTES as l')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'l.COD_PROD')
            ->select(DB::raw('SUM(l.CANTIDAD) AS CANTIDAD'),
            DB::raw('PRODUCTOS.MARCA'),
            DB::raw('PRODUCTOS.LINEA'))
            ->where('l.ID_SUCURSAL', '=', $sucursal)
            ->groupBy('PRODUCTOS.MARCA', 'PRODUCTOS.LINEA')
            ->get();

        foreach ($stockGeneral as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            if (array_key_exists($value->MARCA, $marcas))   {

                // CARGAR STOCK GENERAL A MARCA

                $marcas[$value->MARCA]["STOCK_G"] += $value->CANTIDAD;

            }
/*
            if (array_key_exists($value->MARCA.''.$value->LINEA, $categorias))   {

                // CARGAR STOCK GENERAL CATEGORIA

                $categorias[$value->MARCA.''.$value->LINEA]["STOCK_G"] += $value->CANTIDAD;
            }*/

            /*  --------------------------------------------------------------------------------- */
        }

        /*  --------------------------------------------------------------------------------- */

        $marca[] = (array) $marcas;
       /* $categoria[] = (array) $categorias;*/

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR TODOS LOS ARRAYS

        return ['ventas' => $ventasdet, 'marcas' => (array)$marca[0] ];

        /*  --------------------------------------------------------------------------------- */
    }

    public static function pago($data){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER CANDEC 

        $candec = (Parametro::candec($data["data"]["moneda"]))["CANDEC"];

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES
        
        $guaranies = Common::quitar_coma($data["data"]["pago"]["GUARANIES"], 2);
        if ($guaranies === '') {
            $guaranies = 0;
        } 

        $dolares = Common::quitar_coma($data["data"]["pago"]["DOLARES"], 2);
        if ($dolares === '') {
            $dolares = 0;
        } 

        $pesos = Common::quitar_coma($data["data"]["pago"]["PESOS"], 2);
        if ($pesos === '') {
            $pesos = 0;
        } 

        $reales = Common::quitar_coma($data["data"]["pago"]["REALES"], 2);
        if ($reales === '') {
            $reales = 0;
        }
        
        $fecha = $data["data"]["cabecera"]["FECHA"];
        $recibo = $data["data"]["cabecera"]["RECIBO"];
        $saldo = Common::quitar_coma($data["data"]["pago"]["SALDO"], $candec);
        $vuelto = Common::quitar_coma($data["data"]["pago"]["VUELTO"], $candec);
        $efectivo = Common::quitar_coma($data["data"]["pago"]["EFECTIVO"], $candec);
        $tarjeta = Common::quitar_coma($data["data"]["pago"]["TARJETA"], $candec);
        $codigo_tarjeta = $data["data"]["pago"]["CODIGO_TARJETA"];
        $cheques = $data["data"]["pago"]["CHEQUE"];
        $total = 0;
        $dia = date('Y-m-d');
        $hora = date("H:i:s");
        
        /*  --------------------------------------------------------------------------------- */

        // SELECCIONAR CUENTA 

        $deudas = Deuda::obtener_deudas($data["data"]["codigo"]);
        
        if ($deudas["response"] === false) {
            return $deudas;
        } else {
            $deudas = $deudas["deudas"];
        }

        /*  --------------------------------------------------------------------------------- */

        // OBTENER PAGO DEUDA - COMPRA

        $pagado = Pagos_Prov::obtener_pagos_deudas_compra($data["data"]["codigo"])["pagado"];

        /*  --------------------------------------------------------------------------------- */

        // PAGO

        $pago = [
            "FECHA" => $fecha, 
            "GUARANIES" => $guaranies, 
            "DOLARES" => $dolares, 
            "PESOS" => $pesos, 
            "REALES" => $reales,
            "PAGO" => $efectivo,
            "VUELTO" => $vuelto,
            "SALDO" => $saldo,
            "RECIBO" => $recibo,
            "FECALTAS" => $dia,
            "HORALTAS" => $hora,
            "FK_USER_CR" => $user->id,
            "FK_DEUDA" => 0
        ];

        /*  --------------------------------------------------------------------------------- */

        // INSERTAR PAGO PROVEEDOR 

        $pago_prov = Pagos_Prov::insertar($pago);

        /*  --------------------------------------------------------------------------------- */

        // INSERTAR PAGO TARJETA
        
        if ($codigo_tarjeta !== '0' && $codigo_tarjeta !== '' && $codigo_tarjeta !== NULL && !empty($codigo_tarjeta)) {
            if ($pago_prov["response"] === true && $codigo_tarjeta !== '') {
                $pago_tarjeta = Pagos_Prov_Tarjeta::guardar_referencia([
                    'FK_TARJETA' => $codigo_tarjeta,
                    'FK_PAGO_PROV' => $pago_prov["id"],
                    'MONTO' => $tarjeta,
                    'MONEDA' => 1
                ]);
            } else {
                return $pago_prov;
            }
        }
         
        /*  --------------------------------------------------------------------------------- */

        // INSERTAR PAGO CHEQUES 

        $pago_cheque = Pagos_Prov_Cheque::guardar_referencia($cheques, $pago_prov["id"]);

        if ($pago_cheque["response"] === false) {
            return $pago_cheque;
        }

        /*  --------------------------------------------------------------------------------- */
        
        // RECORRER DEUDAS 

        foreach ($deudas as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            // SI EL EFECTIVO ES 0 TERMINAR WHILE 
            
            if ((float)$efectivo === 0) {
                break;
            }

            /*  --------------------------------------------------------------------------------- */

            // INICIALIZAR EL TOTAL DE DEUDA Y SU ID

            $total = $value->TOTAL;
            $pago["FK_DEUDA"] = $value->ID;

            /*  --------------------------------------------------------------------------------- */

            // OBTENER LOS PAGOS DE LA DEUDA 

            $pagado_deuda = Pagos_Prov::obtener_pagos_deuda($value->ID);

            if ($pagado_deuda["response"] === true) {
                $total = $total - $pagado_deuda["pagado"];
            } 

            /*  --------------------------------------------------------------------------------- */

            // SI EFECTIVO ES MENOR O IGUAL AL TOTAL DE DEUDA
           
            if($total > $efectivo) {
               
                /*  --------------------------------------------------------------------------------- */

                // PAGO 

                $pago["PAGO"] = $efectivo;
                    
                /*  --------------------------------------------------------------------------------- */

                if ($pago_prov["response"] === true) {

                    /*  --------------------------------------------------------------------------------- */

                    // INSERTAR PAGO PROVEEDOR DET 

                    Pagos_Prov_Det::insertar([
                        "FK_PAGOS_PROV" => $pago_prov["id"],
                        "PAGO" => $pago["PAGO"],
                        "FK_DEUDA" =>  $pago["FK_DEUDA"] 
                    ]);

                    /*  --------------------------------------------------------------------------------- */

                    // CAMBIAR ESTADO 

                    Deuda::cambiar_estado($value->ID, 2);

                    /*  --------------------------------------------------------------------------------- */

                }

                /*  --------------------------------------------------------------------------------- */

                $efectivo = 0;
                
                /*  --------------------------------------------------------------------------------- */

            } else if ($total > 0) {
                
                /*  --------------------------------------------------------------------------------- */

                // RESTAR EFECTIVO A MEDIDA QUE VA DESCONTANDO 
                
                $efectivo = $efectivo - $total;

                /*  --------------------------------------------------------------------------------- */

                // PAGO 
               
                $pago["PAGO"] = $total;
                    
                /*  --------------------------------------------------------------------------------- */

                if ($pago_prov["response"] === true) {

                    /*  --------------------------------------------------------------------------------- */

                    // INSERTAR PAGO PROVEEDOR DET 

                    Pagos_Prov_Det::insertar([
                        "FK_PAGOS_PROV" => $pago_prov["id"],
                        "PAGO" => $pago["PAGO"],
                        "FK_DEUDA" =>  $pago["FK_DEUDA"] 
                    ]);

                    /*  --------------------------------------------------------------------------------- */

                    // CAMBIAR ESTADO 

                    Deuda::cambiar_estado($value->ID, 3);

                    /*  --------------------------------------------------------------------------------- */

                }

                /*  --------------------------------------------------------------------------------- */

            } else {
                return ["response" => false];
            }

            /*  --------------------------------------------------------------------------------- */

        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return ["response" => true, "statusText" => "Se ha guardado correctamente el pago !"];

        /*  --------------------------------------------------------------------------------- */

    }

     public static function datatable($request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'CODIGO', 
                            1 => 'RUC',
                            2 => 'DIRECCION',
                            3 => 'TELEFONO',
                            4 => 'CONTACTO',
                            5 => 'EMAIL',
                            6 => 'ACCION',
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = Proveedor::count();  
        
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

            $posts = Proveedor::select(DB::raw('CODIGO, RUC, NOMBRE, DIRECCION, TELEFONO, EMAIL'))
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

            $posts =  Proveedor::select(DB::raw('CODIGO, RUC, NOMBRE, DIRECCION, TELEFONO, EMAIL'))
                            ->where(function ($query) use ($search) {
                                $query->where('CODIGO','LIKE',"{$search}%")
                                      ->orWhere('NOMBRE', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = Proveedor::select(DB::raw('CODIGO, RUC, NOMBRE, DIRECCION, TELEFONO, EMAIL'))
                            ->where(function ($query) use ($search) {
                                $query->where('CODIGO','LIKE',"{$search}%")
                                      ->orWhere('NOMBRE', 'LIKE',"%{$search}%");
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

                $nestedData['CODIGO'] = $post->CODIGO;
                $nestedData['RUC'] = $post->RUC;
                $nestedData['NOMBRE'] = $post->NOMBRE;
                $nestedData['DIRECCION'] = $post->DIRECCION;
                $nestedData['TELEFONO'] = $post->TELEFONO;
                $nestedData['EMAIL'] = $post->EMAIL;
                
                $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrar' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a>&emsp;<a href='#' id='editar' title='Editar'><i class='fa fa-edit text-warning' aria-hidden='true'></i></a>&emsp;<a href='#' id='eliminar' title='Eliminar'><i class='fa fa-trash text-danger' aria-hidden='true'></i></a>
                    &emsp;<a href='#' id='reporte' title='Reporte'><i class='fa fa-file text-secondary' aria-hidden='true'></i></a>";

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

    public static function loteProducto($request) {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $codigo = $request->input('codigo');
        $proveedor = $request->input('proveedor');
        $moneda = $request->input('moneda');

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'COD_PROD', 
                            1 => 'COSTO',
                            2 => 'INICIAL',
                            3 => 'STOCK',
                            4 => 'VENCIMIENTO',
                            5 => 'LOTE',
                            6 => 'MONEDA',
                            7 => 'DECIMAL',
                            8 => 'DESCRIPCION',
                            9 => 'LOTE_ID'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = Compra::
                    leftJoin('COMPRASDET', function($join){
                        $join->on('COMPRAS.CODIGO', '=', 'COMPRASDET.CODIGO')
                             ->on('COMPRAS.ID_SUCURSAL', '=', 'COMPRASDET.ID_SUCURSAL');
                    })
                    ->leftJoin('LOTES', function($join){
                        $join->on('LOTES.COD_PROD', '=', 'COMPRASDET.COD_PROD')
                             ->on('LOTES.ID_SUCURSAL', '=', 'COMPRAS.ID_SUCURSAL')
                             ->on('LOTES.LOTE', '=', 'COMPRASDET.LOTE');
                    })
                    ->where([
                        'COMPRAS.PROVEEDOR' => $proveedor,
                        'COMPRASDET.COD_PROD' => $codigo,
                        'COMPRAS.MONEDA' => $moneda,
                        'COMPRAS.ID_SUCURSAL' => $user->id_sucursal,
                    ])
                    ->where('LOTES.CANTIDAD', '>', 0)
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

            $posts = Compra::select(DB::raw('COMPRASDET.COD_PROD, LOTES.COSTO, LOTES.CANTIDAD_INICIAL, LOTES.CANTIDAD, LOTES.FECHA_VENC, LOTES.LOTE, COMPRAS.MONEDA, MONEDAS.CANDEC, LOTES.ID AS LOTE_ID, PRODUCTOS.DESCRIPCION, PRODUCTOS.VENCIMIENTO'))
                    ->leftJoin('COMPRASDET', function($join){
                        $join->on('COMPRAS.CODIGO', '=', 'COMPRASDET.CODIGO')
                             ->on('COMPRAS.ID_SUCURSAL', '=', 'COMPRASDET.ID_SUCURSAL');
                    })
                    ->leftJoin('LOTES', function($join){
                        $join->on('LOTES.COD_PROD', '=', 'COMPRASDET.COD_PROD')
                             ->on('LOTES.ID_SUCURSAL', '=', 'COMPRAS.ID_SUCURSAL')
                             ->on('LOTES.LOTE', '=', 'COMPRASDET.LOTE');
                    })
                    ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'COMPRASDET.COD_PROD')
                    ->leftJoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'COMPRAS.MONEDA')
                    ->where([
                        'COMPRAS.PROVEEDOR' => $proveedor,
                        'COMPRASDET.COD_PROD' => $codigo,
                        'COMPRAS.MONEDA' => $moneda,
                        'COMPRAS.ID_SUCURSAL' => $user->id_sucursal,
                    ])
                    ->where('LOTES.CANTIDAD', '>', 0)
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

            $posts =  Compra::select(DB::raw('COMPRASDET.COD_PROD, LOTES.COSTO, LOTES.CANTIDAD_INICIAL, LOTES.CANTIDAD, LOTES.FECHA_VENC, LOTES.LOTE, COMPRAS.MONEDA, MONEDAS.CANDEC, LOTES.ID AS LOTE_ID, PRODUCTOS.DESCRIPCION, PRODUCTOS.VENCIMIENTO'))
                    ->leftJoin('COMPRASDET', function($join){
                        $join->on('COMPRAS.CODIGO', '=', 'COMPRASDET.CODIGO')
                             ->on('COMPRAS.ID_SUCURSAL', '=', 'COMPRASDET.ID_SUCURSAL');
                    })
                    ->leftJoin('LOTES', function($join){
                        $join->on('LOTES.COD_PROD', '=', 'COMPRASDET.COD_PROD')
                             ->on('LOTES.ID_SUCURSAL', '=', 'COMPRAS.ID_SUCURSAL')
                             ->on('LOTES.LOTE', '=', 'COMPRASDET.LOTE');
                    })
                    ->leftJoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'COMPRAS.MONEDA')
                    ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'COMPRASDET.COD_PROD')
                    ->where([
                        'COMPRAS.PROVEEDOR' => $proveedor,
                        'COMPRASDET.COD_PROD' => $codigo,
                        'COMPRAS.MONEDA' => $moneda,
                        'COMPRAS.ID_SUCURSAL' => $user->id_sucursal,
                    ])
                    ->where('LOTES.CANTIDAD', '>', 0)
                            ->where(function ($query) use ($search) {
                                $query->where('LOTES.LOTE','LIKE',"{$search}%")
                                      ->orWhere('LOTES.CANTIDAD', 'LIKE',"{$search}%");
                            })
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = Compra::leftJoin('COMPRASDET', function($join){
                                $join->on('COMPRAS.CODIGO', '=', 'COMPRASDET.CODIGO')
                                     ->on('COMPRAS.ID_SUCURSAL', '=', 'COMPRASDET.ID_SUCURSAL');
                            })
                            ->leftJoin('LOTES', function($join){
                                $join->on('LOTES.COD_PROD', '=', 'COMPRASDET.COD_PROD')
                                     ->on('LOTES.ID_SUCURSAL', '=', 'COMPRAS.ID_SUCURSAL')
                                     ->on('LOTES.LOTE', '=', 'COMPRASDET.LOTE');
                            })
                            ->where([
                                'COMPRAS.PROVEEDOR' => $proveedor,
                                'COMPRASDET.COD_PROD' => $codigo,
                                'COMPRAS.MONEDA' => $moneda,
                                'COMPRAS.ID_SUCURSAL' => $user->id_sucursal,
                            ])
                            ->where('LOTES.CANTIDAD', '>', 0)
                            ->where(function ($query) use ($search) {
                                    $query->where('LOTES.LOTE','LIKE',"{$search}%")
                                    ->orWhere('LOTES.CANTIDAD', 'LIKE',"{$search}%");
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

                $nestedData['COD_PROD'] = $post->COD_PROD;
                $nestedData['DESCRIPCION'] = $post->DESCRIPCION;
                $nestedData['COSTO'] = Common::formato_precio($post->COSTO, $post->CANDEC);
                $nestedData['INICIAL'] = $post->CANTIDAD_INICIAL;
                $nestedData['STOCK'] = $post->CANTIDAD;

                if ($post->VENCIMIENTO === 1) {
                    $nestedData['VENCIMIENTO'] = $post->FECHA_VENC;
                } else {
                    $nestedData['VENCIMIENTO'] = 'N/A';
                }
                
                $nestedData['LOTE'] = $post->LOTE;
                $nestedData['MONEDA'] = $post->MONEDA;
                $nestedData['DECIMAL'] = $post->CANDEC;
                $nestedData['LOTE_ID'] = $post->LOTE_ID;

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

    public static function devolucion($data) {

        try {

            /*  --------------------------------------------------------------------------------- */

            // GUARDAR DEVOLUCION PROVEEDOR 

            $devolucionProv = DevolucionProv::guardar([
                'FK_PROVEEDOR' => $data['data']['proveedor'],
                'OBSERVACION' => $data['data']['observacion'],
                'FK_MONEDA' => $data['data']['moneda'],
                'TOTAL' => Common::quitar_coma($data['data']['total'], 2)
            ]);

            /*  --------------------------------------------------------------------------------- */

            // GUARDAR DEVOLUCION PROVEEDOR DETALLE 

            if ($devolucionProv["response"] === true) {
                DevolucionProvDet::guardar($data['data']['productos'], $devolucionProv["id"]);
            } else {
                return $devolucionProv;
            }

            /*  --------------------------------------------------------------------------------- */

            // RETORNAR VALOR 

            return ["response" => true, "statusText" => "Se ha realizado con éxito la devolución"];

            /*  --------------------------------------------------------------------------------- */

        } catch (Exception $e) {

            /*  --------------------------------------------------------------------------------- */

            // ERROR 

            Log::error('Devolucion Prov: Error al guardar.', ['PROVEEDOR' => $data["FK_PROVEEDOR"]]);

            /*  --------------------------------------------------------------------------------- */

            return ["response" => false, "statusText" => "Error al guardar devolución proveedor"];

            /*  --------------------------------------------------------------------------------- */
        }

    }

    public static function devolucionMostrar($request) {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'CODIGO', 
                            1 => 'PROVEEDOR',
                            2 => 'OBSERVACION',
                            3 => 'TOTAL',
                            4 => 'CREACION',
                            5 => 'ACCION',
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = DevolucionProv::
                     where('ID_SUCURSAL', '=', $user->id_sucursal)
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

            $posts = DevolucionProv::select(DB::raw('DEVOLUCION_PROV.CODIGO, PROVEEDORES.NOMBRE, DEVOLUCION_PROV.OBSERVACION, DEVOLUCION_PROV.TOTAL, DEVOLUCION_PROV.FECALTAS,  DEVOLUCION_PROV.FK_MONEDA AS MONEDA'))
                         ->leftjoin('PROVEEDORES', 'DEVOLUCION_PROV.FK_PROVEEDOR', '=', 'PROVEEDORES.CODIGO')
                         ->where('ID_SUCURSAL', '=', $user->id_sucursal)
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

            $posts =  DevolucionProv::select(DB::raw('DEVOLUCION_PROV.CODIGO, PROVEEDORES.NOMBRE, DEVOLUCION_PROV.OBSERVACION, DEVOLUCION_PROV.TOTAL, DEVOLUCION_PROV.FECALTAS,  DEVOLUCION_PROV.FK_MONEDA AS MONEDA'))
                         ->leftjoin('PROVEEDORES', 'DEVOLUCION_PROV.FK_PROVEEDOR', '=', 'PROVEEDORES.CODIGO')
                         ->where('ID_SUCURSAL', '=', $user->id_sucursal)
                            ->where(function ($query) use ($search) {
                                $query->where('DEVOLUCION_PROV.CODIGO','LIKE',"{$search}%")
                                      ->orWhere('PROVEEDORES.NOMBRE', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = DevolucionProv::leftjoin('PROVEEDORES', 'DEVOLUCION_PROV.FK_PROVEEDOR', '=', 'PROVEEDORES.CODIGO')
                         ->where('ID_SUCURSAL', '=', $user->id_sucursal)
                            ->where(function ($query) use ($search) {
                                $query->where('DEVOLUCION_PROV.CODIGO','LIKE',"{$search}%")
                                      ->orWhere('PROVEEDORES.NOMBRE', 'LIKE',"%{$search}%");
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

                $nestedData['CODIGO'] = $post->CODIGO;
                $nestedData['PROVEEDOR'] = $post->NOMBRE;
                $nestedData['OBSERVACION'] = $post->OBSERVACION;
                $nestedData['TOTAL'] = Common::precio_candec($post->TOTAL, $post->MONEDA);
                $nestedData['CREACION'] = $post->FECALTAS;
                $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrar' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a>";
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


    public static function devolucionDetalle($request) {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $codigo = $request->input('codigo');
        $c = 0;

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'COD_PROD', 
                            1 => 'COSTO',
                            2 => 'INICIAL',
                            3 => 'STOCK',
                            4 => 'VENCIMIENTO',
                            5 => 'LOTE',
                            6 => 'MONEDA',
                            7 => 'DECIMAL',
                            8 => 'DESCRIPCION',
                            9 => 'LOTE_ID'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = DevolucionProvDet::
                    leftjoin('DEVOLUCION_PROV', 'DEVOLUCION_PROV.ID', '=', 'DEVOLUCION_PROV_DET.FK_DEVOLUCION_PROV')
                    ->where([
                        'DEVOLUCION_PROV.ID_SUCURSAL' => $user->id_sucursal,
                        'DEVOLUCION_PROV.CODIGO' => $codigo
                    ])
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

            $posts = DevolucionProvDet::select(DB::raw('0 AS ITEM, DEVOLUCION_PROV_DET.COD_PROD, PRODUCTOS.DESCRIPCION, DEVOLUCION_PROV_DET.CANTIDAD, DEVOLUCION_PROV_DET.COSTO, DEVOLUCION_PROV_DET.COSTO_TOTAL, LOTES.LOTE'))
                ->leftjoin('DEVOLUCION_PROV', 'DEVOLUCION_PROV.ID', '=', 'DEVOLUCION_PROV_DET.FK_DEVOLUCION_PROV')
                ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'DEVOLUCION_PROV_DET.COD_PROD')
                ->leftjoin('LOTES', 'LOTES.ID', '=', 'DEVOLUCION_PROV_DET.FK_ID_LOTE')
                    ->where([
                        'DEVOLUCION_PROV.ID_SUCURSAL' => $user->id_sucursal,
                        'DEVOLUCION_PROV.CODIGO' => $codigo
                    ])
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

            $posts = DevolucionProvDet::select(DB::raw('0 AS ITEM, DEVOLUCION_PROV_DET.COD_PROD, PRODUCTOS.DESCRIPCION, DEVOLUCION_PROV_DET.CANTIDAD, DEVOLUCION_PROV_DET.COSTO, DEVOLUCION_PROV_DET.COSTO_TOTAL, LOTES.LOTE'))
                ->leftjoin('DEVOLUCION_PROV', 'DEVOLUCION_PROV.ID', '=', 'DEVOLUCION_PROV_DET.FK_DEVOLUCION_PROV')
                ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'DEVOLUCION_PROV_DET.COD_PROD')
                ->leftjoin('LOTES', 'LOTES.ID', '=', 'DEVOLUCION_PROV_DET.FK_ID_LOTE')
                    ->where([
                        'DEVOLUCION_PROV.ID_SUCURSAL' => $user->id_sucursal,
                        'DEVOLUCION_PROV.CODIGO' => $codigo
                    ])
                            ->where(function ($query) use ($search) {
                                $query->where('DEVOLUCION_PROV_DET.COD_PROD','LIKE',"{$search}%")
                                      ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"{$search}%");
                            })
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = DevolucionProvDet::leftjoin('DEVOLUCION_PROV', 'DEVOLUCION_PROV.ID', '=', 'DEVOLUCION_PROV_DET.FK_DEVOLUCION_PROV')
                ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'DEVOLUCION_PROV_DET.COD_PROD')
                ->leftjoin('LOTES', 'LOTES.ID', '=', 'DEVOLUCION_PROV_DET.FK_ID_LOTE')
                    ->where([
                        'DEVOLUCION_PROV.ID_SUCURSAL' => $user->id_sucursal,
                        'DEVOLUCION_PROV.CODIGO' => $codigo
                    ])
                            ->where(function ($query) use ($search) {
                                $query->where('DEVOLUCION_PROV_DET.COD_PROD','LIKE',"{$search}%")
                                      ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"{$search}%");
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

                $c = $c + 1;
                $nestedData['ITEM'] = $c;
                $nestedData['COD_PROD'] = $post->COD_PROD;
                $nestedData['CANTIDAD'] = $post->CANTIDAD;
                $nestedData['DESCRIPCION'] = $post->DESCRIPCION;
                $nestedData['COSTO'] = Common::formato_precio($post->COSTO, $post->CANDEC);
                $nestedData['COSTO_TOTAL'] = Common::formato_precio($post->COSTO_TOTAL, $post->CANDEC);
                $nestedData['LOTE'] = $post->LOTE;

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


              public static function filtrar_proveedor($datos)
    {

        /*  --------------------------------------------------------------------------------- */
     
        // OBTENER TODOS LOS DATOS DEL TALLE
        $proveedor = Proveedor::select(DB::raw('CODIGO, NOMBRE,RUC,DIRECCION,TELEFONO,CELULAR,EMAIL,CONTACTO'))->Where('CODIGO','=',$datos['id'])->get()->toArray();
        if(count($proveedor)<=0){
           return ["response"=>false];
        }
        // RETORNAR EL VALOR

       return ["response"=>true,"proveedor"=>$proveedor];

        /*  --------------------------------------------------------------------------------- */

    }
            public static function proveedor_guardar($datos)
    {
        $user = auth()->user();
        $dia = date("Y-m-d");
        $hora = date("H:i:s");
        $codigo_proveedor=$datos['data']['Codigo'];


        try { 

        /*  --------------------------------------------------------------------------------- */
         if($datos['data']['Existe']=== false){
         $proveedor=Proveedor::insertGetId(
         ['NOMBRE'=> $datos['data']['Descripcion'],'RUC'=> $datos['data']['Ruc'], 'DIRECCION'=> $datos['data']['Direccion'],'TELEFONO'=> $datos['data']['Telefono'],'celular'=> $datos['data']['cel'],'CONTACTO'=> $datos['data']['contacto'],'EMAIL'=> $datos['data']['email'],'USER'=>$user->name,'FECALTAS'=>$dia,'HORALTAS'=>$hora]);
         $codigo_proveedor = $proveedor;
        }else{
            $proveedor=Proveedor::where('CODIGO', $codigo_proveedor)
            ->update(['NOMBRE'=> $datos['data']['Descripcion'],'RUC'=> $datos['data']['Ruc'], 'DIRECCION'=> $datos['data']['Direccion'],'TELEFONO'=> $datos['data']['Telefono'],'celular'=> $datos['data']['cel'],'CONTACTO'=> $datos['data']['contacto'],'EMAIL'=> $datos['data']['email'], 'USERM'=>$user->name,'FECMODIF'=>$dia,'HORMODIF'=>$hora]);
        }
       return ["response"=>true];

        /*  --------------------------------------------------------------------------------- */
        } catch(Exception $ex){ 

 
        if($ex->errorInfo[1]===1062){
      return ["response"=>false,'statusText'=>'Esta descripcion de proveedor ya fue registrada!!'];
       }else{
      return ["response"=>false,'statusText'=>$ex->getMessage()];
      }
  
      }


    }
         public static function proveedor_eliminar($datos)
    {
        $user = auth()->user();
        $dia = date("Y-m-d");
        $hora = date("H:i:s");
        $codigo_proveedor=$datos['data']['Codigo'];
        /*  --------------------------------------------------------------------------------- */

        if($datos['data']['Existe']=== true){
        
         $producto=Producto::select('CODIGO')->Where('PROVEEDOR','=',$codigo_proveedor)->limit(1)->get()->toArray();
         if(count($producto)>0){
            return ["response"=>false];
         }else{
            $tela=Proveedor::where('CODIGO', $codigo_proveedor)->delete();
                    }

        }else{
        return ["response"=>false];
        }
  return ["response"=>true];

        /*  --------------------------------------------------------------------------------- */

    }
public static function proveedor_datatable($request)
    {
 /*  --------------------------------------------------------------------------------- */

        // INICIARA VARIABLES

        //global $search;

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 



        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'codigo', 
                            1 => 'nombre',
                            2 => 'ruc',
                            3 => 'direccion',
                            5 => 'telefono',
                            6 => 'contacto',
                            7 => 'celular',
                            8 => 'email'
                         
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = Proveedor::count();  
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        /*  ------------------------------------- -------------------------------------------- */

        // REVISAR SI EXISTE VALOR EN VARIABLE SEARCH

        if(empty($request->input('search.value')))
        {            

            /*  ************************************************************ */

            //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

            $posts = Proveedor::select(DB::raw('CODIGO,NOMBRE,RUC,DIRECCION,TELEFONO,CONTACTO,CELULAR,EMAIL'))
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

            $posts =Proveedor::select(DB::raw('CODIGO,NOMBRE,RUC,DIRECCION,TELEFONO,CONTACTO,CELULAR,EMAIL'))
                            ->where(function ($query) use ($search) {
                                $query->where('CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('NOMBRE', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = Proveedor::where(function ($query) use ($search) {
                                $query->where('CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('NOMBRE', 'LIKE',"%{$search}%");
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

                $nestedData['CODIGO'] = $post->CODIGO;
                $nestedData['NOMBRE'] = $post->NOMBRE;
                $nestedData['RUC'] = $post->RUC;
                $nestedData['DIRECCION'] = $post->DIRECCION;
                $nestedData['TELEFONO'] = $post->TELEFONO;
                $nestedData['CONTACTO'] = $post->CONTACTO;
                $nestedData['CELULAR'] = $post->CELULAR;
                $nestedData['EMAIL'] = $post->EMAIL;
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
