<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use App\Common;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    //
    protected $connection = 'retail';
	protected $table = 'roles';


    public static function obtener_roles($data)
    {
     Permission:all()

    }

}
