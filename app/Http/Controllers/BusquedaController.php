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

        $sucursales = Sucursal::select(DB::raw('CODIGO, DESCRIPCION'))-> where('CODIGO','=',$user->id_sucursal)
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

        /*  *********** RETORNAR VALORES *********** */

        return ['sucursales' => $sucursales, 'marcas' => $marcas, 'categorias' => $categorias,'vendedor'=>$vendedor,'cliente'=>$cliente,'proveedores'=>$proveedores];
    }

    
}
