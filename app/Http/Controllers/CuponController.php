<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cupon;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CuponController extends Controller
{
    //
        public function datatable(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        return Cupon::datatable_cupon($request);

        }
      public function CrearCupon()
    {

        /*  --------------------------------------------------------------------------------- */

        return Cupon::crear_cupon();

    }
          public function ConseguirCupon(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        return Cupon::conseguir_cupon($request->all());

    }
        public function cuponGuardar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Usuarios

        $cupon = Cupon::guardar_cupon($request->all());
        return response()->json($cupon);
        
        /*  --------------------------------------------------------------------------------- */

    }
            public function cuponModificar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Usuarios

        $cupon = Cupon::modificar_cupon($request->all());
        return response()->json($cupon);
        
        /*  --------------------------------------------------------------------------------- */

    }
              public function cuponDeshabilitar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Usuarios

        $cupon = Cupon::deshabilitar_cupon($request->all());
        return response()->json($cupon);
        
        /*  --------------------------------------------------------------------------------- */

    }
     public function cuponHabilitar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Usuarios

        $cupon = Cupon::habilitar_cupon($request->all());
        return response()->json($cupon);
        
        /*  --------------------------------------------------------------------------------- */

    }
        /*  --------------------------------------------------------------------------------- */

}
