<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Mpdf\Mpdf;
use App\Lote_tiene_ConteoDet;
use App\Ventas_det;
use Illuminate\Support\Facades\Log;

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
    	$observacion = $dato["observacion"];
    	$id_sucursal = $dato["sucursal"]; 

    	/*  --------------------------------------------------------------------------------- */

    	// INSERTAR CONTEO

    	$id = DB::connection('retail')
    	->table('conteo')->insertGetId(
		    ['OBSERVACION' => $observacion, 'FECALTAS' => $diaHora, 'ID_SUCURSAL' => $id_sucursal, 'FK_USER' => $user->id, 'FECMODIF' => $diaHora]
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
                            2 => 'SUCURSAL',
                            3 => 'FECALTAS',
                            4 => 'FECMODIF'
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
  			->select(DB::raw('conteo.ID, conteo.OBSERVACION, conteo.ID_SUCURSAL AS SUCURSAL, conteo.FECALTAS, conteo.FECMODIF'))
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
  			->select(DB::raw('conteo.ID, conteo.OBSERVACION, conteo.FECALTAS, conteo.ID_SUCURSAL AS SUCURSAL, conteo.FECMODIF'))
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

                $nestedData['ID'] = $post->ID;
                $nestedData['OBSERVACION'] = $post->OBSERVACION;
                $nestedData['SUCURSAL'] = $post->SUCURSAL;
                $nestedData['FECALTAS'] = $post->FECALTAS;
                $nestedData['FECMODIF'] = $post->FECMODIF;
                $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarInventario' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a>&emsp;<a href='#' id='editarInventario' title='Editar'><i class='fa fa-edit text-warning' aria-hidden='true'></i></a>&emsp;<a href='#' id='eliminarInventario' title='Eliminar'><i class='fa fa-trash text-danger' aria-hidden='true'></i></a>
                    &emsp;<a href='#' id='imprimirInventario' title='Imprimir'><i class='fa fa-print text-primary' aria-hidden='true'></i></a>
                    ";
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
                        'ID,
                        OBSERVACION,
                        conteo.FECALTAS,
                        SUCURSALES.DESCRIPCION AS SUCURSAL'
                    ))
        ->leftjoin('SUCURSALES', 'SUCURSALES.CODIGO', '=', 'CONTEO.ID_SUCURSAL')
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

            // OBTENER LA CANTIDAD QUE SE VENDIO DESPUES DE INICIAR A CONTAR 
                $hora=substr($value->CREATED_AT, 11);
                $fecha = substr($value->CREATED_AT, 0,-8);

            $vendido = Ventas_det::select(DB::raw('IFNULL(SUM(CANTIDAD),0) AS CANTIDAD'))
            ->WhereDate('FECALTAS', '=', $fecha)
            ->where('ANULADO', '=', 0)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            /*->where('HORALTAS', '>=', date("H:i:s",strtotime($hora)))*/
          //->whereRaw('HOUR(HORALTAS)>="'.date("H:i:s",strtotime($hora)).'"')
            ->WHERE('COD_PROD', '=', $value->COD_PROD)
          /*->WhereDate(DB::raw("CONCAT(FECALTAS, ' ', HORALTAS)"), '>=', $value->CREATED_AT) */
            ->groupBy('COD_PROD')
            ->get();

            /*  --------------------------------------------------------------------------------- */
 
            if(count($vendido) > 0) {
                $conteo_det[$key]->VENDIDO = $vendido[0]['CANTIDAD'];
            } else {
                $conteo_det[$key]->VENDIDO = '0';
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
        $data['observacion'] = $observacion;
        $data['fecaltas'] = $fecaltas;
        $data['tipo'] = $tipo;
        $data['articulos'] = $articulos;
        $data['c'] = $c;
        $data['cantidad'] = $cantidad;
        $data['total'] = $total_conteo;
        $data['nombre_sucursal'] = $nombre_sucursal;

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
                
                
                if ((float)$stock['stock'] === (float)$value->STOCK) {
                    //var_dump("PRODUCTO ".(float)$value->COD_PROD." stock ".(float)$stock['stock']. " CONTEO".$value->CONTEO);
                    //return;

                    if ((float)$stock['stock'] > (float)$value->CONTEO) {
                        $lote = Stock::restar_stock_producto($value->COD_PROD, ((float)$stock['stock'] - (float)$value->CONTEO));
                        foreach ($lote["datos"] as $key => $valor) {
                            Lote_tiene_ConteoDet::guardar_referencia($value->ID, $valor["id"] , 2, $user->id, $valor["cantidad"]);
                        }
                    } else if ((float)$stock['stock'] < (float)$value->CONTEO) {
                        $lote = Stock::insetar_lote($value->COD_PROD, ((float)$value->CONTEO - (float)$stock['stock']), 0, 5, 'INV-'.$id, 'N/A');
                        Lote_tiene_ConteoDet::guardar_referencia($value->ID, $lote["id"] , 1, $user->id, ((float)$value->CONTEO - (float)$stock['stock']));
                    }
                }
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
}
