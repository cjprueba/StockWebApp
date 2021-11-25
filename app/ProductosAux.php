<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Imagen;
use App\Common;

class ProductosAux extends Model
{
    //

    protected $connection = 'retail';
	protected $table = 'productos_aux';
	const CREATED_AT = 'FECALTAS';
	const UPDATED_AT = 'FECMODIF';

	public static function mostrar_datatable($request)
    {
    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER LOS DATOS DEL USUARIO LOGUEADO 
		
    	$user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */

    	// CREAR COLUMNA DE ARRAY 

    	$columns = array( 
                            0 =>'CODIGO', 
                            1 =>'DESCRIPCION',
                            2=> 'PREC_VENTA',
                            3=> 'PRECOSTO',
                            4=> 'PREMAYORISTA',
                            5=> 'STOCK',
                            6=> 'IMAGEN',
                        );
  		
  		/*  --------------------------------------------------------------------------------- */

  		// CONTAR LA CANTIDAD DE PRODUCTOS ENCONTRADOS 
		
        $totalData = ProductosAux::where('PRODUCTOS_AUX.ID_SUCURSAL','=', $user->id_sucursal)
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

        	//	CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 
			//$starttime = microtime(true);
            $posts = ProductosAux::select(DB::raw('PRODUCTOS_AUX.CODIGO, PRODUCTOS.DESCRIPCION, PRODUCTOS_AUX.PREC_VENTA, PRODUCTOS_AUX.PRECOSTO, PRODUCTOS_AUX.PREMAYORISTA, MONEDAS.CANDEC, PRODUCTOS.IMPUESTO AS IVA, PRODUCTOS_AUX.MONEDA'),
        			 DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS STOCK'))
            			 ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
                         ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'PRODUCTOS_AUX.MONEDA')
            			 ->where('PRODUCTOS_AUX.ID_SUCURSAL','=', $user->id_sucursal)
            			 ->offset($start)
                         ->limit($limit)
                         //->orderBy($order,$dir)
                         ->get();
			//$endtime = microtime(true);
			//$duration = $endtime - $starttime; 
			//var_dump($duration);
            //return;			
            /*  ************************************************************ */

        } else {

        	/*  ************************************************************ */

        	// CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            /*  ************************************************************ */

            // CARGAR LOS PRODUCTOS FILTRADOS EN DATATABLE
			
			//$posts = DB::select( DB::raw('PRODUCTOS_AUX.CODIGO, PRODUCTOS.DESCRIPCION, PRODUCTOS_AUX.PREC_VENTA, PRODUCTOS_AUX.PRECOSTO, PRODUCTOS_AUX.PREMAYORISTA, MONEDAS.CANDEC, PRODUCTOS.IMPUESTO AS IVA, PRODUCTOS_AUX.MONEDA from (SELECT  productos_aux.codigo FROM    productos_aux ORDER BY productos_aux.'.$order.' '.$dir.' LIMIT   '.$limit.' OFFSET  '.$start.')'), array(
                   //'order' => $order,
                  // 'dir' => $dir,
                   //'limit' => $limit,
                  // 'offset' => $start,
                // ),
        		//	 DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS STOCK'))
				//	 ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
                  //       ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'PRODUCTOS_AUX.MONEDA')
            		//		->where('PRODUCTOS_AUX.ID_SUCURSAL','=', $user->id_sucursal)
            		//		->where(function ($query) use($search) {
				     //           $query->where('PRODUCTOS_AUX.CODIGO','LIKE',"%{$search}%")
				     //                 ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"%{$search}%");
				     //       })
                     //       ->get();
					 
            $posts =  ProductosAux::select(DB::raw('PRODUCTOS_AUX.CODIGO, PRODUCTOS.DESCRIPCION, PRODUCTOS_AUX.PREC_VENTA, PRODUCTOS_AUX.PRECOSTO, PRODUCTOS_AUX.PREMAYORISTA, MONEDAS.CANDEC, PRODUCTOS.IMPUESTO AS IVA, PRODUCTOS_AUX.MONEDA'),
        			 DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS STOCK'))
            			 ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
                         ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'PRODUCTOS_AUX.MONEDA')
            				->where('PRODUCTOS_AUX.ID_SUCURSAL','=', $user->id_sucursal)
            				->where(function ($query) use($search) {
				                $query->where('PRODUCTOS_AUX.CODIGO','LIKE',"%{$search}%")
				                      ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"%{$search}%");
				            })
                            ->offset($start)
                            ->limit($limit)
                            //->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = ProductosAux::leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
            				 ->where('PRODUCTOS_AUX.ID_SUCURSAL','=', $user->id_sucursal)	
            				 ->where(function ($query) use($search) {
				                $query->where('PRODUCTOS_AUX.CODIGO','LIKE',"%{$search}%")
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

            	// BUSCAR IMAGEN

                $imagen = Imagen::obtenerImagenURL($post->CODIGO);

          //   	$imagen = Imagen::select(DB::raw('PICTURE'))
		        // ->where('COD_PROD','=', $post->CODIGO)
		        // ->get();

            	/*  --------------------------------------------------------------------------------- */

            	// CARGAR EN LA VARIABLE 

                $nestedData['CODIGO'] = $post->CODIGO;
                $nestedData['DESCRIPCION'] = $post->DESCRIPCION;
                $nestedData['PREC_VENTA'] = Common::formato_precio($post->PREC_VENTA, $post->CANDEC);
                $nestedData['PRECOSTO'] = Common::formato_precio($post->PRECOSTO, $post->CANDEC);
                $nestedData['PREMAYORISTA'] = Common::formato_precio($post->PREMAYORISTA, $post->CANDEC);
                $nestedData['IVA'] = $post->IVA;
                $nestedData['STOCK'] = Common::formato_precio($post->STOCK, 0);
                $nestedData['IMAGEN'] = $imagen["imagen_3"];

                //    $nestedData['IMAGEN'] = "<img src='' class='img-thumbnail previsualizar width='50px' alt=''>";
                //    foreach ($imagen as $key => $image) {
	            //     $nestedData['IMAGEN'] = "<img src='data:image/jpg;base64,".base64_encode($image->PICTURE)."' class='img-thumbnail' style='width:60px;height:60px;'>";
	            // }
                $nestedData['MONEDA'] = $post->MONEDA;
          		
                $data[] = $nestedData;

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
    public static function mostrar_datatable_desc($request)
    {
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 
        
        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 =>'CODIGO', 
                            1 =>'DESCRIPCION',
                            2=> 'PREC_VENTA',
                            3=> 'PRECOSTO',
                            4=> 'PREMAYORISTA',
                            5=> 'STOCK',
                            6=> 'IMAGEN',
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE PRODUCTOS ENCONTRADOS 
        
        $totalData = ProductosAux::rightjoin('DETALLE_PROD', 'DETALLE_PROD.COD_PROD', '=', 'PRODUCTOS_AUX.CODIGO')->where('PRODUCTOS_AUX.ID_SUCURSAL','=', $user->id_sucursal)
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
            //$starttime = microtime(true);
            $posts = ProductosAux::select(DB::raw('
                PRODUCTOS_AUX.CODIGO, 
                PRODUCTOS.DESCRIPCION, 
                PRODUCTOS_AUX.PREC_VENTA, 
                PRODUCTOS_AUX.PRECOSTO, 
                PRODUCTOS_AUX.PREMAYORISTA, 
                MONEDAS.CANDEC, 
                PRODUCTOS.IMPUESTO AS IVA, 
                PRODUCTOS_AUX.MONEDA,
                DETALLE_PROD.COD_PROD,
                DETALLE_PROD.NOMBRE,
                IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS STOCK'))
                         
                         ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
                         ->rightjoin('DETALLE_PROD', 'DETALLE_PROD.COD_PROD', '=', 'PRODUCTOS_AUX.CODIGO')
                         ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'PRODUCTOS_AUX.MONEDA')
                         ->where('PRODUCTOS_AUX.ID_SUCURSAL','=', $user->id_sucursal)
                         ->where('DETALLE_PROD.COD_PROD','<>','')
                         ->offset($start)
                         ->limit($limit)
                         //->orderBy($order,$dir)
                         ->get();
            //$endtime = microtime(true);
            //$duration = $endtime - $starttime; 
            //var_dump($duration);
            //return;           
            /*  ************************************************************ */

        } else {

            /*  ************************************************************ */

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            /*  ************************************************************ */

            // CARGAR LOS PRODUCTOS FILTRADOS EN DATATABLE
            
            //$posts = DB::select( DB::raw('PRODUCTOS_AUX.CODIGO, PRODUCTOS.DESCRIPCION, PRODUCTOS_AUX.PREC_VENTA, PRODUCTOS_AUX.PRECOSTO, PRODUCTOS_AUX.PREMAYORISTA, MONEDAS.CANDEC, PRODUCTOS.IMPUESTO AS IVA, PRODUCTOS_AUX.MONEDA from (SELECT  productos_aux.codigo FROM    productos_aux ORDER BY productos_aux.'.$order.' '.$dir.' LIMIT   '.$limit.' OFFSET  '.$start.')'), array(
                   //'order' => $order,
                  // 'dir' => $dir,
                   //'limit' => $limit,
                  // 'offset' => $start,
                // ),
                //   DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS STOCK'))
                //   ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
                  //       ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'PRODUCTOS_AUX.MONEDA')
                    //      ->where('PRODUCTOS_AUX.ID_SUCURSAL','=', $user->id_sucursal)
                    //      ->where(function ($query) use($search) {
                     //           $query->where('PRODUCTOS_AUX.CODIGO','LIKE',"%{$search}%")
                     //                 ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"%{$search}%");
                     //       })
                     //       ->get();
                     
            $posts =  ProductosAux::select(DB::raw('PRODUCTOS_AUX.CODIGO, 
                PRODUCTOS.DESCRIPCION, 
                PRODUCTOS_AUX.PREC_VENTA, 
                PRODUCTOS_AUX.PRECOSTO, 
                PRODUCTOS_AUX.PREMAYORISTA, 
                MONEDAS.CANDEC, 
                PRODUCTOS.IMPUESTO AS IVA, 
                PRODUCTOS_AUX.MONEDA
                DETALLE_PROD.COD_PROD,
                DETALLE_PROD.NOMBRE,
                IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS STOCK'))
                         
                        ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
                        ->rightjoin('DETALLE_PROD', 'DETALLE_PROD.COD_PROD', '=', 'PRODUCTOS_AUX.CODIGO')
                        ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'PRODUCTOS_AUX.MONEDA')
                        ->where('PRODUCTOS_AUX.ID_SUCURSAL','=', $user->id_sucursal)
                        ->where(function ($query) use($search) {
                            $query->where('PRODUCTOS_AUX.CODIGO','LIKE',"%{$search}%")
                            ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            //->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = ProductosAux::rightjoin('DETALLE_PROD', 'DETALLE_PROD.COD_PROD', '=', 'PRODUCTOS_AUX.CODIGO')
                            ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
                            ->where('PRODUCTOS_AUX.ID_SUCURSAL','=', $user->id_sucursal)   
                            ->where(function ($query) use($search) {
                                $query->where('PRODUCTOS_AUX.CODIGO','LIKE',"%{$search}%")
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

                // BUSCAR IMAGEN

                $imagen = Imagen::obtenerImagenURL($post->CODIGO);

          //    $imagen = Imagen::select(DB::raw('PICTURE'))
                // ->where('COD_PROD','=', $post->CODIGO)
                // ->get();

                /*  --------------------------------------------------------------------------------- */

                // CARGAR EN LA VARIABLE 

                $nestedData['CODIGO'] = $post->CODIGO;
                $nestedData['DESCRIPCION'] = $post->DESCRIPCION;
                $nestedData['PREC_VENTA'] = Common::formato_precio($post->PREC_VENTA, $post->CANDEC);
                $nestedData['PRECOSTO'] = Common::formato_precio($post->PRECOSTO, $post->CANDEC);
                $nestedData['PREMAYORISTA'] = Common::formato_precio($post->PREMAYORISTA, $post->CANDEC);
                $nestedData['IVA'] = $post->IVA;
                $nestedData['STOCK'] = Common::formato_precio($post->STOCK, 0);
                $nestedData['IMAGEN'] = $imagen["imagen_3"];

                //    $nestedData['IMAGEN'] = "<img src='' class='img-thumbnail previsualizar width='50px' alt=''>";
                //    foreach ($imagen as $key => $image) {
                //     $nestedData['IMAGEN'] = "<img src='data:image/jpg;base64,".base64_encode($image->PICTURE)."' class='img-thumbnail' style='width:60px;height:60px;'>";
                // }
                $nestedData['MONEDA'] = $post->MONEDA;
                $nestedData['NOMBRE'] = $post->NOMBRE;
                
                $data[] = $nestedData;

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
