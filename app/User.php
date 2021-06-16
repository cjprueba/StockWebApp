<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;
use App\PermisoTienePermisos;

class User extends Authenticatable
{
    use Notifiable, HasRoles, HasApiTokens;
    protected $appends = ['all_permissions','can'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','id_sucursal',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    
  
   
    
   /**
     * Get all user permissions.
     *
     * @return bool
     */
    public function getAllPermissionsAttribute()
    {
        return $this->getAllPermissions();
    }
    
     /**
     * Get all user permissions in a flat array.
     *
     * @return array
     */
    public function getCanAttribute()
    {   

        /* -------------------------------------------------------------------------- */

        // CARGAR PERMISOS DEL USUARIO 

        $permissions = [];
        foreach (Permission::all() as $permission) {
            if (Auth::user()->can($permission->name)) {
                $permissions[] = $permission->name;
            }
        }

        /* -------------------------------------------------------------------------- */

        // RETORNAR PERMISOS 

        return $permissions;

        /* -------------------------------------------------------------------------- */

    }
   
        public static function obtener_roles()
    {
        /* -------------------------------------------------------------------------- */

        // DEFINIR ARRAY PERMISOS 

        $roles = [];

        /* -------------------------------------------------------------------------- */

        // RECORRER ROLES 


        foreach (Role::all() as $key => $rol) {
            $roles[]= array(
                'id' =>$rol->name , 
                'name' =>$rol->name , 
                'description'=>$rol->description
             );
        }
        
        /* -------------------------------------------------------------------------- */

        // DEVOLVER PERMISOS 
        
        return $roles;

        /* -------------------------------------------------------------------------- */
    }
            public static function  obtener_usuario_roles($datos)
    {
        /* -------------------------------------------------------------------------- */

        // DEFINIR ARRAY PERMISOS 
         $user = User::find($datos['id']);
         $roles=$user->getRoleNames();
         $permisos=$user->getAllPermissions();

        $permissions = [];
        foreach ($permisos as $permission) {
            $permissions[] = $permission->id;
        }
        
        /* -------------------------------------------------------------------------- */

         
        
        /* -------------------------------------------------------------------------- */

        // DEVOLVER PERMISOS 
        
        return ["roles"=>$roles,"permisos"=>$permissions];

        /* -------------------------------------------------------------------------- */
    }
    public static function  obtener_permisos_roles($datos)
    {
        /* -------------------------------------------------------------------------- */
          
        // DEFINIR ARRAY PERMISOS 
         $rol =DB::table('roles')
            ->SELECT(['id'])
            ->Where([['name','=',$datos['id']]])
            ->get()
            ->toArray();
        
        if(count($rol)<=0){
            return ["response"=>false];
        }
        
        $roles=Role::find($rol[0]->id );
        $permisos=$roles->permissions()->get();
         
        $permissions = [];
        foreach ($permisos as $permission) { 
            $permissions[] = $permission->id;
        }
        
        // DEVOLVER PERMISOS 
        
             return ["response"=>true,"roles"=>$roles,"permisos"=>$permissions];
         
       

        /* -------------------------------------------------------------------------- */
    }
            public static function obtener_permiso($datos)
    {
        /* -------------------------------------------------------------------------- */

        /* -------------------------------------------------------------------------- */
          
        // DEFINIR ARRAY PERMISOS 
       $permiso =DB::table('permissions')->SELECT(['id'])->Where([['name','=',$datos['id']]])->get()->toArray();
        if(count($permiso)<=0){
           return ["response"=>false];
        }
        $permisos=Permission::find($permiso[0]->id );
       
        
        /* -------------------------------------------------------------------------- */

        
        /* -------------------------------------------------------------------------- */

        // DEVOLVER PERMISOS 
        
             return ["response"=>true,"permisos"=>$permisos];

        /* -------------------------------------------------------------------------- */
    }
                public static function obtener_permisos()
    {
        /* -------------------------------------------------------------------------- */

        /* -------------------------------------------------------------------------- */
          
        // DEFINIR ARRAY PERMISOS 
       // $permisos =Permission::All();





        //-------------------------------------------------------------------
       // ->orderByRaw('SUBSTRING_INDEX(permissions.description,"ar","-1")')
        
        /* -------------------------------------------------------------------------- */

        $permisoPadre=DB::connection('retail')
            ->table('permisos_tiene_permisos')
            ->select(DB::raw('permisos_tiene_permisos.ID_PP AS IDP, permissions.description AS DESCRIPCION, permisos_tiene_permisos.ID'))
            ->leftjoin('permissions', 'permissions.id', '=', 'permisos_tiene_permisos.ID_PP')
            ->groupBy('permisos_tiene_permisos.ID_PP')
            ->orderByRaw('SUBSTRING_INDEX(permissions.description,"ar","-1")')
            ->get();

           

        $permisoPadre_Hijo=DB::connection('retail')
            ->table('permisos_tiene_permisos')
            ->select(DB::raw('permisos_tiene_permisos.ID_PP AS IDP, permisos_tiene_permisos.ID_PH AS IDH, permissions.description AS DESCRIPCION'))
            ->leftjoin('permissions', 'permissions.id', '=', 'permisos_tiene_permisos.ID_PH')
            ->groupBy('permisos_tiene_permisos.ID_PH') 
            ->get()
            ->toArray();

        $permisoPadre_Hijo_Nieto=DB::connection('retail')
            ->table('permisos_tiene_permisos')
            ->select(DB::raw('permisos_tiene_permisos.ID_PP AS IDP, permisos_tiene_permisos.ID_PH AS IDH , permisos_tiene_permisos.ID_PN AS IDN, permissions.description AS DESCRIPCION'))
            ->leftjoin('permissions', 'permissions.id', '=', 'permisos_tiene_permisos.ID_PN')
            ->where('permisos_tiene_permisos.ID_PN', '<>', 0) 
            ->get()
            ->toArray();
             

        
        /* -------------------------------------------------------------------------- */

        // DEVOLVER PERMISOS 
        
             return ["permisos"=>$permisoPadre, "permisosHijo"=>$permisoPadre_Hijo,  "permisosNieto"=>$permisoPadre_Hijo_Nieto];

        /* -------------------------------------------------------------------------- */
    }

        public static function guardar_rol($datos)
    {

        /* -------------------------------------------------------------------------- */
         $rol="";
        // GUARDAR ROL 
        if($datos['existe']===false){
         $rol = Role::create(['name' => $datos['nombre'], 'description' => $datos["descripcion"]]);
        }else{
        $rol=Role::find($datos['id']);
        }
       

        /* -------------------------------------------------------------------------- */

        // ASIGNAR PERMISO A ROLES 

        User::asignar_permisos($rol, $datos["permisos"]);
        
        /* -------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return ["response" => true];

        /* -------------------------------------------------------------------------- */

    }
            public static function asignar_rol($datos)
    {

        /* -------------------------------------------------------------------------- */

        //BUSCAR USUARIO 

        $user = User::find($datos['id']); //Italo Morales
        //$user->removeRole(Role::all());        
        /* -------------------------------------------------------------------------- */

        // ASIGNAR ROL A USUARIO 

        // $user->assignRole($datos['roles']);
        $user->syncRoles($datos['roles']);
        /* -------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return ["response" => true];

        /* -------------------------------------------------------------------------- */

    }
      public static function asignar_permiso($datos)
    { 

        /* -------------------------------------------------------------------------- */

        //BUSCAR USUARIO 

        $user = User::find($datos['id']); //Italo Morales
        //$user->removeRole(Role::all());        
        /* -------------------------------------------------------------------------- */

        // ASIGNAR ROL A USUARIO 

        // $user->assignRole($datos['roles']);
        $user->syncPermissions($datos['permisos']);
        /* -------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return ["response" => true];

        /* -------------------------------------------------------------------------- */

    }
            public static function guardar_permiso($datos)
    {

        /* -------------------------------------------------------------------------- */

        // GUARDAR PERMISO 
        if($datos['existe']===true){
        $permiso = Permission::find($datos['id']);

        $permiso->description = $datos['descripcion'];

        $permiso->save();
        }else{
        $permiso = Permission::create(['name' => $datos['nombre'], 'description' => $datos["descripcion"]]);
        }

      

        /* -------------------------------------------------------------------------- */

        return ["response" => true];

        /* -------------------------------------------------------------------------- */

    }
                public static function guardar_usuario($datos)
    {
      /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */
        // verificar si existe 
         $usuario =DB::table('users')->SELECT(['id'])->Where([['email','=',$datos['email']]])->get()->toArray();
       if(count($usuario)>0){
         return ["response"=>false,"status"=>"ESTE CORREO YA POSEE UN USUARIO!"];
         }
        // GUARDAR USUARIO
      
        $userc= User::create([
            'name' => $datos['nombre'],
            'email' => $datos['email'],
            'password' => Hash::make($datos['contraseña']),
            'id_sucursal' => $user->id_sucursal,
        ]);

      

        /* -------------------------------------------------------------------------- */

        return ["response" => true];

        /* -------------------------------------------------------------------------- */

    }

        public static function asignar_permisos($rol, $permisos)
    {

        /* -------------------------------------------------------------------------- */

        // ASIGNAR PERMISO A ROLES 

        $rol->syncPermissions($permisos);
        
        /* -------------------------------------------------------------------------- */

    }
 public static function mostrar_datatable($request)
    {

        /*  --------------------------------------------------------------------------------- */

        // INICIARA VARIABLES

        //global $search;

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'id', 
                            1 => 'name',
                            2 => 'email',
                         
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = User::where('ID_SUCURSAL','=', $user->id_sucursal)
                     ->count();  
        
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE VALOR EN VARIABLE SEARCH

        if(empty($request->input('search.value')))
        {            

            /*  ************************************************************ */

            //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

            $posts = User::select(DB::raw('id,name,email'))
                         ->where('ID_SUCURSAL','=', $user->id_sucursal)
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();

            /*  ************************************************************ */

        } else {

            /*  ************************************************************ */

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            /*  ************************************************************ */

            // CARGAR LOS PRODUCTOS FILTRADOS EN DATATABLE

            $posts =  User::select(DB::raw('id,name,email'))
                         ->where('ID_SUCURSAL','=', $user->id_sucursal)
                            ->where(function ($query) use ($search) {
                                $query->where('email','LIKE',"%{$search}%")
                                      ->orWhere('name', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = User::where('ID_SUCURSAL','=', $user->id_sucursal)
                            ->where(function ($query) use ($search) {
                                $query->where('email','LIKE',"%{$search}%")
                                      ->orWhere('name', 'LIKE',"%{$search}%");
                            })
                             ->count();

            /*  ************************************************************ */  

        }

        $data = array();

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts))
        {
            foreach ($posts as $post)
            {

                /*  --------------------------------------------------------------------------------- */

                // CARGAR EN LA VARIABLE 

                $nestedData['id'] = $post->id;
                $nestedData['name'] = $post->name;
                $nestedData['email'] = $post->email;

                $data[] = $nestedData;

                /*  --------------------------------------------------------------------------------- */

            }
        }
        
        /*  --------------------------------------------------------------------------------- */

        // PREPARAR EL ARRAY A ENVIAR 

        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );
        
        /*  --------------------------------------------------------------------------------- */

        // CONVERTIR EN JSON EL ARRAY Y ENVIAR 

       return $json_data; 

        /*  --------------------------------------------------------------------------------- */
    }
     public static function roles_datatable($request)
    {

        /*  --------------------------------------------------------------------------------- */

        // INICIARA VARIABLES

        //global $search;

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 



        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'id', 
                            1 => 'name',
                            2 => 'description',
                         
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = Role::count();  
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE VALOR EN VARIABLE SEARCH

        if(empty($request->input('search.value')))
        {            

            /*  ************************************************************ */

            //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

            $posts = Role::select(DB::raw('id,name,description'))
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();

            /*  ************************************************************ */

        } else {

            /*  ************************************************************ */

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            /*  ************************************************************ */

            // CARGAR LOS PRODUCTOS FILTRADOS EN DATATABLE

            $posts =  Role::select(DB::raw('id,name,description'))
                            ->where(function ($query) use ($search) {
                                $query->where('id','LIKE',"%{$search}%")
                                      ->orWhere('name', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = Role::where(function ($query) use ($search) {
                                $query->where('id','LIKE',"%{$search}%")
                                      ->orWhere('name', 'LIKE',"%{$search}%");
                            })
                             ->count();

            /*  ************************************************************ */  

        }

        $data = array();

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts))
        {
            foreach ($posts as $post)
            {

                /*  --------------------------------------------------------------------------------- */

                // CARGAR EN LA VARIABLE 

                $nestedData['id'] = $post->id;
                $nestedData['name'] = $post->name;
                $nestedData['description'] = $post->description;

                $data[] = $nestedData;

                /*  --------------------------------------------------------------------------------- */

            }
        }
        
        /*  --------------------------------------------------------------------------------- */

        // PREPARAR EL ARRAY A ENVIAR 

        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );
        
        /*  --------------------------------------------------------------------------------- */

        // CONVERTIR EN JSON EL ARRAY Y ENVIAR 

       return $json_data; 

    }
     public static function permisos_datatable($request)
    {

        /*  --------------------------------------------------------------------------------- */

        // INICIARA VARIABLES

        //global $search;

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 



        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'id', 
                            1 => 'name',
                            2 => 'description',
                         
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = Permission::count();  
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE VALOR EN VARIABLE SEARCH

        if(empty($request->input('search.value')))
        {            

            /*  ************************************************************ */

            //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

            $posts = Permission::select(DB::raw('id,name,description'))
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir)
                         ->get();

            /*  ************************************************************ */

        } else {

            /*  ************************************************************ */

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            /*  ************************************************************ */

            // CARGAR LOS PRODUCTOS FILTRADOS EN DATATABLE

            $posts =Permission::select(DB::raw('id,name,description'))
                            ->where(function ($query) use ($search) {
                                $query->where('id','LIKE',"%{$search}%")
                                      ->orWhere('name', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = Permission::where(function ($query) use ($search) {
                                $query->where('id','LIKE',"%{$search}%")
                                      ->orWhere('name', 'LIKE',"%{$search}%");
                            })
                             ->count();

            /*  ************************************************************ */  

        }

        $data = array();

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts))
        {
            foreach ($posts as $post)
            {

                /*  --------------------------------------------------------------------------------- */

                // CARGAR EN LA VARIABLE 

                $nestedData['id'] = $post->id;
                $nestedData['name'] = $post->name;
                $nestedData['description'] = $post->description;

                $data[] = $nestedData;

                /*  --------------------------------------------------------------------------------- */

            }
        }
        
        /*  --------------------------------------------------------------------------------- */

        // PREPARAR EL ARRAY A ENVIAR 

        $json_data = array(
                    "draw"            => intval($request->input('draw')),  
                    "recordsTotal"    => intval($totalData),  
                    "recordsFiltered" => intval($totalFiltered), 
                    "data"            => $data   
                    );
        
        /*  --------------------------------------------------------------------------------- */

        // CONVERTIR EN JSON EL ARRAY Y ENVIAR 

       return $json_data; 

    }



}
