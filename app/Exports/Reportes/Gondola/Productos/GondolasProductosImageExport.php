<?php

namespace App\Exports\Reportes\Gondola\Productos;
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
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Gondola;
use App\Exports\Reportes\Gondola\Productos\GondolasProductosImage;

class GondolasProductosImageExport implements WithMultipleSheets
{
  private $sheets = [];
  private $hojas=1;
  private $checkedGondola;
  private $gondolas;
  private $checkedStock;

  /**
    * @return \Illuminate\Support\Collection
  */
   use Exportable;
public function __construct($datos)
    {
     
        
        $this->checkedGondola= $datos['AllGondolas'];
        $this->gondolas=$datos['Gondolas'];
        $this->checkedStock=$datos['Stock'];
       
       
    }
     public function sheets(): array
    { 
    		  $user = auth()->user();
    		  
     		 

	               

			         
	          foreach ($this->gondolas as $key => $value) {
	          	$titulo=Gondola::Select(Db::raw('TRIM(DESCRIPCION) AS DESCRIPCION'))->where('ID','=',$value)->get();
	          	$data=array (
    		  	'Titulo'=>$titulo[0]->DESCRIPCION ,
    		  	'Gondola'=>$value,
    		  	'Stock'=>$this->checkedStock

    		    );
	          	  $this->sheets[]= new GondolasProductosImage($data);
	          
	            }
	                return $this->sheets;
    }
}
