<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Gondola_Tiene_Seccion;
use App\User_Tiene_Seccion;
use App\Compra;
use App\Parametro;
use App\Common;

class Seccion extends Model
{
	 protected $connection = 'retail';
    protected $table = 'secciones';
    public $timestamps = false;

     public static function mostrarSeccion($data)
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER TODOS LOS EMPLEADOS

    	$secciones = Seccion::select(DB::raw('CODIGO, DESCRIPCION, ID_SUCURSAL'))
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if (count($secciones) > 0) {
        	return ['secciones' => $secciones];
        } else {
        	return ['secciones' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function encontrarSeccion($data)
    {

    	/*  --------------------------------------------------------------------------------- */
    	   
    	// OBTENER TODAS LAS SUCURSALES

    	$seccion = Seccion::select(DB::raw('ID, DESCRIPCION, DESC_CORTA, ID_SUCURSAL'))
        ->where('ID', '=', $data['codigo'])
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
        
        if (count($seccion) > 0) {
            return ['seccion' => $seccion];
        } else {
            return ['seccion' => 0];
        }

        /*  --------------------------------------------------------------------------------- */
    }

    public static function secciones_datatable($request){

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 
        $user = auth()->user();
        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'ID', 
                            1 => 'CODIGO',
                            2 => 'DESCRIPCION',
                            3 => 'DESC_CORTA'
                        );
        // CONTAR LA CANTIDAD DE SECCIONES ENCONTRADAS 
        $totalData = Seccion::
        where('ID_SUCURSAL','=', $user->id_sucursal)
        ->count();
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

            //  CARGAR TODOS LAS SECCIONES ENCONTRADOS 

            $posts = Seccion::select(DB::raw('ID, CODIGO, DESCRIPCION, DESC_CORTA'))
                         ->where('ID_SUCURSAL','=', $user->id_sucursal)
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

            // CARGAR LAS SECCIONES FILTRADOS EN DATATABLE

            $posts = Seccion::select(DB::raw('ID, CODIGO, DESCRIPCION, DESC_CORTA'))
                            ->where('ID_SUCURSAL','=', $user->id_sucursal)
                            ->where(function ($query) use ($search) {
                                $query->where('CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('DESCRIPCION', 'LIKE',"%{$search}%")
                                      ->orWhere('DESC_CORTA', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE SECCIONES FILTRADOS 

            $totalFiltered = Seccion::where(function ($query) use ($search) {
                                $query->where('CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('DESCRIPCION', 'LIKE',"%{$search}%")
                                      ->orWhere('DESC_CORTA', 'LIKE',"%{$search}%");
                            })
                             ->where('ID_SUCURSAL','=', $user->id_sucursal)
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
                $nestedData['CODIGO'] = $post->CODIGO;
                $nestedData['DESCRIPCION'] = $post->DESCRIPCION;
                $nestedData['DESC_CORTA'] = $post->DESC_CORTA;
                
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

    public static function nuevaSeccion(){
         $user = auth()->user();

        //OBTENER EL ULTIMO CODIGO DE LA SECCION
        $seccion = seccion::select(DB::raw('IFNULL(CODIGO,0) AS CODIGO'))
                        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
                        ->orderby('CODIGO','DESC')
                        ->limit(1)
                        ->get()
                        ->toArray();
        
        // RETORNAR EL VALOR
        if(count($seccion)>0){
            return ["seccion" => $seccion[0]["CODIGO"]];
        }else{

            return ["seccion" => 0];
        }

     }

     public static function filtrarSeccion($datos){


        $seccion=seccion::select(DB::raw('CODIGO, 
            DESCRIPCION, DESC_CORTA'
            )
        )
        ->where('ID','=', $datos['data'])
        ->get();
        
            return['seccion'=> $seccion];
    }



    public static function guardarSeccion($datos){

        $user = auth()->user();
        try{
            DB::connection('retail')->beginTransaction();

            if($datos['data']['btn_guardar']==true){

                $seccion=seccion::insertGetId([
                    'CODIGO'=> $datos['data']['codigo'],
                    'DESCRIPCION'=> $datos['data']['descripcion'],
                    'DESC_CORTA'=> $datos['data']['descripcionCorta'],
                    'ID_SUCURSAL'=>$user->id_sucursal]);

                DB::connection('retail')->commit();

                return['response'=>true];

            }else{
                $seccion = Seccion::Where('CODIGO','=',$datos['data']['codigo'])->where('ID_SUCURSAL', '=', $user->id_sucursal)
                    ->update([
                    'DESCRIPCION'=> $datos['data']['descripcion'],
                    'DESC_CORTA'=> $datos['data']['descripcionCorta']
                ]);
                DB::connection('retail')->commit();
                return['response'=>true];
            }
        }
        catch(Exception $ex){
            DB::connection('retail')->rollBack();
            if($ex->errorInfo[1]==1062){
                return ["response"=>false,'statusText'=>'¡Este Código de seccion ya fue registrado!'];
            }else{
                return ["response"=>false,'statusText'=>$ex->getMessage()];
            }
        }
    }

    public static function eliminarSeccion($datos){
        $user = auth()->user();

        if($datos['data']['btn_guardar']==false){

            
            
            $gondola_seccion = Seccion::verificarGondolaTieneSeccion($datos['data']['codigo']);
            
            if($gondola_seccion['response']==false){
                return $gondola_seccion;
            }


            
            $user_seccion = Seccion::verificarUserTieneSeccion($datos['data']['codigo']);

            if($user_seccion['response']==false){
                return $user_seccion;
            }

            $seccion = Seccion::where('CODIGO','=',$datos['data']['codigo'])
            				->where('ID_SUCURSAL', '=', $user->id_sucursal)->delete();

            return ['response'=>true];
        }else{
            return["response"=>false, 'statusText'=> 'No existe esta seccion '.$datos['data']['codigo']];
        }
    }

    public static function conseguir_id($codigo,$id_sucursal){
    	$id_seccion=Seccion::select('ID')
            		->where('CODIGO','=',$codigo)
            		->where('ID_SUCURSAL','=',$id_sucursal)
                    ->get()
                    ->toArray();
                    return $id_seccion[0]['ID'];

    }
    public static function verificarGondolaTieneSeccion($codigo){

        $user = auth()->user();

        $gondola = Gondola_Tiene_Seccion::select('ID')
        ->where('ID_SECCION', '=', $codigo)
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->limit(1)
        ->get();

         // RETORNAR EL VALOR

        if(count($gondola) > 0 && !empty($codigo)){

            return  ["response"=>false,'statusText'=> 'Existe una gondola cargado con esta seccion.'];
        } else {

            // MUESTRA QUE NO EXISTE

            return ["response"=>true];

        }
    }

    public static function verificarUserTieneSeccion($codigo){

    	$user = auth()->user();

        $userSeccion = User_Tiene_Seccion::select('FK_USER')
        ->where('FK_SECCION', '=', $codigo)
        ->limit(1)
        ->get();

         // RETORNAR EL VALOR

        if(count($userSeccion) > 0 && !empty($codigo)){

            return  ["response"=>false,'statusText'=> 'Existe un usuario cargado con esta seccion.'];
        } else {

            // MUESTRA QUE NO EXISTE

            return ["response"=>true];

        }
    }

    public static function obtenerDatos($datos){

        $user = auth()->user();

        $ganancia = 0;

        $compra = Compra::select(DB::raw('
                PROVEEDORES.NOMBRE AS PROVEEDOR,
                SUM(COMPRASDET.CANTIDAD) AS ENTRADA,
                LOTES.LOTE AS LOTE,
                AVG(LOTES.COSTO) AS COSTO_PROMEDIO,
                SUM(COMPRASDET.COSTO_TOTAL) AS COSTO_TOTAL,
                PROVEEDORES.CODIGO AS COD_PROVEEDOR,
                IFNULL(SECCIONES.ID, 0) AS SECCION_CODIGO,
                IFNULL(SECCIONES.DESCRIPCION,"INDEFINIDO") AS SECCION,
                IFNULL(SUM(LOTES.CANTIDAD), 0) AS STOCK_ACTUAL,
                IFNULL(SUM(LOTES.CANTIDAD * LOTES.COSTO),0) AS COSTO_SOBRANTE'))
            ->leftjoin('COMPRASDET','COMPRASDET.FK_COMPRAS','=','COMPRAS.ID')
            ->leftjoin('PRODUCTOS_AUX', function($join){
                $join->on('PRODUCTOS_AUX.CODIGO','=','COMPRASDET.COD_PROD')
                     ->on('PRODUCTOS_AUX.ID_SUCURSAL','=','COMPRASDET.ID_SUCURSAL');
                })
            ->leftjoin('PRODUCTOS_TIENE_SECCION', function($join){
                $join->on('PRODUCTOS_TIENE_SECCION.COD_PROD','=','COMPRASDET.COD_PROD')
                     ->on('PRODUCTOS_TIENE_SECCION.ID_SUCURSAL','=','COMPRASDET.ID_SUCURSAL');
                })
            ->leftjoin('SECCIONES','SECCIONES.ID','=','PRODUCTOS_TIENE_SECCION.SECCION')
            ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'COMPRAS.PROVEEDOR')
            ->leftjoin('LOTE_TIENE_COMPRASDET', 'LOTE_TIENE_COMPRASDET.ID_COMPRAS_DET', '=', 'COMPRASDET.ID')
            ->leftjoin('LOTES', 'LOTES.ID', '=', 'LOTE_TIENE_COMPRASDET.ID_LOTE')
        ->whereBetween('COMPRAS.FECALTAS', [$datos["inicio"], $datos["final"] ])
        ->Where('COMPRAS.ID_SUCURSAL','=',$datos["sucursal"])
        ->where('SECCIONES.ID', '=', $datos["secciones"])
        ->whereIn('COMPRAS.PROVEEDOR', $datos["proveedores"])
        ->groupBy('COMPRAS.PROVEEDOR')
        ->orderBy('PROVEEDORES.NOMBRE')
        ->get();

        $venta = Compra::select(DB::raw('
                IFNULL(SUM(VENTASDET.PRECIO_UNIT * VENTASDET_TIENE_LOTES.CANTIDAD), 0) AS TOTAL_VENTA,
                IFNULL(SUM(VENTASDET_TIENE_LOTES.CANTIDAD), 0) AS VENDIDO,
                IFNULL(SUM(VENTASDET_TIENE_LOTES.CANTIDAD * LOTES.COSTO), 0) AS VENTA_COSTO,
                PROVEEDORES.CODIGO AS COD_PROVEEDOR,
                PROVEEDORES.NOMBRE AS PROVEEDOR'))
            ->leftjoin('COMPRASDET','COMPRASDET.FK_COMPRAS','=','COMPRAS.ID')
            ->leftjoin('PRODUCTOS_AUX',function($join){
                $join->on('PRODUCTOS_AUX.CODIGO','=','COMPRASDET.COD_PROD')
                     ->on('PRODUCTOS_AUX.ID_SUCURSAL','=','COMPRASDET.ID_SUCURSAL');
            })
            ->leftjoin('PRODUCTOS_TIENE_SECCION',function($join){
                $join->on('PRODUCTOS_TIENE_SECCION.COD_PROD','=','COMPRASDET.COD_PROD')
                     ->on('PRODUCTOS_TIENE_SECCION.ID_SUCURSAL','=','COMPRASDET.ID_SUCURSAL');
            })
        ->leftjoin('SECCIONES','SECCIONES.ID','=','PRODUCTOS_TIENE_SECCION.SECCION')
        ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'COMPRAS.PROVEEDOR')
        ->leftjoin('LOTE_TIENE_COMPRASDET', 'LOTE_TIENE_COMPRASDET.ID_COMPRAS_DET', '=', 'COMPRASDET.ID')
        ->leftjoin('LOTES', 'LOTES.ID', '=', 'LOTE_TIENE_COMPRASDET.ID_LOTE')
        ->leftjoin('VENTASDET_TIENE_LOTES','VENTASDET_TIENE_LOTES.ID_LOTE','=','LOTES.ID')
        ->leftjoin('VENTASDET','VENTASDET.ID','=','VENTASDET_TIENE_LOTES.ID_VENTAS_DET')
        ->leftjoin('VENTAS','VENTAS.ID','=','VENTASDET.FK_VENTA')
        ->whereBetween('COMPRAS.FECALTAS', [$datos["inicio"], $datos["final"] ])
        ->where('COMPRAS.ID_SUCURSAL','=',$datos["sucursal"]) 
        ->where('SECCIONES.ID', '=', $datos["secciones"])
        ->whereIn('COMPRAS.PROVEEDOR',$datos["proveedores"])
        ->groupBy('COMPRAS.PROVEEDOR')
        ->orderBy('PROVEEDORES.NOMBRE')
        ->get();
        
        $transferencia = Compra::select(DB::raw('
                IFNULL(SUM(TRANSFERENCIADET_TIENE_LOTES.CANTIDAD), 0) AS TRANSFERENCIA,
                IFNULL(SUM(TRANSFERENCIAS_DET.PRECIO * TRANSFERENCIADET_TIENE_LOTES.CANTIDAD), 0) AS TOTAL_TRANSFERENCIA,
                PROVEEDORES.CODIGO AS COD_PROVEEDOR,
                PROVEEDORES.NOMBRE AS PROVEEDOR'))
            ->leftjoin('COMPRASDET','COMPRASDET.FK_COMPRAS','=','COMPRAS.ID')
            ->leftjoin('PRODUCTOS_AUX',function($join){
                $join->on('PRODUCTOS_AUX.CODIGO','=','COMPRASDET.COD_PROD')
                     ->on('PRODUCTOS_AUX.ID_SUCURSAL','=','COMPRASDET.ID_SUCURSAL');
            })
            ->leftjoin('PRODUCTOS_TIENE_SECCION',function($join){
                $join->on('PRODUCTOS_TIENE_SECCION.COD_PROD','=','COMPRASDET.COD_PROD')
                     ->on('PRODUCTOS_TIENE_SECCION.ID_SUCURSAL','=','COMPRASDET.ID_SUCURSAL');
            })
            ->leftjoin('SECCIONES','SECCIONES.ID','=','PRODUCTOS_TIENE_SECCION.SECCION')
            ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'COMPRAS.PROVEEDOR')
            ->leftjoin('LOTE_TIENE_COMPRASDET', 'LOTE_TIENE_COMPRASDET.ID_COMPRAS_DET', '=', 'COMPRASDET.ID')
            ->leftjoin('LOTES', 'LOTES.ID', '=', 'LOTE_TIENE_COMPRASDET.ID_LOTE')
            ->leftjoin('TRANSFERENCIADET_TIENE_LOTES','TRANSFERENCIADET_TIENE_LOTES.ID_LOTE','=','LOTES.ID')
            ->leftjoin('TRANSFERENCIAS_DET','TRANSFERENCIAS_DET.ID','=','TRANSFERENCIADET_TIENE_LOTES.ID_TRANSFERENCIA')
            ->leftjoin('TRANSFERENCIAS','TRANSFERENCIAS.ID','=','TRANSFERENCIAS_DET.FK_TRANSFERENCIA')
        ->whereBetween('COMPRAS.FECALTAS', [$datos["inicio"], $datos["final"] ])
        ->where('COMPRAS.ID_SUCURSAL','=',$datos["sucursal"]) 
        ->where('SECCIONES.ID', '=', $datos["secciones"])
        ->whereIn('COMPRAS.PROVEEDOR',$datos["proveedores"])
        ->groupBy('COMPRAS.PROVEEDOR')
        ->orderBy('PROVEEDORES.NOMBRE')
        ->get();

        $venta_in = array();

        foreach ($compra as $key =>  $value_compra ) {

            foreach ($venta as $key => $value_venta) {

                if($value_venta->COD_PROVEEDOR == $value_compra->COD_PROVEEDOR){ 

                    $total_venta_proveedor = $value_venta->TOTAL_VENTA;
                    $cantidadvendida = $value_venta->VENDIDO;
                    $nestedData['TOTAL_VENTA'] =  $total_venta_proveedor;
                    $nestedData['VENDIDO'] = $cantidadvendida;
                    $nestedData['VENTA_COSTO'] =  $value_venta->VENTA_COSTO;
                }
            }

            foreach ($transferencia as $key => $value_transf) {
                if($value_transf->COD_PROVEEDOR == $value_compra->COD_PROVEEDOR){ 

                    $nestedData['TOTAL_TRANSFERENCIA'] = $value_transf->TOTAL_TRANSFERENCIA;
                    $nestedData['TRANSFERENCIA'] =  $value_transf->TRANSFERENCIA;
                }
            }

            $nestedData['PROVEEDOR'] = $value_compra->PROVEEDOR;
            $nestedData['ENTRADA'] = $value_compra->ENTRADA;
            $nestedData['COSTO_TOTAL'] = $value_compra->COSTO_TOTAL;
            $nestedData['COSTO_PROMEDIO'] = $value_compra->COSTO_PROMEDIO;
            $nestedData['SECCION'] = $value_compra->SECCION;
            $nestedData['STOCK_ACTUAL'] = $value_compra->STOCK_ACTUAL;
            $nestedData['COSTO_SOBRANTE'] = $value_compra->COSTO_SOBRANTE;

            $venta_in[] = $nestedData;

        }
        return ["proveedores" => $venta_in];
    }

    public static function generar_reporte_proveedor_seccion($datos) {

        // INCICIAR VARIABLES 
    
        $insert = $datos["data"]["Insert"];
        $totales[] = array();
        $secciones_array = array();
        $secciones_totales_array = array();
        $user = auth()->user();
        $user_id = $user->id;
        $inicio = date('Y-m-d', strtotime($datos["data"]['Inicio']));
        $final = date('Y-m-d', strtotime($datos["data"]['Final']));
        $sucursal = $datos["data"]['Sucursal'];
        $total_general = 0;
        $total_descuento = 0;
        $total_preciounit = 0;
        $cantidadvendida = 0;
        $costo = 0;
        $totalcosto = 0;
        $datos[] = array();
            
        $data = array(
            'inicio' => date('Y-m-d', strtotime($datos["data"]["Inicio"])),
            'final' => date('Y-m-d', strtotime($datos["data"]["Final"])),
            'sucursal' => $datos["data"]["Sucursal"],
            'checkedProveedor' => $datos["data"]["AllProveedores"],
            'proveedores' => $datos["data"]["Proveedores"],
            'secciones' => $datos["data"]["Seccion"]
        );
        
        $datos = Seccion::obtenerDatos($data);

       //  // RETORNAR TODOS LOS ARRAYS


        return ['proveedores' => $datos["proveedores"]];

        /*  --------------------------------------------------------------------------------- */
    }
}
