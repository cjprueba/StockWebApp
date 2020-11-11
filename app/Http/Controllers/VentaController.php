<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Venta;
use App\VentaVale;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class VentaController extends Controller
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
        //return Venta::whereDate('FECALTAS', '2019-06-01');
        //return DB::connection('retail')->select('select * from ventas limit 10');
        $ventas = Venta::ventas(date("Y-m-d"));
        return response()->json($ventas);
    }

       public function reporteVenta(Request $request)
    {
 

            $ventas = Venta::generarReporteVenta($request->all());
            return response()->json($ventas);
       
        //return response()->json([$request->all()]);
    }
  
    public function mostrar(Request $request)
    {

        if ($request["Opcion"] === 2) {
            $ventas = Venta::generarTablaMarca($request->all());
            return response()->json($ventas);
        } else if  ($request["Opcion"] === 3) {
            $ventas = Venta::generarTablaCategoria($request->all());
            return response()->json($ventas);
        } else {
            $ventas = Venta::generarConsulta($request->all());
            return response()->json($ventas);
        }
        //return response()->json([$request->all()]);
    }

    public function periodos_superados(Request $request)
    {
        $ventas = Venta::periodos_superados($request);
        return response()->json($ventas);
    }

    public function guardar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR DATATABLE

        $venta = Venta::guardar($request->all());
        return response()->json($venta);

        /*  --------------------------------------------------------------------------------- */

    }

    public function numeracion(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR DATATABLE

        $venta = Venta::numeracion($request->all());
        return response()->json($venta);

        /*  --------------------------------------------------------------------------------- */

    }

    public function inicio(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR DATATABLE

        $venta = Venta::inicio($request->all());
        return response()->json($venta);

        /*  --------------------------------------------------------------------------------- */

    }

    public function factura(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        return Venta::factura_pdf($request->all());

        /*  --------------------------------------------------------------------------------- */

    }

    public function ticket(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        return Venta::ticket_pdf($request->all());

        /*  --------------------------------------------------------------------------------- */

    }

    public function resumen(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        return Venta::resumen_pdf($request->all());

        /*  --------------------------------------------------------------------------------- */

    }

    public function datatable(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        return Venta::datatable_venta($request);

        /*  --------------------------------------------------------------------------------- */

    }

    public function filtrarVenta(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

       // OBTENER TODOS LOS DATOS DEL TALLE

         $venta= Venta::filtrar_venta($request->all());
        return response()->json($venta);
        
        /*  --------------------------------------------------------------------------------- */

    }
    
    public function ventasDatatable(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Talles

        $ventas = Venta::ventas_datatable($request);
        return response()->json($ventas);
        
        /*  --------------------------------------------------------------------------------- */

    }
    
    public function anularVenta(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL TALLE

        $venta= Venta::anular_venta($request->all());
        return response()->json($venta);
        
        /*  --------------------------------------------------------------------------------- */

    }

    public function devolucionProductos(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $venta = Venta::devolucion_productos($request);
        return response()->json($venta);

        /*  --------------------------------------------------------------------------------- */

    }

    public function mostrarProductos(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $venta = Venta::mostrar_productos($request);
        return response()->json($venta);

        /*  --------------------------------------------------------------------------------- */

    }

    public function rptVale(Request $request){

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $vale = VentaVale::valePDF($request->all());
        return response()->json($vale);

        /*  --------------------------------------------------------------------------------- */

    }

    public function generarVentaVale(Request $request){

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $vale = VentaVale::generarVentaVale($request);
        return response()->json($vale);

        /*  --------------------------------------------------------------------------------- */

    }

    public function obtenerCuentas(Request $request){

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $vale = Venta::obtenerCuentas($request);
        return response()->json($vale);

        /*  --------------------------------------------------------------------------------- */

    }

    public function pagoEntrega(Request $request){

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $vale = Venta::pagoEntrega($request);
        return response()->json($vale);

        /*  --------------------------------------------------------------------------------- */

    }

    public function pagoCredito(Request $request){

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $vale = Venta::pagoCredito($request);
        return response()->json($vale);

        /*  --------------------------------------------------------------------------------- */

    }

     public function reporteUnico(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $venta = Venta::reporteUnico($request->all());
        return response()->json($venta);
        
        /*  --------------------------------------------------------------------------------- */

    }

    public function asignarNotaCreditoCreditoCliente(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */
        
        $nota_credito = Venta::asignarNotaCreditoCreditoCliente($request);
        return response()->json($nota_credito);

        /*  --------------------------------------------------------------------------------- */
    }
        public function reporteDiario(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */
        
        $resumen_dia = Venta::resumen_dia($request);
        return response()->json($resumen_dia);

        /*  --------------------------------------------------------------------------------- */
    }

}
