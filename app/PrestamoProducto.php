<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;
use App\PrestamoProductoDet;
use App\Cliente;
Use App\Common;
use Illuminate\Support\Facades\Log;
use App\Parametro;

class PrestamoProducto extends Model
{
    //
     protected $connection = 'retail';
    protected $table = 'prestamo_productos';
    const CREATED_AT = 'FECALTAS';
    const UPDATED_AT = 'FECMODIF';

    public static function prestar($data) {
    		/*var_dump($cliente);
    		return;*/
        try {  
        	  DB::connection('retail')->beginTransaction();
            // GUARDAR DEVOLUCION PROVEEDOR 
            $cliente=Cliente::id_cliente($data['data']['codigoCliente'])['ID_CLIENTE'];

            $parametro=Parametro::mostrarParametro()["parametros"];
            

            $PrestamoProducto = PrestamoProducto::guardar([
                'OBSERVACION' => $data['data']['observacion'],
                'FK_MONEDA' => $parametro[0]->MONEDA,
                'GARANTIA' => $data['data']['tipo'],
                'CLIENTE' => $cliente,
            ]);

            /*  --------------------------------------------------------------------------------- */

            // GUARDAR DEVOLUCION PROVEEDOR DETALLE 

            if ($PrestamoProducto["response"] === true) {
                PrestamoProductoDet::guardar($data['data']['productos'], $PrestamoProducto["id"]);
            } else {
                return $PrestamoProducto;
            }

            /*  --------------------------------------------------------------------------------- */

            // RETORNAR VALOR 
            DB::connection('retail')->commit();
            return ["response" => true, "statusText" => "Se ha realizado con éxito el prestamo"];

            /*  --------------------------------------------------------------------------------- */

        } catch (Exception $e) {
            DB::connection('retail')->rollBack();
            /*  --------------------------------------------------------------------------------- */

            // ERROR 

            Log::error('Prestamo de producto: Error al guardar.');

            /*  --------------------------------------------------------------------------------- */

            return ["response" => false, "statusText" => "Error al guardar el prestamo de productos"];

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

	    	$prestamo = PrestamoProducto::insertGetId([
	    		'OBSERVACION' => $data["OBSERVACION"],
	    		'FK_MONEDA' => $data["FK_MONEDA"],
	    		'GARANTIA' => $data["GARANTIA"],
	    		'CLIENTE' => $data["CLIENTE"],
	    		'FECALTAS' => $dia,
	    		'FECMODIF' => $dia,
	    		'FK_USER_CR' => $user->id,
	    		'FK_USER_MD' => $user->id,
	    		'ID_SUCURSAL' => $user->id_sucursal
	    	]);

	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Prestamo de  producto: Éxito al guardar.', ['Prestamo de producto id:' => $prestamo]);

	    	/*  --------------------------------------------------------------------------------- */
  			
	    	return ["response" => true, "id" => $prestamo];

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {
    	

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Prestamo de productos: Error al guardar.');

			/*  --------------------------------------------------------------------------------- */

			return ["response" => false, "statusText" => "Error al guardar el prestamo de los productos"];

			/*  --------------------------------------------------------------------------------- */
		}
    }
    public static function prestamoMostrar($request) {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'CODIGO', 
                            1 => 'OBSERVACION',
                            2 => 'CLIENTE', 
                            3 => 'GARANTIA',
                            4 => 'ESTADO',
                            5 => 'CREACION',
                            6 => 'ACCION',
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = PrestamoProducto::
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

            $posts = PrestamoProducto::select(DB::raw('prestamo_productos.ID AS CODIGO, prestamo_productos.OBSERVACION,CLIENTES.NOMBRE AS CLIENTE ,prestamo_productos.GARANTIA, prestamo_productos.ESTADO, prestamo_productos.FECALTAS,  prestamo_productos.FK_MONEDA AS MONEDA'))
            ->leftjoin('CLIENTES','CLIENTES.ID','=','prestamo_productos.CLIENTE')
                         ->where('prestamo_productos.ID_SUCURSAL', '=', $user->id_sucursal)
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

            $posts =  PrestamoProducto::select(DB::raw('prestamo_productos.ID AS CODIGO,CLIENTES.NOMBRE AS CLIENTE ,prestamo_productos.GARANTIA, prestamo_productos.OBSERVACION, prestamo_productos.TOTAL, prestamo_productos.FECALTAS,  prestamo_productos.FK_MONEDA AS MONEDA, prestamo_productos.ESTADO'))
            ->leftjoin('CLIENTES','CLIENTES.ID','=','prestamo_productos.CLIENTE')
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

            $totalFiltered = PrestamoProducto::leftjoin('CLIENTES','CLIENTES.ID','=','prestamo_productos.CLIENTE')
                         ->where('prestamo_productos.ID_SUCURSAL', '=', $user->id_sucursal)

                            ->where(function ($query) use ($search) {
                                $query->Where('CLIENTES.NOMBRE', 'LIKE',"%{$search}%")
                                      ->orWhere('prestamo_productos.OBSERVACION', 'LIKE',"%{$search}%")
                                      ->orWhere('prestamo_productos.ID','LIKE',"{$search}%");
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
                $nestedData['OBSERVACION'] = $post->OBSERVACION;
                $nestedData['CLIENTE'] =ucwords(utf8_encode($post->CLIENTE)); 
                 if($post->GARANTIA==1){
                 	$nestedData['GARANTIA'] = 'OBJETO';
                 }else{
                 	if($post->GARANTIA==2){
                 		$nestedData['GARANTIA'] = 'EFECTIVO';
                 	}
                 }
                 
               
                if($post->ESTADO==0){
                 	$nestedData['ESTADO'] = '<span class="badge badge-secondary">Prestado</span>';
                 }else{
                 	if($post->ESTADO==1){
                 		$nestedData['ESTADO'] = '<span class="badge badge-success">Devuelto</span>';
                 	}
                 }
                $nestedData['CREACION'] = substr($post->FECALTAS,0,10);

                if ($post->ESTADO === 1) {
				 $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrar' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a>";
				 $nestedData['ESTATUS'] = 'table-success';
                   
                    
                } else {
                	$nestedData['ACCION'] = "&emsp;<a href='#' id='mostrar' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a>&emsp;<a href='#' id='devolverProducto' title='Devolver'><i class='fa fa-arrow-alt-circle-left text-danger' aria-hidden='true'></i></a>";
                	 $nestedData['ESTATUS'] = 'table-danger';
                     

                   
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
    public static function prestamoProductoDetalle($request) {

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
                            1 => 'DESCRIPCION',
                            2 => 'CANTIDAD'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = PrestamoProducto::
                    leftjoin('PRESTAMO_PRODUCTOS_DET', 'PRESTAMO_PRODUCTOS_DET.FK_PRESTAMO_PRODUCTOS', '=', 'PRESTAMO_PRODUCTOS.ID')
                    ->where([
                        'PRESTAMO_PRODUCTOS.ID_SUCURSAL' => $user->id_sucursal,
                        'PRESTAMO_PRODUCTOS.ID' => $codigo
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

            $posts = PrestamoProducto::select(DB::raw('0 AS ITEM, PRESTAMO_PRODUCTOS_DET.COD_PROD, PRODUCTOS.DESCRIPCION, sum(PRESTAMO_PRODUCTOS_DET.CANTIDAD) as CANTIDAD'))
                ->leftjoin('PRESTAMO_PRODUCTOS_DET', 'PRESTAMO_PRODUCTOS_DET.FK_PRESTAMO_PRODUCTOS', '=', 'PRESTAMO_PRODUCTOS.ID')
                ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRESTAMO_PRODUCTOS_DET.COD_PROD')
                
                    ->where([
                        'PRESTAMO_PRODUCTOS.ID_SUCURSAL' => $user->id_sucursal,
                        'PRESTAMO_PRODUCTOS.ID' => $codigo
                    ])
                    ->GROUPBY('PRESTAMO_PRODUCTOS_DET.COD_PROD')
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

            $posts = PrestamoProducto::select(DB::raw('0 AS ITEM, PRESTAMO_PRODUCTOS_DET.COD_PROD, PRODUCTOS.DESCRIPCION, sum(PRESTAMO_PRODUCTOS_DET.CANTIDAD) as CANTIDAD'))
                ->leftjoin('PRESTAMO_PRODUCTOS_DET', 'PRESTAMO_PRODUCTOS_DET.FK_PRESTAMO_PRODUCTOS', '=', 'PRESTAMO_PRODUCTOS.ID')
                ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRESTAMO_PRODUCTOS_DET.COD_PROD')
                    ->where([
                        'PRESTAMO_PRODUCTOS.ID_SUCURSAL' => $user->id_sucursal,
                        'PRESTAMO_PRODUCTOS.ID' => $codigo
                    ])
                            ->where(function ($query) use ($search) {
                                $query->where('PRESTAMO_PRODUCTOS_DET.COD_PROD','LIKE',"{$search}%")
                                      ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"{$search}%");
                            })
                            ->GROUPBY('PRESTAMO_PRODUCTOS_DET.COD_PROD')
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = PrestamoProducto::leftjoin('PRESTAMO_PRODUCTOS_DET', 'PRESTAMO_PRODUCTOS_DET.FK_PRESTAMO_PRODUCTOS', '=', 'PRESTAMO_PRODUCTOS.ID')
                ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRESTAMO_PRODUCTOS_DET.COD_PROD')
                    ->where([
                        'PRESTAMO_PRODUCTOS.ID_SUCURSAL' => $user->id_sucursal,
                        'PRESTAMO_PRODUCTOS.ID' => $codigo
                    ])
                            ->where(function ($query) use ($search) {
                                $query->where('PRESTAMO_PRODUCTOS_DET.COD_PROD','LIKE',"{$search}%")
                                      ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"{$search}%");
                            })
                            ->GROUPBY('PRESTAMO_PRODUCTOS_DET.COD_PROD')
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
        
        $salida = PrestamoProducto::select(DB::raw(
                        'ID,
                        OBSERVACION,
                        FECALTAS,
                        TIPO'
                    ))
        ->where('ID','=', $codigo)
        ->where('ID_SUCURSAL','=', $user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        $tipo = PrestamoProducto::mostrar_tipo($salida[0]->TIPO);
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

        $prestamo_det = PrestamoProducto::select(DB::raw(
                        'prestamo_productos_det.ID,
                        prestamo_productos_det.COD_PROD, 
                        PRODUCTOS.DESCRIPCION,
                        prestamo_productos_det.CANTIDAD,
                        prestamo_productos_det.COSTO,
                        0 AS COSTO_TOTAL,
                        prestamo_productos_det.FK_ID_LOTE'
                    ))
        ->leftjoin('PRESTAMO_PRODUCTOS_DET', 'PRESTAMO_PRODUCTOS_DET.FK_PRESTAMO_PRODUCTOS', '=', 'PRESTAMO_PRODUCTOS.ID')
        ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRESTAMO_productos_det.COD_PROD')
        ->where('prestamo_productos.ID_SUCURSAL','=', $user->id_sucursal)
        ->where('prestamo_productos_det.FK_PRESTAMO_PRODUCTOS','=', $codigo)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        foreach ($prestamo_det as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            $prestamo_det[$key]->COD_PROD = $value->COD_PROD;
            $prestamo_det[$key]->DESCRIPCION = $value->DESCRIPCION;
            $prestamo_det[$key]->CANTIDAD = $value->CANTIDAD;
            $prestamo_det[$key]->COSTO = $value->COSTO;
            $prestamo_det[$key]->COSTO_TOTAL = $value->COSTO * $value->CANTIDAD;

            /*  --------------------------------------------------------------------------------- */

        }

        return $prestamo_det;

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

        $salida = PrestamoProducto::mostrar_cabecera($dato['id']);

        /*  --------------------------------------------------------------------------------- */

        // OBTENER DATOS DETALLE 

        $prestamo_det = PrestamoProducto::mostrar_cuerpo($dato['id']);
        
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

        foreach ($prestamo_det as $key => $value) {

            

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
        
        $html = view('pdf.rptPrestamoProducto',$data)->render();
        
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

            $prestamo_det = PrestamoProducto::mostrar_cuerpo($data['codigo']);
            
            /*  --------------------------------------------------------------------------------- */

            // CARGAR DETALLE DE TRANSFERENCIA DET 

            foreach ($prestamo_det as $key => $value) {

                /*  --------------------------------------------------------------------------------- */

                // DEVOLVER STOCK 

                Stock::sumar_stock_id_lote($value->FK_ID_LOTE, $value->CANTIDAD);

                /*  --------------------------------------------------------------------------------- */

            }

            /*  --------------------------------------------------------------------------------- */

            PrestamoProducto::where('ID','=', $data['codigo'])
            ->where('ID_SUCURSAL','=', $user->id_sucursal)
            ->update(['ESTADO' => 1]);

            /*  --------------------------------------------------------------------------------- */

            // RESPONSE

            

            /*  --------------------------------------------------------------------------------- */

            // ENVIAR TRANSACCION A BD

            DB::connection('retail')->commit();

            /*  --------------------------------------------------------------------------------- */

            return ["response" => true, "statusText" => "Se han devuelto los productos del prestamo"];

            /*  --------------------------------------------------------------------------------- */

        } catch (Exception $e) {
            
            /*  --------------------------------------------------------------------------------- */

           // NO GUARDAR NINGUN CAMBIO 

           DB::connection('retail')->rollBack();
           throw $e;
           
           /*  --------------------------------------------------------------------------------- */

        }
    }
}
