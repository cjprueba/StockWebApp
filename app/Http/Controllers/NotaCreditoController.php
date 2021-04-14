<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\NotaCredito;

class NotaCreditoController extends Controller
{
    public function generar_cuerpo(Request $request)
    {

		/*  --------------------------------------------------------------------------------- */
		
		$productos = NotaCredito::generar_cuerpo($request);
        return response()->json($productos);

        /*  --------------------------------------------------------------------------------- */
    }

    public function guardar(Request $request)
    {

		/*  --------------------------------------------------------------------------------- */
		
		$productos = NotaCredito::guardar($request);
        return response()->json($productos);

        /*  --------------------------------------------------------------------------------- */
    }

    public function pdf(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        return NotaCredito::factura_pdf($request->all());

        /*  --------------------------------------------------------------------------------- */

    }

    public function obtenerCreditoCliente(Request $request)
    {

		/*  --------------------------------------------------------------------------------- */
		
		$nota_credito = NotaCredito::obtener_credito_cliente_datatable($request);
        return response()->json($nota_credito);

        /*  --------------------------------------------------------------------------------- */
    }

    public function mostrarNotaCredito(Request $request){

        $nota_credito = NotaCredito::notaCreditoMostrar($request);
        return response()->json($nota_credito);
    }
    public function cancelarNota(Request $request){

        $nota_credito = NotaCredito::notaCreditoCancelar($request->all());
        return response()->json($nota_credito);
    }
            public function CreditoNotaDetalle(Request $request)
    {
        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR PROVEEDORES 

        $nota_credito = NotaCredito::creditoNotaDetalle($request);
        return response()->json($nota_credito);

        /*  --------------------------------------------------------------------------------- */

    }
}
