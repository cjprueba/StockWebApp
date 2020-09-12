<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Servicios extends Model
{

    protected $connection = 'retail';
    protected $table = 'services';
    public $timestamps = false;

    public static function servicios_pos($datos){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $codigo = substr($datos["codigo"], 1);

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE CODIGO PRODUCTO O CODIGO INTERNO

        $servicio = Servicios::select(DB::raw('CODIGO, DESCRIPCION, MANUAL_DESCRIPCION, MANUAL_PRECIO, IVA'))
        ->where('CODIGO', '=', $codigo)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
        
        if (count($servicio) > 0) {

        	/*  --------------------------------------------------------------------------------- */

            return ["response" => true, "servicio" => $servicio[0]];

            /*  --------------------------------------------------------------------------------- */

        } else {

        	/*  --------------------------------------------------------------------------------- */

            return ["response" => false];

            /*  --------------------------------------------------------------------------------- */
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function generarConsulta($sucursal, $inicio, $final, $order, $dir){

        $delivery = DB::connection('retail')->table('VENTASDET_SERVICIOS')
            ->leftjoin('VENTAS' , function($join){
                            $join->on('VENTAS.CODIGO', '=', 'VENTASDET_SERVICIOS.CODIGO')
                                 ->on('VENTAS.ID_SUCURSAL', '=', 'VENTASDET_SERVICIOS.ID_SUCURSAL')
                                 ->on('VENTAS.CAJA','=','VENTASDET_SERVICIOS.CAJA');
                        })
            ->leftjoin('VENTAS_ANULADO', 'VENTAS_ANULADO.FK_VENTA', '=', 'VENTAS.ID')
            ->leftJoin('CLIENTES', function($join){
                            $join->on('CLIENTES.CODIGO', '=', 'VENTAS.CLIENTE')
                                 ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
                        })
            ->select(DB::raw('CLIENTES.NOMBRE AS CLIENTE'),
                DB::raw('SUM(VENTASDET_SERVICIOS.PRECIO) AS TOTAL'),
                DB::raw('VENTAS.MONEDA AS MONEDA'),
                DB::raw('VENTAS.CAJA AS CAJA'),
                DB::raw('VENTAS.CODIGO AS CODIGO_VENTA'),
                DB::raw('VENTAS.FECALTAS AS FECHA'))
            ->whereBetween('VENTAS.FECALTAS', [$inicio , $final])
                ->where([
                    ['VENTAS.ID_SUCURSAL', '=', $sucursal],
                    ['VENTAS_ANULADO.ANULADO', '<>', 1]
                ])
            ->groupBy('CLIENTES.CODIGO')
            ->orderBy($order,$dir)
            ->get()
            ->toArray(); 

        return $delivery;
    }

    public static function rptServicioDelivery($datos){

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

        $delivery = Servicios::generarConsulta($sucursal, $inicio, $final, $order, $dir);
        
        //INICIAR VARIABLES

        $moneda = $delivery[0]->MONEDA;
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

        foreach ($delivery as $key => $value) {

            $articulos[$c_rows]['CODIGO'] = $value->CODIGO_VENTA;
            $total = $total + $value->TOTAL;
            $nombre = strtolower($value->CLIENTE);
            $articulos[$c_rows]['NOMBRE'] = ucwords($nombre);
            $articulos[$c_rows]['TOTAL'] = Common::formato_precio($value->TOTAL, $candec);
            $fechaV = substr($value->FECHA,0,-9);
            $articulos[$c_rows]['FECHA'] = $fechaV;
            if($c_rows == $limite){
                $articulos[$c_rows]['SALTO'] = true;
                $limite = $limite + 36;
            }else{

                $articulos[$c_rows]['SALTO'] = false;
            }
            $c_rows = $c_rows + 1;
        }

        $total = Common::formato_precio($total, $candec);
        $namefile = 'reporteServicioDelivery'.time().'.pdf';
        $data['c_rows'] = $c_rows;
        $data['fecha'] = $fecha;
        $data['generador'] = $generador;
        $data['intervalo'] = $intervalo;
        $data['articulos'] = $articulos;
        $data['total'] = $total;

        $html = view('pdf.rptServicioDelivery', $data)->render();

        $mpdf->WriteHTML($html);

        // CREAR HOJA 

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle("ReporteDelivery");

        // DESCARGAR ARCHIVO PDF 

        $mpdf->Output();
        
        /*  --------------------------------------------------------------------------------- */
    }

    public static function generarReporteDelivery($request) {

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
                            1 => 'VENTAS.CODIGO',
                            2 => 'CLIENTES.NOMBRE',
                            3 => 'VENTAS.FECALTAS',
                            4 => 'VENTASDET_SERVICIOS.PRECIO'
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
        
        /*  --------------------------------------------------------------------------------- */

        //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

        $posts = Servicios::generarConsulta($sucursal, $inicio, $final, $order, $dir);

        /*  ************************************************************ */

        $data = array();

        $moneda = $posts[0]->MONEDA;
        $candec = (Parametro::candec($moneda))["CANDEC"];

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts))
        {
            foreach ($posts as $post)
            {

                /*  --------------------------------------------------------------------------------- */

                // CARGAR EN LA VARIABLE 

                $cliente = strtolower($post->CLIENTE);
                $nestedData['ITEM'] = $item;
                $nestedData['CODIGO'] = $post->CODIGO_VENTA;
                $nestedData['CLIENTE'] = ucwords($cliente);
                $fechaV = substr($post->FECHA,0,-9);
                $nestedData['FECHA'] = $fechaV;
                $nestedData['TOTAL'] = Common::formato_precio($post->TOTAL, $candec);

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
