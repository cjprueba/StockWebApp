<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Piso;
use Illuminate\Support\Facades\DB;

class PisoController extends Controller
{	
	public function mostrar(Request $request){
		$columns = array( 
            0 =>'ID',
            1 =>'NRO_PISO', 
            2 =>'DESCRIPCION',
            3=> 'ID_SUCURSAL',
        );
        $totalData = Piso::count();
            
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if(empty($request->input('search.value')))
        {            
            $posts = Piso::select(DB::raw('PISOS.ID, PISOS.DESCRIPCION, PISOS.DESCRIPCION'))
            	->join('PISOS', 'ID_SUCURSAL', '=', 'PISOS.NRO_PISO')
       			->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        }else {
            $search = $request->input('search.value'); 

            $posts =  Piso::select(DB::raw('PISOS.ID, PISOS.DESCRIPCION AS PISOS_DESCRIPCION, SUCURSALES.DESCRIPCION AS SUCURSALES_DESCRIPCION'))
				->join('SUCURSALES', 'ID_SUCURSAL', '=', 'SUCURSALES.CODIGO')
				->where('PISOS.NRO_PISO','LIKE',"%{$search}%")
                ->orWhere('PISOS.DESCRIPCION', 'LIKE',"%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
            $totalFiltered = Piso::where('NRO_PISO','LIKE',"%{$search}%")
		        ->orWhere('DESCRIPCION', 'LIKE',"%{$search}%")
		        ->count();
		}


		$data = array();
        if(!empty($posts))
        {
            foreach ($posts as $post)
            {
                // $show =  route('posts.show',$post->id);
                // $edit =  route('posts.edit',$post->id);

                $nestedData['NRO_PISO'] = $post->NRO_PISO;
                $nestedData['DESCRIPCION'] = $post->PISOS_DESCRIPCION;
                $nestedData['ID_SUCURSAL'] = $post->SUCURSALES_DESCRIPCION;
                // $nestedData['ID_SUCURSAL'] = "&emsp;<a href='{$show}' title='SHOW' ><span class='glyphicon glyphicon-list'></span></a>
                //                           &emsp;<a href='{$edit}' title='EDIT' ><span class='glyphicon glyphicon-edit'></span></a>";
                $data[] = $nestedData;

            }
        }


        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );
            
        echo json_encode($json_data); 

	}
	public function datatable(Request $request){
        /*  --------------------------------------------------------------------------------- */

        // Mostrar Colores

        $pisos = Piso::pisos_datatable($request);
        return response()->json($pisos);
        
        /*  --------------------------------------------------------------------------------- */
    }

	public function encontrar(Request $request){
        $pisos = Piso::encontrarPiso($request->all());
        return response()->json($pisos);
    }

	public function filtrarPiso(Request $request){
         /*  --------------------------------------------------------------------------------- */

        // OBTENER

        $piso = Piso::filtrarPiso($request->all());
        return response()->json($piso);

        /*  --------------------------------------------------------------------------------- */
    }

	public function PisoNuevo(){
         /*  --------------------------------------------------------------------------------- */
        // OBTENER
        $piso = Piso::nuevoPiso();
        return response()->json($piso);

        /*  --------------------------------------------------------------------------------- */
    }

	public function PisoGuardar(Request $request){
        // OBTENER

        $piso = Piso::guardarPiso($request->all());
        return response()->json($piso);   
    }

    public function PisoEliminar(Request $request){
        $piso = Piso::eliminarPiso($request->all());
        return response()->json($piso);
    }
}
