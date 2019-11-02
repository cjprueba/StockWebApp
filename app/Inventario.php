<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

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

        $producto = DB::connection('retail')
        ->table('PRODUCTOS_AUX')
        ->select(DB::raw('PRODUCTOS_AUX.CODIGO'),
        DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS STOCK'))
        ->where('PRODUCTOS_AUX.CODIGO', '=', $codigo)
        ->where('PRODUCTOS_AUX.ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
        
        if (count($producto) > 0) {
            $stock = $producto[0]->STOCK;
        } else {
            return ["response" => false, "status" => "No existe producto", "codigo" => $codigo];
        }

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE PRODUCTO 

        $existe = DB::connection('retail')
        ->table('conteo_det')
        ->select(DB::raw('STOCK'))
        ->where('COD_PROD', '=', $codigo)
        ->where('FK_CONTEO', '=', $id)
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();

        if (count($existe) > 0) {
            $stock = $existe[0]->STOCK;
        } 

        /*  --------------------------------------------------------------------------------- */

       $insert = DB::connection('retail')
       ->table('conteo_det')
       ->updateOrInsert(
            ['COD_PROD' => $codigo, 'FK_CONTEO' => $id, 'ID_SUCURSAL' => $user->id_sucursal],
            ['CONTEO' => \DB::raw('CONTEO + 1'), 'STOCK' => $stock]
        );

       /*  --------------------------------------------------------------------------------- */

       // RETORNAR VALOR 

       if ($insert === true) {

        $pro = DB::connection('retail')
        ->table('conteo_det')
        ->select(DB::raw('CONTEO'))
        ->where('COD_PROD', '=', $codigo)
        ->where('FK_CONTEO', '=', $id)
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->limit(1)
        ->get();

          return ["response" => true, "status" => "Contado correctamente", "codigo" => $codigo, "cantidad" => $pro[0]->CONTEO." - Stock: ".$stock." - Interno: ".$codigointerno];
       } 
       

       /*  --------------------------------------------------------------------------------- */

    }

    public static function guardar_inventario($dato) {

    	/*  --------------------------------------------------------------------------------- */

    	// INICIAR VARIABLES 

    	$diaHora = date("Y-m-d H:i:s");
    	$observacion = $dato["observacion"];
    	$id_sucursal = $dato["sucursal"]; 

    	/*  --------------------------------------------------------------------------------- */

    	// INSERTAR CONTEO

    	$id = DB::connection('retail')
    	->table('conteo')->insertGetId(
		    ['OBSERVACION' => $observacion, 'FECALTAS' => $diaHora, 'ID_SUCURSAL' => $id_sucursal]
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

  			$posts = Inventario::select(DB::raw('conteo_det.COD_PROD, PRODUCTOS.DESCRIPCION, conteo_det.CONTEO, conteo_det.STOCK'))
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

            $posts =  Inventario::select(DB::raw('conteo_det.COD_PROD, PRODUCTOS.DESCRIPCION, conteo_det.CONTEO, conteo_det.STOCK'))
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
                $nestedData['STOCK'] = $post->STOCK;
                
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
                $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarTransferencia' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a>&emsp;<a href='#' id='editarTransferencia' title='Editar'><i class='fa fa-edit text-warning' aria-hidden='true'></i></a>&emsp;<a href='#' id='eliminarTransferencia' title='Eliminar'><i class='fa fa-trash text-danger' aria-hidden='true'></i></a>
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
}
