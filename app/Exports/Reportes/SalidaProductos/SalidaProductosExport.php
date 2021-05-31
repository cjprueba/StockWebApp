<?php

namespace App\Exports\Reportes\SalidaProductos;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
USE Illuminate\Support\Facades\DB;
use App\Exports\Reportes\SalidaProductos\SalidaProductosTotales;
use App\Exports\Reportes\SalidaProductos\SalidaProductos;
USE Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use DateTime;
use App\SalidaProducto;

class SalidaProductosExport implements WithMultipleSheets
{
  private $sheets = [];
  private $hojas=1;
  private $inicio;
  private $final;
  private $sucursal;
  private $checkedTipos;
  private $tipos;

  /**
    * @return \Illuminate\Support\Collection
  */
   use Exportable;
   public function __construct($datos)
    {
     
        $this->inicio = date('Y-m-d', strtotime($datos['Inicio']));
        $this->final  =  date('Y-m-d', strtotime($datos['Final']));
        $this->sucursal = $datos['Sucursal'];
        $this->tipos=$datos['Tipos'];
        $this->checkedTipos=$datos['AllTipos'];
       
    }
        public function sheets(): array
    {
    	$datos=array (
    		  	'sucursal'=>$this->sucursal,
    		  	'inicio'=>$this->inicio,
    		  	'final'=>$this->final,
    		  	'tipos'=>$this->tipos,
    		  	'checkedTipo'=>$this->checkedTipos
    		  );
    	  $this->sheets[]= new SalidaProductosTotales($datos);
    	  $descripcion='';
    	  if(!$this->checkedTipos){
    	  		foreach ($this->tipos as $key => $value) {
    	  			if($value==1){
    	  				$descripcion="AVERIO";
    	  			}elseif ($value==2) {
    	  				$descripcion="VENCIDOS";
    	  			}elseif ($value==3) {
    	  				$descripcion="ROBADO";
    	  			}elseif ($value==4) {
    	  				$descripcion="MUESTRA";
    	  			}elseif ($value==5) {
    	  				$descripcion="EXTRAVIADO";
    	  			}elseif ($value==6) {
    	  				$descripcion="REGALO";
    	  			}elseif ($value==7) {
    	  				$descripcion="USO_INTERNO";
    	  			}
    	  			$this->sheets[]= new SalidaProductos($datos,$value,$descripcion);
    	  		}
    	  }else{
    	  	$tipos_mes=SalidaProducto::Select(DB::raw('TIPO as TIPOS'))->WHERE('ID_SUCURSAL','=',$this->sucursal)->whereBetween('FECALTAS',[$this->inicio,$this->final])->where('ESTADO','=',0)->GROUPBY('TIPO')->get();
    	  	if(count($tipos_mes)>0){
    	  		foreach ($tipos_mes as $key => $value) {
    	  			if($value->TIPOS==1){
    	  				$descripcion="AVERIO";
    	  			}elseif ($value->TIPOS==2) {
    	  				$descripcion="VENCIDOS";
    	  			}elseif ($value->TIPOS==3) {
    	  				$descripcion="ROBADO";
    	  			}elseif ($value->TIPOS==4) {
    	  				$descripcion="MUESTRA";
    	  			}elseif ($value->TIPOS==5) {
    	  				$descripcion="EXTRAVIADO";
    	  			}elseif ($value->TIPOS==6) {
    	  				$descripcion="REGALO";
    	  			}elseif ($value->TIPOS==7) {
    	  				$descripcion="USO_INTERNO";
    	  			}
				   $this->sheets[]= new SalidaProductos($datos,$value->TIPOS,$descripcion);
    	  		}
    	  	}
    	  }
    	    return $this->sheets;

    }


}
