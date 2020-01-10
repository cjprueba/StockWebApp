<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Common;

class Deuda extends Model
{
    protected $connection = 'retail';
    protected $table = 'deudas';
    const CREATED_AT = 'FECALTAS';
    const UPDATED_AT = 'FECMODIF';

    public static function insertar($datos, $candec, $compra) {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */
		
		// INICIAR VARIABLES 

    	$dia = date('Y-m-d');
    	$hora = date("H:i:s");	
		$cuotas = $datos["credito"]["cuotas"];
		$plan = $datos["credito"]["dias"];
		$dias = $datos["credito"]["dias"];
		$fecha = $datos["credito"]["fecha"];
		$moneda = $datos["moneda"];
		$tipo = $datos["credito"]["opcion"];
		$c = 0;

		/*  --------------------------------------------------------------------------------- */

		// TIPO 
		// 1 - DIAS
		// 2 - CUOTAS
		// 3 - FECHA 

		if ($tipo === "1") {
			$cuotas = 1;
		} else if ($tipo === "2") {
			// echo date('Y-m-d', strtotime($Date. ' + 1 days'));
		} else if ($tipo === "3") {

		}
		
		/*  --------------------------------------------------------------------------------- */
		
		try {

			/*  --------------------------------------------------------------------------------- */

			while ($c < $cuotas) {

				$c++;

				/*  --------------------------------------------------------------------------------- */

				// OBTENRE ULTIMO CODIGO 

				$codigo = Deuda::ultimo_codigo();

				/*  --------------------------------------------------------------------------------- */

				// CALCULAR FECHA

				if ($tipo === "2" && $c > 1) {
					$fecha = date('Y-m-d', strtotime($fecha. ' + '.$plan.' days'));
				}

				/*  --------------------------------------------------------------------------------- */

				Deuda::insert([
					'CODIGO' => $codigo,
					'FK_COMPRA' => $compra,
					'PLAN_PAGO' => $plan,
					'TOTAL' => (Common::quitar_coma($datos["total_compra"], $candec) / $cuotas),
					'FEC_VENC' => $fecha,
					'NRO_CUOTA' => $c,
					'ESTATUS' => 1,
					'TIPO' => $tipo,
					'ID_SUCURSAL' => $user->id_sucursal,
					'HORALTAS' => $hora,
					'FECALTAS' => $dia
				]);

				/*  --------------------------------------------------------------------------------- */

				// SUMAR PLAN

				$plan = $plan + $dias;

				/*  --------------------------------------------------------------------------------- */
			}

			/*  --------------------------------------------------------------------------------- */

			// GUARDAR LOG DE CREACION DE DEUDA

			Log::info('Deuda: Creación de deuda por usuario.', ['id' => $user->id, 'COMPRA' => $compra, 'id_sucursal' => $user->id_sucursal]);

			/*  --------------------------------------------------------------------------------- */

		} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Deuda: Error al guardar.', ['id' => $user->id, 'COMPRA' => $compra, 'id_sucursal' => $user->id_sucursal]);

			/*  --------------------------------------------------------------------------------- */

		}

		/*  --------------------------------------------------------------------------------- */

	}

	public static function ultimo_codigo() {

		/*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

		/*  --------------------------------------------------------------------------------- */

		// OBTENER ULTIMO CODIGO

		$codigo = Deuda::select('CODIGO')
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

	public static function deuda_datatable($request) {

		/*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'PROVEEDOR',
                            1 => 'FEC_VENC',
                            2 => 'NRO_CUOTA',
                            3 => 'INICIAL',
                            4 => 'ACTUAL',
                            5 => 'ESTATUS',
                            6 => 'COMPRA',
                            7 => 'FECALTAS'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = Deuda::select(DB::raw('COUNT(*) AS CONTEO'))
                     ->where('DEUDAS.ID_SUCURSAL','=', $user->id_sucursal)
        			 ->groupBy('DEUDAS.FK_COMPRA')
                     ->get();

        $totalData = count($totalData);

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

            $posts = Deuda::select(DB::raw('COMPRAS.CODIGO AS COMPRA, COMPRAS.MONEDA, PROVEEDORES.NOMBRE, SUBSTRING(DEUDAS.FEC_VENC, 1, 10) AS FEC_VENC, COUNT(*) AS NRO_CUOTA, SUBSTRING(DEUDAS.FECALTAS, 1, 10) AS CREACION, COMPRAS.TOTAL AS INICIAL, SUM(DEUDAS.TOTAL) AS ACTUAL, SUM(DEUDAS.ESTATUS) AS ESTATUS, SUM(PAGOS_PROV_DET.PAGO) AS PAGADO'))
                         ->leftjoin('PAGOS_PROV_DET', 'PAGOS_PROV_DET.FK_DEUDA', '=', 'DEUDAS.ID')
            			 ->leftjoin('COMPRAS', 'COMPRAS.ID', '=', 'DEUDAS.FK_COMPRA')
                         ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'COMPRAS.PROVEEDOR')
                         ->where('DEUDAS.ID_SUCURSAL','=', $user->id_sucursal)
                         ->groupBy('COMPRAS.CODIGO')
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

            $posts =  Deuda::select(DB::raw('COMPRAS.CODIGO AS COMPRA, COMPRAS.MONEDA, PROVEEDORES.NOMBRE, SUBSTRING(DEUDAS.FEC_VENC, 1, 10) AS FEC_VENC, COUNT(*) AS NRO_CUOTA, SUBSTRING(DEUDAS.FECALTAS, 1, 10) AS FECALTAS, COMPRAS.TOTAL AS INICIAL, SUM(DEUDAS.TOTAL) AS ACTUAL, SUM(DEUDAS.ESTATUS) AS ESTATUS, SUM(PAGOS_PROV_DET.PAGO) AS PAGADO'))
                         ->leftjoin('PAGOS_PROV_DET', 'PAGOS_PROV_DET.FK_DEUDA', '=', 'DEUDAS.ID')
             			 ->leftjoin('COMPRAS', 'COMPRAS.ID', '=', 'DEUDAS.FK_COMPRA')
                         ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'COMPRAS.PROVEEDOR')
                         ->where('DEUDAS.ID_SUCURSAL','=', $user->id_sucursal)
                            ->where(function ($query) use ($search) {
                                $query->where('COMPRAS.CODIGO','LIKE',"{$search}%")
                                      ->orWhere('PROVEEDORES.NOMBRE', 'LIKE',"%{$search}%");
                            })
                         ->groupBy('COMPRAS.CODIGO')   
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS

            $totalFiltered = Deuda::select(DB::raw('COUNT(*) AS CONTEO'))
                         ->leftjoin('COMPRAS', 'COMPRAS.ID', '=', 'DEUDAS.FK_COMPRA')
                         ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'COMPRAS.PROVEEDOR')
                         ->where('DEUDAS.ID_SUCURSAL','=', $user->id_sucursal)
                         ->where(function ($query) use ($search) {
                                $query->where('COMPRAS.CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('PROVEEDORES.NOMBRE', 'LIKE',"%{$search}%");
                            })
                         ->groupBy('COMPRAS.CODIGO')   
                         ->get();

              $totalFiltered = count($totalFiltered);

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

                $nestedData['CODIGO'] = $post->CODIGO;
                $nestedData['COMPRA'] = $post->COMPRA;
                $nestedData['PROVEEDOR'] = $post->NOMBRE;

                /*  --------------------------------------------------------------------------------- */

                // REVISAR ESTATUS 

                if ((int)($post->NRO_CUOTA * 3) === (int)$post->ESTATUS) {
                    $nestedData['ESTATUS'] = '<span class="badge badge-success">Pagado</span>';
                } else if ((int)$post->NRO_CUOTA === (int)$post->ESTATUS){
                    $nestedData['ESTATUS'] = '<span class="badge badge-warning">Pendiente</span>';
                } else if ((int)$post->ESTATUS > (int)$post->NRO_CUOTA) {
                    $nestedData['ESTATUS'] = '<span class="badge badge-info">Parcial</span>';
                } else {
                    $nestedData['ESTATUS'] = 'N/A';
                }

                /*  --------------------------------------------------------------------------------- */

                $nestedData['PLAN_PAGO'] = $post->PLAN_PAGO;

                if ($dia >= $post->FEC_VENC) {
                	$nestedData['FEC_VENC'] = '<span class="text-danger">'.$post->FEC_VENC.'</span>';
                } else if (($post->FEC_VENC <= $cinco_dias_despues) && ($post->FEC_VENC > $dia)) {
                	$nestedData['FEC_VENC'] = '<span class="text-warning">'.$post->FEC_VENC.'</span>';
                } else {
                	$nestedData['FEC_VENC'] = $post->FEC_VENC;
                }

                $nestedData['FECALTAS'] = $post->CREACION;
                $nestedData['NRO_CUOTA'] = $post->NRO_CUOTA;
                $nestedData['INICIAL'] = Common::precio_candec($post->INICIAL, $post->MONEDA);
                //$nestedData['ACTUAL'] = Common::precio_candec($post->ACTUAL, $post->MONEDA);

                $nestedData['ACTUAL'] = Common::precio_candec(($post->INICIAL - $post->PAGADO), $post->MONEDA);

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

	public static function datos_nota($codigo){

		/*  --------------------------------------------------------------------------------- */

		// OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

		// INICIAR VARIABLES 

		$codigo = $codigo["codigo"];

		/*  --------------------------------------------------------------------------------- */

		$deuda = Deuda::select(DB::raw('COMPRAS.ID AS COMPRA_ID, COMPRAS.NRO_FACTURA, SUBSTRING(COMPRAS.FEC_FACTURA, 1, 11) AS FEC_FACTURA, COMPRAS.TOTAL AS TOTAL_COMPRA, COMPRAS.MONEDA, MONEDAS.DESCRIPCION_LARGA AS MONEDA_DESCRIPCION, PROVEEDORES.NOMBRE, SUBSTRING(DEUDAS.FEC_VENC, 1, 11) AS FEC_VENC, DEUDAS.PLAN_PAGO, 0 AS NRO_CUOTA, SUM(PAGOS_PROV_DET.PAGO) AS PAGADO, SUM(DEUDAS.ESTATUS) AS ESTATUS, MONEDAS.CANDEC'))
            			 ->leftjoin('COMPRAS', 'COMPRAS.ID', '=', 'DEUDAS.FK_COMPRA')
                         ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'COMPRAS.MONEDA')
                         ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'COMPRAS.PROVEEDOR')
                         ->leftjoin('PAGOS_PROV_DET', 'PAGOS_PROV_DET.FK_DEUDA', '=', 'DEUDAS.ID')
                         ->where('DEUDAS.ID_SUCURSAL','=', $user->id_sucursal)
                         ->where('COMPRAS.CODIGO','=', $codigo)
                         ->groupBy('COMPRAS.CODIGO')
                         ->get();

        /*  --------------------------------------------------------------------------------- */

		// PERSONALIZAR 
        
		foreach ($deuda as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            $conteo = Deuda::where('DEUDAS.ID_SUCURSAL','=', $user->id_sucursal)
                         ->where('DEUDAS.FK_COMPRA','=', $deuda[$key]['COMPRA_ID'])
                         ->count();

            $deuda[$key]['NRO_CUOTA'] = $conteo;

            /*  --------------------------------------------------------------------------------- */

            // REVISAR PAGO 
            
            if ((int)($deuda[$key]['NRO_CUOTA'] * 3) === (int)$deuda[$key]['ESTATUS']) {
                $deuda[$key]['ESTATUS'] = '<span class="badge badge-success">Pagado</span>';
            } else if ((int)$deuda[$key]['NRO_CUOTA'] === (int)$deuda[$key]['ESTATUS']){
                $deuda[$key]['ESTATUS'] = '<span class="badge badge-warning">Pendiente</span>';
            } else if ($deuda[$key]['ESTATUS'] > $deuda[$key]['NRO_CUOTA']) {
                $deuda[$key]['ESTATUS'] = '<span class="badge badge-info">Parcial</span>';
            }

            /*  --------------------------------------------------------------------------------- */

			$deuda[$key]['PLAN_PAGO'] = $value->PLAN_PAGO.' días';
            $deuda[$key]['TOTAL'] = Common::precio_candec(($value->TOTAL_COMPRA - $value->PAGADO), $value->MONEDA);
            $deuda[$key]['TOTAL_CRUDO'] = $value->TOTAL_COMPRA - $value->PAGADO;
			$deuda[$key]['TOTAL_COMPRA'] = Common::precio_candec($value->TOTAL_COMPRA, $value->MONEDA);
		}

		/*  --------------------------------------------------------------------------------- */

		if (count($deuda) > 0) {
			return ["response" => true, "deuda" => $deuda[0]];
		} else {
			return ["response" => false];
		}

		/*  --------------------------------------------------------------------------------- */

	}

	public static function deuda_compra_datatable($request) {

		/*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $codigo = $request->input('codigo');

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'CODIGO', 
                            1 => 'PROVEEDOR',
                            2 => 'FEC_VENC',
                            3 => 'PLAN_PAGO',
                            4 => 'NRO_CUOTA',
                            5 => 'TOTAL',
                            6 => 'ESTATUS',
                            7 => 'COMPRA'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = Deuda::where('DEUDAS.ID_SUCURSAL','=', $user->id_sucursal)
        			 ->leftjoin('COMPRAS', 'COMPRAS.ID', '=', 'DEUDAS.FK_COMPRA')
        			 ->where('COMPRAS.CODIGO','=', $codigo)
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

            $posts = Deuda::select(DB::raw('DEUDAS.CODIGO, COMPRAS.CODIGO AS COMPRA, COMPRAS.MONEDA, PROVEEDORES.NOMBRE, SUBSTRING(DEUDAS.FEC_VENC, 1, 11) AS FEC_VENC, DEUDAS.PLAN_PAGO, DEUDAS.NRO_CUOTA, DEUDAS.TOTAL, DEUDAS.ESTATUS'))
            			 ->leftjoin('COMPRAS', 'COMPRAS.ID', '=', 'DEUDAS.FK_COMPRA')
                         ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'COMPRAS.PROVEEDOR')
                         ->where('DEUDAS.ID_SUCURSAL','=', $user->id_sucursal)
                         ->where('COMPRAS.CODIGO','=', $codigo)
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

            $posts =  Deuda::select(DB::raw('DEUDAS.CODIGO, COMPRAS.CODIGO AS COMPRA, COMPRAS.MONEDA, PROVEEDORES.NOMBRE, SUBSTRING(DEUDAS.FEC_VENC, 1, 11) AS FEC_VENC, DEUDAS.PLAN_PAGO, DEUDAS.NRO_CUOTA, DEUDAS.TOTAL, DEUDAS.ESTATUS'))
             			 ->leftjoin('COMPRAS', 'COMPRAS.ID', '=', 'DEUDAS.FK_COMPRA')
                         ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'COMPRAS.PROVEEDOR')
                         ->where('DEUDAS.ID_SUCURSAL','=', $user->id_sucursal)
                         ->where('COMPRAS.CODIGO','=', $codigo)
                            ->where(function ($query) use ($search) {
                                $query->where('DEUDAS.CODIGO','LIKE',"{$search}%")
                                      ->orWhere('PROVEEDORES.NOMBRE', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = Deuda::leftjoin('COMPRAS', 'COMPRAS.ID', '=', 'DEUDAS.FK_COMPRA')
                         ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'COMPRAS.PROVEEDOR')
                         ->where('DEUDAS.ID_SUCURSAL','=', $user->id_sucursal)
                         ->where('COMPRAS.CODIGO','=', $codigo)
                            ->where(function ($query) use ($search) {
                                $query->where('DEUDAS.CODIGO','LIKE',"{$search}%")
                                      ->orWhere('PROVEEDORES.NOMBRE', 'LIKE',"%{$search}%");
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

                $nestedData['CODIGO'] = $post->CODIGO;
                $nestedData['COMPRA'] = $post->COMPRA;
                $nestedData['PROVEEDOR'] = $post->NOMBRE;

                /*  --------------------------------------------------------------------------------- */

                // TIPO DE COMPRA

                if ($post->ESTATUS === 1) {
                    $nestedData['ESTATUS'] = '<span class="badge badge-warning">Pendiente</span>';
                } else if ($post->ESTATUS === 2) {
                    $nestedData['ESTATUS'] = '<span class="badge badge-info">Parcial</span>';
                } else if ($post->ESTATUS === 3) {
                    $nestedData['ESTATUS'] = '<span class="badge badge-success">Pagado</span>';
                } else {
                	$nestedData['ESTATUS'] = 'N/A';
                }

                /*  --------------------------------------------------------------------------------- */

                $nestedData['PLAN_PAGO'] = $post->PLAN_PAGO;

                if ($dia >= $post->FEC_VENC) {
                	$nestedData['FEC_VENC'] = '<span class="text-danger">'.$post->FEC_VENC.'</span>';
                } else if (($post->FEC_VENC <= $cinco_dias_despues) && ($post->FEC_VENC > $dia)) {
                	$nestedData['FEC_VENC'] = '<span class="text-warning">'.$post->FEC_VENC.'</span>';
                } else {
                	$nestedData['FEC_VENC'] = $post->FEC_VENC;
                }
                
                $nestedData['NRO_CUOTA'] = $post->NRO_CUOTA;
                $nestedData['TOTAL'] = Common::precio_candec($post->TOTAL, $post->MONEDA);

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

    public static function obtener_deudas($codigo){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER DEUDAS

        $deudas = Deuda::select(DB::raw('DEUDAS.ID, DEUDAS.CODIGO, COMPRAS.CODIGO AS COMPRA, COMPRAS.MONEDA, PROVEEDORES.NOMBRE, SUBSTRING(DEUDAS.FEC_VENC, 1, 11) AS FEC_VENC, DEUDAS.PLAN_PAGO, DEUDAS.NRO_CUOTA, DEUDAS.TOTAL, DEUDAS.ESTATUS'))
                         ->leftJoin('COMPRAS', function($join){
                            $join->on('COMPRAS.ID', '=', 'DEUDAS.FK_COMPRA')
                                 ->on('COMPRAS.ID_SUCURSAL', '=', 'DEUDAS.ID_SUCURSAL');
                         })
                         ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'COMPRAS.PROVEEDOR')
                         ->where('DEUDAS.ID_SUCURSAL','=', $user->id_sucursal)
                         ->where('COMPRAS.CODIGO','=', $codigo)
                         ->where('DEUDAS.ESTATUS','<>', 3)
                         ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR 
        
        if (count($deudas) > 0) {
            return ["response" => true, "deudas" => $deudas];
        } else {
            return ["response" => false, "statusText" => "Ya no se encuentran deudas !"];
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function cambiar_estado($id, $estado){

        try {

            /*  --------------------------------------------------------------------------------- */

            // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

            $user = auth()->user();

            /*  --------------------------------------------------------------------------------- */

            // OBTENER DEUDAS
            
            $deudas = Deuda::
            where('DEUDAS.ID','=', $id)
            ->update([
                'ESTATUS' => $estado
            ]);


            /*  --------------------------------------------------------------------------------- */
            // INFO

            Log::info('Deuda Estatus: Éxito al cambiar.', ['DEUDA' => $id, 'ESTATUS' => $estado]);

            /*  --------------------------------------------------------------------------------- */

            // RETORNAR 

            return ["response" => true];
            
            /*  --------------------------------------------------------------------------------- */

        } catch (Exception $e) {

            /*  --------------------------------------------------------------------------------- */

            // ERROR 

            Log::error('Deuda Estatus: Error al cambiar.', ['DEUDA' => $id, 'ESTATUS' => $estado]);

            /*  --------------------------------------------------------------------------------- */

            // RETORNAR VALOR 
        
            return ["response" => false];

            /*  --------------------------------------------------------------------------------- */

        }
    }	
}
