<?php

namespace App\Http\Controllers;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Proveedor;
use App\Exports\VentasProveedor;
use App\Exports\VentasProveedorExport;
use DateTime;
use App\Temp_venta;
class ProveedorController extends Controller
{
    public function obtenerProveedores(){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER PROVEEDORES

        $proveedor = Proveedor::obtener_proveedores();
        return response()->json($proveedor);

        /*  --------------------------------------------------------------------------------- */
    }
    public function NuevoProveedor(){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER PROVEEDORES

        $proveedor = Proveedor::obtener_codigo();
        return response()->json($proveedor);

        /*  --------------------------------------------------------------------------------- */
    }

    public function mostrar(Request $request)
    {
        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR PROVEEDORES 

        $ventas = Proveedor::generarConsulta($request->all());
        return response()->json($ventas);

        /*  --------------------------------------------------------------------------------- */

    }

    public function descargar(Request $request)
    {

    	/*  --------------------------------------------------------------------------------- */

    	// DESCARGAR REPORTE PROVEEDORES 
    	
        return Excel::download(new VentasProveedor($request->all()), 'VentasProveedores.xlsx');

        /*  --------------------------------------------------------------------------------- */

    }

    public function pago(Request $request)
    {
        /*  --------------------------------------------------------------------------------- */

        // REALIZAR PAGO

        $proveedor = Proveedor::pago($request->all());
        return response()->json($proveedor);

        /*  --------------------------------------------------------------------------------- */

    }

    public function datatable(Request $request)
    {
        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR PROVEEDORES 

        $proveedor = Proveedor::datatable($request);
        return response()->json($proveedor);

        /*  --------------------------------------------------------------------------------- */

    }

    public function loteProducto(Request $request)
    {
        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR PROVEEDORES 

        $proveedor = Proveedor::loteProducto($request);
        return response()->json($proveedor);

        /*  --------------------------------------------------------------------------------- */

    }

    public function devolucion(Request $request)
    {
        /*  --------------------------------------------------------------------------------- */

        // REALIZAR PAGO

        $proveedor = Proveedor::devolucion($request->all());
        return response()->json($proveedor);

        /*  --------------------------------------------------------------------------------- */

    }

    public function devolucionMostrar(Request $request)
    {
        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR PROVEEDORES 

        $proveedor = Proveedor::devolucionMostrar($request);
        return response()->json($proveedor);

        /*  --------------------------------------------------------------------------------- */

    }
    
    public function devolucionDetalle(Request $request)
    {
        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR PROVEEDORES 

        $proveedor = Proveedor::devolucionDetalle($request);
        return response()->json($proveedor);

        /*  --------------------------------------------------------------------------------- */

    }

            public function proveedorGuardar(Request $request)
    {
        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR PROVEEDORES 

        $proveedor = Proveedor::proveedor_guardar($request);
        return response()->json($proveedor);

        /*  --------------------------------------------------------------------------------- */

    }
            public function proveedorEliminar(Request $request)
    {
        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR PROVEEDORES 

        $proveedor = Proveedor::proveedor_eliminar($request);
        return response()->json($proveedor);

        /*  --------------------------------------------------------------------------------- */

    }
        public function filtrarProveedor(Request $request)
    {
        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR PROVEEDORES 

        $proveedor = Proveedor::filtrar_proveedor($request);
        return response()->json($proveedor);

        /*  --------------------------------------------------------------------------------- */

    }
        public function proveedorDatatable(Request $request)
    {
        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR PROVEEDORES 

        $proveedor = Proveedor::proveedor_datatable($request);
        return response()->json($proveedor);

        /*  --------------------------------------------------------------------------------- */

    }
        public function descargar_excel(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // DESCARGAR REPORTE PROVEEDORES 
            if($request->Insert==true){
                 if(isset($request->Tipo)){
                  $datos=array(
                        'inicio'=>date('Y-m-d', strtotime($request->Inicio)),
                        'final'=>date('Y-m-d', strtotime($request->Final)),
                        'sucursal'=>$request->Sucursal,
                        'checkedCategoria'=>$request->AllCategory,
                        'checkedProveedor'=>$request->AllProveedores,
                        'proveedores'=>$request->Proveedores,
                        'linea'=>$request->Categorias,
                        'tipos'=>$request->Tipo
                    );
                  }else{
                     $datos=array(
                            'inicio'=>date('Y-m-d', strtotime($request->Inicio)),
                            'final'=>date('Y-m-d', strtotime($request->Final)),
                            'sucursal'=>$request->Sucursal,
                            'checkedCategoria'=>$request->AllCategory,
                            'checkedProveedor'=>$request->AllProveedores,
                            'proveedores'=>$request->Proveedores,
                            'linea'=>$request->Categorias
                        );
                  }
          
                   Temp_venta::insertar_reporte($datos);
            }
           
        return Excel::download(new VentasProveedorExport($request->all()), 'VentasProveedores.xlsx');

        /*  --------------------------------------------------------------------------------- */

    }
}
