<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Stock;
use App\Producto;
use App\ComprasDet;
use App\Common;
use App\Parametro;

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
                    $nestedData['ESTATUS'] = 'Pendiente';
                } else if ($post->ESTATUS === 1) {
                    $nestedData['ESTATUS'] = 'Enviado';
                } else {
                    $nestedData['ESTATUS'] = 'Procesado';
                }
                
                $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarTransferencia' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a>&emsp;<a href='#' id='enviarTransferencia' title='Enviar'><i class='fa fa-paper-plane text-success'  aria-hidden='true'></i></a> &emsp;<a href='#' id='editarTransferencia' title='Editar'><i class='fa fa-edit text-warning' aria-hidden='true'></i></a>&emsp;<a href='#' id='eliminarTransferencia' title='Eliminar'><i class='fa fa-trash text-danger' aria-hidden='true'></i></a>
                    &emsp;<a href='#' id='imprimirTransferencia' title='Imprimir'><i class='fa fa-print text-primary' aria-hidden='true'></i></a>";

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
        
        $dia = date("Y-m-d");
        $hora = date("H:i:s");

        $c = 0;
        $filas = $datos["data"]["length"];
        $cod_prod = '';
        $cantidad = 0;
        $cantidad_total = 0;
        $precio = 0;
        $iva = 0;
        $base5 = 0;
        $base10 = 0;
        $exentas = 0;
        $total = 0;
        $sin_stock = [];
        $respuesta_FK_CD = [];
        $cantidad_FK_CD = 1;
        $todos_guardados = true;

        $tr_gravada = 0;
        $tr_iva = 0;
        $tr_exenta = 0;
        $tr_base5 = 0;
        $tr_base10 = 0;

        $tr_dt_gravada = 0;
        $tr_dt_iva = 0;
        $tr_dt_exenta = 0;
        $tr_dt_base5 = 0;
        $tr_dt_base10 = 0;
        $tr_dt_total = 0;

        $total_total = 0;
        $total_iva = 0;
        $total_subtotal = 0;

        $lote = 0;

        /*  --------------------------------------------------------------------------------- */
        
        // PARAMETRO 
        
        $parametro = Parametro::mostrarParametro();

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

            $transferencias_det = DB::connection('retail')
            ->table('transferencias')
            ->insert(
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

                $cantidad_FK_CD = 1;
                $cod_prod = $datos["data"][$c]["CODIGO"];
                $cantidad = Common::quitar_coma($datos["data"][$c]["CANTIDAD"], $parametro['parametros'][0]->CANDEC);
                $cantidad_total = $cantidad;

                /*  --------------------------------------------------------------------------------- */

                // COMPROBAR SI EXISTE STOCK 

                if (Stock::comprobar_stock_producto($cod_prod, $cantidad) === true){

                    /*  --------------------------------------------------------------------------------- */

                    $iva = Common::quitar_coma($datos["data"][$c]["IVA"], $parametro['parametros'][0]->CANDEC);

                    /*  --------------------------------------------------------------------------------- */

                    // REALIZAR CALCULOS 

                    if ($datos["data"][$c]["IVA_PORCENTAJE"] === 5) {
                        $base5 = Common::quitar_coma($datos["data"][$c]["IVA"], $parametro['parametros'][0]->CANDEC);
                    } else if ($datos["data"][$c]["IVA_PORCENTAJE"] === 10) {
                        $base10 = Common::quitar_coma($datos["data"][$c]["IVA"], $parametro['parametros'][0]->CANDEC);
                    } else {
                        $exentas = Common::quitar_coma($datos["data"][$c]["TOTAL"], $parametro['parametros'][0]->CANDEC);
                    }

                    $precio = Common::quitar_coma($datos["data"][$c]["PRECIO"], $parametro['parametros'][0]->CANDEC);
                    
                    $gravadas = Common::quitar_coma($datos["data"][$c]["TOTAL"], $parametro['parametros'][0]->CANDEC) - Common::quitar_coma($datos["data"][$c]["IVA"], $parametro['parametros'][0]->CANDEC);
                    $total = Common::quitar_coma($datos["data"][$c]["TOTAL"], $parametro['parametros'][0]->CANDEC); 

                    $tr_gravada = Common::quitar_coma(($gravadas/$cantidad), $parametro['parametros'][0]->CANDEC);
                    $tr_iva = Common::quitar_coma(($iva/$cantidad), $parametro['parametros'][0]->CANDEC);
                    $tr_exenta = Common::quitar_coma(($exentas/$cantidad), $parametro['parametros'][0]->CANDEC);
                    $tr_base5 = Common::quitar_coma(($base5/$cantidad), $parametro['parametros'][0]->CANDEC);
                    $tr_base10 = Common::quitar_coma(($base10/$cantidad), $parametro['parametros'][0]->CANDEC);    
                    $tr_dt_total = Common::quitar_coma(($total/$cantidad), $parametro['parametros'][0]->CANDEC); 

                    /*  --------------------------------------------------------------------------------- */

                    // TOTALES 

                    $total_total = $total_total + $total;
                    $total_iva = $total_iva + $iva;

                    /*  --------------------------------------------------------------------------------- */

                    // OBTENER DATOS FALTANTES 

                    $producto = Producto::datosVariosProducto($cod_prod);

                    /*  --------------------------------------------------------------------------------- */ 

                    // RECORRER HASTA TERMINAR 

                    while ($cantidad_FK_CD > 0) {
                        
                        /*  --------------------------------------------------------------------------------- */

                        // RESTAR STOCK DEL PRODUCTO

                        $respuesta_resta = Stock::restar_stock_producto_FK_CD($cod_prod, $cantidad);
                        $cantidad_FK_CD = $respuesta_resta['cantidad'];
                        $cantidad = $cantidad - $cantidad_FK_CD;
                        $lote = $respuesta_resta['lote'];

                        /*  --------------------------------------------------------------------------------- */

                        // CALCULAR POR DETALLE 

                        $tr_dt_gravada = $tr_gravada * $cantidad;
                        $tr_dt_iva = $tr_iva * $cantidad;
                        $tr_dt_exenta = $tr_exenta * $cantidad;
                        $tr_dt_base5 = $tr_base5 * $cantidad;
                        $tr_dt_base10 = $tr_base10 * $cantidad;

                        if ($cantidad_FK_CD === 0) {
                            $tr_dt_gravada = $gravadas - (($cantidad_total - $cantidad) * $tr_gravada);
                            $tr_dt_iva = $iva - (($cantidad_total - $cantidad) * $tr_iva);
                            $tr_dt_exenta = $exentas - (($cantidad_total - $cantidad) * $tr_exenta);
                            $tr_dt_base5 = $base5 - (($cantidad_total - $cantidad) * $tr_base5);
                            $tr_dt_base10 = $base10 - (($cantidad_total - $cantidad) * $tr_base10);
                        }

                        /*  --------------------------------------------------------------------------------- */

                        // OBTENER FK_CD

                        $fk_cd = ComprasDet::id_cd($cod_prod, $lote);

                        /*  --------------------------------------------------------------------------------- */

                        // INSERTAR TRANSFERENCIA DET 

                        $transferencias_det = DB::connection('retail')
                        ->table('transferencias_det')
                        ->insert(
                            [
                            'CODIGO' => $codigo, 
                            'ITEM' => $c + 1,
                            'CODIGO_PROD' => $cod_prod,
                            'CODIGO_INTERNO' =>  $producto['producto'][0]->CODIGO_INTERNO,
                            'LOTE' => $lote,
                            'DESCRIPCION' => $datos["data"][$c]["DESCRIPCION"],
                            'TIPO' => $datos["data"][$c]["ITEM"],
                            'CANTIDAD' => $cantidad,
                            'PRECIO' => Common::quitar_coma($datos["data"][$c]["PRECIO"], $parametro['parametros'][0]->CANDEC),
                            'EXENTAS' => $tr_exenta,
                            'GRABADAS' => $tr_gravada,
                            'IVA' => $tr_dt_iva,
                            'TOTAL' => $tr_dt_total,
                            'DESCUENTO' => 0,
                            'BASE5' => $tr_dt_base5,
                            'BASE10' => $tr_dt_base10,
                            'DEVUELTO' => 'NO',
                            'USERALTAS' => $user->name,
                            'FECALTAS' =>  $dia,
                            'HORALTAS' =>  $hora,
                            'ID_SUCURSAL' => $user->id_sucursal,
                            'FK_CD' => $fk_cd
                            ]
                        );

                        /*  --------------------------------------------------------------------------------- */

                    }

                } else {

                    /*  --------------------------------------------------------------------------------- */ 

                    // SI NO HAY STOCK SE GUARDDARA EN ESTE ARRAY EL CODIGO Y SE ACTIVARA LA VARIABLE TODOS GUARDADOS

                    $sin_stock[] = $cod_prod;
                    $todos_guardados = false;

                    /*  --------------------------------------------------------------------------------- */ 

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

        return true; 
        
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

        // INICIAR VARIABLES

        $codigo = $data['codigo'];

        /*  --------------------------------------------------------------------------------- */

        $transferencia = DB::connection('retail')
        ->table('transferencias')
        ->select(DB::raw(
                        'TRANSFERENCIAS.CODIGO, 
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
                        TRANSFERENCIAS.ESTATUS'
                    ))
        ->leftjoin('SUCURSALES AS ORIGEN', 'ORIGEN.CODIGO', '=', 'TRANSFERENCIAS.SUCURSAL_ORIGEN')
        ->leftjoin('SUCURSALES AS DESTINO', 'DESTINO.CODIGO', '=', 'TRANSFERENCIAS.SUCURSAL_DESTINO')
        ->leftjoin('EMPLEADOS AS ENVIA', 'ENVIA.CODIGO', '=', 'TRANSFERENCIAS.ENVIA')
        ->leftjoin('EMPLEADOS AS TRANSPORTA', 'TRANSPORTA.CODIGO', '=', 'TRANSFERENCIAS.TRANSPORTA')
        ->leftjoin('EMPLEADOS AS RECIBE', 'RECIBE.CODIGO', '=', 'TRANSFERENCIAS.RECIBE')
        ->where('TRANSFERENCIAS.ID_SUCURSAL','=', $user->id_sucursal)
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
                        TRANSFERENCIAS_DET.TOTAL,
                        TRANSFERENCIAS.MONEDA,
                        0 AS IVA_PORCENTAJE'
                    ),
                 DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = TRANSFERENCIAS_DET.CODIGO_PROD) AND (l.ID_SUCURSAL = TRANSFERENCIAS_DET.ID_SUCURSAL))),0) AS STOCK'))
        ->leftJoin('TRANSFERENCIAS', function($join){
                                $join->on('TRANSFERENCIAS.CODIGO', '=', 'TRANSFERENCIAS_DET.CODIGO')
                                     ->on('TRANSFERENCIAS.ID_SUCURSAL', '=', 'TRANSFERENCIAS_DET.ID_SUCURSAL');
                            })
        ->where('TRANSFERENCIAS_DET.ID_SUCURSAL','=', $user->id_sucursal)
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

        /*  --------------------------------------------------------------------------------- */

        // VERIFICAR SI EXISTE
        
        if (Transferencia::verificar_existencia($codigo) === false) {
            return false;
        }

        /*  --------------------------------------------------------------------------------- */

        // VERIFICAR ESTATUS
        
        $estatus = Transferencia::verificar_estatus($codigo, $user->id_sucursal);

        if ($estatus === false) {
            return false;
        }

        /*  --------------------------------------------------------------------------------- */

        // VERIFICAR SI LA TRANSFERENCIA YA SE PROCESO 

        if ($estatus === 1 or $estatus === 2) {
            return false;
        }

        /*  --------------------------------------------------------------------------------- */

        // OBTENER EL CODIGO Y LA CANTIDA DEL PRODUCTO 

        $transferencias_det = DB::connection('retail')
        ->table('transferencias_det')
        ->select(DB::raw(
                        'CODIGO_PROD, 
                        CANTIDAD,
                        LOTE'
                    ))
        ->where('ID_SUCURSAL','=', $user->id_sucursal)
        ->where('CODIGO','=', $codigo)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RECORRER FILAS DE TRANSFERENCIA DET 

        foreach ($transferencias_det as $key => $value) {
            
            /*  --------------------------------------------------------------------------------- */

            // DEVOLVER STOCK 

            Stock::sumar_stock_producto($value->CODIGO_PROD, $value->CANTIDAD, $value->LOTE);

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

           $transferencias = DB::connection('retail')
           ->table('transferencias')
           ->where('ID_SUCURSAL','=', $user->id_sucursal)
           ->where('CODIGO','=', $codigo)
           ->delete();
 
        }
        
        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return true;

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
         
        if ($eliminar === false) {
            return false;
        }

        /*  --------------------------------------------------------------------------------- */

        // MODIFICAR TRANSFERENCIA 

        Transferencia::guardar_modificar($data, 2);

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return true;

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
                    $nestedData['ESTATUS'] = 'Pendiente';
                } else if ($post->ESTATUS === 2) {
                    $nestedData['ESTATUS'] = 'Procesado';
                }
                
                $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarTransferencia' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a> &emsp;<a href='#' id='importarTransferencia' title='Importar'><i class='fa fa-check text-success' aria-hidden='true'></i></a>&emsp;<a href='#' id='rechazarTransferencia' title='Cancelar'><i class='fa fa-times text-danger' aria-hidden='true'></i></a>
                    &emsp;<a href='#' id='imprimirTransferencia' title='Imprimir'><i class='fa fa-print text-primary' aria-hidden='true'></i></a>";

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
        ->table('transferencias_det')
        ->select(DB::raw(
                        'CODIGO_PROD, 
                        CANTIDAD, 
                        PRECIO'
                    ))
        ->where('ID_SUCURSAL','=', $codigo_origen)
        ->where('CODIGO','=', $codigo)
        ->get();
        
        /*  --------------------------------------------------------------------------------- */

        // RECORRER TODOS LOS PRODUCTOS 

        foreach ($transferencia_det as $td) {
            
            /*  --------------------------------------------------------------------------------- */

            // REVISAR SI EXISTE PRODUCTO 

            if (Producto::existeProducto($td->CODIGO_PROD, $user->id_sucursal) !== true) {

                /*  --------------------------------------------------------------------------------- */

                // OBTENER DATOS DEL PRODUCTO 

                $producto = DB::connection('retail')
                ->table('PRODUCTOS_AUX')
                ->where('CODIGO', '=', $td->CODIGO_PROD)
                ->where('ID_SUCURSAL', '=', $codigo_origen)
                ->select(DB::raw('CODIGO_INTERNO, BAJA, PREC_VENTA, PREMAYORISTA, PREVIP, DESCUENTO, STOCK_MIN, PROVEEDOR, OBSERVACION, PORCENTAJE, CODIGO_REAL'))
                ->get();

                /*  --------------------------------------------------------------------------------- */

                // REVISAR SI CONVERSION ES VERDADERO PARA COTIZAR
                
                if ($conversion === true) {

                    $precio_venta = $td->PRECIO * $cambio;
                    $precio_mayorista = $producto[0]->PREMAYORISTA * $cambio;
                    $precio_vip = $producto[0]->PREVIP * $cambio;

                } else {

                    $precio_venta = $td->PRECIO;
                    $precio_mayorista = $producto[0]->PREMAYORISTA;
                    $precio_vip = $producto[0]->PREVIP;

                }

                /*  --------------------------------------------------------------------------------- */

                // INSERTAR PRODUCTO 

                $transferencias_det = DB::connection('retail')
                ->table('PRODUCTOS_AUX')
                ->insert(
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

            $lote = Stock::insetar_lote($td->CODIGO_PROD, $td->CANTIDAD, $precio_venta, 2, $usere);

            /*  --------------------------------------------------------------------------------- */

            // MODIFICAR TRANSFERENCIA DET 

            Transferencia::agregar_lote_tranferencia_det($td->CODIGO_PROD, $codigo_origen, $lote);

            /*  --------------------------------------------------------------------------------- */

        }

        /*  --------------------------------------------------------------------------------- */
        
        return ["response" => true];

        /*  --------------------------------------------------------------------------------- */

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
}
