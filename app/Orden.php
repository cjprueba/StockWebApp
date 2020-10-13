<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Fpdf\Fpdf;
use Mpdf\Mpdf;
use Luecano\NumeroALetras\NumeroALetras;
require_once '../vendor/autoload.php';
use Automattic\WooCommerce\Client;

class Orden extends Model
{
    //
    protected $connection = 'retail';
    protected $table = 'wp_orden';
    const CREATED_AT='FECALTAS';
    const UPDATED_AT='FECMODIF';

    public static function datatableOrden($request){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'ORDEN_ID', 
                            1 => 'NOMBRES', 
                            2 => 'CIUDAD',
                            3 => 'WP_ORDEN.FECALTAS',
                            4 => 'WP_ORDEN.HORALTAS',
                            5 => 'TOTAL',
                            6 => 'WP_ORDEN.ESTADO',
                            7 => 'ACCION'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE ORDENES ENCONTRADAS 

        $totalData = Orden::count();  
        
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        if($request->input('draw') == '1'){
            $dir = 'DESC';
        }
        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE VALOR EN VARIABLE SEARCH

        if(empty($request->input('search.value')))
        {            

            /*  ************************************************************ */

            //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

            $posts = Orden::select(DB::raw('WP_ORDEN.ORDEN_ID, 
            	WP_SHIPPING.NOMBRES AS CLIENTE,
            	WP_SHIPPING.APELLIDOS,
            	WP_SHIPPING.ESTADO,
            	WP_SHIPPING.CIUDAD, 
            	substring(WP_ORDEN.FECALTAS, 1, 11) AS FECHA, 
            	WP_ORDEN.HORALTAS AS HORA, 
            	WP_ORDEN.TOTAL,
            	WP_ORDEN.ESTADO AS ESTADO, 
            	WP_ORDEN.MONEDA'))
            ->leftjoin('WP_SHIPPING', 'WP_SHIPPING.ORDEN_ID', '=', 'WP_ORDEN.ORDEN_ID')
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

            $posts =  Orden::select(DB::raw('WP_ORDEN.ORDEN_ID, 
            	WP_SHIPPING.NOMBRES AS CLIENTE, 
            	WP_SHIPPING.APELLIDOS, 
            	WP_SHIPPING.ESTADO,
            	WP_SHIPPING.CIUDAD, 
            	substring(WP_ORDEN.FECALTAS, 1, 11) AS FECHA, 
            	WP_ORDEN.HORALTAS AS HORA, 
            	WP_ORDEN.TOTAL,
            	WP_ORDEN.ESTADO AS ESTADO, 
            	WP_ORDEN.MONEDA'))
            ->leftjoin('WP_SHIPPING', 'WP_SHIPPING.ORDEN_ID', '=', 'WP_ORDEN.ORDEN_ID')
            ->where(function ($query) use ($search) {
                                $query->where('WP_ORDEN.ORDEN_ID','LIKE',"%{$search}%");
                            })
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = Orden::where(function ($query) use ($search) {
                                $query->where('WP_ORDEN.ORDEN_ID','LIKE',"%{$search}%");
                            })->count();

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

                $cliente = strtolower($post->CLIENTE.' '.$post->APELLIDOS);
                $ciudad = strtolower($post->CIUDAD);
                $nestedData['ORDEN_ID'] = $post->ORDEN_ID;
                $nestedData['CLIENTE'] = ucwords(utf8_encode($cliente));
                $nestedData['CIUDAD'] = ucwords(utf8_encode($ciudad));
                $nestedData['FECHA'] = $post->FECHA;
                $nestedData['HORA'] = $post->HORA;
                $nestedData['TOTAL'] =Common::formato_precio($post->TOTAL,0);

                if ($post->ESTADO === "completed") {
                    $nestedData['ESTADO'] = '<span class="badge badge-secondary">Procesado</span>';
                } else if ($post->ESTADO === "enviado") {
                    $nestedData['ESTADO'] = '<span class="badge badge-info">Enviado</span>';
                }

               $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarDetalle' title='Detalle'><i class='fa fa-list text-primary' aria-hidden='true'></i></a>
               		&emsp;<a href='#' id='imprimirFactura' title='Factura'><i class='fa fa-file text-secondary' aria-hidden='true'></i></a>
                    &emsp;<a href='#' id='imprimirDireccion' title='Direccion'><i class='fa fa-print text-info' aria-hidden='true'></i></a> 
                    &emsp;<a href='#' id='enviarOrden' title='Enviar'><i class='fa fa-paper-plane text-primary'  aria-hidden='true'></i></a>" ;
                
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

        /*  --------------------------------------------------------------------------------- */
    }

    public static function enviarOrden($data){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $codigo = $data["data"];

        /*  --------------------------------------------------------------------------------- */

        // REVISAR ESTADO 

        $estado = Orden::verificarEstado($codigo);

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EL PEDIDO YA FUE ENVIADO

        if ($estado === "enviado") {
            return ["response" => false, "statusText" => "¡Ya se encuentra enviada!"];
        }

        /*  --------------------------------------------------------------------------------- */

        //SOLO SERA MODIFICADA SI SE ENCUENTRA EN ESTADO COMPLETADO

        if ($estado === "completed") {

            $orden = DB::connection('retail')
            ->table('wp_orden')
            ->where('ORDEN_ID','=', $codigo)
            ->update([
                'ESTADO' => "enviado"
            ]);

        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return ["response" => true];

        /*  --------------------------------------------------------------------------------- */
	}

    public static function verificarEstado($codigo) {

        /*  --------------------------------------------------------------------------------- */

        // VERIFICAR SI EXISTE 

        $orden = DB::connection('retail')
        ->table('wp_orden')
        ->select(DB::raw(
                        'ESTADO'
                    ))
        ->where('ORDEN_ID','=', $codigo)
        ->get();

        /*  --------------------------------------------------------------------------------- */
        
        // RETORNAL VALOR 
        
        if(count($orden) > 0){
            return $orden[0]->ESTADO;
        } else {
            return false;
        }

        /*  --------------------------------------------------------------------------------- */
	}

    public static function mostrarProductos($request){

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $codigo = $request->input('codigoOrden');

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'SKU',
                            1 => 'SKU',
                            2 => 'DESCRIPCION',
                            3 => 'CANTIDAD',
                            4 => 'PRECIO',
                            5 => 'WP_ORDEN_DET.TOTAL'
                            // 5 => 'PORC_DESCUENTO',
                            // 6 => 'TOTAL_DESCUENTO'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE PRODUCTOS ENCONTRADOS 

        $totalData = DB::connection('retail')->table('WP_ORDEN_DET')
                    ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'WP_ORDEN_DET.SKU')
                    ->where('WP_ORDEN_DET.ORDEN_ID','=', $codigo)
                    ->count();  
        
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $item = 1;
        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE VALOR EN VARIABLE SEARCH

        if(empty($request->input('search.value')))
        {            

            /*  ************************************************************ */

            //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

            $posts = DB::connection('retail')->table('WP_ORDEN_DET')
                         ->select(DB::raw('WP_ORDEN_DET.SKU, 
                         	PRODUCTOS.DESCRIPCION, 
                         	WP_ORDEN_DET.CANTIDAD, 
                         	WP_ORDEN_DET.PRECIO, 
                         	WP_ORDEN_DET.SUBTOTAL, 
                         	WP_ORDEN_DET.TOTAL, 
                         	WP_ORDEN.MONEDA'))
                         ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'WP_ORDEN_DET.SKU')
                         ->leftjoin('WP_ORDEN', 'WP_ORDEN.ORDEN_ID', '=', 'WP_ORDEN_DET.ORDEN_ID')
                         ->where('WP_ORDEN_DET.ORDEN_ID','=', $codigo)
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

            $posts =  DB::connection('retail')->table('WP_ORDEN_DET')
                         ->select(DB::raw('WP_ORDEN_DET.SKU, 
                         	PRODUCTOS.DESCRIPCION, 
                         	WP_ORDEN_DET.CANTIDAD, 
                         	WP_ORDEN_DET.PRECIO, 
                         	WP_ORDEN_DET.SUBTOTAL, 
                         	WP_ORDEN_DET.TOTAL, 
                         	WP_ORDEN.MONEDA'))
                         ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'WP_ORDEN_DET.SKU')
                         ->leftjoin('WP_ORDEN', 'WP_ORDEN.ORDEN_ID', '=', 'WP_ORDEN_DET.ORDEN_ID')
                         ->where('WP_ORDEN_DET.ORDEN_ID','=', $codigo)
                         ->where(function ($query) use ($search) {
                                $query->where('WP_ORDEN_DET.SKU','LIKE',"%{$search}%")
                                      ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = DB::connection('retail')->table('WP_ORDEN_DET')
                         ->select(DB::raw('WP_ORDEN_DET.SKU, 
                         	PRODUCTOS.DESCRIPCION, 
                         	WP_ORDEN_DET.CANTIDAD, 
                         	WP_ORDEN_DET.PRECIO, 
                         	WP_ORDEN_DET.SUBTOTAL, 
                         	WP_ORDEN_DET.TOTAL, 
                         	WP_ORDEN.MONEDA'))
                         ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'WP_ORDEN_DET.SKU')
                         ->leftjoin('WP_ORDEN', 'WP_ORDEN.ORDEN_ID', '=', 'WP_ORDEN_DET.ORDEN_ID')
                         ->where('WP_ORDEN_DET.ORDEN_ID','=', $codigo)
                            ->where(function ($query) use ($search) {
                                $query->where('WP_ORDEN_DET.SKU','LIKE',"%{$search}%")
                                      ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"%{$search}%");
                            })
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

                $nestedData['ITEM'] = $item;
                $nestedData['SKU'] = $post->SKU;
                $nestedData['DESCRIPCION'] = $post->DESCRIPCION;
                $nestedData['CANTIDAD'] = $post->CANTIDAD;
                $nestedData['PRECIO'] = Common::formato_precio($post->PRECIO, 2);
                $nestedData['TOTAL'] = Common::formato_precio($post->TOTAL, 0);
                // $nestedData['PORC_DESCUENTO'] = '';
                // $nestedData['TOTAL_DESCUENTO'] = Common::precio_candec($post->TOTAL, $post->MONEDA);


                $data[] = $nestedData;
                $item = $item +1;
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

        /*  --------------------------------------------------------------------------------- */
    }

    public static function mostrarCabecera($codigo){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
         
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        /*  --------------------------------------------------------------------------------- */

        $orden = DB::connection('retail')->table('WP_BILLING')
            		->select(DB::raw(
                        'WP_BILLING.ORDEN_ID,
                        WP_BILLING.NOMBRES AS CLIENTE,
                        WP_BILLING.APELLIDOS,  
                        WP_BILLING.DIRECCION_1,
                        WP_BILLING.DIRECCION_2,
                        WP_BILLING.CIUDAD,
                        WP_BILLING.ESTADO,
                        WP_BILLING.CODIGO_POSTAL,
                        WP_BILLING.CELULAR,
                        WP_BILLING.DOCUMENTO,
                        WP_BILLING.RUC,
                        WP_BILLING.EMAIL,
                        WP_ORDEN.MONEDA AS MONEDA,
                        WP_ORDEN.SHIPPING_TOTAL,
                        WP_ORDEN.TOTAL,
            			substring(WP_ORDEN.FECALTAS, 1, 11) AS FECHA,
            			substring(WP_ORDEN.CREACION_DIA, 1, 11) AS DIA,
                        WP_ORDEN.HORALTAS,
                        WP_ORDEN.ID,
                        WP_PAGO.NOTA_DEL_CLIENTE AS NOTA,
                        WP_PAGO.METODO_PAGO_TITULO AS METODO'
                    ))
                    ->leftjoin('WP_ORDEN', 'WP_ORDEN.ORDEN_ID', '=', 'WP_BILLING.ORDEN_ID')
                    ->leftjoin('WP_PAGO', 'WP_PAGO.ORDEN_ID', '=', 'WP_BILLING.ORDEN_ID')
			        ->where('WP_BILLING.ORDEN_ID','=', $codigo)
			        ->get()->toArray();

        /*  --------------------------------------------------------------------------------- */

        // RUC / CI

        if ($orden[0]->RUC === '' || $orden[0]->RUC === null) {
            $orden[0]->RUC = $orden[0]->DOCUMENTO;
        }

        $cliente = $orden[0]->CLIENTE.' '.$orden[0]->APELLIDOS;

        $orden[0]->CIUDAD = ucwords(strtolower($orden[0]->CIUDAD));

        $orden[0]->CLIENTE = ucwords(strtolower($cliente));

        if(empty($orden[0]->NOTA)){
            $orden[0]->NOTA = '--';
        }

        if(!empty($orden[0]->DIRECCION_2)){

            $orden[0]->DIRECCION_1 = $orden[0]->DIRECCION_1.', '.$orden[0]->DIRECCION_2.'.';
        }else{

            $orden[0]->DIRECCION_1 = $orden[0]->DIRECCION_1.'.';
        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR 

        return $orden[0];

        /*  --------------------------------------------------------------------------------- */
	}

    public static function mostrarCuerpo($data){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
         
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $codigo = $data;
        $data = array();

        /*  --------------------------------------------------------------------------------- */

        $orden_det = DB::connection('retail')->table('WP_ORDEN_DET')
            		->select(DB::raw(
                        'WP_ORDEN_DET.ORDEN_ID, 
                        WP_ORDEN_DET.SKU, 
                        PRODUCTOS.DESCRIPCION, 
                        WP_ORDEN_DET.CANTIDAD, 
                        WP_ORDEN_DET.PRECIO,
                        WP_ORDEN_DET.SUBTOTAL,
                        WP_ORDEN_DET.TOTAL,
                        WP_ORDEN.MONEDA,
                        10 AS IVA_PORCENTAJE,
                        PESO.PESO'
                    ))
                    ->leftjoin('WP_ORDEN', 'WP_ORDEN.ORDEN_ID', '=', 'WP_ORDEN_DET.ORDEN_ID')
                    ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'WP_ORDEN_DET.SKU')
                    ->leftjoin('PESO', 'PESO.CODIGO', '=', 'WP_ORDEN_DET.SKU')
        ->where('WP_ORDEN_DET.ORDEN_ID','=', $codigo)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        foreach ($orden_det as $key => $value) {
            
            /*  --------------------------------------------------------------------------------- */

            // BUSCAR IVA PRODUCTO

            $producto = DB::connection('retail')
            ->table('PRODUCTOS')
            ->select(DB::raw('IMPUESTO'))
            ->where('CODIGO', '=', $value->SKU)
            ->get();

            /*  --------------------------------------------------------------------------------- */

            $nestedData['ORDEN_ID'] = $value->ORDEN_ID;
            $nestedData['ITEM'] = $key;
            $nestedData['SKU'] = $value->SKU;
            $nestedData['DESCRIPCION'] = $value->DESCRIPCION;
            $nestedData['CANTIDAD'] = Common::precio_candec_sin_letra($value->CANTIDAD, 1);
            $nestedData['PRECIO'] = Common::precio_candec_sin_letra($value->PRECIO, $value->MONEDA);
            $nestedData['TOTAL'] = Common::precio_candec_sin_letra($value->TOTAL, $value->MONEDA);
            $nestedData['MONEDA'] = $value->MONEDA;
            $nestedData['IVA_PORCENTAJE'] = $producto[0]->IMPUESTO;

            if(empty($value->PESO)){
                $nestedData['PESO'] = 'No Disponible';
            }else{

                $nestedData['PESO'] = round(($value->PESO/1000), 3).'kg';;
            }

            /*  --------------------------------------------------------------------------------- */

            // CARGAR DATOS EN ARRAY

            $data[] = $nestedData;

            /*  --------------------------------------------------------------------------------- */

        }
        
        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return $data;

        /*  --------------------------------------------------------------------------------- */
    }

    public static function facturaPdf($dato){

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
        $dia = date("Y-m-d");

        // OBTENER DATOS DE CABECERA 

        $orden = Orden::mostrarCabecera($dato['data']);

        // OBTENER DATOS DETALLE 

        $orden_det = Orden::mostrarCuerpo($dato['data']);

        // CANTIDAD DE DECIMALES Y MONEDA

        $candec = 0;
        $moneda = 1;

        // INICIAR VARIABLES 

        $metodo = $orden->METODO;
        $c = 0;
        $c_rows = 0;
        $c_rows_array = count($orden_det);
        $c_filas_total = count($orden_det);
        $codigo = $orden->ORDEN_ID;
        $cliente = $orden->CLIENTE.' '.$orden->APELLIDOS;
        $direccion = $orden->DIRECCION_1;
        $direccion_2 = $orden->DIRECCION_2;
        $ruc = $orden->RUC;
        $documento = $orden->DOCUMENTO;
        $telefono = $orden->CELULAR;
        $ciudad = $orden->CIUDAD;
        $fecha = $orden->DIA;
        $ruc = $orden->RUC;
        $codigoPostal = $orden->CODIGO_POSTAL;
        $email = $orden->EMAIL;
        $nombre = 'Orden_'.$codigo.'_'.time().'';
        $articulos = [];
        $cantidad = 0;
        $switch_hojas = false;
        $namefile = 'boleta_de_venta_'.time().'.pdf';
        $letra = '';
        $total = Common::precio_candec_sin_letra($orden->TOTAL, $moneda);
        $envio = Common::precio_candec_sin_letra($orden->SHIPPING_TOTAL, $moneda);
        $subtotal = $orden->TOTAL-$orden->SHIPPING_TOTAL;
        $subtotal = Common::precio_candec_sin_letra($subtotal, $moneda);
        $factura = $orden->ID;

        /*  --------------------------------------------------------------------------------- */

        // CARGAR VARIABLES CABECERA
        
        $data['metodo'] = $metodo;
        $data['cliente'] = ucwords(strtolower($cliente));
        $data['documento'] = $documento;
        $data['ciudad'] = ucwords(strtolower($ciudad));
        $data['direccion'] = $direccion;
        $data['direccion_2'] = $direccion_2;
        $data['telefono'] = $telefono;
        $data['codigo'] = $codigo;
        $data['dia'] = $dia;
        $data['ruc'] = $ruc;        
        $data['fecha'] = $fecha;
        $data['nombre'] = $nombre;
        $data['c'] = $c;
        $data['tipo'] = 'online';
        $data['envio'] = $envio;
        $data['total'] = $total;
        $data['subtotal'] = $subtotal;
        $data['ruc'] = $ruc;
        $data['factura'] = $factura;
        $data['email'] = $email;
        $data['codigoPostal'] = $codigoPostal;

        $total = 0;
        
        // INICIAR MPDF 

		$mpdf = new \Mpdf\Mpdf([
			'margin_left' => 20,
			'margin_right' => 20,
			'margin_top' => 40,
			'margin_bottom' => 10,
			'margin_header' => 10,
			'margin_footer' => 10
		]);

        $mpdf->SetDisplayMode('fullpage');

		foreach ($orden_det as $key => $value) {

            $totalP = Common::quitar_coma($value["TOTAL"], $candec);

            //COTIZACION

           $cotizacion = $totalP/($value["CANTIDAD"]*$value["PRECIO"]);

            // PRECIO 

           $precio = $cotizacion*$value["PRECIO"];

            // SI NO ENCUENTRA COTIZACION RETORNAR 

            $articulos[$c_rows]["precio"] = Common::precio_candec_sin_letra($precio, $moneda);

            // CARGAR VARIABLES 

            $articulos[$c_rows]["cantidad"] = $value["CANTIDAD"];
            $articulos[$c_rows]["cod_prod"] = $value["SKU"];
            $articulos[$c_rows]["descripcion"] = $value["DESCRIPCION"];
            $articulos[$c_rows]["total"] = Common::precio_candec_sin_letra($totalP, $moneda);
            $articulos[$c_rows]["peso"] = $value["PESO"];

	        $total = $total+$totalP;
	        

            // CONTAR CANTIDAD DE FILAS DE HOJAS 

            $c_rows = $c_rows + 1;    

            // CONTAR LA CANTIDAD DE FILAS 

            $c = $c + 1;

            // SI CANTIDAD DE FILAS ES IGUAL A 18 ENTONCES CREAR PAGINA 

            if ($c_rows === 10){

                // AGREGAR ARTICULOS 

                $data['articulos'] = $articulos;

                // RESTAR LAS CANTIDADES CARGADAS 

                $c_rows_array = $c_rows_array - 10;

                // PONER TRUE SWITCH YA QUE CREO UNA PAGINA 

                $switch_hojas = true;

                // CARGAR SUB TOTALES POR HOJA
                $subtotal = $total;
		        $subtotal = Common::precio_candec_sin_letra($subtotal, $moneda);

		        $data['total'] = Common::precio_candec_sin_letra($total, $moneda);;
		        $data['subtotal'] = $subtotal;

                $html = view('pdf.facturaOrden', $data)->render();

                // SI NO ES LA PRIMERA HOJA AGREGAR PAGINA

                if ($c !== 10) {
                    $mpdf->AddPage();
                }

                // CERAR CONTADOR DE FILAS CARGADAS POR HOJAS Y ARTICULOS

                $c_rows = 0;
                $data['articulos'] = [];
                $articulos = [];
                    
                $mpdf->WriteHTML($html);

            } else if ($c_rows_array < 10 && $c_filas_total === $c) {
                
                // AGREGAR ARTICULOS 
                
                $data['articulos'] = $articulos;
                $subtotal = $total;
		        $subtotal = Common::precio_candec_sin_letra($subtotal, $moneda);
		        $total = $total+$orden->SHIPPING_TOTAL;
		        $data['total'] = Common::precio_candec_sin_letra($total, $moneda);
		        $data['subtotal'] = $subtotal;

                // CREAR HOJA 

                $html = view('pdf.facturaOrden', $data)->render();

                if ($switch_hojas === true) {
                    $mpdf->AddPage();
                }
                    
                $mpdf->WriteHTML($html);
            }
        }

        $mpdf->SetProtection(array('print'));
		$mpdf->SetTitle("Calbea/Factura");
        
        // DESCARGAR ARCHIVO PDF 

        $mpdf->Output();

        /*  --------------------------------------------------------------------------------- */
    }

    public static function direccionPDF($datos){

    	$user = auth()->user();

        // INICIAR VARIABLES 

        $dia = date("Y-m-d");
        $envio = $datos['data']['tipo'];
        $codigo = $datos['data']['codigo'];
        $hora = date("H:i:s");

    	$orden = Orden::mostrarCabecera($codigo);
    	$orden_id = $orden->ORDEN_ID;
        $cliente = $orden->CLIENTE.' '.$orden->APELLIDOS;
    	$cliente = ucwords(strtolower($cliente));
        $ciudad = ucwords(strtolower($orden->CIUDAD));

    	$mpdf = new \Mpdf\Mpdf([
			'mode' => 'B',
			'margin_left' => 10,
			'margin_right' => 10,
			'margin_top' => 40,
			'margin_header' => 10,
			'margin_footer' => 10,
			'watermarkAngle' => 135
		]);

    	if($datos['data']['tamanho'] == 'Grande'){
			$text2= '<div><br /><br /><br /><br />
					<span style="font-size: 105pt; color: #e02020; font-family: Comic Sans;">
						<b>CUIDADO<br />FRÁGIL</b>
					</span></div>';
			$text= '<br />';

    	}else{
    		$text= '<span style="font-size: 47pt; color: #e02020; font-family: Comic Sans;">
						<b>CUIDADO FRÁGIL</b>
					</span>';
			$text2= '';
    	}

        if(!empty($orden->DIRECCION_2)){

            $direccion = 'Dirección: '.$orden->DIRECCION_1.'<br /> 
                        Atención: '.$orden->DIRECCION_2.'.';
        }else{

            $direccion = 'Dirección: '.$orden->DIRECCION_1.'<br /><br />';
        }

        if(!empty($orden->CODIGO_POSTAL)){

            $codigoPostal = 'Código Postal: '.$orden->CODIGO_POSTAL.', ';
        }else{

            $codigoPostal = ' ';
        }

		$header = '
		<table 
			width="100%" style="
			vertical-align:center; 
			font-family: Comic Sans; 
			font-size: 16pt; 
			color: #1c6692;">
			<tr>
				<td width="40%" align="center">
					<img src="C:\laragon\www\StockWebApp\resources\imagenes\LOGO.png" width="240px">
					<br />
				</td>
			</tr>
		</table>';

		$mensaje = '<div class="text-left">
			<span style="font-size:12pt; color: #000000; font-family: Comic Sans;">En este periodo de cuarentena, nada es más seguro que comprar desde la comodidad y seguridad de tu casa, y recibir tu pedido rápidamente.</span>
					<br /><br />
			<span style="font-size:12pt; color: #000000; font-family: Comic Sans;">Calbea cuenta con productos importados que van desde comidas, cosméticos, decoraciones, utensilios de cocina y MUCHO MÁS.</span>
					<br /><br />
			<span style="font-size:12pt; color: #000000; font-family: Comic Sans;">No olvides seguir a @CalbeaOfficial en las redes sociales para acompañar todas las novedades de primera mano.</span><br /><br /></div>
		';

		$mpdf->SetHTMLHeader($header);

    	$html = '
			<html>
			<head>
			<style>
				p {	
					margin: 47pt; 
					font-size: 14pt;
				}
				td { 
					vertical-align: center; 
					text-align:justify;
				}
				body {
					text-align:center;
					font-family: Comic Sans;
					font-size: 14pt;
				}

				div{
					text-align="center";
				}
			</style>
			</head>
			<body>

			<table width="100%" style="font-family: Comic Sans; margin-top: 0px; padding-bottom: 0px;" cellpadding="10">
			<tr>
				<td width="30%" align="right">
					<span style="font-size: 18pt; color: #1c6692; font-family: Comic Sans;">
						<br/><b>PARA:</b>
					</span>
					<br /><br /><br /><br />
					<span style="font-size: 16pt; color: #1c6692; font-family: Comic Sans;">
						<b>ENVIO A:</b>
					</span>
				</td>
				<td width="50%" align="justify">
					<p><br />Nombre: '.$cliente.'<br />
					Tel: '.$orden->CELULAR.'<br /><br /></p>
					<p>'.$codigoPostal.''.$orden->ESTADO.', '.$ciudad.'.<br />'.$direccion.'
				</td>
			</tr></table><br />
			<div class="text">
				<span style="font-size: 16pt; color: #1c6692; font-family: Comic Sans;">
						<b>TIPO DE ENVIO:</b>
				</span>
				<span style="font-size: 16pt; color: #1c6692; 
						font-family: Comic Sans;"><b>'.$envio.'</b>
				</span><br />
				<br />'.$text.'<br />
			</div>
			<br/><br />
			<table width="100%" style=" vertical-align:center; font-family: Comic Sans;">
			<tr>
				<td width="50%" align="center">
					<div><span style="font-size:17pt; color: #1c6692; font-family: Comic Sans;">
						<br/><b>¡Tu paquete ha llegado!
					</span></div><br />
					'.$mensaje.'
					<div><span style="font-size: 16pt; color: #1c6692; font-family: Comic Sans;">
						<b>¡Muchas gracias que tengas un gran día!</b>
					</span></div>
					<br/><img src="C:\laragon\www\StockWebApp\resources\imagenes\LOGO.png" width="170px">
				</td><td width="8%" align="center">
					
				</td>
				<td width="42" align="center"><br/>
					<br/><img src="C:\laragon\www\StockWebApp\resources\imagenes\LOGO.png" width="280px">
					<br/><img src="C:\laragon\www\StockWebApp\resources\imagenes\qr-code-calbea.png" width="240px">
				</td>
			</tr></table>
			'.$text2.'
			</body>
			</html>
		';

		$mpdf->SetProtection(array('print'));
		$mpdf->SetTitle("Calbea/Direccion");
		$mpdf->SetDisplayMode('fullpage');

		// $mpdf->SetWatermarkImage($watermark, 0.15, '', array(20,135));
		// $mpdf->showWatermarkImage = true;

		$mpdf->WriteHTML($html);

		$mpdf->Output();
    }

    public static function datatablePendiente($request){

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'ORDEN_ID', 
                            1 => 'CLIENTE', 
                            3 => 'CIUDAD',
                            2 => 'FECHA',
                            3 => 'HORA',
                            5 => 'TOTAL',
                            // 6 => 'ESTADO',
                            6 => 'ACCION'
                        );
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS  

        $woocommerce = new Client(
            'https://www.calbea.com.py', // Your store URL
            'ck_14760e0d817b4c57551d17de6404aac61ebff682', // Your consumer key
            'cs_91baae6016d43f30e19d95b8259c3098abccfa9e', // Your consumer secret
            [
                'wp_json' => true, // Enable the WP REST API integration
                'wp_api' => true, // Enable the WP REST API integration
                'version' => 'wc/v3',// WooCommerce WP REST API version
                'query_string_auth' => true,
                'verify_ssl' => false 
            ]
        );

        $data = [
            'per_page' => '20',
		    'status' => 'pending',
		];

        $posts = ($woocommerce->get('orders', $data));
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE ORDENES ENCONTRADAS 

        $totalData = count($posts);  

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        /*  --------------------------------------------------------------------------------- */

        $data = array();

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts))
        {
            foreach ($posts as $post)
            {

                /*  --------------------------------------------------------------------------------- */

                // CARGAR EN LA VARIABLE 
                $cliente = $post->billing->first_name.' '.$post->billing->last_name;
                $nestedData['ORDEN_ID'] = $post->id;
                $nestedData['CLIENTE']  = ucwords(strtolower(utf8_encode($cliente)));
                $nestedData['CLIENTE'] = str_replace  ("'", "",  $nestedData['CLIENTE'] );
                $nestedData['CLIENTE'] = preg_replace('/[\x00-\x1F\x7F]/', '',  $nestedData['CLIENTE']);
                $nestedData['CLIENTE'] = preg_replace ('/[^\p{L}\p{N}]/u', '',  $nestedData['CLIENTE'] );
                $nestedData['CIUDAD'] = ucwords(strtolower(utf8_encode($post->billing->city)));
                $nestedData['CIUDAD'] = str_replace  ("'", "",  $nestedData['CIUDAD'] );
                $nestedData['CIUDAD'] = preg_replace('/[\x00-\x1F\x7F]/', '',  $nestedData['CIUDAD']);
                $nestedData['CIUDAD'] = preg_replace ('/[^\p{L}\p{N}]/u', '',  $nestedData['CIUDAD'] );
                $nestedData['FECHA'] = substr($post->date_created, 0, -9);
                $nestedData['HORA'] = substr($post->date_created, 11);
                $nestedData['TOTAL'] =Common::formato_precio($post->total,0);

                // if ($post->status === "pending") {
                //     $nestedData['ESTADO'] = '<span class="badge badge-secondary">Pendiente de Pago</span>';
                // } 

               $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarDetalle' title='Detalle'><i class='fa fa-list text-primary' aria-hidden='true'></i></a>
               		&emsp;<a href='#' id='imprimirFactura' title='Factura'><i class='fa fa-file text-secondary' aria-hidden='true'></i></a>
                    &emsp;<a href='#' id='imprimirDireccion' title='Direccion'><i class='fa fa-print text-info' aria-hidden='true'></i></a>" ;
                
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

        /*  --------------------------------------------------------------------------------- */
    }

    public static function datatableProcesando($request){

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'ID', 
                            1 => 'billing.first_name', 
                            3 => 'billing.city',
                            2 => 'date_created',
                            3 => 'date_created',
                            5 => 'TOTAL',
                            // 6 => 'ESTADO',
                            6 => 'ACCION'
                        );
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS  

        $woocommerce = new Client(
            'https://www.calbea.com.py', // Your store URL
            'ck_14760e0d817b4c57551d17de6404aac61ebff682', // Your consumer key
            'cs_91baae6016d43f30e19d95b8259c3098abccfa9e', // Your consumer secret
            [
                'wp_json' => true, // Enable the WP REST API integration
                'wp_api' => true, // Enable the WP REST API integration
                'version' => 'wc/v3',// WooCommerce WP REST API version
                'query_string_auth' => true,
                'verify_ssl' => false 
            ]
        );

        $data = [
            'per_page' => '20',
		    'status' => 'processing'
		];

        $posts = ($woocommerce->get('orders', $data));

        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE ORDENES ENCONTRADAS 

        $totalData = count($posts);  

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        /*  --------------------------------------------------------------------------------- */

        $data = array();

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts))
        {
            foreach ($posts as $post)
            {

                /*  --------------------------------------------------------------------------------- */

                // CARGAR EN LA VARIABLE 

                $cliente = $post->billing->first_name.' '.$post->billing->last_name;

                $nestedData['ORDEN_ID'] = $post->id;
                $nestedData['CLIENTE'] = ucwords(strtolower(utf8_encode($cliente)));
                $nestedData['CIUDAD'] = ucwords(strtolower(utf8_encode($post->billing->city)));
                $nestedData['FECHA'] = substr($post->date_created, 0, -9);
                $nestedData['HORA'] = substr($post->date_created, 11);
                $nestedData['TOTAL'] =Common::formato_precio($post->total,0);

                // if ($post->status === "pending") {
                //     $nestedData['ESTADO'] = '<span class="badge badge-secondary">Pendiente de Pago</span>';
                // } 

               $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarDetalle' title='Detalle'><i class='fa fa-list text-primary' aria-hidden='true'></i></a>
               		&emsp;<a href='#' id='imprimirFactura' title='Factura'><i class='fa fa-file text-secondary' aria-hidden='true'></i></a>
                    &emsp;<a href='#' id='imprimirDireccion' title='Direccion'><i class='fa fa-print text-info' aria-hidden='true'></i></a>" ;
                
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

        /*  --------------------------------------------------------------------------------- */
    }

    public static function mostrarCabeceraPendiente($codigo){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        $woocommerce = new Client(
            'https://www.calbea.com.py', // Your store URL
            'ck_14760e0d817b4c57551d17de6404aac61ebff682', // Your consumer key
            'cs_91baae6016d43f30e19d95b8259c3098abccfa9e', // Your consumer secret
            [
                'wp_json' => true, // Enable the WP REST API integration
                'wp_api' => true, // Enable the WP REST API integration
                'version' => 'wc/v3',// WooCommerce WP REST API version
                'query_string_auth' => true,
                'verify_ssl' => false 
            ]
        );
        $orden_id=$codigo["codigo"];

        $posts = ($woocommerce->get('orders/'.$orden_id));
       
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        /*  --------------------------------------------------------------------------------- */

        $nombre = $posts->billing->first_name.' '.$posts->billing->last_name;

        if(empty($posts->customer_note)){
            $posts->customer_note = '--';
        }

        if(!empty($posts->billing->address_2)){

            $posts->billing->address_1 = $posts->billing->address_1.', '.$posts->billing->address_2.'.';
        }else{

            $posts->billing->address_1 = $posts->billing->address_1.'.';
        }

        $orden = array(
        		'NOMBRE' => ucwords(strtolower($nombre)),
        		'DIRECCION_1' => $posts->billing->address_1,
        		'CIUDAD' => ucwords(strtolower($posts->billing->city)),
        		'CELULAR' => $posts->billing->phone,
        		'DOCUMENTO' => $posts->meta_data[0]->value,
        		'ESTADO' => $posts->billing->state,
        		'NOTA' => $posts->customer_note,
        	);

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR 

        return $orden;

        /*  --------------------------------------------------------------------------------- */
	}

    public static function mostrarProductosPendiente($request){

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $codigo = $request->input('codigoOrden');

        $woocommerce = new Client(
            'https://www.calbea.com.py', // Your store URL
            'ck_14760e0d817b4c57551d17de6404aac61ebff682', // Your consumer key
            'cs_91baae6016d43f30e19d95b8259c3098abccfa9e', // Your consumer secret
            [
                'wp_json' => true, // Enable the WP REST API integration
                'wp_api' => true, // Enable the WP REST API integration
                'version' => 'wc/v3',// WooCommerce WP REST API version
                'query_string_auth' => true ,
                'verify_ssl' => false
            ]
        );

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'SKU',
                            1 => 'DESCRIPCION',
                            2 => 'CANTIDAD',
                            3 => 'PRECIO',
                            4 => 'TOTAL'
                            // 5 => 'PORC_DESCUENTO',
                            // 6 => 'TOTAL_DESCUENTO'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        $orden = ($woocommerce->get('orders/'.$codigo));

        $posts = $orden->line_items;

        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE PRODUCTOS ENCONTRADOS 

        $totalData = count($posts);  
        
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $item = 1;

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE VALOR EN VARIABLE SEARCH

        $data = array();

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts))
        {
            foreach ($posts as $post)
            {

                /*  --------------------------------------------------------------------------------- */

                // CARGAR EN LA VARIABLE 
                $descripcion = utf8_encode($post->name);
                $nestedData['ITEM'] = $item;
                $nestedData['SKU'] = $post->sku;
                $nestedData['DESCRIPCION'] = utf8_encode($descripcion);
                $nestedData['CANTIDAD'] = $post->quantity;
                $nestedData['PRECIO'] = Common::formato_precio($post->price, 0);
                $nestedData['TOTAL'] = Common::formato_precio($post->total, 0);
                // $nestedData['PORC_DESCUENTO'] = '';
                // $nestedData['TOTAL_DESCUENTO'] = Common::precio_candec($post->TOTAL, $post->MONEDA);


                $data[] = $nestedData;
                $item = $item +1;
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

        /*  --------------------------------------------------------------------------------- */
    }

    public static function facturaPendientePDF($dato){

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
        $dia = date("Y-m-d");

        $woocommerce = new Client(
            'https://www.calbea.com.py', // Your store URL
            'ck_14760e0d817b4c57551d17de6404aac61ebff682', // Your consumer key
            'cs_91baae6016d43f30e19d95b8259c3098abccfa9e', // Your consumer secret
            [
                'wp_json' => true, // Enable the WP REST API integration
                'wp_api' => true, // Enable the WP REST API integration
                'version' => 'wc/v3',// WooCommerce WP REST API version
                'query_string_auth' => true ,
                'verify_ssl' => false
            ]
        );

        // OBTENER DATOS DE CABECERA 

        $orden = ($woocommerce->get('orders/'.$dato['data']));

        // OBTENER DATOS DETALLE 
		
		$orden_det = $orden->line_items;

        // CANTIDAD DE DECIMALES Y MONEDA

        $candec = 0;
        $moneda = 1;

        // INICIAR VARIABLES 

        $metodo = $orden->payment_method_title;
        $c = 0;
        $c_rows = 0;
        $c_rows_array = count($orden_det);
        $c_filas_total = count($orden_det);
        $codigo = $orden->id;
        $cliente = $orden->billing->first_name.' '.$orden->billing->last_name;
        $direccion = $orden->billing->address_1;
        $direccion_2 = $orden->billing->address_2;
        $ruc = $orden->meta_data[0]->value;
        $telefono = $orden->billing->phone;
        $ciudad = $orden->billing->city;
        $fecha = substr($orden->date_created, 0, -9);
        $codigoPostal = $orden->billing->postcode;
        $email = $orden->billing->email;
        $nombre = 'Orden_'.$codigo.'_'.time().'';
        $articulos = [];
        $cantidad = 0;
        $switch_hojas = false;
        $namefile = 'boleta_de_venta_'.time().'.pdf';
        $letra = '';
        $total = Common::precio_candec_sin_letra($orden->total, $moneda);
        $envio = Common::precio_candec_sin_letra($orden->shipping_total, $moneda).' '.$orden->shipping_lines[0]->method_title;
        $subtotal = $orden->total-$orden->shipping_total;
        $subtotal = Common::precio_candec_sin_letra($subtotal, $moneda);
        $factura = $codigo-29000;

        /*  --------------------------------------------------------------------------------- */

        // CARGAR VARIABLES CABECERA
        
        $data['metodo'] = $metodo;
        $data['cliente'] = ucwords(strtolower($cliente));
        $data['ruc'] = $ruc;
        $data['ciudad'] = ucwords(strtolower($ciudad));
        $data['direccion'] = $direccion;
        $data['direccion_2'] = $direccion_2;
        $data['telefono'] = $telefono;
        $data['codigo'] = $codigo;
        $data['dia'] = $dia;    
        $data['fecha'] = $fecha;
        $data['nombre'] = $nombre;
        $data['c'] = $c;
        $data['tipo'] = 'online';
        $data['envio'] = $envio;
        $data['total'] = $total;
        $data['subtotal'] = $subtotal;
        $data['factura'] = $factura;
        $data['email'] = $email;
        $data['codigoPostal'] = $codigoPostal;

        $total = 0;
        
        // INICIAR MPDF 

		$mpdf = new \Mpdf\Mpdf([
			'margin_left' => 20,
			'margin_right' => 20,
			'margin_top' => 40,
			'margin_bottom' => 10,
			'margin_header' => 10,
			'margin_footer' => 10
		]);

        $mpdf->SetDisplayMode('fullpage');

		foreach ($orden_det as $key => $value) {

			// BUSCAR PESO DEL PRODUCTO

            $peso = DB::connection('retail')
            ->table('PESO')
            ->select(DB::raw('PESO'))
            ->where('CODIGO', '=', $value->sku)
            ->get();

            $totalP = Common::quitar_coma($value->total, $candec);

            // PRECIO 

           $precio = $value->price;

            // SI NO ENCUENTRA COTIZACION RETORNAR 

            $articulos[$c_rows]["precio"] = Common::precio_candec_sin_letra($precio, $moneda);

            // CARGAR VARIABLES 

            $articulos[$c_rows]["cantidad"] = $value->quantity;
            $articulos[$c_rows]["cod_prod"] = $value->sku;
            $articulos[$c_rows]["descripcion"] = $value->name;
            $articulos[$c_rows]["total"] = Common::precio_candec_sin_letra($totalP, $moneda);

            if(empty($peso[0])){
                $articulos[$c_rows]["peso"] = 'No Disponible';
            }else{

                $articulos[$c_rows]["peso"] = round(($peso[0]->PESO/1000), 3).'kg';
            }

	        $total = $total+$totalP;
	        

            // CONTAR CANTIDAD DE FILAS DE HOJAS 

            $c_rows = $c_rows + 1;    

            // CONTAR LA CANTIDAD DE FILAS 

            $c = $c + 1;

            // SI CANTIDAD DE FILAS ES IGUAL A 10 ENTONCES CREAR PAGINA 

            if ($c_rows === 10){

                // AGREGAR ARTICULOS 

                $data['articulos'] = $articulos;

                // RESTAR LAS CANTIDADES CARGADAS 

                $c_rows_array = $c_rows_array - 10;

                // PONER TRUE SWITCH YA QUE CREO UNA PAGINA 

                $switch_hojas = true;

                // CARGAR SUB TOTALES POR HOJA
                $subtotal = $total;
		        $subtotal = Common::precio_candec_sin_letra($subtotal, $moneda);

		        $data['total'] = Common::precio_candec_sin_letra($total, $moneda);;
		        $data['subtotal'] = $subtotal;

                $html = view('pdf.facturaOrden', $data)->render();

                // SI NO ES LA PRIMERA HOJA AGREGAR PAGINA

                if ($c !== 10) {
                    $mpdf->AddPage();
                }

                // CERAR CONTADOR DE FILAS CARGADAS POR HOJAS Y ARTICULOS

                $c_rows = 0;
                $data['articulos'] = [];
                $articulos = [];
                    
                $mpdf->WriteHTML($html);

            } else if ($c_rows_array < 10 && $c_filas_total === $c) {
                
                // AGREGAR ARTICULOS 
                
                $data['articulos'] = $articulos;
                $subtotal = $total;
		        $subtotal = Common::precio_candec_sin_letra($subtotal, $moneda);
		        $total = $total+$orden->shipping_total;
		        $data['total'] = Common::precio_candec_sin_letra($total, $moneda);
		        $data['subtotal'] = $subtotal;

                // CREAR HOJA 

                $html = view('pdf.facturaOrden', $data)->render();

                if ($switch_hojas === true) {
                    $mpdf->AddPage();
                }
                    
                $mpdf->WriteHTML($html);
            }
        }

        $mpdf->SetProtection(array('print'));
		$mpdf->SetTitle("Calbea/Factura");
        
        // DESCARGAR ARCHIVO PDF 

        $mpdf->Output();

        /*  --------------------------------------------------------------------------------- */
    }

    public static function direccionPendientePDF($datos){

    	$user = auth()->user();

        // INICIAR VARIABLES 

        $dia = date("Y-m-d");
        $envio = $datos['data']['tipo'];
        $codigo = $datos['data']['codigo'];
        $hora = date("H:i:s");

        $woocommerce = new Client(
            'https://www.calbea.com.py', // Your store URL
            'ck_14760e0d817b4c57551d17de6404aac61ebff682', // Your consumer key
            'cs_91baae6016d43f30e19d95b8259c3098abccfa9e', // Your consumer secret
            [
                'wp_json' => true, // Enable the WP REST API integration
                'wp_api' => true, // Enable the WP REST API integration
                'version' => 'wc/v3',// WooCommerce WP REST API version
                'query_string_auth' => true,
                'verify_ssl' => false 
            ]
        );

        // OBTENER DATOS

        $orden = ($woocommerce->get('orders/'.$codigo));

    	$orden_id = $orden->id;

        $nombre = $orden->shipping->first_name.' '.$orden->shipping->last_name;
    	
    	$mpdf = new \Mpdf\Mpdf([
			'mode' => 'B',
			'margin_left' => 10,
			'margin_right' => 10,
			'margin_top' => 40,
			'margin_header' => 10,
			'margin_footer' => 10,
			'watermarkAngle' => 135
		]);

    	if($datos['data']['tamanho'] == 'Grande'){
			$text2= '
			<div><br /><br /><br /><br /><span style="font-size: 105pt; color: #e02020; font-family: Comic Sans;">
						<b>CUIDADO<br />FRÁGIL</b>
					</span></div>';
			$text= '<br />';

    	}else{
    		$text= '<span style="font-size: 47pt; color: #e02020; font-family: Comic Sans;">
						<b>CUIDADO FRÁGIL</b>
					</span>';
			$text2= '';
    	}

        if(!empty($orden->shipping->address_2)){

            $direccion = 'Dirección: '.$orden->shipping->address_1.'.<br /> 
                        Atención: '.$orden->shipping->address_2;
        }else{

            $direccion = 'Dirección: '.$orden->shipping->address_1.'<br /><br />';
        }

        if(!empty($orden->shipping->postcode)){

            $codigoPostal = 'Código Postal: '.$orden->shipping->postcode.', ';
        }else{

            $codigoPostal = ' ';
        }

		$header = '
		<table 
			width="100%" style="
			vertical-align:center; 
			font-family: Comic Sans; 
			font-size: 16pt; 
			color: #1c6692;">
			<tr>
				<td width="40%" align="center">
					<img src="C:\laragon\www\StockWebApp\resources\imagenes\LOGO.png" width="240px">
					<br />
				</td>
			</tr>
		</table>';

		$mensaje = '
			<span style="font-size:12pt; color: #000000; font-family: Comic Sans;">En este periodo de cuarentena, nada es más seguro que comprar desde la comodidad y seguridad de tu casa, y recibir tu pedido rápidamente.</span>
					<br /><br />
			<span style="font-size:12pt; color: #000000; font-family: Comic Sans;">Calbea cuenta con productos importados que van desde comidas, cosméticos, decoraciones, utensilios de cocina y MUCHO MÁS.</span>
					<br /><br />
			<span style="font-size:12pt; color: #000000; font-family: Comic Sans;">No olvides seguir a @CalbeaOfficial en las redes sociales para acompañar todas las novedades de primera mano.</span><br /><br />
		';

		$mpdf->SetHTMLHeader($header);

    	$html = '
			<html>
			<head>
			<style>
				p {	
					margin: 47pt; 
					font-size: 14pt;
				}
				td { 
					vertical-align: center; 
					text-align:justify;
				}
				body {
					text-align:center;
					font-family: Comic Sans;
					font-size: 14pt;
				}

				div{
					text-align="center";
				}
			</style>
			</head>
			<body>

			<table width="100%" style="font-family: Comic Sans; margin-top: 0px; padding-bottom: 0px;" cellpadding="10">
			<tr>
				<td width="30%" align="right">
					<span style="font-size: 18pt; color: #1c6692; font-family: Comic Sans;">
						<br/><b>PARA:</b>
					</span>
					<br /><br /><br /><br />
					<span style="font-size: 16pt; color: #1c6692; font-family: Comic Sans;">
						<b>ENVIO A:</b>
					</span>
				</td>
				<td width="50%" align="justify">
					<p><br />Nombre: '.ucwords(strtolower($nombre)).'<br />
					Tel: '.$orden->billing->phone.'<br /><br /></p>
					<p>'.$codigoPostal.''.$orden->shipping->state.', '.ucwords(strtolower($orden->shipping->city)).'.<br />'.$direccion.'.
				</td>
			</tr></table><br />
			<div class="text">
				<span style="font-size: 16pt; color: #1c6692; font-family: Comic Sans;">
						<b>TIPO DE ENVIO:</b>
				</span>
				<span style="font-size: 16pt; color: #1c6692; 
						font-family: Comic Sans;"><b>'.$envio.'</b>
				</span><br />
				<br />'.$text.'<br />
			</div>
			<br/><br />
			<table width="100%" style=" vertical-align:center; font-family: Comic Sans;">
			<tr>
				<td width="50%" align="center">
					<div><span style="font-size:17pt; color: #1c6692; font-family: Comic Sans;">
						<br/><b>¡Tu paquete ha llegado!
					</span></div><br />
					'.$mensaje.'
					<div><span style="font-size: 16pt; color: #1c6692; font-family: Comic Sans;">
						<b>¡Muchas gracias que tengas un gran día!</b>
					</span></div>
					<br/><img src="C:\laragon\www\StockWebApp\resources\imagenes\LOGO.png" width="170px">
				</td><td width="8%" align="center">
					
				</td>
				<td width="42" align="center"><br/>
					<br/><img src="C:\laragon\www\StockWebApp\resources\imagenes\LOGO.png" width="280px">
					<br/><img src="C:\laragon\www\StockWebApp\resources\imagenes\qr-code-calbea.png" width="240px">
				</td>
			</tr></table>
			'.$text2.'
			</body>
			</html>
		';

		$mpdf->SetProtection(array('print'));
		$mpdf->SetTitle("Calbea/Direccion");
		$mpdf->SetDisplayMode('fullpage');

		// $mpdf->SetWatermarkImage($watermark, 0.15, '', array(20,135));
		// $mpdf->showWatermarkImage = true;

		$mpdf->WriteHTML($html);

		$mpdf->Output();
    }
     public static function Completado_wp($datos)
    {

      try {
        $dia = date("Y-m-d");
        $hora = date("H:i:s");
      /*  --------------------------------------------------------------------------------- */
      $items=(array)json_decode($datos["items"]);
      $orden=(array)json_decode($datos["orden"]);


      $tb_ord=DB::connection('retail')->table('wp_orden')->select('id')->Where('ORDEN_ID','=',$orden["id"])->limit(1)->get()->toArray();
      if(count($tb_ord)<=0){
         $in_ord=DB::connection('retail')->table('wp_orden')->insertGetId(
           ['ORDEN_ID'=> $orden["id"],
           'PARENT_ID'=> $orden["parent_id"], 
           'ESTADO'=>$orden["status"],
           'MONEDA'=>$orden["currency"],
           'PRECIO_INCLUYE_TAX'=>$orden["prices_include_tax"],
           'CREACION_DIA'=>$orden["date_created"]->date,
           'DIA_MODIF'=>$orden["date_modified"]->date,
           'ZONA_HORARIA'=>$orden["date_created"]->timezone,
           'ZONA_HORARIA_MODIF'=>$orden["date_modified"]->timezone,
           'TOTAL'=>$orden["total"],
           'TOTAL_TAX'=>$orden["total_tax"],
           'SHIPPING_TOTAL'=>$orden["total"],
           'SHIPPING_TAX'=>$orden["total_tax"],
           'FECALTAS'=>$dia,
           'HORALTAS'=>$hora,]);
      }else{
        $delete_det=DB::connection('retail')->table('wp_orden_det')->Where('ORDEN_ID','=',$orden["id"])->delete();
        $up_ord=DB::connection('retail')->table('wp_orden')->where('ORDEN_ID',"=",$orden["id"])
            ->update([
           'PARENT_ID'=> $orden["parent_id"], 
           'ESTADO'=>$orden["status"],
           'MONEDA'=>$orden["currency"],
           'PRECIO_INCLUYE_TAX'=>$orden["prices_include_tax"],
           'DIA_MODIF'=>$orden["date_modified"]->date,
           'ZONA_HORARIA_MODIF'=>$orden["date_modified"]->timezone,
           'TOTAL'=>$orden["total"],
           'TOTAL_TAX'=>$orden["total_tax"],
           'FECMODIF'=>$dia,
           'HORMODIF'=>$hora]);
      }
      //Log::info(["array insert" => $in_ord]);
/*        $delete_orden_lote=DB::connection('retail')->table('orden_tiene_lotes')->Where('ORDEN_ID','=',$orden["id"])->delete();*/
      foreach ($items as $key => $value) {
     //Log::error('entre2',['datos'=>$orden["id"]]);
          $in_ord_det=DB::connection('retail')->table('wp_orden_det')->insertGetId(
           ['ORDEN_ID'=> $orden["id"],
           'PROD_ID'=> $value->id, 
           'SKU'=>$value->sku,
           'CANTIDAD'=>$value->quantity,
           'PRECIO'=>$value->precio,
           'SUBTOTAL'=>$value->subtotal,
           'SUBTOTAL_TAX'=>$value->taxas,
           'TIPO_TAX'=>$value->tipotaxa,
           'ESTADO_TAX'=>$value->taxstatus,
           'TOTAL'=>$value->total,         
           'FECALTAS'=>$dia,
           'HORALTAS'=>$hora]);

         // $codigo=DB::connection('retail')->table('productos_aux')->select('CODIGO')->where('CODIGO_INTERNO', '=', $value->sku)
          //->limit(1)->get()->toArray();
          //Log::error('entre2',['datos'=>]);
      
        //  $restarStock = Stock::restar_stock_producto_web($codigo["0"]->CODIGO , $value->quantity);

       // if($restarStock["datos"]){

           
         // foreach ($restarStock["datos"] as $key => $value2) {
            # code...
           

           // $orden_tiene_lotes=DB::connection('retail')->table('orden_tiene_lotes')->insertGetId(
           //['ORDEN_ID'=> $orden["id"],
           //'LOTE_ID'=> $value2["id"], 
           //'CANTIDAD'=>$value2["cantidad"]]);

          //}
        //}

      }

       $delete_ship=DB::connection('retail')->table('wp_shipping')->Where('ORDEN_ID','=',$orden["id"])->delete();

        $shipp=DB::connection('retail')->table('wp_shipping')->insertGetId(
             ['ORDEN_ID'=> $orden["id"],
             'NOMBRES'=>$orden["shipping"]->first_name, 
             'APELLIDOS'=>$orden["shipping"]->last_name,
             'COMPANY'=>$orden["shipping"]->company,
             'DIRECCION_1'=>$orden["shipping"]->address_1,
             'DIRECCION_2'=>$orden["shipping"]->address_2,
             'CIUDAD'=>$orden["shipping"]->city,
             'ESTADO'=>$orden["shipping"]->state,
             'CODIGO_POSTAL'=>$orden["shipping"]->postcode,
             'COUNTRY'=>$orden["shipping"]->country,
             'FECALTAS'=>$dia,
             'HORALTAS'=>$hora]);

        $delete_bill=DB::connection('retail')->table('wp_billing')->Where('ORDEN_ID','=',$orden["id"])->delete();
              $ruc="";
              $ci="";
              $raz="";
        foreach ($orden["meta_data"] as $key => $value) {
             # code...
            if($value->key=='billing_ruc'){
              $ruc=$value->value;
            
            }
            if($value->key=='billing_documento'){
              $ci=$value->value;
            
            }
            if($value->key=='billing_razon_social'){
              $raz=$value->value;
           
            }
           }
     $bill=DB::connection('retail')->table('wp_billing')->insertGetId(
           ['ORDEN_ID'=> $orden["id"],
           'NOMBRES'=>$orden["billing"]->first_name, 
           'APELLIDOS'=>$orden["billing"]->last_name,
           'COMPANY'=>$orden["billing"]->company,
           'DIRECCION_1'=>$orden["billing"]->address_1,
           'DIRECCION_2'=>$orden["billing"]->address_2,
           'CIUDAD'=>$orden["billing"]->city,
           'ESTADO'=>$orden["billing"]->state,
           'CODIGO_POSTAL'=>$orden["billing"]->postcode,
           'COUNTRY'=>$orden["billing"]->country,
           'EMAIL'=>$orden["billing"]->email,
           'DOCUMENTO'=>$ci,
           'RUC'=>$ruc,
           'RAZON_SOCIAL'=>$raz,
           'CELULAR'=>$orden["billing"]->phone,
           'FECALTAS'=>$dia,
           'HORALTAS'=>$hora]);
     
       $delete_pag=DB::connection('retail')->table('wp_pago')->Where('ORDEN_ID','=',$orden["id"])->delete();
          $pag=DB::connection('retail')->table('wp_pago')->insertGetId(
           ['ORDEN_ID'=> $orden["id"],
           'METODO_PAGO'=>$orden["payment_method"], 
           'METODO_PAGO_TITULO'=>$orden["payment_method_title"],
           'CLIENTE_IP'=>$orden["customer_ip_address"],
           'CREADO_VIA'=>$orden["created_via"],
           'NOTA_DEL_CLIENTE'=>$orden["customer_note"],
           'FECHA_PAGO'=>$orden["date_paid"]->date,
           'ZONA_HORARIA'=>$orden["date_paid"]->timezone,
           'FECALTAS'=>$dia,
           'HORALTAS'=>$hora]);
          

        
       


      //Log::error('entre2',['datos'=>$datos]);
       



        /*  --------------------------------------------------------------------------------- */
        return ["response"=>true];
      } catch (Exception $e) {
        Log::error(['TIPO'=>$e->getMessage()], ['ORDEN NUMERO:'=>$orden["id"]]);
        Log::error(['DATOS DE LA ORDEN:'=>$orden]);
      }

    }
}
