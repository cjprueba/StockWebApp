<?php

namespace App\Exports;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
USE Illuminate\Support\Facades\DB;
use App\Exports\RptTransferenciaVenta;
use App\Exports\RptTransferenciaVentaTotales;
USE Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use DateTime;
use App\Temp_venta;

class RptTransferenciaVentaExport implements WithMultipleSheets
{
  private $marca;
  private $descuentogeneral;
  private $descuento;
  private $calculo;
  private $calculos;
  private $ventageneral;
  private $linea=[];
  private $nullsheets;
  private $sheets = [];
  private $marcas=[];
  private $hojas=1;
  private $inicio;
  private $final;
  private $checkedProveedor;
  private $insert;
  private $checkedSucursal;
  private $sucursal;
  private $agrupar;

   private $vales = array("v", "a", "l", "e", "%");
         use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
 public function __construct($datos)
    {
     
        $this->inicio = date('Y-m-d', strtotime($datos['Inicio']));
        $this->final  =  date('Y-m-d', strtotime($datos['Final']));
        $this->sucursal = $datos['Sucursal'];
        $this->checkedProveedor= $datos['AllProveedores'];
        $this->checkedSucursal=$datos['AllSucursales'];
        $this->Proveedores=$datos['Proveedores'];
        $this->sucursales=$datos['SucursalOrigen'];
        $this->insert=$datos['Insert'];
        $this->agrupar=$datos["Agrupar"];
    }
    public function sheets(): array
    {
    	$user = auth()->user();
    	if($this->insert===true){
    		$datos=array(
    			'inicio'=>$this->inicio,
    			'final'=>$this->final,
    			'sucursal'=>$this->sucursal,
    			'checkedProveedor'=>$this->checkedProveedor,
    			'checkedSucursal'=>$this->checkedSucursal,
    			'proveedores'=>$this->Proveedores,
    			'sucursales'=>$this->sucursales
    		);
           Temp_venta::insertar_transferencia_reporte($datos);
    	}
    	        
if($this->hojas==1){

	
				              
                    $datos=array(
		                 
		                'HOJAS'=> $this->hojas,
		                'PROVEEDOR_CODIGO'=> '',
		                'DESCRI_P'=> '',
		                'SUCURSAL_CODIGO'=> '',
		                'DESCRI_S'=> '',
		                'INICIO'=> $this->inicio,
		                'FINAL'=> $this->final,
		                'SUCURSAL'=>$this->sucursal,
		                'USER_ID'=>$user->id,
		                'AGRUPAR'=>$this->agrupar

		            );


         /* $this->sheets[]= new RptTransferenciaVenta($datos);*/
          $this->hojas=$this->hojas+1;
     }
                         $datos=array(
		                 
		                'HOJAS'=> $this->hojas,
		                'PROVEEDOR_CODIGO'=> '',
		                'DESCRI_P'=> '',
		                'SUCURSAL_CODIGO'=> '',
		                'DESCRI_S'=> '',
		                'INICIO'=> $this->inicio,
		                'FINAL'=> $this->final,
		                'SUCURSAL'=>$this->sucursal,
		                'USER_ID'=>$user->id,
		                'AGRUPAR'=>$this->agrupar

		            );
         $this->sheets[]= new RptTransferenciaVentaTotales($datos);

/*     	 $temp=DB::connection('retail')->table('temp_ventas')
	   	
	           ->select(
	            DB::raw('temp_ventas.COD_PROD'))
	             ->WHERE('temp_ventas.PROVEEDOR','=',19)
	           ->where('USER_ID','=',$user->id)
	                  ->where('ID_SUCURSAL','=',$this->sucursal)
	         ->LIMIT(1)
	          ->get()
	          ->toArray();
	          if(count($temp)>0){
	          	$datos=array(
		                 
		                'HOJAS'=> 3,
		                'MARCA_CODIGO'=> '',
		                'DESCRI_M'=> '',
		                'LINEA_CODIGO'=> '',
		                'DESCRI_L'=> '',
		                'INICIO'=> $this->inicio,
		                'FINAL'=> $this->final,
		                'SUCURSAL'=>$this->sucursal,
		                'USER_ID'=>$user->id

		            );
	          	  $this->sheets[]= new RptVentaMarca($datos);
	          }*/
	          $datos=array(
		                 
		                'HOJAS'=> $this->hojas,
		                'PROVEEDOR_CODIGO'=> '',
		                'DESCRI_P'=> '',
		                'SUCURSAL_CODIGO'=> '',
		                'DESCRI_S'=> '',
		                'INICIO'=> $this->inicio,
		                'FINAL'=> $this->final,
		                'SUCURSAL'=>$this->sucursal,
		                'USER_ID'=>$user->id,
		                 'AGRUPAR'=>$this->agrupar

		            );

         	 $temp=DB::connection('retail')->table('temp_ventas')
	   	
	           ->select(
	            DB::raw('temp_ventas.PROVEEDOR AS PROVEEDOR'),
	            DB::raw('temp_ventas.PROVEEDOR_NOMBRE AS DESCRI_P'),
	            DB::raw('temp_ventas.SUCURSAL_ORIGEN AS SUCURSAL_ORIGEN'),
	            DB::raw('temp_ventas.SUCURSAL_NOMBRE as DESCRI_S'))
	            
	              ->where('USER_ID','=',$user->id)
	                  ->where('ID_SUCURSAL','=',$this->sucursal)
	          /*->GROUPBY('temp_ventas.MARCAS_CODIGO','temp_ventas.LINEA_CODIGO') */
	          ->orderby('temp_ventas.PROVEEDOR');
	          if($this->agrupar==0){
                $temp->GROUPBY('temp_ventas.PROVEEDOR');
	          }else{
				$temp->GROUPBY('temp_ventas.SUCURSAL_ORIGEN');
	          }
	          $temp=$temp->get()->toArray();
	          foreach ($temp as $key => $value) {
                    $datos=array(
		                 
		                'HOJAS'=> $this->hojas,
		                'PROVEEDOR_CODIGO'=> $value->PROVEEDOR,
		                'DESCRI_P'=> $value->DESCRI_P,
		                'SUCURSAL_CODIGO'=> $value->SUCURSAL_ORIGEN,
		                'DESCRI_S'=> $value->DESCRI_S,
		                'INICIO'=> $this->inicio,
		                'FINAL'=> $this->final,
		                'SUCURSAL'=>$this->sucursal,
		                'USER_ID'=>$user->id,
		                'AGRUPAR'=>$this->agrupar

		            );

	          	  $this->sheets[]= new RptTransferenciaVenta($datos);
	          	# code...
	          }


	          return $this->sheets;

          
}
}
