<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Mpdf\Mpdf;
use App\Lote_tiene_ConteoDet;
use App\Ventas_det;
use Illuminate\Support\Facades\Log;
use App\SalidaProductoDet;
use App\DevolucionProvDet;
use App\Stock;
use App\NotaCreditoDet;
use App\Gondola_tiene_Productos;

class Inventario extends Model
{
	
	/*  --------------------------------------------------------------------------------- */

    // INICIAR LAS VARIABLES GLOBALES

    protected $connection = 'retail';
    protected $table = 'conteo_det';
    
    /*  --------------------------------------------------------------------------------- */

    public static function insertar_producto($dato) {

       /*  --------------------------------------------------------------------------------- */

       // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

       $user = auth()->user();

       /*  --------------------------------------------------------------------------------- */
       
       // INICIAR VARIABLE 

       $codigo = $dato["codigo"];
       $id = $dato["id"];
       $cantidad = $dato["cantidad"];
       $codigointerno = 'No Usado';

       /*  --------------------------------------------------------------------------------- */
        
        $estatus = Inventario::comprobar_status($dato);

        if ($estatus["response"] === false){

            return $estatus;
        }

        $gondola = DB::connection('retail')
            ->table('CONTEO')
            ->select(DB::raw('IFNULL(CONTEO.GONDOLA, 0) AS ID_GONDOLA,
                IFNULL(GONDOLAS.DESCRIPCION, 0) AS DESCRIPCION'))
            ->leftjoin('GONDOLAS', 'GONDOLAS.ID', '=', 'CONTEO.GONDOLA')
            ->where('CONTEO.ID', '=', $id)
            ->get();

        if(count($gondola) > 0){

            if($gondola[0]->ID_GONDOLA != 658 && $gondola[0]->ID_GONDOLA != 0){

                $verificar_producto = Gondola_tiene_Productos::select(DB::raw('ID'))
                    ->where('GONDOLA_COD_PROD', '=', $codigo)
                    ->where('ID_SUCURSAL', '=', $user->id_sucursal)
                    ->where('ID_GONDOLA', '=', $gondola[0]->ID_GONDOLA)
                    ->get();

                if(count($verificar_producto) == 0){

                    return ["response" => false, "status" => 'El producto no pertenece a la góndola '.$gondola[0]->DESCRIPCION, "codigo" => $codigo];
                }

            }
        }

       /*  --------------------------------------------------------------------------------- */
        
       $codigo_interno = DB::connection('retail')
        ->table('PRODUCTOS_AUX')
        ->select(DB::raw('CODIGO'))
        ->where('CODIGO_INTERNO', '=', $codigo)
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();
        
       /*  --------------------------------------------------------------------------------- */

       // CAMBIAR CODIGO PRODUCTO

       if (count($codigo_interno) > 0) {
            $codigointerno = $codigo;
            $codigo = $codigo_interno[0]->CODIGO;
       } 

       /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE PRODUCTO 

        $existe = Inventario::select(DB::raw('STOCK'))
        ->where('COD_PROD', '=', $codigo)
        ->where('FK_CONTEO', '=', $id)
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();

        if (count($existe) > 0) {

            /*  --------------------------------------------------------------------------------- */

            $stock = $existe[0]->STOCK;

            /*  --------------------------------------------------------------------------------- */

            // MODIFICAR PRODUCTO EXISTENTE

            $insert = Inventario::where(['COD_PROD' => $codigo, 'FK_CONTEO' => $id, 'ID_SUCURSAL' => $user->id_sucursal])
            ->update(['UPDATED_AT' => date('Y-m-d H:m:s'), 'CONTEO' => \DB::raw('CONTEO + '.$cantidad.''), 'FK_USER' => $user->id]);

            /*  --------------------------------------------------------------------------------- */

        } else {

            /*  --------------------------------------------------------------------------------- */

            $producto = DB::connection('retail')
            ->table('PRODUCTOS_AUX')
            ->select(DB::raw('PRODUCTOS_AUX.CODIGO'),
            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS STOCK'))
            ->where('PRODUCTOS_AUX.CODIGO', '=', $codigo)
            ->where('PRODUCTOS_AUX.ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            // RETORNAR EL VALOR
        
            if (count($producto) > 0) {
                $stock = $producto[0]->STOCK;
            } else {
                return ["response" => false, "status" => "No existe producto", "codigo" => $codigo];
            }
            /*  --------------------------------------------------------------------------------- */

            // INSERTAR PRODUCTO 

            $insert = Inventario::insert(
                [
                 'COD_PROD' => $codigo, 
                 'FK_CONTEO' => $id, 
                 'ID_SUCURSAL' => $user->id_sucursal, 
                 'CONTEO' => $cantidad, 
                 'STOCK' => $stock,
                 'CREATED_AT' => date('Y-m-d H:m:s'), 
                 'FK_USER' => $user->id]
            );

            /*  --------------------------------------------------------------------------------- */

        } 

        /*  --------------------------------------------------------------------------------- */

        // UPDATE OR INSERT

        // $insert = DB::connection('retail')
        // ->table('conteo_det')
        // ->updateOrInsert(
        //      ['COD_PROD' => $codigo, 'FK_CONTEO' => $id, 'ID_SUCURSAL' => $user->id_sucursal],
        //      ['CONTEO' => \DB::raw('CONTEO + 1'), 'STOCK' => $stock, 'FK_USER' => $user->id]
        //  );

       /*  --------------------------------------------------------------------------------- */

       // RETORNAR VALOR 

       if ($insert === true || $insert === 1) {

        $pro = DB::connection('retail')
        ->table('conteo_det')
        ->select(DB::raw('CONTEO'))
        ->where('COD_PROD', '=', $codigo)
        ->where('FK_CONTEO', '=', $id)
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->limit(1)
        ->get();

        DB::connection('retail')->table('conteo')->where(['ID' => $id, 'ID_SUCURSAL' => $user->id_sucursal])
            ->update(['FECMODIF' => date('Y-m-d H:m:s')]);

          return ["response" => true, "status" => "Contado correctamente", "codigo" => $codigo, "cantidad" => $pro[0]->CONTEO." - Stock: ".$stock." - Interno: ".$codigointerno];
       } 
       

       /*  --------------------------------------------------------------------------------- */

    }

    public static function guardar_inventario($dato) {

    	/*  --------------------------------------------------------------------------------- */
        
        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

    	// INICIAR VARIABLES 

    	$diaHora = date("Y-m-d H:i:s");
    	$observacion = $dato['data']["observacion"];
    	$id_sucursal = $dato['data']["sucursal"]; 
        $motivo = $dato['data']["motivo"];
        $gondola = $dato['data']["gondola"];
        
        if(count($gondola[0])>0){
            $gondola = $gondola[0]['ID'];
        }else{
            $gondola = 0;
        }

    	/*  --------------------------------------------------------------------------------- */

    	// INSERTAR CONTEO

    	$id = DB::connection('retail')
    	->table('conteo')->insertGetId([
            'OBSERVACION' => $observacion, 
            'FECALTAS' => $diaHora, 
            'ID_SUCURSAL' => $user->id_sucursal, 
            'GONDOLA' => $gondola, 
            'MOTIVO' => $motivo,
            'FK_USER' => $user->id, 
            'FECMODIF' => $diaHora]
		);

    	/*  --------------------------------------------------------------------------------- */

    	// RETORNAR TRUE SI GUARDO

    	return ["response" => true, "id" => $id];
    	
    	/*  --------------------------------------------------------------------------------- */

    }

    public static function mostrarDatatable($request)
    {

        /*  --------------------------------------------------------------------------------- */

        // INICIARA VARIABLES

        $id = $request->input('id');

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'COD_PROD', 
                            1 => 'DESCRIPCION',
                            2 => 'CONTEO',
                            3 => 'STOCK'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = Inventario::where('ID_SUCURSAL','=', $user->id_sucursal)
        			 ->where('conteo_det.FK_CONTEO','=', $id)
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

  			$posts = Inventario::select(DB::raw('conteo_det.COD_PROD, PRODUCTOS.DESCRIPCION, conteo_det.CONTEO, conteo_det.STOCK, conteo_det.COMENTARIO'))
            ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'conteo_det.COD_PROD')
            ->where('conteo_det.FK_CONTEO','=', $id)
            ->where('conteo_det.ID_SUCURSAL','=', $user->id_sucursal)
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

            $posts =  Inventario::select(DB::raw('conteo_det.COD_PROD, PRODUCTOS.DESCRIPCION, conteo_det.CONTEO, conteo_det.STOCK, conteo_det.COMENTARIO'))
            ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'conteo_det.COD_PROD')
            ->where('conteo_det.FK_CONTEO','=', $id)
      		->where('conteo_det.ID_SUCURSAL','=', $user->id_sucursal)
            ->where(function ($query) use ($search) {
                                $query->where('conteo_det.COD_PROD','LIKE',"%{$search}%")
                                      ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"%{$search}%");
                         })
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = Inventario::leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'conteo_det.COD_PROD')
                         	 ->where('conteo_det.FK_CONTEO','=', $id)
            			 	 ->where('conteo_det.ID_SUCURSAL','=', $user->id_sucursal)
                             ->where(function ($query) use ($search) {
                                $query->where('conteo_det.COD_PROD','LIKE',"%{$search}%")
                                      ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"%{$search}%");
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
                $nestedData['CONTEO'] = $post->CONTEO;
                $nestedData['COMENTARIO'] = $post->COMENTARIO;
                $nestedData['STOCK'] = $post->STOCK;

                $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarDetalle' title='Detalle'><i class='fa fa-list' aria-hidden='true'></i></a>
                    &emsp;<a href='#' id='comentario' title='Comentario'><i class='fa fa-comments text-secondary' aria-hidden='true'></i></a>&emsp;<a href='#' id='eliminar' title='Eliminar'><i class='fa fa-trash text-danger' aria-hidden='true'></i></a>";

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

    public static function mostrarDatatableInventario($request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'ID', 
                            1 => 'OBSERVACION',
                            2 => 'MOTIVO',
                            3 => 'SUCURSAL',
                            4 => 'FECALTAS',
                            5 => 'FECMODIF'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = DB::connection('retail')
        			 ->table('conteo')
        			 ->where('ID_SUCURSAL','=', $user->id_sucursal)
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

  			$posts = DB::connection('retail')
  			->table('conteo')
  			->select(DB::raw('conteo.ID,CONTEO.GONDOLA AS GONDOLA, conteo.OBSERVACION, conteo.MOTIVO, conteo.ID_SUCURSAL AS SUCURSAL, conteo.FECALTAS, conteo.FECMODIF'))
            ->where('conteo.ID_SUCURSAL','=', $user->id_sucursal)
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

            $posts =  DB::connection('retail')
            ->table('conteo')
  			->select(DB::raw('conteo.ID,CONTEO.GONDOLA AS GONDOLA, conteo.OBSERVACION, conteo.MOTIVO, conteo.FECALTAS, conteo.ID_SUCURSAL AS SUCURSAL, conteo.FECMODIF'))
            ->where('conteo.ID_SUCURSAL','=', $user->id_sucursal)
            ->where(function ($query) use ($search) {
                                $query->where('conteo.ID','LIKE',"%{$search}%")
                                      ->orWhere('conteo.OBSERVACION', 'LIKE',"%{$search}%");
                         })
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = DB::table('conteo')
            				 ->where('conteo.ID_SUCURSAL','=', $user->id_sucursal)
            				 ->where(function ($query) use ($search) {
                                $query->where('conteo.ID','LIKE',"%{$search}%")
                                      ->orWhere('conteo.OBSERVACION', 'LIKE',"%{$search}%");
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
                $gondola='';
                if($post->GONDOLA<>0 && $post->GONDOLA<>658){
                    $gondola="&emsp;<a href='#' id='reporte' title='Reporte'><i class='fa fa-file text-secondary' aria-hidden='true'></i></a>";

                }
                $nestedData['ID'] = $post->ID;
                $nestedData['OBSERVACION'] = $post->OBSERVACION;
                $nestedData['MOTIVO'] = $post->MOTIVO;
                $nestedData['SUCURSAL'] = $post->SUCURSAL;
                $nestedData['FECALTAS'] = $post->FECALTAS;
                $nestedData['FECMODIF'] = $post->FECMODIF;
                 $nestedData['ID_GONDOLA'] = $post->GONDOLA;

                if($user->can("inventario.mostrar.procesar")){
                    $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarInventario' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a>&emsp;<a href='#' id='editarInventario' title='Editar'><i class='fa fa-edit text-warning' aria-hidden='true'></i></a>&emsp;<a href='#' id='eliminarInventario' title='Eliminar'><i class='fa fa-trash text-danger' aria-hidden='true'></i></a>
                    &emsp;<a href='#' id='imprimirInventario' title='Imprimir'><i class='fa fa-print text-primary' aria-hidden='true'></i></a>&emsp;<a href='#' id='procesarInventario' title='Imprimir'><i class='fa fa-check-square text-success' aria-hidden='true'></i></a>".$gondola;

                }else{
                    $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarInventario' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a>&emsp;<a href='#' id='editarInventario' title='Editar'><i class='fa fa-edit text-warning' aria-hidden='true'></i></a>&emsp;<a href='#' id='eliminarInventario' title='Eliminar'><i class='fa fa-trash text-danger' aria-hidden='true'></i></a>
                    &emsp;<a href='#' id='imprimirInventario' title='Imprimir'><i class='fa fa-print text-primary' aria-hidden='true'></i></a>".$gondola;

                }
                
                    // &emsp;<a href='#' id='procesarInventario' title='Imprimir'><i class='fa fa-check-square text-success' aria-hidden='true'></i></a>
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

    public static function modificarComentario($dato) {

       /*  --------------------------------------------------------------------------------- */

       // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

       $user = auth()->user();

       /*  --------------------------------------------------------------------------------- */
       
       // INICIAR VARIABLE 

       $codigo = $dato["codigo"];
       $id = $dato["id"];
       $comentario = $dato["comentario"];

       /*  --------------------------------------------------------------------------------- */

       // REVISAR SI EXISTE PRODUCTO 

       $insert = Inventario::where(['COD_PROD' => $codigo, 'FK_CONTEO' => $id, 'ID_SUCURSAL' => $user->id_sucursal])
       ->update(['UPDATED_AT' => date('Y-m-d'), 'COMENTARIO' => $comentario, 'FK_USER' => $user->id]);

       /*  --------------------------------------------------------------------------------- */

       // RETORNAR VALOR 

       return ["response" => true];

       /*  --------------------------------------------------------------------------------- */

    }

    public static function eliminarProducto($dato) {

       /*  --------------------------------------------------------------------------------- */

       // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

       $user = auth()->user();

       /*  --------------------------------------------------------------------------------- */
       
       // INICIAR VARIABLE 

       $codigo = $dato["codigo"];
       $id = $dato["id"];

       /*  --------------------------------------------------------------------------------- */

       // REVISAR SI EXISTE PRODUCTO 

       $conteo = Inventario::where('ID_SUCURSAL','=', $user->id_sucursal)
        ->where('COD_PROD','=', $codigo)
        ->where('FK_CONTEO','=', $id)
        ->delete();

       /*  --------------------------------------------------------------------------------- */

       // RETORNAR VALOR 

       return ["response" => true];

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
        
        $Inventario = DB::connection('retail')->table('conteo')->select(DB::raw(
                        'CONTEO.ID,
                        CONTEO.OBSERVACION,
                        conteo.FECALTAS,
                        SUCURSALES.DESCRIPCION AS SUCURSAL,
                        IFNULL(GONDOLAS.DESCRIPCION, "NO POSEE") AS GONDOLA,
                        IFNULL(CONTEO.MOTIVO, "NO POSEE") AS MOTIVO '
                    ))
        ->leftjoin('SUCURSALES', 'SUCURSALES.CODIGO', '=', 'CONTEO.ID_SUCURSAL')
        ->leftjoin('GONDOLAS', 'GONDOLAS.ID', '=', 'CONTEO.GONDOLA')
        ->where('CONTEO.ID','=', $codigo)
        ->where('CONTEO.ID_SUCURSAL','=', $user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        return $Inventario[0];

        /*  --------------------------------------------------------------------------------- */

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

        $conteo_det = Inventario::select(DB::raw(
                        'conteo_det.ID,
                        conteo_det.COD_PROD, 
                        PRODUCTOS.DESCRIPCION,
                        conteo_det.STOCK, 
                        conteo_det.CONTEO,
                        conteo_det.CREATED_AT,
                        conteo_det.UPDATED_AT,
                        conteo_det.COMENTARIO,
                        0 AS VENDIDO'
                    ))
        ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'conteo_det.COD_PROD')
        ->where('conteo_det.ID_SUCURSAL','=', $user->id_sucursal)
        ->where('conteo_det.FK_CONTEO','=', $codigo)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        foreach ($conteo_det as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            // VER PRECIOS DE PRODUCTOS 

            if ($value->UPDATED_AT === '0000-00-00') {
                $value->UPDATED_AT = 'N/A';
            }

            /*  --------------------------------------------------------------------------------- */

            $conteo_det[$key]->COD_PROD = $value->COD_PROD;
            $conteo_det[$key]->DESCRIPCION = $value->DESCRIPCION;
            $conteo_det[$key]->STOCK = $value->STOCK;
            $conteo_det[$key]->CONTEO =$value->CONTEO;
            $conteo_det[$key]->CREATED_AT = $value->CREATED_AT;
            $conteo_det[$key]->UPDATED_AT = $value->UPDATED_AT;
            $conteo_det[$key]->COMENTARIO = $value->COMENTARIO;

            /*  --------------------------------------------------------------------------------- */

           
                $hora=substr($value->CREATED_AT, 11);
                $fecha = substr($value->CREATED_AT, 0,-8);
            // OBTENER LA CANTIDAD QUE SE VENDIO DESPUES DE INICIAR A CONTAR 

            $vendido = Ventas_det::select(DB::raw('IFNULL(SUM(CANTIDAD),0) AS CANTIDAD'))
            ->where('ANULADO', '=', 0)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->Where('COD_PROD', '=', $value->COD_PROD)
            ->Where(DB::raw("CONCAT(SUBSTR(FECALTAS, 1, 11), HORALTAS)"), '>=', $value->CREATED_AT) 
            ->get();

            /*  --------------------------------------------------------------------------------- */
             // OBTENER LA CANTIDAD DE SALIDA DE PRODUCTOS DESPUES DE INICIAR A CONTAR 
            $salida=SalidaProductoDet::Select(DB::raw('IFNULL(SUM(CANTIDAD),0) AS CANTIDAD'))
            ->leftjoin('salida_productos','salida_productos.ID','=','FK_SALIDA_PRODUCTOS')
            ->Where(DB::raw("FECALTAS"), '>=', $value->CREATED_AT) 
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->Where('COD_PROD', '=', $value->COD_PROD)
            ->where('ESTADO', '=', 0)
            ->get();

            /*  --------------------------------------------------------------------------------- */
            // OBTENER LA CANTIDAD DE DEVOLUCION A PROVEEDOR DESPUES DE INICIAR A CONTAR 
            $dev_prov=DevolucionProvDet::Select(DB::raw('IFNULL(SUM(CANTIDAD),0) AS CANTIDAD'))
            ->leftjoin('devolucion_prov','devolucion_prov.ID','=','FK_DEVOLUCION_PROV')
            ->Where(DB::raw("FECALTAS"), '>=', $value->CREATED_AT) 
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->Where('COD_PROD', '=', $value->COD_PROD)
            ->get();


            /*  --------------------------------------------------------------------------------- */
                // OBTENER LA CANTIDAD QUE SE CREO DESPUES DE INICIAR A CONTAR 

            $creado = Stock::select(DB::raw('IFNULL(SUM(CANTIDAD_INICIAL),0) AS CANTIDAD'))
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->Where('COD_PROD', '=', $value->COD_PROD)
            ->Where(DB::raw("CONCAT(SUBSTR(FECALTAS, 1, 11), HORALTAS)"), '>=', $value->CREATED_AT) 
            ->get();

            /*  --------------------------------------------------------------------------------- */
            // OBTENER LA CANTIDAD QUE SE DEVOLVIO POR NOTA DE CREDITO DESPUES DE INICIAR A CONTAR 

            $nota_credito = NotaCreditoDet::select(DB::raw('IFNULL(SUM(NOTA_CREDITO_DET.CANTIDAD),0) AS CANTIDAD'))
            ->leftjoin('NOTA_CREDITO','NOTA_CREDITO.ID','=','FK_NOTA_CREDITO')
            ->where('NOTA_CREDITO_DET.ID_SUCURSAL', '=', $user->id_sucursal)
            ->where('NOTA_CREDITO.TIPO', '=', 1)
            ->Where('NOTA_CREDITO_DET.CODIGO_PROD', '=', $value->COD_PROD)
            ->Where(DB::raw("CONCAT(SUBSTR(NOTA_CREDITO_DET.FECALTAS, 1, 11), NOTA_CREDITO_DET.HORALTAS)"), '>=', $value->CREATED_AT) 
            ->get();

            /*  --------------------------------------------------------------------------------- */
            // OBTENER LA CANTIDAD QUE SE TRANSFIRIO DESPUES DE INICIAR A CONTAR 

            $transferencias = DB::connection('retail')->table('TRANSFERENCIAS_DET')->select(DB::raw('IFNULL(SUM(CANTIDAD),0) AS CANTIDAD'))
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->Where('CODIGO_PROD', '=', $value->COD_PROD)
            ->Where(DB::raw("CONCAT(SUBSTR(FECALTAS, 1, 11), HORALTAS)"), '>=', $value->CREATED_AT) 
            ->get();

            /*  --------------------------------------------------------------------------------- */
 
            if(count($vendido) > 0) {
                $conteo_det[$key]->VENDIDO = $conteo_det[$key]->VENDIDO-$vendido[0]['CANTIDAD'] ;
            } else {
                $conteo_det[$key]->VENDIDO = 0;
            }

            if(count($salida) > 0) {
                $conteo_det[$key]->VENDIDO = $conteo_det[$key]->VENDIDO-$salida[0]['CANTIDAD'] ;
            }

            if(count($dev_prov) > 0) {
                $conteo_det[$key]->VENDIDO = $conteo_det[$key]->VENDIDO-$dev_prov[0]['CANTIDAD'] ;
            }

           if(count($creado) > 0) {
                $conteo_det[$key]->VENDIDO = $conteo_det[$key]->VENDIDO+$creado[0]['CANTIDAD'] ;
            }

            if(count($nota_credito) > 0) {
                $conteo_det[$key]->VENDIDO = $conteo_det[$key]->VENDIDO+$nota_credito[0]['CANTIDAD'] ;
            }

            if(count($transferencias) > 0) {
                $conteo_det[$key]->VENDIDO = $conteo_det[$key]->VENDIDO-$transferencias[0]->CANTIDAD ;
            }

    


            /*  --------------------------------------------------------------------------------- */

        }

        return $conteo_det;

        /*  --------------------------------------------------------------------------------- */

    }

    public static function conteo_procesado($dato) {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        $conteo_det = Lote_tiene_ConteoDet::select(DB::raw(
                        'ACCION, SUM(CANTIDAD) AS CANTIDAD'
                    ))
        ->where('ID_CONTEO_DET','=', $dato)
        ->groupBy('ID_CONTEO_DET')
        ->get();

        /*  --------------------------------------------------------------------------------- */

        if (count($conteo_det) > 0) {
            if ($conteo_det[0]["ACCION"] === 1) {
                $status = "+ ".$conteo_det[0]["CANTIDAD"];
            } else if ($conteo_det[0]["ACCION"] === 2) {
                $status = "- ".$conteo_det[0]["CANTIDAD"];
            }
        } else {
            $status = "SIN AJUSTE";
        }

        /*  --------------------------------------------------------------------------------- */ 

        return $status;

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

        $conteo = Inventario::mostrar_cabecera($dato);

        /*  --------------------------------------------------------------------------------- */

        // OBTENER DATOS DETALLE 

        $conteo_det = Inventario::mostrar_cuerpo($dato);
        
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $c = 0;
        $codigo = $conteo->ID;
        $observacion = $conteo->OBSERVACION;
        $fecaltas = $conteo->FECALTAS;
        $motivo = $conteo->MOTIVO;
        $gondola = $conteo->GONDOLA;
        $tipo = '';

        /*  --------------------------------------------------------------------------------- */

        // TIPO

        if ($dato["tipo"] === "1") {
            $tipo = 'GENERAL';
        } else if ($dato["tipo"] === "2") {
            $tipo = 'COINCIDEN STOCK';
        } else if ($dato["tipo"] === "3") {
            $tipo = 'VARIAN A STOCK';
        } else if ($dato["tipo"] === "4") {
            $tipo = 'MAYOR A STOCK';
        } else if ($dato["tipo"] === "5") {
            $tipo = 'MENOR A STOCK';
        }

        /*  --------------------------------------------------------------------------------- */

        $nombre = 'Inventario_'.$codigo.'_'.time().'';
        $articulos = [];
        $cantidad = 0;
        $total_conteo = 0;
        $nombre_sucursal = $conteo->SUCURSAL;

        /*  --------------------------------------------------------------------------------- */
        
        // CARGAR DETALLE DE TRANSFERENCIA DET 

        foreach ($conteo_det as $key => $value) {

            if ($dato["tipo"] === "1") {

                $articulos[$c]["cod_prod"] = $value->COD_PROD;
                $articulos[$c]["descripcion"] = $value->DESCRIPCION;
                $articulos[$c]["stock"] = $value->STOCK;
                $articulos[$c]["conteo"] = $value->CONTEO;
                $articulos[$c]["created_at"] = $value->CREATED_AT;
                $articulos[$c]["updated_at"] = $value->UPDATED_AT;
                $articulos[$c]["comentario"] = $value->COMENTARIO;
                $cantidad = $cantidad + $value->STOCK;
                $total_conteo = $total_conteo + $value->CONTEO;
                $articulos[$c]["vendidos"] = $value->VENDIDO;

                if ($value->STOCK === $value->CONTEO) {
                    $articulos[$c]["estatus"] = 'COINCIDEN';
                } else {
                    $articulos[$c]["estatus"] = 'DIFERENCIA';
                }

                $articulos[$c]["ajuste"] = Inventario::conteo_procesado($value->ID);

                $c = $c + 1;

            } else if($dato["tipo"] === "2") {

                if ($value->STOCK === $value->CONTEO) {

                    $articulos[$c]["cod_prod"] = $value->COD_PROD;
                    $articulos[$c]["descripcion"] = $value->DESCRIPCION;
                    $articulos[$c]["stock"] = $value->STOCK;
                    $articulos[$c]["conteo"] = $value->CONTEO;
                    $articulos[$c]["created_at"] = $value->CREATED_AT;
                    $articulos[$c]["updated_at"] = $value->UPDATED_AT;
                    $articulos[$c]["comentario"] = $value->COMENTARIO;
                    $cantidad = $cantidad + $value->STOCK;
                    $total_conteo = $total_conteo + $value->CONTEO;
                    $articulos[$c]["vendidos"] = $value->VENDIDO;
                    
                    $articulos[$c]["estatus"] = 'COINCIDEN';
                    
                    $articulos[$c]["ajuste"] = Inventario::conteo_procesado($value->ID);

                    $c = $c + 1;
                }

            } else if($dato["tipo"] === "3") {

                if ($value->STOCK <> $value->CONTEO) {

                    $articulos[$c]["cod_prod"] = $value->COD_PROD;
                    $articulos[$c]["descripcion"] = $value->DESCRIPCION;
                    $articulos[$c]["stock"] = $value->STOCK;
                    $articulos[$c]["conteo"] = $value->CONTEO;
                    $articulos[$c]["created_at"] = $value->CREATED_AT;
                    $articulos[$c]["updated_at"] = $value->UPDATED_AT;
                    $articulos[$c]["comentario"] = $value->COMENTARIO;
                    $cantidad = $cantidad + $value->STOCK;
                    $total_conteo = $total_conteo + $value->CONTEO;
                    $articulos[$c]["vendidos"] = $value->VENDIDO;
                    
                    $articulos[$c]["estatus"] = 'DIFERENCIA';
                    
                    $articulos[$c]["ajuste"] = Inventario::conteo_procesado($value->ID);

                    $c = $c + 1;
                }

            } else if($dato["tipo"] === "4") {

                if ($value->STOCK < $value->CONTEO) {

                    $articulos[$c]["cod_prod"] = $value->COD_PROD;
                    $articulos[$c]["descripcion"] = $value->DESCRIPCION;
                    $articulos[$c]["stock"] = $value->STOCK;
                    $articulos[$c]["conteo"] = $value->CONTEO;
                    $articulos[$c]["created_at"] = $value->CREATED_AT;
                    $articulos[$c]["updated_at"] = $value->UPDATED_AT;
                    $articulos[$c]["comentario"] = $value->COMENTARIO;
                    $cantidad = $cantidad + $value->STOCK;
                    $total_conteo = $total_conteo + $value->CONTEO;
                    $articulos[$c]["vendidos"] = $value->VENDIDO;
                    
                    $articulos[$c]["estatus"] = 'DIFERENCIA';
                    
                    $articulos[$c]["ajuste"] = Inventario::conteo_procesado($value->ID);

                    $c = $c + 1;
                }

            } else if($dato["tipo"] === "5") {

                if ($value->STOCK > $value->CONTEO) {

                    $articulos[$c]["cod_prod"] = $value->COD_PROD;
                    $articulos[$c]["descripcion"] = $value->DESCRIPCION;
                    $articulos[$c]["stock"] = $value->STOCK;
                    $articulos[$c]["conteo"] = $value->CONTEO;
                    $articulos[$c]["created_at"] = $value->CREATED_AT;
                    $articulos[$c]["updated_at"] = $value->UPDATED_AT;
                    $articulos[$c]["comentario"] = $value->COMENTARIO;
                    $cantidad = $cantidad + $value->STOCK;
                    $total_conteo = $total_conteo + $value->CONTEO;
                    $articulos[$c]["vendidos"] = $value->VENDIDO;
                    
                    $articulos[$c]["estatus"] = 'DIFERENCIA';
                    
                    $articulos[$c]["ajuste"] = Inventario::conteo_procesado($value->ID);

                    $c = $c + 1;
                }

            }
        
           
        }

        /*  --------------------------------------------------------------------------------- */

        // CARGAR VARIABLES 
        
        $data['codigo'] = $codigo;
        $data['nombre'] = $nombre;
        $data['motivo'] = $motivo;
        $data['gondola'] = $gondola;
        $data['observacion'] = $observacion;
        $data['fecaltas'] = $fecaltas;
        $data['tipo'] = $tipo;
        $data['articulos'] = $articulos;
        $data['c'] = $c;
        $data['cantidad'] = $cantidad;
        $data['total'] = $total_conteo;
        $data['nombre_sucursal'] = $nombre_sucursal;
        $data['logo']=(Imagen::obtenerLogoDireccion())['imagen'];

        /*  --------------------------------------------------------------------------------- */
        
        $html = view('pdf.rptInventario',$data)->render();
        
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

    public static function comprobar_status($dato) {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
         
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $codigo = $dato["id"];

        /*  --------------------------------------------------------------------------------- */
        
        $Inventario = DB::connection('retail')->table('conteo')->select(DB::raw(
                        'ESTATUS'
                    ))
        ->where('CONTEO.ID','=', $codigo)
        ->where('CONTEO.ID_SUCURSAL','=', $user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        if ($Inventario[0]->ESTATUS === 1) {
            return ["response" => true, "statusText" => "Se puede procesar"];
        } else {
            return ["response" => false, "statusText" => "No se puede modificar porque ya se encuentra procesada"];
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function procesar($dato) {


        /*  --------------------------------------------------------------------------------- */

        try {
       
           DB::connection('retail')->beginTransaction();

           // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

           $user = auth()->user();

           /*  --------------------------------------------------------------------------------- */
           
           // INICIAR VARIABLE 

           $id = $dato["id"];

           /*  --------------------------------------------------------------------------------- */
           
           $estatus = Inventario::comprobar_status($dato);

           if ($estatus["response"] === false){
                return $estatus;
           }
        
           /*  --------------------------------------------------------------------------------- */

           // OBTENER DATOS DETALLE 

           $conteo_det = Inventario::mostrar_cuerpo(["codigo" => $id]);
            
           /*  --------------------------------------------------------------------------------- */

           // CARGAR DETALLE DE TRANSFERENCIA DET 

           foreach ($conteo_det as $key => $value) {
                $stock = Stock::obtener_stock($value->COD_PROD);
                
                
               /* if ((float)$stock['stock'] === (float)$value->STOCK) {*/
                    //var_dump("PRODUCTO ".(float)$value->COD_PROD." stock ".(float)$stock['stock']. " CONTEO".$value->CONTEO);
                    //return;

                    if ((float)$value->STOCK > (float)$value->CONTEO) {
                        $lote = Stock::restar_stock_producto($value->COD_PROD, ((float)$value->STOCK - (float)$value->CONTEO));
                        foreach ($lote["datos"] as $key => $valor) {
                            Lote_tiene_ConteoDet::guardar_referencia($value->ID, $valor["id"] , 2, $user->id, $valor["cantidad"]);
                        }
                    } else if ((float)$value->STOCK < (float)$value->CONTEO) {
                        $costo_proveedor=Stock::Select(DB::raw('IFNULL((sum(lotes.costo)/count(lotes.id)),0) AS COSTO,
                        IFNULL((SELECT l1.FK_PROVEEDOR FROM LOTES AS l1 where l1.COD_PROD=lotes.COD_PROD and l1.id_sucursal=lotes.id_sucursal ORDER BY l1.id DESC LIMIT 1),0) as PROVEEDOR'))
                        ->where('ID_SUCURSAL','=',$user->id_sucursal)
                        ->where('lotes.costo','<>',0)
                        ->where('LOTES.COD_PROD','=',$value->COD_PROD)
                        ->get();

                        $lote = Stock::insetar_lote($value->COD_PROD, ((float)$value->CONTEO - (float)$value->STOCK), $costo_proveedor[0]->COSTO, 5, 'INV-'.$id, 'N/A',$costo_proveedor[0]->PROVEEDOR);
                        Lote_tiene_ConteoDet::guardar_referencia($value->ID, $lote["id"] , 1, $user->id, ((float)$value->CONTEO - (float)$value->STOCK) );
                    }
            /*    }*/
           }

           /*  --------------------------------------------------------------------------------- */

           DB::connection('retail')->table('conteo')->where(['ID' => $id, 'ID_SUCURSAL' => $user->id_sucursal])
            ->update(['ESTATUS' => 2]);

           /*  --------------------------------------------------------------------------------- */

           // 

           DB::connection('retail')->commit();

           /*  --------------------------------------------------------------------------------- */

           // RETORNAR VALOR 

           return ["response" => true];

           /*  --------------------------------------------------------------------------------- */

        } catch (Exception $e) {

            DB::connection('retail')->rollBack();
            throw $e;

        }   
    }
        public static function generar_Reporte_Inventario_Seccion($datos) 
    {

        
         /*  --------------------------------------------------------------------------------- */

         // INCICIAR VARIABLES 
        
            $insert=$datos["data"]["Insert"];
            $marcas[] = array();
            $categorias[] = array();
            $totales[] = array();
            $secciones_array=array();
            $secciones_totales_array=array();
            $secciones_productos_array=array();
            $user = auth()->user();
            $user=$user->id;
            $inicio = date('Y-m-d', strtotime($datos["data"]['Inicio']));
            $final = date('Y-m-d', strtotime($datos["data"]['Final']));
            $sucursal = $datos["data"]['Sucursal'];
            $total_general=0;
            $total_descuento=0;
            $total_preciounit=0;
            $cantidadvendida=0;
            $costo=0;
            $totalcosto=0;
            $total_porcentaje_cantidad_sobrante=0;
            $total_porcentaje_cantidad_perdida=0;
            $total_porcentaje_costo_sobrante=0;
            $total_porcentaje_costo_perdida=0;
            $temp_seccion_gondola=[];

            if($insert==true){
                 $data=array(
                    'inicio'=>date('Y-m-d', strtotime($datos["data"]["Inicio"])),
                    'final'=>date('Y-m-d', strtotime($datos["data"]["Final"])),
                    'sucursal'=>$datos["data"]["Sucursal"],
                    'checkedProveedor'=>$datos["data"]["AllProveedores"],
                    'checkedSeccion'=>$datos["data"]["AllSecciones"],
                    'proveedores'=>$datos["data"]["Proveedores"],
                    'secciones'=>$datos["data"]["secciones"],
                    'gondolas'=>$datos["data"]["gondolas"],
                    'checkedGondola'=>$datos["data"]["AllGondolas"]
                 );
                    
                 Temp_venta::insertar_reporte_Inventario_Seccion($data);
               

            }
            $temp_seccion_gondola=DB::connection('retail')->table('temp_ventas')
                
                     ->select(
                         DB::raw('IFNULL(temp_ventas.GONDOLA,0) AS GONDOLA,
                          IFNULL(temp_ventas.GONDOLA_NOMBRE,"INDEFINIDO") AS GONDOLA_NOMBRE,
                          CONCAT(IFNULL(temp_ventas.SECCION,"INDEFINIDO")," ", IFNULL(temp_ventas.GONDOLA_NOMBRE,"INDEFINIDO")) AS DESCRIPCION,
                          temp_ventas.SECCION_CODIGO AS SECCION_CODIGO,
                         IFNULL((SELECT SUM(t.vendido) FROM temp_ventas AS t WHERE t.ID_SUCURSAL = temp_ventas.ID_SUCURSAL AND t.CREDITO_COBRADO =1 and t.USER_ID=temp_ventas.USER_ID and  t.SECCION_CODIGO=temp_ventas.SECCION_CODIGO AND t.GONDOLA=temp_ventas.GONDOLA ), 0) AS CANTIDAD_PERDIDA,
                        IFNULL((SELECT SUM(t.vendido) FROM temp_ventas AS t WHERE t.ID_SUCURSAL = temp_ventas.ID_SUCURSAL AND t.CREDITO_COBRADO =0 and t.USER_ID=temp_ventas.USER_ID and  t.SECCION_CODIGO=temp_ventas.SECCION_CODIGO AND t.GONDOLA=temp_ventas.GONDOLA ), 0) AS CANTIDAD_SOBRANTE,
                        IFNULL((SELECT SUM(t.COSTO_TOTAL) FROM temp_ventas AS t WHERE t.ID_SUCURSAL = temp_ventas.ID_SUCURSAL AND t.CREDITO_COBRADO =1 and t.USER_ID=temp_ventas.USER_ID and  t.SECCION_CODIGO=temp_ventas.SECCION_CODIGO AND t.GONDOLA=temp_ventas.GONDOLA), 0) AS COSTO_PERDIDA ,
                        IFNULL((SELECT SUM(t.COSTO_TOTAL) FROM temp_ventas AS t WHERE t.ID_SUCURSAL = temp_ventas.ID_SUCURSAL AND t.CREDITO_COBRADO =0 and t.USER_ID=temp_ventas.USER_ID and  t.SECCION_CODIGO=temp_ventas.SECCION_CODIGO AND t.GONDOLA=temp_ventas.GONDOLA), 0) AS COSTO_SOBRANTE,
                          IFNULL(temp_ventas.SECCION,"INDEFINIDO") AS SECCION_NOMBRE'))
                       ->where('USER_ID','=',$user)
                       ->where('ID_SUCURSAL','=',$sucursal)
                       ->GROUPBY('temp_ventas.SECCION_CODIGO','temp_ventas.GONDOLA') 
                       ->orderby('temp_ventas.SECCION','ASC')
                       ->orderby('temp_ventas.GONDOLA_NOMBRE','ASC')
                            
                ->get()
                ->toArray();
               
            if($datos["data"]['Agrupado']==='1'){
                $temp=$temp_seccion_gondola;

            }else{
                 $temp=DB::connection('retail')->table('temp_ventas')
                
                     ->select(
                         DB::raw('IFNULL(temp_ventas.GONDOLA,0) AS GONDOLA,
                          IFNULL(temp_ventas.GONDOLA_NOMBRE,"INDEFINIDO") AS GONDOLA_NOMBRE,
                          IFNULL(temp_ventas.SECCION,"INDEFINIDO") AS DESCRIPCION,
                          IFNULL(temp_ventas.SECCION_CODIGO,0) AS SECCION_CODIGO,
                         IFNULL((SELECT SUM(t.vendido) FROM temp_ventas AS t WHERE t.ID_SUCURSAL = temp_ventas.ID_SUCURSAL AND t.CREDITO_COBRADO =1 and t.USER_ID=temp_ventas.USER_ID and  t.SECCION_CODIGO=temp_ventas.SECCION_CODIGO ), 0) AS CANTIDAD_PERDIDA,
                        IFNULL((SELECT SUM(t.vendido) FROM temp_ventas AS t WHERE t.ID_SUCURSAL = temp_ventas.ID_SUCURSAL AND t.CREDITO_COBRADO =0 and t.USER_ID=temp_ventas.USER_ID and  t.SECCION_CODIGO=temp_ventas.SECCION_CODIGO ), 0) AS CANTIDAD_SOBRANTE,
                        IFNULL((SELECT SUM(t.COSTO_TOTAL) FROM temp_ventas AS t WHERE t.ID_SUCURSAL = temp_ventas.ID_SUCURSAL AND t.CREDITO_COBRADO =1 and t.USER_ID=temp_ventas.USER_ID and  t.SECCION_CODIGO=temp_ventas.SECCION_CODIGO ), 0) AS COSTO_PERDIDA ,
                        IFNULL((SELECT SUM(t.COSTO_TOTAL) FROM temp_ventas AS t WHERE t.ID_SUCURSAL = temp_ventas.ID_SUCURSAL AND t.CREDITO_COBRADO =0 and t.USER_ID=temp_ventas.USER_ID and  t.SECCION_CODIGO=temp_ventas.SECCION_CODIGO ), 0) AS COSTO_SOBRANTE,
                          IFNULL(temp_ventas.SECCION,"INDEFINIDO") AS SECCION_NOMBRE'))
                       ->where('USER_ID','=',$user)
                       ->where('ID_SUCURSAL','=',$sucursal)
                       ->GROUPBY('temp_ventas.SECCION_CODIGO') 
                       ->orderby('temp_ventas.SECCION','ASC')
                            
                ->get()
                ->toArray();
            }
            

           $TOTALG=DB::connection('retail')->table('temp_ventas')
              ->select(
               DB::raw(  'IFNULL((SELECT SUM(t.vendido) FROM temp_ventas as t where t.ID_SUCURSAL=temp_ventas.ID_SUCURSAL and t.USER_ID=temp_ventas.user_id and t.CREDITO_COBRADO=1),0) as CANTIDAD_PERDIDA,
                          IFNULL((SELECT SUM(t.vendido) FROM temp_ventas as t where t.ID_SUCURSAL=temp_ventas.ID_SUCURSAL and t.USER_ID=temp_ventas.user_id and t.CREDITO_COBRADO=0),0) as CANTIDAD_SOBRANTE,
                          IFNULL((SELECT SUM(t.COSTO_TOTAL) FROM temp_ventas as t where t.ID_SUCURSAL=temp_ventas.ID_SUCURSAL and t.USER_ID=temp_ventas.user_id and t.CREDITO_COBRADO=1),0) as COSTO_PERDIDA,
                          IFNULL((SELECT SUM(t.COSTO_TOTAL) FROM temp_ventas as t where t.ID_SUCURSAL=temp_ventas.ID_SUCURSAL and t.USER_ID=temp_ventas.user_id and t.CREDITO_COBRADO=0),0) as COSTO_SOBRANTE'))
              ->where('USER_ID','=',$user)
              ->where('ID_SUCURSAL','=',$sucursal)
              ->limit(1)
            ->get()
            ->toArray();

                          

            $total_porcentaje_costo_perdida=$TOTALG[0]->COSTO_PERDIDA;
            $total_porcentaje_costo_sobrante=$TOTALG[0]->COSTO_SOBRANTE;
            $total_porcentaje_cantidad_perdida=$TOTALG[0]->CANTIDAD_PERDIDA;
            $total_porcentaje_cantidad_sobrante=$TOTALG[0]->CANTIDAD_SOBRANTE;

            foreach ($temp as $key => $value) {

                             $secciones_array[]=array(
                                'TOTALES'=> $value->DESCRIPCION,
                                'CANTIDAD_PERDIDA'=> intval($value->CANTIDAD_PERDIDA),
                                'CANTIDAD_SOBRANTE'=> intval($value->CANTIDAD_SOBRANTE),
                                'COSTO_PERDIDA'=> $value->COSTO_PERDIDA,
                                'COSTO_SOBRANTE'=>$value->COSTO_SOBRANTE,
                                'SECCIONES'=>$value->SECCION_CODIGO,
                                'GONDOLA'=>$value->GONDOLA,
                                'GONDOLA_NOMBRE'=>$value->GONDOLA_NOMBRE,
                                'SECCION_NOMBRE'=>$value->SECCION_NOMBRE,
                                'PORCENTAJE_CANTIDAD_PERDIDA'=>($value->CANTIDAD_PERDIDA*100)/$total_porcentaje_cantidad_perdida,
                                'PORCENTAJE_CANTIDAD_SOBRANTE'=>($value->CANTIDAD_SOBRANTE*100)/$total_porcentaje_cantidad_sobrante,
                                'PORCENTAJE_COSTO_PERDIDA'=>($value->COSTO_PERDIDA*100)/$total_porcentaje_costo_perdida,
                                'PORCENTAJE_COSTO_SOBRANTE'=>($value->COSTO_SOBRANTE*100)/$total_porcentaje_costo_sobrante
                                
                             );
            }
        
                   
                $temp=DB::connection('retail')->table('temp_ventas')
                 
                   ->select(
                     DB::raw('temp_ventas.COD_PROD AS COD_PROD,
                        
                        SUM(temp_ventas.VENDIDO) AS VENDIDO,
                        IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = temp_ventas.COD_PROD) AND (l.ID_SUCURSAL = temp_ventas.ID_SUCURSAL))),0) AS STOCK,
                        IFNULL((SELECT SUM(t.vendido) FROM temp_ventas as t where t.ID_SUCURSAL=temp_ventas.ID_SUCURSAL and t.USER_ID=temp_ventas.user_id and t.CREDITO_COBRADO=1 and t.COD_PROD=temp_ventas.COD_PROD),0) as CANTIDAD_PERDIDA,
                        IFNULL((SELECT SUM(t.vendido) FROM temp_ventas as t where t.ID_SUCURSAL=temp_ventas.ID_SUCURSAL and t.USER_ID=temp_ventas.user_id and t.CREDITO_COBRADO=0 and t.SECCION_CODIGO=temp_ventas.SECCION_CODIGO and t.COD_PROD=temp_ventas.COD_PROD),0) as CANTIDAD_SOBRANTE,
                        IFNULL((SELECT SUM(t.COSTO_TOTAL) FROM temp_ventas as t where t.ID_SUCURSAL=temp_ventas.ID_SUCURSAL and t.USER_ID=temp_ventas.user_id and t.CREDITO_COBRADO=1 and t.SECCION_CODIGO=temp_ventas.SECCION_CODIGO and t.COD_PROD=temp_ventas.COD_PROD),0) as COSTO_PERDIDA,
                        IFNULL((SELECT SUM(t.COSTO_TOTAL) FROM temp_ventas as t where t.ID_SUCURSAL=temp_ventas.ID_SUCURSAL and t.USER_ID=temp_ventas.user_id and t.CREDITO_COBRADO=0 and t.SECCION_CODIGO=temp_ventas.SECCION_CODIGO and t.COD_PROD=temp_ventas.COD_PROD),0) as COSTO_SOBRANTE,
                        temp_ventas.CATEGORIA AS CATEGORIA,
                        IFNULL(temp_ventas.LINEA_CODIGO,0) AS LINEA_CODIGO,
                        temp_ventas.SUBCATEGORIA AS SUBCATEGORIA,
                        temp_ventas.GONDOLA AS GONDOLA,
                        temp_ventas.SECCION AS SECCION,
                        temp_ventas.SECCION_CODIGO AS SECCION_CODIGO,
                        Temp_ventas.PROVEEDOR AS PROVEEDOR'))
                  ->where('temp_ventas.ID_SUCURSAL','=',$sucursal)
                  ->where('temp_ventas.USER_ID','=',$user)
                  ->GROUPBY('temp_ventas.COD_PROD','Temp_ventas.SECCION_CODIGO') 
                  ->orderby('COD_PROD')
                ->get()
                ->toArray();
                $total_general=0;
                $total_descuento=0;
                $total_preciounit=0;
                $cantidadvendida=0;
                $costo=0;
                $totalcosto=0;
           
                foreach ($temp as $key => $value) {

                        
                    
                      
                        $secciones_productos_array[]=array(
                             
                                'COD_PROD'=> $value->COD_PROD,
                               
                                'STOCK'=> $value->STOCK,
                                'CATEGORIA'=> $value->CATEGORIA,
                                'SUBCATEGORIA'=> $value->SUBCATEGORIA,
                                'CANTIDAD_PERDIDA'=> $value->CANTIDAD_PERDIDA,
                                'CANTIDAD_SOBRANTE'=>$value->CANTIDAD_SOBRANTE,
                                'COSTO_PERDIDA'=>$value->COSTO_PERDIDA,
                                'COSTO_SOBRANTE'=>$value->COSTO_SOBRANTE,
                                'GONDOLA_CODIGO'=> $value->GONDOLA,
                                'SECCION'=> $value->SECCION,
                                'SECCION_CODIGO'=>$value->SECCION_CODIGO
                        );
                  }
                $seccion_total=DB::connection('retail')->table('temp_ventas')
                
                     ->select(
                       DB::raw('temp_ventas.SECCION AS DESCRIPCION,
                          temp_ventas.SECCION_CODIGO AS SECCION_CODIGO,
                          IFNULL((SELECT SUM(t.vendido) FROM temp_ventas as t where t.ID_SUCURSAL=temp_ventas.ID_SUCURSAL and t.USER_ID=temp_ventas.user_id and t.CREDITO_COBRADO=0 and t.SECCION_CODIGO=temp_ventas.SECCION_CODIGO),0) as CANTIDAD_PERDIDA,
                          IFNULL((SELECT SUM(t.vendido) FROM temp_ventas as t where t.ID_SUCURSAL=temp_ventas.ID_SUCURSAL and t.USER_ID=temp_ventas.user_id and t.CREDITO_COBRADO=1 and t.SECCION_CODIGO=temp_ventas.SECCION_CODIGO),0) as CANTIDAD_SOBRANTE,
                          IFNULL((SELECT SUM(t.COSTO_TOTAL) FROM temp_ventas as t where t.ID_SUCURSAL=temp_ventas.ID_SUCURSAL and t.USER_ID=temp_ventas.user_id and t.CREDITO_COBRADO=0 and t.SECCION_CODIGO=temp_ventas.SECCION_CODIGO),0) as COSTO_PERDIDA,
                          IFNULL((SELECT SUM(t.COSTO_TOTAL) FROM temp_ventas as t where t.ID_SUCURSAL=temp_ventas.ID_SUCURSAL and t.USER_ID=temp_ventas.user_id and t.CREDITO_COBRADO=1 and t.SECCION_CODIGO=temp_ventas.SECCION_CODIGO),0) as COSTO_SOBRANTE'))
                        ->where('USER_ID','=',$user)
                        ->where('ID_SUCURSAL','=',$sucursal)
                        ->GROUPBY('temp_ventas.SECCION_CODIGO') 
                        ->orderby('temp_ventas.SECCION','ASC')
                   
                ->get()
                ->toArray();
                foreach ($seccion_total as $key => $value) {

                              
                               

                                  $secciones_totales_array[]=array(
                                    'TOTALES'=> $value->DESCRIPCION,
                                    'SECCIONES'=>$value->SECCION_CODIGO,
                                    'CANTIDAD_PERDIDA'=> $value->CANTIDAD_PERDIDA,
                                    'CANTIDAD_SOBRANTE'=> $value->CANTIDAD_SOBRANTE,
                                    'COSTO_PERDIDA'=> $value->COSTO_PERDIDA,
                                    'COSTO_SOBRANTE'=>$value->COSTO_SOBRANTE,
                                    'PORCENTAJE_CANTIDAD_PERDIDA'=>($value->CANTIDAD_PERDIDA*100)/$total_porcentaje_cantidad_perdida,
                                    'PORCENTAJE_CANTIDAD_SOBRANTE'=>($value->CANTIDAD_SOBRANTE*100)/$total_porcentaje_cantidad_sobrante,
                                    'PORCENTAJE_COSTO_PERDIDA'=>($value->COSTO_PERDIDA*100)/$total_porcentaje_costo_perdida,
                                    'PORCENTAJE_COSTO_SOBRANTE'=>($value->COSTO_SOBRANTE*100)/$total_porcentaje_costo_sobrante
                                );
                            # code...
                }

                  



                 
 
            /*  --------------------------------------------------------------------------------- */


            /*  --------------------------------------------------------------------------------- */



            /*  --------------------------------------------------------------------------------- */

            // RETORNAR TODOS LOS ARRAYS


            return ['productos' => $secciones_productos_array, 'secciones' => $secciones_array,'secciones_totales'=>$temp_seccion_gondola];

            /*  --------------------------------------------------------------------------------- */
    } 
}
