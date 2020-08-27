<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;

class VentaTransferencia extends Model
{
    protected $connection = 'retail';
    protected $table = 'ventas_transferencia';

    public static function guardar_referencia($data){

    	try {

	    	/*  --------------------------------------------------------------------------------- */

	    	$transferencia = VentaTransferencia::insertGetId([
	    		'FK_BANCO' => $data["FK_BANCO"],
	    		'FK_VENTA' => $data["FK_VENTA"],
	    		'MONTO' => $data["MONTO"],
	    		'MONEDA' => $data["MONEDA"]
	    	]);

	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Venta Transferencia: Éxito al guardar.', ['VENTA' => $data["FK_VENTA"], 'ID TRANSFERENCIA' => $transferencia]);

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Venta Transferencia: Error al guardar.', ['VENTA' => $data["FK_VENTA"]]);

			/*  --------------------------------------------------------------------------------- */

		}
    }

    public static function transferenciaPDF($datos){

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $fecha = $fecha.' '.$hora;
        $generador = ucfirst($user->name);
        $inicio = date('Y-m-d', strtotime($datos['data']['inicio']));
        $final = date('Y-m-d', strtotime($datos['data']['final']));
        $sucursal = $datos['data']['sucursal'];

        // OBTENER DATOS 

	    $ventaTransferencia = VentaTransferencia::join('VENTAS', 'VENTAS.ID', '=', 'VENTAS_TRANSFERENCIA.FK_VENTA')
	        ->leftjoin('VENTAS_ANULADO', 'VENTAS_ANULADO.FK_VENTA', '=', 'VENTAS_TRANSFERENCIA.FK_VENTA')
	        ->leftJoin('CLIENTES', function($join){
	                        $join->on('CLIENTES.CODIGO', '=', 'VENTAS.CLIENTE')
	                             ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
	                    })
	        ->select(DB::raw('CLIENTES.NOMBRE AS CLIENTE'),
	            DB::raw('VENTAS_TRANSFERENCIA.FK_BANCO AS BANCO_ID'),
	            DB::raw('SUM(VENTAS_TRANSFERENCIA.MONTO) AS TOTAL'),
	            DB::raw('VENTAS_TRANSFERENCIA.MONEDA AS MONEDA'),
	            DB::raw('CLIENTES.CODIGO AS COD_CLI'),
	            DB::raw('BANCOS.DESCRIPCION AS BANCO'))
	        ->leftjoin('BANCOS', 'BANCOS.ID', '=', 'VENTAS_TRANSFERENCIA.FK_BANCO')
	        ->whereBetween('VENTAS.FECALTAS', [$inicio , $final])
	            ->where([
	                ['VENTAS.ID_SUCURSAL', '=', $sucursal],
	                ['VENTAS_ANULADO.ANULADO', '<>', 1]
	            ])
	            ->groupBy('CLIENTES.CODIGO')
	            ->get()
	            ->toArray(); 
            
        //INICIAR VARIABLES
        
        $moneda = $ventaTransferencia[0]["MONEDA"];
        $candec = (Parametro::candec($moneda))["CANDEC"];
        $intervalo = $inicio.'/'.$final;
        $total = 0;
        $c_rows = 0;
        $articulos = [];
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

        foreach ($ventaTransferencia as $key => $value) {

            $total = $total + $value["TOTAL"];
            $nombre = strtolower($value["CLIENTE"]);
            $articulos[$c_rows]['NOMBRE'] = ucwords($nombre);
            $articulos[$c_rows]['TOTAL'] = Common::formato_precio($value["TOTAL"], $candec);
            $banco = strtolower($value["BANCO"]);
            $articulos[$c_rows]['BANCO'] = ucwords($banco);
            if($c_rows == $limite){
            	$articulos[$c_rows]['SALTO'] = true;
            	$limite = $limite + 32;
            }else{

            	$articulos[$c_rows]['SALTO'] = false;
            }
            $c_rows = $c_rows + 1;
        }

        $total = Common::formato_precio($total, $candec);
        $namefile = 'reporteVentaTransferencia'.time().'.pdf';
		$data['c_rows'] = $c_rows;
        $data['fecha'] = $fecha;
        $data['generador'] = $generador;
        $data['intervalo'] = $intervalo;
        $data['articulos'] = $articulos;
        $data['total'] = $total;

        $html = view('pdf.rptVentaTransferencia', $data)->render();

        $mpdf->WriteHTML($html);

        // CREAR HOJA 

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle("ReporteVentaTransferencia");

        // DESCARGAR ARCHIVO PDF 

        $mpdf->Output();

        /*  --------------------------------------------------------------------------------- */
    }

    public static function generarVentaTransferencia($request) {

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
                            1 => 'CLIENTE',
                            2 => 'TOTAL'
                        );
        

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
		$item = 1;
        
        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE VALOR EN VARIABLE SEARCH

            /*  ************************************************************ */

            //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

            $posts = VentaTransferencia::join('VENTAS', 'VENTAS.ID', '=', 'VENTAS_TRANSFERENCIA.FK_VENTA')
	        ->leftjoin('VENTAS_ANULADO', 'VENTAS_ANULADO.FK_VENTA', '=', 'VENTAS_TRANSFERENCIA.FK_VENTA')
	        ->leftJoin('CLIENTES', function($join){
	                        $join->on('CLIENTES.CODIGO', '=', 'VENTAS.CLIENTE')
	                             ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
	                    })
	        ->select(DB::raw('CLIENTES.NOMBRE AS CLIENTE'),
	            DB::raw('VENTAS_TRANSFERENCIA.FK_BANCO AS BANCO_ID'),
	            DB::raw('SUM(VENTAS_TRANSFERENCIA.MONTO) AS TOTAL'),
	            DB::raw('VENTAS_TRANSFERENCIA.MONEDA AS MONEDA'),
	            DB::raw('CLIENTES.CODIGO AS COD_CLI'),
	            DB::raw('BANCOS.DESCRIPCION AS BANCO'))
	        ->leftjoin('BANCOS', 'BANCOS.ID', '=', 'VENTAS_TRANSFERENCIA.FK_BANCO')
	        ->whereBetween('VENTAS.FECALTAS', [$inicio , $final])
	            ->where([
	                ['VENTAS.ID_SUCURSAL', '=', $sucursal],
	                ['VENTAS_ANULADO.ANULADO', '<>', 1]
	            ])
	            ->groupBy('CLIENTES.CODIGO')
                ->get()
                ->toArray();

            /*  ************************************************************ */

        $data = array();

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts))
        {
            foreach ($posts as $post)
            {

                /*  --------------------------------------------------------------------------------- */

                // CARGAR EN LA VARIABLE 

                $cliente = strtolower($post["CLIENTE"]);
                $nestedData['ITEM'] = $item;
                $nestedData['CLIENTE'] = ucwords($cliente);
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
