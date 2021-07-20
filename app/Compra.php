<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Mpdf\Mpdf;
use App\ComprasDet;
use App\Lotes_tiene_ComprasDet;
use App\Deuda;
use App\Pagos_Prov_Det;
use App\CompraUser;
use Illuminate\Support\Facades\Log;
use App\Gondola;
use App\Container;
use App\Seccion;

class Compra extends Model
{
    protected $connection = 'retail';
	protected $table = 'compras';
	const CREATED_AT = 'FECALTAS';
	const UPDATED_AT = 'FECMODIF';

	public static function guardar_compra($data){

		/*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
        
    	/*  --------------------------------------------------------------------------------- */

    	// INIICAR VARIABLES 

    	$c = 0;
        $diaHora = date('Y-m-d H:i:s');
    	$dia = date('Y-m-d');
    	$hora = date("H:i:s");
    	$lote = '';

    	/*  --------------------------------------------------------------------------------- */

    	try {
    		

    		/*  --------------------------------------------------------------------------------- */

    		// OBTENER CANDEC 

    		$candec = (Parametro::candec($data->data["moneda"]))['CANDEC'];

    		/*  --------------------------------------------------------------------------------- */

    		// OBTENER ULTIMO NUMERO 

    		$codigo = Compra::ultimo_codigo();

    		/*  --------------------------------------------------------------------------------- */

    		DB::connection('retail')->beginTransaction();

    		$compra = new Compra();

    		$compra->CODIGO = $codigo;
    		$compra->PROVEEDOR = $data->data["proveedor"];
    		$compra->NRO_FACTURA = $data->data["nro_caja"];
    		$compra->FEC_FACTURA = $data->data["fecha"];
    		
    		/*  --------------------------------------------------------------------------------- */

    		// REVISAR PLAN DE PAGO 

    		$compra->TIPO = $data->data["tipo_compra"];

    		if ($data->data["tipo_compra"] === 'CO') {
    			$compra->PLAN_PAGO = 0;
                $compra->CUOTAS = 0;
    		} else if ($data->data["tipo_compra"] === 'CR') {
    			$compra->PLAN_PAGO = $data->data["credito"]["dias"]; 
                $compra->CUOTAS = $data->data["cuotas"];
    		} else if ($data->data["tipo_compra"] === 'CS') {
    			$compra->PLAN_PAGO = 0;
                $compra->CUOTAS = 0;
    		}
    		
    		/*  --------------------------------------------------------------------------------- */

    		$compra->TOTAL = Common::quitar_coma($data->data["total_compra"], $candec);
    		$compra->EXENTAS = Common::quitar_coma($data->data["exentas"], $candec);
    		$compra->MONEDA = $data->data["moneda"];
    		$compra->CANCELADO = 'NO';
    		$compra->ID_SUCURSAL = $user->id_sucursal;
    		$compra->PEDIDO = $data->data["nro_pedido"];
    		//$compra->FK_USER_CR = $user->id;
    		$compra->save();

            // VERIFICAR SI ES SISTEMA DE DEPOSITO

            if($data->data['sistema_deposito'] === true){

                // OBTENER ID COMPRA 

                $id_compra = Compra::select('ID')
                ->where('ID_SUCURSAL','=', $user->id_sucursal)
                ->where('CODIGO','=', $codigo)
                ->get();

                // OBTENER ID CONTAINER

                $id_container = Container::select('ID')
                ->where('ID_SUCURSAL','=', $user->id_sucursal)
                ->where('CODIGO','=', $data->data["codigoContainer"])
                ->get();

                // GUARDAR REFERENCIAS DE DEPOSITO

                $deposito = DB::connection('retail')
                ->table('COMPRAS_DEPOSITO')->insertGetId([
                    'FK_CONTAINER' => $id_container[0]->ID,
                    'FK_COMPRA' => $id_compra[0]->ID,
                    'FK_GONDOLA' => $data->data["gondola"],
                    'FK_SECCION' => $data->data["seccion"],
                    'FK_SECTOR' => $data->data["sector"],
                    'FK_PISO' => $data->data["piso"]
                ]);
            }   
    		
    		/*  --------------------------------------------------------------------------------- */

    		// RECORRER PRODUCTOS 

    		foreach ($data->data["productos"] as $key => $value) {

    			/*  --------------------------------------------------------------------------------- */
    		     /* log::error(["Cantidad"=>$value['CANTIDAD']]);*/
    			$compra_det = new ComprasDet();
    			
    			$c = $c + 1;
    			$compra_det->CODIGO = $codigo;
    			$compra_det->ITEM = $c;
    			$compra_det->COD_PROD = $value['CODIGO'];
    			$compra_det->DESCRIPCION = $value['DESCRIPCION'];
    			$compra_det->ETIQUETAS = 0;
    			$compra_det->CANTIDAD = Common::quitar_coma($value['CANTIDAD'],$candec);
    			$compra_det->COSTO = Common::quitar_coma($value['COSTO'], $candec);
    			$compra_det->COSTO_TOTAL = Common::quitar_coma($value['COSTO_TOTAL'], $candec);

    			/*  --------------------------------------------------------------------------------- */

    			// INSERTAR LOTE 

    			$lote = Stock::insetar_lote($value['CODIGO'], Common::quitar_coma($value['CANTIDAD'],$candec), Common::quitar_coma($value['COSTO'], $candec), 1, '', $value['VENCIMIENTO']);
    			$compra_det->LOTE = $lote["lote"];
                 Stock::actualizar_Stock_Minimo($value['CODIGO']);

    			/*  --------------------------------------------------------------------------------- */

    			$compra_det->PORCENTAJE = $value['PORCENTAJE'];
    			$compra_det->PREC_VENTA = Common::quitar_coma($value['PRECIO'], $candec);
    			$compra_det->PREMAYORISTA = Common::quitar_coma($value['MAYORISTA'], $candec);
    			$compra_det->USER = $user->name;
    			$compra_det->HORALTAS = $hora;
    			$compra_det->ID_SUCURSAL = $user->id_sucursal;

    			$compra_det->save();

    			/*  --------------------------------------------------------------------------------- */

    			// GUARDAR REFERENCIA 

    			Lotes_tiene_ComprasDet::guardar_referencia($compra_det->id, $lote["id"]);

    			/*  --------------------------------------------------------------------------------- */

    		}

    		DB::connection('retail')->commit();

    		/*  --------------------------------------------------------------------------------- */

    		// INSERTAR DEUDA 

    		if ($data->data["tipo_compra"] === 'CR') {
    			Deuda::insertar($data->data, $candec, $compra->id);
    		}

    		/*  --------------------------------------------------------------------------------- */

            // INSERTAR USER REFERENCIA

            CompraUser::guardar_referencia($user->id, 1, $compra->id, $diaHora);

            /*  --------------------------------------------------------------------------------- */

    		// RETORNAR VALOR 

    		return ["response" => true, "codigo" => $codigo];

    		/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

    		DB::connection('retail')->rollBack();

    		/*  --------------------------------------------------------------------------------- */

    		// RETORNAR VALOR 
    		
    		return ["response" => false];

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

		$codigo = Compra::select('CODIGO')
		->where(['ID_SUCURSAL' => $user->id_sucursal])
		->orderBy('CODIGO', 'desc')
		->first();

		/*  --------------------------------------------------------------------------------- */

		// RETORNAR VALOR 

		return $codigo['CODIGO'] + 1;

		/*  --------------------------------------------------------------------------------- */

	}

	public static function mostrarDatatable($request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'CODIGO', 
                            1 => 'PROVEEDOR',
                            2 => 'TIPO',
                            3 => 'NRO_FACTURA',
                            4 => 'FEC_FACTURA',
                            5 => 'TOTAL',
                            6 => 'ACCION',
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE COMPRAS ENCONTRADAS 

        $totalData = Compra::where('ID_SUCURSAL','=', $user->id_sucursal)
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

            $posts = Compra::select(DB::raw('COMPRAS.CODIGO, COMPRAS.MONEDA, PROVEEDORES.NOMBRE, COMPRAS.TIPO, IFNULL(COMPRAS.NRO_FACTURA,"NO POSEE") AS NRO_FACTURA, COMPRAS.FEC_FACTURA, COMPRAS.TOTAL'))
                         ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'COMPRAS.PROVEEDOR')
                         ->where('COMPRAS.ID_SUCURSAL','=', $user->id_sucursal)
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

            $posts =  Compra::select(DB::raw('COMPRAS.CODIGO, COMPRAS.MONEDA, PROVEEDORES.NOMBRE, COMPRAS.TIPO, COMPRAS.NRO_FACTURA AS NRO_FACTURA, COMPRAS.FEC_FACTURA, COMPRAS.TOTAL'))
                         ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'COMPRAS.PROVEEDOR')
                         ->where('COMPRAS.ID_SUCURSAL','=', $user->id_sucursal)
                            ->where(function ($query) use ($search) {
                                $query->where('COMPRAS.CODIGO','LIKE',"{$search}%")
                                    ->orWhere('COMPRAS.NRO_FACTURA', 'LIKE',"%{$search}%")
                                    ->orWhere('PROVEEDORES.NOMBRE', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = Compra::where('COMPRAS.ID_SUCURSAL','=', $user->id_sucursal)
            				->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'COMPRAS.PROVEEDOR')
                            ->where(function ($query) use ($search) {
                                $query->where('COMPRAS.CODIGO','LIKE',"{$search}%")
                                  ->orWhere('COMPRAS.NRO_FACTURA', 'LIKE',"%{$search}%")
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
                $nestedData['PROVEEDOR'] = $post->NOMBRE;

                /*  --------------------------------------------------------------------------------- */

                // TIPO DE COMPRA

                if ($post->TIPO === 'CO') {
                    $nestedData['TIPO'] = 'Contado';
                } else if ($post->TIPO === 'CR') {
                    $nestedData['TIPO'] = 'Credito';
                } else if ($post->TIPO === 'CS') {
                    $nestedData['TIPO'] = 'Consignacion';
                } else {
                	$nestedData['TIPO'] = 'N/A';
                }
                
                $deposito = (Parametro::mostrarParametro())["parametros"][0]->RACK;
                $sistema_deposito = '';
                $nestedData['NRO_FACTURA'] = $post->NRO_FACTURA;
                if($deposito == 'SI'){
                    if($post->NRO_FACTURA==="NO POSEE"){
                       $sistema_deposito = "&emsp;<a href='#' id='editarUbicacion' title='Editar Ubicación'><i class='fa fa-box-open text-dark' aria-hidden='true'></i></a>";
                    }else{
                       $sistema_deposito = "&emsp;<a href='#' id='editarUbicacion' title='Editar Ubicación'><i class='fa fa-box-open text-dark' aria-hidden='true'></i></a>&emsp;<a href='#' id='qr_caja' title='Imprimir Qr'><i class='fa fa-qrcode text-secondary' aria-hidden='true'></i></a>";
                    }
                }
                /*  --------------------------------------------------------------------------------- */

                
                $nestedData['FEC_FACTURA'] = substr($post->FEC_FACTURA, 0,10);
                $nestedData['TOTAL'] = Common::precio_candec($post->TOTAL, $post->MONEDA);

                
                $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrar' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a>&emsp;<a href='#' id='editar' title='Editar'><i class='fa fa-edit text-warning' aria-hidden='true'></i></a>&emsp;<a href='#' id='eliminar' title='Eliminar'><i class='fa fa-trash text-danger' aria-hidden='true'></i></a>
                    &emsp;<a href='#' id='reporte' title='Reporte'><i class='fa fa-file text-secondary' aria-hidden='true'></i></a>".$sistema_deposito;

                
                
               


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

    public static function verificar_modificacion($codigo) {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $codigo = $codigo["data"];

        /*  --------------------------------------------------------------------------------- */
        
        // OBTENER ID COMPRA 

        $id_compra = Compra::select('ID')
        ->where('ID_SUCURSAL','=', $user->id_sucursal)
        ->where('CODIGO','=', $codigo)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // VERIFICAR PAGOS

        /*  --------------------------------------------------------------------------------- */

        // COMPROBAR SI YA HAY PAGO
        
        $pagos = Pagos_Prov_Det::leftjoin('DEUDAS', 'DEUDAS.ID', '=', 'PAGOS_PROV_DET.FK_DEUDA')
        ->where('DEUDAS.FK_COMPRA','=', $id_compra[0]["ID"])
        ->count();

        if ($pagos > 0) {
            return ["response" => false, "statusText" => "Hay pagos a proveedores hechos con esta compra !"];
        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR TRUE SI NO SE PUEDE ELIMINAR 

        return ["response" => true, "statusText" => "Se puede eliminar", "ID_COMPRA" => $id_compra[0]["ID"]];

        /*  --------------------------------------------------------------------------------- */

    }

    public static function verificar_eliminacion($codigo) {

    	/*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $codigo = $codigo["data"];
        $modificado = false;

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS LOTES A ELIMINAR

		$compradet = ComprasDet::select(DB::raw(
                        'COMPRASDET.ID AS COMPRA_ID, LOTES.ID AS LOTE_ID, LOTES.COD_PROD'
                    ))
        ->rightJoin('LOTE_TIENE_COMPRASDET', 'LOTE_TIENE_COMPRASDET.ID_COMPRAS_DET', '=', 'COMPRASDET.ID')
        ->leftJoin('LOTES', 'LOTES.ID', '=', 'LOTE_TIENE_COMPRASDET.ID_LOTE')
        ->where('COMPRASDET.ID_SUCURSAL','=', $user->id_sucursal)
        ->where('COMPRASDET.CODIGO','=', $codigo)
        ->get();      	  

        /*  --------------------------------------------------------------------------------- */

        // SI NO ENCUENTRA REGISTROS EN LAS CLAVES FORANEAS PROHIBIR ELIMINAR 

        if(count($compradet) <= 0) {
        	return ["response" => false, "statusText" => "¡Esta compra no se podrá eliminar o modificar a través de esta plataforma!"];
        }

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LOTE HA SIDO MODIFICADO 

        foreach ($compradet as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            // LOTE USER - REVISAR SI EL ESTA EDITADO

            $lote_user = LoteUser::select(DB::raw(
                            'ID'
                        ))
            ->where('FK_LOTE','=', $value->LOTE_ID)
            ->where('ACCION','=', 2)
            ->get();  

            /*  --------------------------------------------------------------------------------- */

            // SI ENCONTRAMOS LOTES EDITADOS DETENER EL FOREACH

            if(count($lote_user) > 0) {
                $modificado = true;
                break;
            }

            /*  --------------------------------------------------------------------------------- */
        }

        /*  --------------------------------------------------------------------------------- */

    
        // SI ES TRUE MODIFICADO SE RECHAZA LA ELIMINACION
        
        if($modificado === true) {
        	return ["response" => false, "statusText" => "Esta compra tiene lotes ya modificados"];
        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR TRUE SI NO SE PUEDE ELIMINAR 

        return ["response" => true, "statusText" => "Se puede eliminar", "data" => $compradet];

        /*  --------------------------------------------------------------------------------- */

    }

   	public static function eliminar($codigo) 
    {
    	
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
        $diaHora = date('Y-m-d H:i:s');
        $id_compra = 0;

        /*  --------------------------------------------------------------------------------- */

        // VERIFICAR ELIMINACION 

        $verificacion = Compra::verificar_eliminacion($codigo);
        
        /*  --------------------------------------------------------------------------------- */

        if ($verificacion["response"] === false) {
        	return $verificacion;
        } else {
        	$verificacion = $verificacion["data"];
        }

        /*  --------------------------------------------------------------------------------- */

        // ELIMINAR TODA LAS REFERENCIAS

        foreach ($verificacion as $key => $value) {

        	/*  --------------------------------------------------------------------------------- */

        	// ELIMINAR REFERENCIA 

        	Lotes_tiene_ComprasDet::where('ID_COMPRAS_DET','=', $value->COMPRA_ID)
        	->delete();

        	/*  --------------------------------------------------------------------------------- */

        	// ELIMINAR LOTE 

        	Stock::eliminar_lote_por_id($value->LOTE_ID);

        	/*  --------------------------------------------------------------------------------- */

        }
        
        /*  --------------------------------------------------------------------------------- */

        // ELIMINAR COMPRAS DET 

        ComprasDet::where('ID_SUCURSAL','=', $user->id_sucursal)
        ->where('CODIGO','=', $codigo["data"])
        ->delete();

        /*  --------------------------------------------------------------------------------- */
        // OBTENER ID COMPRA 

        $id_compra = Compra::select('ID')
        ->where('ID_SUCURSAL','=', $user->id_sucursal)
        ->where('CODIGO','=', $codigo["data"])
        ->get();

        /*  --------------------------------------------------------------------------------- */
        // ELIMINAR COMPRAS DEPOSITO 

        DB::connection('retail')
        ->table('COMPRAS_DEPOSITO')
        ->where('FK_COMPRA','=', $id_compra[0]->ID)
        ->delete();

        /*  --------------------------------------------------------------------------------- */

        // ELIMINAR TODA LA COMPRA
        // SI LA OPCION ES 1 ELIMINAR LA COMPRA, SINO SOLO ELIMINAR COMPRASDET

        if ($codigo["opcion"] === 1) {
            
            /*  --------------------------------------------------------------------------------- */

            // INSERTAR USER REFERENCIA

            CompraUser::guardar_referencia($user->id, 3, $id_compra[0]["ID"], $diaHora);

            /*  --------------------------------------------------------------------------------- */

            // REVISAR SI HAY DEUDA

        	Compra::where('ID_SUCURSAL','=', $user->id_sucursal)
        	->where('CODIGO','=', $codigo["data"])
        	->delete();

            /*  --------------------------------------------------------------------------------- */
        }
        
        /*  --------------------------------------------------------------------------------- */

        // ELIMINAR DEUDAS 

        Deuda::where('FK_COMPRA','=', $id_compra[0]->ID)
            ->delete();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return ["response" => true];

        /*  --------------------------------------------------------------------------------- */
    }

    public static function mostrar_productos($request) {

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $codigo = $request->input('codigoCompra');

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'ITEM', 
                            1 => 'COD_PROD',
                            2 => 'DESCRIPCION',
                            3 => 'CANTIDAD',
                            4 => 'COSTO',
                            5 => 'COSTO_TOTAL',
                            6 => 'LOTE',
                            7 => 'PRECIO'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE PRODUCTOS ENCONTRADOS 

        $totalData = Comprasdet::leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'COMPRASDET.COD_PROD')
                    ->where('COMPRASDET.CODIGO','=', $codigo)
                    ->where('COMPRASDET.ID_SUCURSAL','=', $user->id_sucursal)
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

            $posts = ComprasDet::select(DB::raw('COMPRASDET.ITEM, COMPRASDET.COD_PROD, PRODUCTOS.DESCRIPCION, COMPRASDET.CANTIDAD, COMPRASDET.PREC_VENTA AS PRECIO, COMPRASDET.COSTO, COMPRASDET.COSTO_TOTAL, COMPRASDET.LOTE, COMPRAS.MONEDA'))
            ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'COMPRASDET.COD_PROD')
            ->leftJoin('COMPRAS', function($join){
			    $join->on('COMPRAS.CODIGO', '=', 'COMPRASDET.CODIGO')
			         ->on('COMPRAS.ID_SUCURSAL', '=', 'COMPRASDET.ID_SUCURSAL');
			})
            ->where('COMPRASDET.ID_SUCURSAL','=', $user->id_sucursal)
            ->where('COMPRASDET.CODIGO','=', $codigo)
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

            $posts =  ComprasDet::select(DB::raw('COMPRASDET.ITEM, COMPRASDET.COD_PROD, PRODUCTOS.DESCRIPCION, COMPRASDET.CANTIDAD, COMPRASDET.PREC_VENTA AS PRECIO, COMPRASDET.COSTO, COMPRASDET.COSTO_TOTAL, COMPRASDET.LOTE, COMPRAS.CODIGO'))
            ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'COMPRASDET.COD_PROD')
            ->leftJoin('COMPRAS', function($join){
			    $join->on('COMPRAS.CODIGO', '=', 'COMPRASDET.CODIGO')
			         ->on('COMPRAS.ID_SUCURSAL', '=', 'COMPRASDET.ID_SUCURSAL');
			})
            ->where('COMPRASDET.ID_SUCURSAL','=', $user->id_sucursal)
            ->where('COMPRASDET.CODIGO','=', $codigo)
            ->where(function ($query) use ($search) {
                $query->where('COMPRASDET.COD_PROD','LIKE',"{$search}%")
                ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"%{$search}%");
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = ComprasDet::leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'COMPRASDET.COD_PROD')
            ->where('COMPRASDET.ID_SUCURSAL','=', $user->id_sucursal)
            ->where('COMPRASDET.CODIGO','=', $codigo)
            ->where(function ($query) use ($search) {
                $query->where('COMPRASDET.COD_PROD','LIKE',"{$search}%")
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

                $nestedData['ITEM'] = $post->ITEM;
                $nestedData['COD_PROD'] = $post->COD_PROD;
                $nestedData['DESCRIPCION'] = $post->DESCRIPCION;
                $nestedData['CANTIDAD'] = $post->CANTIDAD;
                $nestedData['COSTO'] = Common::precio_candec($post->COSTO, $post->MONEDA);
                $nestedData['COSTO_TOTAL'] = Common::precio_candec($post->COSTO_TOTAL, $post->MONEDA);
                $nestedData['LOTE'] = $post->LOTE;
                $nestedData['PRECIO'] = Common::precio_candec($post->PRECIO, $post->MONEDA);

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

    public static function mostrar_cabecera($data)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
         
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $codigo = $data['codigo'];

        /*  --------------------------------------------------------------------------------- */

        $compra = Compra::select(DB::raw(
                        'COMPRAS.ID,
                        COMPRAS.CODIGO,
                        PROVEEDORES.NOMBRE,
                        SUBSTRING(COMPRAS.FEC_FACTURA, 1, 10) AS FEC_FACTURA,
                        COMPRAS.FECALTAS,
                        COMPRAS.NRO_FACTURA,
                        COMPRAS.TIPO,
                        COMPRAS.PLAN_PAGO,
                        COMPRAS.CUOTAS,
                        COMPRAS.MONEDA,
                        COMPRAS.PROVEEDOR,
                        COMPRAS.TOTAL,
                        COMPRAS.PEDIDO AS NRO_PEDIDO,
                        DEUDAS.TIPO AS DEUDA_TIPO,
                        COMPRAS.EXENTAS'
                    ))
        ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'COMPRAS.PROVEEDOR')
        ->leftjoin('DEUDAS', 'DEUDAS.FK_COMPRA', '=', 'COMPRAS.ID')
        ->where('COMPRAS.ID_SUCURSAL','=', $user->id_sucursal)
        ->where('COMPRAS.CODIGO','=', $codigo)
        ->get();

        $deposito = (Parametro::mostrarParametro())["parametros"][0]->RACK;

        $compra[0]['SISTEMA_DEPOSITO'] = false;

        if($deposito == 'SI'){

            $compras_deposito = DB::connection('retail')
                ->table('COMPRAS_DEPOSITO')
                ->select(DB::raw('CONTAINERS.CODIGO,
                    CONTAINERS.DESCRIPCION,
                    COMPRAS_DEPOSITO.FK_SECCION AS ID_SECCION,
                    COMPRAS_DEPOSITO.FK_PISO AS PISO,
                    COMPRAS_DEPOSITO.FK_SECTOR AS SECTOR,
                    COMPRAS_DEPOSITO.FK_GONDOLA AS GONDOLA,
                    SECCIONES.DESCRIPCION AS DESC_SECCION,
                    PISOS.NRO_PISO AS DESC_PISO,
                    SECTORES.DESCRIPCION AS DESC_SECTOR,
                    GONDOLAS.DESCRIPCION AS DESC_GONDOLA
                    '))
                ->leftjoin('CONTAINERS','CONTAINERS.ID','=','COMPRAS_DEPOSITO.FK_CONTAINER')
                ->leftjoin('PISOS','PISOS.ID','=','COMPRAS_DEPOSITO.FK_PISO')
                ->leftjoin('SECTORES','SECTORES.ID','=','COMPRAS_DEPOSITO.FK_SECTOR')
                ->leftjoin('GONDOLAS','GONDOLAS.ID','=','COMPRAS_DEPOSITO.FK_GONDOLA')
                ->leftjoin('SECCIONES','SECCIONES.ID','=','COMPRAS_DEPOSITO.FK_SECCION')
                ->where('COMPRAS_DEPOSITO.FK_COMPRA','=', $compra[0]["ID"])
                ->get();

            $secciones = Seccion::select(DB::raw('IFNULL(ID, 0) AS ID_SECCION,
                    IFNULL(DESCRIPCION, 0) AS DESCRIPCION,
                    IFNULL(SECCIONES.DESC_CORTA, 0) AS DESC_CORTA'))
                ->where('ID_SUCURSAL','=',$user->id_sucursal)
                ->orderBy('DESCRIPCION', 'ASC')
                ->get()
                ->toArray();

            $compra[0]['SECCIONES'] = $secciones;
            if(count($compras_deposito)>0){

                $pisos = Gondola_Tiene_Piso::select(DB::raw('PISOS.ID,
                        PISOS.NRO_PISO'))
                    ->leftjoin('PISOS','PISOS.ID','=','GONDOLA_TIENE_PISOS.FK_PISO')
                    ->where('GONDOLA_TIENE_PISOS.FK_GONDOLA','=',$compras_deposito[0]->GONDOLA)
                    ->get()
                    ->toArray();

                $sectores = Gondola_Tiene_Sector::select(DB::raw('SECTORES.ID,
                    SECTORES.DESCRIPCION'))
                    ->leftjoin('SECTORES','SECTORES.ID','=','GONDOLA_TIENE_SECTORES.FK_SECTOR')
                    ->where('GONDOLA_TIENE_SECTORES.FK_GONDOLA','=',$compras_deposito[0]->GONDOLA)
                    ->get()
                    ->toArray();

                $compra[0]['SISTEMA_DEPOSITO'] = true;
                $compra[0]['SECTORES'] = $sectores;
                $compra[0]['PISOS'] = $pisos;
                $compra[0]["DATOS_DEPOSITO"] = $compras_deposito[0];
            }else{
                $compra[0]['SISTEMA_DEPOSITO'] = false;
            }
        }

        /*  --------------------------------------------------------------------------------- */

        // REVISAR DEUDAS TIPO ESTA NULL PARA DEVOLVER EL VALOR 1 EN LA OPCION SI ES QUE EL USUARIO 
        // HABILITA LA VENTA A CREDITO 

        if ($compra[0]["DEUDA_TIPO"] === null) {
            $compra[0]["DEUDA_TIPO"] = 1;
        }

        /*  --------------------------------------------------------------------------------- */

        return $compra[0];

    }

    public static function mostrar_cuerpo($data)
    {
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $codigo = $data['codigo'];

        /*  --------------------------------------------------------------------------------- */

        $compra = ComprasDet::select(DB::raw(
                        'COMPRASDET.ITEM, 
                        COMPRASDET.COD_PROD, 
                        COMPRASDET.DESCRIPCION,
                        COMPRASDET.LOTE,
                        COMPRASDET.PORCENTAJE, 
                        COMPRASDET.CANTIDAD, 
                        COMPRASDET.COSTO,
                        COMPRASDET.COSTO_TOTAL,
                        COMPRASDET.PREC_VENTA,
                        COMPRASDET.PREMAYORISTA,
                        COMPRAS.MONEDA,
                        SUBSTRING(LOTES.FECHA_VENC, 1, 10) AS VENCIMIENTO'
                    ))
        ->leftJoin('COMPRAS', function($join){
			    $join->on('COMPRAS.CODIGO', '=', 'COMPRASDET.CODIGO')
			         ->on('COMPRAS.ID_SUCURSAL', '=', 'COMPRASDET.ID_SUCURSAL');
		})
        ->leftjoin('LOTE_TIENE_COMPRASDET', 'LOTE_TIENE_COMPRASDET.ID_COMPRAS_DET', '=', 'COMPRASDET.ID')
        ->leftjoin('LOTES', 'LOTES.ID', '=', 'LOTE_TIENE_COMPRASDET.ID_LOTE')
        ->where('COMPRASDET.ID_SUCURSAL','=', $user->id_sucursal)
        ->where('COMPRASDET.CODIGO','=', $codigo)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        foreach ($compra as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            // VER PRECIOS DE PRODUCTOS 

            if ($value->VENCIMIENTO === '0000-00-00') {
                $value->VENCIMIENTO = 'N/A';
            }

            /*  --------------------------------------------------------------------------------- */

            $compra[$key]->CANTIDAD = Common::precio_candec_sin_letra($value->CANTIDAD, 1);
            $compra[$key]->PREC_VENTA = Common::precio_candec_sin_letra($value->PREC_VENTA, $value->MONEDA);
            $compra[$key]->PREMAYORISTA = Common::precio_candec_sin_letra($value->PREMAYORISTA, $value->MONEDA);
            $compra[$key]->COSTO = Common::precio_candec_sin_letra($value->COSTO, $value->MONEDA);
            $compra[$key]->COSTO_TOTAL = Common::precio_candec_sin_letra($value->COSTO_TOTAL, $value->MONEDA);

            /*  --------------------------------------------------------------------------------- */

        }

        return $compra;

        /*  --------------------------------------------------------------------------------- */

    }

    public static function pdf_compra($dato)
    {
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
         
        /*  --------------------------------------------------------------------------------- */

        // OBTENER DATOS DE CABECERA 

        $compra = Compra::mostrar_cabecera($dato);

        /*  --------------------------------------------------------------------------------- */

        // OBTENER DATOS DETALLE 

        $compra_det = Compra::mostrar_cuerpo($dato);

        /*  --------------------------------------------------------------------------------- */

        // PARAMETRO CANDEC 

        $candec = Parametro::candec($compra->MONEDA);

        /*  --------------------------------------------------------------------------------- */

        // OBTENER SUCURSAL

        $sucursal = Sucursal::encontrarSucursal(['codigoOrigen' => $user->id_sucursal]);
        
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $c = 0;
        $codigo = $compra->CODIGO;
        $proveedor = $compra->NOMBRE;
        $fec_factura = $compra->FEC_FACTURA;
        $fecaltas = $compra->FECALTAS;
        $nro_factura = $compra->NRO_FACTURA;
        $tipo = '';
        $plan_pago = $compra->PLAN_PAGO;

        /*  --------------------------------------------------------------------------------- */

        // PLAN PAGO 

        if ($compra->TIPO === 'CO') {
        	$tipo = 'CONTADO';
        } else if ($compra->TIPO === 'CR') {
        	$tipo = 'CREDITO';
        } else if ($compra->TIPO === 'CS') {
        	$tipo = 'CONSIGNACION';
        }

        /*  --------------------------------------------------------------------------------- */

        $nombre = 'Compra_'.$codigo.'_'.time().'';
        $articulos = [];
        $cantidad = 0;
        $total = 0;
        $nombre_sucursal = $sucursal['sucursal'][0]['DESCRIPCION'];
        $direccion = $sucursal['sucursal'][0]['DIRECCION'];

        /*  --------------------------------------------------------------------------------- */

        // CARGAR DETALLE DE COMPRAS DET 

        foreach ($compra_det as $key => $value) {
            $articulos[$key]["cantidad"] = $value->CANTIDAD;
            $articulos[$key]["cod_prod"] = $value->COD_PROD;
            $articulos[$key]["descripcion"] = $value->DESCRIPCION;
            $articulos[$key]["precio"] = $value->PREC_VENTA;
            $articulos[$key]["premayorista"] = $value->PREMAYORISTA;
            $articulos[$key]["lote"] = $value->LOTE;
            $articulos[$key]["costo"] = $value->COSTO;
            $articulos[$key]["costo_total"] = $value->COSTO_TOTAL;
            $cantidad = $cantidad + $value->CANTIDAD;
            $total = $total + Common::quitar_coma($value->COSTO_TOTAL, $candec['CANDEC']);
            $c = $c + 1;
        }

        /*  --------------------------------------------------------------------------------- */

        // CARGAR VARIABLES 
        
        $data['codigo'] = $codigo;
        $data['proveedor'] = $proveedor;
        $data['fec_factura'] = $fec_factura;
        $data['fecaltas'] = $fecaltas;
        $data['nro_factura'] = $nro_factura;
        $data['tipo'] = $tipo;
        $data['plan_pago'] = $plan_pago;
        $data['nombre'] = $nombre;
        $data['articulos'] = $articulos;
        $data['c'] = $c;
        $data['cantidad'] = $cantidad;
        $data['total'] = Common::precio_candec($total, $compra->MONEDA);
        $data['sucursal'] = $nombre_sucursal;
        $data['direccion'] = $direccion;
        $data['logo']=(Imagen::obtenerLogoDireccion())['imagen'];

        /*  --------------------------------------------------------------------------------- */
        
        $html = view('pdf.rptCompra',$data)->render();
        
        /*  --------------------------------------------------------------------------------- */

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
             "format" => "A4",
        ]);
        
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->WriteHTML($html);

        /*  --------------------------------------------------------------------------------- */

        // GENERAR ARCHIVO 

        $mpdf->Output($nombre,"I");
        
        /*  --------------------------------------------------------------------------------- */

    }

    public static function modificar_compra($data){

		/*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
        
    	/*  --------------------------------------------------------------------------------- */

    	// INIICAR VARIABLES 

    	$c = 0;
        $diaHora = date('Y-m-d H:i:s');
    	$dia = date('Y-m-d');
    	$hora = date("H:i:s");
    	$lote = '';
    	$plan_pago = 0;
        $cuotas = 0;
    	$codigo = $data->data["codigo"];

        /*  --------------------------------------------------------------------------------- */

        // VERIFICAR ELIMINACION 

    	/*  --------------------------------------------------------------------------------- */

    	try {
    		

            DB::connection('retail')->beginTransaction();
            
    		/*  --------------------------------------------------------------------------------- */

    		// OBTENER CANDEC 

    		$candec = (Parametro::candec($data->data["moneda"]))['CANDEC'];

    		/*  --------------------------------------------------------------------------------- */

    		// PLAN PAGO 

    		if ($data->data["tipo_compra"] === 'CO') {
    			$plan_pago = 0;
    		} else if ($data->data["tipo_compra"] === 'CR') {
                $cuotas = $data->data["cuotas"];
                $plan_pago = $data->data["credito"]["dias"];
    		} else if ($data->data["tipo_compra"] === 'CS') {
    			$plan_pago = 0;
    		}

            /*  --------------------------------------------------------------------------------- */

            // VERIFICAR MODIFICACION 

            $verificacion = Compra::verificar_modificacion(["data" => $codigo]);

    		/*  --------------------------------------------------------------------------------- */

            if ($verificacion["response"] === false) {
                return $verificacion;
            } else {
                $verificacion = $verificacion["ID_COMPRA"];
            }

            /*  --------------------------------------------------------------------------------- */

    		Compra::
    		where([
    			['CODIGO', '=', $codigo],
    			['ID_SUCURSAL', '=', $user->id_sucursal]
    		])
    		->update([
    			'PROVEEDOR' => $data->data["proveedor"],
    			'NRO_FACTURA' => $data->data["nro_caja"],
    			'FEC_FACTURA' => $data->data["fecha"],
    			'PLAN_PAGO' => $plan_pago,
    			'TIPO' => $data->data["tipo_compra"],
    			'TOTAL' => Common::quitar_coma($data->data["total_compra"], $candec),
    			'EXENTAS' => Common::quitar_coma($data->data["exentas"], $candec),
    			'MONEDA' => $data->data["moneda"],
    			'CANCELADO' => 'NO',
                'CUOTAS' => $cuotas,
    			'PEDIDO' => $data->data["nro_pedido"]
    		]);
    		
    		/*  --------------------------------------------------------------------------------- */

    		// ELIMINAR PRODUCTOS COMPRAS DET 
    		// ENVIAR OPCION 2 PARA EVITAR LA ELIMINACION DEL REGISTRO COMPRA 

    		$eliminar = Compra::eliminar(["data" => $codigo, "opcion" => 2]);

    		if ($eliminar["response"] === false) {
    			return $eliminar;
    		}

            // VERIFICAR SI ES SISTEMA DE DEPOSITO

            if($data->data['sistema_deposito'] === true){

                // OBTENER ID COMPRA 

                $id_compra = Compra::select('ID')
                ->where('ID_SUCURSAL','=', $user->id_sucursal)
                ->where('CODIGO','=', $codigo)
                ->get();

                // OBTENER ID CONTAINER

                $id_container = Container::select('ID')
                ->where('ID_SUCURSAL','=', $user->id_sucursal)
                ->where('CODIGO','=', $data->data["codigoContainer"])
                ->get();

                // GUARDAR REFERENCIAS DE DEPOSITO

                $deposito = DB::connection('retail')
                ->table('COMPRAS_DEPOSITO')->insertGetId([
                    'FK_CONTAINER' => $id_container[0]->ID,
                    'FK_COMPRA' => $id_compra[0]->ID,
                    'FK_GONDOLA' => $data->data["gondola"],
                    'FK_SECCION' => $data->data["seccion"],
                    'FK_SECTOR' => $data->data["sector"],
                    'FK_PISO' => $data->data["piso"]
                ]);

            } 

    		/*  --------------------------------------------------------------------------------- */

    		// RECORRER PRODUCTOS 

    		foreach ($data->data["productos"] as $key => $value) {

    			/*  --------------------------------------------------------------------------------- */
    			
    			$compra_det = new ComprasDet();
    			
    			$c = $c + 1;
    			$compra_det->CODIGO = $codigo;
    			$compra_det->ITEM = $c;
    			$compra_det->COD_PROD = $value['CODIGO'];
    			$compra_det->DESCRIPCION = $value['DESCRIPCION'];
    			$compra_det->ETIQUETAS = 0;
    			$compra_det->CANTIDAD =  Common::quitar_coma($value['CANTIDAD'],$candec);
    			$compra_det->COSTO = Common::quitar_coma($value['COSTO'], $candec);
    			$compra_det->COSTO_TOTAL = Common::quitar_coma($value['COSTO_TOTAL'], $candec);

    			/*  --------------------------------------------------------------------------------- */

    			// INSERTAR LOTE 

    			$lote = Stock::insetar_lote($value['CODIGO'], Common::quitar_coma($value['CANTIDAD'],$candec), Common::quitar_coma($value['COSTO'], $candec), 1, '', $value['VENCIMIENTO']);
    			$compra_det->LOTE = $lote["lote"];

    			/*  --------------------------------------------------------------------------------- */

    			$compra_det->PORCENTAJE = $value['PORCENTAJE'];
    			$compra_det->PREC_VENTA = Common::quitar_coma($value['PRECIO'], $candec);
    			$compra_det->PREMAYORISTA = Common::quitar_coma($value['MAYORISTA'], $candec);
    			$compra_det->USER = $user->name;
    			$compra_det->HORALTAS = $hora;
    			$compra_det->ID_SUCURSAL = $user->id_sucursal;

    			$compra_det->save();

    			/*  --------------------------------------------------------------------------------- */

    			// GUARDAR REFERENCIA 

    			Lotes_tiene_ComprasDet::guardar_referencia($compra_det->id, $lote["id"]);

    			/*  --------------------------------------------------------------------------------- */

    		}
    		
    		DB::connection('retail')->commit();

    		/*  --------------------------------------------------------------------------------- */

            $id = Compra::select(DB::raw('ID'))
            ->where('CODIGO','=', $codigo)
            ->where('ID_SUCURSAL','=',$user->id_sucursal)
            ->get();

            /*  --------------------------------------------------------------------------------- */

    	    // INSERTAR USER REFERENCIA

            CompraUser::guardar_referencia($user->id, 2, $id[0]->ID, $diaHora);

            /*  --------------------------------------------------------------------------------- */

            // INSERTAR DEUDA 

            if ($data->data["tipo_compra"] === 'CR') {
                Deuda::insertar($data->data, $candec, $verificacion);
            }

            /*  --------------------------------------------------------------------------------- */


    		// RETORNAR VALOR 

    		return ["response" => true, "codigo" => $codigo];


    		/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

    		DB::connection('retail')->rollBack();

    		/*  --------------------------------------------------------------------------------- */

    		// RETORNAR VALOR 
    		
    		return ["response" => false];

    		/*  --------------------------------------------------------------------------------- */
    	}

    	/*  --------------------------------------------------------------------------------- */

	}

	public static function guardar_modificar_compra($data){

		/*  --------------------------------------------------------------------------------- */

		// DE ACUERDO A LA OPCION QUE VENGA DESDE EL CLIENTE 

		if ($data->data["guardar"] === true) {
			return Compra::guardar_compra($data);
		} else {
			return Compra::modificar_compra($data);
		}

		/*  --------------------------------------------------------------------------------- */

	}  
          public static function CompraCajaQr($data){

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $sucursal = $data["data"]["sucursal"];
        $codigo_ca = $data["data"]["codigo_ca"];
        //ENCABEZADO /CAJA/RACK/PISO
        $encabezado= Compra::Select(DB::raw(
            'COMPRAS.NRO_FACTURA AS NUMERO_CAJA,
            GONDOLAS.DESCRIPCION AS RACK,
            PISOS.NRO_PISO AS PISO,
            CONTAINERS.DESCRIPCION AS CONTAINER,
            SECTORES.DESCRIPCION AS SECTOR'))
        ->leftjoin('COMPRAS_DEPOSITO','COMPRAS_DEPOSITO.FK_COMPRA','=','COMPRAS.ID')
        ->leftjoin('GONDOLAS','GONDOLAS.ID','=','COMPRAS_DEPOSITO.FK_GONDOLA')
        ->leftjoin('PISOS','PISOS.ID','=','COMPRAS_DEPOSITO.FK_PISO')
        ->leftjoin('SECTORES','SECTORES.ID','=','COMPRAS_DEPOSITO.FK_SECTOR')
        ->leftjoin('CONTAINERS','CONTAINERS.ID','=','COMPRAS_DEPOSITO.FK_CONTAINER')
        ->where('COMPRAS.NRO_FACTURA','=',$codigo_ca)->where('COMPRAS.ID_SUCURSAL','=',$sucursal)->get();
       /* $data_encabezado=array();
        if(!empty($encabezado)){
            foreach ($encabezado as $post) {
                # code...
                $nestedDataEncabezado["RACK"]=$post->RACK;
                $nestedDataEncabezado["PISO"]=$post->PISO;
                $data_encabezado[]=$nestedDataEncabezado;
            }
        }*/

        /*  --------------------------------------------------------------------------------- */

        // PRODUCTOS CON OFERTA 
        
        $posts = Compra::select(DB::raw('COMPRASDET.COD_PROD, 
            PRODUCTOS.DESCRIPCION, 
            LOTES.LOTE, 
            LOTES.CANTIDAD_INICIAL AS STOCK_INICIAL, 
            LOTES.CANTIDAD AS STOCK_LOTE, 
            LOTES.COSTO, 
            SUBSTR(LOTES.FECHA_VENC,1,11) AS FECHA_VENC,PRODUCTOS_AUX.MONEDA'))
            ->leftJoin('COMPRASDET', function($join){
                $join->on('COMPRASDET.CODIGO', '=', 'COMPRAS.CODIGO')
                     ->on('COMPRASDET.ID_SUCURSAL', '=', 'COMPRAS.ID_SUCURSAL');
            })
            ->leftJoin('PRODUCTOS_AUX', function($join){
                $join->on('PRODUCTOS_AUX.CODIGO', '=', 'COMPRASDET.COD_PROD')
                     ->on('PRODUCTOS_AUX.ID_SUCURSAL', '=', 'COMPRAS.ID_SUCURSAL');
            })
          /*  ->leftjoin('MONEDAS','MONEDAS.CODIGO','=','PRODUCTOS_AUX.MONEDA')*/
            ->leftjoin('lote_tiene_comprasdet','lote_tiene_comprasdet.ID_COMPRAS_DET','=','COMPRASDET.ID')
            ->leftjoin('LOTES','LOTES.ID','=','lote_tiene_comprasdet.ID_LOTE')
            ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'COMPRASDET.COD_PROD')          
            ->where('COMPRAS.ID_SUCURSAL','=', $sucursal)
            ->where('COMPRAS.NRO_FACTURA', '=' , $codigo_ca)
            ->orderBy('LOTES.CANTIDAD','DESC')
            ->get();
                         
        /*  --------------------------------------------------------------------------------- */

        // CONVERT IMAGE DEFAULT TO BLOB 
        
        $data = array();

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts))
        {
            foreach ($posts as $post)
            {

                /*  --------------------------------------------------------------------------------- */

                // BUSCAR IMAGEN

                $imagen = Imagen::obtenerImagenURL($post->COD_PROD);
                
                /*  --------------------------------------------------------------------------------- */

                // CARGAR EN LA VARIABLE 

                $nestedData['CODIGO'] = $post->COD_PROD;

                $nestedData['DESCRIPCION'] = $post->DESCRIPCION;


                $nestedData['COSTO'] = Common::precio_candec($post->COSTO, $post->MONEDA);
             /*  $nestedData['PRECOSTO'] = Common::precio_candec($post->PRECOSTO, $post->MONEDA);
                $nestedData['PREMAYORISTA'] = Common::precio_candec($post->PREMAYORISTA, $post->MONEDA);*/
                
                $nestedData['LOTE'] = $post->LOTE;
                if($post->FECHA_VENC==="0000-00-00" || $post->FECHA_VENC==="1899-12-31"){
                     $nestedData['FECHA_VENC'] = "NO POSEE";
                }else{
                     $nestedData['FECHA_VENC']=$post->FECHA_VENC;
                }
               

                
                $nestedData['STOCK_INICIAL'] = Common::formato_precio($post->STOCK_INICIAL,0);
                $nestedData['STOCK_LOTE'] = Common::formato_precio($post->STOCK_LOTE,0);
                
                /*  --------------------------------------------------------------------------------- */

                $nestedData['IMAGEN'] = $imagen["imagen_external"];

                /*  --------------------------------------------------------------------------------- */

                
                $data[] = $nestedData;

            }


        } 

        /*  --------------------------------------------------------------------------------- */

        // PREPARAR EL ARRAY A ENVIAR 

        $json_data = array(
                    "data"            => $data,
                    "encabezado"=> $encabezado
        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONVERTIR EN JSON EL ARRAY Y ENVIAR 

        return $json_data; 

        /*  --------------------------------------------------------------------------------- */

    }


    public static function modificarUbicacionCompra($datos){

        try {
            
            //INICIAR VARIABLE

            $codigo = $datos["data"]["codigo"];
            $diaHora = date("Y-m-d H:i:s");

            /*  --------------------------------------------------------------------------------- */

            // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

            $user = auth()->user();

            /*  --------------------------------------------------------------------------------- */

            // INICIAR TRANSACCION 

            DB::connection('retail')->beginTransaction();

            /*  --------------------------------------------------------------------------------- */
            // OBTENER ID DE COMPRA

            $compra = Compra::select(DB::raw('ID'))
                ->where('CODIGO','=', $codigo)
                ->where('ID_SUCURSAL','=',$user->id_sucursal)
                ->get();

            /*  --------------------------------------------------------------------------------- */
            // ELIMINAR COMPRA DEPOSITO 

            DB::connection('retail')
                ->table('COMPRAS_DEPOSITO')
                ->where('FK_COMPRA','=', $compra[0]->ID)
                ->delete();


            // OBTENER ID CONTAINER

            $id_container = Container::select('ID')
                ->where('ID_SUCURSAL','=', $user->id_sucursal)
                ->where('CODIGO','=', $datos["data"]["codigoContainer"])
                ->get();

            /*  --------------------------------------------------------------------------------- */
            // MODIFICAR NRO DE CAJA EN COMPRA

            Compra::where([
                ['CODIGO', '=', $codigo],
                ['ID_SUCURSAL', '=', $user->id_sucursal]
            ])
            ->update([
                'NRO_FACTURA' => $datos['data']["nro_caja"] 
            ]);

            /*  --------------------------------------------------------------------------------- */
            // INSERTAR COMPRA DEPOSITO 

            $deposito = DB::connection('retail')
                ->table('COMPRAS_DEPOSITO')->insertGetId([
                        'FK_CONTAINER' => $id_container[0]->ID,
                        'FK_COMPRA' => $compra[0]->ID,
                        'FK_GONDOLA' => $datos["data"]["gondola"],
                        'FK_SECCION' => $datos["data"]["seccion"],
                        'FK_SECTOR' => $datos["data"]["sector"],
                        'FK_PISO' => $datos["data"]["piso"]
                    ]);

            /*  --------------------------------------------------------------------------------- */ 

            // INSERTAR USER REFERENCIA

            CompraUser::guardar_referencia($user->id, 4, $compra[0]->ID, $diaHora);

            /*  --------------------------------------------------------------------------------- */
            
            // ENVIAR TRANSACCION A BD

            DB::connection('retail')->commit();

            /*  --------------------------------------------------------------------------------- */

            return ["response" => true];

            /*  --------------------------------------------------------------------------------- */

        }catch (Exception $e){
            
            /*  --------------------------------------------------------------------------------- */

           // NO GUARDAR NINGUN CAMBIO 

           DB::connection('retail')->rollBack();
           throw $e;
           
           /*  --------------------------------------------------------------------------------- */

        }
    }


}
