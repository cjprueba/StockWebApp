<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{

    protected $connection = 'retail';
    protected $table = 'lineas';
    protected $primaryKey='Codigo';
    const CREATED_AT='FECALTAS';
    const UPDATED_AT='FECMODIF';
     public static function obtener_categorias()
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER TODAS LAS CATEGORIAS

    	$categorias = Categoria::select(DB::raw('CODIGO, DESCRIPCION, ATRIBTELA, ATRIBCOLOR, ATRIBTALLE, ATRIBGENERO, ATRIBMARCA, ATRIBTEMPORADA'))
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($categorias) {
        	return ['categorias' => $categorias];
        } else {
        	return ['categorias' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
            public static function filtrar_categoria($datos)
    {
        $marcados=[];
        $marcadosMarca=[];
        /*  --------------------------------------------------------------------------------- */
     
        // OBTENER TODOS LOS DATOS DEL TALLE
        $categoria = Categoria::select(DB::raw('CODIGO, DESCRIPCION'))->Where('CODIGO','=',$datos['id'])->get()->toArray();
        if(count($categoria)<=0){
           return ["response"=>false];
        }
     $categoriaporSublinea=DB::connection('retail')->table('lineas_tiene_sublineas')->select(DB::raw('FK_COD_SUBLINEA'))
     ->Where('FK_COD_LINEA','=',$datos['id'])
     ->Where('ACTIVO','=',1)
     ->get()
     ->toArray();
     foreach ($categoriaporSublinea as $key => $value) {
         $marcados[$key]=$value->FK_COD_SUBLINEA;
     }
     $categoriaporMarca=DB::connection('retail')->table('lineas_tiene_Marcas')->select(DB::raw('FK_COD_MARCA'))
     ->Where('FK_COD_LINEA_LINEAS_TIENE_MARCAS','=',$datos['id'])
     ->Where('ACTIVO','=',1)
     ->get()
     ->toArray();
     foreach ($categoriaporMarca as $key => $value) {
         $marcadosMarca[$key]="Marcados-".$value->FK_COD_MARCA;
     }
        // RETORNAR EL VALOR

       return ["response"=>true,"categoria"=>$categoria,"marcados"=>$marcados,"MarcaMarcados"=>$marcadosMarca];

        /*  --------------------------------------------------------------------------------- */

    }
              public static function obtener_codigo()
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODAS LAS CATEGORIAS

        $categoria = Categoria::select('CODIGO')->orderby('CODIGO','DESC')->limit(1)
        ->get()->toArray();
      
        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($categoria) {
            return ['categoria' => $categoria];
        } else {
            return ['categoria' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
 public static function categoria_datatable($request)
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

        $totalData = Categoria::count();  

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

            $posts = Categoria::select(DB::raw('CODIGO,DESCRIPCION,USER,substr(fecaltas,1,10) AS FECALTAS'))
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

            $posts =Categoria::select(DB::raw(' CODIGO,DESCRIPCION,USER, substr(fecaltas,1,10) AS FECALTAS'))
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

            $totalFiltered = Categoria::where(function ($query) use ($search) {
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
    public static function categorias_datatable($request)
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

        $totalData = Categoria::count();  
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

            $posts = Categoria::select(DB::raw('CODIGO,DESCRIPCION'))
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

            $posts =Categoria::select(DB::raw('CODIGO,DESCRIPCION'))
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

            $totalFiltered = Categoria::where(function ($query) use ($search) {
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
 public static function categoria_guardar($datos)
    {

        $user = auth()->user();
        $dia = date("Y-m-d");
        $hora = date("H:i:s");
        $codigo_categoria=$datos['data']['Codigo'];

            //var_dump($datos['data']['Marcados']);
        try { 
        /*  --------------------------------------------------------------------------------- */
         if($datos['data']['Existe']=== false){
         $categoria=Categoria::insertGetId(
         ['DESCRIPCION'=> $datos['data']['Descripcion'], 'USER'=>$user->name,'FECALTAS'=>$dia,'HORALTAS'=>$hora]);
         $codigo_categoria = $categoria;
  
          foreach ($datos['data']['Marcados'] as $key => $marcados) {


                 $categoriaporSublinea=DB::connection('retail')->table('lineas_tiene_sublineas')->insertGetId(
                 ['FK_COD_LINEA'=> $codigo_categoria,'FK_COD_SUBLINEA'=> $marcados , 'FK_USER_CR'=>$user->id,'FECALTAS'=>$dia,'HORALTAS'=>$hora,'ACTIVO'=>1]);
 
         
          }
                    foreach ($datos['data']['MarcaMarcados'] as $key => $marcados) {


                 $categoriaporMarca=DB::connection('retail')->table('lineas_tiene_marcas')->insertGetId(
                 ['FK_COD_LINEA_LINEAS_TIENE_MARCAS'=> $codigo_categoria,'FK_COD_MARCA'=> substr($marcados,9) , 'FK_USER_CR_LINEAS_TIENE_MARCAS'=>$user->id,'FECALTAS'=>$dia,'HORALTAS'=>$hora,'ACTIVO'=>1]);
 
         
          }

        }else{
             //LINEAS UPDATE
            $categoria=Categoria::where('CODIGO', $codigo_categoria)
            ->update(['DESCRIPCION'=> $datos['data']['Descripcion'], 'USERM'=>$user->name,'FECMODIF'=>$dia,'HORMODIF'=>$hora]);
            // ------------------------------------------------------------------------
            //LINEAS_TIENE_SUBLINEAS UPDATE ACTIVOS

            $activos=DB::connection('retail')->table('lineas_tiene_sublineas')->where('FK_COD_LINEA', $codigo_categoria)
            ->update(['ACTIVO'=> 0,'FECMODIF'=>$dia,'FK_USER_MD'=>$user->id,"HORMODIF"=>$hora]);
            // ------------------------------------------------------------------------
            //LINEAS_TIENE_MARCAS UPDATE ACTIVOS

            $activos_marca=DB::connection('retail')->table('lineas_tiene_marcas')->where('FK_COD_LINEA_LINEAS_TIENE_MARCAS', $codigo_categoria)
            ->update(['ACTIVO'=> 0,'FECMODIF'=>$dia,'FK_USER_MD_LINEAS_TIENE_MARCAS'=>$user->id,"HORMODIF"=>$hora]);
           // ------------------------------------------------------------------------

            // INSERTAR O ACTUALIZAR EN LINEAS_TIENE_SUBLINEAS
           foreach ($datos['data']['Marcados'] as $key => $marcados){
          $categoriaporSublinea=DB::connection('retail')->table('lineas_tiene_sublineas')->select(DB::raw('FK_COD_SUBLINEA'))
          ->Where('FK_COD_LINEA','=',$codigo_categoria)
          ->where('FK_COD_SUBLINEA','=',$marcados)
          ->get()
          ->toArray();

         if(count($categoriaporSublinea)<=0){
            $categoriaporSublinea1=DB::connection('retail')->table('lineas_tiene_sublineas')->insertGetId(
            ['FK_COD_LINEA'=> $codigo_categoria,'FK_COD_SUBLINEA'=> $marcados , 'FK_USER_CR'=>$user->id,'FECALTAS'=>$dia,'HORALTAS'=>$hora,'ACTIVO'=>1]);

          }else{
            $activos=DB::connection('retail')->table('lineas_tiene_sublineas')
            ->where('FK_COD_LINEA', $codigo_categoria)
            ->where('FK_COD_SUBLINEA','=',$marcados)
            ->update(['ACTIVO'=> 1]);
         }

         }
         // ------------------------------------------------------------------------
         // INSERTAR O ACTUALIZAR EN LINEAS_TIENE_MARCAS
           foreach ($datos['data']['MarcaMarcados'] as $key => $marcados){
          $categoriaporMarcas=DB::connection('retail')->table('lineas_tiene_Marcas')->select(DB::raw('FK_COD_MARCA'))
          ->Where('FK_COD_LINEA_LINEAS_TIENE_MARCAS','=',$codigo_categoria)
          ->where('FK_COD_MARCA','=',substr($marcados,9))
          ->get()
          ->toArray();

         if(count($categoriaporMarcas)<=0){
            $categoriaporMarcas1=DB::connection('retail')->table('lineas_tiene_Marcas')->insertGetId(
            ['FK_COD_LINEA_LINEAS_TIENE_MARCAS'=> $codigo_categoria,'FK_COD_MARCA'=> substr($marcados,9) , 'FK_USER_CR_LINEAS_TIENE_MARCAS'=>$user->id,'FECALTAS'=>$dia,'HORALTAS'=>$hora,'ACTIVO'=>1]);

          }else{
            $activos=DB::connection('retail')->table('lineas_tiene_Marcas')
            ->where('FK_COD_LINEA_LINEAS_TIENE_MARCAS', $codigo_categoria)
            ->where('FK_COD_MARCA','=',substr($marcados,9))
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
             public static function Categoria_eliminar($datos)
    {
        $user = auth()->user();
        $dia = date("Y-m-d");
        $hora = date("H:i:s");
        $codigo_categoria=$datos['data']['Codigo'];
        /*  --------------------------------------------------------------------------------- */

        if($datos['data']['Existe']=== true){
        
         $producto=Producto::select('CODIGO')->Where('linea','=',$codigo_categoria)->limit(1)->get()->toArray();
         if(count($producto)>0){
            return ["response"=>false];
         }else{
            $activos=DB::connection('retail')->table('lineas_tiene_sublineas')->where('FK_COD_LINEA', $codigo_categoria)
            ->update(['FECMODIF'=>$dia,'FK_USER_MD'=>$user->id,"HORMODIF"=>$hora]);

            $activos1=DB::connection('retail')->table('lineas_tiene_marcas')->where('FK_COD_LINEA_LINEAS_TIENE_MARCAS', $codigo_categoria)
            ->update(['FECMODIF'=>$dia,'FK_USER_MD_LINEAS_TIENE_MARCAS'=>$user->id,"HORMODIF"=>$hora]);

        $categoriaporSublinea=DB::connection('retail')->table('lineas_tiene_sublineas')->Where(['FK_COD_LINEA'=> $codigo_categoria])->delete();
        $categoriaporMarca=DB::connection('retail')->table('lineas_tiene_marcas')->Where(['FK_COD_LINEA_LINEAS_TIENE_MARCAS'=> $codigo_categoria])->delete();
        $Categoria=Categoria::where('CODIGO', $codigo_categoria)->delete();
                    }

        }else{
        return ["response"=>false];
        }
  return ["response"=>true];

        /*  --------------------------------------------------------------------------------- */

    }
}
