<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MarcaAux extends Model
{
    //
    protected $connection = 'retail';
    protected $table = 'marca_aux';
    protected $primaryKey='Codigo';
    const CREATED_AT='FECALTAS';
    const UPDATED_AT='FECMODIF';
    //public $timestamps=false;
    protected $fillable = [
        'CODIGO_MARCA',
        'DESCUENTO',
        'FECHAINI',
        'FECHAFIN',
        'FECMODIF',
        'FECALTAS'
   ];

   public static function obtener_descuento($marca, $id_sucursal)
    {

        /*  --------------------------------------------------------------------------------- */

        $fecha = date('Y-m-d');

        /*  --------------------------------------------------------------------------------- */

        // OBTENER DESCUENTO

        $descuento = MarcaAux::
        select(DB::raw('DESCUENTO, FECHAINI, FECHAFIN'))
        ->where('CODIGO_MARCA', '=', $marca)
        ->where('ID_SUCURSAL', '=', $id_sucursal)
        ->whereDate('FECHAINI', '<=', $fecha)
        ->whereDate('FECHAFIN', '>=', $fecha)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        if (count($descuento) > 0) {
            $descuento = $descuento[0]->DESCUENTO;
        } else {
            $descuento = 0;
        }

        /*  --------------------------------------------------------------------------------- */

        return $descuento;

        /*  --------------------------------------------------------------------------------- */

    }
}
