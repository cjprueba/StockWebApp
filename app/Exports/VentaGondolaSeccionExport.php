<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
USE Illuminate\Support\Facades\DB;
use App\Exports\VentaGondolaSeccion;
use App\Exports\VentaGondolaSeccionTotales;
USE Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use DateTime;

use Maatwebsite\Excel\Concerns\FromCollection;

class VentaGondolaSeccionExport implements WithMultipleSheets
{
  private $marca;
  private $descuentogeneral;
  private $descuento;
  private $calculo;
  private $calculos;
  private $ventageneral;
  private $linea;
  private $nullsheets;
  private $sheets = [];
  private $hojas=1;
  private $inicio;
  private $final;
  private $sucursal;

    /**
    * @return \Illuminate\Support\Collection
    */
        public function __construct()
    {
        
    }
        public function sheets(): array
    {
$dia1='2020-11-01';
$dia2='2020-11-30';
    	      /*$dia2 = date("Y-m-d");
              $dia1 = new DateTime();
              $dia1=  $dia1->modify('first day of this month');
              $dia1= $dia1->format('Y-m-d');*/
if($this->hojas==1){
	DB::connection('retail')->table('temp_ventas')->truncate();

	   $reporte=DB::connection('retail')->table('VENTASDET')
           
            ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
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
              ->leftjoin('GONDOLA_TIENE_PRODUCTOS',function($join){
             $join->on('GONDOLA_TIENE_PRODUCTOS.GONDOLA_COD_PROD','=','VENTASDET.COD_PROD')
               ->on('GONDOLA_TIENE_PRODUCTOS.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL');
         })
           ->leftjoin('GONDOLAS','GONDOLAS.ID','=','GONDOLA_TIENE_PRODUCTOS.ID_GONDOLA')
           ->leftjoin('GONDOLA_TIENE_SECCION','GONDOLA_TIENE_SECCION.ID_GONDOLA','=','GONDOLAS.ID')
           ->leftjoin('SECCIONES','SECCIONES.ID','=','GONDOLA_TIENE_SECCION.ID_SECCION')
           
          
           ->leftjoin('VENTAS',function($join){
             $join->on('VENTAS.CODIGO','=','VENTASDET.CODIGO')
             ->on('VENTAS.CAJA','=','VENTASDET.CAJA')
               ->on('VENTAS.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL');
         })
             ->leftjoin('VENTAS_DESCUENTO', 'VENTAS_DESCUENTO.FK_VENTAS', '=', 'VENTAS.ID')
            ->select(
            DB::raw('VENTASDET.COD_PROD AS COD_PROD,
            	VENTASDET.CODIGO,
             VENTASDET_TIENE_LOTES.CANTIDAD AS VENDIDO,
             MARCA.DESCRIPCION AS MARCA,
             LOTES.LOTE AS LOTE,
             LOTES.COSTO AS COSTO_UNIT,
             (VENTASDET_TIENE_LOTES.CANTIDAD*LOTES.COSTO) AS COSTO_TOTAL,
             LINEAS.DESCRIPCION AS CATEGORIA,
             SUBLINEAS.DESCRIPCION AS SUBCATEGORIA,
             SUBLINEA_DET.DESCRIPCION AS NOMBRE,
             PRODUCTOS_AUX.PROVEEDOR AS PROVEEDOR,
             PROVEEDORES.NOMBRE AS PROVEEDOR_NOMBRE,
             GONDOLAS.DESCRIPCION AS GONDOLA_NOMBRE,
             SECCIONES.DESCRIPCION AS SECCION,
             VENTAS.ID AS ID,
             (VENTASDET.PRECIO_UNIT*VENTASDET_TIENE_LOTES.CANTIDAD) AS PRECIO,
             VENTASDET.PRECIO_UNIT AS PRECIO_UNIT'),
            DB::raw('IFNULL(VENTASDET_DESCUENTO.TOTAL,0) AS DESCUENTO'),
            DB::raw('IFNULL(GONDOLAS.ID,0) AS GONDOLA'),
            DB::raw('IFNULL(SECCIONES.ID,0) AS SECCION_CODIGO'),

            DB::raw('IFNULL(VENTAS_DESCUENTO.PORCENTAJE,0) AS PORCENTAJE_GENERAL'),
            DB::raw('IFNULL(MARCA.CODIGO,0) AS MARCA_CODIGO'),
            DB::raw('IFNULL(LINEAS.CODIGO,0) AS LINEA_CODIGO'),
            DB::raw('IFNULL(SUBLINEAS.CODIGO,0) AS SUBLINEA_CODIGO'),
           
            DB::raw('IFNULL(VENTASDET_DESCUENTO.PORCENTAJE,0) AS DESCUENTO_PORCENTAJE'))

        ->Where('VENTASDET.ANULADO','=',0)
        /*->Where('VENTAS.VENDEDOR','=',1)*/
       /* ->Where('VENTAS.TIPO','<>','CR')*/
        
      
        ->whereBetween('VENTASDET.FECALTAS', [$dia1, $dia2])
        ->Where('VENTASDET.ID_SUCURSAL','=',9) 
        ->orderby('VENTASDET.COD_PROD')

                ->get()
                ->toArray();
		         $precio=100;
		        $descuento_precio=0;
		        $precio_descontado=0;
		        $costo=0;
		        $total_dev=0;
		        $total_des=0;
		        $descuento=0;
		        $descuento_general=0;
		        $precio_descontado_general=0;
		      	$precio_descontado_total=0;
		      	$descuento_real=0;
		     $venta_in = array();
		     

			foreach ($reporte as $key=>$value ) {


				            if($value->VENDIDO>0){


				             if ($value->PORCENTAJE_GENERAL>0 ) {
				             	$descuento_precio=round((($value->PRECIO*$value->PORCENTAJE_GENERAL)/100),2);
				             	$total_des=$total_des+$descuento_precio;
				             	$value->PRECIO=(round($value->PRECIO-$descuento_precio,2));
				             	$value->DESCUENTO=($value->DESCUENTO+round($descuento_precio,2));
				             	$value->PRECIO_UNIT=round((($value->PRECIO_UNIT*$value->PORCENTAJE_GENERAL)/100),2);

				               $descuento=($precio*$value->DESCUENTO_PORCENTAJE)/100;
				               $precio_descontado=$precio-$descuento;
				               $descuento_general=($precio_descontado*$value->PORCENTAJE_GENERAL)/100;
				               $precio_descontado_general=$precio_descontado-$descuento_general;
				               $precio_descontado_total=$descuento+$descuento_general;
				               $descuento_real=($precio_descontado_total*100)/$precio;
				               $value->DESCUENTO_PORCENTAJE=$descuento_real;
				                
				             }
				               
				             if($value->DESCUENTO_PORCENTAJE==0){
				             	$value->DESCUENTO_PORCENTAJE='0';
				             }

				            if($value->NOMBRE==NULL){
				                $value->NOMBRE='';
				             }
				              if($value->MARCA==NULL){
				                $value->MARCA='';
				             }
				             if($value->GONDOLA_NOMBRE==NULL){
				             	$value->GONDOLA_NOMBRE='INDEFINIDO';
				             }
				             if($value->SECCION==NULL){
				             	$value->SECCION='INDEFINIDO';
				             }

				                       
							        $nestedData['COD_PROD'] = $value->COD_PROD;
									$nestedData['VENDIDO'] =$value->VENDIDO;
				              		$nestedData['CATEGORIA'] =$value->CATEGORIA;
				              		$nestedData['SUBCATEGORIA']=$value->SUBCATEGORIA;
				              		$nestedData['NOMBRE']=$value->NOMBRE;
				              		$nestedData['DESCUENTO_PORCENTAJE']=$value->PORCENTAJE_GENERAL;
				              		$nestedData['DESCUENTO_PRODUCTO']=$value->DESCUENTO_PORCENTAJE;
				              		$nestedData['PRECIO']= $value->PRECIO;
				              		$nestedData['PRECIO_UNIT']= $value->PRECIO_UNIT;
				              		$nestedData['COSTO_UNIT']= $value->COSTO_UNIT;
				              		$nestedData['COSTO_TOTAL']= $value->COSTO_TOTAL;
				              		$nestedData['DESCUENTO']= $value->DESCUENTO;
				              		$nestedData['MARCAS_CODIGO']= $value->MARCA_CODIGO;
				              		$nestedData['LINEA_CODIGO']=$value->LINEA_CODIGO;
				              		$nestedData['SUBLINEA_CODIGO']= $value->SUBLINEA_CODIGO;
				              		$nestedData['PROVEEDOR']= $value->PROVEEDOR;
				              		$nestedData['PROVEEDOR_NOMBRE']= $value->PROVEEDOR_NOMBRE;
				              		$nestedData['GONDOLA']= $value->GONDOLA;
				              		$nestedData['GONDOLA_NOMBRE']= $value->GONDOLA_NOMBRE;
				              		$nestedData['SECCION']= $value->SECCION;
				              		$nestedData['SECCION_CODIGO']= $value->SECCION_CODIGO;
				              		$nestedData['MARCA']= $value->MARCA;
				              		$nestedData['LOTE']= $value->LOTE;
							    

				             

                             $venta_in[]=$nestedData;
				              	
							}
		              
		           # code...

		        };
		      
		         log::error(["des"=>$total_des]);
		           foreach (array_chunk($venta_in,1000) as $t) {

                              	DB::connection('retail')->table('temp_ventas')->insert($t);


                            }
                              $venta_in = array();


	   $reporte=DB::connection('retail')->table('VENTASDET_DEVOLUCIONES')
           
            ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET_DEVOLUCIONES.COD_PROD')
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
            ->leftjoin('PRODUCTOS_AUX',function($join){
             $join->on('PRODUCTOS_AUX.CODIGO','=','VENTASDET_DEVOLUCIONES.COD_PROD')
               ->on('PRODUCTOS_AUX.ID_SUCURSAL','=','VENTASDET_DEVOLUCIONES.ID_SUCURSAL');
         })
           
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
             ->leftjoin('VENTASDET', 'VENTASDET.ID', '=', 'VENTASDET_DEVOLUCIONES.FK_VENTASDET')
            ->leftjoin('VENTASDET_TIENE_LOTES', 'VENTASDET_TIENE_LOTES.ID_VENTAS_DET', '=', 'VENTASDET.ID')
            ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'PRODUCTOS_AUX.PROVEEDOR')
            ->leftjoin('SUBLINEAS', 'SUBLINEAS.CODIGO', '=', 'PRODUCTOS.SUBLINEA')
            ->leftjoin('LOTES', 'LOTES.ID', '=', 'VENTASDET_TIENE_LOTES.ID_LOTE')
            ->leftjoin('SUBLINEA_DET', 'SUBLINEA_DET.CODIGO', '=', 'PRODUCTOS.SUBLINEADET')
            ->leftjoin('GONDOLA_TIENE_PRODUCTOS',function($join){
             $join->on('GONDOLA_TIENE_PRODUCTOS.GONDOLA_COD_PROD','=','VENTASDET.COD_PROD')
               ->on('GONDOLA_TIENE_PRODUCTOS.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL');
         })
             ->leftjoin('VENTAS',function($join){
             $join->on('VENTAS.CODIGO','=','VENTASDET.CODIGO')
             ->on('VENTAS.CAJA','=','VENTASDET.CAJA')
               ->on('VENTAS.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL');
         })
           ->leftjoin('GONDOLAS','GONDOLAS.ID','=','GONDOLA_TIENE_PRODUCTOS.ID_GONDOLA')
           ->leftjoin('GONDOLA_TIENE_SECCION','GONDOLA_TIENE_SECCION.ID_GONDOLA','=','GONDOLAS.ID')
           ->leftjoin('SECCIONES','SECCIONES.ID','=','GONDOLA_TIENE_SECCION.ID_SECCION')
           
          
          
           
            ->select(
            DB::raw('VENTASDET_DEVOLUCIONES.COD_PROD AS COD_PROD,
            	VENTASDET.CODIGO,
             VENTASDET_DEVOLUCIONES.CANTIDAD AS VENDIDO,
             MARCA.DESCRIPCION AS MARCA,
             LOTES.LOTE AS LOTE,
             LOTES.COSTO AS COSTO_UNIT,
             (VENTASDET_DEVOLUCIONES.CANTIDAD*LOTES.COSTO) AS COSTO_TOTAL,
             LINEAS.DESCRIPCION AS CATEGORIA,
             SUBLINEAS.DESCRIPCION AS SUBCATEGORIA,
             SUBLINEA_DET.DESCRIPCION AS NOMBRE,
             PRODUCTOS_AUX.PROVEEDOR AS PROVEEDOR,
             PROVEEDORES.NOMBRE AS PROVEEDOR_NOMBRE,
             GONDOLAS.DESCRIPCION AS GONDOLA_NOMBRE,
             SECCIONES.DESCRIPCION AS SECCION,
             (VENTASDET_DEVOLUCIONES.PRECIO_UNIT*VENTASDET_DEVOLUCIONES.CANTIDAD) AS PRECIO,
             VENTASDET_DEVOLUCIONES.PRECIO_UNIT AS PRECIO_UNIT'),
            DB::raw('IFNULL(MARCA.CODIGO,0) AS MARCA_CODIGO'),
            DB::raw('IFNULL(GONDOLAS.ID,0) AS GONDOLA'),
            DB::raw('IFNULL(SECCIONES.ID,0) AS SECCION_CODIGO'),
            DB::raw('IFNULL(LINEAS.CODIGO,0) AS LINEA_CODIGO'),
            DB::raw('IFNULL(SUBLINEAS.CODIGO,0) AS SUBLINEA_CODIGO'))

        ->Where('VENTASDET.ANULADO','=',0)
        /* ->Where('VENTAS.TIPO','<>','CR')*/
    
        ->whereBetween('VENTASDET_DEVOLUCIONES.FECALTAS', [$dia1, $dia2])
        ->Where('VENTASDET_DEVOLUCIONES.ID_SUCURSAL','=',9) 
          /*->Where('VENTAS.VENDEDOR','=',1)*/
        
        ->orderby('VENTASDET.COD_PROD')
               ->get()
                ->toArray();
         foreach ($reporte as $key => $value) {
				               
				            
				            if($value->NOMBRE==NULL){
				                $value->NOMBRE='';
				             }
				              if($value->MARCA==NULL){
				                $value->MARCA='';
				             }
				             if($value->GONDOLA_NOMBRE==NULL){
				             	$value->GONDOLA_NOMBRE='INDEFINIDO';
				             }
				             if($value->SECCION==NULL){
				             	$value->SECCION='INDEFINIDO';
				             }
         	  		$nestedDataS['COD_PROD'] = $value->COD_PROD;
					$nestedDataS['VENDIDO'] =-$value->VENDIDO;
				    $nestedDataS['CATEGORIA'] =$value->CATEGORIA;
            		$nestedDataS['SUBCATEGORIA']=$value->SUBCATEGORIA;
				    $nestedDataS['NOMBRE']='DEVOLUCION:'.$value->NOMBRE;
				    $nestedDataS['DESCUENTO_PORCENTAJE']=0;
				    $nestedDataS['DESCUENTO_PRODUCTO']=0;
				    $nestedDataS['PRECIO']= $value->PRECIO;
				    $nestedDataS['PRECIO_UNIT']= $value->PRECIO_UNIT;
				    $nestedDataS['COSTO_UNIT']= $value->COSTO_UNIT;
				    $nestedDataS['COSTO_TOTAL']= $value->COSTO_TOTAL;
				    $nestedDataS['DESCUENTO']= 0;
				    $nestedDataS['MARCAS_CODIGO']= $value->MARCA_CODIGO;
				    $nestedDataS['LINEA_CODIGO']=$value->LINEA_CODIGO;
				    $nestedDataS['SUBLINEA_CODIGO']= $value->SUBLINEA_CODIGO;
				    $nestedDataS['PROVEEDOR']= $value->PROVEEDOR;
				    $nestedDataS['PROVEEDOR_NOMBRE']= $value->PROVEEDOR_NOMBRE;
				    $nestedDataS['GONDOLA']= $value->GONDOLA;
				    $nestedDataS['GONDOLA_NOMBRE']= $value->GONDOLA_NOMBRE;
				    $nestedDataS['SECCION']= $value->SECCION;
				    $nestedDataS['SECCION_CODIGO']= $value->SECCION_CODIGO;
				    $nestedDataS['MARCA']= $value->MARCA;
				    $nestedDataS['LOTE']= $value->LOTE;
				     $venta_in[]=$nestedDataS;
         	# code...
         }
            $venta_nc = array();
                       foreach (array_chunk($venta_in,1000) as $t) {

                              	DB::connection('retail')->table('temp_ventas')->insert($t);


                            } 
            $venta_nc = array();
		  $reporte=DB::connection('retail')->table('NOTA_CREDITO_DET')
           
            ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'NOTA_CREDITO_DET.CODIGO_PROD')
            ->leftjoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
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
            ->leftjoin('GONDOLA_TIENE_PRODUCTOS',function($join){
             $join->on('GONDOLA_TIENE_PRODUCTOS.GONDOLA_COD_PROD','=','VENTASDET.COD_PROD')
               ->on('GONDOLA_TIENE_PRODUCTOS.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL');
         })
             ->leftjoin('VENTAS',function($join){
             $join->on('VENTAS.CODIGO','=','VENTASDET.CODIGO')
             ->on('VENTAS.CAJA','=','VENTASDET.CAJA')
               ->on('VENTAS.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL');
         })
            ->leftjoin('GONDOLAS','GONDOLAS.ID','=','GONDOLA_TIENE_PRODUCTOS.ID_GONDOLA')
            ->leftjoin('GONDOLA_TIENE_SECCION','GONDOLA_TIENE_SECCION.ID_GONDOLA','=','GONDOLAS.ID')
            ->leftjoin('SECCIONES','SECCIONES.ID','=','GONDOLA_TIENE_SECCION.ID_SECCION')

            ->select(
            DB::raw('NOTA_CREDITO_DET.CODIGO_PROD AS COD_PROD,
              VENTASDET.CODIGO,
             nota_credito_tiene_lote.CANTIDAD AS VENDIDO,
             MARCA.DESCRIPCION AS MARCA,
             LOTES.LOTE AS LOTE,
             LOTES.COSTO AS COSTO_UNIT,
             (nota_credito_tiene_lote.CANTIDAD*LOTES.COSTO) AS COSTO_TOTAL,
             LINEAS.DESCRIPCION AS CATEGORIA,
             SUBLINEAS.DESCRIPCION AS SUBCATEGORIA,
             SUBLINEA_DET.DESCRIPCION AS NOMBRE,
             PRODUCTOS_AUX.PROVEEDOR AS PROVEEDOR,
             PROVEEDORES.NOMBRE AS PROVEEDOR_NOMBRE,
             GONDOLAS.DESCRIPCION AS GONDOLA_NOMBRE,
             SECCIONES.DESCRIPCION AS SECCION,
            (nota_credito_tiene_lote.CANTIDAD*NOTA_CREDITO_DET.PRECIO) AS PRECIO,
             NOTA_CREDITO_DET.PRECIO AS PRECIO_UNIT'),
            DB::raw('IFNULL(MARCA.CODIGO,0) AS MARCA_CODIGO'),
            DB::raw('IFNULL(LINEAS.CODIGO,0) AS LINEA_CODIGO'),
            DB::raw('IFNULL(GONDOLAS.ID,0) AS GONDOLA'),
            DB::raw('IFNULL(SECCIONES.ID,0) AS SECCION_CODIGO'),
            DB::raw('IFNULL(SUBLINEAS.CODIGO,0) AS SUBLINEA_CODIGO'))

        ->Where('VENTASDET.ANULADO','<>',1)
        ->Where('NOTA_CREDITO.PROCESADO','=',1)
         /*->Where('VENTAS.VENDEDOR','=',1)*/
        /* ->Where('VENTAS.TIPO','<>','CR')*/
      
        ->whereBetween('NOTA_CREDITO_DET.FECALTAS', [$dia1, $dia2])
        ->Where('NOTA_CREDITO_DET.ID_SUCURSAL','=',9) 
        ->orderby('VENTASDET.COD_PROD')
               ->get()
                ->toArray();
         foreach ($reporte as $key => $value) {

				            
				            if($value->NOMBRE==NULL){
				                $value->NOMBRE='';
				             }
				              if($value->MARCA==NULL){
				                $value->MARCA='';
				             }
				             if($value->GONDOLA_NOMBRE==NULL){
				             	$value->GONDOLA_NOMBRE='INDEFINIDO';
				             }
				             if($value->SECCION==NULL){
				             	$value->SECCION='INDEFINIDO';
				             }
            $nestedDataNC['COD_PROD'] = $value->COD_PROD;
            $nestedDataNC['VENDIDO'] =-$value->VENDIDO;
            $nestedDataNC['CATEGORIA'] =$value->CATEGORIA;
            $nestedDataNC['SUBCATEGORIA']=$value->SUBCATEGORIA;
            $nestedDataNC['NOMBRE']='DEVOLUCION NC:'.$value->NOMBRE;
            $nestedDataNC['DESCUENTO_PORCENTAJE']=0;
            $nestedDataNC['DESCUENTO_PRODUCTO']=0;
            $nestedDataNC['PRECIO']= $value->PRECIO*-1;
            $nestedDataNC['PRECIO_UNIT']= $value->PRECIO_UNIT*-1;
            $nestedDataNC['COSTO_UNIT']= $value->COSTO_UNIT*-1;
            $nestedDataNC['COSTO_TOTAL']= $value->COSTO_TOTAL*-1;
            $nestedDataNC['DESCUENTO']= 0;
            $nestedDataNC['MARCAS_CODIGO']= $value->MARCA_CODIGO;
            $nestedDataNC['LINEA_CODIGO']=$value->LINEA_CODIGO;
            $nestedDataNC['SUBLINEA_CODIGO']= $value->SUBLINEA_CODIGO;
            $nestedDataNC['PROVEEDOR']= $value->PROVEEDOR;
            $nestedDataNC['PROVEEDOR_NOMBRE']= $value->PROVEEDOR_NOMBRE;
            $nestedDataNC['GONDOLA']= $value->GONDOLA;
	        $nestedDataNC['GONDOLA_NOMBRE']= $value->GONDOLA_NOMBRE;
	        $nestedDataNC['SECCION']= $value->SECCION;
	        $nestedDataNC['SECCION_CODIGO']= $value->SECCION_CODIGO;
            $nestedDataNC['MARCA']= $value->MARCA;
            $nestedDataNC['LOTE']= $value->LOTE;
             $venta_nc[]=$nestedDataNC;
        
         }
                       foreach (array_chunk($venta_nc,1000) as $t) {

                                DB::connection('retail')->table('temp_ventas')->insert($t);


                            }

                $this->sheets[]= new VentaGondolaSeccionTotales();
               $temp=DB::connection('retail')->table('temp_ventas')
	   	
	           ->select(
	            DB::raw('temp_ventas.SECCION_CODIGO AS SECCION'),
	            DB::raw('temp_ventas.SECCION as DESCRI_S'))	
	          ->GROUPBY('temp_ventas.SECCION_CODIGO') 
	          ->orderby('temp_ventas.SECCION_CODIGO')
	          ->get()
	          ->toArray();
	          foreach ($temp as $key => $value) {
				             if($value->DESCRI_S==NULL){
				             	$value->DESCRI_S='INDEFINIDO';
				             }
	          	  $this->sheets[]= new VentaGondolaSeccion(1,$value->SECCION,$value->DESCRI_S);
	          	# code...
	          }



         
          $this->hojas=$this->hojas+1;

}
       /*  $this->sheets[]= new VentaMensualTotales();*/

          /*$temp=DB::connection('retail')->table('temp_ventas')
	   	
	           ->select(
	            DB::raw('temp_ventas.PROVEEDOR AS PROVEEDOR'),
	            DB::raw('temp_ventas.PROVEEDOR_NOMBRE as DESCRI_P'))
	       
	          ->GROUPBY('temp_ventas.PROVEEDOR') 
	          ->get()
	          ->toArray();
	          
	          

	          foreach ($temp as $key => $value) {
	
	          	$this->sheets[]= new VentaMensual(2,"reporte",$value->PROVEEDOR,$value->DESCRI_P);
	          	# code...
	          }*/


	          return $this->sheets;
	          
  
            


          
}

}
