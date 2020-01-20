<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Pagos_Prov_Cheque;
use App\Moneda;

class Pagos_Prov extends Model
{

	/*  --------------------------------------------------------------------------------- */

    protected $connection = 'retail';
    protected $table = 'pagos_prov';

    /*  --------------------------------------------------------------------------------- */

    public static function insertar($data){

    	try {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

    	// OBTENER ULTIMO CODIGO 

    	$codigo = Pagos_Prov::ultimo_codigo();

    	/*  --------------------------------------------------------------------------------- */

    	$pago = Pagos_Prov::insertGetId([
    		'CODIGO' => $codigo,
    		'FECHA' => $data["FECHA"],
    		'MONEDA1' => $data["GUARANIES"],
    		'MONEDA2' => $data["DOLARES"],
    		'MONEDA3' => $data["PESOS"],
    		'MONEDA4' => $data["REALES"],
    		'PAGO' => $data["PAGO"],
    		'VUELTO' => $data["VUELTO"],
    		'SALDO' => $data["SALDO"],
    		'RECIBO' => $data["RECIBO"],
    		'FECALTAS' => $data["FECALTAS"],
    		'HORALTAS' => $data["HORALTAS"],
    		'FK_USER_CR' => $data["FK_USER_CR"],
    		'ID_SUCURSAL' => $user->id_sucursal
    	]);

    	/*  --------------------------------------------------------------------------------- */

    	Log::info('Pago Proveedor: Éxito al guardar.', ['PAGO' => $pago]);

    	/*  --------------------------------------------------------------------------------- */

    	// RETORNAR VALOR 

    	return ["response" => true, "id" => $pago];

    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Pago Proveedor: Error al guardar.', ['DEUDA' => $deuda, 'MONTO' => $monto]);

			/*  --------------------------------------------------------------------------------- */

			// RETORNAR VALOR 
    	
    		return ["response" => false, "Error al guardar el Pago del Proveedor"];

    		/*  --------------------------------------------------------------------------------- */

		}
    }

    public static function ultimo_codigo() {

		/*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

		/*  --------------------------------------------------------------------------------- */

		// OBTENER ULTIMO CODIGO

		$codigo = Pagos_Prov::select('CODIGO')
		->where(['ID_SUCURSAL' => $user->id_sucursal])
		->orderBy('CODIGO', 'desc')
		->limit(1)
		->get();

		/*  --------------------------------------------------------------------------------- */

		// RETORNAR VALOR 

		if (count($codigo) > 0) {
			return $codigo[0]['CODIGO'] + 1;
		} else {
			return 1;
		}

		/*  --------------------------------------------------------------------------------- */

	}

	public static function obtener_pagos_deudas_compra($codigo){

		/*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER DEUDAS

        $pagado = Pagos_Prov::select(DB::raw('SUM(PAGOS_PROV_DET.PAGO) AS PAGO'))
        				 ->leftjoin('PAGOS_PROV_DET', 'PAGOS_PROV_DET.FK_PAGOS_PROV', '=', 'PAGOS_PROV.ID')
        				 ->leftjoin('DEUDAS', 'DEUDAS.ID', '=', 'PAGOS_PROV_DET.FK_DEUDA')
                         ->leftjoin('COMPRAS', 'COMPRAS.ID', '=', 'DEUDAS.FK_COMPRA')
                         ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'COMPRAS.PROVEEDOR')
                         ->where('DEUDAS.ID_SUCURSAL','=', $user->id_sucursal)
                         ->where('COMPRAS.CODIGO','=', $codigo)
                         ->where('DEUDAS.ESTATUS','<>', 3)
                         ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR 
        
        if (count($pagado) > 0) {
            return ["response" => true, "pagado" => $pagado[0]["PAGO"]];
        } else {
            return ["response" => false];
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function obtener_pagos_deuda($id){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER DEUDAS

        $pagado = Pagos_Prov::select(DB::raw('SUM(PAGOS_PROV_DET.PAGO) AS PAGO'))
                         ->leftjoin('PAGOS_PROV_DET', 'PAGOS_PROV_DET.FK_PAGOS_PROV', '=', 'PAGOS_PROV.ID')
                         ->leftjoin('DEUDAS', 'DEUDAS.ID', '=', 'PAGOS_PROV_DET.FK_DEUDA')
                         ->where('DEUDAS.ID_SUCURSAL','=', $user->id_sucursal)
                         ->where('DEUDAS.ID','=', $id)
                         ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR 
        
        if (count($pagado) > 0) {
            return ["response" => true, "pagado" => $pagado[0]["PAGO"]];
        } else {
            return ["response" => false];
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function pago_datatable($request) {

		/*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $compra = $request->input('codigo');

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'PAGO',
                            1 => 'FECHA',
                            2 => 'GUARANIES',
                            3 => 'DOLARES',
                            4 => 'PESOS',
                            5 => 'REALES',
                            6 => 'RECIBO',
                            7 => 'ID'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE PAGOS ENCONTRADAS 

        $totalData = Pagos_Prov::where('PAGOS_PROV.ID_SUCURSAL','=', $user->id_sucursal)
        			 ->where('COMPRAS.CODIGO','=', $compra)
        			 ->leftjoin('PAGOS_PROV_DET', 'PAGOS_PROV_DET.FK_PAGOS_PROV', '=', 'PAGOS_PROV.ID')
        			 ->leftjoin('DEUDAS', 'DEUDAS.ID', '=', 'PAGOS_PROV_DET.FK_DEUDA')
        			 ->leftjoin('COMPRAS', 'COMPRAS.ID', '=', 'DEUDAS.FK_COMPRA')
                     ->count();  
        
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $dia = date('Y-m-d');
        $cinco_dias_despues = date('Y-m-d', strtotime($dia. ' + 5 days'));

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE VALOR EN VARIABLE SEARCH

        if(empty($request->input('search.value')))
        {            

            /*  ************************************************************ */

            //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

            $posts = Pagos_Prov::select(DB::raw('PAGOS_PROV.ID, PAGOS_PROV.PAGO, SUBSTRING(PAGOS_PROV.FECHA, 1, 10) AS FECHA, PAGOS_PROV.MONEDA1 AS GUARANIES, PAGOS_PROV.MONEDA2 AS DOLARES, PAGOS_PROV.MONEDA3 AS PESOS, PAGOS_PROV.MONEDA4 AS REALES, COMPRAS.MONEDA, RECIBO, PAGOS_PROV_TARJETA.MONTO AS TARJETA'))
            		 ->where('PAGOS_PROV.ID_SUCURSAL','=', $user->id_sucursal)
        			 ->where('COMPRAS.CODIGO','=', $compra)
        			 ->leftjoin('PAGOS_PROV_DET', 'PAGOS_PROV_DET.FK_PAGOS_PROV', '=', 'PAGOS_PROV.ID')
                     ->leftjoin('PAGOS_PROV_TARJETA', 'PAGOS_PROV_TARJETA.FK_PAGO_PROV', '=', 'PAGOS_PROV.ID')
        			 ->leftjoin('DEUDAS', 'DEUDAS.ID', '=', 'PAGOS_PROV_DET.FK_DEUDA')
        			 ->leftjoin('COMPRAS', 'COMPRAS.ID', '=', 'DEUDAS.FK_COMPRA')
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

            $posts =  Pagos_Prov::select(DB::raw('PAGOS_PROV.ID, PAGOS_PROV.PAGO, SUBSTRING(PAGOS_PROV.FECHA, 1, 10) AS FECHA, PAGOS_PROV.MONEDA1 AS GUARANIES, PAGOS_PROV.MONEDA2 AS DOLARES, PAGOS_PROV.MONEDA3 AS PESOS, PAGOS_PROV.MONEDA4 AS REALES, COMPRAS.MONEDA, RECIBO, PAGOS_PROV_TARJETA.MONTO AS TARJETA'))
            		 ->where('PAGOS_PROV.ID_SUCURSAL','=', $user->id_sucursal)
        			 ->where('COMPRAS.CODIGO','=', $compra)
        			 ->leftjoin('PAGOS_PROV_DET', 'PAGOS_PROV_DET.FK_PAGOS_PROV', '=', 'PAGOS_PROV.ID')
                     ->leftjoin('PAGOS_PROV_TARJETA', 'PAGOS_PROV_TARJETA.FK_PAGO_PROV', '=', 'PAGOS_PROV.ID')
        			 ->leftjoin('DEUDAS', 'DEUDAS.ID', '=', 'PAGOS_PROV_DET.FK_DEUDA')
        			 ->leftjoin('COMPRAS', 'COMPRAS.ID', '=', 'DEUDAS.FK_COMPRA')
                            ->where(function ($query) use ($search) {
                                $query->where('PAGOS_PROV.ID','LIKE',"{$search}%")
                                      ->orWhere('PAGOS_PROV.PAGO', 'LIKE',"%{$search}%");
                            })   
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered =  Pagos_Prov::where('PAGOS_PROV.ID_SUCURSAL','=', $user->id_sucursal)
        			 ->where('COMPRAS.CODIGO','=', $compra)
        			 ->leftjoin('PAGOS_PROV_DET', 'PAGOS_PROV_DET.FK_PAGOS_PROV', '=', 'PAGOS_PROV.ID')
        			 ->leftjoin('DEUDAS', 'DEUDAS.ID', '=', 'PAGOS_PROV_DET.FK_DEUDA')
        			 ->leftjoin('COMPRAS', 'COMPRAS.ID', '=', 'DEUDAS.FK_COMPRA')
                            ->where(function ($query) use ($search) {
                                $query->where('PAGOS_PROV.ID','LIKE',"{$search}%")
                                      ->orWhere('PAGOS_PROV.PAGO', 'LIKE',"%{$search}%");
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
                $nestedData['PAGO'] = Common::precio_candec($post->PAGO, $post->MONEDA);
                $nestedData['FECHA'] = $post->FECHA;
                $nestedData['GUARANIES'] = Common::precio_candec($post->GUARANIES, 1);
                $nestedData['DOLARES'] = Common::precio_candec($post->DOLARES, 2);
                $nestedData['PESOS'] = Common::precio_candec($post->PESOS, 3);
                $nestedData['REALES'] = Common::precio_candec($post->REALES, 4);
                $nestedData['RECIBO'] = $post->RECIBO;

                $nestedData['TARJETA'] = Common::precio_candec($post->TARJETA, 1);

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

    public static function pagoUnico($id){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $id = $id["id"];

        /*  --------------------------------------------------------------------------------- */

        // OBTENER DEUDAS

        $pago = Pagos_Prov::select(DB::raw('PAGOS_PROV.ID, PAGOS_PROV.PAGO, SUBSTRING(PAGOS_PROV.FECHA, 1, 10) AS FECHA, PAGOS_PROV.MONEDA1 AS GUARANIES, PAGOS_PROV.MONEDA2 AS DOLARES, PAGOS_PROV.MONEDA3 AS PESOS, PAGOS_PROV.MONEDA4 AS REALES, COMPRAS.MONEDA, RECIBO, PAGOS_PROV_TARJETA.MONTO AS TARJETA, 0 AS CHEQUES, TARJETAS.DESCRIPCION AS TARJETA_DESCRIPCION'))
                     ->where('PAGOS_PROV.ID_SUCURSAL','=', $user->id_sucursal)
                     ->where('PAGOS_PROV.ID','=', $id)
                     ->leftjoin('PAGOS_PROV_DET', 'PAGOS_PROV_DET.FK_PAGOS_PROV', '=', 'PAGOS_PROV.ID')
                     ->leftjoin('PAGOS_PROV_TARJETA', 'PAGOS_PROV_TARJETA.FK_PAGO_PROV', '=', 'PAGOS_PROV.ID')
                     ->leftjoin('TARJETAS', 'TARJETAS.ID', '=', 'PAGOS_PROV_TARJETA.FK_TARJETA')
                     ->leftjoin('DEUDAS', 'DEUDAS.ID', '=', 'PAGOS_PROV_DET.FK_DEUDA')
                     ->leftjoin('COMPRAS', 'COMPRAS.ID', '=', 'DEUDAS.FK_COMPRA')
                     ->groupBy('PAGOS_PROV_DET.FK_PAGOS_PROV')
                     ->get();    

        /*  --------------------------------------------------------------------------------- */

        // DAR FORMATO MONEDAS

        $monedas = (Moneda::obtener_monedas())["monedas"];

        foreach ($pago as $key => $value) {

            $pago[$key]->PAGO = Common::precio_candec($value->PAGO, $value->MONEDA);
            $pago[$key]->GUARANIES = Common::precio_candec($value->GUARANIES, $monedas[0]->CODIGO);
            $pago[$key]->DOLARES = Common::precio_candec($value->DOLARES, $monedas[1]->CODIGO);
            $pago[$key]->PESOS = Common::precio_candec($value->PESOS, $monedas[2]->CODIGO);
            $pago[$key]->REALES = Common::precio_candec($value->REALES, $monedas[3]->CODIGO);
            $pago[$key]->TARJETA = Common::precio_candec($value->TARJETA, $monedas[0]->CODIGO);


        }

        /*  --------------------------------------------------------------------------------- */

        // CHEQUES 

        $cheques = Pagos_Prov_Cheque::select(DB::raw('BANCOS.DESCRIPCION, PAGOS_PROV_CHEQUE.NUMERO, PAGOS_PROV_CHEQUE.FECHA_COBRO, PAGOS_PROV_CHEQUE.FORMA, PAGOS_PROV_CHEQUE.MONEDA, PAGOS_PROV_CHEQUE.MONTO'))
                     ->where('PAGOS_PROV_CHEQUE.FK_PAGO_PROV','=', $id)
                     ->leftjoin('BANCOS', 'PAGOS_PROV_CHEQUE.FK_BANCO', '=', 'BANCOS.ID')
                     ->get();

        foreach ($cheques as $key => $value) {
            
            $cheques[$key]->MONTO = Common::precio_candec($value->MONTO, $value->MONEDA);

            if ($value->FORMA === 1) {
                $cheques[$key]->FORMA = 'En Fecha';
            } else  if ($value->FORMA === 2) {
                $cheques[$key]->FORMA = 'Diferido';
            }

        }

        $pago[0]["CHEQUES"] = $cheques;

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR 
        
        if (count($pago) > 0) {
            return ["response" => true, "pago" => $pago[0]];
        } else {
            return ["response" => false];
        }

        /*  --------------------------------------------------------------------------------- */

    }    
}
