<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class EspecificacionAux extends Model
{
    //
        protected $connection = 'retail';
    protected $table = 'especificaciones_aux';
}
