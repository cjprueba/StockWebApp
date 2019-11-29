<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class TransferenciaDet_tiene_Lotes extends Model
{
    /*  --------------------------------------------------------------------------------- */

    // INICIAR LAS VARIABLES GLOBALES

    protected $connection = 'retail';
    protected $table = 'transferenciadet_tiene_lotes';
    
    /*  --------------------------------------------------------------------------------- */


    public static function guardar_referencia($id_tr, $id_lote, $cantidad)
    {

    	/*  --------------------------------------------------------------------------------- */

        // INSERTAR REFERENCIAS

        $tdtl = TransferenciaDet_tiene_lotes::insert(
            [
                'ID_TRANSFERENCIA' => $id_tr,
                'ID_LOTE' => $id_lote,
                'CANTIDAD' => $cantidad
            ]
        );

        /*  --------------------------------------------------------------------------------- */

    }


    public static function eliminar_referencia($id_tr)
    {

        /*  --------------------------------------------------------------------------------- */

        $tdtl = TransferenciaDet_tiene_lotes::select(DB::raw('ID_LOTE, CANTIDAD'))
        ->where('ID_TRANSFERENCIA','=', $id_tr)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // ELIMINAR REFERENCIAS

        TransferenciaDet_tiene_lotes::where('ID_TRANSFERENCIA','=', $id_tr)
        ->delete();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 
        
        return $tdtl;

        /*  --------------------------------------------------------------------------------- */

    }
}
