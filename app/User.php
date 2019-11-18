<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles;
    protected $appends = ['all_permissions','can'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
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

        $permissions = [];

        /* -------------------------------------------------------------------------- */

        // RECORRER PERMISOS 

        foreach (Permission::all() as $key => $permission) {
            $permissions[]= array(
                'id' =>$permission->name , 
                'name' =>$permission->description , 
             );
        }
        
        /* -------------------------------------------------------------------------- */

        // DEVOLVER PERMISOS 
        
        return $permissions;

        /* -------------------------------------------------------------------------- */
    }

        public static function guardar_rol($datos)
    {

        /* -------------------------------------------------------------------------- */

        // GUARDAR ROL 

        $rol = Role::create(['name' => $datos['nombre'], 'description' => $datos["descripcion"]]);

        /* -------------------------------------------------------------------------- */

        // ASIGNAR PERMISO A ROLES 

        User::asignar_permisos($rol, $datos["permisos"]);
        
        /* -------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return ["response" => true];

        /* -------------------------------------------------------------------------- */

    }


        public static function asignar_permisos($rol, $permisos)
    {

        /* -------------------------------------------------------------------------- */

        // ASIGNAR PERMISO A ROLES 
        
        $rol->givePermissionTo([
            $permisos
        ]);
        
        /* -------------------------------------------------------------------------- */

    }




}
