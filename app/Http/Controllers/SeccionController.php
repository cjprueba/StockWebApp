<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Seccion;
use Illuminate\Support\Facades\DB;


class SeccionController extends Controller
{
	public function mostrar(Request $request){
		$columns = array( 
            0 =>'ID',
            1 =>'CODIGO', 
            2 =>'DESCRIPCION',
            3=> 'ID_SUCURSAL',
        );
        $totalData = Seccion::count();
            
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if(empty($request->input('search.value')))
        {            
            $posts = Seccion::select(DB::raw('SECCIONES.ID, SECCIONES.DESCRIPCION, SUCURSALES.DESCRIPCION'))
            	->join('SUCURSALES', 'ID_SUCURSAL', '=', 'SUCURSALES.CODIGO')
       			->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        }else {
            $search = $request->input('search.value'); 

            $posts =  Seccion::select(DB::raw('SECCIONES.ID, SECCIONES.DESCRIPCION AS SECCIONES_DESCRIPCION, SUCURSALES.DESCRIPCION AS SUCURSALES_DESCRIPCION'))
				->join('SUCURSALES', 'ID_SUCURSAL', '=', 'SUCURSALES.CODIGO')
				->where('SECCIONES.CODIGO','LIKE',"%{$search}%")
                ->orWhere('SECCIONES.DESCRIPCION', 'LIKE',"%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
            $totalFiltered = Seccion::where('CODIGO','LIKE',"%{$search}%")
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

                $nestedData['CODIGO'] = $post->ID;
                $nestedData['DESCRIPCION'] = $post->SECCIONES_DESCRIPCION;
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

	public function encontrar(Request $request){
        $secciones = Seccion::encontrarSeccion($request->all());
        return response()->json($secciones);
    }

    public function datatable(Request $request){
        /*  --------------------------------------------------------------------------------- */

        // Mostrar Colores

        $secciones = Seccion::secciones_datatable($request);
        return response()->json($secciones);
        
        /*  --------------------------------------------------------------------------------- */
    }

    public function seccionNuevo(){
         /*  --------------------------------------------------------------------------------- */

        // OBTENER

        $seccion = Seccion::nuevaSeccion();
        return response()->json($seccion);

        /*  --------------------------------------------------------------------------------- */
    }

    public function filtrarSeccion(Request $request){
         /*  --------------------------------------------------------------------------------- */

        // OBTENER

        $seccion = Seccion::filtrarSeccion($request->all());
        return response()->json($seccion);

        /*  --------------------------------------------------------------------------------- */
    }

    public function seccionGuardar(Request $request){
        // OBTENER

        $seccion = Seccion::guardarSeccion($request->all());
        return response()->json($seccion);
    }

    public function seccionEliminar(Request $request){
        $seccion = Seccion::eliminarSeccion($request->all());
        return response()->json($seccion);
    }
}
