<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sector;
use Illuminate\Support\Facades\DB;

class SectorController extends Controller
{
	public function mostrar(Request $request){
		$columns = array( 
            0 =>'ID', 
            2 =>'DESCRIPCION',
            3=> 'ID_SUCURSAL',
        );
        $totalData = Sector::count();
            
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        if(empty($request->input('search.value')))
        {            
            $posts = Sector::select(DB::raw('SECTORES.ID, SECTORES.DESCRIPCION'))
            	->join('SECTORES', 'ID_SUCURSAL', '=', 'SECTORES.DESCRIPCION')
       			->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        }else {
            $search = $request->input('search.value'); 

            $posts =  Sector::select(DB::raw('SECTORES.ID, SECTORES.DESCRIPCION AS SECTORES_DESCRIPCION, SUCURSALES.DESCRIPCION AS SUCURSALES_DESCRIPCION'))
				->join('SUCURSALES', 'ID_SUCURSAL', '=', 'SUCURSALES.CODIGO')
                ->where('SECTORES.DESCRIPCION', 'LIKE',"%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
            $totalFiltered = Sector::where('DESCRIPCION', 'LIKE',"%{$search}%")
		        ->count();
		}


		$data = array();
        if(!empty($posts))
        {
            foreach ($posts as $post)
            {
                // $show =  route('posts.show',$post->id);
                // $edit =  route('posts.edit',$post->id);
                $nestedData['DESCRIPCION'] = $post->SECTORES_DESCRIPCION;
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

        $sectores = Sector::sectores_datatable($request);
        return response()->json($sectores);
        
        /*  --------------------------------------------------------------------------------- */
    }

	public function encontrar(Request $request){
        $sectores = Sector::encontrarSector($request->all());
        return response()->json($sectores);
    }

	public function SectorNuevo(){
         /*  --------------------------------------------------------------------------------- */
        // OBTENER
        $sector = Sector::nuevoSector();
        return response()->json($sector);

        /*  --------------------------------------------------------------------------------- */
    }
    public function filtrarSector(Request $request){
         /*  --------------------------------------------------------------------------------- */

        // OBTENER

        $sector = Sector::filtrarSector($request->all());
        return response()->json($sector);

        /*  --------------------------------------------------------------------------------- */
    }
    public function SectorGuardar(Request $request){
        // OBTENER

        $sector = Sector::guardarSector($request->all());
        return response()->json($sector);   
    }

    public function SectorEliminar(Request $request){
        $sector = Sector::eliminarSector($request->all());
        return response()->json($sector);
    }
}
