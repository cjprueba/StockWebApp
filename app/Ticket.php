<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Ticket extends Model
{

    /*  --------------------------------------------------------------------------------- */

    // INICIAR LAS VARIABLES GLOBALES

    protected $connection = 'retail';
    protected $table = 'tickets';
    public $timestamps = false;
    
    /*  --------------------------------------------------------------------------------- */

    public static function numero_caja($caja, $dia, $hora){

    	/*  --------------------------------------------------------------------------------- */

    	// USER

    	$user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */

    	$ticket = Ticket::select(DB::raw(''.$caja.' AS CAJA, NUMERO'))
        ->where('ID_SUCURSAL','=', $user->id_sucursal)
        ->where('FECALTAS','=', $dia)
        ->get();

    	/*  --------------------------------------------------------------------------------- */

    	if (count($ticket) === 0) {

            /*  --------------------------------------------------------------------------------- */

            // INSERTAR TICKET

            Ticket::insert(
                [
                    'NUMERO' => 1,
                    'USER' => $user->name,
                    'FECALTAS' => $dia,
                    'HORALTAS' => $hora,
                    'ID_SUCURSAL' => $user->id_sucursal
                ]
            );

            /*  --------------------------------------------------------------------------------- */

            // OBTENER TICKETS 

            $ticket = Ticket::select(DB::raw(''.$caja.' AS CAJA'))
            ->where('ID_SUCURSAL','=', $user->id_sucursal)
            ->where('FECALTAS','=', $dia)
            ->get();

            /*  --------------------------------------------------------------------------------- */

        }
        
        /*  --------------------------------------------------------------------------------- */

        // SUMAR MAS UNO

        $ticket[0]["CAJA"] = $ticket[0]["CAJA"] + 1;

        /*  --------------------------------------------------------------------------------- */

        // NUMERO MAS UNO CAJA 

        $ticket[0]["NUMERO"] = $ticket[0]["NUMERO"] + 1;

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR CAJA

        return $ticket;

        /*  --------------------------------------------------------------------------------- */

    }

    public static function actualizar_numero($codigo, $codigo_caja, $caja, $dia, $hora) {

        /*  --------------------------------------------------------------------------------- */

        // USER

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        Ticket::where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->where('FECALTAS','=', $dia)
        ->update([
                    'NUMERO' => $codigo,
                    $caja => $codigo_caja,
                    'USERM' => $user->name,
                    'FECMODIF' => $dia,
                    'HORMODIF' => $hora
                ]);

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return ["response" => true];

        /*  --------------------------------------------------------------------------------- */

    }
}
