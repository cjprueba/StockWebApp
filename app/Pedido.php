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

    	/*  --------------------------------------------------------------------------------- */

    	// REVISAR CANTDIAD MAYORISTA

    	$mayorista = Common::calculoMayorista($datos["precio_unit"], $datos["precio_mayorista"], $cantidad);
    	$precio = $mayorista["valor"];
    	$precio_unit = $mayorista["precio_unit"];

    	/*  --------------------------------------------------------------------------------- */

    	// CALCULAR TOTAL

    	$total = $precio * $cantidad;

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
	            'MONEDA' => $parametro['MONEDA']
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

    		$mayorista = Common::calculoMayorista($datos["precio_unit"], $datos["precio_mayorista"], ($cantidad + Common::quitar_coma($producto[0]["CANTIDAD"], 0)));
    		$precio = $mayorista["valor"];
    		$precio_unit = $mayorista["precio_unit"];

    		/*  --------------------------------------------------------------------------------- */  

    		// COMPRBAR STOCK 

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
	            'TIPO_PRECIO' => $mayorista["tipo"], 
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
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODAS LAS CATEGORIAS
        
        $marcas = Marca::select(DB::raw('CODIGO, DESCRIPCION'))
        ->get();
      
        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS PROVEEDORES 

        $proveedores = Proveedor::select(DB::raw('CODIGO, NOMBRE AS DESCRIPCION'))
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

        $pedido = Pedido::totales($pedido[0]['ID']);

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($categorias) {
            return ['categorias' => $categorias, 'marcas' => $marcas, 'proveedores' => $proveedores, "conteo" => $pedido["conteo"], "total" => $pedido["total"]];
        } else {
            return ['categorias' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
}
