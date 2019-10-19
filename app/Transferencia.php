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

            $posts = Transferencia::select(DB::raw('TRANSFERENCIAS.CODIGO, ORIGEN.DESCRIPCION AS ORIGEN, DESTINO.DESCRIPCION AS DESTINO, TRANSFERENCIAS.NRO_CAJA, TRANSFERENCIAS.IVA, TRANSFERENCIAS.TOTAL, TRANSFERENCIAS.ESTATUS'))
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

            $posts =  Transferencia::select(DB::raw('TRANSFERENCIAS.CODIGO, ORIGEN.DESCRIPCION AS ORIGEN, DESTINO.DESCRIPCION AS DESTINO, TRANSFERENCIAS.NRO_CAJA, TRANSFERENCIAS.IVA, TRANSFERENCIAS.TOTAL, TRANSFERENCIAS.ESTATUS'))
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
                $nestedData['IVA'] = $post->IVA;
                $nestedData['TOTAL'] = $post->TOTAL;

                if ($post->ESTATUS === 0) {
                    $nestedData['ESTATUS'] = 'Pendiente';
                } else if ($post->ESTATUS === 1) {
                    $nestedData['ESTATUS'] = 'Enviado';
                } else {
                    $nestedData['ESTATUS'] = 'Procesado';
                }
                
                $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarProductoFila' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a> &emsp;<a href='#' id='editarTransferencia' title='Editar'><i class='fa fa-edit text-warning' aria-hidden='true'></i></a>&emsp;<a href='#' id='eliminarTransferencia' title='Eliminar'><i class='fa fa-trash text-danger' aria-hidden='true'></i></a>
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
                            'ITEM' => $datos["data"][$c]["ITEM"],
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
        ->table('transferencias_det')
        ->select(DB::raw(
                        'ITEM, 
                        CODIGO_PROD, 
                        DESCRIPCION, 
                        CANTIDAD, 
                        PRECIO,
                        IVA,
                        TOTAL'
                    ))
        ->where('ID_SUCURSAL','=', $user->id_sucursal)
        ->where('CODIGO','=', $codigo)
        ->get();

        /*  --------------------------------------------------------------------------------- */

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

    public static function verificar_estatus($codigo) {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // VERIFICAR SI EXISTE 

        $transferencia = DB::connection('retail')
        ->table('transferencias')
        ->select(DB::raw(
                        'ESTATUS'
                    ))
        ->where('ID_SUCURSAL','=', $user->id_sucursal)
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
        
        $estatus = Transferencia::verificar_estatus($codigo);

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
}
