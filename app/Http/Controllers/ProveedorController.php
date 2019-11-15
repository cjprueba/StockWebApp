<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Proveedor;

class ProveedorController extends Controller
{
    public function obtenerProveedores(){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER PROVEEDORES

        $proveedor = Proveedor::obtener_proveedores();
        return response()->json($proveedor);

        /*  --------------------------------------------------------------------------------- */
    }
}
