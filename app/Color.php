<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
Use Exception;
class Color extends Model
{
    protected $connection = 'retail';
    protected $table = 'colores';
    protected $primaryKey='Codigo';
        const CREATED_AT='FECALTAS';
    const UPDATED_AT='FECMODIF';
     public static function obtener_colores()
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER TODAS LAS CATEGORIAS

    	$colores = DB::connection('retail')
        ->table('COLORES')
        ->select(DB::raw('CODIGO, DESCRIPCION'))
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($colores) {
        	return ['colores' => $colores];
        } else {
        	return ['colores' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
            public static function obtener_codigo()
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODAS LAS CATEGORIAS

        $color = Color::select('CODIGO')->orderby('CODIGO','DESC')->limit(1)
        ->get()->toArray();
      
        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($color) {
            return ['colores' => $color];
        } else {
            return ['colores' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
            public static function filtrar_colores($datos)
    {

        /*  --------------------------------------------------------------------------------- */
        $user = auth()->user();
        // OBTENER TODOS LOS COLORES 
        $color = Color::select(DB::raw('CODIGO, DESCRIPCION'))->Where('CODIGO','=',$datos['id'])->get()->toArray();
        if(count($color)<=0){
           return ["response"=>false];
        }
        // RETORNAR EL VALOR

       return ["response"=>true,"colores"=>$color];

        /*  --------------------------------------------------------------------------------- */

    }
     public static function colores_datatable($request)
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

        $totalData = Color::count();  
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

            $posts = Color::select(DB::raw('CODIGO,DESCRIPCION'))
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

            $posts =Color::select(DB::raw('CODIGO,DESCRIPCION'))
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

            $totalFiltered = Color::where(function ($query) use ($search) {
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
    public static function colores_guardar($datos)
    {
        $user = auth()->user();
        $dia = date("Y-m-d");
        $hora = date("H:i:s");
        $codigo_color=$datos['data']['Codigo'];
        /*  --------------------------------------------------------------------------------- */
        try { 
         if($datos['data']['Existe']=== false){

         $color=Color::insertGetId(
         ['DESCRIPCION'=> $datos['data']['Descripcion'], 'USER'=>$user->name,'FECALTAS'=>$dia,'HORALTAS'=>$hora]);
         $codigo_color = $color;
        }else{
            $color=Color::where('CODIGO', $codigo_color)
            ->update(['DESCRIPCION'=> $datos['data']['Descripcion'], 'USERM'=>$user->name,'FECMODIF'=>$dia,'HORMODIF'=>$hora]);
        }
  return ["response"=>true];


} catch(Exception $ex){ 

 if($ex->errorInfo[1]===1062){
      return ["response"=>false,'statusText'=>'Esta descripcion de Color ya fue registrada!!'];
 }else{
    return ["response"=>false,'statusText'=>$ex->getMessage()];
 }
  
}
        


        /*  --------------------------------------------------------------------------------- */

    }
         public static function colores_eliminar($datos)
    {
        $user = auth()->user();
        $dia = date("Y-m-d");
        $hora = date("H:i:s");
        $codigo_color=$datos['data']['Codigo'];
        /*  --------------------------------------------------------------------------------- */

        if($datos['data']['Existe']=== true){
        
         $producto=Producto::select('CODIGO')->Where('COLOR','=',$codigo_color)->limit(1)->get()->toArray();
         if(count($producto)>0){
            return ["response"=>false];
         }else{
            $color=Color::where('CODIGO', $codigo_color)->delete();
                    }

        }else{
        return ["response"=>false];
        }
  return ["response"=>true];

        /*  --------------------------------------------------------------------------------- */

    }
}
