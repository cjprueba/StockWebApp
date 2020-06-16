<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Container extends Model
{  
	protected $connection = 'retail';
    protected $table = 'containers';
      public $timestamps=false;
         public static function filtrar_container($datos)
    {
    	$user = auth()->user();

             /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL TALLE
        $container = Container::select(DB::raw('CODIGO,DESCRIPCION,FECHA_INICIO,FECHAPRIME_C,FECHULT_C'))
        ->Where('CODIGO','=',$datos['id'])
        ->Where('ID_SUCURSAL','=',$user->id_sucursal)
        ->orWhere('ACTIVOS_TODOS','=',1)
        ->get()->toArray();
        if(count($container)<=0){
           return ["response"=>false];
        }
        // RETORNAR EL VALOR

       return ["response"=>true,"container"=>$container];

        /*  --------------------------------------------------------------------------------- */

    }
    public static function container_datatable($request)
    {
    	$user = auth()->user();
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
                            2 => 'fecha_inicio',
                            3 => 'fechapirme_c',
                            5 => 'fechult_c'
                         
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = Container::count();  
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

            $posts = Container::select(DB::raw('CODIGO,DESCRIPCION,FECHA_INICIO,FECHAPRIME_C,FECHULT_C'))
                         ->Where('ID_SUCURSAL','=',$user->id_sucursal)
                         ->orWhere('ACTIVOS_TODOS','=',1)
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

            $posts =Container::select(DB::raw('CODIGO,DESCRIPCION,FECHA_INICIO,FECHAPRIME_C,FECHULT_C'))
            				->Where('ID_SUCURSAL','=',$user->id_sucursal)
            				->orWhere('ACTIVOS_TODOS','=',1)
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

            $totalFiltered = Container::Where('ID_SUCURSAL','=',$user->id_sucursal)
            ->orWhere('ACTIVOS_TODOS','=',1)
            ->where(function ($query) use ($search) {
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
                $nestedData['FECHA_INICIO'] = $post->FECHA_INICIO;
                $nestedData['FECHAPRIME_C'] = $post->FECHAPRIME_C;
                $nestedData['FECHULT_C'] = $post->FECHULT_C;
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
    	$user = auth()->user();
        $codigo=[];

        /*  --------------------------------------------------------------------------------- */

        // OBTENER EL CODIGO

        $container = Container::select('CODIGO')->Where('ID_SUCURSAL','=',$user->id_sucursal)
            				->orWhere('ACTIVOS_TODOS','=',1)->orderby('CODIGO','DESC')->limit(1)->get()->toArray();
      
        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($container) {
            return ['container' => $container];
        } else {
            $codigo[0]['CODIGO']=0;
            return ['container' => $codigo];
        }

        /*  --------------------------------------------------------------------------------- */

    }
        public static function container_guardar($datos)
    {
        $user = auth()->user();
        $dia = date("Y-m-d");
        $hora = date("H:i:s");
        $codigo_container=$datos['data']['Codigo'];
$middle = strtotime($datos['data']['fecha_inicio']);             // returns bool(false)

$inicio = date('Y-m-d H:i:s', $middle);



        /*  --------------------------------------------------------------------------------- */
         if($datos['data']['Existe']=== false){
         	$container=Container::select('CODIGO')
         	->Where('ID_SUCURSAL','=',$user->id_sucursal)
         	->Where('CODIGO','=',$codigo_container)
            ->orderby('CODIGO','DESC')
            ->limit(1)
            ->get()
            ->toArray();
            if(count($container)>0){
            	$codigo_container=$codigo_container+1;
            }
         $container=Container::insertGetId(
         ['CODIGO'=> $codigo_container,'DESCRIPCION'=> $datos['data']['Descripcion'],'FECHA_INICIO'=>$inicio,'FK_USER_CR'=>$user->id,'FECALTAS'=>$dia,'HORALTAS'=>$hora,'ID_SUCURSAL'=>$user->id_sucursal]);
        }else{
            $container=Container::where('CODIGO', $codigo_container)
            ->Where('ID_SUCURSAL','=',$user->id_sucursal)
            ->update(['DESCRIPCION'=> $datos['data']['Descripcion'],'FK_USER_MD'=>$user->id,'FECMODIF'=>$dia,'HORMODIF'=>$hora]);
        }
       return ["response"=>true];




    }
     public static function container_eliminar($datos)
    {
        $user = auth()->user();
        $dia = date("Y-m-d");
        $hora = date("H:i:s");
        $codigo_container=$datos['data']['Codigo'];
        /*  --------------------------------------------------------------------------------- */

        if($datos['data']['Existe']=== true){
        
         //$container=Compras::select('CODIGO')->Where('container','=',$codigo_container) ->Where('ID_SUCURSAL','=',$user->id_sucursal)->limit(1)->get()->toArray();
         //if(count($container)>0){
          //  return ["response"=>false];
        // }else{
            $container=Container::where('CODIGO', $codigo_container)
            ->Where('ID_SUCURSAL','=',$user->id_sucursal)
            ->delete();
          //          }

        }else{
        return ["response"=>false];
        }
  return ["response"=>true];

        /*  --------------------------------------------------------------------------------- */

    }

    //
}
