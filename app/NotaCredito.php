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

        if ($tipo === '1'){


	        /*  --------------------------------------------------------------------------------- */

	    	foreach ($productos as $key => $value) {
	    		
	    		/*  --------------------------------------------------------------------------------- */

	    		// OBTENER PRECIOS DE LA VENTA DETALLE

	    		$dato = Ventas_det::select(DB::raw('VENTASDET.ID, VENTASDET.BASE5, VENTASDET.BASE10, VENTASDET.EXENTA, VENTASDET.GRAVADA, VENTASDET.IVA, VENTASDET.PRECIO_UNIT, VENTASDET.PRECIO, VENTASDET.DESCRIPCION, VENTAS.MONEDA, PRODUCTOS.IMPUESTO, MONEDAS.CANDEC, VENTAS.CLIENTE, CLIENTES.NOMBRE, CLIENTES.RAZON_SOCIAL, CLIENTES.RUC, IFNULL(VENTAS_DESCUENTO.PORCENTAJE, 0) AS DESCUENTO_GENERAL_PORCENTAJE, IFNULL(VENTAS_CUPON.CUPON_PORCENTAJE, 0) AS DESCUENTO_CUPON_PORCENTAJE'))
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
        $c_rows_array = count($nota_credito_det);
        $c_filas_total = count($nota_credito_det);
        $codigo = $nota_credito->ID;
        $cliente = $nota_credito->CLIENTE;
        $direccion = $nota_credito->DIRECCION;
        $ruc = $nota_credito->RUC;
        $tipo = $nota_credito->TIPO;
        $fecha = $nota_credito->FECALTAS;
        $telefono = $nota_credito->TELEFONO;
        $nombre = 'Nota_Credito'.$codigo.'_'.time().'';
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
        $data['cliente'] = $cliente;
        $data['direccion'] = $direccion;
        $data['ruc'] = $ruc;
        $data['fecha'] = $fecha;
        $data['telefono'] = $telefono;
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
            "format" => [210,297],
            'margin_left' => 11,
			'margin_right' => 7,
			'margin_bottom' => 5,
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

                // PRECIO 
                
                $cotizacion = Cotizacion::CALMONED(['monedaProducto' => $monedaNotaCredito, 'monedaSistema' => 1, 'precio' => Common::quitar_coma($value["PRECIO_UNIT"], 2), 'decSistema' => 0, 'tab_unica' => $tab_unica, "id_sucursal" => $user["id_sucursal"]]);

                // SI NO ENCUENTRA COTIZACION RETORNAR 

                if ($cotizacion["response"] === false) {
                    header('HTTP/1.1 500 Internal Server Error');
                    exit;
                }

                $articulos[$c_rows]["precio"] = $cotizacion["valor"];

                /*  --------------------------------------------------------------------------------- */

                // TOTAL 

                $cotizacion = Cotizacion::CALMONED(['monedaProducto' => $monedaNotaCredito, 'monedaSistema' => 1, 'precio' => Common::quitar_coma($value["PRECIO"], 2), 'decSistema' => 0, 'tab_unica' => $tab_unica, "id_sucursal" => $user["id_sucursal"]]);
                $articulos[$c_rows]["total"] = $cotizacion["valor"];

                // SI NO ENCUENTRA COTIZACION RETORNAR

                if ($cotizacion["response"] === false) {
                    header('HTTP/1.1 500 Internal Server Error');
                    exit;
                }

                $exentas = $exentas + Common::quitar_coma($articulos[$c_rows]["total"], $candec);
                $total = $total + Common::quitar_coma($articulos[$c_rows]["total"], $candec);

                /*  --------------------------------------------------------------------------------- */

            } else {
                
                $articulos[$c_rows]["precio"] = $value["PRECIO_UNIT"];
                $articulos[$c_rows]["total"] = $value["PRECIO"];
                $exentas = $exentas + Common::quitar_coma($value["EXENTAS"], $candec);
                $total = $total + Common::quitar_coma($value["PRECIO"], $candec);
            }

            
            /*  --------------------------------------------------------------------------------- */

            // CARGAR VARIABLES 

            $articulos[$c_rows]["cantidad"] = $value["CANTIDAD"];
            $articulos[$c_rows]["cod_prod"] = $value["COD_PROD"];
            $articulos[$c_rows]["descripcion"] = substr('DEVOLUCION '.$value["DESCRIPCION"], 0,30);
            $cantidad = $cantidad + $value["CANTIDAD"];

            /*  --------------------------------------------------------------------------------- */

            // REVISAR SI EXISTE EXENTAS 

            if ($value["EXENTAS"] > 0) {
                $articulos[$c_rows]["exentas"] = $articulos[$c_rows]["total"];
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

            $c_rows = $c_rows + 1;    
            
            /*  --------------------------------------------------------------------------------- */

            // CONTAR LA CANTIDAD DE FILAS 

            $c = $c + 1;

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
                        NOTA_CREDITO_DET.BASE10'
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
                        CLIENTES.RAZON_SOCIAL,
                        CLIENTES.DIRECCION,
                        CLIENTES.TELEFONO,
                        CLIENTES.CELULAR,
                        CLIENTES.RUC,
                        CLIENTES.CI,
                        CLIENTES.NOMBRE AS CLIENTE'
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
                            4 => 'SUB_TOTAL',
                            5 => 'IVA',
                            6 => 'TOTAL',
                            7 => 'FECHA',
                            8 => 'TIPO',
                            9 => 'CAJA'
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
		                  ->orWhere('NOTA_CREDITO.ID', 'LIKE',"%{$search}%");
		            })
	            ->offset($start)
	            ->limit($limit)
	            ->orderBy($order,$dir)
	            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = NotaCredito::where('NOTA_CREDITO.ID_SUCURSAL', '=', $user->id_sucursal)
	            ->where(function ($query) use ($search) {
		            $query->where('NOTA_CREDITO.FK_VENTA','LIKE',"%{$search}%")
		                  ->orWhere('NOTA_CREDITO.TOTAL', 'LIKE',"%{$search}%")
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
                }

                $nestedData['CLIENTE'] = $post->NOMBRE;
                $nestedData['RUC'] = $post->RUC;
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

                    $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarCredito' title='Mostrar Nota'><i class='fa fa-list  text-secondary'  aria-hidden='true'></i></a>&emsp;<a href='#' id='imprimirReporte' title='Reporte'><i class='fa fa-file text-secondary' aria-hidden='true'></i></a>&emsp;<a href='#' id='devolverProducto' title='Devolver'><i class='fa fa-arrow-alt-circle-left text-danger' aria-hidden='true'></i></a>";
                    $nestedData['ESTATUS'] = '';
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

}
