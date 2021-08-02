<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Gondola_Tiene_Piso;

class Piso extends Model
{
	protected $connection = 'retail';
    protected $table = 'pisos';
    public $timestamps = false;

    public static function pisos_datatable($request){

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 
        $user = auth()->user();
        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'ID', 
                            1 => 'NRO_PISO',
                            2 => 'DESCRIPCION'
                        );
        // CONTAR LA CANTIDAD DE SECCIONES ENCONTRADAS 
        $totalData = Piso::
        where('ID_SUCURSAL','=', $user->id_sucursal)
        ->count();
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

            //  CARGAR TODOS LAS SECCIONES ENCONTRADOS 

            $posts = Piso::select(DB::raw('ID, NRO_PISO, DESCRIPCION'))
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

            // CARGAR LOS PISOS FILTRADOS EN DATATABLE

            $posts = Piso::select(DB::raw('ID, NRO_PISO, DESCRIPCION'))
                            ->where('ID_SUCURSAL','=', $user->id_sucursal)
                            ->where(function ($query) use ($search) {
                                $query->where('NRO_PISO','LIKE',"%{$search}%")
                                      ->orWhere('DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PISOS FILTRADOS 

            $totalFiltered = Piso::where(function ($query) use ($search) {
                                $query->where('NRO_PISO','LIKE',"%{$search}%")
                                      ->orWhere('DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                             ->where('ID_SUCURSAL','=', $user->id_sucursal)
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
                $nestedData['ID'] = $post->ID;
                $nestedData['NRO_PISO'] = $post->NRO_PISO;
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

    public static function mostrarPiso($data){

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER TODOS LOS EMPLEADOS

    	$pisos = Piso::select(DB::raw('NRO_PISO, DESCRIPCION, ID_SUCURSAL'))
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if (count($pisos) > 0) {
        	return ['pisos' => $pisos];
        } else {
        	return ['pisos' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function filtrarPiso($datos){


        $piso=piso::select(DB::raw('NRO_PISO, 
            DESCRIPCION')
        )
        ->where('NRO_PISO','=', $datos['data'])
        ->get();
        if(count($piso)>0){
        	return['piso'=> $piso, 'response'=>true];
        }else{
        	return['piso'=> $piso, 'response'=>false];
        }

        
            
    }

    public static function encontrarPiso($data)
    {

    	/*  --------------------------------------------------------------------------------- */
    	   
    	// OBTENER TODAS LAS SUCURSALES

    	$piso = Piso::select(DB::raw('ID, NRO_PISO, DESCRIPCION, ID_SUCURSAL'))
        ->where('ID', '=', $data['codigo'])
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
        
        if (count($piso) > 0) {
            return ['piso' => $piso];
        } else {
            return ['piso' => 0];
        }

        /*  --------------------------------------------------------------------------------- */
    }


    public static function nuevoPiso(){
         $user = auth()->user();

        //OBTENER EL ULTIMO CODIGO DE LA SECCION
        $piso = Piso::select(DB::raw('IFNULL(NRO_PISO,0) AS NRO_PISO'))
                        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
                        ->orderby('NRO_PISO','DESC')
                        ->limit(1)
                        ->get()
                        ->toArray();
        
        // RETORNAR EL VALOR
        if(count($piso)>0){
            return ["piso" => $piso[0]["NRO_PISO"]];
        }else{

            return ["piso" => 0];
        }

    }

    public static function guardarPiso($datos){

        $user = auth()->user();
        $dia = date("Y-m-d");
        $hora = date("H:i:s");
        try{
            DB::connection('retail')->beginTransaction();

            if($datos['data']['btn_guardar']==true){

                $piso=piso::insertGetId([
                    'NRO_PISO'=> $datos['data']['Nro_Piso'],
                    'DESCRIPCION'=> $datos['data']['descripcion'],
                    'HORALTAS' => $hora,
                    'FECALTAS' => $dia,
                    'FK_USERALTAS' => strtoupper($user->name),
                    'HORMODIF' => $hora,
                    'FECMODIF' => $dia,
                    'FK_USERMODIF' => strtoupper($user->name),
                    'ID_SUCURSAL'=>$user->id_sucursal
                ]);

                DB::connection('retail')->commit();
                return['response'=>true];
            }else{
            	return ["response"=>false,'statusText'=>'¡Este número de piso ya fue registrado!'];
            }
        }
        catch(Exception $ex){
            DB::connection('retail')->rollBack();
            if($ex->errorInfo[1]==1062){
                return ["response"=>false,'statusText'=>'¡Este número de piso ya fue registrado!'];
            }else{
                return ["response"=>false,'statusText'=>$ex->getMessage()];
            }
        }
    }

    public static function eliminarPiso($datos){
        $user = auth()->user();

        if($datos['data']['btn_guardar']==false){

            
            
            $piso_gondola = Piso::verificarPisoTieneGondola($datos['data']['Nro_Piso']);
            
            if($piso_gondola['response']==false){
                return $piso_gondola;
            }

            $piso = Piso::where('NRO_PISO','=',$datos['data']['Nro_Piso'])
            				->where('ID_SUCURSAL', '=', $user->id_sucursal)->delete();

            return ['response'=>true];
        }else{
            return["response"=>false, 'statusText'=> 'No existe este piso '.$datos['data']['Nro_Piso']];
        }
    }

   public static function verificarPisoTieneGondola($codigo){

        $user = auth()->user();

        $piso = Gondola_Tiene_Piso::select('ID')
        ->where('FK_PISO', '=', $codigo)
        ->limit(1)
        ->get();

         // RETORNAR EL VALOR

        if(count($piso) > 0 && !empty($codigo)){

            return  ["response"=>false,'statusText'=> 'Existe una gondola cargado con este piso.'];
        } else {

            // MUESTRA QUE NO EXISTE

            return ["response"=>true];

        }
    }

    
}
