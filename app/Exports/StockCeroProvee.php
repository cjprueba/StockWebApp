<?php

namespace App\Exports;
use App\Stock;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Facades\Excel as MaatExcel;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Maatwebsite\Excel\Concerns\WithHeadings;
USE Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
USE Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Facades\Log;
use DateTime;

class StockCeroProvee implements FromArray, WithTitle,WithEvents,ShouldAutoSize,WithColumnFormatting 
{
	 private $proveedor;
    private $descri_p;
    public $posicion=1;
    public $marcas_array=[];
	    /**
    * @return \Illuminate\Support\Collection
    */
            public function __construct($proveedor_codigo,$descrip)
    {
    	
        $this->proveedor=$proveedor_codigo;
        $this->descri_p=$descrip;
    }
    public function array(): array
    {
    	$dia2 = date("Y-m-d");
         $dia1 = new DateTime();
         $dia1=  $dia1->modify('first day of this month');
        $reporte = Stock::
        	select(DB::raw('LOTES.COD_PROD, LINEAS.DESCRIPCION AS CATEGORIA, SUBLINEAS.DESCRIPCION AS SUBCATEGORIA, SUBLINEA_DET.DESCRIPCION AS NOMBRE, SUM(LOTES.CANTIDAD) AS STOCK'))
        	->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'LOTES.COD_PROD')
        	   ->leftjoin('PRODUCTOS_AUX',function($join){
        	$join->on('PRODUCTOS_AUX.CODIGO','=','lotes.COD_PROD')
               ->on('PRODUCTOS_AUX.ID_SUCURSAL','=','LOTES.ID_SUCURSAL');
         })
        	->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'PRODUCTOS_AUX.PROVEEDOR')
        	 ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->leftjoin('SUBLINEAS', 'SUBLINEAS.CODIGO', '=', 'PRODUCTOS.SUBLINEA')
            ->leftjoin('SUBLINEA_DET', 'SUBLINEA_DET.CODIGO', '=', 'PRODUCTOS.SUBLINEADET')
        	->leftjoin('LOTES_USER', 'LOTES.ID', '=', 'LOTES_USER.FK_LOTE')
        	->whereRaw('(IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = LOTES.COD_PROD) AND (l.ID_SUCURSAL = LOTES.ID_SUCURSAL))),0)) = 0')
        	//->whereRaw('SUBSTR(LOTES_USER.FECHA, 1,11) = "'.$dia.'"')
        	->whereBetween('LOTES_USER.FECHA', [$dia1, $dia2])
        	->where('LOTES.ID_SUCURSAL','=', 9)
        	->where('PRODUCTOS_AUX.PROVEEDOR','=',$this->proveedor)
        	->groupBy('LOTES.COD_PROD')
            ->orderBy('LOTES.COD_PROD')
            ->get();
                    $marcas_array[]=array('COD_PROD','CATEGORIA','SUBCATEGORIA','NOMBRE','STOCK');

       







        foreach ($reporte as $key=>$value ) {

               $this->posicion=$this->posicion+1;
             if($value->STOCK==0){
             	$value->STOCK='0';
             }

            if($value->NOMBRE==NULL){
                $value->NOMBRE='';
             }
              $marcas_array[]=array(
                 
                'COD_PROD'=> $value->COD_PROD,
                'CATEGORIA'=> $value->CATEGORIA,
                'SUBCATEGORIA'=> $value->SUBCATEGORIA,
                'NOMBRE'=> $value->NOMBRE,
                'STOCK'=> $value->STOCK,
            );

              
              	
          }
              



       

        

  


          return $marcas_array;
    }
    /**
     * @return array
     */
     public function registerEvents(): array
    {
        

      


 
    $styleArray = [
    'font' => [
        'bold' => true,
    ],
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
    ],
    'borders' => [
        'top' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        ],
    ],
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
        'rotation' => 90,
        'startColor' => [
            'argb' => '97B4C8',
        ],
        'endColor' => [
            'argb' => '97B4C8',
        ],
    ],
];


   
   
        return [
            // Handle by a closure.
         AfterSheet::class => function(AfterSheet $event) use($styleArray)  {
            $event->sheet->getStyle('A1:E1')->applyfromarray($styleArray);
           
             
        }

        ];
       
       
      
    }







    public function columnFormats(): array
    {
         return [
         	 'A' => NumberFormat::FORMAT_NUMBER,
           'B' => NumberFormat::FORMAT_NUMBER,
           

        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
         
             return $this->descri_p;
         
       
        }
}
