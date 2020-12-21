<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Common;
use App\Parametro;

class NewCotizacion extends Model
{

    protected $connection = 'retail';
    protected $table = 'cotizaciones';
    public $timestamps = false;
    public static function obtener_cotizacion()
    {

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $hoy = date("Y-m-d");
        $dia = date("d");
        $mes = date("m");
        $ano = date("Y");

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */
          $cotizaciones=NewCotizacion::
            ->select(DB::raw('ID,FK_DE AS DE,FK_A AS A,VALOR,FECHA,FECALTAS'))
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get()
            ->toArray();

      
        return $cotizaciones;

        /*  --------------------------------------------------------------------------------- */    
         
    }
      public static function guardar_cotizacion($datos){
      	try {
      		DB::connection('retail')->beginTransaction();

        // INICIAR VARIABLES 

			$dia = date("Y-m-d");
			$hora = date("H:i:s");
			$user = auth()->user();

			 // OBTENER ID DE FORMULA
			 $FORMULA = DB::connection('retail')
            ->table('FORMULAS_COTIZACION')
            ->select(DB::raw('ifnull(ID,0)')
            ->where('FK_DE', '=',$datos['data']['DE'])
            ->where('FK_A', '=', $datos['data']['A'])
            ->get()->toArray();

              $cotizacion=NewCotizacion::insert([
                'FK_DE'=> $datos['data']['DE'], 
                'FK_A'=> $datos['data']['A'],
                'VALOR'=> Common::quitar_coma($datos['data']['VALOR'], 2),
                'FK_FORMULA'=> $FORMULA[0]["ID"],
                'FK_USER'=> $user->id,
                'ID_SUCURSAL' => $user->id_sucursal,
                'FECALTAS'=> $dia,
                'HORALTAS'=> $hora]);

            DB::connection('retail')->commit();

            return ["response"=>true,"cotizacion"=>$cotizacion];
      	} catch (Exception $e) {
      	   DB::connection('retail')->rollBack();

           throw $e;

           return ["response"=>false,"statusText"=>"Ha ocurrido un error!. Reporten el error a los encargados del sistema!"];
      	}
      	/*  --------------------------------------------------------------------------------- */



      }
}
