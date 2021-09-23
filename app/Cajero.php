<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;
use App\Common;
use DateTime;

class Cajero extends Model
{
	protected $connection = 'retail';
    protected $table = 'users';

    public static function obtenerDatos($data, $order, $dir){

        $inicio = date('Y-m-d', strtotime($data['inicio']));
        $final = date('Y-m-d', strtotime($data['final']));
        $codigoCajero = $data['codigoCajero'];
        $sucursal = $data['sucursal'];

        $ventaCajero = DB::connection('retail')->table('VENTAS')

                ->select(DB::raw('VENTAS.FECHA AS FECHA, 
                                  COUNT(VENTAS.ID) AS TOTAL, 
	                    		  USERS.NAME AS CAJERO' 
	                    		)
            			)

                ->leftjoin('VENTAS_ANULADO', 'VENTAS_ANULADO.FK_VENTA', '=', 'VENTAS.ID')
                ->leftjoin('USERS', function($join){
                            $join->on('USERS.NAME', 'LIKE', DB::raw("CONCAT(VENTAS.USER, '%')"))
                                 ->on('USERS.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
                        })
                
                ->where([   
                    ['VENTAS.ID_SUCURSAL', '=', $sucursal],
                    ['USERS.ID', '=', $codigoCajero],
                    ['VENTAS_ANULADO.ANULADO', '<>', 1]])
                ->whereBetween('VENTAS.FECHA', [$inicio , $final])
                ->groupBy('VENTAS.FECHA')
                ->orderBy('VENTAS.FECHA')
                ->get();

        return $ventaCajero;
    }

	public static function rptVentaCajero($datos){

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
        $fecha = date("Y-m-d");
        $hora = date("H:i:s");
        $fecha = $fecha.' '.$hora;
        $generador = ucfirst($user->name);
        $inicio = date('Y-m-d', strtotime($datos['data']['inicio']));
        $final = date('Y-m-d', strtotime($datos['data']['final']));
        $cajero = $datos['data']['cajero'];
        $sucursal = $datos['data']['sucursal'];
        $order ='VENTAS.FECALTAS';
        $dir = 'ASC';

        // OBTENER DATOS 

        $ventaCajero = Cajero::obtenerDatos($datos['data'], $order, $dir); 

        //INICIAR VARIABLES
        
        
         
        $intervalo = $inicio.'/'.$final;
        $total = 0;
        $c_rows = 0;
        $articulos = [];
        $limite = 35;
        $totalPagado = 0;


        // INICIAR MPDF 

        $mpdf = new \Mpdf\Mpdf([
            'margin_left' => 20,
            'margin_right' => 20,
            'margin_top' => 16,
            'margin_bottom' => 10,
            'margin_header' => 5,
            'margin_footer' => 10
        ]);

        $mpdf->SetDisplayMode('fullpage');

        foreach ($ventaCajero as $key => $value) {

            $total = $total + $value->TOTAL;
            $iva = $iva + $value->IVA;
            $subtotal = $subtotal + $value->SUBTOTAL;
            $nombre = mb_strtolower($value->CLIENTE);
            $vendedor = mb_strtolower($value->VENDEDOR);
            $nombre = substr($nombre,0,27);
            $articulos[$c_rows]['NOMBRE'] = utf8_decode(utf8_encode(ucwords($nombre)));
            $articulos[$c_rows]['CODIGO'] = $value->ID;
            $fecha = substr($value->FECHA,0,-9);
            $articulos[$c_rows]['FECHA'] = $fecha;
            $articulos[$c_rows]['CAJERO'] = utf8_decode(utf8_encode(ucwords($cajero)));
            $articulos[$c_rows]['TOTAL'] = Common::formato_precio($value->TOTAL, $candec);

            // CREAR HOJA 

            $articulos[$c_rows]['SALTO'] = false;

            if($c_rows == $limite){
                
                $articulos[$c_rows]['SALTO'] = true;
                $limite = $limite + 42;
            }

            $c_rows = $c_rows + 1;
        }

        $namefile = 'reporteVentaCajero'.time().'.pdf';
        $data['c_rows'] = $c_rows;
        $data['fecha'] = $fecha;
        $data['generador'] = $generador;
        $data['intervalo'] = $intervalo;
        $data['total'] = Common::formato_precio($total, $candec);

        $html = view('pdf.rptVentaCajero', $data)->render();

        $mpdf->WriteHTML($html);

        // CREAR HOJA 

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle("ReporteVentaCajero");

        // DESCARGAR ARCHIVO PDF 

        $mpdf->Output();


        /*  --------------------------------------------------------------------------------- */
    }

    public static function busquedaCajero($datos){
		$user = auth()->user();
		$cajeros = Cajero::select(DB::raw('users.id AS ID, users.name AS NOMBRE, users.email AS EMAIL'))
            			->leftjoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                        ->where('users.ID_SUCURSAL', '=', $datos['data']['selectedSucursal'])
                        ->where('model_has_roles.role_id','=', '14')
                        ->get();

            
        return['cajero'=> $cajeros];
    }

    public static function cajero_datatable($request){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
        
        /*  --------------------------------------------------------------------------------- */
        
        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'ID',
                            1 => 'EMAIL',
                            2 => 'NOMBRE'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE USERS ENCONTRADAS 

        $totalData =   users::leftjoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
                        ->where('model_has_roles.role_id','=', '14')
						->count();  

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE VALOR EN VARIABLE SEARCH

        if(empty($request->input('search.value')))
        {            

            /*  ************************************************************ */

            //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

            $posts = Cajero::select(DB::raw('users.id AS ID, users.name AS NOMBRE, users.email AS EMAIL'))
            			->leftjoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
                        ->where('model_has_roles.role_id','=', '14')
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();

            /*  ************************************************************ */

        } else {

            /*  ************************************************************ */

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            /*  ************************************************************ */

            // CARGAR LOS PRODUCTOS FILTRADOS EN DATATABLE

            $posts = Cajero::select(DB::raw('users.id AS ID, users.name AS NOMBRE, users.email AS EMAIL'))
            			->leftjoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
                        ->where('model_has_roles.role_id','=', '14')
                            ->where(function ($query) use ($search) {
                                $query->where('users.email','LIKE',"%{$search}%")
                                      ->orWhere('users.id', 'LIKE',"%{$search}%")
                                      ->orWhere('users.name', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = Cajero::where(function ($query) use ($search) {
                                $query->where('user.email','LIKE',"%{$search}%")
                                      ->orWhere('users.id', 'LIKE',"%{$search}%")
                                      ->orWhere('users.name', 'LIKE',"%{$search}%");
                            })
                            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
                             ->count();

            /*  ************************************************************ */  

        }

        $data = array();

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts))
        {
            foreach ($posts as $post)
            {

                /*  --------------------------------------------------------------------------------- */

                // CARGAR EN LA VARIABLE 

                $nestedData['ID'] = $post->ID;
                $nestedData['EMAIL'] = $post->EMAIL;
                $nestedData['NAME'] = $post->NOMBRE;

                $data[] = $nestedData;

                /*  --------------------------------------------------------------------------------- */

            }
        }
        
        /*  --------------------------------------------------------------------------------- */

        // PREPARAR EL ARRAY A ENVIAR 

        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );
        
        /*  --------------------------------------------------------------------------------- */

        // CONVERTIR EN JSON EL ARRAY Y ENVIAR 

       return $json_data; 
    }
    public static function generarReporteVentaCajero($request) {

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $sucursal = $request->input('sucursal');
        $inicio =  date('Y-m-d', strtotime($request->input('inicio')));
        $final = date('Y-m-d', strtotime($request->input('final')));
        $cajero = $request->input('cajero');

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'ID', 
                            1 => 'CAJERO',
                            2 => 'FECHA',
                            3 => 'TOTAL'
                        );
        

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $item = 1;

        $datos = array(
                'sucursal' => $sucursal,
                'inicio' => $inicio,
                'final' => $final,
                'codigoCajero' => $cajero,
            );
        

    
        /*  --------------------------------------------------------------------------------- */

        //  CARGAR TODOS LOS DATOS ENCONTRADOS 


        $posts = Cajero::obtenerDatos($datos, $order, $dir);     


          // ************************************************************ 

       
        $data = array();
        
        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts)){
            foreach ($posts as $post){

                /*  --------------------------------------------------------------------------------- */

                // CARGAR EN LA VARIABLE 

                // $cliente = mb_strtolower($post->CLIENTE);
                // $vendedor = mb_strtolower($post->VENDEDOR);
                // $cliente = substr($cliente,0,27);
                $nestedData['ID'] = $item;
                $nestedData['CAJERO'] =utf8_decode(utf8_encode(ucwords($post->CAJERO))) ;
                $fecha = substr($post->FECHA,0,-9);
                $nestedData['FECHA'] = $fecha;   
                
                $nestedData['TOTAL'] = $post->TOTAL;
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
