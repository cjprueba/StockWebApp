<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;

class VentaTarjeta extends Model
{
    protected $connection = 'retail';
    protected $table = 'ventas_tarjeta';

    public static function guardar_referencia($data){

    	try {

	    	/*  --------------------------------------------------------------------------------- */

	    	$tarjeta = VentaTarjeta::insertGetId([
	    		'FK_TARJETA' => $data["FK_TARJETA"],
	    		'FK_VENTA' => $data["FK_VENTA"],
	    		'MONTO' => $data["MONTO"],
	    		'MONEDA' => $data["MONEDA"]
	    	]);

	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Venta Tarjeta: Éxito al guardar.', ['VENTA' => $data["FK_VENTA"], 'ID TARJETA' => $tarjeta]);

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Venta Tarjeta: Error al guardar.', ['VENTA' => $data["FK_VENTA"]]);

			/*  --------------------------------------------------------------------------------- */

		}
    }

    public static function generarConsulta($sucursal, $inicio, $final, $order, $dir){

        $ventaTarjeta = VentaTarjeta::join('VENTAS', 'VENTAS.ID', '=', 'VENTAS_TARJETA.FK_VENTA')
            ->leftjoin('VENTAS_ANULADO', 'VENTAS_ANULADO.FK_VENTA', '=', 'VENTAS_TARJETA.FK_VENTA')
            ->leftJoin('CLIENTES', function($join){
                            $join->on('CLIENTES.CODIGO', '=', 'VENTAS.CLIENTE')
                                 ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
                        })
            ->select(DB::raw('CLIENTES.NOMBRE AS CLIENTE'),
                DB::raw('VENTAS.FECALTAS AS FECHA'),
                DB::raw('VENTAS_TARJETA.FK_TARJETA AS ID_TARJETA'),
                DB::raw('SUM(VENTAS_TARJETA.MONTO) AS TOTAL'),
                DB::raw('VENTAS_TARJETA.MONEDA AS MONEDA'),
                DB::raw('CLIENTES.CODIGO AS COD_CLI'),
                DB::raw('TARJETAS.DESCRIPCION AS TARJETAS'))
            ->leftjoin('TARJETAS', 'TARJETAS.CODIGO', '=', 'VENTAS_TARJETA.FK_TARJETA')
            ->whereBetween('VENTAS.FECALTAS', [$inicio , $final])
                ->where([
                    ['VENTAS.ID_SUCURSAL', '=', $sucursal],
                    ['VENTAS_ANULADO.ANULADO', '<>', 1]
                ])
                ->groupBy('VENTAS.ID')
                ->orderBy($order,$dir)
                ->get()
                ->toArray(); 

        return $ventaTarjeta;
    }

    public static function rptVentaTarjeta($datos){

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $fecha = $fecha.' '.$hora;
        $generador = ucfirst($user->name);
        $inicio = date('Y-m-d', strtotime($datos['data']['inicio']));
        $final = date('Y-m-d', strtotime($datos['data']['final']));
        $sucursal = $datos['data']['sucursal'];
        $order ='VENTAS.FECALTAS';
        $dir = 'ASC';

        // OBTENER DATOS 

	    $ventaTarjeta = VentaTarjeta::generarConsulta($sucursal, $inicio, $final, $order, $dir); 

        //INICIAR VARIABLES
        
        $moneda = $ventaTarjeta[0]["MONEDA"];
        $candec = (Parametro::candec($moneda))["CANDEC"];
        $intervalo = $inicio.'/'.$final;
        $total = 0;
        $c_rows = 0;
        $articulos = [];
        $limite = 30;

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

        foreach ($ventaTarjeta as $key => $value) {

            $total = $total + $value["TOTAL"];
            $nombre = strtolower($value["CLIENTE"]);
            $articulos[$c_rows]['NOMBRE'] = ucwords($nombre);
            $fecha = substr($value["FECHA"],0,-9);
            $articulos[$c_rows]['FECHA'] = $fecha;
            $articulos[$c_rows]['TOTAL'] = Common::formato_precio($value["TOTAL"], $candec);
            $tarjeta = strtolower($value["TARJETAS"]);
            $articulos[$c_rows]['TARJETA'] = ucwords($tarjeta);
            if($c_rows == $limite){
            	$articulos[$c_rows]['SALTO'] = true;
            	$limite = $limite + 36;
            }else{

            	$articulos[$c_rows]['SALTO'] = false;
            }
            $c_rows = $c_rows + 1;
        }

        $total = Common::formato_precio($total, $candec);
        $namefile = 'reporteVentaTarjeta'.time().'.pdf';
		$data['c_rows'] = $c_rows;
        $data['fecha'] = $fecha;
        $data['generador'] = $generador;
        $data['intervalo'] = $intervalo;
        $data['articulos'] = $articulos;
        $data['total'] = $total;

        $html = view('pdf.rptVentaTarjeta', $data)->render();

        $mpdf->WriteHTML($html);

        // CREAR HOJA 

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle("ReporteVentaTarjeta");

        // DESCARGAR ARCHIVO PDF 

        $mpdf->Output();

        /*  --------------------------------------------------------------------------------- */
    }

    public static function generarReporteVentaTarjeta($request) {

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
                            0 => 'ITEM', 
                            1 => 'CLIENTES.NOMBRE',
                            2 => 'TARJETAS.DESCRIPCION',
                            3 => 'VENTAS.FECALTAS',
                            4 => 'VENTAS_TARJETA.MONTO'
                        );
        

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if($order == 'ITEM'){
            $order ='VENTAS.FECALTAS';
        }
		$item = 1;
        
        // var_dump($order);
        /*  --------------------------------------------------------------------------------- */

        //  CARGAR TODOS LOS DATOS ENCONTRADOS 

        $posts = VentaTarjeta::generarConsulta($sucursal, $inicio, $final, $order, $dir);

        /*  ************************************************************ */

        $data = array();

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts)){
            foreach ($posts as $post){

                /*  --------------------------------------------------------------------------------- */

                // CARGAR EN LA VARIABLE 

                $cliente = strtolower($post["CLIENTE"]);
                $nestedData['ITEM'] = $item;
                $nestedData['CLIENTE'] = ucwords($cliente);
                $tarjeta = strtolower($post["TARJETAS"]);
                $nestedData['TARJETA'] = ucwords($tarjeta);
                $fecha = substr($post["FECHA"],0,-9);
                $nestedData['FECHA'] = $fecha;
                $nestedData['TOTAL'] = Common::formato_precio($post["TOTAL"], 0);

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
