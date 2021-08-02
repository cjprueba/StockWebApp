<?php

namespace  App\Http\Controllers;
use App\Exports\Compras;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Compra;
use App\Transferencia;
use Illuminate\Support\Facades\Log;
use App\Temp_venta;
use App\Exports\Reportes\Compra\Entrada\CompraSeccionExport;
class CompraController extends Controller
{
    //
    public function Descargar(Request $request)
    {


           return Excel::download(new Compras($request->all()), 'Inventario.xlsx');

        //return response()->json([$request->all()]);
    }

    public function guardarModificarCompra(Request $request)
    {

    	/*  --------------------------------------------------------------------------------- */

        // GUARDAR COMPRA 

    	$comprasDet = Compra::guardar_modificar_compra($request);
        return response()->json($comprasDet);

        /*  --------------------------------------------------------------------------------- */

    }

    public function obtenerCodigo()
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $compra = Compra::ultimo_codigo();
        return response()->json($compra);

        /*  --------------------------------------------------------------------------------- */

    }

    public function mostrarDataTable(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR DATATABLE

        $compra = Compra::mostrarDatatable($request);
        return response()->json($compra);

        /*  --------------------------------------------------------------------------------- */

    }

    public function eliminarCompra(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // ELIMINAR TRANSFERENCIA

        $compra = Compra::eliminar($request->all());
        return response()->json($compra); 
        
        /*  --------------------------------------------------------------------------------- */
        
    }

    public function mostrarProductos(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $compra = Compra::mostrar_productos($request);
        return response()->json($compra);

        /*  --------------------------------------------------------------------------------- */

    }

    public function getPdf(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // GENERAR PDF TRANSFERENCIA 

        return Compra::pdf_compra($request->all());

        /*  --------------------------------------------------------------------------------- */
        
    }

    public function obtenerCabecera(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // GENERAR PDF TRANSFERENCIA 

        return Compra::mostrar_cabecera($request->all());

        /*  --------------------------------------------------------------------------------- */
        
    }

    public function obtenerCuerpo(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // GENERAR PDF TRANSFERENCIA 

        return Compra::mostrar_cuerpo($request->all());

        /*  --------------------------------------------------------------------------------- */
        
    }
        public function qr(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO
       
      if($request["data"]["tipo_busqueda"]===1){
         $productos = Compra::CompraCajaQr($request->all());
      }else{
        $productos=Transferencia::TransferenciaCajaQr($request->all());
      }
       
        return response()->json($productos);

        
        /*  --------------------------------------------------------------------------------- */

    }

    public function ubicacionModificarCompra(Request $request){

        /*  --------------------------------------------------------------------------------- */

        // MODIFICAR COMPRA DEPOSITO

        $compra = Compra::modificarUbicacionCompra($request->all());
        return response()->json($compra); 
        
        /*  --------------------------------------------------------------------------------- */
        
    }
    public function generar_reporte_entrada_seccion(Request $request){

        /*  --------------------------------------------------------------------------------- */

        // MODIFICAR COMPRA DEPOSITO

        $compra = Compra::generar_Reporte_Entrada_Seccion($request->all());
        return response()->json($compra); 
        
        /*  --------------------------------------------------------------------------------- */
        
    }
    public function generar_reporte_venta_compra_seccion(Request $request){

        /*  --------------------------------------------------------------------------------- */

        // MODIFICAR COMPRA DEPOSITO

        $compra = Compra::generar_Reporte_Compra_Venta_Seccion($request->all());
        return response()->json($compra); 
        
        /*  --------------------------------------------------------------------------------- */
        
    }
    public function reporteEntradaSeccion(Request $request){

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
                        'Insert'=>$request->Insert
                    );
           $data["data"]=$datos;
           $respuesta=Compra::generar_Reporte_Entrada_Seccion($data);
          
         
        return Excel::download(new CompraSeccionExport($respuesta), 'VentaSeccionGondola.xlsx');

       /* $compra = Compra::generar_Reporte_Entrada_Seccion($request->all());
        return response()->json($compra); */
        
        /*  --------------------------------------------------------------------------------- */
        
    }
    
}
