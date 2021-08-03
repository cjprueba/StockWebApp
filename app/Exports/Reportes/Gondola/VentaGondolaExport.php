<?php

namespace App\Exports\Reportes\Gondola;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
USE Illuminate\Support\Facades\DB;
use App\Exports\Reportes\Gondola\VentaGondolaTotales;
use App\Exports\Reportes\Gondola\VentaGondola;
USE Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use DateTime;

use Maatwebsite\Excel\Concerns\FromCollection;
class VentaGondolaExport implements WithMultipleSheets
{
  private $sheets = [];
  private $hojas=1;
  private $inicio;
  private $final;
  private $sucursal;
  private $checkedSeccion;
  private $checkedGondola;
  private $gondolas;
  private $secciones;
  private $mayoristaContado;
  private $mayoristaCredito;
  private $servicioDelivery;

  /**
    * @return \Illuminate\Support\Collection
  */
   use Exportable;
public function __construct($datos)
    {
     
        $this->inicio = date('Y-m-d', strtotime($datos['Inicio']));
        $this->final  =  date('Y-m-d', strtotime($datos['Final']));
        $this->sucursal = $datos['Sucursal'];
        $this->checkedGondola= $datos['AllGondolas'];
        $this->checkedSeccion= $datos['AllSecciones'];
        $this->gondolas=$datos['Gondolas'];
        $this->secciones=$datos['secciones'];
        $this->mayoristaContado=$datos['MayoristaContado'];
        $this->mayoristaCredito=$datos['MayoristaCredito'];
        $this->servicioDelivery=$datos['ServicioDelivery'];
       
    }
    public function sheets(): array
    { 
    		  $user = auth()->user();
    		  $datos=array (
    		  	'sucursal'=>$this->sucursal,
    		  	'inicio'=>$this->inicio,
    		  	'final'=>$this->final,
    		  	'mayoristaContado'=>$this->mayoristaContado,
    		  	'mayoristaCredito'=>$this->mayoristaCredito,
    		  	'servicioDelivery'=>$this->servicioDelivery
    		  );
     		  $this->sheets[]= new VentaGondolaTotales($datos);

	               $temp=DB::connection('retail')->table('temp_ventas')
		   	
		           ->select(
		            DB::raw('temp_ventas.SECCION_CODIGO AS SECCION'),
		            DB::raw('IFNULL(temp_ventas.SECCION,"INDEFINIDO") as DESCRI_S'))	
		           ->where('temp_ventas.ID_SUCURSAL','=',$this->sucursal)
		           ->where('temp_ventas.USER_ID','=',$user->id)
		           ->where('temp_ventas.VENDEDOR','=',1)
		           ->WHERE('TEMP_VENTAS.CREDITO_COBRADO','=',0)
		          ->GROUPBY('temp_ventas.SECCION_CODIGO') 
		          ->orderby('temp_ventas.SECCION_CODIGO')
		          ->get()
		          ->toArray();
		          	   $TOTALG=DB::connection('retail')->table('temp_ventas')
			           ->select(
			            DB::raw('SUM(temp_ventas.VENDIDO) AS VENDIDO'),
			            DB::raw('SUM(temp_ventas.DESCUENTO) AS DESCUENTO'),
			            DB::raw('SUM(COSTO_TOTAL) AS COSTO_TOTAL'),
			            DB::raw('SUM(COSTO_UNIT) AS COSTO_UNIT'),
			            DB::raw('SUM(temp_ventas.PRECIO) AS TOTAL'),
			            DB::raw('SUM(temp_ventas.PRECIO_UNIT) AS PRECIO_UNIT'))
			           ->where('USER_ID','=',$user->id)
			           ->where('ID_SUCURSAL','=',$this->sucursal)
			          ->get()
			          ->toArray();

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
			           
		             ->Where('VENTAS_ANULADO.ANULADO','<>',1)
		             ->Where('VENTAS.ID_SUCURSAL','=',$this->sucursal)
		             ->whereBetween('VENTAS.FECALTAS', [$this->inicio, $this->final])
			         ->get()
			         ->toArray();

			         if(count($ser)>0){
			         	$total_porcentaje=$TOTALG[0]->TOTAL+$ser[0]->PRECIO_SERVICIO;
			         }else{
			         	$total_porcentaje=$TOTALG[0]->TOTAL;
			         }
	          foreach ($temp as $key => $value) {
	          	  $this->sheets[]= new VentaGondola($datos,$value->SECCION,$value->DESCRI_S,$total_porcentaje);
	          
	            }
	                return $this->sheets;
    }

}
