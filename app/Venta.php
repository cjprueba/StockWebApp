<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Mpdf\Mpdf;
use Fpdf\Fpdf;
use Luecano\NumeroALetras\NumeroALetras;
use App\Ticket;
use App\Ventas_det;
use App\Cliente;
use App\Ventas_det_Descuento;
use App\VentaTarjeta;
use App\VentaGiro;
use App\VentaTransferencia;
use App\VentasDetServicios;
use App\VentasAnulado;

class Venta extends Model
{
    protected $connection = 'retail';

    public static function ventas($fecha)
    {
    	
        /*  --------------------------------------------------------------------------------- */

        //INIICAR VARIABLES

        $anioNull[] = array("TOTAL" => 0,"ID_SUCURSAL"=>0,"SUCURSAL"=>"Ninguna Venta");
        $anio = date('Y');
        $mes = date('m');
        $dia = date('d');
        $sucursal = 4;
        $ventasMes[] = array();

        /*  --------------------------------------------------------------------------------- */

        // CALCULAR DATOS DE FECHAS

        if($mes === 1) {
            $mesAnterior = 12;
            $anioAnterior = $anio - 1;
        } else {
            $mesAnterior = $mes - 1;
            $anioAnterior = $anio;
        }

        if($dia === 1) {
            if ($mes === 1) {
                $diaAnterior = date("d",(mktime(0,0,0,$mes+1,1,$anio-1)-1));
                $diaMesAnterior = 12;
                $diaAnioAnterior = $anio - 1;
            } else {
                $diaAnterior = date("d",(mktime(0,0,0,$mes,1,$anio)-1));
                $diaMesAnterior = $mes - 1;
                $diaAnioAnterior = $anio;
            }
        } else {
            $diaAnterior = $dia - 1;
            $diaMesAnterior = $mes;
            $diaAnioAnterior = $anio;
        }
        
        //print_r("dia Anterior ".$diaAnterior." Mes Anterior ".$diaMesAnterior." Año anteriro ".$diaAnioAnterior);
        /*  --------------------------------------------------------------------------------- */

        // LLAMAR LA CONSULTA - PRIMERA CAJA
        
        // Año y dia Actual

        $diaActualR = DB::connection('retail')
        ->table('VENTAS')
        ->select(DB::raw('SUM(VENTAS.TOTAL) AS TOTAL'))
        ->whereYear('VENTAS.FECALTAS', $anio)
        ->whereMonth('VENTAS.FECALTAS', $mes)
        ->whereDay('VENTAS.FECALTAS', $dia)
        ->where('VENTAS.ID_SUCURSAL', '=', $sucursal)
        ->groupBy('VENTAS.ID_SUCURSAL')
        ->get();

        // Año y mes Anterior
        
        $diaAnteriorR = DB::connection('retail')
        ->table('VENTAS')
        ->select(DB::raw('SUM(VENTAS.TOTAL) AS TOTAL'))
        ->whereYear('VENTAS.FECALTAS', $diaAnioAnterior)
        ->whereMonth('VENTAS.FECALTAS', $diaMesAnterior)
        ->whereDay('VENTAS.FECALTAS', $diaAnterior)
        ->where('VENTAS.ID_SUCURSAL', '=', $sucursal)
        ->groupBy('VENTAS.ID_SUCURSAL')
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // LLAMAR LA CONSULTA - SEGUNDA CAJA
    	
        // Año y mes Actual

        $mesActualR = DB::connection('retail')
    	->table('VENTAS')
    	->select(DB::raw('SUM(VENTAS.TOTAL) AS TOTAL'))
    	->whereYear('VENTAS.FECALTAS', $anio)
        ->whereMonth('VENTAS.FECALTAS', $mes)
    	->where('VENTAS.ID_SUCURSAL', '=', $sucursal)
    	->groupBy('VENTAS.ID_SUCURSAL')
    	->get();

        // Año y mes Anterior
        
        $mesAnteriorR = DB::connection('retail')
        ->table('VENTAS')
        ->select(DB::raw('SUM(VENTAS.TOTAL) AS TOTAL'))
        ->whereYear('VENTAS.FECALTAS', $anioAnterior)
        ->whereMonth('VENTAS.FECALTAS', $mesAnterior)
        ->where('VENTAS.ID_SUCURSAL', '=', $sucursal)
        ->groupBy('VENTAS.ID_SUCURSAL')
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // LLAMAR LA CONSULTA - TERCERA CAJA
        
        // Año Actual

        $anioActualR = DB::connection('retail')
        ->table('VENTAS')
        ->join('SUCURSALES', 'ID_SUCURSAL', '=', 'SUCURSALES.CODIGO')
        ->select(DB::raw('SUM(VENTAS.TOTAL) AS TOTAL, VENTAS.ID_SUCURSAL, SUCURSALES.DESCRIPCION AS SUCURSAL'))
        ->whereYear('VENTAS.FECALTAS', $anio)
        ->where('VENTAS.ID_SUCURSAL', '=', $sucursal)
        ->groupBy('VENTAS.ID_SUCURSAL')
        ->get();

        // Año Anterior
        
        $anioAnteriorR = DB::connection('retail')
        ->table('VENTAS')
        ->select(DB::raw('SUM(VENTAS.TOTAL) AS TOTAL'))
        ->whereYear('VENTAS.FECALTAS', $anio - 1)
        ->where('VENTAS.ID_SUCURSAL', '=', $sucursal)
        ->groupBy('VENTAS.ID_SUCURSAL')
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // CUARTA CAJA
        
        // CANTIDAD DE ANULACIONES

        $anulacionesActual = DB::connection('retail')
        ->table('VENTASDET')
        ->select(DB::raw('COUNT(ANULADO) AS ANULADO'))
        ->whereYear('VENTASDET.FECALTAS', $anio)
        ->whereMonth('VENTASDET.FECALTAS', $mes)
        ->where('VENTASDET.ID_SUCURSAL', '=', $sucursal)
        ->where('VENTASDET.ANULADO', '=', 1)
        ->groupBy('VENTASDET.CODIGO')
        ->groupBy('VENTASDET.CAJA')
        ->get();


        /*  --------------------------------------------------------------------------------- */

        $anulacionesActualTotal = DB::connection('retail')
        ->table('VENTASDET')
        ->select(DB::raw('SUM(PRECIO) AS TOTAL'))
        ->whereYear('VENTASDET.FECALTAS', $anio)
        ->whereMonth('VENTASDET.FECALTAS', $mes)
        ->where('VENTASDET.ID_SUCURSAL', '=', $sucursal)
        ->where('VENTASDET.ANULADO', '=', 1)
        ->where(function ($query) {
            $query->where('VENTASDET.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%')
            ->where('VENTASDET.COD_PROD', '<>', 2);
        })
        ->groupBy('VENTASDET.ID_SUCURSAL')
        ->get();

        /*  --------------------------------------------------------------------------------- */

        //  PREPARAR ARRAY

        unset($ventasMes[0]);

        // MES ACTUAL
        
        if (count($mesActualR) <> 0) {
            foreach ($mesActualR as $value) {
                $ventasMes[0]["mesActual"] = $value->TOTAL;
            }
        } else {
            $ventasMes[0]["mesActual"] = 0;
        }  
         
        if (count($mesAnteriorR) <> 0) {    
            foreach ($mesAnteriorR as $value) {
                $ventasMes[0]["mesAnterior"] = $value->TOTAL;
                $ventasMes[0]["comportamiento"] = number_format((($ventasMes[0]["mesActual"] / $ventasMes[0]["mesAnterior"]) - 1) * 100, 2);
            }
        } else {
            $ventasMes[0]["mesAnterior"] = 0;
            if ($ventasMes[0]["mesActual"] === 0) {
                $ventasMes[0]["comportamiento"] = 0;
            } else {
                $ventasMes[0]["comportamiento"] = 100;
            }
        } 

        // DIA ACTUAL

        if (count($diaActualR) <> 0) {
            foreach ($diaActualR as $key => $value) {
                $ventasMes[0]["diaActual"] = $value->TOTAL;
            }
        } else {
            $ventasMes[0]["diaActual"] = 0;
        }    

        // DIA ANTERIOR

        if (count($diaAnteriorR) <> 0) {
            foreach ($diaAnteriorR as $key => $value) {
                $ventasMes[0]["diaAnterior"] = $value->TOTAL;
                $ventasMes[0]["comportamientoDia"] = number_format((($ventasMes[0]["diaActual"] / $ventasMes[0]["diaAnterior"]) - 1) * 100, 2);
            }
        } else {
            $ventasMes[0]["diaAnterior"] = 0;
            if ($ventasMes[0]["diaActual"] === 0) {
                $ventasMes[0]["comportamientoDia"] = 0;
            } else {
                $ventasMes[0]["comportamientoDia"] = 100;
            }
        } 

        // AÑO ACTUAL

        if (count($anioActualR) <> 0) {
            foreach ($anioActualR as $key => $value) {
                $ventasMes[0]["anioActual"] = $value->TOTAL;
                $ventasMes[0]["sucursal"] = $value->SUCURSAL;
                $ventasMes[0]["id_sucursal"] = $value->ID_SUCURSAL; 
            }
        } else {
            $ventasMes[0]["anioActual"] = 0;
        }    

        // AÑO ANTERIOR

        if (count($anioAnteriorR) <> 0) {
            foreach ($anioAnteriorR as $key => $value) {
                $ventasMes[0]["anioAnterior"] = $value->TOTAL;
                $ventasMes[0]["comportamientoAnio"] = number_format((($ventasMes[0]["anioActual"] / $ventasMes[0]["anioAnterior"]) - 1) * 100, 2);
            }
        } else {
            $ventasMes[0]["anioAnterior"] = 0;
            if ($ventasMes[0]["anioActual"] === 0) {
                $ventasMes[0]["comportamientoAnio"] = 0;
            } else {
                $ventasMes[0]["comportamientoAnio"] = 100;
            }
        } 

        // ANULACIONES CANTIDAD

        if (count($anulacionesActual) <> 0) {
            $ventasMes[0]["anulado"] = 0;
            foreach ($anulacionesActual as $value) {
                $ventasMes[0]["anulado"] += 1;
            }
        } else {
            $ventasMes[0]["anulado"] = 0;
        }

        // ANULACIONES TOTAL

        if (count($anulacionesActualTotal) <> 0) {
            foreach ($anulacionesActualTotal as $value) {
                $ventasMes[0]["anuladoTotal"] = -$value->TOTAL;
            }
        } else {
            $ventasMes[0]["anuladoTotal"] = 0;
        }
            
        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        if (count($ventasMes) === 0) {
            return $anioNull;
        } else {
            return $ventasMes;
        }

        /*  --------------------------------------------------------------------------------- */
        
    }

    public static function generarConsulta($datos) 
    {

        
         /*  --------------------------------------------------------------------------------- */

         // INCICIAR VARIABLES 

        $marcas[] = array();
        $categorias[] = array();
        $totales[] = array();

        $inicio = date('Y-m-d', strtotime($datos['Inicio']));
        $final = date('Y-m-d', strtotime($datos['Final']));
        $sucursal = $datos['Sucursal'];

        // CARGAR MARCAS 0 EN VENTAS

        //array_unshift($datos['Marcas'], 0);
        //var_dump($datos['Marcas']);
        /*  --------------------------------------------------------------------------------- */

        /*  *********** TODAS LAS VENTAS ENTRE LAS FECHAS INTERVALOS *********** */

        if ($datos['AllCategory'] AND $datos['AllBrand']) {
            $ventasdet = DB::connection('retail')->table('VENTASDET as v')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->select(DB::raw('SUM(v.PRECIO) AS PRECIO'),
            DB::raw('SUM(v.CANTIDAD) AS VENDIDO'),
            DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = v.COD_PROD) AND (l.ID_SUCURSAL = v.ID_SUCURSAL))),0) AS STOCK'),
            DB::raw('MARCA.DESCRIPCION AS MARCA_NOMBRE'),
            DB::raw('LINEAS.DESCRIPCION AS LINEA_NOMBRE'),
            DB::raw('v.COD_PROD'),
            DB::raw('PRODUCTOS.MARCA AS MARCA'),
            DB::raw('PRODUCTOS.LINEA AS LINEA'))  
            ->whereBetween('v.FECALTAS', [$inicio , $final])
            ->where([
                ['v.ID_SUCURSAL', '=', $sucursal],
                ['v.ANULADO', '<>', 1],
                ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
            ])
            ->groupBy('v.COD_PROD')
            ->get()
            ->toArray(); 

            
        } else if ($datos['AllCategory']) {
            
            $ventasdet = DB::connection('retail')->table('VENTASDET as v')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->select(DB::raw('SUM(v.PRECIO) AS PRECIO'),
            DB::raw('SUM(v.CANTIDAD) AS VENDIDO'),
            DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = v.COD_PROD) AND (l.ID_SUCURSAL = v.ID_SUCURSAL)) Group By v.COD_PROD),0) AS STOCK'),
            DB::raw('MARCA.DESCRIPCION AS MARCA_NOMBRE'),
            DB::raw('LINEAS.DESCRIPCION AS LINEA_NOMBRE'),
            DB::raw('v.COD_PROD'),
            DB::raw('PRODUCTOS.MARCA AS MARCA'),
            DB::raw('PRODUCTOS.LINEA AS LINEA'))  
            ->whereBetween('v.FECALTAS', [$inicio , $final])
            ->whereIn('PRODUCTOS.MARCA', $datos['Marcas'])
            ->where([
                ['v.ID_SUCURSAL', '=', $sucursal],
                ['v.ANULADO', '<>', 1],
                ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
            ])
            ->groupBy('v.COD_PROD')
            ->get()
            ->toArray(); 

        } else if ($datos['AllBrand']) {
             
            $ventasdet = DB::connection('retail')->table('VENTASDET as v')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->select(DB::raw('SUM(v.PRECIO) AS PRECIO'),
            DB::raw('SUM(v.CANTIDAD) AS VENDIDO'),
            DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = v.COD_PROD) AND (l.ID_SUCURSAL = v.ID_SUCURSAL)) Group By v.COD_PROD),0) AS STOCK'),
            DB::raw('MARCA.DESCRIPCION AS MARCA_NOMBRE'),
            DB::raw('LINEAS.DESCRIPCION AS LINEA_NOMBRE'),
            DB::raw('v.COD_PROD'),
            DB::raw('PRODUCTOS.MARCA AS MARCA'),
            DB::raw('PRODUCTOS.LINEA AS LINEA'))  
            ->whereBetween('v.FECALTAS', [$inicio , $final])
            ->whereIn('PRODUCTOS.LINEA', $datos['Categorias'])
            ->where([
                ['v.ID_SUCURSAL', '=', $sucursal],
                ['v.ANULADO', '<>', 1],
                ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
            ])
            ->groupBy('v.COD_PROD')
            ->get()
            ->toArray(); 
        } else  {

            $ventasdet = DB::connection('retail')->table('VENTASDET as v')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->select(DB::raw('SUM(v.PRECIO) AS PRECIO'),
            DB::raw('SUM(v.CANTIDAD) AS VENDIDO'),
            DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = v.COD_PROD) AND (l.ID_SUCURSAL = v.ID_SUCURSAL)) Group By v.COD_PROD),0) AS STOCK'),
            DB::raw('MARCA.DESCRIPCION AS MARCA_NOMBRE'),
            DB::raw('LINEAS.DESCRIPCION AS LINEA_NOMBRE'),
            DB::raw('v.COD_PROD'),
            DB::raw('PRODUCTOS.MARCA AS MARCA'),
            DB::raw('PRODUCTOS.LINEA AS LINEA'))  
            ->whereBetween('v.FECALTAS', [$inicio , $final])
            ->whereIn('PRODUCTOS.MARCA', $datos['Marcas'])
            ->whereIn('PRODUCTOS.LINEA', $datos['Categorias'])
            ->where([
                ['v.ID_SUCURSAL', '=', $sucursal],
                ['v.ANULADO', '<>', 1],
                ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
            ])
            ->groupBy('v.COD_PROD')
            ->get()
            ->toArray(); 
        }

        /*  --------------------------------------------------------------------------------- */

        /*  *********** TODOS LOS DESCUENTOS GENERALES  *********** */

        $descuentos = DB::connection('retail')->table('VENTASDET as v')
        ->select(DB::raw('v.CODIGO'),
        DB::raw('substring(v.DESCRIPCION, 11, 3) AS PORCENTAJE'),
        DB::raw('v.CODIGO'),  
        DB::raw('v.CAJA'),
        DB::raw('v.ID_SUCURSAL'),
        DB::raw('v.ITEM'))  
        ->whereBetween('v.FECALTAS', [$inicio , $final])
        ->where([
            ['v.ID_SUCURSAL', '=', $sucursal],
            ['v.ANULADO', '<>', 1],
            ['v.DESCRIPCION', 'LIKE', 'DESCUENTO%'],
            ['v.COD_PROD', '=', 2],
        ])
        ->get(); 

       
        /*  --------------------------------------------------------------------------------- */

        foreach ($descuentos as $descuento) {

            /*  --------------------------------------------------------------------------------- */

            /*  *********** RECORRER LAS VENTAS CON LOS DESCUENTOS GENERALES *********** */

            if ($datos['AllCategory'] AND $datos['AllBrand']) {
                $ventas_con_descuentos = DB::connection('retail')->table('VENTASDET as v')
                ->select(DB::raw('v.COD_PROD'),
                DB::raw('v.PRECIO'),
                DB::raw('v.PRECIO_UNIT'),
                DB::raw('v.ITEM'))
                ->where([
                    ['v.ID_SUCURSAL', '=', $descuento->ID_SUCURSAL],
                    ['v.CODIGO', '=', $descuento->CODIGO],
                    ['v.CAJA', '=', $descuento->CAJA],
                    ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
                ])
                ->get();

            } else if ($datos['AllCategory']) { 

                 $ventas_con_descuentos = DB::connection('retail')->table('VENTASDET as v')
                ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
                ->select(DB::raw('v.COD_PROD'),
                DB::raw('v.PRECIO'),
                DB::raw('v.PRECIO_UNIT'),
                DB::raw('v.ITEM'))  
                ->whereBetween('v.FECALTAS', [$inicio , $final])
                ->whereIn('PRODUCTOS.MARCA', $datos['Marcas'])
                ->where([
                    ['v.ID_SUCURSAL', '=', $descuento->ID_SUCURSAL],
                    ['v.CODIGO', '=', $descuento->CODIGO],
                    ['v.CAJA', '=', $descuento->CAJA],
                    ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
                ])
                ->get();

            } else if ($datos['AllBrand']) { 

                 $ventas_con_descuentos = DB::connection('retail')->table('VENTASDET as v')
                ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
                ->select(DB::raw('v.COD_PROD'),
                DB::raw('v.PRECIO'),
                DB::raw('v.PRECIO_UNIT'),
                DB::raw('v.ITEM'))  
                ->whereBetween('v.FECALTAS', [$inicio , $final])
                ->whereIn('PRODUCTOS.LINEA', $datos['Categorias'])
                ->where([
                    ['v.ID_SUCURSAL', '=', $descuento->ID_SUCURSAL],
                    ['v.CODIGO', '=', $descuento->CODIGO],
                    ['v.CAJA', '=', $descuento->CAJA],
                    ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
                ])
                ->get();

            } else {

                $ventas_con_descuentos = DB::connection('retail')->table('VENTASDET as v')
                ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
                ->select(DB::raw('v.COD_PROD'),
                DB::raw('v.PRECIO'),
                DB::raw('v.PRECIO_UNIT'),
                DB::raw('v.ITEM'))  
                ->whereBetween('v.FECALTAS', [$inicio , $final])
                ->whereIn('PRODUCTOS.MARCA', $datos['Marcas'])
                ->whereIn('PRODUCTOS.LINEA', $datos['Categorias'])
                ->where([
                    ['v.ID_SUCURSAL', '=', $descuento->ID_SUCURSAL],
                    ['v.CODIGO', '=', $descuento->CODIGO],
                    ['v.CAJA', '=', $descuento->CAJA],
                    ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
                ])
                ->get();

            }

            /*  --------------------------------------------------------------------------------- */

            /*  *********** EMPEZAR A MODIFICAR LOS VALORES DEL ARRAY *********** */

            foreach ($ventas_con_descuentos as $ventas_con_descuento) {
                if ($ventas_con_descuento->ITEM < $descuento->ITEM) {
                    $key = array_search($ventas_con_descuento->COD_PROD, array_column($ventasdet, 'COD_PROD'));
                    $ventasdet[$key]->PRECIO = (int)$ventasdet[$key]->PRECIO - (((int)$ventas_con_descuento->PRECIO * (int)$descuento->PORCENTAJE)/100);
                }
            }

            /*  --------------------------------------------------------------------------------- */
        }

        /*  --------------------------------------------------------------------------------- */

        unset($marcas[0]);
        unset($categorias[0]);
        unset($totales[0]);

        /*  --------------------------------------------------------------------------------- */

        // CREAR FILA PARA PRODUCTOS CON MARCAS INDEFINIDAS

        // $marcas[0]["CODIGO"] = 0;
        // $marcas[0]["MARCA"] = "INDEFINIDO";
        // $marcas[0]["TOTAL"] = 0;

        /*  --------------------------------------------------------------------------------- */
        foreach ($ventasdet as $key => $value) {


            /*  --------------------------------------------------------------------------------- */

            // CREAR ARRAY DE MARCAS

            if (array_key_exists($value->MARCA, $marcas))   {
                $marcas[$value->MARCA]["TOTAL"] += $value->PRECIO;
                $marcas[$value->MARCA]["STOCK"] += $value->STOCK;
                $marcas[$value->MARCA]["VENDIDO"] += $value->VENDIDO;
            } else {
                $marcas[$value->MARCA]["CODIGO"] = $value->MARCA;
                $marcas[$value->MARCA]["MARCA"] = $value->MARCA_NOMBRE;
                $marcas[$value->MARCA]["STOCK"] = $value->STOCK;
                $marcas[$value->MARCA]["TOTAL"] = $value->PRECIO;
                $marcas[$value->MARCA]["VENDIDO"] = $value->VENDIDO;
                $marcas[$value->MARCA]["STOCK_G"] = 0;
            }

             /*  --------------------------------------------------------------------------------- */

            // CREAR ARRAY DE CATEGORIAS

            if (array_key_exists($value->MARCA.''.$value->LINEA, $categorias))   {
                $categorias[$value->MARCA.''.$value->LINEA]["TOTAL"] += $value->PRECIO;
                $categorias[$value->MARCA.''.$value->LINEA]["STOCK"] += $value->STOCK;
                $categorias[$value->MARCA.''.$value->LINEA]["VENDIDO"] += $value->VENDIDO;
            } else {
                $categorias[$value->MARCA.''.$value->LINEA]["CODIGO"] = $value->LINEA;
                $categorias[$value->MARCA.''.$value->LINEA]["LINEA"] = $value->LINEA_NOMBRE;
                $categorias[$value->MARCA.''.$value->LINEA]["STOCK"] = $value->STOCK;
                $categorias[$value->MARCA.''.$value->LINEA]["TOTAL"] = $value->PRECIO;
                $categorias[$value->MARCA.''.$value->LINEA]["MARCA"] = $value->MARCA;
                $categorias[$value->MARCA.''.$value->LINEA]["VENDIDO"] = $value->VENDIDO;
                $categorias[$value->MARCA.''.$value->LINEA]["STOCK_G"] = 0;
            }

            /*  --------------------------------------------------------------------------------- */

        }

        /*  --------------------------------------------------------------------------------- */

        // BUSCAR STOCK GENERAL DE TODAS CATEGORIAS

        $stockGeneral = DB::connection('retail')
            ->table('LOTES as l')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'l.COD_PROD')
            ->select(DB::raw('SUM(l.CANTIDAD) AS CANTIDAD'),
            DB::raw('PRODUCTOS.MARCA'),
            DB::raw('PRODUCTOS.LINEA'))
            ->where('l.ID_SUCURSAL', '=', $sucursal)
            ->groupBy('PRODUCTOS.MARCA', 'PRODUCTOS.LINEA')
            ->get();

        foreach ($stockGeneral as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            if (array_key_exists($value->MARCA, $marcas))   {

                // CARGAR STOCK GENERAL A MARCA

                $marcas[$value->MARCA]["STOCK_G"] += $value->CANTIDAD;

            }

            if (array_key_exists($value->MARCA.''.$value->LINEA, $categorias))   {

                // CARGAR STOCK GENERAL CATEGORIA

                $categorias[$value->MARCA.''.$value->LINEA]["STOCK_G"] += $value->CANTIDAD;
            }

            /*  --------------------------------------------------------------------------------- */
        }

        /*  --------------------------------------------------------------------------------- */

        $marca[] = (array) $marcas;
        $categoria[] = (array) $categorias;

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR TODOS LOS ARRAYS

        return ['ventas' => $ventasdet, 'marcas' => (array)$marca[0], 'categorias' => (array)$categoria[0]];

        /*  --------------------------------------------------------------------------------- */
    }

     public static function generarTablaMarca($datos) 
    {

        
         /*  --------------------------------------------------------------------------------- */

         // INCICIAR VARIABLES 

        $inicio = date('Y-m-d', strtotime($datos['Inicio']));
        $final = date('Y-m-d', strtotime($datos['Final']));
        $mes = date('m', strtotime($datos['Inicio']));
        $anio = date('Y', strtotime($datos['Inicio']));
        $sucursal = $datos['Sucursal'];

        $total = 0;
        $totalVendido = 0;
        $totalStock = 0;


        // CARGAR MES PASADO

        if ($mes === 1) {
            $mes = 12;
            $anio = $anio - 1;
        } else {
            $mes = $mes - 1;
        }
        
        /*  --------------------------------------------------------------------------------- */

        /*  *********** TODAS LAS VENTAS ENTRE LAS FECHAS INTERVALOS *********** */

        

            $ventasdet = DB::connection('retail')->table('VENTASDET as v')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
            ->select(DB::raw('SUM(v.PRECIO) AS PRECIO'),
            DB::raw('0 AS COMPORTAMIENTO_PRECIO'),
            DB::raw('0 AS COMPORTAMIENTO_VENDIDO'), 
            DB::raw('0 AS PRECIO_ANTERIOR'), 
            DB::raw('0 AS VENDIDO_ANTERIOR'), 
            DB::raw('0 AS P_TOTAL'),    
            DB::raw('SUM(v.CANTIDAD) AS VENDIDO'),
            DB::raw('0 AS P_VENDIDO'),
            DB::raw('0 AS STOCK_G'),
            DB::raw('0 AS P_STOCK'),
            DB::raw('MARCA.DESCRIPCION AS MARCA_NOMBRE'),
            DB::raw('PRODUCTOS.MARCA'))
            ->whereBetween('v.FECALTAS', [$inicio , $final])
            ->where([
                ['v.ID_SUCURSAL', '=', $sucursal],
                ['v.ANULADO', '<>', 1],
                ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
            ])
            ->groupBy('PRODUCTOS.MARCA')
            ->get()
            ->toArray(); 

        /*  *********** MES ANTERIOR *********** */
            
            $ventasdetAnterior = DB::connection('retail')->table('VENTASDET as v')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
            ->select(DB::raw('SUM(v.PRECIO) AS PRECIO'),
            DB::raw('SUM(v.CANTIDAD) AS VENDIDO'),
            DB::raw('PRODUCTOS.MARCA'))
            ->whereMonth('v.FECALTAS', $mes)
            ->whereYear('v.FECALTAS', $anio)
            ->where([
                ['v.ID_SUCURSAL', '=', $sucursal],
                ['v.ANULADO', '<>', 1],
                ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
            ])
            ->groupBy('PRODUCTOS.MARCA')
            ->get()
            ->toArray(); 

            
        
        /*  --------------------------------------------------------------------------------- */

        /*  *********** TODOS LOS DESCUENTOS GENERALES  *********** */

        $descuentos = DB::connection('retail')->table('VENTASDET as v')
        ->select(DB::raw('v.CODIGO'),
        DB::raw('substring(v.DESCRIPCION, 11, 3) AS PORCENTAJE'),
        DB::raw('v.CODIGO'),  
        DB::raw('v.CAJA'),
        DB::raw('v.ID_SUCURSAL'),
        DB::raw('v.ITEM'))  
        ->whereBetween('v.FECALTAS', [$inicio , $final])
        ->where([
            ['v.ID_SUCURSAL', '=', $sucursal],
            ['v.ANULADO', '<>', 1],
            ['v.DESCRIPCION', 'LIKE', 'DESCUENTO%'],
            ['v.COD_PROD', '=', 2],
        ])
        ->get(); 

       
        /*  --------------------------------------------------------------------------------- */

        foreach ($descuentos as $descuento) {

            /*  --------------------------------------------------------------------------------- */

            /*  *********** RECORRER LAS VENTAS CON LOS DESCUENTOS GENERALES *********** */

            
                $ventas_con_descuentos = DB::connection('retail')->table('VENTASDET as v')
                ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
                ->select(DB::raw('v.PRECIO'),
                DB::raw('v.PRECIO_UNIT'),
                DB::raw('v.ITEM'),
                DB::raw('PRODUCTOS.MARCA'))
                ->where([
                    ['v.ID_SUCURSAL', '=', $descuento->ID_SUCURSAL],
                    ['v.CODIGO', '=', $descuento->CODIGO],
                    ['v.CAJA', '=', $descuento->CAJA],
                    ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
                ])
                ->get();

            

            /*  --------------------------------------------------------------------------------- */

            /*  *********** EMPEZAR A MODIFICAR LOS VALORES DEL ARRAY *********** */

            foreach ($ventas_con_descuentos as $ventas_con_descuento) {
                if ($ventas_con_descuento->ITEM < $descuento->ITEM) {
                    $key = array_search($ventas_con_descuento->MARCA, array_column($ventasdet, 'MARCA'));
                    $ventasdet[$key]->PRECIO = (int)$ventasdet[$key]->PRECIO - (((int)$ventas_con_descuento->PRECIO * (int)$descuento->PORCENTAJE)/100);
                }
            }

            /*  --------------------------------------------------------------------------------- */
        }

        
        /*  --------------------------------------------------------------------------------- */

        /*  *********** TODOS LOS DESCUENTOS GENERALES MES ANTERIOR *********** */

        $descuentos = DB::connection('retail')->table('VENTASDET as v')
        ->select(DB::raw('v.CODIGO'),
        DB::raw('substring(v.DESCRIPCION, 11, 3) AS PORCENTAJE'),
        DB::raw('v.CODIGO'),  
        DB::raw('v.CAJA'),
        DB::raw('v.ID_SUCURSAL'),
        DB::raw('v.ITEM'))  
        ->whereMonth('v.FECALTAS', $mes)
        ->whereYear('v.FECALTAS', $anio)
        ->where([
            ['v.ID_SUCURSAL', '=', $sucursal],
            ['v.ANULADO', '<>', 1],
            ['v.DESCRIPCION', 'LIKE', 'DESCUENTO%'],
            ['v.COD_PROD', '=', 2],
        ])
        ->get(); 

       
        /*  --------------------------------------------------------------------------------- */

        foreach ($descuentos as $descuento) {

            /*  --------------------------------------------------------------------------------- */

            /*  *********** RECORRER LAS VENTAS CON LOS DESCUENTOS GENERALES *********** */

            
                $ventas_con_descuentos = DB::connection('retail')->table('VENTASDET as v')
                ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
                ->select(DB::raw('v.PRECIO'),
                DB::raw('v.PRECIO_UNIT'),
                DB::raw('v.ITEM'),
                DB::raw('PRODUCTOS.MARCA'))
                ->where([
                    ['v.ID_SUCURSAL', '=', $descuento->ID_SUCURSAL],
                    ['v.CODIGO', '=', $descuento->CODIGO],
                    ['v.CAJA', '=', $descuento->CAJA],
                    ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
                ])
                ->get();

            

            /*  --------------------------------------------------------------------------------- */

            /*  *********** EMPEZAR A MODIFICAR LOS VALORES DEL ARRAY *********** */

            foreach ($ventas_con_descuentos as $ventas_con_descuento) {
                if ($ventas_con_descuento->ITEM < $descuento->ITEM) {
                    $key = array_search($ventas_con_descuento->MARCA, array_column($ventasdetAnterior, 'MARCA'));
                    $ventasdetAnterior[$key]->PRECIO = (int)$ventasdetAnterior[$key]->PRECIO - (((int)$ventas_con_descuento->PRECIO * (int)$descuento->PORCENTAJE)/100);
                }
            }

            /*  --------------------------------------------------------------------------------- */
        }

        
        /*  --------------------------------------------------------------------------------- */
        // BUSCAR STOCK GENERAL DE TODAS CATEGORIAS

        // $stockGeneral = DB::connection('retail')
        //     ->table('LOTES as l')
        //     ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'l.COD_PROD')
        //     ->select(DB::raw('SUM(l.CANTIDAD) AS CANTIDAD'),
        //     DB::raw('PRODUCTOS.MARCA'))
        //     ->where('l.ID_SUCURSAL', '=', $sucursal)
        //     ->groupBy('PRODUCTOS.MARCA')
        //     ->get();

        //return $stockGeneral;    
        foreach ($ventasdet as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            // $key2 = array_search($value->MARCA, array_column($ventasdet, 'MARCA'));
            // if ($key2 <> "null") {

            //     if (array_key_exists($key2, $ventasdet))   {
            //         $ventasdet[$key2]->STOCK_G += $value->CANTIDAD;   
            //     }
            // }

            $stockGeneral = DB::connection('retail')
            ->table('LOTES as l')
            ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'l.COD_PROD')
            ->select(DB::raw('SUM(l.CANTIDAD) AS CANTIDAD'),
            DB::raw('PRODUCTOS.MARCA'))
            ->where('PRODUCTOS.MARCA', '=', $value->MARCA)
            ->where('l.ID_SUCURSAL', '=', $sucursal)
            ->groupBy('PRODUCTOS.MARCA')
            ->get();

            $ventasdet[$key]->STOCK_G = $stockGeneral[0]->CANTIDAD;

            /*  --------------------------------------------------------------------------------- */
        }

        /*  --------------------------------------------------------------------------------- */

        // TOTALES

        foreach ($ventasdet as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            // OBTENER LA UBICACION DE LA MARCA EN LAS VENTAS ANTERIORES 

            $key2 = array_search($value->MARCA, array_column($ventasdetAnterior, 'MARCA'));
            
            /*  --------------------------------------------------------------------------------- */

            // CARGAR PRECIOS ANTERIORES

            if ($key2 <> null) {
                $ventasdet[$key]->PRECIO_ANTERIOR = $ventasdetAnterior[$key2]->PRECIO;
                $ventasdet[$key]->VENDIDO_ANTERIOR = $ventasdetAnterior[$key2]->VENDIDO;
            } else if ($key2 === 0) {
                $ventasdet[$key]->PRECIO_ANTERIOR = $ventasdetAnterior[$key2]->PRECIO;
                $ventasdet[$key]->VENDIDO_ANTERIOR = $ventasdetAnterior[$key2]->VENDIDO; 
            }

            /*  --------------------------------------------------------------------------------- */

            // CALCULAR COMPORTAMIENTOS 

            if ($ventasdet[$key]->PRECIO_ANTERIOR <> 0) {
                $ventasdet[$key]->COMPORTAMIENTO_PRECIO = number_format((($ventasdet[$key]->PRECIO / $ventasdet[$key]->PRECIO_ANTERIOR) - 1) * 100, 2);
            } else {
                $ventasdet[$key]->COMPORTAMIENTO_PRECIO = 100;
            }
            
            if ($ventasdet[$key]->VENDIDO_ANTERIOR <> 0) {

                $ventasdet[$key]->COMPORTAMIENTO_VENDIDO = number_format((($ventasdet[$key]->VENDIDO / $ventasdet[$key]->VENDIDO_ANTERIOR) - 1) * 100, 2);
            } else {
                $ventasdet[$key]->COMPORTAMIENTO_VENDIDO = 100;
            }
            
            /*  --------------------------------------------------------------------------------- */

            // CARGAR LOS TOTALES 

            $total += $value->PRECIO;
            $totalVendido += $value->VENDIDO;
            $totalStock += $value->STOCK_G;

            /*  --------------------------------------------------------------------------------- */
        }

        /*  --------------------------------------------------------------------------------- */

        // CALCULAR LOS PORCENTAJES

        foreach ($ventasdet as $key => $value) {
            $ventasdet[$key]->P_TOTAL = round(($value->PRECIO * 100) / $total, 2);
            $ventasdet[$key]->P_VENDIDO = round(($value->VENDIDO * 100) / $totalVendido, 2);
            $ventasdet[$key]->P_STOCK = round(($value->STOCK_G * 100) / $totalStock, 2);
        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR TODOS LOS ARRAYS

        return ['marcas' => $ventasdet];

        /*  --------------------------------------------------------------------------------- */
    }

    public static function generarTablaCategoria($datos) 
    {

        
         /*  --------------------------------------------------------------------------------- */

         // INCICIAR VARIABLES 

        $inicio = date('Y-m-d', strtotime($datos['Inicio']));
        $final = date('Y-m-d', strtotime($datos['Final']));
        $mes = date('m', strtotime($datos['Inicio']));
        $anio = date('Y', strtotime($datos['Inicio']));
        $sucursal = $datos['Sucursal'];

        $total = 0;
        $totalVendido = 0;
        $totalStock = 0;


        // CARGAR MES PASADO

        if ($mes === 1) {
            $mes = 12;
            $anio = $anio - 1;
        } else {
            $mes = $mes - 1;
        }
        
        /*  --------------------------------------------------------------------------------- */

        /*  *********** TODAS LAS VENTAS ENTRE LAS FECHAS INTERVALOS *********** */

        

            $ventasdet = DB::connection('retail')->table('VENTASDET as v')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->select(DB::raw('SUM(v.PRECIO) AS PRECIO'),
            DB::raw('0 AS COMPORTAMIENTO_PRECIO'),
            DB::raw('0 AS COMPORTAMIENTO_VENDIDO'), 
            DB::raw('0 AS PRECIO_ANTERIOR'), 
            DB::raw('0 AS VENDIDO_ANTERIOR'), 
            DB::raw('0 AS P_TOTAL'),    
            DB::raw('SUM(v.CANTIDAD) AS VENDIDO'),
            DB::raw('0 AS P_VENDIDO'),
            DB::raw('0 AS STOCK_G'),
            DB::raw('0 AS P_STOCK'),
            DB::raw('LINEAS.DESCRIPCION AS LINEA_NOMBRE'),
            DB::raw('PRODUCTOS.LINEA'))
            ->whereBetween('v.FECALTAS', [$inicio , $final])
            ->where([
                ['v.ID_SUCURSAL', '=', $sucursal],
                ['v.ANULADO', '<>', 1],
                ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
            ])
            ->groupBy('PRODUCTOS.LINEA')
            ->get()
            ->toArray(); 

        /*  *********** MES ANTERIOR *********** */
            
            $ventasdetAnterior = DB::connection('retail')->table('VENTASDET as v')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->select(DB::raw('SUM(v.PRECIO) AS PRECIO'),
            DB::raw('SUM(v.CANTIDAD) AS VENDIDO'),
            DB::raw('LINEAS.DESCRIPCION AS LINEA_NOMBRE'),
            DB::raw('PRODUCTOS.LINEA'))
            ->whereMonth('v.FECALTAS', $mes)
            ->whereYear('v.FECALTAS', $anio)
            ->where([
                ['v.ID_SUCURSAL', '=', $sucursal],
                ['v.ANULADO', '<>', 1],
                ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
            ])
            ->groupBy('PRODUCTOS.LINEA')
            ->get()
            ->toArray(); 

            
        
        /*  --------------------------------------------------------------------------------- */

        /*  *********** TODOS LOS DESCUENTOS GENERALES  *********** */

        $descuentos = DB::connection('retail')->table('VENTASDET as v')
        ->select(DB::raw('v.CODIGO'),
        DB::raw('substring(v.DESCRIPCION, 11, 3) AS PORCENTAJE'),
        DB::raw('v.CODIGO'),  
        DB::raw('v.CAJA'),
        DB::raw('v.ID_SUCURSAL'),
        DB::raw('v.ITEM'))  
        ->whereBetween('v.FECALTAS', [$inicio , $final])
        ->where([
            ['v.ID_SUCURSAL', '=', $sucursal],
            ['v.ANULADO', '<>', 1],
            ['v.DESCRIPCION', 'LIKE', 'DESCUENTO%'],
            ['v.COD_PROD', '=', 2],
        ])
        ->get(); 

       
        /*  --------------------------------------------------------------------------------- */

        foreach ($descuentos as $descuento) {

            /*  --------------------------------------------------------------------------------- */

            /*  *********** RECORRER LAS VENTAS CON LOS DESCUENTOS GENERALES *********** */

            
                $ventas_con_descuentos = DB::connection('retail')->table('VENTASDET as v')
                ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
                ->select(DB::raw('v.PRECIO'),
                DB::raw('v.PRECIO_UNIT'),
                DB::raw('v.ITEM'),
                DB::raw('PRODUCTOS.LINEA'))
                ->where([
                    ['v.ID_SUCURSAL', '=', $descuento->ID_SUCURSAL],
                    ['v.CODIGO', '=', $descuento->CODIGO],
                    ['v.CAJA', '=', $descuento->CAJA],
                    ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
                ])
                ->get();

            

            /*  --------------------------------------------------------------------------------- */

            /*  *********** EMPEZAR A MODIFICAR LOS VALORES DEL ARRAY *********** */

            foreach ($ventas_con_descuentos as $ventas_con_descuento) {
                if ($ventas_con_descuento->ITEM < $descuento->ITEM) {
                    $key = array_search($ventas_con_descuento->LINEA, array_column($ventasdet, 'LINEA'));
                    $ventasdet[$key]->PRECIO = (int)$ventasdet[$key]->PRECIO - (((int)$ventas_con_descuento->PRECIO * (int)$descuento->PORCENTAJE)/100);
                }
            }

            /*  --------------------------------------------------------------------------------- */
        }

        
        /*  --------------------------------------------------------------------------------- */

        /*  *********** TODOS LOS DESCUENTOS GENERALES MES ANTERIOR *********** */

        $descuentos = DB::connection('retail')->table('VENTASDET as v')
        ->select(DB::raw('v.CODIGO'),
        DB::raw('substring(v.DESCRIPCION, 11, 3) AS PORCENTAJE'),
        DB::raw('v.CODIGO'),  
        DB::raw('v.CAJA'),
        DB::raw('v.ID_SUCURSAL'),
        DB::raw('v.ITEM'))  
        ->whereMonth('v.FECALTAS', $mes)
        ->whereYear('v.FECALTAS', $anio)
        ->where([
            ['v.ID_SUCURSAL', '=', $sucursal],
            ['v.ANULADO', '<>', 1],
            ['v.DESCRIPCION', 'LIKE', 'DESCUENTO%'],
            ['v.COD_PROD', '=', 2],
        ])
        ->get(); 

       
        /*  --------------------------------------------------------------------------------- */

        foreach ($descuentos as $descuento) {

            /*  --------------------------------------------------------------------------------- */

            /*  *********** RECORRER LAS VENTAS CON LOS DESCUENTOS GENERALES *********** */

            
                $ventas_con_descuentos = DB::connection('retail')->table('VENTASDET as v')
                ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
                ->select(DB::raw('v.PRECIO'),
                DB::raw('v.PRECIO_UNIT'),
                DB::raw('v.ITEM'),
                DB::raw('PRODUCTOS.LINEA'))
                ->where([
                    ['v.ID_SUCURSAL', '=', $descuento->ID_SUCURSAL],
                    ['v.CODIGO', '=', $descuento->CODIGO],
                    ['v.CAJA', '=', $descuento->CAJA],
                    ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%'],
                ])
                ->get();

            

            /*  --------------------------------------------------------------------------------- */

            /*  *********** EMPEZAR A MODIFICAR LOS VALORES DEL ARRAY *********** */

            foreach ($ventas_con_descuentos as $ventas_con_descuento) {
                if ($ventas_con_descuento->ITEM < $descuento->ITEM) {
                    $key = array_search($ventas_con_descuento->LINEA, array_column($ventasdetAnterior, 'LINEA'));
                    $ventasdetAnterior[$key]->PRECIO = (int)$ventasdetAnterior[$key]->PRECIO - (((int)$ventas_con_descuento->PRECIO * (int)$descuento->PORCENTAJE)/100);
                }
            }

            /*  --------------------------------------------------------------------------------- */
        }

        
        /*  --------------------------------------------------------------------------------- */
        // BUSCAR STOCK GENERAL DE TODAS CATEGORIAS

        // $stockGeneral = DB::connection('retail')
        //     ->table('LOTES as l')
        //     ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'l.COD_PROD')
        //     ->select(DB::raw('SUM(l.CANTIDAD) AS CANTIDAD'),
        //     DB::raw('PRODUCTOS.MARCA'))
        //     ->where('l.ID_SUCURSAL', '=', $sucursal)
        //     ->groupBy('PRODUCTOS.MARCA')
        //     ->get();

        //return $stockGeneral;    
        foreach ($ventasdet as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            // $key2 = array_search($value->MARCA, array_column($ventasdet, 'MARCA'));
            // if ($key2 <> "null") {

            //     if (array_key_exists($key2, $ventasdet))   {
            //         $ventasdet[$key2]->STOCK_G += $value->CANTIDAD;   
            //     }
            // }

            $stockGeneral = DB::connection('retail')
            ->table('LOTES as l')
            ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'l.COD_PROD')
            ->select(DB::raw('SUM(l.CANTIDAD) AS CANTIDAD'),
            DB::raw('PRODUCTOS.LINEA'))
            ->where('PRODUCTOS.LINEA', '=', $value->LINEA)
            ->where('l.ID_SUCURSAL', '=', $sucursal)
            ->groupBy('PRODUCTOS.LINEA')
            ->get();

            $ventasdet[$key]->STOCK_G = $stockGeneral[0]->CANTIDAD;

            /*  --------------------------------------------------------------------------------- */
        }

        /*  --------------------------------------------------------------------------------- */

        // TOTALES

        // foreach ($ventasdetAnterior as $key => $value) {

        //     /*  --------------------------------------------------------------------------------- */

        //     // OBTENER LA UBICACION DE LA MARCA EN LAS VENTAS ANTERIORES 

        //     $key2 = array_search($value->LINEA, array_column($ventasdet, 'LINEA'));
            
        //     /*  --------------------------------------------------------------------------------- */

        //     // CARGAR PRECIOS ANTERIORES

        //     if ($key2 <> null) {
        //         $ventasdet[$key2]->PRECIO_ANTERIOR = $ventasdetAnterior[$key]->PRECIO;
        //         $ventasdet[$key2]->VENDIDO_ANTERIOR = $ventasdetAnterior[$key]->VENDIDO;
        //     } else if ($key2 === 0) {
        //         $ventasdet[$key2]->PRECIO_ANTERIOR = $ventasdetAnterior[$key]->PRECIO;
        //         $ventasdet[$key2]->VENDIDO_ANTERIOR = $ventasdetAnterior[$key]->VENDIDO; 
        //     } else {
        //          $ventasdet->append($ventasdet->PRECIO = 0);
        //         //                                             "COMPORTAMIENTO_PRECIO"=> 0, 
        //         //                                             "COMPORTAMIENTO_VENDIDO"=> 0, 
        //         //                                             "PRECIO_ANTERIOR"=> $ventasdetAnterior[$key]->PRECIO,
        //         //                                             "VENDIDO_ANTERIOR"=> $ventasdetAnterior[$key]->VENDIDO,
        //         //                                             "P_TOTAL"=> 0,
        //         //                                             "VENDIDO"=> 0,
        //         //                                             "P_VENDIDO"=> 0,
        //         //                                             "STOCK_G"=> 0,
        //         //                                             "P_STOCK"=> 0,
        //         //                                             "LINEA_NOMBRE"=> $ventasdetAnterior[$key]->LINEA_NOMBRE,
        //         //                                             "LINEA"=> $ventasdetAnterior[$key]->LINEA]);
        //         // $ventasdet->append("PRECIO"=> 0,
        //         //                                             "COMPORTAMIENTO_PRECIO"=> 0, 
        //         //                                             "COMPORTAMIENTO_VENDIDO"=> 0, 
        //         //                                             "PRECIO_ANTERIOR"=> $ventasdetAnterior[$key]->PRECIO,
        //         //                                             "VENDIDO_ANTERIOR"=> $ventasdetAnterior[$key]->VENDIDO,
        //         //                                             "P_TOTAL"=> 0,
        //         //                                             "VENDIDO"=> 0,
        //         //                                             "P_VENDIDO"=> 0,
        //         //                                             "STOCK_G"=> 0,
        //         //                                             "P_STOCK"=> 0,
        //         //                                             "LINEA_NOMBRE"=> $ventasdetAnterior[$key]->LINEA_NOMBRE,
        //         //                                             "LINEA"=> $ventasdetAnterior[$key]->LINEA);

        //     }

            
        // }

        // return $ventasdet;

        // foreach ($ventasdet as $key => $value) {

        //     /*  --------------------------------------------------------------------------------- */

        //     // CALCULAR COMPORTAMIENTOS 

        //     if ($ventasdet[$key]->PRECIO_ANTERIOR <> 0) {
        //         $ventasdet[$key]->COMPORTAMIENTO_PRECIO = number_format((($ventasdet[$key]->PRECIO / $ventasdet[$key]->PRECIO_ANTERIOR) - 1) * 100, 2);
        //     } else {
        //         $ventasdet[$key]->COMPORTAMIENTO_PRECIO = 100;
        //     }
            
        //     if ($ventasdet[$key]->VENDIDO_ANTERIOR <> 0) {

        //         $ventasdet[$key]->COMPORTAMIENTO_VENDIDO = number_format((($ventasdet[$key]->VENDIDO / $ventasdet[$key]->VENDIDO_ANTERIOR) - 1) * 100, 2);
        //     } else {
        //         $ventasdet[$key]->COMPORTAMIENTO_VENDIDO = 100;
        //     }
            
        //     /*  --------------------------------------------------------------------------------- */

        //     // CARGAR LOS TOTALES 

        //     $total += $value->PRECIO;
        //     $totalVendido += $value->VENDIDO;
        //     $totalStock += $value->STOCK_G;

        //     /*  --------------------------------------------------------------------------------- */
        // }

        foreach ($ventasdet as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            // OBTENER LA UBICACION DE LA MARCA EN LAS VENTAS ANTERIORES 

            $key2 = array_search($value->LINEA, array_column($ventasdetAnterior, 'LINEA'));
            
            /*  --------------------------------------------------------------------------------- */

            // CARGAR PRECIOS ANTERIORES

            if ($key2 <> null) {
                $ventasdet[$key]->PRECIO_ANTERIOR = $ventasdetAnterior[$key2]->PRECIO;
                $ventasdet[$key]->VENDIDO_ANTERIOR = $ventasdetAnterior[$key2]->VENDIDO;
            } else if ($key2 === 0) {
                $ventasdet[$key]->PRECIO_ANTERIOR = $ventasdetAnterior[$key2]->PRECIO;
                $ventasdet[$key]->VENDIDO_ANTERIOR = $ventasdetAnterior[$key2]->VENDIDO; 
            } 

            /*  --------------------------------------------------------------------------------- */

            // CALCULAR COMPORTAMIENTOS 

            if ($ventasdet[$key]->PRECIO_ANTERIOR <> 0) {
                $ventasdet[$key]->COMPORTAMIENTO_PRECIO = number_format((($ventasdet[$key]->PRECIO / $ventasdet[$key]->PRECIO_ANTERIOR) - 1) * 100, 2);
            } else {
                $ventasdet[$key]->COMPORTAMIENTO_PRECIO = 100;
            }
            
            if ($ventasdet[$key]->VENDIDO_ANTERIOR <> 0) {

                $ventasdet[$key]->COMPORTAMIENTO_VENDIDO = number_format((($ventasdet[$key]->VENDIDO / $ventasdet[$key]->VENDIDO_ANTERIOR) - 1) * 100, 2);
            } else {
                $ventasdet[$key]->COMPORTAMIENTO_VENDIDO = 100;
            }
            
            /*  --------------------------------------------------------------------------------- */

            // CARGAR LOS TOTALES 

            $total += $value->PRECIO;
            $totalVendido += $value->VENDIDO;
            $totalStock += $value->STOCK_G;

            /*  --------------------------------------------------------------------------------- */
        }

        /*  --------------------------------------------------------------------------------- */

        // CALCULAR LOS PORCENTAJES

        foreach ($ventasdet as $key => $value) {
            $ventasdet[$key]->P_TOTAL = round(($value->PRECIO * 100) / $total, 2);
            $ventasdet[$key]->P_VENDIDO = round(($value->VENDIDO * 100) / $totalVendido, 2);
            $ventasdet[$key]->P_STOCK = round(($value->STOCK_G * 100) / $totalStock, 2);
        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR TODOS LOS ARRAYS

        return ['categorias' => $ventasdet];

        /*  --------------------------------------------------------------------------------- */
    }

    public static function periodos_superados($request)
    {
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'C', 
                            1 => 'ID',
                            2 => 'CODIGO',
                            3 => 'FEC_VENTA',
                            5 => 'IMAGEN',
                            6 => 'ACCION',
                            7 => 'ESTATUS'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $dia = date('Y-m-d');
        $dias_filtro = date('Y-m-d', strtotime($dia. ' + 30 days'));

        $c = 0;

        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE PRODUCTOS VENCIDOS QUE PASAN EL TIEMPO DE VENCIMIENTO

        $totalData = $posts = ProductosAux::leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
                    ->leftJoin('LOTES', function($join){
                        $join->on('LOTES.COD_PROD', '=', 'PRODUCTOS_AUX.CODIGO')
                             ->on('LOTES.ID_SUCURSAL', '=', 'PRODUCTOS_AUX.ID_SUCURSAL');
                    })
                    ->where('PRODUCTOS_AUX.ID_SUCURSAL','=', $user->id_sucursal)
                    ->where('LOTES.CANTIDAD','>', 0)
                    ->where('PRODUCTOS.PERIODO','>', 0)
                    ->where('PRODUCTOS_AUX.FECHULT_V','<=', '(DATE_ADD('.$dia.', interval -(PRODUCTOS.PERIODO) month))')
                    ->count();  
        
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $imagen_producto = '';

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE VALOR EN VARIABLE SEARCH

        if(empty($request->input('search.value')))
        {            

            /*  ************************************************************ */

            //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

            $posts = ProductosAux::select(DB::raw('0 AS C, PRODUCTOS_AUX.CODIGO, PRODUCTOS.DESCRIPCION, PRODUCTOS_AUX.FECHULT_V, DATE_ADD(PRODUCTOS_AUX.FECHULT_V, interval PRODUCTOS.PERIODO month) AS LIMITE'))
                    ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
                    ->leftJoin('LOTES', function($join){
                        $join->on('LOTES.COD_PROD', '=', 'PRODUCTOS_AUX.CODIGO')
                             ->on('LOTES.ID_SUCURSAL', '=', 'PRODUCTOS_AUX.ID_SUCURSAL');
                    })
                    ->where('PRODUCTOS_AUX.ID_SUCURSAL','=', $user->id_sucursal)
                    ->where('LOTES.CANTIDAD','>', 0)
                    ->where('PRODUCTOS.PERIODO','>', 0)
                    ->where('PRODUCTOS_AUX.FECHULT_V','<=', '(DATE_ADD('.$dia.', interval -(PRODUCTOS.PERIODO) month))')
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

            $posts =  ProductosAux::select(DB::raw('0 AS C, PRODUCTOS_AUX.CODIGO, PRODUCTOS.DESCRIPCION, PRODUCTOS_AUX.FECHULT_V, interval PRODUCTOS.PERIODO month) AS LIMITE'))
                    ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
                    ->leftJoin('LOTES', function($join){
                        $join->on('LOTES.COD_PROD', '=', 'PRODUCTOS_AUX.CODIGO')
                             ->on('LOTES.ID_SUCURSAL', '=', 'PRODUCTOS_AUX.ID_SUCURSAL');
                    })
                    ->where('PRODUCTOS_AUX.ID_SUCURSAL','=', $user->id_sucursal)
                    ->where('LOTES.CANTIDAD','>', 0)
                    ->where('PRODUCTOS.PERIODO','>', 0)
                    ->where('PRODUCTOS_AUX.FECHULT_V','<=', '(DATE_ADD('.$dia.', interval -(PRODUCTOS.PERIODO) month))')
                            ->where(function ($query) use ($search) {
                                $query->where('PRODUCTOS_AUX.CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = ProductosAux::leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
                    ->leftJoin('LOTES', function($join){
                        $join->on('LOTES.COD_PROD', '=', 'PRODUCTOS_AUX.CODIGO')
                             ->on('LOTES.ID_SUCURSAL', '=', 'PRODUCTOS_AUX.ID_SUCURSAL');
                    })
                    ->where('PRODUCTOS_AUX.ID_SUCURSAL','=', $user->id_sucursal)
                    ->where('LOTES.CANTIDAD','>', 0)
                    ->where('PRODUCTOS.PERIODO','>', 0)
                    ->where('PRODUCTOS_AUX.FECHULT_V','<=', '(DATE_ADD('.$dia.', interval -(PRODUCTOS.PERIODO) month))')
                            ->where(function ($query) use ($search) {
                                $query->where('PRODUCTOS_AUX.CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('PRODUCTOS.DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                             ->count();

            /*  ************************************************************ */  

        }

        $data = array();

        /*  --------------------------------------------------------------------------------- */

        // CONVERT IMAGE DEFAULT TO BLOB 

        $path = '../storage/app/imagenes/product.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $dataDefaultImage = file_get_contents($path);

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts))
        {
            foreach ($posts as $post)
            {

                /*  --------------------------------------------------------------------------------- */

                // BUSCAR IMAGEN

                $imagen = Imagen::select(DB::raw('PICTURE'))
                ->where('COD_PROD','=', $post->CODIGO)
                ->get();
                
                /*  --------------------------------------------------------------------------------- */

                // CARGAR EN LA VARIABLE 

                $c = $c + 1;

                $nestedData['C'] = $c;
                $nestedData['CODIGO'] = $post->COD_PROD;
                $nestedData['DESCRIPCION'] = substr($post->DESCRIPCION, 0, 20).'...';
                $nestedData['FEC_VENTA'] = substr($post->FECHULT_V, 0, 11);

                $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarDetalle' title='Mostrar'><i class='fa fa-list text-white'  aria-hidden='true'></i></a>";

                /*  --------------------------------------------------------------------------------- */

                // SI NO HAY IMAGEN CARGAR IMAGEN DEFAULT 

                if (count($imagen) > 0) {
                   foreach ($imagen as $key => $image) {
                        $imagen_producto = $image->PICTURE;
                    }
                } else {
                    $imagen_producto = $dataDefaultImage;
                }

                /*  --------------------------------------------------------------------------------- */

                $nestedData['IMAGEN'] = "<img src='data:image/jpg;base64,".base64_encode($imagen_producto)."' class='img-thumbnail' style='width:60px;height:60px;'>";

                /*  --------------------------------------------------------------------------------- */

                
                $data[] = $nestedData;

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
    }


    public static function guardar($data)
    {

        try {

            /*  --------------------------------------------------------------------------------- */

            // INICIAR TRANSACCION 

            DB::connection('retail')->beginTransaction();

            /*  --------------------------------------------------------------------------------- */

            // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

            $user = auth()->user();
            
            /*  --------------------------------------------------------------------------------- */

            // OBTENER CANDEC 

            $moneda = $data["data"]["moneda"];
            $candec = (Parametro::candec($moneda))["CANDEC"];

            /*  --------------------------------------------------------------------------------- */

            // INICIAR VARIABLES

            $guaranies = Common::quitar_coma($data["data"]["pago"]["GUARANIES"], 2);
            if ($guaranies === '') {
                $guaranies = 0;
            } 

            $dolares = Common::quitar_coma($data["data"]["pago"]["DOLARES"], 2);
            if ($dolares === '') {
                $dolares = 0;
            } 

            $pesos = Common::quitar_coma($data["data"]["pago"]["PESOS"], 2);
            if ($pesos === '') {
                $pesos = 0;
            } 

            $reales = Common::quitar_coma($data["data"]["pago"]["REALES"], 2);
            if ($reales === '') {
                $reales = 0;
            }

            /*  --------------------------------------------------------------------------------- */

            // REVISAR DINERO Y QUITARLE VUELTO 

            $opcion_vuelto = $data["data"]["pago"]["OPCION_VUELTO"];

            if ($opcion_vuelto === '1') {
                $guaranies = $guaranies - Common::quitar_coma($data["data"]["pago"]["VUELTO"]["guaranies"] , 2);
            } else if ($opcion_vuelto === '2') {
                $dolares = $dolares - Common::quitar_coma($data["data"]["pago"]["VUELTO"]["dolares"] , 2);
            } else if ($opcion_vuelto === '3') {
                $reales = $reales - Common::quitar_coma($data["data"]["pago"]["VUELTO"]["reales"] , 2);
            } else if ($opcion_vuelto === '4') {
                $pesos = $pesos - Common::quitar_coma($data["data"]["pago"]["VUELTO"]["pesos"] , 2);
            } 

            /*  --------------------------------------------------------------------------------- */

            // VUELTO PRINCIPAL 

            if($moneda === 1) {
                $vuelto = Common::quitar_coma($data["data"]["pago"]["VUELTO"]["guaranies"] , $candec);
            } else if ($moneda === 2) {
                $vuelto = Common::quitar_coma($data["data"]["pago"]["VUELTO"]["dolares"] , $candec);
            } else if ($moneda === 3) {
                $vuelto = Common::quitar_coma($data["data"]["pago"]["VUELTO"]["reales"] , $candec);
            } else if ($moneda === 4) {
                $vuelto = Common::quitar_coma($data["data"]["pago"]["VUELTO"]["pesos"] , $candec);
            }

            /*  --------------------------------------------------------------------------------- */

            $numero_caja = 'CA'.'01';
            $codigo_caja = $data["data"]["cabecera"]["CODIGO_CAJA"];
            $saldo = Common::quitar_coma($data["data"]["pago"]["SALDO"], $candec);
            $efectivo = Common::quitar_coma($data["data"]["pago"]["EFECTIVO"], $candec);

            /*  --------------------------------------------------------------------------------- */

            // Tarjeta
            
            $tarjeta = Common::quitar_coma($data["data"]["pago"]["TARJETA"], $candec);
            //$codigo_tarjeta = Common::quitar_coma($data["data"]["pago"]["CODIGO_TARJETA"], $candec);
            $codigo_tarjeta = $data["data"]["pago"]["CODIGO_TARJETA"];
             
            /*  --------------------------------------------------------------------------------- */

            // TRANSFERENCIA 

            $transferencia = Common::quitar_coma($data["data"]["pago"]["TRANSFERENCIA"], 0);
            $codigo_banco = $data["data"]["pago"]["CODIGO_BANCO"];

            /*  --------------------------------------------------------------------------------- */

            // GIRO 

            $giro = Common::quitar_coma($data["data"]["pago"]["GIRO"], 0);
            $codigo_entidad = $data["data"]["pago"]["CODIGO_ENT"];

            /*  --------------------------------------------------------------------------------- */

            $caja = $data["data"]["caja"]["CODIGO"];
            $cliente = $data["data"]["cliente"]["CODIGO"];
            $vendedor = $data["data"]["vendedor"]["CODIGO"];
            $cheques = $data["data"]["pago"]["CHEQUE"];
            $opcion_impresion = $data["data"]["pago"]["TIPO_IMPRESION"];
            $total = 0;
            $dia = date('Y-m-d');
            $hora = date("H:i:s");
            $codigo = 0;
            $c = 0;
            $total = 0;
            $impuesto = 0;
            $total_total = 0;
            $total_iva = 0;
            $total_base5 = 0;
            $total_base10 = 0;
            $total_gravadas = 0;
            $total_exentas = 0;
            $descuento = 0;
            $descuento_total = 0;

            /*  --------------------------------------------------------------------------------- */

            // PRODUCTOS 

            $filas = count($data["data"]["productos"]);

            /*  --------------------------------------------------------------------------------- */

            // TOTALES 

            $gravadas = Common::quitar_coma($data["data"]["cabecera"]["GRAVADAS"], $candec);
            $impuesto_cabecera = Common::quitar_coma($data["data"]["cabecera"]["IMPUESTO"], $candec);
            $total_cabecera = Common::quitar_coma($data["data"]["cabecera"]["TOTAL"], $candec);

            /*  --------------------------------------------------------------------------------- */

            // REVISAR SI YA EXISTE VENTA 

            $codigo = Venta::existe_codigo($data["data"]["cabecera"]["CODIGO"], $data["data"]["caja"]["CODIGO"]);

            /*  --------------------------------------------------------------------------------- */

            // RECORRER VENTAS

            while($c < $filas) {

                /*  --------------------------------------------------------------------------------- */

                // INICIAR DATOS 

                $cod_prod = $data["data"]["productos"][$c]["CODIGO"];
                $cantidad = Common::quitar_coma($data["data"]["productos"][$c]["CANTIDAD"], $candec);
                $cantidad_total = $cantidad;
                $precio = Common::quitar_coma($data["data"]["productos"][$c]["PRECIO"], $candec);
                $impuesto = Common::quitar_coma($data["data"]["productos"][$c]["IMPUESTO"], $candec);
                $porcentaje = Common::quitar_coma($data["data"]["productos"][$c]["IVA"], $candec);
                $descuento = Common::quitar_coma($data["data"]["productos"][$c]["DESCUENTO_TOTAL"], $candec);
                $descuento_porcentaje = (int)$data["data"]["productos"][$c]["DESCUENTO"];

                /*  --------------------------------------------------------------------------------- */

                // REALIZAR CALCULOS 
                
                $total = Common::quitar_coma($data["data"]["productos"][$c]["PRECIO_TOTAL"], $candec);

                /*  --------------------------------------------------------------------------------- */

                // OBTENER DATOS FALTANTES 

                $producto = Producto::datosVariosProducto($cod_prod);

                /*  --------------------------------------------------------------------------------- */ 

                // SI TIPO ES PRODUCTO

                if ($data["data"]["productos"][$c]["TIPO"] === 1) {

                    /*  --------------------------------------------------------------------------------- */ 

                    // RESTAR STOCK DEL PRODUCTO

                    $respuesta_resta = Stock::restar_stock_producto($cod_prod, $cantidad);
                            
                    /*  --------------------------------------------------------------------------------- */ 

                    // SI LA RESPUESTA TIENE DATOS GUARDA EL REGISTRO 
                    

                    if ($respuesta_resta["datos"]) {

                        /*  --------------------------------------------------------------------------------- */

                        // SUMAR LA CANTIDAD DEVUELTA POR EL ARRAY 

                        $cantidad_guardada = 0;

                        foreach ($respuesta_resta["datos"] as $key => $value) {
                            $cantidad_guardada = $cantidad_guardada + $value["cantidad"];
                        }

                        /*  --------------------------------------------------------------------------------- */

                        // SI LA CANTIDAD GUARDADA DIFIERE CON LA CANTIDAD A ENVIAR SE RECALCULA EL TOTAL

                        if ($cantidad !== $cantidad_guardada) {
                            $total = $precio * $cantidad_guardada;
                        }

                        /*  --------------------------------------------------------------------------------- */

                        // CALCULAR IVA
                        
                        $impuesto = Common::calculo_iva((int)$porcentaje, $total, $candec);
                                
                        /*  --------------------------------------------------------------------------------- */

                        // TOTALES 

                        $total_total = $total_total + $total;
                        
                        $total_iva = $total_iva + $impuesto["impuesto"];
                        $total_base10 = $total_base10 + $impuesto["base10"];
                        $total_base5 = $total_base5 + $impuesto["base5"];
                        $total_exentas = $total_exentas + $impuesto["exentas"];
                        $total_gravadas = $total_gravadas + $impuesto["gravadas"];
                        $descuento_total = $descuento_total + $descuento;
                        
                        /*  --------------------------------------------------------------------------------- */

                        // INSERTAR TRANSFERENCIA DET 

                        $id_ventas_det = Ventas_det::insertGetId(
                            [
                            'CODIGO' => $codigo, 
                            'CAJA' => $caja, 
                            'ITEM' => $c + 1, 
                            'COD_PROD' => $cod_prod, 
                            'DESCRIPCION' => $data["data"]["productos"][$c]["DESCRIPCION"], 
                            'LOTE' => $data["data"]["productos"][$c]["LOTE"], 
                            'CANTIDAD' => $cantidad_guardada, 
                            'GRAVADA' => $impuesto["gravadas"],
                            'IVA' => $impuesto["impuesto"],
                            'EXENTA' => $impuesto["exentas"],
                            'DESCUENTO' => '', 
                            'PRECIO' => $total, 
                            'PRECIO_UNIT' => $precio, 
                            'BASE5' => $impuesto["base5"],
                            'BASE10' =>  $impuesto["base10"],
                            'TIVA' => '', 
                            'USER' => $user->name, 
                            'FECALTAS' => $dia, 
                            'HORALTAS' => $hora, 
                            'ID_SUCURSAL' => $user->id_sucursal, 
                            'CODIGO_CA' => $codigo_caja, 
                            'ANULADO' => 0
                            ]
                        );

                        /*  --------------------------------------------------------------------------------- */

                        // INSERTAR DESCUENTO 

                        if ($descuento_porcentaje > 0) {
                            Ventas_det_Descuento::guardar_referencia($descuento_porcentaje, $descuento, $id_ventas_det, $moneda, $cod_prod);
                        }

                        /*  --------------------------------------------------------------------------------- */

                    }

                    /*  --------------------------------------------------------------------------------- */

                    // AQUI RECORRO EL ARRAY MANDANDO LOS ID LOTE Y LA TRANSFERENCIA EN LA TABLA DE CLAVES FORANEAS

                    foreach ($respuesta_resta["datos"] as $key => $value) {
                        VentasDetTieneLotes::guardar_referencia($id_ventas_det, $value["id"], $value["cantidad"]);
                    }
                            
                    /*  --------------------------------------------------------------------------------- */

                    // CARGAR LOS PRODUCTOS CON LAS CANTIDADES QUE NO SE GUARDARON 

                    if ($respuesta_resta["response"] === false){
                        $sin_stock[] = (array)['cod_prod' => $cod_prod, 'guardado' => (float)$cantidad - (float)$respuesta_resta["restante"], "restante" => $respuesta_resta["restante"], "cantidad" => $cantidad];
                    }

                /*  --------------------------------------------------------------------------------- */

                // SI TIPO ES SERVICIO 

                } else if ($data["data"]["productos"][$c]["TIPO"] === 2) {

                    /*  --------------------------------------------------------------------------------- */

                    // CALCULAR IVA
                        
                    $impuesto = Common::calculo_iva((int)$porcentaje, $total, $candec);
                            
                    /*  --------------------------------------------------------------------------------- */

                    // TOTALES 

                    $total_total = $total_total + $total;
                        
                    $total_iva = $total_iva + $impuesto["impuesto"];
                    $total_base10 = $total_base10 + $impuesto["base10"];
                    $total_base5 = $total_base5 + $impuesto["base5"];
                    $total_exentas = $total_exentas + $impuesto["exentas"];
                    $total_gravadas = $total_gravadas + $impuesto["gravadas"];
                    $descuento_total = $descuento_total + $descuento;
                        
                    /*  --------------------------------------------------------------------------------- */

                    // INSERTAR TRANSFERENCIA DET 

                    VentasDetServicios::insert(
                            [
                            'CODIGO' => $codigo, 
                            'CAJA' => $caja, 
                            'ITEM' => $c + 1, 
                            'COD_SERV' => $cod_prod, 
                            'DESCRIPCION' => $data["data"]["productos"][$c]["DESCRIPCION"], 
                            'CANTIDAD' => $cantidad, 
                            'GRAVADA' => $impuesto["gravadas"],
                            'IVA' => $impuesto["impuesto"],
                            'EXENTA' => $impuesto["exentas"],
                            'DESCUENTO' => '', 
                            'PRECIO' => $total, 
                            'PRECIO_UNIT' => $precio, 
                            'BASE5' => $impuesto["base5"],
                            'BASE10' =>  $impuesto["base10"],
                            'TIVA' => '', 
                            'ID_SUCURSAL' => $user->id_sucursal, 
                            'CODIGO_CA' => $codigo_caja, 
                            'ANULADO' => 0
                            ]
                    );

                    /*  --------------------------------------------------------------------------------- */

                }

                /*  --------------------------------------------------------------------------------- */
                        
                // AUMENTAR CONTADOR 

                $c++;
                        
                /*  --------------------------------------------------------------------------------- */
            }

            /*  --------------------------------------------------------------------------------- */

            // INSERTAR VENTA 

            $venta = Venta::insertGetId(
                [
                    "CODIGO" => $codigo, 
                    "FECHA" => $dia, 
                    "HORA" => $hora, 
                    //"FACTURA" => '', 
                    "CAJA" => $caja, 
                    "CLIENTE" => $cliente, 
                    "VENDEDOR" => $vendedor, 
                    "TIPO" => 'CO', 
                    //"FORMA_PAGO" => '', 
                    "PLAN_PAGO" => 0, 
                    "TARJETA" => 0, 
                    "DESCUENTO" => $descuento_total, 
                    "GRAVADAS" => $total_gravadas, 
                    "IMPUESTOS" => $total_iva, 
                    "EXENTAS" => $total_exentas, 
                    "BASE5" => $total_base5, 
                    "BASE10" => $total_base10, 
                    "SUB_TOTAL" => ($total_gravadas + $total_exentas), 
                    "TOTAL" => $total_total, 
                    "EFECTIVO" => $efectivo, 
                    "CHEQUE" => 0, 
                    "VALE" => 0, 
                    "DONACION" => 0, 
                    "VUELTO" => $vuelto, 
                    "GIROS" => 0, 
                    "MONEDA" => $moneda, 
                    "MONEDA1" => $dolares, 
                    "MONEDA2" => $reales, 
                    "MONEDA3" => $guaranies, 
                    "MONEDA4" => $pesos, 
                    "OPCION_IMPRESION" => $opcion_impresion, 
                    "USER" => $user->name, 
                    "FECALTAS" => $dia, 
                    "HORALTAS" => $hora, 
                    "ID_SUCURSAL" => $user->id_sucursal, 
                    "CODIGO_CA" => $codigo_caja, 
                    //"TIPO_PRECIO" =>
                ]
            );
            
            /*  --------------------------------------------------------------------------------- */

            // INSERTAR ANULADO 

            VentasAnulado::guardar_referencia([
                    'FK_VENTA' => $venta,
                    'ANULADO' => 0,
                    'FECHA' => date('Y-m-d H:i:s')
            ]);

            /*  --------------------------------------------------------------------------------- */

            // INSERTAR PAGO TARJETA

            if ($codigo_tarjeta !== '0' && $codigo_tarjeta !== '0.00' && $codigo_tarjeta !== NULL) {
                if ($codigo_tarjeta !== '') {
                    $pago_tarjeta = VentaTarjeta::guardar_referencia([
                        'FK_TARJETA' => $codigo_tarjeta,
                        'FK_VENTA' => $venta,
                        'MONTO' => $tarjeta,
                        'MONEDA' => 1
                    ]);
                } 
            }

            /*  --------------------------------------------------------------------------------- */

            // INSERTAR PAGO TRANSFERENCIA
            
            if ($codigo_banco !== '0' && $codigo_banco !== '0.00' && $codigo_banco !== NULL) {
                if ($codigo_banco !== '') {
                    VentaTransferencia::guardar_referencia([
                        'FK_BANCO' => $codigo_banco,
                        'FK_VENTA' => $venta,
                        'MONTO' => $transferencia,
                        'MONEDA' => 1
                    ]);
                } 
            }

            /*  --------------------------------------------------------------------------------- */

            // INSERTAR PAGO GIRO
            
            if ($codigo_entidad !== '0' && $codigo_entidad !== '0.00' && $codigo_entidad !== NULL) {
                if ($codigo_entidad !== '') {
                    VentaGiro::guardar_referencia([
                        'FK_ENTIDAD' => $codigo_entidad,
                        'FK_VENTA' => $venta,
                        'MONTO' => $giro,
                        'MONEDA' => 1
                    ]);
                } 
            }

            /*  --------------------------------------------------------------------------------- */

            // INSERTAR PAGO CHEQUES 

            $pago_cheque = VentaCheque::guardar_referencia($cheques, $venta);

            if ($pago_cheque["response"] === false) {
                return $pago_cheque;
            }

            /*  --------------------------------------------------------------------------------- */

            // ACTUALIZAR NUMERO TICKET 

            Ticket::actualizar_numero($venta, $codigo_caja, $numero_caja, $dia, $hora);

            /*  --------------------------------------------------------------------------------- */

            // ENVIAR TRANSACCION A BD

            DB::connection('retail')->commit();

            /*  --------------------------------------------------------------------------------- */

            // RETORNAR VALOR 

            return ["response" => true, "statusText" => "Se ha guardado correctamente la venta !", "CODIGO" => $codigo, "CAJA" => $caja];

            /*  --------------------------------------------------------------------------------- */

        } catch (Exception $e) {
        
           /*  --------------------------------------------------------------------------------- */

           // NO GUARDAR NINGUN CAMBIO 

           DB::connection('retail')->rollBack();
           throw $e;
           
           /*  --------------------------------------------------------------------------------- */

        }

    }

    public static function existe_codigo($codigo, $caja){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        $existe = 1;

        while ($existe > 0) {

            /*  --------------------------------------------------------------------------------- */

            $existe = Venta::where('CODIGO', '=', $codigo)
            ->where('CAJA', '=', $caja)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->count();

            /*  --------------------------------------------------------------------------------- */

            // SI EXISTE EL CODIGO SUMAS + 1 Y VOLVER A RECORRER 
            
            if($existe > 0) {
                $codigo = $codigo + 1;
            }

            /*  --------------------------------------------------------------------------------- */

        }

        return $codigo;

        /*  --------------------------------------------------------------------------------- */

    }

    public static function numeracion() {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
        $dia = date('Y-m-d');
        $hora = date("H:i:s");
        $numero_caja = 1;
        $caja = 'CA'.'01';

        /*  --------------------------------------------------------------------------------- */

        // NUMERO TICKET 

        $ticket = Ticket::numero_caja($caja, $dia, $hora);
        
        /*  --------------------------------------------------------------------------------- */

        // VER SI EXISTE NUMERO DE VENTA 

        $codigo = Venta::where('CODIGO','=', 1)
        ->where('ID_SUCURSAL','=', $user->id_sucursal)
        ->count();
        
        /*  --------------------------------------------------------------------------------- */

        if ($codigo === 0) {
            $codigo = 1;
        } else {
            $codigo = $ticket[0]["NUMERO"];
        }

        /*  --------------------------------------------------------------------------------- */ 

        $ticket = $ticket[0]["CAJA"];

        /*  --------------------------------------------------------------------------------- */ 

        $numero = Venta::select(DB::raw('(CODIGO) AS CODIGO'))
        ->whereRaw('LENGTH(CODIGO) < 8')
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->where('CAJA', '=', $numero_caja)
        ->orderBy('CODIGO', 'desc')
        ->limit(1)
        ->get();

        /*  --------------------------------------------------------------------------------- */ 

        // RETORNAR VALOR 

        return ['CODIGO' => $numero[0]['CODIGO'], 'CODIGO_CAJA' => $ticket];

        /*  --------------------------------------------------------------------------------- */ 

    }


    public static function inicio(){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER CLIENTE 

        $cliente = Cliente::select(DB::raw('CODIGO, NOMBRE'))
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->orderBy('CODIGO', 'asc')
        ->limit(1)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        $empleado = Empleado::select(DB::raw('CODIGO, NOMBRE'))
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->orderBy('CODIGO', 'asc')
        ->limit(1)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER PARAMETRO

        $parametro = Parametro::select(DB::raw('MONEDA, DESTINO'))
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // CANDEC

        $candec = Parametro::candec($parametro[0]['MONEDA']);
        $candec["CODIGO"] = $parametro[0]['MONEDA'];

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return ['CLIENTE' => $cliente[0], 'EMPLEADO' => $empleado[0], 'MONEDA' => $candec, 'LIMITE_MAYORISTA' => $parametro[0]['DESTINO'], 'IMPRESORA_TICKET' => 'EPSON TM-U220 Receipt', 'IMPRESORA_MATRICIAL' => 'EPSON LX-350 ESC/P (Copy 3)'];

        /*  --------------------------------------------------------------------------------- */

    }

    public static function mostrar_cabecera($data)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
         
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $codigo = $data['codigo'];
        $caja = $data['caja'];

        /*  --------------------------------------------------------------------------------- */

        $venta = Venta::select(DB::raw(
                        'VENTAS.CODIGO,
                        VENTAS.FECALTAS,  
                        CLIENTES.NOMBRE AS CLIENTE,
                        CLIENTES.DIRECCION,
                        CLIENTES.RUC,
                        CLIENTES.CI,
                        VENTAS.TIPO,
                        VENTAS.HORALTAS,
                        VENTAS.CAJA,
                        EMPLEADOS.NOMBRE AS VENDEDOR,
                        VENTAS.USER AS CAJERO,
                        VENTAS.EFECTIVO AS PAGO,
                        VENTAS.VUELTO,
                        VENTAS.DESCUENTO,
                        VENTAS.CODIGO_CA,
                        VENTAS.TIPO,
                        VENTAS.MONEDA'
                    ))
        ->leftJoin('CLIENTES', function($join){
                                $join->on('CLIENTES.CODIGO', '=', 'VENTAS.CLIENTE')
                                     ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
        })
        ->leftJoin('EMPLEADOS', function($join){
                                $join->on('EMPLEADOS.CODIGO', '=', 'VENTAS.VENDEDOR')
                                     ->on('EMPLEADOS.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
        })
        ->where('VENTAS.ID_SUCURSAL','=', $user->id_sucursal)
        ->where('VENTAS.CODIGO','=', $codigo)
        ->where('VENTAS.CAJA','=', $caja)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // TIPO FACTURA 

        if ($venta[0]->TIPO === 'CO') {
            $venta[0]->TIPO = 'CONTADO';
        } else if ($venta[0]->TIPO === 'CR') {
            $venta[0]->TIPO = 'CRÉDITO';
        }

        /*  --------------------------------------------------------------------------------- */

        // RUC / CI

        if ($venta[0]->RUC === '' || $venta[0]->RUC === null) {
            $venta[0]->RUC = $venta[0]->CI;
        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VENTA 

        return $venta[0];

        /*  --------------------------------------------------------------------------------- */

    }

    public static function mostrar_cuerpo($data)
    {
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
         
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $codigo = $data['codigo'];
        $caja = $data['caja'];
        $data = array();

        /*  --------------------------------------------------------------------------------- */

        $ventas_det = Ventas_det::select(DB::raw(
                        'VENTASDET.ID,
                        VENTASDET.ITEM, 
                        VENTASDET.COD_PROD, 
                        VENTASDET.DESCRIPCION, 
                        VENTASDET.CANTIDAD, 
                        VENTASDET.PRECIO_UNIT,
                        VENTASDET.IVA,
                        VENTASDET.EXENTA AS EXENTAS,
                        VENTASDET.BASE5,
                        VENTASDET.BASE10,
                        VENTASDET.PRECIO,
                        VENTAS.MONEDA,
                        0 AS IVA_PORCENTAJE'
                    ))
        ->leftJoin('VENTAS', function($join){
                                $join->on('VENTAS.CODIGO', '=', 'VENTASDET.CODIGO')
                                     ->on('VENTAS.ID_SUCURSAL', '=', 'VENTASDET.ID_SUCURSAL')
                                     ->on('VENTAS.CAJA', '=', 'VENTASDET.CAJA');
                            })
        ->where('VENTASDET.ID_SUCURSAL','=', $user->id_sucursal)
        ->where('VENTASDET.CODIGO','=', $codigo)
        ->where('VENTASDET.CAJA','=', $caja)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        foreach ($ventas_det as $key => $value) {
            
            /*  --------------------------------------------------------------------------------- */

            // BUSCAR IVA PRODUCTO

            $producto = DB::connection('retail')
            ->table('PRODUCTOS')
            ->select(DB::raw('IMPUESTO'))
            ->where('CODIGO', '=', $value->COD_PROD)
            ->get();

            /*  --------------------------------------------------------------------------------- */

            $nestedData['ID'] = $value->ID;
            $nestedData['ITEM'] = $value->ITEM;
            $nestedData['COD_PROD'] = $value->COD_PROD;
            $nestedData['DESCRIPCION'] = $value->DESCRIPCION;
            $nestedData['CANTIDAD'] = Common::precio_candec_sin_letra($value->CANTIDAD, 1);
            $nestedData['PRECIO_UNIT'] = Common::precio_candec_sin_letra($value->PRECIO_UNIT, $value->MONEDA);
            $nestedData['IVA'] = Common::precio_candec_sin_letra($value->IVA, $value->MONEDA);
            $nestedData['EXENTAS'] = $value->EXENTAS;
            $nestedData['BASE5'] = $value->BASE5;
            $nestedData['BASE10'] = $value->BASE10;
            $nestedData['PRECIO'] = Common::precio_candec_sin_letra($value->PRECIO, $value->MONEDA);
            $nestedData['MONEDA'] = $value->MONEDA;
            $nestedData['IVA_PORCENTAJE'] = $producto[0]->IMPUESTO;

            /*  --------------------------------------------------------------------------------- */

            // CARGAR DATOS EN ARRAY

            $data[] = $nestedData;

            /*  --------------------------------------------------------------------------------- */

        }

        /*  --------------------------------------------------------------------------------- */

        // OBTENER SERVICIOS 

        $ventas_det_servicios = VentasDetServicios::select(DB::raw(
                        'ventasdet_servicios.ID,
                        ventasdet_servicios.ITEM, 
                        ventasdet_servicios.COD_SERV, 
                        ventasdet_servicios.DESCRIPCION, 
                        ventasdet_servicios.CANTIDAD, 
                        ventasdet_servicios.PRECIO_UNIT,
                        ventasdet_servicios.IVA,
                        ventasdet_servicios.EXENTA AS EXENTAS,
                        ventasdet_servicios.BASE5,
                        ventasdet_servicios.BASE10,
                        ventasdet_servicios.PRECIO,
                        VENTAS.MONEDA,
                        0 AS IVA_PORCENTAJE'
                    ))
        ->leftJoin('VENTAS', function($join){
                                $join->on('VENTAS.CODIGO', '=', 'ventasdet_servicios.CODIGO')
                                     ->on('VENTAS.ID_SUCURSAL', '=', 'ventasdet_servicios.ID_SUCURSAL')
                                     ->on('VENTAS.CAJA', '=', 'ventasdet_servicios.CAJA');
                            })
        ->where('ventasdet_servicios.ID_SUCURSAL','=', $user->id_sucursal)
        ->where('ventasdet_servicios.CODIGO','=', $codigo)
        ->where('ventasdet_servicios.CAJA','=', $caja)
        ->get();

        foreach ($ventas_det_servicios as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            $nestedData['ID'] = $value->ID;
            $nestedData['ITEM'] = $value->ITEM;
            $nestedData['COD_PROD'] = $value->COD_SERV;
            $nestedData['DESCRIPCION'] = $value->DESCRIPCION;
            $nestedData['CANTIDAD'] = Common::precio_candec_sin_letra($value->CANTIDAD, 1);
            $nestedData['PRECIO_UNIT'] = Common::precio_candec_sin_letra($value->PRECIO_UNIT, $value->MONEDA);
            $nestedData['IVA'] = Common::precio_candec_sin_letra($value->IVA, $value->MONEDA);
            $nestedData['EXENTAS'] = $value->EXENTAS;
            $nestedData['BASE5'] = $value->BASE5;
            $nestedData['BASE10'] = $value->BASE10;
            $nestedData['PRECIO'] = Common::precio_candec_sin_letra($value->PRECIO, $value->MONEDA);
            $nestedData['MONEDA'] = $value->MONEDA;
            $nestedData['IVA_PORCENTAJE'] = 10;

            /*  --------------------------------------------------------------------------------- */

            // CARGAR SERVICIOS EN ARRAY 

            $data[] = $nestedData;

            /*  --------------------------------------------------------------------------------- */

        }
        
        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return $data;

        /*  --------------------------------------------------------------------------------- */

    }

    public static function ticket_pdf_ejemplo($dato) {

        /*  --------------------------------------------------------------------------------- */
        
        define('EURO',chr(128));
        $pdf = new FPDF('P','mm',array(80,150));
        $pdf->AddPage();
         
        // CABECERA
        $pdf->SetFont('Helvetica','',12);
        $pdf->Cell(60,4,'Lacodigoteca.com',0,1,'C');
        $pdf->SetFont('Helvetica','',8);
        $pdf->Cell(60,4,'C.I.F.: 01234567A',0,1,'C');
        $pdf->Cell(60,4,'C/ Arturo Soria, 1',0,1,'C');
        $pdf->Cell(60,4,'C.P.: 28028 Madrid (Madrid)',0,1,'C');
        $pdf->Cell(60,4,'999 888 777',0,1,'C');
        $pdf->Cell(60,4,'alfredo@lacodigoteca.com',0,1,'C');
         
        // DATOS FACTURA        
        $pdf->Ln(5);
        $pdf->Cell(60,4,'Factura Simpl.: F2019-000001',0,1,'');
        $pdf->Cell(60,4,'Fecha: 28/10/2019',0,1,'');
        $pdf->Cell(60,4,'Metodo de pago: Tarjeta',0,1,'');
         
        // COLUMNAS
        $pdf->SetFont('Helvetica', 'B', 7);
        $pdf->Cell(30, 10, 'Articulo', 0);
        $pdf->Cell(5, 10, 'Ud',0,0,'R');
        $pdf->Cell(10, 10, 'Precio',0,0,'R');
        $pdf->Cell(15, 10, 'Total',0,0,'R');
        $pdf->Ln(8);
        $pdf->Cell(60,0,'','T');
        $pdf->Ln(0);
         
        // PRODUCTOS
        $pdf->SetFont('Helvetica', '', 7);
        $pdf->MultiCell(30,4,'Manzana golden 1Kg',0,'L'); 
        $pdf->Cell(35, -5, '2',0,0,'R');
        $pdf->Cell(10, -5, number_format(round(3,2), 2, ',', ' ').EURO,0,0,'R');
        $pdf->Cell(15, -5, number_format(round(2*3,2), 2, ',', ' ').EURO,0,0,'R');
        $pdf->Ln(3);
        $pdf->MultiCell(30,4,'Malla naranjas 3Kg',0,'L'); 
        $pdf->Cell(35, -5, '1',0,0,'R');
        $pdf->Cell(10, -5, number_format(round(1.25,2), 2, ',', ' ').EURO,0,0,'R');
        $pdf->Cell(15, -5, number_format(round(1.25,2), 2, ',', ' ').EURO,0,0,'R');
        $pdf->Ln(3);
        $pdf->MultiCell(30,4,'Uvas',0,'L'); 
        $pdf->Cell(35, -5, '5',0,0,'R');
        $pdf->Cell(10, -5, number_format(round(1,2), 2, ',', ' ').EURO,0,0,'R');
        $pdf->Cell(15, -5, number_format(round(1*5,2), 2, ',', ' ').EURO,0,0,'R');
        $pdf->Ln(3);
         
        // SUMATORIO DE LOS PRODUCTOS Y EL IVA
        $pdf->Ln(6);
        $pdf->Cell(60,0,'','T');
        $pdf->Ln(2);    
        $pdf->Cell(25, 10, 'TOTAL SIN I.V.A.', 0);    
        $pdf->Cell(20, 10, '', 0);
        $pdf->Cell(15, 10, number_format(round((round(12.25,2)/1.21),2), 2, ',', ' ').EURO,0,0,'R');
        $pdf->Ln(3);    
        $pdf->Cell(25, 10, 'I.V.A. 21%', 0);    
        $pdf->Cell(20, 10, '', 0);
        $pdf->Cell(15, 10, number_format(round((round(12.25,2)),2)-round((round(2*3,2)/1.21),2), 2, ',', ' ').EURO,0,0,'R');
        $pdf->Ln(3);    
        $pdf->Cell(25, 10, 'TOTAL', 0);    
        $pdf->Cell(20, 10, '', 0);
        $pdf->Cell(15, 10, number_format(round(12.25,2), 2, ',', ' ').EURO,0,0,'R');
         
        // PIE DE PAGINA
        $pdf->Ln(10);
        $pdf->Cell(60,0,'EL PERIODO DE DEVOLUCIONES',0,1,'C');
        $pdf->Ln(3);
        $pdf->Cell(60,0,'CADUCA EL DIA  01/11/2019',0,1,'C');
         
        $pdf->Output('ticket.pdf','i');

    }

    public static function resumen_pdf($dato) {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
        
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $fecha = date('Y-m-d');
        $hora = date('H:i:s');
        $dato = ['codigo' => 820671264471, 'caja' => 1];

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODAS LAS VENTAS DEL DIA DE HOY

        $ventas = Venta::select('CODIGO')
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->where('FECHA', '=', $fecha)
        ->where('CAJA', '=', $dato['caja'])
        ->count();

        /*  --------------------------------------------------------------------------------- */

        // CANTIDAD DE VENTAS 

        if ($ventas === 0) {
            return ["response" => false, "statusText" => "No se ha encontrado ningúna venta !"];
        }

        /*  --------------------------------------------------------------------------------- */

        // OBTENER EL PRIMER TICKET 

        $primer_ticket = Venta::select('CODIGO_CA')
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->where('FECHA', '=', $fecha)
        ->where('CAJA', '=', $dato['caja'])
        ->orderBy('CODIGO_CA', 'ASC')
        ->limit(1)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        $ultimo_ticket = Venta::select('CODIGO_CA')
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->where('FECHA', '=', $fecha)
        ->where('CAJA', '=', $dato['caja'])
        ->orderBy('CODIGO_CA', 'DESC')
        ->limit(1)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // SUMAR TODOS LOS VALORES CONTADO

        $contado = Venta::select(DB::raw('SUM(EFECTIVO) AS T_EFECTIVO, SUM(TARJETAS) AS T_TARJETAS, SUM(VALE) AS T_VALES, SUM(CHEQUE) AS T_CHEQUE, SUM(DONACION) AS T_DONACION, SUM(GIROS) AS T_GIROS, SUM(VUELTO) AS T_VUELTOS, SUM(BASE5) AS T_BASE5, SUM(BASE10) AS T_BASE10, SUM(EXENTAS) AS T_EXENTAS, SUM(MONEDA1) AS DOLARES, SUM(MONEDA2) AS REALES, SUM(MONEDA3) AS GUARANIES, SUM(MONEDA4) AS PESOS, SUM(TOTAL) AS T_TOTAL'))
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->where('FECHA', '=', $fecha)
        ->where('TIPO', '=', 'CO')
        ->where('CAJA', '=', $dato['caja'])
        ->where('TOTAL', '<>', 0)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // SUMAR TODOS LOS VALORES CREDITO

        $credito = Venta::select(DB::raw('IFNULL(SUM(TOTAL), 0) AS T_TOTAL'))
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->where('FECHA', '=', $fecha)
        ->where('TIPO', '=', 'CR')
        ->where('CAJA', '=', $dato['caja'])
        ->where('TOTAL', '<>', 0)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // TARJETA 
        
        $tarjeta = VentaTarjeta::select(DB::raw('IFNULL(SUM(VENTAS_TARJETA.MONTO), 0) AS TOTAL'))
        ->leftjoin('VENTAS', 'VENTAS.ID', '=', 'VENTAS_TARJETA.FK_VENTA')
        ->where('VENTAS.ID_SUCURSAL', '=', $user->id_sucursal)
        ->where('VENTAS.FECHA', '=', $fecha)
        ->where('VENTAS.CAJA', '=', $dato['caja'])
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // TRANSFERENCIA
        
        $transferencia = VentaTransferencia::select(DB::raw('IFNULL(SUM(VENTAS_TRANSFERENCIA.MONTO), 0) AS TOTAL'))
        ->leftjoin('VENTAS', 'VENTAS.ID', '=', 'VENTAS_TRANSFERENCIA.FK_VENTA')
        ->where('VENTAS.ID_SUCURSAL', '=', $user->id_sucursal)
        ->where('VENTAS.FECHA', '=', $fecha)
        ->where('VENTAS.CAJA', '=', $dato['caja'])
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // GIRO
        
        $giro = VentaGiro::select(DB::raw('IFNULL(SUM(VENTAS_GIRO.MONTO), 0) AS TOTAL'))
        ->leftjoin('VENTAS', 'VENTAS.ID', '=', 'VENTAS_GIRO.FK_VENTA')
        ->where('VENTAS.ID_SUCURSAL', '=', $user->id_sucursal)
        ->where('VENTAS.FECHA', '=', $fecha)
        ->where('VENTAS.CAJA', '=', $dato['caja'])
        ->get();

        /*  --------------------------------------------------------------------------------- */


        // SUMAR ANULADOS

        $anulados = Venta::select('CODIGO')
        ->where('TOTAL', '=', 0)
        ->where('FECHA', '=', $fecha)
        ->where('CAJA', '=', $dato['caja'])
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->count();

        /*  --------------------------------------------------------------------------------- */

        // PARAMETROS 

        $parametro = Parametro::select(DB::raw('EMPRESA, PROPIETARIO, DIRECCION, CIUDAD, ACTIVIDAD, RUC, MONEDA'))
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // HABILITACION CAJA

        $habilitacion = Venta::select(DB::raw('USER, HORALTAS'))
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->where('FECHA', '=', $fecha)
        ->where('CAJA', '=', $dato['caja'])
        ->orderBy('CODIGO', 'ASC')
        ->limit(1)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // CIERRE CAJA

        $cierre = Venta::select(DB::raw('USER, HORALTAS'))
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->where('FECHA', '=', $fecha)
        ->where('CAJA', '=', $dato['caja'])
        ->orderBy('CODIGO', 'DESC')
        ->limit(1)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // CANTIDAD DE DECIMALES Y MONEDA

        $candec = 0;
        $moneda = 1;

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI ES TABLA UNICA 

        $tab_unica = Parametro::tab_unica();

        if ($tab_unica === "SI") {
            $tab_unica = true;
        } else {
            $tab_unica = false;
        }

        /*  --------------------------------------------------------------------------------- */

        //$nombre = 'Venta_'.$codigo.'_'.time().'';
        $articulos = [];
        $cantidad = 0;
        $exentas = 0;
        $base5 = 0;
        $base10 = 0;
        $iva = 0;
        $total = 0;
        $switch_hojas = false;
        $namefile = 'boleta_de_venta_'.time().'.pdf';
        $letra = '';

        /*  --------------------------------------------------------------------------------- */
        
        $pdf = new FPDF('P','mm',array(80,190));
        $pdf->AddPage();
         
        // CABECERA

        $pdf->SetFont('Helvetica','',12);
        $pdf->Cell(60,4, $parametro[0]->EMPRESA,0,1,'C');
        $pdf->SetFont('Helvetica','',8);
        $pdf->Cell(60,4, 'RESUMEN DE CAJA',0,1,'C');
        $pdf->Cell(30,4, 'Fecha: '.$fecha ,0,0,'L');
        $pdf->Cell(30,4, 'Hora: '.$hora ,0,1,'R');
        $pdf->Cell(60,4, 'Caja: '.$dato['caja'] ,0,1,'C');
          
        $pdf->Cell(25, 4, 'Cajero:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, $user->name,0,0,'R');
        $pdf->Ln(4);
        $pdf->Cell(25, 4, 'Intervalo Ticket:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, $primer_ticket[0]->CODIGO_CA.' - '.$ultimo_ticket[0]->CODIGO_CA,0,0,'R');

        $pdf->Ln(6);

        /*  --------------------------------------------------------------------------------- */

        // LINEA 

        $pdf->Cell(60,0,'','T');

        /*  --------------------------------------------------------------------------------- */

        // MONEDAS 

        $pdf->Ln(2);
        $pdf->Cell(25, 4, 'Dolares:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec($contado[0]->DOLARES, 2),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Reales:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec($contado[0]->REALES, 4),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Guaranies:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec($contado[0]->GUARANIES, 1),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Pesos:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec($contado[0]->PESOS, 3),0,0,'R');
        $pdf->Ln(6);

        /*  --------------------------------------------------------------------------------- */

        // LINEA 
        
        $pdf->Cell(60,0,'','T');

        /*  --------------------------------------------------------------------------------- */

        // MONEDAS 

        $pdf->Ln(2);
        $pdf->Cell(25, 4, utf8_decode('Dotación:'), 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec($contado[0]->T_DONACION, $parametro[0]->MONEDA),0,0,'R');
        $pdf->Ln(4);

        // $pdf->Cell(25, 4, 'Efectivo:', 0);
        // $pdf->Cell(20, 4, '', 0);
        // $pdf->Cell(15, 4, Common::precio_candec($contado[0]->T_EFECTIVO, $parametro[0]->MONEDA),0,0,'R');
        // $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Tarjetas:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec($tarjeta[0]->TOTAL, 1),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Transferencia:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec($transferencia[0]->TOTAL, 1),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Cheques:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec($contado[0]->T_CHEQUE, $parametro[0]->MONEDA),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Giros:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec($giro[0]->TOTAL, 1),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Vales:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec($contado[0]->T_VALES, $parametro[0]->MONEDA),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Giros:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec($contado[0]->T_GIROS, $parametro[0]->MONEDA),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, utf8_decode('Donación:'), 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec($contado[0]->T_DONACION, $parametro[0]->MONEDA),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Retiros:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec($contado[0]->T_DONACION , $parametro[0]->MONEDA),0,0,'R');
        $pdf->Ln(4);

        // $pdf->Cell(25, 4, 'Vuelto:', 0);
        // $pdf->Cell(20, 4, '', 0);
        // $pdf->Cell(15, 4, Common::precio_candec($contado[0]->T_VUELTOS, $parametro[0]->MONEDA),0,0,'R');
        // $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Gastos:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec($contado[0]->T_DONACION, $parametro[0]->MONEDA),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Total Ventas:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec($contado[0]->T_TOTAL, $parametro[0]->MONEDA),0,0,'R');
        // $pdf->Ln(4);

        // $pdf->Cell(25, 4, 'Total Efectivo:', 0);
        // $pdf->Cell(20, 4, '', 0);
        // $pdf->Cell(15, 4, Common::precio_candec(($contado[0]->T_EFECTIVO + $contado[0]->T_CHEQUE + $contado[0]->T_VALES + $contado[0]->T_GIROS), $parametro[0]->MONEDA),0,0,'R');
        $pdf->Ln(6);

        /*  --------------------------------------------------------------------------------- */

        // LINEA 
        
        $pdf->Cell(60,0,'','T');

        /*  --------------------------------------------------------------------------------- */

        $pdf->Ln(2);
        $pdf->Cell(25, 4, utf8_decode('Ventas Crédito:'), 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, $credito[0]->T_TOTAL,0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Tickets Anulados:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, $anulados,0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'TOTAL GRAVADAS 10%:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, $contado[0]->T_BASE10,0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'TOTAL GRAVADAS 5%:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, $contado[0]->T_BASE5,0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'TOTAL EXENTAS:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, $contado[0]->T_EXENTAS,0,0,'R');
        $pdf->Ln(6);

        /*  --------------------------------------------------------------------------------- */

        // LINEA 
        
        $pdf->Cell(60,0,'','T');

        /*  --------------------------------------------------------------------------------- */

        $pdf->Ln(6);

        $pdf->Cell(25, 4, utf8_decode('HABILITACIÓN CAJA:'), 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, $habilitacion[0]["USER"],0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'HORA:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, $habilitacion[0]["HORALTAS"],0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 10, utf8_decode('HABILITACIÓN CAJA:'), 0);
        $pdf->Cell(20, 10, '', 0);
        $pdf->Cell(15, 10, $cierre[0]["USER"],0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 10, 'HORA:', 0);
        $pdf->Cell(20, 10, '', 0);
        $pdf->Cell(15, 10, $cierre[0]["HORALTAS"],0,0,'R');
        $pdf->Ln(4);

        /*  --------------------------------------------------------------------------------- */

        $pdf->Output('ticket.pdf','i');

    }

    public static function ticket_pdf($dato) {

        //$dato = ['codigo' => 820671264471, 'caja' => 1];

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER DATOS DE CABECERA 

        $ventas = Venta::mostrar_cabecera($dato);
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER DATOS DETALLE 

        $ventas_det = Venta::mostrar_cuerpo($dato);
        
        /*  --------------------------------------------------------------------------------- */

        // CANTIDAD DE DECIMALES Y MONEDA

        $candec = (Parametro::candec($ventas->MONEDA))["CANDEC"];
        $monedaVenta = $ventas->MONEDA;

        /*  --------------------------------------------------------------------------------- */

        // PARAMETROS 

        $mensaje = Parametro::select(DB::raw('MENSAJE'))
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI ES TABLA UNICA 

        $tab_unica = Parametro::tab_unica();

        if ($tab_unica === "SI") {
            $tab_unica = true;
        } else {
            $tab_unica = false;
        }

        /*  --------------------------------------------------------------------------------- */

        $sucursal = Sucursal::encontrarSucursal(['codigoOrigen' => $user->id_sucursal]);
        
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $nombre_sucursal = $sucursal['sucursal'][0]['DESCRIPCION'];
        $codigo = $ventas->CODIGO;
        $cliente = $ventas->CLIENTE;
        $direccion = $ventas->DIRECCION;
        $ruc = $ventas->RUC;
        $tipo = $ventas->TIPO;
        $fecha = substr($ventas->FECALTAS,0,10);
        $hora = $ventas->HORALTAS;
        $caja = $ventas->CAJA;
        $vendedor = $ventas->VENDEDOR;
        $cajero = $ventas->CAJERO;
        $pago = $ventas->PAGO;
        $vuelto = $ventas->VUELTO;
        $descuento = $ventas->DESCUENTO;
        $ticket = $ventas->CODIGO_CA;
        $tipo = $ventas->TIPO;
        $documento = $ventas->RUC;
        $nombre = 'Venta_'.$codigo.'_'.time().'';
        $articulos = [];
        $cantidad = 0;
        $exentas = 0;
        $base5 = 0;
        $base10 = 0;
        $iva = 0;
        $total = 0;
        $switch_hojas = false;
        $namefile = 'boleta_de_venta_'.time().'.pdf';
        $letra = '';

        /*  --------------------------------------------------------------------------------- */
        
        $pdf = new FPDF('P','mm',array(75,150));
        $pdf->AddPage();
         
        // CABECERA

        $pdf->SetFont('Helvetica','',12);
        $pdf->Cell(60,4, $nombre_sucursal ,0,1,'C');
        $pdf->SetFont('Helvetica','',8);
        $pdf->Cell(30,4, 'Fecha: '.$fecha ,0,0,'L');
        $pdf->Cell(30,4, 'Hora: '.$hora ,0,1,'R');
        $pdf->Cell(30,4, 'Caja: '.$caja ,0,0,'L');
        $pdf->Cell(30,4, 'Ticket: '.$ticket ,0,1,'R');

        $pdf->Cell(25, 4, 'Vendedor:', 0);   
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, $vendedor,0,0,'R');
        $pdf->Ln(4);
        $pdf->Cell(25, 4, 'Cajero:', 0);   
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, $cajero,0,0,'R');
        $pdf->Ln(5);

        // DATOS FACTURA

        // $pdf->Ln(5);
        // $pdf->Cell(60,4,'Factura Simpl.: F2019-000001',0,1,'');
        // $pdf->Cell(60,4,'Fecha: 28/10/2019',0,1,'');
        // $pdf->Cell(60,4,'Metodo de pago: Tarjeta',0,1,'');
         
        // COLUMNAS

        $pdf->SetFont('Helvetica', 'B', 7);
        $pdf->Cell(25, 10, 'Articulo', 0);
        $pdf->Cell(5, 10, 'Cant.',0,0,'R');
        $pdf->Cell(15, 10, 'Precio',0,0,'R');
        $pdf->Cell(15, 10, 'Total',0,0,'R');
        $pdf->Ln(8);
        $pdf->Cell(60,0,'','T');
        $pdf->Ln(2);

        /*  --------------------------------------------------------------------------------- */

        // PRODUCTOS
        
        foreach ($ventas_det as $key => $value) {
            
            /*  --------------------------------------------------------------------------------- */
                
            $articulos["precio"] = $value["PRECIO_UNIT"];
            $articulos["total"] = $value["PRECIO"];
            $exentas = $exentas + Common::quitar_coma($value["EXENTAS"], $candec);
            $total = $total + Common::quitar_coma($value["PRECIO"], $candec);
            
            /*  --------------------------------------------------------------------------------- */

            // CARGAR VARIABLES 

            $articulos["cantidad"] = $value["CANTIDAD"];
            $articulos["cod_prod"] = $value["COD_PROD"];
            $articulos["descripcion"] = utf8_decode(substr($value["DESCRIPCION"], 0,38));
            $cantidad = $cantidad + $value["CANTIDAD"];

            /*  --------------------------------------------------------------------------------- */

            // REVISAR SI EXISTE EXENTAS 

            if ($value["EXENTAS"] > 0) {
                $articulos["exentas"] = $articulos["total"];
            } else {
                $articulos["exentas"] = '';
            }
            
            /*  --------------------------------------------------------------------------------- */

            // REVISAR SI EXISTE BASE5 O BASE10

            if ($value["BASE5"] > 0) {
                $articulos["base10"] = '';
                $articulos["base5"] = $articulos["total"];
                $base5 = $base5 + Common::quitar_coma($articulos["total"], $candec);
            } else if ($value["BASE10"] > 0) {
                $articulos["base5"] = '';
                $articulos["base10"] = $articulos["total"];
                $base10 = $base10 + Common::quitar_coma($articulos["total"], $candec);
            } else {
                $articulos["base5"] = '';
                $articulos["base10"] = '';
            }
                
            /*  --------------------------------------------------------------------------------- */

            // CARGAR PRODUCTOS 

            $pdf->SetFont('Helvetica', '', 7);
            $pdf->Cell(25,4,$articulos["cod_prod"],0,'L'); 
            $pdf->Cell(5, 4, $articulos["cantidad"],0,0,'R');
            $pdf->Cell(15, 4, $articulos["precio"],0,0,'R');
            $pdf->Cell(15, 4, $articulos["total"],0,0,'R');
            $pdf->Ln(3);
            $pdf->Cell(60,4,$articulos["descripcion"],0,1,'L');
            $pdf->Ln(2);

            /*  --------------------------------------------------------------------------------- */

            // BUSCAR DESCUENTOS 

            $descuento_producto = Ventas_det_Descuento::select(DB::raw('PORCENTAJE, TOTAL'))
            ->WHERE('FK_VENTASDET', '=', $value["ID"])
            ->WHERE('FK_COD_PROD', '=', $value["COD_PROD"])
            ->get();

            /*  --------------------------------------------------------------------------------- */

            if (count($descuento_producto) > 0) {

                /*  --------------------------------------------------------------------------------- */

                $pdf->SetFont('Helvetica', '', 7);
                $pdf->Cell(25,4,$articulos["cod_prod"],0,'L'); 
                $pdf->Cell(5, 4, $articulos["cantidad"],0,0,'R');
                $pdf->Cell(15, 4, '',0,0,'R');
                $pdf->Cell(15, 4, $descuento_producto[0]->TOTAL ,0,0,'R');
                $pdf->Ln(3);
                $pdf->Cell(60,4,'DESCUENTO '.$descuento_producto[0]->PORCENTAJE.'%',0,1,'L');
                $pdf->Ln(2);

                /*  --------------------------------------------------------------------------------- */

            }
        }
         
        // SUMATORIO DE LOS PRODUCTOS Y EL IVA

        $pdf->Ln(1);
        $pdf->Cell(60,0,'','T');
        $pdf->Ln(2);
        $pdf->SetFont('Helvetica', 'B', 7);    
        $pdf->Cell(25, 10, 'TOTAL:', 0);
        $pdf->SetFont('Helvetica', '', 7);    
        $pdf->Cell(20, 10, '', 0);
        $pdf->Cell(15, 10, Common::precio_candec_sin_letra(($total + $descuento), $monedaVenta),0,0,'R');
        $pdf->Ln(3);
        $pdf->SetFont('Helvetica', 'B', 7);    
        $pdf->Cell(25, 10, 'DESCUENTO:', 0);
        $pdf->SetFont('Helvetica', '', 7);    
        $pdf->Cell(20, 10, '', 0);
        $pdf->Cell(15, 10, Common::precio_candec_sin_letra($descuento, $monedaVenta),0,0,'R');
        $pdf->Ln(3);
        $pdf->SetFont('Helvetica', 'B', 7);    
        $pdf->Cell(25, 10, 'TOTAL A PAGAR:', 0);
        $pdf->SetFont('Helvetica', '', 7);    
        $pdf->Cell(20, 10, '', 0);
        $pdf->Cell(15, 10, Common::precio_candec_sin_letra($total, $monedaVenta),0,0,'R');
        
        $pdf->Ln(10);
        $pdf->Cell(60,0,'','T');
        $pdf->Ln(2);
        $pdf->SetFont('Helvetica', 'B', 7);    
        $pdf->Cell(25, 10, 'PAGO:', 0);
        $pdf->SetFont('Helvetica', '', 7);    
        $pdf->Cell(20, 10, '', 0);
        $pdf->Cell(15, 10, Common::precio_candec($pago, $monedaVenta),0,0,'R');
        $pdf->Ln(3);
        $pdf->SetFont('Helvetica', 'B', 7);    
        $pdf->Cell(25, 10, 'VUELTO:', 0);
        $pdf->SetFont('Helvetica', '', 7);    
        $pdf->Cell(20, 10, '', 0);
        $pdf->Cell(15, 10, Common::precio_candec($vuelto, $monedaVenta),0,0,'R');

        $pdf->Ln(10);
        $pdf->Cell(60,0,'','T');
        $pdf->Ln(2);
        $pdf->SetFont('Helvetica', 'B', 7);    
        $pdf->Cell(25, 10, 'CLIENTE:', 0);
        $pdf->SetFont('Helvetica', '', 7);    
        $pdf->Cell(20, 10, '', 0);
        $pdf->Cell(15, 10, $cliente,0,0,'R');
        $pdf->Ln(3);
        $pdf->SetFont('Helvetica', 'B', 7);
        $pdf->Cell(25, 10, 'R.U.C./C.I.:', 0);
        $pdf->SetFont('Helvetica', '', 7);    
        $pdf->Cell(20, 10, '', 0);
        $pdf->Cell(15, 10, $documento,0,0,'R');
        $pdf->Ln(3);
        $pdf->SetFont('Helvetica', 'B', 7);     
        $pdf->Cell(25, 10, 'TIPO VENTA:', 0);
        $pdf->SetFont('Helvetica', '', 7);    
        $pdf->Cell(20, 10, '', 0);
        $pdf->Cell(15, 10, $tipo,0,0,'R');

        // PIE DE PAGINA

        $pdf->Ln(10);
        $pdf->Cell(60,0,'','T');
        $pdf->Ln(10);
        $pdf->Cell(60,0,$mensaje[0]->MENSAJE,0,1,'C');
         
        $pdf->Output('ticket.pdf','i');

    }

    public static function factura_pdf($dato)
    {
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
        $formatter = new NumeroALetras;

        /*  --------------------------------------------------------------------------------- */

        // OBTENER DATOS DE CABECERA 

        $ventas = Venta::mostrar_cabecera($dato);
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER DATOS DETALLE 

        $ventas_det = Venta::mostrar_cuerpo($dato);
        
        /*  --------------------------------------------------------------------------------- */

        // CANTIDAD DE DECIMALES Y MONEDA

        $candec = 0;
        $moneda = 1;
        $monedaVenta = $ventas->MONEDA;

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI ES TABLA UNICA 

        $tab_unica = Parametro::tab_unica();

        if ($tab_unica === "SI") {
            $tab_unica = true;
        } else {
            $tab_unica = false;
        }

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $c = 0;
        $c_rows = 0;
        $c_rows_array = count($ventas_det);
        $c_filas_total = count($ventas_det);
        $codigo = $ventas->CODIGO;
        $cliente = $ventas->CLIENTE;
        $direccion = $ventas->DIRECCION;
        $ruc = $ventas->RUC;
        $tipo = $ventas->TIPO;
        $fecha = $ventas->FECALTAS;
        $nombre = 'Venta_'.$codigo.'_'.time().'';
        $articulos = [];
        $cantidad = 0;
        $exentas = 0;
        $base5 = 0;
        $base10 = 0;
        $iva = 0;
        $total = 0;
        $switch_hojas = false;
        $namefile = 'boleta_de_venta_'.time().'.pdf';
        $letra = '';

        /*  --------------------------------------------------------------------------------- */

        // CARGAR VARIABLES 
        
        $data['codigo'] = $codigo;
        $data['cliente'] = $cliente;
        $data['direccion'] = $direccion;
        $data['ruc'] = $ruc;
        $data['fecha'] = $fecha;
        $data['nombre'] = $nombre;
        $data['c'] = $c;
        $data['tipo'] = 'fisico';
        
        /*  --------------------------------------------------------------------------------- */
        
        // INICIAR MPDF 

        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
         
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf([
            'fontDir' => array_merge($fontDirs, [
                public_path() . '/fonts',
            ]),
            'fontdata' => $fontData + [
                'arial' => [
                    'R' => 'arial.ttf',
                    'B' => 'arialbd.ttf',
                ],
            ],
            'default_font' => 'arial',
            "format" => [240,140],
        ]);

        $mpdf->SetDisplayMode('fullpage');

        /*  --------------------------------------------------------------------------------- */

        // CARGAR DETALLE DE TRANSFERENCIA DET 
        
        foreach ($ventas_det as $key => $value) {
            
            /*  --------------------------------------------------------------------------------- */

            // SI LA MONEDA DEL PRODUCTO ES DIFERENTE A GUARANIES COTIZAR 
            
            if ($value["MONEDA"] <> 1) {

                /*  --------------------------------------------------------------------------------- */

                // PRECIO 

                $cotizacion = Cotizacion::CALMONED(['monedaProducto' => $monedaVenta, 'monedaSistema' => 1, 'precio' => Common::quitar_coma($value["PRECIO_UNIT"], 2), 'decSistema' => 0, 'tab_unica' => $tab_unica, "id_sucursal" => $user["id_sucursal"]]);

                // SI NO ENCUENTRA COTIZACION RETORNAR 

                if ($cotizacion["response"] === false) {
                    header('HTTP/1.1 500 Internal Server Error');
                    exit;
                }

                $articulos[$c_rows]["precio"] = $cotizacion["valor"];

                /*  --------------------------------------------------------------------------------- */

                // TOTAL 

                $cotizacion = Cotizacion::CALMONED(['monedaProducto' => $monedaVenta, 'monedaSistema' => 1, 'precio' => Common::quitar_coma($value["PRECIO"], 2), 'decSistema' => 0, 'tab_unica' => $tab_unica, "id_sucursal" => $user["id_sucursal"]]);
                $articulos[$c_rows]["total"] = $cotizacion["valor"];

                // SI NO ENCUENTRA COTIZACION RETORNAR

                if ($cotizacion["response"] === false) {
                    header('HTTP/1.1 500 Internal Server Error');
                    exit;
                }

                $exentas = $exentas + Common::quitar_coma($articulos[$c_rows]["total"], $candec);
                $total = $total + Common::quitar_coma($articulos[$c_rows]["total"], $candec);

                /*  --------------------------------------------------------------------------------- */

            } else {
                
                $articulos[$c_rows]["precio"] = $value["PRECIO_UNIT"];
                $articulos[$c_rows]["total"] = $value["PRECIO"];
                $exentas = $exentas + Common::quitar_coma($value["EXENTAS"], $candec);
                $total = $total + Common::quitar_coma($value["PRECIO"], $candec);
            }

            
            /*  --------------------------------------------------------------------------------- */

            // CARGAR VARIABLES 

            $articulos[$c_rows]["cantidad"] = $value["CANTIDAD"];
            $articulos[$c_rows]["cod_prod"] = $value["COD_PROD"];
            $articulos[$c_rows]["descripcion"] = substr($value["DESCRIPCION"], 0,30);
            $cantidad = $cantidad + $value["CANTIDAD"];

            /*  --------------------------------------------------------------------------------- */

            // REVISAR SI EXISTE EXENTAS 

            if ($value["EXENTAS"] > 0) {
                $articulos[$c_rows]["exentas"] = $articulos[$c_rows]["total"];
            } else {
                $articulos[$c_rows]["exentas"] = '';
            }
            
            /*  --------------------------------------------------------------------------------- */

            // REVISAR SI EXISTE BASE5 O BASE10

            if ($value["BASE5"] > 0) {
                $articulos[$c_rows]["base10"] = '';
                $articulos[$c_rows]["base5"] = $articulos[$c_rows]["total"];
                $base5 = $base5 + Common::quitar_coma($articulos[$c_rows]["total"], $candec);
            } else if ($value["BASE10"] > 0) {
                $articulos[$c_rows]["base5"] = '';
                $articulos[$c_rows]["base10"] = $articulos[$c_rows]["total"];
                $base10 = $base10 + Common::quitar_coma($articulos[$c_rows]["total"], $candec);
            } else {
                $articulos[$c_rows]["base5"] = '';
                $articulos[$c_rows]["base10"] = '';
            }
            
            /*  --------------------------------------------------------------------------------- */
            
            // CONTAR CANTIDAD DE FILAS DE HOJAS 

            $c_rows = $c_rows + 1;    
            
            /*  --------------------------------------------------------------------------------- */

            // CONTAR LA CANTIDAD DE FILAS 

            $c = $c + 1;

            /*  --------------------------------------------------------------------------------- */

            // SI CANTIDAD DE FILAS ES IGUAL A 10 ENTONCES CREAR PAGINA 

            if ($c_rows === 10){
                
                /*  --------------------------------------------------------------------------------- */

                // AGREGAR ARTICULOS 

                $data['articulos'] = $articulos;

                /*  --------------------------------------------------------------------------------- */

                // RESTAR LAS CANTIDADES CARGADAS 

                $c_rows_array = $c_rows_array - 10;

                /*  --------------------------------------------------------------------------------- */

                // PONER TRUE SWITCH YA QUE CREO UNA PAGINA 

                $switch_hojas = true;

                /*  --------------------------------------------------------------------------------- */

                // CARGAR SUB TOTALES POR HOJA

                $data['cantidad'] = $cantidad;
                //$data['letra'] = 'Son Guaranies: '.substr(NumeroALetras::convertir($total, 'guaranies'), 0, strpos(NumeroALetras::convertir($total, 'guaranies'), "CON"));
                $data['letra'] = 'Son Guaranies: '.($formatter->toMoney($total, 0, 'guaranies'));
                $data['total'] = Common::precio_candec_sin_letra($total, $moneda);
                $data['exentas'] = Common::precio_candec_sin_letra($exentas, $moneda);
                $data['base5'] = Common::precio_candec_sin_letra($base5 / 21, $moneda);
                $data['base10'] = Common::precio_candec_sin_letra($base10 / 11, $moneda);
                $data['iva'] = Common::precio_candec_sin_letra(($base5 / 21) + ($base10 / 11), $moneda);

                /*  --------------------------------------------------------------------------------- */

                $html = view('pdf.facturaVenta', $data)->render();
                
                /*  --------------------------------------------------------------------------------- */

                // SI NO ES LA PRIMERA HOJA AGREGAR PAGINA

                if ($c !== 10) {
                    $mpdf->AddPage();
                }

                /*  --------------------------------------------------------------------------------- */

                // CERAR CONTADOR DE FILAS CARGADAS POR HOJAS Y ARTICULOS

                $c_rows = 0;
                $exentas = 0;
                $base5 = 0;
                $base10 = 0;
                $total = 0;
                $data['articulos'] = [];
                $articulos = [];

                /*  --------------------------------------------------------------------------------- */
                    
                $mpdf->WriteHTML($html);

                /*  --------------------------------------------------------------------------------- */

            } else if ($c_rows_array < 10 && $c_filas_total === $c) {
               
                /*  --------------------------------------------------------------------------------- */
                
                // AGREGAR ARTICULOS 
                
                $data['articulos'] = $articulos;

                /*  --------------------------------------------------------------------------------- */

                // CARGAR SUB TOTALES POR HOJA

                $data['cantidad'] = $cantidad;
                //$data['letra'] = 'Son Guaranies: '.substr(NumeroALetras::convertir($total, 'guaranies'), 0, strpos(NumeroALetras::convertir($total, 'guaranies'), "CON"));
                $data['letra'] = 'Son Guaranies: '.($formatter->toMoney($total, 0, 'guaranies'));
                $data['total'] = Common::precio_candec_sin_letra($total, $moneda);
                $data['exentas'] = Common::precio_candec_sin_letra($exentas, $moneda);
                $data['base5'] = Common::precio_candec_sin_letra($base5 / 21, $moneda);
                $data['base10'] = Common::precio_candec_sin_letra($base10 / 11, $moneda);
                $data['iva'] = Common::precio_candec_sin_letra(($base5 / 21) + ($base10 / 11), $moneda);

                /*  --------------------------------------------------------------------------------- */

                // CREAR HOJA 

                $html = view('pdf.facturaVenta', $data)->render();

                /*  --------------------------------------------------------------------------------- */

                // SI NO ES LA PRIMERA HOJA AGREGAR PAGINA

                if ($switch_hojas === true) {
                    $mpdf->AddPage();
                }

                /*  --------------------------------------------------------------------------------- */
                    
                $mpdf->WriteHTML($html);

                /*  --------------------------------------------------------------------------------- */

            }
        }
        
        /*  --------------------------------------------------------------------------------- */
        
        // DESCARGAR ARCHIVO PDF 

        $mpdf->Output($namefile,"D");

        /*  --------------------------------------------------------------------------------- */
        
    }

    /*  --------------------------------------------------------------------------------- */

    public static function datatable_venta($request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'CODIGO', 
                            3 => 'FECHA',
                            4 => 'IVA',
                            5 => 'TOTAL',
                            6 => 'ACCION',
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = Venta::where('ID_SUCURSAL','=', $user->id_sucursal)
                     ->count();  
        
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

            $posts = Venta::select(DB::raw('VENTAS.CODIGO, VENTAS.CAJA, substring(VENTAS.FECHA, 1, 11) AS FECHA, VENTAS.HORA, VENTAS.TIPO, VENTAS.TOTAL, VENTAS.MONEDA, CLIENTES.NOMBRE AS CLIENTE'))
                         ->leftJoin('CLIENTES', function($join){
                            $join->on('VENTAS.CLIENTE', '=', 'CLIENTES.CODIGO')
                                 ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
                         })
                         ->where('VENTAS.ID_SUCURSAL','=', $user->id_sucursal)
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

            $posts =  Venta::select(DB::raw('VENTAS.CODIGO, VENTAS.CAJA, substring(VENTAS.FECHA, 1, 11) AS FECHA, VENTAS.HORA, VENTAS.TIPO, VENTAS.TOTAL, VENTAS.MONEDA, CLIENTES.NOMBRE AS CLIENTE'))
                         ->leftJoin('CLIENTES', function($join){
                            $join->on('VENTAS.CLIENTE', '=', 'CLIENTES.CODIGO')
                                 ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
                         })
                         ->where('VENTAS.ID_SUCURSAL','=', $user->id_sucursal)
                            ->where(function ($query) use ($search) {
                                $query->where('VENTAS.CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('VENTAS.FECHA', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = Venta::where('VENTAS.ID_SUCURSAL','=', $user->id_sucursal)
                            ->where(function ($query) use ($search) {
                                $query->where('VENTAS.CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('VENTAS.FECHA', 'LIKE',"%{$search}%");
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

                $nestedData['CODIGO'] = $post->CODIGO;
                $nestedData['CAJA'] = $post->CAJA;
                $nestedData['CLIENTE'] = $post->CLIENTE;
                $nestedData['FECHA'] = $post->FECHA;
                $nestedData['HORA'] = $post->HORA;
                $nestedData['TIPO'] = $post->TIPO;
                $nestedData['TOTAL'] = Common::precio_candec($post->TOTAL, $post->MONEDA);
                
                $nestedData['ACCION'] = "
                    &emsp;<a href='#' id='imprimirTicket' title='Reporte'><i class='fa fa-file text-secondary' aria-hidden='true'></i></a>&emsp;<a href='#' id='imprimirFactura' title='Imprimir'><i class='fa fa-print text-primary' aria-hidden='true'></i></a>";

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
    }

    /*  --------------------------------------------------------------------------------- */

}
