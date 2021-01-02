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

        $dia = date("Y-m-d");
        $user = auth()->user();
           $cotizaciones=NewCotizacion::select(DB::raw('ID,FK_DE AS DE,FK_A AS A,VALOR,FECHA,FECALTAS'))
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get()
            ->toArray();

      

        /*  --------------------------------------------------------------------------------- */

        // INICIARA VARIABLES

        //global $search;

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 



        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'ID', 
                            1 => 'DE',
                            2 => 'A',
                            3 => 'VALOR',
                            4 => 'FECHA',
                            5 => 'FECALTAS',
                         
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = NewCotizacion::select(DB::raw('ID,FK_DE AS DE,FK_A AS A,VALOR,FECHA,FECALTAS'))
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->COUNT();

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

             $posts =NewCotizacion::
            select(DB::raw('NewCotizacion.ID,moneda1.DESCRIPCION AS DE,monedas2.DESCRIPCION AS A,NewCotizacion.VALOR,NewCotizacion.FECHA,NewCotizacion.FECALTAS'))
            ->leftjoin('monedas as moneda1','monedas1.codigo','=','FK_DE')
            ->leftjoin('monedas as moneda2','monedas2.codigo','=','FK_A')
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)

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

            
                $posts = NewCotizacion::
            select(DB::raw(
            	'NewCotizacion.ID,
            	moneda1.DESCRIPCION AS DE,
            	monedas2.DESCRIPCION AS A,
            	NewCotizacion.VALOR,
            	NewCotizacion.FECHA,
            	NewCotizacion.FECALTAS'))
            ->leftjoin('monedas as moneda1','monedas1.codigo','=','FK_DE')
            ->leftjoin('monedas as moneda2','monedas2.codigo','=','FK_A')
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->where(function ($query) use ($search) {
                                $query->where('monedas1.DESCRIPCION','LIKE',"%{$search}%")
                                      ->orWhere('monedas2.DESCRIPCION', 'LIKE',"%{$search}%")
                                      ->orWhere('NewCotizacion.ID', 'LIKE',"%{$search}%");
                            })

                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered =NewCotizacion::
            select(DB::raw(
            	'NewCotizacion.ID,
            	moneda1.DESCRIPCION AS DE,
            	monedas2.DESCRIPCION AS A,
            	NewCotizacion.VALOR,
            	NewCotizacion.FECHA,
            	NewCotizacion.FECALTAS'))
            ->leftjoin('monedas as moneda1','monedas1.codigo','=','FK_DE')
            ->leftjoin('monedas as moneda2','monedas2.codigo','=','FK_A')
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->where(function ($query) use ($search) {
                                  $query->where('monedas1.DESCRIPCION','LIKE',"%{$search}%")
                                      ->orWhere('monedas2.DESCRIPCION', 'LIKE',"%{$search}%")
                                      ->orWhere('NewCotizacion.ID', 'LIKE',"%{$search}%");
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

                $nestedData['ID'] = $post->ID;
                $nestedData['DE'] = $post->DE;
                $nestedData['A'] = $post->A;
                $nestedData['FECHA'] = $post->FECHA;
                $nestedData['FECALTAS'] = $post->FECALTAS;
                $nestedData['VALOR'] = $post->VALOR;
               

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
            ->select(DB::raw('ifnull(ID,0)'))
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
            if(count($dolares_new)<=0){
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
    
}
