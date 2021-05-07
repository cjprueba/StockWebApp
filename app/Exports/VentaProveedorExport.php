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
class VentaProveedorExport implements WithMultipleSheets
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
        $this->tipos=$datos['Tipo'];

       
    }
    public function sheets(): array
    {
		    	$user = auth()->user();
		    	
		    		$datos=array(
		    			'inicio'=>$this->inicio,
		    			'final'=>$this->final,
		    			'sucursal'=>$this->sucursal,
		    			'checkedCategoria'=>$this->checkedCategoria,
		    			'checkedProveedor'=>$this->checkedProveedor,
		    			'marcas'=>$this->marcas,
		    			'proveedores'=>$this->proveedores,
		    		    'checkedMarca'=>$this->checkedMarca,
		    			'linea'=>$this->linea,
		    			'tipos'=>$this->tipos
		    		);
		           Temp_venta::insertar_reporte($datos);
		    	
		    	        
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
				                'USER_ID'=>$user->id

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
				                'TIPOS'=>$this->tipos

				            );
		         $this->sheets[]= new VentaProveedorTotales($datos);

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
				                 
				                'HOJAS'=> $this->hojas,
				                'PROVEEDOR_CODIGO'=> '',
				                'DESCRI_P'=> '',
				                'LINEA_CODIGO'=> '',
				                'DESCRI_L'=> '',
				                'INICIO'=> $this->inicio,
				                'FINAL'=> $this->final,
				                'SUCURSAL'=>$this->sucursal,
				                'USER_ID'=>$user->id

				            );
			          	  $this->sheets[]= new VentaProveedor($datos);
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
				                'USER_ID'=>$user->id

				            );

		         	 $temp=DB::connection('retail')->table('temp_ventas')
			   	
			           ->select(
			            DB::raw('temp_ventas.PROVEEDOR AS PROVEEDOR'),
			            DB::raw('temp_ventas.PROVEEDOR_NOMBRE AS DESCRI_P'),
			            DB::raw('temp_ventas.LINEA_CODIGO AS LINEA'),
			            DB::raw('temp_ventas.CATEGORIA as DESCRI_L'))
			             ->WHERE('temp_ventas.PROVEEDOR','<>',19)
			              ->where('USER_ID','=',$user->id)
			                  ->where('ID_SUCURSAL','=',$this->sucursal)
			          ->GROUPBY('temp_ventas.PROVEEDOR','temp_ventas.LINEA_CODIGO') 
			          ->orderby('temp_ventas.MARCA')
			          ->get()
			          ->toArray();
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
				                'USER_ID'=>$user->id

				            );

			          	  $this->sheets[]= new VentaProveedor($datos);
			          	# code...
			          }


			          return $this->sheets;

          
	}
}
