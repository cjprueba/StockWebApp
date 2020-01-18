<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class SubCategoria extends Model
{
    protected $connection = 'retail';
    protected $table = 'sublineas';
    protected $primaryKey='Codigo';
    const CREATED_AT='FECALTAS';
    const UPDATED_AT='FECMODIF';
    public static function obtener_subCategorias()
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER TODAS LAS SUB CATEGORIAS

    	$subCategorias = DB::connection('retail')
        ->table('SUBLINEAS')
        ->select(DB::raw('CODIGO, DESCRIPCION'))
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($subCategorias) {
        	return ['subCategorias' => $subCategorias];
        } else {
        	return ['subCategorias' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
                public static function filtrar_sub_categoria($datos)
    {
        $marcados=[];

        /*  --------------------------------------------------------------------------------- */
     
        // OBTENER TODOS LOS DATOS DEL TALLE
        $subCategoria = SubCategoria::select(DB::raw('CODIGO, DESCRIPCION'))->Where('CODIGO','=',$datos['id'])->get()->toArray();
        if(count($subCategoria)<=0){
           return ["response"=>false];
        }
     $categoriaporSublinea=DB::connection('retail')->table('lineas_tiene_sublineas')->select(DB::raw('FK_COD_LINEA'))
     ->Where('FK_COD_SUBLINEA','=',$datos['id'])
     ->Where('ACTIVO','=',1)
     ->get()
     ->toArray();
     foreach ($categoriaporSublinea as $key => $value) {
         $marcados[$key]=$value->FK_COD_LINEA;
     }
        // RETORNAR EL VALOR

       return ["response"=>true,"subCategoria"=>$subCategoria,"marcados"=>$marcados];

        /*  --------------------------------------------------------------------------------- */

    }
 public static function subcategorias_datatable($request)
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

        $totalData = SubCategoria::count();  

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

            $posts = SubCategoria::select(DB::raw('CODIGO,DESCRIPCION,USER,substr(fecaltas,1,10) AS FECALTAS'))
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

            $posts =SubCategoria::select(DB::raw(' CODIGO,DESCRIPCION,USER, substr(fecaltas,1,10) AS FECALTAS'))
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

            $totalFiltered = SubCategoria::where(function ($query) use ($search) {
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
  <input type="checkbox" name="check" class="custom-control-input call-checkbox" id="'.$post->CODIGO.'">
  <label class="custom-control-label" for="'.$post->CODIGO.'">'.$post->DESCRIPCION.'</label>
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
     public static function subcategoria_datatable($request)
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
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = SubCategoria::count();  

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

            $posts = SubCategoria::select(DB::raw('CODIGO,DESCRIPCION,USER'))
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

            $posts =SubCategoria::select(DB::raw(' CODIGO,DESCRIPCION,USER'))
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

            $totalFiltered = SubCategoria::where(function ($query) use ($search) {
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
    public static function obtener_codigo()
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODAS LAS CATEGORIAS

        $subCategoria = SubCategoria::select('CODIGO')->orderby('CODIGO','DESC')->limit(1)
        ->get()->toArray();
      
        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($subCategoria) {
            return ['subCategoria' => $subCategoria];
        } else {
            return ['subCategoria' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
     public static function subCategoria_guardar($datos)
    {

        $user = auth()->user();
        $dia = date("Y-m-d");
        $hora = date("H:i:s");
        $codigo_subcategoria=$datos['data']['Codigo'];

            //var_dump($datos['data']['Marcados']);
        try { 
        /*  --------------------------------------------------------------------------------- */
         if($datos['data']['Existe']=== false){
         $subcategoria =SubCategoria::insertGetId(
         ['DESCRIPCION'=> $datos['data']['Descripcion'], 'USER'=>$user->name,'FECALTAS'=>$dia,'HORALTAS'=>$hora]);
         $codigo_subcategoria = $subcategoria;
  
          foreach ($datos['data']['Marcados'] as $key => $marcados) {


                 $categoriaporSublinea =DB::connection('retail')->table('lineas_tiene_sublineas')->insertGetId(
                 ['FK_COD_SUBLINEA'=> $codigo_subcategoria,'FK_COD_LINEA'=> $marcados , 'FK_USER_CR'=>$user->id,'FECALTAS'=>$dia,'HORALTAS'=>$hora,'ACTIVO'=>1]);
 
         
          }


        }else{
             //LINEAS UPDATE
            $subcategoria=SubCategoria::where('CODIGO', $codigo_subcategoria)
            ->update(['DESCRIPCION'=> $datos['data']['Descripcion'], 'USERM'=>$user->name,'FECMODIF'=>$dia,'HORMODIF'=>$hora]);
            // ------------------------------------------------------------------------
            //LINEAS_TIENE_SUBLINEAS UPDATE ACTIVOS

            $activos=DB::connection('retail')->table('lineas_tiene_sublineas')->where('FK_COD_SUBLINEA', $codigo_subcategoria)
            ->update(['ACTIVO'=> 0,'FECMODIF'=>$dia,'FK_USER_MD'=>$user->id,"HORMODIF"=>$hora]);
            // ------------------------------------------------------------------------
            //LINEAS_TIENE_MARCAS UPDATE ACTIVOS

        
           // ------------------------------------------------------------------------

            // INSERTAR O ACTUALIZAR EN LINEAS_TIENE_SUBLINEAS
           foreach ($datos['data']['Marcados'] as $key => $marcados){
          $categoriaporSublinea=DB::connection('retail')->table('lineas_tiene_sublineas')->select(DB::raw('FK_COD_LINEA'))
          ->Where('FK_COD_SUBLINEA','=',$codigo_subcategoria)
          ->where('FK_COD_LINEA','=',$marcados)
          ->get()
          ->toArray();

         if(count($categoriaporSublinea)<=0){
            $categoriaporSublinea1=DB::connection('retail')->table('lineas_tiene_sublineas')->insertGetId(
            ['FK_COD_SUBLINEA'=> $codigo_subcategoria,'FK_COD_LINEA'=> $marcados , 'FK_USER_CR'=>$user->id,'FECALTAS'=>$dia,'HORALTAS'=>$hora,'ACTIVO'=>1]);

          }else{
            $activos=DB::connection('retail')->table('lineas_tiene_sublineas')
            ->where('FK_COD_SUBLINEA', $codigo_subcategoria)
            ->where('FK_COD_LINEA','=',$marcados)
            ->update(['ACTIVO'=> 1]);
         }

         }
         // ------------------------------------------------------------------------


           }


            return ["response"=>true];
        
      

        /*  --------------------------------------------------------------------------------- */
        } catch(Exception $ex){ 

 
        if($ex->errorInfo[1]===1062){
      return ["response"=>false,'statusText'=>'Esta descripcion de Categoria ya fue registrada!!'];
       }else{
      return ["response"=>false,'statusText'=>$ex->getMessage()];
      }
  
      }


    }
         public static function subCategoria_eliminar($datos)
    {
        $user = auth()->user();
        $dia = date("Y-m-d");
        $hora = date("H:i:s");
        $codigo_subcategoria=$datos['data']['Codigo'];
        /*  --------------------------------------------------------------------------------- */

        if($datos['data']['Existe']=== true){
        
         $producto=Producto::select('CODIGO')->Where('sublinea','=',$codigo_subcategoria)->limit(1)->get()->toArray();
         if(count($producto)>0){
            return ["response"=>false];
         }else{
            $activos=DB::connection('retail')->table('lineas_tiene_sublineas')->where('FK_COD_SUBLINEA', $codigo_subcategoria)
            ->update(['FECMODIF'=>$dia,'FK_USER_MD'=>$user->id,"HORMODIF"=>$hora]);
        $categoriaporSublinea=DB::connection('retail')->table('lineas_tiene_sublineas')->Where(['FK_COD_SUBLINEA'=> $codigo_subcategoria])->delete();
        $subCategoria=SubCategoria::where('CODIGO', $codigo_subcategoria)->delete();
                    }

        }else{
        return ["response"=>false];
        }
  return ["response"=>true];

        /*  --------------------------------------------------------------------------------- */

    }

}
