<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Gondola_Tiene_Sector;

class Sector extends Model
{
	protected $connection = 'retail';
    protected $table = 'sectores';
    public $timestamps = false;

    public static function sectores_datatable($request){

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 
        $user = auth()->user();
        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'ID',
                            1 => 'DESCRIPCION'
                        );
        // CONTAR LA CANTIDAD DE SECCIONES ENCONTRADAS 
        $totalData = Sector::
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

            $posts = Sector::select(DB::raw('ID, DESCRIPCION'))
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

            // CARGAR LOS SECTORES FILTRADOS EN DATATABLE

            $posts = Sector::select(DB::raw('ID, DESCRIPCION'))
                            ->where('ID_SUCURSAL','=', $user->id_sucursal)
                            ->where(function ($query) use ($search) {
                                $query->where('DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE SECTORES FILTRADOS 

            $totalFiltered = Sector::where(function ($query) use ($search) {
                                $query->where('DESCRIPCION', 'LIKE',"%{$search}%");
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

    public static function mostrarSector($data){

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER TODOS LOS EMPLEADOS

    	$sectores = Sector::select(DB::raw('DESCRIPCION, ID_SUCURSAL'))
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if (count($sectores) > 0) {
        	return ['sectores' => $sectores];
        } else {
        	return ['sectores' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
    
    public static function filtrarSector($datos){
    	
        $sector=sector::select(DB::raw('DESCRIPCION'))
        ->where('ID','=', $datos['data'])
        ->orWhere('DESCRIPCION','=', $datos['data'])
        ->get();
        if(count($sector)>0){
        	return["sector"=> $sector, "response"=>true];
        }else{
        	return["sector"=> $sector, "response"=>false];
        }       
    }

    public static function encontrarSector($data)
    {

    	/*  --------------------------------------------------------------------------------- */
    	   
    	// OBTENER TODAS LAS SUCURSALES

    	$sector = Sector::select(DB::raw('ID, DESCRIPCION, ID_SUCURSAL'))
        ->where('ID', '=', $data['codigo'])
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR
        
        if (count($sector) > 0) {
            return ['sector' => $sector];
        } else {
            return ['sector' => 0];
        }

        /*  --------------------------------------------------------------------------------- */
    }

    public static function nuevoSector(){
         $user = auth()->user();

        //OBTENER EL ULTIMO CODIGO DE LA SECCION
        $sector = Sector::select(DB::raw('IFNULL(DESCRIPCION,NULL) AS DESCRIPCION'))
                        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
                        ->orderby('DESCRIPCION','DESC')
                        ->limit(1)
                        ->get()
                        ->toArray();
        
        // RETORNAR EL VALOR
        if(count($sector)>0){
            return ["sector" => $sector[0]["DESCRIPCION"]];
        }else{

            return ["sector" => 0];
        }
    }
    public static function guardarSector($datos){
        $user = auth()->user();
        $dia = date("Y-m-d");
        $hora = date("H:i:s");
        try{
            DB::connection('retail')->beginTransaction();

            if($datos['data']['btn_guardar']==true){

                $sector=sector::insertGetId([
                    'DESCRIPCION'=> $datos['data']['Sector'],
                    'FK_USER_ALTA'=> strtoupper($user->name),
                    'HORALTAS' => $hora,
                    'FECALTAS' => $dia,
                    'HORMODIF' => $hora,
                    'FECMODIF' => $dia,
                    'FK_USER_MODIF' => strtoupper($user->name),
                    'ID_SUCURSAL'=>$user->id_sucursal,
                ]);

                DB::connection('retail')->commit();
                return['response'=>true];
            }else{
            	return ["response"=>false,'statusText'=>'¡Este sector ya fue registrado!'];
            }
        }
        catch(Exception $ex){
            DB::connection('retail')->rollBack();
            if($ex->errorInfo[1]==1062){
                return ["response"=>false,'statusText'=>'¡Este sector ya fue registrado!'];
            }else{
                return ["response"=>false,'statusText'=>$ex->getMessage()];
            }
        }
    }
    public static function eliminarSector($datos){
        $user = auth()->user();

        if($datos['data']['btn_guardar']==false){

            
            
            $sector_gondola = Sector::verificarSectorTieneGondola($datos['data']['Sector']);
            
            if($sector_gondola['response']==false){
                return $sector_gondola;
            }

            $sector = Sector::where('DESCRIPCION','=',$datos['data']['Sector'])
            			->where('ID_SUCURSAL', '=', $user->id_sucursal)->delete();
            return ['response'=>true];
        }else{
            return["response"=>false, 'statusText'=> 'No existe este sector '.$datos['data']['Sector']];
        }
    }

    public static function verificarSectorTieneGondola($codigo){

        $user = auth()->user();

        $sector = Gondola_Tiene_Sector::select('ID')
        ->where('FK_SECTOR', '=', $codigo)
        ->limit(1)
        ->get();

         // RETORNAR EL VALOR

        if(count($sector) > 0 && !empty($codigo)){

            return  ["response"=>false,'statusText'=> 'Existe una gondola cargado con este sector.'];
        } else {

            // MUESTRA QUE NO EXISTE

            return ["response"=>true];

        }
    }
}
