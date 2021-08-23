<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
Use Exception;
class Tela extends Model
{
    protected $connection = 'retail';
    protected $table = 'telas';
    protected $primaryKey='Codigo';
    const CREATED_AT='FECALTAS';
    const UPDATED_AT='FECMODIF';
    public static function obtener_telas()
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER TODAS LAS CATEGORIAS

    	$telas = DB::connection('retail')
        ->table('TELAS')
        ->select(DB::raw('CODIGO, DESCRIPCION'))
        ->orderBy('DESCRIPCION')
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($telas) {
        	return ['telas' => $telas];
        } else {
        	return ['telas' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
     public static function telas_datatable($request)
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
                            1 => 'descripcion',
                         
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = Tela::count();  
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

            $posts = Tela::select(DB::raw('CODIGO,DESCRIPCION'))
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

            $posts =Tela::select(DB::raw('CODIGO,DESCRIPCION'))
                            ->where(function ($query) use ($search) {
                                $query->where('CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = Tela::where(function ($query) use ($search) {
                                $query->where('CODIGO','LIKE',"%{$search}%")
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
                $nestedData['DESCRIPCION'] = $post->DESCRIPCION;


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
        public static function filtrar_tela($datos)
    {

        /*  --------------------------------------------------------------------------------- */
     
        // OBTENER TODOS LOS DATOS DEL TALLE
        $tela = Tela::select(DB::raw('CODIGO, DESCRIPCION'))->Where('CODIGO','=',$datos['id'])->get()->toArray();
        if(count($tela)<=0){
           return ["response"=>false];
        }
        // RETORNAR EL VALOR

       return ["response"=>true,"tela"=>$tela];

        /*  --------------------------------------------------------------------------------- */

    }
          public static function obtener_codigo()
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODAS LAS CATEGORIAS

        $tela = Tela::select('CODIGO')->orderby('CODIGO','DESC')->limit(1)
        ->get()->toArray();
      
        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($tela) {
            return ['tela' => $tela];
        } else {
            return ['tela' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
        public static function tela_guardar($datos)
    {
        $user = auth()->user();
        $dia = date("Y-m-d");
        $hora = date("H:i:s");
        $codigo_tela=$datos['data']['Codigo'];


        try { 
        /*  --------------------------------------------------------------------------------- */
         if($datos['data']['Existe']=== false){
         $tela=Tela::insertGetId(
         ['DESCRIPCION'=> $datos['data']['Descripcion'], 'USER'=>$user->name,'FECALTAS'=>$dia,'HORALTAS'=>$hora]);
         $codigo_tela = $tela;
        }else{
            $tela=Tela::where('CODIGO', $codigo_tela)
            ->update(['DESCRIPCION'=> $datos['data']['Descripcion'], 'USERM'=>$user->name,'FECMODIF'=>$dia,'HORMODIF'=>$hora]);
        }
       return ["response"=>true];

        /*  --------------------------------------------------------------------------------- */
        } catch(Exception $ex){ 

 
        if($ex->errorInfo[1]===1062){
      return ["response"=>false,'statusText'=>'Esta descripcion de Tela ya fue registrada!!'];
       }else{
      return ["response"=>false,'statusText'=>$ex->getMessage()];
      }
  
      }


    }



     public static function tela_eliminar($datos)
    {
        $user = auth()->user();
        $dia = date("Y-m-d");
        $hora = date("H:i:s");
        $codigo_tela=$datos['data']['Codigo'];
        /*  --------------------------------------------------------------------------------- */

        if($datos['data']['Existe']=== true){
        
         $producto=Producto::select('CODIGO')->Where('TELA','=',$codigo_tela)->limit(1)->get()->toArray();
         if(count($producto)>0){
            return ["response"=>false];
         }else{
            $tela=Tela::where('CODIGO', $codigo_tela)->delete();
                    }

        }else{
        return ["response"=>false];
        }
  return ["response"=>true];

        /*  --------------------------------------------------------------------------------- */

    }
}
