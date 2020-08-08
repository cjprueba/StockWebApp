<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Fpdf\Fpdf;

class VentaVale extends Model
{
    protected $connection = 'retail';
    protected $table = 'ventas_vale';

    public static function guardar_referencia($data){

    	try {

	    	/*  --------------------------------------------------------------------------------- */

	    	$vale = VentaVale::insertGetId([
	    		'FK_VENTA' => $data["FK_VENTA"],
	    		'MONTO' => $data["MONTO"],
	    		'MONEDA' => $data["MONEDA"]
	    	]);

	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Venta Vale: Éxito al guardar.', ['VENTA' => $data["FK_VENTA"], 'ID VALE' => $vale]);

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Venta Vale: Error al guardar.', ['VENTA' => $data["FK_VENTA"]]);

			/*  --------------------------------------------------------------------------------- */

		}
    }

    public static function valePDF($datos){

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $fecha = $fecha.' '.$hora;
        $generador = ucfirst($user->name);
        $inicio = date('Y-m-d', strtotime($datos['data']['inicio']));
        $final = date('Y-m-d', strtotime($datos['data']['final']));
        $sucursal = $datos['data']['sucursal'];
        $accion = $datos['data']['accion'];

        // OBTENER DATOS 

        $vales = DB::connection('retail')->table('VENTAS_VALE')
        ->join('VENTAS', 'VENTAS.ID', '=', 'VENTAS_VALE.FK_VENTA')
        ->leftjoin('VENTAS_ANULADO', 'VENTAS_ANULADO.FK_VENTA', '=', 'VENTAS_VALE.FK_VENTA')
        ->leftJoin('CLIENTES', function($join){
                        $join->on('CLIENTES.CODIGO', '=', 'VENTAS.CLIENTE')
                             ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
                    })
        ->select(DB::raw('CLIENTES.NOMBRE AS CLIENTE'),
            DB::raw('CLIENTES.FK_EMPRESA AS EMPRESA_ID'),
            DB::raw('SUM(VENTAS.TOTAL) AS TOTAL'),
            DB::raw('VENTAS_VALE.MONEDA AS MONEDA'),
            DB::raw('CLIENTES.CODIGO AS COD_CLI'),
            DB::raw('EMPRESAS.NOMBRE AS EMPRESA'))
        ->leftjoin('EMPRESAS', 'EMPRESAS.ID', '=', 'CLIENTES.FK_EMPRESA')
        ->whereBetween('VENTAS.FECALTAS', [$inicio , $final])
            ->where([
                ['VENTAS.ID_SUCURSAL', '=', $sucursal],
                ['VENTAS_ANULADO.ANULADO', '<>', 1]
            ])
            ->groupBy('CLIENTES.CODIGO')
            ->get()
            ->toArray(); 

        //INICIAR VARIABLES

        $moneda = $vales[0]->MONEDA;
        $candec = (Parametro::candec($moneda))["CANDEC"];
        $total = 0;
        $intervalo = $inicio.'/'.$final;
        $articulos = [];
        $c_rows = 0;

        // INICIAR MPDF 

        $mpdf = new \Mpdf\Mpdf([
            'margin_left' => 20,
            'margin_right' => 20,
            'margin_top' => 18,
            'margin_bottom' => 10,
            'margin_header' => 5,
            'margin_footer' => 10
        ]);

        $mpdf->SetDisplayMode('fullpage');

        foreach ($vales as $key => $value) {
            $total = $total + $value->TOTAL;
            $nombre = strtolower($value->CLIENTE);
            $articulos[$c_rows]['NOMBRE'] = ucwords($nombre);
            $articulos[$c_rows]['TOTAL_VALE'] = Common::precio_candec($value->TOTAL, $candec);
            $empresa = strtolower($value->EMPRESA);
            $articulos[$c_rows]['EMPRESA'] = ucwords($empresa);
            $c_rows = $c_rows +1;
        }

        $total = Common::precio_candec($total, $candec);
        $data['c_rows'] = $c_rows;
        $data['fecha'] = $fecha;
        $data['generador'] = $generador;
        $data['intervalo'] = $intervalo;
        $data['articulos'] = $articulos;
        $data['total'] = $total;

        $namefile = 'reporteVale'.time().'.pdf';

        // CREAR HOJA 

        $html = view('pdf.rptVale', $data)->render();

        $mpdf->WriteHTML($html);

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle("ReporteVale");

        // DESCARGAR ARCHIVO PDF 

        if($accion=='ver'){
            $mpdf->Output($namefile,'I');
        }elseif($accion=='descargar'){
            $mpdf->Output($namefile,'D');
        }

        /*  --------------------------------------------------------------------------------- */
    }
}
