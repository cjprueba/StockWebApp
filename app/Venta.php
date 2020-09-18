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
use App\VentasDetDevolucion;
use App\Ventas_Descuento;
use App\VentaCredito;

class Venta extends Model
{
    protected $connection = 'retail';
    public $timestamps = false;

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
    public static function generarReporteVenta($datos) 
    {

        
         /*  --------------------------------------------------------------------------------- */

         // INCICIAR VARIABLES 
      $insert=$datos["data"]["Insert"];
        $marcas[] = array();
        $categorias[] = array();
        $totales[] = array();
        $marcas_array=array();
         $marcas_categoria_array=array();
          $marcas_productos_array=array();
                $user = auth()->user();
         $user=$user->id;
        $inicio = date('Y-m-d', strtotime($datos["data"]['Inicio']));
        $final = date('Y-m-d', strtotime($datos["data"]['Final']));
        $sucursal = $datos["data"]['Sucursal'];
                    $total_general=0;
                   $total_descuento=0;
                   $total_preciounit=0;
                   $cantidadvendida=0;
                   $costo=0;
                   $totalcosto=0;

                if($insert==true){
                    $datos=array(
                'inicio'=> date('Y-m-d', strtotime($datos["data"]['Inicio'])),
                'final'=>date('Y-m-d', strtotime($datos["data"]['Final'])),
                'sucursal'=>$datos["data"]['Sucursal'],
                'checkedCategoria'=>$datos["data"]['AllCategory'],
                'checkedMarca'=>$datos["data"]['AllBrand'],
                'marcas'=>$datos["data"]['Marcas'],
                'linea'=>$datos["data"]['Categorias']
            );
           Temp_venta::insertar_reporte($datos);
                }
        // CARGAR MARCAS 0 EN VENTAS

        //array_unshift($datos["data"]['Marcas'], 0);
        //var_dump($datos["data"]['Marcas']);

                       $temp=DB::connection('retail')->table('temp_ventas')
                
                       ->select(
                        DB::raw('temp_ventas.MARCAS_CODIGO AS MARCA'),
                        DB::raw('temp_ventas.MARCA AS DESCRI_M'), 
                        DB::raw('SUM(temp_ventas.VENDIDO) AS VENDIDO'),
                        DB::raw('SUM(temp_ventas.DESCUENTO) AS DESCUENTO'),
                        DB::raw('SUM(COSTO_TOTAL) AS COSTO_TOTAL'),
                        DB::raw('SUM(COSTO_UNIT) AS COSTO_UNIT'),
                        DB::raw('SUM(temp_ventas.PRECIO) AS TOTAL'),
                        DB::raw('SUM(temp_ventas.PRECIO_UNIT) AS PRECIO_UNIT'))
                      ->where('USER_ID','=',$user)
                      ->where('ID_SUCURSAL','=',$sucursal)
                      ->GROUPBY('temp_ventas.MARCAS_CODIGO') 
                      ->orderby('temp_ventas.MARCA')
                      ->get()
                      ->toArray();

                      
                         

                        foreach ($temp as $key => $value) {



                              
                               $total_general=$total_general+$value->TOTAL;
                               $total_descuento=$total_descuento+$value->DESCUENTO;
                               $total_preciounit=$total_preciounit+$value->PRECIO_UNIT;
                               $cantidadvendida=$cantidadvendida+$value->VENDIDO;
                               $costo=$costo+$value->COSTO_UNIT;
                               $totalcosto=$totalcosto+$value->COSTO_TOTAL;
                                  $marcas_array[]=array(
                                'TOTALES'=> $value->DESCRI_M,
                                'VENDIDO'=> $value->VENDIDO,
                                'DESCUENTO'=>$value->DESCUENTO,
                                'COSTO'=> $value->COSTO_UNIT,
                                'COSTO TOTAL'=> $value->COSTO_TOTAL,
                                'PRECIO'=> $value->PRECIO_UNIT,
                                'TOTAL'=> $value->TOTAL,
                                'MARCAS'=>$value->MARCA
                                
                                );
                            # code...
                        }

                             $ser=DB::connection('retail')->table('ventasdet_servicios')
                             ->leftjoin('VENTAS',function($join){
                             $join->on('VENTAS.CODIGO','=','ventasdet_servicios.CODIGO')
                             ->on('VENTAS.CAJA','=','ventasdet_servicios.CAJA')
                             ->on('VENTAS.ID_SUCURSAL','=','ventasdet_servicios.ID_SUCURSAL');
                             })
                             ->leftjoin('VENTAS_ANULADO', 'VENTAS_ANULADO.FK_VENTA', '=', 'VENTAS.ID')
                             ->select(DB::raw('SUM(ventasdet_servicios.PRECIO) AS PRECIO_SERVICIO,
                                    sum(ventasdet_servicios.CANTIDAD) AS VENDIDO,
                                    sum(ventasdet_servicios.PRECIO_UNIT) AS PRECIO_UNIT')) 
                             ->Where('VENTAS_ANULADO.ANULADO','=',0)
                             ->Where('VENTAS.ID_SUCURSAL','=',$sucursal)
                             ->whereBetween('VENTAS.FECALTAS', [$inicio, $final])
                             ->get()
                             ->toArray();
                              if(count($ser)>0){
                            
                                $total_general=$total_general+$ser[0]->PRECIO_SERVICIO;
                               $total_preciounit=$total_preciounit+$ser[0]->PRECIO_UNIT;
                               $cantidadvendida=$cantidadvendida+$ser[0]->VENDIDO;
                              
                                   $marcas_array[]=array(
                                'TOTALES'=> 'SERVICIO DE DELIVERY',
                                'VENDIDO'=> $ser[0]->VENDIDO,
                                'DESCUENTO'=>'0',
                                'COSTO'=> '0',
                                'COSTO TOTAL'=> '0',
                                'PRECIO'=> $ser[0]->PRECIO_UNIT,
                                'TOTAL'=> $ser[0]->PRECIO_SERVICIO,
                                
                                );
                              }

   
                                 //TOTALES POR CATEGORIA AGRUPADOS POR MARCA
                                 //---------------------------------------------------------------------------------------------------------
                              
                   
                             $temp=DB::connection('retail')->table('temp_ventas')
                        
                               ->select(
                                DB::raw('temp_ventas.MARCAS_CODIGO AS MARCA'),
                                DB::raw('temp_ventas.MARCA AS DESCRI_M'),
                                DB::raw('temp_ventas.LINEA_CODIGO AS LINEA'),
                                DB::raw('SUM(temp_ventas.VENDIDO) AS VENDIDO'),
                                DB::raw('SUM(temp_ventas.DESCUENTO) AS DESCUENTO'),
                                DB::raw('SUM(COSTO_TOTAL) AS COSTO_TOTAL'),
                                DB::raw('SUM(temp_ventas.PRECIO) AS TOTAL'),
                                DB::raw('temp_ventas.CATEGORIA as DESCRI_L'))
                              ->where('USER_ID','=',$user)
                              ->where('ID_SUCURSAL','=',$sucursal)
                             
                              ->GROUPBY('temp_ventas.MARCAS_CODIGO','temp_ventas.LINEA_CODIGO') 
                              ->orderby('temp_ventas.MARCA')
                              ->get()
                              ->toArray();
                             
                             foreach ($temp as $key => $value) {
                                      $marcas_categoria_array[]=array(
                                    'MARCA'=> $value->MARCA,
                                    'DESCRI_M'=>$value->DESCRI_M,
                                    'LINEA'=> $value->LINEA,
                                    'DESCRI_L'=>$value->DESCRI_L,
                                    'VENDIDO'=> $value->VENDIDO,
                                    'COSTO_TOTAL'=> $value->COSTO_TOTAL,
                                    'TOTAL'=> $value->TOTAL,
                                    'DESCUENTO'=>$value->DESCUENTO,
                                    
                                    );
                                 # code...
                             }

                   //-------------------------------------------------------------------------------------------------------------------  
                   //TRAER TODOS LOS PRODUCTOS CON EL CODIGO DE MARCA
                   //-------------------------------------------------------------------------------------------------------------------
                   
                  $temp=DB::connection('retail')->table('temp_ventas')
                 
                   ->select(
                    DB::raw('temp_ventas.COD_PROD AS COD_PROD'),
                    DB::raw('temp_ventas.LOTE AS LOTE'),
                    DB::raw('SUM(temp_ventas.VENDIDO) AS VENDIDO'),
                    DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = temp_ventas.COD_PROD) AND (l.ID_SUCURSAL = temp_ventas.ID_SUCURSAL))),0) AS STOCK'),
                    DB::raw('SUM(temp_ventas.DESCUENTO) AS DESCUENTO'),
                    DB::raw('SUM(COSTO_TOTAL) AS COSTO_TOTAL'),
                    DB::raw('COSTO_UNIT AS COSTO_UNIT'),
                    DB::raw('SUM(temp_ventas.PRECIO) AS TOTAL'),
                    DB::raw('temp_ventas.PRECIO_UNIT AS PRECIO_UNIT'),
                    DB::raw('temp_ventas.CATEGORIA AS CATEGORIA'),
                    DB::raw('temp_ventas.SUBCATEGORIA AS SUBCATEGORIA'),
                    DB::raw('temp_ventas.MARCA AS MARCA'),
                     DB::raw('temp_ventas.MARCAS_CODIGO AS MARCAS_CODIGO'),
                    DB::raw('temp_ventas.DESCUENTO_PORCENTAJE AS DESCUENTO_PORCENTAJE'),
                    DB::raw('temp_ventas.DESCUENTO_PRODUCTO AS DESCUENTO_PRODUCTO'))
                  ->where('temp_ventas.ID_SUCURSAL','=',$sucursal)
                  ->where('temp_ventas.USER_ID','=',$user)
                  ->GROUPBY('temp_ventas.COD_PROD','temp_ventas.LOTE','temp_ventas.DESCUENTO_PRODUCTO') 
                  ->orderby('COD_PROD')
                  ->get()
                  ->toArray();
                   $total_general=0;
                   $total_descuento=0;
                   $total_preciounit=0;
                   $cantidadvendida=0;
                   $costo=0;
                   $totalcosto=0;
           
                  foreach ($temp as $key => $value) {
                    if($value->TOTAL==0){
                    $value->TOTAL='0';
                    }
                    if($value->PRECIO_UNIT==0){
                        $value->PRECIO_UNIT='0';
                    }
                    if($value->STOCK==0){
                        $value->STOCK='0';
                    }
                    if($value->DESCUENTO==0){
                        $value->DESCUENTO='0';
                    }
                   
                   $total_general=$total_general+$value->TOTAL;
                   $total_descuento=$total_descuento+$value->DESCUENTO;
                   $total_preciounit=$total_preciounit+$value->PRECIO_UNIT;
                   $cantidadvendida=$cantidadvendida+$value->VENDIDO;
                  $costo=$costo+$value->COSTO_UNIT;
                  $totalcosto=$totalcosto+$value->COSTO_TOTAL;
                  
                            $marcas_productos_array[]=array(
                         
                        'COD_PROD'=> $value->COD_PROD,
                        'LOTE'=> $value->LOTE,
                        'STOCK'=> $value->STOCK,
                        'CATEGORIA'=> $value->CATEGORIA,
                        'SUBCATEGORIA'=> $value->SUBCATEGORIA,
                        'MARCA'=> $value->MARCA,
                        'VENDIDO'=> $value->VENDIDO,
                        'PRECIO_UNIT'=>$value->PRECIO_UNIT,
                        'TOTAL'=>$value->TOTAL,
                        'DESCUENTO'=>$value->DESCUENTO,
                        'COSTO_UNIT'=>$value->COSTO_UNIT,
                        'COSTO_TOTAL'=>$value->COSTO_TOTAL,
                        'DESCUENTO_PORCENTAJE'=> $value->DESCUENTO_PRODUCTO,
                        'MARCAS_CODIGO'=> $value->MARCAS_CODIGO
                        
                        

                      
                      

                    );
                  }


                  



                 
 
        /*  --------------------------------------------------------------------------------- */


        /*  --------------------------------------------------------------------------------- */



        /*  --------------------------------------------------------------------------------- */

        // RETORNAR TODOS LOS ARRAYS


        return ['ventas' => $marcas_productos_array, 'marcas' => $marcas_array, 'categorias' => $marcas_categoria_array];

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

            // INICIAR VARIABLE FORMA DE COBRO

            $pago_al_entregar = $data["data"]["pago"]["PAGO_AL_ENTREGAR"];
            $tipo_venta = 'CO';
            $estatus_venta = 0;

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

            // REVISAR DINERO Y QUITARLE VUELTO SI EL PAGO NO ES AL ENTREGAR

            $opcion_vuelto = $data["data"]["pago"]["OPCION_VUELTO"];

            if ($opcion_vuelto === '1' && $pago_al_entregar === false) {
                $guaranies = $guaranies - Common::quitar_coma($data["data"]["pago"]["VUELTO"]["guaranies"] , 2);
            } else if ($opcion_vuelto === '2' && $pago_al_entregar === false) {
                $dolares = $dolares - Common::quitar_coma($data["data"]["pago"]["VUELTO"]["dolares"] , 2);
            } else if ($opcion_vuelto === '3' && $pago_al_entregar === false) {
                $reales = $reales - Common::quitar_coma($data["data"]["pago"]["VUELTO"]["reales"] , 2);
            } else if ($opcion_vuelto === '4' && $pago_al_entregar === false) {
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

            
            $codigo_caja = $data["data"]["cabecera"]["CODIGO_CAJA"];
            $caja = $data["data"]["caja"]["CODIGO"];
            $numero_caja = 'CA'.sprintf("%02d", $caja).'';
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

            // DESCUENTO GENERAL 

            $descuento_general = Common::quitar_coma($data["data"]["pago"]["DESCUENTO_GENERAL"], $candec);
            $descuento_general_porcentaje = $data["data"]["pago"]["DESCUENTO_GENERAL_PORCENTAJE"];

            /*  --------------------------------------------------------------------------------- */

            // GIRO 

            $giro = Common::quitar_coma($data["data"]["pago"]["GIRO"], 0);
            $codigo_entidad = $data["data"]["pago"]["CODIGO_ENT"];

            /*  --------------------------------------------------------------------------------- */

            // VALE 

            $vale = Common::quitar_coma($data["data"]["pago"]["VALE"], 2);

            /*  --------------------------------------------------------------------------------- */

            // CREDITO

            $credito = Common::quitar_coma($data["data"]["pago"]["CREDITO"], 2);
            $dias_credito = $data["data"]["pago"]["DIAS_CREDITO"];
            $credito_fin = $data["data"]["pago"]["CREDITO_FIN"];

            /*  --------------------------------------------------------------------------------- */

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

                $id_ventasdet = Common::quitar_coma($data["data"]["productos"][$c]["ID"], 0);
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

                
                // DEVOLUCION

                } else if ($data["data"]["productos"][$c]["TIPO"] === 3) {

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

                    VentasDetDevolucion::insertGetId(
                            [
                            'FK_VENTASDET' => $id_ventasdet,     
                            'CODIGO' => $codigo, 
                            'CAJA' => $caja, 
                            'ITEM' => $c + 1, 
                            'COD_PROD' => $cod_prod, 
                            'DESCRIPCION' => $data["data"]["productos"][$c]["DESCRIPCION"], 
                            'LOTE' => $data["data"]["productos"][$c]["LOTE"], 
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
                            'USER' => $user->name, 
                            'FECALTAS' => $dia, 
                            'HORALTAS' => $hora, 
                            'ID_SUCURSAL' => $user->id_sucursal, 
                            'CODIGO_CA' => $codigo_caja
                            ]
                        );

                    /*  --------------------------------------------------------------------------------- */

                    // SUMAR STOCK 

                    Stock::sumar_stock_producto_devolucion($cod_prod, $cantidad, $id_ventasdet);
                    
                    /*  --------------------------------------------------------------------------------- */

                }

                /*  --------------------------------------------------------------------------------- */
                        
                // AUMENTAR CONTADOR 

                $c++;
                        
                /*  --------------------------------------------------------------------------------- */
            }

            /*  --------------------------------------------------------------------------------- */

            // CALCULAR TOTAL GENERAL IVA  CON DESCUENTO GENERAL 

            if ($descuento_general_porcentaje > 0) {
                $total_iva = $total_iva - (($total_iva * $descuento_general_porcentaje) / 100);
                $total_base10 = $total_base10 - (($total_base10 * $descuento_general_porcentaje) / 100);
                $total_base5 = $total_base5 - (($total_base5 * $descuento_general_porcentaje) / 100);
                $total_exentas = $total_exentas - (($total_exentas * $descuento_general_porcentaje) / 100);
                $total_gravadas = $total_gravadas - (($total_gravadas * $descuento_general_porcentaje) / 100);
            }

            /*  --------------------------------------------------------------------------------- */

            // REVISAR TIPO DE VENTA 

            if ($credito !== '0' && $credito !== '0.00' && $credito !== NULL) {

                /*  --------------------------------------------------------------------------------- */

                // TIPO CREDITO 

                $tipo_venta = 'CR';

                /*  --------------------------------------------------------------------------------- */

            } else if ($pago_al_entregar === true) {

                /*  --------------------------------------------------------------------------------- */

                // PARA FIJAR PAGO AL ENTREGAR 
                
                $tipo_venta = 'PE';
                $estatus_venta = 2;

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
                    "TIPO" => $tipo_venta, 
                    //"FORMA_PAGO" => '', 
                    "PLAN_PAGO" => 0, 
                    "TARJETA" => 0, 
                    "DESCUENTO" => ($descuento_total + $descuento_general), 
                    "GRAVADAS" => $total_gravadas, 
                    "IMPUESTOS" => $total_iva, 
                    "EXENTAS" => $total_exentas, 
                    "BASE5" => $total_base5, 
                    "BASE10" => $total_base10, 
                    "SUB_TOTAL" => ($total_gravadas + $total_exentas), 
                    "TOTAL" => ($total_total - $descuento_general), 
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
                    'ANULADO' => $estatus_venta,
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

            // INSERTAR PAGO VALE
            
            if ($vale !== '0' && $vale !== '0.00' && $vale !== NULL) {

                VentaVale::guardar_referencia([
                        'FK_VENTA' => $venta,
                        'MONTO' => $vale,
                        'MONEDA' => $moneda
                ]);
                
            }

            /*  --------------------------------------------------------------------------------- */

            // INSERTAR PAGO CREDITO
            
            if ($credito !== '0' && $credito !== '0.00' && $credito !== NULL) {

                VentaCredito::guardar_referencia([
                        'FK_VENTA' => $venta,
                        'MONTO' => $credito,
                        'MONEDA' => $moneda,
                        'DIAS_CREDITO' => $dias_credito,
                        'FECHA_CREDITO_FIN' => $credito_fin,
                        'SALDO' => $credito
                ]);
                
                /*  --------------------------------------------------------------------------------- */

                // OBTENER ID CLIENTE 

                $id_cliente = (Cliente::id_cliente($cliente))['ID_CLIENTE'];

                /*  --------------------------------------------------------------------------------- */

                // ACTUALIZAR CREDITO 

                Cliente::actualizarCredito($id_cliente);

                /*  --------------------------------------------------------------------------------- */

            }

            /*  --------------------------------------------------------------------------------- */

            // INSERTAR PAGO CHEQUES 

            $pago_cheque = VentaCheque::guardar_referencia($cheques, $venta);

            if ($pago_cheque["response"] === false) {
                return $pago_cheque;
            }

            /*  --------------------------------------------------------------------------------- */

            // INSERTAR DESCUENTO GENERAL

            if ($descuento_general_porcentaje > 0) {
                Ventas_Descuento::guardar_referencia($descuento_general_porcentaje, $descuento_general, $venta, $moneda);
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

    public static function numeracion($data) {
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
        $dia = date('Y-m-d');
        $hora = date("H:i:s");
        $numero_caja = $data["caja"];
        $caja = 'CA'.sprintf("%02d", $numero_caja).'';

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

        // NUMERO CPOR PRIMERA VEZ POR CAJA 

        if (count($numero) === 0) {
            $numero = 0;
        } else {
            $numero = $numero[0]['CODIGO'];
        }

        /*  --------------------------------------------------------------------------------- */ 

        // RETORNAR VALOR 

        return ['CODIGO' => $numero, 'CODIGO_CAJA' => $ticket];

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

        return ['CLIENTE' => $cliente[0], 'EMPLEADO' => $empleado[0], 'MONEDA' => $candec, 'LIMITE_MAYORISTA' => $parametro[0]['DESTINO'], 'IMPRESORA_TICKET' => 'EPSON TM-U220 Receipt', 'IMPRESORA_MATRICIAL' => 'Microsoft Print to PDF'];

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
                        CLIENTES.CELULAR,
                        CLIENTES.TELEFONO,
                        EMPLEADOS.NOMBRE AS VENDEDOR,
                        VENTAS.USER AS CAJERO,
                        VENTAS.EFECTIVO AS PAGO,
                        VENTAS.VUELTO,
                        VENTAS.DESCUENTO,
                        VENTAS.CODIGO_CA,
                        VENTAS.TIPO,
                        VENTAS.MONEDA,
                        VENTAS.TOTAL'
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

        // TELEFONO / CELULAR

        if ($venta[0]->TELEFONO === '' || $venta[0]->TELEFONO === null) {
            $venta[0]->TELEFONO = $venta[0]->CELULAR;
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

        // DEVOLUCIONES 

        $ventas_det_devoluciones = VentasDetDevolucion::select(DB::raw(
                        'VENTASDET_DEVOLUCIONES.ID,
                        VENTASDET_DEVOLUCIONES.ITEM, 
                        VENTASDET_DEVOLUCIONES.COD_PROD, 
                        VENTASDET_DEVOLUCIONES.DESCRIPCION, 
                        VENTASDET_DEVOLUCIONES.CANTIDAD, 
                        VENTASDET_DEVOLUCIONES.PRECIO_UNIT,
                        VENTASDET_DEVOLUCIONES.IVA,
                        VENTASDET_DEVOLUCIONES.EXENTA AS EXENTAS,
                        VENTASDET_DEVOLUCIONES.BASE5,
                        VENTASDET_DEVOLUCIONES.BASE10,
                        VENTASDET_DEVOLUCIONES.PRECIO,
                        VENTAS.MONEDA,
                        0 AS IVA_PORCENTAJE'
                    ))
        ->leftJoin('VENTAS', function($join){
                                $join->on('VENTAS.CODIGO', '=', 'VENTASDET_DEVOLUCIONES.CODIGO')
                                     ->on('VENTAS.ID_SUCURSAL', '=', 'VENTASDET_DEVOLUCIONES.ID_SUCURSAL')
                                     ->on('VENTAS.CAJA', '=', 'VENTASDET_DEVOLUCIONES.CAJA');
                            })
        ->where('VENTASDET_DEVOLUCIONES.ID_SUCURSAL','=', $user->id_sucursal)
        ->where('VENTASDET_DEVOLUCIONES.CODIGO','=', $codigo)
        ->where('VENTASDET_DEVOLUCIONES.CAJA','=', $caja)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        foreach ($ventas_det_devoluciones as $key => $value) {
            
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

        // DESCUENTO 

        $ventas_descuento = Ventas_Descuento::select(DB::raw(
                        'VENTAS_DESCUENTO.ID,
                        0 AS ITEM, 
                        1 AS COD_PROD, 
                        0 AS DESCRIPCION, 
                        1 AS CANTIDAD, 
                        VENTAS_DESCUENTO.TOTAL AS PRECIO_UNIT,
                        0 AS IVA,
                        0 AS EXENTAS,
                        0 AS BASE5,
                        0 AS BASE10,
                        VENTAS_DESCUENTO.TOTAL AS PRECIO,
                        VENTAS.MONEDA,
                        0 AS IVA_PORCENTAJE'
                    ))
        ->leftJoin('VENTAS', function($join){
                                $join->on('VENTAS.ID', '=', 'VENTAS_DESCUENTO.FK_VENTAS');
                            })
        ->where('VENTAS.ID_SUCURSAL','=', $user->id_sucursal)
        ->where('VENTAS.CODIGO','=', $codigo)
        ->where('VENTAS.CAJA','=', $caja)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        foreach ($ventas_descuento as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            $nestedData['ID'] = $value->ID;
            $nestedData['ITEM'] = $value->ITEM;
            $nestedData['COD_PROD'] = $value->COD_PROD;
            $nestedData['DESCRIPCION'] = 'DESCUENTO GENERAL';
            $nestedData['CANTIDAD'] = Common::precio_candec_sin_letra($value->CANTIDAD, 1);
            $nestedData['PRECIO_UNIT'] = Common::precio_candec_sin_letra($value->PRECIO_UNIT * -1, $value->MONEDA);
            $nestedData['IVA'] = Common::precio_candec_sin_letra($value->IVA * -1, $value->MONEDA);
            $nestedData['EXENTAS'] = $value->EXENTAS * -1;
            $nestedData['BASE5'] = $value->BASE5 * -1;
            $nestedData['BASE10'] = ($value->PRECIO * -1 / 11);
            $nestedData['PRECIO'] = Common::precio_candec_sin_letra($value->PRECIO * -1, $value->MONEDA);
            $nestedData['MONEDA'] = $value->MONEDA;
            $nestedData['IVA_PORCENTAJE'] = 0;

            /*  --------------------------------------------------------------------------------- */

            // CARGAR DATOS EN ARRAY

            $data[] = $nestedData;

            /*  --------------------------------------------------------------------------------- */

        }

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
        ->leftjoin('VENTAS_ANULADO', 'VENTAS.ID', '=', 'VENTAS_ANULADO.FK_VENTA')
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->where('VENTAS.FECHA', '=', $fecha)
        ->where('TIPO', '<>', 'CR')
        ->where('CAJA', '=', $dato['caja'])
        ->where('VENTAS_ANULADO.ANULADO', '=', 0)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // SUMAR TODOS LOS VALORES CREDITO

        $credito = Venta::select(DB::raw('IFNULL(SUM(TOTAL), 0) AS T_TOTAL'))
        ->leftjoin('VENTAS_ANULADO', 'VENTAS.ID', '=', 'VENTAS_ANULADO.FK_VENTA')
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->where('VENTAS.FECHA', '=', $fecha)
        ->where('TIPO', '=', 'CR')
        ->where('CAJA', '=', $dato['caja'])
        ->where('VENTAS_ANULADO.ANULADO', '=', 0)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // SUMAR TODOS LOS VALORES PAGO A ENTREGAR

        $pe = Venta::select(DB::raw('IFNULL(SUM(TOTAL), 0) AS T_TOTAL'))
        ->leftjoin('VENTAS_ANULADO', 'VENTAS.ID', '=', 'VENTAS_ANULADO.FK_VENTA')
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->where('VENTAS.FECHA', '=', $fecha)
        ->where('TIPO', '=', 'PE')
        ->where('CAJA', '=', $dato['caja'])
        ->where('VENTAS_ANULADO.ANULADO', '=', 2)
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
        ->leftjoin('VENTAS_ANULADO', 'VENTAS_TRANSFERENCIA.FK_VENTA', '=', 'VENTAS_ANULADO.FK_VENTA')
        ->where('VENTAS.ID_SUCURSAL', '=', $user->id_sucursal)
        ->where('VENTAS.FECHA', '=', $fecha)
        ->where('VENTAS.CAJA', '=', $dato['caja'])
        ->where('VENTAS_ANULADO.ANULADO', '=', 0)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // GIRO
        
        $giro = VentaGiro::select(DB::raw('IFNULL(SUM(VENTAS_GIRO.MONTO), 0) AS TOTAL'))
        ->leftjoin('VENTAS', 'VENTAS.ID', '=', 'VENTAS_GIRO.FK_VENTA')
        ->leftjoin('VENTAS_ANULADO', 'VENTAS_GIRO.FK_VENTA', '=', 'VENTAS_ANULADO.FK_VENTA')
        ->where('VENTAS.ID_SUCURSAL', '=', $user->id_sucursal)
        ->where('VENTAS.FECHA', '=', $fecha)
        ->where('VENTAS.CAJA', '=', $dato['caja'])
        ->where('VENTAS_ANULADO.ANULADO', '=', 0)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // VALE
        
        $vale = VentaVale::select(DB::raw('IFNULL(SUM(VENTAS_VALE.MONTO), 0) AS TOTAL'))
        ->leftjoin('VENTAS', 'VENTAS.ID', '=', 'VENTAS_VALE.FK_VENTA')
        ->leftjoin('VENTAS_ANULADO', 'VENTAS_VALE.FK_VENTA', '=', 'VENTAS_ANULADO.FK_VENTA')
        ->where('VENTAS.ID_SUCURSAL', '=', $user->id_sucursal)
        ->where('VENTAS.FECHA', '=', $fecha)
        ->where('VENTAS.CAJA', '=', $dato['caja'])
        ->where('VENTAS_ANULADO.ANULADO', '=', 0)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // CHEQUE
        
        $cheque = VentaCheque::select(DB::raw('IFNULL(SUM(VENTAS_CHEQUE.MONTO), 0) AS TOTAL, VENTAS_CHEQUE.MONEDA'))
        ->leftjoin('VENTAS', 'VENTAS.ID', '=', 'VENTAS_CHEQUE.FK_VENTA')
        ->leftjoin('VENTAS_ANULADO', 'VENTAS_CHEQUE.FK_VENTA', '=', 'VENTAS_ANULADO.FK_VENTA')
        ->where('VENTAS.ID_SUCURSAL', '=', $user->id_sucursal)
        ->where('VENTAS.FECHA', '=', $fecha)
        ->where('VENTAS.CAJA', '=', $dato['caja'])
        ->where('VENTAS_ANULADO.ANULADO', '=', 0)
        ->groupBy('VENTAS_CHEQUE.MONEDA')
        ->get();


        /*  --------------------------------------------------------------------------------- */

        // SUMAR ANULADOS

        $anulados = Venta::select('CODIGO')
        ->leftjoin('VENTAS_ANULADO', 'VENTAS.ID', '=', 'VENTAS_ANULADO.FK_VENTA')
        ->where('VENTAS_ANULADO.ANULADO', '=', 1)
        ->where('VENTAS.FECHA', '=', $fecha)
        ->where('CAJA', '=', $dato['caja'])
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->count();

        /*  --------------------------------------------------------------------------------- */

        // ************************************ ABONO *****************************************

        /*  --------------------------------------------------------------------------------- */
        
        // SUMAR TODOS LOS VALORES ABONO

        $abono = VentaAbono::select(DB::raw('IFNULL(SUM(PAGO), 0) AS T_TOTAL'))
        ->where('FK_SUCURSAL', '=', $user->id_sucursal)
        ->whereDate('FECHA', '=', $fecha)
        ->where('CAJA', '=', $dato['caja'])
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // TARJETA ABONO
        
        $tarjetaAbono = VentaAbonoTarjeta::select(DB::raw('IFNULL(SUM(VENTAS_ABONO_TARJETA.MONTO), 0) AS TOTAL'))
        ->leftjoin('VENTAS_ABONO', 'VENTAS_ABONO.ID', '=', 'VENTAS_ABONO_TARJETA.FK_ABONO')
        ->where('VENTAS_ABONO.FK_SUCURSAL', '=', $user->id_sucursal)
        ->whereDate('VENTAS_ABONO.FECHA', '=', $fecha)
        ->where('VENTAS_ABONO.CAJA', '=', $dato['caja'])
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // TRANSFERENCIA ABONO
        
        $transferenciaAbono = VentaAbonoTransferencia::select(DB::raw('IFNULL(SUM(VENTAS_ABONO_TRANSFERENCIA.MONTO), 0) AS TOTAL'))
        ->leftjoin('VENTAS_ABONO', 'VENTAS_ABONO.ID', '=', 'VENTAS_ABONO_TRANSFERENCIA.FK_ABONO')
        ->where('VENTAS_ABONO.FK_SUCURSAL', '=', $user->id_sucursal)
        ->whereDate('VENTAS_ABONO.FECHA', '=', $fecha)
        ->where('VENTAS_ABONO.CAJA', '=', $dato['caja'])
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // GIRO ABONO
        
        $giroAbono = VentaAbonoGiro::select(DB::raw('IFNULL(SUM(VENTAS_ABONO_GIRO.MONTO), 0) AS TOTAL'))
        ->leftjoin('VENTAS_ABONO', 'VENTAS_ABONO.ID', '=', 'VENTAS_ABONO_GIRO.FK_ABONO')
        ->where('VENTAS_ABONO.FK_SUCURSAL', '=', $user->id_sucursal)
        ->whereDate('VENTAS_ABONO.FECHA', '=', $fecha)
        ->where('VENTAS_ABONO.CAJA', '=', $dato['caja'])
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // VALE ABONO
        
        $valeAbono = VentaAbonoVale::select(DB::raw('IFNULL(SUM(VENTAS_ABONO_VALE.MONTO), 0) AS TOTAL'))
        ->leftjoin('VENTAS_ABONO', 'VENTAS_ABONO.ID', '=', 'VENTAS_ABONO_VALE.FK_ABONO')
        ->where('VENTAS_ABONO.FK_SUCURSAL', '=', $user->id_sucursal)
        ->whereDate('VENTAS_ABONO.FECHA', '=', $fecha)
        ->where('VENTAS_ABONO.CAJA', '=', $dato['caja'])
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // CHEQUE ABONO
        
        $chequeAbono = VentaAbonoCheque::select(DB::raw('IFNULL(SUM(VENTAS_ABONO_CHEQUE.MONTO), 0) AS TOTAL, VENTAS_ABONO_CHEQUE.MONEDA'))
        ->leftjoin('VENTAS_ABONO', 'VENTAS_ABONO.ID', '=', 'VENTAS_ABONO_CHEQUE.FK_ABONO')
        ->where('VENTAS_ABONO.FK_SUCURSAL', '=', $user->id_sucursal)
        ->whereDate('VENTAS_ABONO.FECHA', '=', $fecha)
        ->where('VENTAS_ABONO.CAJA', '=', $dato['caja'])
        ->groupBy('VENTAS_ABONO_CHEQUE.MONEDA')
        ->get();


        /*  --------------------------------------------------------------------------------- */

        // MONEDAS ABONO
        
        $monedaAbono = VentaAbonoMoneda::select(DB::raw('IFNULL(SUM(VENTAS_ABONO_MONEDAS.MONTO), 0) AS TOTAL, VENTAS_ABONO_MONEDAS.FK_MONEDA'))
        ->leftjoin('VENTAS_ABONO', 'VENTAS_ABONO.ID', '=', 'VENTAS_ABONO_MONEDAS.FK_ABONO')
        ->where('VENTAS_ABONO.FK_SUCURSAL', '=', $user->id_sucursal)
        ->whereDate('VENTAS_ABONO.FECHA', '=', $fecha)
        ->where('VENTAS_ABONO.CAJA', '=', $dato['caja'])
        ->groupBy('VENTAS_ABONO_MONEDAS.FK_MONEDA')
        ->get();

        

        /*  --------------------------------------------------------------------------------- */

        foreach ($monedaAbono as $key => $value) {

           if ($value->FK_MONEDA === 1) {
                $contado[0]->GUARANIES = $contado[0]->GUARANIES + $value->TOTAL;
           } else if ($value->FK_MONEDA === 2) {
                $contado[0]->DOLARES = $contado[0]->DOLARES + $value->TOTAL;
           } else if ($value->FK_MONEDA === 3) {
                $contado[0]->PESOS = $contado[0]->PESOS + $value->TOTAL;
           } else if ($value->FK_MONEDA === 4) {
                $contado[0]->REALES = $contado[0]->REALES + $value->TOTAL;
           }

        }

        /*  --------------------------------------------------------------------------------- */

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

        $cheque_dolar = 0.00;
        $cheque_guarani = 0;
        $cheque_peso = 0.00;
        $cheque_real = 0.00;

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
        $pdf->Ln(6);

        /*  --------------------------------------------------------------------------------- */

        // LINEA 
        
        $pdf->Cell(60,0,'','T');

        /*  --------------------------------------------------------------------------------- */

        // CARGAR CHEQUES 

        foreach ($chequeAbono as $key => $value) {

           if ($value->MONEDA === 1) {
                $cheque_guarani = $cheque_guarani + $value->TOTAL;
           } else if ($value->MONEDA === 2) {
                $cheque_dolar = $cheque_dolar + $value->TOTAL;
           } else if ($value->MONEDA === 3) {
                $cheque_peso = $cheque_peso + $value->TOTAL;
           } else if ($value->MONEDA === 4) {
                $cheque_real = $cheque_real + $value->TOTAL;
           }

        }

        foreach ($cheque as $key => $value) {

            if ($value->MONEDA === 1) {
                $cheque_guarani = $cheque_guarani + $value->TOTAL;
           } else if ($value->MONEDA === 2) {
                $cheque_dolar = $cheque_dolar + $value->TOTAL;
           } else if ($value->MONEDA === 3) {
                $cheque_peso = $cheque_peso + $value->TOTAL;
           } else if ($value->MONEDA === 4) {
                $cheque_real = $cheque_real + $value->TOTAL;
           }
        }

        /*  --------------------------------------------------------------------------------- */

        // CHEQUES 
        $pdf->SetFont('Helvetica','B',8);
        $pdf->Ln(2);
        $pdf->Cell(25, 4, 'CHEQUES:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, '',0,0,'R');
        $pdf->Ln(4);
        $pdf->SetFont('Helvetica','',8);

        $pdf->Cell(25, 4, 'Guaranies', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec($cheque_guarani, 1),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Dolares', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec($cheque_dolar, 2),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Pesos', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec($cheque_peso, 3),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Reales', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec($cheque_real, 4),0,0,'R');
        $pdf->Ln(4);

        /*  --------------------------------------------------------------------------------- */

        // LINEA 

        $pdf->Ln(2);
        $pdf->Cell(60,0,'','T');
        $pdf->Ln(2);

        /*  --------------------------------------------------------------------------------- */

        $pdf->Cell(25, 4, 'Giros:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec($giro[0]->TOTAL + $giroAbono[0]->TOTAL, 1),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Vales:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec($vale[0]->TOTAL + $valeAbono[0]->TOTAL, $parametro[0]->MONEDA),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Giros:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec($contado[0]->T_GIROS  + $giroAbono[0]->TOTAL, $parametro[0]->MONEDA),0,0,'R');
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
        $pdf->Cell(15, 4, Common::precio_candec($contado[0]->T_TOTAL + $abono[0]->T_TOTAL, $parametro[0]->MONEDA),0,0,'R');
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
        $pdf->Cell(15, 4, Common::precio_candec($credito[0]->T_TOTAL, $parametro[0]->MONEDA),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, utf8_decode('Ventas PE:'), 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec($pe[0]->T_TOTAL, $parametro[0]->MONEDA),0,0,'R');
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

        /*  --------------------------------------------------------------------------------- */

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
        $contado_x = '';
        $credito_x = '';
        $c_rows_array = count($ventas_det);
        $c_filas_total = count($ventas_det);
        $codigo = $ventas->CODIGO;
        $cliente = $ventas->CLIENTE;
        $direccion = $ventas->DIRECCION;
        $ruc = $ventas->RUC;
        $tipo = $ventas->TIPO;
        $fecha = $ventas->FECALTAS;
        $telefono = $ventas->TELEFONO;
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

        // TIPO 

        if ($tipo === 'CRÉDITO') {
            $credito_x = 'X';
        } else {
            $contado_x = 'X';
        }

        /*  --------------------------------------------------------------------------------- */

        // CARGAR VARIABLES 
        
        $data['codigo'] = $codigo;
        $data['cliente'] = $cliente;
        $data['direccion'] = $direccion;
        $data['ruc'] = $ruc;
        $data['fecha'] = $fecha;
        $data['telefono'] = $telefono;
        $data['nombre'] = $nombre;
        $data['c'] = $c;
        $data['tipo'] = 'fisico';
        $data['contado_x'] = $contado_x;
        $data['credito_x'] = $credito_x;

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

            if ($value["BASE5"] !== 0 && $value["BASE5"] !== 0.00) {
                $articulos[$c_rows]["base10"] = '';
                $articulos[$c_rows]["base5"] = $articulos[$c_rows]["total"];
                $base5 = $base5 + Common::quitar_coma($articulos[$c_rows]["total"], $candec);
            } else if ($value["BASE10"] !== 0 && $value["BASE10"] !== 0.00) {
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
                            1 => 'CAJA', 
                            3 => 'FECHA',
                            4 => 'IVA',
                            5 => 'TIPO',
                            6 => 'TOTAL',
                            7 => 'ACCION',
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = Venta::where('ID_SUCURSAL','=', $user->id_sucursal);  
        
        if (!empty($request->input('caja'))){
                $totalData = $totalData->where('VENTAS.CAJA','=', $request->input('caja'));
        } 

        $totalData = $totalData->count();

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

            $posts = Venta::select(DB::raw('VENTAS.CODIGO, VENTAS.CAJA, substring(VENTAS.FECHA, 1, 11) AS FECHA, VENTAS.HORA, VENTAS.TIPO, VENTAS.TOTAL, VENTAS.MONEDA, CLIENTES.NOMBRE AS CLIENTE, VENTAS_ANULADO.ANULADO, MONEDAS.CANDEC, CLIENTES.CODIGO AS CLIENTE_CODIGO'))
            ->leftjoin('VENTAS_ANULADO', 'VENTAS.ID', '=', 'VENTAS_ANULADO.FK_VENTA')
                         ->leftJoin('CLIENTES', function($join){
                            $join->on('VENTAS.CLIENTE', '=', 'CLIENTES.CODIGO')
                                 ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
                         })
                         ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'VENTAS.MONEDA')
                         ->where('VENTAS.ID_SUCURSAL','=', $user->id_sucursal)
                         ->offset($start)
                         ->limit($limit)
                         ->orderBy($order,$dir);

            /*  ************************************************************ */

            if (!empty($request->input('caja'))){
                $posts = $posts->where('VENTAS.CAJA','=', $request->input('caja'));
            } 

            $posts = $posts->get();

        } else {

            /*  ************************************************************ */

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            /*  ************************************************************ */

            // CARGAR LOS PRODUCTOS FILTRADOS EN DATATABLE

            $posts =  Venta::select(DB::raw('VENTAS.CODIGO, VENTAS.CAJA, substring(VENTAS.FECHA, 1, 11) AS FECHA, VENTAS.HORA, VENTAS.TIPO, VENTAS.TOTAL, VENTAS.MONEDA, CLIENTES.NOMBRE AS CLIENTE, VENTAS_ANULADO.ANULADO, MONEDAS.CANDEC, CLIENTES.CODIGO AS CLIENTE_CODIGO'))
            ->leftjoin('VENTAS_ANULADO', 'VENTAS.ID', '=', 'VENTAS_ANULADO.FK_VENTA')
                         ->leftJoin('CLIENTES', function($join){
                            $join->on('VENTAS.CLIENTE', '=', 'CLIENTES.CODIGO')
                                 ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
                         })
                         ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'VENTAS.MONEDA')
                         ->where('VENTAS.ID_SUCURSAL','=', $user->id_sucursal)
                            ->where(function ($query) use ($search) {
                                $query->where('VENTAS.CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('VENTAS.CODIGO_CA', 'LIKE',"%{$search}%")
                                      ->orWhere('CLIENTES.NOMBRE', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir);

            if (!empty($request->input('caja'))){
                $posts = $posts->where('VENTAS.CAJA','=', $request->input('caja'));
            } 

            $posts = $posts->get();                

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = Venta::where('VENTAS.ID_SUCURSAL','=', $user->id_sucursal)
                            ->leftJoin('CLIENTES', function($join){
                                $join->on('VENTAS.CLIENTE', '=', 'CLIENTES.CODIGO')
                                     ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
                             })
                            ->where(function ($query) use ($search) {
                                $query->where('VENTAS.CODIGO','LIKE',"%{$search}%")
                                       ->orWhere('VENTAS.CODIGO_CA', 'LIKE',"%{$search}%")
                                       ->orWhere('CLIENTES.NOMBRE', 'LIKE',"%{$search}%");
                            });

            /*  ************************************************************ */  

            if (!empty($request->input('caja'))){
                $totalFiltered = $totalFiltered->where('VENTAS.CAJA','=', $request->input('caja'));
            }

            $totalFiltered = $totalFiltered->count();

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
                $nestedData['TOTAL_SIN_LETRA'] = Common::precio_candec_sin_letra($post->TOTAL, $post->MONEDA);
                $nestedData['MONEDA'] = $post->MONEDA;
                $nestedData['CANDEC'] = $post->CANDEC;
                $nestedData['CLIENTE_CODIGO'] = $post->CLIENTE_CODIGO;

                if ($post->ANULADO === 1) {

                    /*  --------------------------------------------------------------------------------- */

                    // VENTA ANULADA 

                    $nestedData['ESTATUS'] = 'table-danger';
                    $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarDetalle' title='Detalle'><i class='fa fa-list' aria-hidden='true'></i></a>
                    &emsp;<a href='#' id='imprimirTicket' title='Reporte'><i class='fa fa-file text-secondary' aria-hidden='true'></i></a>";

                    /*  --------------------------------------------------------------------------------- */

                } else if($post->TIPO === 'PE' && $post->ANULADO === 2) {

                    /*  --------------------------------------------------------------------------------- */

                    // PAGO AL ENTREGAR PENDIENTE

                    $nestedData['ESTATUS'] = 'table-warning';
                    $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarDetalle' title='Detalle'><i class='fa fa-list' aria-hidden='true'></i></a>
                    &emsp;<a href='#' id='pagarVenta' title='Pagar'><i class='fa fa-vote-yea text-success' aria-hidden='true'></i></a>
                    &emsp;<a href='#' id='imprimirTicket' title='Reporte'><i class='fa fa-file text-secondary' aria-hidden='true'></i></a>&emsp;<a href='#' id='imprimirFactura' title='Imprimir'><i class='fa fa-print text-primary' aria-hidden='true'></i></a>&emsp;<a href='#' id='imprimirPdf' title='Pdf'><i class='fa fa-file text-danger' aria-hidden='true'></i></a>";

                    /*  --------------------------------------------------------------------------------- */

                } else {
                    $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrarDetalle' title='Detalle'><i class='fa fa-list' aria-hidden='true'></i></a>
                    &emsp;<a href='#' id='imprimirTicket' title='Reporte'><i class='fa fa-file text-secondary' aria-hidden='true'></i></a>&emsp;<a href='#' id='imprimirFactura' title='Imprimir'><i class='fa fa-print text-primary' aria-hidden='true'></i></a>&emsp;<a href='#' id='imprimirPdf' title='Pdf'><i class='fa fa-file text-danger' aria-hidden='true'></i></a>";
                    $nestedData['ESTATUS'] = '';
                }

                

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

    public static function filtrar_venta($datos)
    {
        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */
 
        // OBTENER TODOS LOS DATOS DEL TALLE
        if($datos['id']['co_ca']===1){


            $venta = DB::connection('retail')->table('VENTAS')
            ->leftJoin('CLIENTES', function($join){
                        $join->on('VENTAS.CLIENTE', '=', 'CLIENTES.CODIGO')
                             ->on('VENTAS.ID_SUCURSAL', '=', 'CLIENTES.ID_SUCURSAL');
                    })
                        ->leftJoin('EMPLEADOS', function($join){
                        $join->on('VENTAS.VENDEDOR', '=', 'EMPLEADOS.CODIGO')
                             ->on('VENTAS.ID_SUCURSAL', '=', 'EMPLEADOS.ID_SUCURSAL');
                    })
            ->leftjoin('MONEDAS', 'VENTAS.MONEDA', '=', 'MONEDAS.CODIGO')
          ->select( 
            DB::raw('VENTAS.CODIGO AS CODIGO'),
            DB::raw('VENTAS.CAJA AS CAJA'),
            DB::raw('VENTAS.CODIGO_CA AS CODIGO_CA'),
            DB::raw('VENTAS.FECALTAS AS FECHA'),
             DB::raw('VENTAS.HORA AS HORA'),
            DB::raw('CLIENTES.NOMBRE AS NOMBRE'),
            DB::raw('EMPLEADOS.NOMBRE AS NOMBRE_E'),
            DB::raw('VENTAS.TIPO AS TIPO'),
          
            DB::raw('VENTAS.DESCUENTO AS DESCUENTO'),
            DB::raw('VENTAS.TOTAL AS TOTAL'),
            DB::raw('MONEDAS.DESCRIPCION AS MONEDA'))
        ->Where('VENTAS.CODIGO','=',$datos['id']['Codigo'])
        ->Where('VENTAS.CAJA','=',$datos['id']['caja'])
        ->Where('VENTAS.ID_SUCURSAL','=',$user->id_sucursal)  
                ->get()
                ->toArray();
        }else{


            $venta = DB::connection('retail')->table('VENTAS')
            ->leftJoin('CLIENTES', function($join){
                        $join->on('VENTAS.CLIENTE', '=', 'CLIENTES.CODIGO')
                             ->on('VENTAS.ID_SUCURSAL', '=', 'CLIENTES.ID_SUCURSAL');
                    })
                    ->leftJoin('EMPLEADOS', function($join){
                        $join->on('VENTAS.VENDEDOR', '=', 'EMPLEADOS.CODIGO')
                             ->on('VENTAS.ID_SUCURSAL', '=', 'EMPLEADOS.ID_SUCURSAL');
                    })
            ->leftjoin('MONEDAS', 'VENTAS.MONEDA', '=', 'MONEDAS.CODIGO')
            ->select(
            DB::raw('VENTAS.CODIGO AS CODIGO'),
            DB::raw('VENTAS.CAJA AS CAJA'),
            DB::raw('VENTAS.CODIGO_CA AS CODIGO_CA'),
            DB::raw('VENTAS.FECALTAS AS FECHA'),
             DB::raw('VENTAS.HORA AS HORA'),
            DB::raw('CLIENTES.NOMBRE AS NOMBRE'),
            DB::raw('EMPLEADOS.NOMBRE AS NOMBRE_E'),
            DB::raw('VENTAS.TIPO AS TIPO'),
           
            DB::raw('VENTAS.DESCUENTO AS DESCUENTO'),
            DB::raw('VENTAS.TOTAL AS TOTAL'),
            DB::raw('MONEDAS.DESCRIPCION AS MONEDA'))
        ->Where('VENTAS.CODIGO_CA','=',$datos['id']['co_ca'])
        
        ->Where('VENTAS.FECALTAS','like',$datos['id']['fecha'].'%')
        ->Where('VENTAS.ID_SUCURSAL','=',$user->id_sucursal)  
                ->get()
                ->toArray();
        }

        if(count($venta)<=0){
           return ["response"=>false];
        }
        // RETORNAR EL VALOR
        
       return ["response"=>true,"ventas"=>$venta];

        /*  --------------------------------------------------------------------------------- */

    }

    public static function ventas_datatable($request)
    {
        $dia = date("Y-m-d");
        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIARA VARIABLES

        //global $search;

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 



        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'CODIGO', 
                            1 => 'caja',
                            2 => 'codigo_ca',
                            3 => 'fecha',
                            4 => 'nombre',
                            5 => 'tipo',
                            6 => 'tarjeta',
                            7 => 'descuento',
                            8 => 'total',
                            9 => 'moneda'
                         
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = Venta::leftjoin('VENTAS_ANULADO', 'VENTAS_ANULADO.FK_VENTA', '=', 'VENTAS.ID')->where('id_sucursal','=',$user->id_sucursal)->where('FECALTAS','=',$dia)->Where('VENTAS_ANULADO.anulado','=',0)->where('VENTAS.TIPO','=','CO')->where('CAJA','=',$request->input('caja_numero'))->count();  
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

                          $posts = DB::connection('retail')->table('VENTAS')
             ->leftjoin('VENTAS_TARJETA', 'FK_VENTA', '=', 'VENTAS.ID')
              ->leftjoin('VENTAS_ANULADO', 'VENTAS_ANULADO.FK_VENTA', '=', 'VENTAS.ID')
            ->select(
            DB::raw('VENTAS.CODIGO AS CODIGO, 
                VENTAS.CAJA AS CAJA,
                VENTAS.CODIGO_CA AS CODIGO_CA,
                VENTAS.FECALTAS AS FECHA,
                CLIENTES.NOMBRE AS NOMBRE,
                VENTAS.TIPO AS TIPO,
              IFNULL(VENTAS_TARJETA.MONTO, 0) AS TARJETA,
                VENTAS.DESCUENTO AS DESCUENTO,
                VENTAS.TOTAL AS TOTAL,
                MONEDAS.DESCRIPCION AS MONEDA'))
                       ->leftJoin('CLIENTES', function($join){
                        $join->on('CLIENTES.CODIGO', '=', 'VENTAS.CLIENTE')
                             ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
                    })
            ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'VENTAS.MONEDA')
             ->Where('VENTAS.ID_SUCURSAL','=',$user->id_sucursal)
             ->Where('VENTAS.FECALTAS','=',$dia)
              ->Where('VENTAS_ANULADO.anulado','=',0)
              ->where('VENTAS.TIPO','=','CO')
             ->where('CAJA','=',$request->input('caja_numero'))   
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

            
                $posts = DB::connection('retail')->table('VENTAS')
            ->leftjoin('VENTAS_TARJETA', 'FK_VENTA', '=', 'VENTAS.ID')
              ->leftjoin('VENTAS_ANULADO', 'VENTAS_ANULADO.FK_VENTA', '=', 'VENTAS.ID')
            ->select(
            DB::raw('VENTAS.CODIGO AS CODIGO, 
                VENTAS.CAJA AS CAJA,
                VENTAS.CODIGO_CA AS CODIGO_CA,
                VENTAS.FECALTAS AS FECHA,
                CLIENTES.NOMBRE AS NOMBRE,
                VENTAS.TIPO AS TIPO,
                IFNULL(VENTAS_TARJETA.MONTO, 0) AS TARJETA,
                VENTAS.DESCUENTO AS DESCUENTO,
                VENTAS.TOTAL AS TOTAL,
                MONEDAS.DESCRIPCION AS MONEDA'))
                       ->leftJoin('CLIENTES', function($join){
                        $join->on('CLIENTES.CODIGO', '=', 'VENTAS.CLIENTE')
                             ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
                    })
            ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'VENTAS.MONEDA')
          
            ->Where('VENTAS.ID_SUCURSAL','=',$user->id_sucursal)
            ->Where('VENTAS.FECALTAS','=',$dia)
             ->Where('VENTAS_ANULADO.anulado','=',0)  
             ->where('VENTAS.TIPO','=','CO')
            ->where('CAJA','=',$request->input('caja_numero')) 
            ->where(function ($query) use ($search) {
                                $query->where('VENTAS.CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('CLIENTES.NOMBRE', 'LIKE',"%{$search}%")
                                      ->orWhere('ventas.CODIGO_CA', 'LIKE',"%{$search}%");
                            })

                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered =DB::connection('retail')->table('VENTAS')
            ->leftjoin('VENTAS_TARJETA', 'FK_VENTA', '=', 'VENTAS.ID')
             ->leftjoin('VENTAS_ANULADO', 'VENTAS_ANULADO.FK_VENTA', '=', 'VENTAS.ID')
            ->select(
            DB::raw('VENTAS.CODIGO AS CODIGO, 
                VENTAS.CAJA AS CAJA,
                VENTAS.CODIGO_CA AS CODIGO_CA,
                VENTAS.FECALTAS AS FECHA,
                CLIENTES.NOMBRE AS NOMBRE,
                VENTAS.TIPO AS TIPO,
               IFNULL(VENTAS_TARJETA.MONTO, 0) AS TARJETA,
                VENTAS.DESCUENTO AS DESCUENTO,
                VENTAS.TOTAL AS TOTAL,
                MONEDAS.DESCRIPCION AS MONEDA'))
                       ->leftJoin('CLIENTES', function($join){
                        $join->on('CLIENTES.CODIGO', '=', 'VENTAS.CLIENTE')
                             ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
                    })
            ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'VENTAS.MONEDA')
            ->leftjoin('TARJETAS', 'TARJETAS.CODIGO', '=', 'VENTAS.TARJETA')
            ->Where('VENTAS.ID_SUCURSAL','=',$user->id_sucursal)   
            ->Where('VENTAS.FECALTAS','=',$dia)
            ->where('VENTAS.TIPO','=','CO')
             ->Where('VENTAS_ANULADO.anulado','=',0)
            ->where('CAJA','=',$request->input('caja_numero'))
            ->where(function ($query) use ($search) {
                                $query->where('VENTAS.CODIGO','LIKE',"%{$search}%")
                                      ->orWhere('CLIENTES.NOMBRE', 'LIKE',"%{$search}%")
                                      ->orWhere('ventas.CODIGO_CA', 'LIKE',"%{$search}%");
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
                $nestedData['CODIGO_CA'] = $post->CODIGO_CA;
                $nestedData['FECHA'] = $post->FECHA;
                $nestedData['NOMBRE'] = $post->NOMBRE;
                $nestedData['TIPO'] = $post->TIPO;
                $nestedData['TARJETA'] = $post->TARJETA;
                $nestedData['DESCUENTO'] = $post->DESCUENTO;
                $nestedData['TOTAL'] = $post->TOTAL;
                $nestedData['MONEDA'] = $post->MONEDA;

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

    }
    
    public static function anular_venta($datos)
    {

        try {
            DB::connection('retail')->beginTransaction();
      
        $user = auth()->user();
        $dia = date("Y-m-d H:i:s");
        $hora = date("H:i:s");

        /*  --------------------------------------------------------------------------------- */

           $venta = DB::connection('retail')->table('VENTAS')->select(
            DB::raw('VENTAS.TOTAL AS TOTAL,VENTAS.ID AS ID'))
            ->Where('VENTAS.CODIGO','=',$datos['data']['codigo'])
            ->Where('VENTAS.CAJA','=',$datos['data']['caja'])
            ->Where('VENTAS.ID_SUCURSAL','=',$user->id_sucursal) 
            ->get()
            ->toArray();
            
        if(count($venta)<=0){
           return ["response"=>false,'status_text'=> "No existe la venta seleccionada"];
        }
        


            VentasAnulado::anular_venta($venta["0"]->ID,$dia);

            $venta= DB::connection('retail')->table('VENTAS')->where('CODIGO', $datos['data']['codigo'])
               ->Where('VENTAS.CAJA','=',$datos['data']['caja'])
              ->Where('VENTAS.ID_SUCURSAL','=',$user->id_sucursal) 
            ->update(['FECMODIF'=>$dia,'HORMODIF'=>$hora, 'USERM'=>$user->name]);

                  $venta = DB::connection('retail')->table('VENTASDET')
                   ->leftjoin('VENTASDET_TIENE_LOTES', 'ID_VENTAS_DET', '=', 'VENTASDET.ID')
                   ->select(
            DB::raw('VENTASDET.COD_PROD AS COD_PROD'),
            DB::raw('VENTASDET.ID AS ID'),
            DB::raw('VENTASDET_TIENE_LOTES.ID_LOTE AS ID_LOTE'),
            DB::raw('VENTASDET.CANTIDAD AS CANTIDAD'),
            DB::raw('VENTASDET.PRECIO AS PRECIO'),
            DB::raw('VENTASDET.LOTE AS LOTE'))
            ->Where('VENTASDET.CODIGO','=',$datos['data']['codigo'])
            ->Where('VENTASDET.CAJA','=',$datos['data']['caja'])
            ->Where('VENTASDET.ID_SUCURSAL','=',$user->id_sucursal) 
            ->where('VENTASDET.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%')
            ->get()
            ->toArray();
             foreach ($venta as $key => $value) {
                 # code...
                Stock::sumar_stock_id_lote($value->ID_LOTE,$value->CANTIDAD);
                /*if ($value->LOTE>0){
              

            }
            if($value->PRECIO<0){
              Stock::restar_stock_producto($value->COD_PROD, $value->CANTIDAD);
            }*/

                $delete_lote_venta= DB::connection('retail')->table('VENTASDET_TIENE_LOTES')->where('ID_VENTAS_DET', $value->ID)
               ->delete();

             }
            
    
            $ventas= DB::connection('retail')->table('VENTASDET')->where('CODIGO', $datos['data']['codigo'])
            ->Where('VENTASDET.CAJA','=',$datos['data']['caja'])
            ->Where('VENTASDET.ID_SUCURSAL','=',$user->id_sucursal) 
            ->update(['ANULADO'=> 1,'FECMODIF'=>$dia,'HORMODIF'=>$hora,'USERM'=>$user->name]);
            DB::connection('retail')->commit();

            return ["response"=>true,"ventas"=>$venta];

    } catch (Exception $e) {
        DB::connection('retail')->rollBack();
        throw $e;
    }

        /*  --------------------------------------------------------------------------------- */

    }

    public static function devolucion_productos($request) {

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $codigo = $request->input('codigo');
        $caja = $request->input('caja');

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'ITEM', 
                            1 => 'COD_PROD',
                            2 => 'DESCRIPCION',
                            3 => 'CANTIDAD',
                            4 => 'PRECIO',
                            5 => 'TOTAL'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE PRODUCTOS ENCONTRADOS 

        $totalData = Ventas_det::where('ID_SUCURSAL','=', $user->id_sucursal)
                    ->where('CODIGO','=', $codigo)
                    ->where('CAJA','=', $caja)
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

            $posts = Ventas_det::select(DB::raw('VENTASDET.ID, VENTASDET.ITEM, VENTASDET.COD_PROD, VENTASDET.DESCRIPCION, VENTASDET.CANTIDAD, VENTASDET.IVA AS IMPUESTO, PRODUCTOS.IMPUESTO AS IVA_PORCENTAJE, VENTASDET.PRECIO_UNIT AS PRECIO, VENTASDET.PRECIO AS TOTAL, VENTAS.MONEDA, IFNULL(ventasdet_descuento.TOTAL, 0) AS DESCUENTO_TOTAL, IFNULL(ventasdet_descuento.PORCENTAJE, 0) AS DESCUENTO_PORCENTAJE'))
                         ->leftJoin('VENTAS', function($join){
                            $join->on('VENTAS.CODIGO', '=', 'VENTASDET.CODIGO')
                                 ->on('VENTAS.ID_SUCURSAL', '=', 'VENTASDET.ID_SUCURSAL')
                                 ->on('VENTAS.CAJA', '=', 'VENTASDET.CAJA');
                         })
                         ->leftjoin('ventasdet_descuento', 'ventasdet_descuento.FK_VENTASDET', '=', 'VENTASDET.ID')
                         ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
                         ->where('VENTASDET.ID_SUCURSAL','=', $user->id_sucursal)
                         ->where('VENTASDET.CODIGO','=', $codigo)
                         ->where('VENTASDET.CAJA','=', $caja)
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

            $posts =  Ventas_det::select(DB::raw('VENTASDET.ID, VENTASDET.ITEM, VENTASDET.COD_PROD, VENTASDET.DESCRIPCION, VENTASDET.CANTIDAD, VENTASDET.IVA AS IMPUESTO, PRODUCTOS.IMPUESTO AS IVA_PORCENTAJE, VENTASDET.PRECIO_UNIT AS PRECIO, VENTASDET.PRECIO AS TOTAL, VENTAS.MONEDA'))
                         ->leftJoin('VENTAS', function($join){
                            $join->on('VENTAS.CODIGO', '=', 'VENTASDET.CODIGO')
                                 ->on('VENTAS.ID_SUCURSAL', '=', 'VENTASDET.ID_SUCURSAL')
                                 ->on('VENTAS.CAJA', '=', 'VENTASDET.CAJA');
                         })
                         ->where('VENTASDET.ID_SUCURSAL','=', $user->id_sucursal)
                         ->where('VENTASDET.CODIGO','=', $codigo)
                         ->where('VENTASDET.CAJA','=', $caja)
                            ->where(function ($query) use ($search) {
                                $query->where('VENTASDET.COD_PROD','LIKE',"%{$search}%")
                                      ->orWhere('VENTASDET.DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered =  Ventas_det::where('ID_SUCURSAL','=', $user->id_sucursal)
                         ->where('CODIGO','=', $codigo)
                         ->where('CAJA','=', $caja)
                            ->where(function ($query) use ($search) {
                                $query->where('COD_PROD','LIKE',"%{$search}%")
                                      ->orWhere('DESCRIPCION', 'LIKE',"%{$search}%");
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
                $nestedData['ITEM'] = $post->ITEM;
                $nestedData['COD_PROD'] = $post->COD_PROD;
                $nestedData['DESCRIPCION'] = $post->DESCRIPCION;
                $nestedData['CANTIDAD'] = $post->CANTIDAD;
                $nestedData['DESCUENTO_TOTAL'] = Common::precio_candec_sin_letra($post->DESCUENTO_TOTAL, $post->MONEDA);
                $nestedData['DESCUENTO'] = Common::precio_candec_sin_letra($post->DESCUENTO_PORCENTAJE, 1);
                $nestedData['IVA_PORCENTAJE'] = Common::precio_candec_sin_letra($post->IVA_PORCENTAJE, 1);
                $nestedData['IMPUESTO'] = Common::precio_candec_sin_letra($post->IMPUESTO, $post->MONEDA);
                $nestedData['PRECIO'] = Common::precio_candec_sin_letra($post->PRECIO, $post->MONEDA);
                $nestedData['TOTAL'] = Common::precio_candec_sin_letra($post->TOTAL, $post->MONEDA);


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

    public static function mostrar_productos($request) {

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $codigo = $request->input('codigoVenta');
        $caja =  $request->input('codigoCaja');

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'ITEM', 
                            1 => 'COD_PROD',
                            2 => 'DESCRIPCION',
                            3 => 'CANTIDAD',
                            4 => 'PRECIO_UNIT',
                            5 => 'PRECIO',
                            6 => 'ventasdet_descuento.PORCENTAJE',
                            7 => 'ventasdet_descuento.TOTAL'
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE PRODUCTOS ENCONTRADOS 

        $totalData = DB::connection('retail')->table('VENTASDET')
                    ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
                    ->leftJoin('VENTAS', function($join){
                        $join->on('VENTAS.CODIGO', '=', 'VENTASDET.CODIGO')
                             ->on('VENTAS.ID_SUCURSAL', '=', 'VENTASDET.ID_SUCURSAL')
                             ->on('VENTAS.CAJA', '=', 'VENTASDET.CAJA');
                    })
                    ->where('VENTAS.ID_SUCURSAL','=', $user->id_sucursal)
                    ->where('VENTASDET.CODIGO','=', $codigo)
                    ->where('VENTASDET.CAJA','=', $caja)
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

            $posts = DB::connection('retail')->table('VENTASDET')
                         ->select(DB::raw('VENTASDET.ITEM, 
                            VENTASDET.COD_PROD, 
                            VENTASDET.DESCRIPCION, 
                            VENTASDET.CANTIDAD, 
                            VENTASDET.PRECIO_UNIT, 
                            VENTASDET.PRECIO, 
                            IFNULL(ventasdet_descuento.PORCENTAJE, 0) as PORCENTAJE, 
                            ventasdet_descuento.TOTAL, 
                            VENTAS.MONEDA'))
                         ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
                         ->leftjoin('ventasdet_descuento', 'ventasdet_descuento.FK_VENTASDET', '=', 'VENTASDET.ID')
                         ->leftJoin('VENTAS', function($join){
                            $join->on('VENTAS.CODIGO', '=', 'VENTASDET.CODIGO')
                                 ->on('VENTAS.ID_SUCURSAL', '=', 'VENTASDET.ID_SUCURSAL')
                                 ->on('VENTAS.CAJA', '=', 'VENTASDET.CAJA');
                         })
                         ->where('VENTASDET.ID_SUCURSAL','=', $user->id_sucursal)
                         ->where('VENTASDET.CODIGO','=', $codigo)
                         ->where('VENTASDET.CAJA','=', $caja)
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

            $posts =  DB::connection('retail')->table('VENTASDET')
                        ->select(DB::raw('VENTASDET.ITEM, 
                            VENTASDET.COD_PROD, 
                            VENTASDET.DESCRIPCION, 
                            VENTASDET.CANTIDAD, 
                            VENTASDET.PRECIO_UNIT, 
                            VENTASDET.PRECIO, 
                            IFNULL(ventasdet_descuento.PORCENTAJE, 0) as PORCENTAJE, 
                            ventasdet_descuento.TOTAL, 
                            VENTAS.MONEDA'))
                         ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
                         ->leftjoin('ventasdet_descuento', 'ventasdet_descuento.FK_VENTASDET', '=', 'VENTASDET.ID')
                         ->leftJoin('VENTAS', function($join){
                            $join->on('VENTAS.CODIGO', '=', 'VENTASDET.CODIGO')
                                 ->on('VENTAS.ID_SUCURSAL', '=', 'VENTASDET.ID_SUCURSAL')
                                 ->ON('VENTAS.CAJA', '=', 'VENTASDET.CAJA');
                         })
                         ->where('VENTASDET.CODIGO','=', $codigo)
                         ->where('VENTASDET.ID_SUCURSAL','=', $user->id_sucursal)
                         ->where('VENTASDET.CAJA','=', $caja)
                            ->where(function ($query) use ($search) {
                                $query->where('VENTASDET.COD_PROD','LIKE',"%{$search}%")
                                      ->orWhere('VENTASDET.DESCRIPCION', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = DB::connection('retail')->table('VENTASDET')
                            ->select(DB::raw('VENTASDET.ITEM, 
                                VENTASDET.COD_PROD, 
                                VENTASDET.DESCRIPCION, 
                                VENTASDET.CANTIDAD, 
                                VENTASDET.PRECIO_UNIT, 
                                VENTASDET.PRECIO, 
                                IFNULL(ventasdet_descuento.PORCENTAJE, 0) as PORCENTAJE, 
                                ventasdet_descuento.TOTAL, 
                                VENTAS.MONEDA'))
                             ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
                             ->leftjoin('ventasdet_descuento', 'ventasdet_descuento.FK_VENTASDET', '=', 'VENTASDET.ID')
                             ->leftJoin('VENTAS', function($join){
                                $join->on('VENTAS.CODIGO', '=', 'VENTASDET.CODIGO')
                                     ->on('VENTAS.ID_SUCURSAL', '=', 'VENTASDET.ID_SUCURSAL')
                                     ->on('VENTAS.CAJA', '=', 'VENTASDET.CAJA');
                             })
                             ->where('VENTASDET.CODIGO','=', $codigo)
                             ->where('VENTASDET.ID_SUCURSAL','=', $user->id_sucursal)
                             ->where('VENTASDET.CAJA','=', $caja)
                            ->where(function ($query) use ($search) {
                                $query->where('VENTASDET.COD_PROD','LIKE',"%{$search}%")
                                      ->orWhere('VENTASDET.DESCRIPCION', 'LIKE',"%{$search}%");
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

                $nestedData['ITEM'] = $post->ITEM;
                $nestedData['COD_PROD'] = $post->COD_PROD;
                $nestedData['DESCRIPCION'] = $post->DESCRIPCION;
                $nestedData['CANTIDAD'] = $post->CANTIDAD;
                $nestedData['PRECIO'] = Common::precio_candec($post->PRECIO_UNIT, $post->MONEDA);
                $nestedData['TOTAL'] = Common::precio_candec($post->PRECIO, $post->MONEDA);
                $nestedData['PORC_DESCUENTO'] = $post->PORCENTAJE;
                $nestedData['TOTAL_DESCUENTO'] = Common::precio_candec($post->TOTAL, $post->MONEDA);


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

     public static function obtenerCuentas($request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
                            0 => 'CODIGO', 
                            1 => 'PROVEEDOR',
                            2 => 'TIPO',
                            3 => 'NRO_FACTURA',
                            4 => 'FEC_FACTURA',
                            5 => 'TOTAL',
                            6 => 'ACCION',
                        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = Compra::where('ID_SUCURSAL','=', $user->id_sucursal)
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

            $posts = Compra::select(DB::raw('COMPRAS.CODIGO, COMPRAS.MONEDA, PROVEEDORES.NOMBRE, COMPRAS.TIPO, COMPRAS.NRO_FACTURA, COMPRAS.FEC_FACTURA, COMPRAS.TOTAL'))
                         ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'COMPRAS.PROVEEDOR')
                         ->where('COMPRAS.ID_SUCURSAL','=', $user->id_sucursal)
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

            $posts =  Compra::select(DB::raw('COMPRAS.CODIGO, COMPRAS.MONEDA, PROVEEDORES.NOMBRE, COMPRAS.TIPO, COMPRAS.NRO_FACTURA, COMPRAS.FEC_FACTURA, COMPRAS.TOTAL'))
                         ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'COMPRAS.PROVEEDOR')
                         ->where('COMPRAS.ID_SUCURSAL','=', $user->id_sucursal)
                            ->where(function ($query) use ($search) {
                                $query->where('COMPRAS.CODIGO','LIKE',"{$search}%")
                                      ->orWhere('PROVEEDORES.NOMBRE', 'LIKE',"%{$search}%");
                            })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();

            /*  ************************************************************ */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = Compra::where('COMPRAS.ID_SUCURSAL','=', $user->id_sucursal)
                            ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'COMPRAS.PROVEEDOR')
                            ->where(function ($query) use ($search) {
                                $query->where('COMPRAS.CODIGO','LIKE',"{$search}%")
                                      ->orWhere('PROVEEDORES.NOMBRE', 'LIKE',"%{$search}%");
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
                $nestedData['PROVEEDOR'] = $post->NOMBRE;

                /*  --------------------------------------------------------------------------------- */

                // TIPO DE COMPRA

                if ($post->TIPO === 'CO') {
                    $nestedData['TIPO'] = 'Contado';
                } else if ($post->TIPO === 'CR') {
                    $nestedData['TIPO'] = 'Credito';
                } else if ($post->TIPO === 'CS') {
                    $nestedData['TIPO'] = 'Consignacion';
                } else {
                    $nestedData['TIPO'] = 'N/A';
                }

                /*  --------------------------------------------------------------------------------- */

                $nestedData['NRO_FACTURA'] = $post->NRO_CAJA;
                $nestedData['FEC_FACTURA'] = $post->FEC_FACTURA;
                $nestedData['TOTAL'] = Common::precio_candec($post->TOTAL, $post->MONEDA);
                
                $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrar' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a>&emsp;<a href='#' id='editar' title='Editar'><i class='fa fa-edit text-warning' aria-hidden='true'></i></a>&emsp;<a href='#' id='eliminar' title='Eliminar'><i class='fa fa-trash text-danger' aria-hidden='true'></i></a>
                    &emsp;<a href='#' id='reporte' title='Reporte'><i class='fa fa-file text-secondary' aria-hidden='true'></i></a>";

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

    public static function iniciar_variables_venta($data) {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER CANDEC 

        $moneda = $data["data"]["moneda"];
        $candec = (Parametro::candec($moneda))["CANDEC"];

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLE FORMA DE COBRO

        $pago_al_entregar = $data["data"]["pago"]["PAGO_AL_ENTREGAR"];
        $tipo_venta = 'CO';
        $estatus_venta = 0;
        $vuelto = 0;

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

        // REVISAR DINERO Y QUITARLE VUELTO SI EL PAGO NO ES AL ENTREGAR

        $opcion_vuelto = $data["data"]["pago"]["OPCION_VUELTO"];

        if ($opcion_vuelto === '1' && $pago_al_entregar === false && Common::quitar_coma($data["data"]["pago"]["VUELTO"]["guaranies"] , 2) > 0) {
            $guaranies = $guaranies - Common::quitar_coma($data["data"]["pago"]["VUELTO"]["guaranies"] , 2);
        } else if ($opcion_vuelto === '2' && $pago_al_entregar === false && Common::quitar_coma($data["data"]["pago"]["VUELTO"]["dolares"], 2) > 0) {
            $dolares = $dolares - Common::quitar_coma($data["data"]["pago"]["VUELTO"]["dolares"] , 2);
        } else if ($opcion_vuelto === '3' && $pago_al_entregar === false && Common::quitar_coma($data["data"]["pago"]["VUELTO"]["reales"] , 2) > 0) {
            $reales = $reales - Common::quitar_coma($data["data"]["pago"]["VUELTO"]["reales"] , 2);
        } else if ($opcion_vuelto === '4' && $pago_al_entregar === false && Common::quitar_coma($data["data"]["pago"]["VUELTO"]["pesos"] , 2) > 0) {
            $pesos = $pesos - Common::quitar_coma($data["data"]["pago"]["VUELTO"]["pesos"] , 2);
        } 
            
        /*  --------------------------------------------------------------------------------- */

        // VUELTO PRINCIPAL 

        if($moneda === 1 && Common::quitar_coma($data["data"]["pago"]["VUELTO"]["guaranies"] , $candec) > 0) {
            $vuelto = Common::quitar_coma($data["data"]["pago"]["VUELTO"]["guaranies"] , $candec);
        } else if ($moneda === 2 && Common::quitar_coma($data["data"]["pago"]["VUELTO"]["guaranies"] , $candec) > 0) {
            $vuelto = Common::quitar_coma($data["data"]["pago"]["VUELTO"]["dolares"] , $candec);
        } else if ($moneda === 3 && Common::quitar_coma($data["data"]["pago"]["VUELTO"]["guaranies"] , $candec) > 0) {
            $vuelto = Common::quitar_coma($data["data"]["pago"]["VUELTO"]["reales"] , $candec);
        } else if ($moneda === 4 && Common::quitar_coma($data["data"]["pago"]["VUELTO"]["guaranies"] , $candec) > 0) {
            $vuelto = Common::quitar_coma($data["data"]["pago"]["VUELTO"]["pesos"] , $candec);
        }

        /*  --------------------------------------------------------------------------------- */

        //  SI EL PAGO ES DE PAGO AL ETNREGAR NO INICIARA ESTAS VARIABLES 

        if ($data["data"]["estatus"] !== 2) {

            $codigo_caja = $data["data"]["cabecera"]["CODIGO_CAJA"];
            $caja = $data["data"]["caja"]["CODIGO"];
            $numero_caja = 'CA'.sprintf("%02d", $caja).'';
            $cliente = $data["data"]["cliente"]["CODIGO"];
            $vendedor = $data["data"]["vendedor"]["CODIGO"];
            $opcion_impresion = $data["data"]["pago"]["TIPO_IMPRESION"];

        }    
        
        $saldo = Common::quitar_coma($data["data"]["pago"]["SALDO"], $candec);
        $efectivo = Common::quitar_coma($data["data"]["pago"]["EFECTIVO"], $candec);

        /*  --------------------------------------------------------------------------------- */

        // Tarjeta
            
        $tarjeta = Common::quitar_coma($data["data"]["pago"]["TARJETA"], $candec);
        $codigo_tarjeta = $data["data"]["pago"]["CODIGO_TARJETA"];
             
        /*  --------------------------------------------------------------------------------- */

        // TRANSFERENCIA 

        $transferencia = Common::quitar_coma($data["data"]["pago"]["TRANSFERENCIA"], 0);
        $codigo_banco = $data["data"]["pago"]["CODIGO_BANCO"];

        /*  --------------------------------------------------------------------------------- */

        // DESCUENTO GENERAL 

        $descuento_general = Common::quitar_coma($data["data"]["pago"]["DESCUENTO_GENERAL"], $candec);
        $descuento_general_porcentaje = $data["data"]["pago"]["DESCUENTO_GENERAL_PORCENTAJE"];

        /*  --------------------------------------------------------------------------------- */

        // GIRO 

        $giro = Common::quitar_coma($data["data"]["pago"]["GIRO"], 0);
        $codigo_entidad = $data["data"]["pago"]["CODIGO_ENT"];

        /*  --------------------------------------------------------------------------------- */

        // VALE 

        $vale = Common::quitar_coma($data["data"]["pago"]["VALE"], 2);

        /*  --------------------------------------------------------------------------------- */

        // CREDITO

        $credito = Common::quitar_coma($data["data"]["pago"]["CREDITO"], 2);
        $dias_credito = $data["data"]["pago"]["DIAS_CREDITO"];
        $credito_fin = $data["data"]["pago"]["CREDITO_FIN"];

        /*  --------------------------------------------------------------------------------- */

        // CHEQUE

        $cheques = $data["data"]["pago"]["CHEQUE"];

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return [
            'MONEDA' => $moneda,
            'CANDEC' => $candec,
            'PAGO_ENTREGA' => $pago_al_entregar,
            'GUARANIES' => $guaranies,
            'DOLARES' => $dolares,
            'PESOS' => $pesos,
            'REALES' => $reales,
            'VUELTO' => $vuelto,
            'SALDO' => $saldo,
            'EFECTIVO' => $efectivo,
            'TARJETA' => $tarjeta,
            'CODIGO_TARJETA' => $codigo_tarjeta,
            'TRANSFERENCIA' => $transferencia,
            'CODIGO_BANCO' => $codigo_banco,
            'DESCUENTO_GENERAL' => $descuento_general,
            'DESCUENTO_GENERAL_PORCENTAJE' => $descuento_general_porcentaje,
            'GIRO' => $giro,
            'CODIGO_ENTIDAD' => $codigo_entidad,
            'VALE' => $vale,
            'CREDITO' => $credito,
            'DIAS_CREDITO' => $dias_credito,
            'CREDITO_FIN' => $credito_fin,
            'CHEQUE' => $cheques
        ];

        /*  --------------------------------------------------------------------------------- */

    }

    public static function pagoEntrega($data){

        /*  --------------------------------------------------------------------------------- */

        // PAGO AL ENTREGAR FUNCION
        
        try {

            /*  --------------------------------------------------------------------------------- */

            // INICIAR TRANSACCION 

            DB::connection('retail')->beginTransaction();

            /*  --------------------------------------------------------------------------------- */
            
            // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

            $user = auth()->user();

            /*  --------------------------------------------------------------------------------- */

            // FECHA 

            $dia = date('Y-m-d');
            $hora = date("H:i:s");

            /*  --------------------------------------------------------------------------------- */

            // OBTENER ID VENTA 

            $id = Venta::select('ID')
            ->where('CODIGO', '=', $data['data']['codigo'])
            ->where('CAJA', '=', $data['data']['caja'])
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->get();

            /*  --------------------------------------------------------------------------------- */

            // OBTENER TODAS LAS VARIABLES INICIALIZADAS 

            $datos = Venta::iniciar_variables_venta($data);
            Venta::metodos_pago($datos, $id[0]['ID']);

            /*  --------------------------------------------------------------------------------- */

            // MODIFICAR VENTA

            $venta = Venta::where('ID', '=', $id[0]['ID'])
                ->update(
                [ 
                    "FECHA" => $dia,
                    "DESCUENTO" => $datos['DESCUENTO_GENERAL'], 
                    "EFECTIVO" => $datos['EFECTIVO'], 
                    "VUELTO" => $datos['VUELTO'], 
                    "MONEDA" => $datos['MONEDA'], 
                    "MONEDA1" => $datos['DOLARES'], 
                    "MONEDA2" => $datos['REALES'], 
                    "MONEDA3" => $datos['GUARANIES'], 
                    "MONEDA4" => $datos['PESOS'], 
                    "USERM" => $user->name, 
                    "FECMODIF" => $dia, 
                    "HORMODIF" => $hora, 
                ]
            );
            
            /*  --------------------------------------------------------------------------------- */

            // INSERTAR ANULADO 

            VentasAnulado::modificar_referencia([
                    'FK_VENTA' => $id[0]['ID'],
                    'ANULADO' => 0
            ]);

            /*  --------------------------------------------------------------------------------- */

            // ENVIAR TRANSACCION A BD

            DB::connection('retail')->commit();

            /*  --------------------------------------------------------------------------------- */

            // RETORNAR VALOR 

            return ["response" => true, "statusText" => "Se ha guardado correctamente el pago !", "CODIGO" => $data['data']['codigo'], "CAJA" => $data['data']['caja']];

            /*  --------------------------------------------------------------------------------- */


        } catch (Exception $e) {
            
           /*  --------------------------------------------------------------------------------- */

           // NO GUARDAR NINGUN CAMBIO 

           DB::connection('retail')->rollBack();
           throw $e;
           
           /*  --------------------------------------------------------------------------------- */

        }
    }


    public static function metodos_pago($datos, $venta){

        /*  --------------------------------------------------------------------------------- */

        // METODOS DE PAGO
        
        try {

            /*  --------------------------------------------------------------------------------- */

            // INICIAR TRANSACCION 

            DB::connection('retail')->beginTransaction();

            /*  --------------------------------------------------------------------------------- */

            // INSERTAR PAGO TARJETA

            if ($datos['CODIGO_TARJETA'] !== '0' && $datos['CODIGO_TARJETA'] !== '0.00' && $datos['CODIGO_TARJETA'] !== NULL) {
                if ($datos['CODIGO_TARJETA'] !== '') {
                    $pago_tarjeta = VentaTarjeta::guardar_referencia([
                        'FK_TARJETA' => $datos['CODIGO_TARJETA'],
                        'FK_VENTA' => $venta,
                        'MONTO' => $datos['TARJETA'],
                        'MONEDA' => 1
                    ]);
                } 
            }

            /*  --------------------------------------------------------------------------------- */

            // INSERTAR PAGO TRANSFERENCIA
            
            if ($datos['CODIGO_BANCO'] !== '0' && $datos['CODIGO_BANCO'] !== '0.00' && $datos['CODIGO_BANCO'] !== NULL) {
                if ($datos['CODIGO_BANCO'] !== '') {
                    VentaTransferencia::guardar_referencia([
                        'FK_BANCO' => $datos['CODIGO_BANCO'],
                        'FK_VENTA' => $venta,
                        'MONTO' => $datos['TRANSFERENCIA'],
                        'MONEDA' => 1
                    ]);
                } 
            }

            /*  --------------------------------------------------------------------------------- */

            // INSERTAR PAGO GIRO
            
            if ($datos['CODIGO_ENTIDAD'] !== '0' && $datos['CODIGO_ENTIDAD'] !== '0.00' && $datos['CODIGO_ENTIDAD'] !== NULL) {
                if ($datos['CODIGO_ENTIDAD'] !== '') {
                    VentaGiro::guardar_referencia([
                        'FK_ENTIDAD' => $datos['CODIGO_ENTIDAD'],
                        'FK_VENTA' => $venta,
                        'MONTO' => $datos['GIRO'],
                        'MONEDA' => 1
                    ]);
                } 
            }

            /*  --------------------------------------------------------------------------------- */

            // INSERTAR PAGO VALE
            
            if ($datos['VALE'] !== '0' && $datos['VALE'] !== '0.00' && $datos['VALE'] !== NULL) {

                VentaVale::guardar_referencia([
                        'FK_VENTA' => $venta,
                        'MONTO' => $datos['VALE'],
                        'MONEDA' => $moneda
                ]);
                
            }

            /*  --------------------------------------------------------------------------------- */

            // INSERTAR PAGO CREDITO
            
            if ($datos['CREDITO'] !== '0' && $datos['CREDITO'] !== '0.00' && $datos['CREDITO'] !== NULL) {

                VentaCredito::guardar_referencia([
                        'FK_VENTA' => $venta,
                        'MONTO' => $datos['CREDITO'],
                        'MONEDA' => $datos['MONEDA'],
                        'DIAS_CREDITO' => $datos['DIAS_CREDITO'],
                        'FECHA_CREDITO_FIN' => $datos['CREDITO_FIN'],
                        'SALDO' => $datos['CREDITO']
                ]);
                
            }

            /*  --------------------------------------------------------------------------------- */

            // INSERTAR PAGO CHEQUES 

            $pago_cheque = VentaCheque::guardar_referencia($datos['CHEQUE'], $venta);

            if ($pago_cheque["response"] === false) {
                return $pago_cheque;
            }

            /*  --------------------------------------------------------------------------------- */

            // INSERTAR DESCUENTO GENERAL

            if ($datos['DESCUENTO_GENERAL_PORCENTAJE'] > 0) {
                Ventas_Descuento::guardar_referencia($datos['DESCUENTO_GENERAL_PORCENTAJE'], $datos['DESCUENTO_GENERAL'], $venta, $datos['MONEDA']);
            }

            /*  --------------------------------------------------------------------------------- */

            // ENVIAR TRANSACCION A BD

            DB::connection('retail')->commit();

            /*  --------------------------------------------------------------------------------- */

            // RETORNAR VALOR 

            return ["response" => true, "statusText" => "Se ha guardado correctamente los metodos de pago !"];

            /*  --------------------------------------------------------------------------------- */


        } catch (Exception $e) {
            
           /*  --------------------------------------------------------------------------------- */

           // NO GUARDAR NINGUN CAMBIO 

           DB::connection('retail')->rollBack();
           throw $e;
           
           /*  --------------------------------------------------------------------------------- */

        }

    }

    public static function metodos_pago_credito($datos, $abono){

        /*  --------------------------------------------------------------------------------- */

        // METODOS DE PAGO
        
        try {

            /*  --------------------------------------------------------------------------------- */

            // INICIAR TRANSACCION 

            DB::connection('retail')->beginTransaction();

            /*  --------------------------------------------------------------------------------- */

            // INSERTAR PAGO TARJETA
            
            if ($datos['CODIGO_TARJETA'] !== '0' && $datos['CODIGO_TARJETA'] !== '0.00' && $datos['CODIGO_TARJETA'] !== NULL) {
                if ($datos['CODIGO_TARJETA'] !== '') {
                    $pago_tarjeta = VentaAbonoTarjeta::guardar_referencia([
                        'FK_TARJETA' => $datos['CODIGO_TARJETA'],
                        'FK_ABONO' => $abono,
                        'MONTO' => $datos['TARJETA'],
                        'MONEDA' => 1
                    ]);
                } 
            }

            /*  --------------------------------------------------------------------------------- */

            // INSERTAR PAGO TRANSFERENCIA
            
            if ($datos['CODIGO_BANCO'] !== '0' && $datos['CODIGO_BANCO'] !== '0.00' && $datos['CODIGO_BANCO'] !== NULL) {
                if ($datos['CODIGO_BANCO'] !== '') {
                    VentaAbonoTransferencia::guardar_referencia([
                        'FK_BANCO' => $datos['CODIGO_BANCO'],
                        'FK_ABONO' => $abono,
                        'MONTO' => $datos['TRANSFERENCIA'],
                        'MONEDA' => 1
                    ]);
                } 
            }

            /*  --------------------------------------------------------------------------------- */

            // INSERTAR PAGO GIRO
            
            if ($datos['CODIGO_ENTIDAD'] !== '0' && $datos['CODIGO_ENTIDAD'] !== '0.00' && $datos['CODIGO_ENTIDAD'] !== NULL) {
                if ($datos['CODIGO_ENTIDAD'] !== '') {
                    VentaAbonoGiro::guardar_referencia([
                        'FK_ENTIDAD' => $datos['CODIGO_ENTIDAD'],
                        'FK_ABONO' => $abono,
                        'MONTO' => $datos['GIRO'],
                        'MONEDA' => 1
                    ]);
                } 
            }

            /*  --------------------------------------------------------------------------------- */

            // MONEDAS
            
            /*  --------------------------------------------------------------------------------- */

            // GUARANIES 

            if ($datos['GUARANIES'] !== '0' && $datos['GUARANIES'] !== '0.00' && $datos['GUARANIES'] !== NULL) {
                if ($datos['GUARANIES'] !== '') {
                    VentaAbonoMoneda::guardar_referencia([
                        'FK_ABONO' => $abono,
                        'MONTO' => $datos['GUARANIES'],
                        'FK_MONEDA' => 1
                    ]);
                } 
            }

            /*  --------------------------------------------------------------------------------- */

            // DOLARES 
            
            if ($datos['DOLARES'] !== '0' && $datos['DOLARES'] !== '0.00' && $datos['DOLARES'] !== NULL) {
                if ($datos['DOLARES'] !== '') {
                    VentaAbonoMoneda::guardar_referencia([
                        'FK_ABONO' => $abono,
                        'MONTO' => $datos['DOLARES'],
                        'FK_MONEDA' => 2
                    ]);
                } 
            }

            /*  --------------------------------------------------------------------------------- */

            // PESOS 
            
            if ($datos['PESOS'] !== '0' && $datos['PESOS'] !== '0.00' && $datos['PESOS'] !== NULL) {
                if ($datos['PESOS'] !== '') {
                    VentaAbonoMoneda::guardar_referencia([
                        'FK_ABONO' => $abono,
                        'MONTO' => $datos['PESOS'],
                        'FK_MONEDA' => 3
                    ]);
                } 
            }

            /*  --------------------------------------------------------------------------------- */

            // REALES 
            
            if ($datos['REALES'] !== '0' && $datos['REALES'] !== '0.00' && $datos['REALES'] !== NULL) {
                if ($datos['REALES'] !== '') {
                    VentaAbonoMoneda::guardar_referencia([
                        'FK_ABONO' => $abono,
                        'MONTO' => $datos['REALES'],
                        'FK_MONEDA' => 4
                    ]);
                } 
            }

            /*  --------------------------------------------------------------------------------- */

            /*  --------------------------------------------------------------------------------- */

            // INSERTAR PAGO VALE
            
            if ($datos['VALE'] !== '0' && $datos['VALE'] !== '0.00' && $datos['VALE'] !== NULL) {

                VentaAbonoVale::guardar_referencia([
                        'FK_ABONO' => $abono,
                        'MONTO' => $datos['VALE'],
                        'MONEDA' => $moneda
                ]);
                
            }


            /*  --------------------------------------------------------------------------------- */

            // INSERTAR PAGO CHEQUES 

            $pago_cheque = VentaAbonoCheque::guardar_referencia($datos['CHEQUE'], $abono);

            if ($pago_cheque["response"] === false) {
                return $pago_cheque;
            }

            /*  --------------------------------------------------------------------------------- */

            // ENVIAR TRANSACCION A BD

            DB::connection('retail')->commit();

            /*  --------------------------------------------------------------------------------- */

            // RETORNAR VALOR 

            return ["response" => true, "statusText" => "Se ha guardado correctamente los metodos de pago !"];

            /*  --------------------------------------------------------------------------------- */


        } catch (Exception $e) {
            
           /*  --------------------------------------------------------------------------------- */

           // NO GUARDAR NINGUN CAMBIO 

           DB::connection('retail')->rollBack();
           throw $e;
           
           /*  --------------------------------------------------------------------------------- */

        }

    }

    public static function pagoCredito($data){

        /*  --------------------------------------------------------------------------------- */

        // PAGO AL ENTREGAR FUNCION
        
        try {

            /*  --------------------------------------------------------------------------------- */

            // INICIAR TRANSACCION 

            DB::connection('retail')->beginTransaction();

            /*  --------------------------------------------------------------------------------- */
            
            // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

            $user = auth()->user();

            /*  --------------------------------------------------------------------------------- */

            // FECHA 

            $fecha = date('Y-m-d H:i:s');
            $hora = date("H:i:s");

            /*  --------------------------------------------------------------------------------- */

            // OBTENER TODAS LAS VARIABLES INICIALIZADAS 

            $datos = Venta::iniciar_variables_venta($data);

            /*  --------------------------------------------------------------------------------- */

            // OBTENER ID CLIENTE 

            $id_cliente = (Cliente::id_cliente($data['data']['cliente']))['ID_CLIENTE'];

            /*  --------------------------------------------------------------------------------- */

            // INSERTAR PAGO 

            $venta_abono_id = (VentaAbono::insertar_abono([
                            'PAGO' => ($datos['EFECTIVO'] - $datos['VUELTO']), 
                            'MONEDA' => $datos['MONEDA'], 
                            'FECHA' => $fecha,
                            'SALDO' => $datos['SALDO'],
                            'VUELTO' => $datos['VUELTO'],
                            'CAJA' => $data['data']['caja'],
                            'FK_CLIENTE' => $id_cliente,
                            'FK_USER' => $user->id,
                            'FK_SUCURSAL' => $user->id_sucursal
                        ]))['valor'];

            /*  --------------------------------------------------------------------------------- */

            // METODOS PAGO
            
            Venta::metodos_pago_credito($datos, $venta_abono_id);

            /*  --------------------------------------------------------------------------------- */

            // CALCULO DE PAGO PRO VENTA 

            $creditos = VentaCredito::obtener_creditos_cliente($data["data"]["cliente"]);
            
            if ($creditos["response"] === false) {
                return $creditos;
            } else {
                $creditos = $creditos["creditos"];
            }

            /*  --------------------------------------------------------------------------------- */

            // RECORRER VENTAS CON CREDITO 

            foreach ($creditos as $key => $value) {
                
                if ($value->SALDO >= $datos['EFECTIVO']) {

                    /*  --------------------------------------------------------------------------------- */

                    // INSERTAR VENTAS DET CREDITO 

                    VentaAbonoDet::guardar_referencia([
                        'FK_VENTA' => $value->FK_VENTA,
                        'FK_VENTAS_ABONO' => $venta_abono_id,
                        'PAGO' => $datos['EFECTIVO']
                    ]);

                    /*  --------------------------------------------------------------------------------- */

                    // ACTUALIZAR VENTA CREDITO 

                    VentaCredito::where('FK_VENTA', '=', $value->FK_VENTA)
                    ->update([
                        'PAGO' => \DB::raw('PAGO + '.$datos['EFECTIVO'].''),
                        'SALDO' => ($value->SALDO - $datos['EFECTIVO'])
                    ]);

                    /*  --------------------------------------------------------------------------------- */

                    // FIJAR CERO EFECTIVO 

                    $datos['EFECTIVO'] = 0;

                    /*  --------------------------------------------------------------------------------- */

                } else {

                    /*  --------------------------------------------------------------------------------- */

                    // INSERTAR VENTAS DET CREDITO 

                    VentaAbonoDet::guardar_referencia([
                        'FK_VENTA' => $value->FK_VENTA,
                        'FK_VENTAS_ABONO' => $venta_abono_id,
                        'PAGO' => $value->SALDO
                    ]);

                    /*  --------------------------------------------------------------------------------- */

                    // ACTUALIZAR VENTA CREDITO 

                    VentaCredito::where('FK_VENTA', '=', $value->FK_VENTA)
                    ->update([
                        'PAGO' => \DB::raw('PAGO + '.$value->SALDO.''),
                        'SALDO' => 0
                    ]);

                    /*  --------------------------------------------------------------------------------- */

                    // FIJAR CERO EFECTIVO 

                    $datos['EFECTIVO'] = $datos['EFECTIVO'] - $value->SALDO;

                    /*  --------------------------------------------------------------------------------- */

                }

            }

            /*  --------------------------------------------------------------------------------- */

            // ACTUALIZAR CREDITO CLIENTE 
            
            Cliente::actualizarCredito($id_cliente);

            /*  --------------------------------------------------------------------------------- */

            // ENVIAR TRANSACCION A BD

            DB::connection('retail')->commit();

            /*  --------------------------------------------------------------------------------- */

            // RETORNAR VALOR 

            return ["response" => true, "statusText" => "Se ha guardado correctamente el pago !", "CODIGO" => $venta_abono_id, "CAJA" => $data['data']['caja']];

            /*  --------------------------------------------------------------------------------- */


        } catch (Exception $e) {
            
           /*  --------------------------------------------------------------------------------- */

           // NO GUARDAR NINGUN CAMBIO 

           DB::connection('retail')->rollBack();
           throw $e;
           
           /*  --------------------------------------------------------------------------------- */

        }
    }

    public static function reporteUnico($dato){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
        $dia = date("Y-m-d");

        /*  --------------------------------------------------------------------------------- */

        // OBTENER DATOS DE CABECERA 

        $pedido = Venta::mostrar_cabecera(['codigo' => $dato['codigo'], 'caja' => $dato['caja']]);

        /*  --------------------------------------------------------------------------------- */

        // OBTENER DATOS DETALLE 

        $pedido_det = Venta::mostrar_cuerpo(['codigo' => $dato['codigo'], 'caja' => $dato['caja']]);

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
        $c_rows_array = count($pedido_det);
        $c_filas_total = count($pedido_det);
        $codigo = $pedido->ID;
        $cliente = $pedido->CLIENTE;
        $direccion = $pedido->DIRECCION;
        $ruc = $pedido->RUC;
        $documento = $pedido->RUC;
        $telefono = $pedido->TELEFONO;
        $celular = $pedido->CELULAR;
        $ciudad = $pedido->CIUDAD;
        $fecha = $pedido->FECALTAS;
        $ruc = $pedido->RUC;
        $email = $pedido->EMAIL;
        $nombre = 'Orden_'.$codigo.'_'.time().'';
        $articulos = [];
        $cantidad = 0;
        $moneda = $pedido->MONEDA;
        $switch_hojas = false;
        $namefile = 'boleta_de_pedido_'.time().'.pdf';
        $letra = '';
        
        
        $total =  (Cotizacion::CALMONED(['monedaProducto' => $moneda, 'monedaSistema' => (int)$dato['moneda'], 'precio' => Common::quitar_coma($pedido->TOTAL, 2), 'decSistema' => $pedido->CANDEC, 'tab_unica' => $tab_unica, "id_sucursal" => $user->id_sucursal]))['valor'];
        $factura = $pedido->ID;

        $total = Common::precio_candec(Common::quitar_coma($total, 2), (int)$dato['moneda']);

        /*  --------------------------------------------------------------------------------- */

        // CARGAR VARIABLES CABECERA

        $data['cliente'] = $cliente;
        $data['documento'] = $documento;
        $data['ciudad'] = $ciudad;
        $data['direccion'] = $direccion;
        $data['telefono'] = $telefono;
        $data['codigo'] = $codigo;
        $data['dia'] = $dia;
        $data['ruc'] = $ruc;        
        $data['fecha'] = $fecha;
        $data['nombre'] = $nombre;
        $data['c'] = $c;
        $data['tipo'] = 'online';
        $data['total'] = $total;
        $data['ruc'] = $ruc;
        $data['factura'] = $factura;
        $data['email'] = $email;

        $total = 0;
        
        // INICIAR MPDF 

        $mpdf = new \Mpdf\Mpdf([
            'margin_left' => 20,
            'margin_right' => 20,
            'margin_top' => 40,
            'margin_bottom' => 10,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $mpdf->SetDisplayMode('fullpage');

        foreach ($pedido_det as $key => $value) {

            $totalP = $value["PRECIO"];

            //COTIZACION

           $cotizacion = $totalP/($value["CANTIDAD"]*$value["PRECIO_UNIT"]);

            // PRECIO 

           $precio = $cotizacion*$value["PRECIO_UNIT"];

            // SI NO ENCUENTRA COTIZACION RETORNAR 

            $articulos[$c_rows]["precio"] = (Cotizacion::CALMONED(['monedaProducto' => $moneda, 'monedaSistema' => (int)$dato['moneda'], 'precio' => Common::quitar_coma($precio, 2), 'decSistema' => 2, 'tab_unica' => $tab_unica, "id_sucursal" => $user->id_sucursal]))['valor'];

            $articulos[$c_rows]["precio"] = Common::precio_candec(Common::quitar_coma($articulos[$c_rows]["precio"], 2), (int)$dato['moneda']);
            // CARGAR VARIABLES 

            $articulos[$c_rows]["cantidad"] = $value["CANTIDAD"];
            $articulos[$c_rows]["cod_prod"] = $value["COD_PROD"];
            $articulos[$c_rows]["descripcion"] = $value["DESCRIPCION"];
            $articulos[$c_rows]["total"] = (Cotizacion::CALMONED(['monedaProducto' => $moneda, 'monedaSistema' => (int)$dato['moneda'], 'precio' => Common::quitar_coma($totalP, 2), 'decSistema' => 2, 'tab_unica' => $tab_unica, "id_sucursal" => $user->id_sucursal]))['valor'];
            $articulos[$c_rows]["peso"] = 'INDEFINIDO';

            $articulos[$c_rows]["total"] = Common::precio_candec(Common::quitar_coma($articulos[$c_rows]["total"], 2), (int)$dato['moneda']);
            
            $total = Common::quitar_coma($total, 2) +Common::quitar_coma($totalP,2);
            

            // CONTAR CANTIDAD DE FILAS DE HOJAS 

            $c_rows = $c_rows + 1;    

            // CONTAR LA CANTIDAD DE FILAS 

            $c = $c + 1;

            // SI CANTIDAD DE FILAS ES IGUAL A 18 ENTONCES CREAR PAGINA 

            if ($c_rows === 10){

                // AGREGAR ARTICULOS 

                $data['articulos'] = $articulos;

                // RESTAR LAS CANTIDADES CARGADAS 

                $c_rows_array = $c_rows_array - 10;

                // PONER TRUE SWITCH YA QUE CREO UNA PAGINA 

                $switch_hojas = true;

                // CARGAR SUB TOTALES POR HOJA


                $data['total'] = (Cotizacion::CALMONED(['monedaProducto' => $moneda, 'monedaSistema' => (int)$dato['moneda'], 'precio' => Common::quitar_coma($total, 2), 'decSistema' => 2, 'tab_unica' => $tab_unica, "id_sucursal" => $user->id_sucursal]))['valor'];
                $data['total'] = Common::precio_candec(Common::quitar_coma($data['total'], 2), (int)$dato['moneda']);

                $html = view('pdf.facturaPedido', $data)->render();

                // SI NO ES LA PRIMERA HOJA AGREGAR PAGINA

                if ($c !== 10) {
                    $mpdf->AddPage();
                }

                // CERAR CONTADOR DE FILAS CARGADAS POR HOJAS Y ARTICULOS

                $c_rows = 0;
                $data['articulos'] = [];
                $articulos = [];
                    
                $mpdf->WriteHTML($html);

            } else if ($c_rows_array < 10 && $c_filas_total === $c) {
                
                // AGREGAR ARTICULOS 
                
                $data['articulos'] = $articulos;
                $data['total'] = (Cotizacion::CALMONED(['monedaProducto' => $moneda, 'monedaSistema' => (int)$dato['moneda'], 'precio' => Common::quitar_coma($total, 2), 'decSistema' => 2, 'tab_unica' => $tab_unica, "id_sucursal" => $user->id_sucursal]))['valor'];
                $data['total'] = Common::precio_candec(Common::quitar_coma($data['total'], 2), (int)$dato['moneda']);

                // CREAR HOJA 
                
                $html = view('pdf.facturaPedido', $data)->render();

                if ($switch_hojas === true) {
                    $mpdf->AddPage();
                }
                    
                $mpdf->WriteHTML($html);
            }
        }

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle("Calbea/Factura");
        
        // DESCARGAR ARCHIVO PDF 

        $mpdf->Output();

        /*  --------------------------------------------------------------------------------- */
    }

}
