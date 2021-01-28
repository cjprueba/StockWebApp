<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Proveedor;
use App\Cliente;
use App\Categoria;
use App\Empleado;
use App\Marca;
use App\Sucursal;
use App\SubCategoria;

class BusquedaController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        /*  *********** USUARIO *********** */

        $user=auth()->user();

        /*  *********** SUCURSALES *********** */

        $sucursales = 0;

        // LIMITAR SOLO CRISTIAN Y JOHN

        if ($user->id === 24 || $user->id === 1) {

            $sucursales = Sucursal::select(DB::raw('CODIGO, DESCRIPCION'))
            ->orderBy('CODIGO')
            ->get();

        } else {

            $sucursales = Sucursal::select(DB::raw('CODIGO, DESCRIPCION'))-> where('CODIGO','=',$user->id_sucursal)
            ->orderBy('CODIGO')
            ->get();

        }
            $sucursalesgeneral = Sucursal::select(DB::raw('CODIGO, DESCRIPCION'))
            ->orderBy('CODIGO')
            ->get();
        

        /*  *********** MARCAS *********** */

        $marcas = Marca::select(DB::raw('CODIGO, DESCRIPCION'))
        ->orderBy('CODIGO')
        ->get();

        /*  *********** CATEGORIAS *********** */

        $categorias = Categoria::select(DB::raw('CODIGO, DESCRIPCION'))
        ->orderBy('CODIGO')
        ->get();

        /*  *********** VENDEDORES *********** */

        $vendedor = Empleado::select(DB::raw('CODIGO, NOMBRE'))
        ->where('ID_SUCURSAL','=',$user->id_sucursal)
        ->orderBy('CODIGO')
        ->get();

        /*  *********** CLIENTES *********** */

        $cliente = Cliente::select(DB::raw('CODIGO, NOMBRE'))
        ->where('ID_SUCURSAL','=',$user->id_sucursal)
        ->orderBy('NOMBRE')
        ->get();

         /*  *********** PROVEEDORES *********** */

        $proveedores = Proveedor::select(DB::raw('CODIGO, NOMBRE'))
        ->orderBy('CODIGO')
        ->get();

         /*  *********** OBTENER LA SECCION DEL USER *********** */

        $seccion = DB::connection('retail')
            ->table('USERS_TIENE_SECCION')
            ->leftjoin('SECCIONES', 'SECCIONES.ID', '=', 'USERS_TIENE_SECCION.FK_SECCION')
            ->select(DB::raw('IFNULL(FK_SECCION, 0) AS ID_SECCION'),
                    DB::raw('IFNULL(SECCIONES.DESCRIPCION, 0) AS DESCRIPCION'))
            ->where('FK_USER', '=', $user->id)
        ->get()
        ->toArray();

        if(count($seccion)>0){

            /*  *********** CATEGORIAS POR SECCION *********** */

            $seccionCategorias = DB::connection('retail')
                ->table('LINEA_SUBLINEA_TIENE_SECCION')
                ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'LINEA_SUBLINEA_TIENE_SECCION.LINEA')
                ->select(DB::raw('LINEAS.CODIGO, LINEAS.DESCRIPCION'))
            ->where('LINEA_SUBLINEA_TIENE_SECCION.SECCION', '=', $seccion[0]->ID_SECCION)
            ->where('LINEA_SUBLINEA_TIENE_SECCION.ID_SUCURSAL','=',$user->id_sucursal)
            ->groupBy('LINEA_SUBLINEA_TIENE_SECCION.LINEA')
            ->orderBy('LINEAS.DESCRIPCION')
            ->get();

            /*  *********** SUB CATEGORIAS POR SECCION *********** */

            $subCategorias = DB::connection('retail')
                ->table('LINEA_SUBLINEA_TIENE_SECCION')
                ->leftjoin('SUBLINEAS', 'SUBLINEAS.CODIGO', '=', 'LINEA_SUBLINEA_TIENE_SECCION.SUBLINEA')
                ->select(DB::raw('SUBLINEAS.CODIGO, SUBLINEAS.DESCRIPCION'))
            ->where('LINEA_SUBLINEA_TIENE_SECCION.SECCION', '=', $seccion[0]->ID_SECCION)
            ->where('LINEA_SUBLINEA_TIENE_SECCION.ID_SUCURSAL','=',$user->id_sucursal)
            ->groupBy('LINEA_SUBLINEA_TIENE_SECCION.SUBLINEA')
            ->orderBy('SUBLINEAS.DESCRIPCION')
            ->get();

            /*  *********** RETORNAR VALORES *********** */

            return ['sucursales' => $sucursales, 
                    'marcas' => $marcas, 
                    'categorias' => $categorias,
                    'vendedor'=>$vendedor,
                    'cliente'=>$cliente,
                    'proveedores'=>$proveedores,
                    'sucursalesgeneral'=>$sucursalesgeneral, 
                    'seccionCategorias' => $seccionCategorias, 
                    'subCategorias' => $subCategorias,
                    'seccion' => $seccion];

        }else{


            $seccion = DB::connection('retail')
                ->table('SECCIONES')
                ->select(DB::raw('IFNULL(ID, 0) AS ID_SECCION'),
                        DB::raw('IFNULL(DESCRIPCION, 0) AS DESCRIPCION'))
            ->where('ID_SUCURSAL','=',$user->id_sucursal)
            ->get()
            ->toArray();

            /*  *********** RETORNAR VALORES *********** */

            return ['sucursales' => $sucursales, 
                    'marcas' => $marcas, 
                    'categorias' => $categorias,
                    'vendedor'=>$vendedor,
                    'cliente'=>$cliente,
                    'proveedores'=>$proveedores,
                    'sucursalesgeneral'=>$sucursalesgeneral,
                    'seccion' => $seccion];
        }
    }
}
