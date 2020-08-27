<?php

namespace App\Exports;

use Illuminate\Http\Request;
USE App\Stock;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
USE Illuminate\Support\Facades\DB;
use App\Exports\StockCeroProvee;
use Illuminate\Support\Facades\Log;
use DateTime;
class StockCeroProveeExport implements WithMultipleSheets
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
     public function __construct()
    {
        
    }
                public function sheets(): array
    {
    	    	$dia2 = date("Y-m-d");
         $dia1 = new DateTime();
         $dia1=  $dia1->modify('first day of this month');
    			if($this->hojas==1){
    	     $reporte = Stock::
        	select(DB::raw('PROVEEDORES.CODIGO AS CODIGO_PROV , PROVEEDORES.NOMBRE AS DESCRI_PROV'))
          ->leftjoin('PRODUCTOS_AUX',function($join){
        	$join->on('PRODUCTOS_AUX.CODIGO','=','lotes.COD_PROD')
               ->on('PRODUCTOS_AUX.ID_SUCURSAL','=','LOTES.ID_SUCURSAL');
         })
        	->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'PRODUCTOS_AUX.PROVEEDOR')
        	->leftjoin('LOTES_USER', 'LOTES.ID', '=', 'LOTES_USER.FK_LOTE')
        	->whereRaw('(IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = LOTES.COD_PROD) AND (l.ID_SUCURSAL = LOTES.ID_SUCURSAL))),0)) = 0')
        	//->whereRaw('SUBSTR(LOTES_USER.FECHA, 1,11) = "'.$dia.'"')
        	->whereBetween('LOTES_USER.FECHA', [$dia1, $dia2])
        	->where('LOTES.ID_SUCURSAL','=', 9)
        	->where('PRODUCTOS_AUX.PROVEEDOR','<>',19)
        	->groupBy('PRODUCTOS_AUX.PROVEEDOR')
           
            ->get();
				foreach ($reporte as $key => $value) {
	   				$this->sheets[]= new StockCeroProvee($value->CODIGO_PROV,$value->DESCRI_PROV);
	# code...
						}
    			}


        


          return $this->sheets;

    }
}
