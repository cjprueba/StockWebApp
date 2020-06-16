<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Container;
class ContainerController extends Controller
{
    //
     public function filtrarContainer(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

       // OBTENER TODOS LOS DATOS DEL TALLE

         $container= Container::filtrar_container($request->all());
        return response()->json($container);
        
        /*  --------------------------------------------------------------------------------- */

    }
                public function containerDatatable(Request $request)
    {
        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR PROVEEDORES 

        $container = Container::container_datatable($request);
        return response()->json($container);

        /*  --------------------------------------------------------------------------------- */

    }
            public function obtenerCodigo(){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER CODIGO TRANSPORTE

        $container = Container::obtener_codigo();
        return response()->json($container);

        /*  --------------------------------------------------------------------------------- */
    }
          public function containerGuardar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

 

        $container = Container::container_guardar($request->all());
        return response()->json($container);
        
        /*  --------------------------------------------------------------------------------- */

    }
        public function containerEliminar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Eliminar Telas

        $container = Container::container_eliminar($request->all());
        return response()->json($container);
        
        /*  --------------------------------------------------------------------------------- */

    }
}
