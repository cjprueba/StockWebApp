<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Producto;
use Illuminate\Support\Facades\DB;
use App\ProductosAux;

class ProductoController extends Controller
{
	/*  --------------------------------------------------------------------------------- */

	// INICIAR LAS VARIABLES GLOBALES

	private $search = '';
    
    /*  --------------------------------------------------------------------------------- */

    public function mostrar(Request $request)
    {

		/*  --------------------------------------------------------------------------------- */
		
		$productos = ProductosAux::mostrar_datatable($request);
        return response()->json($productos);

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
       public function encontrarFoto()
    {

        /*  --------------------------------------------------------------------------------- */

        // GENERAR CODIGO PRODUCTO

        $producto = Producto::guardar_img_principal();
        return response()->json($producto);

        /*  --------------------------------------------------------------------------------- */

    }

     public function guardar(Request $request)
    {
        
        /*  --------------------------------------------------------------------------------- */

        // GUARDAR PRODUCTO 

        $productos = Producto::guardar_modificar($request->all());
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
         public function importar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $productos = Producto::importar_producto($request->all());
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


    public function mostrarDataTableGeneral(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER PRODUCTOS DATATABLE 

        $producto = Producto::productos_datatable($request);
        return response()->json($producto);

        /*  --------------------------------------------------------------------------------- */

    }

     public function productoDetalle(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $productos = Producto::producto_detalle($request->all());
        return response()->json($productos);
        
        /*  --------------------------------------------------------------------------------- */

    }

     public function productoProveedor(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $productos = Producto::producto_proveedor($request->all());
        return response()->json($productos);
        
        /*  --------------------------------------------------------------------------------- */

    }

    public function productoTransferencia(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $productos = Producto::producto_transferencia($request->all());
        return response()->json($productos);
        
        /*  --------------------------------------------------------------------------------- */

    }

    public function eliminar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $productos = Producto::eliminar($request->all());
        return response()->json($productos);
        
        /*  --------------------------------------------------------------------------------- */

    }

    public function minimo(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $productos = Producto::minimo($request);
        return response()->json($productos);
        
        /*  --------------------------------------------------------------------------------- */

    }
    
    public function baja(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $productos = Producto::baja($request->all());
        return response()->json($productos);
        
        /*  --------------------------------------------------------------------------------- */

    }
	
	public function ubicacion(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $productos = Producto::ubicacion($request->all());
        return response()->json($productos);
        
        /*  --------------------------------------------------------------------------------- */

    }
	
	public function existe(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $productos = Producto::existe($request->all());
        return response()->json($productos);
        
        /*  --------------------------------------------------------------------------------- */

    }
	
	 public function obtenerProductoPOS(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $productos = Producto::obtener_producto_POS($request->all());
        return response()->json($productos);
        
        /*  --------------------------------------------------------------------------------- */

    }

    public function mostrar_new(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $productos = Producto::mostrar_new($request->all());
        return response()->json($productos);
        
        /*  --------------------------------------------------------------------------------- */

    }

    public function ofertas(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $productos = Producto::ofertas($request->all());
        return response()->json($productos);
        
        /*  --------------------------------------------------------------------------------- */

    }

    public function catalogo_cliente(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $productos = Producto::catalogo_cliente($request->all());
        return response()->json($productos);
        
        /*  --------------------------------------------------------------------------------- */

    }     

    public function productoMovimiento(Request $request){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $productos = Producto::movimientoProducto($request->all());
        return response()->json($productos);
        
        /*  --------------------------------------------------------------------------------- */

    }

    public function detalleProductoVenta(Request $request){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS 

        $ventasP = Producto::ventaProducto($request);
        return response()->json($ventasP);
        
        /*  --------------------------------------------------------------------------------- */

    }

    public function qr(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO
    
        $productos = Producto::productoqr($request->all());
        return response()->json($productos);
        
        /*  --------------------------------------------------------------------------------- */

    }
}
