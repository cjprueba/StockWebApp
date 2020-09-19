<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Mpdf\Mpdf;
use App\Stock;
use App\Producto;
use App\ComprasDet;
use App\Common;
use App\Parametro;
use App\Sucursal;
use App\TransferenciaDet_tiene_Lotes;
// use NumeroALetras\NumeroALetras;
use Luecano\NumeroALetras\NumeroALetras;
use App\TransferenciaUser;
use App\Dev_Transferencia_User;
use App\Central_tiene_Sucursales;

class DevTransferencia extends Model
{
	   protected $connection = 'retail';
    protected $table = 'dev_transferencia';
    public $timestamps = false;
    //
     public static function mostrarDatatable($request)
    {

        /*  --------------------------------------------------------------------------------- */

        // INICIARA VARIABLES

        //global $search;

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'CODIGO',
                            1 => 'CODIGO_TRANSFERENCIA',
                            2 => 'ORIGEN',
                            3 => 'DESTINO',
                            4 => 'FECHA',
                            5 => 'HORA',
                            6 => 'ESTATUS',
                            7 => 'ACCION'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = DevTransferencia::where('ID_SUCURSAL','=', $user->id_sucursal)
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

            $posts = DevTransferencia::select(DB::raw('DEV_TRANSFERENCIA.ID AS CODIGO, ORIGEN.DESCRIPCION AS ORIGEN, DESTINO.DESCRIPCION AS DESTINO,DEV_TRANSFERENCIA.FECALTAS,DEV_TRANSFERENCIA.HORALTAS, DEV_TRANSFERENCIA.ESTATUS,TRANSFERENCIAS.CODIGO AS CODIGO_TRANSFERENCIA'))
                         ->leftjoin('SUCURSALES AS ORIGEN', 'ORIGEN.CODIGO', '=', 'DEV_TRANSFERENCIA.ID_SUCURSAL_ENVIA')
                         ->leftjoin('SUCURSALES AS DESTINO', 'DESTINO.CODIGO', '=', 'DEV_TRANSFERENCIA.ID_SUCURSAL_RECIBE')
                         ->leftjoin('TRANSFERENCIAS', 'TRANSFERENCIAS.ID', '=', 'DEV_TRANSFERENCIA.ID_TRANSFERENCIA_ENVIA')
                         ->where('DEV_TRANSFERENCIA.ID_SUCURSAL','=', $user->id_sucursal)
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

            $posts =  DevTransferencia::select(DB::raw('DEV_TRANSFERENCIA.ID AS CODIGO, ORIGEN.DESCRIPCION AS ORIGEN, DESTINO.DESCRIPCION AS DESTINO,DEV_TRANSFERENCIA.FECALTAS,DEV_TRANSFERENCIA.HORALTAS, DEV_TRANSFERENCIA.ESTATUS,TRANSFERENCIAS.CODIGO AS CODIGO_TRANSFERENCIA'))
                          ->leftjoin('SUCURSALES AS ORIGEN', 'ORIGEN.CODIGO', '=', 'DEV_TRANSFERENCIA.ID_SUCURSAL_ENVIA')
                             ->leftjoin('TRANSFERENCIAS', 'TRANSFERENCIAS.ID', '=', 'DEV_TRANSFERENCIA.ID_TRANSFERENCIA_ENVIA')
                         ->leftjoin('SUCURSALES AS DESTINO', 'DESTINO.CODIGO', '=', 'DEV_TRANSFERENCIA.ID_SUCURSAL_RECIBE')
                        ->where('DEV_TRANSFERENCIA.ID_SUCURSAL','=', $user->id_sucursal)
                            ->where(function ($query) use ($search) {
                                $query->where('DEV_TRANSFERENCIA.ID','LIKE',"%{$search}%")
                                      ->orWhere('DESTINO.DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = Dev_Transferencia::where('ID_SUCURSAL','=', $user->id_sucursal)
                            ->leftjoin('SUCURSALES AS DESTINO', 'DESTINO.CODIGO', '=', 'DEV_TRANSFERENCIA.ID_SUCURSAL_RECIBE')
                            ->where(function ($query) use ($search) {
                                $query->where('DEV_TRANSFERENCIA.ID','LIKE',"%{$search}%")
                                      ->orWhere('DESTINO.DESCRIPCION', 'LIKE',"%{$search}%");
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
                $nestedData['ORIGEN'] = $post->ORIGEN;
                $nestedData['DESTINO'] = $post->DESTINO;
                $nestedData['FECHA'] = substr($post->FECALTAS,0,10);
                $nestedData['HORA'] = $post->HORALTAS;
                $nestedData['CODIGO_TRANSFERENCIA'] = $post->CODIGO_TRANSFERENCIA;


                if ($post->ESTATUS == 0) {
                    $nestedData['ESTATUS'] = '<span class="badge badge-warning">Pendiente</span>';
                } else if ($post->ESTATUS == 1) {
                    $nestedData['ESTATUS'] = '<span class="badge badge-info">Enviado</span>';
                } else {
                    $nestedData['ESTATUS'] = '<span class="badge badge-success">Procesado</span>';
                }
                
                $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarTransferencia' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a>&emsp;<a href='#' id='enviarTransferencia' title='Enviar'><i class='fa fa-paper-plane text-success'  aria-hidden='true'></i></a> &emsp;<a href='#' id='eliminarTransferencia' title='Eliminar'><i class='fa fa-trash text-danger' aria-hidden='true'></i></a>";

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
     public static function mostrar_productos_devolucion($request) {

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $codigo = $request->input('codigoDevTransferencia');


        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
                $codigo_origen = $user->id_sucursal;

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'COD_PROD',
                            1 => 'LOTE',
                            2 => 'DESCRIPCION',
                            3 => 'CANTIDAD',
                            4 => 'COSTO_UNIT',
                            5 => 'COSTO_TOTAL'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE PRODUCTOS ENCONTRADOS 

        $totalData = DB::connection('retail')->table('dev_transferencia_det')
                    ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'dev_transferencia_det.COD_PROD')
                    ->leftjoin('LOTES','LOTES.ID','=','dev_transferencia_det.ID_LOTE_RECIBE')
                       ->leftjoin('dev_transferencia','dev_transferencia.ID','=','dev_transferencia_det.ID_DEV_TRANSFERENCIA')
                   
                    ->where('dev_transferencia_det.ID_DEV_TRANSFERENCIA','=', $codigo)
                      ->groupBy("dev_transferencia_det.ID_LOTE_RECIBE")
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

            $posts = DB::connection('retail')->table('dev_transferencia_det')
                         ->select(DB::raw('dev_transferencia_det.COD_PROD,LOTES.LOTE,SUM(dev_transferencia_det.CANTIDAD) AS CANTIDAD, PRODUCTOS.DESCRIPCION, LOTES.COSTO AS COSTO_UNIT, sum(dev_transferencia_det.CANTIDAD)*LOTES.COSTO AS COSTO_TOTAL,TRANSFERENCIAS.MONEDA AS MONEDA'))
                         ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'dev_transferencia_det.COD_PROD')
                             ->leftjoin('LOTES', 'LOTES.ID', '=', 'dev_transferencia_det.ID_LOTE_RECIBE')
                             ->leftjoin('dev_transferencia','dev_transferencia.ID','=','dev_transferencia_det.ID_DEV_TRANSFERENCIA')
                            ->leftjoin('TRANSFERENCIAS','TRANSFERENCIAS.ID','=','dev_transferencia.ID_TRANSFERENCIA_ENVIA')
                      
                        ->where('dev_transferencia_det.ID_DEV_TRANSFERENCIA','=', $codigo)
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->groupBy("dev_transferencia_det.ID_LOTE_RECIBE")
                         ->get();


            /*  ************************************************************ */

        } else {

            /*  ************************************************************ */

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            /*  ************************************************************ */

            // CARGAR LOS PRODUCTOS FILTRADOS EN DATATABLE

            $posts =   DB::connection('retail')->table('dev_transferencia_det')
                         ->select(DB::raw('dev_transferencia_det.COD_PROD,LOTES.LOTE,SUM(dev_transferencia_det.CANTIDAD) AS CANTIDAD, PRODUCTOS.DESCRIPCION, LOTES.COSTO AS COSTO_UNIT, SUM(dev_transferencia_det.CANTIDAD)*LOTES.COSTO AS COSTO_TOTAL,TRANSFERENCIAS.MONEDA AS MONEDA'))
                         ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'dev_transferencia_det.COD_PROD')
                             ->leftjoin('LOTES', 'LOTES.ID', '=', 'dev_transferencia_det.ID_LOTE_RECIBE')

                              ->leftjoin('dev_transferencia','dev_transferencia.ID','=','dev_transferencia_det.ID_DEV_TRANSFERENCIA')
                                ->leftjoin('TRANSFERENCIAS','TRANSFERENCIAS.ID','=','dev_transferencia.ID_TRANSFERENCIA_ENVIA')
                       
                         ->where('dev_transferencia_det.ID_DEV_TRANSFERENCIA','=', $codigo)
                            ->where(function ($query) use ($search) {
                                $query->where('dev_transferencia_det.COD_PROD','LIKE',"%{$search}%")
                                      ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                              ->groupBy("dev_transferencia_det.ID_LOTE_RECIBE")
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = DB::connection('retail')->table('dev_transferencia_det')
                         ->select(DB::raw('dev_transferencia_det.COD_PROD,LOTES.LOTE,SUM(dev_transferencia_det.CANTIDAD) AS CANTIDAD, PRODUCTOS.DESCRIPCION, LOTES.COSTO AS COSTO_UNIT, SUM(dev_transferencia_det.CANTIDAD)*LOTES.COSTO AS COSTO_TOTAL,TRANSFERENCIAS.MONEDA AS MONEDA'))
                         ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'dev_transferencia_det.COD_PROD')
                             ->leftjoin('LOTES', 'LOTES.ID', '=', 'dev_transferencia_det.ID_LOTE_RECIBE')
                       
                              ->leftjoin('dev_transferencia','dev_transferencia.ID','=','dev_transferencia_det.ID_DEV_TRANSFERENCIA')
                                ->leftjoin('TRANSFERENCIAS','TRANSFERENCIAS.ID','=','dev_transferencia.ID_TRANSFERENCIA_ENVIA')
                        
                 ->where('dev_transferencia_det.ID_DEV_TRANSFERENCIA','=', $codigo)
                            ->where(function ($query) use ($search) {
                                $query->where('dev_transferencia_det.COD_PROD','LIKE',"%{$search}%")
                                      ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                              ->groupBy("dev_transferencia_det.ID_LOTE_RECIBE")
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

                $nestedData['COD_PROD'] = $post->COD_PROD;
                $nestedData['LOTE'] = $post->LOTE;
                $nestedData['DESCRIPCION'] = $post->DESCRIPCION;
                $nestedData['CANTIDAD'] = $post->CANTIDAD;

                $nestedData['COSTO_UNIT'] = Common::precio_candec($post->COSTO_UNIT,$post->MONEDA);
                $nestedData['COSTO_TOTAL'] = Common::precio_candec($post->COSTO_TOTAL,$post->MONEDA);

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
       public static function enviar_dev_transferencia($data){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

            $user = auth()->user();
         $dia = date("Y-m-d");
        $hora = date("H:i:s");

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $codigo = $data["data"];

        /*  --------------------------------------------------------------------------------- */

        // REVISAR ESTATUS 

        $estatus = DevTransferencia::verificar_estatus($codigo);

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EL ESTATUS SE ENCUENTRA EN 1 PARA SER IMPORTADO

        if ($estatus === false or $estatus === 1 or $estatus === 2) {
            return ["response" => false, "statusText" => "Ya se encuentra procesada o enviada !"];
        }

        /*  --------------------------------------------------------------------------------- */

        // LA TRANSFERENCIA SOLO SERA MODIFICADA SI SE ENCUENTRA EN ESTATUS 1 QUE ES ENVIADO

        if ($estatus === 0) {

            $devtransferencia = DB::connection('retail')
            ->table('dev_transferencia')
            ->where('ID','=', $codigo)
            ->where('ID_SUCURSAL','=', $user->id_sucursal)
            ->update([
                'ESTATUS' => 1,
                'FECMODIF'=>$dia,
                'HORMODIF'=>$hora,
                'USERM'=>$user->id_sucursal

            ]);

        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return ["response" => true];

        /*  --------------------------------------------------------------------------------- */

    }
       public static function verificar_estatus($codigo) {

        /*  --------------------------------------------------------------------------------- */

        // VERIFICAR SI EXISTE 

        $devtransferencia = DB::connection('retail')
        ->table('dev_transferencia')
        ->select(DB::raw(
                        'ESTATUS'
                    ))
       
        ->where('ID','=', $codigo)
        ->get();

        /*  --------------------------------------------------------------------------------- */
        
        // RETORNAL VALOR 
        
        if(count($devtransferencia) > 0){
            return $devtransferencia[0]->ESTATUS;
        } else {
            return false;
        }

        /*  --------------------------------------------------------------------------------- */

    }
    public static function eliminar($codigo, $opcion) 
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $codigo = $codigo["data"];
        $estatus = '';
        $diaHora = date("Y-m-d H:i:s");
        
        /*  --------------------------------------------------------------------------------- */

        // VERIFICAR SI EXISTE
 

        /*  --------------------------------------------------------------------------------- */

        // VERIFICAR ESTATUS
        
        $estatus = DevTransferencia::verificar_estatus($datos["codigo"]);

        if ($estatus === false) {
            return ["response" => false, "statusText" => "Ya se encuentra procesada o enviada !"];
        }

        /*  --------------------------------------------------------------------------------- */

        // VERIFICAR SI LA TRANSFERENCIA YA SE PROCESO 

        if ($estatus === 1 or $estatus === 2) {
            return ["response" => false, "statusText" => "Ya se encuentra procesada o enviada !"];
        }

        /*  --------------------------------------------------------------------------------- */

        // OBTENER EL CODIGO Y LA CANTIDA DEL PRODUCTO 

        $devtransferencias_det = DB::connection('retail')
        ->table('dev_transferencia_det')
        ->select(DB::raw(
                        'ID,
                        COD_PROD, 
                        CANTIDAD,
                        ID_LOTE_RECIBE AS ID_LOTE'
                    ))
        ->where('ID_SUCURSAL','=', $user->id_sucursal)
        ->where('ID_DEV_TRANSFERENCIA','=', $codigo)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RECORRER FILAS DE TRANSFERENCIA DET 

        foreach ($devtransferencias_det as $key => $td) {

            /*  --------------------------------------------------------------------------------- */

            // ELIMINAR CLAVES FORANEAS DE TRANSFERENCIA TIENE LOTES 
            
       /*   var_dump($td);*/
             Stock::sumar_stock_id_lote($td->ID_LOTE, $td->CANTIDAD);
            /*  --------------------------------------------------------------------------------- */

            // DEVOLVER STOCK 

            

            /*  --------------------------------------------------------------------------------- */

        }

        /*  --------------------------------------------------------------------------------- */

        // ELIMINAR TODA LA DEVOLUCION DE TRANSFERENIA DET

        $devtransferencias_det = DB::connection('retail')
        ->table('dev_transferencia_det')
        ->where('ID_SUCURSAL','=', $user->id_sucursal)
        ->where('ID_DEV_TRANSFERENCIA','=', $codigo)
        ->delete();

        /*  --------------------------------------------------------------------------------- */

        // ELIMINAR TODA TRANSFERENIA

        if ($opcion === 1) {

            /*  --------------------------------------------------------------------------------- */

            $devtransferencia = DevTransferencia::select(DB::raw('ID'))
            ->where('ID','=', $codigo)
            ->where('ID_SUCURSAL','=',$user->id_sucursal)
            ->get();

            /*  --------------------------------------------------------------------------------- */

            // INSERTAR USER REFERENCIA

            Dev_Transferencia_User::guardar_referencia($user->id, 3, $devtransferencia[0]->ID, $diaHora);

            /*  --------------------------------------------------------------------------------- */

           $transferencias = DB::connection('retail')
           ->table('dev_transferencia')
           ->where('ID_SUCURSAL','=', $user->id_sucursal)
           ->where('ID','=', $codigo)
           ->delete();
 
        }
        
        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return ["response" => true];

        /*  --------------------------------------------------------------------------------- */
    }
    public static function mostrarImportar($request)
    {

        /*  --------------------------------------------------------------------------------- */

        // INICIARA VARIABLES


        //global $search;

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 
        $columns = array( 
                            0 => 'CODIGO',
                            1 => 'CODIGO_TRANSFERENCIA',
                            2 => 'ORIGEN',
                            3 => 'DESTINO',
                            4 => 'FECHA',
                            5 => 'HORA',
                            6 => 'ESTATUS',
                            7 => 'ACCION'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = DevTransferencia::where('ID_SUCURSAL_ENVIA','=', $user->id_sucursal)
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

            $posts = DevTransferencia::select(DB::raw('DEV_TRANSFERENCIA.ID AS CODIGO, ORIGEN.DESCRIPCION AS ORIGEN, DESTINO.DESCRIPCION AS DESTINO,DEV_TRANSFERENCIA.FECALTAS,DEV_TRANSFERENCIA.HORALTAS, DEV_TRANSFERENCIA.ESTATUS,TRANSFERENCIAS.CODIGO AS CODIGO_TRANSFERENCIA'))
                         ->leftjoin('SUCURSALES AS ORIGEN', 'ORIGEN.CODIGO', '=', 'DEV_TRANSFERENCIA.ID_SUCURSAL_ENVIA')
                         ->leftjoin('SUCURSALES AS DESTINO', 'DESTINO.CODIGO', '=', 'DEV_TRANSFERENCIA.ID_SUCURSAL_RECIBE')
                         ->leftjoin('TRANSFERENCIAS', 'TRANSFERENCIAS.ID', '=', 'DEV_TRANSFERENCIA.ID_TRANSFERENCIA_ENVIA')
                         ->where('DEV_TRANSFERENCIA.ID_SUCURSAL_ENVIA','=', $user->id_sucursal)
                          ->where('DEV_TRANSFERENCIA.ESTATUS','>', 0)
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

            $posts =  DevTransferencia::select(DB::raw('DEV_TRANSFERENCIA.ID AS CODIGO, ORIGEN.DESCRIPCION AS ORIGEN, DESTINO.DESCRIPCION AS DESTINO,DEV_TRANSFERENCIA.FECALTAS,DEV_TRANSFERENCIA.HORALTAS, DEV_TRANSFERENCIA.ESTATUS,TRANSFERENCIAS.CODIGO AS CODIGO_TRANSFERENCIA'))
                          ->leftjoin('SUCURSALES AS ORIGEN', 'ORIGEN.CODIGO', '=', 'DEV_TRANSFERENCIA.ID_SUCURSAL_ENVIA')
                             ->leftjoin('TRANSFERENCIAS', 'TRANSFERENCIAS.ID', '=', 'DEV_TRANSFERENCIA.ID_TRANSFERENCIA_ENVIA')
                         ->leftjoin('SUCURSALES AS DESTINO', 'DESTINO.CODIGO', '=', 'DEV_TRANSFERENCIA.ID_SUCURSAL_RECIBE')
                         ->where('DEV_TRANSFERENCIA.ID_SUCURSAL_ENVIA','=', $user->id_sucursal)
                             ->where('DEV_TRANSFERENCIA.ESTATUS','>', 0)
                            ->where(function ($query) use ($search) {
                                $query->where('DEV_TRANSFERENCIA.ID','LIKE',"%{$search}%")
                                      ->orWhere('DESTINO.DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = Dev_Transferencia::where('ID_SUCURSAL','=', $user->id_sucursal)
                            ->leftjoin('SUCURSALES AS DESTINO', 'DESTINO.CODIGO', '=', 'DEV_TRANSFERENCIA.ID_SUCURSAL_RECIBE')
                             ->where('DEV_TRANSFERENCIA.ID_SUCURSAL_ENVIA','=', $user->id_sucursal)
                                 ->where('DEV_TRANSFERENCIA.ESTATUS','>', 0)
                            ->where(function ($query) use ($search) {
                                $query->where('DEV_TRANSFERENCIA.ID','LIKE',"%{$search}%")
                                      ->orWhere('DESTINO.DESCRIPCION', 'LIKE',"%{$search}%");
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
                $nestedData['ORIGEN'] = $post->ORIGEN;
                $nestedData['DESTINO'] = $post->DESTINO;
                $nestedData['FECHA'] = substr($post->FECALTAS,0,10);
                $nestedData['HORA'] = $post->HORALTAS;
                $nestedData['CODIGO_TRANSFERENCIA'] = $post->CODIGO_TRANSFERENCIA;


                if ($post->ESTATUS == 0) {
                    $nestedData['ESTATUS'] = '<span class="badge badge-warning">Pendiente</span>';
                } else if ($post->ESTATUS == 1) {
                    $nestedData['ESTATUS'] = '<span class="badge badge-info">Recibido</span>';
                } else {
                    $nestedData['ESTATUS'] = '<span class="badge badge-success">Procesado</span>';
                }
                
                $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarTransferencia' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a> &emsp;<a href='#' id='importarDevTransferencia' title='Importar'><i class='fa fa-check text-success' aria-hidden='true'></i></a>&emsp;<a href='#' id='rechazarDevTransferencia' title='Cancelar'><i class='fa fa-times text-danger' aria-hidden='true'></i></a>
                          &emsp;<a href='#' id='imprimirReporte' title='Reporte'><i class='fa fa-file text-secondary' aria-hidden='true'></i>";

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
        public static function importar_dev_transferencia($datos)
    { 

    	         try{
    	         	               $user = auth()->user();
         $dia = date("Y-m-d");
        $hora = date("H:i:s");
    	         	 $estatus = DevTransferencia::verificar_estatus($datos["codigo"]);

            /*  --------------------------------------------------------------------------------- */

            // REVISAR SI EL ESTATUS SE ENCUENTRA EN 1 PARA SER IMPORTADO

            if ($estatus === false or $estatus === 0 or $estatus === 2) {
                return ["response" => false, "statusText" => "Ya se encuentra procesada !"];
            }
    	         	$posts = DB::connection('retail')->table('dev_transferencia_det')
                         ->select(DB::raw('dev_transferencia_det.COD_PROD,SUM(dev_transferencia_det.CANTIDAD) AS CANTIDAD, dev_transferencia_det.ID_LOTE_ENVIA AS ID_LOTE'))
                         ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'dev_transferencia_det.COD_PROD')
                             ->leftjoin('dev_transferencia','dev_transferencia.ID','=','dev_transferencia_det.ID_DEV_TRANSFERENCIA')
                            ->leftjoin('TRANSFERENCIAS','TRANSFERENCIAS.ID','=','dev_transferencia.ID_TRANSFERENCIA_ENVIA')
                      
                        ->where('dev_transferencia_det.ID_DEV_TRANSFERENCIA','=', $datos["codigo"])
                         
                         ->groupBy("dev_transferencia_det.ID_LOTE_ENVIA")
                         ->get()->toArray();
                       foreach ($posts as $key => $value) {

                       	   Stock::sumar_stock_id_lote($value->ID_LOTE,$value->CANTIDAD);
                       	# code...
                       }

            $DEVtransferencia = DB::connection('retail')
            ->table('dev_transferencia')
            ->where('ID','=',$datos["codigo"])

            ->update([
                'ESTATUS' => 2,
                'FECMODIF'=>$dia,
                'HORMODIF'=>$hora,
                'USERM'=>$user->id_sucursal
            ]);
                
                 return ["response"=>true];

    	         }catch (Exception $e) {
                DB::connection('retail')->rollBack();
                throw $e;
            }
    }
     public static function mostrar_productos_devolucion_imp($request) {

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $codigo = $request->input('codigoDevTransferencia');


        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
                $codigo_origen = $user->id_sucursal;

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'COD_PROD',
                            1 => 'LOTE',
                            2 => 'DESCRIPCION',
                            3 => 'CANTIDAD',
                            4 => 'COSTO_UNIT',
                            5 => 'COSTO_TOTAL'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE PRODUCTOS ENCONTRADOS 

        $totalData = DB::connection('retail')->table('dev_transferencia_det')
                    ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'dev_transferencia_det.COD_PROD')
                    ->leftjoin('LOTES','LOTES.ID','=','dev_transferencia_det.ID_LOTE_ENVIA')
                       ->leftjoin('dev_transferencia','dev_transferencia.ID','=','dev_transferencia_det.ID_DEV_TRANSFERENCIA')
                   
                    ->where('dev_transferencia_det.ID_DEV_TRANSFERENCIA','=', $codigo)
                      ->groupBy("dev_transferencia_det.ID_LOTE_ENVIA")
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

            $posts = DB::connection('retail')->table('dev_transferencia_det')
                         ->select(DB::raw('dev_transferencia_det.COD_PROD,LOTES.LOTE,SUM(dev_transferencia_det.CANTIDAD) AS CANTIDAD, PRODUCTOS.DESCRIPCION, LOTES.COSTO AS COSTO_UNIT, sum(dev_transferencia_det.CANTIDAD)*LOTES.COSTO AS COSTO_TOTAL,TRANSFERENCIAS.MONEDA AS MONEDA'))
                         ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'dev_transferencia_det.COD_PROD')
                             ->leftjoin('LOTES', 'LOTES.ID', '=', 'dev_transferencia_det.ID_LOTE_ENVIA')
                             ->leftjoin('dev_transferencia','dev_transferencia.ID','=','dev_transferencia_det.ID_DEV_TRANSFERENCIA')
                            ->leftjoin('TRANSFERENCIAS','TRANSFERENCIAS.ID','=','dev_transferencia.ID_TRANSFERENCIA_ENVIA')
                      
                        ->where('dev_transferencia_det.ID_DEV_TRANSFERENCIA','=', $codigo)
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->groupBy("dev_transferencia_det.ID_LOTE_ENVIA")
                         ->get();


            /*  ************************************************************ */

        } else {

            /*  ************************************************************ */

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            /*  ************************************************************ */

            // CARGAR LOS PRODUCTOS FILTRADOS EN DATATABLE

            $posts =   DB::connection('retail')->table('dev_transferencia_det')
                         ->select(DB::raw('dev_transferencia_det.COD_PROD,LOTES.LOTE,SUM(dev_transferencia_det.CANTIDAD) AS CANTIDAD, PRODUCTOS.DESCRIPCION, LOTES.COSTO AS COSTO_UNIT, SUM(dev_transferencia_det.CANTIDAD)*LOTES.COSTO AS COSTO_TOTAL,TRANSFERENCIAS.MONEDA AS MONEDA'))
                         ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'dev_transferencia_det.COD_PROD')
                             ->leftjoin('LOTES', 'LOTES.ID', '=', 'dev_transferencia_det.ID_LOTE_ENVIA')

                              ->leftjoin('dev_transferencia','dev_transferencia.ID','=','dev_transferencia_det.ID_DEV_TRANSFERENCIA')
                                ->leftjoin('TRANSFERENCIAS','TRANSFERENCIAS.ID','=','dev_transferencia.ID_TRANSFERENCIA_ENVIA')
                       
                         ->where('dev_transferencia_det.ID_DEV_TRANSFERENCIA','=', $codigo)
                            ->where(function ($query) use ($search) {
                                $query->where('dev_transferencia_det.COD_PROD','LIKE',"%{$search}%")
                                      ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                              ->groupBy("dev_transferencia_det.ID_LOTE_ENVIA")
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = DB::connection('retail')->table('dev_transferencia_det')
                         ->select(DB::raw('dev_transferencia_det.COD_PROD,LOTES.LOTE,SUM(dev_transferencia_det.CANTIDAD) AS CANTIDAD, PRODUCTOS.DESCRIPCION, LOTES.COSTO AS COSTO_UNIT, SUM(dev_transferencia_det.CANTIDAD)*LOTES.COSTO AS COSTO_TOTAL,TRANSFERENCIAS.MONEDA AS MONEDA'))
                         ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'dev_transferencia_det.COD_PROD')
                             ->leftjoin('LOTES', 'LOTES.ID', '=', 'dev_transferencia_det.ID_LOTE_ENVIA')
                       
                              ->leftjoin('dev_transferencia','dev_transferencia.ID','=','dev_transferencia_det.ID_DEV_TRANSFERENCIA')
                                ->leftjoin('TRANSFERENCIAS','TRANSFERENCIAS.ID','=','dev_transferencia.ID_TRANSFERENCIA_ENVIA')
                        
                 ->where('dev_transferencia_det.ID_DEV_TRANSFERENCIA','=', $codigo)
                            ->where(function ($query) use ($search) {
                                $query->where('dev_transferencia_det.COD_PROD','LIKE',"%{$search}%")
                                      ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                              ->groupBy("dev_transferencia_det.ID_LOTE_ENVIA")
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

                $nestedData['COD_PROD'] = $post->COD_PROD;
                $nestedData['LOTE'] = $post->LOTE;
                $nestedData['DESCRIPCION'] = $post->DESCRIPCION;
                $nestedData['CANTIDAD'] = $post->CANTIDAD;

                $nestedData['COSTO_UNIT'] = Common::precio_candec($post->COSTO_UNIT,$post->MONEDA);
                $nestedData['COSTO_TOTAL'] = Common::precio_candec($post->COSTO_TOTAL,$post->MONEDA);

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
        public static function rechazar_dev_transferencia($data){

        /*  --------------------------------------------------------------------------------- */
        $user = auth()->user();
         $dia = date("Y-m-d");
        $hora = date("H:i:s");
        // INICIAR VARIABLES 

        $codigo = $data["codigo"];

        /*  --------------------------------------------------------------------------------- */

        // REVISAR ESTATUS 

        $estatus = DevTransferencia::verificar_estatus($codigo);

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EL ESTATUS SE ENCUENTRA EN 1 PARA SER RECHAZADO 

        if ($estatus === false or $estatus === 0 or $estatus === 2) {
            return ["response" => false, "statusText" => "Ya se encuentra procesada !"];
        }

        /*  --------------------------------------------------------------------------------- */

        // LA TRANSFERENCIA SOLO SERA MODIFICADA SI SE ENCUENTRA EN ESTATUS 1 QUE ES ENVIADO

        if ($estatus === 1) {

            DevTransferencia::where('ID','=', $codigo)
            ->update([
                'ESTATUS' => 0,
                'FECMODIF'=>$dia,
                'HORMODIF'=>$hora,
                'USERM'=>$user->id_sucursal
            ]);

        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return ["response"=>true];

        /*  --------------------------------------------------------------------------------- */

    }
}
