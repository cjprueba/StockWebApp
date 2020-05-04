<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Marca;

class MarcaController extends Controller
{
    public function obtenerMarcas(Request $request){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER MARCAS

        $marcas = Marca::obtener_marcas($request->all());
        return response()->json($marcas);

        /*  --------------------------------------------------------------------------------- */
    }
        public function obtenerCodigo(){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER MARCAS

        $marcas = Marca::obtener_codigo();
        return response()->json($marcas);

        /*  --------------------------------------------------------------------------------- */
    }
        public function filtrarMarca(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

       // OBTENER TODOS LOS DATOS DE LA MARCA

         $marcas = Marca::filtrar_marcas($request->all());
        return response()->json($marcas);
        
        /*  --------------------------------------------------------------------------------- */

    }
            public function MarcasDatatable(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Usuarios

        $marca = Marca::marcas_datatable($request);
        return response()->json($marca);
        
        /*  --------------------------------------------------------------------------------- */

    }
                public function MarcasPorCategoriaDatatable(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Usuarios

        $marca = Marca::marcas_por_categoria_datatable($request);
        return response()->json($marca);
        
        /*  --------------------------------------------------------------------------------- */

    }
                public function marcaGuardar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Usuarios

        $marca = Marca::marcas_guardar($request->all());
        return response()->json($marca);
        
        /*  --------------------------------------------------------------------------------- */

    }
     public function marcaEliminar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Usuarios

        $marca = Marca::marcas_eliminar($request->all());
        return response()->json($marca);
        
        /*  --------------------------------------------------------------------------------- */

    }

    public function marcaCategoriaSeleccion(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DE LA MARCA

        $marcas = Marca::marca_categoria_seleccion($request->all());
        return response()->json($marcas);
        
        /*  --------------------------------------------------------------------------------- */

    }
    
}
