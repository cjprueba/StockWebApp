<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusquedaRmController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){

        /*  *********** USUARIO *********** */

        $user=auth()->user();

        /*  *********** SECTORES *********** */

        $sectores = DB::connection('retail')->table('SECTORES_RM')->select(DB::raw('ID, DESCRIPCION, DESC_CORTA'))
        ->orderBy('ID')
        ->get();

        /*  *********** SUCURSALES *********** */

        $sucursales = DB::connection('retail')->table('SUCURSALES_RM')->select(DB::raw('ID, DESCRIPCION, DESC_CORTA'))
        ->orderBy('ID')
        ->get();

        /*  *********** RETORNAR VALORES *********** */

        return ['sucursales' => $sucursales, 'sectores' => $sectores];
    }
}
