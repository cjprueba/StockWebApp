<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Transporte extends Model
{
	    protected $connection = 'retail';
    protected $table = 'transporte';
    protected $primaryKey='Codigo';
    const CREATED_AT='FECALTAS';
    const UPDATED_AT='FECMODIF';
    //
                public static function filtrar_transporte($datos)
    {

        /*  --------------------------------------------------------------------------------- */
     
        // OBTENER TODOS LOS DATOS DEL TALLE
        $transporte = Transporte::select(DB::raw('CODIGO, NOMBRE,RUC,DIRECCION,TELEFONO,CELULAR,EMAIL'))->Where('CODIGO','=',$datos['id'])->get()->toArray();
        if(count($transporte)<=0){
           return ["response"=>false];
        }
        // RETORNAR EL VALOR

       return ["response"=>true,"transporte"=>$transporte];

        /*  --------------------------------------------------------------------------------- */

    }
     public static function transporte_datatable($request)
    {
 /*  --------------------------------------------------------------------------------- */

        // INICIARA VARIABLES

        //global $search;

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 



        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'codigo', 
                            1 => 'nombre',
                            2 => 'ruc',
                            3 => 'direccion',
                            5 => 'telefono',
                            6 => 'celular',
                            7 => 'email'
                         
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = Transporte::count();  
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        /*  ------------------------------------- -------------------------------------------- */

        // REVISAR SI EXISTE VALOR EN VARIABLE SEARCH

        if(empty($request->input('search.value')))
        {            

            /*  ************************************************************ */

            //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

            $posts = Transporte::select(DB::raw('CODIGO,NOMBRE,RUC,DIRECCION,TELEFONO,CELULAR,EMAIL'))
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

            $posts =Transporte::select(DB::raw('CODIGO,NOMBRE,RUC,DIRECCION,TELEFONO,CELULAR,EMAIL'))
                            ->where(function ($query) use ($search) {
                                $query->where('CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('NOMBRE', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = Transporte::where(function ($query) use ($search) {
                                $query->where('CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('NOMBRE', 'LIKE',"%{$search}%");
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
                $nestedData['NOMBRE'] = $post->NOMBRE;
                $nestedData['RUC'] = $post->RUC;
                $nestedData['DIRECCION'] = $post->DIRECCION;
                $nestedData['TELEFONO'] = $post->TELEFONO;
                $nestedData['CELULAR'] = $post->CELULAR;
                $nestedData['EMAIL'] = $post->EMAIL;
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
                public static function obtener_codigo()
    {
        $codigo=[];

        /*  --------------------------------------------------------------------------------- */

        // OBTENER EL CODIGO

        $transporte = Transporte::select('CODIGO')->orderby('CODIGO','DESC')->limit(1)
        ->get()->toArray();
      
        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($transporte) {
            return ['transporte' => $transporte];
        } else {
            $codigo[0]['CODIGO']=0;
            return ['transporte' => $codigo];
        }

        /*  --------------------------------------------------------------------------------- */

    }
    public static function transporte_guardar($datos)
    {
        $user = auth()->user();
        $dia = date("Y-m-d");
        $hora = date("H:i:s");
        $codigo_transporte=$datos['data']['Codigo'];


        try { 

        /*  --------------------------------------------------------------------------------- */
         if($datos['data']['Existe']=== false){
         $transporte=Transporte::insertGetId(
         ['NOMBRE'=> $datos['data']['Descripcion'],'RUC'=> $datos['data']['Ruc'], 'DIRECCION'=> $datos['data']['Direccion'],'TELEFONO'=> $datos['data']['Telefono'],'celular'=> $datos['data']['cel'],'EMAIL'=> $datos['data']['email'],'FK_USER_CR'=>$user->id,'FECALTAS'=>$dia,'HORALTAS'=>$hora]);
         $codigo_transporte = $transporte;
        }else{
            $transporte=Transporte::where('CODIGO', $codigo_transporte)
            ->update(['NOMBRE'=> $datos['data']['Descripcion'],'RUC'=> $datos['data']['Ruc'], 'DIRECCION'=> $datos['data']['Direccion'],'TELEFONO'=> $datos['data']['Telefono'],'celular'=> $datos['data']['cel'],'EMAIL'=> $datos['data']['email'], 'FK_USER_MD'=>$user->id,'FECMODIF'=>$dia,'HORMODIF'=>$hora]);
        }
       return ["response"=>true];

        /*  --------------------------------------------------------------------------------- */
        } catch(Exception $ex){ 

 
        if($ex->errorInfo[1]===1062){
      return ["response"=>false,'statusText'=>'Esta descripcion de transporte ya fue registrada!!'];
       }else{
      return ["response"=>false,'statusText'=>$ex->getMessage()];
      }
  
      }


    }
      public static function transporte_eliminar($datos)
    {
        $user = auth()->user();
        $dia = date("Y-m-d");
        $hora = date("H:i:s");
        $codigo_transporte=$datos['data']['Codigo'];
        /*  --------------------------------------------------------------------------------- */

        if($datos['data']['Existe']=== true){
        
         //$transferencia=Transferencia::select('CODIGO')->Where('transporta','=',$codigo_transporte)->limit(1)->get()->toArray();
         //if(count($transferencia)>0){
          //  return ["response"=>false];
        // }else{
            $transporta=Transporte::where('CODIGO', $codigo_transporte)->delete();
          //          }

        }else{
        return ["response"=>false];
        }
  return ["response"=>true];

        /*  --------------------------------------------------------------------------------- */

    }
}
