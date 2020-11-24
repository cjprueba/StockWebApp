<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;
use App\Common;
class Vendedores extends Model
{

    protected $connection = 'retail';
    protected $table = 'empleados';

    public static function generarConsulta($datos) 
    {

        
        /*  --------------------------------------------------------------------------------- */

        // INCICIAR VARIABLES 

        $marcas[] = array();
        $categorias[] = array();
        $totales[] = array();

        $inicio = date('Y-m-d', strtotime($datos['Inicio']));
        $final = date('Y-m-d', strtotime($datos['Final']));
        $sucursal = $datos['Sucursal'];

        // CARGAR MARCAS 0 EN VENTAS

        //array_unshift($datos['Marcas'], 0);
        //var_dump($datos['Marcas']);
        /*  --------------------------------------------------------------------------------- */

        /*  *********** TODAS LAS VENTAS ENTRE LAS FECHAS INTERVALOS *********** */

        if ($datos['AllCategory'] AND $datos['AllBrand']) {
            $ventasdet = DB::connection('retail')->table('VENTASDET as v')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
                ->leftjoin('ventas AS ven',function($join){
          $join->on('ven.CODIGO','=','v.CODIGO')
               ->on('ven.ID_SUCURSAL','=','v.ID_SUCURSAL')
                ->on('ven.CAJA','=','v.CAJA');
               })
                ->leftjoin('EMPlEADOS AS e',function($join){
          $join->on('e.CODIGO','=','ven.VENDEDOR')
               ->on('e.ID_SUCURSAL','=','v.ID_SUCURSAL');
               })
            ->select(DB::raw('SUM(v.PRECIO) AS PRECIO'),
            DB::raw('SUM(v.CANTIDAD) AS VENDIDO'),
            DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = v.COD_PROD) AND (l.ID_SUCURSAL = v.ID_SUCURSAL))),0) AS STOCK'),
            DB::raw('e.NOMBRE AS MARCA_NOMBRE'),
            DB::raw('MARCA.DESCRIPCION  AS LINEA_NOMBRE'),
            DB::raw('v.COD_PROD'),
             DB::raw('PRODUCTOS.MARCA AS LINEA'), 
            DB::raw('e.CODIGO AS MARCA'))
            ->whereBetween('v.FECALTAS', [$inicio , $final])
            ->where([
                ['v.ID_SUCURSAL', '=', $sucursal],
                ['v.ANULADO', '<>', 1],
                ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
            ])
            ->groupBy('v.COD_PROD')
            ->get()
            ->toArray(); 


        } else if ($datos['AllCategory']) {
            
            $ventasdet = DB::connection('retail')->table('VENTASDET as v')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
                ->leftjoin('ventas AS ven',function($join){
          $join->on('ven.CODIGO','=','v.CODIGO')
               ->on('ven.ID_SUCURSAL','=','v.ID_SUCURSAL')
                ->on('ven.CAJA','=','v.CAJA');
               })
                ->leftjoin('EMPlEADOS AS e',function($join){
          $join->on('e.CODIGO','=','ven.VENDEDOR')
               ->on('e.ID_SUCURSAL','=','v.ID_SUCURSAL');
               })
            ->select(DB::raw('SUM(v.PRECIO) AS PRECIO'),
            DB::raw('SUM(v.CANTIDAD) AS VENDIDO'),
            DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = v.COD_PROD) AND (l.ID_SUCURSAL = v.ID_SUCURSAL)) Group By v.COD_PROD),0) AS STOCK'),
            DB::raw('e.NOMBRE AS MARCA_NOMBRE'),
            DB::raw('MARCA.DESCRIPCION AS LINEA_NOMBRE'),
            DB::raw('v.COD_PROD'),
            DB::raw('PRODUCTOS.MARCA  AS LINEA'), 
            DB::raw('e.CODIGO AS MARCA')) 
            ->whereBetween('v.FECALTAS', [$inicio , $final])
            ->whereIn('PRODUCTOS.MARCA', $datos['Marcas'])
            ->where([
                ['v.ID_SUCURSAL', '=', $sucursal],
                ['v.ANULADO', '<>', 1],
                ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
            ])
            ->groupBy('v.COD_PROD')
            ->get()
            ->toArray(); 

        } else if ($datos['AllBrand']) {
             
            $ventasdet = DB::connection('retail')->table('VENTASDET as v')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
                ->leftjoin('ventas AS ven',function($join){
          $join->on('ven.CODIGO','=','v.CODIGO')
               ->on('ven.ID_SUCURSAL','=','v.ID_SUCURSAL')
                ->on('ven.CAJA','=','v.CAJA');
               })
                ->leftjoin('EMPlEADOS AS e',function($join){
          $join->on('e.CODIGO','=','ven.VENDEDOR')
               ->on('e.ID_SUCURSAL','=','v.ID_SUCURSAL');
               })
            ->select(DB::raw('SUM(v.PRECIO) AS PRECIO'),
            DB::raw('SUM(v.CANTIDAD) AS VENDIDO'),
            DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = v.COD_PROD) AND (l.ID_SUCURSAL = v.ID_SUCURSAL)) Group By v.COD_PROD),0) AS STOCK'),
            DB::raw('e.NOMBRE AS MARCA_NOMBRE'),
            DB::raw('MARCA.DESCRIPCION AS LINEA_NOMBRE'),
            DB::raw('v.COD_PROD'),
                 DB::raw('PRODUCTOS.MARCA AS LINEA'), 
            DB::raw('e.CODIGO AS MARCA')) 
            ->whereBetween('v.FECALTAS', [$inicio , $final])
            ->whereIn('ven.VENDEDOR', $datos['Categorias'])
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
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
            ->leftjoin('ventas AS ven',function($join){
          $join->on('ven.CODIGO','=','v.CODIGO')
               ->on('ven.ID_SUCURSAL','=','v.ID_SUCURSAL')
                ->on('ven.CAJA','=','v.CAJA');
               })
                ->leftjoin('EMPlEADOS AS e',function($join){
          $join->on('e.CODIGO','=','ven.VENDEDOR')
               ->on('e.ID_SUCURSAL','=','v.ID_SUCURSAL');
               })
            ->select(DB::raw('SUM(v.PRECIO) AS PRECIO'),
            DB::raw('SUM(v.CANTIDAD) AS VENDIDO'),
            DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = v.COD_PROD) AND (l.ID_SUCURSAL = v.ID_SUCURSAL)) Group By v.COD_PROD),0) AS STOCK'),
            DB::raw('e.NOMBRE AS MARCA_NOMBRE'),
            DB::raw('MARCA.DESCRIPCION AS LINEA_NOMBRE'),
            DB::raw('v.COD_PROD'),
            DB::raw('PRODUCTOS.MARCA AS LINEA'), 
            DB::raw('e.CODIGO AS MARCA'))  
            ->whereBetween('v.FECALTAS', [$inicio , $final])
            ->whereIn('PRODUCTOS.MARCA', $datos['Marcas'])
            ->whereIn('ven.VENDEDOR', $datos['Categorias'])
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
        DB::raw('substring(v.DESCRIPCION, 11, 3) AS PORCENTAJE'),
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

            /*  *********** RECORRER LAS VENTAS CON LOS DESCUENTOS GENERALES *********** */

            if ($datos['AllCategory'] AND $datos['AllBrand']) {
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

            } else if ($datos['AllCategory']) { 

                 $ventas_con_descuentos = DB::connection('retail')->table('VENTASDET as v')
                ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
                ->select(DB::raw('v.COD_PROD'),
                DB::raw('v.PRECIO'),
                DB::raw('v.PRECIO_UNIT'),
                DB::raw('v.ITEM'))  
                ->whereBetween('v.FECALTAS', [$inicio , $final])
                ->whereIn('PRODUCTOS.MARCA', $datos['Marcas'])
                ->where([
                    ['v.ID_SUCURSAL', '=', $descuento->ID_SUCURSAL],
                    ['v.CODIGO', '=', $descuento->CODIGO],
                    ['v.CAJA', '=', $descuento->CAJA],
                    ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
                ])
                ->get();

            } else if ($datos['AllBrand']) { 

                 $ventas_con_descuentos = DB::connection('retail')->table('VENTASDET as v')
                ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
             ->leftjoin('ventas',function($join){
          $join->on('ventas.CODIGO','=','v.CODIGO')
               ->on('ventas.ID_SUCURSAL','=','v.ID_SUCURSAL')
                ->on('ventas.CAJA','=','v.CAJA');
               })
                ->select(DB::raw('v.COD_PROD'),
                DB::raw('v.PRECIO'),
                DB::raw('ventas.VENDEDOR'),
                DB::raw('v.PRECIO_UNIT'),
                DB::raw('v.ITEM'))  
                ->whereBetween('v.FECALTAS', [$inicio , $final])
                ->whereIn('ventas.VENDEDOR', $datos['Categorias'])
                ->where([
                    ['v.ID_SUCURSAL', '=', $descuento->ID_SUCURSAL],
                    ['v.CODIGO', '=', $descuento->CODIGO],
                    ['v.CAJA', '=', $descuento->CAJA],
                    ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
                ])
                ->get();

            } else {

                $ventas_con_descuentos = DB::connection('retail')->table('VENTASDET as v')
                ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
                 ->leftjoin('ventas AS ven',function($join){
          $join->on('ven.CODIGO','=','v.CODIGO')
               ->on('ven.ID_SUCURSAL','=','v.ID_SUCURSAL')
                ->on('ven.CAJA','=','v.CAJA');
               })
                ->select(DB::raw('v.COD_PROD'),
                DB::raw('v.PRECIO'),
                DB::raw('v.PRECIO_UNIT'),
                DB::raw('v.ITEM'))  
                ->whereBetween('v.FECALTAS', [$inicio , $final])
                ->whereIn('PRODUCTOS.MARCA', $datos['Marcas'])
                ->whereIn('ven.VENDEDOR', $datos['Categorias'])
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
                    $ventasdet[$key]->PRECIO = (int)$ventasdet[$key]->PRECIO - (((int)$ventas_con_descuento->PRECIO * (int)$descuento->PORCENTAJE)/100);
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
                $marcas[$value->MARCA]["STOCK"] += $value->STOCK;
                $marcas[$value->MARCA]["VENDIDO"] += $value->VENDIDO;
            } else {
                $marcas[$value->MARCA]["CODIGO"] = $value->MARCA;
                $marcas[$value->MARCA]["MARCA"] = $value->MARCA_NOMBRE;
                $marcas[$value->MARCA]["STOCK"] = $value->STOCK;
                $marcas[$value->MARCA]["TOTAL"] = $value->PRECIO;
                $marcas[$value->MARCA]["VENDIDO"] = $value->VENDIDO;
                $marcas[$value->MARCA]["STOCK_G"] = 0;
            }

             /*  --------------------------------------------------------------------------------- */

            // CREAR ARRAY DE CATEGORIAS

            if (array_key_exists($value->MARCA.''.$value->LINEA, $categorias))   {
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
            }

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

//            if (array_key_exists($value->MARCA.''.$value->LINEA, $categorias))   {

                // CARGAR STOCK GENERAL CATEGORIA

               // $categorias[$value->MARCA.''.$value->LINEA]["STOCK_G"] += $value->CANTIDAD;
           // }

            /*  --------------------------------------------------------------------------------- */
        }

        /*  --------------------------------------------------------------------------------- */

        $marca[] = (array) $marcas;
        $categoria[] = (array) $categorias;

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR TODOS LOS ARRAYS

        return ['ventas' => $ventasdet, 'marcas' => (array)$marca[0], 'categorias' => (array)$categoria[0]];

        /*  --------------------------------------------------------------------------------- */
    }

     

    public static function vendedor_datatable($request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
        
        /*  --------------------------------------------------------------------------------- */
        
        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'CODIGO', 
                            1 => 'CI',
                            2 => 'NOMBRE'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = Vendedores::where('ID_SUCURSAL', '=', $user->id_sucursal)->count();  

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

            $posts = Vendedores::select(DB::raw('CODIGO, CI, NOMBRE'))
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

            $posts = Vendedores::select(DB::raw('CODIGO, CI, NOMBRE'))
                            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
                            ->where(function ($query) use ($search) {
                                $query->where('CI','LIKE',"%{$search}%")
                                      ->orWhere('CODIGO', 'LIKE',"%{$search}%")
                                      ->orWhere('NOMBRE', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = Vendedores::where(function ($query) use ($search) {
                                $query->where('CI','LIKE',"%{$search}%")
                                      ->orWhere('CODIGO', 'LIKE',"%{$search}%")
                                      ->orWhere('NOMBRE', 'LIKE',"%{$search}%");
                            })
                            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
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


    public static function obtenerDatos($data, $order, $dir){

        $inicio = date('Y-m-d', strtotime($data['inicio']));
        $final = date('Y-m-d', strtotime($data['final']));
        $codigoVendedor = $data['vendedor'];
        $sucursal = $data['sucursal'];
        $tipo = $data['tipo'];

        $ventaVendedor = DB::connection('retail')->table('VENTAS')
                ->select(DB::raw('CLIENTES.NOMBRE AS CLIENTE'),
                    DB::raw('VENTAS.FECALTAS AS FECHA'),
                    DB::raw('VENTAS.SUB_TOTAL AS SUBTOTAL'),
                    DB::raw('VENTAS.IMPUESTOS AS IVA'),
                    DB::raw('VENTAS.TOTAL AS TOTAL'),
                    DB::raw('VENTAS.ID AS ID'),
                    DB::raw('VENTAS.TIPO AS TIPO'),
                    DB::raw('VENTAS.MONEDA AS MONEDA'),
                    DB::raw('CLIENTES.CODIGO AS COD_CLI'),
                    DB::raw('EMPlEADOS.NOMBRE AS VENDEDOR'),
                    DB::raw('VENTAS_CREDITO.PAGO AS PAGADO'))
                ->leftjoin('VENTAS_ANULADO', 'VENTAS_ANULADO.FK_VENTA', '=', 'VENTAS.ID')
                ->leftjoin('VENTAS_CREDITO', 'VENTAS_CREDITO.FK_VENTA', '=', 'VENTAS.ID')
                ->leftJoin('CLIENTES', function($join){
                                $join->on('CLIENTES.CODIGO', '=', 'VENTAS.CLIENTE')
                                     ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
                            })
                ->leftjoin('EMPlEADOS', function($join){
                            $join->on('EMPlEADOS.CODIGO', '=', 'VENTAS.VENDEDOR')
                                 ->on('EMPlEADOS.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
                        })
                ->whereBetween('VENTAS.FECALTAS', [$inicio , $final])
                ->where([
                    ['VENTAS.ID_SUCURSAL', '=', $sucursal],
                    ['VENTAS_ANULADO.ANULADO', '<>', 1]])
                ->groupBy('VENTAS.ID')
                ->orderBy($order, $dir);

        if($codigoVendedor !== "null"){

            $ventaVendedor->where('VENTAS.VENDEDOR', '=', $codigoVendedor);
        }

        if($tipo !== "GENERAL"){

            $ventaVendedor->where('VENTAS.TIPO', '=', $tipo);
        }

        $ventaVendedor = $ventaVendedor->get();

        return $ventaVendedor;
    }

    public static function rptVentaVendedor($datos){

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $fecha = $fecha.' '.$hora;
        $generador = ucfirst($user->name);
        $inicio = date('Y-m-d', strtotime($datos['data']['inicio']));
        $final = date('Y-m-d', strtotime($datos['data']['final']));
        $vendedor = $datos['data']['vendedor'];
        $sucursal = $datos['data']['sucursal'];
        $order ='VENTAS.FECALTAS';
        $dir = 'ASC';

        // OBTENER DATOS 

        $ventaVendedor = Vendedores::obtenerDatos($datos['data'], $order, $dir); 

        //INICIAR VARIABLES
        
        $moneda = $ventaVendedor[0]->MONEDA;
        $candec = (Parametro::candec($moneda))["CANDEC"];
        $intervalo = $inicio.'/'.$final;
        $total = 0;
        $c_rows = 0;
        $iva = 0;
        $subtotal = 0;
        $articulos = [];
        $limite = 35;
        $tipo = $datos['data']['tipo'];
        $totalPagado = 0;


        // INICIAR MPDF 

        $mpdf = new \Mpdf\Mpdf([
            'margin_left' => 20,
            'margin_right' => 20,
            'margin_top' => 16,
            'margin_bottom' => 10,
            'margin_header' => 5,
            'margin_footer' => 10
        ]);

        $mpdf->SetDisplayMode('fullpage');

        foreach ($ventaVendedor as $key => $value) {

            $total = $total + $value->TOTAL;
            $iva = $iva + $value->IVA;
            $subtotal = $subtotal + $value->SUBTOTAL;
            $nombre = mb_strtolower($value->CLIENTE);
            $vendedor = mb_strtolower($value->VENDEDOR);
            $nombre = substr($nombre,0,27);
            $articulos[$c_rows]['NOMBRE'] = utf8_decode(utf8_encode(ucwords($nombre)));
            $articulos[$c_rows]['CODIGO'] = $value->ID;
            $fecha = substr($value->FECHA,0,-9);
            $articulos[$c_rows]['FECHA'] = $fecha;
            $articulos[$c_rows]['TIPO'] = $value->TIPO;
            $articulos[$c_rows]['VENDEDOR'] = utf8_decode(utf8_encode(ucwords($vendedor)));
            $articulos[$c_rows]['IVA'] = Common::formato_precio($value->IVA, $candec);
            $articulos[$c_rows]['SUBTOTAL'] = Common::formato_precio($value->SUBTOTAL, $candec);
            $articulos[$c_rows]['TOTAL'] = Common::formato_precio($value->TOTAL, $candec);
 
            // ESTADO DE PAGO CREDITO

            if($tipo == 'CR'){
                $totalPagado = $totalPagado + $value->PAGADO;
                $articulos[$c_rows]['PAGADO'] = Common::formato_precio($value->PAGADO, $candec);
            }

            // CREAR HOJA 

            $articulos[$c_rows]['SALTO'] = false;

            if($c_rows == $limite){
                
                $articulos[$c_rows]['SALTO'] = true;
                $limite = $limite + 42;
            }

            $c_rows = $c_rows + 1;
        }

        $namefile = 'reporteVentaVendedor'.time().'.pdf';
        $data['c_rows'] = $c_rows;
        $data['fecha'] = $fecha;
        $data['generador'] = $generador;
        $data['intervalo'] = $intervalo;
        $data['articulos'] = $articulos;
        $data['tipo'] = $tipo;
        $data['iva'] = Common::formato_precio($iva, $candec);
        $data['subtotal'] = Common::formato_precio($subtotal, $candec);
        $data['total'] = Common::formato_precio($total, $candec);
        $data['totalPagado'] = Common::formato_precio($totalPagado, $candec);

        $html = view('pdf.rptVentaVendedor', $data)->render();

        $mpdf->WriteHTML($html);

        // CREAR HOJA 

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle("ReporteVentaVendedor");

        // DESCARGAR ARCHIVO PDF 

        $mpdf->Output();


        /*  --------------------------------------------------------------------------------- */
    }



    public static function generarReporteVentaVendedor($request) {

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $sucursal = $request->input('sucursal');
        $inicio =  date('Y-m-d', strtotime($request->input('inicio')));
        $final = date('Y-m-d', strtotime($request->input('final')));
        $vendedor = $request->input('vendedor');

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'VENTAS.FECALTAS', 
                            1 => 'VENTAS.ID',
                            2 => 'CLIENTES.NOMBRE',
                            3 => 'VENTAS.FECALTAS',
                            4 => 'VENTAS.TIPO',
                            5 => 'EMPLEADOS.NOMBRE',
                            6 => 'VENTAS.IMPUESTOS',
                            7 => 'VENTAS.SUB_TOTAL',
                            8 => 'VENTAS.TOTAL'
                        );
        

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $item = 1;


        $datos = array(
                'sucursal' => $request->input('sucursal'),
                'inicio' => date('Y-m-d', strtotime($request->input('inicio'))),
                'final' => date('Y-m-d', strtotime($request->input('final'))),
                'vendedor' => $request->input('vendedor'),
                'tipo' => $request->input('tipo'),
            );
        

    
        /*  --------------------------------------------------------------------------------- */

        //  CARGAR TODOS LOS DATOS ENCONTRADOS 


        $posts = Vendedores::obtenerDatos($datos, $order, $dir);     


        /*  ************************************************************ */

        $moneda = $posts[0]->MONEDA;
        $candec = (Parametro::candec($moneda))["CANDEC"];
        $data = array();
        
        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts)){
            foreach ($posts as $post){

                /*  --------------------------------------------------------------------------------- */

                // CARGAR EN LA VARIABLE 

                $cliente = mb_strtolower($post->CLIENTE);
                $vendedor = mb_strtolower($post->VENDEDOR);
                $cliente = substr($cliente,0,27);
                $nestedData['ITEM'] = $item;
                $nestedData['ID'] = $post->ID;
                $nestedData['CLIENTE'] = utf8_encode(ucwords($cliente));
                $fecha = substr($post->FECHA,0,-9);
                $nestedData['FECHA'] = $fecha;
                $nestedData['TIPO'] = $post->TIPO;       
                $nestedData['VENDEDOR'] = utf8_decode(utf8_encode(ucwords($vendedor)));
                $nestedData['IVA'] = Common::formato_precio($post->IVA, $candec);
                $nestedData['SUBTOTAL'] = Common::formato_precio($post->SUBTOTAL, $candec);
                $nestedData['TOTAL'] = Common::formato_precio($post->TOTAL, $candec);
                $data[] = $nestedData;
                $item = $item +1;

                /*  --------------------------------------------------------------------------------- */
            }
        }
        
        /*  --------------------------------------------------------------------------------- */

        // PREPARAR EL ARRAY A ENVIAR 

        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($item),  
                    "recordsFiltered" => intval($item), 
                    "data"            => $data   
                    );
        
        /*  --------------------------------------------------------------------------------- */

        // CONVERTIR EN JSON EL ARRAY Y ENVIAR 

        return $json_data; 

        /*  --------------------------------------------------------------------------------- */
    }

}
