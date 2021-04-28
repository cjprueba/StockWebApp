<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
class UserController extends Controller
{   
    public function obtenerSucursalUsuario()
    {
        $usuario = User::obtener_sucursal_usuario();
        return response()->json($usuario);
    }
    //
    public function obtenerRoles()
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $rol = User::obtener_roles();
        return response()->json($rol);
        
        /*  --------------------------------------------------------------------------------- */

    }
        public function filtrarPermisos(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

       // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $rol = User::obtener_permisos_roles($request->all());
        return response()->json($rol);
        
        /*  --------------------------------------------------------------------------------- */

    }
            public function filtrarPermiso(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

       // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $rol = User::obtener_permiso($request->all());
        return response()->json($rol);
        
        /*  --------------------------------------------------------------------------------- */

    }
        public function obtenerRolesUsuario(Request $request)
    { 

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $rol = User::obtener_usuario_roles($request->all());
        return response()->json($rol);
        
        /*  --------------------------------------------------------------------------------- */

    }
        public function obtenerPermisos()
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $rol = User::obtener_permisos();
        return response()->json($rol);
        
        /*  --------------------------------------------------------------------------------- */

    }
        public function guardarRol(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // GUARDAR ROLES

        $rol = User::guardar_rol($request->all());
        return response()->json($rol);
        
        /*  --------------------------------------------------------------------------------- */

    }
        public function asignarRol(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // GUARDAR ROLES

        $rol = User::asignar_rol($request->all());
        return response()->json($rol);
        
        /*  --------------------------------------------------------------------------------- */

    }
            public function asignarPermiso(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // GUARDAR ROLES

        $rol = User::asignar_permiso($request->all());
        return response()->json($rol);
        
        /*  --------------------------------------------------------------------------------- */

    }
            public function guardarPermiso(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // GUARDAR Permiso

        $Permiso = User::guardar_permiso($request->all());
        return response()->json($Permiso);
        
        /*  --------------------------------------------------------------------------------- */

    }
                public function guardarUsuario(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // GUARDAR Permiso

        $Permiso = User::guardar_usuario($request->all());
        return response()->json($Permiso);
        
        /*  --------------------------------------------------------------------------------- */

    }
        public function mostrar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Usuarios

        $Usuarios = User::mostrar_datatable($request);
        return response()->json($Usuarios);
        
        /*  --------------------------------------------------------------------------------- */

    }
         public function rolesDatatable(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Usuarios

        $rol = User::roles_datatable($request);
        return response()->json($rol);
        
        /*  --------------------------------------------------------------------------------- */

    }
        public function permisosDatatable(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Usuarios

        $rol = User::permisos_datatable($request);
        return response()->json($rol);
        
        /*  --------------------------------------------------------------------------------- */

    }
}
