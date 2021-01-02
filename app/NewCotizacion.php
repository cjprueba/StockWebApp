<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Common;
use App\Parametro;

class NewCotizacion extends Model
{

    protected $connection = 'retail';
    protected $table = 'cotizaciones';
    public $timestamps = false;
    public static function obtener_cotizaciones($request)
    {

        $dia = date("Y-m-d");
        $user = auth()->user();

      

        /*  --------------------------------------------------------------------------------- */

        // INICIARA VARIABLES

        //global $search;

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 



        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'ID', 
                            1 => 'DE',
                            2 => 'A',
                            3 => 'VALOR',
                            4 => 'FECHA',
                            5 => 'FECALTAS',
                         
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = NewCotizacion::where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->count();

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

             $posts =NewCotizacion::
            select(DB::raw('cotizaciones.ID,monedas1.DESCRIPCION AS DE,monedas2.DESCRIPCION AS A,cotizaciones.VALOR,cotizaciones.FECHA,cotizaciones.FECALTAS'))
            ->leftjoin('monedas as monedas1','monedas1.codigo','=','FK_DE')
            ->leftjoin('monedas as monedas2','monedas2.codigo','=','FK_A')
            ->where('cotizaciones.ID_SUCURSAL', '=', $user->id_sucursal)

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

            
                $posts = NewCotizacion::
            select(DB::raw(
            	'cotizaciones.ID,
            	monedas1.DESCRIPCION AS DE,
            	monedas2.DESCRIPCION AS A,
            	cotizaciones.VALOR,
            	cotizaciones.FECHA,
            	cotizaciones.FECALTAS'))
            ->leftjoin('monedas as monedas1','monedas1.codigo','=','FK_DE')
            ->leftjoin('monedas as monedas2','monedas2.codigo','=','FK_A')
            ->where('cotizaciones.ID_SUCURSAL', '=', $user->id_sucursal)
            ->where(function ($query) use ($search) {
                                $query->where('monedas1.DESCRIPCION','LIKE',"%{$search}%")
                                      ->orWhere('monedas2.DESCRIPCION', 'LIKE',"%{$search}%")
                                      ->orWhere('cotizaciones.ID', 'LIKE',"%{$search}%")
                                      ->orWhere('cotizaciones.FECHA', 'LIKE',"%{$search}%");
                            })

                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered =NewCotizacion::leftjoin('monedas as monedas1','monedas1.codigo','=','FK_DE')
            ->leftjoin('monedas as monedas2','monedas2.codigo','=','FK_A')
            ->where('cotizaciones.ID_SUCURSAL', '=', $user->id_sucursal)
            ->where(function ($query) use ($search) {
                                  $query->where('monedas1.DESCRIPCION','LIKE',"%{$search}%")
                                      ->orWhere('monedas2.DESCRIPCION', 'LIKE',"%{$search}%")
                                      ->orWhere('cotizaciones.ID', 'LIKE',"%{$search}%")
                                      ->orWhere('cotizaciones.FECHA', 'LIKE',"%{$search}%");
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

                $nestedData['ID'] = $post->ID;
                $nestedData['DE'] = $post->DE;
                $nestedData['A'] = $post->A;
                $nestedData['FECHA'] = $post->FECHA;
                $nestedData['FECALTAS'] = $post->FECALTAS;
                $nestedData['VALOR'] = $post->VALOR;
               

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


        /*  --------------------------------------------------------------------------------- */
       

        /*  --------------------------------------------------------------------------------- */    
         
    }
      public static function guardar_cotizacion($datos){
      	try {
      		DB::connection('retail')->beginTransaction();

        // INICIAR VARIABLES 
      $user = auth()->user();
			$dia = date("Y-m-d");
			$hora = date("H:i:s");
        
			 // OBTENER ID DE FORMULA
			 $FORMULA = DB::connection('retail')
            ->table('FORMULAS_COTIZACION')
            ->select(DB::raw('ifnull(ID,0) AS ID'))
            ->where('FK_DE', '=',$datos['data']['de'])
            ->where('FK_A', '=', $datos['data']['a'])
            ->get()->toArray();
            
              $cotizacion=NewCotizacion::insert([
                'FK_DE'=> $datos['data']['de'], 
                'FK_A'=> $datos['data']['a'],
                'VALOR'=> Common::quitar_coma($datos['data']['valor'], 2),
                'FECHA'=> $datos['data']['fecha'],
                'FK_FORMULA'=> $FORMULA[0]->ID,
                'FK_USER'=> $user->id,
                'ID_SUCURSAL' => $user->id_sucursal,
                'FECALTAS'=> $dia,
                'HORALTAS'=> $hora]);

            DB::connection('retail')->commit();

            return ["response"=>true,"cotizacion"=>$cotizacion];
      	} catch (Exception $e) {
      	   DB::connection('retail')->rollBack();

           throw $e;

           return ["response"=>false,"statusText"=>"Ha ocurrido un error!. Reporten el error a los encargados del sistema!"];
      	}
      	/*  --------------------------------------------------------------------------------- */



      }
}
