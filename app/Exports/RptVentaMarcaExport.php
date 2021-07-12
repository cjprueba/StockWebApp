<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
USE Illuminate\Support\Facades\DB;
use App\Exports\RptVentaMarca;
use App\Exports\RptVentaMarcaTotales;
USE Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use DateTime;
use App\Temp_venta;

use Maatwebsite\Excel\Concerns\FromCollection;

class RptVentaMarcaExport implements WithMultipleSheets
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
  private $checkedMarca;
  private $insert;
  private $checkedCategoria;
  private $checkedProveedor;
  private $proveedores;
  private $sucursal;

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
        $this->checkedMarca= $datos['AllBrand'];
        $this->checkedCategoria=$datos['AllCategory'];
        $this->checkedProveedor=$datos['AllProveedores'];
        $this->proveedores=$datos['Proveedores'];
        $this->marcas=$datos['Marcas'];
        $this->linea=$datos['Categorias'];
        $this->insert=$datos['Insert'];
    }
   public function sheets(): array
    {
    	$user = auth()->user();
    	if($this->insert===true){
    		$datos=array(
    			'inicio'=>$this->inicio,
    			'final'=>$this->final,
    			'sucursal'=>$this->sucursal,
    			'checkedCategoria'=>$this->checkedCategoria,
    			'checkedMarca'=>$this->checkedMarca,
    			'checkedProveedor'=>$this->checkedProveedor,
    			'proveedores'=>$this->proveedores,
    			'marcas'=>$this->marcas,
    			'linea'=>$this->linea
    		);
           Temp_venta::insertar_reporte($datos);
    	}
    	        
if($this->hojas==1){

	
				              
                    $datos=array(
		                 
		                'HOJAS'=> $this->hojas,
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
          $this->hojas=$this->hojas+1;
     }
                         $datos=array(
		                 
		                'HOJAS'=> $this->hojas,
		                'MARCA_CODIGO'=> '',
		                'DESCRI_M'=> '',
		                'LINEA_CODIGO'=> '',
		                'DESCRI_L'=> '',
		                'INICIO'=> $this->inicio,
		                'FINAL'=> $this->final,
		                'SUCURSAL'=>$this->sucursal,
		                'USER_ID'=>$user->id

		            );
         $this->sheets[]= new RptVentaMarcaTotales($datos);

     	 $temp=DB::connection('retail')->table('temp_ventas')
	   	
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
	          }
	          $datos=array(
		                 
		                'HOJAS'=> $this->hojas,
		                'MARCA_CODIGO'=> '',
		                'DESCRI_M'=> '',
		                'LINEA_CODIGO'=> '',
		                'DESCRI_L'=> '',
		                'INICIO'=> $this->inicio,
		                'FINAL'=> $this->final,
		                'SUCURSAL'=>$this->sucursal,
		                'USER_ID'=>$user->id

		            );

         	 $temp=DB::connection('retail')->table('temp_ventas')
	   	
	           ->select(
	            DB::raw('temp_ventas.MARCAS_CODIGO AS MARCA'),
	            DB::raw('temp_ventas.MARCA AS DESCRI_M'),
	            DB::raw('temp_ventas.LINEA_CODIGO AS LINEA'),
	            DB::raw('temp_ventas.CATEGORIA as DESCRI_L'))
	             ->WHERE('temp_ventas.PROVEEDOR','<>',19)
	              ->where('USER_ID','=',$user->id)
	                  ->where('ID_SUCURSAL','=',$this->sucursal)
	          ->GROUPBY('temp_ventas.MARCAS_CODIGO','temp_ventas.LINEA_CODIGO') 
	          ->orderby('temp_ventas.MARCA')
	          ->get()
	          ->toArray();
	          foreach ($temp as $key => $value) {
                    $datos=array(
		                 
		                'HOJAS'=> $this->hojas,
		                'MARCA_CODIGO'=> $value->MARCA,
		                'DESCRI_M'=> $value->DESCRI_M,
		                'LINEA_CODIGO'=> $value->LINEA,
		                'DESCRI_L'=> $value->DESCRI_L,
		                'INICIO'=> $this->inicio,
		                'FINAL'=> $this->final,
		                'SUCURSAL'=>$this->sucursal,
		                'USER_ID'=>$user->id

		            );

	          	  $this->sheets[]= new RptVentaMarca($datos);
	          	# code...
	          }


	          return $this->sheets;

          
}
}
