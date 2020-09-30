<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\PedidoDet;
use Illuminate\Support\Facades\DB;
use App\Parametro;
use App\Categoria;
use App\Marca;
use App\Proveedor;

class Pedido extends Model
{

	protected $connection = 'retail';
	protected $table = 'pedidos';
    public $timestamps = false;

    public static function agregarProducto($data) {

    	/*  --------------------------------------------------------------------------------- */

    	// INICIAR VARIABLES

    	$user = auth()->user();
    	$datos = $data["data"];
    	$fecha = date("Y-m-d H:i:s");
    	$hora = date("H:i:s");
    	$precio = 0.00;
    	$precio_unit = 0.00;
    	$total = 0.00;
    	$cantidad = (float)$datos["cantidad"];
    	$porcentaje = (float)$datos["descuento"];
    	$linea = $datos["linea"];
    	$marca = $datos["marca"];

    	/*  --------------------------------------------------------------------------------- */

    	// REVISAR CANTDIAD MAYORISTA

    	$calculoPrecio = Common::calculoPrecio($datos["precio_unit"], $datos["precio_mayorista"], $cantidad, $porcentaje, $linea, $marca);
    	$precio = $calculoPrecio["valor"];
    	$precio_unit = $calculoPrecio["precio_unit"];

    	/*  --------------------------------------------------------------------------------- */

    	// REVISAR SI PEDIDO EXISTE 

    	$pedido = Pedido::select(DB::raw('ID'))
    	->where('ESTATUS', '=', 1)
    	->where('ID_SUCURSAL', '=', $user->id_sucursal) 
    	->where('USER_ID', '=', $user->id)
    	->get();

    	if(count($pedido) === 0) {

    		/*  --------------------------------------------------------------------------------- */

    		// OBTENER MONEDA 

    		$parametro = Parametro::consultaPersonalizada('MONEDA');

    		/*  --------------------------------------------------------------------------------- */

    		// INSERTAR PEDIDO 

    		$pedido = Pedido::insertGetId([
	            'DESCUENTO' => 0,
	            'TOTAL' => 0, 
	            'USER_ID' => $user->id, 
	            'CLIENTE_ID' => 1, 
	            'FECALTAS' => $fecha,
	            'HORALTAS' => $hora,
	            'FECMODIF' => $fecha,
	            'HORMODIF' => $hora,
	            'ID_SUCURSAL' => $user->id_sucursal,
	            'ESTATUS' => 1,
	            'FK_MONEDA' => $parametro->MONEDA
	        ]);

    		/*  --------------------------------------------------------------------------------- */

    	} else {
    		$pedido = $pedido[0]->ID;
    	}

    	/*  --------------------------------------------------------------------------------- */

    	$producto = PedidoDet::select(DB::raw('COD_PROD, CANTIDAD'))
    	->where('ID_PEDIDO', '=', $pedido)
    	->where('ID_SUCURSAL', '=', $user->id_sucursal) 
    	->where('USER_ID', '=', $user->id)
    	->where('COD_PROD', '=', $datos["codigo"])
    	->get();

    	/*  --------------------------------------------------------------------------------- */
   
    	if (count($producto) > 0) {

    		/*  --------------------------------------------------------------------------------- */  

    		$calculoPrecio = Common::calculoPrecio($datos["precio_unit"], $datos["precio_mayorista"], ($cantidad + Common::quitar_coma($producto[0]["CANTIDAD"], 0)), $porcentaje, $linea, $marca);
    		$precio = $calculoPrecio["valor"];
    		$precio_unit = $calculoPrecio["precio_unit"];

	    	/*  --------------------------------------------------------------------------------- */

    		// COMPROBAR STOCK 

    		$stock = Common::comprobarCantidadLimiteStock($datos["codigo"], ($cantidad + Common::quitar_coma($producto[0]["CANTIDAD"], 0)));

    		if ($stock["response"] === false) {
    			return $stock;
    		}

    		/*  --------------------------------------------------------------------------------- */

    		// MODIFICAR 

    		PedidoDet::where('COD_PROD', '=', $datos["codigo"])
	        ->where('ID_PEDIDO', '=', $pedido)
    		->where('ID_SUCURSAL', '=', $user->id_sucursal) 
    		->where('USER_ID', '=', $user->id)
    		->update([
	            'COD_PROD' => $datos["codigo"],
	            'CANTIDAD' =>  \DB::raw('CANTIDAD + '.$datos["cantidad"].''), 
	            'TIPO_PRECIO' => $calculoPrecio["tipo"], 
	            'PRECIO_UNIT' => $precio_unit, 
	            'PRECIO' => $precio,
	            'FECMODIF' => $fecha,
	            'HORMODIF' => $hora,
	        ]);

    		/*  --------------------------------------------------------------------------------- */  

    		// MODIFICADA 

    		$id = 'MODIFICADO';

    		/*  --------------------------------------------------------------------------------- */ 

    	} else {

    		/*  --------------------------------------------------------------------------------- */  

    		// COMPROBAR STOCK 

    		$stock = Common::comprobarCantidadLimiteStock($datos["codigo"], $cantidad);

    		if ($stock["response"] === false) {
    			return $stock;
    		}

    		/*  --------------------------------------------------------------------------------- */  

    		// INSERTAR 

	    	$id = PedidoDet::insertGetId([
	    		'ID_PEDIDO' => $pedido,
	            'COD_PROD' => $datos["codigo"],
	            'CANTIDAD' => $datos["cantidad"], 
	            'TIPO_PRECIO' => $calculoPrecio["tipo"], 
	            'PRECIO_UNIT' => $precio_unit, 
	            'PRECIO' => $precio, 
	            'DESCUENTO' => 0, 
	            'USER_ID' => $user->id,
	            'FECALTAS' => $fecha,
	            'HORALTAS' => $hora,
	            'FECMODIF' => $fecha,
	            'HORMODIF' => $hora,
	            'ID_SUCURSAL' => $user->id_sucursal
	        ]);

	        /*  --------------------------------------------------------------------------------- */   

    	}

    	/*  --------------------------------------------------------------------------------- */

    	// CONTAR CANTIDAD DE PRODUCTOS 

    	$total = Pedido::totales($pedido);

    	/*  --------------------------------------------------------------------------------- */          

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        if (!empty($id)) {
        	return ["response" => true, "id" => $data, "conteo" => $total["conteo"], "total" => $total["total"]];
        } else {
        	return ["response" => false];
        }

    	/*  --------------------------------------------------------------------------------- */

    }

    public static function obtenerProductos($datos)
    {
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
        $start = $datos["datos"]["offset"];
        $json_data = 0;

        /*  --------------------------------------------------------------------------------- */

        $posts = PedidoDet::select(DB::raw('PEDIDOS_DET.COD_PROD AS CODIGO, PRODUCTOS.DESCRIPCION, PEDIDOS_DET.CANTIDAD, LINEAS.DESCRIPCION AS CATEGORIA, PEDIDOS_DET.PRECIO_UNIT AS PREC_VENTA, PEDIDOS_DET.PRECIO, MONEDAS.CANDEC, PRODUCTOS_AUX.MONEDA'))
            			 ->leftjoin('PEDIDOS', 'PEDIDOS.ID', '=', 'PEDIDOS_DET.ID_PEDIDO')
                         ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PEDIDOS_DET.COD_PROD')
                         ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
                         ->leftJoin('PRODUCTOS_AUX', function($join){
	                        $join->on('PRODUCTOS_AUX.CODIGO', '=', 'PEDIDOS_DET.COD_PROD')
	                             ->on('PRODUCTOS_AUX.ID_SUCURSAL', '=', 'PEDIDOS_DET.ID_SUCURSAL');
	                     })
                         ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'PRODUCTOS_AUX.MONEDA')
                         ->where('PEDIDOS_DET.ID_SUCURSAL','=', $user->id_sucursal)
                         ->where('PEDIDOS.USER_ID','=', $user->id)
                         ->where('PEDIDOS.ESTATUS', '=', 1)
                         // ->offset($start)
                         // ->limit($limit)
                         ->get();
                         //->orderBy($order,$dir)

        /*  --------------------------------------------------------------------------------- */

        // TOTAL FILTRADO 
        
        $totalFiltered = PedidoDet::leftjoin('PEDIDOS', 'PEDIDOS.ID', '=', 'PEDIDOS_DET.ID_PEDIDO')
                         ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PEDIDOS_DET.COD_PROD')
                         ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
                         ->where('PEDIDOS_DET.ID_SUCURSAL','=', $user->id_sucursal)
                         ->where('PEDIDOS.USER_ID','=', $user->id)
                         ->where('PEDIDOS.ESTATUS', '=', 1)
                         ->count();

        /*  --------------------------------------------------------------------------------- */

        $data = array();

        /*  --------------------------------------------------------------------------------- */

        // CONVERT IMAGE DEFAULT TO BLOB 

        $path = '../storage/app/imagenes/product.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $dataDefaultImage = file_get_contents($path);

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
                $nestedData['CANTIDAD'] = $post->CANTIDAD;
                $nestedData['CATEGORIA'] = $post->CATEGORIA;
                $nestedData['PREC_VENTA'] = Common::precio_candec($post->PREC_VENTA, $post->MONEDA);
                $nestedData['PRECIO'] = Common::precio_candec($post->PRECIO, $post->MONEDA);
                $nestedData['STOCK'] = Common::formato_precio($post->STOCK, 0);
                $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarDetalle' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a> &emsp;<a href='#' id='editarProducto' title='Editar'><i class='fa fa-edit text-warning' aria-hidden='true'></i></a>
                    &emsp;<a href='#' id='eliminarProducto' title='Eliminar'><i class='fa fa-trash text-danger' aria-hidden='true'></i></a>";

                /*  --------------------------------------------------------------------------------- */

                // SI NO HAY IMAGEN CARGAR IMAGEN DEFAULT 

                if (count($imagen) > 0) {
                   foreach ($imagen as $key => $image) {
                        $imagen_producto = $image->PICTURE;
                    }
                } else {
                    $imagen_producto = $dataDefaultImage;
                }

                /*  --------------------------------------------------------------------------------- */

                $nestedData['IMAGEN'] = "<img src='data:image/jpg;base64,".base64_encode($imagen_producto)."' alt='' width='70' class='img-fluid rounded shadow-sm'>";

                /*  --------------------------------------------------------------------------------- */

                
                $data[] = $nestedData;

            }
        }
        
        /*  --------------------------------------------------------------------------------- */

        // TOTAL

        $total = PedidoDet::select(DB::raw('IFNULL(SUM(PEDIDOS_DET.PRECIO),0) AS TOTAL, IFNULL(SUM(PEDIDOS_DET.CANTIDAD),0) AS CANTIDAD, COUNT(PEDIDOS_DET.COD_PROD) AS CANTIDAD_ITEMS'))
        ->leftjoin('PEDIDOS', 'PEDIDOS.ID', '=', 'PEDIDOS_DET.ID_PEDIDO')
        ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PEDIDOS_DET.COD_PROD')
        ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
        ->leftJoin('PRODUCTOS_AUX', function($join){
	                        $join->on('PRODUCTOS_AUX.CODIGO', '=', 'PEDIDOS_DET.COD_PROD')
	                             ->on('PRODUCTOS_AUX.ID_SUCURSAL', '=', 'PEDIDOS_DET.ID_SUCURSAL');
	                     })
        ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'PRODUCTOS_AUX.MONEDA')
        ->where('PEDIDOS_DET.ID_SUCURSAL','=', $user->id_sucursal)
        ->where('PEDIDOS.USER_ID','=', $user->id)
        ->where('PEDIDOS.ESTATUS', '=', 1)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        $datos_pedido = Pedido::select(DB::raw('FK_MONEDA'))
    	->where('ESTATUS', '=', 1)
    	->where('ID_SUCURSAL', '=', $user->id_sucursal) 
    	->where('USER_ID', '=', $user->id)
    	->get();

    	/*  --------------------------------------------------------------------------------- */

        // PREPARAR EL ARRAY A ENVIAR 

    	if (count($posts) > 0) {

        	$json_data = array(
                    //"draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval(12),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data,
                    "offset"          => $start,
                    "total"			  => Common::precio_candec($total[0]["TOTAL"], $datos_pedido[0]['FK_MONEDA']),
                    "cantidad"	      => $total[0]["CANTIDAD"],
                    "cantidad_items"  => $total[0]["CANTIDAD_ITEMS"],
            );
        
        } else {

        	$json_data = array(
                    //"draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval(12),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data,
                    "offset"          => $start,
                    "total"			  => 0,
                    "cantidad"	      => 0,
                    "cantidad_items"  => 0,
            );

        }

        /*  --------------------------------------------------------------------------------- */

        // CONVERTIR EN JSON EL ARRAY Y ENVIAR 

        return $json_data; 

        /*  --------------------------------------------------------------------------------- */
    }

    public static function confirmar($datos) {

    	/*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */

    	// INICIAR VARIABLES 

    	$fecha = date("Y-m-d H:i:s");
    	$hora = date("H:i:s");
    	$datos = $datos["data"];

    	/*  --------------------------------------------------------------------------------- */

    	// TOTAL

        $total = PedidoDet::select(DB::raw('IFNULL(SUM(PEDIDOS_DET.PRECIO),0) AS TOTAL, IFNULL(SUM(PEDIDOS_DET.CANTIDAD),0) AS CANTIDAD, COUNT(PEDIDOS_DET.COD_PROD) AS CANTIDAD_ITEMS'))
        ->leftjoin('PEDIDOS', 'PEDIDOS.ID', '=', 'PEDIDOS_DET.ID_PEDIDO')
        ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PEDIDOS_DET.COD_PROD')
        ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
        ->leftJoin('PRODUCTOS_AUX', function($join){
	                        $join->on('PRODUCTOS_AUX.CODIGO', '=', 'PEDIDOS_DET.COD_PROD')
	                             ->on('PRODUCTOS_AUX.ID_SUCURSAL', '=', 'PEDIDOS_DET.ID_SUCURSAL');
	                     })
        ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'PRODUCTOS_AUX.MONEDA')
        ->where('PEDIDOS_DET.ID_SUCURSAL','=', $user->id_sucursal)
        ->where('PEDIDOS.USER_ID','=', $user->id)
        ->where('PEDIDOS.ESTATUS', '=', 1)
        ->get();

    	/*  --------------------------------------------------------------------------------- */

    	// MODIFICAR 

    	Pedido::where('ESTATUS', '=', 1)
    	->where('ID_SUCURSAL', '=', $user->id_sucursal) 
    	->where('USER_ID', '=', $user->id)
    	->update([
    		'CLIENTE_ID' => $datos["codigocliente"],
	        'TOTAL' => $total[0]["TOTAL"],
	        'FECMODIF' => $fecha,
	        'HORMODIF' => $hora,
	        'ESTATUS' => 2,
	        'OBSERVACION' => $datos["observacion"]
	    ]);

    	/*  --------------------------------------------------------------------------------- */ 

    	return ["response" => true]; 

    	/*  --------------------------------------------------------------------------------- */

    }

    public static function totales($pedido) {

    	/*  --------------------------------------------------------------------------------- */

    	// INICIAR VARIABLES

    	$user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */

    	$datos_pedido = Pedido::select(DB::raw('FK_MONEDA'))
    	->where('ID', '=', $pedido)
    	->where('ID_SUCURSAL', '=', $user->id_sucursal) 
    	->where('USER_ID', '=', $user->id)
    	->get();

    	/*  --------------------------------------------------------------------------------- */

    	$general = PedidoDet::select(DB::raw('COUNT(COD_PROD) AS CONTEO, IFNULL(SUM(PRECIO), 0) AS TOTAL'))
    	->where('ID_PEDIDO', '=', $pedido)
    	->where('ID_SUCURSAL', '=', $user->id_sucursal) 
    	->where('USER_ID', '=', $user->id)
    	->get();

    	/*  --------------------------------------------------------------------------------- */

    	return ["response" => true, "conteo" => $general[0]["CONTEO"], "total" =>  Common::precio_candec($general[0]["TOTAL"], $datos_pedido[0]["FK_MONEDA"])];

    	/*  --------------------------------------------------------------------------------- */

    }

    public static function inicio_mostrar_new(){

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

    	$user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */

        // OBTENER TODAS LAS CATEGORIAS

        $categorias = Categoria::select(DB::raw('CODIGO, DESCRIPCION'))
        ->orderBy('DESCRIPCION', 'ASC')
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODAS LAS CATEGORIAS
        
        $marcas = Marca::select(DB::raw('CODIGO, DESCRIPCION'))
        ->orderBy('DESCRIPCION', 'ASC')
        ->get();
      
        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS PROVEEDORES 

        $proveedores = Proveedor::select(DB::raw('CODIGO, NOMBRE AS DESCRIPCION'))
        ->orderBy('NOMBRE', 'ASC')
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // PEDIDOS 

        $pedido = Pedido::select(DB::raw('ID'))
    	->where('ESTATUS', '=', 1)
    	->where('ID_SUCURSAL', '=', $user->id_sucursal) 
    	->where('USER_ID', '=', $user->id)
    	->get();

        /*  --------------------------------------------------------------------------------- */

        // TOTAL 
		
		if(count($pedido) > 0) {
			$pedido = Pedido::totales($pedido[0]['ID']);
		} else {
			$pedido["conteo"] = 0;
			$pedido["total"] = 0;
		}
		
        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($categorias) {
            return ['categorias' => $categorias, 'marcas' => $marcas, 'proveedores' => $proveedores, "conteo" => $pedido["conteo"], "total" => $pedido["total"]];
        } else {
            return ['categorias' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function cambiar_cantidad($datos) {

    	/*  --------------------------------------------------------------------------------- */

    	// INICIAR VARIABLES

    	$user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */  

    	// CARGAR VARIABLES 

    	$data = $datos["data"];
    	$fecha = date("Y-m-d H:i:s");
    	$hora = date("H:i:s");

    	/*  --------------------------------------------------------------------------------- */  

    	// REVISAR SI VIENE 0 

    	if (empty($data["cantidad"]) or (int)$data["cantidad"] === 0 or (int)$data["cantidad"] < 1){
    		return ["response" => false, "statusText" => "No puede ser cero o vacio"];
    	}

    	/*  --------------------------------------------------------------------------------- */  

    	$producto = ProductosAux::select(DB::raw('PRODUCTOS_AUX.PREC_VENTA, PRODUCTOS_AUX.PREMAYORISTA, PRODUCTOS_AUX.DESCUENTO AS PORCENTAJE, PRODUCTOS.MARCA, PRODUCTOS.LINEA'))
    					 ->leftjoin('PRODUCTOS', 'PRODUCTOS_AUX.CODIGO', '=', 'PRODUCTOS.CODIGO')
                         ->where('PRODUCTOS_AUX.ID_SUCURSAL','=', $user->id_sucursal)
                         ->where('PRODUCTOS_AUX.CODIGO','=', $data["codigo"])
                         ->get();

        /*  --------------------------------------------------------------------------------- */  
        
        $calculoPrecio = Common::calculoPrecio($producto[0]["PREC_VENTA"], $producto[0]["PREMAYORISTA"], (int)$data["cantidad"], $producto[0]["PORCENTAJE"], $producto[0]["LINEA"], $producto[0]["LINEA"]);
    	$precio = $calculoPrecio["valor"];
    	$precio_unit = $calculoPrecio["precio_unit"];

    	/*  --------------------------------------------------------------------------------- */  

    	// COMPRBAR STOCK 

    	$stock = Common::comprobarCantidadLimiteStock($data["codigo"], (int)$data["cantidad"]);

    	if ($stock["response"] === false) {
    			return $stock;
    	}

    	/*  --------------------------------------------------------------------------------- */

    	// ACTUALIZAR CANTIDAD Y TOTAL

    	PedidoDet::
    	leftjoin('PEDIDOS', 'PEDIDOS.ID', '=', 'PEDIDOS_DET.ID_PEDIDO')
    	->where('COD_PROD', '=', $data["codigo"])
	    ->where('PEDIDOS.ESTATUS', '=', 1)
    	->where('PEDIDOS_DET.ID_SUCURSAL', '=', $user->id_sucursal) 
    	->where('PEDIDOS_DET.USER_ID', '=', $user->id)
    	->update([
	        'PEDIDOS_DET.CANTIDAD' =>  (int)$data["cantidad"], 
	        'PEDIDOS_DET.PRECIO_UNIT' => $precio_unit, 
	        'PEDIDOS_DET.PRECIO' => $precio,
	        'PEDIDOS_DET.FECMODIF' => $fecha,
	        'PEDIDOS_DET.HORMODIF' => $hora,
	    ]);

    	/*  --------------------------------------------------------------------------------- */

    	return ["response" => true];

    	/*  --------------------------------------------------------------------------------- */

    }

    public static function eliminar_producto($data){

    	/*  --------------------------------------------------------------------------------- */

    	// INICIAR VARIABLES

    	$user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */  

    	// INICIAR VARIABLES 

    	$codigo = $data["data"];

    	/*  --------------------------------------------------------------------------------- */

    	// ELIMINAR PRODUCTOS 

    	PedidoDet::leftjoin('PEDIDOS', 'PEDIDOS.ID', '=', 'PEDIDOS_DET.ID_PEDIDO')
    	->where('COD_PROD', '=', $codigo)
        ->where('PEDIDOS.ESTATUS', '=', 1)
    	->where('PEDIDOS_DET.ID_SUCURSAL', '=', $user->id_sucursal) 
    	->where('PEDIDOS_DET.USER_ID', '=', $user->id)
        ->delete();

        /*  --------------------------------------------------------------------------------- */

        return ["response" => true];

    	/*  --------------------------------------------------------------------------------- */

    }

   	public static function mostrar_datatable($request)
    {
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'CODIGO', 
                            1 => 'DESCRIPCION',
                            2 => 'CATEGORIA',
                            3 => 'PREC_VENTA',
                            4 => 'PRECOSTO',
                            5 => 'PREMAYORISTA',
                            6 => 'STOCK',
                            7 => 'IMAGEN',
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE PRODUCTOS ENCONTRADOS 

        $totalData = Pedido::where('PEDIDOS.ID_SUCURSAL','=', $user->id_sucursal)
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

            $posts = Pedido::select(DB::raw('PEDIDOS.ID AS CODIGO, CLIENTES.NOMBRE, PEDIDOS.FECALTAS, PEDIDOS.OBSERVACION, USERS.NAME, PEDIDOS.TOTAL, PEDIDOS.ESTATUS, PEDIDOS.FK_MONEDA AS MONEDA'))
                         ->leftJoin('CLIENTES', function($join){
	                        $join->on('CLIENTES.CODIGO', '=', 'PEDIDOS.CLIENTE_ID')
	                             ->on('CLIENTES.ID_SUCURSAL', '=', 'PEDIDOS.ID_SUCURSAL');
	                     })
                         ->leftjoin('USERS', 'USERS.ID', '=', 'PEDIDOS.USER_ID')
                         ->where('PEDIDOS.ID_SUCURSAL','=', $user->id_sucursal)
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

            $posts =  Pedido::select(DB::raw('PEDIDOS.ID AS CODIGO, CLIENTES.NOMBRE, PEDIDOS.FECALTAS, PEDIDOS.OBSERVACION, USERS.NAME, PEDIDOS.TOTAL, PEDIDOS.ESTATUS, PEDIDOS.FK_MONEDA AS MONEDA'))
                         ->leftJoin('CLIENTES', function($join){
	                        $join->on('CLIENTES.CODIGO', '=', 'PEDIDOS.CLIENTE_ID')
	                             ->on('CLIENTES.ID_SUCURSAL', '=', 'PEDIDOS.ID_SUCURSAL');
	                     })
                         ->leftjoin('USERS', 'USERS.ID', '=', 'PEDIDOS.USER_ID')
                         ->where('PEDIDOS.ID_SUCURSAL','=', $user->id_sucursal)
                         ->where(function ($query) use ($search) {
                            	$query->where('PEDIDOS.ID','LIKE',"%{$search}%")
                            	->orWhere('CLIENTES.NOMBRE', 'LIKE',"%{$search}%");
                		  })
                          ->offset($start)
                          ->orderBy($order,$dir)
                          ->limit($limit)
                          ->get();
                            

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
                $nestedData['CLIENTE'] = $post->NOMBRE;
                $nestedData['FECALTAS'] = $post->FECALTAS;
                $nestedData['OBSERVACION'] = $post->OBSERVACION;
                $nestedData['USUARIO'] = $post->NAME;
                $nestedData['TOTAL'] = Common::precio_candec($post->TOTAL, $post->MONEDA);

                if ($post->ESTATUS === 1) {
                	$post->ESTATUS = '<span class="badge badge-info">En Proceso</span>';
                } else if ($post->ESTATUS === 2) {
                	$post->ESTATUS = '<span class="badge badge-warning">Pendiente</span>';
                } else if ($post->ESTATUS === 3) {
                	$post->ESTATUS = '<span class="badge badge-primary">Confirmado</span>';
                } else if ($post->ESTATUS === 3) {
                	$post->ESTATUS = '<span class="badge badge-success">Procesado</span>';
                }

                $nestedData['ESTATUS'] = $post->ESTATUS;
                $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarDetalle' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a>&emsp;<a href='#' id='confirmarPedido' title='Confirmar'><i class='fa fa-check text-success'  aria-hidden='true'></i></a>&emsp;<a href='#' id='reporte' title='Reporte'><i class='fa fa-file text-secondary'  aria-hidden='true'></i></a> &emsp;<a href='#' id='editarProducto' title='Editar'><i class='fa fa-edit text-warning' aria-hidden='true'></i></a>
                    &emsp;<a href='#' id='eliminarProducto' title='Eliminar'><i class='fa fa-trash text-danger' aria-hidden='true'></i></a>";

                /*  --------------------------------------------------------------------------------- */

                
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

    public static function cambiar_estatus($data){

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

    	$fecha = date("Y-m-d H:i:s");
    	$hora = date("H:i:s");

    	/*  --------------------------------------------------------------------------------- */

    	// MODIFICAR ESTATUS 

    	Pedido::where('ESTATUS', '=', 2)
    	->where('ID_SUCURSAL', '=', $user->id_sucursal) 
    	->where('ID', '=',  $data["codigo"])
    	->update([
	        'FECMODIF' => $fecha,
	        'HORMODIF' => $hora,
	        'ESTATUS' => $data["estatus"]
	    ]);

    	/*  --------------------------------------------------------------------------------- */

    	// RESPONSE 

    	if ($data["estatus"] === 1) {
    		return ["response" => false, "statusText" => "No se puede cambiar el estatus porque se encuentra en edición"];
    	} else {
    		return ["response" => true, "statusText" => "Se ha cambiado de estatus"];
    	}

    	/*  --------------------------------------------------------------------------------- */

    }

    public static function mostrarCabecera($codigo){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */
                         
        $pedido = Pedido::select(DB::raw(
                        'PEDIDOS.ID AS CODIGO,
                        CLIENTES.CI,
                        CLIENTES.RUC,
                        CLIENTES.NOMBRE AS CLIENTE, 
                        CLIENTES.DIRECCION,
                        CLIENTES.CIUDAD,
                        CLIENTES.EMAIL,
                        PEDIDOS.FECALTAS, 
                        CLIENTES.TELEFONO,
                        CLIENTES.CELULAR,
                        PEDIDOS.OBSERVACION, 
                        USERS.NAME, 
                        PEDIDOS.TOTAL, 
                        PEDIDOS.ESTATUS, 
                        PEDIDOS.FK_MONEDA AS MONEDA,
                        PEDIDOS.FECALTAS,
                        MONEDAS.CANDEC'
        ))
        ->leftJoin('CLIENTES', function($join){
	        $join->on('CLIENTES.CODIGO', '=', 'PEDIDOS.CLIENTE_ID')
	        ->on('CLIENTES.ID_SUCURSAL', '=', 'PEDIDOS.ID_SUCURSAL');
	    })
        ->leftjoin('USERS', 'USERS.ID', '=', 'PEDIDOS.USER_ID')
        ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'PEDIDOS.FK_MONEDA')
		->where('PEDIDOS.ID','=', $codigo)
		->get();

        /*  --------------------------------------------------------------------------------- */

        // RUC / CI

        if ($pedido[0]->RUC === '' || $pedido[0]->RUC === null) {
            $pedido[0]->RUC = $pedido[0]->CI;
        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR 

        return $pedido[0];

        /*  --------------------------------------------------------------------------------- */
	}

	 public static function mostrarCuerpo($data){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
         
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $codigo = $data;
        $data = array();

        /*  --------------------------------------------------------------------------------- */

        $pedido_det = PedidoDet::select(DB::raw(
                        'PEDIDOS.ID,
                         PEDIDOS_DET.COD_PROD AS CODIGO, 
                         PRODUCTOS.CODIGO AS COD_PROD,
                         PRODUCTOS.DESCRIPCION, PEDIDOS_DET.CANTIDAD, 
                         LINEAS.DESCRIPCION AS CATEGORIA, 
                         PEDIDOS_DET.PRECIO_UNIT AS PREC_VENTA, 
                         PEDIDOS_DET.PRECIO,
                         MONEDAS.CANDEC, 
                         PRODUCTOS_AUX.MONEDA,
                         PESO.PESO,
                         PEDIDOS.FK_MONEDA'
                    ))
        			->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PEDIDOS_DET.COD_PROD')
        			->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
        			->leftjoin('PEDIDOS', 'PEDIDOS.ID', '=', 'PEDIDOS_DET.ID_PEDIDO')
                    ->leftJoin('PRODUCTOS_AUX', function($join){
	                        $join->on('PRODUCTOS_AUX.CODIGO', '=', 'PEDIDOS_DET.COD_PROD')
	                             ->on('PRODUCTOS_AUX.ID_SUCURSAL', '=', 'PEDIDOS_DET.ID_SUCURSAL');
	                     })
                    ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'PRODUCTOS_AUX.MONEDA')
                    ->leftjoin('PESO', 'PESO.CODIGO', '=', 'PRODUCTOS.CODIGO')
			        ->where('PEDIDOS.ID','=', $codigo)
			        ->get();

        /*  --------------------------------------------------------------------------------- */

        foreach ($pedido_det as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            $nestedData['CODIGO'] = $value->CODIGO;
            $nestedData['ITEM'] = $key;
            $nestedData['COD_PROD'] = $value->COD_PROD;
            $nestedData['DESCRIPCION'] = $value->DESCRIPCION;
            $nestedData['CANTIDAD'] = Common::precio_candec_sin_letra($value->CANTIDAD, 0);
            $nestedData['PRECIO'] = Common::precio_candec_sin_letra($value->PREC_VENTA, $value->FK_MONEDA);
            $nestedData['TOTAL'] = Common::precio_candec_sin_letra($value->PRECIO, $value->FK_MONEDA);
            $nestedData['MONEDA'] = $value->MONEDA;
            $nestedData['PESO'] = round(($value->PESO/1000), 2).'kg';

            /*  --------------------------------------------------------------------------------- */

            // CARGAR DATOS EN ARRAY

            $data[] = $nestedData;

            /*  --------------------------------------------------------------------------------- */

        }
        
        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return $data;

        /*  --------------------------------------------------------------------------------- */
    }

    public static function reporte($dato){

    	/*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
        $dia = date("Y-m-d");

        /*  --------------------------------------------------------------------------------- */

        // OBTENER DATOS DE CABECERA 

        $pedido = Pedido::mostrarCabecera($dato['data']);

        /*  --------------------------------------------------------------------------------- */

        // OBTENER DATOS DETALLE 

        $pedido_det = Pedido::mostrarCuerpo($dato['data']);

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI ES TABLA UNICA 

        $tab_unica = Parametro::tab_unica();

        if ($tab_unica === "SI") {
            $tab_unica = true;
        } else {
            $tab_unica = false;
        }

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $c = 0;
        $c_rows = 0;
        $c_rows_array = count($pedido_det);
        $c_filas_total = count($pedido_det);
        $codigo = $pedido->ID;
        $cliente = $pedido->CLIENTE;
        $direccion = $pedido->DIRECCION;
        $ruc = $pedido->RUC;
        $documento = $pedido->RUC;
        $telefono = $pedido->TELEFONO;
        $celular = $pedido->CELULAR;
        $ciudad = $pedido->CIUDAD;
        $fecha = $pedido->FECALTAS;
        $ruc = $pedido->RUC;
        $email = $pedido->EMAIL;
        $nombre = 'Orden_'.$codigo.'_'.time().'';
        $articulos = [];
        $cantidad = 0;
        $moneda = $pedido->MONEDA;
        $switch_hojas = false;
        $namefile = 'boleta_de_pedido_'.time().'.pdf';
        $letra = '';
        
        
        $total =  (Cotizacion::CALMONED(['monedaProducto' => $moneda, 'monedaSistema' => (int)$dato['moneda'], 'precio' => Common::quitar_coma($pedido->TOTAL, 2), 'decSistema' => $pedido->CANDEC, 'tab_unica' => $tab_unica, "id_sucursal" => $user->id_sucursal]))['valor'];
        $factura = $pedido->ID;

        $total = Common::precio_candec(Common::quitar_coma($total, 2), (int)$dato['moneda']);

        /*  --------------------------------------------------------------------------------- */

        // CARGAR VARIABLES CABECERA

        $data['cliente'] = $cliente;
        $data['documento'] = $documento;
        $data['ciudad'] = $ciudad;
        $data['direccion'] = $direccion;
        $data['telefono'] = $telefono;
        $data['codigo'] = $codigo;
        $data['dia'] = $dia;
        $data['ruc'] = $ruc;        
        $data['fecha'] = $fecha;
        $data['nombre'] = $nombre;
        $data['c'] = $c;
        $data['tipo'] = 'online';
        $data['total'] = $total;
        $data['ruc'] = $ruc;
        $data['factura'] = $factura;
        $data['email'] = $email;

        $total = 0;
        
        // INICIAR MPDF 

		$mpdf = new \Mpdf\Mpdf([
			'margin_left' => 20,
			'margin_right' => 20,
			'margin_top' => 40,
			'margin_bottom' => 10,
			'margin_header' => 10,
			'margin_footer' => 10
		]);

        $mpdf->SetDisplayMode('fullpage');

		foreach ($pedido_det as $key => $value) {

            $totalP = $value["TOTAL"];

            //COTIZACION

           $cotizacion = $totalP/($value["CANTIDAD"]*$value["PRECIO"]);

            // PRECIO 

           $precio = $cotizacion*$value["PRECIO"];

            // SI NO ENCUENTRA COTIZACION RETORNAR 

            $articulos[$c_rows]["precio"] = (Cotizacion::CALMONED(['monedaProducto' => $moneda, 'monedaSistema' => (int)$dato['moneda'], 'precio' => Common::quitar_coma($precio, 2), 'decSistema' => $pedido->CANDEC, 'tab_unica' => $tab_unica, "id_sucursal" => $user->id_sucursal]))['valor'];

            $articulos[$c_rows]["precio"] = Common::precio_candec(Common::quitar_coma($articulos[$c_rows]["precio"], 2), (int)$dato['moneda']);
            // CARGAR VARIABLES 

            $articulos[$c_rows]["cantidad"] = $value["CANTIDAD"];
            $articulos[$c_rows]["cod_prod"] = $value["COD_PROD"];
            $articulos[$c_rows]["descripcion"] = $value["DESCRIPCION"];
            $articulos[$c_rows]["total"] = (Cotizacion::CALMONED(['monedaProducto' => $moneda, 'monedaSistema' => (int)$dato['moneda'], 'precio' => Common::quitar_coma($totalP, 2), 'decSistema' => $pedido->CANDEC, 'tab_unica' => $tab_unica, "id_sucursal" => $user->id_sucursal]))['valor'];
            $articulos[$c_rows]["peso"] = $value["PESO"];

            $articulos[$c_rows]["total"] = Common::precio_candec(Common::quitar_coma($articulos[$c_rows]["total"], 2), (int)$dato['moneda']);
            
	        $total = Common::quitar_coma($total, 2) +Common::quitar_coma($totalP,2);
	        

            // CONTAR CANTIDAD DE FILAS DE HOJAS 

            $c_rows = $c_rows + 1;    

            // CONTAR LA CANTIDAD DE FILAS 

            $c = $c + 1;

            // SI CANTIDAD DE FILAS ES IGUAL A 18 ENTONCES CREAR PAGINA 

            if ($c_rows === 10){

                // AGREGAR ARTICULOS 

                $data['articulos'] = $articulos;

                // RESTAR LAS CANTIDADES CARGADAS 

                $c_rows_array = $c_rows_array - 10;

                // PONER TRUE SWITCH YA QUE CREO UNA PAGINA 

                $switch_hojas = true;

                // CARGAR SUB TOTALES POR HOJA


		        $data['total'] = (Cotizacion::CALMONED(['monedaProducto' => $moneda, 'monedaSistema' => (int)$dato['moneda'], 'precio' => Common::quitar_coma($total, 2), 'decSistema' => $pedido->CANDEC, 'tab_unica' => $tab_unica, "id_sucursal" => $user->id_sucursal]))['valor'];
		        $data['total'] = Common::precio_candec(Common::quitar_coma($data['total'], 2), (int)$dato['moneda']);

                $html = view('pdf.facturaPedido', $data)->render();

                // SI NO ES LA PRIMERA HOJA AGREGAR PAGINA

                if ($c !== 10) {
                    $mpdf->AddPage();
                }

                // CERAR CONTADOR DE FILAS CARGADAS POR HOJAS Y ARTICULOS

                $c_rows = 0;
                $data['articulos'] = [];
                $articulos = [];
                    
                $mpdf->WriteHTML($html);

            } else if ($c_rows_array < 10 && $c_filas_total === $c) {
                
                // AGREGAR ARTICULOS 
                
                $data['articulos'] = $articulos;
		        $data['total'] = (Cotizacion::CALMONED(['monedaProducto' => $moneda, 'monedaSistema' => (int)$dato['moneda'], 'precio' => Common::quitar_coma($total, 2), 'decSistema' => $pedido->CANDEC, 'tab_unica' => $tab_unica, "id_sucursal" => $user->id_sucursal]))['valor'];
                $data['total'] = Common::precio_candec(Common::quitar_coma($data['total'], 2), (int)$dato['moneda']);

                // CREAR HOJA 
                
                $html = view('pdf.facturaPedido', $data)->render();

                if ($switch_hojas === true) {
                    $mpdf->AddPage();
                }
                    
                $mpdf->WriteHTML($html);
            }
        }

        $mpdf->SetProtection(array('print'));
		$mpdf->SetTitle("Calbea/Factura");
        
        // DESCARGAR ARCHIVO PDF 

        $mpdf->Output();

        /*  --------------------------------------------------------------------------------- */
    }

    public static function inicio_catalogo(){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODAS LAS CATEGORIAS

        $categorias = Categoria::select(DB::raw('CODIGO, DESCRIPCION'))
        ->orderBy('DESCRIPCION', 'ASC')
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODAS LAS CATEGORIAS
        
        $marcas = Marca::select(DB::raw('CODIGO, DESCRIPCION'))
        ->orderBy('DESCRIPCION', 'ASC')
        ->get();
      
        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS PROVEEDORES 

        $proveedores = Proveedor::select(DB::raw('CODIGO, NOMBRE AS DESCRIPCION'))
        ->orderBy('NOMBRE', 'ASC')
        ->get();
        
        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($categorias) {
            return ['categorias' => $categorias, 'marcas' => $marcas, 'proveedores' => $proveedores];
        } else {
            return ['categorias' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
}
