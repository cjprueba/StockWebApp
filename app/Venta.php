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
use App\VentaCupon;
use App\Cupon;
use App\Cliente_tiene_Cupon;
use App\NotaCredito;
use App\User_Supervisor;
use App\VentaTieneNotaCredito;
use App\VentaRetencion;
use App\VentasTieneAgencia;
use App\VentasTieneAutorizacion;
use App\VentasCreditoTieneNotaCredito;
use App\VentaAnuladoTieneAutorizacion;
use App\Movimiento_Caja;
use App\Producto;
use DateTime;
use App\LoteTieneDescuento;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;

class Venta extends Model
{
    protected $connection = 'retail';
    protected $table = 'ventas';
    public $timestamps = false;

    public static function ventas($fecha){
    	
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
        
        /*  --------------------------------------------------------------------------------- */

        // LLAMAR LA CONSULTA - PRIMERA CAJA
        
        // Año y dia Actual

        $diaActualR = Venta::select(DB::raw('SUM(VENTAS.TOTAL) AS TOTAL'))
        ->whereYear('VENTAS.FECALTAS', $anio)
        ->whereMonth('VENTAS.FECALTAS', $mes)
        ->whereDay('VENTAS.FECALTAS', $dia)
        ->where('VENTAS.ID_SUCURSAL', '=', $sucursal)
        ->groupBy('VENTAS.ID_SUCURSAL')
        ->get();

        // Año y mes Anterior
        
        $diaAnteriorR = Venta::select(DB::raw('SUM(VENTAS.TOTAL) AS TOTAL'))
        ->whereYear('VENTAS.FECALTAS', $diaAnioAnterior)
        ->whereMonth('VENTAS.FECALTAS', $diaMesAnterior)
        ->whereDay('VENTAS.FECALTAS', $diaAnterior)
        ->where('VENTAS.ID_SUCURSAL', '=', $sucursal)
        ->groupBy('VENTAS.ID_SUCURSAL')
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // LLAMAR LA CONSULTA - SEGUNDA CAJA
    	
        // Año y mes Actual

        $mesActualR = Venta::select(DB::raw('SUM(VENTAS.TOTAL) AS TOTAL'))
    	->whereYear('VENTAS.FECALTAS', $anio)
        ->whereMonth('VENTAS.FECALTAS', $mes)
    	->where('VENTAS.ID_SUCURSAL', '=', $sucursal)
    	->groupBy('VENTAS.ID_SUCURSAL')
    	->get();

        // Año y mes Anterior
        
        $mesAnteriorR = Venta::select(DB::raw('SUM(VENTAS.TOTAL) AS TOTAL'))
        ->whereYear('VENTAS.FECALTAS', $anioAnterior)
        ->whereMonth('VENTAS.FECALTAS', $mesAnterior)
        ->where('VENTAS.ID_SUCURSAL', '=', $sucursal)
        ->groupBy('VENTAS.ID_SUCURSAL')
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // LLAMAR LA CONSULTA - TERCERA CAJA
        
        // Año Actual

        $anioActualR = Venta::join('SUCURSALES', 'ID_SUCURSAL', '=', 'SUCURSALES.CODIGO')
        ->select(DB::raw('SUM(VENTAS.TOTAL) AS TOTAL, VENTAS.ID_SUCURSAL, SUCURSALES.DESCRIPCION AS SUCURSAL'))
        ->whereYear('VENTAS.FECALTAS', $anio)
        ->where('VENTAS.ID_SUCURSAL', '=', $sucursal)
        ->groupBy('VENTAS.ID_SUCURSAL')
        ->get();

        // Año Anterior
        
        $anioAnteriorR = Venta::select(DB::raw('SUM(VENTAS.TOTAL) AS TOTAL'))
        ->whereYear('VENTAS.FECALTAS', $anio - 1)
        ->where('VENTAS.ID_SUCURSAL', '=', $sucursal)
        ->groupBy('VENTAS.ID_SUCURSAL')
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // CUARTA CAJA
        
        // CANTIDAD DE ANULACIONES

        $anulacionesActual = Ventas_det::select(DB::raw('COUNT(ANULADO) AS ANULADO'))
        ->whereYear('VENTASDET.FECALTAS', $anio)
        ->whereMonth('VENTASDET.FECALTAS', $mes)
        ->where('VENTASDET.ID_SUCURSAL', '=', $sucursal)
        ->where('VENTASDET.ANULADO', '=', 1)
        ->groupBy('VENTASDET.CODIGO')
        ->groupBy('VENTASDET.CAJA')
        ->get();

        /*  --------------------------------------------------------------------------------- */

        $anulacionesActualTotal = Ventas_det::select(DB::raw('SUM(PRECIO) AS TOTAL'))
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
    }

    public static function generarReporteVenta($datos){

        // INCICIAR VARIABLES 

        $insert = $datos["data"]["Insert"];
        $marcas[] = array();
        $categorias[] = array();
        $totales[] = array();
        $marcas_array = array();
        $marcas_categoria_array = array();
        $marcas_productos_array = array();
        $user = auth()->user();
        $user = $user->id;
        $inicio = date('Y-m-d', strtotime($datos["data"]['Inicio']));
        $final = date('Y-m-d', strtotime($datos["data"]['Final']));
        $sucursal = $datos["data"]['Sucursal'];
        $total_general = 0;
        $total_descuento = 0;
        $total_preciounit = 0;
        $cantidadvendida = 0;
        $costo = 0;
        $totalcosto = 0;

        if($insert == true){

            if(isset($datos["data"]["Tipo"])){

                $datos = array(
                    'inicio' => date('Y-m-d', strtotime($datos["data"]['Inicio'])),
                    'final' => date('Y-m-d', strtotime($datos["data"]['Final'])),
                    'sucursal' => $datos["data"]['Sucursal'],
                    'checkedCategoria' => $datos["data"]['AllCategory'],
                    'checkedMarca' => $datos["data"]['AllBrand'],
                    'checkedProveedor' => $datos["data"]["AllProveedores"],
                    'proveedores' => $datos["data"]["Proveedores"],
                    'marcas' => $datos["data"]['Marcas'],
                    'linea' => $datos["data"]['Categorias'],
                    'tipos' => $datos["data"]["Tipo"]
                );
            }else{

                $datos = array(
                    'inicio'=> date('Y-m-d', strtotime($datos["data"]['Inicio'])),
                    'final'=>date('Y-m-d', strtotime($datos["data"]['Final'])),
                    'sucursal' => $datos["data"]['Sucursal'],
                    'checkedCategoria' => $datos["data"]['AllCategory'],
                    'checkedMarca' => $datos["data"]['AllBrand'],
                    'checkedProveedor' => $datos["data"]["AllProveedores"],
                    'proveedores' => $datos["data"]["Proveedores"],
                    'marcas' => $datos["data"]['Marcas'],
                    'linea' => $datos["data"]['Categorias']
                );
            }
            
            Temp_venta::insertar_reporte($datos);
        }
        
        $temp = DB::connection('retail')->table('temp_ventas')->select(
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

            $total_general = $total_general + $value->TOTAL;
            $total_descuento = $total_descuento + $value->DESCUENTO;
            $total_preciounit = $total_preciounit + $value->PRECIO_UNIT;
            $cantidadvendida = $cantidadvendida + $value->VENDIDO;
            $costo = $costo + $value->COSTO_UNIT;
            $totalcosto = $totalcosto + $value->COSTO_TOTAL;

            $marcas_array[] = array(
                'TOTALES' => $value->DESCRI_M,
                'VENDIDO' => $value->VENDIDO,
                'DESCUENTO' => $value->DESCUENTO,
                'COSTO' => $value->COSTO_UNIT,
                'COSTO_TOTAL' => $value->COSTO_TOTAL,
                'PRECIO' => $value->PRECIO_UNIT,
                'TOTAL' => $value->TOTAL,
                'MARCAS' => $value->MARCA
            );
        }

        $ser = VentasDetServicios::leftjoin('VENTAS',function($join){
                $join->on('VENTAS.CODIGO','=','VENTASDET_SERVICIOS.CODIGO')
                     ->on('VENTAS.CAJA','=','VENTASDET_SERVICIOS.CAJA')
                     ->on('VENTAS.ID_SUCURSAL','=','VENTASDET_SERVICIOS.ID_SUCURSAL');
                })
            ->leftjoin('VENTAS_ANULADO', 'VENTAS_ANULADO.FK_VENTA', '=', 'VENTAS.ID')
            ->select(DB::raw('
                SUM(VENTASDET_SERVICIOS.PRECIO) AS PRECIO_SERVICIO,
                sum(VENTASDET_SERVICIOS.CANTIDAD) AS VENDIDO,
                sum(VENTASDET_SERVICIOS.PRECIO_UNIT) AS PRECIO_UNIT')) 
        ->Where('VENTAS_ANULADO.ANULADO','=',0)
        ->Where('VENTAS.ID_SUCURSAL','=',$sucursal)
        ->whereBetween('VENTAS.FECALTAS', [$inicio, $final])
        ->get()
        ->toArray();

        if(count($ser) > 0){
    
            $total_general = $total_general + $ser[0]->PRECIO_SERVICIO;
            $total_preciounit = $total_preciounit + $ser[0]->PRECIO_UNIT;
            $cantidadvendida = $cantidadvendida + $ser[0]->VENDIDO;
        
            $marcas_array[] = array(
                'TOTALES' => 'SERVICIO DE DELIVERY',
                'VENDIDO' => $ser[0]->VENDIDO,
                'DESCUENTO' =>'0',
                'COSTO' => '0',
                'COSTO_TOTAL' => '0',
                'PRECIO' => $ser[0]->PRECIO_UNIT,
                'TOTAL' => $ser[0]->PRECIO_SERVICIO,
            );
        }

        //TOTALES POR CATEGORIA AGRUPADOS POR MARCA
        /*  --------------------------------------------------------------------------------- */
                              
        $temp = DB::connection('retail')->table('temp_ventas')->select(
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
                             
        foreach ($temp as $key => $value){

            $marcas_categoria_array[] = array(
                'MARCA' => $value->MARCA,
                'DESCRI_M' => $value->DESCRI_M,
                'LINEA' =>  $value->LINEA,
                'DESCRI_L' => $value->DESCRI_L,
                'VENDIDO' => $value->VENDIDO,
                'COSTO_TOTAL' => $value->COSTO_TOTAL,
                'TOTAL' => $value->TOTAL,
                'DESCUENTO' =>$value->DESCUENTO,
            );
        }
        
        /*  --------------------------------------------------------------------------------- */  
        
        //TRAER TODOS LOS PRODUCTOS CON EL CODIGO DE MARCA
        
        /*  --------------------------------------------------------------------------------- */
                   
        $temp = DB::connection('retail')->table('temp_ventas')->select(
            DB::raw('temp_ventas.COD_PROD AS COD_PROD'),
            DB::raw('temp_ventas.LOTE AS LOTE'),
            DB::raw('SUM(temp_ventas.VENDIDO) AS VENDIDO'),
            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = temp_ventas.COD_PROD) AND (l.ID_SUCURSAL temp_ventas.ID_SUCURSAL))),0)   AS STOCK'),
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

        $total_general = 0;
        $total_descuento = 0;
        $total_preciounit = 0;
        $cantidadvendida = 0;
        $costo = 0;
        $totalcosto = 0;
           
        foreach ($temp as $key => $value){

            if($value->TOTAL == 0){
                $value->TOTAL = '0';
            }

            if($value->PRECIO_UNIT == 0){
                $value->PRECIO_UNIT = '0';
            }

            if($value->STOCK == 0){
                $value->STOCK = '0';
            }

            if($value->DESCUENTO == 0){
                $value->DESCUENTO = '0';
            }
         
            $total_general = $total_general + $value->TOTAL;
            $total_descuento = $total_descuento + $value->DESCUENTO;
            $total_preciounit = $total_preciounit + $value->PRECIO_UNIT;
            $cantidadvendida = $cantidadvendida + $value->VENDIDO;
            $costo = $costo + $value->COSTO_UNIT;
            $totalcosto = $totalcosto + $value->COSTO_TOTAL;
                  
            $marcas_productos_array[] = array(
                'COD_PROD' => $value->COD_PROD,
                'LOTE' => $value->LOTE,
                'STOCK' => $value->STOCK,
                'CATEGORIA' => $value->CATEGORIA,
                'SUBCATEGORIA' => $value->SUBCATEGORIA,
                'MARCA' => $value->MARCA,
                'VENDIDO' => $value->VENDIDO,
                'PRECIO_UNIT' => $value->PRECIO_UNIT,
                'TOTAL' => $value->TOTAL,
                'DESCUENTO' => $value->DESCUENTO,
                'COSTO_UNIT' => $value->COSTO_UNIT,
                'COSTO_TOTAL' => $value->COSTO_TOTAL,
                'DESCUENTO_PORCENTAJE' => $value->DESCUENTO_PRODUCTO,
                'MARCAS_CODIGO' => $value->MARCAS_CODIGO
            );
        }
        
        /*  --------------------------------------------------------------------------------- */

        // RETORNAR TODOS LOS ARRAYS

        return ['ventas' => $marcas_productos_array, 'marcas' => $marcas_array, 'categorias' => $marcas_categoria_array];
    }

    public static function generarConsulta($datos) {
         
        // INCICIAR VARIABLES 

        $marcas[] = array();
        $categorias[] = array();
        $totales[] = array();
        $inicio = date('Y-m-d', strtotime($datos['Inicio']));
        $final = date('Y-m-d', strtotime($datos['Final']));
        $sucursal = $datos['Sucursal'];

        /*  --------------------------------------------------------------------------------- */

        /*  *********** TODAS LAS VENTAS ENTRE LAS FECHAS INTERVALOS *********** */

        if ($datos['AllCategory'] AND $datos['AllBrand']){

            $ventasdet = DB::connection('retail')->table('VENTASDET as v')
                ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
                ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
                ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
                ->select(
                    DB::raw('SUM(v.PRECIO) AS PRECIO'),
                    DB::raw('SUM(v.CANTIDAD) AS VENDIDO'),
                    DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
                    DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l 
                        WHERE ((l.COD_PROD = v.COD_PROD) AND (l.ID_SUCURSAL = v.ID_SUCURSAL))),0) AS STOCK'),
                    DB::raw('MARCA.DESCRIPCION AS MARCA_NOMBRE'),
                    DB::raw('LINEAS.DESCRIPCION AS LINEA_NOMBRE'),
                    DB::raw('v.COD_PROD'),
                    DB::raw('PRODUCTOS.MARCA AS MARCA'),
                    DB::raw('PRODUCTOS.LINEA AS LINEA'))  
            ->whereBetween('v.FECALTAS', [$inicio , $final])
            ->where([['v.ID_SUCURSAL', '=', $sucursal],
                ['v.ANULADO', '<>', 1],
                ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%']])
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
            ->where([['v.ID_SUCURSAL', '=', $sucursal],
                ['v.ANULADO', '<>', 1],
                ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%']])
            ->groupBy('v.COD_PROD')
            ->get()
            ->toArray(); 

        } else if ($datos['AllBrand']){
             
            $ventasdet = DB::connection('retail')->table('VENTASDET as v')
                ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'v.COD_PROD')
                ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
                ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
                ->select(DB::raw('SUM(v.PRECIO) AS PRECIO'),
                    DB::raw('SUM(v.CANTIDAD) AS VENDIDO'),
                    DB::raw('PRODUCTOS.DESCRIPCION AS DESCRIPCION'),
                    DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = v.COD_PROD) AND (l.ID_SUCURSAL = v.ID_SUCURSAL)) Group By      v.COD_PROD),0) AS STOCK'),
                    DB::raw('MARCA.DESCRIPCION AS MARCA_NOMBRE'),
                    DB::raw('LINEAS.DESCRIPCION AS LINEA_NOMBRE'),
                    DB::raw('v.COD_PROD'),
                    DB::raw('PRODUCTOS.MARCA AS MARCA'),
                    DB::raw('PRODUCTOS.LINEA AS LINEA'))  
            ->whereBetween('v.FECALTAS', [$inicio , $final])
            ->whereIn('PRODUCTOS.LINEA', $datos['Categorias'])
            ->where([['v.ID_SUCURSAL', '=', $sucursal],
                ['v.ANULADO', '<>', 1],
                ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%']])
            ->groupBy('v.COD_PROD')
            ->get()
            ->toArray(); 

        } else{

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
            ->where([['v.ID_SUCURSAL', '=', $sucursal],
                ['v.ANULADO', '<>', 1],
                ['v.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%']])
            ->groupBy('v.COD_PROD')
            ->get()
            ->toArray(); 
        }

        /*  --------------------------------------------------------------------------------- */

        /*  *********** TODOS LOS DESCUENTOS GENERALES  *********** */

        $descuentos = Ventas_det::select(DB::raw('VENTASDET.CODIGO'),
            DB::raw('substring(VENTASDET.DESCRIPCION, 11, 3) AS PORCENTAJE'),
            DB::raw('VENTASDET.CODIGO'),  
            DB::raw('VENTASDET.CAJA'),
            DB::raw('VENTASDET.ID_SUCURSAL'),
            DB::raw('VENTASDET.ITEM'))  
        ->whereBetween('VENTASDET.FECALTAS', [$inicio , $final])
        ->where([['VENTASDET.ID_SUCURSAL', '=', $sucursal],
            ['VENTASDET.ANULADO', '<>', 1],
            ['VENTASDET.DESCRIPCION', 'LIKE', 'DESCUENTO%'],
            ['VENTASDET.COD_PROD', '=', 2]])
        ->get(); 
       
        /*  --------------------------------------------------------------------------------- */

        foreach ($descuentos as $descuento) {

            /*  *********** RECORRER LAS VENTAS CON LOS DESCUENTOS GENERALES *********** */

            if ($datos['AllCategory'] AND $datos['AllBrand']) {

                $ventas_con_descuentos = Ventas_det::select(DB::raw('VENTASDET.COD_PROD'),
                        DB::raw('VENTASDET.PRECIO'),
                        DB::raw('VENTASDET.PRECIO_UNIT'),
                        DB::raw('VENTASDET.ITEM'))
                ->where([['VENTASDET.ID_SUCURSAL', '=', $descuento->ID_SUCURSAL],
                    ['VENTASDET.CODIGO', '=', $descuento->CODIGO],
                    ['VENTASDET.CAJA', '=', $descuento->CAJA],
                    ['VENTASDET.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%']])
                ->get();

            } else if ($datos['AllCategory']) { 

                $ventas_con_descuentos = Ventas_det::join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
                    ->select(DB::raw('VENTASDET.COD_PROD'),
                        DB::raw('VENTASDET.PRECIO'),
                        DB::raw('VENTASDET.PRECIO_UNIT'),
                        DB::raw('VENTASDET.ITEM'))  
                ->whereBetween('VENTASDET.FECALTAS', [$inicio , $final])
                ->whereIn('PRODUCTOS.MARCA', $datos['Marcas'])
                ->where([['VENTASDET.ID_SUCURSAL', '=', $descuento->ID_SUCURSAL],
                    ['VENTASDET.CODIGO', '=', $descuento->CODIGO],
                    ['VENTASDET.CAJA', '=', $descuento->CAJA],
                    ['VENTASDET.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%']])
                ->get();

            } else if ($datos['AllBrand']) { 

                $ventas_con_descuentos = Ventas_det::join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
                    ->select(DB::raw('VENTASDET.COD_PROD'),
                        DB::raw('VENTASDET.PRECIO'),
                        DB::raw('VENTASDET.PRECIO_UNIT'),
                        DB::raw('VENTASDET.ITEM'))  
                ->whereBetween('VENTASDET.FECALTAS', [$inicio , $final])
                ->whereIn('PRODUCTOS.LINEA', $datos['Categorias'])
                ->where([['VENTASDET.ID_SUCURSAL', '=', $descuento->ID_SUCURSAL],
                    ['VENTASDET.CODIGO', '=', $descuento->CODIGO],
                    ['VENTASDET.CAJA', '=', $descuento->CAJA],
                    ['VENTASDET.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%']])
                ->get();

            } else {

                $ventas_con_descuentos = Ventas_det::join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
                    ->select(DB::raw('VENTASDET.COD_PROD'),
                        DB::raw('VENTASDET.PRECIO'),
                        DB::raw('VENTASDET.PRECIO_UNIT'),
                        DB::raw('VENTASDET.ITEM'))  
                ->whereBetween('VENTASDET.FECALTAS', [$inicio , $final])
                ->whereIn('PRODUCTOS.MARCA', $datos['Marcas'])
                ->whereIn('PRODUCTOS.LINEA', $datos['Categorias'])
                ->where([['VENTASDET.ID_SUCURSAL', '=', $descuento->ID_SUCURSAL],
                    ['VENTASDET.CODIGO', '=', $descuento->CODIGO],
                    ['VENTASDET.CAJA', '=', $descuento->CAJA],
                    ['VENTASDET.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%']])
                ->get();
            }

            /*  *********** EMPEZAR A MODIFICAR LOS VALORES DEL ARRAY *********** */

            foreach ($ventas_con_descuentos as $ventas_con_descuento) {

                if ($ventas_con_descuento->ITEM < $descuento->ITEM) {

                    $key = array_search($ventas_con_descuento->COD_PROD, array_column($ventasdet, 'COD_PROD'));
                    $ventasdet[$key]->PRECIO = (int)$ventasdet[$key]->PRECIO - (((int)$ventas_con_descuento->PRECIO * (int)$descuento->PORCENTAJE)/100);
                }
            }
        }

        /*  --------------------------------------------------------------------------------- */

        unset($marcas[0]);
        unset($categorias[0]);
        unset($totales[0]);

        /*  --------------------------------------------------------------------------------- */

        foreach ($ventasdet as $key => $value) {

            // CREAR ARRAY DE MARCAS

            if (array_key_exists($value->MARCA, $marcas)) {

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

            // CREAR ARRAY DE CATEGORIAS

            if (array_key_exists($value->MARCA.''.$value->LINEA, $categorias)) {

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
        }

        /*  --------------------------------------------------------------------------------- */

        // BUSCAR STOCK GENERAL DE TODAS CATEGORIAS

        $stockGeneral = DB::connection('retail')->table('LOTES as l')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'l.COD_PROD')
            ->select(DB::raw('SUM(l.CANTIDAD) AS CANTIDAD'),
                DB::raw('PRODUCTOS.MARCA'),
                DB::raw('PRODUCTOS.LINEA'))
        ->where('l.ID_SUCURSAL', '=', $sucursal)
        ->groupBy('PRODUCTOS.MARCA', 'PRODUCTOS.LINEA')
        ->get();

        /*  --------------------------------------------------------------------------------- */

        foreach ($stockGeneral as $key => $value) {

            if (array_key_exists($value->MARCA, $marcas)) {

                // CARGAR STOCK GENERAL A MARCA

                $marcas[$value->MARCA]["STOCK_G"] += $value->CANTIDAD;
            }

            if (array_key_exists($value->MARCA.''.$value->LINEA, $categorias)) {

                // CARGAR STOCK GENERAL CATEGORIA

                $categorias[$value->MARCA.''.$value->LINEA]["STOCK_G"] += $value->CANTIDAD;
            }
        }

        /*  --------------------------------------------------------------------------------- */

        $marca[] = (array) $marcas;
        $categoria[] = (array) $categorias;

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR TODOS LOS ARRAYS

        return ['ventas' => $ventasdet, 'marcas' => (array)$marca[0], 'categorias' => (array)$categoria[0]];
    }

    public static function generarTablaMarca($datos) {

        // INCICIAR VARIABLES 

        $inicio = date('Y-m-d', strtotime($datos['Inicio']));
        $final = date('Y-m-d', strtotime($datos['Final']));
        $mes = date('m', strtotime($datos['Inicio']));
        $anio = date('Y', strtotime($datos['Inicio']));
        $sucursal = $datos['Sucursal'];
        $total = 0;
        $totalVendido = 0;
        $totalStock = 0;

        /*  --------------------------------------------------------------------------------- */
        // CARGAR MES PASADO

        if ($mes === 1) {
            $mes = 12;
            $anio = $anio - 1;
        } else {
            $mes = $mes - 1;
        }

        /*  *********** TODAS LAS VENTAS ENTRE LAS FECHAS INTERVALOS *********** */

        $ventasdet = Ventas_det::join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
            ->select(DB::raw('SUM(VENTASDET.PRECIO) AS PRECIO'),
                DB::raw('0 AS COMPORTAMIENTO_PRECIO'),
                DB::raw('0 AS COMPORTAMIENTO_VENDIDO'), 
                DB::raw('0 AS PRECIO_ANTERIOR'), 
                DB::raw('0 AS VENDIDO_ANTERIOR'), 
                DB::raw('0 AS P_TOTAL'),    
                DB::raw('SUM(VENTASDET.CANTIDAD) AS VENDIDO'),
                DB::raw('0 AS P_VENDIDO'),
                DB::raw('0 AS STOCK_G'),
                DB::raw('0 AS P_STOCK'),
                DB::raw('MARCA.DESCRIPCION AS MARCA_NOMBRE'),
                DB::raw('PRODUCTOS.MARCA'))
        ->whereBetween('VENTASDET.FECALTAS', [$inicio , $final])
        ->where([['VENTASDET.ID_SUCURSAL', '=', $sucursal],
            ['VENTASDET.ANULADO', '<>', 1],
            ['VENTASDET.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%']])
        ->groupBy('PRODUCTOS.MARCA')
        ->get()
        ->toArray(); 

        /*  *********** MES ANTERIOR *********** */
            
        $ventasdetAnterior = Ventas_det::join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
            ->select(DB::raw('SUM(VENTASDET.PRECIO) AS PRECIO'),
                DB::raw('SUM(VENTASDET.CANTIDAD) AS VENDIDO'),
                DB::raw('PRODUCTOS.MARCA'))
        ->whereMonth('VENTASDET.FECALTAS', $mes)
        ->whereYear('VENTASDET.FECALTAS', $anio)
        ->where([['VENTASDET.ID_SUCURSAL', '=', $sucursal],
            ['VENTASDET.ANULADO', '<>', 1],
            ['VENTASDET.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%']])
        ->groupBy('PRODUCTOS.MARCA')
        ->get()
        ->toArray(); 

        /*  --------------------------------------------------------------------------------- */

        /*  *********** TODOS LOS DESCUENTOS GENERALES  *********** */

        $descuentos = Ventas_det::select(DB::raw('VENTASDET.CODIGO'),
                DB::raw('substring(VENTASDET.DESCRIPCION, 11, 3) AS PORCENTAJE'),
                DB::raw('VENTASDET.CODIGO'),  
                DB::raw('VENTASDET.CAJA'),
                DB::raw('VENTASDET.ID_SUCURSAL'),
                DB::raw('VENTASDET.ITEM'))  
        ->whereBetween('VENTASDET.FECALTAS', [$inicio , $final])
        ->where([['VENTASDET.ID_SUCURSAL', '=', $sucursal],
            ['VENTASDET.ANULADO', '<>', 1],
            ['VENTASDET.DESCRIPCION', 'LIKE', 'DESCUENTO%'],
            ['VENTASDET.COD_PROD', '=', 2]])
        ->get(); 

        /*  --------------------------------------------------------------------------------- */

        foreach ($descuentos as $descuento) {

            /*  *********** RECORRER LAS VENTAS CON LOS DESCUENTOS GENERALES *********** */
            
            $ventas_con_descuentos = Ventas_det::join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
                ->select(DB::raw('VENTASDET.PRECIO'),
                    DB::raw('VENTASDET.PRECIO_UNIT'),
                    DB::raw('VENTASDET.ITEM'),
                    DB::raw('PRODUCTOS.MARCA'))
            ->where([['VENTASDET.ID_SUCURSAL', '=', $descuento->ID_SUCURSAL],
                ['VENTASDET.CODIGO', '=', $descuento->CODIGO],
                ['VENTASDET.CAJA', '=', $descuento->CAJA],
                ['VENTASDET.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%']])
            ->get();

            /*  --------------------------------------------------------------------------------- */

            /*  *********** EMPEZAR A MODIFICAR LOS VALORES DEL ARRAY *********** */

            foreach ($ventas_con_descuentos as $ventas_con_descuento) {

                if ($ventas_con_descuento->ITEM < $descuento->ITEM) {

                    $key = array_search($ventas_con_descuento->MARCA, array_column($ventasdet, 'MARCA'));
                    $ventasdet[$key]->PRECIO = (int)$ventasdet[$key]->PRECIO - (((int)$ventas_con_descuento->PRECIO * (int)$descuento->PORCENTAJE)/100);
                }
            }
        }

        /*  --------------------------------------------------------------------------------- */

        /*  *********** TODOS LOS DESCUENTOS GENERALES MES ANTERIOR *********** */

        $descuentos = Ventas_det::select(DB::raw('VENTASDET.CODIGO'),
                DB::raw('substring(VENTASDET.DESCRIPCION, 11, 3) AS PORCENTAJE'),
                DB::raw('VENTASDET.CODIGO'),  
                DB::raw('VENTASDET.CAJA'),
                DB::raw('VENTASDET.ID_SUCURSAL'),
                DB::raw('VENTASDET.ITEM'))  
        ->whereMonth('VENTASDET.FECALTAS', $mes)
        ->whereYear('VENTASDET.FECALTAS', $anio)
        ->where([['VENTASDET.ID_SUCURSAL', '=', $sucursal],
            ['VENTASDET.ANULADO', '<>', 1],
            ['VENTASDET.DESCRIPCION', 'LIKE', 'DESCUENTO%'],
            ['VENTASDET.COD_PROD', '=', 2]])
        ->get(); 
       
        /*  --------------------------------------------------------------------------------- */

        foreach ($descuentos as $descuento) {

            /*  *********** RECORRER LAS VENTAS CON LOS DESCUENTOS GENERALES *********** */
            
            $ventas_con_descuentos = Ventas_det::join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
                ->select(DB::raw('VENTASDET.PRECIO'),
                    DB::raw('VENTASDET.PRECIO_UNIT'),
                    DB::raw('VENTASDET.ITEM'),
                    DB::raw('PRODUCTOS.MARCA'))
            ->where([['VENTASDET.ID_SUCURSAL', '=', $descuento->ID_SUCURSAL],
                    ['VENTASDET.CODIGO', '=', $descuento->CODIGO],
                    ['VENTASDET.CAJA', '=', $descuento->CAJA],
                    ['VENTASDET.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%']])
            ->get();

            /*  *********** EMPEZAR A MODIFICAR LOS VALORES DEL ARRAY *********** */

            foreach ($ventas_con_descuentos as $ventas_con_descuento) {

                if ($ventas_con_descuento->ITEM < $descuento->ITEM) {

                    $key = array_search($ventas_con_descuento->MARCA, array_column($ventasdetAnterior, 'MARCA'));
                    $ventasdetAnterior[$key]->PRECIO = (int)$ventasdetAnterior[$key]->PRECIO - (((int)$ventas_con_descuento->PRECIO * (int)$descuento->PORCENTAJE)/100);
                }
            }
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

            // $key2 = array_search($value->MARCA, array_column($ventasdet, 'MARCA'));
            // if ($key2 <> "null") {

            //     if (array_key_exists($key2, $ventasdet))   {
            //         $ventasdet[$key2]->STOCK_G += $value->CANTIDAD;   
            //     }
            // }

            $stockGeneral = DB::connection('retail')->table('LOTES as l')
                ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'l.COD_PROD')
                ->select(DB::raw('SUM(l.CANTIDAD) AS CANTIDAD'),
                    DB::raw('PRODUCTOS.MARCA'))
            ->where('PRODUCTOS.MARCA', '=', $value->MARCA)
            ->where('l.ID_SUCURSAL', '=', $sucursal)
            ->groupBy('PRODUCTOS.MARCA')
            ->get();

            $ventasdet[$key]->STOCK_G = $stockGeneral[0]->CANTIDAD;
        }

        /*  --------------------------------------------------------------------------------- */

        // TOTALES

        foreach ($ventasdet as $key => $value) {

            // OBTENER LA UBICACION DE LA MARCA EN LAS VENTAS ANTERIORES 

            $key2 = array_search($value->MARCA, array_column($ventasdetAnterior, 'MARCA'));

            // CARGAR PRECIOS ANTERIORES

            if ($key2 <> null) {

                $ventasdet[$key]->PRECIO_ANTERIOR = $ventasdetAnterior[$key2]->PRECIO;
                $ventasdet[$key]->VENDIDO_ANTERIOR = $ventasdetAnterior[$key2]->VENDIDO;

            } else if ($key2 === 0) {

                $ventasdet[$key]->PRECIO_ANTERIOR = $ventasdetAnterior[$key2]->PRECIO;
                $ventasdet[$key]->VENDIDO_ANTERIOR = $ventasdetAnterior[$key2]->VENDIDO; 
            }

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

            // CARGAR LOS TOTALES 

            $total += $value->PRECIO;
            $totalVendido += $value->VENDIDO;
            $totalStock += $value->STOCK_G;
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
    }

    public static function generarTablaCategoria($datos) {

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

        $ventasdet = Ventas_det::join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->select(DB::raw('SUM(VENTASDET.PRECIO) AS PRECIO'),
                DB::raw('0 AS COMPORTAMIENTO_PRECIO'),
                DB::raw('0 AS COMPORTAMIENTO_VENDIDO'), 
                DB::raw('0 AS PRECIO_ANTERIOR'), 
                DB::raw('0 AS VENDIDO_ANTERIOR'), 
                DB::raw('0 AS P_TOTAL'),    
                DB::raw('SUM(VENTASDET.CANTIDAD) AS VENDIDO'),
                DB::raw('0 AS P_VENDIDO'),
                DB::raw('0 AS STOCK_G'),
                DB::raw('0 AS P_STOCK'),
                DB::raw('LINEAS.DESCRIPCION AS LINEA_NOMBRE'),
                DB::raw('PRODUCTOS.LINEA'))
        ->whereBetween('VENTASDET.FECALTAS', [$inicio , $final])
        ->where([['VENTASDET.ID_SUCURSAL', '=', $sucursal],
            ['VENTASDET.ANULADO', '<>', 1],
            ['VENTASDET.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%']])
        ->groupBy('PRODUCTOS.LINEA')
        ->get()
        ->toArray(); 

        /*  *********** MES ANTERIOR *********** */
            
        $ventasdetAnterior = Ventas_det::join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->select(DB::raw('SUM(VENTASDET.PRECIO) AS PRECIO'),
                DB::raw('SUM(VENTASDET.CANTIDAD) AS VENDIDO'),
                DB::raw('LINEAS.DESCRIPCION AS LINEA_NOMBRE'),
                DB::raw('PRODUCTOS.LINEA'))
        ->whereMonth('VENTASDET.FECALTAS', $mes)
        ->whereYear('VENTASDET.FECALTAS', $anio)
        ->where([['VENTASDET.ID_SUCURSAL', '=', $sucursal],
            ['VENTASDET.ANULADO', '<>', 1],
            ['VENTASDET.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%']])
        ->groupBy('PRODUCTOS.LINEA')
        ->get()
        ->toArray(); 

        /*  --------------------------------------------------------------------------------- */

        /*  *********** TODOS LOS DESCUENTOS GENERALES  *********** */

        $descuentos = Ventas_det::select(DB::raw('VENTASDET.CODIGO'),
                DB::raw('substring(VENTASDET.DESCRIPCION, 11, 3) AS PORCENTAJE'),
                DB::raw('VENTASDET.CODIGO'),  
                DB::raw('VENTASDET.CAJA'),
                DB::raw('VENTASDET.ID_SUCURSAL'),
                DB::raw('VENTASDET.ITEM'))  
        ->whereBetween('VENTASDET.FECALTAS', [$inicio , $final])
        ->where([['VENTASDET.ID_SUCURSAL', '=', $sucursal],
            ['VENTASDET.ANULADO', '<>', 1],
            ['VENTASDET.DESCRIPCION', 'LIKE', 'DESCUENTO%'],
            ['VENTASDET.COD_PROD', '=', 2]])
        ->get(); 

        /*  --------------------------------------------------------------------------------- */

        foreach ($descuentos as $descuento) {

            /*  *********** RECORRER LAS VENTAS CON LOS DESCUENTOS GENERALES *********** */
            
            $ventas_con_descuentos = Ventas_det::join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
                ->select(DB::raw('VENTASDET.PRECIO'),
                    DB::raw('VENTASDET.PRECIO_UNIT'),
                    DB::raw('VENTASDET.ITEM'),
                    DB::raw('PRODUCTOS.LINEA'))
            ->where([['VENTASDET.ID_SUCURSAL', '=', $descuento->ID_SUCURSAL],
                ['VENTASDET.CODIGO', '=', $descuento->CODIGO],
                ['VENTASDET.CAJA', '=', $descuento->CAJA],
                ['VENTASDET.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%']])
            ->get();

            /*  *********** EMPEZAR A MODIFICAR LOS VALORES DEL ARRAY *********** */

            foreach ($ventas_con_descuentos as $ventas_con_descuento) {

                if ($ventas_con_descuento->ITEM < $descuento->ITEM) {

                    $key = array_search($ventas_con_descuento->LINEA, array_column($ventasdet, 'LINEA'));
                    $ventasdet[$key]->PRECIO = (int)$ventasdet[$key]->PRECIO - (((int)$ventas_con_descuento->PRECIO * (int)$descuento->PORCENTAJE)/100);
                }
            }
        }
        
        /*  --------------------------------------------------------------------------------- */

        /*  *********** TODOS LOS DESCUENTOS GENERALES MES ANTERIOR *********** */

        $descuentos = Ventas_det::select(DB::raw('VENTASDET.CODIGO'),
                DB::raw('substring(VENTASDET.DESCRIPCION, 11, 3) AS PORCENTAJE'),
                DB::raw('VENTASDET.CODIGO'),  
                DB::raw('VENTASDET.CAJA'),
                DB::raw('VENTASDET.ID_SUCURSAL'),
                DB::raw('VENTASDET.ITEM'))  
        ->whereMonth('VENTASDET.FECALTAS', $mes)
        ->whereYear('VENTASDET.FECALTAS', $anio)
        ->where([['VENTASDET.ID_SUCURSAL', '=', $sucursal],
            ['VENTASDET.ANULADO', '<>', 1],
            ['VENTASDET.DESCRIPCION', 'LIKE', 'DESCUENTO%'],
            ['VENTASDET.COD_PROD', '=', 2]])
        ->get(); 

        /*  --------------------------------------------------------------------------------- */

        foreach ($descuentos as $descuento) {

            /*  *********** RECORRER LAS VENTAS CON LOS DESCUENTOS GENERALES *********** */

            $ventas_con_descuentos = Ventas_det::join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
                ->select(DB::raw('VENTASDET.PRECIO'),
                    DB::raw('VENTASDET.PRECIO_UNIT'),
                    DB::raw('VENTASDET.ITEM'),
                    DB::raw('PRODUCTOS.LINEA'))
            ->where([['VENTASDET.ID_SUCURSAL', '=', $descuento->ID_SUCURSAL],
                    ['VENTASDET.CODIGO', '=', $descuento->CODIGO],
                    ['VENTASDET.CAJA', '=', $descuento->CAJA],
                    ['VENTASDET.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%']])
            ->get();

            /*  --------------------------------------------------------------------------------- */

            /*  *********** EMPEZAR A MODIFICAR LOS VALORES DEL ARRAY *********** */

            foreach ($ventas_con_descuentos as $ventas_con_descuento) {

                if ($ventas_con_descuento->ITEM < $descuento->ITEM) {

                    $key = array_search($ventas_con_descuento->LINEA, array_column($ventasdetAnterior, 'LINEA'));
                    $ventasdetAnterior[$key]->PRECIO = (int)$ventasdetAnterior[$key]->PRECIO - (((int)$ventas_con_descuento->PRECIO * (int)$descuento->PORCENTAJE)/100);
                }
            }
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

            // $key2 = array_search($value->MARCA, array_column($ventasdet, 'MARCA'));
            // if ($key2 <> "null") {

            //     if (array_key_exists($key2, $ventasdet))   {
            //         $ventasdet[$key2]->STOCK_G += $value->CANTIDAD;   
            //     }
            // }

            $stockGeneral = DB::connection('retail')->table('LOTES as l')
                ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'l.COD_PROD')
                ->select(DB::raw('SUM(l.CANTIDAD) AS CANTIDAD'),
                    DB::raw('PRODUCTOS.LINEA'))
            ->where('PRODUCTOS.LINEA', '=', $value->LINEA)
            ->where('l.ID_SUCURSAL', '=', $sucursal)
            ->groupBy('PRODUCTOS.LINEA')
            ->get();

            $ventasdet[$key]->STOCK_G = $stockGeneral[0]->CANTIDAD;
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

            // OBTENER LA UBICACION DE LA MARCA EN LAS VENTAS ANTERIORES 

            $key2 = array_search($value->LINEA, array_column($ventasdetAnterior, 'LINEA'));

            // CARGAR PRECIOS ANTERIORES

            if ($key2 <> null) {

                $ventasdet[$key]->PRECIO_ANTERIOR = $ventasdetAnterior[$key2]->PRECIO;
                $ventasdet[$key]->VENDIDO_ANTERIOR = $ventasdetAnterior[$key2]->VENDIDO;

            } else if ($key2 === 0) {

                $ventasdet[$key]->PRECIO_ANTERIOR = $ventasdetAnterior[$key2]->PRECIO;
                $ventasdet[$key]->VENDIDO_ANTERIOR = $ventasdetAnterior[$key2]->VENDIDO; 
            } 

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

            // CARGAR LOS TOTALES 

            $total += $value->PRECIO;
            $totalVendido += $value->VENDIDO;
            $totalStock += $value->STOCK_G;
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
    }

    public static function periodos_superados($request){

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

        if(empty($request->input('search.value'))){            

            //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

            $posts = ProductosAux::select(DB::raw('0 AS C, 
                    PRODUCTOS_AUX.CODIGO, PRODUCTOS.DESCRIPCION, 
                    PRODUCTOS_AUX.FECHULT_V, 
                    DATE_ADD(PRODUCTOS_AUX.FECHULT_V, 
                    interval PRODUCTOS.PERIODO month) AS LIMITE'))
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

        } else {

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            // CARGAR LOS PRODUCTOS FILTRADOS EN DATATABLE

            $posts =  ProductosAux::select(DB::raw('0 AS C, 
                    PRODUCTOS_AUX.CODIGO, 
                    PRODUCTOS.DESCRIPCION, 
                    PRODUCTOS_AUX.FECHULT_V, 
                    interval PRODUCTOS.PERIODO month) AS LIMITE'))
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

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = count($posts);
        }

        /*  --------------------------------------------------------------------------------- */

        $data = array();

        // CONVERT IMAGE DEFAULT TO BLOB 

        $path = '../storage/app/imagenes/product.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $dataDefaultImage = file_get_contents($path);

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts)){

            foreach ($posts as $post){

                // BUSCAR IMAGEN

                $imagen = Imagen::select(DB::raw('PICTURE'))
                ->where('COD_PROD','=', $post->CODIGO)
                ->get();
                
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
    }

    public static function guardar($data){

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
            $porcentaje_retencion = 0;

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

            // CUPON 

            if(isset($data["data"]["pago"]["CUPON_TOTAL"])){
                $cupon = Common::quitar_coma($data["data"]["pago"]["CUPON_TOTAL"], 2);
            }else{
                $cupon = 0;
            }

            $total_cupon_iva = 0;
            $total_cupon_base10 = 0;
            $total_cupon_gravadas = 0;

            /*  --------------------------------------------------------------------------------- */            

            // CREDITO

            $credito = Common::quitar_coma($data["data"]["pago"]["CREDITO"], 2);
            $dias_credito = $data["data"]["pago"]["DIAS_CREDITO"];
            $credito_fin = $data["data"]["pago"]["CREDITO_FIN"];

            /*  --------------------------------------------------------------------------------- */

            // NOTA DE CREDITO 

            $nota_credito_data = array();
            $total_nota_credito = 0;
            $total_nota_credito_base5 = 0;
            $total_nota_credito_base10 = 0;
            $total_nota_credito_gravadas = 0;
            $total_nota_credito_iva = 0;

            /*  --------------------------------------------------------------------------------- */

            // RETENCION 

            if(isset($data["data"]["cabecera"]["RETENCION"])){
                $retencion = Common::quitar_coma($data["data"]["cabecera"]["RETENCION"], 2);
                $porcentaje_retencion=Common::quitar_coma(($data["data"]["cliente"]["RETENCION_PORCENTAJE"]*100), 2);
            }else{
                $retencion = 0;
            }

            /*  --------------------------------------------------------------------------------- */

            // COTIZACION 

            if(isset($data["data"]["pago"]["COTIZACION"])){
                $cotizacion = $data["data"]["pago"]["COTIZACION"];
            }else{
                $cotizacion = 0;
            }

            /*  --------------------------------------------------------------------------------- */

            // AGENCIA 

            if(isset($data["data"]["agencia"]["CODIGO"])){
                $agencia = $data["data"]["agencia"]["CODIGO"];
            }else{
                $agencia = 0;
            }

            /*  --------------------------------------------------------------------------------- */
            // MAYORISTA 

            if(isset($data["data"]["mayorista"])){
                $may = $data["data"]["mayorista"];
            }else{
                $may = 0;
            }

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
            $impuesto_cupon = 0;

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
        
            $items  = array_column( $data["data"]["productos"], 'ITEM');
            array_multisort($items, SORT_ASC, $data["data"]["productos"]);

            Log::error(["PRODUCTOS"=>$data["data"]["productos"]]);

            $venta = Venta::insertGetId([
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
                "DESCUENTO" => 0, 
                "GRAVADAS" => 0, 
                "IMPUESTOS" => 0, 
                "EXENTAS" => 0, 
                "BASE5" => 0, 
                "BASE10" => 0, 
                "SUB_TOTAL" => 0, 
                "TOTAL" => 0, 
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
                "MAYORISTA"=> $may
            ]);

            /*  --------------------------------------------------------------------------------- */

            while($c < $filas) {

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

                    // RESTAR STOCK DEL PRODUCTO

                    $respuesta_resta = Stock::restar_stock_producto($cod_prod, $cantidad);

                    // SI LA RESPUESTA TIENE DATOS GUARDA EL REGISTRO 

                    if ($respuesta_resta["datos"]) {

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

                        // CALCULAR IVA
                        
                        $impuesto = Common::calculo_iva((int)$porcentaje, $total, $candec);               

                        // TOTALES 

                        $total_total = $total_total + $total;
                        
                        $total_iva = $total_iva + $impuesto["impuesto"];
                        $total_base10 = $total_base10 + $impuesto["base10"];
                        $total_base5 = $total_base5 + $impuesto["base5"];
                        $total_exentas = $total_exentas + $impuesto["exentas"];
                        $total_gravadas = $total_gravadas + $impuesto["gravadas"];
                        $descuento_total = $descuento_total + $descuento;

                        // INSERTAR TRANSFERENCIA DET 

                        $id_ventas_det = Ventas_det::insertGetId([
                            'FK_VENTA' => $venta,
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
                        ]);

                        // INSERTAR DESCUENTO 

                        if ($descuento_porcentaje > 0  ) {

                            Ventas_det_Descuento::guardar_referencia(
                                $descuento_porcentaje, 
                                $descuento, 
                                $id_ventas_det, 
                                $moneda, 
                                $cod_prod, 
                                $data["data"]["productos"][$c]["TIPO_DESCUENTO"]
                            );
                        }
                    }

                    // AQUI RECORRO EL ARRAY MANDANDO LOS ID LOTE Y LA TRANSFERENCIA EN LA TABLA DE CLAVES FORANEAS

                    foreach ($respuesta_resta["datos"] as $key => $value) {

                        if($data["data"]["productos"][$c]["TIPO_DESCUENTO"]==7){

                            $descuento = LoteTieneDescuento::Select(DB::raw('IFNULL(ID,0) AS ID'))
                            ->where('FK_LOTE','=',$value["id"])
                            ->where('FECHA_FIN','>=',$dia)
                            ->get();

                            VentasDetTieneLotes::guardar_referencia($id_ventas_det, $value["id"], $value["cantidad"],$descuento[0]->ID);

                        }else{
                            VentasDetTieneLotes::guardar_referencia($id_ventas_det, $value["id"], $value["cantidad"],0);
                        }
                    }

                    /*  --------------------------------------------------------------------------------- */
                    // CARGAR LOS PRODUCTOS CON LAS CANTIDADES QUE NO SE GUARDARON 

                    if ($respuesta_resta["response"] === false){

                        $sin_stock[] = (array)[
                            'cod_prod' => $cod_prod, 
                            'guardado' => (float)$cantidad - (float)$respuesta_resta["restante"], 
                            "restante" => $respuesta_resta["restante"], 
                            "cantidad" => $cantidad
                        ];
                    }

                    /*  --------------------------------------------------------------------------------- */
                    // SI TIPO ES SERVICIO 

                } else if ($data["data"]["productos"][$c]["TIPO"] === 2) {

                    // CALCULAR IVA
                        
                    $impuesto = Common::calculo_iva((int)$porcentaje, $total, $candec);
                            
                    // TOTALES 

                    $total_total = $total_total + $total;
                        
                    $total_iva = $total_iva + $impuesto["impuesto"];
                    $total_base10 = $total_base10 + $impuesto["base10"];
                    $total_base5 = $total_base5 + $impuesto["base5"];
                    $total_exentas = $total_exentas + $impuesto["exentas"];
                    $total_gravadas = $total_gravadas + $impuesto["gravadas"];
                    $descuento_total = $descuento_total + $descuento;

                    // INSERTAR TRANSFERENCIA DET 

                    VentasDetServicios::insert([
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
                    ]);

                    /*  --------------------------------------------------------------------------------- */
                
                    // DEVOLUCION

                } else if ($data["data"]["productos"][$c]["TIPO"] === 3) {

                    // CALCULAR IVA
                        
                    $impuesto = Common::calculo_iva((int)$porcentaje, $total, $candec);

                    // TOTALES 

                    $total_total = $total_total + $total;
                    $total_iva = $total_iva + $impuesto["impuesto"];
                    $total_base10 = $total_base10 + $impuesto["base10"];
                    $total_base5 = $total_base5 + $impuesto["base5"];
                    $total_exentas = $total_exentas + $impuesto["exentas"];
                    $total_gravadas = $total_gravadas + $impuesto["gravadas"];
                    $descuento_total = $descuento_total + $descuento;

                    // INSERTAR TRANSFERENCIA DET 

                    VentasDetDevolucion::insertGetId([
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
                    ]);

                    /*  --------------------------------------------------------------------------------- */

                    // SUMAR STOCK 

                    Stock::sumar_stock_producto_devolucion($cod_prod, $cantidad, $id_ventasdet);
                    
                    /*  --------------------------------------------------------------------------------- */

                } else if ($data["data"]["productos"][$c]["TIPO"] === 4) {

                    /*  --------------------------------------------------------------------------------- */

                    $nestedData['ID'] = $data["data"]["productos"][$c]["ID"];
                    $nestedData['TOTAL'] = $total;
                    $nestedData['ITEM'] = $c + 1;

                    /*  --------------------------------------------------------------------------------- */

                    // OBTENER DATOS DE LA NOTA DE CREDITO

                    $nota_credito_impuesto = NotaCredito::select(DB::raw('BASE5, BASE10, SUB_TOTAL, IVA, TOTAL'))
                    ->where('ID', '=', $data["data"]["productos"][$c]["ID"])
                    ->get();

                    /*  --------------------------------------------------------------------------------- */

                    // CAMBIAR ESTATUS NOTA DE CREDITO 

                    NotaCredito::where('ID', '=', $data["data"]["productos"][$c]["ID"]) 
                    ->update([ 
                        'PROCESADO' => 1,
                        'FECMODIF' => $dia,
                        'HORMODIF' => $hora
                    ]);

                    /*  --------------------------------------------------------------------------------- */

                    // IMPUESTO 

                    $total_nota_credito_base5 =  $total_nota_credito_base5 + $nota_credito_impuesto[0]['BASE5'];
                    $total_nota_credito_base10 =  $total_nota_credito_base10 + $nota_credito_impuesto[0]['BASE10'];
                    $total_nota_credito_gravadas =  $total_nota_credito_gravadas + $nota_credito_impuesto[0]['SUB_TOTAL'];
                    $total_nota_credito_iva =  $total_nota_credito_iva + $nota_credito_impuesto[0]['IVA'];
                    $total_nota_credito =  $total_nota_credito + $nota_credito_impuesto[0]['TOTAL'];

                    $nota_credito_data[] = $nestedData;
                }

                /*  --------------------------------------------------------------------------------- */
                        
                // AUMENTAR CONTADOR 

                $c++;
            }

            /*  --------------------------------------------------------------------------------- */

            if ($cupon !== '0' && $cupon !== 0 && $cupon !== '0.00' && $cupon !== NULL) {

                // GUARDAR VENTA CON CUPON
                
                $impuesto_cupon = Common::calculo_iva(10, (float)$cupon, $candec);

                $total_iva = $total_iva - $impuesto_cupon["impuesto"];
                $total_base10 = $total_base10 - $impuesto_cupon["base10"];
                $total_gravadas = $total_gravadas - $impuesto_cupon["gravadas"];
                $total_cupon_iva = $impuesto_cupon["impuesto"];
                $total_cupon_base10 = $impuesto_cupon["base10"];
                $total_cupon_gravadas = $impuesto_cupon["gravadas"];
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

            // CALCULAR TOTAL GENERAL IVA  CON NOTA DE CREDITO

            if ($total_nota_credito !== 0) {

                $total_iva = $total_iva - $total_nota_credito_iva;
                $total_base10 = $total_base10 - $total_nota_credito_base10;
                $total_base5 = $total_base5 - $total_nota_credito_base5;
                $total_gravadas = $total_gravadas - $total_nota_credito_gravadas;
                $total_total = $total_total - $total_nota_credito;
            }

            /*  --------------------------------------------------------------------------------- */

            // REVISAR TIPO DE VENTA 

            if ($credito !== '0' && $credito !== '0.00' && $credito !== NULL) {

                // TIPO CREDITO 

                $tipo_venta = 'CR';

            } else if ($pago_al_entregar === true) {

                // PARA FIJAR PAGO AL ENTREGAR 
                
                $tipo_venta = 'PE';
                $estatus_venta = 2;
            }

            /*  --------------------------------------------------------------------------------- */

            // INSERTAR VENTA 

            Venta::where('ID', '=', $venta)
            ->update([
                "DESCUENTO" => ($descuento_total + $descuento_general),
                "TIPO" =>  $tipo_venta,
                "GRAVADAS" => $total_gravadas, 
                "IMPUESTOS" => $total_iva, 
                "EXENTAS" => $total_exentas, 
                "BASE5" => $total_base5, 
                "BASE10" => $total_base10, 
                "SUB_TOTAL" => ($total_gravadas + $total_exentas), 
                "TOTAL" => ((($total_total - $descuento_general) - $cupon))
            ]);
            
            /*  --------------------------------------------------------------------------------- */

            // INSERTAR ANULADO 

            VentasAnulado::guardar_referencia([
                'FK_VENTA' => $venta,
                'ANULADO' => $estatus_venta,
                'FECHA' => date('Y-m-d H:i:s')
            ]);

            /*  --------------------------------------------------------------------------------- */

            // INSERTAR REFERENCIA COTIZACION 

            VentasTieneCotizacion::guardar_referencia([
                'FK_VENTA' => $venta,
                'COTIZACION' => $cotizacion
            ]);

            /*  --------------------------------------------------------------------------------- */

            // INSERTAR NOTA DE CREDITO 

            foreach ($nota_credito_data as $key => $value) {

                // GUARDAR REFERENCIA

                VentaTieneNotaCredito::guardar_referencia([
                    'FK_VENTA' => $venta,
                    'FK_NOTA_CREDITO' => $value['ID'],
                    'ITEM' => $value['ITEM']
                ]);
            }

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

            // INSERTAR VENTA RETENCION 

            if ($retencion !== '0' && $retencion !== '0.00' && $retencion !== 0 && $retencion !== NULL) {

                VentaRetencion::guardar_referencia([
                    'FK_VENTA' => $venta,
                    'MONTO' => $retencion,
                    'PORCENTAJE' => $porcentaje_retencion
                ]);   
            }

            /*  --------------------------------------------------------------------------------- */

            // INSERTAR AGENCIA

            if ($agencia !== '0' && $agencia !== '0.00' && $agencia !== 0 && $agencia !== NULL) {

                VentasTieneAgencia::guardar_referencia([
                    'FK_VENTA' => $venta,
                    'FK_AGENCIA' => $agencia
                ]);
            }

            /*  --------------------------------------------------------------------------------- */

            // INSERTAR PAGO CUPON

            if ($cupon !== '0' && $cupon !== 0 && $cupon !== '0.00' && $cupon !== NULL) {

                // GUARDAR VENTA CON CUPON
                
                VentaCupon::guardar_referencia([
                    'FK_CUPON' => $data["data"]["pago"]["CUPON_ID"],
                    'FK_VENTA' => $venta,
                    'CUPON_IMPORTE' => $cupon,
                    'CUPON_PORCENTAJE' => $data["data"]["pago"]["CUPON_PORCENTAJE"],
                    'CUPON_TIPO' => $data["data"]["pago"]["CUPON_TIPO"],
                    'FK_USER' => $user->id,
                    'MONEDA' => $moneda,
                    'FECALTAS' => $dia,
                    'HORALTAS' => $hora,
                    'BASE5' => 0,
                    'BASE10' => $total_cupon_base10,
                    'GRAVADAS' => $total_cupon_gravadas,
                    'IVA' => $total_cupon_iva
                ]);

                /*  --------------------------------------------------------------------------------- */ 

                // OBTENER ID CLIENTE 

                $id_cliente = (Cliente::id_cliente($cliente))['ID_CLIENTE'];

                /*  --------------------------------------------------------------------------------- */

                // ACTUALIZAR USO DEL CUPON 

                Cupon::actualizar_uso($data["data"]["pago"]["CUPON_ID"],1);

                /*  --------------------------------------------------------------------------------- */

                // GUARDAR USO DEL CLIENTE 

                Cliente_tiene_Cupon::guardar_referencia([
                    'FK_CUPON' => $data["data"]["pago"]["CUPON_ID"],
                    'FK_VENTA' => $venta,
                    'FK_CLIENTE' => $id_cliente,
                    'MONEDA' => $moneda
                ]);

            }

            /*  --------------------------------------------------------------------------------- */
            // AUTORIZACION

            if(isset($data["data"]["autorizacion"]["PERMITIDO"])){

                $autorizacion = $data["data"]["autorizacion"]["PERMITIDO"];
            }else{
                $autorizacion = 0;
            }

            if($autorizacion == 1){

                VentasTieneAutorizacion::guardar_referencia([
                    'FK_VENTA' => $venta,
                    'FK_USER' => $data["data"]["autorizacion"]["ID_USUARIO"],
                    'FK_USER_SUPERVISOR' => $data["data"]["autorizacion"]["ID_USER_SUPERVISOR"]
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

            return ["response" => true, "statusText" => "¡Se ha guardado correctamente la venta!", "CODIGO" => $codigo, "CAJA" => $caja];

            /*  --------------------------------------------------------------------------------- */

        } catch (Exception $e) {
        
           /*  --------------------------------------------------------------------------------- */

           // NO GUARDAR NINGUN CAMBIO 

           DB::connection('retail')->rollBack();
           throw $e;
        }
    }

    public static function existe_codigo($codigo, $caja){

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        $existe = 1;

        while ($existe > 0) {

            $existe = Venta::where('CODIGO', '=', $codigo)
            ->where('CAJA', '=', $caja)
            ->where('ID_SUCURSAL', '=', $user->id_sucursal)
            ->count();

            // SI EXISTE EL CODIGO SUMAS + 1 Y VOLVER A RECORRER 
            
            if($existe > 0) {
                $codigo = $codigo + 1;
            }
        }

        /*  --------------------------------------------------------------------------------- */

        return $codigo;
    }

    public static function numeracion($data) {

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
        $dia = date('Y-m-d');
        $hora = date("H:i:s");
        $numero_caja = $data["caja"];
        $caja = 'CA'.sprintf("%02d", $numero_caja).'';

        // NUMERO TICKET 

        $ticket = Ticket::numero_caja($caja, $dia, $hora);

        // VER SI EXISTE NUMERO DE VENTA 

        $codigo = Venta::where('CODIGO','=', 1)
        ->where('ID_SUCURSAL','=', $user->id_sucursal)
        ->count();

        if ($codigo === 0) {
            $codigo = 1;
        } else {
            $codigo = $ticket[0]["NUMERO"];
        }

        /*  --------------------------------------------------------------------------------- */

        
        $ticket = $ticket[0]["CAJA"];

        $numero = Venta::select(DB::raw('(CODIGO) AS CODIGO'))
        ->whereRaw('LENGTH(CODIGO) < 8')
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->where('CAJA', '=', $numero_caja)
        ->orderBy('CODIGO', 'desc')
        ->limit(1)
        ->get();

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
        //var_dump($user->hasRole('Admin'));
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

        $parametro = Parametro::select(DB::raw('MONEDA, DESTINO, SUPERVISOR'))
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // CANDEC

        $candec = Parametro::candec($parametro[0]['MONEDA']);
        $candec["CODIGO"] = $parametro[0]['MONEDA'];

        /*  --------------------------------------------------------------------------------- */

        // LOGO

        $imagen = (Imagen::obtenerLogoURL())['imagen'];

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        // return ['CLIENTE' => $cliente[0], 'EMPLEADO' => $empleado[0], 'MONEDA' => $candec, 'LIMITE_MAYORISTA' => $parametro[0]['DESTINO'], 'IMPRESORA_TICKET' => 'EPSON TM-U220 Receipt', 'IMPRESORA_MATRICIAL' => 'Microsoft Print to PDF'];

        return ['CLIENTE' => $cliente[0], 
            'EMPLEADO' => $empleado[0], 
            'MONEDA' => $candec, 
            'LIMITE_MAYORISTA' => $parametro[0]['DESTINO'], 
            'IMPRESORA_TICKET' => 'TICKET', 
            'IMPRESORA_MATRICIAL' => 'FACTURA',
            'SUPERVISOR' => $parametro[0]['SUPERVISOR'], 
            'LOGO' => $imagen
        ];
        
        /*  --------------------------------------------------------------------------------- */

    }

    public static function mostrar_cabecera($data){

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
         
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $codigo = $data['codigo'];
        $caja = $data['caja'];

        /*  --------------------------------------------------------------------------------- */

        $venta = Venta::select(DB::raw('
                VENTAS.ID,
                VENTAS.CODIGO,
                VENTAS.FECALTAS,  
                CLIENTES.NOMBRE AS CLIENTE,
                CLIENTES.RAZON_SOCIAL,
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
                VENTAS.TOTAL,
                VENTAS.MONEDA1 AS DOLARES,
                VENTAS.MONEDA2 AS REALES,
                VENTAS.MONEDA3 AS GUARANIES,
                VENTAS.MONEDA4 AS PESOS'))
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

        // RAZON SOCIAL

        if ($venta[0]->RAZON_SOCIAL !== '' && $venta[0]->RAZON_SOCIAL !== null) {
            $venta[0]->CLIENTE = $venta[0]->RAZON_SOCIAL;
        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VENTA 

        return $venta[0];
    }

    public static function mostrar_cuerpo($data){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
         
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $codigo = $data['codigo'];
        $caja = $data['caja'];
        $data = array();
        $item_array = array();
        $cantidad_items  = 0;

        /*  --------------------------------------------------------------------------------- */

        // DEVOLUCIONES 

        $ventas_det_devoluciones = VentasDetDevolucion::select(DB::raw('
                VENTASDET_DEVOLUCIONES.ID,
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
                0 AS IVA_PORCENTAJE'))
            ->leftjoin('VENTASDET', 'VENTASDET.ID', '=', 'VENTASDET_DEVOLUCIONES.FK_VENTASDET')
            ->leftJoin('VENTAS', 'VENTAS.ID', '=', 'VENTASDET.FK_VENTA')
        ->where('VENTASDET_DEVOLUCIONES.ID_SUCURSAL','=', $user->id_sucursal)
        ->where('VENTASDET_DEVOLUCIONES.CODIGO','=', $codigo)
        ->where('VENTASDET_DEVOLUCIONES.CAJA','=', $caja)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        foreach ($ventas_det_devoluciones as $key => $value) {

            // BUSCAR IVA PRODUCTO

            $producto = Producto::select(DB::raw('IMPUESTO'))
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
            $item_array[] = $nestedData['ITEM'];
        }

        /*  --------------------------------------------------------------------------------- */

        // DESCUENTO 

        $ventas_descuento = Ventas_Descuento::select(DB::raw('
                VENTAS_DESCUENTO.ID,
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
                0 AS IVA_PORCENTAJE'))
            ->leftJoin('VENTAS', 'VENTAS.ID', '=', 'VENTAS_DESCUENTO.FK_VENTAS')
        ->where('VENTAS.ID_SUCURSAL','=', $user->id_sucursal)
        ->where('VENTAS.CODIGO','=', $codigo)
        ->where('VENTAS.CAJA','=', $caja)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        foreach ($ventas_descuento as $key => $value) {

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
            $item_array[] = $nestedData['ITEM'];
        }

        /*  --------------------------------------------------------------------------------- */

        // CUPON 

        $ventas_cupon = VentaCupon::select(DB::raw('
                VENTAS_CUPON.ID,
                0 AS ITEM, 
                2 AS COD_PROD, 
                0 AS DESCRIPCION, 
                1 AS CANTIDAD, 
                VENTAS_CUPON.CUPON_IMPORTE AS PRECIO_UNIT,
                VENTAS_CUPON.IVA AS IVA,
                0 AS EXENTAS,
                VENTAS_CUPON.BASE5 AS BASE5,
                VENTAS_CUPON.BASE10 AS BASE10,
                VENTAS_CUPON.CUPON_IMPORTE AS PRECIO,
                VENTAS.MONEDA,
                10 AS IVA_PORCENTAJE'))
            ->leftJoin('VENTAS', 'VENTAS.ID', '=', 'VENTAS_CUPON.FK_VENTA')
        ->where('VENTAS.ID_SUCURSAL','=', $user->id_sucursal)
        ->where('VENTAS.CODIGO','=', $codigo)
        ->where('VENTAS.CAJA','=', $caja)
        ->get();

        /*  --------------------------------------------------------------------------------- */
        
        foreach ($ventas_cupon as $key => $value) {

            $nestedData['ID'] = $value->ID;
            $nestedData['ITEM'] = $value->ITEM;
            $nestedData['COD_PROD'] = $value->COD_PROD;
            $nestedData['DESCRIPCION'] = 'DESCUENTO CUPON';
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
            $item_array[] = $nestedData['ITEM'];
        }

        /*  --------------------------------------------------------------------------------- */

        // NOTA CREDITO

        $ventas_nota_credito = VentaTieneNotaCredito::select(DB::raw('
                VENTAS_TIENE_NOTA_CREDITO.ID,
                VENTAS_TIENE_NOTA_CREDITO.ITEM AS ITEM, 
                3 AS COD_PROD, 
                0 AS DESCRIPCION, 
                1 AS CANTIDAD, 
                NOTA_CREDITO.TOTAL AS PRECIO_UNIT,
                NOTA_CREDITO.IVA AS IVA,
                0 AS EXENTAS,
                NOTA_CREDITO.BASE5 AS BASE5,
                NOTA_CREDITO.BASE10 AS BASE10,
                NOTA_CREDITO.TOTAL AS PRECIO,
                VENTAS.MONEDA,
                10 AS IVA_PORCENTAJE'))
            ->leftJoin('VENTAS', 'VENTAS.ID', '=', 'VENTAS_TIENE_NOTA_CREDITO.FK_VENTA')
            ->leftjoin('NOTA_CREDITO', 'NOTA_CREDITO.ID', '=', 'VENTAS_TIENE_NOTA_CREDITO.FK_NOTA_CREDITO')
        ->where('VENTAS.ID_SUCURSAL','=', $user->id_sucursal)
        ->where('VENTAS.CODIGO','=', $codigo)
        ->where('VENTAS.CAJA','=', $caja)
        ->get();

        /*  --------------------------------------------------------------------------------- */
        
        foreach ($ventas_nota_credito as $key => $value) {

            $nestedData['ID'] = $value->ID;
            $nestedData['ITEM'] = $value->ITEM;
            $nestedData['COD_PROD'] = $value->COD_PROD;
            $nestedData['DESCRIPCION'] = 'DESCUENTO NC';
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
            $item_array[]= $nestedData['ITEM'];
        }

        /*  --------------------------------------------------------------------------------- */

        $ventas_det = Ventas_det::select(DB::raw('
                VENTASDET.ID,
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
                0 AS IVA_PORCENTAJE'))
            ->leftJoin('VENTAS', 'VENTAS.ID', '=', 'VENTASDET.FK_VENTA')
        ->where('VENTASDET.ID_SUCURSAL','=', $user->id_sucursal)
        ->where('VENTASDET.CODIGO','=', $codigo)
        ->where('VENTASDET.CAJA','=', $caja)
        ->get();

        $cantidad_items = count($ventas_det);

        /*  --------------------------------------------------------------------------------- */

        foreach ($ventas_det as $key => $value) {

            // BUSCAR IVA PRODUCTO

            $producto = Producto::select(DB::raw('IMPUESTO'))
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
            $nestedData['CANTIDAD_ITEMS'] = $cantidad_items;

            /*  --------------------------------------------------------------------------------- */

            // CARGAR DATOS EN ARRAY

            $data[] = $nestedData;
            $item_array[]= $nestedData['ITEM'];
        }

        /*  --------------------------------------------------------------------------------- */

        // OBTENER SERVICIOS 

        $ventas_det_servicios = VentasDetServicios::select(DB::raw('
                ventasdet_servicios.ID,
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
                0 AS IVA_PORCENTAJE'))
            ->leftJoin('VENTAS', function($join){
                $join->on('VENTAS.CODIGO', '=', 'ventasdet_servicios.CODIGO')
                     ->on('VENTAS.ID_SUCURSAL', '=', 'ventasdet_servicios.ID_SUCURSAL')
                     ->on('VENTAS.CAJA', '=', 'ventasdet_servicios.CAJA');
            })
        ->where('ventasdet_servicios.ID_SUCURSAL','=', $user->id_sucursal)
        ->where('ventasdet_servicios.CODIGO','=', $codigo)
        ->where('ventasdet_servicios.CAJA','=', $caja)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        foreach ($ventas_det_servicios as $key => $value) {

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
            $nestedData['CANTIDAD_ITEMS'] = $cantidad_items;

            /*  --------------------------------------------------------------------------------- */

            // CARGAR SERVICIOS EN ARRAY 

            $data[] = $nestedData;
            $item_array[] = $nestedData['ITEM'];

        }

        // ORDENAR EL ARRAY EN BASE A LA NUMERACION DE ITEMS.
        /*  --------------------------------------------------------------------------------- */

        array_multisort($item_array, SORT_ASC, $data);

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        return $data;
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

        // NOTA CREDITO VALORES

        $nota_credito_guaranies = 0;
        $nota_credito_dolares = 0;
        $nota_credito_pesos = 0;
        $nota_credito_reales = 0;
        $nota_credito_cheque = 0;
        $nota_credito_transferencia = 0;
        $nota_credito_total = 0;

        /*  --------------------------------------------------------------------------------- */

        // CANTIDAD DE VENTAS 

        if ($ventas === 0) {
            return Venta::resumen_pdf_vacio($dato);
        }

        /*  --------------------------------------------------------------------------------- */

        // OBTENER EL PRIMER TICKET 

        $primer_ticket = Venta::select(DB::raw('IFNULL(CODIGO_CA,0) AS CODIGO_CA'))
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->where('FECHA', '=', $fecha)
        ->where('CAJA', '=', $dato['caja'])
        ->orderBy('CODIGO_CA', 'ASC')
        ->limit(1)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        $ultimo_ticket = Venta::select(DB::raw('IFNULL(CODIGO_CA,0) AS CODIGO_CA'))
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->where('FECHA', '=', $fecha)
        ->where('CAJA', '=', $dato['caja'])
        ->orderBy('CODIGO_CA', 'DESC')
        ->limit(1)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // SUMAR TODOS LOS VALORES CONTADO

        $contado = Venta::select(DB::raw('
                IFNULL(SUM(EFECTIVO),0) AS T_EFECTIVO, 
                IFNULL(SUM(TARJETAS),0) AS T_TARJETAS, 
                IFNULL(SUM(VALE),0) AS T_VALES, 
                IFNULL(SUM(CHEQUE),0) AS T_CHEQUE, 
                IFNULL(SUM(DONACION),0) AS T_DONACION, 
                IFNULL(SUM(GIROS),0) AS T_GIROS, 
                IFNULL(SUM(VUELTO),0) AS T_VUELTOS, 
                IFNULL(SUM(BASE5),0) AS T_BASE5, 
                IFNULL(SUM(BASE10),0) AS T_BASE10, 
                IFNULL(SUM(EXENTAS),0) AS T_EXENTAS, 
                IFNULL(SUM(MONEDA1),0) AS DOLARES, 
                IFNULL(SUM(MONEDA2),0) AS REALES, 
                IFNULL(SUM(MONEDA3),0) AS GUARANIES, 
                IFNULL(SUM(MONEDA4),0) AS PESOS, 
                IFNULL(SUM(TOTAL),0) AS T_TOTAL'))
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
            ->leftjoin('VENTAS_ANULADO', 'VENTAS.ID', '=', 'VENTAS_ANULADO.FK_VENTA')
        ->where('VENTAS.ID_SUCURSAL', '=', $user->id_sucursal)
        ->where('VENTAS.FECHA', '=', $fecha)
        ->where('VENTAS.CAJA', '=', $dato['caja'])
        ->where('VENTAS_ANULADO.ANULADO', '=', 0)
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

        // RETENCION 30%

        $retencion = Venta::select(DB::raw('IFNULL(SUM(VENTAS_RETENCION.MONTO),0) AS T_TOTAL,PORCENTAJE'))
            ->leftjoin('VENTAS_ANULADO', 'VENTAS.ID', '=', 'VENTAS_ANULADO.FK_VENTA')
            ->rightjoin('VENTAS_RETENCION', 'VENTAS.ID', '=', 'VENTAS_RETENCION.FK_VENTA')
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->where('VENTAS.FECHA', '=', $fecha)
        ->where('VENTAS_ANULADO.ANULADO', '<>', 1)
        ->where('VENTAS.CAJA', '=', $dato['caja'])
        ->GROUPBY('PORCENTAJE')->orderby('PORCENTAJE')->get();

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

        // NOTA DE CREDITO

        $nota_credito = NotaCredito::select(DB::raw('
                IFNULL(SUM(NOTA_CREDITO_MEDIOS.TOTAL), 0) AS TOTAL, 
                NOTA_CREDITO_MEDIOS.TIPO_MEDIO, 
                IFNULL(SUM(NOTA_CREDITO.TOTAL), 0) AS MONTO'))
            ->leftjoin('NOTA_CREDITO_MEDIOS', 'NOTA_CREDITO.ID', '=', 'NOTA_CREDITO_MEDIOS.FK_NOTA_CREDITO')
        ->where('NOTA_CREDITO.ID_SUCURSAL', '=', $user->id_sucursal)
        ->whereDate('NOTA_CREDITO.FECMODIF', '=', $fecha)
        ->where('NOTA_CREDITO.CAJA', '=', $dato['caja'])
        ->where('NOTA_CREDITO.PROCESADO', '=', 1)
        ->groupBy('NOTA_CREDITO_MEDIOS.TIPO_MEDIO')
        ->get();

        foreach ($nota_credito as $key => $value) {

            $nota_credito_total = $nota_credito_total + $value->MONTO;

            if ($value->TIPO_MEDIO === 1) {
                $nota_credito_guaranies = $value->TOTAL;    
            } else if ($value->TIPO_MEDIO === 2) {
                $nota_credito_dolares = $value->TOTAL;    
            } else if ($value->TIPO_MEDIO === 3) {
                $nota_credito_pesos = $value->TOTAL;    
            } else if ($value->TIPO_MEDIO === 4) {
                $nota_credito_reales = $value->TOTAL;    
            } else if ($value->TIPO_MEDIO === 5) {
                $nota_credito_cheque = $value->TOTAL;    
            } else if ($value->TIPO_MEDIO === 6) {
                $nota_credito_transferencia = $value->TOTAL;    
            } 
             
        }

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
        $pdf->Cell(15, 4, Common::precio_candec($contado[0]->DOLARES - $nota_credito_dolares, 2),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Reales:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec($contado[0]->REALES - $nota_credito_reales, 4),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Guaranies:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec($contado[0]->GUARANIES - $nota_credito_guaranies, 1),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Pesos:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec($contado[0]->PESOS - $nota_credito_pesos, 3),0,0,'R');
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
        $pdf->Cell(15, 4, Common::precio_candec( ($transferencia[0]->TOTAL+$transferenciaAbono[0]->TOTAL) - $nota_credito_transferencia, 1),0,0,'R');
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
        $pdf->Cell(15, 4, Common::precio_candec($cheque_guarani - $nota_credito_cheque, 1),0,0,'R');
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

        $pdf->Cell(25, 4, utf8_decode('Notas Crédito:'), 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec($nota_credito_total, $parametro[0]->MONEDA),0,0,'R');
        $pdf->Ln(4);

        if(count($retencion) > 0){

            foreach ($retencion as $key => $value) {
                $pdf->Cell(25, 4, utf8_decode('Retención '.$value->PORCENTAJE.'%:'), 0);
                $pdf->Cell(20, 4, '', 0);
                $pdf->Cell(15, 4, Common::precio_candec($value->T_TOTAL, $parametro[0]->MONEDA),0,0,'R');
                $pdf->Ln(4);
            }
        }else{
            $pdf->Cell(25, 4, utf8_decode('Retención:'), 0);
            $pdf->Cell(20, 4, '', 0);
            $pdf->Cell(15, 4,Common::precio_candec(0,$parametro[0]->MONEDA) ,0,0,'R');
            $pdf->Ln(4);
        }

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

    public static function resumen_pdf_vacio($dato) {

        $user = auth()->user();
        $fecha = date('Y-m-d');
        $hora = date('H:i:s');
        $nota_credito_guaranies = 0;
        $nota_credito_dolares = 0;
        $nota_credito_pesos = 0;
        $nota_credito_reales = 0;
        $nota_credito_cheque = 0;
        $nota_credito_transferencia = 0;
        $nota_credito_total = 0;
        $cheque_dolar = 0.00;
        $cheque_guarani = 0;
        $cheque_peso = 0.00;
        $cheque_real = 0.00;
        $guaranies= 0;
        $reales= 0;
        $pesos= 0;
        $dolares= 0;

        //COMENZAR CON LAS CONSULTAS 

        $parametro = Parametro::select(DB::raw('EMPRESA, PROPIETARIO, DIRECCION, CIUDAD, ACTIVIDAD, RUC, MONEDA'))
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();
     
         // SUMAR TODOS LOS VALORES ABONO

        $abono = VentaAbono::select(DB::raw('IFNULL(SUM(PAGO), 0) AS T_TOTAL'))
        ->where('FK_SUCURSAL', '=', $user->id_sucursal)
        ->whereDate('FECHA', '=', $fecha)
        ->where('CAJA', '=', $dato['caja'])
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

        /*  --------------------------------------------------------------------------------- */

        // MONEDAS ABONO
        
        $monedaAbono = VentaAbonoMoneda::select(DB::raw('
                IFNULL(SUM(VENTAS_ABONO_MONEDAS.MONTO), 0) AS TOTAL, 
                VENTAS_ABONO_MONEDAS.FK_MONEDA'))
            ->leftjoin('VENTAS_ABONO', 'VENTAS_ABONO.ID', '=', 'VENTAS_ABONO_MONEDAS.FK_ABONO')
        ->where('VENTAS_ABONO.FK_SUCURSAL', '=', $user->id_sucursal)
        ->whereDate('VENTAS_ABONO.FECHA', '=', $fecha)
        ->where('VENTAS_ABONO.CAJA', '=', $dato['caja'])
        ->groupBy('VENTAS_ABONO_MONEDAS.FK_MONEDA')
        ->get();

        foreach ($monedaAbono as $key => $value) {

           if ($value->FK_MONEDA === 1) {
                $guaranies = $guaranies + $value->TOTAL;
           } else if ($value->FK_MONEDA === 2) {
                $dolares = $dolares + $value->TOTAL;
           } else if ($value->FK_MONEDA === 3) {
                $pesos = $pesos + $value->TOTAL;
           } else if ($value->FK_MONEDA === 4) {
                $reales = $reales + $value->TOTAL;
           }

        }

        $nota_credito = NotaCredito::select(DB::raw('
                IFNULL(SUM(NOTA_CREDITO_MEDIOS.TOTAL), 0) AS TOTAL, 
                NOTA_CREDITO_MEDIOS.TIPO_MEDIO, 
                IFNULL(SUM(NOTA_CREDITO.TOTAL), 0) AS MONTO'))
            ->leftjoin('NOTA_CREDITO_MEDIOS', 'NOTA_CREDITO.ID', '=', 'NOTA_CREDITO_MEDIOS.FK_NOTA_CREDITO')
        ->where('NOTA_CREDITO.ID_SUCURSAL', '=', $user->id_sucursal)
        ->whereDate('NOTA_CREDITO.FECMODIF', '=', $fecha)
        ->where('NOTA_CREDITO.CAJA', '=', $dato['caja'])
        ->where('NOTA_CREDITO.PROCESADO', '=', 1)
        ->groupBy('NOTA_CREDITO_MEDIOS.TIPO_MEDIO')
        ->get();

        foreach ($nota_credito as $key => $value) {

            $nota_credito_total = $nota_credito_total + $value->MONTO;

            if ($value->TIPO_MEDIO === 1) {
                $nota_credito_guaranies = $value->TOTAL;    
            } else if ($value->TIPO_MEDIO === 2) {
                $nota_credito_dolares = $value->TOTAL;    
            } else if ($value->TIPO_MEDIO === 3) {
                $nota_credito_pesos = $value->TOTAL;    
            } else if ($value->TIPO_MEDIO === 4) {
                $nota_credito_reales = $value->TOTAL;    
            } else if ($value->TIPO_MEDIO === 5) {
                $nota_credito_cheque = $value->TOTAL;    
            } else if ($value->TIPO_MEDIO === 6) {
                $nota_credito_transferencia = $value->TOTAL;    
            } 
        }

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
        $pdf->Cell(15, 4, '0 - 0',0,0,'R');
        $pdf->Ln(6);

        /*  --------------------------------------------------------------------------------- */

        // LINEA 

        $pdf->Cell(60,0,'','T');

        /*  --------------------------------------------------------------------------------- */

        // MONEDAS 

        $pdf->Ln(2);
        $pdf->Cell(25, 4, 'Dolares:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec( $dolares- $nota_credito_dolares, 2),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Reales:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec( $reales- $nota_credito_reales, 4),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Guaranies:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec($guaranies-$nota_credito_guaranies, 1),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Pesos:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec( $pesos- $nota_credito_pesos, 3),0,0,'R');
        $pdf->Ln(6);

        /*  --------------------------------------------------------------------------------- */

        // LINEA 
        
        $pdf->Cell(60,0,'','T');

        /*  --------------------------------------------------------------------------------- */

        // MONEDAS 

        $pdf->Ln(2);
        $pdf->Cell(25, 4, utf8_decode('Dotación:'), 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec(0, $parametro[0]->MONEDA),0,0,'R');
        $pdf->Ln(4);

        // $pdf->Cell(25, 4, 'Efectivo:', 0);
        // $pdf->Cell(20, 4, '', 0);
        // $pdf->Cell(15, 4, Common::precio_candec($contado[0]->T_EFECTIVO, $parametro[0]->MONEDA),0,0,'R');
        // $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Tarjetas:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec(0, 1),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Transferencia:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec( $transferenciaAbono[0]->TOTAL-$nota_credito_transferencia, 1),0,0,'R');
        $pdf->Ln(6);

        /*  --------------------------------------------------------------------------------- */

        // LINEA 
        
        $pdf->Cell(60,0,'','T');
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
        $pdf->Cell(15, 4, Common::precio_candec( $giroAbono[0]->TOTAL, 1),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Vales:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec( $valeAbono[0]->TOTAL, $parametro[0]->MONEDA),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Giros:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec( $giroAbono[0]->TOTAL, $parametro[0]->MONEDA),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, utf8_decode('Donación:'), 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec(0, $parametro[0]->MONEDA),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Retiros:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec(0 , $parametro[0]->MONEDA),0,0,'R');
        $pdf->Ln(4);

        // $pdf->Cell(25, 4, 'Vuelto:', 0);
        // $pdf->Cell(20, 4, '', 0);
        // $pdf->Cell(15, 4, Common::precio_candec($contado[0]->T_VUELTOS, $parametro[0]->MONEDA),0,0,'R');
        // $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Gastos:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec(0, $parametro[0]->MONEDA),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'Total Ventas:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec( $abono[0]->T_TOTAL, $parametro[0]->MONEDA),0,0,'R');
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
        $pdf->Cell(15, 4, Common::precio_candec(0, $parametro[0]->MONEDA),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, utf8_decode('Ventas PE:'), 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec(0, $parametro[0]->MONEDA),0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, utf8_decode('Notas Crédito:'), 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, Common::precio_candec($nota_credito_total, $parametro[0]->MONEDA),0,0,'R');
        $pdf->Ln(4);
        $pdf->Cell(25, 4, utf8_decode('Retención:'), 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4,Common::precio_candec(0,$parametro[0]->MONEDA) ,0,0,'R');
        $pdf->Ln(4);
        $pdf->Cell(25, 4, 'Tickets Anulados:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, 0,0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'TOTAL GRAVADAS 10%:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, 0,0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'TOTAL GRAVADAS 5%:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, 0,0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'TOTAL EXENTAS:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, 0,0,0,'R');
        $pdf->Ln(6);

        /*  --------------------------------------------------------------------------------- */

        // LINEA 
        
        $pdf->Cell(60,0,'','T');

        /*  --------------------------------------------------------------------------------- */

        $pdf->Ln(6);

        $pdf->Cell(25, 4, utf8_decode('HABILITACIÓN CAJA:'), 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, $user->name,0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 4, 'HORA:', 0);
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, $hora,0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 10, utf8_decode('HABILITACIÓN CAJA:'), 0);
        $pdf->Cell(20, 10, '', 0);
        $pdf->Cell(15, 10, $user->name,0,0,'R');
        $pdf->Ln(4);

        $pdf->Cell(25, 10, 'HORA:', 0);
        $pdf->Cell(20, 10, '', 0);
        $pdf->Cell(15, 10, $hora,0,0,'R');
        $pdf->Ln(4);

        /*  --------------------------------------------------------------------------------- */

        $pdf->Output('ticket.pdf','i');
    }

    public static function ticket_pdf($dato) {

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

        $parametro = Parametro::select(DB::raw('MENSAJE, LOGO, TOTAL_MONEDAS_TICKET, NOMBRE_LOGO, PIE_TICKET_PERSONALIZABLE, TIPO_IMPRESORA_TICKET'))
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
        $direccion_sucursal = $sucursal['sucursal'][0]['DIRECCION'];
        $ciudad_sucursal = $sucursal['sucursal'][0]['CIUDAD'];
        $ticket_id = $ventas->ID;
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
        $cantidad_producto_descuento = 0;
        $cantidad_items = 0;

        /*  --------------------------------------------------------------------------------- */
        
        // TOTAL EN MONEDAS

        $total_dolares = (Cotizacion::ventaCotizacion([
            'monedaProducto' => (int)$ventas->MONEDA, 
            'monedaSistema' => 2, 
            'precio' => $ventas->TOTAL, 
            'decSistema' => 2, 
            'venta' => $ticket_id, 
            "id_sucursal" => $user->id_sucursal])
        );

        if ($total_dolares['response'] == true) {
            $total_dolares = $total_dolares['valor'];
        } else {
            $total_dolares = 0;
        }

        $total_guaranies = (Cotizacion::ventaCotizacion([
            'monedaProducto' => (int)$ventas->MONEDA, 
            'monedaSistema' => 1, 
            'precio' => $ventas->TOTAL, 
            'decSistema' => 0, 
            'venta' => $ticket_id, 
            "id_sucursal" => $user->id_sucursal])
        );

        if ($total_guaranies['response'] == true) {
            $total_guaranies = $total_guaranies['valor'];
        } else {
            $total_guaranies = 0;
        }

        $total_pesos = (Cotizacion::ventaCotizacion([
            'monedaProducto' => (int)$ventas->MONEDA, 
            'monedaSistema' => 3, 
            'precio' => $ventas->TOTAL, 
            'decSistema' => 2, 
            'venta' => $ticket_id, 
            "id_sucursal" => $user->id_sucursal])
        );

        if ($total_pesos['response'] == true) {
            $total_pesos = $total_pesos['valor'];
        } else {
            $total_pesos = 0;
        }

        $total_reales = (Cotizacion::ventaCotizacion([
            'monedaProducto' => (int)$ventas->MONEDA, 
            'monedaSistema' => 4, 
            'precio' => $ventas->TOTAL, 
            'decSistema' => 2, 
            'venta' => $ticket_id, 
            "id_sucursal" => $user->id_sucursal])
        );

        if ($total_reales['response'] == true) {
            $total_reales = $total_reales['valor'];
        } else {
            $total_reales = 0;
        }

        /*  --------------------------------------------------------------------------------- */

        // VUELTO EN MONEDAS

        $vuelto_dolares = (Cotizacion::ventaCotizacion([
            'monedaProducto' => (int)$ventas->MONEDA, 
            'monedaSistema' => 2, 
            'precio' => $ventas->VUELTO, 
            'decSistema' => 2, 
            'venta' => $ticket_id, 
            "id_sucursal" => $user->id_sucursal])
        );

        if ($vuelto_dolares['response'] == true) {
            $vuelto_dolares = $vuelto_dolares['valor'];
        } else {
            $vuelto_dolares = 0;
        }

        $vuelto_guaranies = (Cotizacion::ventaCotizacion([
            'monedaProducto' => (int)$ventas->MONEDA, 
            'monedaSistema' => 1, 
            'precio' => $ventas->VUELTO, 
            'decSistema' => 0, 
            'venta' => $ticket_id, 
            "id_sucursal" => $user->id_sucursal])
        );

        if ($vuelto_guaranies['response'] == true) {
            $vuelto_guaranies = $vuelto_guaranies['valor'];
        } else {
            $vuelto_guaranies = 0;
        }

        $vuelto_pesos = (Cotizacion::ventaCotizacion([
            'monedaProducto' => (int)$ventas->MONEDA, 
            'monedaSistema' => 3, 
            'precio' => $ventas->VUELTO, 
            'decSistema' => 2, 
            'venta' => $ticket_id, 
            "id_sucursal" => $user->id_sucursal])
        );

        if ($vuelto_pesos['response'] == true) {
            $vuelto_pesos = $vuelto_pesos['valor'];
        } else {
            $vuelto_pesos = 0;
        }

        $vuelto_reales = (Cotizacion::ventaCotizacion([
            'monedaProducto' => (int)$ventas->MONEDA, 
            'monedaSistema' => 4, 
            'precio' => $ventas->VUELTO, 
            'decSistema' => 2, 
            'venta' => $ticket_id, 
            "id_sucursal" => $user->id_sucursal])
        );

        if ($vuelto_reales['response'] == true) {
            $vuelto_reales = $vuelto_reales['valor'];
        } else {
            $vuelto_reales = 0;
        }

        /*  --------------------------------------------------------------------------------- */

        if ($parametro[0]->TIPO_IMPRESORA_TICKET === 1) {

            $pdf = new FPDF('P','mm',array(76,297));
            $pdf->AddPage();

        } else if ($parametro[0]->TIPO_IMPRESORA_TICKET === 2){

            $pdf = new FPDF('P','mm',array(80,400));
            $pdf->AddPage();
        }

        /*  --------------------------------------------------------------------------------- */

        // CABECERA

        if ($parametro[0]->LOGO === 1) {

            $pdf->Image('../storage/app/public/imagenes/tiendas/'.$parametro[0]->NOMBRE_LOGO.'',7,7,65,0);
            $pdf->Ln(10);
        } else {

            $pdf->SetFont('Helvetica','',10);
            $pdf->Cell(60,4, $nombre_sucursal ,0,1,'C');
        }

        /*  --------------------------------------------------------------------------------- */
        
        /*new*/ 

        $pdf->SetFont('Helvetica','',8);
        $pdf->Cell(60,4, utf8_decode($direccion_sucursal) ,0,1,'C');
        $pdf->Cell(60,4, utf8_decode($ciudad_sucursal) ,0,1,'C');
        $pdf->Cell(60,4, "***" ,0,1,'C');
        $pdf->Ln(1);
        $pdf->Cell(60,0,'','T');   

        /*datos del cliente*/

        $pdf->Ln(1);
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
        $pdf->Cell(15, 10, utf8_decode($tipo),0,0,'R');
        $pdf->Ln(3);

        $pdf->SetFont('Helvetica', 'B', 7);     
        $pdf->Cell(25, 10, 'TICKET ID:', 0);
        $pdf->SetFont('Helvetica', '', 7);    
        $pdf->Cell(20, 10, '', 0);
        $pdf->Cell(15, 10, $ticket_id,0,0,'R');
        $pdf->Ln(10);

        $pdf->Cell(60,0,'','T');

        /*new se movió acá de abajo*/

        $pdf->Ln(1);
        $pdf->SetFont('Helvetica','',8);
        $pdf->Cell(30,4, 'Fecha: '.$fecha ,0,0,'L');
        $pdf->Cell(30,4, 'Hora: '.$hora ,0,1,'R');  
        $pdf->Cell(30,4, 'Caja: '.$caja ,0,0,'L');
        $pdf->Cell(30,4, 'Ticket: '.$ticket ,0,1,'R');

        $pdf->Cell(25, 4, 'Vendedor:', 0);   
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, utf8_decode($vendedor),0,0,'R');
        $pdf->Ln(4);
        $pdf->Cell(25, 4, 'Cajero:', 0);   
        $pdf->Cell(20, 4, '', 0);
        $pdf->Cell(15, 4, utf8_decode($cajero),0,0,'R');
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
            $pdf->Ln(2.3);
            $pdf->Cell(60,4,$articulos["descripcion"],0,1,'L');

            /*  --------------------------------------------------------------------------------- */

            // BUSCAR DESCUENTOS 

            $descuento_producto = Ventas_det_Descuento::select(DB::raw('PORCENTAJE, TOTAL'))
            ->WHERE('FK_VENTASDET', '=', $value["ID"])
            ->WHERE('FK_COD_PROD', '=', $value["COD_PROD"])
            ->get();

            /*  --------------------------------------------------------------------------------- */

            if (count($descuento_producto) > 0) {

                $cantidad_producto_descuento = $cantidad_producto_descuento + 1;

                /*  --------------------------------------------------------------------------------- */

                $pdf->Ln(1);
                $pdf->SetFont('Helvetica', '', 7);
                $pdf->Cell(25,4,$articulos["cod_prod"],0,'L'); 
                $pdf->Cell(5, 4, $articulos["cantidad"],0,0,'R');
                $pdf->Cell(15, 4, '',0,0,'R');
                $pdf->Cell(15, 4, $descuento_producto[0]->TOTAL ,0,0,'R');
                $pdf->Ln(2.3);
                $pdf->Cell(60,4,'DESCUENTO '.$descuento_producto[0]->PORCENTAJE.'%',0,1,'L');
                $pdf->Cell(60,0,'------------------------------------------------------------------------','');
                $pdf->Ln(1);

                /*  --------------------------------------------------------------------------------- */

            } else {

                $pdf->Cell(60,0,'------------------------------------------------------------------------','');
                $pdf->Ln(1);

            }

            /*  --------------------------------------------------------------------------------- */

            // CANTIDAD ITEMS VENTA

            if(isset($value["CANTIDAD_ITEMS"])) {
                $cantidad_items = $value["CANTIDAD_ITEMS"];
            }
        }
         
        // SUMATORIO DE LOS PRODUCTOS Y EL IVA

        //$pdf->Ln(1);
        //$pdf->Cell(60,0,'','T');
        //$pdf->Ln(2);
        /*new*/
        $pdf->SetFont('Helvetica', 'B', 7);    
        $pdf->Cell(25, 10, 'CANT. DE ITEMS:', 0);
        $pdf->SetFont('Helvetica', '', 7);    
        $pdf->Cell(20, 10, '', 0);
        $pdf->Cell(15, 10, $cantidad_items,0,0,'R');
        $pdf->Ln(3); 
        $pdf->SetFont('Helvetica', 'B', 7);    
        $pdf->Cell(25, 10, 'ITEMS CON DESC.:', 0);
        $pdf->SetFont('Helvetica', '', 7);    
        $pdf->Cell(20, 10, '', 0);
        $pdf->Cell(15, 10, $cantidad_producto_descuento,0,0,'R');
        $pdf->Ln(3); 
        /*new*/
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
        $pdf->Ln(3);

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR TOTALES MONEDA 

        if ($parametro[0]->TOTAL_MONEDAS_TICKET === 1) { 

            /*new*/
            $pdf->SetFont('Helvetica', 'B', 7);
            $pdf->Cell(13, 10, '', 0);
            $pdf->Cell(23, 10, 'Total',0,0,'R');
            //$pdf->Cell(18, 10, 'Pagado',0,0,'R');
            $pdf->Cell(24, 10, 'Vuelto',0,0,'R');
            $pdf->Ln(8);
            $pdf->Cell(60,0,'','T');
            $pdf->Ln(2);

            //total en monedas
            $pdf->SetFont('Helvetica', '', 7);
            $pdf->Cell(15,4,'Dolares: ',0,'L');
            $pdf->Cell(22, 4, $total_dolares,0,0,'R');
            //$pdf->Cell(17, 4, $ventas->DOLARES,0,0,'R');
            $pdf->Cell(23, 4, $vuelto_dolares,0,0,'R');
            $pdf->Ln(3);
            //pagado en monedas
            $pdf->SetFont('Helvetica', '', 7);
            $pdf->Cell(15,4,'Guaranies: ',0,'L'); 
            $pdf->Cell(22, 4, $total_guaranies,0,0,'R');
            //$pdf->Cell(17, 4, $ventas->GUARANIES,0,0,'R');
            $pdf->Cell(23, 4, $vuelto_guaranies,0,0,'R');
            $pdf->Ln(3);
            //vuelto en monedas
            $pdf->SetFont('Helvetica', '', 7);
            $pdf->Cell(15,4,'Reales: ',0,'L'); 
            $pdf->Cell(22, 4, $total_reales,0,0,'R');
            //$pdf->Cell(17, 4, $ventas->REALES,0,0,'R');
            $pdf->Cell(23, 4, $vuelto_reales,0,0,'R');
            $pdf->Ln(3);
            //vuelto en monedas
            $pdf->SetFont('Helvetica', '', 7);
            $pdf->Cell(15,4,'Pesos: ',0,'L'); 
            $pdf->Cell(22, 4, $total_pesos,0,0,'R');
            //$pdf->Cell(17, 4, $ventas->PESOS,0,0,'R');
            $pdf->Cell(23, 4, $vuelto_pesos,0,0,'R');
            /*new*/
        }

        /*  --------------------------------------------------------------------------------- */

        // PIE DE PAGINA

        $pdf->Ln(5);
        $pdf->Cell(60,0,'','T');
        $pdf->Ln(3);

        if ($parametro[0]->PIE_TICKET_PERSONALIZABLE === 1) {
            $pdf->Multicell(0,2,"Guarde este ticket.\n\nPara compras online visite www.calbea.com.\n\n ** GRACIAS POR SU COMPRA **", 0, 'C', false);
        } else {
            $pdf->Cell(60,0,$parametro[0]->MENSAJE,0,1,'C');
        }
        
        /*  --------------------------------------------------------------------------------- */

        // EJECTUAR TICKET 

        $pdf->Output('ticket.pdf','i');
    }

    public static function ticket_pdf_viejo($dato) {

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

                $pdf->SetFont('Helvetica', '', 7);
                $pdf->Cell(25,4,$articulos["cod_prod"],0,'L'); 
                $pdf->Cell(5, 4, $articulos["cantidad"],0,0,'R');
                $pdf->Cell(15, 4, '',0,0,'R');
                $pdf->Cell(15, 4, $descuento_producto[0]->TOTAL ,0,0,'R');
                $pdf->Ln(3);
                $pdf->Cell(60,4,'DESCUENTO '.$descuento_producto[0]->PORCENTAJE.'%',0,1,'L');
                $pdf->Ln(2);
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

    public static function factura_pdf($dato){
        
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
        $cliente = utf8_decode(utf8_encode($ventas->CLIENTE));
        $direccion = utf8_decode(utf8_encode($ventas->DIRECCION));
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
        $id_venta = $ventas->ID;

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
            "format" => [ 240, 148 ],
        ]);

        $mpdf->SetDisplayMode('fullpage');

        /*  --------------------------------------------------------------------------------- */

        // CARGAR DETALLE DE TRANSFERENCIA DET 
        
        foreach ($ventas_det as $key => $value) {

            // SI LA MONEDA DEL PRODUCTO ES DIFERENTE A GUARANIES COTIZAR 
            
            if ($value["MONEDA"] <> 1) {

                // PRECIO 
                
                $cotizacion = Cotizacion::ventaCotizacion([
                    'monedaProducto' => $monedaVenta, 
                    'monedaSistema' => 1, 
                    'precio' => Common::quitar_coma($value["PRECIO_UNIT"], 2), 
                    'decSistema' => 0, 
                    'venta' => $id_venta, 
                    "id_sucursal" => $user["id_sucursal"]
                ]);

                // SI NO ENCUENTRA COTIZACION RETORNAR 

                if ($cotizacion["response"] === false) {
                    header('HTTP/1.1 500 Internal Server Error');
                    exit;
                }

                $articulos[$c_rows]["precio"] = $cotizacion["valor"];

                /*  --------------------------------------------------------------------------------- */

                // TOTAL 

                $cotizacion = Cotizacion::ventaCotizacion([
                    'monedaProducto' => $monedaVenta, 
                    'monedaSistema' => 1, 
                    'precio' => Common::quitar_coma($value["PRECIO"], 2), 
                    'decSistema' => 0, 
                    'venta' => $id_venta, 
                    "id_sucursal" => $user["id_sucursal"]
                ]);

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
            $articulos[$c_rows]["descripcion"] = utf8_decode(utf8_encode(substr($value["DESCRIPCION"], 0,29)));
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

            // SI CANTIDAD DE FILAS ES IGUAL A 9 ENTONCES CREAR PAGINA 

            if ($c_rows === 9){

                // AGREGAR ARTICULOS 

                $data['articulos'] = $articulos;

                // RESTAR LAS CANTIDADES CARGADAS 

                $c_rows_array = $c_rows_array - 9;

                // PONER TRUE SWITCH YA QUE CREO UNA PAGINA 

                $switch_hojas = true;

                // CARGAR SUB TOTALES POR HOJA

                $data['cantidad'] = $cantidad;
                //$data['letra'] = 'Son Guaranies: '.substr(NumeroALetras::convertir($total, 'guaranies'), 0, strpos(NumeroALetras::convertir($total, 'guaranies'), "CON"));
                $data['letra'] = 'Son Guaranies: '.($formatter->toMoney($total, 0, 'guaranies'));
                $data['total'] = Common::precio_candec_sin_letra($total, $moneda);
                $data['exentas'] = Common::precio_candec_sin_letra($exentas, $moneda);
                $data['base5'] = Common::precio_candec_sin_letra($base5 / 21, $moneda);
                $data['base10'] = Common::precio_candec_sin_letra($base10 / 11, $moneda);
                $data['iva'] = Common::precio_candec_sin_letra(($base5 / 21) + ($base10 / 11), $moneda);

                $html = view('pdf.facturaVenta', $data)->render();
                
                /*  --------------------------------------------------------------------------------- */

                // SI NO ES LA PRIMERA HOJA AGREGAR PAGINA

                if ($c !== 9) {
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
                    
                $mpdf->WriteHTML($html);

                /*  --------------------------------------------------------------------------------- */

            } else if ($c_rows_array < 9 && $c_filas_total === $c) {
                
                // AGREGAR ARTICULOS 
                
                $data['articulos'] = $articulos;

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
            }
        }
        
        /*  --------------------------------------------------------------------------------- */
        
        // DESCARGAR ARCHIVO PDF 

        $mpdf->Output($namefile,"D");
    }

    public static function datatable_venta($request){
        
        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
            0 => 'VENTAS.ID',
            1 => 'VENTAS.CODIGO', 
            2 => 'VENTAS.CAJA', 
            3 => 'CLIENTES.NOMBRE',
            4 => 'EMPLEADOS.NOMBRE',
            5 => 'VENTAS.FECALTAS',
            6 => 'VENTAS.HORALTAS',
            7 => 'VENTAS.TIPO',
            8 => 'VENTAS.TOTAL',
            9 => 'VENTAS.ID'
        );  
        
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $dia = date("Y-m-d");

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = Venta::where('ID_SUCURSAL','=', $user->id_sucursal);

        // VERIFICAR SI NO BUSCAR POR ID VENTA

        if(empty($request->input('columns.0.search.value'))){

            if (!empty($request->input('caja'))){

                $totalData = $totalData->where('VENTAS.CAJA','=', $request->input('caja'));
            } 

            /*  --------------------------------------------------------------------------------- */

            // BUSCAR VENTAS POR FECHAS

            if (!empty($request->input('inicial'))){

                $totalData = $totalData->whereDate('VENTAS.FECALTAS', '>=', $request->input('inicial'))
                ->whereDate('VENTAS.FECALTAS', '<=', $request->input('final'))->count();

            } else {
                $totalData = $totalData->count();
            }
                             
            /*  --------------------------------------------------------------------------------- */

        } else {

            $totalData = $totalData->where('VENTAS.ID', '=', $request->input('columns.0.search.value'))->count();
        }

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE VALOR EN VARIABLE SEARCH

        if(empty($request->input('search.value'))){            

            //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

            $posts = Venta::select(DB::raw('
                    VENTAS.ID, 
                    VENTAS.CODIGO, 
                    VENTAS.CAJA,
                    EMPLEADOS.NOMBRE AS VENDEDOR, 
                    substring(VENTAS.FECHA, 1, 11) AS FECHA, 
                    VENTAS.HORA, 
                    VENTAS.TIPO, 
                    VENTAS.TOTAL, 
                    VENTAS.MONEDA, 
                    CLIENTES.NOMBRE AS CLIENTE, 
                    VENTAS_ANULADO.ANULADO, 
                    MONEDAS.CANDEC, 
                    CLIENTES.CODIGO AS CLIENTE_CODIGO'))
                ->leftjoin('VENTAS_ANULADO', 'VENTAS.ID', '=', 'VENTAS_ANULADO.FK_VENTA')
                ->leftJoin('CLIENTES', function($join){
                    $join->on('VENTAS.CLIENTE', '=', 'CLIENTES.CODIGO')
                         ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
                 })
                 ->leftJoin('EMPLEADOS', function($join){
                    $join->on('VENTAS.VENDEDOR', '=', 'EMPLEADOS.CODIGO')
                         ->on('EMPLEADOS.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
                 })
                 ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'VENTAS.MONEDA')
            ->where('VENTAS.ID_SUCURSAL','=', $user->id_sucursal)
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir);

            /*  --------------------------------------------------------------------------------- */

            if(empty($request->input('columns.0.search.value'))){

                if (!empty($request->input('caja'))){
                    $posts = $posts->where('VENTAS.CAJA','=', $request->input('caja'));
                }

                if (!empty($request->input('inicial'))){
                    $posts->whereDate('VENTAS.FECALTAS', '>=', $request->input('inicial'))
                    ->whereDate('VENTAS.FECALTAS', '<=', $request->input('final'));
                }

                /*  --------------------------------------------------------------------------------- */

            } else {

                $posts->where('VENTAS.ID', '=', $request->input('columns.0.search.value'));
            } 

            $posts = $posts->get();

            /*  --------------------------------------------------------------------------------- */

        } else {

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            /*  --------------------------------------------------------------------------------- */

            // CARGAR LOS PRODUCTOS FILTRADOS EN DATATABLE

            $posts =  Venta::select(DB::raw('
                    VENTAS.ID, 
                    VENTAS.CODIGO, 
                    VENTAS.CAJA,
                    EMPLEADOS.NOMBRE AS VENDEDOR, 
                    substring(VENTAS.FECHA, 1, 11) AS FECHA, 
                    VENTAS.HORA, 
                    VENTAS.TIPO, 
                    VENTAS.TOTAL, 
                    VENTAS.MONEDA, 
                    CLIENTES.NOMBRE AS CLIENTE, 
                    VENTAS_ANULADO.ANULADO, 
                    MONEDAS.CANDEC, 
                    CLIENTES.CODIGO AS CLIENTE_CODIGO'))
                ->leftjoin('VENTAS_ANULADO', 'VENTAS.ID', '=', 'VENTAS_ANULADO.FK_VENTA')
                ->leftJoin('CLIENTES', function($join){
                    $join->on('VENTAS.CLIENTE', '=', 'CLIENTES.CODIGO')
                         ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
                })
                ->leftJoin('EMPLEADOS', function($join){
                    $join->on('VENTAS.VENDEDOR', '=', 'EMPLEADOS.CODIGO')
                         ->on('EMPLEADOS.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
                })
                ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'VENTAS.MONEDA')
            ->where('VENTAS.ID_SUCURSAL','=', $user->id_sucursal)
                ->where(function ($query) use ($search) {
                    $query->where('VENTAS.CODIGO','LIKE',"%{$search}%")
                          ->orWhere('VENTAS.CODIGO_CA', 'LIKE',"%{$search}%")
                          ->orWhere('CLIENTES.NOMBRE', 'LIKE',"%{$search}%")
                          ->orWhere('VENTAS.ID', 'LIKE',"%{$search}%");
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir);

            /*  --------------------------------------------------------------------------------- */

            if(empty($request->input('columns.0.search.value'))){

                if (!empty($request->input('caja'))){
                    $posts = $posts->where('VENTAS.CAJA','=', $request->input('caja'));
                } 

                if (!empty($request->input('inicial'))){
                    $posts->whereDate('VENTAS.FECALTAS', '>=', $request->input('inicial'))
                    ->whereDate('VENTAS.FECALTAS', '<=', $request->input('final'));
                }

                /*  --------------------------------------------------------------------------------- */

            } else {

                $posts->where('VENTAS.ID', '=', $request->input('columns.0.search.value'));
            }  

            $posts = $posts->get();                

            /*  --------------------------------------------------------------------------------- */

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 
        
            $totalFiltered = count($posts);
        }

        /*  --------------------------------------------------------------------------------- */

        $data = array();

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts)){
            foreach ($posts as $post){

                /*  --------------------------------------------------------------------------------- */

                // CARGAR EN LA VARIABLE 

                $nestedData['ID'] = $post->ID;
                $nestedData['CODIGO'] = $post->CODIGO;
                $nestedData['CAJA'] = $post->CAJA;
                $nestedData['CLIENTE'] = $post->CLIENTE;
                $nestedData['VENDEDOR'] = $post->VENDEDOR;
                $nestedData['FECHA'] = $post->FECHA;
                $nestedData['HORA'] = $post->HORA;
                $nestedData['TIPO'] = $post->TIPO;
                $nestedData['TOTAL'] = Common::precio_candec($post->TOTAL, $post->MONEDA);
                $nestedData['TOTAL_SIN_LETRA'] = Common::precio_candec_sin_letra($post->TOTAL, $post->MONEDA);
                $nestedData['TOTAL_CRUDO'] = $post->TOTAL;
                $nestedData['MONEDA'] = $post->MONEDA;
                $nestedData['CANDEC'] = $post->CANDEC;
                $nestedData['CLIENTE_CODIGO'] = $post->CLIENTE_CODIGO;

                if ($post->ANULADO === 1) {

                    /*  --------------------------------------------------------------------------------- */

                    // VENTA ANULADA 

                    $nestedData['ESTATUS'] = 'table-danger';

                    $nestedData['ACCION'] = "
                        &emsp;<a href='#' id='mostrarDetalle' title='Detalle'><i class='fa fa-list' aria-hidden='true'></i></a>
                        &emsp;<a href='#' id='imprimirTicket' title='Reporte'><i class='fa fa-file text-secondary' aria-hidden='true'></i></a>
                        &emsp;<a href='#' id='imprimirPdf' title='Pdf'><i class='fa fa-file text-danger' aria-hidden='true'></i></a>";

                    /*  --------------------------------------------------------------------------------- */

                } else if($post->TIPO === 'PE' && $post->ANULADO === 2) {

                    /*  --------------------------------------------------------------------------------- */

                    // PAGO AL ENTREGAR PENDIENTE

                    $nestedData['ESTATUS'] = 'table-warning';

                    $nestedData['ACCION'] = "
                        &emsp;<a href='#' id='mostrarDetalle' title='Detalle'><i class='fa fa-list' aria-hidden='true'></i></a>
                        &emsp;<a href='#' id='pagarVenta' title='Pagar'><i class='fa fa-vote-yea text-success' aria-hidden='true'></i></a>
                        &emsp;<a href='#' id='imprimirTicket' title='Reporte'><i class='fa fa-file text-secondary' aria-hidden='true'></i></a>
                        &emsp;<a href='#' id='imprimirFactura' title='Imprimir'><i class='fa fa-print text-primary' aria-hidden='true'></i></a>
                        &emsp;<a href='#' id='imprimirPdf' title='Pdf'><i class='fa fa-file text-danger' aria-hidden='true'></i></a>";

                    /*  --------------------------------------------------------------------------------- */

                } else {

                    $nestedData['ACCION'] = "
                        &emsp;<a href='#' id='mostrarDetalle' title='Detalle'><i class='fa fa-list' aria-hidden='true'></i></a>
                        &emsp;<a href='#' id='imprimirTicket' title='Reporte'><i class='fa fa-file text-secondary' aria-hidden='true'></i></a>
                        &emsp;<a href='#' id='imprimirFactura' title='Imprimir'><i class='fa fa-print text-primary' aria-hidden='true'></i></a>
                        &emsp;<a href='#' id='imprimirPdf' title='Pdf'><i class='fa fa-file text-danger' aria-hidden='true'></i></a>";

                    $nestedData['ESTATUS'] = '';
                }

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
    }

    public static function filtrar_venta($datos){

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */
 
        // OBTENER TODOS LOS DATOS DEL TALLE

        if($datos['id']['co_ca'] === 1){

            $venta = Venta::leftJoin('CLIENTES', function($join){
                    $join->on('VENTAS.CLIENTE', '=', 'CLIENTES.CODIGO')
                         ->on('VENTAS.ID_SUCURSAL', '=', 'CLIENTES.ID_SUCURSAL');
                })
                ->leftJoin('EMPLEADOS', function($join){
                    $join->on('VENTAS.VENDEDOR', '=', 'EMPLEADOS.CODIGO')
                         ->on('VENTAS.ID_SUCURSAL', '=', 'EMPLEADOS.ID_SUCURSAL');
                })
                ->leftjoin('MONEDAS', 'VENTAS.MONEDA', '=', 'MONEDAS.CODIGO')
                ->select( 
                    DB::raw('VENTAS.ID AS FK_VENTA'),
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

            $venta = Venta::leftJoin('CLIENTES', function($join){
                    $join->on('VENTAS.CLIENTE', '=', 'CLIENTES.CODIGO')
                         ->on('VENTAS.ID_SUCURSAL', '=', 'CLIENTES.ID_SUCURSAL');
                })
                ->leftJoin('EMPLEADOS', function($join){
                    $join->on('VENTAS.VENDEDOR', '=', 'EMPLEADOS.CODIGO')
                         ->on('VENTAS.ID_SUCURSAL', '=', 'EMPLEADOS.ID_SUCURSAL');
                })
                ->leftjoin('MONEDAS', 'VENTAS.MONEDA', '=', 'MONEDAS.CODIGO')
                ->select(
                    DB::raw('VENTAS.ID AS FK_VENTA'),
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

        /*  --------------------------------------------------------------------------------- */

        if(count($venta) <= 0){
           return ["response" => false];
        }
        
        // RETORNAR EL VALOR
        
        return ["response" => true, "ventas" => $venta];
    }

    public static function ventas_datatable($request){

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 
        
        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIARA VARIABLES

        $dia = date("Y-m-d");

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
            0 => 'VENTAS.ID', 
            1 => 'VENTAS.CODIGO', 
            2 => 'VENTAS.CAJA',
            3 => 'VENTAS.CODIGO_CA',
            4 => 'CLIENTES.NOMBRE',
            5 => 'VENTAS.FECALTAS',
            6 => 'VENTAS.TIPO',
            7 => 'VENTAS_TARJETA.MONTO',
            8 => 'VENTAS.DESCUENTO',
            9 => 'VENTAS.TOTAL',
            10 => 'MONEDAS.DESCRIPCION'
        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = Venta::leftjoin('VENTAS_ANULADO', 'VENTAS_ANULADO.FK_VENTA', '=', 'VENTAS.ID')
        ->where('id_sucursal','=',$user->id_sucursal)
        ->where('FECALTAS','=',$dia)
        ->Where('VENTAS_ANULADO.anulado','=',0)
        ->where('CAJA','=',$request->input('caja_numero'))
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

        if(empty($request->input('search.value'))){            

            //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

            $posts = Venta::leftjoin('VENTAS_TARJETA', 'FK_VENTA', '=', 'VENTAS.ID')
                ->leftjoin('VENTAS_ANULADO', 'VENTAS_ANULADO.FK_VENTA', '=', 'VENTAS.ID')
                ->select(DB::raw('
                    VENTAS.ID AS FK_VENTA,
                    VENTAS.CODIGO AS CODIGO, 
                    VENTAS.CAJA AS CAJA,
                    VENTAS.CODIGO_CA AS CODIGO_CA,
                    SUBSTRING(VENTAS.FECALTAS, 1, 10) AS FECHA,
                    CLIENTES.NOMBRE AS NOMBRE,
                    VENTAS.TIPO AS TIPO,
                    IFNULL(VENTAS_TARJETA.MONTO, 0) AS TARJETA,
                    VENTAS.DESCUENTO AS DESCUENTO,
                    VENTAS.TOTAL AS TOTAL,
                    MONEDAS.DESCRIPCION AS MONEDA,
                    MONEDAS.CANDEC AS CANDEC'))
                ->leftJoin('CLIENTES', function($join){
                    $join->on('CLIENTES.CODIGO', '=', 'VENTAS.CLIENTE')
                         ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
                })
                ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'VENTAS.MONEDA')
            ->Where('VENTAS.ID_SUCURSAL','=',$user->id_sucursal)
            ->Where('VENTAS.FECALTAS','=',$dia)
            ->Where('VENTAS_ANULADO.anulado','<>',1)
            ->where('CAJA','=',$request->input('caja_numero'))   
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();

        } else {

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            // CARGAR LOS PRODUCTOS FILTRADOS EN DATATABLE

            $posts = Venta::select(DB::raw('
                    VENTAS.ID AS FK_VENTA,
                    VENTAS.CODIGO AS CODIGO, 
                    VENTAS.CAJA AS CAJA,
                    VENTAS.CODIGO_CA AS CODIGO_CA,
                    CLIENTES.NOMBRE AS NOMBRE,
                    SUBSTRING(VENTAS.FECALTAS, 1, 10) AS FECHA,
                    VENTAS.TIPO AS TIPO,
                    IFNULL(VENTAS_TARJETA.MONTO, 0) AS TARJETA,
                    VENTAS.DESCUENTO AS DESCUENTO,
                    VENTAS.TOTAL AS TOTAL,
                    MONEDAS.DESCRIPCION AS MONEDA,
                    MONEDAS.CANDEC AS CANDEC'))
                ->leftjoin('VENTAS_TARJETA', 'FK_VENTA', '=', 'VENTAS.ID')
                ->leftjoin('VENTAS_ANULADO', 'VENTAS_ANULADO.FK_VENTA', '=', 'VENTAS.ID')
                ->leftjoin('MONEDAS', 'MONEDAS.CODIGO', '=', 'VENTAS.MONEDA')
                ->leftJoin('CLIENTES', function($join){
                        $join->on('CLIENTES.CODIGO', '=', 'VENTAS.CLIENTE')
                             ->on('CLIENTES.ID_SUCURSAL', '=', 'VENTAS.ID_SUCURSAL');
                    })
            ->Where('VENTAS.ID_SUCURSAL','=',$user->id_sucursal)
            ->Where('VENTAS.FECALTAS','=',$dia)
            ->Where('VENTAS_ANULADO.anulado','<>',1)  
            ->where('CAJA','=',$request->input('caja_numero')) 
            ->where(function ($query) use ($search) {
                $query->where('VENTAS.ID', 'LIKE',"%{$search}%")
                      ->orwhere('VENTAS.CODIGO','LIKE',"%{$search}%")
                      ->orWhere('CLIENTES.NOMBRE', 'LIKE',"%{$search}%")
                      ->orWhere('ventas.CODIGO_CA', 'LIKE',"%{$search}%");
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = count($posts); 
        }

        /*  --------------------------------------------------------------------------------- */

        $data = array();

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts)){

            foreach ($posts as $post){

                /*  --------------------------------------------------------------------------------- */

                // CARGAR EN LA VARIABLE

                $nestedData['ID_VENTA'] = $post->FK_VENTA;
                $nestedData['CODIGO'] = $post->CODIGO;
                $nestedData['CAJA'] = $post->CAJA;
                $nestedData['CODIGO_CA'] = $post->CODIGO_CA;
                $nestedData['FECHA'] = $post->FECHA;
                $nestedData['NOMBRE'] = $post->NOMBRE;
                $nestedData['TIPO'] = $post->TIPO;
                $nestedData['TARJETA'] = $post->TARJETA;
                $nestedData['DESCUENTO'] = $post->DESCUENTO;
                $nestedData['TOTAL'] = Common::precio_candec_sin_letra($post->TOTAL, $post->CANDEC);
                $nestedData['MONEDA'] = $post->MONEDA;

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
    }
    
    public static function anular_venta($datos){

        try {
            
            DB::connection('retail')->beginTransaction();
      
            $user = auth()->user();
            $dia = date("Y-m-d H:i:s");
            $hora = date("H:i:s");

            /*  --------------------------------------------------------------------------------- */

           $venta = Venta::select(DB::raw('
                VENTAS.TIPO,
                VENTAS.TOTAL AS TOTAL,
                VENTAS.ID AS ID'))
            ->Where('VENTAS.ID','=',$datos['data']['id_venta'])
            ->get();
            
            if(count($venta) <= 0){
               return ["response" => false, 'status_text' => "No existe la venta seleccionada"];
            }

            VentasAnulado::anular_venta($venta["0"]->ID, $dia);

            $id_cupon = VentaCupon::obtener_id_cupon_venta($venta["0"]->ID);

            if($id_cupon !== 0){

                Cupon::actualizar_uso($id_cupon, 2);
            }

           //SI ES DISTINTO DE VENTAS AL CONTADO

           if($venta["0"]->TIPO !== "CO"){

                //SI LA VENTAS ES A CREDITO 

                if($venta["0"]->TIPO === "CR"){

                    //ELIMINAR REGISTROS DE DEUDAS SI NO EXISTEN YA PAGOS EXISTENTES

                    $venta_credito = VentaCredito::select(DB::raw('VENTAS_CREDITO.PAGO'))
                    ->Where('VENTAS_CREDITO.FK_VENTA','=',$datos['data']['id_venta'])
                    ->where('VENTAS_CREDITO.PAGO','=',0)
                    ->get()
                    ->toArray();

                    //SI ENCUENTRA EL REGISTRO Y SIN PAGAR ENTONCES ELIMINA

                    if(count($venta_credito) > 0){

                        VentaCredito::where('FK_VENTA', $datos['data']['id_venta'])->delete();

                    }else{

                        // SI NO ENCUENTRA IMPLICA QUE YA EXISTEN PAGOS ENTONCES NO ELIMINA Y RETORNA
                        return ["response" => false,"status_text" => "Esta venta ya posee pagos."];
                    }
                }
            }

            $venta = Venta::where('CODIGO', $datos['data']['codigo'])
            ->Where('VENTAS.CAJA','=',$datos['data']['caja'])
            ->Where('VENTAS.ID_SUCURSAL','=',$user->id_sucursal) 
            ->update([
                'FECMODIF' => $dia,
                'HORMODIF' => $hora, 
                'USERM' => $user->name]
            );

            $venta = Ventas_det::leftjoin('VENTASDET_TIENE_LOTES', 'ID_VENTAS_DET', '=', 'VENTASDET.ID')
                ->select(DB::raw('VENTASDET.COD_PROD AS COD_PROD'),
                    DB::raw('VENTASDET.ID AS ID'),
                    DB::raw('VENTASDET_TIENE_LOTES.ID_LOTE AS ID_LOTE'),
                    DB::raw('VENTASDET_TIENE_LOTES.CANTIDAD AS CANTIDAD'),
                    DB::raw('VENTASDET.PRECIO AS PRECIO'),
                    DB::raw('VENTASDET.LOTE AS LOTE'))
            ->Where('VENTASDET.CODIGO','=',$datos['data']['codigo'])
            ->Where('VENTASDET.CAJA','=',$datos['data']['caja'])
            ->Where('VENTASDET.ID_SUCURSAL','=',$user->id_sucursal) 
            ->where('VENTASDET.DESCRIPCION', 'NOT LIKE', 'DESCUENTO%')
            ->get();

            foreach ($venta as $key => $value) {

                Stock::sumar_stock_id_lote($value->ID_LOTE,$value->CANTIDAD);

                $delete_lote_venta = DB::connection('retail')->table('VENTASDET_TIENE_LOTES')
                ->where('ID_VENTAS_DET', $value->ID)
                ->delete();
            }
            
            $ventas = Ventas_det::where('CODIGO', $datos['data']['codigo'])
            ->Where('CAJA','=',$datos['data']['caja'])
            ->Where('ID_SUCURSAL','=',$user->id_sucursal) 
            ->update([
                'ANULADO' => 1,
                'FECMODIF' => $dia,
                'HORMODIF' => $hora,
                'USERM' => $user->name]
            );

            //GUARDAR LA AUTORIZACION DE LA ANULACION DE LA FACTURA

            VentaAnuladoTieneAutorizacion::guardar_referencia([
                "FK_VENTA" => $datos['data']['id_venta'],
                "FK_USER" => $datos['data']['autorizacion']['ID_USUARIO'],
                "FK_USER_SUPERVISOR" => $datos['data']['autorizacion']['ID_USER_SUPERVISOR']
            ]);

            DB::connection('retail')->commit();

            return ["response" => true, "ventas" => $venta];

        } catch (Exception $e) {

            DB::connection('retail')->rollBack();
            throw $e;
        }
    }

    public static function devolucion_productos($request) {

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $codigo = $request->input('codigo');
        $caja = $request->input('caja');

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
            0 => 'VENTASDET.ITEM', 
            1 => 'VENTASDET.COD_PROD',
            2 => 'VENTASDET.DESCRIPCION',
            3 => 'VENTASDET.CANTIDAD',
            4 => 'VENTASDET.PRECIO_UNIT',
            5 => 'VENTASDET.PRECIO'
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

        if(empty($request->input('search.value'))){            

            //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

            $posts = Ventas_det::select(DB::raw('
                    VENTASDET.ID, 
                    VENTASDET.ITEM, 
                    VENTASDET.COD_PROD, 
                    VENTASDET.DESCRIPCION, 
                    VENTASDET.CANTIDAD, 
                    VENTASDET.IVA AS IMPUESTO, 
                    PRODUCTOS.IMPUESTO AS IVA_PORCENTAJE, 
                    VENTASDET.PRECIO_UNIT AS PRECIO, 
                    VENTASDET.PRECIO AS TOTAL, 
                    VENTAS.MONEDA, 
                    IFNULL(ventasdet_descuento.TOTAL, 0) AS DESCUENTO_TOTAL, 
                    IFNULL(ventasdet_descuento.PORCENTAJE, 0) AS DESCUENTO_PORCENTAJE, 
                    IFNULL((SELECT SUM(NC_DET.CANTIDAD) FROM NOTA_CREDITO_DET as NC_DET LEFT JOIN NOTA_CREDITO AS NC ON NC.ID=NC_DET.FK_NOTA_CREDITO WHERE ((NC_DET.FK_VENTASDET = VENTASDET.ID) AND (NC.PROCESADO <> 2))),0) AS CANTIDAD_DEVUELTA,
                    IFNULL(VENTAS_DESCUENTO.PORCENTAJE, 0) AS DESCUENTO_GENERAL_PORCENTAJE, 
                    IFNULL(VENTAS_CUPON.CUPON_PORCENTAJE, 0) AS DESCUENTO_CUPON_PORCENTAJE'))
                ->leftJoin('VENTAS', 'VENTAS.ID', '=', 'VENTASDET.FK_VENTA')
                ->leftjoin('NOTA_CREDITO_DET', 'NOTA_CREDITO_DET.FK_VENTASDET', '=', 'VENTASDET.ID')
                ->leftjoin('ventasdet_descuento', 'ventasdet_descuento.FK_VENTASDET', '=', 'VENTASDET.ID')
                ->leftjoin('VENTAS_DESCUENTO', 'VENTAS_DESCUENTO.FK_VENTAS', '=', 'VENTAS.ID')
                ->leftjoin('VENTAS_CUPON', 'VENTAS_CUPON.FK_VENTA', '=', 'VENTAS.ID')
                ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
            ->where('VENTASDET.ID_SUCURSAL','=', $user->id_sucursal)
            ->where('VENTASDET.CODIGO','=', $codigo)
            ->where('VENTASDET.CAJA','=', $caja)
            ->groupBy('VENTASDET.ID')
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();

        } else {

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            // CARGAR LOS PRODUCTOS FILTRADOS EN DATATABLE

            $posts =  Ventas_det::select(DB::raw('
                    VENTASDET.ID, 
                    VENTASDET.ITEM, 
                    VENTASDET.COD_PROD, 
                    VENTASDET.DESCRIPCION, 
                    VENTASDET.CANTIDAD, 
                    VENTASDET.IVA AS IMPUESTO, 
                    PRODUCTOS.IMPUESTO AS IVA_PORCENTAJE, 
                    VENTASDET.PRECIO_UNIT AS PRECIO, 
                    VENTASDET.PRECIO AS TOTAL, 
                    VENTAS.MONEDA, 
                    IFNULL(ventasdet_descuento.TOTAL, 0) AS DESCUENTO_TOTAL, 
                    IFNULL(ventasdet_descuento.PORCENTAJE, 0) AS DESCUENTO_PORCENTAJE, 
                    IFNULL((SELECT SUM(NC_DET.CANTIDAD) FROM NOTA_CREDITO_DET as NC_DET LEFT JOIN NOTA_CREDITO AS NC ON NC.ID=NC_DET.FK_NOTA_CREDITO WHERE ((NC_DET.FK_VENTASDET = VENTASDET.ID) AND (NC.PROCESADO <> 2))),0) AS CANTIDAD_DEVUELTA,
                    IFNULL(VENTAS_DESCUENTO.PORCENTAJE, 0) AS DESCUENTO_GENERAL_PORCENTAJE'))
                ->leftJoin('VENTAS', 'VENTAS.ID', '=', 'VENTASDET.FK_VENTA')
                ->leftjoin('NOTA_CREDITO_DET', 'NOTA_CREDITO_DET.FK_VENTASDET', '=', 'VENTASDET.ID')
                ->leftjoin('ventasdet_descuento', 'ventasdet_descuento.FK_VENTASDET', '=', 'VENTASDET.ID')
                ->leftjoin('VENTAS_DESCUENTO', 'VENTAS_DESCUENTO.FK_VENTAS', '=', 'VENTAS.ID')
                ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
            ->where('VENTASDET.ID_SUCURSAL','=', $user->id_sucursal)
            ->where('VENTASDET.CODIGO','=', $codigo)
            ->where('VENTASDET.CAJA','=', $caja)
            ->where(function ($query) use ($search) {
                $query->where('VENTASDET.COD_PROD','LIKE',"%{$search}%")
                    ->orWhere('VENTASDET.DESCRIPCION', 'LIKE',"%{$search}%");
            })
            ->groupBy('VENTASDET.ID')
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = count($posts);  
        }

        /*  --------------------------------------------------------------------------------- */

        $data = array();

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts)){

            foreach ($posts as $post){

                // DESCUENTO GENERAL 

                $desc = Common::calculo_porcentaje_descuentos([
                    'PRECIO_PRODUCTO' => $post->PRECIO,
                    'PORCENTAJE_DESCUENTO' => $post->DESCUENTO_GENERAL_PORCENTAJE,
                    'CANTIDAD' => $post->CANTIDAD,
                ]);

                $post->PRECIO = $desc['PRECIO_REAL'];
                $post->TOTAL = $desc['TOTAL_REAL'];

                /*  --------------------------------------------------------------------------------- */

                // DESCUENTO CUPON
                
                $desc = Common::calculo_porcentaje_descuentos([
                    'PRECIO_PRODUCTO' => $post->PRECIO,
                    'PORCENTAJE_DESCUENTO' => $post->DESCUENTO_CUPON_PORCENTAJE,
                    'CANTIDAD' => $post->CANTIDAD,
                ]);
                
                $post->PRECIO = $desc['PRECIO_REAL'];
                $post->TOTAL = $desc['TOTAL_REAL'];

                /*  --------------------------------------------------------------------------------- */

                // CARGAR EN LA VARIABLE 

                $nestedData['ID'] = $post->ID;
                $nestedData['ITEM'] = $post->ITEM;
                $nestedData['COD_PROD'] = $post->COD_PROD;
                $nestedData['DESCRIPCION'] = $post->DESCRIPCION;
                $nestedData['CANTIDAD'] = $post->CANTIDAD - $post->CANTIDAD_DEVUELTA;
                $nestedData['CANTIDAD_DEVUELTA'] = $post->CANTIDAD_DEVUELTA;
                $nestedData['DESCUENTO_TOTAL'] = Common::precio_candec_sin_letra($post->DESCUENTO_TOTAL, $post->MONEDA);
                $nestedData['DESCUENTO'] = Common::precio_candec_sin_letra($post->DESCUENTO_PORCENTAJE, 1);
                $nestedData['IVA_PORCENTAJE'] = Common::precio_candec_sin_letra($post->IVA_PORCENTAJE, 1);
                $nestedData['IMPUESTO'] = Common::precio_candec_sin_letra($post->IMPUESTO, $post->MONEDA);
                $nestedData['PRECIO'] = Common::precio_candec_sin_letra($post->PRECIO, $post->MONEDA);
                $nestedData['TOTAL'] = Common::precio_candec_sin_letra($post->TOTAL, $post->MONEDA);

                if ($nestedData['CANTIDAD'] == 0) {
                    $nestedData['ACCION'] = 'SIN CANTIDAD';
                } else {
                    $nestedData['ACCION'] = '
                        <form class="form-inline">
                            <div class="custom-control custom-checkbox">
                                <input  type="checkbox" name="check" class="custom-control-input call-checkbox" id="'.$post->COD_PROD.'">
                                <label for="'.$post->COD_PROD.'"  class="custom-control-label"></label>
                                <input type="number" value='.$nestedData['CANTIDAD'].' id="'.$post->COD_PROD.'" name="'.$post->COD_PROD.'" class="form-control-sm" min="0" max='.$nestedData['CANTIDAD'].'>
                            </div>
                        </form>';
                }
                
                if ($nestedData['CANTIDAD'] == 0) {
                    $nestedData['ESTATUS'] = 'table-danger';
                } else if ($nestedData['CANTIDAD_DEVUELTA'] > 0) {
                    $nestedData['ESTATUS'] = 'table-warning';
                } else {
                    $nestedData['ESTATUS'] = '';
                }

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
    }

    public static function mostrar_productos($request) {

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $codigo = $request->input('codigoVenta');
        $caja =  $request->input('codigoCaja');

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
            0 => 'VENTASDET.ITEM', 
            1 => 'VENTASDET.COD_PROD',
            2 => 'VENTASDET.DESCRIPCION',
            3 => 'VENTASDET.CANTIDAD',
            4 => 'VENTASDET.PRECIO_UNIT',
            5 => 'VENTASDET.PRECIO',
            6 => 'ventasdet_descuento.PORCENTAJE',
            7 => 'ventasdet_descuento.TOTAL'
        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE PRODUCTOS ENCONTRADOS 

        $totalData = Ventas_det::join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
            ->leftJoin('VENTAS', 'VENTAS.ID', '=', 'VENTASDET.FK_VENTA')
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

        if(empty($request->input('search.value'))){            

            //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

            $posts = Ventas_det::select(DB::raw('
                    VENTASDET.ITEM, 
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
                ->leftJoin('VENTAS', 'VENTAS.ID', '=', 'VENTASDET.FK_VENTA')
            ->where('VENTASDET.ID_SUCURSAL','=', $user->id_sucursal)
            ->where('VENTASDET.CODIGO','=', $codigo)
            ->where('VENTASDET.CAJA','=', $caja)
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();

        } else {

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            // CARGAR LOS PRODUCTOS FILTRADOS EN DATATABLE

            $posts = Ventas_det::select(DB::raw('VENTASDET.ITEM, 
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
                ->leftJoin('VENTAS', 'VENTAS.ID', '=', 'VENTASDET.FK_VENTA')
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

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = count($posts); 
        }

        $data = array();

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts)){

            foreach ($posts as $post){

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

    public static function obtenerCuentas($request){

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
            0 => 'COMPRAS.CODIGO', 
            1 => 'PROVEEDORES.NOMBRE',
            2 => 'COMPRAS.TIPO',
            3 => 'COMPRAS.NRO_FACTURA',
            4 => 'COMPRAS.FEC_FACTURA',
            5 => 'COMPRAS.TOTAL',
            6 => 'ACCION',
        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR LA CANTIDAD DE TRANSFERENCIAS ENCONTRADAS 

        $totalData = Compra::where('ID_SUCURSAL','=', $user->id_sucursal)->count();  
        
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $totalFiltered = $totalData; 
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI EXISTE VALOR EN VARIABLE SEARCH

        if(empty($request->input('search.value'))){            

            //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

            $posts = Compra::select(DB::raw('
                    COMPRAS.CODIGO, 
                    COMPRAS.MONEDA, 
                    PROVEEDORES.NOMBRE, 
                    COMPRAS.TIPO, 
                    COMPRAS.NRO_FACTURA, 
                    COMPRAS.FEC_FACTURA, 
                    COMPRAS.TOTAL'))
                ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'COMPRAS.PROVEEDOR')
            ->where('COMPRAS.ID_SUCURSAL','=', $user->id_sucursal)
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();

        } else {

            // CARGAR EL VALOR A BUSCAR 

            $search = $request->input('search.value'); 

            // CARGAR LOS PRODUCTOS FILTRADOS EN DATATABLE

            $posts = Compra::select(DB::raw('
                    COMPRAS.CODIGO, 
                    COMPRAS.MONEDA, 
                    PROVEEDORES.NOMBRE, 
                    COMPRAS.TIPO, 
                    COMPRAS.NRO_FACTURA, 
                    COMPRAS.FEC_FACTURA, 
                    COMPRAS.TOTAL'))
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

            // CARGAR LA CANTIDAD DE PRODUCTOS FILTRADOS 

            $totalFiltered = count($posts);
        }

        /*  --------------------------------------------------------------------------------- */

        $data = array();

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts)){

            foreach ($posts as $post){

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
            
                $nestedData['ACCION'] = "&emsp;<a href='#' id='mostrar' title='Mostrar'><i class='fa fa-list'  aria-hidden='true'></i></a>
                &emsp;<a href='#' id='editar' title='Editar'><i class='fa fa-edit text-warning' aria-hidden='true'></i></a>
                &emsp;<a href='#' id='eliminar' title='Eliminar'><i class='fa fa-trash text-danger' aria-hidden='true'></i></a>
                &emsp;<a href='#' id='reporte' title='Reporte'><i class='fa fa-file text-secondary' aria-hidden='true'></i></a>";

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
    }

    public static function pagoEntrega($data){
        
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
            ->update([ 
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
                "HORMODIF" => $hora
            ]);
            
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

            return [
                "response" => true, 
                "statusText" => "¡Se ha guardado correctamente el pago!", 
                "CODIGO" => $data['data']['codigo'], 
                "CAJA" => $data['data']['caja']
            ];

            /*  --------------------------------------------------------------------------------- */

        } catch (Exception $e) {
            
           /*  --------------------------------------------------------------------------------- */

           // NO GUARDAR NINGUN CAMBIO 

           DB::connection('retail')->rollBack();
           throw $e;
        }
    }

    public static function metodos_pago($datos, $venta){

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

            return ["response" => true, "statusText" => "¡Se ha guardado correctamente los metodos de pago!"];


        } catch (Exception $e) {
            
           /*  --------------------------------------------------------------------------------- */

           // NO GUARDAR NINGUN CAMBIO 

           DB::connection('retail')->rollBack();
           throw $e;
        }

    }

    public static function metodos_pago_credito($datos, $abono){
        
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

            return ["response" => true, "statusText" => "¡Se ha guardado correctamente los metodos de pago!"];

        } catch (Exception $e) {
            
           /*  --------------------------------------------------------------------------------- */

           // NO GUARDAR NINGUN CAMBIO 

           DB::connection('retail')->rollBack();
           throw $e;
        }

    }

    public static function pagoCredito($data){
        
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
                'FK_SUCURSAL' => $user->id_sucursal])
            )['valor'];

            /*  --------------------------------------------------------------------------------- */

            // METODOS PAGO
            
            Venta::metodos_pago_credito($datos, $venta_abono_id);

            /*  --------------------------------------------------------------------------------- */

            // CALCULO DE PAGO PRO VENTA 

            if($data["data"]["tipo_proceso"]==3){

                $creditos = VentaCredito::obtener_creditos_cliente($data["data"]["cliente"]);
        
                if ($creditos["response"] === false) {
                    return $creditos;
                } else {
                    $creditos = $creditos["creditos"];
                }

                /*  --------------------------------------------------------------------------------- */

                // RECORRER VENTAS CON CREDITO 

                foreach ($creditos as $key => $value) {
                    
                    if ($value->SALDO >= $datos['EFECTIVO'] && $datos['EFECTIVO'] !== 0) {

                        // INSERTAR VENTAS DET CREDITO 

                        VentaAbonoDet::guardar_referencia([
                            'FK_VENTA' => $value->FK_VENTA,
                            'FK_VENTAS_ABONO' => $venta_abono_id,
                            'PAGO' => $datos['EFECTIVO']
                        ]);

                        // ACTUALIZAR VENTA CREDITO 

                        VentaCredito::where('FK_VENTA', '=', $value->FK_VENTA)
                        ->update([
                            'PAGO' => DB::raw('PAGO + '.$datos['EFECTIVO'].''),
                            'SALDO' => ($value->SALDO - $datos['EFECTIVO']),
                            'FECHA_CANCELACION' => $fecha
                        ]);

                        // FIJAR CERO EFECTIVO 

                        $datos['EFECTIVO'] = 0;

                    } else if($value->SALDO > 0 && $datos['EFECTIVO'] !== 0) {

                        // INSERTAR VENTAS DET CREDITO 

                        VentaAbonoDet::guardar_referencia([
                            'FK_VENTA' => $value->FK_VENTA,
                            'FK_VENTAS_ABONO' => $venta_abono_id,
                            'PAGO' => $value->SALDO
                        ]);

                        // ACTUALIZAR VENTA CREDITO 

                        VentaCredito::where('FK_VENTA', '=', $value->FK_VENTA)
                        ->update([
                            'PAGO' => DB::raw('PAGO + '.$value->SALDO.''),
                            'SALDO' => 0,
                            'FECHA_CANCELACION' => $fecha
                        ]);

                        // FIJAR CERO EFECTIVO 

                        $datos['EFECTIVO'] = $datos['EFECTIVO'] - $value->SALDO;
                    }
                }

            }else{

                $credito = VentaCredito::obtener_credito_cliente($data["data"]["fk_venta"]);
        
                if ($credito["response"] === false) {

                    return $credito;
                } else {
                    $credito = $credito["credito"];
                }

                if($credito[0]["SALDO"]>= $datos["EFECTIVO"] && $datos['EFECTIVO'] !== 0){

                    //GUARDAR REFERENCIA EN ABONO_DET

                    VentaAbonoDet::guardar_referencia([
                        'FK_VENTA' => $credito[0]["FK_VENTA"],
                        'FK_VENTAS_ABONO' => $venta_abono_id,
                        'PAGO' => $datos['EFECTIVO']
                    ]);

                    /*  --------------------------------------------------------------------------------- */

                    // ACTUALIZAR VENTA CREDITO 

                    VentaCredito::where('FK_VENTA', '=',$credito[0]["FK_VENTA"])
                    ->update([
                        'PAGO' => DB::raw('PAGO + '.$datos['EFECTIVO'].''),
                        'SALDO' => ($credito[0]["SALDO"] - $datos['EFECTIVO']),
                        'FECHA_CANCELACION' => $fecha
                    ]);

                    /*  --------------------------------------------------------------------------------- */

                    // FIJAR CERO EFECTIVO 

                    $datos['EFECTIVO'] = 0;

                }elseif($credito[0]["SALDO"]>0 && $datos['EFECTIVO'] !== 0){

                    // INSERTAR VENTAS DET CREDITO 

                    VentaAbonoDet::guardar_referencia([
                        'FK_VENTA' =>$credito[0]["FK_VENTA"],
                        'FK_VENTAS_ABONO' => $venta_abono_id,
                        'PAGO' => $credito[0]["SALDO"]
                    ]);

                    /*  --------------------------------------------------------------------------------- */

                    // ACTUALIZAR VENTA CREDITO 

                    VentaCredito::where('FK_VENTA', '=', $credito[0]["FK_VENTA"])
                    ->update([
                        'PAGO' => DB::raw('PAGO + '.$credito[0]["SALDO"].''),
                        'SALDO' => 0,
                        'FECHA_CANCELACION' => $fecha

                    ]);

                    /*  --------------------------------------------------------------------------------- */

                    // FIJAR CERO EFECTIVO 

                    $datos['EFECTIVO'] = $datos['EFECTIVO'] - $credito[0]["SALDO"];
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

            return [
                "response" => true, 
                "statusText" => "¡Se ha guardado correctamente el pago!", 
                "CODIGO" => $venta_abono_id, 
                "CAJA" => $data['data']['caja']
            ];

        } catch (Exception $e) {
            
           /*  --------------------------------------------------------------------------------- */

           // NO GUARDAR NINGUN CAMBIO 

           DB::connection('retail')->rollBack();
           throw $e;
        }
    }

    public static function reporteUnico($dato){

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
        $cotizaciones = [];
        $count = 0;
        $cotizacion_aux = '';
        
        /*  --------------------------------------------------------------------------------- */
        // OBTENER COTIZACIONES

        $cotizacionVenta = Cotizacion::obtener_venta_cotizacion($codigo)['cotizaciones'];

        foreach ($cotizacionVenta as $key => $value){

            if($count == 0){

                $cotizacion_aux = $value->VALOR;
                $cotizaciones[$count]["nombre"] = $value->MON_DE_DESC.' a '.$value->MON_A_DESC;
                $cotizaciones[$count]["cambio"] = $value->VALOR;

            }else if($cotizacion_aux != $value->VALOR){

                $cotizacion_aux = $value->VALOR;
                $cotizaciones[$count]["nombre"] = $value->MON_DE_DESC.' a '.$value->MON_A_DESC;
                $cotizaciones[$count]["cambio"] = $value->VALOR;
            }

            $count = $count + 1;   
        }

        /*  --------------------------------------------------------------------------------- */

        $total =  (Cotizacion::ventaCotizacion([
            'monedaProducto' => $moneda, 
            'monedaSistema' => (int)$dato['moneda'], 
            'precio' => Common::quitar_coma($pedido->TOTAL, 2), 
            'decSistema' => $pedido->CANDEC, 
            'venta' => $codigo, 
            "id_sucursal" => $user->id_sucursal
        ]))['valor'];

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
        $data['cotizaciones'] = $cotizaciones;

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

            // PRECIO 

           $precio = $value["PRECIO_UNIT"];

            // SI NO ENCUENTRA COTIZACION RETORNAR 

            $articulos[$c_rows]["precio"] = (Cotizacion::ventaCotizacion([
                'monedaProducto' => $moneda, 
                'monedaSistema' => (int)$dato['moneda'], 
                'precio' => Common::quitar_coma($precio, 2), 
                'decSistema' => 2, 
                'venta' => $codigo, 
                "id_sucursal" => $user->id_sucursal
            ]))['valor'];

            $articulos[$c_rows]["precio"] = Common::precio_candec(Common::quitar_coma($articulos[$c_rows]["precio"], 2), (int)$dato['moneda']);

            // CARGAR VARIABLES 

            $articulos[$c_rows]["cantidad"] = $value["CANTIDAD"];
            $articulos[$c_rows]["cod_prod"] = $value["COD_PROD"];
            $articulos[$c_rows]["descripcion"] = $value["DESCRIPCION"];

            $articulos[$c_rows]["total"] = (Cotizacion::ventaCotizacion([
                'monedaProducto' => $moneda, 
                'monedaSistema' => (int)$dato['moneda'], 
                'precio' => Common::quitar_coma($totalP, 2), 
                'decSistema' => 2, 
                'venta' => $codigo, 
                "id_sucursal" => $user->id_sucursal
            ]))['valor'];

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

                $data['total'] = (Cotizacion::ventaCotizacion([
                    'monedaProducto' => $moneda, 
                    'monedaSistema' => (int)$dato['moneda'], 
                    'precio' => Common::quitar_coma($total, 2), 
                    'decSistema' => 2, 'venta' => $codigo, 
                    "id_sucursal" => $user->id_sucursal
                ]))['valor'];

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

                $data['total'] = (Cotizacion::ventaCotizacion([
                    'monedaProducto' => $moneda, 
                    'monedaSistema' => (int)$dato['moneda'], 
                    'precio' => Common::quitar_coma($total, 2), 
                    'decSistema' => 2, 
                    'venta' => $codigo, 
                    "id_sucursal" => $user->id_sucursal
                ]))['valor'];

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
    }

    public static function asignarNotaCreditoCreditoCliente($data){

        try {

            /*  --------------------------------------------------------------------------------- */

            // INICIAR TRANSACCION 

            DB::connection('retail')->beginTransaction();

            /*  --------------------------------------------------------------------------------- */

            // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

            $user = auth()->user();

            /*  --------------------------------------------------------------------------------- */

            $data = $data["datos"];
            $fecha = date('Y-m-d');
            $hora = date('H:i:s');
            $total_saldo = 0;

            /*  --------------------------------------------------------------------------------- */

            // OBTENER ID CLIENTE 

            $id_cliente = (Cliente::id_cliente($data['cliente']))['ID_CLIENTE'];

            /*  --------------------------------------------------------------------------------- */

            // CALCULO DE PAGO PRO VENTA 

            $creditos = VentaCredito::obtener_creditos_cliente($data["cliente"]);
                
            if ($creditos["response"] === false) {
                return $creditos;
            } else {
                $creditos = $creditos["creditos"];
            }

            /*  --------------------------------------------------------------------------------- */

            foreach ($creditos as $key => $value) {
                $total_saldo = $value->SALDO + $total_saldo;
            }

            /*  --------------------------------------------------------------------------------- */

            if ($data['total'] > $total_saldo) {
                return ["response" => false, "statusText" => "¡La nota de crédito supera el saldo!"];
            }

            /*  --------------------------------------------------------------------------------- */

            $nota_credito = NotaCredito::Obtener_Nota_Credito_Con_id_venta($data['id_nota_credito']);

            if ($nota_credito["response"] === false) {
                return $nota_credito;
            } else {
                $nota_credito = $nota_credito["nota"][0];
            }

            // RECORRER VENTAS CON CREDITO 

            $saldo = 0;
            $saldo_restado = 0;
            $total_nota_credito = 0;
            $total_nota_credito_restado = 0;

            /*  --------------------------------------------------------------------------------- */   
            //COTIZAR EL TOTAL DE NOTA DE CREDITO EN LA MONEDA SELECCIONADA.

            $total_nota_credito = COTIZACION::CALMONED([
                'monedaProducto' => (int)$data["moneda"], 
                'monedaSistema' => (int)$data["moneda_aplicar"], 
                'precio' => $data['total'], 
                'decSistema' => $data["candec"], 
                "id_sucursal" => $user->id_sucursal,
                "FK_VENTA"=> $nota_credito["FK_VENTA"]
            ]);

            if($data["moneda_aplicar"]<>$data["moneda"]){
                
                if(!$total_nota_credito["response"]){
                    return $total_nota_credito;
                }else{
                   $total_nota_credito = Common::quitar_coma($total_nota_credito["valor"],$data["candec"]);
                }

            } else{
                $total_nota_credito = $data['total'];
            }       

            foreach ($creditos as $key => $value) {

                if($data["moneda_aplicar"] <> $data["moneda"]){

                    /*  --------------------------------------------------------------------------------- */
                    //COTIZAR EL SALDO DE CREDITO EN LA MONEDA SELECCIONADA.

                    $saldo = COTIZACION::CALMONED([
                        'monedaProducto' => (int)$data["moneda"], 
                        'monedaSistema' => (int)$data["moneda_aplicar"], 
                        'precio' => $value->SALDO, 
                        'decSistema' => $data["candec"], 
                        "id_sucursal" => $user->id_sucursal,
                        "FK_VENTA"=>$value->FK_VENTA]
                    );
                       
                    if(!$saldo["response"]){
                        return $saldo;
                    }else{
                         
                        $saldo=Common::quitar_coma($saldo["valor"],$data["candec"]);
                    }

                    /*  --------------------------------------------------------------------------------- */

                }else{

                    $saldo = $value->SALDO;
                }
                    
                if ($saldo >= $total_nota_credito && $total_nota_credito !== 0) {

                    /*  --------------------------------------------------------------------------------- */

                    // INSERTAR VENTAS DET CREDITO 

                    VentasCreditoTieneNotaCredito::guardar_referencia([
                        'FK_VENTA' => $value->FK_VENTA,
                        'FK_NOTA_CREDITO' => $data['id_nota_credito'],
                        'MONTO' => $total_nota_credito,
                        'MONEDA' => $data["moneda_aplicar"]
                    ]);

                    /*  --------------------------------------------------------------------------------- */

                    // ACTUALIZAR VENTA CREDITO 

                    if($data["moneda_aplicar"] <> $data["moneda"]){

                        //ACTUALIZAR EL TOTAL NOTA CREDITO CON LA COTIZACION DE LA VENTA A APLICAR

                        $total_nota_credito_restado = COTIZACION::CALMONED([
                            'monedaProducto' => (int)$data["moneda_aplicar"], 
                            'monedaSistema' => (int)$data["moneda"],
                            'precio' => $total_nota_credito, 
                            'decSistema' => $data["candec"], 
                            "id_sucursal" => $user->id_sucursal,
                            "FK_VENTA"=>$value->FK_VENTA]
                        );

                        if(!$total_nota_credito_restado["response"]){
                            return $total_nota_credito_restado;
                        }else{
                            $total_nota_credito_restado = Common::quitar_coma($total_nota_credito_restado["valor"],$data["candec"]);
                        }

                    }else{

                        $total_nota_credito_restado= $total_nota_credito;
                    }
                   
                    VentaCredito::where('FK_VENTA', '=', $value->FK_VENTA)
                    ->update([
                        'PAGO' => DB::raw('PAGO + '.$total_nota_credito_restado.''),
                        'SALDO' => ($value->SALDO - $total_nota_credito_restado)
                    ]);

                    /*  --------------------------------------------------------------------------------- */

                    // FIJAR CERO EFECTIVO 

                    $total_nota_credito = 0;

                    /*  --------------------------------------------------------------------------------- */

                } else if($saldo > 0  && $total_nota_credito !== 0) {

                    /*  --------------------------------------------------------------------------------- */

                    // ACTUALIZAR VENTA CREDITO 

                    VentaCredito::where('FK_VENTA', '=', $value->FK_VENTA)
                    ->update([
                        'PAGO' => DB::raw('PAGO + '.$value->SALDO.''),
                        'SALDO' => 0,
                        'FECHA_CANCELACION' => $fecha
                    ]);

                    /*  --------------------------------------------------------------------------------- */

                    // GUARDAR REFERENCIA

                    VentasCreditoTieneNotaCredito::guardar_referencia([
                        'FK_VENTA' => $value->FK_VENTA,
                        'FK_NOTA_CREDITO' => $data['id_nota_credito'],
                        'MONTO' => $saldo,
                        'MONEDA' => $data["moneda_aplicar"]
                    ]);

                    /*  --------------------------------------------------------------------------------- */
                    
                    // FIJAR CERO EFECTIVO 

                    $total_nota_credito = $total_nota_credito - $saldo;
                }
            }

            /*  --------------------------------------------------------------------------------- */

            // CAMBIAR ESTATUS NOTA DE CREDITO 

            NotaCredito::where('ID', '=', $data['id_nota_credito']) 
            ->update([ 
                'PROCESADO' => 1,
                'FECMODIF' => $fecha,
                'HORMODIF' => $hora
            ]);

            /*  --------------------------------------------------------------------------------- */

            // ACTUALIZAR CREDITO CLIENTE 
                
            Cliente::actualizarCredito($id_cliente);

            /*  --------------------------------------------------------------------------------- */

            // ENVIAR TRANSACCION A BD

            DB::connection('retail')->commit();

            /*  --------------------------------------------------------------------------------- */

            // RETORNAR VALOR 

            return ["response" => true, "statusText" => "¡Se ha guardado correctamente la nota de credito!"];

        } catch (Exception $e) {
        
           /*  --------------------------------------------------------------------------------- */

           // NO GUARDAR NINGUN CAMBIO 

           DB::connection('retail')->rollBack();
           throw $e;
        }
    }

    public static function resumen_dia($datos){
        
        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();
        
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES 

        $fecha = date('Y-m-d');
        $hora = date('H:i:s');
        $final = date('Y-m-d', strtotime($datos['data']['final']));

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODAS LAS VENTAS DEL DIA DE HOY

        $ventas = Venta::select('CODIGO')
        ->where('ID_SUCURSAL', '=', $datos['data']["sucursal"])
        ->where('FECHA', '=', $final)
        ->GROUPBY('CAJA')
        ->count();

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI ES TABLA UNICA 

        $tab_unica = Parametro::tab_unica();

        if ($tab_unica === "SI") {
            $tab_unica = true;
        } else {
            $tab_unica = false;
        }

        /*  --------------------------------------------------------------------------------- */

        // NOTA CREDITO VALORES

        $nota_credito_guaranies = 0;
        $nota_credito_dolares = 0;
        $nota_credito_pesos = 0;
        $nota_credito_reales = 0;
        $nota_credito_cheque = 0;
        $nota_credito_transferencia = 0;
        $nota_credito_total = 0;

        // INICIAR MPDF 

        $mpdf = new \Mpdf\Mpdf([
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 25,
            'margin_bottom' => 30,
            'margin_header' => 8,
            'margin_footer' => 10
        ]);

        $mpdf->SetDisplayMode('fullpage');

        /*  --------------------------------------------------------------------------------- */

        // OBTENER EL PRIMER TICKET 

        $parametro = Parametro::select(DB::raw('MONEDA, DESTINO, SUPERVISOR'))
        ->where('ID_SUCURSAL', '=', $datos['data']["sucursal"])
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // CANDEC

        $candec = Parametro::candec($parametro[0]['MONEDA']);

        /*  --------------------------------------------------------------------------------- */

        // SUMAR TODOS LOS VALORES CONTADO POR CAJA

        $cajas = Venta::select(DB::raw('
                ventas.CAJA AS CAJAS,
                SUM(EFECTIVO) AS T_EFECTIVO, 
                SUM(VENTAS_TARJETA.MONTO) AS T_TARJETAS, 
                SUM(VENTAS_VALE.MONTO) AS T_VALES, 
                SUM(VENTAS_GIRO.MONTO) AS T_GIROS, 
                SUM(MONEDA1) AS DOLARES, SUM(MONEDA2) AS REALES, 
                SUM(MONEDA3) AS GUARANIES, SUM(MONEDA4) AS PESOS, 
                SUM(TOTAL) AS T_TOTAL,
                SUM(VENTAS_TRANSFERENCIA.MONTO) AS T_TRANSFERENCIA'))
            ->leftjoin('VENTAS_ANULADO', 'VENTAS.ID', '=', 'VENTAS_ANULADO.FK_VENTA')
            ->leftjoin('VENTAS_TRANSFERENCIA','VENTAS_TRANSFERENCIA.FK_VENTA','=','VENTAS.ID')
            ->leftjoin('VENTAS_TARJETA','VENTAS_TARJETA.FK_VENTA','=','VENTAS.ID')
            ->leftjoin('VENTAS_GIRO','VENTAS_GIRO.FK_VENTA','=','VENTAS.ID')
            ->leftjoin('VENTAS_VALE','VENTAS_VALE.FK_VENTA','=','VENTAS.ID')
        ->where('ID_SUCURSAL', '=', $datos['data']["sucursal"])
        ->where('VENTAS.FECHA', '=', $final)
        ->where('TIPO', '<>', 'CR')
        ->where('VENTAS_ANULADO.ANULADO', '=', 0)
        ->GROUPBY('CAJA')
        ->orderBy('CAJA')
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // SUMAR TODOS LOS VALORES CONTADO GENERAL

        $contado = Venta::select(DB::raw('SUM(TOTAL) AS T_TOTAL'))
            ->leftjoin('VENTAS_ANULADO', 'VENTAS.ID', '=', 'VENTAS_ANULADO.FK_VENTA')
        ->where('ID_SUCURSAL', '=', $datos['data']["sucursal"])
        ->where('VENTAS.FECHA', '=', $final)
        ->where('TIPO', '=', 'CO')
        ->where('VENTAS_ANULADO.ANULADO', '=', 0)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // SUMAR TODOS LOS VALORES CREDITO

        $credito = Venta::select(DB::raw('IFNULL(SUM(TOTAL), 0) AS T_TOTAL'))
            ->leftjoin('VENTAS_ANULADO', 'VENTAS.ID', '=', 'VENTAS_ANULADO.FK_VENTA')
        ->where('ID_SUCURSAL', '=', $datos['data']["sucursal"])
        ->where('VENTAS.FECHA', '=', $final)
        ->where('TIPO', '=', 'CR')
        ->where('VENTAS_ANULADO.ANULADO', '=', 0)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // SUMAR TODOS LOS VALORES PAGO A ENTREGAR

        $pe = Venta::select(DB::raw('IFNULL(SUM(TOTAL), 0) AS T_TOTAL'))
            ->leftjoin('VENTAS_ANULADO', 'VENTAS.ID', '=', 'VENTAS_ANULADO.FK_VENTA')
        ->where('ID_SUCURSAL', '=', $datos['data']["sucursal"])
        ->where('VENTAS.FECHA', '=', $final)
        ->where('TIPO', '=', 'PE')
        ->where('VENTAS_ANULADO.ANULADO', '=', 2)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // TARJETA 
        
        $tarjeta = VentaTarjeta::select(DB::raw('IFNULL(SUM(VENTAS_TARJETA.MONTO), 0) AS TOTAL'))
            ->leftjoin('VENTAS', 'VENTAS.ID', '=', 'VENTAS_TARJETA.FK_VENTA')
        ->where('VENTAS.ID_SUCURSAL', '=', $datos['data']["sucursal"])
        ->where('VENTAS.FECHA', '=', $final)
        ->get(); 

        /*  --------------------------------------------------------------------------------- */

        // TRANSFERENCIA
        
        $transferencia = VentaTransferencia::select(DB::raw('IFNULL(SUM(VENTAS_TRANSFERENCIA.MONTO), 0) AS TOTAL'))
            ->leftjoin('VENTAS', 'VENTAS.ID', '=', 'VENTAS_TRANSFERENCIA.FK_VENTA')
            ->leftjoin('VENTAS_ANULADO', 'VENTAS_TRANSFERENCIA.FK_VENTA', '=', 'VENTAS_ANULADO.FK_VENTA')
        ->where('VENTAS.ID_SUCURSAL', '=', $datos['data']["sucursal"])
        ->where('VENTAS.FECHA', '=', $final)
        ->where('VENTAS_ANULADO.ANULADO', '=', 0)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // GIRO
        
        $giro = VentaGiro::select(DB::raw('IFNULL(SUM(VENTAS_GIRO.MONTO), 0) AS TOTAL'))
            ->leftjoin('VENTAS', 'VENTAS.ID', '=', 'VENTAS_GIRO.FK_VENTA')
            ->leftjoin('VENTAS_ANULADO', 'VENTAS_GIRO.FK_VENTA', '=', 'VENTAS_ANULADO.FK_VENTA')
        ->where('VENTAS.ID_SUCURSAL', '=', $datos['data']["sucursal"])
        ->where('VENTAS.FECHA', '=', $final)
        ->where('VENTAS_ANULADO.ANULADO', '=', 0)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // VALE
        
        $vale = VentaVale::select(DB::raw('IFNULL(SUM(VENTAS_VALE.MONTO), 0) AS TOTAL'))
            ->leftjoin('VENTAS', 'VENTAS.ID', '=', 'VENTAS_VALE.FK_VENTA')
            ->leftjoin('VENTAS_ANULADO', 'VENTAS_VALE.FK_VENTA', '=', 'VENTAS_ANULADO.FK_VENTA')
        ->where('VENTAS.ID_SUCURSAL', '=', $datos['data']["sucursal"])
        ->where('VENTAS.FECHA', '=', $final)
        ->where('VENTAS_ANULADO.ANULADO', '=', 0)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // CHEQUE
        
        $cheque = VentaCheque::select(DB::raw('
                VENTAS.CAJA AS CAJAS,
                IFNULL(SUM(VENTAS_CHEQUE.MONTO), 0) AS TOTAL, 
                VENTAS_CHEQUE.MONEDA'))
            ->leftjoin('VENTAS', 'VENTAS.ID', '=', 'VENTAS_CHEQUE.FK_VENTA')
            ->leftjoin('VENTAS_ANULADO', 'VENTAS_CHEQUE.FK_VENTA', '=', 'VENTAS_ANULADO.FK_VENTA')
        ->where('VENTAS.ID_SUCURSAL', '=', $datos['data']["sucursal"])
        ->where('VENTAS.FECHA', '=', $final)
        ->where('VENTAS_ANULADO.ANULADO', '=', 0)
        ->groupBy('VENTAS_CHEQUE.MONEDA','VENTAS.CAJA')
        ->orderBy('VENTAS.CAJA')
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // SUMAR ANULADOS

        $anulados = Venta::select('CODIGO')
            ->leftjoin('VENTAS_ANULADO', 'VENTAS.ID', '=', 'VENTAS_ANULADO.FK_VENTA')
        ->where('VENTAS_ANULADO.ANULADO', '=', 1)
        ->where('VENTAS.FECHA', '=', $final)
        ->where('ID_SUCURSAL', '=', $datos['data']["sucursal"])
        ->count();

        /*  --------------------------------------------------------------------------------- */

        // ************************************ ABONO *****************************************

        /*  --------------------------------------------------------------------------------- */
        
        // SUMAR TODOS LOS VALORES ABONO

        $abono = VentaAbono::select(DB::raw('IFNULL(SUM(PAGO), 0) AS T_TOTAL'))
        ->where('FK_SUCURSAL', '=', $datos['data']["sucursal"])
        ->whereDate('FECHA', '=', $final)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // NOTA DE CREDITO

        $nota_credito = NotaCredito::select(DB::raw('IFNULL(SUM(NOTA_CREDITO.TOTAL), 0) AS MONTO'))
        ->where('NOTA_CREDITO.ID_SUCURSAL', '=', $datos['data']["sucursal"])
        ->whereDate('NOTA_CREDITO.FECMODIF', '=', $final)
        ->where('NOTA_CREDITO.PROCESADO', '=', 1)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // DESCUENTO GENERAL

        $descuento_general = Venta::select(DB::raw('IFNULL(SUM(VENTAS_DESCUENTO.TOTAL),0) AS T_TOTAL'))
            ->leftjoin('VENTAS_ANULADO', 'VENTAS.ID', '=', 'VENTAS_ANULADO.FK_VENTA')
            ->leftjoin('VENTAS_DESCUENTO', 'VENTAS.ID', '=', 'VENTAS_DESCUENTO.FK_VENTAS')
        ->where('ID_SUCURSAL', '=', $datos['data']["sucursal"])
        ->where('VENTAS.FECHA', '=', $final)
        ->where('TIPO', '<>', 'CR')
        ->where('VENTAS_ANULADO.ANULADO', '=', 0)
        ->get();
        
        /*  --------------------------------------------------------------------------------- */

        // RETENCION 30%

        $retencion = Venta::select(DB::raw('IFNULL(SUM(VENTAS_RETENCION.MONTO),0) AS T_TOTAL'))
            ->leftjoin('VENTAS_ANULADO', 'VENTAS.ID', '=', 'VENTAS_ANULADO.FK_VENTA')
            ->leftjoin('VENTAS_RETENCION', 'VENTAS.ID', '=', 'VENTAS_RETENCION.FK_VENTA')
        ->where('ID_SUCURSAL', '=', $datos['data']["sucursal"])
        ->where('VENTAS.FECHA', '=', $final)
        ->where('TIPO', '<>', 'CR')
        ->where('VENTAS_ANULADO.ANULADO', '=', 0)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // SALIDA DE PRODUCTOS %

        $salida_p = DB::connection('retail')->table('SALIDA_PRODUCTOS')
            ->select(DB::raw('IFNULL(SUM(TOTAL),0) AS T_TOTAL'))
        ->where('ID_SUCURSAL', '=', $datos['data']["sucursal"])
        ->whereDate('FECALTAS', '=', $final)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // CUPON

        $cupon = Venta::select(DB::raw('IFNULL(SUM(VENTAS_CUPON.CUPON_IMPORTE),0) AS T_TOTAL'))
        ->leftjoin('VENTAS_ANULADO', 'VENTAS.ID', '=', 'VENTAS_ANULADO.FK_VENTA')
        ->leftjoin('VENTAS_CUPON', 'VENTAS.ID', '=', 'VENTAS_CUPON.FK_VENTA')
        ->where('VENTAS.ID_SUCURSAL', '=', $datos['data']["sucursal"])
        ->where('VENTAS.FECHA', '=', $final)
        ->where('TIPO', '<>', 'CR')
        ->where('VENTAS_ANULADO.ANULADO', '=', 0)
        ->get();

        /*  --------------------------------------------------------------------------------- */
        // DESCUENTO POR PRODUCTO

        $descuento_producto = Venta::select(DB::raw('IFNULL(SUM(ventasdet_descuento.TOTAL),0) AS T_TOTAL'))
            ->leftjoin('VENTAS_ANULADO', 'VENTAS.ID', '=', 'VENTAS_ANULADO.FK_VENTA')
            ->leftjoin('VENTASDET', 'VENTASDET.FK_VENTA', '=', 'VENTAS.ID')
            ->leftjoin('VENTASDET_DESCUENTO', 'VENTASDET_DESCUENTO.FK_VENTASDET', '=', 'VENTASDET.ID')
        ->where('VENTAS.ID_SUCURSAL', '=', $datos['data']["sucursal"])
        ->where('VENTAS.FECHA', '=', $final)
        ->where('TIPO', '<>', 'CR')
        ->where('VENTAS_ANULADO.ANULADO', '=', 0)
        ->get();
      
        $cajas_totales = Venta::select(DB::raw('
                ventas.CAJA AS CAJAS,SUM(EFECTIVO) AS T_EFECTIVO, 
                SUM(VENTAS_TARJETA.MONTO) AS T_TARJETAS, 
                SUM(VENTAS_VALE.MONTO) AS T_VALES, SUM(VENTAS_GIRO.MONTO) AS T_GIROS, 
                SUM(MONEDA1) AS DOLARES, SUM(MONEDA2) AS REALES, 
                SUM(MONEDA3) AS GUARANIES, SUM(MONEDA4) AS PESOS, 
                SUM(TOTAL) AS T_TOTAL,
                SUM(VENTAS_TRANSFERENCIA.MONTO) AS T_TRANSFERENCIA'))
            ->leftjoin('VENTAS_ANULADO', 'VENTAS.ID', '=', 'VENTAS_ANULADO.FK_VENTA')
            ->leftjoin('VENTAS_TRANSFERENCIA','VENTAS_TRANSFERENCIA.FK_VENTA','=','VENTAS.ID')
            ->leftjoin('VENTAS_TARJETA','VENTAS_TARJETA.FK_VENTA','=','VENTAS.ID')
            ->leftjoin('VENTAS_GIRO','VENTAS_GIRO.FK_VENTA','=','VENTAS.ID')
            ->leftjoin('VENTAS_VALE','VENTAS_VALE.FK_VENTA','=','VENTAS.ID')
        ->where('ID_SUCURSAL', '=', $datos['data']["sucursal"])
        ->where('VENTAS.FECHA', '=', $final)
        ->where('TIPO', '<>', 'CR')
        ->where('VENTAS_ANULADO.ANULADO', '=', 0)
        ->get();

        $ventas_cheque = VentaCheque::select(DB::raw('IFNULL(SUM(VENTAS_CHEQUE.MONTO), 0) AS TOTAL, VENTAS_CHEQUE.MONEDA'))
            ->leftjoin('VENTAS', 'VENTAS.ID', '=', 'VENTAS_CHEQUE.FK_VENTA')
            ->leftjoin('VENTAS_ANULADO', 'VENTAS_CHEQUE.FK_VENTA', '=', 'VENTAS_ANULADO.FK_VENTA')
        ->where('VENTAS.ID_SUCURSAL', '=', $datos['data']["sucursal"])
        ->where('VENTAS.FECHA', '=', $final)
        ->where('VENTAS_ANULADO.ANULADO', '=', 0)
        ->groupBy('VENTAS_CHEQUE.MONEDA')
        ->orderBy('VENTAS.CAJA')
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // MOVIMIENTO CAJA 

        $movimiento_caja_entrada_total = Movimiento_Caja::select(DB::raw('
            IFNULL(SUM(MovimientoS_CajaS.IMPORTE_SISTEMA), 0) AS TOTAL_SISTEMA, 
            MovimientoS_CajaS.MONEDA_SISTEMA'))
        ->where('MOVIMIENTOS_CAJAS.ID_SUCURSAL', '=', $datos['data']["sucursal"])
        ->whereDate('MOVIMIENTOS_CAJAS.FECALTAS', '=', $final)
        ->where('MOVIMIENTOS_CAJAS.MOVIMIENTO','=', 1)
        ->get();

        $movimiento_caja_salida_total = Movimiento_Caja::select(DB::raw('
            IFNULL(SUM(MovimientoS_CajaS.IMPORTE_SISTEMA), 0) AS TOTAL_SISTEMA, 
            MovimientoS_CajaS.MONEDA_SISTEMA'))
        ->where('MOVIMIENTOS_CAJAS.ID_SUCURSAL', '=', $datos['data']["sucursal"])
        ->where('MOVIMIENTOS_CAJAS.MOVIMIENTO','=',2)
        ->whereDate('MOVIMIENTOS_CAJAS.FECALTAS', '=', $final)
        ->get();

        $movimiento_caja_entrada = Movimiento_Caja::select(DB::raw('
            IFNULL(SUM(MovimientoS_CajaS.IMPORTE), 0) AS TOTAL, 
            MovimientoS_CajaS.MONEDA,
            MOVIMIENTOS_CAJAS.CAJA AS CAJAS'))
        ->where('MOVIMIENTOS_CAJAS.ID_SUCURSAL', '=', $datos['data']["sucursal"])
        ->whereDate('MOVIMIENTOS_CAJAS.FECALTAS', '=', $final)
        ->where('MOVIMIENTOS_CAJAS.MOVIMIENTO','=',1)
        ->groupBy('MOVIMIENTOS_CAJAS.MONEDA','MOVIMIENTOS_CAJAS.CAJA')
        ->orderBy('MOVIMIENTOS_CAJAS.CAJA','ASC')
        ->get();
        
        $movimiento_caja_salida = Movimiento_Caja::select(DB::raw('
            IFNULL(SUM(MovimientoS_CajaS.IMPORTE), 0) AS TOTAL, 
            MovimientoS_CajaS.MONEDA,
            MOVIMIENTOS_CAJAS.CAJA AS CAJAS'))
        ->where('MOVIMIENTOS_CAJAS.ID_SUCURSAL', '=', $datos['data']["sucursal"])
        ->where('MOVIMIENTOS_CAJAS.MOVIMIENTO','=',2)
        ->whereDate('MOVIMIENTOS_CAJAS.FECALTAS', '=', $final)
        ->groupBy('MOVIMIENTOS_CAJAS.MONEDA','MOVIMIENTOS_CAJAS.CAJA')
        ->orderBy('MOVIMIENTOS_CAJAS.CAJA','ASC')
        ->get();

         $movimiento_caja_entrada_t = Movimiento_Caja::select(DB::raw('
            IFNULL(SUM(MovimientoS_CajaS.IMPORTE), 0) AS TOTAL, 
            MovimientoS_CajaS.MONEDA,
            MOVIMIENTOS_CAJAS.CAJA AS CAJAS'))
        ->where('MOVIMIENTOS_CAJAS.ID_SUCURSAL', '=', $datos['data']["sucursal"])
        ->whereDate('MOVIMIENTOS_CAJAS.FECALTAS', '=', $final)
        ->where('MOVIMIENTOS_CAJAS.MOVIMIENTO','=',1)
        ->groupBy('MOVIMIENTOS_CAJAS.MONEDA')
        ->orderBy('MOVIMIENTOS_CAJAS.CAJA','ASC')
        ->get();

        $movimiento_caja_salida_t = Movimiento_Caja::select(DB::raw('
            IFNULL(SUM(MovimientoS_CajaS.IMPORTE), 0) AS TOTAL, 
            MovimientoS_CajaS.MONEDA,
            MOVIMIENTOS_CAJAS.CAJA AS CAJAS'))
        ->where('MOVIMIENTOS_CAJAS.ID_SUCURSAL', '=', $datos['data']["sucursal"])
        ->where('MOVIMIENTOS_CAJAS.MOVIMIENTO','=',2)
        ->whereDate('MOVIMIENTOS_CAJAS.FECALTAS', '=', $final)
        ->groupBy('MOVIMIENTOS_CAJAS.MONEDA')
        ->orderBy('MOVIMIENTOS_CAJAS.CAJA','ASC')
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // AUTORIZACIONES 

        $autorizacion = VentasTieneAutorizacion::leftjoin('VENTAS', 'VENTAS.ID', '=', 'VENTAS_TIENE_AUTORIZACION.FK_VENTA')
            ->leftjoin('VENTAS_ANULADO', 'VENTAS.ID', '=', 'VENTAS_ANULADO.FK_VENTA')
        ->where('VENTAS.ID_SUCURSAL', '=' , $datos['data']["sucursal"])
        ->where('VENTAS.FECALTAS', '=', $final)
        ->where('VENTAS_ANULADO.ANULADO', '<>', 1)
        ->count();

        /*  --------------------------------------------------------------------------------- */

        // COMPRAS 

        $compras = Compra::select(DB::raw('IFNULL(SUM(COMPRAS.TOTAL), 0) AS TOTAL, COMPRAS.MONEDA'))
        ->where('COMPRAS.ID_SUCURSAL', '=', $datos['data']["sucursal"])
        ->whereDate('COMPRAS.FECALTAS', '=', $final)
        ->groupBy('COMPRAS.MONEDA')
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // PAGO A PROVEEDOR 

        $pago_proveedor = Pagos_Prov::select(DB::raw('IFNULL(SUM(PAGOS_PROV.PAGO), 0) AS TOTAL'))
        ->where('ID_SUCURSAL', '=', $datos['data']["sucursal"])
        ->whereDate('FECALTAS', '=', $final)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        $anulados_total = Venta::select(DB::raw('IFNULL(SUM(TOTAL),0) AS T_TOTAL'))
            ->leftjoin('VENTAS_ANULADO', 'VENTAS.ID', '=', 'VENTAS_ANULADO.FK_VENTA')
        ->where('ID_SUCURSAL', '=', $datos['data']["sucursal"])
        ->where('VENTAS.FECHA', '=', $final)
        ->where('VENTAS_ANULADO.ANULADO', '=', 1)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        //ARMAR ARRAY DE MOVIMIENTO ENTRADA

        $caja_aux = 0;
        $c_rows_entrada = 0;
        $c_rows_salida = 0;   
        $c_rows = 0;
        $guaranies_e = Common::precio_candec(0, 1);
        $dolares_e = Common::precio_candec(0, 2);
        $pesos_e = Common::precio_candec(0, 3);
        $reales_e = Common::precio_candec(0, 4);
        $entrada = array();
        $entrada2 = array();
        $salida = array();
        $salida2 = array();
        $efectivo = array();
        $medios = array();
        $cheques_c = array();
        $entrada_prueba = array();
        $compras_total = 0; 

        foreach ($movimiento_caja_entrada as $key => $value) {
          
            if($c_rows_entrada == 0 && $caja_aux == 0){
    
                $entrada2[$c_rows_entrada]['GUARANIES'] = 0;
                $entrada2[$c_rows_entrada]['DOLARES'] = 0;
                $entrada2[$c_rows_entrada]['PESOS'] = 0;
                $entrada2[$c_rows_entrada]['REALES'] = 0;
                $caja_aux = $value["CAJAS"];
            }

            if($caja_aux != $value["CAJAS"]){
    
                $c_rows_entrada = $c_rows_entrada+1;
                $caja_aux = $value["CAJAS"];
                $guaranies_e = Common::precio_candec(0, 1);
                $dolares_e = Common::precio_candec(0, 2);
                $pesos_e = Common::precio_candec(0, 3);
                $reales_e = Common::precio_candec(0, 4);
                $entrada2[$c_rows_entrada]['GUARANIES'] = 0;
                $entrada2[$c_rows_entrada]['DOLARES'] = 0;
                $entrada2[$c_rows_entrada]['PESOS'] = 0;
                $entrada2[$c_rows_entrada]['REALES'] = 0;
            }
    
            if($caja_aux == $value["CAJAS"]){
           
                $entrada[$c_rows_entrada]['CAJA'] = $value["CAJAS"];
                $entrada2[$c_rows_entrada]['CAJA'] = $value["CAJAS"];

                if($value["MONEDA"] == 1){

                    $guaranies_e = Common::precio_candec($value["TOTAL"], 1);
                    $entrada2[$c_rows_entrada]['GUARANIES'] = $value["TOTAL"];
                }

                if($value["MONEDA"] == 2){
                    $dolares_e = Common::precio_candec($value["TOTAL"], 2);
                    $entrada2[$c_rows_entrada]['DOLARES'] = $value["TOTAL"];
                }

                if($value["MONEDA"] == 3){
                    $pesos_e = Common::precio_candec($value["TOTAL"], 3);
                    $entrada2[$c_rows_entrada]['PESOS'] = $value["TOTAL"];
                }

                if($value["MONEDA"] == 4){
                    $reales_e = Common::precio_candec($value["TOTAL"], 4);
                    $entrada2[$c_rows_entrada]['REALES'] = $value["TOTAL"];
                }

                $entrada[$c_rows_entrada]['GUARANIES'] = $guaranies_e;
                $entrada[$c_rows_entrada]['DOLARES'] = $dolares_e;
                $entrada[$c_rows_entrada]['PESOS'] = $pesos_e;
                $entrada[$c_rows_entrada]['REALES'] = $reales_e;
            }
        }

        //FIN DE MOVIMIENTO ENTRADA

        /*  --------------------------------------------------------------------------------- */

        //ARMAR ARRAY DE MOVIMIENTO SALIDA

        $guaranies_s = Common::precio_candec(0, 1);
        $dolares_s = Common::precio_candec(0, 2);
        $pesos_s = Common::precio_candec(0, 3);
        $reales_s = Common::precio_candec(0, 4);
       
        $caja_aux = 0;

        foreach ($movimiento_caja_salida as $key => $value) {

            if($c_rows_salida == 0 && $caja_aux == 0){

                $caja_aux = $value["CAJAS"];
                $salida2[$c_rows_salida]['GUARANIES'] = 0;
                $salida2[$c_rows_salida]['DOLARES'] = 0;
                $salida2[$c_rows_salida]['PESOS'] = 0;
                $salida2[$c_rows_salida]['REALES'] = 0;
            }

            if($caja_aux != $value["CAJAS"]){

                $c_rows_salida = $c_rows_salida + 1;
                $caja_aux = $value["CAJAS"];
                $guaranies_s = Common::precio_candec(0, 1);
                $dolares_s = Common::precio_candec(0, 2);
                $pesos_s = Common::precio_candec(0, 3);
                $reales_s = Common::precio_candec(0, 4);
                $salida2[$c_rows_salida]['GUARANIES'] = 0;
                $salida2[$c_rows_salida]['DOLARES'] = 0;
                $salida2[$c_rows_salida]['PESOS'] = 0;
                $salida2[$c_rows_salida]['REALES'] = 0;
            }

            if($caja_aux == $value["CAJAS"]){

                $salida[$c_rows_salida]['CAJAS'] = $value["CAJAS"] ;
                $salida2[$c_rows_salida]['CAJAS'] = $value["CAJAS"] ;

                if($value["MONEDA"] == 1){
                    $guaranies_s = Common::precio_candec($value["TOTAL"], 1);
                    $salida2[$c_rows_salida]['GUARANIES'] = $value["TOTAL"];
                }

                if($value["MONEDA"] == 2){
                    $dolares_s = Common::precio_candec($value["TOTAL"], 2);
                    $salida2[$c_rows_salida]['DOLARES'] = $value["TOTAL"];
                }

                if($value["MONEDA"] == 3){
                    $pesos_s = Common::precio_candec($value["TOTAL"], 3);
                    $salida2[$c_rows_salida]['PESOS'] = $value["TOTAL"];
                }

                if($value["MONEDA"] == 4){
                    $reales_s = Common::precio_candec($value["TOTAL"], 4);
                    $salida2[$c_rows_salida]['REALES'] = $value["TOTAL"];
                }

                $salida[$c_rows_salida]['GUARANIES'] = $guaranies_s;
                $salida[$c_rows_salida]['DOLARES'] = $dolares_s;
                $salida[$c_rows_salida]['PESOS'] = $pesos_s;
                $salida[$c_rows_salida]['REALES'] = $reales_s;
            }
        }

        //FIN DE MOVIMIENTO SALIDA

        /*  --------------------------------------------------------------------------------- */

        //MOVIMIENTO ENTRADA TOTALES

        $guaranies_e = 0;
        $dolares_e = 0;
        $pesos_e = 0;
        $reales_e = 0;

        foreach ($movimiento_caja_entrada_t as $key => $value) {

            if($value["MONEDA"] == 1){
                $guaranies_e = $value["TOTAL"];
            }
            if($value["MONEDA"] == 2){
                $dolares_e = $value["TOTAL"];
            }
            if($value["MONEDA"] == 3){
                $pesos_e = $value["TOTAL"];
            }
            if($value["MONEDA"] == 4){
                $reales_e = $value["TOTAL"];
            }
        }


        //FIN MOVIMIENTO ENTRADA TOTALES

        /*  --------------------------------------------------------------------------------- */

        //MOVIMIENTO SALIDA TOTALES

        $guaranies_s = 0;
        $dolares_s = 0;
        $pesos_s = 0;
        $reales_s = 0;

        foreach ($movimiento_caja_salida_t as $key => $value) {

            if($value["MONEDA"] == 1){
                $guaranies_s = $value["TOTAL"];
            }
            if($value["MONEDA"] == 2){
                $dolares_s = $value["TOTAL"];
            }
            if($value["MONEDA"] == 3){
                $pesos_s = $value["TOTAL"];
            }
            if($value["MONEDA"] == 4){
                $reales_s = $value["TOTAL"];
            }
        }

        //FIN MOVIMIENTO SALIDA TOTALES

        /*  --------------------------------------------------------------------------------- */

        //ARMAR ARRAY DE EFECTIVO Y MEDIOS
        
        foreach ($cajas as $key => $value) {

            foreach ($entrada2 as $key => $entrada_m) {

                if($entrada_m["CAJA"] == $value["CAJAS"]){
                    $value["DOLARES"] = $value["DOLARES"] + $entrada_m["DOLARES"];
                    $value["GUARANIES"] = $value["GUARANIES"] + $entrada_m["GUARANIES"];
                    $value["REALES"] = $value["REALES"] + $entrada_m["REALES"];
                    $value["PESOS"] = $value["PESOS"] + $entrada_m["PESOS"];
                }
            }

            foreach ($salida2 as $key => $salida_m) {

                if($salida_m["CAJAS"] == $value["CAJAS"]){

                    $value["DOLARES"] = $value["DOLARES"] - $salida_m["DOLARES"];
                    $value["GUARANIES"] = $value["GUARANIES"] - $salida_m["GUARANIES"];
                    $value["REALES"] = $value["REALES"] - $salida_m["REALES"];
                    $value["PESOS"] = $value["PESOS"] - $salida_m["PESOS"];
                }
            }

            $efectivo[$c_rows]['CAJA'] = $value["CAJAS"];
            $efectivo[$c_rows]['DOLARES'] = Common::precio_candec($value["DOLARES"], $candec);
            $efectivo[$c_rows]['REALES'] = Common::precio_candec($value["REALES"], 4);
            $efectivo[$c_rows]['GUARANIES'] = Common::precio_candec($value["GUARANIES"], 1);
            $efectivo[$c_rows]['PESOS'] = Common::precio_candec($value["PESOS"], 3);
            $medios[$c_rows]['CAJAS'] = $value["CAJAS"];
            $medios[$c_rows]['TARJETAS'] = Common::precio_candec($value["T_TARJETAS"], 1);
            $medios[$c_rows]['VALES'] = Common::precio_candec($value["T_VALES"], $candec);
            $medios[$c_rows]['TRANSFERENCIAS'] = Common::precio_candec($value["T_TRANSFERENCIA"], 1);
            $medios[$c_rows]['GIROS'] = Common::precio_candec($value["T_GIROS"], 1);
            $c_rows = $c_rows + 1;
        }

        //FIN DE EFECTIVO Y MEDIOS

        /*  --------------------------------------------------------------------------------- */

        //ARMAR ARRAY DE CHEQUE

        $c_rows_cheque = 0;
        $caja_aux = 0;
        $guaranies_c = Common::precio_candec(0, 1);
        $dolares_c = Common::precio_candec(0, 2);
        $pesos_c = Common::precio_candec(0, 3);
        $reales_c = Common::precio_candec(0, 4);

        foreach ($cheque as $key => $value) {

            if($c_rows_cheque == 0 && $caja_aux == 0){
                $caja_aux = $value["CAJAS"];
            }

            if($caja_aux != $value["CAJAS"]){

                $c_rows_cheque = $c_rows_cheque + 1;
                $caja_aux = $value["CAJAS"];
                $guaranies_c = Common::precio_candec(0, 1);
                $dolares_c = Common::precio_candec(0, 2);
                $pesos_c = Common::precio_candec(0, 3);
                $reales_c = Common::precio_candec(0, 4);
            }

            if($caja_aux == $value["CAJAS"]){

                $cheques_c[$c_rows_cheque]['CAJA'] = $value["CAJAS"] ;

                if($value["MONEDA"] == 1){
                    $guaranies_c = Common::precio_candec($value["TOTAL"],1);
                }

                if($value["MONEDA"] == 2){
                    $dolares_c = Common::precio_candec($value["TOTAL"], 2);
                }

                if($value["MONEDA"] == 3){
                    $pesos_c = Common::precio_candec($value["TOTAL"], 3);
                }

                if($value["MONEDA"] == 4){
                    $reales_c = Common::precio_candec($value["TOTAL"], 4);
                }

                $cheques_c[$c_rows_cheque]['GUARANIES'] = $guaranies_c;
                $cheques_c[$c_rows_cheque]['DOLARES'] = $dolares_c;
                $cheques_c[$c_rows_cheque]['PESOS'] = $pesos_c;
                $cheques_c[$c_rows_cheque]['REALES'] = $reales_c;
            }
        }

        //FIN ARRAY CHEQUE
        
        /*  --------------------------------------------------------------------------------- */
        
        //ARMAR ARRAY DE CHEQUE TOTALES

        $guaranies_c = Common::precio_candec(0, 1);
        $dolares_c = Common::precio_candec(0, 2);
        $pesos_c = Common::precio_candec(0, 3);
        $reales_c = Common::precio_candec(0, 4);

        foreach ($ventas_cheque as $key => $value) {

            if($value["MONEDA"] == 1){
                $guaranies_c = Common::precio_candec($value["TOTAL"], 1);
            }

            if($value["MONEDA"] == 2){
                $dolares_c = Common::precio_candec($value["TOTAL"], 2);
            }

            if($value["MONEDA"] == 3){
                $pesos_c = Common::precio_candec($value["TOTAL"], 3);
            }

            if($value["MONEDA"] == 4){
                $reales_c = Common::precio_candec($value["TOTAL"], 4);
            }
        }

        //FIN ARRAY DE CHEQUE TOTALES

        /*  --------------------------------------------------------------------------------- */

        // PREPARAR COMPRAS, EL TOTAL POR LA MONEDA DEL SISTEMA

        foreach ($compras as $key => $value) {

            if ($value->MONEDA == $parametro[0]['MONEDA']) {
                $compras_total = $compras_total + $value->TOTAL;

            } else {

                $total_sistema_compra = (Cotizacion::CALMONED([
                    'monedaProducto' => (int)$parametro[0]['MONEDA'], 
                    'monedaSistema' => (int)$value->MONEDA, 
                    'precio' => $value->TOTAL, 
                    'decSistema' => $candec, 
                    'tab_unica' => $tab_unica, 
                    "id_sucursal" => $user->id_sucursal
                ]));

                if ($total_sistema_compra['response'] == true) {
                    $compras_total = $compras_total + $total_sistema_compra['valor'];
                } 
            }
        }

        /*  --------------------------------------------------------------------------------- */

        $data['efectivo'] = $efectivo;
        $data['medios'] = $medios;
        $data['cheques'] = $cheques_c;
        $data['contado'] = Common::precio_candec($contado[0]["T_TOTAL"], (int)$parametro[0]['MONEDA']);
        $data['creditoV'] = Common::precio_candec($credito[0]["T_TOTAL"], (int)$parametro[0]['MONEDA']);
        $data['pago'] = Common::precio_candec($pe[0]["T_TOTAL"], (int)$parametro[0]['MONEDA']);
        $data['anulado'] = $anulados;
        $data['descuentoP'] = Common::precio_candec($descuento_producto[0]["T_TOTAL"],(int)$parametro[0]['MONEDA']);
        $data['retencion'] = Common::precio_candec($retencion[0]["T_TOTAL"], (int)$parametro[0]['MONEDA']);
        $data['credito'] = Common::precio_candec($nota_credito[0]["T_TOTAL"], (int)$parametro[0]['MONEDA']);   
        $data['descuentoG'] = Common::precio_candec($descuento_general[0]["T_TOTAL"],(int)$parametro[0]['MONEDA']);
        $data['salida'] = Common::precio_candec($salida_p[0]->T_TOTAL, (int)$parametro[0]['MONEDA']);
        $data['cupon'] = Common::precio_candec($cupon[0]->T_TOTAL, (int)$parametro[0]['MONEDA']);
        $data['abono'] = Common::precio_candec($abono[0]["T_TOTAL"], (int)$parametro[0]['MONEDA']);
        $data['movimientoCajaEntrada'] = Common::precio_candec($movimiento_caja_entrada_total[0]->TOTAL_SISTEMA, (int)$parametro[0]['MONEDA']);
        $data['movimientoCajaSalida'] = Common::precio_candec($movimiento_caja_salida_total[0]->TOTAL_SISTEMA, (int)$parametro[0]['MONEDA']);
        $data['totalV'] = ($abono[0]->T_TOTAL + $contado[0]->T_TOTAL + $credito[0]->T_TOTAL + $pe[0]->T_TOTAL + $movimiento_caja_entrada_total[0]->TOTAL_SISTEMA) - $movimiento_caja_salida_total[0]->TOTAL_SISTEMA;
        $data['totalV'] = Common::precio_candec($data['totalV'],(int)$parametro[0]['MONEDA']);
        $data['total'] = $abono[0]->T_TOTAL + $contado[0]->T_TOTAL + $credito[0]->T_TOTAL + $pe[0]->T_TOTAL - $salida_p[0]->T_TOTAL;
        $data['total'] = Common::precio_candec($data['total'],(int)$parametro[0]['MONEDA']);
        $data['totalGes'] = Common::precio_candec($cajas_totales[0]['GUARANIES'],1);
        $data['totalDles'] = Common::precio_candec($cajas_totales[0]['DOLARES'], (int)$parametro[0]['MONEDA']);
        $data['totalRles'] = Common::precio_candec($cajas_totales[0]['REALES'],4);
        $data['totalPes'] = Common::precio_candec($cajas_totales[0]['PESOS'],3);
        $data['totalTjs'] = Common::precio_candec($cajas_totales[0]['T_TARJETAS'],1);
        $data['totalVls'] = Common::precio_candec($cajas_totales[0]['T_VALES'], (int)$parametro[0]['MONEDA']);
        $data['totalTrs'] = Common::precio_candec($cajas_totales[0]['T_TRANSFERENCIA'],1);
        $data['totalGrs'] = Common::precio_candec($cajas_totales[0]['T_GIROS'],1);
        $data['totalGs'] = $guaranies_c;
        $data['totalDls'] = $dolares_c;
        $data['totalRls'] = $reales_c;
        $data['totalPs'] = $pesos_c;
        $data['entrada'] = $entrada;
        $data['salidaC'] = $salida;
        $data['entradaTotalGs'] = Common::precio_candec($guaranies_e,1);
        $data['entradaTotalDls'] = Common::precio_candec($dolares_e,2);
        $data['entradaTotalRls'] = Common::precio_candec($reales_e,4);
        $data['entradaTotalPs'] = Common::precio_candec($pesos_e,3);

        $data['salidaTotalGs'] = Common::precio_candec($guaranies_s,1);
        $data['salidaTotalDls'] = Common::precio_candec($dolares_s,2);
        $data['salidaTotalRls'] = Common::precio_candec($reales_s,4);
        $data['salidaTotalPs'] = Common::precio_candec($pesos_s,3);

        $data['sucursal'] = $datos["data"]["sucursal"];
        $data['fecha'] = $final;
        $data['c_rows'] = $c_rows;
        $data['c_rows_cheque'] = $c_rows_cheque;
        $data['c_rows_salida'] = $c_rows_salida;
        $data['c_rows_entrada'] = $c_rows_entrada;
        $namefile = 'ReporteDiario'.time().'.pdf';
        $data['logo'] = 0;
        $data['IMAGEN'] = '';
        $data['autorizaciones'] = $autorizacion;
        
        $data['compraTotal'] = Common::precio_candec($compras_total, (int)$parametro[0]['MONEDA']);

        $data['pagoProveedor'] = Common::precio_candec($pago_proveedor[0]['TOTAL'], (int)$parametro[0]['MONEDA']);

        $data['anulados_total'] = Common::precio_candec($anulados_total[0]['T_TOTAL'], (int)$parametro[0]['MONEDA']);

        /*  --------------------------------------------------------------------------------- */

        // TOTALES ENTRADA 

        $contado_entrada = $contado[0]["T_TOTAL"] + $anulados_total[0]['T_TOTAL'] + $nota_credito[0]["T_TOTAL"] + $movimiento_caja_salida_total[0]->TOTAL_SISTEMA;

        $totales_entrada = $contado[0]["T_TOTAL"] + $movimiento_caja_entrada_total[0]->TOTAL_SISTEMA + $abono[0]["T_TOTAL"];
        $data['totales_entrada'] = Common::precio_candec($totales_entrada, (int)$parametro[0]['MONEDA']);

        /*  --------------------------------------------------------------------------------- */

        // TOTALES SALIDA 

        $totales_salida = $compras_total + $movimiento_caja_salida_total[0]->TOTAL_SISTEMA + $pago_proveedor[0]['TOTAL'] + $nota_credito[0]["T_TOTAL"] + $anulados_total[0]['T_TOTAL'];
        $data['totales_salida'] = Common::precio_candec($totales_salida, (int)$parametro[0]['MONEDA']);

        /*  --------------------------------------------------------------------------------- */

        // PARAMETROS 

        $parametro = Parametro::select(DB::raw('LOGO, NOMBRE_LOGO, COLOR'))
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // CABECERA

        $filename = '../storage/app/public/imagenes/tiendas/'.$parametro[0]->NOMBRE_LOGO.'';
                
        if(file_exists($filename)) {
            $data['logo'] = 1;
        } 

        /*  --------------------------------------------------------------------------------- */

        if ($data['logo'] === 1) {
            $data['IMAGEN'] = $filename;
        } 

        /*  --------------------------------------------------------------------------------- */

        // COLOR 

        $data['color'] = $parametro[0]->COLOR;

        // CREAR HOJA 

        $html = view('pdf.ReporteDiario', $data)->render();

        $mpdf->WriteHTML($html);

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle("ReporteDiario");

        // DESCARGAR ARCHIVO PDF 

        $mpdf->Output();
       /* return ["response"=>true, "efectivo"=>$cajas,"medios"=>$cajas,"contado"=>$contado,"credito"=>$credito,"pe"=>$pe, "descuento_producto"=>$descuento_producto,"retencion"=>$retencion,"nota_credito"=>$nota_credito,"descuento_general"=>$descuento_general,"salida_p"=>$salida_p,"cupon"=>$cupon,"abonos"=>$abono]*/
        /*  --------------------------------------------------------------------------------- */
    }

    public static function rpt_top_articulos($datos){

        $user = auth()->user();

        Venta::insert_top($datos);
       
        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
            0 => 'CODIGO', 
            1 => 'DESCRIPCION', 
            3 => 'CATEGORIA',
            4 => 'VENDIDO',
            5 => 'CANTIDAD',
            6 => 'PRECIO',
            7 => 'TOTAL',
            8 => 'UTILIDAD',
            9 => 'DESCUENTO',
        );
        
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $dia = date("Y-m-d");

        //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

        $posts = DB::connection('retail')->table('temp_ventas')
            ->leftjoin('productos','productos.CODIGO','=','temp_ventas.COD_PROD')
            ->select(DB::raw('
                temp_ventas.COD_PROD as CODIGO,
                PRODUCTOS.DESCRIPCION AS DESCRIPCION,
                SUM(temp_ventas.VENDIDO) AS CANTIDAD,
                IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = temp_ventas.COD_PROD) AND (l.ID_SUCURSAL = temp_ventas.ID_SUCURSAL))),0) AS STOCK,
                (SUM(temp_ventas.PRECIO)/SUM(temp_ventas.VENDIDO)) AS PRECIO,
                SUM(temp_ventas.PRECIO) AS TOTAL,
                (SUM(temp_ventas.COSTO_TOTAL)/SUM(temp_ventas.VENDIDO)) AS COSTO_UNIT,
                SUM(temp_ventas.COSTO_TOTAL),
                SUM(temp_ventas.UTILIDAD) AS UTILIDAD,
                SUM(temp_ventas.DESCUENTO) AS DESCUENTO,
                temp_ventas.CATEGORIA,
                temp_ventas.SUBCATEGORIA,
                temp_ventas.NOMBRE,
                temp_ventas.SECCION,
                temp_ventas.PROVEEDOR_NOMBRE'))
        ->WHERE('temp_ventas.USER_ID','=',$user->id)
        ->WHERE('temp_ventas.ID_SUCURSAL','=',$datos->input("Sucursal"))    
        ->GROUPBY('temp_ventas.COD_PROD'); 

        if($datos->input("filtro") == "SECCION"){

            $posts->where('temp_ventas.SECCION_CODIGO','=', $datos->input("Seccion"));
            
            if($datos->input("AllProveedores") == 'false'){

                $posts->whereIn('temp_ventas.PROVEEDOR',$datos->input("Proveedores"));
            }
        }
        
        if($datos->input("filtro") == "PROVEEDOR"){
            if($datos->input("AllProveedores") == 'false'){
                $posts->whereIn('temp_ventas.PROVEEDOR',$datos->input("Proveedores"));
            }
        }
        
        /*  --------------------------------------------------------------------------------- */

        // ORDENAR 

        if($datos->input("Agrupar") == "false"){
            $posts->orderby('UTILIDAD','DESC');
        }else{
            
            $posts->orderby('CANTIDAD','DESC');
        }
        
        $posts = $posts->limit($datos->input("Top"))->get();
        
        $data = array();
        $c = 1;

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts)){

            foreach ($posts as $post){

                // CARGAR EN LA VARIABLE 

                $nestedData['ITEM'] = $c;
                $nestedData['COD_PROD'] = $post->CODIGO;
                $nestedData['DESCRIPCION'] = ucwords(utf8_encode(utf8_decode(substr($post->DESCRIPCION,0,25))));
                $nestedData['CATEGORIA'] = ucwords(utf8_encode(utf8_decode($post->CATEGORIA)));
                $nestedData['VENDIDO'] = $post->CANTIDAD;
                $nestedData['CANTIDAD'] = $post->STOCK;
                $nestedData['PRECIO'] = round($post->PRECIO,2);
                $nestedData['TOTAL'] = round($post->TOTAL,2);
                $nestedData['UTILIDAD'] = round($post->UTILIDAD,2);
                $nestedData['DESCUENTO'] = round($post->DESCUENTO,2);
                $data[] = $nestedData;
                $c = $c + 1;
            }
        }
        
        /*  --------------------------------------------------------------------------------- */

        // PREPARAR EL ARRAY A ENVIAR 

        $json_data = array(
            "draw"            => intval($datos->input('draw')),  
            "recordsTotal"    => intval($c),  
            "recordsFiltered" => intval($c), 
            "data"            => $data   
        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONVERTIR EN JSON EL ARRAY Y ENVIAR 

       return $json_data; 
    }

    public static function insert_top($datos){

        $inicion = '';
        $finaln = '';
        $inicio = date('Y-m-d', strtotime($datos->input("Inicio")));
        $final  =  date('Y-m-d', strtotime($datos->input("Final")));

        $user = auth()->user();
        
        Temp_venta::where('USER_ID', $user->id)->WHERE('ID_SUCURSAL','=',$datos->input("Sucursal"))->delete();
        
        $reporte = Ventas_det::leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
            ->leftjoin('PRODUCTOS_AUX',function($join){
                $join->on('PRODUCTOS_AUX.CODIGO','=','VENTASDET.COD_PROD')
                 ->on('PRODUCTOS_AUX.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL');
                })
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->leftjoin('VENTASDET_TIENE_LOTES', 'VENTASDET_TIENE_LOTES.ID_VENTAS_DET', '=', 'VENTASDET.ID')
            ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'PRODUCTOS_AUX.PROVEEDOR')
            ->leftjoin('SUBLINEAS', 'SUBLINEAS.CODIGO', '=', 'PRODUCTOS.SUBLINEA')
            ->leftjoin('LOTES', 'LOTES.ID', '=', 'VENTASDET_TIENE_LOTES.ID_LOTE')
            ->leftjoin('SUBLINEA_DET', 'SUBLINEA_DET.CODIGO', '=', 'PRODUCTOS.SUBLINEADET')
            ->leftjoin('VENTASDET_DESCUENTO', 'VENTASDET_DESCUENTO.FK_VENTASDET', '=', 'VENTASDET.ID')
            ->leftjoin('PRODUCTOS_TIENE_SECCION',function($join){
                $join->on('PRODUCTOS_TIENE_SECCION.COD_PROD','=','VENTASDET.COD_PROD')
                     ->on('PRODUCTOS_TIENE_SECCION.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL');
                })
            ->leftjoin('SECCIONES','SECCIONES.ID','=','PRODUCTOS_TIENE_SECCION.SECCION')
            ->leftJoin('VENTAS', 'VENTAS.ID', '=', 'VENTASDET.FK_VENTA')
            ->leftjoin('VENTAS_DESCUENTO', 'VENTAS_DESCUENTO.FK_VENTAS', '=', 'VENTAS.ID')
            ->select(DB::raw('
                VENTASDET.COD_PROD AS COD_PROD,
                VENTASDET.CODIGO,
                VENTASDET_TIENE_LOTES.CANTIDAD AS VENDIDO,
                LOTES.LOTE AS LOTE,
                LOTES.COSTO AS COSTO_UNIT,
                (VENTASDET_TIENE_LOTES.CANTIDAD*LOTES.COSTO) AS COSTO_TOTAL,
                LINEAS.DESCRIPCION AS CATEGORIA,
                SUBLINEAS.DESCRIPCION AS SUBCATEGORIA,
                SUBLINEA_DET.DESCRIPCION AS NOMBRE,
                PRODUCTOS_AUX.PROVEEDOR AS PROVEEDOR,
                PROVEEDORES.NOMBRE AS PROVEEDOR_NOMBRE,
                SECCIONES.DESCRIPCION AS SECCION,
                VENTAS.ID AS ID,
                VENTAS.VENDEDOR,
                (VENTASDET.PRECIO_UNIT*VENTASDET_TIENE_LOTES.CANTIDAD) AS PRECIO,
                VENTASDET.PRECIO_UNIT AS PRECIO_UNIT'),
                DB::raw('IFNULL(VENTASDET_DESCUENTO.TOTAL,0) AS DESCUENTO'),
                DB::raw('IFNULL(SECCIONES.ID,0) AS SECCION_CODIGO'),
                DB::raw('IFNULL(VENTAS_DESCUENTO.PORCENTAJE,0) AS PORCENTAJE_GENERAL'),
                DB::raw('IFNULL(LINEAS.CODIGO,0) AS LINEA_CODIGO'),
                DB::raw('IFNULL(SUBLINEAS.CODIGO,0) AS SUBLINEA_CODIGO'),
                DB::raw('IFNULL(VENTASDET_DESCUENTO.PORCENTAJE,0) AS DESCUENTO_PORCENTAJE'))
        ->Where('VENTASDET.ANULADO','<>',1)
        ->Where('VENTAS.TIPO','<>','CR')
        ->whereBetween('VENTASDET.FECALTAS', [$inicio, $final])
        ->Where('VENTASDET.ID_SUCURSAL','=',$datos->input("Sucursal"));


        if($datos->stock){
            $reporte->where('LOTES.CANTIDAD','>',0);
        }

        $reporte = $reporte->orderby('VENTASDET.COD_PROD')->get()->toArray();
        
        $precio = 100;
        $descuento_precio = 0;
        $precio_descontado = 0;
        $costo = 0;
        $total_dev = 0;
        $total_des = 0;
        $descuento = 0;
        $descuento_general = 0;
        $precio_descontado_general = 0;
        $precio_descontado_total = 0;
        $descuento_real = 0;
        $venta_in = array();

        foreach ($reporte as $key => $value ) {

            if($value->VENDIDO > 0){

                if ($value->PORCENTAJE_GENERAL > 0) {

                    $descuento_precio = round((($value->PRECIO * $value->PORCENTAJE_GENERAL) / 100),2);
                    $total_des = $total_des + $descuento_precio;
                    $value->PRECIO = (round($value->PRECIO - $descuento_precio,2));
                    $value->DESCUENTO = ($value->DESCUENTO + round($descuento_precio,2));
                    $value->PRECIO_UNIT = round((($value->PRECIO_UNIT * $value->PORCENTAJE_GENERAL) / 100),2);
                    $descuento = ($precio * $value->DESCUENTO_PORCENTAJE) / 100;
                    $precio_descontado = $precio - $descuento;
                    $descuento_general = ($precio_descontado * $value->PORCENTAJE_GENERAL) / 100;
                    $precio_descontado_general = $precio_descontado - $descuento_general;
                    $precio_descontado_total = $descuento + $descuento_general;
                    $descuento_real = ($precio_descontado_total * 100) / $precio;
                    $value->DESCUENTO_PORCENTAJE = $descuento_real;
                                
                }
                               
                if($value->DESCUENTO_PORCENTAJE == 0){
                    $value->DESCUENTO_PORCENTAJE = '0';
                }

                if($value->NOMBRE == NULL){
                    $value->NOMBRE = '';
                }
                
                if($value->SECCION == NULL){
                    $value->SECCION = 'INDEFINIDO';
                }
                   
                $nestedData['COD_PROD'] = $value->COD_PROD;
                $nestedData['VENDIDO'] = $value->VENDIDO;
                $nestedData['CATEGORIA'] = $value->CATEGORIA;
                $nestedData['SUBCATEGORIA'] = $value->SUBCATEGORIA;
                $nestedData['NOMBRE'] = $value->NOMBRE;
                $nestedData['DESCUENTO_PORCENTAJE'] = $value->PORCENTAJE_GENERAL;
                $nestedData['DESCUENTO_PRODUCTO'] = $value->DESCUENTO_PORCENTAJE;
                $nestedData['PRECIO'] = $value->PRECIO;
                $nestedData['PRECIO_UNIT'] = $value->PRECIO_UNIT;
                $nestedData['COSTO_UNIT'] = $value->COSTO_UNIT;
                $nestedData['COSTO_TOTAL'] = $value->COSTO_TOTAL;
                $nestedData['UTILIDAD'] = $value->PRECIO-$value->COSTO_TOTAL;
                $nestedData['DESCUENTO'] = $value->DESCUENTO;
                $nestedData['LINEA_CODIGO'] = $value->LINEA_CODIGO;
                $nestedData['SUBLINEA_CODIGO'] = $value->SUBLINEA_CODIGO;
                $nestedData['PROVEEDOR'] = $value->PROVEEDOR;
                $nestedData['PROVEEDOR_NOMBRE'] = $value->PROVEEDOR_NOMBRE;
                $nestedData['SECCION'] = $value->SECCION;
                $nestedData['SECCION_CODIGO'] = $value->SECCION_CODIGO;
                $nestedData['LOTE'] = $value->LOTE;
                $nestedData['USER_ID'] = $user->id;
                $nestedData['ID_SUCURSAL'] = $datos->input("Sucursal");
                $venta_in[] = $nestedData;         
            }  
        }
              
        foreach (array_chunk($venta_in,1000) as $t) {
            DB::connection('retail')->table('temp_ventas')->insert($t);
        }

        $venta_nc = array();

        $reporte = DB::connection('retail')->table('NOTA_CREDITO_DET')  
            ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'NOTA_CREDITO_DET.CODIGO_PROD')
            ->leftjoin('PRODUCTOS_AUX',function($join){
                $join->on('PRODUCTOS_AUX.CODIGO','=','NOTA_CREDITO_DET.CODIGO_PROD')
                    ->on('PRODUCTOS_AUX.ID_SUCURSAL','=','NOTA_CREDITO_DET.ID_SUCURSAL');
            }) 
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->leftjoin('VENTASDET', 'VENTASDET.ID', '=', 'NOTA_CREDITO_DET.FK_VENTASDET')
            ->leftjoin('nota_credito_tiene_lote', 'nota_credito_tiene_lote.FK_VENTA_DET', '=', 'VENTASDET.ID')
            ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'PRODUCTOS_AUX.PROVEEDOR')
            ->leftjoin('SUBLINEAS', 'SUBLINEAS.CODIGO', '=', 'PRODUCTOS.SUBLINEA')
            ->leftjoin('LOTES', 'LOTES.ID', '=', 'nota_credito_tiene_lote.ID_LOTE')
            ->leftjoin('SUBLINEA_DET', 'SUBLINEA_DET.CODIGO', '=', 'PRODUCTOS.SUBLINEADET')
            ->leftjoin('NOTA_CREDITO', 'NOTA_CREDITO.ID', '=', 'NOTA_CREDITO_DET.FK_NOTA_CREDITO')
            ->leftJoin('VENTAS', 'VENTAS.ID', '=', 'VENTASDET.FK_VENTA')
            ->leftjoin('PRODUCTOS_TIENE_SECCION',function($join){
                $join->on('PRODUCTOS_TIENE_SECCION.COD_PROD','=','NOTA_CREDITO_DET.CODIGO_PROD')
                    ->on('PRODUCTOS_TIENE_SECCION.ID_SUCURSAL','=','NOTA_CREDITO_DET.ID_SUCURSAL');
            })
            ->leftjoin('SECCIONES','SECCIONES.ID','=','PRODUCTOS_TIENE_SECCION.SECCION')
            ->select(DB::raw('
                NOTA_CREDITO_DET.CODIGO_PROD AS COD_PROD,
                VENTASDET.CODIGO,
                nota_credito_tiene_lote.CANTIDAD AS VENDIDO,
                LOTES.LOTE AS LOTE,
                LOTES.COSTO AS COSTO_UNIT,
                (nota_credito_tiene_lote.CANTIDAD*LOTES.COSTO) AS COSTO_TOTAL,
                LINEAS.DESCRIPCION AS CATEGORIA,
                SUBLINEAS.DESCRIPCION AS SUBCATEGORIA,
                SUBLINEA_DET.DESCRIPCION AS NOMBRE,
                PRODUCTOS_AUX.PROVEEDOR AS PROVEEDOR,
                PROVEEDORES.NOMBRE AS PROVEEDOR_NOMBRE,
                SECCIONES.DESCRIPCION AS SECCION,
                VENTAS.VENDEDOR,
                (nota_credito_tiene_lote.CANTIDAD*NOTA_CREDITO_DET.PRECIO) AS PRECIO,
                NOTA_CREDITO_DET.PRECIO AS PRECIO_UNIT'),
                DB::raw('IFNULL(LINEAS.CODIGO,0) AS LINEA_CODIGO'),
                DB::raw('IFNULL(SECCIONES.ID,0) AS SECCION_CODIGO'),
                DB::raw('IFNULL(SUBLINEAS.CODIGO,0) AS SUBLINEA_CODIGO'))
        ->Where('VENTASDET.ANULADO','<>',1)
        ->Where('NOTA_CREDITO.PROCESADO','=',1)
        ->Where('VENTAS.TIPO','<>','CR')
        ->whereBetween('NOTA_CREDITO_DET.FECALTAS', [$inicio , $final])
        ->Where('NOTA_CREDITO_DET.ID_SUCURSAL','=',$datos->input("Sucursal"));

        if($datos->stock){
            $reporte->where('LOTES.CANTIDAD','>',0);
        }

        $reporte = $reporte->orderby('VENTASDET.COD_PROD')->get()->toArray();

        foreach ($reporte as $key => $value) {
       
            if($value->NOMBRE == NULL){

                $value->NOMBRE = '';
            }

            if($value->SECCION == NULL){
 
                $value->SECCION = 'INDEFINIDO';
            }

            $nestedDataNC['COD_PROD'] = $value->COD_PROD;
            $nestedDataNC['VENDIDO'] =- $value->VENDIDO;
            $nestedDataNC['CATEGORIA'] = $value->CATEGORIA;
            $nestedDataNC['SUBCATEGORIA']= $value->SUBCATEGORIA;
            $nestedDataNC['NOMBRE'] = 'DEVOLUCION NC:'.$value->NOMBRE;
            $nestedDataNC['DESCUENTO_PORCENTAJE'] = 0;
            $nestedDataNC['DESCUENTO_PRODUCTO'] = 0;
            $nestedDataNC['PRECIO'] = $value->PRECIO*-1;
            $nestedDataNC['PRECIO_UNIT'] = $value->PRECIO_UNIT*-1;
            $nestedDataNC['COSTO_UNIT'] = $value->COSTO_UNIT*-1;
            $nestedDataNC['COSTO_TOTAL'] = $value->COSTO_TOTAL*-1;

            if($value->COSTO_TOTAL > $value->PRECIO){
                $nestedDataNC['UTILIDAD'] = $value->COSTO_TOTAL*-1 + $value->PRECIO ;
            }elseif ($value->PRECIO>$value->COSTO_TOTAL) {
                $nestedDataNC['UTILIDAD'] = ($value->PRECIO*-1) + $value->COSTO_TOTAL;
            }else{
                $nestedDataNC['COSTO_TOTAL'] = ($value->PRECIO*-1) + $value->COSTO_TOTAL;
            }

            $nestedDataNC['DESCUENTO'] = 0;
            $nestedDataNC['LINEA_CODIGO'] = $value->LINEA_CODIGO;
            $nestedDataNC['SUBLINEA_CODIGO'] = $value->SUBLINEA_CODIGO;
            $nestedDataNC['PROVEEDOR'] = $value->PROVEEDOR;
            $nestedDataNC['PROVEEDOR_NOMBRE'] = $value->PROVEEDOR_NOMBRE;
            $nestedDataNC['SECCION'] = $value->SECCION;
            $nestedDataNC['SECCION_CODIGO'] = $value->SECCION_CODIGO;
            $nestedDataNC['LOTE'] = $value->LOTE;
            $nestedDataNC['USER_ID'] = $user->id;
            $nestedDataNC['ID_SUCURSAL'] = $datos->input("Sucursal");

            $venta_nc[] = $nestedDataNC;
        }

        foreach (array_chunk($venta_nc,1000) as $t) {
            DB::connection('retail')->table('temp_ventas')->insert($t);
        }

        return;
    }

    public static function rpt_periodo_venta($datos){

        $user = auth()->user();

        $inicio = date('Y-m-d', strtotime($datos["datos"]["Inicio"]));
        $final  =  date('Y-m-d', strtotime($datos["datos"]["Final"]));
     
        /*  --------------------------------------------------------------------------------- */

        // CREAR COLUMNA DE ARRAY 

        $columns = array( 
            0 => 'CODIGO', 
            1 => 'IMAGEN', 
            2 => 'DESCRIPCION',
            3 => 'CATEGORIA',
            4 => 'PROVEEDOR',
            5 => 'PREC_VENTA',
            6 => 'PREMAYORISTA',
            7 => 'STOCK',
            8 => 'CANTIDAD_INICIAL',
            9 => 'ULTIMA_ENTRADA',
            10 => 'ULTIMO_MOVIMIENTO',
            11 => 'ULTIMA_VENTA',
        );
        
        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $dia = date("Y-m-d");

        //  CARGAR TODOS LOS PRODUCTOS ENCONTRADOS 

        $posts = Stock::query()->select(DB::raw('
                LOTES.COD_PROD, 
                0 AS IMAGEN, 
                PRODUCTOS.DESCRIPCION, 
                PRODUCTOS_AUX.PREC_VENTA, 
                PRODUCTOS_AUX.PREMAYORISTA,
                PROVEEDORES.NOMBRE AS PROVEEDOR,
                LINEAS.DESCRIPCION AS CATEGORIA,
                IFNULL((SELECT SUM(l4.CANTIDAD) FROM lotes as l4 WHERE ((l4.COD_PROD = LOTES.COD_PROD) AND (l4.ID_SUCURSAL = LOTES.ID_SUCURSAL))),"0") AS STOCK,
                (IFNULL((SELECT sum(l.CANTIDAD_INICIAL) FROM lotes as l WHERE (l.COD_PROD = LOTES.COD_PROD) AND (l.ID_SUCURSAL = LOTES.ID_SUCURSAL) AND l.FECALTAS BETWEEN date_add((select max(l1.FECALTAS) FROM LOTES AS l1 WHERE l1.COD_PROD =l.COD_PROD AND (l1.ID_SUCURSAL = l.ID_SUCURSAL)) , INTERVAL -3 WEEK) AND (select max(l2.FECALTAS) FROM LOTES AS l2 WHERE l2.COD_PROD =l.COD_PROD AND (l2.ID_SUCURSAL = l.ID_SUCURSAL))),0)) as CANTIDAD_INICIAL,
                (SELECT MAX(L7.FECALTAS) FROM LOTES AS L7 WHERE L7.ID_SUCURSAL=LOTES.ID_SUCURSAL AND L7.COD_PROD=LOTES.COD_PROD) AS ULTIMA_ENTRADA,
                (SELECT MAX(L5.FECMODIF) AS ULT_FECHA FROM LOTES AS L5 WHERE L5.ID_SUCURSAL=LOTES.ID_SUCURSAL AND L5.COD_PROD=LOTES.COD_PROD) AS ULTIMO_MOVIMIENTO, 
                (SELECT MAX(VENTASDET.FECALTAS) AS ULTIMA_VENTA FROM VENTASDET WHERE VENTASDET.COD_PROD=LOTES.COD_PROD AND VENTASDET.ID_SUCURSAL=LOTES.ID_SUCURSAL) AS ULTIMA_VENTA'))
            ->leftJoin('PRODUCTOS_AUX', function($join){
                $join->on('PRODUCTOS_AUX.CODIGO', '=', 'lOTES.COD_PROD')
                     ->on('lOTES.ID_SUCURSAL', '=', 'PRODUCTOS_AUX.ID_SUCURSAL');
                })
            ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'LOTES.COD_PROD')
            ->leftjoin('proveedores','proveedores.codigo','=','PRODUCTOS_AUX.PROVEEDOR')
            ->leftjoin('LINEAS','LINEAS.CODIGO','=','PRODUCTOS.LINEA')
        ->Where('LOTES.ID_SUCURSAL', '=', $datos["datos"]["Sucursal"])
        ->where(DB::raw('(SELECT MAX(L6.FECMODIF) AS ULT_FECHA FROM LOTES AS L6 WHERE L6.ID_SUCURSAL=LOTES.ID_SUCURSAL AND L6.COD_PROD=LOTES.COD_PROD)'), '<', $inicio)
        ->whereNotBetween(DB::raw('(SELECT MAX(L3.FECMODIF) AS ULT_FECHA FROM LOTES AS L3 WHERE L3.ID_SUCURSAL=LOTES.ID_SUCURSAL AND L3.COD_PROD=LOTES.COD_PROD)'), [$inicio, $final]);

        if ($datos->input("Stock") === false) {

            $posts = $posts->whereRaw('(IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = LOTES.COD_PROD) AND (l.ID_SUCURSAL = LOTES.ID_SUCURSAL))),0)) <= 0');
        } else {

            $posts = $posts->whereRaw('(IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = LOTES.COD_PROD) AND (l.ID_SUCURSAL = LOTES.ID_SUCURSAL))),0)) > 0');
        }

        if($datos["datos"]["Filtro"] == "SECCION"){

            $posts->leftjoin('GONDOLA_TIENE_PRODUCTOS',function($join){
                $join->on('GONDOLA_TIENE_PRODUCTOS.GONDOLA_COD_PROD','=','LOTES.COD_PROD')
                     ->on('GONDOLA_TIENE_PRODUCTOS.ID_SUCURSAL','=','LOTES.ID_SUCURSAL');
                })
                ->leftjoin('GONDOLA_TIENE_SECCION','GONDOLA_TIENE_SECCION.ID_GONDOLA','=','GONDOLA_TIENE_PRODUCTOS.ID_GONDOLA')
                ->leftjoin('SECCIONES','SECCIONES.ID','=','GONDOLA_TIENE_SECCION.ID_SECCION')
            ->where('SECCIONES.ID','=', $datos["datos"]["Seccion"]);

            if($datos["datos"]["AllProveedores"] == false){
                $posts->whereIn('PRODUCTOS_AUX.PROVEEDOR',$datos["datos"]["Proveedores"]);
            }

            if($datos["datos"]["AllCategorySeccion"] == false){
                $posts->whereIn('PRODUCTOS.LINEA',$datos["datos"]["CategoriaSeccion"]);
            }
        }

        if($datos["datos"]["Filtro"] == "PROVEEDOR"){

            if($datos["datos"]["AllProveedores"] == false){

                $posts->whereIn('PRODUCTOS_AUX.PROVEEDOR',$datos["datos"]["Proveedores"]);
            }
            
            if($datos["datos"]["AllCategory"]==false){
                $posts->whereIn('PRODUCTOS.LINEA',$datos["datos"]["Categorias"]);
            }
        }
        
        $posts = $posts->groupBy('LOTES.COD_PROD')->get();

        $data = array();
        $c = 1;

        /*  --------------------------------------------------------------------------------- */

        // REVISAR SI LA VARIABLES POST ESTA VACIA 

        if(!empty($posts)){

            foreach ($posts as $post){

                // CARGAR EN LA VARIABLE 
          
                $filename = '../storage/app/public/imagenes/productos/'.$post->COD_PROD.'.jpg';
                
                if(file_exists($filename)) {
                    $imagen_producto = 'http://131.196.192.165:8080/storage/imagenes/productos/'.$post->COD_PROD.'.jpg';
                } else {
                    $imagen_producto = 'http://131.196.192.165:8080/storage/imagenes/productos/product.png';
                }

                $nestedData['CODIGO'] = $post->COD_PROD;
                $nestedData['IMAGEN'] = "<img src='".$imagen_producto."'  width='100%'>";
                $nestedData['DESCRIPCION'] = ucwords(utf8_encode(utf8_decode(substr($post->DESCRIPCION,0,25))));
                $nestedData['CATEGORIA'] = ucwords(utf8_encode(utf8_decode($post->CATEGORIA)));
                $nestedData['PROVEEDOR'] = ucwords(utf8_encode(utf8_decode($post->PROVEEDOR)));
                $nestedData['PREC_VENTA'] = round($post->PREC_VENTA,2);
                $nestedData['PREMAYORISTA'] = round($post->PREMAYORISTA,2);
                $nestedData['STOCK'] = $post->STOCK;
                $nestedData['CANTIDAD_INICIAL'] = $post->CANTIDAD_INICIAL;
                $nestedData['ULTIMA_ENTRADA'] = substr($post->ULTIMA_ENTRADA,0,10);
                $nestedData['ULTIMO_MOVIMIENTO'] = $post->ULTIMO_MOVIMIENTO;
                $nestedData['ULTIMA_VENTA'] = substr($post->ULTIMA_VENTA, 0,10);

                $data[] = $nestedData;
                $c = $c + 1;
            }
        }
        
        /*  --------------------------------------------------------------------------------- */

        // PREPARAR EL ARRAY A ENVIAR 

        $json_data = array(
            "draw"            => intval($datos->input('draw')),  
            "recordsTotal"    => intval($c),  
            "recordsFiltered" => intval($c), 
            "data"            => $data   
        );
        
        /*  --------------------------------------------------------------------------------- */

        // CONVERTIR EN JSON EL ARRAY Y ENVIAR 

       return $json_data;         
    }

    public static function generarReporteVentaProveedor($datos){
        
        /*  --------------------------------------------------------------------------------- */

        // INCICIAR VARIABLES 
        
        $insert = $datos["data"]["Insert"];
        $marcas[] = array();
        $categorias[] = array();
        $totales[] = array();
        $proveedores_array = array();
        $proveedores_categoria_array = array();
        $proveedores_productos_array = array();
        $user = auth()->user();
        $user = $user->id;
        $inicio = date('Y-m-d', strtotime($datos["data"]['Inicio']));
        $final = date('Y-m-d', strtotime($datos["data"]['Final']));
        $sucursal = $datos["data"]['Sucursal'];
        $total_general = 0;
        $total_descuento = 0;
        $total_preciounit = 0;
        $cantidadvendida = 0;
        $costo = 0;
        $totalcosto = 0;
     
        if($insert == true){

            if(isset($datos["data"]["Tipo"])){
                 
                $data = array(
                    'inicio' => date('Y-m-d', strtotime($datos["data"]['Inicio'])),
                    'final' => date('Y-m-d', strtotime($datos["data"]['Final'])),
                    'sucursal' => $datos["data"]['Sucursal'],
                    'checkedCategoria' => $datos["data"]['AllCategory'],
                    'checkedProveedor' => $datos["data"]["AllProveedores"],
                    'proveedores' => $datos["data"]["Proveedores"],
                    'linea' => $datos["data"]['Categorias'],
                    'tipos' => $datos["data"]["Tipo"]
                );

            }else{

                $data=array(
                    'inicio'=> date('Y-m-d', strtotime($datos["data"]['Inicio'])),
                    'final' => date('Y-m-d', strtotime($datos["data"]['Final'])),
                    'sucursal' => $datos["data"]['Sucursal'],
                    'checkedCategoria' => $datos["data"]['AllCategory'],
                    'checkedProveedor' => $datos["data"]["AllProveedores"],
                    'proveedores' => $datos["data"]["Proveedores"],
                    'linea' => $datos["data"]['Categorias']
                );
            }

            Temp_venta::insertar_reporte($data);
        }
        if($datos["data"]["agruparCategoria"] == true && $datos["data"]["AgruparProveedor"] == true){
            
            $temp = DB::connection('retail')->table('temp_ventas')->select(
                DB::raw('temp_ventas.PROVEEDOR AS PROVEEDOR'),
                DB::raw('CONCAT (temp_ventas.PROVEEDOR_NOMBRE," ", CATEGORIA) AS DESCRIPCION'),
                DB::raw('temp_ventas.LINEA_CODIGO AS LINEA'),
                DB::raw('SUM(temp_ventas.VENDIDO) AS VENDIDO'),
                DB::raw('SUM(temp_ventas.DESCUENTO) AS DESCUENTO'),
                DB::raw('SUM(COSTO_TOTAL) AS COSTO_TOTAL'),
                DB::raw('SUM(COSTO_UNIT) AS COSTO_UNIT'),
                DB::raw('SUM(temp_ventas.PRECIO) AS TOTAL'),
                DB::raw('SUM(temp_ventas.PRECIO_UNIT) AS PRECIO_UNIT'))
            ->where('USER_ID','=',$user)
            ->where('ID_SUCURSAL','=',$sucursal)
            ->GROUPBY('temp_ventas.PROVEEDOR','temp_ventas.LINEA_CODIGO') 
            ->orderby('temp_ventas.PROVEEDOR_NOMBRE','ASC')
            ->get()
            ->toArray();

        }else if( $datos["data"]["AgruparProveedor"] == true) {

            $temp = DB::connection('retail')->table('temp_ventas')->select(
                DB::raw('temp_ventas.PROVEEDOR AS PROVEEDOR'),
                DB::raw('temp_ventas.PROVEEDOR_NOMBRE AS DESCRIPCION'),
                DB::raw('0 AS LINEA'),
                DB::raw('SUM(temp_ventas.VENDIDO) AS VENDIDO'),
                DB::raw('SUM(temp_ventas.DESCUENTO) AS DESCUENTO'),
                DB::raw('SUM(COSTO_TOTAL) AS COSTO_TOTAL'),
                DB::raw('SUM(COSTO_UNIT) AS COSTO_UNIT'),
                DB::raw('SUM(temp_ventas.PRECIO) AS TOTAL'),
                DB::raw('SUM(temp_ventas.PRECIO_UNIT) AS PRECIO_UNIT'))
            ->where('USER_ID','=',$user)
            ->where('ID_SUCURSAL','=',$sucursal)
            ->GROUPBY('temp_ventas.PROVEEDOR') 
            ->orderby('temp_ventas.PROVEEDOR_NOMBRE')
            ->get()
            ->toArray();

        }elseif ($datos["data"]["agruparCategoria"] == true) {

            $temp = DB::connection('retail')->table('temp_ventas')->select(
                DB::raw('0 AS PROVEEDOR'),
                DB::raw('"" AS DESCRI_P'),
                DB::raw('temp_ventas.LINEA_CODIGO AS LINEA'),
                DB::raw('temp_ventas.CATEGORIA as DESCRIPCION'),
                DB::raw('SUM(temp_ventas.VENDIDO) AS VENDIDO'),
                DB::raw('SUM(temp_ventas.DESCUENTO) AS DESCUENTO'),
                DB::raw('SUM(COSTO_TOTAL) AS COSTO_TOTAL'),
                DB::raw('SUM(COSTO_UNIT) AS COSTO_UNIT'),
                DB::raw('SUM(temp_ventas.PRECIO) AS TOTAL'),
                DB::raw('SUM(temp_ventas.PRECIO_UNIT) AS PRECIO_UNIT'))
            ->where('USER_ID','=',$user)
            ->where('ID_SUCURSAL','=',$sucursal)
            ->GROUPBY('temp_ventas.LINEA_CODIGO') 
            ->orderby('temp_ventas.CATEGORIA')
            ->get()
            ->toArray();
        }

        foreach ($temp as $key => $value) {

            $total_general = $total_general + $value->TOTAL;
            $total_descuento = $total_descuento + $value->DESCUENTO;
            $total_preciounit = $total_preciounit + $value->PRECIO_UNIT;
            $cantidadvendida = $cantidadvendida + $value->VENDIDO;
            $costo = $costo + $value->COSTO_UNIT;
            $totalcosto = $totalcosto + $value->COSTO_TOTAL;

            $proveedores_array[] = array(
                'TOTALES' => $value->DESCRIPCION,
                'VENDIDO' => $value->VENDIDO,
                'DESCUENTO' => $value->DESCUENTO,
                'COSTO' => $value->COSTO_UNIT,
                'COSTO_TOTAL' => $value->COSTO_TOTAL,
                'PRECIO' => $value->PRECIO_UNIT,
                'TOTAL' => $value->TOTAL,
                'PROVEEDORES' => $value->PROVEEDOR,
                'LINEAS' => $value->LINEA
            );
        }

        $ser = VentasDetServicios::leftjoin('VENTAS',function($join){
                $join->on('VENTAS.CODIGO','=','VENTASDET_SERVICIOS.CODIGO')
                    ->on('VENTAS.CAJA','=','VENTASDET_SERVICIOS.CAJA')
                    ->on('VENTAS.ID_SUCURSAL','=','VENTASDET_SERVICIOS.ID_SUCURSAL');
                })
            ->leftjoin('VENTAS_ANULADO', 'VENTAS_ANULADO.FK_VENTA', '=', 'VENTAS.ID')
                ->select(DB::raw('
                    SUM(VENTASDET_SERVICIOS.PRECIO) AS PRECIO_SERVICIO,
                    SUM(VENTASDET_SERVICIOS.CANTIDAD) AS VENDIDO,
                    SUM(VENTASDET_SERVICIOS.PRECIO_UNIT) AS PRECIO_UNIT')) 
        ->Where('VENTAS_ANULADO.ANULADO','=',0)
        ->Where('VENTAS.ID_SUCURSAL','=',$sucursal)
        ->whereBetween('VENTAS.FECALTAS', [$inicio, $final])
        ->get()
        ->toArray();
        
        if(count($ser) > 0){
        
            $total_general = $total_general + $ser[0]->PRECIO_SERVICIO;
            $total_preciounit = $total_preciounit + $ser[0]->PRECIO_UNIT;
            $cantidadvendida = $cantidadvendida + $ser[0]->VENDIDO;
          
            $proveedores_array[] = array(
                'TOTALES' => 'SERVICIO DE DELIVERY',
                'VENDIDO' => $ser[0]->VENDIDO,
                'DESCUENTO' =>'0',
                'COSTO' => '0',
                'COSTO_TOTAL' => '0',
                'PRECIO' => $ser[0]->PRECIO_UNIT,
                'TOTAL' => $ser[0]->PRECIO_SERVICIO,
            );
        }

        //TOTALES POR CATEGORIA AGRUPADOS POR MARCA
        /*  --------------------------------------------------------------------------------- */ 

        $temp = DB::connection('retail')->table('temp_ventas')->select(
            DB::raw('temp_ventas.PROVEEDOR AS PROVEEDOR'),
            DB::raw('temp_ventas.PROVEEDOR_NOMBRE AS DESCRI_P'),
            DB::raw('temp_ventas.LINEA_CODIGO AS LINEA'),
            DB::raw('SUM(temp_ventas.VENDIDO) AS VENDIDO'),
            DB::raw('SUM(temp_ventas.DESCUENTO) AS DESCUENTO'),
            DB::raw('SUM(COSTO_TOTAL) AS COSTO_TOTAL'),
            DB::raw('SUM(temp_ventas.PRECIO) AS TOTAL'),
            DB::raw('temp_ventas.CATEGORIA as DESCRI_L'))
        ->where('USER_ID','=',$user)
        ->where('ID_SUCURSAL','=',$sucursal)
        ->GROUPBY('temp_ventas.PROVEEDOR','temp_ventas.LINEA_CODIGO') 
        ->orderby('temp_ventas.PROVEEDOR')
        ->get()
        ->toArray();
         
        foreach ($temp as $key => $value) {
            
            $proveedores_categoria_array[] = array(
                'PROVEEDOR' => $value->PROVEEDOR,
                'DESCRI_P' => $value->DESCRI_P,
                'LINEA' => $value->LINEA,
                'DESCRI_L' => $value->DESCRI_L,
                'VENDIDO' => $value->VENDIDO,
                'COSTO_TOTAL' => $value->COSTO_TOTAL,
                'TOTAL' => $value->TOTAL,
                'DESCUENTO' => $value->DESCUENTO,
                
            );
        }

        /*  --------------------------------------------------------------------------------- */  
        //TRAER TODOS LOS PRODUCTOS CON EL CODIGO DE MARCA
        /*  --------------------------------------------------------------------------------- */
               
        $temp = DB::connection('retail')->table('temp_ventas')->select(
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
            DB::raw('IFNULL(temp_ventas.LINEA_CODIGO,0) AS LINEA_CODIGO'),
            DB::raw('temp_ventas.SUBCATEGORIA AS SUBCATEGORIA'),
            DB::raw('temp_ventas.MARCA AS MARCA'),
            DB::raw('temp_ventas.MARCAS_CODIGO AS MARCAS_CODIGO'),
            DB::raw('temp_ventas.PROVEEDOR_NOMBRE AS PROVEEDOR'),
            DB::raw('temp_ventas.PROVEEDOR AS PROVEEDOR_CODIGO'),
            DB::raw('temp_ventas.DESCUENTO_PORCENTAJE AS DESCUENTO_PORCENTAJE'),
            DB::raw('temp_ventas.DESCUENTO_PRODUCTO AS DESCUENTO_PRODUCTO'))
        ->where('temp_ventas.ID_SUCURSAL','=',$sucursal)
        ->where('temp_ventas.USER_ID','=',$user)
        ->GROUPBY('temp_ventas.COD_PROD','temp_ventas.LOTE','temp_ventas.DESCUENTO_PRODUCTO') 
        ->orderby('COD_PROD')
        ->get()
        ->toArray();

        $total_general = 0;
        $total_descuento = 0;
        $total_preciounit = 0;
        $cantidadvendida = 0;
        $costo = 0;
        $totalcosto = 0;
       
        foreach ($temp as $key => $value) {

            if($value->TOTAL == 0){
                $value->TOTAL ='0';
            }
            if($value->PRECIO_UNIT == 0){
                $value->PRECIO_UNIT ='0';
            }
            if($value->STOCK == 0){
                $value->STOCK ='0';
            }
            if($value->DESCUENTO == 0){
                $value->DESCUENTO ='0';
            }
               
            $total_general = $total_general + $value->TOTAL;
            $total_descuento = $total_descuento + $value->DESCUENTO;
            $total_preciounit = $total_preciounit + $value->PRECIO_UNIT;
            $cantidadvendida = $cantidadvendida + $value->VENDIDO;
            $costo = $costo + $value->COSTO_UNIT;
            $totalcosto = $totalcosto + $value->COSTO_TOTAL;
              
            $proveedores_productos_array[] = array(  
                'COD_PROD' => $value->COD_PROD,
                'LOTE' => $value->LOTE,
                'STOCK' => $value->STOCK,
                'CATEGORIA' => $value->CATEGORIA,
                'SUBCATEGORIA' => $value->SUBCATEGORIA,
                'MARCA' => $value->MARCA,
                'VENDIDO' => $value->VENDIDO,
                'PRECIO_UNIT' => $value->PRECIO_UNIT,
                'TOTAL' => $value->TOTAL,
                'DESCUENTO' => $value->DESCUENTO,
                'COSTO_UNIT' => $value->COSTO_UNIT,
                'COSTO_TOTAL' => $value->COSTO_TOTAL,
                'DESCUENTO_PORCENTAJE' => $value->DESCUENTO_PRODUCTO,
                'MARCAS_CODIGO' => $value->MARCAS_CODIGO,
                'LINEAS_CODIGO' => $value->LINEA_CODIGO,
                'PROVEEDOR' => $value->PROVEEDOR,
                'PROVEEDOR_CODIGO' => $value->PROVEEDOR_CODIGO
            );
        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR TODOS LOS ARRAYS

        return ['ventas' => $proveedores_productos_array, 'proveedores' => $proveedores_array, 'categorias' => $proveedores_categoria_array];

        /*  --------------------------------------------------------------------------------- */
    }

    public static function generarReporteVentaGondola($datos) {
        
        /*  --------------------------------------------------------------------------------- */

        // INCICIAR VARIABLES 
        
        $insert = $datos["data"]["Insert"];
        $marcas[] = array();
        $categorias[] = array();
        $totales[] = array();
        $secciones_array = array();
        $secciones_gondola_array = array();
        $secciones_productos_array = array();
        $user = auth()->user();
        $user = $user->id;
        $inicio = date('Y-m-d', strtotime($datos["data"]['Inicio']));
        $final = date('Y-m-d', strtotime($datos["data"]['Final']));
        $sucursal = $datos["data"]['Sucursal'];
        $total_general = 0;
        $total_descuento = 0;
        $total_preciounit = 0;
        $cantidadvendida = 0;
        $costo = 0;
        $totalcosto = 0;

        if($insert == true){

            $data = array(
                'inicio' => date('Y-m-d', strtotime($datos["data"]["Inicio"])),
                'final' => date('Y-m-d', strtotime($datos["data"]["Final"])),
                'sucursal' => $datos["data"]["Sucursal"],
                'checkedGondola' => $datos["data"]["AllSecciones"],
                'checkedSeccion' => $datos["data"]["AllGondolas"],
                'gondolas' => $datos["data"]["Gondolas"],
                'secciones' => $datos["data"]["secciones"],
                'mayoristaContado' => $datos["data"]["MayoristaContado"],
                'mayoristaCredito' => $datos["data"]["MayoristaCredito"],
                'servicioDelivery' => $datos["data"]["ServicioDelivery"]
            );
                
            Temp_venta::insertar_reporte($data);
        }

        $temp = DB::connection('retail')->table('temp_ventas')->select(
            DB::raw('temp_ventas.SECCION_CODIGO AS SECCION_CODIGO'),
            DB::raw('IFNULL(Temp_ventas.SECCION,"INDEFINIDO") AS DESCRIPCION'),
            DB::raw('IFNULL(SUM(temp_ventas.VENDIDO),0) AS VENDIDO'),
            DB::raw('IFNULL(SUM(temp_ventas.DESCUENTO),0) AS DESCUENTO'),
            DB::raw('IFNULL(SUM(COSTO_TOTAL),0) AS COSTO_TOTAL'),
            DB::raw('IFNULL(SUM(COSTO_UNIT),0) AS COSTO_UNIT'),
            DB::raw('IFNULL(SUM(temp_ventas.PRECIO),0) AS TOTAL'),
            DB::raw('IFNULL(SUM(temp_ventas.PRECIO_UNIT),0) AS PRECIO_UNIT'))
        ->where('USER_ID','=',$user)
        ->where('ID_SUCURSAL','=',$sucursal)
        ->where('Temp_ventas.CREDITO_COBRADO','=',0)
        ->where('temp_ventas.vendedor','=',1)
        ->GROUPBY('temp_ventas.SECCION_CODIGO') 
        ->orderby('temp_ventas.SECCION','ASC')
        ->get()
        ->toArray();

        $TOTALG = DB::connection('retail')->table('temp_ventas')->select(
            DB::raw('SUM(temp_ventas.VENDIDO) AS VENDIDO'),
            DB::raw('SUM(temp_ventas.DESCUENTO) AS DESCUENTO'),
            DB::raw('SUM(COSTO_TOTAL) AS COSTO_TOTAL'),
            DB::raw('SUM(COSTO_UNIT) AS COSTO_UNIT'),
            DB::raw('SUM(temp_ventas.PRECIO) AS TOTAL'),
            DB::raw('SUM(temp_ventas.PRECIO_UNIT) AS PRECIO_UNIT'))
        ->where('USER_ID','=',$user)
        ->where('ID_SUCURSAL','=',$sucursal)
        ->get()
        ->toArray();

        $ser = VentasDetServicios::leftjoin('VENTAS',function($join){
                $join->on('VENTAS.CODIGO','=','ventasdet_servicios.CODIGO')
                     ->on('VENTAS.CAJA','=','ventasdet_servicios.CAJA')
                     ->on('VENTAS.ID_SUCURSAL','=','ventasdet_servicios.ID_SUCURSAL');
            })
            ->leftjoin('VENTAS_ANULADO', 'VENTAS_ANULADO.FK_VENTA', '=', 'VENTAS.ID')
                ->select(DB::raw('
                    SUM(ventasdet_servicios.PRECIO) AS PRECIO_SERVICIO,
                    sum(ventasdet_servicios.CANTIDAD) AS VENDIDO,
                    sum(ventasdet_servicios.PRECIO_UNIT) AS PRECIO_UNIT')) 
        ->Where('VENTAS_ANULADO.ANULADO','=',0)
        ->Where('VENTAS.ID_SUCURSAL','=',$sucursal)
        ->whereBetween('VENTAS.FECALTAS', [$inicio, $final])
        ->get()
        ->toArray();

        if(count($ser) > 0){
            $total_porcentaje = $TOTALG[0]->TOTAL+$ser[0]->PRECIO_SERVICIO;
        }else{
            $total_porcentaje = $TOTALG[0]->TOTAL;
        }

        foreach ($temp as $key => $value) {

           $total_general = $total_general + $value->TOTAL;
           $total_descuento = $total_descuento + $value->DESCUENTO;
           $total_preciounit = $total_preciounit + $value->PRECIO_UNIT;
           $cantidadvendida = $cantidadvendida + $value->VENDIDO;
           $costo = $costo + $value->COSTO_UNIT;
           $totalcosto = $totalcosto + $value->COSTO_TOTAL;

            $secciones_array[] = array(
                'TOTALES' => $value->DESCRIPCION,
                'VENDIDO' => $value->VENDIDO,
                'DESCUENTO' => $value->DESCUENTO,
                'COSTO' => $value->COSTO_UNIT,
                'COSTO_TOTAL' => $value->COSTO_TOTAL,
                'PRECIO' => $value->PRECIO_UNIT,
                'TOTAL' => $value->TOTAL,
                'SECCIONES' => $value->SECCION_CODIGO,
                'PORCENTAJE'=>($value->TOTAL*100)/$total_porcentaje
            );
        }
                             
        if(count($ser) > 0){

            if($datos["data"]["ServicioDelivery"]){

                $total_general = $total_general + $ser[0]->PRECIO_SERVICIO;
                $total_preciounit = $total_preciounit + $ser[0]->PRECIO_UNIT;
                $cantidadvendida = $cantidadvendida + $ser[0]->VENDIDO;
              
                $secciones_array[] = array(
                    'TOTALES'=> 'SERVICIO DE DELIVERY',
                    'VENDIDO' => $ser[0]->VENDIDO,
                    'DESCUENTO'=> 0,
                    'COSTO'=> 0,
                    'COSTO_TOTAL'=> 0,
                    'PRECIO' => $ser[0]->PRECIO_UNIT,
                    'TOTAL' => $ser[0]->PRECIO_SERVICIO,
                    'PORCENTAJE'=> ($ser[0]->PRECIO_UNIT * 100) / $total_porcentaje
                );
            }
        }

        if($datos["data"]["MayoristaContado"]){

            $TOTAL = DB::connection('retail')->table('temp_ventas')->select(
                DB::raw('IFNULL(SUM(temp_ventas.VENDIDO),0) AS VENDIDO'),
                DB::raw('IFNULL(SUM(temp_ventas.DESCUENTO),0) AS DESCUENTO'),
                DB::raw('IFNULL(SUM(COSTO_TOTAL),0) AS COSTO_TOTAL'),
                DB::raw('IFNULL(SUM(COSTO_UNIT),0) AS COSTO_UNIT'),
                DB::raw('IFNULL(SUM(temp_ventas.PRECIO),0) AS TOTAL'),
                DB::raw('IFNULL(SUM(temp_ventas.PRECIO_UNIT),0) AS PRECIO_UNIT'))
            ->WHERE('TEMP_VENTAS.VENDEDOR','<>',1)
            ->where('TEMP_VENTAS.CREDITO_COBRADO','=',0)
            ->where('TEMP_VENTAS.USER_ID','=',$user)
            ->where('TEMP_VENTAS.ID_SUCURSAL','=',$sucursal)
            ->get()
            ->toArray();

            $secciones_array[] = array(
                'TOTALES' => "MAYORISTA AL CONTADO",
                'VENDIDO' => $TOTAL[0]->VENDIDO,
                'DESCUENTO' => $TOTAL[0]->DESCUENTO,
                'COSTO' => $TOTAL[0]->COSTO_UNIT,
                'COSTO_TOTAL' => $TOTAL[0]->COSTO_TOTAL,
                'PRECIO' => $TOTAL[0]->PRECIO_UNIT,
                'TOTAL' => $TOTAL[0]->TOTAL,  
                'PORCENTAJE' => (100 * $TOTAL[0]->TOTAL) / $total_porcentaje,
            );
        }

        if($datos["data"]["MayoristaCredito"]){

            $TOTAL = DB::connection('retail')->table('temp_ventas')->select(
                DB::raw('IFNULL(SUM(temp_ventas.VENDIDO),0) AS VENDIDO'),
                DB::raw('IFNULL(SUM(temp_ventas.DESCUENTO),0) AS DESCUENTO'),
                DB::raw('IFNULL(SUM(COSTO_TOTAL),0) AS COSTO_TOTAL'),
                DB::raw('IFNULL(SUM(COSTO_UNIT),0) AS COSTO_UNIT'),
                DB::raw('IFNULL(SUM(temp_ventas.PRECIO),0) AS TOTAL'),
                DB::raw('IFNULL(SUM(temp_ventas.PRECIO_UNIT),0) AS PRECIO_UNIT'))
            ->where('TEMP_VENTAS.CREDITO_COBRADO','=',1)
            ->where('TEMP_VENTAS.USER_ID','=',$user)
            ->where('TEMP_VENTAS.ID_SUCURSAL','=',$sucursal)
            ->get()
            ->toArray();

            $secciones_array[] = array(
                'TOTALES'=> "MAYORISTA CREDITO COBRADO",
                'VENDIDO' => $TOTAL[0]->VENDIDO,
                'DESCUENTO' => $TOTAL[0]->DESCUENTO,
                'COSTO' => $TOTAL[0]->COSTO_UNIT,
                'COSTO_TOTAL' => $TOTAL[0]->COSTO_TOTAL,
                'PRECIO' => $TOTAL[0]->PRECIO_UNIT,
                'TOTAL' => $TOTAL[0]->TOTAL,  
                'PORCENTAJE'=> (100 * $TOTAL[0]->TOTAL) / $total_porcentaje,
            );
        }
                          
        //TOTALES POR CATEGORIA AGRUPADOS POR MARCA
        /*  --------------------------------------------------------------------------------- */
          
        $temp = DB::connection('retail')->table('temp_ventas')->select(
            DB::raw('temp_ventas.SECCION_CODIGO AS SECCION'),
            DB::raw('IFNULL(temp_ventas.SECCION,"INDEFINIDO") AS DESCRI_S'),
            DB::raw('temp_ventas.GONDOLA AS GONDOLA'),
            DB::raw('IFNULL(SUM(temp_ventas.VENDIDO),0) AS VENDIDO'),
            DB::raw('IFNULL(SUM(temp_ventas.DESCUENTO),0) AS DESCUENTO'),
            DB::raw('IFNULL(SUM(COSTO_TOTAL),0) AS COSTO_TOTAL'),
            DB::raw('IFNULL(SUM(temp_ventas.PRECIO),0) AS TOTAL'),
            DB::raw('IFNULL(temp_ventas.GONDOLA_NOMBRE,"INDEFINIDO") as DESCRI_G'))
        ->where('USER_ID','=',$user)
        ->where('ID_SUCURSAL','=',$sucursal)
        ->where('Temp_ventas.CREDITO_COBRADO','=',0)
        ->where('temp_ventas.vendedor','=',1)
        ->GROUPBY('temp_ventas.SECCION','temp_ventas.GONDOLA') 
        ->orderby('temp_ventas.SECCION')
        ->get()
        ->toArray();
         
        foreach ($temp as $key => $value) {
            
            $secciones_gondola_array[] = array(
                'SECCION' => $value->SECCION,
                'DESCRI_S' => $value->DESCRI_S,
                'GONDOLA' => $value->GONDOLA,
                'DESCRI_G' => $value->DESCRI_G,
                'VENDIDO' => $value->VENDIDO,
                'COSTO_TOTAL' => $value->COSTO_TOTAL,
                'TOTAL' => $value->TOTAL,
                'DESCUENTO' => $value->DESCUENTO,
                'PORCENTAJE'=> ($value->TOTAL * 100) / $total_porcentaje
            );
        }

        /*  --------------------------------------------------------------------------------- */  
        //TRAER TODOS LOS PRODUCTOS CON EL CODIGO DE MARCA
        /*  --------------------------------------------------------------------------------- */
                   
        $temp = DB::connection('retail')->table('temp_ventas')->select(
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
            DB::raw('IFNULL(temp_ventas.LINEA_CODIGO,0) AS LINEA_CODIGO'),
            DB::raw('temp_ventas.SUBCATEGORIA AS SUBCATEGORIA'),
            DB::raw('temp_ventas.MARCA AS MARCA'),
            DB::raw('temp_ventas.MARCAS_CODIGO AS MARCAS_CODIGO'),
            DB::raw('temp_ventas.SECCION AS SECCION'),
            DB::raw('temp_ventas.SECCION_CODIGO AS SECCION_CODIGO'),
            DB::raw('temp_ventas.DESCUENTO_PORCENTAJE AS DESCUENTO_PORCENTAJE'),
            DB::raw('temp_ventas.DESCUENTO_PRODUCTO AS DESCUENTO_PRODUCTO'))
        ->where('temp_ventas.ID_SUCURSAL','=',$sucursal)
        ->where('temp_ventas.USER_ID','=',$user)
        ->GROUPBY('temp_ventas.COD_PROD','temp_ventas.LOTE','temp_ventas.DESCUENTO_PRODUCTO') 
        ->orderby('COD_PROD')
        ->get()
        ->toArray();

        $total_general = 0;
        $total_descuento = 0;
        $total_preciounit = 0;
        $cantidadvendida = 0;
        $costo = 0;
        $totalcosto = 0;
           
        foreach ($temp as $key => $value) {

            if($value->TOTAL == 0){
                $value->TOTAL = '0';
            }
            if($value->PRECIO_UNIT == 0){
                $value->PRECIO_UNIT = '0';
            }
            if($value->STOCK == 0){
                $value->STOCK = '0';
            }
            if($value->DESCUENTO == 0){
                $value->DESCUENTO = '0';
            }
                   
            $total_general = $total_general + $value->TOTAL;
            $total_descuento = $total_descuento + $value->DESCUENTO;
            $total_preciounit = $total_preciounit + $value->PRECIO_UNIT;
            $cantidadvendida = $cantidadvendida + $value->VENDIDO;
            $costo = $costo + $value->COSTO_UNIT;
            $totalcosto = $totalcosto + $value->COSTO_TOTAL;
                  
            $secciones_productos_array[] = array(
                'COD_PROD' => $value->COD_PROD,
                'LOTE' => $value->LOTE,
                'STOCK' => $value->STOCK,
                'CATEGORIA' => $value->CATEGORIA,
                'SUBCATEGORIA' => $value->SUBCATEGORIA,
                'MARCA' => $value->MARCA,
                'VENDIDO' => $value->VENDIDO,
                'PRECIO_UNIT' => $value->PRECIO_UNIT,
                'TOTAL' => $value->TOTAL,
                'DESCUENTO' => $value->DESCUENTO,
                'COSTO_UNIT' => $value->COSTO_UNIT,
                'COSTO_TOTAL' => $value->COSTO_TOTAL,
                'DESCUENTO_PORCENTAJE' => $value->DESCUENTO_PRODUCTO,
                'MARCAS_CODIGO' => $value->MARCAS_CODIGO,
                'LINEAS_CODIGO' => $value->LINEA_CODIGO,
                'SECCION' => $value->SECCION,
                'SECCION_CODIGO' => $value->SECCION_CODIGO
            );
        }

        /*  --------------------------------------------------------------------------------- */
        // RETORNAR TODOS LOS ARRAYS

        return ['ventas' => $secciones_productos_array, 'secciones' => $secciones_array, 'gondolas' => $secciones_gondola_array];
    }
}
