<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Transferencia;
use App\Venta;
use App\Imagen;
use App\ImagenesWeb;
use App\Empleado_Tiene_Gondola;

class Empleado extends Model
{   
    protected $connection = 'retail';
    protected $table = 'empleados';
    public $timestamps = false;

     public static function mostrarEmpleado($data)
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER TODOS LOS EMPLEADOS

    	$empleados = Empleado::select(DB::raw('CODIGO, NOMBRE, CI, DIRECCION, ID_SUCURSAL'))
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if (count($empleados) > 0) {
        	return ['empleados' => $empleados];
        } else {
        	return ['empleados' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function encontrarEmpleado($data)
    {

    	/*  --------------------------------------------------------------------------------- */
    	   
    	// OBTENER TODAS LAS SUCURSALES

    	$empleado = Empleado::select(DB::raw('ID, NOMBRE, CI, DIRECCION, ID_SUCURSAL'))
        ->where('ID', '=', $data['codigo'])
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
        
        if (count($empleado) > 0) {
            return ['empleado' => $empleado];
        } else {
            return ['empleado' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
    public static function empleados_datatable($request){

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 
        $user = auth()->user();
        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'ID', 
                            1 => 'NOMBRE',
                            2 => 'CODIGO',
                            3 => 'CI',
                            4 => 'CIUDAD',
                            5 => 'DIRECCION',
                            6 => 'TELEFONO',
                            7 => 'CARGO'
                        );
        // CONTAR LA CANTIDAD DE EMPLEADOS ENCONTRADAS 
        $totalData = Empleado::
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

            //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

            $posts = Empleado::select(DB::raw('ID, NOMBRE, CODIGO, CI, CIUDAD, DIRECCION, TELEFONO, CARGO'))
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

            // CARGAR LOS PRODUCTOS FILTRADOS EN DATATABLE

            $posts = Empleado::select(DB::raw('ID, NOMBRE, CODIGO, CI, CIUDAD, DIRECCION, TELEFONO, CARGO'))
                            ->where('ID_SUCURSAL','=', $user->id_sucursal)
                            ->where(function ($query) use ($search) {
                                $query->where('CI','LIKE',"%{$search}%")
                                      ->orWhere('CODIGO', 'LIKE',"%{$search}%")
                                      ->orWhere('NOMBRE', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = Empleado::where(function ($query) use ($search) {
                                $query->where('CI','LIKE',"%{$search}%")
                                      ->orWhere('CODIGO', 'LIKE',"%{$search}%")
                                      ->orWhere('NOMBRE', 'LIKE',"%{$search}%");
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

                $nestedData['CODIGO'] = $post->CODIGO;
                $nestedData['CI'] = $post->CI;
                $nestedData['NOMBRE'] = utf8_encode($post->NOMBRE);
                $nestedData['ID'] = $post->ID;
                $nestedData['DIRECCION'] = utf8_encode($post->DIRECCION);
                $nestedData['CIUDAD'] = utf8_encode($post->CIUDAD);
                $nestedData['TELEFONO'] = $post->TELEFONO;
                $nestedData['CARGO'] = $post->CARGO;
                
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
     public static function nuevoEmpleado(){
         $user = auth()->user();

        //OBTENER EL ULTIMO CODIGO DE CLIENTE

        $empleado = empleado::select(DB::raw('IFNULL(CODIGO,0) AS CODIGO'))
                        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
                        ->orderby('CODIGO','DESC')
                        ->limit(1)
                        ->get()
                        ->toArray();
        
        // RETORNAR EL VALOR
        if(count($empleado)>0){
            return ["empleado" => $empleado[0]["CODIGO"]];
        }else{

            return ["empleado" => 0];
        }

     }
     public static function filtrarEmpleado($datos){


        $empleados=empleado::select(DB::raw('CODIGO, 
            NOMBRE, 
            DIRECCION, 
            CIUDAD, 
            TELEFONO, 
            CARGO, 
            SUBSTR(FECHA_NAC,1,10) AS FECHA_NAC, 
            CI')
        )
        ->where('ID','=', $datos['data'])
        ->get();
        $imagen=Imagen::obtenerImagenURL_Empleado($datos['data']);
        $gondola=DB::connection('retail')
            ->table('empleado_tiene_gondola')
            ->select(DB::raw('empleado_tiene_gondola.FK_GONDOLA AS ID, GONDOLAS.DESCRIPCION AS DESCRIPCION'))
            ->leftjoin('GONDOLAS', 'GONDOLAS.ID', '=', 'empleado_tiene_gondola.FK_GONDOLA')
            ->where('FK_EMPLEADO', '=', $datos['data'])
            ->get();
            
        return['empleado'=> $empleados, 'gondola' => $gondola, 'imagen'=> $imagen['imagen']];
    }
    public static function guardarEmpleado($datos){

        $user = auth()->user();
        $dia = date("Y-m-d");
        $hora = date("H:i:s");
        $img = preg_replace('#^data:image/[^;]+;base64,#', '', $datos["data"]["imagen"]);
        $blob = '';

        $gondola = $datos['data']['seleccion_gondola'];
       

        try{

             $fecha_nac = date('Y-m-d', strtotime($datos['data']['fecha_nac']));

            DB::connection('retail')->beginTransaction();

            if($datos['data']['btn_guardar']==true){

                $empleado=empleado::insertGetId([
                    'CODIGO'=> $datos['data']['codigo'],
                    'NOMBRE'=> $datos['data']['nombre'],
                    'CI'=> $datos['data']['cedula'],
                    'DIRECCION'=>$datos['data']['direccion'],
                    'CIUDAD'=>$datos['data']['ciudad'],
                    'FECHA_NAC'=>$fecha_nac,
                    'TELEFONO'=>$datos['data']['telefono'],
                    'CARGO'=>$datos['data']['cargo'],
                    'USER'=>strtoupper($user->name),
                    'FECALTAS'=>$dia,
                    'HORALTAS'=>$hora,
                    'ID_SUCURSAL'=>$user->id_sucursal]);

                

                DB::connection('retail')->commit();

                if(isset($gondola[0]['ID'])){

                    $id_empleado = $empleado;

                    Empleado_Tiene_Gondola::asignar_gondola($gondola, $id_empleado);
                }

                if ($img !== "") { 
                    Imagen::guardar_imagen_empleado([
                        'CODIGO' => $empleado,
                        'PICTURE' => $img
                    ]);
                }

                return['response'=>true];

            }else{
                $img = preg_replace('#^data:image/[^;]+;base64,#', '', $datos["data"]["imagen"]);

                if ($datos["imagen"] = "/images/SinImagen.png?343637be705e4033f95789ab8ec70808") {
                    $datos["imagen"] = "";
                }

                $empleado = Empleado::Where('CODIGO','=',$datos['data']['codigo'])->where('ID_SUCURSAL', '=', $user->id_sucursal)
                    ->update(['CI' => $datos['data']['cedula'],
                    'NOMBRE'=> $datos['data']['nombre'],
                    'DIRECCION' => $datos['data']['direccion'],
                    'CIUDAD' => $datos['data']['ciudad'],
                    'TELEFONO' => $datos['data']['telefono'],
                    'CARGO'=>$datos['data']['cargo'],
                    'FECHA_NAC'=>$fecha_nac,
                    'USERM'=>strtoupper($user->name),
                    'FECMODIF'=>$dia,
                    'HORMODIF'=>$hora]);

                $id_empleado = Empleado::select('ID')
                    ->where('CODIGO','=',$datos['data']['codigo'])
                    ->where('ID_SUCURSAL','=',$user->id_sucursal)
                    ->get()
                    ->toArray();

                if ($img !== "") { 
                    Imagen::guardar_imagen_empleado([
                        'CODIGO' => $id_empleado[0]['ID'],
                        'PICTURE' => $img
                    ]);
                }

                Empleado_Tiene_Gondola::modificar_gondola($gondola, $id_empleado[0]['ID']);

                DB::connection('retail')->commit();
                return['response'=>true];
            }
        }
        catch(Exception $ex){
            DB::connection('retail')->rollBack();
            if($ex->errorInfo[1]==1062){
                return ["response"=>false,'statusText'=>'¡Este Código de empleado ya fue registrado!'];
            }else{
                return ["response"=>false,'statusText'=>$ex->getMessage()];
            }
        }
    }



    public static function eliminarEmpleado($datos){
        $user = auth()->user();

        if($datos['data']['btn_guardar']==false){
            
            $transferencia = Empleado::verificarTransferencia($datos['data']['codigo']);

            if($transferencia['response']==false){
                return $transferencia;
            }

            $venta = Empleado::verificarVenta($datos['data']['codigo']);

            if($venta['response']==false){
                return $venta;
            }
            $id_empleado = Empleado::select('ID')
                    ->where('CODIGO','=',$datos['data']['codigo'])
                    ->where('ID_SUCURSAL','=',$user->id_sucursal)
                    ->get()
                    ->toArray();
            $gondola = Empleado_Tiene_Gondola::where('FK_EMPLEADO','=',$id_empleado[0]['ID'])->delete();
            $empleado = Empleado::where('CODIGO','=',$datos['data']['codigo'])
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)->delete();

            return ['response'=>true];
        }else{
            return["response"=>false, 'statusText'=> 'No existe este empleado '.$datos['data']['codigo']];
        }
    }
    public static function verificarTransferencia($codigo){

        $user = auth()->user();

        $empleado = Transferencia::select('ID')
        ->where('ENVIA', '=', $codigo)
        ->orWhere('TRANSPORTA', '=', $codigo)
        ->orwhere('RECIBE', '=', $codigo)
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->limit(1)
        ->get();

         // RETORNAR EL VALOR

        if(count($empleado) > 0 && !empty($codigo)){

            return  ["response"=>false,'statusText'=> 'Existe una transferencia hecho por este empleado.'];
        } else {

            // MUESTRA QUE NO EXISTE

            return ["response"=>true];

        }
    }

    public static function verificarVenta($codigo){
        $user = auth()->user();

        $empleado = Venta::select('ID')
        ->where('VENDEDOR', '=', $codigo)
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->limit(1)
        ->get();

        if(count($empleado) > 0 && !empty($codigo)){

            return  ["response"=>false,'statusText'=> 'Existe una venta hecho por este empleado.'];
        } else {

            // MUESTRA QUE NO EXISTE

            return ["response"=>true];
        }
    }
    
}