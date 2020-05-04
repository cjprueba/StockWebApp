<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\MarcaAux;
use App\Producto;
Use Exception;
class Marca extends Model
{

    protected $connection = 'retail';
    protected $table = 'marca';

    protected $primaryKey='Codigo';
    const CREATED_AT='FECALTAS';
    const UPDATED_AT='FECMODIF';
    //public $timestamps=false;
    protected $fillable = [
        'CODIGO',
        'DESCRIPCION'
    ];


    public static function obtener_marcas($categoria)
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER TODAS LAS CATEGORIAS
        
    	$marca = Marca::select(DB::raw('CODIGO, DESCRIPCION'))
        ->leftjoin('LINEAS_TIENE_MARCAS', 'LINEAS_TIENE_MARCAS.FK_COD_MARCA', '=', 'MARCA.CODIGO')
        ->where('LINEAS_TIENE_MARCAS.FK_COD_LINEA_LINEAS_TIENE_MARCAS', '=', $categoria)
        ->get();
      
        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($marca) {
        	return ['marcas' => $marca];
        } else {
        	return ['marcas' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
        public static function obtener_codigo()
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODAS LAS CATEGORIAS

        $marca = Marca::select('CODIGO')->orderby('CODIGO','DESC')->limit(1)
        ->get()->toArray();
      
        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($marca) {
            return ['marcas' => $marca];
        } else {
            return ['marcas' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
        public static function filtrar_marcas($datos)
    {

        /*  --------------------------------------------------------------------------------- */
        $user = auth()->user();
        // OBTENER TODAS LAS CATEGORIAS
      $existe_descuento=false;
        $marca = Marca::select(DB::raw('CODIGO, DESCRIPCION'))->Where('CODIGO','=',$datos['id'])->get()->toArray();
        if(count($marca)<=0){
           return ["response"=>false];
        }
        $marcas_aux=MarcaAux::select(DB::raw('DESCUENTO, substr(FECHAINI,1,10) as FECHAINI, substr(FECHAFIN,1,10) AS FECHAFIN'))->Where([['CODIGO_MARCA','=',$datos['id']], ['id_sucursal','=',$user->id_sucursal]])->get()->toArray();
        /*  --------------------------------------------------------------------------------- */
       if(count($marcas_aux)>0){
           $existe_descuento=true;
        }
        // RETORNAR EL VALOR

       return ["response"=>true,"marcas"=>$marca,"descuento"=>$marcas_aux, "existe_descuento"=>$existe_descuento];

        /*  --------------------------------------------------------------------------------- */

    }
            public static function marcas_guardar($datos)
    {
        $user = auth()->user();
        $dia = date("Y-m-d");
        $hora = date("H:i:s");
        $codigo_marca=$datos['data']['Codigo'];
        /*  --------------------------------------------------------------------------------- */
        try { 
         if($datos['data']['Existe']=== false){


         $marca=Marca::insertGetId(
         ['DESCRIPCION'=> $datos['data']['Descripcion'], 'USER'=>$user->name,'FECALTAS'=>$dia,'HORALTAS'=>$hora]);
         $codigo_marca = $marca;
         }else{
            $marca=Marca::where('CODIGO', $codigo_marca)
            ->update(['DESCRIPCION'=> $datos['data']['Descripcion'], 'USERM'=>$user->name,'FECMODIF'=>$dia,'HORMODIF'=>$hora]);
         }



         if($datos['data']['switch_descuento']===true){
          $marcas_aux=MarcaAux::select('CODIGO_MARCA')->Where([['CODIGO_MARCA','=',$codigo_marca],['ID_SUCURSAL','=',$user->id_sucursal]])->get()->toArray();
         if(count($marcas_aux)>0){

            $marca_aux=MarcaAux::where([['CODIGO_MARCA', $codigo_marca],['ID_SUCURSAL','=',$user->id_sucursal]])
            ->update(['DESCUENTO' => $datos['data']['descuento'], 'FECHAINI'=> $datos['data']['fechaini'],'FECHAFIN'=> $datos['data']['fechafin'],'FECMODIF'=>$dia,'USERM'=>$user->name]);

         }else{
         $marca_aux=MarcaAux::insert([
         ['CODIGO_MARCA' => $codigo_marca,'DESCUENTO' => $datos['data']['descuento'], 'FECHAINI'=> $datos['data']['fechaini'],'FECHAFIN'=> $datos['data']['fechafin'],'FECALTAS'=>$dia, 'ID_SUCURSAL'=>$user->id_sucursal,'USER'=>$user->name]
         ]);
         }



          }


  return ["response"=>true];


} catch(Exception $ex){ 

 
 if($ex->errorInfo[1]===1062){
      return ["response"=>false,'statusText'=>'Esta descripcion de Marca ya fue registrada!!'];
 }else{
    return ["response"=>false,'statusText'=>$ex->getMessage()];
 }
  
}


        /*  --------------------------------------------------------------------------------- */

    }
     public static function marcas_eliminar($datos)
    {
        $user = auth()->user();
        $dia = date("Y-m-d");
        $hora = date("H:i:s");
        $codigo_marca=$datos['data']['Codigo'];
        /*  --------------------------------------------------------------------------------- */

        if($datos['data']['Existe']=== true){
         


         $producto=Producto::select('CODIGO')->Where('MARCA','=',$codigo_marca)->limit(1)->get()->toArray();
         if(count($producto)>0){
            return ["response"=>false];
         }else{
           $marcas_aux=MarcaAux::where([['CODIGO_MARCA', $codigo_marca],['ID_SUCURSAL',$user->id_sucursal]])->delete();
            $marca=Marca::where('CODIGO', $codigo_marca)->delete();
                    }
         $codigo_marca = $marca;



        }else{
        return ["response"=>false];
        }
  return ["response"=>true];

        /*  --------------------------------------------------------------------------------- */

    }
    public static function marcas_datatable($request)
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

        $totalData = Marca::count();  
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

            $posts = Marca::select(DB::raw('CODIGO,DESCRIPCION'))
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

            $posts =Marca::select(DB::raw('CODIGO,DESCRIPCION'))
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

            $totalFiltered = Marca::where(function ($query) use ($search) {
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
    public static function marcas_por_categoria_datatable($request)
    {

        /*  --------------------------------------------------------------------------------- */

        // INICIARA VARIABLES

        //global $search;

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 



        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            
                            0 => 'CODIGO', 
                            1 => 'DESCRIPCION',
                            2 => 'FECALTAS'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = Marca::count();  

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

            $posts = Marca::select(DB::raw('CODIGO,DESCRIPCION,USER,substr(fecaltas,1,10) AS FECALTAS'))
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

            $posts =Marca::select(DB::raw(' CODIGO,DESCRIPCION,USER, substr(fecaltas,1,10) AS FECALTAS'))
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

            $totalFiltered = Marca::where(function ($query) use ($search) {
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
                $nestedData['DESCRIPCION'] = '<div class="custom-control custom-checkbox">
  <input type="checkbox" name="check-marcado" class="custom-control-input call-checkbox" id="Marcados-'.$post->CODIGO.'">
  <label class="custom-control-label" for="Marcados-'.$post->CODIGO.'">'.$post->DESCRIPCION.'</label>
</div>';
 $nestedData['FECALTAS'] = substr($post->FECALTAS,0,10);

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

    public static function marca_categoria_seleccion($data)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODAS LAS CATEGORIAS

        $marca = Marca::select(DB::raw('CODIGO, DESCRIPCION'))
        ->leftjoin('LINEAS_TIENE_MARCAS', 'LINEAS_TIENE_MARCAS.FK_COD_MARCA', '=', 'MARCA.CODIGO')
        ->where('LINEAS_TIENE_MARCAS.FK_COD_LINEA_LINEAS_TIENE_MARCAS', '=', $data['categoria'])
        ->where('LINEAS_TIENE_MARCAS.FK_COD_MARCA', '=', $data['marca'])
        ->get();
      
        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if (count($marca) > 0) {
            return ['marcas' => $marca[0]];
        } else {
            return ['marcas' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
}
