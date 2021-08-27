<?php

namespace App\Exports\Reportes\Inventario\Web;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
USE Illuminate\Support\Facades\DB;
use App\Exports\Reportes\Inventario\Web\InventarioSeccionTotales;
use App\Exports\Reportes\Inventario\Web\InventarioSeccion;
USE Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use DateTime;
use Maatwebsite\Excel\Concerns\FromCollection;

class InventarioSeccionExport implements WithMultipleSheets
{
	 private $sheets = [];
	 private $hojas=1;
	 private $productos=[];
	 private $secciones=[];
	 private $seccion_totales=[];
	 private $agrupado=0;
    /**
    * @return \Illuminate\Support\Collection
    */
  public function __construct($datos,$agrupado)
	{
	       
	        $this->secciones = $datos['secciones'];
	        $this->productos= $datos['productos'];
	        $this->seccion_totales= $datos['secciones_totales'];
	        $this->agrupado=$agrupado;    
	       
	}
	    public function sheets(): array
    {
    	  $this->sheets[]= new InventarioSeccionTotales($this->secciones);
    	  if($this->agrupado===1){
    	  	foreach ($this->secciones as $key => $value) {
    	  	 $data=array(
    	  	 	'SECCION'=>$value["SECCIONES"],
    	  	 	'TITULO'=>$value["TOTALES"],
    	  	 	'PRODUCTOS'=> $this->productos,
    	  	 	'GONDOLA'=>$value["GONDOLA"],
    	  	 	'AGRUPADO'=>$this->agrupado
    	  	     );
    	  	 $this->sheets[]= new InventarioSeccion($data);
    	    }
    	  }else{
    	  	foreach ($this->secciones as $key => $value) {
    	  	 $data=array(
    	  	 	'SECCION'=>$value["SECCIONES"],
    	  	 	'TITULO'=>$value["TOTALES"],
    	  	 	'PRODUCTOS'=> $this->productos,
    	  	 	'GONDOLA'=>'0',
    	  	 	'AGRUPADO'=>$this->agrupado
    	  	     );
    	  	 $this->sheets[]= new InventarioSeccion($data);
    	    }
    	 
    	  }

    	  

    	  return $this->sheets;
    }
}
