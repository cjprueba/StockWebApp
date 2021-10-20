<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Gondola;
use App\Exports\Reportes\Gondola\Productos\GondolasProductosImageExport;
use Maatwebsite\Excel\Facades\Excel;
class GondolaController extends Controller
{
    public function obtenerGondolas(){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER GONDOLAS

        $gondolas = Gondola::obtener_gondolas();
        return response()->json($gondolas);

        /*  --------------------------------------------------------------------------------- */
    }
        public function GondolasDatatable(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Talles

        $gondolas = Gondola::gondolas_datatable($request);
        return response()->json($gondolas);
        
        /*  --------------------------------------------------------------------------------- */

}
   public function filtrarGondola(Request $request)
{

        /*  --------------------------------------------------------------------------------- */

       // OBTENER TODOS LOS DATOS DEL TALLE

         $gondolas = Gondola::filtrar_gondola($request->all());
        return response()->json($gondolas);
        
        /*  --------------------------------------------------------------------------------- */

    }
               public function obtenerCodigo(){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER MARCAS

        $gondolas = Gondola::obtener_codigo();
        return response()->json($gondolas);

        /*  --------------------------------------------------------------------------------- */
    }
        public function gondolaGuardar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Usuarios

        $gondolas = Gondola::gondola_guardar($request->all());
        return response()->json($gondolas);
        
        /*  --------------------------------------------------------------------------------- */

    }
        public function gondolaEliminar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Usuarios

        $gondolas = Gondola::gondola_eliminar($request->all());
        return response()->json($gondolas);
        
        /*  --------------------------------------------------------------------------------- */

    }


    public function obtenerGondolasProducto(Request $request){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER GONDOLAS

        $gondolas = Gondola::obtener_gondolas_por_producto($request->all());
        return response()->json($gondolas);

        /*  --------------------------------------------------------------------------------- */
    }
    public function ConfiguracionInicioGondola(){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER GONDOLAS

        $gondolas = Gondola::configuracion_inicio_gondola();
        return response()->json($gondolas);

        /*  --------------------------------------------------------------------------------- */
    }
    public function obtenerGondolasEncargada(){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER GONDOLAS

        $gondolas = Gondola::obtener_gondolas_encargada();
        return response()->json($gondolas);

        /*  --------------------------------------------------------------------------------- */
    }
       public function descagrarProductosEnGondola(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // DESCARGAR REPORTE PROVEEDORES 
        
        return Excel::download(new GondolasProductosImageExport($request->all()), 'ProductosEnGondola.xlsx');

        /*  --------------------------------------------------------------------------------- */

    }
    
}
