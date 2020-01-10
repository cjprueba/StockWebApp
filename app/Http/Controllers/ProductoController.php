<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Producto;
use Illuminate\Support\Facades\DB;
use App\Common;
use App\ProductosAux;
use App\Imagen;

class ProductoController extends Controller
{
	/*  --------------------------------------------------------------------------------- */

	// INICIAR LAS VARIABLES GLOBALES

	private $search = '';
    
    /*  --------------------------------------------------------------------------------- */

    public function mostrar(Request $request)
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

            $posts = ProductosAux::select(DB::raw('PRODUCTOS_AUX.CODIGO, PRODUCTOS.DESCRIPCION, PRODUCTOS_AUX.PREC_VENTA, PRODUCTOS_AUX.PRECOSTO, PRODUCTOS_AUX.PREMAYORISTA, MONEDAS.CANDEC, PRODUCTOS.IMPUESTO AS IVA, PRODUCTOS_AUX.MONEDA'),
        			 DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS STOCK'))
            			 ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
                         ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'PRODUCTOS_AUX.MONEDA')
            			 ->where('PRODUCTOS_AUX.ID_SUCURSAL','=', $user->id_sucursal)
            			 ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();

            /*  ************************************************************ */

        } else {

        	/*  ************************************************************ */

        	// CARGAR EL VALOR A BUSCAR 

            $this->search = $request->input('search.value'); 

            /*  ************************************************************ */

            // CARGAR LOS PRODUCTOS FILTRADOS EN DATATABLE

            $posts =  ProductosAux::select(DB::raw('PRODUCTOS_AUX.CODIGO, PRODUCTOS.DESCRIPCION, PRODUCTOS_AUX.PREC_VENTA, PRODUCTOS_AUX.PRECOSTO, PRODUCTOS_AUX.PREMAYORISTA, MONEDAS.CANDEC, PRODUCTOS.IMPUESTO AS IVA, PRODUCTOS_AUX.MONEDA'),
        			 DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS STOCK'))
            			 ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
                         ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'PRODUCTOS_AUX.MONEDA')
            				->where('PRODUCTOS_AUX.ID_SUCURSAL','=', $user->id_sucursal)
            				->where(function ($query) {
				                $query->where('PRODUCTOS_AUX.CODIGO','LIKE',"%{$this->search}%")
				                      ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"%{$this->search}%");
				            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = ProductosAux::leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
            				 ->where('PRODUCTOS_AUX.ID_SUCURSAL','=', $user->id_sucursal)	
            				 ->where(function ($query) {
				                $query->where('PRODUCTOS_AUX.CODIGO','LIKE',"%{$this->search}%")
				                      ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"%{$this->search}%");
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

            	$imagen = Imagen::select(DB::raw('PICTURE'))
		        ->where('COD_PROD','=', $post->CODIGO)
		        ->get();

            	/*  --------------------------------------------------------------------------------- */

            	// CARGAR EN LA VARIABLE 

                $nestedData['CODIGO'] = $post->CODIGO;
                $nestedData['DESCRIPCION'] = $post->DESCRIPCION;
                $nestedData['PREC_VENTA'] = Common::formato_precio($post->PREC_VENTA, $post->CANDEC);
                $nestedData['PRECOSTO'] = Common::formato_precio($post->PRECOSTO, $post->CANDEC);
                $nestedData['PREMAYORISTA'] = Common::formato_precio($post->PREMAYORISTA, $post->CANDEC);
                $nestedData['IVA'] = $post->IVA;
                $nestedData['STOCK'] = Common::formato_precio($post->STOCK, 0);
                $nestedData['IMAGEN'] = "<img src='' class='img-thumbnail previsualizar width='50px' alt=''>";
                foreach ($imagen as $key => $image) {
	                $nestedData['IMAGEN'] = "<img src='data:image/jpg;base64,".base64_encode($image->PICTURE)."' class='img-thumbnail' style='width:60px;height:60px;'>";
	            }
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

        echo json_encode($json_data); 

        /*  --------------------------------------------------------------------------------- */
    }

     public function encontrar(Request $request)
    {
        if ($request["Opcion"] === 1) {
            $productos = Producto::encontrarProducto($request->all());
            return response()->json($productos);
        } else if ($request["Opcion"] === 2) {
            $productos = Producto::mostrarProductoImagen($request->all());
            return response()->json($productos);
        } else if ($request["Opcion"] === 3) {
            $productos = Producto::filtrarProductos($request->all());
            return response()->json($productos);
        } else if ($request["Opcion"] === 4) {
            $productos = Producto::codigoInterno($request->all());
            return response()->json($productos);
        } 
    }

    public function generarCI()
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER PROVEEDORES

        $producto = Producto::generar_ci();
        return response()->json($producto);

        /*  --------------------------------------------------------------------------------- */

    }

    public function generarCodigo()
    {

        /*  --------------------------------------------------------------------------------- */

        // GENERAR CODIGO PRODUCTO

        $producto = Producto::generar_codigo();
        return response()->json($producto);

        /*  --------------------------------------------------------------------------------- */

    }

     public function guardar(Request $request)
    {
        
        /*  --------------------------------------------------------------------------------- */

        // GUARDAR PRODUCTO 

        $productos = Producto::guardar($request->all());
        return response()->json($productos);
        
        /*  --------------------------------------------------------------------------------- */

    }

     public function obtener(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $productos = Producto::obtener_datos($request->all());
        return response()->json($productos);
        
        /*  --------------------------------------------------------------------------------- */

    }

     public function obtenerProductoCompra(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $productos = Producto::obtener_producto_compra($request->all());
        return response()->json($productos);
        
        /*  --------------------------------------------------------------------------------- */

    }
}
