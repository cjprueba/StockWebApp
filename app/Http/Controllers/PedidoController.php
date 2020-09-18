<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pedido;

class PedidoController extends Controller
{
    public function agregarProducto(Request $request)
    {

		/*  --------------------------------------------------------------------------------- */
		
		$productos = Pedido::agregarProducto($request);
        return response()->json($productos);

        /*  --------------------------------------------------------------------------------- */
    }

    public function obtenerProductos(Request $request)
    {

		/*  --------------------------------------------------------------------------------- */
		
		$productos = Pedido::obtenerProductos($request);
        return response()->json($productos);

        /*  --------------------------------------------------------------------------------- */
    }

    public function confirmar(Request $request)
    {

		/*  --------------------------------------------------------------------------------- */
		
		$productos = Pedido::confirmar($request);
        return response()->json($productos);

        /*  --------------------------------------------------------------------------------- */
    }

    public function inicio_mostrar_new(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $productos = Pedido::inicio_mostrar_new($request->all());
        return response()->json($productos);
        
        /*  --------------------------------------------------------------------------------- */

    }

    public function cambiar_cantidad(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $productos = Pedido::cambiar_cantidad($request->all());
        return response()->json($productos);
        
        /*  --------------------------------------------------------------------------------- */

    }

    public function eliminar_producto(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $productos = Pedido::eliminar_producto($request->all());
        return response()->json($productos);
        
        /*  --------------------------------------------------------------------------------- */

    }

    public function mostrar_datatable(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */
        
        $pedidos = Pedido::mostrar_datatable($request);
        return response()->json($pedidos);

        /*  --------------------------------------------------------------------------------- */
    }

    public function cambiar_estatus(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $productos = Pedido::cambiar_estatus($request->all());
        return response()->json($productos);
        
        /*  --------------------------------------------------------------------------------- */

    }

    public function reporte(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $productos = Pedido::reporte($request->all());
        return response()->json($productos);
        
        /*  --------------------------------------------------------------------------------- */

    }

    public function inicio_catalogo(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $productos = Pedido::inicio_catalogo($request->all());
        return response()->json($productos);
        
        /*  --------------------------------------------------------------------------------- */

    }
}
