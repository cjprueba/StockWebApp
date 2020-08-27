<?php

namespace App\Exports;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
USE Illuminate\Support\Facades\DB;
use App\Exports\VentaMensualMarcaCategoria;
USE App\Exports\VentaMensualMarcaCategoriaTotales;
USE Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use DateTime;
use Maatwebsite\Excel\Concerns\FromCollection;

class VentaMensualMarcaCategoriaExport implements WithMultipleSheets
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
if($this->hojas==1){
  $this->sheets[]= new VentaMensualMarcaCategoriaTotales();
	 $temp=DB::connection('retail')->table('temp_ventas')
	   	
	           ->select(
	            DB::raw('temp_ventas.MARCAS_CODIGO AS MARCA'),
	            DB::raw('temp_ventas.MARCA AS DESCRI_M'),
	            DB::raw('temp_ventas.LINEA_CODIGO AS LINEA'),
	            DB::raw('temp_ventas.CATEGORIA as DESCRI_L'))
	          ->where('proveedor','<>',19)
	          ->GROUPBY('temp_ventas.MARCAS_CODIGO','temp_ventas.LINEA_CODIGO') 
	          ->orderby('temp_ventas.MARCA')
	          ->get()
	          ->toArray();
	          foreach ($temp as $key => $value) {

	          	  $this->sheets[]= new VentaMensualMarcaCategoria(1,$value->MARCA,$value->DESCRI_M,$value->LINEA,$value->DESCRI_L);
	          	# code...
	          }

        
          $this->hojas=$this->hojas+1;

}
/*         $this->sheets[]= new VentaMensualTotales();

          $temp=DB::connection('retail')->table('temp_ventas')
	   	
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
