<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Producto;
use App\Empleado_Tiene_Gondola;
use App\Gondola_Tiene_Seccion;
use App\Piso;
use App\Sector;
use App\Gondola_Tiene_Piso;
use App\Gondola_Tiene_Sector;
use App\Parametro;
use App\ComprasDet;

class Gondola extends Model
{

    protected $connection = 'retail';
    protected $table = 'gondolas';
    protected $primaryKey ='Codigo';
    public $timestamps = false;

    const CREATED_AT = 'FECALTAS';
    const UPDATED_AT = 'FECMODIF';

    public static function obtener_gondolas(){
        
        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODAS LAS GONDOLAS

        $gondolas = Gondola::select(DB::raw('ID, CODIGO, DESCRIPCION'))
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($gondolas) {
            return ['gondolas' => $gondolas];
        } else {
            return ['gondolas' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
    
    public static function filtrar_gondola($datos){
        
        $user = auth()->user();

        // OBTENER TODOS LOS DATOS DEL TALLE

        $gondolas = Gondola::leftjoin('gondola_tiene_seccion', 'gondola_tiene_seccion.ID_GONDOLA', '=', 'GONDOLAS.ID')
            ->select(DB::raw('
                gondolas.ID as CODIGO,
                DESCRIPCION, 
                gondola_tiene_seccion.ID_SECCION AS ID_SECCION'))
        ->where('gondolas.ID_SUCURSAL','=',$user->id_sucursal)
        ->Where('gondolas.ID','=',$datos['id'])
        ->get()
        ->toArray();

         if(count($gondolas) <= 0){

           return ["response" => false];
        }

        if($datos['rack'] === 'SI'){

            $pisos = Gondola_Tiene_Piso::select(DB::raw('PISOS.ID,PISOS.NRO_PISO'))
                ->leftjoin('PISOS','PISOS.ID','=','GONDOLA_TIENE_PISOS.FK_PISO')
            ->where('GONDOLA_TIENE_PISOS.FK_GONDOLA','=',$datos['id'])
            ->orderBy('PISOS.DESCRIPCION')
            ->get()
            ->toArray();

            $sectores = Gondola_Tiene_Sector::select(DB::raw('SECTORES.ID,SECTORES.DESCRIPCION'))
                ->leftjoin('SECTORES','SECTORES.ID','=','GONDOLA_TIENE_SECTORES.FK_SECTOR')
            ->where('GONDOLA_TIENE_SECTORES.FK_GONDOLA','=',$datos['id'])
            ->orderBy('SECTORES.DESCRIPCION')
            ->get()
            ->toArray();

            /*  --------------------------------------------------------------------------------- */

            // RETORNAR EL VALOR

            return ["response" => true,"Gondolas" => $gondolas,'Pisos' => $pisos,'Sectores' => $sectores];

        }else{

            return ["response" => true,"Gondolas" => $gondolas];
        }
    }
    
    public static function gondolas_datatable($request){

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
            0 => 'codigo', 
            1 => 'descripcion',
            2 => 'seccion'
        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = Gondola::where('id_sucursal', '=', $user->id_sucursal)->count();  

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE VALOR EN VARIABLE SEARCH

        if(empty($request->input('search.value'))){            

            //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

            $posts = Gondola::select(DB::raw('GONDOLAS.ID AS CODIGO,
                    GONDOLAS.DESCRIPCION, 
                    IFNULL(SECCIONES.DESCRIPCION,"NO POSEE") AS SECCION'))
                ->leftjoin('gondola_tiene_seccion', 'gondola_tiene_seccion.ID_GONDOLA', '=', 'GONDOLAS.ID')
                ->leftjoin('secciones', 'secciones.ID', '=', 'gondola_tiene_seccion.ID_SECCION')
            ->where('Gondolas.id_sucursal','=',$user->id_sucursal)
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();

        } else {

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            // CARGAR LOS PRODUCTOS FILTRADOS EN DATATABLE

            $posts = Gondola::select(DB::raw('GONDOLAS.ID AS CODIGO,
                    GONDOLAS.DESCRIPCION, 
                    IFNULL(SECCIONES.DESCRIPCION,"NO POSEE") AS SECCION'))
                ->leftjoin('gondola_tiene_seccion', 'gondola_tiene_seccion.ID_GONDOLA', '=', 'GONDOLAS.ID')
                ->leftjoin('secciones', 'secciones.ID', '=', 'gondola_tiene_seccion.ID_SECCION')
            ->where('GONDOLAS.id_sucursal','=',$user->id_sucursal)
            ->where(function ($query) use ($search) {
                $query->where('GONDOLAS.ID','LIKE',"%{$search}%")
                      ->orWhere('GONDOLAS.DESCRIPCION', 'LIKE',"%{$search}%")
                      ->orWhere('SECCIONES.DESCRIPCION', 'LIKE',"%{$search}%");
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = count($posts);
        }

        $data = array();

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts)){

            foreach ($posts as $post){

                // CARGAR EN LA VARIABLE 

                $nestedData['CODIGO'] = $post->CODIGO;
                $nestedData['DESCRIPCION'] = $post->DESCRIPCION;
                $nestedData['SECCION'] = $post->SECCION;

                $data[] = $nestedData;

                /*  --------------------------------------------------------------------------------- */

            }
        }

        // PREPARAR EL ARRAY A ENVIAR 

        $json_data = array(
            "draw"            => intval($request->input('draw')),  
            "recordsTotal"    => intval($totalData),  
            "recordsFiltered" => intval($totalFiltered), 
            "data"            => $data   
        );
        
        // CONVERTIR EN JSON EL ARRAY Y ENVIAR 

       return $json_data; 
    }
    
    public static function obtener_codigo(){
                
        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODAS LAS GONDOLAS

        $gondolas = Gondola::select('ID AS CODIGO')
        ->where('id_sucursal','=',$user->id_sucursal)
        ->orderby('CODIGO','DESC')
        ->limit(1)
        ->get()->toArray();
      
        /*  --------------------------------------------------------------------------------- */
        
        $codigo = [];

        /*  --------------------------------------------------------------------------------- */
        // RETORNAR EL VALOR
       
        if ($gondolas) {

            return ['Gondola' => $gondolas];
        } else {

            $codigo[0]['CODIGO'] = $user->id_sucursal*1000;
            return ['Gondola' => $codigo];
        }
    }

    public static function gondola_guardar($datos){

        $user = auth()->user();
        $dia = date("Y-m-d H:i:s");
        $hora = date("H:i:s");

        $codigo_gondola = $datos['data']['Codigo'];
        $seccion_id = $datos['data']['SeccionGuardar'];
      

        /*  --------------------------------------------------------------------------------- */

        try { 

            DB::connection('retail')->beginTransaction();

            if($datos['data']['Existe'] === false){

                $gondolas = Gondola::insertGetId([
                    'DESCRIPCION' => $datos['data']['Descripcion'], 
                    'FK_USER_CR' => $user->id,
                    'FECALTAS' => $dia,
                    'ID_SUCURSAL' => $user->id_sucursal
                ]);
             
                if($seccion_id !== "null" &&  $seccion_id !== '' && $seccion_id !== null){

                    $gondola_id = $gondolas;
                    Gondola_Tiene_Seccion::asignar_seccion($gondola_id, $seccion_id, $user->id_sucursal);
                }

                if($datos['data']['Rack'] === 'SI'){

                    foreach ($datos['data']['Piso'] as $key => $value) {
                        Gondola_Tiene_Piso::guardar_referencia(["FK_GONDOLA"=>$gondolas,"FK_PISO"=>$value]);
                    }

                    foreach ($datos['data']['Sector'] as $key => $value) {
                        Gondola_Tiene_Sector::guardar_referencia(["FK_GONDOLA"=>$gondolas,"FK_SECTOR"=>$value]);
                    }
                }      
               
            }else{
                
                //GONDOLAS UPDATE

                if($datos['data']['Rack'] === 'SI'){

                    //SI EXISTEN PISOS DESMARCADOS
                  
                    if(isset($datos['data']['PisosDesmarcados'][0]["ID"])){

                        //VERIFICAR SI EXISTEN CAJAS EN LAS GONDOLAS Y EN LOS PISOS DESMARCADOS POR COMPRAS.

                        $compra = Gondola::VerificarComprasPisos($datos['data']['PisosDesmarcados'],$codigo_gondola);

                        if(!$compra["response"]){
                            return $compra;
                        }

                        //VERIFICAR SI EXISTEN CAJAS EN LAS GONDOLAS Y EN EL PISO DESMARCADO POR TRANSFERENCIAS.

                        $transferencias = Gondola::VerificarTransferenciasPisos($datos['data']['PisosDesmarcados'],$codigo_gondola);

                        if(!$transferencias["response"]){
                            return $transferencias;
                        }

                        //ELIMINAR LAS REFERENCIAS DE LA GONDOLA CON LOS PISOS DESMARCADOS.

                        Gondola::EliminarReferenciaPiso($datos['data']['PisosDesmarcados'],$codigo_gondola);
                      
                    }
                    
                    //-----------------------------------------------------------------------------------------------------------------------

                    //SI EXISTEN SECTORES DESMARCADOS

                    if(isset($datos['data']['SectoresDesmarcados'][0]["ID"])){

                        //VERIFICAR SI EXISTEN CAJAS EN LAS GONDOLAS Y EN LOS SECTORES DESMARCADOS POR COMPRAS.

                        $compra = Gondola::VerificarComprasSectores($datos['data']['SectoresDesmarcados'],$codigo_gondola);

                        if(!$compra["response"]){
                            return $compra;
                        }

                        //VERIFICAR SI EXISTEN CAJAS EN LAS GONDOLAS Y EN LOS SECTORES DESMARCADOS POR TRANSFERENCIAS.

                        $transferencias = Gondola::VerificarTransferenciasSectores($datos['data']['SectoresDesmarcados'],$codigo_gondola);

                        if(!$transferencias["response"]){
                            return $transferencias;
                        }
                        //ELIMINAR LAS REFERENCIAS DE LA GONDOLA CON LOS PISOS DESMARCADOS.

                        Gondola::EliminarReferenciaSector($datos['data']['SectoresDesmarcados'],$codigo_gondola);
                    }

                    //------------------------------------------------------------------------------------------------------------------------

                    //SI EXISTEN NUEVOS PISOS

                    if(isset($datos['data']['PisosNuevos'][0]["ID"])){

                        foreach($datos['data']['PisosNuevos'] as $key => $value){

                            Gondola_Tiene_Piso::guardar_referencia([
                                "FK_GONDOLA" => $codigo_gondola,
                                "FK_PISO" => $value["ID"]
                            ]);
                        }
                    }

                    //------------------------------------------------------------------------------------------------------------------------

                    // SI EXISTEN NUEVOS SECTORES

                    if(isset($datos['data']['SectoresNuevos'][0]["ID"])){

                        foreach($datos['data']['SectoresNuevos'] as $key => $value){

                            Gondola_Tiene_Sector::guardar_referencia([
                                "FK_GONDOLA" => $codigo_gondola,
                                "FK_SECTOR" => $value["ID"]
                            ]);
                        }
                    }
                }

                $gondolas = Gondola::where('ID', '=' ,$codigo_gondola)
                ->update([
                    'DESCRIPCION' => $datos['data']['Descripcion'], 
                    'FK_USER_MD' => $user->id,
                    'FECMODIF' => $dia
                ]);

                Gondola_Tiene_Seccion::modificar_seccion($seccion_id, $codigo_gondola, $user->id_sucursal);
            }
            
            /*  --------------------------------------------------------------------------------- */

            DB::connection('retail')->commit();
            
            return ["response" => true];
           
        }catch(Exception $ex){

            DB::connection('retail')->rollBack(); 

            if($ex->errorInfo[1] === 1062){
                return ["response" => false,'statusText' => '¡Este código de góndola ya fué registrado!'];
            }else{
                return ["response" => false,'statusText' => $ex->getMessage()];
            }
        }
    }
    
    public static function gondola_eliminar($datos){

        $user = auth()->user();
        $dia = date("Y-m-d");
        $hora = date("H:i:s");

        $codigo_gondola = $datos['data']['Codigo'];

        /*  --------------------------------------------------------------------------------- */

        if($datos['data']['Existe'] === true){

            $producto = Producto::select('Productos.CODIGO')
                ->leftjoin('GONDOLA_TIENE_PRODUCTOS','GONDOLA_TIENE_PRODUCTOS.GONDOLA_COD_PROD','=','productos.CODIGO')
            ->Where('GONDOLA_TIENE_PRODUCTOS.ID_GONDOLA','=',$codigo_gondola)
            ->limit(1)
            ->get()
            ->toArray();

            if(count($producto) > 0){

                return ["response" => false, "statusText" => "Este código de góndola no existe o tiene productos asignados"];
            }
            
            $empleado = Empleado_Tiene_Gondola::select('empleado_tiene_gondola.FK_GONDOLA')
                ->leftjoin('Gondolas','Gondolas.ID','=','empleado_tiene_gondola.FK_GONDOLA')
            ->Where('empleado_tiene_gondola.FK_GONDOLA','=',$codigo_gondola)
            ->limit(1)
            ->get()
            ->toArray();

            if(count($empleado) > 0){

                return ["response" => false, "statusText" => "Este código de góndola no existe o tiene empleados asignados"];
            }

            gondola_tiene_seccion::where('ID_GONDOLA', '=', $codigo_gondola)
            ->delete();  

            $gondola = Gondola::where('ID', $codigo_gondola)
            ->delete();

            /*  --------------------------------------------------------------------------------- */

            return ["response" => true];

        }else{
            return ["response" => false, "statusText" => "Este código góndola no existe"];
        }
    }

    public static function obtener_gondolas_por_producto($codigo){

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODAS LAS GONDOLAS

        $gondolas = Gondola::select(DB::raw('
                GONDOLAS.ID, 
                CODIGO, 
                DESCRIPCION,
                GONDOLA_TIENE_PRODUCTOS.FECALTAS'))
            ->rightjoin('GONDOLA_TIENE_PRODUCTOS', 'GONDOLA_TIENE_PRODUCTOS.ID_GONDOLA', '=', 'GONDOLAS.ID')
        ->where('GONDOLAS.ID_SUCURSAL', '=', $user->id_sucursal)
        ->where('GONDOLA_TIENE_PRODUCTOS.GONDOLA_COD_PROD', '=', $codigo)
        ->get();

        $deposito = (Parametro::mostrarParametro())["parametros"][0]->RACK;
        
        if($deposito === 'SI'){

            $compras = ComprasDet::select(DB::raw('SUBSTR(COMPRAS.FECALTAS, 1, 10) AS FECHA, 
                    GONDOLAS.DESCRIPCION AS GONDOLA, 
                    COMPRAS.CODIGO AS CODIGO,
                    COMPRAS.NRO_FACTURA AS NRO_CAJA,
                    SECCIONES.DESCRIPCION AS SECCION,
                    PISOS.NRO_PISO AS PISO,
                    SECTORES.DESCRIPCION AS SECTOR,
                    LOTES.LOTE AS LOTE'))
                ->leftjoin('COMPRAS', 'COMPRAS.ID', '=', 'COMPRASDET.FK_COMPRAS')
                ->rightjoin('COMPRAS_DEPOSITO', 'COMPRAS_DEPOSITO.FK_COMPRA', '=', 'COMPRAS.ID')
                ->leftjoin('GONDOLAS', 'GONDOLAS.ID','=', 'COMPRAS_DEPOSITO.FK_GONDOLA')
                ->leftjoin('SECTORES', 'SECTORES.ID','=', 'COMPRAS_DEPOSITO.FK_SECTOR')
                ->leftjoin('SECCIONES', 'SECCIONES.ID','=', 'COMPRAS_DEPOSITO.FK_SECCION')
                ->leftjoin('PISOS', 'PISOS.ID','=', 'COMPRAS_DEPOSITO.FK_PISO')
                ->leftjoin('LOTE_TIENE_COMPRASDET', 'LOTE_TIENE_COMPRASDET.ID_COMPRAS_DET','=', 'COMPRASDET.ID')
                ->leftjoin('LOTES', 'LOTES.ID','=', 'LOTE_TIENE_COMPRASDET.ID_LOTE')
            ->where('COMPRASDET.COD_PROD', '=', $codigo)
            ->Where('COMPRASDET.ID_SUCURSAL', '=', $user->id_sucursal)
            ->groupBy('COMPRASDET.ID')
            ->get();

            $transferencias = DB::connection('retail')->table('TRANSFERENCIAS_DET')
                ->select(DB::raw('SUBSTR(TRANSFERENCIAS.FECMODIF, 1, 10) AS FECHA,
                    TRANSFERENCIAS.ID,
                    TRANSFERENCIAS.CODIGO,
                    GONDOLAS.DESCRIPCION AS GONDOLA,
                    TRANSFERENCIAS_DEPOSITO.NRO_CAJA AS NRO_CAJA,
                    SECCIONES.DESCRIPCION AS SECCION,
                    PISOS.NRO_PISO AS PISO,
                    SECTORES.DESCRIPCION AS SECTOR,
                    LOTES.LOTE AS LOTE'))
                ->leftJoin('TRANSFERENCIAS', 'TRANSFERENCIAS.ID', '=', 'TRANSFERENCIAS_DET.FK_TRANSFERENCIA')
                ->rightjoin('TRANSFERENCIAS_DEPOSITO','TRANSFERENCIAS_DEPOSITO.FK_TRANSFERENCIA','=','TRANSFERENCIAS.ID')
                ->leftjoin('GONDOLAS', 'GONDOLAS.ID', '=', 'TRANSFERENCIAS_DEPOSITO.FK_GONDOLA')
                ->leftjoin('SECTORES', 'SECTORES.ID','=', 'TRANSFERENCIAS_DEPOSITO.FK_SECTOR')
                ->leftjoin('SECCIONES', 'SECCIONES.ID','=', 'TRANSFERENCIAS_DEPOSITO.FK_SECCION')
                ->leftjoin('PISOS', 'PISOS.ID','=', 'TRANSFERENCIAS_DEPOSITO.FK_PISO')
                ->leftjoin('LOTE_TIENE_TRANSFERENCIADET', 'LOTE_TIENE_TRANSFERENCIADET.ID_TRANSFERENCIA_DET','=', 'TRANSFERENCIAS_DET.ID')
                ->leftjoin('LOTES', 'LOTES.ID','=', 'LOTE_TIENE_TRANSFERENCIADET.ID_LOTE')
            ->where('TRANSFERENCIAS_DET.CODIGO_PROD', '=', $codigo)
            ->where('TRANSFERENCIAS.SUCURSAL_DESTINO', '=', $user->id_sucursal)
            ->where('TRANSFERENCIAS.ESTATUS', '=', 2)
            ->groupBy('TRANSFERENCIAS_DET.ID')
            ->get();

            /*  --------------------------------------------------------------------------------- */
           
            // RETORNAR EL VALOR

            return ['response' => true, 'gondolas' => $gondolas, 'COMPRAS' => $compras, 'TRANSFERENCIAS' => $transferencias];

        }else{

            /*  --------------------------------------------------------------------------------- */
           
            // RETORNAR EL VALOR

            return ['response' => true, 'gondolas' => $gondolas];
        }
    }
    
    public static function configuracion_inicio_gondola(){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        // OBTENER TODOS LOS PISOS

        $pisos = Piso::Select(DB::raw('ID,DESCRIPCION,NRO_PISO'))
        ->orderBy('NRO_PISO')
        ->get();

        /*  --------------------------------------------------------------------------------- */
        // OBTENER TODOS LOS PISOS

        $sectores = Sector::Select(DB::raw('ID,DESCRIPCION'))
        ->orderBy('DESCRIPCION')
        ->get();

        /*  --------------------------------------------------------------------------------- */
        // RETORNAR EL VALOR

        if(count($pisos) > 0 && count($sectores) > 0){

            return ['response' => true, 'pisos' => $pisos,'sectores' => $sectores];
         }else{
            return ['response' => false];
         }
    }

    public static function VerificarComprasPisos($pisos, $gondola){

        $response = true; 
        $statusText = '';
                          
        foreach ($pisos  as $key => $value){

            $compras = DB::connection('retail')->table('COMPRAS_DEPOSITO')->select('ID')
            ->where('FK_GONDOLA','=',$gondola)
            ->where('FK_PISO','=',$value["ID"])
            ->limit(1)
            ->get()
            ->toArray();

            if(count($compras)>0){
                $response = false;
                $statusText = $statusText.'La Góndola : '.$gondola. ' Con Piso : '.$value["NRO_PISO"].' Ya Posee Cajas En Compras <br>';
            }
        }

        if(!$response){
            return["response" => false, "statusText" =>  '<br>'.$statusText];
        }else{
            return["response" => true];
        }
    }

    public static function VerificarTransferenciasPisos($pisos, $gondola){

        $response = true; 
        $statusText = '';

        foreach ($pisos  as $key => $value){

            $transferencias = DB::connection('retail')->table('TRANSFERENCIAS_DEPOSITO')->select('ID')
            ->where('FK_GONDOLA','=',$gondola)
            ->where('FK_PISO','=',$value["ID"])
            ->limit(1)
            ->get()
            ->toArray();

            if(count($transferencias) > 0){

                $response = false;
                $statusText = $statusText.'La Góndola : '.$gondola. ' Con Piso : '.$value["NRO_PISO"].' Ya Posee Cajas En Transferencias <br>';
            }
        }

        if(!$response){
            return["response"=>false, "statusText"=> '<br>'.$statusText];
        }else{
            return["response"=>true];
        }
    }
    
    public static function EliminarReferenciaPiso($pisos, $gondola){

        foreach ($pisos  as $key => $value){
            Gondola_Tiene_Piso::eliminar_referencia([
                "FK_GONDOLA" => $gondola,
                "FK_PISO" => $value["ID"]
            ]);
        }

        return;
    }

    public static function VerificarComprasSectores($sectores, $gondola){

        $response = true; 
        $statusText = '';                      

        foreach ($sectores  as $key => $value){

            $compras = DB::connection('retail')->table('COMPRAS_DEPOSITO')->select('ID')
            ->where('FK_GONDOLA','=',$gondola)
            ->where('FK_SECTOR','=',$value["ID"])
            ->limit(1)
            ->get()
            ->toArray();

            if(count($compras)>0){
                $response = false;
                $statusText = $statusText.'La Góndola : '.$gondola. ' Con Sector : '.$value["DESCRIPCION"].' Ya Posee Cajas En Compras <br>';
            }
        }

        if(!$response){
            return["response" => false, "statusText" =>  '<br>'.$statusText];
        }else{
            return["response" => true];
        }
    }

    public static function VerificarTransferenciasSectores($sectores, $gondola){

        $response = true; 
        $statusText = ''; 

        foreach ($sectores  as $key => $value){

            $transferencias = DB::connection('retail')->table('TRANSFERENCIAS_DEPOSITO')->select('ID')
            ->where('FK_GONDOLA','=',$gondola)
            ->where('FK_SECTOR','=',$value["ID"])
            ->limit(1)
            ->get()
            ->toArray();

            if(count($transferencias)>0){
                $response = false;
                $statusText = $statusText.'La Góndola : '.$gondola. ' Con Sector : '.$value["DESCRIPCION"].' Ya Posee Cajas En Transferencias <br>';
            }
        }

        if(!$response){
            return["response" => false, "statusText" => '<br>'.$statusText];
        }else{
            return["response" => true];
        }
    }

    public static function EliminarReferenciaSector($pisos, $gondola){

        foreach ($pisos  as $key => $value){

            Gondola_Tiene_Sector::eliminar_referencia([
                "FK_GONDOLA" => $gondola,
                "FK_SECTOR" => $value["ID"]
            ]);
        }
        
        return;
    }

    public static function obtener_gondolas_encargada(){
        
        $user = auth()->user();

        // OBTENER TODAS LAS GONDOLAS

        $gondolas = Gondola::Select(Db::raw('GONDOLAS.ID, 
                GONDOLAS.CODIGO, 
                GONDOLAS.DESCRIPCION'))
            ->Rightjoin('Gondola_Tiene_Seccion','Gondola_Tiene_Seccion.ID_GONDOLA','=','GONDOLAS.ID')
            ->Rightjoin('USERS_TIENE_SECCION','USERS_TIENE_SECCION.FK_SECCION','=','Gondola_Tiene_Seccion.ID_SECCION')
        ->where('GONDOLAS.ID_SUCURSAL','=',$user->id_sucursal)
        ->where('USERS_TIENE_SECCION.FK_USER','=',$user->id)
        ->orderby(DB::raw('TRIM(GONDOLAS.DESCRIPCION)'))
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($gondolas) {
            return ['gondolas' => $gondolas];
        } else {
            return ['gondolas' => 0];
        }
    }
}
