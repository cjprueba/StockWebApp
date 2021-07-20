<?php

namespace  App\Http\Controllers;
use App\Exports\Compras;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Compra;

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
    
        $productos = Compra::CompraCajaQr($request->all());
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
}
