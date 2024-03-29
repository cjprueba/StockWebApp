<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;
use App\SalidaProductoDet;
Use App\Common;
use Illuminate\Support\Facades\Log;
use DateTime;


class SalidaProducto extends Model
{
    //
    protected $connection = 'retail';
    protected $table = 'salida_productos';
    const CREATED_AT = 'FECALTAS';
    const UPDATED_AT = 'FECMODIF';

    public static function salida($data) {

        try {
         DB::connection('retail')->beginTransaction();
            /*  ----------------------------------------------------f----------------------------- */

            // GUARDAR DEVOLUCION PROVEEDOR 

            $salidaProducto = SalidaProducto::guardar([
                'TIPO' => $data['data']['tipo'],
                'OBSERVACION' => $data['data']['observacion'],
                'FK_MONEDA' => $data['data']['moneda'],
                'TOTAL' => Common::quitar_coma($data['data']['total'], 2)
            ]);

            /*  --------------------------------------------------------------------------------- */

            // GUARDAR DEVOLUCION PROVEEDOR DETALLE 

            if ($salidaProducto["response"] === true) {
                SalidaProductoDet::guardar($data['data']['productos'], $salidaProducto["id"]);
            } else {
                return $salidaProducto;
            }

            /*  --------------------------------------------------------------------------------- */
            SalidaProductoTieneAutorizacion::guardar_referencia(["FK_SALIDA_PRODUCTO"=>$salidaProducto["id"],"FK_USER"=>$data['data']['autorizacion']['ID_USUARIO'],"FK_USER_SUPERVISOR"=>$data['data']['autorizacion']['ID_USER_SUPERVISOR']]);
            // RETORNAR VALOR 
            DB::connection('retail')->commit();
            return ["response" => true, "statusText" => "Se ha realizado con éxito la salida"];

            /*  --------------------------------------------------------------------------------- */

        } catch (Exception $e) {
            DB::connection('retail')->rollBack();
            /*  --------------------------------------------------------------------------------- */

            // ERROR 

            Log::error('Salida de producto: Error al guardar.');

            /*  --------------------------------------------------------------------------------- */

            return ["response" => false, "statusText" => "Error al guardar la salida de productos"];

            /*  --------------------------------------------------------------------------------- */
        }

    }
        public static function guardar($data){

    	try {
    	

    		/*  --------------------------------------------------------------------------------- */

    		// OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        	$user = auth()->user();

        	/*  --------------------------------------------------------------------------------- */

    		$dia = date('Y-m-d H:i:s');
    

    		/*  --------------------------------------------------------------------------------- */

	    	$salida = SalidaProducto::insertGetId([
	    		'OBSERVACION' => $data["OBSERVACION"],
	    		'TIPO' => $data["TIPO"],
	    		'FK_MONEDA' => $data["FK_MONEDA"],
	    		'TOTAL' => $data["TOTAL"],
	    		'FECALTAS' => $dia,
	    		'FECMODIF' => $dia,
	    		'FK_USER_CR' => $user->id,
	    		'FK_USER_MD' => $user->id,
	    		'ID_SUCURSAL' => $user->id_sucursal
	    	]);

	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Salida de producto: Éxito al guardar.', ['Salida de producto id:' => $salida]);

	    	/*  --------------------------------------------------------------------------------- */
  			
	    	return ["response" => true, "id" => $salida];

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {
    	

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Salida de productos: Error al guardar.');

			/*  --------------------------------------------------------------------------------- */

			return ["response" => false, "statusText" => "Error al guardar la salida de los productos"];

			/*  --------------------------------------------------------------------------------- */
		}
    }
    public static function salidaMostrar($request) {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'CODIGO', 
                            1 => 'TIPO',
                            2 => 'OBSERVACION',
                            3 => 'TOTAL',
                            4 => 'CREACION',
                            5 => 'ACCION',
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = SalidaProducto::
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

            $posts = SalidaProducto::select(DB::raw('ID AS CODIGO, TIPO, OBSERVACION, TOTAL, FECALTAS,  FK_MONEDA AS MONEDA, ESTADO'))
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

            $posts =  SalidaProducto::select(DB::raw('ID AS CODIGO, TIPO, OBSERVACION, TOTAL, FECALTAS,  FK_MONEDA AS MONEDA, ESTADO'))
                         ->where('ID_SUCURSAL', '=', $user->id_sucursal)
                            ->where(function ($query) use ($search) {
                                $query->where('ID','LIKE',"{$search}%")
                                      ->orWhere('OBSERVACION', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = DevolucionProv::
                         where('ID_SUCURSAL', '=', $user->id_sucursal)
                            ->where(function ($query) use ($search) {
                                $query->where('CODIGO','LIKE',"{$search}%")
                                      ->orWhere('OBSERVACION', 'LIKE',"%{$search}%");
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
                 if($post->TIPO==1){
                 	$nestedData['TIPO'] = 'AVERIO';
                 }else{
                 	if($post->TIPO==2){
                 		$nestedData['TIPO'] = 'VENCIDO';
                 	}else{
                 		if($post->TIPO==3){
                 			$nestedData['TIPO'] = 'ROBADO';
                 		}else{
                          if($post->TIPO==4){
                          	$nestedData['TIPO'] = 'MUESTRA';
                          }else{
                          	if($post->TIPO==5){
                          		$nestedData['TIPO'] = 'EXTRAVIADO';
                          	}else{
                          		if($post->TIPO==6){
                          			$nestedData['TIPO'] = 'REGALO';
                          		}else{
                                    if($post->TIPO==7){
                                        $nestedData['TIPO'] = 'USO INTERNO';
                                    }
                                    
                                }
                          	}
                          }
                 		}
                 	}
                 }

                $nestedData['OBSERVACION'] = $post->OBSERVACION;
                $nestedData['TOTAL'] = Common::precio_candec($post->TOTAL, $post->MONEDA);
                $nestedData['CREACION'] = $post->FECALTAS;

                if ($post->ESTADO === 1) {

                    $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrar' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a>&emsp;<a href='#' id='imprimirReporte' title='Reporte'><i class='fa fa-file text-secondary' aria-hidden='true'></i></a>";
                    $nestedData['ESTATUS'] = 'table-danger';
                } else {

                    $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrar' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a>&emsp;<a href='#' id='imprimirReporte' title='Reporte'><i class='fa fa-file text-secondary' aria-hidden='true'></i></a>&emsp;<a href='#' id='devolverProducto' title='Devolver'><i class='fa fa-arrow-alt-circle-left text-danger' aria-hidden='true'></i></a>";
                    $nestedData['ESTATUS'] = '';
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
    public static function salidaProductoDetalle($request) {

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

        $totalData = SalidaProducto::
                    leftjoin('SALIDA_PRODUCTOS_DET', 'SALIDA_PRODUCTOS_DET.FK_SALIDA_PRODUCTOS', '=', 'SALIDA_PRODUCTOS.ID')
                    ->where([
                        'SALIDA_PRODUCTOS.ID_SUCURSAL' => $user->id_sucursal,
                        'SALIDA_PRODUCTOS.ID' => $codigo
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

            $posts = SalidaProductoDet::select(DB::raw('0 AS ITEM, SALIDA_PRODUCTOS_DET.COD_PROD, PRODUCTOS.DESCRIPCION, SALIDA_PRODUCTOS_DET.CANTIDAD, SALIDA_PRODUCTOS_DET.COSTO, SALIDA_PRODUCTOS_DET.COSTO_TOTAL, LOTES.LOTE'))
                ->leftjoin('SALIDA_PRODUCTOS', 'SALIDA_PRODUCTOS.ID', '=', 'SALIDA_PRODUCTOS_DET.FK_SALIDA_PRODUCTOS')
                ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'SALIDA_PRODUCTOS_DET.COD_PROD')
                ->leftjoin('LOTES', 'LOTES.ID', '=', 'SALIDA_PRODUCTOS_DET.FK_ID_LOTE')
                    ->where([
                        'SALIDA_PRODUCTOS.ID_SUCURSAL' => $user->id_sucursal,
                        'SALIDA_PRODUCTOS.ID' => $codigo
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

            $posts = DevolucionProvDet::select(DB::raw('0 AS ITEM, SALIDA_PRODUCTOS_DET.COD_PROD, PRODUCTOS.DESCRIPCION, SALIDA_PRODUCTOS_DET.CANTIDAD, SALIDA_PRODUCTOS_DET.COSTO, SALIDA_PRODUCTOS_DET.COSTO_TOTAL, LOTES.LOTE'))
                ->leftjoin('SALIDA_PRODUCTOS', 'SALIDA_PRODUCTOS.ID', '=', 'SALIDA_PRODUCTOS_DET.FK_SALIDA_PRODUCTOS')
                ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'SALIDA_PRODUCTOS_DET.COD_PROD')
                ->leftjoin('LOTES', 'LOTES.ID', '=', 'SALIDA_PRODUCTOS_DET.FK_ID_LOTE')
                    ->where([
                        'SALIDA_PRODUCTOS.ID_SUCURSAL' => $user->id_sucursal,
                        'SALIDA_PRODUCTOS.ID' => $codigo
                    ])
                            ->where(function ($query) use ($search) {
                                $query->where('SALIDA_PRODUCTOS_DET.COD_PROD','LIKE',"{$search}%")
                                      ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"{$search}%");
                            })
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = DevolucionProvDet::leftjoin('SALIDA_PRODUCTOS', 'SALIDA_PRODUCTOS.ID', '=', 'SALIDA_PRODUCTOS_DET.FK_SALIDA_PRODUCTOS')
                ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'SALIDA_PRODUCTOS_DET.COD_PROD')
                ->leftjoin('LOTES', 'LOTES.ID', '=', 'SALIDA_PRODUCTOS_DET.FK_ID_LOTE')
                    ->where([
                        'SALIDA_PRODUCTOS.ID_SUCURSAL' => $user->id_sucursal,
                        'SALIDA_PRODUCTOS.ID' => $codigo
                    ])
                            ->where(function ($query) use ($search) {
                                $query->where('SALIDA_PRODUCTOS_DET.COD_PROD','LIKE',"{$search}%")
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

    public static function mostrar_tipo($TIPO){

        $tipo = '';

        if($TIPO==1){
            $tipo = 'AVERIO';
        }else if($TIPO==2){
            $tipo = 'VENCIDO';
        }else if($TIPO==3){
            $tipo = 'ROBADO';
        }else if($TIPO==4){
            $tipo = 'MUESTRA';
        }else if($TIPO==5){
            $tipo = 'EXTRAVIADO';
        }else if($TIPO==6){
            $tipo = 'REGALO';                   
        }
         
        return ['TIPO' => $tipo];
                    
    }
    public static function mostrar_cabecera($codigo)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */
        
        $salida = SalidaProducto::select(DB::raw(
                        'ID,
                        OBSERVACION,
                        FECALTAS,
                        TIPO'
                    ))
        ->where('ID','=', $codigo)
        ->where('ID_SUCURSAL','=', $user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        $tipo = SalidaProducto::mostrar_tipo($salida[0]->TIPO);
        $salida[0]->TIPO = $tipo['TIPO'];

        return $salida[0];

        /*  --------------------------------------------------------------------------------- */

    }

    public static function mostrar_cuerpo($codigo)
    {
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        $salida_det = SalidaProducto::select(DB::raw(
                        'salida_productos_det.ID,
                        salida_productos_det.COD_PROD, 
                        PRODUCTOS.DESCRIPCION,
                        salida_productos_det.CANTIDAD,
                        salida_productos_det.COSTO,
                        0 AS COSTO_TOTAL,
                        salida_productos_det.FK_ID_LOTE'
                    ))
        ->leftjoin('SALIDA_PRODUCTOS_DET', 'SALIDA_PRODUCTOS_DET.FK_SALIDA_PRODUCTOS', '=', 'SALIDA_PRODUCTOS.ID')
        ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'salida_productos_det.COD_PROD')
        ->where('salida_productos.ID_SUCURSAL','=', $user->id_sucursal)
        ->where('salida_productos_det.FK_SALIDA_PRODUCTOS','=', $codigo)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        foreach ($salida_det as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            $salida_det[$key]->COD_PROD = $value->COD_PROD;
            $salida_det[$key]->DESCRIPCION = $value->DESCRIPCION;
            $salida_det[$key]->CANTIDAD = $value->CANTIDAD;
            $salida_det[$key]->COSTO = $value->COSTO;
            $salida_det[$key]->COSTO_TOTAL = $value->COSTO * $value->CANTIDAD;

            /*  --------------------------------------------------------------------------------- */

        }

        return $salida_det;

        /*  --------------------------------------------------------------------------------- */

    }

    public static function reporte($dato)
    {
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
        ini_set("pcre.backtrack_limit", "10000000"); 

        /*  --------------------------------------------------------------------------------- */

        // OBTENER DATOS DE CABECERA 

        $salida = SalidaProducto::mostrar_cabecera($dato['id']);

        /*  --------------------------------------------------------------------------------- */

        // OBTENER DATOS DETALLE 

        $salida_det = SalidaProducto::mostrar_cuerpo($dato['id']);
        
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $c = 0;
        $codigo = $salida->ID;
        $observacion = $salida->OBSERVACION;
        $fecaltas = $salida->FECALTAS;
        $tipo = $salida->TIPO;

        /*  --------------------------------------------------------------------------------- */

        $nombre = 'Salida_'.$codigo.'_'.time().'';
        $articulos = [];
        $cantidad = 0;
        $total_salida = 0;
        $total = 0;

        /*  --------------------------------------------------------------------------------- */
        
        // CARGAR DETALLE DE TRANSFERENCIA DET 

        foreach ($salida_det as $key => $value) {

            

                $articulos[$c]["cod_prod"] = $value->COD_PROD;
                $articulos[$c]["descripcion"] = $value->DESCRIPCION;
                $articulos[$c]["cantidad"] = $value->CANTIDAD;
                $articulos[$c]["costo"] = $value->COSTO;
                $articulos[$c]["costo_total"] = $value->COSTO_TOTAL * $value->CANTIDAD;
                $total += $value->COSTO;
                $cantidad = $cantidad + $value->CANTIDAD;
                $total_salida = $total_salida + $articulos[$c]["costo_total"];
                $c = $c + 1;

            
        
           
        }

        /*  --------------------------------------------------------------------------------- */

        // CARGAR VARIABLES 
        
        $data['codigo'] = $codigo;
        $data['nombre'] = $nombre;
        $data['observacion'] = $observacion;
        $data['fecaltas'] = $fecaltas;
        $data['tipo'] = $tipo;
        $data['articulos'] = $articulos;
        $data['c'] = $c;
        $data['cantidad'] = $cantidad;
        $data['total_salida'] = $total_salida;
        $data['total'] = $total;
        $data['nombre_sucursal'] = '';
        $data['logo']=(Imagen::obtenerLogoDireccion())['imagen'];
        

        /*  --------------------------------------------------------------------------------- */
        
        $html = view('pdf.rptSalidaProducto',$data)->render();
        
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

    public static function devolver($data){

        try {
            
            /*  --------------------------------------------------------------------------------- */

            // INICIAR TRANSACCION 

            DB::connection('retail')->beginTransaction();

            /*  --------------------------------------------------------------------------------- */

            // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

            $user = auth()->user();

            /*  --------------------------------------------------------------------------------- */

            // OBTENER DATOS DETALLE 
            
            $salida_det = SalidaProducto::mostrar_cuerpo($data['codigo']['id']);
            
            /*  --------------------------------------------------------------------------------- */

            // CARGAR DETALLE DE TRANSFERENCIA DET 

            foreach ($salida_det as $key => $value) {

                /*  --------------------------------------------------------------------------------- */

                // DEVOLVER STOCK 

                Stock::sumar_stock_id_lote($value->FK_ID_LOTE, $value->CANTIDAD);

                /*  --------------------------------------------------------------------------------- */

            }

            /*  --------------------------------------------------------------------------------- */

            SalidaProducto::where('ID','=', $data['codigo']['id'])
            ->where('ID_SUCURSAL','=', $user->id_sucursal)
            ->update(['ESTADO' => 1]);

            DevolverProductoTieneAutorizacion::guardar_referencia(["FK_SALIDA_PRODUCTO"=>$data['codigo']['id'],"FK_USER"=>$data['codigo']['autorizacion']['ID_USUARIO'],"FK_USER_SUPERVISOR"=>$data['codigo']['autorizacion']['ID_USER_SUPERVISOR']]);

            /*  --------------------------------------------------------------------------------- */

            // RESPONSE

            

            /*  --------------------------------------------------------------------------------- */

            // ENVIAR TRANSACCION A BD

            DB::connection('retail')->commit();

            /*  --------------------------------------------------------------------------------- */

            return ["response" => true, "statusText" => "Se han devuelto los productos"];

            /*  --------------------------------------------------------------------------------- */

        } catch (Exception $e) {
            
            /*  --------------------------------------------------------------------------------- */

           // NO GUARDAR NINGUN CAMBIO 

           DB::connection('retail')->rollBack();
           throw $e;
           
           /*  --------------------------------------------------------------------------------- */

        }
    }
        public static function generarReporteSalidaProductos($datos) 
    {

        
         /*  --------------------------------------------------------------------------------- */

         // INCICIAR VARIABLES 
            $inicio = date('Y-m-d', strtotime($datos["data"]['Inicio']));
            $final = date('Y-m-d', strtotime($datos["data"]['Final']));
            $sucursal = $datos["data"]['Sucursal'];





   
                                 //TOTALES POR CATEGORIA AGRUPADOS POR MARCA
                                 /*  --------------------------------------------------------------------------------- */
                              
                   
                            $totales=SalidaProducto::Select(DB::raw("IF(SALIDA_PRODUCTOS.TIPO = 1,
                                    'AVERIO',
                                    IF(SALIDA_PRODUCTOS.TIPO = 2,
                                        'VENCIDOS',
                                        IF(SALIDA_PRODUCTOS.TIPO = 3,
                                            'ROBADO',
                                            IF(SALIDA_PRODUCTOS.TIPO = 4,
                                                'MUESTRA',
                                                IF(SALIDA_PRODUCTOS.TIPO = 5,
                                                    'EXTRAVIADO',
                                                    IF(SALIDA_PRODUCTOS.TIPO = 6,
                                                        'REGALO',
                                                        IF(SALIDA_PRODUCTOS.TIPO = 7,
                                                            'USO INTERNO',
                                                            'INDEFINIDO'))))))) AS TOTALES,
                                       SALIDA_PRODUCTOS.TIPO AS TIPO,
                                      SUM(SALIDA_PRODUCTOS_DET.CANTIDAD) AS SALIDA_PIEZAS,
                                      SUM(SALIDA_PRODUCTOS_DET.COSTO_TOTAL) AS COSTO_TOTAL"))
                                    ->LEFTJOIN('SALIDA_PRODUCTOS_DET','SALIDA_PRODUCTOS_DET.FK_SALIDA_PRODUCTOS','=','SALIDA_PRODUCTOS.ID')
                                    ->WHERE('SALIDA_PRODUCTOS.ID_SUCURSAL','=',$sucursal)
                                    ->WHERE('SALIDA_PRODUCTOS.ESTADO','=',0)
                                    ->whereBetween('SALIDA_PRODUCTOS.FECALTAS',[$inicio,$final]);
                                    if(!$datos["data"]["AllTipos"]){
                                        $totales->whereIn('SALIDA_PRODUCTOS.TIPO',$datos["data"]["Tipos"]);
                                    }
                                    $totales=$totales->GROUPBY('SALIDA_PRODUCTOS.TIPO')->get()->toArray();

                   /*  --------------------------------------------------------------------------------- */  
                   //TRAER TODOS LOS PRODUCTOS CON EL CODIGO DE TIPO
                   /*  --------------------------------------------------------------------------------- */
                   
                  $productos=SalidaProducto::Select(DB::raw("SALIDA_PRODUCTOS_DET.COD_PROD AS COD_PROD,
                                LOTES.LOTE AS LOTE,
                                PRODUCTOS.DESCRIPCION AS DESCRIPCION,
                                IFNULL(LINEAS.DESCRIPCION,'INDEFINIDO') AS CATEGORIA,
                                IFNULL(SUBLINEAS.DESCRIPCION,'INDEFINIDO') AS SUBCATEGORIA,
                                IFNULL(SUBLINEA_DET.DESCRIPCION,'INDEFINIDO') AS NOMBRE,
                                IFNULL(UPPER(SALIDA_PRODUCTOS.OBSERVACION),'NO POSEE' ) AS COMENTARIO,
                                SUM(SALIDA_PRODUCTOS_DET.CANTIDAD) AS CANTIDAD,
                                LOTES.COSTO AS COSTO_UNITARIO,
                                SUM(SALIDA_PRODUCTOS_DET.COSTO_TOTAL) AS COSTO_TOTAL,
                                SALIDA_PRODUCTOS.TIPO AS TIPO"))
                            ->LEFTJOIN('SALIDA_PRODUCTOS_DET','SALIDA_PRODUCTOS_DET.FK_SALIDA_PRODUCTOS','=','SALIDA_PRODUCTOS.ID')
                            ->LEFTJOIN('LOTES','LOTES.ID','=','SALIDA_PRODUCTOS_DET.FK_ID_LOTE')
                            ->LEFTJOIN('PRODUCTOS','PRODUCTOS.CODIGO','=','LOTES.COD_PROD')
                            ->LEFTJOIN('LINEAS','LINEAS.CODIGO','=','PRODUCTOS.LINEA')
                            ->LEFTJOIN('SUBLINEAS','SUBLINEAS.CODIGO','=','PRODUCTOS.LINEA')
                            ->LEFTJOIN('SUBLINEA_DET','SUBLINEA_DET.CODIGO','=','PRODUCTOS.SUBLINEADET')
                            ->WHERE('SALIDA_PRODUCTOS.ID_SUCURSAL','=',$sucursal)
                            ->WHERE('SALIDA_PRODUCTOS.ESTADO','=',0)->whereBetween('SALIDA_PRODUCTOS.FECALTAS',[$inicio,$final]);
                             if(!$datos["data"]["AllTipos"]){
                                        $productos->whereIn('SALIDA_PRODUCTOS.TIPO',$datos["data"]["Tipos"]);
                                    }
                           $productos=$productos->GROUPBY('SALIDA_PRODUCTOS_DET.COD_PROD',"SALIDA_PRODUCTOS_DET.FK_ID_LOTE")
                            ->ORDERBY('SALIDA_PRODUCTOS.OBSERVACION')
                            ->get()
                            ->toArray();


                  



                 
 
            /*  --------------------------------------------------------------------------------- */


            /*  --------------------------------------------------------------------------------- */



            /*  --------------------------------------------------------------------------------- */

            // RETORNAR TODOS LOS ARRAYS


            return ['salidaProductos' => $productos, 'salidas' => $totales];

            /*  --------------------------------------------------------------------------------- */
    }

}
