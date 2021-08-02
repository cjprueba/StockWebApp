<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
USE Illuminate\Support\Facades\DB;
use App\Exports\VentaProveedor;
use App\Exports\VentaProveedorTotales;
USE Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use DateTime;
use App\Temp_venta;

use Maatwebsite\Excel\Concerns\FromCollection;
class VentasProveedorExport implements WithMultipleSheets
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
  private $proveedores=[];
  private $tipos=[];
  private $hojas=1;
  private $inicio;
  private $final;
  private $checkedProveedor;
  private $checkedMarca;
  private $insert;
  private $checkedCategoria;
  private $sucursal;
  private $agruparCategoria;
  private $agruparProveedor;

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
        $this->checkedMarca= true;
        $this->marcas=[];
        $this->checkedCategoria=$datos['AllCategory'];
        $this->proveedores=$datos['Proveedores'];
        $this->linea=$datos['Categorias'];
     /*   $this->tipos=$datos['Tipo'];*/
        $this->agruparCategoria=$datos['agruparCategoria'];
        $this->agruparProveedor=$datos['AgruparProveedor'];
       

       
    }
    public function sheets(): array
    {
		    	$user = auth()->user();
		    	
		    		
		    	
		    	        
		if($this->hojas==1){

			
						              
		                    $datos=array(
				                 
				                'HOJAS'=> $this->hojas,
				                'PROVEEDOR_CODIGO'=> '',
				                'DESCRI_P'=> '',
				                'LINEA_CODIGO'=> '',
				                'DESCRI_L'=> '',
				                'INICIO'=> $this->inicio,
				                'FINAL'=> $this->final,
				                'SUCURSAL'=>$this->sucursal,
				                'USER_ID'=>$user->id,
 								'AGRUPAR_CATEGORIA'=>$this->agruparCategoria,
				                'AGRUPAR_PROVEEDOR'=> $this->agruparProveedor

				            );


				          $this->sheets[]= new VentaProveedor($datos);
				          $this->hojas=$this->hojas+1;
		     }
		                        $datos=array(
				                 
				                'HOJAS'=> $this->hojas,
				                'PROVEEDOR_CODIGO'=> '',
				                'DESCRI_P'=> '',
				                'LINEA_CODIGO'=> '',
				                'DESCRI_L'=> '',
				                'INICIO'=> $this->inicio,
				                'FINAL'=> $this->final,
				                'SUCURSAL'=>$this->sucursal,
				                'USER_ID'=>$user->id,
				                'TIPOS'=>$this->tipos,
				                'AGRUPAR_CATEGORIA'=>$this->agruparCategoria,
				                'AGRUPAR_PROVEEDOR'=> $this->agruparProveedor

				            );
		       


		         	  if($this->agruparCategoria==true && $this->agruparProveedor==true){
			                    	$temp=DB::connection('retail')->table('temp_ventas')
			   	
							           ->select(
							            DB::raw('temp_ventas.PROVEEDOR AS PROVEEDOR'),
							            DB::raw('temp_ventas.PROVEEDOR_NOMBRE AS DESCRI_P'),
							            DB::raw('temp_ventas.LINEA_CODIGO AS LINEA'),
							            DB::raw('temp_ventas.CATEGORIA as DESCRI_L'))
							             ->where('USER_ID','=',$user->id)
							             ->where('ID_SUCURSAL','=',$this->sucursal)
							           ->GROUPBY('temp_ventas.PROVEEDOR','temp_ventas.LINEA_CODIGO') 
							           ->orderby('temp_ventas.PROVEEDOR_NOMBRE','ASC')
							           ->get()
							           ->toArray();
			                    }elseif ($this->agruparProveedor==true) {
			                    		$temp=DB::connection('retail')->table('temp_ventas')
			   	
								           ->select(
								            DB::raw('temp_ventas.PROVEEDOR AS PROVEEDOR'),
								            DB::raw('temp_ventas.PROVEEDOR_NOMBRE AS DESCRI_P'),
								            DB::raw('0 AS LINEA'),
								            DB::raw('"" as DESCRI_L'))
								             ->where('USER_ID','=',$user->id)
								             ->where('ID_SUCURSAL','=',$this->sucursal)
								           ->GROUPBY('temp_ventas.PROVEEDOR') 
								           ->orderby('temp_ventas.PROVEEDOR_NOMBRE')
								           ->get()
								           ->toArray();
			                    }elseif ($this->agruparCategoria==true) {
			                    		$temp=DB::connection('retail')->table('temp_ventas')
			   	
								           ->select(
								            DB::raw('0 AS PROVEEDOR'),
								            DB::raw('"" AS DESCRI_P'),
								            DB::raw('temp_ventas.LINEA_CODIGO AS LINEA'),
								            DB::raw('temp_ventas.CATEGORIA as DESCRI_L'))
								             ->where('USER_ID','=',$user->id)
								             ->where('ID_SUCURSAL','=',$this->sucursal)
								           ->GROUPBY('temp_ventas.LINEA_CODIGO') 
								           ->orderby('temp_ventas.CATEGORIA')
								           ->get()
								           ->toArray();
			                    }
			                    $this->sheets[]= new VentaProveedorTotales($datos,$temp);
		         	
			          foreach ($temp as $key => $value) {
		                    $datos=array(
				                 
				                'HOJAS'=> $this->hojas,
				                'PROVEEDOR_CODIGO'=> $value->PROVEEDOR,
				                'DESCRI_P'=> $value->DESCRI_P,
				                'LINEA_CODIGO'=> $value->LINEA,
				                'DESCRI_L'=> $value->DESCRI_L,
				                'INICIO'=> $this->inicio,
				                'FINAL'=> $this->final,
				                'SUCURSAL'=>$this->sucursal,
				                'USER_ID'=>$user->id,
				                'AGRUPAR_CATEGORIA'=>$this->agruparCategoria,
				                'AGRUPAR_PROVEEDOR'=> $this->agruparProveedor

				            );

			          	  $this->sheets[]= new VentaProveedor($datos);
			          
			          }


			          return $this->sheets;

          
	}
}
