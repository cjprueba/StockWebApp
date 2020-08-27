<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Mpdf\Mpdf;
use App\Stock;
use App\Producto;
use App\ComprasDet;
use App\Common;
use App\Parametro;
use App\Sucursal;
use App\TransferenciaDet_tiene_Lotes;
use NumeroALetras\NumeroALetras;
use App\TransferenciaUser;

class Transferencia extends Model
{

    /*  --------------------------------------------------------------------------------- */

    // INICIAR LAS VARIABLES GLOBALES

    protected $connection = 'retail';
    protected $table = 'transferencias';
    
    /*  --------------------------------------------------------------------------------- */

    public static function mostrarDatatable($request)
    {

        /*  --------------------------------------------------------------------------------- */

        // INICIARA VARIABLES

        //global $search;

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'CODIGO', 
                            1 => 'ORIGEN',
                            2 => 'DESTINO',
                            3 => 'NRO_CAJA',
                            4 => 'IVA',
                            5 => 'TOTAL',
                            6 => 'ESTATUS',
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = Transferencia::where('ID_SUCURSAL','=', $user->id_sucursal)
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

            $posts = Transferencia::select(DB::raw('TRANSFERENCIAS.CODIGO, ORIGEN.DESCRIPCION AS ORIGEN, DESTINO.DESCRIPCION AS DESTINO, TRANSFERENCIAS.NRO_CAJA, TRANSFERENCIAS.IVA, TRANSFERENCIAS.TOTAL, TRANSFERENCIAS.ESTATUS, TRANSFERENCIAS.MONEDA'))
                         ->leftjoin('SUCURSALES AS ORIGEN', 'ORIGEN.CODIGO', '=', 'TRANSFERENCIAS.SUCURSAL_ORIGEN')
                         ->leftjoin('SUCURSALES AS DESTINO', 'DESTINO.CODIGO', '=', 'TRANSFERENCIAS.SUCURSAL_DESTINO')
                         ->where('TRANSFERENCIAS.ID_SUCURSAL','=', $user->id_sucursal)
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

            $posts =  Transferencia::select(DB::raw('TRANSFERENCIAS.CODIGO, ORIGEN.DESCRIPCION AS ORIGEN, DESTINO.DESCRIPCION AS DESTINO, TRANSFERENCIAS.NRO_CAJA, TRANSFERENCIAS.IVA, TRANSFERENCIAS.TOTAL, TRANSFERENCIAS.ESTATUS, TRANSFERENCIAS.MONEDA'))
                         ->leftjoin('SUCURSALES AS ORIGEN', 'ORIGEN.CODIGO', '=', 'TRANSFERENCIAS.SUCURSAL_ORIGEN')
                         ->leftjoin('SUCURSALES AS DESTINO', 'DESTINO.CODIGO', '=', 'TRANSFERENCIAS.SUCURSAL_DESTINO')
                         ->where('TRANSFERENCIAS.ID_SUCURSAL','=', $user->id_sucursal)
                            ->where(function ($query) use ($search) {
                                $query->where('TRANSFERENCIAS.CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('DESTINO.DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = Transferencia::where('ID_SUCURSAL','=', $user->id_sucursal)
                            ->leftjoin('SUCURSALES AS DESTINO', 'DESTINO.CODIGO', '=', 'TRANSFERENCIAS.SUCURSAL_DESTINO')   
                            ->where(function ($query) use ($search) {
                                $query->where('TRANSFERENCIAS.CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('DESTINO.DESCRIPCION', 'LIKE',"%{$search}%");
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
                $nestedData['ORIGEN'] = $post->ORIGEN;
                $nestedData['DESTINO'] = $post->DESTINO;
                $nestedData['NRO_CAJA'] = $post->NRO_CAJA;
                $nestedData['IVA'] = Common::precio_candec($post->IVA, $post->MONEDA);
                $nestedData['TOTAL'] = Common::precio_candec($post->TOTAL, $post->MONEDA);

                if ($post->ESTATUS === 0) {
                    $nestedData['ESTATUS'] = '<span class="badge badge-warning">Pendiente</span>';
                } else if ($post->ESTATUS === 1) {
                    $nestedData['ESTATUS'] = '<span class="badge badge-info">Enviado</span>';
                } else {
                    $nestedData['ESTATUS'] = '<span class="badge badge-success">Procesado</span>';
                }
                
                $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarTransferencia' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a>&emsp;<a href='#' id='enviarTransferencia' title='Enviar'><i class='fa fa-paper-plane text-success'  aria-hidden='true'></i></a> &emsp;<a href='#' id='editarTransferencia' title='Editar'><i class='fa fa-edit text-warning' aria-hidden='true'></i></a>&emsp;<a href='#' id='eliminarTransferencia' title='Eliminar'><i class='fa fa-trash text-danger' aria-hidden='true'></i></a>
                    &emsp;<a href='#' id='imprimirReporte' title='Reporte'><i class='fa fa-file text-secondary' aria-hidden='true'></i></a>&emsp;<a href='#' id='imprimirTransferencia' title='Imprimir'><i class='fa fa-print text-primary' aria-hidden='true'></i></a>";

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

    public static function generarConsulta($datos) 
    {

        
         /*  --------------------------------------------------------------------------------- */

         // INCICIAR VARIABLES 

        $marcas[] = array();
        $categorias[] = array();
        $totales[] = array();

        $inicio = date('Y-m-d', strtotime($datos['Inicio']));
        $final = date('Y-m-d', strtotime($datos['Final']));
        $sucursalOrigen[] = $datos['SucursalOrigen'];
        $sucursalDestino = $datos['SucursalDestino'];
        $estatus = $datos['Estatus'];
        
        /*  --------------------------------------------------------------------------------- */

        /*  *********** TODAS LAS TRANSFERENCIAS ENTRE LAS FECHAS INTERVALOS *********** */

        if ($datos['AllCategory'] AND $datos['AllBrand']) {

            $transferencias_det = DB::connection('retail')->table('transferencias_det as td')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'td.CODIGO_PROD')
            ->leftJoin('transferencias as t', function($join){
			    $join->on('t.CODIGO', '=', 'td.CODIGO')
			         ->on('t.ID_SUCURSAL', '=', 'td.ID_SUCURSAL');
			})
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->select(DB::raw('SUM(td.PRECIO * t.CAMBIO) AS PRECIO'),
            DB::raw('SUM(td.CANTIDAD) AS CANTIDAD'),
            DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = td.CODIGO_PROD) AND (l.ID_SUCURSAL = t.SUCURSAL_DESTINO))),0) AS STOCK'),
            DB::raw('MARCA.DESCRIPCION AS MARCA_NOMBRE'),
            DB::raw('LINEAS.DESCRIPCION AS LINEA_NOMBRE'),
            DB::raw('td.CODIGO_PROD'),
            DB::raw('t.CAMBIO'),
            DB::raw('PRODUCTOS.MARCA AS MARCA'),
            DB::raw('PRODUCTOS.LINEA AS LINEA'))
            ->whereIn('t.SUCURSAL_ORIGEN', $datos['SucursalOrigen'])  
            ->whereBetween('t.FECMODIF', [$inicio , $final])
            ->where([
                ['t.SUCURSAL_DESTINO', '=', $sucursalDestino],
                ['t.ESTATUS', '=', $estatus],
            ])
            ->groupBy('td.CODIGO_PROD')
            ->get()
            ->toArray(); 

           
        } else if ($datos['AllCategory']) {
            
            $transferencias_det = DB::connection('retail')->table('transferencias_det as td')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'td.CODIGO_PROD')
            ->leftJoin('transferencias as t', function($join){
			    $join->on('t.CODIGO', '=', 'td.CODIGO')
			         ->on('t.ID_SUCURSAL', '=', 'td.ID_SUCURSAL');
			})
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->select(DB::raw('SUM(td.PRECIO * t.CAMBIO) AS PRECIO'),
            DB::raw('SUM(td.CANTIDAD) AS CANTIDAD'),
            DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = td.CODIGO_PROD) AND (l.ID_SUCURSAL = t.SUCURSAL_DESTINO))),0) AS STOCK'),
            DB::raw('MARCA.DESCRIPCION AS MARCA_NOMBRE'),
            DB::raw('LINEAS.DESCRIPCION AS LINEA_NOMBRE'),
            DB::raw('td.CODIGO_PROD'),
            DB::raw('PRODUCTOS.MARCA AS MARCA'),
            DB::raw('PRODUCTOS.LINEA AS LINEA'))  
            ->whereBetween('t.FECMODIF', [$inicio , $final])
            ->whereIn('PRODUCTOS.MARCA', $datos['Marcas'])
            ->where([
                ['t.SUCURSAL_ORIGEN', '=', $sucursalOrigen],
                ['t.SUCURSAL_DESTINO', '=', $sucursalDestino],
                ['t.ESTATUS', '=', $estatus],
            ])
            ->groupBy('td.CODIGO_PROD')
            ->get()
            ->toArray();

        } else if ($datos['AllBrand']) {
             
            $transferencias_det = DB::connection('retail')->table('transferencias_det as td')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'td.CODIGO_PROD')
            ->leftJoin('transferencias as t', function($join){
			    $join->on('t.CODIGO', '=', 'td.CODIGO')
			         ->on('t.ID_SUCURSAL', '=', 'td.ID_SUCURSAL');
			})
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->select(DB::raw('SUM(td.PRECIO * t.CAMBIO) AS PRECIO'),
            DB::raw('SUM(td.CANTIDAD) AS CANTIDAD'),
            DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = td.CODIGO_PROD) AND (l.ID_SUCURSAL = t.SUCURSAL_DESTINO))),0) AS STOCK'),
            DB::raw('MARCA.DESCRIPCION AS MARCA_NOMBRE'),
            DB::raw('LINEAS.DESCRIPCION AS LINEA_NOMBRE'),
            DB::raw('td.CODIGO_PROD'),
            DB::raw('PRODUCTOS.MARCA AS MARCA'),
            DB::raw('PRODUCTOS.LINEA AS LINEA'))  
            ->whereBetween('t.FECMODIF', [$inicio , $final])
            ->whereIn('PRODUCTOS.LINEA', $datos['Categorias'])
            ->where([
                ['t.SUCURSAL_ORIGEN', '=', $sucursalOrigen],
                ['t.SUCURSAL_DESTINO', '=', $sucursalDestino],
                ['t.ESTATUS', '=', $estatus],
            ])
            ->groupBy('td.CODIGO_PROD')
            ->get()
            ->toArray();
             
        } else  {

        	$transferencias_det = DB::connection('retail')->table('transferencias_det as td')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'td.CODIGO_PROD')
            ->leftJoin('transferencias as t', function($join){
			    $join->on('t.CODIGO', '=', 'td.CODIGO')
			         ->on('t.ID_SUCURSAL', '=', 'td.ID_SUCURSAL');
			})
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->select(DB::raw('SUM(td.PRECIO * t.CAMBIO) AS PRECIO'),
            DB::raw('SUM(td.CANTIDAD) AS CANTIDAD'),
            DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = td.CODIGO_PROD) AND (l.ID_SUCURSAL = t.SUCURSAL_DESTINO))),0) AS STOCK'),
            DB::raw('MARCA.DESCRIPCION AS MARCA_NOMBRE'),
            DB::raw('LINEAS.DESCRIPCION AS LINEA_NOMBRE'),
            DB::raw('td.CODIGO_PROD'),
            DB::raw('PRODUCTOS.MARCA AS MARCA'),
            DB::raw('PRODUCTOS.LINEA AS LINEA'))  
            ->whereBetween('t.FECMODIF', [$inicio , $final])
            ->whereIn('PRODUCTOS.MARCA', $datos['Marcas'])
            ->whereIn('PRODUCTOS.LINEA', $datos['Categorias'])
            ->where([
                ['t.SUCURSAL_ORIGEN', '=', $sucursalOrigen],
                ['t.SUCURSAL_DESTINO', '=', $sucursalDestino],
                ['t.ESTATUS', '=', $estatus],
            ])
            ->groupBy('td.CODIGO_PROD')
            ->get()
            ->toArray();

        }


        /*  --------------------------------------------------------------------------------- */

        //  CAMBIAR A GUARANIES

        // foreach ($ventasdet as $key => $value) {
        //     if ($value->CAMBIO > 1) {
        //     	$ventasdet[$key] = $value->
        //     }
        // }

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
        foreach ($transferencias_det as $key => $value) {


            /*  --------------------------------------------------------------------------------- */

            // CREAR ARRAY DE MARCAS

            if (array_key_exists($value->MARCA, $marcas))   {
                $marcas[$value->MARCA]["TOTAL"] += $value->PRECIO;
                $marcas[$value->MARCA]["STOCK"] += $value->STOCK;
                $marcas[$value->MARCA]["CANTIDAD"] += $value->CANTIDAD;
            } else {
                $marcas[$value->MARCA]["CODIGO"] = $value->MARCA;
                $marcas[$value->MARCA]["MARCA"] = $value->MARCA_NOMBRE;
                $marcas[$value->MARCA]["STOCK"] = $value->STOCK;
                $marcas[$value->MARCA]["TOTAL"] = $value->PRECIO;
                $marcas[$value->MARCA]["CANTIDAD"] = $value->CANTIDAD;
                $marcas[$value->MARCA]["STOCK_G"] = 0;
            }

             /*  --------------------------------------------------------------------------------- */

            // CREAR ARRAY DE CATEGORIAS

            if (array_key_exists($value->MARCA.''.$value->LINEA, $categorias))   {
                $categorias[$value->MARCA.''.$value->LINEA]["TOTAL"] += $value->PRECIO;
                $categorias[$value->MARCA.''.$value->LINEA]["STOCK"] += $value->STOCK;
                $categorias[$value->MARCA.''.$value->LINEA]["CANTIDAD"] += $value->CANTIDAD;
            } else {
                $categorias[$value->MARCA.''.$value->LINEA]["CODIGO"] = $value->LINEA;
                $categorias[$value->MARCA.''.$value->LINEA]["LINEA"] = $value->LINEA_NOMBRE;
                $categorias[$value->MARCA.''.$value->LINEA]["STOCK"] = $value->STOCK;
                $categorias[$value->MARCA.''.$value->LINEA]["TOTAL"] = $value->PRECIO;
                $categorias[$value->MARCA.''.$value->LINEA]["MARCA"] = $value->MARCA;
                $categorias[$value->MARCA.''.$value->LINEA]["CANTIDAD"] = $value->CANTIDAD;
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
            ->where('l.ID_SUCURSAL', '=', $sucursalDestino)
            ->groupBy('PRODUCTOS.MARCA', 'PRODUCTOS.LINEA')
            ->get();

        foreach ($stockGeneral as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            if (array_key_exists($value->MARCA, $marcas))   {

                // CARGAR STOCK GENERAL A MARCA

                $marcas[$value->MARCA]["STOCK_G"] += $value->CANTIDAD;

            }

            if (array_key_exists($value->MARCA.''.$value->LINEA, $categorias))   {


            	// CARGAR STOCK GENERAL CATEGORIA

                $categorias[$value->MARCA.''.$value->LINEA]["STOCK_G"] += $value->CANTIDAD;
            }

            /*  --------------------------------------------------------------------------------- */
        }

        /*  --------------------------------------------------------------------------------- */

        $marca[] = (array) $marcas;
        $categoria[] = (array) $categorias;

        /*  --------------------------------------------------------------------------------- */
        
        // RETORNAR TODOS LOS ARRAYS

        return ['ventas' => $transferencias_det, 'marcas' => (array)$marca[0], 'categorias' => (array)$categoria[0]];

        /*  --------------------------------------------------------------------------------- */
    }

    public static function guardar_modificar($datos, $opcion) 
    {
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 
        
        $diaHora = date("Y-m-d H:i:s");
        $dia = date("Y-m-d");
        $hora = date("H:i:s");

        $c = 0;
        $filas = count($datos["data"]);
        $cod_prod = '';
        $cantidad = 0;
        $cantidad_guardada = 0;
        $cantidad_total = 0;
        $precio = 0;
        $iva = 0;
        $base5 = 0;
        $base10 = 0;
        $exentas = 0;
        $total = 0;
        $sin_stock = [];
        $porcentaje = 0;
        $transferencia = 0;

        $respuesta_FK_CD = [];
        $cantidad_FK_CD = 1;
        $todos_guardados = true;

        $total_total = 0;
        $total_iva = 0;
        $total_subtotal = 0;

        $lote = 0;

        /*  --------------------------------------------------------------------------------- */
        
        // PARAMETRO 
        
        $parametro = Parametro::mostrarParametro();
        $candec = $parametro['parametros'][0]->CANDEC;

        /*  --------------------------------------------------------------------------------- */

        // SOLICITAR ULTIMO CODIGO TRANSFERENCIA SI ES GUARDADO, SI ES MODIFICACION AGARRAR EL CODIGO ENVIADO

        if ($opcion === 1) {
            $codigo = Transferencia::ultimo_codigo();
        } else if ($opcion === 2) {
            $codigo = $datos["codigo"];
        }

        /*  --------------------------------------------------------------------------------- */

        // OBTENER COTIZACION 

        $cotizacion = Cotizacion::cotizacion_dia($datos["cabecera"]["monedaSistema"], $datos["cabecera"]["monedaEnviar"]);

        /*  --------------------------------------------------------------------------------- */

        // INSERTAR TRANSFERENCIA SI ES GUARDADO

        if ($opcion === 1) {

            $transferencia = DB::connection('retail')
            ->table('transferencias')
            ->insertGetId(
                [
                'CODIGO' => $codigo, 
                'SUCURSAL_ORIGEN' => $datos["cabecera"]["origen"],
                'SUCURSAL_DESTINO' => $datos["cabecera"]["destino"],
                'DIRECCION' =>  '',
                'TELEFONO' => '',
                'FECHA' => $dia,
                'HORA' => $hora,
                'ENVIA' => $datos["cabecera"]["envia"],
                'TRANSPORTA' => $datos["cabecera"]["transporta"],
                'RECIBE' => $datos["cabecera"]["recibe"],
                'NRO_CAJA' => $datos["cabecera"]["nro_caja"],
                'SUB_TOTAL' => 0,
                'IVA' => 0,
                'TOTAL' => 0,
                'ENVIADO' => 'SI',
                'DEVUELTO' => 'NO',
                'MONEDA' => $datos["cabecera"]["monedaSistema"],
                'MONEDA_ENVIAR' => $datos["cabecera"]["monedaEnviar"],
                'USERALTAS' =>  $user->name,
                'FECALTAS' =>  $dia,
                'HORALTAS' =>  $hora,
                'ID_SUCURSAL' => $user->id_sucursal,
                'ESTATUS' => 0,
                'CAMBIO' => $cotizacion
                ]
            );

        }

        /*  --------------------------------------------------------------------------------- */

        // RECORRER TODAS LAS FILAS DEL DATATABLE

        while($c < $filas) {

                    /*  --------------------------------------------------------------------------------- */

                    // INICIAR DATOS 

                    $cod_prod = $datos["data"][$c]["CODIGO"];
                    $cantidad = Common::quitar_coma($datos["data"][$c]["CANTIDAD"], $parametro['parametros'][0]->CANDEC);
                    $cantidad_total = $cantidad;
                    $precio = Common::quitar_coma($datos["data"][$c]["PRECIO"], $parametro['parametros'][0]->CANDEC);
                    $porcentaje = $datos["data"][$c]["IVA_PORCENTAJE"];

                    /*  --------------------------------------------------------------------------------- */

                    // $iva = Common::quitar_coma($datos["data"][$c]["IVA"], $parametro['parametros'][0]->CANDEC);

                    /*  --------------------------------------------------------------------------------- */

                    // REVISAR IMPUESTO

                    // if ($datos["data"][$c]["IVA_PORCENTAJE"] === 5) {

                    //     /*  --------------------------------------------------------------------------------- */

                    //     // BASE 5 

                    //     $base5 = Common::quitar_coma($datos["data"][$c]["IVA"], $parametro['parametros'][0]->CANDEC);

                    //     /*  --------------------------------------------------------------------------------- */

                    // } else if ($datos["data"][$c]["IVA_PORCENTAJE"] === 10) {

                    //     /*  --------------------------------------------------------------------------------- */

                    //     // BASE 10 

                    //     $base10 = Common::quitar_coma($datos["data"][$c]["IVA"], $parametro['parametros'][0]->CANDEC);

                    //     /*  --------------------------------------------------------------------------------- */

                    // } else {

                    //     /*  --------------------------------------------------------------------------------- */

                    //     // EXENTAS 

                    //     $exentas = Common::quitar_coma($datos["data"][$c]["TOTAL"], $parametro['parametros'][0]->CANDEC);

                    //     /*  --------------------------------------------------------------------------------- */

                    // }

                    /*  --------------------------------------------------------------------------------- */

                    // REALIZAR CALCULOS 
                    
                    // $gravadas = Common::quitar_coma($datos["data"][$c]["TOTAL"], $parametro['parametros'][0]->CANDEC) - Common::quitar_coma($datos["data"][$c]["IVA"], $parametro['parametros'][0]->CANDEC);
                    $total = Common::quitar_coma($datos["data"][$c]["TOTAL"], $parametro['parametros'][0]->CANDEC);

                    /*  --------------------------------------------------------------------------------- */

                    // OBTENER DATOS FALTANTES 

                    $producto = Producto::datosVariosProducto($cod_prod);

                    /*  --------------------------------------------------------------------------------- */ 

                    // RESTAR STOCK DEL PRODUCTO

                    $respuesta_resta = Stock::restar_stock_producto($cod_prod, $cantidad);
                    
                    /*  --------------------------------------------------------------------------------- */ 

                    // SI LA RESPUESTA TIENE DATOS GUARDA EL REGISTRO 

                    if ($respuesta_resta["datos"]) {

                        /*  --------------------------------------------------------------------------------- */

                        // SUMAR LA CANTIDAD DEVUELTA POR EL ARRAY 

                        $cantidad_guardada = 0;

                        foreach ($respuesta_resta["datos"] as $key => $value) {
                            $cantidad_guardada = $cantidad_guardada + $value["cantidad"];
                        }

                        /*  --------------------------------------------------------------------------------- */

                        // SI LA CANTIDAD GUARDADA DIFIERE CON LA CANTIDAD A ENVIAR SE RECALCULA EL TOTAL

                        if ($cantidad !== $cantidad_guardada) {
                            $total = $precio * $cantidad_guardada;
                        }

                        /*  --------------------------------------------------------------------------------- */

                        // CALCULAR IVA

                        $impuesto = Common::calculo_iva($porcentaje, $total, $candec);
                        
                        /*  --------------------------------------------------------------------------------- */

                        // TOTALES 

                        $total_total = $total_total + $total;
                        $total_iva = $total_iva + $impuesto["impuesto"];

                        /*  --------------------------------------------------------------------------------- */

                        // INSERTAR TRANSFERENCIA DET 

                        $id_transferencias_det = DB::connection('retail')
                        ->table('transferencias_det')
                        ->insertGetId(
                            [
                            'CODIGO' => $codigo, 
                            'ITEM' => $c + 1,
                            'CODIGO_PROD' => $cod_prod,
                            'CODIGO_INTERNO' =>  $producto['producto'][0]->CODIGO_INTERNO,
                            'LOTE' => $lote,
                            'DESCRIPCION' => $datos["data"][$c]["DESCRIPCION"],
                            'TIPO' => $datos["data"][$c]["ITEM"],
                            'CANTIDAD' => $cantidad_guardada,
                            'PRECIO' => $precio,
                            'EXENTAS' => $impuesto["exentas"],
                            'GRABADAS' => $impuesto["gravadas"],
                            'IVA' => $impuesto["impuesto"],
                            'TOTAL' => $total,
                            'DESCUENTO' => 0,
                            'BASE5' => $impuesto["base5"],
                            'BASE10' => $impuesto["base10"],
                            'DEVUELTO' => 'NO',
                            'USERALTAS' => $user->name,
                            'FECALTAS' =>  $dia,
                            'HORALTAS' =>  $hora,
                            'ID_SUCURSAL' => $user->id_sucursal
                            ]
                        );

                        /*  --------------------------------------------------------------------------------- */

                    }

                    /*  --------------------------------------------------------------------------------- */

                    // AQUI RECORRO EL ARRAY MANDANDO LOS ID LOTE Y LA TRANSFERENCIA EN LA TABLA DE CLAVES FORANEAS

                    foreach ($respuesta_resta["datos"] as $key => $value) {
                        TransferenciaDet_tiene_Lotes::guardar_referencia($id_transferencias_det, $value["id"], $value["cantidad"]);
                    }
                    
                    /*  --------------------------------------------------------------------------------- */

                    // CARGAR LOS PRODUCTOS CON LAS CANTIDADES QUE NO SE GUARDARON 

                    if ($respuesta_resta["response"] === false){
                        $sin_stock[] = (array)['cod_prod' => $cod_prod, 'guardado' => (float)$cantidad - (float)$respuesta_resta["restante"], "restante" => $respuesta_resta["restante"], "cantidad" => $cantidad];
                    }

                    /*  --------------------------------------------------------------------------------- */
                    
                    // AUMENTAR CONTADOR 

                    $c++;

                    /*  --------------------------------------------------------------------------------- */
         }
        
        /*  --------------------------------------------------------------------------------- */ 

        // MODIFICAR TRANSFERENCIA GUARDADA O MODIFICAR TRANSFERENCIA POR EL USUARIO

        if ($opcion === 1) {

            /*  --------------------------------------------------------------------------------- */ 

            // INSERTAR USER REFERENCIA

            TransferenciaUser::guardar_referencia($user->id, 1, $transferencia, $diaHora);

            /*  --------------------------------------------------------------------------------- */

            // MODIFICAR TRANSFERENCIA RECIEN GUARDADA 

            $transferencia = DB::connection('retail')
            ->table('transferencias')
            ->where('CODIGO','=', $codigo)
            ->where('ID_SUCURSAL','=',$user->id_sucursal)
            ->update([
                'IVA' => $total_iva,
                'SUB_TOTAL' => ($total_total - $total_iva),
                'TOTAL' => $total_total
                ]);
            
            /*  --------------------------------------------------------------------------------- */ 

            

        } else if ($opcion === 2) {

            /*  --------------------------------------------------------------------------------- */

            $transferencia = Transferencia::select(DB::raw('ID'))
            ->where('CODIGO','=', $codigo)
            ->where('ID_SUCURSAL','=',$user->id_sucursal)
            ->get();

            /*  --------------------------------------------------------------------------------- */

            // INSERTAR USER REFERENCIA

            TransferenciaUser::guardar_referencia($user->id, 2, $transferencia[0]->ID, $diaHora);

            /*  --------------------------------------------------------------------------------- */

            // MODIDIFICAR TRANSFERENCIA 

            $transferencia = DB::connection('retail')
            ->table('transferencias')
            ->where('CODIGO','=', $codigo)
            ->where('ID_SUCURSAL','=',$user->id_sucursal)
            ->update([
                'SUCURSAL_ORIGEN' => $datos["cabecera"]["origen"],
                'SUCURSAL_DESTINO' => $datos["cabecera"]["destino"],
                'DIRECCION' =>  '',
                'TELEFONO' => '',
                'FECHA' => $dia,
                'HORA' => $hora,
                'ENVIA' => $datos["cabecera"]["envia"],
                'TRANSPORTA' => $datos["cabecera"]["transporta"],
                'RECIBE' => $datos["cabecera"]["recibe"],
                'NRO_CAJA' => $datos["cabecera"]["nro_caja"],
                'IVA' => $total_iva,
                'SUB_TOTAL' => ($total_total - $total_iva),
                'TOTAL' => $total_total,
                'ENVIADO' => 'SI',
                'DEVUELTO' => 'NO',
                'MONEDA' => $datos["cabecera"]["monedaSistema"],
                'MONEDA_ENVIAR' => $datos["cabecera"]["monedaEnviar"],
                'USERMODIF' =>  $user->name,
                'FECMODIF' =>  $dia,
                'HORMODIF' =>  $hora,
                'CAMBIO' => $cotizacion
                ]);

            /*  --------------------------------------------------------------------------------- */ 

        }

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI HAY PRODUCTOS QUE NO SE GUARDARON TOTALMENTE 

        if ($opcion === 1) {
            if (count($sin_stock) > 0) {
                return ["response" => false, "productos" => $sin_stock];
            } else {
                return ["response" => true];
            }
        }

        /*  --------------------------------------------------------------------------------- */

        if ($opcion === 2) {
            return true; 
        }
        
        /*  --------------------------------------------------------------------------------- */
        
    }

    public static function ultimo_codigo() 
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */ 

        // OBTENER CODIGO 

        $transferencia = DB::connection('retail')
        ->table('transferencias')
        ->select(DB::raw('(CODIGO + 1) AS CODIGO'))
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->orderBy('CODIGO', 'desc')
        ->limit(1)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // ULTIMO CODIGO 

        if (count($transferencia) > 0) {
            return $transferencia[0]->CODIGO;
        } else {
            return 1;
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function mostrar_cabecera($data)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // SI ENVIA CODIGO ORIGEN 
        
        if ($data["codigo_origen"] === 0) {
            $sucursal = $user->id_sucursal;
        } else {
            $sucursal = $data["codigo_origen"];
        }
         
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $codigo = $data['codigo'];

        /*  --------------------------------------------------------------------------------- */

        $transferencia = DB::connection('retail')
        ->table('transferencias')
        ->select(DB::raw(
                        'TRANSFERENCIAS.CODIGO,
                        TRANSFERENCIAS.FECALTAS, 
                        ORIGEN.CODIGO AS CODIGO_ORIGEN, 
                        ORIGEN.DESCRIPCION AS ORIGEN, 
                        DESTINO.CODIGO AS CODIGO_DESTINO, 
                        DESTINO.DESCRIPCION AS DESTINO,
                        ENVIA.CODIGO AS CODIGO_ENVIA,
                        ENVIA.NOMBRE AS ENVIA,
                        TRANSPORTA.CODIGO AS CODIGO_TRANSPORTA,
                        TRANSPORTA.NOMBRE AS TRANSPORTA,
                        RECIBE.CODIGO AS CODIGO_RECIBE,
                        RECIBE.NOMBRE AS RECIBE, 
                        TRANSFERENCIAS.NRO_CAJA, 
                        TRANSFERENCIAS.ESTATUS,
                        TRANSFERENCIAS.MONEDA'
                    ))
        ->leftjoin('SUCURSALES AS ORIGEN', 'ORIGEN.CODIGO', '=', 'TRANSFERENCIAS.SUCURSAL_ORIGEN')
        ->leftjoin('SUCURSALES AS DESTINO', 'DESTINO.CODIGO', '=', 'TRANSFERENCIAS.SUCURSAL_DESTINO')
        ->leftjoin('EMPLEADOS AS ENVIA', 'ENVIA.CODIGO', '=', 'TRANSFERENCIAS.ENVIA')
        ->leftjoin('EMPLEADOS AS TRANSPORTA', 'TRANSPORTA.CODIGO', '=', 'TRANSFERENCIAS.TRANSPORTA')
        ->leftjoin('EMPLEADOS AS RECIBE', 'RECIBE.CODIGO', '=', 'TRANSFERENCIAS.RECIBE')
        ->where('TRANSFERENCIAS.ID_SUCURSAL','=', $sucursal)
        ->where('TRANSFERENCIAS.CODIGO','=', $codigo)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        return $transferencia[0];

    }

    public static function mostrar_cuerpo($data)
    {
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // SI ENVIA CODIGO ORIGEN 
        
        if ($data["codigo_origen"] === 0) {
            $sucursal = $user->id_sucursal;
        } else {
            $sucursal = $data["codigo_origen"];
        }
         
        /*  --------------------------------------------------------------------------------- */
        // INICIAR VARIABLES

        $codigo = $data['codigo'];

        /*  --------------------------------------------------------------------------------- */

        $transferencia = DB::connection('retail')
        ->table('TRANSFERENCIAS_DET')
        ->select(DB::raw(
                        'TRANSFERENCIAS_DET.ITEM, 
                        TRANSFERENCIAS_DET.CODIGO_PROD, 
                        TRANSFERENCIAS_DET.DESCRIPCION, 
                        TRANSFERENCIAS_DET.CANTIDAD, 
                        TRANSFERENCIAS_DET.PRECIO,
                        TRANSFERENCIAS_DET.IVA,
                        TRANSFERENCIAS_DET.EXENTAS,
                        TRANSFERENCIAS_DET.BASE5,
                        TRANSFERENCIAS_DET.BASE10,
                        TRANSFERENCIAS_DET.TOTAL,
                        TRANSFERENCIAS.MONEDA,
                        0 AS IVA_PORCENTAJE'
                    ),
                 DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = TRANSFERENCIAS_DET.CODIGO_PROD) AND (l.ID_SUCURSAL = TRANSFERENCIAS_DET.ID_SUCURSAL))),0) AS STOCK'))
        ->leftJoin('TRANSFERENCIAS', function($join){
                                $join->on('TRANSFERENCIAS.CODIGO', '=', 'TRANSFERENCIAS_DET.CODIGO')
                                     ->on('TRANSFERENCIAS.ID_SUCURSAL', '=', 'TRANSFERENCIAS_DET.ID_SUCURSAL');
                            })
        ->where('TRANSFERENCIAS_DET.ID_SUCURSAL','=', $sucursal)
        ->where('TRANSFERENCIAS_DET.CODIGO','=', $codigo)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        foreach ($transferencia as $key => $value) {
            
            /*  --------------------------------------------------------------------------------- */

            // BUSCAR IVA PRODUCTO

            $producto = DB::connection('retail')
            ->table('PRODUCTOS')
            ->select(DB::raw('IMPUESTO'))
            ->where('CODIGO', '=', $value->CODIGO_PROD)
            ->get();

            /*  --------------------------------------------------------------------------------- */

            // CARGAR PORCENTAJE IVA IVA 

            $transferencia[$key]->IVA_PORCENTAJE = $producto[0]->IMPUESTO;

            /*  --------------------------------------------------------------------------------- */

            $transferencia[$key]->CANTIDAD = Common::precio_candec_sin_letra($value->CANTIDAD, 1);
            $transferencia[$key]->PRECIO = Common::precio_candec_sin_letra($value->PRECIO, $value->MONEDA);
            $transferencia[$key]->IVA = Common::precio_candec_sin_letra($value->IVA, $value->MONEDA);
            $transferencia[$key]->TOTAL = Common::precio_candec_sin_letra($value->TOTAL, $value->MONEDA);

        }

        return $transferencia;

        /*  --------------------------------------------------------------------------------- */

    }

    public static function verificar_existencia($codigo)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // VERIFICAR SI EXISTE 

        $transferencia = DB::connection('retail')
        ->table('transferencias')
        ->select(DB::raw(
                        'CODIGO'
                    ))
        ->where('ID_SUCURSAL','=', $user->id_sucursal)
        ->where('CODIGO','=', $codigo)
        ->get();

        /*  --------------------------------------------------------------------------------- */
        
        // RETORNAL VALOR 

        if(count($transferencia) > 0){
            return true;
        } else {
            return false;
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function verificar_estatus($codigo, $codigo_origen) {

        /*  --------------------------------------------------------------------------------- */

        // VERIFICAR SI EXISTE 

        $transferencia = DB::connection('retail')
        ->table('transferencias')
        ->select(DB::raw(
                        'ESTATUS'
                    ))
        ->where('ID_SUCURSAL','=', $codigo_origen)
        ->where('CODIGO','=', $codigo)
        ->get();

        /*  --------------------------------------------------------------------------------- */
        
        // RETORNAL VALOR 
        
        if(count($transferencia) > 0){
            return $transferencia[0]->ESTATUS;
        } else {
            return false;
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function eliminar($codigo, $opcion) 
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $codigo = $codigo["data"];
        $estatus = '';
        $diaHora = date("Y-m-d H:i:s");
        
        /*  --------------------------------------------------------------------------------- */

        // VERIFICAR SI EXISTE
        
        if (Transferencia::verificar_existencia($codigo) === false) {
            return ["response" => false, "statusText" => "No existe Transferencia !"];
        }

        /*  --------------------------------------------------------------------------------- */

        // VERIFICAR ESTATUS
        
        $estatus = Transferencia::verificar_estatus($codigo, $user->id_sucursal);

        if ($estatus === false) {
            return ["response" => false, "statusText" => "Ya se encuentra procesada o enviada !"];
        }

        /*  --------------------------------------------------------------------------------- */

        // VERIFICAR SI LA TRANSFERENCIA YA SE PROCESO 

        if ($estatus === 1 or $estatus === 2) {
            return ["response" => false, "statusText" => "Ya se encuentra procesada o enviada !"];
        }

        /*  --------------------------------------------------------------------------------- */

        // OBTENER EL CODIGO Y LA CANTIDA DEL PRODUCTO 

        $transferencias_det = DB::connection('retail')
        ->table('transferencias_det')
        ->select(DB::raw(
                        'ID,
                        CODIGO_PROD, 
                        CANTIDAD,
                        LOTE'
                    ))
        ->where('ID_SUCURSAL','=', $user->id_sucursal)
        ->where('CODIGO','=', $codigo)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RECORRER FILAS DE TRANSFERENCIA DET 

        foreach ($transferencias_det as $key => $td) {

            /*  --------------------------------------------------------------------------------- */

            // ELIMINAR CLAVES FORANEAS DE TRANSFERENCIA TIENE LOTES 
            
            $tdtl = TransferenciaDet_tiene_Lotes::eliminar_referencia($td->ID);
            
            /*  --------------------------------------------------------------------------------- */

            // DEVOLVER STOCK 

            foreach ($tdtl as $key => $value) {
                Stock::sumar_stock_id_lote($value->ID_LOTE, $value->CANTIDAD);
            }

            /*  --------------------------------------------------------------------------------- */

        }

        /*  --------------------------------------------------------------------------------- */

        // ELIMINAR TODA TRANSFERENIA DET

        $transferencias_det = DB::connection('retail')
        ->table('transferencias_det')
        ->where('ID_SUCURSAL','=', $user->id_sucursal)
        ->where('CODIGO','=', $codigo)
        ->delete();

        /*  --------------------------------------------------------------------------------- */

        // ELIMINAR TODA TRANSFERENIA

        if ($opcion === 1) {

            /*  --------------------------------------------------------------------------------- */

            $transferencia = Transferencia::select(DB::raw('ID'))
            ->where('CODIGO','=', $codigo)
            ->where('ID_SUCURSAL','=',$user->id_sucursal)
            ->get();

            /*  --------------------------------------------------------------------------------- */

            // INSERTAR USER REFERENCIA

            TransferenciaUser::guardar_referencia($user->id, 3, $transferencia[0]->ID, $diaHora);

            /*  --------------------------------------------------------------------------------- */

           $transferencias = DB::connection('retail')
           ->table('transferencias')
           ->where('ID_SUCURSAL','=', $user->id_sucursal)
           ->where('CODIGO','=', $codigo)
           ->delete();
 
        }
        
        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return ["response" => true];

        /*  --------------------------------------------------------------------------------- */
    }

    public static function modificar($data) 
    {

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $codigo = $data["codigo"];

        /*  --------------------------------------------------------------------------------- */

        // ELIMINAR TRANSFERENCIA 

        $eliminar = Transferencia::eliminar(["data" => $codigo], 2);

        /*  --------------------------------------------------------------------------------- */

        // SI ELIMINAR RETORNA FALSE - SE DETIENE LA MODIFICACION
         
        if ($eliminar["response"] === false) {
            return ["response" => false, "statusText" => $eliminar["statusText"]];
        }

        /*  --------------------------------------------------------------------------------- */

        // MODIFICAR TRANSFERENCIA 

        Transferencia::guardar_modificar($data, 2);

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return ["response" => true];

        /*  --------------------------------------------------------------------------------- */

    }

    public static function mostrar_importar($request) {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'CODIGO',
                            1 => 'CODIGO_ORIGEN', 
                            3 => 'ORIGEN',
                            4 => 'RESPONSABLE',
                            5 => 'FECHA',
                            6 => 'HORA',
                            7 => 'TOTAL',
                            8 => 'ESTATUS',
                            9 => 'ACCION',
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = Transferencia::where('SUCURSAL_DESTINO','=', $user->id_sucursal)
                     ->where('TRANSFERENCIAS.ESTATUS','<>', 0)
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

            $posts = Transferencia::select(DB::raw('TRANSFERENCIAS.CODIGO, ORIGEN.CODIGO AS CODIGO_ORIGEN, ORIGEN.DESCRIPCION AS ORIGEN, EMPLEADOS.NOMBRE AS RESPONSABLE, TRANSFERENCIAS.FECHA, TRANSFERENCIAS.HORA, TRANSFERENCIAS.TOTAL, TRANSFERENCIAS.ESTATUS, TRANSFERENCIAS.MONEDA'))
                         ->leftjoin('SUCURSALES AS ORIGEN', 'ORIGEN.CODIGO', '=', 'TRANSFERENCIAS.SUCURSAL_ORIGEN')
                         ->leftJoin('EMPLEADOS', function($join){
                                $join->on('EMPLEADOS.CODIGO', '=', 'TRANSFERENCIAS.ENVIA')
                                     ->on('EMPLEADOS.ID_SUCURSAL', '=', 'TRANSFERENCIAS.ID_SUCURSAL');
                            })
                         ->where('TRANSFERENCIAS.SUCURSAL_DESTINO','=', $user->id_sucursal)
                         ->where('TRANSFERENCIAS.ESTATUS','<>', 0)
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

            $posts =  Transferencia::select(DB::raw('TRANSFERENCIAS.CODIGO, ORIGEN.CODIGO AS CODIGO_ORIGEN, ORIGEN.DESCRIPCION AS ORIGEN, EMPLEADOS.NOMBRE AS RESPONSABLE, TRANSFERENCIAS.FECHA, TRANSFERENCIAS.HORA, TRANSFERENCIAS.TOTAL, TRANSFERENCIAS.ESTATUS, TRANSFERENCIAS.MONEDA'))
                            ->leftjoin('SUCURSALES AS ORIGEN', 'ORIGEN.CODIGO', '=', 'TRANSFERENCIAS.SUCURSAL_ORIGEN')
                            ->leftJoin('EMPLEADOS', function($join){
                                $join->on('EMPLEADOS.CODIGO', '=', 'TRANSFERENCIAS.ENVIA')
                                     ->on('EMPLEADOS.ID_SUCURSAL', '=', 'TRANSFERENCIAS.ID_SUCURSAL');
                            })
                            ->where('TRANSFERENCIAS.SUCURSAL_DESTINO','=', $user->id_sucursal)
                            ->where(function ($query) use ($search) {
                                $query->where('TRANSFERENCIAS.CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('ORIGEN.DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                            ->where('TRANSFERENCIAS.ESTATUS','<>', 0)
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = Transferencia::where('TRANSFERENCIAS.SUCURSAL_DESTINO','=', $user->id_sucursal)
                            ->leftjoin('SUCURSALES AS DESTINO', 'DESTINO.CODIGO', '=', 'TRANSFERENCIAS.SUCURSAL_DESTINO')
                            ->where(function ($query) use ($search) {
                                $query->where('TRANSFERENCIAS.CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('DESTINO.DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                            ->where('TRANSFERENCIAS.ESTATUS','<>', 0)
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
                $nestedData['CODIGO_ORIGEN'] = $post->CODIGO_ORIGEN;
                $nestedData['ORIGEN'] = $post->ORIGEN;
                $nestedData['RESPONSABLE'] = $post->RESPONSABLE;
                $nestedData['FECHA'] = $post->FECHA;
                $nestedData['HORA'] = $post->HORA;
                $nestedData['TOTAL'] = Common::precio_candec($post->TOTAL, $post->MONEDA);

                if ($post->ESTATUS === 1) {
                    $nestedData['ESTATUS'] = '<span class="badge badge-warning">Pendiente</span>';
                } else if ($post->ESTATUS === 2) {
                    $nestedData['ESTATUS'] = '<span class="badge badge-success">Procesado</span>';
                }
                
                $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarTransferencia' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a> &emsp;<a href='#' id='importarTransferencia' title='Importar'><i class='fa fa-check text-success' aria-hidden='true'></i></a>&emsp;<a href='#' id='rechazarTransferencia' title='Cancelar'><i class='fa fa-times text-danger' aria-hidden='true'></i></a>
                    &emsp;<a href='#' id='imprimirReporte' title='Reporte'><i class='fa fa-file text-secondary' aria-hidden='true'></i>";

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

    public static function mostrar_productos($request) {

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $codigo = $request->input('codigoTransferencia');
        $codigo_origen = $request->input('codigo_origen');

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'ITEM', 
                            1 => 'COD_PROD',
                            2 => 'DESCRIPCION',
                            3 => 'CANTIDAD',
                            4 => 'PRECIO',
                            5 => 'TOTAL'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE PRODUCTOS ENCONTRADOS 

        $totalData = DB::connection('retail')->table('transferencias_det as td')
                    ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'td.CODIGO_PROD')
                    ->leftJoin('transferencias as t', function($join){
                        $join->on('t.CODIGO', '=', 'td.CODIGO')
                             ->on('t.ID_SUCURSAL', '=', 'td.ID_SUCURSAL');
                    })
                    ->where('t.SUCURSAL_ORIGEN','=', $codigo_origen)
                    ->where('td.CODIGO','=', $codigo)
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

            $posts = DB::connection('retail')->table('transferencias_det as td')
                         ->select(DB::raw('td.ITEM, td.CODIGO_PROD, td.DESCRIPCION, td.CANTIDAD, td.PRECIO, td.TOTAL, t.MONEDA'))
                         ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'td.CODIGO_PROD')
                         ->leftJoin('transferencias as t', function($join){
                            $join->on('t.CODIGO', '=', 'td.CODIGO')
                                 ->on('t.ID_SUCURSAL', '=', 'td.ID_SUCURSAL');
                         })
                         ->where('td.ID_SUCURSAL','=', $codigo_origen)
                         ->where('td.CODIGO','=', $codigo)
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

            $posts =  DB::connection('retail')->table('transferencias_det as td')
                        ->select(DB::raw('td.ITEM, td.CODIGO_PROD, td.DESCRIPCION, td.CANTIDAD, td.PRECIO, td.TOTAL, t.MONEDA'))
                         ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'td.CODIGO_PROD')
                         ->leftJoin('transferencias as t', function($join){
                            $join->on('t.CODIGO', '=', 'td.CODIGO')
                                 ->on('t.ID_SUCURSAL', '=', 'td.ID_SUCURSAL');
                         })
                         ->where('t.SUCURSAL_ORIGEN','=', $codigo_origen)
                         ->where('td.CODIGO','=', $codigo)
                            ->where(function ($query) use ($search) {
                                $query->where('td.CODIGO_PROD','LIKE',"%{$search}%")
                                      ->orWhere('td.DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = DB::connection('retail')->table('transferencias_det as td')
                            ->select(DB::raw('td.ITEM, td.CODIGO_PROD, td.DESCRIPCION, td.CANTIDAD, td.PRECIO, td.TOTAL'))
                             ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'td.CODIGO_PROD')
                             ->leftJoin('transferencias as t', function($join){
                                $join->on('t.CODIGO', '=', 'td.CODIGO')
                                     ->on('t.ID_SUCURSAL', '=', 'td.ID_SUCURSAL');
                             })
                             ->where('t.SUCURSAL_ORIGEN','=', $codigo_origen)
                             ->where('td.CODIGO','=', $codigo)
                            ->where(function ($query) use ($search) {
                                $query->where('td.CODIGO_PROD','LIKE',"%{$search}%")
                                      ->orWhere('td.DESCRIPCION', 'LIKE',"%{$search}%");
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

                $nestedData['ITEM'] = $post->ITEM;
                $nestedData['COD_PROD'] = $post->CODIGO_PROD;
                $nestedData['DESCRIPCION'] = $post->DESCRIPCION;
                $nestedData['CANTIDAD'] = $post->CANTIDAD;
                $nestedData['PRECIO'] = Common::precio_candec($post->PRECIO, $post->MONEDA);
                $nestedData['TOTAL'] = Common::precio_candec($post->TOTAL, $post->MONEDA);


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

    public static function rechazar_transferencia($data){

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $codigo = $data["codigo"];
        $codigo_origen = $data["codigo_origen"];

        /*  --------------------------------------------------------------------------------- */

        // REVISAR ESTATUS 

        $estatus = Transferencia::verificar_estatus($codigo, $codigo_origen);

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EL ESTATUS SE ENCUENTRA EN 1 PARA SER RECHAZADO 

        if ($estatus === false or $estatus === 0 or $estatus === 2) {
            return ["response" => false, "statusText" => "Ya se encuentra procesada !"];
        }

        /*  --------------------------------------------------------------------------------- */

        // LA TRANSFERENCIA SOLO SERA MODIFICADA SI SE ENCUENTRA EN ESTATUS 1 QUE ES ENVIADO

        if ($estatus === 1) {

            $transferencia = DB::connection('retail')
            ->table('transferencias')
            ->where('CODIGO','=', $codigo)
            ->where('ID_SUCURSAL','=', $codigo_origen)
            ->update([
                'ESTATUS' => 0
            ]);

        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return true;

        /*  --------------------------------------------------------------------------------- */

    }

    public static function aceptar_transferencia($data){

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $codigo = $data["codigo"];
        $codigo_origen = $data["codigo_origen"];

        /*  --------------------------------------------------------------------------------- */

        // REVISAR ESTATUS 

        $estatus = Transferencia::verificar_estatus($codigo, $codigo_origen);

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EL ESTATUS SE ENCUENTRA EN 1 PARA SER IMPORTADO

        if ($estatus === false or $estatus === 0 or $estatus === 2) {
            return false;
        }

        /*  --------------------------------------------------------------------------------- */

        // LA TRANSFERENCIA SOLO SERA MODIFICADA SI SE ENCUENTRA EN ESTATUS 1 QUE ES ENVIADO

        if ($estatus === 1) {

            $transferencia = DB::connection('retail')
            ->table('transferencias')
            ->where('CODIGO','=', $codigo)
            ->where('ID_SUCURSAL','=', $codigo_origen)
            ->update([
                'ESTATUS' => 2
            ]);

        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return true;

        /*  --------------------------------------------------------------------------------- */

    }

    public static function enviar_transferencia($data){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $codigo = $data["data"];

        /*  --------------------------------------------------------------------------------- */

        // REVISAR ESTATUS 

        $estatus = Transferencia::verificar_estatus($codigo, $user->id_sucursal);

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EL ESTATUS SE ENCUENTRA EN 1 PARA SER IMPORTADO

        if ($estatus === false or $estatus === 1 or $estatus === 2) {
            return ["response" => false, "statusText" => "Ya se encuentra procesada o enviada !"];
        }

        /*  --------------------------------------------------------------------------------- */

        // LA TRANSFERENCIA SOLO SERA MODIFICADA SI SE ENCUENTRA EN ESTATUS 1 QUE ES ENVIADO

        if ($estatus === 0) {

            $transferencia = DB::connection('retail')
            ->table('transferencias')
            ->where('CODIGO','=', $codigo)
            ->where('ID_SUCURSAL','=', $user->id_sucursal)
            ->update([
                'ESTATUS' => 1
            ]);

        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return ["response" => true];

        /*  --------------------------------------------------------------------------------- */

    }

    public static function importar_transferencia($data)
    {

        try {
            
            /*  --------------------------------------------------------------------------------- */

            // INICIAR TRANSACCION 

            DB::connection('retail')->beginTransaction();

            /*  --------------------------------------------------------------------------------- */

            // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

            $user = auth()->user();

            /*  --------------------------------------------------------------------------------- */

            // INICIAR VARIABLES 

            $dia = date("Y-m-d");
            $hora = date("H:i:s");
            $codigo = $data["codigo"];
            $codigo_origen = $data["codigo_origen"];
            $conversion = false;

            $precio_venta = 0;
            $precio_mayorista = 0;
            $precio_vip = 0;
            $formula = 0;

            /*  --------------------------------------------------------------------------------- */

            // REVISAR ESTATUS 

            $estatus = Transferencia::verificar_estatus($codigo, $codigo_origen);

            /*  --------------------------------------------------------------------------------- */

            // REVISAR SI EL ESTATUS SE ENCUENTRA EN 1 PARA SER IMPORTADO

            if ($estatus === false or $estatus === 0 or $estatus === 2) {
                return ["response" => false, "statusText" => "Ya se encuentra procesada !"];
            }

            /*  --------------------------------------------------------------------------------- */

            // OBTENER DATOS TRANSFERENCIA

            $transferencia = DB::connection('retail')
            ->table('transferencias')
            ->select(DB::raw(
                            'ID, 
                            SUCURSAL_DESTINO,
                            CAMBIO, 
                            MONEDA,
                            MONEDA_ENVIAR'
                        ))
            ->where('ID_SUCURSAL','=', $codigo_origen)
            ->where('CODIGO','=', $codigo)
            ->get();

            /*  --------------------------------------------------------------------------------- */

            // CARGAR VARIABLES TRANSFERENCIA

            $monedaTransferencia = $transferencia[0]->MONEDA;
            $monedaEnviada = $transferencia[0]->MONEDA_ENVIAR;
            $cambio = $transferencia[0]->CAMBIO;
            $id = $transferencia[0]->ID;
            $usere = 'TRA-'.$id;

            /*  --------------------------------------------------------------------------------- */

            // REVISAR MONEDA SUCURSAL 

            $parametro = Parametro::mostrarParametro();
            $monedaSucursal = $parametro["parametros"][0]->MONEDA;

            /*  --------------------------------------------------------------------------------- */

            // REVISAR SI MONEDA DE TRANSFERENCIA ES DIFERENTE A MONEDA ENVIO 

            if ($monedaTransferencia !== $monedaEnviada) {
                $conversion = true;
            }

            /*  --------------------------------------------------------------------------------- */

            // TIPO DE CALCULO 
            
            if ($monedaTransferencia === 1 && $monedaEnviada === 2) {

                /*  --------------------------------------------- */

                // DIVIDIR - GUARANIES A DOLAR 

                $formula = 2;

                /*  --------------------------------------------- */

            } else if ($monedaTransferencia === 2 && $monedaEnviada === 1) {

                /*  --------------------------------------------- */

                // MULTIPLICAR - DOLAR A GUARANIES

                $formula = 1;

                /*  --------------------------------------------- */

            } else if ($monedaTransferencia === 1 && $monedaEnviada === 3) {

                /*  --------------------------------------------- */

                // DIVIDIR - GUARANIES A PESOS

                $formula = 2;

                /*  --------------------------------------------- */

            } else if ($monedaTransferencia === 1 && $monedaEnviada === 4) {

                /*  --------------------------------------------- */

                // DIVIDIR - GUARANIES A REAL 

                $formula = 2;

                /*  --------------------------------------------- */

            } else if ($monedaTransferencia === 2 && $monedaEnviada === 3) {

                /*  --------------------------------------------- */

                // MULTIPLICAR - DOLAR A PESO 

                $formula = 1;

                /*  --------------------------------------------- */

            } else if ($monedaTransferencia === 2 && $monedaEnviada === 4) {

                /*  --------------------------------------------- */

                // MULTIPLICAR - DOLAR A PESO 

                $formula = 1;

                /*  --------------------------------------------- */

            }

            /*  --------------------------------------------------------------------------------- */

            // REVISAR SI SUCURSAL DESTINO ES IGUAL A LA SUCURSAL

            if ($transferencia[0]->SUCURSAL_DESTINO !== $user->id_sucursal) {
                return ["response" => false, "statusText" => "La transferencia no es de esta sucursal !"];
            }

            /*  --------------------------------------------------------------------------------- */

            // CAMBIAR ESTADO DE TRANSFERENCIA A PROCESADO

            if (Transferencia::aceptar_transferencia($data) === false) {
                return ["response" => false, "statusText" => "La transferencia no pudo cambiar de estado !"];
            }

            /*  --------------------------------------------------------------------------------- */

            // OBTENER TODOS LOS PRODUCTOS 

            $transferencia_det = DB::connection('retail')
            ->table('TRANSFERENCIAS_DET')
            ->leftjoin('TRANSFERENCIADET_TIENE_LOTES', 'TRANSFERENCIADET_TIENE_LOTES.ID_TRANSFERENCIA', '=', 'TRANSFERENCIAS_DET.ID')
            ->leftjoin('LOTES', 'TRANSFERENCIADET_TIENE_LOTES.ID_LOTE', '=', 'LOTES.ID')
            ->select(DB::raw(
                            'TRANSFERENCIAS_DET.ID,
                            TRANSFERENCIAS_DET.CODIGO_PROD, 
                            TRANSFERENCIAS_DET.CANTIDAD, 
                            TRANSFERENCIAS_DET.PRECIO,
                            LOTES.FECHA_VENC AS VENCIMIENTO'
                        ))
            ->where('TRANSFERENCIAS_DET.ID_SUCURSAL','=', $codigo_origen)
            ->where('TRANSFERENCIAS_DET.CODIGO','=', $codigo)
            ->groupBy('TRANSFERENCIAS_DET.ID')
            ->get();
            
            /*  --------------------------------------------------------------------------------- */

            // RECORRER TODOS LOS PRODUCTOS 

            foreach ($transferencia_det as $td) {

                /*  --------------------------------------------------------------------------------- */
                
                // OBTENER DATOS DEL PRODUCTO 

                $producto = DB::connection('retail')
                ->table('PRODUCTOS_AUX')
                ->where('CODIGO', '=', $td->CODIGO_PROD)
                ->where('ID_SUCURSAL', '=', $codigo_origen)
                ->select(DB::raw('CODIGO_INTERNO, BAJA, PREC_VENTA, PREMAYORISTA, PREVIP, DESCUENTO, STOCK_MIN, PROVEEDOR, OBSERVACION, PORCENTAJE, CODIGO_REAL, MONEDA'))
                ->get();

                /*  --------------------------------------------------------------------------------- */

                // REVISAR SI CONVERSION ES VERDADERO PARA COTIZAR
                
                if ($conversion === true && $producto[0]->MONEDA !== $monedaSucursal) {

                    if ($formula === 1) {

                        $precio_venta = $td->PRECIO * $cambio;
                        $precio_mayorista = $producto[0]->PREMAYORISTA * $cambio;
                        $precio_vip = $producto[0]->PREVIP * $cambio;  

                    } else if ($formula === 2) {
                        
                        $precio_venta = $td->PRECIO / $cambio;
                        $precio_mayorista = $producto[0]->PREMAYORISTA / $cambio;
                        $precio_vip = $producto[0]->PREVIP / $cambio; 

                    }
                        

                } else {
                    
                    $precio_venta = $producto[0]->PREC_VENTA;
                    $precio_mayorista = $producto[0]->PREMAYORISTA;
                    $precio_vip = $producto[0]->PREVIP;
    
                }
                
                /*  --------------------------------------------------------------------------------- */

                // REVISAR SI EXISTE PRODUCTO 

                if (Producto::existeProducto($td->CODIGO_PROD, $user->id_sucursal) !== true) {

                    /*  --------------------------------------------------------------------------------- */

                    // INSERTAR PRODUCTO 

                    $productos_aux = ProductosAux::insert(
                        [
                        'CODIGO' => $td->CODIGO_PROD, 
                        'CODIGO_INTERNO' => $producto[0]->CODIGO_INTERNO,
                        'BAJA' => $producto[0]->BAJA,
                        'MONEDA' =>  $monedaEnviada,
                        'PRECOSTO' => $precio_venta,
                        'PREC_VENTA' => $precio_venta,
                        'PREMAYORISTA' => $precio_mayorista,
                        'PREVIP' => $precio_vip,
                        'ID_SUCURSAL' => $user->id_sucursal,
                        'DESCUENTO' => $producto[0]->DESCUENTO,
                        'STOCK_MIN' => $producto[0]->STOCK_MIN,
                        'PROVEEDOR' => $producto[0]->PROVEEDOR,
                        'OBSERVACION' => $producto[0]->OBSERVACION,
                        'PORCENTAJE' => $producto[0]->PROVEEDOR,
                        'USER' => $user->name,
                        'CODIGO_REAL' => $producto[0]->CODIGO_REAL,
                        'FECALTAS' => $dia,
                        'HORALTAS' => $hora,
                        'USERM' => $usere
                        ]
                    );

                    /*  --------------------------------------------------------------------------------- */

                }

                /*  --------------------------------------------------------------------------------- */

                // CREAR LOTE DEL PRODUCTO 

                $lote = (Stock::insetar_lote($td->CODIGO_PROD, $td->CANTIDAD, $precio_venta, 2, $usere, $td->VENCIMIENTO))["id"];

                /*  --------------------------------------------------------------------------------- */

                // MODIFICAR TRANSFERENCIA DET 

                /*  --------------------------------------------------------------------------------- */

                // MODOS
                // MODO 1 - COMPRA
                // MODO 2 - TRANSFERENCIA 

                Lotes_tiene_TransferenciaDet::guardar_referencia($td->ID, $lote);
                //Transferencia::agregar_lote_tranferencia_det($td->CODIGO_PROD, $codigo_origen, $lote);

                /*  --------------------------------------------------------------------------------- */

            }

            /*  --------------------------------------------------------------------------------- */
            
            // ENVIAR TRANSACCION A BD

            DB::connection('retail')->commit();

            /*  --------------------------------------------------------------------------------- */

            return ["response" => true];

            /*  --------------------------------------------------------------------------------- */

        } catch (Exception $e) {
            
            /*  --------------------------------------------------------------------------------- */

           // NO GUARDAR NINGUN CAMBIO 

           DB::connection('retail')->rollBack();
           throw $e;
           
           /*  --------------------------------------------------------------------------------- */

        }

    }

    public static function agregar_lote_tranferencia_det($codigo, $codigo_origen, $lote){

        /*  --------------------------------------------------------------------------------- */

        // MODIFICAR TRANSFERENCIA DET 

        $transferencia_det = DB::connection('retail')
        ->table('transferencias_det')
        ->where('CODIGO_PROD','=', $codigo)
        ->where('ID_SUCURSAL','=',  $codigo_origen)
        ->update([
            'LOTE' => $lote
            ]);

        /*  --------------------------------------------------------------------------------- */
            
    }

    public static function detalle_productos($request) {

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $codigo = $request->input('codigoTransferencia');

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'ITEM', 
                            1 => 'COD_PROD',
                            2 => 'DESCRIPCION',
                            3 => 'CANTIDAD',
                            4 => 'PRECIO',
                            5 => 'TOTAL'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE PRODUCTOS ENCONTRADOS 

        $totalData = DB::connection('retail')->table('transferencias_det as td')
                    ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'td.CODIGO_PROD')
                    ->leftJoin('transferencias as t', function($join){
                        $join->on('t.CODIGO', '=', 'td.CODIGO')
                             ->on('t.ID_SUCURSAL', '=', 'td.ID_SUCURSAL');
                    })
                    ->where('t.SUCURSAL_ORIGEN','=', $user->id_sucursal)
                    ->where('td.CODIGO','=', $codigo)
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

            $posts = DB::connection('retail')->table('transferencias_det as td')
                         ->select(DB::raw('td.ITEM, td.CODIGO_PROD, td.DESCRIPCION, td.CANTIDAD, td.PRECIO, td.TOTAL'))
                         ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'td.CODIGO_PROD')
                         ->where('td.ID_SUCURSAL','=', $codigo_origen)
                         ->where('td.CODIGO','=', $user->id_sucursal)
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

            $posts =  DB::connection('retail')->table('transferencias_det as td')
                        ->select(DB::raw('td.ITEM, td.CODIGO_PROD, td.DESCRIPCION, td.CANTIDAD, td.PRECIO, td.TOTAL'))
                         ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'td.CODIGO_PROD')
                         ->leftJoin('transferencias as t', function($join){
                            $join->on('t.CODIGO', '=', 'td.CODIGO')
                                 ->on('t.ID_SUCURSAL', '=', 'td.ID_SUCURSAL');
                         })
                         ->where('t.SUCURSAL_ORIGEN','=', $user->id_sucursal)
                         ->where('td.CODIGO','=', $codigo)
                            ->where(function ($query) use ($search) {
                                $query->where('td.CODIGO_PROD','LIKE',"%{$search}%")
                                      ->orWhere('td.DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = DB::connection('retail')->table('transferencias_det as td')
                            ->select(DB::raw('td.ITEM, td.CODIGO_PROD, td.DESCRIPCION, td.CANTIDAD, td.PRECIO, td.TOTAL'))
                             ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'td.CODIGO_PROD')
                             ->leftJoin('transferencias as t', function($join){
                                $join->on('t.CODIGO', '=', 'td.CODIGO')
                                     ->on('t.ID_SUCURSAL', '=', 'td.ID_SUCURSAL');
                             })
                             ->where('t.SUCURSAL_ORIGEN','=', $user->id_sucursal)
                             ->where('td.CODIGO','=', $codigo)
                            ->where(function ($query) use ($search) {
                                $query->where('td.CODIGO_PROD','LIKE',"%{$search}%")
                                      ->orWhere('td.DESCRIPCION', 'LIKE',"%{$search}%");
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

                $nestedData['ITEM'] = $post->ITEM;
                $nestedData['COD_PROD'] = $post->CODIGO_PROD;
                $nestedData['DESCRIPCION'] = $post->DESCRIPCION;
                $nestedData['CANTIDAD'] = $post->CANTIDAD;
                $nestedData['PRECIO'] = $post->PRECIO;
                $nestedData['TOTAL'] = $post->TOTAL;


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

    public static function insertar_inventario($dato) {

       /*  --------------------------------------------------------------------------------- */
       
       // INICIAR VARIABLE 

       $codigo = $dato["codigo"];
       $codigointerno = 'Nulo';

       /*  --------------------------------------------------------------------------------- */

       $codigo_interno = DB::connection('retail')
        ->table('PRODUCTOS_AUX')
        ->select(DB::raw('CODIGO'))
        ->where('CODIGO_INTERNO', '=', $codigo)
        ->get();

       /*  --------------------------------------------------------------------------------- */

       // CAMBIAR CODIGO PRODUCTO

       if (count($codigo_interno) > 0) {
            $codigointerno = $codigo;
            $codigo = $codigo_interno[0]->CODIGO;
       } 
         
       /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE PRODUCTO 

        $producto = DB::connection('ret')
        ->table('PRODUCTOS_AUX')
        ->select(DB::raw('PRODUCTOS_AUX.CODIGO, PRODUCTOS.MARCA'),
        DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS STOCK'))
        ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
        ->where('PRODUCTOS_AUX.CODIGO', '=', $codigo)
        ->where('PRODUCTOS_AUX.ID_SUCURSAL', '=', 4)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
        
        if (count($producto) > 0) {
            $stock = $producto[0]->STOCK;
        } else {
            return ["response" => false, "status" => "No existe producto", "codigo" => $codigo];
        }

        /*  --------------------------------------------------------------------------------- */

        // $lote = DB::connection('ret')
        // ->table('LOTES')
        // ->select(DB::raw('SUM(CANTIDAD) AS STOCK'))
        // ->where('COD_PROD', '=', $codigo)
        // ->where('ID_SUCURSAL', '=', '4')
        // ->get();

        
        /*  --------------------------------------------------------------------------------- */
       
       // INSERTAR 

       // DB::connection('retail')
       // ->table('lista_inventario')
       // ->updateOrInsert(
       //      ['CODIGO' => $codigo],
       //      ['CANTIDAD' => $cantidad]
       //  );

       /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE PRODUCTO 

        $existe = DB::connection('retail')
        ->table('lista_inventario')
        ->select(DB::raw('STOCK'))
        ->where('CODIGO', '=', $codigo)
        ->get();

        if (count($existe) > 0) {
            $stock = $existe[0]->STOCK;
        } 

        /*  --------------------------------------------------------------------------------- */

       $insert = DB::connection('retail')
       ->table('lista_inventario')
       ->updateOrInsert(
            ['CODIGO' => $codigo],
            ['CANTIDAD' => \DB::raw('CANTIDAD + 1'), 'STOCK' => $stock, 'MARCA' => $producto[0]->MARCA]
        );

       /*  --------------------------------------------------------------------------------- */

       // RETORNAR VALOR 

       if ($insert === true) {

        $pro = DB::connection('retail')
        ->table('lista_inventario')
        ->select(DB::raw('CANTIDAD'))
        ->where('CODIGO', '=', $codigo)
        ->limit(1)
        ->get();

          return ["response" => true, "status" => "Guardado correctamente", "codigo" => $codigo, "cantidad" => $pro[0]->CANTIDAD." - Stock: ".$stock." - Interno: ".$codigointerno];
       } 
       

       /*  --------------------------------------------------------------------------------- */


    }

    public static function pdf($accion='ver',$tipo='digital')
    {
        $accion='descargar';
        $tipo='fisico';
        $ruc = "10072486893";
        $numero = "00000412";
        $nombres = "DAVID OLIVARES PEÑA";
        $dia = "09";
        $mes = "04";
        $ayo = "17";
        $direccion = "Lima Perú";
        $dni = "23918745";
        $total = 0;
        $articulos = [
            [
                "cantidad" => 3,
                "descripcion" => "COCINA A GAS",
                "precio" => 400.00,
                "importe" => 1200,
            ],
            [
                "cantidad" => 1,
                "descripcion" => "PLANCHA",
                "precio" => 85.00,
                "importe" => 85.00,
            ],
        ];
        foreach ($articulos as $key => $value) {
            $total += $value["importe"];
            $articulos[$key]["precio"] = number_format($value["precio"],2,'.',' ');;
            $articulos[$key]["importe"] = number_format($value["importe"],2,'.',' ');;
 
        }
        $total = number_format($total,2,'.',' ');
 
        $data['ruc'] = $ruc;
        $data['numero'] = $numero;
        $data['nombres'] = $nombres;
        $data['dia'] = $dia;
        $data['mes'] = $mes;
        $data['ayo'] = $ayo;
        $data['direccion'] = $direccion;
        $data['dni'] = $dni;
        $data['articulos'] = $articulos;
        $data['total'] = $total;
        $data['tipo'] = $tipo;
 
        if($accion=='html'){
            return view('pdf.rptTransferencia',$data);
        }else{
            $html = view('pdf.rptTransferencia',$data)->render();
        }
        $namefile = 'boleta_de_venta_'.time().'.pdf';
 
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
 
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        $mpdf = new Mpdf([
            'fontDir' => array_merge($fontDirs, [
                public_path() . '/fonts',
            ]),
            'fontdata' => $fontData + [
                'arial' => [
                    'R' => 'arial.ttf',
                    'B' => 'arialbd.ttf',
                ],
            ],
            'default_font' => 'arial',
             "format" => "A4",
            //"format" => [264.8,188.9],
        ]);
        // $mpdf->SetTopMargin(5);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->WriteHTML($html);

        // OTRA HOJA 
        $mpdf->AddPage();
        $mpdf->WriteHTML($html);
        // dd($mpdf);
        //$mpdf->debug = true;
        if($accion=='ver'){
            $mpdf->Output($namefile,"I");
        }elseif($accion=='descargar'){
            $mpdf->Output($namefile,"D");
        }
    }

    public static function factura_pdf($dato)
    {
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER DATOS DE CABECERA 

        $transferencia = Transferencia::mostrar_cabecera($dato);

        /*  --------------------------------------------------------------------------------- */

        // OBTENER DATOS DETALLE 

        $transferencia_det = Transferencia::mostrar_cuerpo($dato);

        /*  --------------------------------------------------------------------------------- */

        // CANTIDAD DE DECIMALES Y MONEDA

        $candec = 0;
        $moneda = 1;
        $monedaTransferencia = $transferencia->MONEDA;

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI ES TABLA UNICA 

        $tab_unica = Parametro::tab_unica();

        if ($tab_unica === "SI") {
            $tab_unica = true;
        } else {
            $tab_unica = false;
        }

        /*  --------------------------------------------------------------------------------- */

        // OBTENER SUCURSAL

        $sucursal = Sucursal::encontrarSucursal(['codigoOrigen' => $user->id_sucursal]);
        
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $c = 0;
        $c_rows = 0;
        $c_rows_array = count($transferencia_det);
        $c_filas_total = count($transferencia_det);
        $codigo = $transferencia->CODIGO;
        $origen = $transferencia->ORIGEN;
        $destino = $transferencia->DESTINO;
        $envia = $transferencia->ENVIA;
        $transporta = $transferencia->TRANSPORTA;
        $recibe = $transferencia->RECIBE;
        $fecha = $transferencia->FECALTAS;
        $nombre = 'Transferencia_'.$codigo.'_'.time().'';
        $articulos = [];
        $cantidad = 0;
        $exentas = 0;
        $base5 = 0;
        $base10 = 0;
        $iva = 0;
        $total = 0;
        $nombre_sucursal = $sucursal['sucursal'][0]['DESCRIPCION'];
        $direccion = $sucursal['sucursal'][0]['DIRECCION'];
        $switch_hojas = false;
        $namefile = 'boleta_de_venta_'.time().'.pdf';
        $letra = '';

        /*  --------------------------------------------------------------------------------- */

        // CARGAR VARIABLES 
        
        $data['codigo'] = $codigo;
        $data['origen'] = $origen;
        $data['destino'] = $destino;
        $data['envia'] = $envia;
        $data['transporta'] = $transporta;
        $data['recibe'] = $recibe;
        $data['fecha'] = $fecha;
        $data['nombre'] = $nombre;
        $data['c'] = $c;
        $data['sucursal'] = $nombre_sucursal;
        $data['direccion'] = $direccion;
        $data['ruc'] = '111111-1';
        $data['tipo'] = 'fisico';

        /*  --------------------------------------------------------------------------------- */
        
        // INICIAR MPDF 

        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
         
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf([
            'fontDir' => array_merge($fontDirs, [
                public_path() . '/fonts',
            ]),
            'fontdata' => $fontData + [
                'arial' => [
                    'R' => 'arial.ttf',
                    'B' => 'arialbd.ttf',
                ],
            ],
            'default_font' => 'arial',
            "format" => [240,140],
        ]);

        $mpdf->SetDisplayMode('fullpage');

        /*  --------------------------------------------------------------------------------- */

        // CARGAR DETALLE DE TRANSFERENCIA DET 

        foreach ($transferencia_det as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            // SI LA MONEDA DEL PRODUCTO ES DIFERENTE A GUARANIES COTIZAR 

            if ($value->MONEDA <> 1) {

                /*  --------------------------------------------------------------------------------- */

                // PRECIO 

                $cotizacion = Cotizacion::CALMONED(['monedaProducto' => $monedaTransferencia, 'monedaSistema' => 1, 'precio' => Common::quitar_coma($value->PRECIO, $candec), 'decSistema' => 0, 'tab_unica' => $tab_unica, "id_sucursal" => $user->id_sucursal]);

                // SI NO ENCUENTRA COTIZACION RETORNAR 

                if ($cotizacion["response"] === false) {
                    header('HTTP/1.1 500 Internal Server Error');
                    exit;
                }

                $articulos[$c_rows]["precio"] = $cotizacion["valor"];

                /*  --------------------------------------------------------------------------------- */

                // TOTAL 

                $cotizacion = Cotizacion::CALMONED(['monedaProducto' => $monedaTransferencia, 'monedaSistema' => 1, 'precio' => Common::quitar_coma($value->TOTAL, $candec), 'decSistema' => 0, 'tab_unica' => $tab_unica, "id_sucursal" => $user->id_sucursal]);
                $articulos[$c_rows]["total"] = $cotizacion["valor"];

                // SI NO ENCUENTRA COTIZACION RETORNAR

                if ($cotizacion["response"] === false) {
                    header('HTTP/1.1 500 Internal Server Error');
                    exit;
                }

                $exentas = $exentas + Common::quitar_coma($articulos[$c_rows]["total"], $candec);
                $total = $total + Common::quitar_coma($articulos[$c_rows]["total"], $candec);

                /*  --------------------------------------------------------------------------------- */

            } else {
                $articulos[$c_rows]["precio"] = $value->PRECIO;
                $articulos[$c_rows]["total"] = $value->TOTAL;
                $exentas = $exentas + Common::quitar_coma($value->EXENTAS, $candec);
                $total = $total + Common::quitar_coma($value->TOTAL, $candec);
            }

            /*  --------------------------------------------------------------------------------- */

            // CARGAR VARIABLES 

            $articulos[$c_rows]["cantidad"] = $value->CANTIDAD;
            $articulos[$c_rows]["cod_prod"] = $value->CODIGO_PROD;
            $articulos[$c_rows]["descripcion"] = substr($value->DESCRIPCION, 0,30);
            $cantidad = $cantidad + $value->CANTIDAD;

            /*  --------------------------------------------------------------------------------- */

            // REVISAR SI EXISTE EXENTAS 

            if ($value->EXENTAS > 0) {
                $articulos[$c_rows]["exentas"] = $articulos[$c_rows]["total"];
            } else {
                $articulos[$c_rows]["exentas"] = '';
            }
            
            /*  --------------------------------------------------------------------------------- */

            // REVISAR SI EXISTE BASE5 O BASE10

            if ($value->BASE5 > 0) {
                $articulos[$c_rows]["base10"] = '';
                $articulos[$c_rows]["base5"] = $articulos[$c_rows]["total"];
                $base5 = $base5 + Common::quitar_coma($articulos[$c_rows]["total"], $candec);
            } else if ($value->BASE10 > 0) {
                $articulos[$c_rows]["base5"] = '';
                $articulos[$c_rows]["base10"] = $articulos[$c_rows]["total"];
                $base10 = $base10 + Common::quitar_coma($articulos[$c_rows]["total"], $candec);
            } else {
                $articulos[$c_rows]["base5"] = '';
                $articulos[$c_rows]["base10"] = '';
            }
            
            /*  --------------------------------------------------------------------------------- */
            
            // CONTAR CANTIDAD DE FILAS DE HOJAS 

            $c_rows = $c_rows + 1;    
            
            /*  --------------------------------------------------------------------------------- */

            // CONTAR LA CANTIDAD DE FILAS 

            $c = $c + 1;

            /*  --------------------------------------------------------------------------------- */

            // SI CANTIDAD DE FILAS ES IGUAL A 10 ENTONCES CREAR PAGINA 

            if ($c_rows === 10){
                
                /*  --------------------------------------------------------------------------------- */

                // AGREGAR ARTICULOS 

                $data['articulos'] = $articulos;

                /*  --------------------------------------------------------------------------------- */

                // RESTAR LAS CANTIDADES CARGADAS 

                $c_rows_array = $c_rows_array - 10;

                /*  --------------------------------------------------------------------------------- */

                // PONER TRUE SWITCH YA QUE CREO UNA PAGINA 

                $switch_hojas = true;

                /*  --------------------------------------------------------------------------------- */

                // CARGAR SUB TOTALES POR HOJA

                $data['cantidad'] = $cantidad;
                $data['letra'] = 'Son Guaranies: '.substr(NumeroALetras::convertir($total, 'guaranies'), 0, strpos(NumeroALetras::convertir($total, 'guaranies'), "CON"));
                $data['total'] = Common::precio_candec_sin_letra($total, $moneda);
                $data['exentas'] = Common::precio_candec_sin_letra($exentas, $moneda);
                $data['base5'] = Common::precio_candec_sin_letra($base5 / 21, $moneda);
                $data['base10'] = Common::precio_candec_sin_letra($base10 / 11, $moneda);
                $data['iva'] = Common::precio_candec_sin_letra(($base5 / 21) + ($base10 / 11), $moneda);

                /*  --------------------------------------------------------------------------------- */

                $html = view('pdf.facturaTransferencia', $data)->render();
                
                /*  --------------------------------------------------------------------------------- */

                // SI NO ES LA PRIMERA HOJA AGREGAR PAGINA

                if ($c !== 10) {
                    $mpdf->AddPage();
                }

                /*  --------------------------------------------------------------------------------- */

                // CERAR CONTADOR DE FILAS CARGADAS POR HOJAS Y ARTICULOS

                $c_rows = 0;
                $exentas = 0;
                $base5 = 0;
                $base10 = 0;
                $total = 0;
                $data['articulos'] = [];
                $articulos = [];

                /*  --------------------------------------------------------------------------------- */
                    
                $mpdf->WriteHTML($html);

                /*  --------------------------------------------------------------------------------- */

            } else if ($c_rows_array < 10 && $c_filas_total === $c) {
                
                /*  --------------------------------------------------------------------------------- */
                
                // AGREGAR ARTICULOS 
                
                $data['articulos'] = $articulos;

                /*  --------------------------------------------------------------------------------- */

                // CARGAR SUB TOTALES POR HOJA

                $data['cantidad'] = $cantidad;
                $data['letra'] = 'Son Guaranies: '.substr(NumeroALetras::convertir($total, 'guaranies'), 0, strpos(NumeroALetras::convertir($total, 'guaranies'), "CON"));
                $data['total'] = Common::precio_candec_sin_letra($total, $moneda);
                $data['exentas'] = Common::precio_candec_sin_letra($exentas, $moneda);
                $data['base5'] = Common::precio_candec_sin_letra($base5 / 21, $moneda);
                $data['base10'] = Common::precio_candec_sin_letra($base10 / 11, $moneda);
                $data['iva'] = Common::precio_candec_sin_letra(($base5 / 21) + ($base10 / 11), $moneda);

                /*  --------------------------------------------------------------------------------- */

                // CREAR HOJA 

                $html = view('pdf.facturaTransferencia', $data)->render();

                /*  --------------------------------------------------------------------------------- */

                // SI NO ES LA PRIMERA HOJA AGREGAR PAGINA

                if ($switch_hojas === true) {
                    $mpdf->AddPage();
                }

                /*  --------------------------------------------------------------------------------- */
                    
                $mpdf->WriteHTML($html);

                /*  --------------------------------------------------------------------------------- */

            }
        }

        /*  --------------------------------------------------------------------------------- */
        
        // DESCARGAR ARCHIVO PDF 

        $mpdf->Output($namefile,"D");

        /*  --------------------------------------------------------------------------------- */
        
    }

    public static function pdf_transferencia($dato)
    {
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $sucursal = 0;

        /*  --------------------------------------------------------------------------------- */

        // SI ENVIA CODIGO ORIGEN 

        if ($dato["codigo_origen"] === 0) {
            $sucursal = $user->id_sucursal;
        } else {
            $sucursal = $dato["codigo_origen"];
        }
         
        /*  --------------------------------------------------------------------------------- */

        // OBTENER DATOS DE CABECERA 

        $transferencia = Transferencia::mostrar_cabecera($dato);

        /*  --------------------------------------------------------------------------------- */

        // OBTENER DATOS DETALLE 

        $transferencia_det = Transferencia::mostrar_cuerpo($dato);

        /*  --------------------------------------------------------------------------------- */

        // OBTENER PARAMETRO 

        $moneda = $transferencia->MONEDA;
        $candec = Parametro::candec($moneda);

        /*  --------------------------------------------------------------------------------- */

        // OBTENER SUCURSAL

        $sucursal = Sucursal::encontrarSucursal(['codigoOrigen' => $sucursal]);
        
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $c = 0;
        $codigo = $transferencia->CODIGO;
        $origen = $transferencia->ORIGEN;
        $destino = $transferencia->DESTINO;
        $envia = $transferencia->ENVIA;
        $transporta = $transferencia->TRANSPORTA;
        $recibe = $transferencia->RECIBE;
        $fecha = $transferencia->FECALTAS;
        $nombre = 'Transferencia_'.$codigo.'_'.time().'';
        $articulos = [];
        $cantidad = 0;
        $total = 0;
        $nombre_sucursal = $sucursal['sucursal'][0]['DESCRIPCION'];
        $direccion = $sucursal['sucursal'][0]['DIRECCION'];

        /*  --------------------------------------------------------------------------------- */

        // CARGAR DETALLE DE TRANSFERENCIA DET 

        foreach ($transferencia_det as $key => $value) {
            $articulos[$key]["cantidad"] = $value->CANTIDAD;
            $articulos[$key]["cod_prod"] = $value->CODIGO_PROD;
            $articulos[$key]["descripcion"] = $value->DESCRIPCION;
            $articulos[$key]["precio"] = $value->PRECIO;
            $articulos[$key]["total"] = $value->TOTAL;
            $cantidad = $cantidad + $value->CANTIDAD;
            $total = $total + Common::quitar_coma($value->TOTAL, $candec['CANDEC']);
            $c = $c + 1;
        }

        /*  --------------------------------------------------------------------------------- */

        // CARGAR VARIABLES 
        
        $data['codigo'] = $codigo;
        $data['origen'] = $origen;
        $data['destino'] = $destino;
        $data['envia'] = $envia;
        $data['transporta'] = $transporta;
        $data['recibe'] = $recibe;
        $data['fecha'] = $fecha;
        $data['nombre'] = $nombre;
        $data['articulos'] = $articulos;
        $data['c'] = $c;
        $data['cantidad'] = $cantidad;
        $data['total'] = Common::precio_candec($total, $moneda);
        $data['sucursal'] = $nombre_sucursal;
        $data['direccion'] = $direccion;

        /*  --------------------------------------------------------------------------------- */
        
        $html = view('pdf.rptTransferencia',$data)->render();
        
        /*  --------------------------------------------------------------------------------- */

        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
 
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf([
            'fontDir' => array_merge($fontDirs, [
                public_path() . '/fonts',
            ]),
            'fontdata' => $fontData + [
                'arial' => [
                    'R' => 'arial.ttf',
                    'B' => 'arialbd.ttf',
                ],
            ],
            'default_font' => 'arial',
             "format" => "A4",
        ]);
        
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->WriteHTML($html);

        /*  --------------------------------------------------------------------------------- */

        // GENERAR ARCHIVO 

        $mpdf->Output($nombre,"I");
        
        /*  --------------------------------------------------------------------------------- */

    }  
}
