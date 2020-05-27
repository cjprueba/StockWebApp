<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Giro extends Model
{
    protected $connection = 'retail';
    protected $table = 'giro_entidades';

    public static function datatable_entidades($request) {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'CODIGO', 
                            1 => 'DESCRIPCION',
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TARJETAS ENCONTRADOS 

        $totalData = Giro::count();  
        
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

            $posts = Giro::select(DB::raw('ID AS CODIGO, NOMBRE'))
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

            $posts =  Giro::select(DB::raw('ID AS CODIGO, NOMBRE'))
            ->where(function ($query) use ($search) {
                $query->where('ID','LIKE',"{$search}%")
                ->orWhere('NOMBRE', 'LIKE',"%{$search}%");
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = Giro::where(function ($query) use ($search) {
                $query->where('ID','LIKE',"{$search}%")
                ->orWhere('NOMBRE', 'LIKE',"%{$search}%");
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
                $nestedData['DESCRIPCION'] = $post->NOMBRE;

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
    }
}