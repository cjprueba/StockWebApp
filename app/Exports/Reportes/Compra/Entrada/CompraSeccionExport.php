<?php

namespace App\Exports\Reportes\Compra\Entrada;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
USE Illuminate\Support\Facades\DB;
use App\Exports\Reportes\Compra\Entrada\CompraSeccionProveedorTotales;
use App\Exports\Reportes\Compra\Entrada\CompraSeccionProveedor;
USE Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use DateTime;
use Maatwebsite\Excel\Concerns\FromCollection;
class CompraSeccionExport implements WithMultipleSheets
{
	 private $sheets = [];
	 private $hojas=1;
	 private $productos=[];
	 private $seccion_proveedor=[];
	 private $seccion_totales=[];
    /**
    * @return \Illuminate\Support\Collection
    */
	public function __construct($datos)
	{
	     
	       
	        $this->seccion_proveedor = $datos['secciones'];
	        $this->productos= $datos['compras'];
	        $this->seccion_totales= $datos['secciones_totales'];
	     
	       
	}
    public function sheets(): array
    {
    	  $this->sheets[]= new CompraSeccionProveedorTotales($this->seccion_proveedor);

    	  foreach ($this->seccion_totales as $key => $value) {
    	  	 $data=array(
    	  	 	'SECCION'=>$value["SECCIONES"],
    	  	 	'SECCION_NOMBRE'=>$value["TOTALES"],
    	  	 	'PROVEEDOR_TOTALES'=> $this->seccion_proveedor,
    	  	     );
    	  	 $this->sheets[]= new CompraSeccionProveedor($data);
    	  }
    	 

    	  return $this->sheets;
    }
}
