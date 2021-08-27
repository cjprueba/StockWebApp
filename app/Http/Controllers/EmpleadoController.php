<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Empleado;
use Illuminate\Support\Facades\DB;

class EmpleadoController extends Controller
{
     public function mostrar(Request $request)
    {

    	$columns = array( 
                            0 =>'ID', 
                            1 =>'NOMBRE',
                            2=> 'CI',
                            3=> 'DIRECCION',
                            4=> 'ID_SUCURSAL',
                        );
  
        $totalData = Empleado::count();
            
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
            
        if(empty($request->input('search.value')))
        {            
            $posts = Empleado::select(DB::raw('EMPLEADOS.ID, EMPLEADOS.NOMBRE, EMPLEADOS.CI, EMPLEADOS.DIRECCION, SUCURSALES.DESCRIPCION'))
            				->join('SUCURSALES', 'ID_SUCURSAL', '=', 'SUCURSALES.CODIGO')
            				->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();
        }
        else {
            $search = $request->input('search.value'); 

            $posts =  Empleado::select(DB::raw('EMPLEADOS.ID, EMPLEADOS.NOMBRE, EMPLEADOS.CI, EMPLEADOS.DIRECCION, SUCURSALES.DESCRIPCION'))
            				->join('SUCURSALES', 'ID_SUCURSAL', '=', 'SUCURSALES.CODIGO')
            				->where('EMPLEADOS.CODIGO','LIKE',"%{$search}%")
                            ->orWhere('EMPLEADOS.NOMBRE', 'LIKE',"%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            $totalFiltered = Empleado::where('CODIGO','LIKE',"%{$search}%")
                             ->orWhere('NOMBRE', 'LIKE',"%{$search}%")
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
                $nestedData['NOMBRE'] = $post->NOMBRE;
                $nestedData['CI'] = $post->CI;
                $nestedData['DIRECCION'] = $post->DIRECCION;
                $nestedData['ID_SUCURSAL'] = $post->DESCRIPCION;
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

        // $empleados = Empleado::mostrarEmpleado($request->all());
        // return response()->json($empleados);
    }

     public function encontrar(Request $request)
    {
        $empleados = Empleado::encontrarEmpleado($request->all());
        return response()->json($empleados);
    }
    public function datatable(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Colores

        $empleados = Empleado::empleados_datatable($request);
        return response()->json($empleados);
        
        /*  --------------------------------------------------------------------------------- */

    }
        public function datatable_recibe(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Colores

        $empleados = Empleado::empleados_datatable_recibe($request);
        return response()->json($empleados);
        
        /*  --------------------------------------------------------------------------------- */

    }
    public function empleadoNuevo(){
         /*  --------------------------------------------------------------------------------- */

        // OBTENER

        $empleado = Empleado::nuevoEmpleado();
        return response()->json($empleado);

        /*  --------------------------------------------------------------------------------- */
    }
    public function filtrarEmpleado(Request $request){
         /*  --------------------------------------------------------------------------------- */

        // OBTENER

        $empleado = Empleado::filtrarEmpleado($request->all());
        return response()->json($empleado);

        /*  --------------------------------------------------------------------------------- */
    }
    public function empleadoGuardar(Request $request){
        // OBTENER

        $empleado = Empleado::guardarEmpleado($request->all());
        return response()->json($empleado);
    }
    public function empleadoEliminar(Request $request){
        $empleado = Empleado::eliminarEmpleado($request->all());
        return response()->json($empleado);
    }
}
