<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Stock;
use App\Producto;
use App\ComprasDet;
use App\Common;
use App\Parametro;

class Transferencia extends Model
{
    public static function generarConsulta($datos) 
    {

        
         /*  --------------------------------------------------------------------------------- */

         // INCICIAR VARIABLES 

        $marcas[] = array();
        $categorias[] = array();
        $totales[] = array();

        $inicio = date('Y-m-d', strtotime($datos['Inicio']));
        $final = date('Y-m-d', strtotime($datos['Final']));
        $sucursalOrigen[] = $datos['SucursalOrigen'];
        $sucursalDestino = $datos['SucursalDestino'];
        $estatus = $datos['Estatus'];
        
        /*  --------------------------------------------------------------------------------- */

        /*  *********** TODAS LAS TRANSFERENCIAS ENTRE LAS FECHAS INTERVALOS *********** */

        if ($datos['AllCategory'] AND $datos['AllBrand']) {

            $transferencias_det = DB::connection('retail')->table('transferencias_det as td')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'td.CODIGO_PROD')
            ->leftJoin('transferencias as t', function($join){
			    $join->on('t.CODIGO', '=', 'td.CODIGO')
			         ->on('t.ID_SUCURSAL', '=', 'td.ID_SUCURSAL');
			})
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->select(DB::raw('SUM(td.PRECIO * t.CAMBIO) AS PRECIO'),
            DB::raw('SUM(td.CANTIDAD) AS CANTIDAD'),
            DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = td.CODIGO_PROD) AND (l.ID_SUCURSAL = t.SUCURSAL_DESTINO))),0) AS STOCK'),
            DB::raw('MARCA.DESCRIPCION AS MARCA_NOMBRE'),
            DB::raw('LINEAS.DESCRIPCION AS LINEA_NOMBRE'),
            DB::raw('td.CODIGO_PROD'),
            DB::raw('t.CAMBIO'),
            DB::raw('PRODUCTOS.MARCA AS MARCA'),
            DB::raw('PRODUCTOS.LINEA AS LINEA'))
            ->whereIn('t.SUCURSAL_ORIGEN', $datos['SucursalOrigen'])  
            ->whereBetween('t.FECMODIF', [$inicio , $final])
            ->where([
                ['t.SUCURSAL_DESTINO', '=', $sucursalDestino],
                ['t.ESTATUS', '=', $estatus],
            ])
            ->groupBy('td.CODIGO_PROD')
            ->get()
            ->toArray(); 

        } else if ($datos['AllCategory']) {
            
            $transferencias_det = DB::connection('retail')->table('transferencias_det as td')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'td.CODIGO_PROD')
            ->leftJoin('transferencias as t', function($join){
			    $join->on('t.CODIGO', '=', 'td.CODIGO')
			         ->on('t.ID_SUCURSAL', '=', 'td.ID_SUCURSAL');
			})
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->select(DB::raw('SUM(td.PRECIO * t.CAMBIO) AS PRECIO'),
            DB::raw('SUM(td.CANTIDAD) AS CANTIDAD'),
            DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = td.CODIGO_PROD) AND (l.ID_SUCURSAL = t.SUCURSAL_DESTINO))),0) AS STOCK'),
            DB::raw('MARCA.DESCRIPCION AS MARCA_NOMBRE'),
            DB::raw('LINEAS.DESCRIPCION AS LINEA_NOMBRE'),
            DB::raw('td.CODIGO_PROD'),
            DB::raw('PRODUCTOS.MARCA AS MARCA'),
            DB::raw('PRODUCTOS.LINEA AS LINEA'))  
            ->whereBetween('t.FECMODIF', [$inicio , $final])
            ->whereIn('PRODUCTOS.MARCA', $datos['Marcas'])
            ->where([
                ['t.SUCURSAL_ORIGEN', '=', $sucursalOrigen],
                ['t.SUCURSAL_DESTINO', '=', $sucursalDestino],
                ['t.ESTATUS', '=', $estatus],
            ])
            ->groupBy('td.CODIGO_PROD')
            ->get()
            ->toArray();

        } else if ($datos['AllBrand']) {
             
            $transferencias_det = DB::connection('retail')->table('transferencias_det as td')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'td.CODIGO_PROD')
            ->leftJoin('transferencias as t', function($join){
			    $join->on('t.CODIGO', '=', 'td.CODIGO')
			         ->on('t.ID_SUCURSAL', '=', 'td.ID_SUCURSAL');
			})
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->select(DB::raw('SUM(td.PRECIO * t.CAMBIO) AS PRECIO'),
            DB::raw('SUM(td.CANTIDAD) AS CANTIDAD'),
            DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = td.CODIGO_PROD) AND (l.ID_SUCURSAL = t.SUCURSAL_DESTINO))),0) AS STOCK'),
            DB::raw('MARCA.DESCRIPCION AS MARCA_NOMBRE'),
            DB::raw('LINEAS.DESCRIPCION AS LINEA_NOMBRE'),
            DB::raw('td.CODIGO_PROD'),
            DB::raw('PRODUCTOS.MARCA AS MARCA'),
            DB::raw('PRODUCTOS.LINEA AS LINEA'))  
            ->whereBetween('t.FECMODIF', [$inicio , $final])
            ->whereIn('PRODUCTOS.LINEA', $datos['Categorias'])
            ->where([
                ['t.SUCURSAL_ORIGEN', '=', $sucursalOrigen],
                ['t.SUCURSAL_DESTINO', '=', $sucursalDestino],
                ['t.ESTATUS', '=', $estatus],
            ])
            ->groupBy('td.CODIGO_PROD')
            ->get()
            ->toArray();
             
        } else  {

        	$transferencias_det = DB::connection('retail')->table('transferencias_det as td')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'td.CODIGO_PROD')
            ->leftJoin('transferencias as t', function($join){
			    $join->on('t.CODIGO', '=', 'td.CODIGO')
			         ->on('t.ID_SUCURSAL', '=', 'td.ID_SUCURSAL');
			})
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->select(DB::raw('SUM(td.PRECIO * t.CAMBIO) AS PRECIO'),
            DB::raw('SUM(td.CANTIDAD) AS CANTIDAD'),
            DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = td.CODIGO_PROD) AND (l.ID_SUCURSAL = t.SUCURSAL_DESTINO))),0) AS STOCK'),
            DB::raw('MARCA.DESCRIPCION AS MARCA_NOMBRE'),
            DB::raw('LINEAS.DESCRIPCION AS LINEA_NOMBRE'),
            DB::raw('td.CODIGO_PROD'),
            DB::raw('PRODUCTOS.MARCA AS MARCA'),
            DB::raw('PRODUCTOS.LINEA AS LINEA'))  
            ->whereBetween('t.FECMODIF', [$inicio , $final])
            ->whereIn('PRODUCTOS.MARCA', $datos['Marcas'])
            ->whereIn('PRODUCTOS.LINEA', $datos['Categorias'])
            ->where([
                ['t.SUCURSAL_ORIGEN', '=', $sucursalOrigen],
                ['t.SUCURSAL_DESTINO', '=', $sucursalDestino],
                ['t.ESTATUS', '=', $estatus],
            ])
            ->groupBy('td.CODIGO_PROD')
            ->get()
            ->toArray();

        }


        /*  --------------------------------------------------------------------------------- */

        //  CAMBIAR A GUARANIES

        // foreach ($ventasdet as $key => $value) {
        //     if ($value->CAMBIO > 1) {
        //     	$ventasdet[$key] = $value->
        //     }
        // }

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
        foreach ($transferencias_det as $key => $value) {


            /*  --------------------------------------------------------------------------------- */

            // CREAR ARRAY DE MARCAS

            if (array_key_exists($value->MARCA, $marcas))   {
                $marcas[$value->MARCA]["TOTAL"] += $value->PRECIO;
                $marcas[$value->MARCA]["STOCK"] += $value->STOCK;
                $marcas[$value->MARCA]["CANTIDAD"] += $value->CANTIDAD;
            } else {
                $marcas[$value->MARCA]["CODIGO"] = $value->MARCA;
                $marcas[$value->MARCA]["MARCA"] = $value->MARCA_NOMBRE;
                $marcas[$value->MARCA]["STOCK"] = $value->STOCK;
                $marcas[$value->MARCA]["TOTAL"] = $value->PRECIO;
                $marcas[$value->MARCA]["CANTIDAD"] = $value->CANTIDAD;
                $marcas[$value->MARCA]["STOCK_G"] = 0;
            }

             /*  --------------------------------------------------------------------------------- */

            // CREAR ARRAY DE CATEGORIAS

            if (array_key_exists($value->MARCA.''.$value->LINEA, $categorias))   {
                $categorias[$value->MARCA.''.$value->LINEA]["TOTAL"] += $value->PRECIO;
                $categorias[$value->MARCA.''.$value->LINEA]["STOCK"] += $value->STOCK;
                $categorias[$value->MARCA.''.$value->LINEA]["CANTIDAD"] += $value->CANTIDAD;
            } else {
                $categorias[$value->MARCA.''.$value->LINEA]["CODIGO"] = $value->LINEA;
                $categorias[$value->MARCA.''.$value->LINEA]["LINEA"] = $value->LINEA_NOMBRE;
                $categorias[$value->MARCA.''.$value->LINEA]["STOCK"] = $value->STOCK;
                $categorias[$value->MARCA.''.$value->LINEA]["TOTAL"] = $value->PRECIO;
                $categorias[$value->MARCA.''.$value->LINEA]["MARCA"] = $value->MARCA;
                $categorias[$value->MARCA.''.$value->LINEA]["CANTIDAD"] = $value->CANTIDAD;
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
            ->where('l.ID_SUCURSAL', '=', $sucursalDestino)
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

        return ['ventas' => $transferencias_det, 'marcas' => (array)$marca[0], 'categorias' => (array)$categoria[0]];

        /*  --------------------------------------------------------------------------------- */
    }

    public static function guardar($datos) 
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 
        
        $dia = date("Y-m-d");
        $hora = date("H:i:s");

        $c = 0;
        $filas = $datos["data"]["length"];
        $cod_prod = '';
        $cantidad = 0;
        $cantidad_total = 0;
        $precio = 0;
        $iva = 0;
        $base5 = 0;
        $base10 = 0;
        $exentas = 0;
        $total = 0;
        $sin_stock = [];
        $respuesta_FK_CD = [];
        $cantidad_FK_CD = 1;
        $todos_guardados = true;

        $tr_gravada = 0;
        $tr_iva = 0;
        $tr_exenta = 0;
        $tr_base5 = 0;
        $tr_base10 = 0;

        $tr_dt_gravada = 0;
        $tr_dt_iva = 0;
        $tr_dt_exenta = 0;
        $tr_dt_base5 = 0;
        $tr_dt_base10 = 0;
        $tr_dt_total = 0;

        /*  --------------------------------------------------------------------------------- */

        // INSERTAR TRANSFERENCIA 

        $transferencias_det = DB::connection('retail')
        ->table('transferencias')
        ->insert(
            [
            'CODIGO' => 33333, 
            'SUCURSAL_ORIGEN' => $datos["data"][$c]["ITEM"],
            'SUCURSAL_DESTINO' => $cod_prod,
            'DIRECCION' =>  $producto['producto'][0]->CODIGO_INTERNO,
            'TELEFONO' => $datos["data"][$c]["DESCRIPCION"],
            'FECHA' => $datos["data"][$c]["ITEM"],
            'HORA' => $cantidad,
            'ENVIA' => Common::quitar_coma($datos["data"][$c]["PRECIO"], $parametro['parametros'][0]->CANDEC),
            'TRANSPORTA' => $tr_exenta,
            'RECIBE' => $tr_gravada,
            'NRO_CAJA' => $tr_dt_iva,
            'SUB_TOTAL' => $tr_dt_total,
            'IVA' => 0,
            'TOTAL' => $tr_dt_base5,
            'ENVIADO' => $tr_dt_base10,
            'DEVUELTO' => 'NO',
            'MONEDA' => $user->name,
            'USERALTAS' =>  $dia,
            'FECALTAS' =>  $dia,
            'HORALTAS' =>  $hora,
            'ID_SUCURSAL' => $user->id_sucursal,
            'ESTATUS' => 0,
            'CAMBIO' => 
            ]
        );

        /*  --------------------------------------------------------------------------------- */
        
        // PARAMETRO 
        
        $parametro = Parametro::mostrarParametro();

        /*  --------------------------------------------------------------------------------- */

        // RECORRER TODAS LAS FILAS DEL DATATABLE

         while($c < $filas) {

                /*  --------------------------------------------------------------------------------- */

                // INICIAR DATOS 

                $cod_prod = $datos["data"][$c]["CODIGO"];
                $cantidad = Common::quitar_coma($datos["data"][$c]["CANTIDAD"], $parametro['parametros'][0]->CANDEC);
                $cantidad_total = $cantidad;
                $iva = Common::quitar_coma($datos["data"][$c]["IVA"], $parametro['parametros'][0]->CANDEC);

                /*  --------------------------------------------------------------------------------- */

                // REALIZAR CALCULOS 

                if ($datos["data"][$c]["IVA_PORCENTAJE"] === 5) {
                    $base5 = Common::quitar_coma($datos["data"][$c]["IVA"], $parametro['parametros'][0]->CANDEC);
                } else if ($datos["data"][$c]["IVA_PORCENTAJE"] === 10) {
                    $base10 = Common::quitar_coma($datos["data"][$c]["IVA"], $parametro['parametros'][0]->CANDEC);
                } else {
                    $exentas = Common::quitar_coma($datos["data"][$c]["TOTAL"], $parametro['parametros'][0]->CANDEC);
                }

                $precio = Common::quitar_coma($datos["data"][$c]["PRECIO"], $parametro['parametros'][0]->CANDEC);
                
                $gravadas = Common::quitar_coma($datos["data"][$c]["TOTAL"], $parametro['parametros'][0]->CANDEC) - Common::quitar_coma($datos["data"][$c]["IVA"], $parametro['parametros'][0]->CANDEC);
                $total = Common::quitar_coma($datos["data"][$c]["TOTAL"], $parametro['parametros'][0]->CANDEC); 

                $tr_gravada = Common::quitar_coma(($gravadas/$cantidad), $parametro['parametros'][0]->CANDEC);
                $tr_iva = Common::quitar_coma(($iva/$cantidad), $parametro['parametros'][0]->CANDEC);
                $tr_exenta = Common::quitar_coma(($exentas/$cantidad), $parametro['parametros'][0]->CANDEC);
                $tr_base5 = Common::quitar_coma(($base5/$cantidad), $parametro['parametros'][0]->CANDEC);
                $tr_base10 = Common::quitar_coma(($base10/$cantidad), $parametro['parametros'][0]->CANDEC);    
                $tr_dt_total = Common::quitar_coma(($total/$cantidad), $parametro['parametros'][0]->CANDEC); 

                /*  --------------------------------------------------------------------------------- */

                // OBTENER DATOS FALTANTES 

                $producto = Producto::datosVariosProducto($cod_prod);

                /*  --------------------------------------------------------------------------------- */

                // COMPROBAR SI EXISTE STOCK 

                if (Stock::comprobar_stock_producto($cod_prod, $cantidad) === true){

                    /*  --------------------------------------------------------------------------------- */ 

                    // RECORRER HASTA TERMINAR 

                    while ($cantidad_FK_CD > 0) {
                        
                        /*  --------------------------------------------------------------------------------- */

                        // RESTAR STOCK DEL PRODUCTO

                        $respuesta_resta = Stock::restar_stock_producto_FK_CD($cod_prod, $cantidad);
                        $cantidad_FK_CD = $respuesta_resta['cantidad'];
                        $cantidad = $cantidad - $cantidad_FK_CD;
                        
                        /*  --------------------------------------------------------------------------------- */

                        // CALCULAR POR DETALLE 

                        $tr_dt_gravada = $tr_gravada * $cantidad;
                        $tr_dt_iva = $tr_iva * $cantidad;
                        $tr_dt_exenta = $tr_exenta * $cantidad;
                        $tr_dt_base5 = $tr_base5 * $cantidad;
                        $tr_dt_base10 = $tr_base10 * $cantidad;

                        if ($cantidad_FK_CD === 0) {
                            $tr_dt_gravada = $gravadas - (($cantidad_total - $cantidad) * $tr_gravada);
                            $tr_dt_iva = $iva - (($cantidad_total - $cantidad) * $tr_iva);
                            $tr_dt_exenta = $exentas - (($cantidad_total - $cantidad) * $tr_exenta);
                            $tr_dt_base5 = $base5 - (($cantidad_total - $cantidad) * $tr_base5);
                            $tr_dt_base10 = $base10 - (($cantidad_total - $cantidad) * $tr_base10);
                        }

                        /*  --------------------------------------------------------------------------------- */

                        // OBTENER FK_CD

                        $fk_cd = ComprasDet::id_cd($cod_prod, $respuesta_resta['lote']);

                        /*  --------------------------------------------------------------------------------- */

                        // INSERTAR TRANSFERENCIA DET 

                        $transferencias_det = DB::connection('retail')
                        ->table('transferencias_det')
                        ->insert(
                            [
                            'CODIGO' => 33333, 
                            'ITEM' => $datos["data"][$c]["ITEM"],
                            'CODIGO_PROD' => $cod_prod,
                            'CODIGO_INTERNO' =>  $producto['producto'][0]->CODIGO_INTERNO,
                            'DESCRIPCION' => $datos["data"][$c]["DESCRIPCION"],
                            'TIPO' => $datos["data"][$c]["ITEM"],
                            'CANTIDAD' => $cantidad,
                            'PRECIO' => Common::quitar_coma($datos["data"][$c]["PRECIO"], $parametro['parametros'][0]->CANDEC),
                            'EXENTAS' => $tr_exenta,
                            'GRABADAS' => $tr_gravada,
                            'IVA' => $tr_dt_iva,
                            'TOTAL' => $tr_dt_total,
                            'DESCUENTO' => 0,
                            'BASE5' => $tr_dt_base5,
                            'BASE10' => $tr_dt_base10,
                            'DEVUELTO' => 'NO',
                            'USERALTAS' => $user->name,
                            'FECALTAS' =>  $dia,
                            'HORALTAS' =>  $hora,
                            'ID_SUCURSAL' => $user->id_sucursal,
                            'FK_CD' => $fk_cd
                            ]
                        );

                        var_dump($transferencias_det);

                        /*  --------------------------------------------------------------------------------- */

                    }

                } else {

                    /*  --------------------------------------------------------------------------------- */ 

                    // SI NO HAY STOCK SE GUARDDARA EN ESTE ARRAY EL CODIGO Y SE ACTIVARA LA VARIABLE TODOS GUARDADOS

                    $sin_stock[] = $cod_prod;
                    $todos_guardados = false;

                    /*  --------------------------------------------------------------------------------- */ 

                }

                /*  --------------------------------------------------------------------------------- */
                
                // AUMENTAR CONTADOR 

                $c++;

                /*  --------------------------------------------------------------------------------- */
         }
        
        /*  --------------------------------------------------------------------------------- */ 
    }
}
