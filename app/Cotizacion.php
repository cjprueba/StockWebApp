<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Common;
use App\Parametro;

class Cotizacion extends Model
{

    protected $connection = 'retail';
    protected $table = 'TABRELMON';
    public $timestamps = false;
      public static function CALMONED($data)
    {

        /*  --------------------------------------------------------------------------------- */

        // DEVOLVER VALOR SI ES QUE LAS MONEDAS SON IGUALES

        if ($data['monedaProducto'] === $data['monedaSistema']) {
            return ["response" => true, "valor" => Common::formato_precio($data['precio'], $data['decSistema'])];
        }

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $hoy = date("Y-m-d");
        $dia = date("d");
        $mes = date("m");
        $ano = date("Y");
        $diaLetra = '';
        $valor = 0;

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        

        

        /*  --------------------------------------------------------------------------------- */

        // TRAER LA COTIZACION DEL DIA



            $cotizaciones = NewCotizacion::
             select(DB::raw('COTIZACIONES.VALOR AS CAMBIO, FORMULAS_COTIZACION.FORMULA AS FORMULA'))
            ->leftjoin('FORMULAS_COTIZACION','FORMULAS_COTIZACION.ID','=','COTIZACIONES.FK_FORMULA')
            ->where('COTIZACIONES.FK_DE', '=', $data['monedaProducto'])
            ->where('COTIZACIONES.FK_A', '=', $data['monedaSistema'])
            ->where('ID_SUCURSAL', '=', $data['id_sucursal'])
            ->orderBy('COTIZACIONES.ID','DESC')
            ->limit(1);
            $cotizacion_final=$cotizaciones->where('FECHA','=',$hoy)->get();
            if(count($cotizacion_final)<=0){
                $cotizacion_final=$cotizaciones->get();
            }
            

        
        /*  --------------------------------------------------------------------------------- */

        // RETORNAR SI NO ENCUENTRA COTIZACIÓN 
        
        if (count($cotizacion_final) <= 0) {
            return ["response" => false, "statusText" => "No se ha encontrado ninguna cotización"];
        }

        /*  --------------------------------------------------------------------------------- */

        // REALIZAR CALCULO 

        if ($cotizacion_final[0]->FORMULA === '*') {
            $valor = Common::formato_precio(((float)$data['precio'] * (float)$cotizacion_final[0]->CAMBIO), $data['decSistema']);
        } else if ($cotizacion_final[0]->FORMULA === '/') {
            $valor = Common::formato_precio(((float)$data['precio'] / (float)$cotizacion_final[0]->CAMBIO), $data['decSistema']);
        } else if ($cotizacion_final[0]->FORMULA === '+') {
            $valor = Common::formato_precio(((float)$data['precio'] + (float)$cotizacion_final[0]->CAMBIO), $data['decSistema']);
        } else if ($cotizacion_final[0]->FORMULA === '-') {
            $valor = Common::formato_precio(((float)$data['precio'] - (float)$cotizacion_final[0]->CAMBIO), $data['decSistema']);
        }   
        
        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR
        
        return ["response" => true, "valor" => $valor];

        /*  --------------------------------------------------------------------------------- */    
         
    }
    public static function cotizacion_dia($monedaSistema, $monedaEnviar)
    {

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $hoy = date("Y-m-d");
       
        $valor = 0;

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI MONEDAS SON IGUALES 

        if ($monedaSistema === $monedaEnviar) {
            return $cotizacion = 1;
        }

        /*  --------------------------------------------------------------------------------- */

        



        /*  --------------------------------------------------------------------------------- */

        // COMPROBAR SI LA TABLA ES UNICA 

      

            $cotizaciones = NewCotizacion::
             select(DB::raw('COTIZACIONES.VALOR AS CAMBIO'))
            ->where('COTIZACIONES.FK_DE', '=', $monedaEnviar)
            ->where('COTIZACIONES.FK_A', '=', $monedaSistema)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->orderBy('COTIZACIONES.ID','DESC')
            ->limit(1);
            $cotizacion_final=$cotizaciones->where('FECHA','=',$hoy)->get();
            if(count($cotizacion_final)<=0){
                $cotizacion_final=$cotizaciones->get();
            }

       

        /*  --------------------------------------------------------------------------------- */
        
        // RETORNAR VALOR

        return (float)$cotizacion_final[0]->CAMBIO;

        /*  --------------------------------------------------------------------------------- */    
         
    }
    public static function cotizacion_dia_monedas()
    {

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $hoy = date("Y-m-d");
        $valor = 0;

        $valor_guaranies = 0;
        $valor_dolares = 0;
        $valor_pesos = 0;
        $valor_reales = 0;


        $activar_guaranies = false;
        $activar_dolares = false;
        $activar_pesos = false;
        $activar_reales = false;

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER PARAMETROS 

        $parametros = Parametro::mostrarParametro();
        $monedaSistema = $parametros['parametros'][0]->MONEDA;

        /*  --------------------------------------------------------------------------------- */
      

        /*  --------------------------------------------------------------------------------- */



            $guaranies_new = NewCotizacion::
             select(DB::raw('COTIZACIONES.VALOR AS CAMBIO'))
            ->where('COTIZACIONES.FK_DE', '=', 1)
            ->where('COTIZACIONES.FK_A', '=', $monedaSistema)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->orderBy('COTIZACIONES.ID','DESC')
            ->limit(1);
            $guaranies=$guaranies_new->where('FECHA','=',$hoy)->get();
            if(count($guaranies)<=0){
                $guaranies=$guaranies_new->get();
            }

            $dolares_new = NewCotizacion::
             select(DB::raw('COTIZACIONES.VALOR AS CAMBIO'))
            ->where('COTIZACIONES.FK_DE', '=', 2)
            ->where('COTIZACIONES.FK_A', '=', $monedaSistema)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->orderBy('COTIZACIONES.ID','DESC')
            ->limit(1);
            $dolares=$dolares_new->where('FECHA','=',$hoy)->get();
            if(count($dolares)<=0){
                $dolares=$dolares_new->get();
            }

           $pesos_new = NewCotizacion::
             select(DB::raw('COTIZACIONES.VALOR AS CAMBIO'))
            ->where('COTIZACIONES.FK_DE', '=', 3)
            ->where('COTIZACIONES.FK_A', '=', $monedaSistema)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->orderBy('COTIZACIONES.ID','DESC')
            ->limit(1);
            $pesos=$pesos_new->where('FECHA','=',$hoy)->get();
            if(count($pesos)<=0){
                $pesos=$pesos_new->get();
            }

           $reales_new = NewCotizacion::
             select(DB::raw('COTIZACIONES.VALOR AS CAMBIO'))
            ->where('COTIZACIONES.FK_DE', '=', 4)
            ->where('COTIZACIONES.FK_A', '=', $monedaSistema)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->orderBy('COTIZACIONES.ID','DESC')
            ->limit(1);
            $reales=$reales_new->where('FECHA','=',$hoy)->get();
            if(count($reales)<=0){
                $reales=$reales_new->get();
            }

            /*  --------------------------------------------------------------------------------- */


      
        /*  --------------------------------------------------------------------------------- */
        
        // REVISAR SI POSEEN COTIZACIONES 

            if (count($guaranies) <= 0) {
                $valor_guaranies = '';

                if ($monedaSistema !== 1) {
                    $activar_guaranies = true;
                }

            } else {
                $valor_guaranies = (float)$guaranies[0]->CAMBIO;
            }

            if (count($dolares) <= 0) {
                $valor_dolares = '';

                if ($monedaSistema !== 2) {
                    $activar_dolares = true;
                }

            } else {
                $valor_dolares = (float)$dolares[0]->CAMBIO;
            }

            if (count($pesos) <= 0) {
                $valor_pesos = '';

                if ($monedaSistema !== 3) {
                    $activar_pesos = true;
                }

            } else {
                $valor_pesos = (float)$pesos[0]->CAMBIO;
            }

            if (count($reales) <= 0) {
               $valor_reales = '';

               if ($monedaSistema !== 4) {
                    $activar_reales = true;
                }

            } else {
               $valor_reales = (float)$reales[0]->CAMBIO; 
            }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR
        
        return [
            'Guaranies' => $valor_guaranies, 
            'Dolares' => $valor_dolares,
            'Pesos' => $valor_pesos,
            'Reales' => $valor_reales,
            'activar_guaranies' => $activar_guaranies,
            'activar_dolares' => $activar_dolares,
            'activar_pesos' => $activar_pesos,
            'activar_reales' => $activar_reales 
        ];

        /*  --------------------------------------------------------------------------------- */    
         
    }
    public static function cotizacion_dia_monedas_compra()
    {

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $hoy = date("Y-m-d");
        $valor = 0;

        $valor_guaranies = 0;
        $valor_dolares = 0;
        $valor_pesos = 0;
        $valor_reales = 0;

        $formula_guaranies = '';
        $formula_dolares = '';
        $formula_pesos = '';
        $formula_reales = '';

        $formula_guaranies_reves = '';
        $formula_dolares_reves = '';
        $formula_pesos_reves = '';
        $formula_reales_reves = '';

        $activar_guaranies = false;
        $activar_dolares = false;
        $activar_pesos = false;
        $activar_reales = false;

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER PARAMETROS 

        $parametros = Parametro::mostrarParametro();
        $monedaSistema = $parametros['parametros'][0]->MONEDA;
        $candecSistema = (Parametro::candec($monedaSistema))['CANDEC'];

        /*  --------------------------------------------------------------------------------- */

       

        /*  --------------------------------------------------------------------------------- */

        // COMPROBAR SI LA TABLA ES UNICA 
        
       
            
            /*  --------------------------------------------------------------------------------- */

             $guaranies_new = NewCotizacion::
             select(DB::raw('COTIZACIONES.VALOR AS CAMBIO,FORMULAS_COTIZACION.FORMULA AS FORMULA'))
            ->leftjoin('FORMULAS_COTIZACION','FORMULAS_COTIZACION.ID','=','COTIZACIONES.FK_FORMULA')
            ->where('COTIZACIONES.FK_DE', '=', $monedaSistema)
            ->where('COTIZACIONES.FK_A', '=', 1)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->orderBy('COTIZACIONES.ID','DESC')
            ->limit(1);
            $guaranies=$guaranies_new->where('FECHA','=',$hoy)->get();
            if(count($guaranies)<=0){
                $guaranies=$guaranies_new->get();
            }

            $dolares_new = NewCotizacion::
            select(DB::raw('COTIZACIONES.VALOR AS CAMBIO,FORMULAS_COTIZACION.FORMULA AS FORMULA'))
            ->leftjoin('FORMULAS_COTIZACION','FORMULAS_COTIZACION.ID','=','COTIZACIONES.FK_FORMULA')
            ->where('COTIZACIONES.FK_DE', '=', $monedaSistema)
            ->where('COTIZACIONES.FK_A', '=', 2)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->orderBy('COTIZACIONES.ID','DESC')
            ->limit(1);
            $dolares=$dolares_new->where('FECHA','=',$hoy)->get();
            if(count($dolares)<=0){
                $dolares=$dolares_new->get();
            }

           $pesos_new = NewCotizacion::
            select(DB::raw('COTIZACIONES.VALOR AS CAMBIO,FORMULAS_COTIZACION.FORMULA AS FORMULA'))
            ->leftjoin('FORMULAS_COTIZACION','FORMULAS_COTIZACION.ID','=','COTIZACIONES.FK_FORMULA')
            ->where('COTIZACIONES.FK_DE', '=', $monedaSistema)
            ->where('COTIZACIONES.FK_A', '=', 3)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->orderBy('COTIZACIONES.ID','DESC')
            ->limit(1);
            $pesos=$pesos_new->where('FECHA','=',$hoy)->get();
            if(count($pesos)<=0){
                $pesos=$pesos_new->get();
            }

           $reales_new = NewCotizacion::
              select(DB::raw('COTIZACIONES.VALOR AS CAMBIO,FORMULAS_COTIZACION.FORMULA AS FORMULA'))
            ->leftjoin('FORMULAS_COTIZACION','FORMULAS_COTIZACION.ID','=','COTIZACIONES.FK_FORMULA')
            ->where('COTIZACIONES.FK_DE', '=', $monedaSistema)
            ->where('COTIZACIONES.FK_A', '=', 4)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->orderBy('COTIZACIONES.ID','DESC')
            ->limit(1);
            $reales=$reales_new->where('FECHA','=',$hoy)->get();
            if(count($reales)<=0){
                $reales=$reales_new->get();
            }

            /*  --------------------------------------------------------------------------------- */

            // FORMULA REVES
            $formula_gs = NewCotizacion::
             select(DB::raw('COTIZACIONES.VALOR AS CAMBIO,FORMULAS_COTIZACION.FORMULA AS FORMULA'))
            ->leftjoin('FORMULAS_COTIZACION','FORMULAS_COTIZACION.ID','=','COTIZACIONES.FK_FORMULA')
            ->where('COTIZACIONES.FK_DE', '=', 1)
            ->where('COTIZACIONES.FK_A', '=', $monedaSistema)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->orderBy('COTIZACIONES.ID','DESC')
            ->limit(1);
            $formula_gs_reves=$formula_gs->where('FECHA','=',$hoy)->get();
            if(count($formula_gs_reves)<=0){
                $formula_gs_reves=$formula_gs->get();
            }

            $formula_usd = NewCotizacion::
              select(DB::raw('COTIZACIONES.VALOR AS CAMBIO,FORMULAS_COTIZACION.FORMULA AS FORMULA'))
            ->leftjoin('FORMULAS_COTIZACION','FORMULAS_COTIZACION.ID','=','COTIZACIONES.FK_FORMULA')
            ->where('COTIZACIONES.FK_DE', '=', 2)
            ->where('COTIZACIONES.FK_A', '=', $monedaSistema)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->orderBy('COTIZACIONES.ID','DESC')
            ->limit(1);
            $formula_usd_reves=$formula_usd->where('FECHA','=',$hoy)->get();
            if(count($formula_usd_reves)<=0){
                $formula_usd_reves=$formula_usd->get();
            }

           $formula_ps = NewCotizacion::
             select(DB::raw('COTIZACIONES.VALOR AS CAMBIO,FORMULAS_COTIZACION.FORMULA AS FORMULA'))
            ->leftjoin('FORMULAS_COTIZACION','FORMULAS_COTIZACION.ID','=','COTIZACIONES.FK_FORMULA')
            ->where('COTIZACIONES.FK_DE', '=', 3)
            ->where('COTIZACIONES.FK_A', '=', $monedaSistema)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->orderBy('COTIZACIONES.ID','DESC')
            ->limit(1);
            $formula_ps_reves=$formula_ps->where('FECHA','=',$hoy)->get();
            if(count($formula_ps_reves)<=0){
                $formula_ps_reves=$formula_ps->get();
            }

           $formula_rs = NewCotizacion::
               select(DB::raw('COTIZACIONES.VALOR AS CAMBIO,FORMULAS_COTIZACION.FORMULA AS FORMULA'))
            ->leftjoin('FORMULAS_COTIZACION','FORMULAS_COTIZACION.ID','=','COTIZACIONES.FK_FORMULA')
            ->where('COTIZACIONES.FK_DE', '=', 4)
            ->where('COTIZACIONES.FK_A', '=', $monedaSistema)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->orderBy('COTIZACIONES.ID','DESC')
            ->limit(1);
            $formula_rs_reves=$formula_rs->where('FECHA','=',$hoy)->get();
            if(count($formula_rs_reves)<=0){
                $formula_rs_reves=$formula_rs->get();
            }


        

            /*  --------------------------------------------------------------------------------- */

       

        /*  --------------------------------------------------------------------------------- */
        
        // REVISAR SI POSEEN COTIZACIONES 

            if (count($guaranies) <= 0) {
                $valor_guaranies = '';

                if ($monedaSistema !== 1) {
                    $activar_guaranies = true;
                }

            } else {
                $valor_guaranies =  Common::precio_candec_sin_letra((float)$guaranies[0]->CAMBIO, $monedaSistema);
                $formula_guaranies = $guaranies[0]->FORMULA;
                $formula_guaranies_reves = $formula_gs_reves[0]->FORMULA;
            }

            if (count($dolares) <= 0) {
                $valor_dolares = '';

                if ($monedaSistema !== 2) {
                    $activar_dolares = true;
                }

            } else {
                $valor_dolares = Common::precio_candec_sin_letra((float)$dolares[0]->CAMBIO, $monedaSistema);
                $formula_dolares = $dolares[0]->FORMULA;
                $formula_dolares_reves = $formula_usd_reves[0]->FORMULA;
            }

            if (count($pesos) <= 0) {
                $valor_pesos = '';

                if ($monedaSistema !== 3) {
                    $activar_pesos = true;
                }

            } else {
                $valor_pesos = Common::precio_candec_sin_letra((float)$pesos[0]->CAMBIO, $monedaSistema);
                $formula_pesos = $pesos[0]->FORMULA;
                $formula_pesos_reves = $formula_ps_reves[0]->FORMULA;
            }

            if (count($reales) <= 0) {
               $valor_reales = '';

               if ($monedaSistema !== 4) {
                    $activar_reales = true;
                }

            } else {
               $valor_reales = Common::precio_candec_sin_letra((float)$reales[0]->CAMBIO, $monedaSistema); 
               $formula_reales = $reales[0]->FORMULA;
               $formula_reales_reves = $formula_rs_reves[0]->FORMULA;
            }

        /*  --------------------------------------------------------------------------------- */

        // OBTENER CANDEC

        $monedas = (Moneda::obtener_monedas())["monedas"];

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR
        
        return [
            'guaranies' => $valor_guaranies, 
            'dolares' => $valor_dolares,
            'pesos' => $valor_pesos,
            'reales' => $valor_reales,
            'deshabilitar_gs' => $activar_guaranies,
            'deshabilitar_$' => $activar_dolares,
            'deshabilitar_ps' => $activar_pesos,
            'deshabilitar_rs' => $activar_reales,
            'formula_gs' => $formula_guaranies,
            'formula_$' => $formula_dolares,
            'formula_ps' => $formula_pesos,
            'formula_rs' => $formula_reales,
            'formula_gs_reves' => $formula_guaranies_reves,
            'formula_usd_reves' => $formula_dolares_reves,
            'formula_ps_reves' => $formula_pesos_reves,
            'formula_rs_reves' => $formula_reales_reves,
            'candec_gs' => $monedas[0]->CANDEC,
            'candec_$' => $monedas[1]->CANDEC,
            'candec_ps' => $monedas[2]->CANDEC,
            'candec_rs' => $monedas[3]->CANDEC,
            'moneda_gs' => $monedas[0]->CODIGO,
            'moneda_$' => $monedas[1]->CODIGO,
            'moneda_ps' => $monedas[2]->CODIGO,
            'moneda_rs' => $monedas[3]->CODIGO,
            'descripcion_gs' => $monedas[0]->DESCRIPCION,
            'descripcion_$' => $monedas[1]->DESCRIPCION,
            'descripcion_ps' => $monedas[2]->DESCRIPCION,
            'descripcion_rs' => $monedas[3]->DESCRIPCION,
            'moneda' => $monedaSistema,
            'candec' => $candecSistema
        ];

        /*  --------------------------------------------------------------------------------- */    
         
    }
/*    public static function CALMONED($data)
    {

        

        // DEVOLVER VALOR SI ES QUE LAS MONEDAS SON IGUALES

        if ($data['monedaProducto'] === $data['monedaSistema']) {
            return ["response" => true, "valor" => Common::formato_precio($data['precio'], $data['decSistema'])];
        }

        

        // INICIAR VARIABLES 

        $hoy = date("Y-m-d");
        $dia = date("d");
        $mes = date("m");
        $ano = date("Y");
        $diaLetra = '';
        $valor = 0;

        

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        

        // OBTENER COLUMNA DIA 

        $diaLetra = 'CA'.$dia;

        

        // COMPROBAR SI LA TABLA ES UNICA 

        if($data['tab_unica'] === true) {

            $cotizaciones = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO, FORMULA'))
            ->where('CODMON', '=', $data['monedaProducto'])
            ->where('CODMON1', '=', $data['monedaSistema'])
            ->where('MES', '=', '0')
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $data['id_sucursal'])
            ->get();
            
        } else {

            $cotizaciones = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO, FORMULA'))
            ->where('CODMON', '=', $data['monedaProducto'])
            ->where('CODMON1', '=', $data['monedaSistema'])
            ->where('MES', '=', $mes)
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $data['id_sucursal'])
            ->get();

        }
        
        

        // RETORNAR SI NO ENCUENTRA COTIZACIÓN 
        
        if (count($cotizaciones) <= 0) {
            return ["response" => false, "statusText" => "No se ha encontrado ninguna cotización"];
        }

        

        // REALIZAR CALCULO 

        if ($cotizaciones[0]->FORMULA === '*') {
            $valor = Common::formato_precio(((float)$data['precio'] * (float)$cotizaciones[0]->CAMBIO), $data['decSistema']);
        } else if ($cotizaciones[0]->FORMULA === '/') {
            $valor = Common::formato_precio(((float)$data['precio'] / (float)$cotizaciones[0]->CAMBIO), $data['decSistema']);
        } else if ($cotizaciones[0]->FORMULA === '+') {
            $valor = Common::formato_precio(((float)$data['precio'] + (float)$cotizaciones[0]->CAMBIO), $data['decSistema']);
        } else if ($cotizaciones[0]->FORMULA === '-') {
            $valor = Common::formato_precio(((float)$data['precio'] - (float)$cotizaciones[0]->CAMBIO), $data['decSistema']);
        }   
        
        

        // RETORNAR VALOR
        
        return ["response" => true, "valor" => $valor];

            
         
    }


    public static function cotizacion_dia($monedaSistema, $monedaEnviar)
    {

        

        // INICIAR VARIABLES 

        $hoy = date("Y-m-d");
        $dia = date("d");
        $mes = date("m");
        $ano = date("Y");
        $diaLetra = '';
        $valor = 0;

        

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        

        // REVISAR SI MONEDAS SON IGUALES 

        if ($monedaSistema === $monedaEnviar) {
            return $cotizacion = 1;
        }

        

        // OBTENER COLUMNA DIA 

        $diaLetra = 'CA'.$dia;

        

        // REVISAR SI ES TAB UNICA 

        $tab_unica = Parametro::tab_unica();

        

        // COMPROBAR SI LA TABLA ES UNICA 

        if($tab_unica === "SI") {

            $cotizaciones = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO'))
            ->where('CODMON', '=', $monedaEnviar)
            ->where('CODMON1', '=', $monedaSistema)
            ->where('MES', '=', '0')
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

        } else {

            $cotizaciones = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO'))
            ->where('CODMON', '=', $monedaEnviar)
            ->where('CODMON1', '=', $monedaSistema)
            ->where('MES', '=', $mes)
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

        }

        
        
        // RETORNAR VALOR

        return (float)$cotizaciones[0]->CAMBIO;

            
         
    }

    public static function cotizacion_dia_monedas()
    {

        

        // INICIAR VARIABLES 

        $hoy = date("Y-m-d");
        $dia = date("d");
        $mes = date("m");
        $ano = date("Y");
        $diaLetra = '';
        $valor = 0;

        $valor_guaranies = 0;
        $valor_dolares = 0;
        $valor_pesos = 0;
        $valor_reales = 0;


        $activar_guaranies = false;
        $activar_dolares = false;
        $activar_pesos = false;
        $activar_reales = false;

        

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        

        // OBTENER PARAMETROS 

        $parametros = Parametro::mostrarParametro();
        $tab_unica = $parametros['parametros'][0]->TAB_UNICA;
        $monedaSistema = $parametros['parametros'][0]->MONEDA;

        

        // OBTENER COLUMNA DIA 

        $diaLetra = 'CA'.$dia;

        

        // COMPROBAR SI LA TABLA ES UNICA 

        if($tab_unica === "SI") {

            $guaranies = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO'))
            ->where('CODMON', '=', 1)
            ->where('CODMON1', '=', $monedaSistema)
            ->where('MES', '=', '0')
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            $dolares = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO'))
            ->where('CODMON', '=', 2)
            ->where('CODMON1', '=', $monedaSistema)
            ->where('MES', '=', '0')
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            $pesos = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO'))
            ->where('CODMON', '=', 3)
            ->where('CODMON1', '=', $monedaSistema)
            ->where('MES', '=', '0')
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            $reales = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO'))
            ->where('CODMON', '=', 4)
            ->where('CODMON1', '=', $monedaSistema)
            ->where('MES', '=', '0')
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            


        } else {

            $guaranies = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO'))
            ->where('CODMON', '=', 1)
            ->where('CODMON1', '=', $monedaSistema)
            ->where('MES', '=', $mes)
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            $dolares = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO'))
            ->where('CODMON', '=', 2)
            ->where('CODMON1', '=', $monedaSistema)
            ->where('MES', '=', $mes)
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            $pesos = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO'))
            ->where('CODMON', '=', 3)
            ->where('CODMON1', '=', $monedaSistema)
            ->where('MES', '=', $mes)
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            $reales = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO'))
            ->where('CODMON', '=', 4)
            ->where('CODMON1', '=', $monedaSistema)
            ->where('MES', '=', $mes)
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

        }

        
        
        // REVISAR SI POSEEN COTIZACIONES 

            if (count($guaranies) <= 0) {
                $valor_guaranies = '';

                if ($monedaSistema !== 1) {
                    $activar_guaranies = true;
                }

            } else {
                $valor_guaranies = (float)$guaranies[0]->CAMBIO;
            }

            if (count($dolares) <= 0) {
                $valor_dolares = '';

                if ($monedaSistema !== 2) {
                    $activar_dolares = true;
                }

            } else {
                $valor_dolares = (float)$dolares[0]->CAMBIO;
            }

            if (count($pesos) <= 0) {
                $valor_pesos = '';

                if ($monedaSistema !== 3) {
                    $activar_pesos = true;
                }

            } else {
                $valor_pesos = (float)$pesos[0]->CAMBIO;
            }

            if (count($reales) <= 0) {
               $valor_reales = '';

               if ($monedaSistema !== 4) {
                    $activar_reales = true;
                }

            } else {
               $valor_reales = (float)$reales[0]->CAMBIO; 
            }

        

        // RETORNAR VALOR
        
        return [
            'Guaranies' => $valor_guaranies, 
            'Dolares' => $valor_dolares,
            'Pesos' => $valor_pesos,
            'Reales' => $valor_reales,
            'activar_guaranies' => $activar_guaranies,
            'activar_dolares' => $activar_dolares,
            'activar_pesos' => $activar_pesos,
            'activar_reales' => $activar_reales 
        ];

            
         
    }


    public static function cotizacion_dia_monedas_compra()
    {

        

        // INICIAR VARIABLES 

        $hoy = date("Y-m-d");
        $dia = date("d");
        $mes = date("m");
        $ano = date("Y");
        $diaLetra = '';
        $valor = 0;

        $valor_guaranies = 0;
        $valor_dolares = 0;
        $valor_pesos = 0;
        $valor_reales = 0;

        $formula_guaranies = '';
        $formula_dolares = '';
        $formula_pesos = '';
        $formula_reales = '';

        $formula_guaranies_reves = '';
        $formula_dolares_reves = '';
        $formula_pesos_reves = '';
        $formula_reales_reves = '';

        $activar_guaranies = false;
        $activar_dolares = false;
        $activar_pesos = false;
        $activar_reales = false;

        

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        

        // OBTENER PARAMETROS 

        $parametros = Parametro::mostrarParametro();
        $tab_unica = $parametros['parametros'][0]->TAB_UNICA;
        $monedaSistema = $parametros['parametros'][0]->MONEDA;
        $candecSistema = (Parametro::candec($monedaSistema))['CANDEC'];

        

        // OBTENER COLUMNA DIA 

        $diaLetra = 'CA'.$dia;

        

        // COMPROBAR SI LA TABLA ES UNICA 
        
        if($tab_unica === "SI") {
            
            

            $guaranies = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO, FORMULA'))
            ->where('CODMON', '=', $monedaSistema)
            ->where('CODMON1', '=', 1)
            ->where('MES', '=', '0')
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            $dolares = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO, FORMULA'))
            ->where('CODMON', '=', $monedaSistema)
            ->where('CODMON1', '=', 2)
            ->where('MES', '=', '0')
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            $pesos = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO, FORMULA'))
            ->where('CODMON', '=', $monedaSistema)
            ->where('CODMON1', '=', 3)
            ->where('MES', '=', '0')
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            $reales = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO, FORMULA'))
            ->where('CODMON', '=', $monedaSistema)
            ->where('CODMON1', '=', 4)
            ->where('MES', '=', '0')
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            

            // FORMULA REVES

            $formula_gs_reves = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw('FORMULA'))
            ->where('CODMON', '=', 1)
            ->where('CODMON1', '=', $monedaSistema)
            ->where('MES', '=', '0')
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            $formula_usd_reves = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw('FORMULA'))
            ->where('CODMON', '=', 2)
            ->where('CODMON1', '=', $monedaSistema)
            ->where('MES', '=', '0')
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            $formula_ps_reves = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw('FORMULA'))
            ->where('CODMON', '=', 3)
            ->where('CODMON1', '=', $monedaSistema)
            ->where('MES', '=', '0')
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            $formula_rs_reves = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw('FORMULA'))
            ->where('CODMON', '=', 4)
            ->where('CODMON1', '=', $monedaSistema)
            ->where('MES', '=', '0')
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            

        } else {

            


            $guaranies = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO, FORMULA'))
            ->where('CODMON', '=', $monedaSistema)
            ->where('CODMON1', '=', 1)
            ->where('MES', '=', $mes)
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            $dolares = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO, FORMULA'))
            ->where('CODMON', '=', $monedaSistema)
            ->where('CODMON1', '=', 2)
            ->where('MES', '=', $mes)
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            $pesos = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO, FORMULA'))
            ->where('CODMON', '=', $monedaSistema)
            ->where('CODMON1', '=', 3)
            ->where('MES', '=', $mes)
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            $reales = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO, FORMULA'))
            ->where('CODMON', '=', $monedaSistema)
            ->where('CODMON1', '=', 4)
            ->where('MES', '=', $mes)
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            

            // FORMULA REVES 

            $formula_gs_reves = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO, FORMULA'))
            ->where('CODMON', '=', 1)
            ->where('CODMON1', '=', $monedaSistema)
            ->where('MES', '=', $mes)
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            $formula_usd_reves = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO, FORMULA'))
            ->where('CODMON', '=', 2)
            ->where('CODMON1', '=', $monedaSistema)
            ->where('MES', '=', $mes)
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            $formula_ps_reves = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO, FORMULA'))
            ->where('CODMON', '=', 3)
            ->where('CODMON1', '=', $monedaSistema)
            ->where('MES', '=', $mes)
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            $formula_rs_reves = DB::connection('retail')
            ->table('TABRELMON')
            ->select(DB::raw(''.$diaLetra.' AS CAMBIO, FORMULA'))
            ->where('CODMON', '=', 4)
            ->where('CODMON1', '=', $monedaSistema)
            ->where('MES', '=', $mes)
            ->where('ANO', '=', $ano)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            
        }

        
        
        // REVISAR SI POSEEN COTIZACIONES 

            if (count($guaranies) <= 0) {
                $valor_guaranies = '';

                if ($monedaSistema !== 1) {
                    $activar_guaranies = true;
                }

            } else {
                $valor_guaranies =  Common::precio_candec_sin_letra((float)$guaranies[0]->CAMBIO, $monedaSistema);
                $formula_guaranies = $guaranies[0]->FORMULA;
                $formula_guaranies_reves = $formula_gs_reves[0]->FORMULA;
            }

            if (count($dolares) <= 0) {
                $valor_dolares = '';

                if ($monedaSistema !== 2) {
                    $activar_dolares = true;
                }

            } else {
                $valor_dolares = Common::precio_candec_sin_letra((float)$dolares[0]->CAMBIO, $monedaSistema);
                $formula_dolares = $dolares[0]->FORMULA;
                $formula_dolares_reves = $formula_usd_reves[0]->FORMULA;
            }

            if (count($pesos) <= 0) {
                $valor_pesos = '';

                if ($monedaSistema !== 3) {
                    $activar_pesos = true;
                }

            } else {
                $valor_pesos = Common::precio_candec_sin_letra((float)$pesos[0]->CAMBIO, $monedaSistema);
                $formula_pesos = $pesos[0]->FORMULA;
                $formula_pesos_reves = $formula_ps_reves[0]->FORMULA;
            }

            if (count($reales) <= 0) {
               $valor_reales = '';

               if ($monedaSistema !== 4) {
                    $activar_reales = true;
                }

            } else {
               $valor_reales = Common::precio_candec_sin_letra((float)$reales[0]->CAMBIO, $monedaSistema); 
               $formula_reales = $reales[0]->FORMULA;
               $formula_reales_reves = $formula_rs_reves[0]->FORMULA;
            }

        

        // OBTENER CANDEC

        $monedas = (Moneda::obtener_monedas())["monedas"];

        

        // RETORNAR VALOR
        
        return [
            'guaranies' => $valor_guaranies, 
            'dolares' => $valor_dolares,
            'pesos' => $valor_pesos,
            'reales' => $valor_reales,
            'deshabilitar_gs' => $activar_guaranies,
            'deshabilitar_$' => $activar_dolares,
            'deshabilitar_ps' => $activar_pesos,
            'deshabilitar_rs' => $activar_reales,
            'formula_gs' => $formula_guaranies,
            'formula_$' => $formula_dolares,
            'formula_ps' => $formula_pesos,
            'formula_rs' => $formula_reales,
            'formula_gs_reves' => $formula_guaranies_reves,
            'formula_usd_reves' => $formula_dolares_reves,
            'formula_ps_reves' => $formula_pesos_reves,
            'formula_rs_reves' => $formula_reales_reves,
            'candec_gs' => $monedas[0]->CANDEC,
            'candec_$' => $monedas[1]->CANDEC,
            'candec_ps' => $monedas[2]->CANDEC,
            'candec_rs' => $monedas[3]->CANDEC,
            'moneda_gs' => $monedas[0]->CODIGO,
            'moneda_$' => $monedas[1]->CODIGO,
            'moneda_ps' => $monedas[2]->CODIGO,
            'moneda_rs' => $monedas[3]->CODIGO,
            'descripcion_gs' => $monedas[0]->DESCRIPCION,
            'descripcion_$' => $monedas[1]->DESCRIPCION,
            'descripcion_ps' => $monedas[2]->DESCRIPCION,
            'descripcion_rs' => $monedas[3]->DESCRIPCION,
            'moneda' => $monedaSistema,
            'candec' => $candecSistema
        ];

            
         
    }*/

    public static function eliminar_cotizacion($datos){


        //DATOS DEL USUARIO

        $user = auth()->user();

        // OBTENER TODAS LAS COTIZACIONES 

        try {

            // ELIMINA SI EXISTE

            if ($datos['data']['existe']=== true){

                $cotizacion=Cotizacion::Where('CODMON','=',$datos['data']['demoneda'])
                ->Where('CODMON1','=',$datos['data']['amoneda'])
                ->Where('MES','=',$datos['data']['mes'])
                ->Where('ANO','=',$datos['data']['ano'])
                ->Where('ID_SUCURSAL','=', $user->id_sucursal)->delete();

            }else{

                // MUESTRA QUE NO EXISTE

                return ["response"=>false,'statusText'=> 'No se encontró la cotización'];
            }

        }catch(Exception $ex){ 

                return ["response"=>false,'statusText'=>$ex->getMessage()];            
        
             }

        // RETORNAR EL VALOR

       return ["response"=>true];

    }

    public static function filtrar_cotizacion($datos){

        $user = auth()->user();

        // OBTENER TODAS LAS COTIZACIONES 

        $cotizacion = Cotizacion::select(DB::raw('*'))
        ->Where('CODMON','=',$datos['data']['demoneda'])
        ->Where('CODMON1','=',$datos['data']['amoneda'])
        ->Where('MES','=',$datos['data']['mes'])
        ->Where('ANO','=',$datos['data']['ano'])
        ->Where('FORMULA','=',$datos['data']['operacion'])
        ->Where('ID_SUCURSAL','=', $user->id_sucursal)
        ->get()
        ->toArray();


        if(count($cotizacion)<=0){
           
           return ["response"=>false];
        }
        // RETORNAR EL VALOR

        return ["response"=>true,"cotizacion"=>$cotizacion];
    }

    public static function cotizacion_guardar($datos){

        $user = auth()->user();
        $dia = date("Y-m-d");
        $hora = date("H:i:s");

        // var_dump($datos);

        // var_dump(Common::quitar_coma($datos['data']['CA01'], 2));
        try {

            // CONTROLA QUE NO EXISTA PARA INSERTAR

            if ($datos['data']['existe']=== false){

                $cotizacion=Cotizacion::insert([
                'CODMON'=> $datos['data']['CODMONE'], 
                'CODMON1'=> $datos['data']['CODMONE1'],
                'CA01'=> Common::quitar_coma($datos['data']['CA01'], 2),
                'CA02'=> Common::quitar_coma($datos['data']['CA02'], 2),
                'CA03' => Common::quitar_coma($datos['data']['CA03'], 2),
                'CA04' => Common::quitar_coma($datos['data']['CA04'], 2),
                'CA05' => Common::quitar_coma($datos['data']['CA05'], 2),
                'CA06' => Common::quitar_coma($datos['data']['CA06'], 2),
                'CA07' => Common::quitar_coma($datos['data']['CA07'], 2),
                'CA08' => Common::quitar_coma($datos['data']['CA08'], 2),
                'CA09' => Common::quitar_coma($datos['data']['CA09'], 2),
                'CA10' => Common::quitar_coma($datos['data']['CA10'], 2),
                'CA11' => Common::quitar_coma($datos['data']['CA11'], 2),
                'CA12' => Common::quitar_coma($datos['data']['CA12'], 2), 
                'CA13' => Common::quitar_coma($datos['data']['CA13'], 2),
                'CA14' => Common::quitar_coma($datos['data']['CA14'], 2),
                'CA15' => Common::quitar_coma($datos['data']['CA15'], 2),
                'CA16' => Common::quitar_coma($datos['data']['CA16'], 2),
                'CA17' => Common::quitar_coma($datos['data']['CA17'], 2),
                'CA18' => Common::quitar_coma($datos['data']['CA18'], 2),
                'CA19' => Common::quitar_coma($datos['data']['CA19'], 2),
                'CA20' => Common::quitar_coma($datos['data']['CA20'], 2),
                'CA21' => Common::quitar_coma($datos['data']['CA21'], 2),
                'CA22' => Common::quitar_coma($datos['data']['CA22'], 2),
                'CA23' => Common::quitar_coma($datos['data']['CA23'], 2),
                'CA24' => Common::quitar_coma($datos['data']['CA24'], 2),
                'CA25' => Common::quitar_coma($datos['data']['CA25'], 2),
                'CA26' => Common::quitar_coma($datos['data']['CA26'], 2),
                'CA27' => Common::quitar_coma($datos['data']['CA27'], 2),
                'CA28' => Common::quitar_coma($datos['data']['CA28'], 2),
                'CA29' => Common::quitar_coma($datos['data']['CA29'], 2),
                'CA30' => Common::quitar_coma($datos['data']['CA30'], 2),
                'CA31' => Common::quitar_coma($datos['data']['CA31'], 2),
                'ANO'=> $datos['data']['ANO'],
                'MES'=> $datos['data']['MES'],
                'FORMULA' => $datos['data']['FORMULA'],
                'USER'=> $user->name,
                'ID_SUCURSAL' => $user->id_sucursal,
                'FECALTAS'=> $dia,
                'HORALTAS'=> $hora]);

            }else{

                // SI EXISTE ACTUALIZA LOS DATOS

                $cotizacion=Cotizacion::Where('CODMON','=',$datos['data']['CODMONE'])
                    ->Where('CODMON1','=',$datos['data']['CODMONE1'])
                    ->Where('MES','=',$datos['data']['MES'])
                    ->Where('ANO','=',$datos['data']['ANO'])
                    ->Where('ID_SUCURSAL','=', $user->id_sucursal)
                    ->update([
                        'CA01'=> Common::quitar_coma($datos['data']['CA01'], 2),
                        'CA02'=> Common::quitar_coma($datos['data']['CA02'], 2),
                        'CA03' => Common::quitar_coma($datos['data']['CA03'], 2),
                        'CA04' => Common::quitar_coma($datos['data']['CA04'], 2),
                        'CA05' => Common::quitar_coma($datos['data']['CA05'], 2),
                        'CA06' => Common::quitar_coma($datos['data']['CA06'], 2),
                        'CA07' => Common::quitar_coma($datos['data']['CA07'], 2),
                        'CA08' => Common::quitar_coma($datos['data']['CA08'], 2),
                        'CA09' => Common::quitar_coma($datos['data']['CA09'], 2),
                        'CA10' => Common::quitar_coma($datos['data']['CA10'], 2),
                        'CA11' => Common::quitar_coma($datos['data']['CA11'], 2),
                        'CA12' => Common::quitar_coma($datos['data']['CA12'], 2), 
                        'CA13' => Common::quitar_coma($datos['data']['CA13'], 2),
                        'CA14' => Common::quitar_coma($datos['data']['CA14'], 2),
                        'CA15' => Common::quitar_coma($datos['data']['CA15'], 2),
                        'CA16' => Common::quitar_coma($datos['data']['CA16'], 2),
                        'CA17' => Common::quitar_coma($datos['data']['CA17'], 2),
                        'CA18' => Common::quitar_coma($datos['data']['CA18'], 2),
                        'CA19' => Common::quitar_coma($datos['data']['CA19'], 2),
                        'CA20' => Common::quitar_coma($datos['data']['CA20'], 2),
                        'CA21' => Common::quitar_coma($datos['data']['CA21'], 2),
                        'CA22' => Common::quitar_coma($datos['data']['CA22'], 2),
                        'CA23' => Common::quitar_coma($datos['data']['CA23'], 2),
                        'CA24' => Common::quitar_coma($datos['data']['CA24'], 2),
                        'CA25' => Common::quitar_coma($datos['data']['CA25'], 2),
                        'CA26' => Common::quitar_coma($datos['data']['CA26'], 2),
                        'CA27' => Common::quitar_coma($datos['data']['CA27'], 2),
                        'CA28' => Common::quitar_coma($datos['data']['CA28'], 2),
                        'CA29' => Common::quitar_coma($datos['data']['CA29'], 2),
                        'CA30' => Common::quitar_coma($datos['data']['CA30'], 2),
                        'CA31' => Common::quitar_coma($datos['data']['CA31'], 2), 
                    'USERM'=>$user->name,
                    'FECMODIF'=>$dia,
                    'HORMODIF'=>$hora]);
            }

            return ["response"=>true];

        }catch(Exception $ex){ 

                return ["response"=>false,'statusText'=>$ex->getMessage()];
              }
    }
}
