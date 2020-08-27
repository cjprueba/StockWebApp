<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Mpdf\Mpdf;
use Fpdf\Fpdf;
use Luecano\NumeroALetras\NumeroALetras;
use App\Parametro;

class Cupon extends Model
{
	    protected $connection = 'retail';
	    protected $table = 'CUPONES';
	    public $timestamps=false;
    //
public static function crear_cupon()
    {
	       $user= auth()->user();

	      $codigo = Cupon::generateRandomString(6);
	      return $codigo;

    }
    public static function conseguir_cupon($datos){
    	  $user= auth()->user();
    	  $cupon=Cupon::select(DB::raw('CODIGO,TIPO_CUPON,FECHA_CADUCIDAD,IMPORTE,DESCRIPCION,USO_LIMITE,USO,GASTO_MAX,GASTO_MIN,USO_LIMITE_CLIENTE,EXCLUIR_ARTICULOS_CON_DESCUENTO'))
          ->where('CODIGO','=', $datos["data"])
          ->where('ID_SUCURSAL','=', $user->id_sucursal)
          ->get()->toArray();
           return["datos"=>$cupon,"response"=>true];
    }

    public  static function generateRandomString($length = 20) {
    	 $user = auth()->user();
    	 $bol=false;
       
        while ($bol==false) {

        	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        	$charactersLength = strlen($characters);
        	$randomString = $user->id_sucursal.'-';
        	for ($i = 0; $i < $length; $i++) {
            	$randomString .= $characters[rand(0, $charactersLength - 1)];

        	}

	       	$cupon= Cupon::Select('CODIGO')
	        ->WHERE('CODIGO','=',$randomString)
	        ->where('ID_SUCURSAL','=',$user->ID_SUCURSAL)
	        ->get()
	        ->toArray();

	        if(count($cupon)==0){
	        	$bol=true;
	        	return $randomString;
	        }

        }
       
        
      
    }
     public static function guardar_cupon($datos){
	     	 $user = auth()->user();
	        $dia = date("Y-m-d");
	        $hora = date("H:i:s");

     	 try {
     	 	 DB::connection('retail')->beginTransaction();
     	 	  $Cupon=Cupon::insertGetId(
         ['CODIGO'=> $datos['data']['Codigo'],
          'DESCRIPCION'=> $datos['data']['Descripcion'],
          'TIPO_CUPON'=> $datos['data']['Tipo'],
          'IMPORTE'=> $datos['data']['Importe'],
          'USO_LIMITE'=> $datos['data']['Limite_uso_cupon'],
          'USO_LIMITE_CLIENTE'=> $datos['data']['Limite_uso_cliente'],
          'FECHA_CADUCIDAD'=> $datos['data']['Fecha_caducidad'],
          'EXCLUIR_ARTICULOS_CON_DESCUENTO'=> $datos['data']['Excluir_articulos_con_descuento'],
          'GASTO_MAX'=> $datos['data']['Gasto_maximo'],
          'GASTO_MIN'=> $datos['data']['Gasto_minimo'],
          'USER'=>$user->id,
          'ID_SUCURSAL'=>$user->id_sucursal,
          'FECALTAS'=>$dia,
          'HORALTAS'=>$hora]);
     	    DB::connection('retail')->commit();	  
		 return ["response"=>true];
     	 	
     	 } catch (Exception $e) {
     	 	  DB::connection('retail')->rollBack();

              throw $e;
              return ["response"=>false,"status_text"=>$e->getMessage()];
     	 }
           

     }
          public static function modificar_cupon($datos){
	     	 $user = auth()->user();
	        $dia = date("Y-m-d");
	        $hora = date("H:i:s");


     	 try {
     	 	 DB::connection('retail')->beginTransaction();

     	 	  $Cupon=Cupon::WHERE('CODIGO','=',$datos['data']['Codigo'])->WHERE('ID_SUCURSAL','=',$user->id_sucursal)->UPDATE(
          ['DESCRIPCION'=> $datos['data']['Descripcion'],
          'TIPO_CUPON'=> $datos['data']['Tipo'],
          'IMPORTE'=> $datos['data']['Importe'],
          'USO_LIMITE'=> $datos['data']['Limite_uso_cupon'],
          'USO_LIMITE_CLIENTE'=> $datos['data']['Limite_uso_cliente'],
          'FECHA_CADUCIDAD'=> $datos['data']['Fecha_caducidad'],
          'EXCLUIR_ARTICULOS_CON_DESCUENTO'=> $datos['data']['Excluir_articulos_con_descuento'],
          'GASTO_MAX'=> $datos['data']['Gasto_maximo'],
          'GASTO_MIN'=> $datos['data']['Gasto_minimo'],
          'USERM'=>$user->id,
          'FECMODIF'=>$dia,
          'HORMODIF'=>$hora]);
     	    DB::connection('retail')->commit();	  
		 return ["response"=>true];
     	 	
     	 } catch (Exception $e) {
     	 	  DB::connection('retail')->rollBack();

              throw $e;
               return ["response"=>false,"status_text"=>$e->getMessage()];
     	 }
           

     }
        public static function deshabilitar_cupon($datos){
	     	 $user = auth()->user();
	        $dia = date("Y-m-d");
	        $hora = date("H:i:s");


     	 try {
     	 	 DB::connection('retail')->beginTransaction();

     	 	  $Cupon=Cupon::WHERE('CODIGO','=',$datos['data'])->WHERE('ID_SUCURSAL','=',$user->id_sucursal)->UPDATE(
          ['USERM'=>$user->id,
          'ACTIVO'=>1,
          'FECMODIF'=>$dia,
          'HORMODIF'=>$hora]);
     	    DB::connection('retail')->commit();	  
		 return ["response"=>true];
     	 	
     	 } catch (Exception $e) {
     	 	  DB::connection('retail')->rollBack();

              throw $e;
               return ["response"=>false,"status_text"=>$e->getMessage()];
     	 }
           

     }
         public static function habilitar_cupon($datos){
	     	 $user = auth()->user();
	        $dia = date("Y-m-d");
	        $hora = date("H:i:s");


     	 try {
     	 	 DB::connection('retail')->beginTransaction();

     	 	  $Cupon=Cupon::WHERE('CODIGO','=',$datos['data'])->WHERE('ID_SUCURSAL','=',$user->id_sucursal)->UPDATE(
          ['USERM'=>$user->id,
          'ACTIVO'=>0,
          'FECMODIF'=>$dia,
          'HORMODIF'=>$hora]);
     	    DB::connection('retail')->commit();	  
		 return ["response"=>true];
     	 	
     	 } catch (Exception $e) {
     	 	  DB::connection('retail')->rollBack();

              throw $e;
               return ["response"=>false,"status_text"=>$e->getMessage()];
     	 }
           

     }
    
     public static function datatable_cupon($request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 
 $dia = date("Y-m-d");
        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'CODIGO', 
                            1 => 'TIPO_CUPON', 
                            3 => 'IMPORTE',
                            4 => 'DESCRIPCION',
                            5 => 'USO_LIMITE',
                            6 => 'FECHA_CADUCIDAD',
                            7 => 'GASTO_MIN',
                            8 => 'GASTO_MAX',
                            9 => 'EXCLUIR_ARTICULOS_CON_DESCUENTO',
                            10 => 'ACCION',
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = Cupon::where('ID_SUCURSAL','=', $user->id_sucursal)
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

            $posts = Cupon::select(DB::raw('CODIGO,TIPO_CUPON,FECHA_CADUCIDAD,IMPORTE,DESCRIPCION,USO_LIMITE,USO,GASTO_MIN,GASTO_MAX,ACTIVO,EXCLUIR_ARTICULOS_CON_DESCUENTO'))

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

            $posts =  Cupon::select(DB::raw('CODIGO,TIPO_CUPON,FECHA_CADUCIDAD,IMPORTE,DESCRIPCION,USO_LIMITE,USO,GASTO_MIN,GASTO_MAX,ACTIVO,EXCLUIR_ARTICULOS_CON_DESCUENTO'))

                         ->where('ID_SUCURSAL','=', $user->id_sucursal)
   
                            ->where(function ($query) use ($search) {
                                $query->where('CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('IMPORTE', 'LIKE',"%{$search}%")
                                      ->orWhere('DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered =Cupon::select(DB::raw('CODIGO,TIPO_CUPON,FECHA_CADUCIDAD,IMPORTE,DESCRIPCION,USO_LIMITE,USO,GASTO_MIN,GASTO_MAX,ACTIVO,EXCLUIR_ARTICULOS_CON_DESCUENTO'))

                         ->where('ID_SUCURSAL','=', $user->id_sucursal)
                        
                            ->where(function ($query) use ($search) {
                                $query->where('CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('IMPORTE', 'LIKE',"%{$search}%")
                                      ->orWhere('DESCRIPCION', 'LIKE',"%{$search}%");
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
                if($post->TIPO_CUPON==1){
                	$TIPO='PORCENTAJE';
                }else{
                	if($post->TIPO_CUPON==2){
                		$TIPO='EFECTIVO';
                	}
                }
                if($post->ACTIVO==0){


                if($post->FECHA_CADUCIDAD<=$dia ){
                	$nestedData["ESTATUS"]='table-warning';
                }else{

                	$nestedData["ESTATUS"]='table-success';

                }
 				$nestedData['ACCION'] = "
                    <a href='#' id='editar' title='Editar'><i class='fa fa-edit text-warning' aria-hidden='true'></i></a>&emsp;<a href='#' id='deshabilitar' title='Deshabilitar'><i class='fa fa-trash text-danger' aria-hidden='true'></i></a>";
                }else{
                	 $nestedData['ACCION'] = "
                    <a href='#' id='editar' title='Editar'><i class='fa fa-edit text-warning' aria-hidden='true'></i></a>&emsp;<a href='#' id='habilitar' title='Habilitar'><i class='fa fa-check text-success' aria-hidden='true'></i></a>";
                	$nestedData["ESTATUS"]='table-danger';
                }
                if($post->EXCLUIR_ARTICULOS_CON_DESCUENTO==1){
                	$nestedData['EXCLUIR_ARTICULOS_CON_DESCUENTO'] = "Sí";
                }else{
                		$nestedData['EXCLUIR_ARTICULOS_CON_DESCUENTO'] = "No";
                }
                
                $nestedData['TIPO_CUPON'] = $TIPO;
                $nestedData['IMPORTE'] = $post->IMPORTE;
                $nestedData['DESCRIPCION'] = $post->DESCRIPCION;
                $nestedData['USO_LIMITE'] = ''.$post->USO.'/'.$post->USO_LIMITE.'';
                $nestedData['FECHA_CADUCIDAD'] = $post->FECHA_CADUCIDAD;
                $nestedData['GASTO_MIN'] = $post->GASTO_MIN;
                $nestedData['GASTO_MAX'] = $post->GASTO_MAX;

                   
                              

              

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
}
