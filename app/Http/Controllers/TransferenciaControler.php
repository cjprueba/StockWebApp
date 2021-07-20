<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;
use App\Transferencia;
use App\Exports\TransferenciaGeneral;
use App\VentaTransferencia;

class TransferenciaControler extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    public function mostrarDataTable(Request $request)
    {
        $transferencia = Transferencia::mostrarDatatable($request);
        return response()->json($transferencia);
    }

    public function mostrar(Request $request)
    {
        $transferencia = Transferencia::generarConsulta($request->all());
        return response()->json($transferencia);
    }

    
    public function guardarTransferencia(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // GUARDAR TRANSFERENCIA Y ENVIAR UNO COMO REFERENCIA DE GUARDADO

        $transferencia = Transferencia::guardar_modificar($request->all(), 1);
        return response()->json($transferencia); 
        
        /*  --------------------------------------------------------------------------------- */
        
    }

    public function eliminarTransferencia(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // ELIMINAR TRANSFERENCIA

        $transferencia = Transferencia::eliminar($request->all(), 1);
        return response()->json($transferencia); 
        
        /*  --------------------------------------------------------------------------------- */
        
    }

    public function modificarTransferencia(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MODIFICAR TRANSFERENCIA

        $transferencia = Transferencia::modificar($request->all());
        return response()->json($transferencia); 
        
        /*  --------------------------------------------------------------------------------- */
        
    }

    public function mostrarCabecera(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR CABECERA DE TRANSFERENCIA 

        $transferencia = Transferencia::mostrar_cabecera($request->all());
        return response()->json($transferencia);

        /*  --------------------------------------------------------------------------------- */

    }

    public function mostrarCuerpo(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR CABECERA DE TRANSFERENCIA 

        $transferencia = Transferencia::mostrar_cuerpo($request->all());
        return response()->json($transferencia);

        /*  --------------------------------------------------------------------------------- */

    }

    public function mostrarImportar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $transferencia = Transferencia::mostrar_importar($request);
        return response()->json($transferencia);

        /*  --------------------------------------------------------------------------------- */

    }

    public function mostrarProductos(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $transferencia = Transferencia::mostrar_productos($request);
        return response()->json($transferencia);

        /*  --------------------------------------------------------------------------------- */

    }
        public function mostrarProductosDevolucion(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $transferencia = Transferencia::mostrar_productos_devolucion($request);
        return response()->json($transferencia);

        /*  --------------------------------------------------------------------------------- */

    }

    public function enviarTransferencia(Request $request){

        /*  --------------------------------------------------------------------------------- */

        // ENVIAR TRANSFERENCIA

        $transferencia = Transferencia::enviar_transferencia($request);
        return response()->json($transferencia);

        /*  --------------------------------------------------------------------------------- */

    }

    public function rechazarTransferencia(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $transferencia = Transferencia::rechazar_transferencia($request);
        return response()->json($transferencia);

        /*  --------------------------------------------------------------------------------- */

    }

    public function importarTransferencia(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $transferencia = Transferencia::importar_transferencia($request->all());
        return response()->json($transferencia);

        /*  --------------------------------------------------------------------------------- */

    }

    public function detalleTransferencia(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $transferencia = Transferencia::detalle_transferencia($request);
        return response()->json($transferencia);

        /*  --------------------------------------------------------------------------------- */

    }

    public function descargar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // DESCARGAR ARCHIVO EXCEL 

        return Excel::download(new TransferenciaGeneral($request->all()), 'Transferencias.xlsx');

        /*  --------------------------------------------------------------------------------- */

    }

    public function getGenerar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        return Transferencia::factura_pdf($request->all());

        /*  --------------------------------------------------------------------------------- */

    }

    public function getRptTransferencia(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // GENERAR PDF TRANSFERENCIA 

        return Transferencia::pdf_transferencia($request->all());

        /*  --------------------------------------------------------------------------------- */
        
    }
   
    public function rptTransferencia(Request $request){

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $transferencia = VentaTransferencia::transferenciaPDF($request->all());
        return response()->json($transferencia);

        /*  --------------------------------------------------------------------------------- */

    }

    public function generarVentaT(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $transferencia = VentaTransferencia::generarVentaTransferencia($request);
        return response()->json($transferencia);

        /*  --------------------------------------------------------------------------------- */

    }
        public function arreglar()
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $transferencia = Transferencia::arreglar_costo();
        return response()->json($transferencia);

        /*  --------------------------------------------------------------------------------- */

    }
       public function devolverTransferencia(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $transferencia = Transferencia::devolverTransferencia($request->all());
        return response()->json($transferencia);

        /*  --------------------------------------------------------------------------------- */

    }
    public function marcarTransferenciaDevolucion(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $transferencia = Transferencia::marcar_Transferencia_Devolucion($request->all());
        return response()->json($transferencia);

        /*  --------------------------------------------------------------------------------- */

    }


    public function modificarTransferenciaUbicacion(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $transferencia = Transferencia::modificarUbicacionTransferencia($request->all());
        return response()->json($transferencia);

        /*  --------------------------------------------------------------------------------- */

    }
     public function qr(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO
    
        $productos = Transferencia::TransferenciaCajaQr($request->all());
        return response()->json($productos);
        
        /*  --------------------------------------------------------------------------------- */

    }

}
