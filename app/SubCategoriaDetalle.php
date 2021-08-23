<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SubCategoriaDetalle extends Model
{

	/*  --------------------------------------------------------------------------------- */

	// SUBCATEGORIA DETALLE 

	protected $connection = 'retail';
    protected $table = 'sublinea_det';
    protected $primaryKey='CODIGO';
    const CREATED_AT='FECALTAS';
    const UPDATED_AT='FECMODIF';

    /*  --------------------------------------------------------------------------------- */

    public static function obtener_subCategoriasDetalle()
    {
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */

        // REVISAR SUCURSALES 

        $activo = Sucursal::select(DB::raw('CR_DESCRIPCION'))
        ->where('CODIGO', '=', $user->id_sucursal)
        ->get();
        
        if ($activo[0]['CR_DESCRIPCION'] === 0) {
        	$activo = false;
        } else if ($activo[0]['CR_DESCRIPCION'] === 1) {
        	$activo = true;
        }
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODAS LAS SUB CATEGORIAS

        $subCategoriasDetalle = SubCategoriaDetalle::select(DB::raw('CODIGO, DESCRIPCION'))
        ->orderBy('DESCRIPCION')
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($subCategoriasDetalle) {
            return ['subCategoriasDetalle' => $subCategoriasDetalle, 'ACTIVO' => $activo];
        } else {
            return ['subCategoriasDetalle' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function datatable($request)
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

        $totalData = SubCategoriaDetalle::count();

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

            $posts = SubCategoriaDetalle::select(DB::raw('CODIGO,DESCRIPCION'))
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

            $posts = SubCategoriaDetalle::select(DB::raw('CODIGO,DESCRIPCION'))
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

            $totalFiltered = SubCategoriaDetalle::where(function ($query) use ($search) {
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

     public static function filtrar($datos)
    {

        /*  --------------------------------------------------------------------------------- */
        
        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        $subcategoriaDetalle = SubCategoriaDetalle::select(DB::raw('CODIGO, DESCRIPCION'))->Where('CODIGO','=',$datos['id'])->get()->toArray();

        if(count($subcategoriaDetalle)<=0){
           return ["response"=>false];
        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        return ["response"=>true,"subCategoriasDetalle"=>$subcategoriaDetalle];

        /*  --------------------------------------------------------------------------------- */

    }

    public static function nuevo()
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODAS LAS CATEGORIAS

        $subCategoriaDetalle = SubCategoriaDetalle::select('CODIGO')->orderby('CODIGO','DESC')->limit(1)
        ->get()->toArray();
      
        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($subCategoriaDetalle) {
            return ['subCategoriaDetalle' => $subCategoriaDetalle];
        } else {
            return ['subCategoriaDetalle' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function guardar($datos)
    {
        $user = auth()->user();
        $dia = date("Y-m-d");
        $hora = date("H:i:s");
        $codigo_nombre = $datos['data']['Codigo'];

        /*  --------------------------------------------------------------------------------- */
        try { 

            if($datos['data']['Existe']=== false){

             $subCategoriaDetalle=SubCategoriaDetalle::insertGetId(
             ['DESCRIPCION'=> $datos['data']['Descripcion'], 'FK_USER_CR'=>$user->id,'FECALTAS'=>$dia,'HORALTAS'=>$hora]);
             $codigo_nombre  = $subCategoriaDetalle;

            }else{

                $subCategoriaDetalle=SubCategoriaDetalle::where('CODIGO', $codigo_nombre )
                ->update(['DESCRIPCION'=> $datos['data']['Descripcion'], 'FK_USER_MD'=>$user->id,'FECMODIF'=>$dia,'HORMODIF'=>$hora]);

            }

            return ["response"=>true];

        } catch(Exception $ex){ 

            if($ex->errorInfo[1]===1062){
                  return ["response"=>false,'statusText'=>'Esta descripcion de SubCategoria Detalle ya fue registrada!!'];
            } else{
                return ["response"=>false,'statusText'=>$ex->getMessage()];
            }
          
        }
                


                /*  --------------------------------------------------------------------------------- */

    }

     public static function eliminar($datos)
    {
        $user = auth()->user();
        $dia = date("Y-m-d");
        $hora = date("H:i:s");
        $codigo_subcategoriadet=$datos['data']['Codigo'];
        /*  --------------------------------------------------------------------------------- */

        if($datos['data']['Existe']=== true){
        
         $producto=Producto::select('CODIGO')->Where('SUBLINEADET','=',$codigo_subcategoriadet)->limit(1)->get()->toArray();
         if(count($producto)>0){
            return ["response"=>false];
         }else{
            SubCategoriaDetalle::where('CODIGO', $codigo_subcategoriadet)->delete();
         }

        }else{
            return ["response"=>false];
        }

        return ["response"=>true];

        /*  --------------------------------------------------------------------------------- */

    }
}
