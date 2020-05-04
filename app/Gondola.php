<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Producto;
class Gondola extends Model
{

    protected $connection = 'retail';
    protected $table = 'gondolas';
    protected $primaryKey='Codigo';
    const CREATED_AT='FECALTAS';
    const UPDATED_AT='FECMODIF';

     public static function obtener_gondolas()
    {

        /*  --------------------------------------------------------------------------------- */
        
        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODAS LAS GONDOLAS

        $gondolas = DB::connection('retail')
        ->table('GONDOLAS')
        ->select(DB::raw('ID, CODIGO, DESCRIPCION'))
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
        public static function filtrar_gondola($datos)
    {
        /*  --------------------------------------------------------------------------------- */
        
        $user = auth()->user();
        // OBTENER TODOS LOS DATOS DEL TALLE
        $gondolas = Gondola::select(DB::raw('CODIGO, DESCRIPCION'))->where('ID_SUCURSAL','=',$user->id_sucursal)->Where('CODIGO','=',$datos['id'])->get()->toArray();
        if(count($gondolas)<=0){
           return ["response"=>false];
        }
    
        // RETORNAR EL VALOR

       return ["response"=>true,"Gondolas"=>$gondolas];

        /*  --------------------------------------------------------------------------------- */

    }
        public static function gondolas_datatable($request)
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
                         
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = Gondola::where('id_sucursal','=',$user->id_sucursal)->count();  
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

            $posts = Gondola::select(DB::raw('CODIGO,DESCRIPCION'))
                         ->where('id_sucursal','=',$user->id_sucursal)
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

            $posts =Gondola::select(DB::raw('CODIGO,DESCRIPCION'))
                            ->where('id_sucursal','=',$user->id_sucursal)
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

            $totalFiltered = Gondola::where('id_sucursal','=',$user->id_sucursal)
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
blob:https://web.whatsapp.com/3c60c7d0-5c70-40fc-93b4-53017c2e03ef
        // CONVERTIR EN JSON EL ARRAY Y ENVIAR 

       return $json_data; 

    }
        public static function obtener_codigo()
    {
                $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODAS LAS GONDOLAS

        $gondolas = Gondola::select('CODIGO')->where('id_sucursal','=',$user->id_sucursal)->orderby('CODIGO','DESC')->limit(1)
        ->get()->toArray();
      
        /*  --------------------------------------------------------------------------------- */
         $codigo=[];
        // RETORNAR EL VALOR
       
        if ($gondolas) {
            return ['Gondola' => $gondolas];
        } else {
            $codigo[0]['CODIGO'] =$user->id_sucursal*1000;
            return ['Gondola' => $codigo];
        }

        /*  --------------------------------------------------------------------------------- */

    }
         public static function gondola_guardar($datos)
    {

        $user = auth()->user();
        $dia = date("Y-m-d H:i:s");
        $hora = date("H:i:s");
        $codigo_gondola=$datos['data']['Codigo'];
        $limite_menor=$user->id_sucursal*1000;
        $limite_mayor=$limite_menor+99999;
        if($codigo_gondola<$limite_menor || $codigo_gondola>$limite_mayor){
             return ["response"=>false,'statusText'=>'Esta Codigo de gondola no puede ser registrada (PRESIONE F2 o el boton NUEVO para crear un codigo)!!'];
            //var_dump($datos['data']['Marcados']);
        }
        try { 
        /*  --------------------------------------------------------------------------------- */
         if($datos['data']['Existe']=== false){
         $gondolas =Gondola::insertGetId(
         ['CODIGO'=>$codigo_gondola,'DESCRIPCION'=> $datos['data']['Descripcion'], 'FK_USER_CR'=>$user->id,'FECALTAS'=>$dia,'ID_SUCURSAL'=>$user->id_sucursal]);
     

  


        }else{
             //GONDOLAS UPDATE
            $gondolas=Gondola::where('CODIGO', $codigo_gondola)
            ->update(['DESCRIPCION'=> $datos['data']['Descripcion'], 'FK_USER_MD'=>$user->id,'FECMODIF'=>$dia]);


 
           }


            return ["response"=>true];
        
      

        /*  --------------------------------------------------------------------------------- */
        } catch(Exception $ex){ 

 
        if($ex->errorInfo[1]===1062){
      return ["response"=>false,'statusText'=>'Esta Codigo de gondola ya fue registrada!!'];
       }else{
      return ["response"=>false,'statusText'=>$ex->getMessage()];
      }
  
      }


    }
             public static function gondola_eliminar($datos)
    {
        $user = auth()->user();
        $dia = date("Y-m-d");
        $hora = date("H:i:s");
        $codigo_gondola=$datos['data']['Codigo'];
        /*  --------------------------------------------------------------------------------- */

        if($datos['data']['Existe']=== true){
        
         $producto=Producto::select('CODIGO')->Where('FK_GONDOLA','=',$codigo_gondola)->limit(1)->get()->toArray();
         if(count($producto)>0){
            return ["response"=>false];
         }else{


        $gondola=Gondola::where('CODIGO', $codigo_gondola)->delete();
                    }

        }else{
        return ["response"=>false];
        }
  return ["response"=>true];

        /*  --------------------------------------------------------------------------------- */

    }

    public static function obtener_gondolas_por_producto($codigo)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODAS LAS GONDOLAS

        $gondolas = Gondola::select(DB::raw('GONDOLAS.ID, CODIGO, DESCRIPCION, GONDOLA_TIENE_PRODUCTOS.FECALTAS'))
        ->rightjoin('GONDOLA_TIENE_PRODUCTOS', 'GONDOLA_TIENE_PRODUCTOS.ID_GONDOLA', '=', 'GONDOLAS.ID')
        ->where('GONDOLAS.ID_SUCURSAL', '=', $user->id_sucursal)
        ->where('GONDOLA_TIENE_PRODUCTOS.GONDOLA_COD_PROD', '=', $codigo)
        ->get();

        /*  --------------------------------------------------------------------------------- */
       
        // RETORNAR EL VALOR

        return ['response' => true, 'gondolas' => $gondolas];

        /*  --------------------------------------------------------------------------------- */

    }

    
}
