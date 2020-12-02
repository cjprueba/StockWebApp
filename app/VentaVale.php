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

    public static function generarConsulta($sucursal, $inicio, $final, $order, $dir){

        $vales = VentaVale::join('VENTAS', 'VENTAS.ID', '=', 'VENTAS_VALE.FK_VENTA')
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
            DB::raw('EMPRESAS.NOMBRE AS EMPRESA'),
            DB::raw('VENTAS.FECALTAS AS FECHA'))
        ->leftjoin('EMPRESAS', 'EMPRESAS.ID', '=', 'CLIENTES.FK_EMPRESA')
        ->whereBetween('VENTAS.FECALTAS', [$inicio , $final])
            ->where([
                ['VENTAS.ID_SUCURSAL', '=', $sucursal],
                ['VENTAS_ANULADO.ANULADO', '<>', 1]
            ])
            ->groupBy('CLIENTES.CODIGO')
            ->orderBy($order,$dir)
            ->get()
            ->toArray(); 

        return $vales;
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
        $order ='VENTAS.FECALTAS';
        $dir = 'ASC';

        // OBTENER DATOS 

        $vales = VentaVale::generarConsulta($sucursal, $inicio, $final, $order, $dir); 

        //INICIAR VARIABLES

        $moneda = $vales[0]["MONEDA"];
        $candec = (Parametro::candec($moneda))["CANDEC"];
        $total = 0;
        $intervalo = $inicio.'/'.$final;
        $articulos = [];
        $c_rows = 0;
        $limite = 27;

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
            $total = $total + $value["TOTAL"];
            $nombre = mb_strtolower($value["CLIENTE"]);
            $articulos[$c_rows]['NOMBRE'] = utf8_decode(utf8_encode(ucwords($nombre));
            $articulos[$c_rows]['TOTAL_VALE'] = Common::precio_candec($value["TOTAL"], $candec);
            $empresa = mb_strtolower($value["EMPRESA"]);
            $fecha = substr($value["FECHA"],0,-9);
            $articulos[$c_rows]['FECHA'] = $fecha;
            $articulos[$c_rows]['EMPRESA'] = utf8_decode(utf8_encode(ucwords($empresa));

            if($c_rows == $limite){
                $articulos[$c_rows]['SALTO'] = true;
                $limite = $limite + 32;
            }else{

                $articulos[$c_rows]['SALTO'] = false;
            }
            $c_rows = $c_rows +1;
        }

        $total = Common::precio_candec($total, $candec);
        $data['c_rows'] = $c_rows;
        $data['fecha'] = $fecha;
        $data['generador'] = $generador;
        $data['intervalo'] = $intervalo;
        $data['articulos'] = $articulos;
        $data['total'] = $total;
        $data['sucursal'] = $sucursal;

        $namefile = 'reporteVale'.time().'.pdf';

        // CREAR HOJA 

        $html = view('pdf.rptVale', $data)->render();

        $mpdf->WriteHTML($html);

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle("ReporteVale");

        // DESCARGAR ARCHIVO PDF 

        $mpdf->Output();

        /*  --------------------------------------------------------------------------------- */
    }
    public static function generarVentaVale($request) {

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $sucursal = $request->input('sucursal');
        $inicio =  date('Y-m-d', strtotime($request->input('inicio')));
        $final = date('Y-m-d', strtotime($request->input('final')));

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'VENTAS.FECALTAS', 
                            1 => 'CLIENTES.NOMBRE',
                            2 => 'EMPRESA.NOMBRE',
                            3 => 'VENTAS.FECALTAS',
                            4 => 'VENTAS.TOTAL'
                        );

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        $item = 1;
        
        /*  --------------------------------------------------------------------------------- */

        //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

        $posts = VentaVale::generarConsulta($sucursal, $inicio, $final, $order, $dir); 

        /*  ************************************************************ */

        $data = array();

        /*  --------------------------------------------------------------------------------- */

        $moneda = $posts[0]["MONEDA"];
        $candec = (Parametro::candec($moneda))["CANDEC"];

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts))
        {
            foreach ($posts as $post)
            {

                /*  --------------------------------------------------------------------------------- */

                // CARGAR EN LA VARIABLE 

                $cliente = mb_strtolower($post["CLIENTE"]);
                $empresa = mb_strtolower($post["EMPRESA"]);
                $nestedData['ITEM'] = $item;
                $nestedData['CLIENTE'] = utf8_decode(utf8_encode(ucwords($cliente)));
                $nestedData['EMPRESA'] = utf8_decode(utf8_encode(ucwords($empresa)));
                $fecha = substr($post["FECHA"],0,-9);
                $nestedData['FECHA'] = $fecha;
                $nestedData['TOTAL'] = Common::formato_precio($post["TOTAL"], $candec);

                $data[] = $nestedData;
                $item = $item +1;

                /*  --------------------------------------------------------------------------------- */

            }
        }
        
        /*  --------------------------------------------------------------------------------- */

        // PREPARAR EL ARRAY A ENVIAR 

        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($item),  
                    "recordsFiltered" => intval($item), 
                    "data"            => $data   
                    );
        
        /*  --------------------------------------------------------------------------------- */

        // CONVERTIR EN JSON EL ARRAY Y ENVIAR 

       return $json_data; 

        /*  --------------------------------------------------------------------------------- */
    }
}
