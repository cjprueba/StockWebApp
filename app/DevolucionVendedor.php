<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class DevolucionVendedores extends Model
{
    //
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
                ->leftjoin('ventas AS ven',function($join){
          $join->on('ven.CODIGO','=','v.CODIGO')
               ->on('ven.ID_SUCURSAL','=','v.ID_SUCURSAL')
                ->on('ven.CAJA','=','v.CAJA');
               })
                ->leftjoin('EMPlEADOS AS e',function($join){
          $join->on('e.CODIGO','=','ven.VENDEDOR')
               ->on('e.ID_SUCURSAL','=','v.ID_SUCURSAL');
               })
            ->select(DB::raw('SUM(v.PRECIO) AS PRECIO'),
            DB::raw('SUM(v.CANTIDAD) AS VENDIDO'),
            DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = v.COD_PROD) AND (l.ID_SUCURSAL = v.ID_SUCURSAL))),0) AS STOCK'),
            DB::raw('e.NOMBRE AS MARCA_NOMBRE'),
            DB::raw('MARCA.DESCRIPCION  AS LINEA_NOMBRE'),
            DB::raw('v.COD_PROD'),
             DB::raw('PRODUCTOS.MARCA AS LINEA'), 
            DB::raw('e.CODIGO AS MARCA'))
            ->whereBetween('v.FECALTAS', [$inicio , $final])
            ->where([
                ['v.ID_SUCURSAL', '=', $sucursal],
                ['v.ANULADO', '<>', 1],
                ['v.DESCRIPCION', 'LIKE', 'DEVOLUCION%'],
            ])
            ->groupBy('v.COD_PROD')
            ->get()
            ->toArray(); 


        } else if ($datos['AllCategory']) {
            
            $ventasdet = DB::connection('retail')->table('VENTASDET as v')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
                ->leftjoin('ventas AS ven',function($join){
          $join->on('ven.CODIGO','=','v.CODIGO')
               ->on('ven.ID_SUCURSAL','=','v.ID_SUCURSAL')
                ->on('ven.CAJA','=','v.CAJA');
               })
                ->leftjoin('EMPlEADOS AS e',function($join){
          $join->on('e.CODIGO','=','ven.VENDEDOR')
               ->on('e.ID_SUCURSAL','=','v.ID_SUCURSAL');
               })
            ->select(DB::raw('SUM(v.PRECIO) AS PRECIO'),
            DB::raw('SUM(v.CANTIDAD) AS VENDIDO'),
            DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = v.COD_PROD) AND (l.ID_SUCURSAL = v.ID_SUCURSAL)) Group By v.COD_PROD),0) AS STOCK'),
            DB::raw('e.NOMBRE AS MARCA_NOMBRE'),
            DB::raw('MARCA.DESCRIPCION AS LINEA_NOMBRE'),
            DB::raw('v.COD_PROD'),
            DB::raw('PRODUCTOS.MARCA  AS LINEA'), 
            DB::raw('e.CODIGO AS MARCA')) 
            ->whereBetween('v.FECALTAS', [$inicio , $final])
            ->whereIn('PRODUCTOS.MARCA', $datos['Marcas'])
            ->where([
                ['v.ID_SUCURSAL', '=', $sucursal],
                ['v.ANULADO', '<>', 1],
                ['v.DESCRIPCION', 'LIKE', 'DEVOLUCION%'],
            ])
            ->groupBy('v.COD_PROD')
            ->get()
            ->toArray(); 

        } else if ($datos['AllBrand']) {
             
            $ventasdet = DB::connection('retail')->table('VENTASDET as v')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
                ->leftjoin('ventas AS ven',function($join){
          $join->on('ven.CODIGO','=','v.CODIGO')
               ->on('ven.ID_SUCURSAL','=','v.ID_SUCURSAL')
                ->on('ven.CAJA','=','v.CAJA');
               })
                ->leftjoin('EMPlEADOS AS e',function($join){
          $join->on('e.CODIGO','=','ven.VENDEDOR')
               ->on('e.ID_SUCURSAL','=','v.ID_SUCURSAL');
               })
            ->select(DB::raw('SUM(v.PRECIO) AS PRECIO'),
            DB::raw('SUM(v.CANTIDAD) AS VENDIDO'),
            DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = v.COD_PROD) AND (l.ID_SUCURSAL = v.ID_SUCURSAL)) Group By v.COD_PROD),0) AS STOCK'),
            DB::raw('e.NOMBRE AS MARCA_NOMBRE'),
            DB::raw('MARCA.DESCRIPCION AS LINEA_NOMBRE'),
            DB::raw('v.COD_PROD'),
                 DB::raw('PRODUCTOS.MARCA AS LINEA'), 
            DB::raw('e.CODIGO AS MARCA')) 
            ->whereBetween('v.FECALTAS', [$inicio , $final])
            ->whereIn('ven.VENDEDOR', $datos['Categorias'])
            ->where([
                ['v.ID_SUCURSAL', '=', $sucursal],
                ['v.ANULADO', '<>', 1],
                ['v.DESCRIPCION', 'LIKE', 'DEVOLUCION%'],
            ])
            ->groupBy('v.COD_PROD')
            ->get()
            ->toArray(); 
        } else  {

            $ventasdet = DB::connection('retail')->table('VENTASDET as v')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
            ->leftjoin('ventas AS ven',function($join){
          $join->on('ven.CODIGO','=','v.CODIGO')
               ->on('ven.ID_SUCURSAL','=','v.ID_SUCURSAL')
                ->on('ven.CAJA','=','v.CAJA');
               })
                ->leftjoin('EMPlEADOS AS e',function($join){
          $join->on('e.CODIGO','=','ven.VENDEDOR')
               ->on('e.ID_SUCURSAL','=','v.ID_SUCURSAL');
               })
            ->select(DB::raw('SUM(v.PRECIO) AS PRECIO'),
            DB::raw('SUM(v.CANTIDAD) AS VENDIDO'),
            DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = v.COD_PROD) AND (l.ID_SUCURSAL = v.ID_SUCURSAL)) Group By v.COD_PROD),0) AS STOCK'),
            DB::raw('e.NOMBRE AS MARCA_NOMBRE'),
            DB::raw('MARCA.DESCRIPCION AS LINEA_NOMBRE'),
            DB::raw('v.COD_PROD'),
            DB::raw('PRODUCTOS.MARCA AS LINEA'), 
            DB::raw('e.CODIGO AS MARCA'))  
            ->whereBetween('v.FECALTAS', [$inicio , $final])
            ->whereIn('PRODUCTOS.MARCA', $datos['Marcas'])
            ->whereIn('ven.VENDEDOR', $datos['Categorias'])
            ->where([
                ['v.ID_SUCURSAL', '=', $sucursal],
                ['v.ANULADO', '<>', 1],
                ['v.DESCRIPCION', 'LIKE', 'DEVOLUCION%'],
            ])
            ->groupBy('v.COD_PROD')
            ->get()
            ->toArray(); 
        }

        /*  --------------------------------------------------------------------------------- */

        

       
        /*  --------------------------------------------------------------------------------- */

        

            /*  --------------------------------------------------------------------------------- */

            /*  *********** EMPEZAR A MODIFICAR LOS VALORES DEL ARRAY *********** */

            /*  --------------------------------------------------------------------------------- */
        

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

//            if (array_key_exists($value->MARCA.''.$value->LINEA, $categorias))   {

                // CARGAR STOCK GENERAL CATEGORIA

               // $categorias[$value->MARCA.''.$value->LINEA]["STOCK_G"] += $value->CANTIDAD;
           // }

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
}
