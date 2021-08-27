<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Inventario;
use  App\Exports\Reportes\Inventario\InventarioImageGondolaExport;
use  App\Exports\Reportes\Inventario\Web\InventarioSeccionExport;

class InventarioController extends Controller
{
    public function agregarEditarProducto(Request $request){
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR PRODUCTOS

        $inventario = Inventario::insertar_producto($request);
        return response()->json($inventario);

        /*  --------------------------------------------------------------------------------- */
    }

    public function guardarInventario(Request $request){
        
        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $inventario = Inventario::guardar_inventario($request);
        return response()->json($inventario);

        /*  --------------------------------------------------------------------------------- */
    }

    public function productosDataTable(Request $request)
    {

    	/*  --------------------------------------------------------------------------------- */

    	// MOSTRAR PRODUCTOS INGRESADOS 

        $inventario = Inventario::mostrarDatatable($request);
        return response()->json($inventario);

        /*  --------------------------------------------------------------------------------- */
    }

    public function inventarioDataTable(Request $request)
    {

    	/*  --------------------------------------------------------------------------------- */

    	// MOSTRAR PRODUCTOS INGRESADOS 

        $inventario = Inventario::mostrarDatatableInventario($request);
        return response()->json($inventario);

        /*  --------------------------------------------------------------------------------- */
    }

    public function Inventario_Cerrado(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // DESCARGA REPORTE 

        return Excel::download(new Inventario($request->all()), 'Inventario.xlsx');

        /*  --------------------------------------------------------------------------------- */

    }

    public function modificarComentario(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR PRODUCTOS INGRESADOS 

        $inventario = Inventario::modificarComentario($request);
        return response()->json($inventario);

        /*  --------------------------------------------------------------------------------- */
    }

    public function eliminarProducto(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR PRODUCTOS INGRESADOS 

        $inventario = Inventario::eliminarProducto($request);
        return response()->json($inventario);

        /*  --------------------------------------------------------------------------------- */
    }

    public function reporte(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // GENERAR PDF TRANSFERENCIA 

        return Inventario::reporte($request->all());

        /*  --------------------------------------------------------------------------------- */
        
    }

    public function procesar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR PRODUCTOS INGRESADOS 

        $inventario = Inventario::procesar($request);
        return response()->json($inventario);

        /*  --------------------------------------------------------------------------------- */
    }
        public function Inventario_Gondola_Productos(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // DESCARGA REPORTE 

        return Excel::download(new InventarioImageGondolaExport($request->all()), 'Inventario_Gondola.xlsx');

        /*  --------------------------------------------------------------------------------- */

    }
    public function generar_reporte_inventario_seccion(Request $request){

        /*  --------------------------------------------------------------------------------- */

        // MODIFICAR COMPRA DEPOSITO

        $inventario = Inventario::generar_Reporte_Inventario_Seccion($request->all());
        return response()->json($inventario); 
        
        /*  --------------------------------------------------------------------------------- */
        
    }
        public function ExportInventarioSeccion(Request $request){

        /*  --------------------------------------------------------------------------------- */

        // MODIFICAR COMPRA DEPOSITO
/*
            if($request->Insert==true){

                  $datos=array(
                        'inicio'=>date('Y-m-d', strtotime($request->Inicio)),
                        'final'=>date('Y-m-d', strtotime($request->Final)),
                        'sucursal'=>$request->Sucursal,
                        'checkedProveedor'=>$request->AllProveedores,
                        'checkedSeccion'=>$request->AllSecciones,
                        'proveedores'=>$request->Proveedores,
                        'secciones'=>$request->secciones,
                    );
                  
          
                   Temp_venta::insertar_reporte_Entrada_Seccion($datos);
            }*/
            $data=array();

           $datos=array(
                        'Inicio'=>$request->Inicio,
                        'Final'=>$request->Final,
                        'Sucursal'=>$request->Sucursal,
                        'AllProveedores'=>$request->AllProveedores,
                        'AllSecciones'=>$request->AllSecciones,
                        'Proveedores'=>$request->Proveedores,
                        'secciones'=>$request->secciones,
                        'gondolas'=>$request->gondolas,
                        'AllGondolas'=>$request->AllGondolas,
                        'Agrupado'=>$request->Agrupado,
                        'Insert'=>$request->Insert
                    );
           $data["data"]=$datos;
           $respuesta=Inventario::generar_Reporte_Inventario_Seccion($data);
          
         
        return Excel::download(new InventarioSeccionExport($respuesta,$request->Agrupado), 'InventarioSeccionGondola.xlsx');

       /* $compra = Compra::generar_Reporte_Entrada_Seccion($request->all());
        return response()->json($compra); */
        
        /*  --------------------------------------------------------------------------------- */
        
    }
    
}
