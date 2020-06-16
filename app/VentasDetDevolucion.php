<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VentasDetDevolucion extends Model
{
    protected $connection = 'retail';
    protected $table = 'ventasdet_devoluciones';
}
