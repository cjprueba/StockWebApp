<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Ventas_det;
use App\NotaCreditoDet;
use App\VentaDetTieneLotes;
use App\Venta;
use App\NotaCreditoMedios;
use Mpdf\Mpdf;
use Fpdf\Fpdf;
use Luecano\NumeroALetras\NumeroALetras;
use App\Events\OrdenCompletado;
use App\Stock;
use Illuminate\Support\Facades\Log;

class NotaCredito extends Model
{
    protected $connection = 'retail';
	protected $table = 'nota_credito';
    public $timestamps = false;

    public static function generar_cuerpo($datos) {

    	/*  --------------------------------------------------------------------------------- */

    	// INICIAR VARIABLES

    	$user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */

    	$codigo = $datos["datos"]["codigo"];
    	$caja = $datos["datos"]["caja"];
    	$tipo = $datos["datos"]["tipo"];
    	$productos = $datos["datos"]["datos"];
    	$data = array();
    	$total = 0;
    	$moneda = 0;

    	/*  --------------------------------------------------------------------------------- */

    	$base5 = 0; 
        $base10 = 0;
        $exenta = 0;
        $gravadas = 0;
        $impuesto = 0;
        $cliente_codigo = 0;
        $cliente_nombre = '';
        $razon_social = '';
        $ruc = '';

        if ($tipo === '1' || $tipo === '3'){


	        /*  --------------------------------------------------------------------------------- */

	    	foreach ($productos as $key => $value) {
	    		
	    		/*  --------------------------------------------------------------------------------- */

	    		// OBTENER PRECIOS DE LA VENTA DETALLE

	    		$dato = Ventas_det::select(DB::raw('VENTASDET.ID, VENTASDET.BASE5, VENTASDET.BASE10, IFNULL(VENTASDET.EXENTA,0) as EXENTA, VENTASDET.GRAVADA, VENTASDET.IVA, VENTASDET.PRECIO_UNIT, VENTASDET.PRECIO, VENTASDET.DESCRIPCION, VENTAS.MONEDA, PRODUCTOS.IMPUESTO, MONEDAS.CANDEC, VENTAS.CLIENTE, CLIENTES.NOMBRE, CLIENTES.RAZON_SOCIAL, CLIENTES.RUC, IFNULL(VENTAS_DESCUENTO.PORCENTAJE, 0) AS DESCUENTO_GENERAL_PORCENTAJE, IFNULL(VENTAS_CUPON.CUPON_PORCENTAJE, 0) AS DESCUENTO_CUPON_PORCENTAJE'))
	    		->leftJoin('VENTAS', function($join){
	                        $join->on('VENTAS.CODIGO', '=', 'VENTASDET.CODIGO')
	                             ->on('VENTAS.ID_SUCURSAL', '=', 'VENTASDET.ID_SUCURSAL')
	                             ->on('VENTAS.CAJA', '=', 'VENTASDET.CAJA');
	                    })
	    		->leftJoin('CLIENTES', function($join){
	                                $join->on('CLIENTES.CODIGO', '=', 'VENTAS.CLIENTE')
	                                     ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTASDET.ID_SUCURSAL');
	        	})
	    		->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
	    		->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'VENTAS.MONEDA')
	    		->leftjoin('VENTAS_DESCUENTO', 'VENTAS_DESCUENTO.FK_VENTAS', '=', 'VENTAS.ID')
	            ->leftjoin('VENTAS_CUPON', 'VENTAS_CUPON.FK_VENTA', '=', 'VENTAS.ID')
		    	->where([
		    		'VENTASDET.CODIGO' => $codigo,
		    		'VENTASDET.CAJA' => $caja,
		    		'VENTASDET.ID_SUCURSAL' => $user->id_sucursal,
		    		'VENTASDET.COD_PROD' => $value["CODIGO"]
		    	])
		    	->get();

		    	/*  --------------------------------------------------------------------------------- */

		    	// DESCUENTO GENERAL 

	            $desc = Common::calculo_porcentaje_descuentos([
	                'PRECIO_PRODUCTO' => $dato[0]->PRECIO_UNIT,
	                'PORCENTAJE_DESCUENTO' => $dato[0]->DESCUENTO_GENERAL_PORCENTAJE,
	                'CANTIDAD' => $value["CANTIDAD"],
	            ]);

	            $dato[0]->PRECIO_UNIT = $desc['PRECIO_REAL'];
	            $dato[0]->TOTAL = $desc['TOTAL_REAL'];

	            /*  --------------------------------------------------------------------------------- */

	            // DESCUENTO CUPON
	                
	            $desc = Common::calculo_porcentaje_descuentos([
	                    'PRECIO_PRODUCTO' => $dato[0]->PRECIO_UNIT,
	                    'PORCENTAJE_DESCUENTO' => $dato[0]->DESCUENTO_CUPON_PORCENTAJE,
	                    'CANTIDAD' => $value["CANTIDAD"],
	            ]);
	                
	            $dato[0]->PRECIO_UNIT = $desc['PRECIO_REAL'];
	            $dato[0]->TOTAL = $desc['TOTAL_REAL'];

	            /*  --------------------------------------------------------------------------------- */

		    	$nestedData['ID'] = $dato[0]->ID;
		    	$nestedData['CODIGO'] = $value["CODIGO"];
	            $nestedData['CANTIDAD'] = $value["CANTIDAD"];
	            $nestedData['DESCRIPCION'] = $dato[0]->DESCRIPCION;
	            $nestedData['PRECIO_UNIT'] = $dato[0]->PRECIO_UNIT;
	            $nestedData['TOTAL'] = $dato[0]->PRECIO_UNIT * $value["CANTIDAD"];

	            $total = $total + ($dato[0]->PRECIO_UNIT * $value["CANTIDAD"]);
	            $moneda = $dato[0]->MONEDA;
	            $cliente_codigo = $dato[0]->CLIENTE;
	            $cliente_nombre = $dato[0]->NOMBRE;
	            $razon_social = $dato[0]->RAZON_SOCIAL;
	            $ruc =  $dato[0]->RUC;

	            $iva = Common::calculo_iva($dato[0]->IMPUESTO, ($dato[0]->PRECIO_UNIT * $value["CANTIDAD"]), $dato[0]->CANDEC);

	            /*  --------------------------------------------------------------------------------- */

	            // CARGAR IMPUESTOS 

	            $nestedData['BASE5'] = $iva['base5'];
	            $nestedData['BASE10'] = $iva['base10'];
	            $nestedData['EXENTAS'] = $iva['exentas'];
	            $nestedData['GRAVADAS'] = $iva['gravadas'];
	            $nestedData['IMPUESTO'] = $iva['impuesto'];

	            /*  --------------------------------------------------------------------------------- */

	            $base5 = $base5 + $iva['base5'];
	            $base10 = $base10 + $iva['base10'];
	            $exenta = $exenta + $iva['exentas'];
	            $gravadas = $gravadas + $iva['gravadas'];
	            $impuesto = $impuesto + $iva['impuesto'];

		    	//var_dump("CODIGO: ".$value["CODIGO"]." PRECIO_UNIT: ".$data[0]->PRECIO_UNIT." PRECIO: ".($data[0]->PRECIO_UNIT * $value['CANTIDAD']));

		    	$data[] = $nestedData;

	    	}

    	} 
    	// else if ($tipo === '2') {

    	// 	/*  --------------------------------------------------------------------------------- */

    	// 	$productos = Ventas_det::select(DB::raw('VENTASDET.COD_PROD, VENTASDET.ID, VENTASDET.BASE5, VENTASDET.BASE10, VENTASDET.EXENTA, VENTASDET.GRAVADA, VENTASDET.IVA, VENTASDET.CANTIDAD, VENTASDET.PRECIO_UNIT, VENTASDET.PRECIO, VENTASDET.DESCRIPCION, VENTAS.MONEDA, PRODUCTOS.IMPUESTO, MONEDAS.CANDEC, VENTAS.CLIENTE, CLIENTES.NOMBRE, CLIENTES.RAZON_SOCIAL, CLIENTES.RUC, IFNULL(VENTAS_DESCUENTO.PORCENTAJE, 0) AS DESCUENTO_GENERAL_PORCENTAJE, IFNULL(VENTAS_CUPON.CUPON_PORCENTAJE, 0) AS DESCUENTO_CUPON_PORCENTAJE'))
	    // 		->leftJoin('VENTAS', function($join){
	    //                     $join->on('VENTAS.CODIGO', '=', 'VENTASDET.CODIGO')
	    //                          ->on('VENTAS.ID_SUCURSAL', '=', 'VENTASDET.ID_SUCURSAL')
	    //                          ->on('VENTAS.CAJA', '=', 'VENTASDET.CAJA');
	    //                 })
	    // 		->leftJoin('CLIENTES', function($join){
	    //                             $join->on('CLIENTES.CODIGO', '=', 'VENTAS.CLIENTE')
	    //                                  ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTASDET.ID_SUCURSAL');
	    //     	})
	    // 		->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
	    // 		->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'VENTAS.MONEDA')
	    // 		->leftjoin('VENTAS_DESCUENTO', 'VENTAS_DESCUENTO.FK_VENTAS', '=', 'VENTAS.ID')
	    //         ->leftjoin('VENTAS_CUPON', 'VENTAS_CUPON.FK_VENTA', '=', 'VENTAS.ID')
		   //  	->where([
		   //  		'VENTASDET.CODIGO' => $codigo,
		   //  		'VENTASDET.CAJA' => $caja,
		   //  		'VENTASDET.ID_SUCURSAL' => $user->id_sucursal
		   //  	])
		   //  	->get();

    	// 	/*  --------------------------------------------------------------------------------- */

    	// 	foreach ($productos as $key => $value) {

    	// 		/*  --------------------------------------------------------------------------------- */

    	// 		// SI ENCUENTRA UN DATO EN NOTA DE CREDITO DETALLE DEVOLVER

    	// 		$ntd = NotaCreditoDet::select('FK_VENTASDET')
    	// 		->where('FK_VENTASDET', '=', $value->ID)
    	// 		->get();

    	// 		/*  --------------------------------------------------------------------------------- */

    	// 		if (count($ntd) > 0) {
    	// 			return ['response' => false, 'statusText' => 'No se puede procesar de esta manera porque ya tiene productos devueltos.'];
    	// 		}

    	// 		/*  --------------------------------------------------------------------------------- */

		   //  	// DESCUENTO GENERAL 

	    //         $desc = Common::calculo_porcentaje_descuentos([
	    //             'PRECIO_PRODUCTO' => $value->PRECIO_UNIT,
	    //             'PORCENTAJE_DESCUENTO' => $value->DESCUENTO_GENERAL_PORCENTAJE,
	    //             'CANTIDAD' => $value->CANTIDAD,
	    //         ]);

	    //         $value->PRECIO_UNIT = $desc['PRECIO_REAL'];
	    //         $value->TOTAL = $desc['TOTAL_REAL'];

	    //         /*  --------------------------------------------------------------------------------- */

	    //         // DESCUENTO CUPON
	                
	    //         $desc = Common::calculo_porcentaje_descuentos([
	    //                 'PRECIO_PRODUCTO' => $value->PRECIO_UNIT,
	    //                 'PORCENTAJE_DESCUENTO' => $value->DESCUENTO_CUPON_PORCENTAJE,
	    //                 'CANTIDAD' => $value->CANTIDAD,
	    //         ]);
	                
	    //         $value->PRECIO_UNIT = $desc['PRECIO_REAL'];
	    //         $value->TOTAL = $desc['TOTAL_REAL'];

	    //         /*  --------------------------------------------------------------------------------- */

		   //  	$nestedData['ID'] = $value->ID;
		   //  	$nestedData['CODIGO'] = $value->COD_PROD;
	    //         $nestedData['CANTIDAD'] = $value->CANTIDAD;
	    //         $nestedData['DESCRIPCION'] = $value->DESCRIPCION;
	    //         $nestedData['PRECIO_UNIT'] = $value->PRECIO_UNIT;
	    //         $nestedData['TOTAL'] = $value->PRECIO_UNIT * $value->CANTIDAD;

	    //         $total = $total + ($value->PRECIO_UNIT * $value->CANTIDAD);
	    //         $moneda = $value->MONEDA;
	    //         $cliente_codigo = $value->CLIENTE;
	    //         $cliente_nombre = $value->NOMBRE;
	    //         $razon_social = $value->RAZON_SOCIAL;
	    //         $ruc =  $value->RUC;

	    //         $iva = Common::calculo_iva($value->IMPUESTO, ($value->PRECIO_UNIT * $value->CANTIDAD), $value->CANDEC);

	    //         /*  --------------------------------------------------------------------------------- */

	    //         // CARGAR IMPUESTOS 

	    //         $nestedData['BASE5'] = $iva['base5'];
	    //         $nestedData['BASE10'] = $iva['base10'];
	    //         $nestedData['EXENTAS'] = $iva['exentas'];
	    //         $nestedData['GRAVADAS'] = $iva['gravadas'];
	    //         $nestedData['IMPUESTO'] = $iva['impuesto'];

	    //         /*  --------------------------------------------------------------------------------- */

	    //         $base5 = $base5 + $iva['base5'];
	    //         $base10 = $base10 + $iva['base10'];
	    //         $exenta = $exenta + $iva['exentas'];
	    //         $gravadas = $gravadas + $iva['gravadas'];
	    //         $impuesto = $impuesto + $iva['impuesto'];

		   //  	//var_dump("CODIGO: ".$value["CODIGO"]." PRECIO_UNIT: ".$data[0]->PRECIO_UNIT." PRECIO: ".($data[0]->PRECIO_UNIT * $value['CANTIDAD']));

		   //  	$data[] = $nestedData;

		   //  	/*  --------------------------------------------------------------------------------- */

    	// 	}
    	// }

    	/*  --------------------------------------------------------------------------------- */

    	// IMAGEN CLIENTE 

    	$path = '../storage/app/imagenes/cliente.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $dataDefaultImage = file_get_contents($path);

        $imagen_cliente = "<img src='data:image/jpg;base64,".base64_encode($dataDefaultImage)."' class='img-fluid'>";

        /*  --------------------------------------------------------------------------------- */

    	// RESPUESTA 

    	return [
    		"response" => true, 
    		"ruc" => $ruc,
    		"cliente_codigo" => $cliente_codigo,
    		"cliente_nombre" => $cliente_nombre,
    		"razon_social" => $razon_social,
    		"data" => $data, 
    		"base5" => round($base5,2),
    		"base10" => round($base10,2),
    		"exentas" => round($exenta,2),
    		"gravadas" => round($gravadas,2),
    		"impuesto" => round($impuesto,2),
    		"total" => Common::precio_candec($total, $moneda), 
    		"total_crudo" => $total,
    		"moneda" => $moneda,
    		"statusText" => "Agregado correctamente",
    		"imagen_cliente" => $imagen_cliente
    	];

    	/*  --------------------------------------------------------------------------------- */

    }

    public static function guardar($data){

    	/*  --------------------------------------------------------------------------------- */

    	try {

    		/*  --------------------------------------------------------------------------------- */

            DB::connection('retail')->beginTransaction();

            /*  --------------------------------------------------------------------------------- */

	    	// INICIAR VARIABLES

	    	$user = auth()->user();


	    	/*  --------------------------------------------------------------------------------- */
	    	
	    	$data = $data['datos'];
	    	$codigo = ["codigo"];
	    	$dia = Date('Y-m-d');
	    	$hora = Date('H:i:s');
	    	$productos = $data["productos"];
	    	$caja = $data["caja_codigo"];
	    	$base5 = 0;
	    	$procesado = 0;
	    	$moneda_pago = 0;

	    	/*  --------------------------------------------------------------------------------- */

	    	// OBTENER ID VENTA 

	    	$venta = Venta::select(DB::raw('VENTAS.ID, VENTAS.CLIENTE, VENTAS.MONEDA'))
		    	->where([
		    		'VENTAS.CODIGO' => $data['venta_codigo'],
		    		'VENTAS.CAJA' => $data['caja_codigo'],
		    		'VENTAS.ID_SUCURSAL' => $user->id_sucursal
		    	])
		    ->get();

	    	/*  --------------------------------------------------------------------------------- */

	    	//  SI TIPO ES IGUAL AL nota por descuento. HACER EL CALCULO DE LA BASE

	    	if ($data['tipo'] === '2') {

	    		if ($data['tipo_iva'] === '1') {
	    			$data['base5'] = 0;
	    			$data['base10'] = $data['impuesto'];
	    		} else if ($data['tipo_iva'] === '2') {
	    			$data['base10'] = 0;
	    			$data['base5'] = $data['impuesto'];
	    		}	

	    	}

	    	/*  --------------------------------------------------------------------------------- */

	    	// PROCESADO 

	    	if ($data['tipo_proceso'] === '1') {
	    		$procesado = 0;
	    	} else if (($data['tipo_proceso'] === '2')) {
	    		$procesado = 1;
	    	}

	    	/*  --------------------------------------------------------------------------------- */

	    	// GUARDAR 

	    	$id_nota_credito = NotaCredito::insertGetId([
	    		'FK_VENTA' => $venta[0]->ID,
	    		'CLIENTE' => $venta[0]->CLIENTE,
	    		'FECHA' => $dia,
	    		'HORA' => $hora,
	    		'SUB_TOTAL' => $data['gravadas'],
	    		'IVA' => $data['impuesto'],
	    		'TOTAL' => $data['total'],
	    		'BASE5' => $data['base5'],
	    		'BASE10' => $data['base10'],
	    		'DESCUENTO'=> $data['descuento'],
	    		'DESCUENTO_MONTO'=>$data['descuento_monto'],
	    		// 'ENVIADO' =>,
	    		// 'DEVUELTO' =>,
	    		'NUMERO_FACTURA' => $data['numero_factura'],
	    		'MONEDA' => $data['moneda'],
	    		// 'USERALTAS' =>,
	    		'FECALTAS' => $dia,
	    		'HORALTAS' => $hora,
	    		'FECMODIF' => $dia,
	    		'HORMODIF' => $hora,
	    		'ID_SUCURSAL' => $user->id_sucursal,
	    		'TIPO' => $data['tipo'],
	    		'TIPO_PROCESO' => $data['tipo_proceso'],
	    		'PROCESADO' => $procesado,
	    		'CAJA' => $data['caja_proceso'],
	    	]);

	    	/*  --------------------------------------------------------------------------------- */

	    	// TIPO DEVOLVER 
			
			if (($data['tipo_proceso'] === '2')) {

	    		/*  --------------------------------------------------------------------------------- */

	    		if ($data['tipo_medio'] == '1') {
	    			$total_medio = $data['totales']['guaranies'];
	    			$moneda_pago = 1;
	    		} else if ($data['tipo_medio'] == '2'){
	    			$total_medio = $data['totales']['dolares'];
	    			$moneda_pago = 2;
	    		} else if ($data['tipo_medio'] == '3'){
	    			$total_medio = $data['totales']['pesos'];
	    			$moneda_pago = 3;
	    		} else if ($data['tipo_medio'] == '4'){
	    			$total_medio = $data['totales']['reales'];
	    			$moneda_pago = 4;
	    		} else if ($data['tipo_medio'] == '5'){
	    			$total_medio = $data['totales']['cheque'];
	    			$moneda_pago = 1;
	    		} else if ($data['tipo_medio'] == '6'){
	    			$total_medio = $data['totales']['transferencia'];
	    			$moneda_pago = 1;
	    		}

	    		/*  --------------------------------------------------------------------------------- */

	    		NotaCreditoMedios::guardar_referencia([
	                'FK_NOTA_CREDITO' => $id_nota_credito,
	                'TOTAL' => Common::quitar_coma($total_medio, 2),
	                'FK_MONEDA' => $moneda_pago,
	                'TIPO_MEDIO' => $data['tipo_medio'],
            	]);

            	/*  --------------------------------------------------------------------------------- */

	    	}

	    	/*  --------------------------------------------------------------------------------- */

	    	if ($data['tipo'] === '1') {

		    	foreach ($productos as $key => $value) {

		    		/*  --------------------------------------------------------------------------------- */

		    		// INSERTAR TRANSFERENCIA DET 

		    		$id_nota_credito_det = NotaCreditoDet::insertGetId([
			         	'FK_VENTASDET' => $value['ID'],
			         	'FK_NOTA_CREDITO' => $id_nota_credito,   
			         	'CODIGO_PROD' => $value['CODIGO'], 
			         	'DESCRIPCION' => $value['DESCRIPCION'], 
			            'CANTIDAD' => $value['CANTIDAD'], 
			            'GRABADAS' => $value['GRAVADAS'],
			            'IVA' => $value['IMPUESTO'],
			            'EXENTAS' => $value['EXENTAS'],
			            'TOTAL' => $value['TOTAL'], 
			            'PRECIO' => $value['PRECIO_UNIT'], 
			            'BASE5' => $value['BASE5'],
			            'BASE10' =>  $value['BASE10'],
			            //'TIVA' => '', 
			            'USERALTAS' => $user->name, 
			            'FECALTAS' => $dia, 
			            'HORALTAS' => $hora, 
			            'ID_SUCURSAL' => $user->id_sucursal, 
			            ]
			        );

			        /*  --------------------------------------------------------------------------------- */

			        // DEVOLVER STOCK 

			        Stock::sumar_stock_producto_nota_credito($value['CODIGO'], $value['CANTIDAD'], $value['ID'], $id_nota_credito_det);

			        /*  --------------------------------------------------------------------------------- */

		    	}

		    }
		    if ($data['tipo'] === '3') {

		    	foreach ($productos as $key => $value) {

		    		/*  --------------------------------------------------------------------------------- */

		    		// INSERTAR TRANSFERENCIA DET 

		    		$id_nota_credito_det = NotaCreditoDet::insertGetId([
			         	'FK_VENTASDET' => $value['ID'],
			         	'FK_NOTA_CREDITO' => $id_nota_credito,   
			         	'CODIGO_PROD' => $value['CODIGO'], 
			         	'DESCRIPCION' => $value['DESCRIPCION'], 
			            'CANTIDAD' => $value['CANTIDAD'], 
			            'GRABADAS' => $value['GRAVADAS'],
			            'IVA' => $value['IMPUESTO'],
			            'EXENTAS' => $value['EXENTAS'],
			            'TOTAL' => $value['TOTAL'], 
			            'PRECIO' => $value['PRECIO_UNIT'], 
			            'BASE5' => $value['BASE5'],
			            'BASE10' =>  $value['BASE10'],
			            //'TIVA' => '', 
			            'USERALTAS' => $user->name, 
			            'FECALTAS' => $dia, 
			            'HORALTAS' => $hora, 
			            'ID_SUCURSAL' => $user->id_sucursal, 
			            ]
			        );

			        /*  --------------------------------------------------------------------------------- */

			        // GUARDAR REFERENCIA PARA NO PODER DEVOLVER

			        NotaCredito::guardar_referencia_producto_descuento_lote($value['CODIGO'], $value['CANTIDAD'], $value['ID'], $id_nota_credito_det);

			        /*  --------------------------------------------------------------------------------- */

		    	}

		    }
	    	// } else if ($data['tipo'] === '2') {

	    	// 	$dato = Ventas_det::select(DB::raw('VENTASDET.ID, VENTASDET.COD_PROD, VENTASDET.CANTIDAD, VENTASDET.BASE5, VENTASDET.BASE10, VENTASDET.EXENTA, VENTASDET.GRAVADA, VENTASDET.IVA, VENTASDET.PRECIO_UNIT, VENTASDET.PRECIO, VENTASDET.DESCRIPCION, PRODUCTOS.IMPUESTO, IFNULL(VENTAS_DESCUENTO.PORCENTAJE, 0) AS DESCUENTO_GENERAL_PORCENTAJE, IFNULL(VENTAS_CUPON.CUPON_PORCENTAJE, 0) AS DESCUENTO_CUPON_PORCENTAJE'))
	    	// 	->leftJoin('VENTAS', function($join){
      //                   $join->on('VENTAS.CODIGO', '=', 'VENTASDET.CODIGO')
      //                        ->on('VENTAS.ID_SUCURSAL', '=', 'VENTASDET.ID_SUCURSAL')
      //                        ->on('VENTAS.CAJA', '=', 'VENTASDET.CAJA');
      //               })
	    	// 	->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
	    	// 	->leftjoin('VENTAS_DESCUENTO', 'VENTAS_DESCUENTO.FK_VENTAS', '=', 'VENTAS.ID')
	     //        ->leftjoin('VENTAS_CUPON', 'VENTAS_CUPON.FK_VENTA', '=', 'VENTAS.ID')
		    // 	->where([
		    // 		'VENTASDET.CODIGO' => $data['venta_codigo'],
		    // 		'VENTASDET.CAJA' => $data['caja_codigo'],
		    // 		'VENTASDET.ID_SUCURSAL' => $user->id_sucursal
		    // 	])
		    // 	->get();

		    // 	/*  --------------------------------------------------------------------------------- */

		    // 	foreach ($dato as $key => $value) {

		    // 		/*  --------------------------------------------------------------------------------- */

		    // 		// DESCUENTO GENERAL 

		    //         $desc = Common::calculo_porcentaje_descuentos([
		    //             'PRECIO_PRODUCTO' => $value['PRECIO_UNIT'],
		    //             'PORCENTAJE_DESCUENTO' => $dato[0]->DESCUENTO_GENERAL_PORCENTAJE,
		    //             'CANTIDAD' => $value["CANTIDAD"],
		    //         ]);

		    //         $value['PRECIO_UNIT'] = $desc['PRECIO_REAL'];
		    //         $value['PRECIO'] = $desc['TOTAL_REAL'];

		    //           --------------------------------------------------------------------------------- 

		    //         // DESCUENTO CUPON
		                
		    //         $desc = Common::calculo_porcentaje_descuentos([
		    //                 'PRECIO_PRODUCTO' => $value['PRECIO_UNIT'],
		    //                 'PORCENTAJE_DESCUENTO' => $dato[0]->DESCUENTO_CUPON_PORCENTAJE,
		    //                 'CANTIDAD' => $value["CANTIDAD"],
		    //         ]);
		                
		    //         $value['PRECIO_UNIT'] = $desc['PRECIO_REAL'];
		    //         $value['PRECIO'] = $desc['TOTAL_REAL'];

		    //         /*  --------------------------------------------------------------------------------- */

		    // 		$id_nota_credito_det = NotaCreditoDet::insertGetId([
			   //       	'FK_VENTASDET' => $value['ID'],
			   //       	'FK_NOTA_CREDITO' => $id_nota_credito,   
			   //       	'CODIGO_PROD' => $value['COD_PROD'], 
			   //       	'DESCRIPCION' => $value['DESCRIPCION'], 
			   //          'CANTIDAD' => $value['CANTIDAD'], 
			   //          'GRABADAS' => $value['GRAVADA'],
			   //          'IVA' => $value['IVA'],
			   //          'EXENTAS' => $value['EXENTA'],
			   //          'TOTAL' => $value['PRECIO'], 
			   //          'PRECIO' => $value['PRECIO_UNIT'], 
			   //          'BASE5' => $value['BASE5'],
			   //          'BASE10' =>  $value['BASE10'],
			   //          //'TIVA' => '', 
			   //          'USERALTAS' => $user->name, 
			   //          'FECALTAS' => $dia, 
			   //          'HORALTAS' => $hora, 
			   //          'ID_SUCURSAL' => $user->id_sucursal, 
			   //          ]
			   //      );

		    // 		/*  --------------------------------------------------------------------------------- */

			   //      // DEVOLVER STOCK 

			   //      Stock::sumar_stock_producto_nota_credito($value['CODIGO'], $value['CANTIDAD'], $value['ID'], $id_nota_credito_det);

			   //      /*  --------------------------------------------------------------------------------- */

		    // 	}

	    	// }

	        /*  --------------------------------------------------------------------------------- */ 

	        // GUARDAR CAMBIOS 

	        DB::connection('retail')->commit();

	        /*  --------------------------------------------------------------------------------- */ 

	        // RETORNAR RESPUESTA 

	        return ["response" =>  true, "statusText" => "Se ha registrado con éxito la nota de crédito !", "ID" => $id_nota_credito];

	        /*  --------------------------------------------------------------------------------- */            	
		
		} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

	        DB::connection('retail')->rollBack();
	        throw $e;

	        /*  --------------------------------------------------------------------------------- */

	    }

    }

    public static function factura_pdf($dato)
    {
        // event(new OrdenCompletado([
        // 	'ID' => '5678',
        // 	'ID_WP' => '25547',
        // 	'NOMBRES' => 'CRISTIAN JUNNIOR',
        // 	'APELLIDOS' => 'CASTRO VAZQUEZ',
        // 	'CELULAR' => '595973855499',
        // 	'TOTAL' => '120.000'
        // ]));
        // return;

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
        $formatter = new NumeroALetras;

        /*  --------------------------------------------------------------------------------- */

        // OBTENER DATOS DE CABECERA 

        $nota_credito = NotaCredito::mostrar_cabecera($dato)["data"];

        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER IDS DETALLE 

        $nota_credito_det = NotaCredito::mostrar_cuerpo($dato)["data"];
        
        /*  --------------------------------------------------------------------------------- */

        // CANTIDAD DE DECIMALES Y MONEDA

        $candec = 0;
        $moneda = 1;
        $monedaNotaCredito = $nota_credito->MONEDA;

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI ES TABLA UNICA 

        $tab_unica = Parametro::tab_unica();

        if ($tab_unica === "SI") {
            $tab_unica = true;
        } else {
            $tab_unica = false;
        }

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $c = 0;
        $c_rows = 0;
        $contado_x = '';
        $credito_x = '';

        if($nota_credito->DESCUENTO>0){
         $descuento_row=round(count($nota_credito_det)/8);

         $c_rows_array = count($nota_credito_det);
         $c_filas_total = count($nota_credito_det);

        }else{
         $c_rows_array = count($nota_credito_det);
         $c_filas_total = count($nota_credito_det);
        }
      
        
        $codigo = $nota_credito->ID;
        $cliente = $nota_credito->CLIENTE;
        $direccion = $nota_credito->DIRECCION;
        $ruc = $nota_credito->RUC;
        $tipo = $nota_credito->TIPO;
        $fecha = $nota_credito->FECALTAS;
        $telefono = $nota_credito->TELEFONO;
        $numero_factura=$nota_credito->NUMERO_FACTURA;
        $fk_venta=$nota_credito->FK_VENTA;
        $nombre = 'Nota_Credito'.$codigo.'_'.time().'';
        $precio_unit=0;
        $articulos = [];
        $cantidad = 0;
        $exentas = 0;
        $base5 = 0;
        $base10 = 0;
        $iva = 0;
        $total = 0;
        $switch_hojas = false;
        $namefile = 'boleta_de_credito_'.time().'.pdf';
        $letra = '';

        /*  --------------------------------------------------------------------------------- */

        // CARGAR VARIABLES 
        
        $data['codigo'] = $codigo;
        $data['cliente'] = strtoupper($cliente);
        $data['direccion'] = strtoupper($direccion);
        $data['ruc'] = $ruc;
        $data['fecha'] = substr($fecha, 0,11);
        $data['telefono'] = $telefono;
        $data['numero_factura'] = $numero_factura;
        $data['nombre'] = $nombre;
        $data['c'] = $c;
        $data['tipo'] = 'fisico';

        /*  --------------------------------------------------------------------------------- */
        
        // INICIAR MPDF 

        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
         
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf([
            'fontDir' => array_merge($fontDirs, [
                public_path() . '/fonts',
            ]),
            'fontdata' => $fontData + [
                'arial' => [
                    'R' => 'arial.ttf',
                    'B' => 'arialbd.ttf',
                ],
            ],
            'default_font' => 'arial',
            "format" => [210,302],
            'margin_left' => 7.5,
			'margin_right' => 7.5,
			'margin_bottom' => 2,
			'margin_header' => 0,
			'margin_footer' => 0,
        ]);

        $mpdf->SetDisplayMode('fullpage');

        /*  --------------------------------------------------------------------------------- */

        // CARGAR DETALLE DE TRANSFERENCIA DET 
        
        foreach ($nota_credito_det as $key => $value) {
            
            /*  --------------------------------------------------------------------------------- */

            // SI LA MONEDA DEL PRODUCTO ES DIFERENTE A GUARANIES COTIZAR 

            
            if ($value["MONEDA"] <> 1) {

                /*  --------------------------------------------------------------------------------- */
                if($nota_credito->DESCUENTO>0){
                 

                /*$precio_unit=$precio_unit+ $articulos[$c_rows]["precio"];*/
                /*  --------------------------------------------------------------------------------- */

                // TOTAL 

                $cotizacion = Cotizacion::CALMONED(['monedaProducto' => $monedaNotaCredito, 'monedaSistema' => 1, 'precio' => Common::quitar_coma($value["PRECIO"], 2), 'decSistema' => 0, 'tab_unica' => $tab_unica, "id_sucursal" => $user["id_sucursal"],"FK_VENTA"=>$fk_venta]);
                

                
                // SI NO ENCUENTRA COTIZACION RETORNAR

                if ($cotizacion["response"] === false) {
                    header('HTTP/1.1 500 Internal Server Error');
                    exit;
                }
                $articulos[$c_rows]["precio"] = $cotizacion["valor"];
                $articulos[$c_rows]['total']= (Common::formato_precio((Common::calculo_porcentaje_descuentos(['PORCENTAJE_DESCUENTO'=>$nota_credito->DESCUENTO, 'PRECIO_PRODUCTO'=>Common::quitar_coma($articulos[$c_rows]["precio"], $candec), 'CANTIDAD'=>$cantidad])['DESCUENTO']),$candec));

               
                $total = $total + Common::quitar_coma($articulos[$c_rows]["total"], $candec);
            }else{
            	// PRECIO 
                
                $cotizacion = Cotizacion::CALMONED(['monedaProducto' => $monedaNotaCredito, 'monedaSistema' => 1, 'precio' => Common::quitar_coma($value["PRECIO_UNIT"], 2), 'decSistema' => 0, 'tab_unica' => $tab_unica, "id_sucursal" => $user["id_sucursal"],"FK_VENTA"=>$fk_venta]);

                // SI NO ENCUENTRA COTIZACION RETORNAR 

                if ($cotizacion["response"] === false) {
                    header('HTTP/1.1 500 Internal Server Error');
                    exit;
                }

                $articulos[$c_rows]["precio"] = $cotizacion["valor"];
                 

                /*$precio_unit=$precio_unit+ $articulos[$c_rows]["precio"];*/
                /*  --------------------------------------------------------------------------------- */

                // TOTAL 

                $cotizacion = Cotizacion::CALMONED(['monedaProducto' => $monedaNotaCredito, 'monedaSistema' => 1, 'precio' => Common::quitar_coma($value["PRECIO"], 2), 'decSistema' => 0, 'tab_unica' => $tab_unica, "id_sucursal" => $user["id_sucursal"],"FK_VENTA"=>$fk_venta]);
                $articulos[$c_rows]["total"] = $cotizacion["valor"];

                // SI NO ENCUENTRA COTIZACION RETORNAR

                if ($cotizacion["response"] === false) {
                    header('HTTP/1.1 500 Internal Server Error');
                    exit;
                }

               
                $total = $total + Common::quitar_coma($articulos[$c_rows]["total"], $candec);
            }
                

                /*  --------------------------------------------------------------------------------- */

            } else {
            	if($nota_credito->DESCUENTO>0){
            	 $articulos[$c_rows]["precio"] = $value["PRECIO"];
                 $articulos[$c_rows]["total"] = (Common::formato_precio((Common::calculo_porcentaje_descuentos(['PORCENTAJE_DESCUENTO'=>$nota_credito->DESCUENTO, 'PRECIO_PRODUCTO'=>Common::quitar_coma($articulos[$c_rows]["precio"], $candec), 'CANTIDAD'=>$cantidad])['DESCUENTO']),$candec));
               
                 $exentas = $exentas + Common::quitar_coma($value["EXENTAS"], $candec);
                 $total = $total + Common::quitar_coma($value["PRECIO"], $candec);
            	}else{
            	 $articulos[$c_rows]["precio"] = $value["PRECIO_UNIT"];
                 $articulos[$c_rows]["total"] = $value["PRECIO"];
               
                 $exentas = $exentas + Common::quitar_coma($value["EXENTAS"], $candec);
                 $total = $total + Common::quitar_coma($articulos[$c_rows]["total"], $candec);
            	}
                
                
            }


            
            /*  --------------------------------------------------------------------------------- */

            // CARGAR VARIABLES 

            $articulos[$c_rows]["cantidad"] = $value["CANTIDAD"];
            $articulos[$c_rows]["cod_prod"] = $value["COD_PROD"];
            if($nota_credito->DESCUENTO>0){
            	 $articulos[$c_rows]["descripcion"] = substr('DESCUENTO '.$nota_credito->DESCUENTO.'% '.$value["DESCRIPCION"], 0,35);

            }else{
            	 $articulos[$c_rows]["descripcion"] = substr('DEVOLUCION '.$value["DESCRIPCION"], 0,35);
            }
          
            $cantidad = $cantidad + $value["CANTIDAD"];

            /*  --------------------------------------------------------------------------------- */

            // REVISAR SI EXISTE EXENTAS 
             
            if ($value["EXENTAS"] > 0) {

                $articulos[$c_rows]["exentas"] = $articulos[$c_rows]["total"];
                 $exentas = $exentas + Common::quitar_coma($articulos[$c_rows]["total"], $candec);
            } else {
                $articulos[$c_rows]["exentas"] = '';

            }
            
            /*  --------------------------------------------------------------------------------- */

            // REVISAR SI EXISTE BASE5 O BASE10
            
            if ($value["BASE5"] !== 0 && $value["BASE5"] !== 0.00) {
                $articulos[$c_rows]["base10"] = '';
                $articulos[$c_rows]["base5"] = $articulos[$c_rows]["total"];
                $base5 = $base5 + Common::quitar_coma($articulos[$c_rows]["total"], $candec);
            } else if ($value["BASE10"] !== 0 && $value["BASE10"] !== 0.00) {
                $articulos[$c_rows]["base5"] = '';
                $articulos[$c_rows]["base10"] = $articulos[$c_rows]["total"];
                $base10 = $base10 + Common::quitar_coma($articulos[$c_rows]["total"], $candec);
            } else {
                $articulos[$c_rows]["base5"] = '';
                $articulos[$c_rows]["base10"] = '';
            }

            /*  --------------------------------------------------------------------------------- */
            
            // CONTAR CANTIDAD DE FILAS DE HOJAS 
          /*  var_dump($articulos[$c_rows]);*/

            $c_rows = $c_rows + 1;    
            
            /*  --------------------------------------------------------------------------------- */

            // CONTAR LA CANTIDAD DE FILAS 

            $c = $c + 1;

            /*  --------------------------------------------------------------------------------- */
            //SI TIENE DESCUENTO ENTONCES UNA FILA ES PARA MOSTRAR EL DESCUENTO
           
         
				/*  --------------------------------------------------------------------------------- */
            // SI CANTIDAD DE FILAS ES IGUAL A 10 ENTONCES CREAR PAGINA 

            if ($c_rows === 8){

                /*  --------------------------------------------------------------------------------- */

                // AGREGAR ARTICULOS 

                $data['articulos'] = $articulos;

                /*  --------------------------------------------------------------------------------- */

                // RESTAR LAS CANTIDADES CARGADAS 

                $c_rows_array = $c_rows_array - 10;

                /*  --------------------------------------------------------------------------------- */

                // PONER TRUE SWITCH YA QUE CREO UNA PAGINA 

                $switch_hojas = true;

                /*  --------------------------------------------------------------------------------- */

                // CARGAR SUB TOTALES POR HOJA

                $data['cantidad'] = $cantidad;
                //$data['letra'] = 'Son Guaranies: '.substr(NumeroALetras::convertir($total, 'guaranies'), 0, strpos(NumeroALetras::convertir($total, 'guaranies'), "CON"));
                $data['letra'] = 'Son Guaranies: '.($formatter->toMoney($total, 0, 'guaranies'));
                $data['total'] = Common::precio_candec_sin_letra($total, $moneda);
                $data['exentas'] = Common::precio_candec_sin_letra($exentas, $moneda);
                $data['base5'] = Common::precio_candec_sin_letra($base5 / 21, $moneda);
                $data['base10'] = Common::precio_candec_sin_letra($base10 / 11, $moneda);
                $data['iva'] = Common::precio_candec_sin_letra(($base5 / 21) + ($base10 / 11), $moneda);

                /*  --------------------------------------------------------------------------------- */

                $html = view('pdf.notaCredito', $data)->render();
                
                /*  --------------------------------------------------------------------------------- */

                // SI NO ES LA PRIMERA HOJA AGREGAR PAGINA

                if ($c !== 8) {
                    $mpdf->AddPage();
                }

                /*  --------------------------------------------------------------------------------- */

                // CERAR CONTADOR DE FILAS CARGADAS POR HOJAS Y ARTICULOS

                $c_rows = 0;
                $exentas = 0;
                $base5 = 0;
                $base10 = 0;
                $total = 0;
                $precio_unit=0;
                $data['articulos'] = [];
                $articulos = [];

                /*  --------------------------------------------------------------------------------- */
                    
                $mpdf->WriteHTML($html);

                /*  --------------------------------------------------------------------------------- */

            } else if ($c_rows_array < 8 && $c_filas_total === $c) {
              
                /*  --------------------------------------------------------------------------------- */
               
                // AGREGAR ARTICULOS 
                
                
                $data['articulos'] = $articulos;

                /*  --------------------------------------------------------------------------------- */

                // CARGAR SUB TOTALES POR HOJA

                $data['cantidad'] = $cantidad;
                //$data['letra'] = 'Son Guaranies: '.substr(NumeroALetras::convertir($total, 'guaranies'), 0, strpos(NumeroALetras::convertir($total, 'guaranies'), "CON"));
                $data['letra'] = 'Son Guaranies: '.($formatter->toMoney($total, 0, 'guaranies'));
                $data['total'] = Common::precio_candec_sin_letra($total, $moneda);
                $data['exentas'] = Common::precio_candec_sin_letra($exentas, $moneda);
                $data['base5'] = Common::precio_candec_sin_letra($base5 / 21, $moneda);
                $data['base10'] = Common::precio_candec_sin_letra($base10 / 11, $moneda);
                $data['iva'] = Common::precio_candec_sin_letra(($base5 / 21) + ($base10 / 11), $moneda);

                /*  --------------------------------------------------------------------------------- */

                // CREAR HOJA 

                $html = view('pdf.notaCredito', $data)->render();

                /*  --------------------------------------------------------------------------------- */

                // SI NO ES LA PRIMERA HOJA AGREGAR PAGINA

                if ($switch_hojas === true) {
                    $mpdf->AddPage();
                }

                /*  --------------------------------------------------------------------------------- */
                    
                $mpdf->WriteHTML($html);

                /*  --------------------------------------------------------------------------------- */

            }
        }
        
        /*  --------------------------------------------------------------------------------- */
        
        // DESCARGAR ARCHIVO PDF 

        $mpdf->Output($namefile,"D");

        /*  --------------------------------------------------------------------------------- */
        
    }

    public static function mostrar_cuerpo($data)
    {
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $id = $data['id'];

        /*  --------------------------------------------------------------------------------- */

        $nota_credito_det = NotaCreditoDet::select(DB::raw(
                        'NOTA_CREDITO_DET.CODIGO_PROD AS COD_PROD, 
                        NOTA_CREDITO_DET.DESCRIPCION,
                        NOTA_CREDITO_DET.CANTIDAD, 
                        NOTA_CREDITO_DET.PRECIO AS PRECIO_UNIT,
                        NOTA_CREDITO_DET.TOTAL AS PRECIO,
                        NOTA_CREDITO.MONEDA,
                        NOTA_CREDITO_DET.BASE5,
                        NOTA_CREDITO_DET.BASE10,
                        NOTA_CREDITO_DET.EXENTAS'
                    ))
        ->leftjoin('NOTA_CREDITO', 'NOTA_CREDITO.ID', '=', 'NOTA_CREDITO_DET.FK_NOTA_CREDITO')
        ->where('NOTA_CREDITO.ID','=', $id)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        foreach ($nota_credito_det as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            $nota_credito_det[$key]->CANTIDAD = Common::precio_candec_sin_letra($value->CANTIDAD, 1);
            $nota_credito_det[$key]->PRECIO_UNIT = Common::precio_candec_sin_letra($value->PRECIO_UNIT, $value->MONEDA);
            $nota_credito_det[$key]->PRECIO = Common::precio_candec_sin_letra($value->PRECIO, $value->MONEDA);

            /*  --------------------------------------------------------------------------------- */

        }

        return ["response" => true, "data" => $nota_credito_det];

        /*  --------------------------------------------------------------------------------- */

    }

    public static function mostrar_cabecera($data)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
         
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $id = $data['id'];

        /*  --------------------------------------------------------------------------------- */

        $nota_credito = NotaCredito::select(DB::raw(
                        'NOTA_CREDITO.ID,
                        NOTA_CREDITO.FECALTAS, 
                        NOTA_CREDITO.MONEDA,
                        NOTA_CREDITO.TIPO,
                        IFNULL(NOTA_CREDITO.DESCUENTO,0) AS DESCUENTO,
                        CLIENTES.RAZON_SOCIAL,
                        CLIENTES.DIRECCION,
                        CLIENTES.TELEFONO,
                        CLIENTES.CELULAR,
                        CLIENTES.RUC,
                        CLIENTES.CI,
                        CLIENTES.NOMBRE AS CLIENTE,
                        IFNULL(NUMERO_FACTURA,"") AS NUMERO_FACTURA,
                        FK_VENTA'
                    ))
        ->leftJoin('CLIENTES', function($join){
                            $join->on('NOTA_CREDITO.CLIENTE', '=', 'CLIENTES.CODIGO')
                                 ->on('CLIENTES.ID_SUCURSAL', '=', 'NOTA_CREDITO.ID_SUCURSAL');
                         })
        ->where('NOTA_CREDITO.ID','=', $id)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RUC / CI

        if ($nota_credito[0]->RUC === '' || $nota_credito[0]->RUC === null) {
            $nota_credito[0]->RUC = $nota_credito[0]->CI;
        }

        /*  --------------------------------------------------------------------------------- */

        // TELEFONO / CELULAR

        if ($nota_credito[0]->TELEFONO === '' || $nota_credito[0]->TELEFONO === null) {
            $nota_credito[0]->TELEFONO = $nota_credito[0]->CELULAR;
        }

        /*  --------------------------------------------------------------------------------- */

        // RAZON SOCIAL

        if ($nota_credito[0]->RAZON_SOCIAL !== '' && $nota_credito[0]->RAZON_SOCIAL !== null) {
            $nota_credito[0]->CLIENTE = $nota_credito[0]->RAZON_SOCIAL;
        }

        /*  --------------------------------------------------------------------------------- */

        // RESPONSE 

        return  ["response" => true, "data" => $nota_credito[0]];

        /*  --------------------------------------------------------------------------------- */

    }

    public static function obtener_credito_cliente_datatable($request) {

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $cliente = $request->input('cliente');

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'ID', 
                            1 => 'VENTA',
                            2 => 'FECHA',
                            3 => 'HORA',
                            4 => 'TOTAL',
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE PRODUCTOS ENCONTRADOS 

        $totalData = NotaCredito::where([
                         	'NOTA_CREDITO.CLIENTE' => $cliente,
                         	'PROCESADO' => 0
                         ])
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

            $posts = NotaCredito::select(DB::raw('NOTA_CREDITO.ID, NOTA_CREDITO.FK_VENTA, NOTA_CREDITO.FECALTAS, NOTA_CREDITO.HORA, NOTA_CREDITO.TOTAL, NOTA_CREDITO.MONEDA, NOTA_CREDITO.BASE5, NOTA_CREDITO.BASE10, NOTA_CREDITO.IVA'))
                         ->where([
                         	'NOTA_CREDITO.CLIENTE' => $cliente,
                         	'PROCESADO' => 0
                         ])
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

            $posts =  NotaCredito::select(DB::raw('NOTA_CREDITO.ID, NOTA_CREDITO.FK_VENTA, NOTA_CREDITO.FECALTAS, NOTA_CREDITO.HORA, NOTA_CREDITO.TOTAL, NOTA_CREDITO.MONEDA, NOTA_CREDITO.BASE5, NOTA_CREDITO.BASE10, NOTA_CREDITO.IVA'))
            ->where([
                         	'NOTA_CREDITO.CLIENTE' => $cliente,
                         	'PROCESADO' => 0
                         ])
            ->where(function ($query) use ($search) {
            $query->where('NOTA_CREDITO.FK_VENTA','LIKE',"%{$search}%")
                  ->orWhere('NOTA_CREDITO.TOTAL', 'LIKE',"%{$search}%");
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = NotaCredito::
            where([
                         	'NOTA_CREDITO.CLIENTE' => $cliente,
                         	'PROCESADO' => 0
                         ])
            ->where(function ($query) use ($search) {
                $query->where('NOTA_CREDITO.FK_VENTA','LIKE',"%{$search}%")
                ->orWhere('NOTA_CREDITO.TOTAL', 'LIKE',"%{$search}%");
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

                $nestedData['ID'] = $post->ID;
                $nestedData['FK_VENTA'] = $post->FK_VENTA;
                $nestedData['FECALTAS'] = $post->FECALTAS;
                $nestedData['HORA'] = $post->HORA;
                $nestedData['BASE5'] = Common::precio_candec_sin_letra($post->BASE5, $post->MONEDA);
                $nestedData['BASE10'] = Common::precio_candec_sin_letra($post->BASE10, $post->MONEDA);
                $nestedData['IVA'] = Common::precio_candec_sin_letra($post->IVA, $post->MONEDA);
                $nestedData['TOTAL'] = Common::precio_candec_sin_letra($post->TOTAL, $post->MONEDA);
                $nestedData['TOTAL_CRUDO'] = $post->TOTAL;

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

    public static function notaCreditoMostrar($request){

    	/*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'ID', 
                            1 => 'ID_VENTA',
                            2 => 'CLIENTE',
                            3 => 'RUC',
                            4 => 'NRO_FACTURA',
                            5 => 'SUB_TOTAL',
                            6 => 'IVA',
                            7 => 'TOTAL',
                            8 => 'FECHA',
                            9 => 'TIPO',
                            10 => 'CAJA'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE NOTAS ENCONTRADAS 

        $totalData = NotaCredito::where('ID_SUCURSAL', '=', $user->id_sucursal)->count();  
        
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        /*  --------------------------------------------------------------------------------- */

        if(empty($request->input('search.value'))){            

            /*  ************************************************************ */

            //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

            $posts = NotaCredito::select(DB::raw('NOTA_CREDITO.ID, 
	            	NOTA_CREDITO.FK_VENTA, 
	            	NOTA_CREDITO.SUB_TOTAL,
	            	CLIENTES.NOMBRE,
	            	CLIENTES.RUC, 
	            	IFNULL(NOTA_CREDITO.NUMERO_FACTURA,"NO POSEE") AS NRO_FACTURA, 
	            	NOTA_CREDITO.IVA, 
	            	NOTA_CREDITO.TOTAL, 
	            	NOTA_CREDITO.FECALTAS, 
	            	NOTA_CREDITO.TIPO, 
	            	NOTA_CREDITO.BASE10, 
	            	NOTA_CREDITO.CAJA, 
	            	NOTA_CREDITO.MONEDA,
	            	NOTA_CREDITO.PROCESADO,
	            	NOTA_CREDITO.TIPO_PROCESO'))
            		->leftjoin('CLIENTES', function($join){
		                $join->on('CLIENTES.CODIGO', '=', 'NOTA_CREDITO.CLIENTE')
		                ->on('CLIENTES.ID_SUCURSAL', '=', 'NOTA_CREDITO.ID_SUCURSAL');
		            })
                    ->where('NOTA_CREDITO.ID_SUCURSAL', '=', $user->id_sucursal)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

            /*  ************************************************************ */

        }else {

            /*  ************************************************************ */

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            /*  ************************************************************ */

            // CARGAR LOS PRODUCTOS FILTRADOS EN DATATABLE

            $posts =  NotaCredito::select(DB::raw('NOTA_CREDITO.ID, 
	            	NOTA_CREDITO.FK_VENTA, 
	            	NOTA_CREDITO.SUB_TOTAL,
	            	CLIENTES.NOMBRE,
	            	CLIENTES.RUC, 
	            	IFNULL(NOTA_CREDITO.NUMERO_FACTURA,"NO POSEE") AS NRO_FACTURA, 
	            	NOTA_CREDITO.IVA, 
	            	NOTA_CREDITO.TOTAL, 
	            	NOTA_CREDITO.FECALTAS, 
	            	NOTA_CREDITO.TIPO, 
	            	NOTA_CREDITO.BASE10, 
	            	NOTA_CREDITO.CAJA, 
	            	NOTA_CREDITO.MONEDA,
	            	NOTA_CREDITO.PROCESADO,
	            	NOTA_CREDITO.TIPO_PROCESO'))
            		->leftjoin('CLIENTES', function($join){
		                $join->on('CLIENTES.CODIGO', '=', 'NOTA_CREDITO.CLIENTE')
		                ->on('CLIENTES.ID_SUCURSAL', '=', 'NOTA_CREDITO.ID_SUCURSAL');
		            })
            	->where('NOTA_CREDITO.ID_SUCURSAL', '=', $user->id_sucursal)
	            ->where(function ($query) use ($search) {
		            $query->where('NOTA_CREDITO.FK_VENTA','LIKE',"%{$search}%")
		                  ->orWhere('NOTA_CREDITO.TOTAL', 'LIKE',"%{$search}%")
		                  ->orWhere('CLIENTES.NOMBRE', 'LIKE',"%{$search}%")
		                  ->orWhere('NOTA_CREDITO.ID', 'LIKE',"%{$search}%");
		            })
	            ->offset($start)
	            ->limit($limit)
	            ->orderBy($order,$dir)
	            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = NotaCredito::where('NOTA_CREDITO.ID_SUCURSAL', '=', $user->id_sucursal)
            ->leftjoin('CLIENTES', function($join){
		                $join->on('CLIENTES.CODIGO', '=', 'NOTA_CREDITO.CLIENTE')
		                ->on('CLIENTES.ID_SUCURSAL', '=', 'NOTA_CREDITO.ID_SUCURSAL');
		            })
	            ->where(function ($query) use ($search) {
		            $query->where('NOTA_CREDITO.FK_VENTA','LIKE',"%{$search}%")
		                  ->orWhere('NOTA_CREDITO.TOTAL', 'LIKE',"%{$search}%")
		                  ->orWhere('CLIENTES.NOMBRE', 'LIKE',"%{$search}%")
		                  ->orWhere('NOTA_CREDITO.ID', 'LIKE',"%{$search}%");
		            })
            	->count();

            /*  ************************************************************ */  

        }

        $data = array();

        if(!empty($posts)){
            foreach ($posts as $post){

            	// CARGAR EN LA VARIABLE 

                if($post->TIPO==1){
                 	$nestedData['TIPO'] = 'POR DEVOLUCIÓN';
                }else if($post->TIPO==2){
                 	$nestedData['TIPO'] = 'POR DESCUENTO';
                }else if($post->TIPO==3){
                	$nestedData['TIPO']= 'POR DESCUENTO DE MERCADERIA';
                }

                $nestedData['CLIENTE'] = $post->NOMBRE;
                $nestedData['RUC'] = $post->RUC;
                $nestedData['NRO_FACTURA'] = $post->NRO_FACTURA;
                $nestedData['ID'] = $post->ID;
                $nestedData['ID_VENTA'] = $post->FK_VENTA;
                $nestedData['TOTAL'] = Common::precio_candec($post->TOTAL, $post->MONEDA);
                $nestedData['SUB_TOTAL'] = Common::precio_candec($post->SUB_TOTAL, $post->MONEDA);
                $nestedData['IVA'] = Common::precio_candec($post->IVA, $post->MONEDA);
                $nestedData['FECHA'] = substr($post->FECALTAS, 0, 10);
                $nestedData['CAJA'] = $post->CAJA;

                if ($post->PROCESADO == 1) {

                    $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarCredito' title='Mostrar Nota'><i class='fa fa-list  text-secondary'  aria-hidden='true'></i></a>&emsp;<a href='#' id='imprimirReporte' title='Reporte'><i class='fa fa-file text-secondary' aria-hidden='true'></i></a>";
                    $nestedData['ESTATUS'] = 'table-success';
                } else {
                	if($post->PROCESADO == 2){
	                	$nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarCredito' title='Mostrar Nota'><i class='fa fa-list  text-secondary'  aria-hidden='true'></i></a>";
	                    $nestedData['ESTATUS'] = 'table-danger';
                	}else{
                		 $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarCredito' title='Mostrar Nota'><i class='fa fa-list  text-secondary'  aria-hidden='true'></i></a>&emsp;<a href='#' id='imprimirReporte' title='Reporte'><i class='fa fa-file text-secondary' aria-hidden='true'></i></a>&emsp;<a href='#' id='devolverCreditoNota' title='Devolver'><i class='fa fa-arrow-alt-circle-left text-danger' aria-hidden='true'></i></a>";
                    $nestedData['ESTATUS'] = '';
                	}

                   
                }
                
                $data[] = $nestedData;

            }
        }

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
     public static function notaCreditoCancelar($data){
     	try {
     		/*  --------------------------------------------------------------------------------- */
     		//INICAR LA TRANSACCION

     		DB::connection('retail')->beginTransaction();
     		/*  --------------------------------------------------------------------------------- */

     		//OBTENER TODOS LOS DATOS DEL USUARIO
     		$user = auth()->user();
     		/*  --------------------------------------------------------------------------------- */

     		//DEFINIR LA VARIABLE PARA EL STATUSTEXT
 			$statusText='';
 			/*  --------------------------------------------------------------------------------- */

 			//DEFINIR LA VARIABLE PARA MOSTRAR O NO EL MENSAJE
 			$control_seleccionador=false;
 			/*  --------------------------------------------------------------------------------- */
 			$nota_credito=NotaCredito::Select(DB::raw('TIPO'))->where('ID','=',$data['id'])->get();
 			if($nota_credito[0]['TIPO']===1){
 				 //OBTENER LOS VALORES DE LA NOTA DE CREDITO SELECCIONADA AGRUPADA POR CANTIDAD Y POR LOTE

			     $valores =  NotaCreditoDet::select(DB::raw('NOTA_CREDITO_DET.CODIGO_PROD AS COD_PROD, 
				            	NOTA_CREDITO_TIENE_LOTE.CANTIDAD AS CANTIDAD, 
				            	NOTA_CREDITO_DET.FK_VENTASDET, 
				            	NOTA_CREDITO_TIENE_LOTE.ID_LOTE'))
							->leftjoin('NOTA_CREDITO_TIENE_LOTE','NOTA_CREDITO_TIENE_LOTE.FK_NOTA_CREDITO_DET','=','NOTA_CREDITO_DET.ID')
							->leftjoin('NOTA_CREDITO','NOTA_CREDITO.ID','=','NOTA_CREDITO_DET.FK_NOTA_CREDITO')
			            	->where('NOTA_CREDITO.ID_SUCURSAL', '=', $user->id_sucursal)
			            	->where('NOTA_CREDITO.ID','=',$data['id'])
			            	->groupBy('NOTA_CREDITO_TIENE_LOTE.ID_LOTE')
			            	->orderBy('NOTA_CREDITO_DET.CODIGO_PROD')
				            ->get();
			     /*  --------------------------------------------------------------------------------- */
							
				 //RECORRER LOS VALORES PARA LA VERIFICACION DEL STOCK DEL LOTE			
				 foreach ($valores as $key => $value) {
					/*  --------------------------------------------------------------------------------- */
					//VERIFICAR LA RESTA ANTES DE REALIZARLA
				    $control_stock=Stock::verificar_resta($value->ID_LOTE,$value->CANTIDAD);

				    /*  --------------------------------------------------------------------------------- */
				    //SI LA RESTA ES NEGATIVA ENTONCES ENTRA
				    if($control_stock<0){
				    	/*  --------------------------------------------------------------------------------- */
				    	//CAMBIO DE LA VARIABLE PARA MOSTRAR EL MENSAJE EN ESPECIFICO
				        $control_seleccionador=true;
				        /*  --------------------------------------------------------------------------------- */
				        //ALMACENAR EN LA VARIABLE STATUSTEXT LOS CODIGOS QUE YA NO CUMPLEN LA CONDICION PARA ANULAR LA NOTA DE CREDITO
				        $statusText=$statusText.'El Codigo : '.$value->COD_PROD. ' Id lote : '.$value->ID_LOTE.'<br>';
				           		
				    }
				 }
				  /*  --------------------------------------------------------------------------------- */
				  //SI CONTROL SELECCIONADOR ES VERDADERO ENTONCES MUESTRA EL MENSAJE Y TERMINA LA EJECUCION DEL PROGRAMA

				 if($control_seleccionador){
					/*  --------------------------------------------------------------------------------- */
					//CONCATENA UNA ULTIMA VEZ LA VARIABLE STATUS TEXT CON EL MENSAJE FINAL A MOSTRAR
					$statusText=$statusText.' Ya tienen movimientos en los lotes seleccionados, Por ende es imposible realizar la cancelacion de la nota de credito';
					/*  --------------------------------------------------------------------------------- */
					//RETORNA EL VALOR FALSO CON EL STATUSTEXT
					 return ['response' => false, 'statusText' => '<br>'.$statusText];
				 }
				    foreach ($valores as $key => $value) {
						Stock::restar_stock_id_lote($value->ID_LOTE, $value->CANTIDAD);
				          # code...
					}
 			}

 			

			/*  --------------------------------------------------------------------------------- */
			//ACTUALIZAR EL PROCESO DE NOTA A CREDITO PROCESADO EN 2 PARA REFERENCIAR A CANCELADO
			NotaCredito::where('ID','=', $data['id'])
		     ->where('ID_SUCURSAL','=', $user->id_sucursal)
		     ->update(['PROCESADO' => 2]);


			/*  --------------------------------------------------------------------------------- */
			// GUARDA UN LOG DE INFORMACION
		    Log::info('Nota de credito: Éxito al cancelar.', ['Nota de credito id:' => $data['id']]);
		    /*  --------------------------------------------------------------------------------- */
			// ENVIAR TRANSACCION A BD

            DB::connection('retail')->commit();
            //DEVUELVE TRUE Y TERMINA EL PROGRAMA
		   /*  --------------------------------------------------------------------------------- */
     	   return ['response' => true];
     		
     	} catch (Exception $e) {
     		/*  --------------------------------------------------------------------------------- */
     		// NO GUARDAR NINGUN CAMBIO 

           DB::connection('retail')->rollBack();
           throw $e;
     	}

     	
     	
     	/*return ['response' => true];*/
    }
        public static function creditoNotaDetalle($request) {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $codigo = $request->input('codigo');
        $c = 0;

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'COD_PROD', 
                            1 => 'DESCRIPCION',
                            2 => 'CANTIDAD',
                            3 => 'PRECIO',
                            4 => 'IVA',
                            5 => 'TOTAL'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = NotaCreditoDet::
                    where([
                        'NOTA_CREDITO_DET.ID_SUCURSAL' => $user->id_sucursal,
                        'NOTA_CREDITO_DET.FK_NOTA_CREDITO' => $codigo
                    ])
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

            $posts = NotaCreditoDet::select(DB::raw('0 AS ITEM, NOTA_CREDITO_DET.CODIGO_PROD AS COD_PROD, NOTA_CREDITO_DET.DESCRIPCION, NOTA_CREDITO_DET.CANTIDAD, NOTA_CREDITO_DET.PRECIO, NOTA_CREDITO_DET.IVA, NOTA_CREDITO_DET.TOTAL'))
                    ->where([
                        'NOTA_CREDITO_DET.ID_SUCURSAL' => $user->id_sucursal,
                        'NOTA_CREDITO_DET.FK_NOTA_CREDITO' => $codigo
                    ])
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

            $posts = NotaCreditoDet::select(DB::raw('0 AS ITEM, NOTA_CREDITO_DET.CODIGO_PROD AS COD_PROD, NOTA_CREDITO_DET.DESCRIPCION, NOTA_CREDITO_DET.CANTIDAD, NOTA_CREDITO_DET.PRECIO, NOTA_CREDITO_DET.IVA, NOTA_CREDITO_DET.TOTAL'))
                    ->where([
                        'NOTA_CREDITO_DET.ID_SUCURSAL' => $user->id_sucursal,
                        'NOTA_CREDITO_DET.FK_NOTA_CREDITO' => $codigo
                    ])
                            ->where(function ($query) use ($search) {
                                $query->where('NOTA_CREDITO_DET.CODIGO_PROD','LIKE',"{$search}%")
                                      ->orWhere('NOTA_CREDITO_DET.DESCRIPCION', 'LIKE',"{$search}%");
                            })
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered =  NotaCreditoDet::select(DB::raw('0 AS ITEM, NOTA_CREDITO_DET.CODIGO_PROD AS COD_PROD, NOTA_CREDITO_DET.DESCRIPCION, NOTA_CREDITO_DET.CANTIDAD, NOTA_CREDITO_DET.PRECIO, NOTA_CREDITO_DET.IVA, NOTA_CREDITO_DET.TOTAL'))
                    ->where([
                        'NOTA_CREDITO_DET.ID_SUCURSAL' => $user->id_sucursal,
                        'NOTA_CREDITO_DET.FK_NOTA_CREDITO' => $codigo
                    ])
                            ->where(function ($query) use ($search) {
                                $query->where('NOTA_CREDITO_DET.CODIGO_PROD','LIKE',"{$search}%")
                                      ->orWhere('NOTA_CREDITO_DET.DESCRIPCION', 'LIKE',"{$search}%");
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

                $c = $c + 1;
                $nestedData['ITEM'] = $c;
                $nestedData['COD_PROD'] = $post->COD_PROD;
                $nestedData['CANTIDAD'] = $post->CANTIDAD;
                $nestedData['DESCRIPCION'] = $post->DESCRIPCION;
                $nestedData['PRECIO'] = Common::formato_precio($post->PRECIO, 2);
                $nestedData['IVA'] = Common::formato_precio($post->IVA, 2);
                $nestedData['TOTAL'] = Common::formato_precio($post->TOTAL, 2);

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
        public static function guardar_referencia_producto_descuento_lote($codigo, $cantidad, $id_ventas_det, $id_nota_credito)
    {
    	//ESTA FUNCION SE CREA PARA NO PUEDAN DEVOLVER PRODUCTOS QUE TIENEN DESCUENTO YA REALIZADO POR NOTA DE CREDITO , SERIA UNA DEVOLUCION SIN QUE EL STOCK ENTRE EN LA TIENDA NUEVAMENTE.
    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER LOS DATOS DEL USUARIO LOGUEADO 

    	$user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

    	$diaHora = date('Y-m-d H:i:s');
    	$dia = date('Y-m-d');
    	$hora = date('H:i:s');

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LOTE NO ES NULO 

        if ($id_ventas_det === NULL) {
            $id_ventas_det = 0;
        }

        /*  --------------------------------------------------------------------------------- */

        if ($id_ventas_det > 0) {

        	/*  --------------------------------------------------------------------------------- */

        	$lotes_a_no_devolver = VentasDetTieneLotes::select(DB::raw('ventasdet_tiene_lotes.ID_LOTE, ventasdet_tiene_lotes.CANTIDAD, IFNULL((SELECT 
                    sum(NCL.CANTIDAD)
                FROM
                    NOTA_CREDITO_TIENE_LOTE AS NCL
                        LEFT JOIN
                    NOTA_CREDITO_DET ON NOTA_CREDITO_DET.ID = NCL.FK_NOTA_CREDITO_DET
                        LEFT JOIN
                    NOTA_CREDITO ON NOTA_CREDITO.ID = NOTA_CREDITO_DET.FK_NOTA_CREDITO
                WHERE
                    NCL.ID_LOTE = ventasdet_tiene_lotes.ID_LOTE
                        AND NCL.FK_VENTA_DET = ventasdet_tiene_lotes.ID_VENTAS_DET
                        AND NOTA_CREDITO.PROCESADO <> 2 group by ncl.ID_LOTE ),
            0) AS CANTIDAD_DEVUELTA, 
               IFNULL((SELECT 
                    sum(NCLTD.CANTIDAD)
                FROM
                    NOTA_CREDITO_LOTE_TIENE_DESCUENTO AS NCLTD
                        LEFT JOIN
                    NOTA_CREDITO_DET ON NOTA_CREDITO_DET.ID = NCLTD.FK_NOTA_CREDITO_DET
                        LEFT JOIN
                    NOTA_CREDITO ON NOTA_CREDITO.ID = NOTA_CREDITO_DET.FK_NOTA_CREDITO
                WHERE
                    NCLTD.ID_LOTE = ventasdet_tiene_lotes.ID_LOTE
                        AND NCLTD.FK_VENTA_DET = ventasdet_tiene_lotes.ID_VENTAS_DET
                        AND NOTA_CREDITO.PROCESADO <> 2 group by NCLTD.ID_LOTE ),
            0) AS CANTIDAD_DEVUELTA_DESCUENTO '))
	        ->where('ID_VENTAS_DET','=',$id_ventas_det)
	        ->get();

        	/*  --------------------------------------------------------------------------------- */

        	// SUMAR ID LOTES 

        	foreach ($lotes_a_no_devolver as $key => $value) {
        		
        		/*  --------------------------------------------------------------------------------- */

        		// RESTAR LA CANTIDAD QUE YA SE DEVOLVIO 

        		$value->CANTIDAD = $value->CANTIDAD - ($value->CANTIDAD_DEVUELTA+$value->CANTIDAD_DEVUELTA_DESCUENTO);

        		/*  --------------------------------------------------------------------------------- */
        		
        		if ($value->CANTIDAD >= $cantidad) {

        			/*  --------------------------------------------------------------------------------- */

        			/*Stock::where('ID','=', $value->ID_LOTE)
			        ->increment('CANTIDAD', $cantidad);*/

			        /*  --------------------------------------------------------------------------------- */

			       /* LoteUser::guardar_referencia($user->id, 3, $value->ID_LOTE, $diaHora);*/

    				/*  --------------------------------------------------------------------------------- */

    				NotaCreditoLoteTieneDescuento::guardar_referencia(
			            [
			                'FK_VENTA_DET' => $id_ventas_det,
			                'FK_NOTA_CREDITO_DET' => $id_nota_credito,
			                'ID_LOTE' => $value->ID_LOTE,
			                'CANTIDAD' => $cantidad
			            ]
			        );

    				/*  --------------------------------------------------------------------------------- */

    				$cantidad = 0;

    				/*  --------------------------------------------------------------------------------- */

        		} else if($value->CANTIDAD > 0) {

        			/*  --------------------------------------------------------------------------------- */

        			/*Stock::where('ID','=', $value->ID_LOTE)
			        ->increment('CANTIDAD', $value->CANTIDAD);*/

			        /*  --------------------------------------------------------------------------------- */

			        NotaCreditoLoteTieneDescuento::guardar_referencia(
			            [
			                'FK_VENTA_DET' => $id_ventas_det,
			                'FK_NOTA_CREDITO_DET' => $id_nota_credito,
			                'ID_LOTE' => $value->ID_LOTE,
			                'CANTIDAD' => $value->CANTIDAD
			            ]
			        );

    				/*  --------------------------------------------------------------------------------- */

			        $cantidad = $cantidad - $value->CANTIDAD;


			      

    				/*  --------------------------------------------------------------------------------- */

        		}

        		if ($cantidad === 0) {
        			break;
        		}

        	}

        	/*  --------------------------------------------------------------------------------- */
        	

        } 

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return ["response" => true];

        /*  --------------------------------------------------------------------------------- */

    }

    public static function generarConsulta($data, $order, $dir){

        $inicio = date('Y-m-d', strtotime($data['Inicio']));
        $final = date('Y-m-d', strtotime($data['Final']));
        $procesado = $data['Procesado'];
        $sucursal = $data['Sucursal'];
        $tipo = $data['Tipo'];
        $cod_cliente = $data['Cliente'];
        $caja = $data['Caja'];

        if(count($data['Procesado']) == 1 && $data['Procesado'][0] == 1){
        	
        	$procesado = $data['Procesado'][0];

	        $notaCredito = NotaCredito::select(
	                DB::raw('NOTA_CREDITO.ID AS ID'),
	    			DB::raw('CLIENTES.NOMBRE AS CLIENTE'),
	                DB::raw('NOTA_CREDITO.CAJA AS CAJA'),
	                DB::raw('NOTA_CREDITO.FECMODIF AS FECHA'),
	                DB::raw('NOTA_CREDITO.TIPO AS TIPO'),
	                DB::raw('NOTA_CREDITO.PROCESADO AS PROCESADO'),
	                DB::raw('NOTA_CREDITO.IVA AS IVA'),
	                DB::raw('NOTA_CREDITO.SUB_TOTAL AS SUBTOTAL'),
	                DB::raw('NOTA_CREDITO.TOTAL AS TOTAL'),
	                DB::raw('NOTA_CREDITO.FK_VENTA AS FK_VENTA'),
	                DB::raw('NOTA_CREDITO.MONEDA AS MONEDA'),
	                DB::raw('NOTA_CREDITO.NUMERO_FACTURA AS NRO_FACTURA'),
	                DB::raw('NOTA_CREDITO.DESCUENTO_MONTO AS DESCUENTO_MONTO'))
	            ->leftJoin('CLIENTES', function($join){
	                    $join->on('CLIENTES.CODIGO', '=', 'NOTA_CREDITO.CLIENTE')
	                         ->on('CLIENTES.ID_SUCURSAL', '=', 'NOTA_CREDITO.ID_SUCURSAL');
	                })
	        ->whereBetween('NOTA_CREDITO.FECMODIF', [$inicio , $final])
	        ->where('NOTA_CREDITO.ID_SUCURSAL', '=', $sucursal)
	        ->whereIn('NOTA_CREDITO.TIPO', $tipo)
	        ->where('NOTA_CREDITO.PROCESADO', '=', $procesado)
	        ->groupBy('NOTA_CREDITO.ID')
	        ->orderBy($order, $dir);

        }else{

	        $notaCredito = NotaCredito::select(
	                DB::raw('NOTA_CREDITO.ID AS ID'),
	    			DB::raw('CLIENTES.NOMBRE AS CLIENTE'),
	                DB::raw('NOTA_CREDITO.CAJA AS CAJA'),
	                DB::raw('NOTA_CREDITO.FECALTAS AS FECHA'),
	                DB::raw('NOTA_CREDITO.TIPO AS TIPO'),
	                DB::raw('NOTA_CREDITO.PROCESADO AS PROCESADO'),
	                DB::raw('NOTA_CREDITO.IVA AS IVA'),
	                DB::raw('NOTA_CREDITO.SUB_TOTAL AS SUBTOTAL'),
	                DB::raw('NOTA_CREDITO.TOTAL AS TOTAL'),
	                DB::raw('NOTA_CREDITO.FK_VENTA AS FK_VENTA'),
	                DB::raw('NOTA_CREDITO.MONEDA AS MONEDA'),
	                DB::raw('NOTA_CREDITO.NUMERO_FACTURA AS NRO_FACTURA'),
	                DB::raw('NOTA_CREDITO.DESCUENTO_MONTO AS DESCUENTO_MONTO'))
	            ->leftJoin('CLIENTES', function($join){
	                    $join->on('CLIENTES.CODIGO', '=', 'NOTA_CREDITO.CLIENTE')
	                         ->on('CLIENTES.ID_SUCURSAL', '=', 'NOTA_CREDITO.ID_SUCURSAL');
	                })
	        ->whereBetween('NOTA_CREDITO.FECALTAS', [$inicio , $final])
	        ->where('NOTA_CREDITO.ID_SUCURSAL', '=', $sucursal)
	        ->whereIn('NOTA_CREDITO.TIPO', $tipo)
	        ->whereIn('NOTA_CREDITO.PROCESADO', $procesado)
	        ->groupBy('NOTA_CREDITO.ID')
	        ->orderBy($order, $dir);
        }

        if(!empty($caja) && $caja!='NaN'){

            $notaCredito->where('NOTA_CREDITO.CAJA', '=', $caja);
        }
        
        if(!empty($cod_cliente)){

            $notaCredito->where('NOTA_CREDITO.CLIENTE', '=', $cod_cliente);
        }


        $notaCredito = $notaCredito->get();

        return $notaCredito;
    }

    public static function notaCreditoRpt($request) {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
            0 => 'NOTA_CREDITO.FECALTAS', 
            1 => 'NOTA_CREDITO.ID',
            2 => 'CLIENTES.NOMBRE',
            3 => 'NOTA_CREDITO.CAJA',
            4 => 'NOTA_CREDITO.NUMERO_FACTURA',
            5 => 'NOTA_CREDITO.FK_VENTA',
            6 => 'NOTA_CREDITO.FECALTAS',
            7 => 'NOTA_CREDITO.TIPO',
            8 => 'NOTA_CREDITO.PROCESADO',
            9 => 'NOTA_CREDITO.DESCUENTO_MONTO',
            10 => 'NOTA_CREDITO.IVA',
            11 => 'NOTA_CREDITO.SUB_TOTAL',
            12 => 'NOTA_CREDITO.TOTAL'
        );


        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $item = 1;

        $datos = array(
            'Sucursal' => $request->input('Sucursal'),
            'Inicio' => date('Y-m-d', strtotime($request->input('Inicio'))),
            'Final' => date('Y-m-d', strtotime($request->input('Final'))),
            'Procesado' => $request->input('Procesado'),
            'Tipo' => $request->input('Tipo'),
            'Cliente' => $request->input('Cliente'),
            'Caja' => $request->input('Caja'),
        );
    
        /*  --------------------------------------------------------------------------------- */

        //  CARGAR TODOS LOS DATOS ENCONTRADOS 

        $posts = NotaCredito::generarConsulta($datos, $order, $dir);     
        $data = array();
        
        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts)){

	        /*  ************************************************************ */
	        if(count($posts) > 0){

		        $moneda = $posts[0]->MONEDA;
		        $candec = (Parametro::candec($moneda))["CANDEC"];

	        }

            foreach ($posts as $post){

                /*  --------------------------------------------------------------------------------- */

                // CARGAR EN LA VARIABLE 

                $cliente = mb_strtolower($post->CLIENTE);
                $cliente = substr($cliente,0,27);
                $nestedData['ITEM'] = $item;
                $nestedData['ID'] = $post->ID;
                $nestedData['CLIENTE'] = utf8_decode(utf8_encode(ucwords($cliente)));
                $fecha = substr($post->FECHA,0,-9);
                $nestedData['FECHA'] = $fecha;     
                $nestedData['CAJA'] = $post->CAJA;
                $nestedData['FK_VENTA'] = $post->FK_VENTA; 

                if($post->PROCESADO == 0){
                	$nestedData['PROCESADO'] = 'Pendiente';
                }elseif($post->PROCESADO == 1){
                	$nestedData['PROCESADO'] = 'Procesado';
                }elseif ($post->PROCESADO == 2) {
                	$nestedData['PROCESADO'] = 'Cancelado';
                }

                if($post->TIPO == 1){
                	$nestedData['TIPO'] = 'Por Devolución';
                }elseif($post->TIPO == 2){
                	$nestedData['TIPO'] = 'Por Descuento de Venta';
                }elseif ($post->TIPO == 3) {
                	$nestedData['TIPO'] = 'Por Descuento de Mercadería';
                }

                $nestedData['IVA'] = Common::formato_precio($post->IVA, $candec);
                $nestedData['DESCUENTO_MONTO'] = Common::formato_precio($post->DESCUENTO_MONTO, $candec);
                $nestedData['SUBTOTAL'] = Common::formato_precio($post->SUBTOTAL, $candec);
                $nestedData['TOTAL'] = Common::formato_precio($post->TOTAL, $candec);
                $nestedData['NRO_FACTURA'] = $post->NRO_FACTURA;
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

 public static function Obtener_Nota_Credito_Con_id_venta($id_nota){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER NOTA DE CREDITO CON O SIN COTIZACION

           
                $nota_credito = NotaCredito::select(DB::raw('NOTA_CREDITO.FK_VENTA, NOTA_CREDITO.TOTAL'))
                ->where('NOTA_CREDITO.ID','=',$id_nota)
                ->get()->toArray();
            
       

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR 

        if (count($nota_credito) > 0) {
            return ['response' => true, 'nota' => $nota_credito];
        } else {
            return ['response' => false, 'statusText' => 'No se han encontrado Notas de Credito'];
        }

        /*  --------------------------------------------------------------------------------- */

    }
}
