<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
Use App\Stock;
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
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithDrawings;

class Catalogo implements FromArray, WithTitle,WithEvents,ShouldAutoSize,WithColumnFormatting
{
   private $CODIGO;
    private $ventageneral=[];   
    private $DESCRIPCION;
    private $CODIGOL;
    private $DESCRIPCIONL;
    private $marcas;
    private  $venta;
    private $total_utilidad;
    private $total_general;
    private $total_preciounit;
    private $costo;
    private $totalcosto;
    private $cantidadvendida;
    private $stock;
    private $stocktotal;
    public $posicion=1;
    private $stockarray;
    private $stockarrays;
    public $marcas_array=[];
     public $marcas_array_aux=[];
    private $descuentogeneral;
    private $descuentos;


    /**
     * @return Builder
     */
        public function __construct()
    {
        
    }
    public function drawings()
    {

    }

       public function  array(): array
    {
  $dia ='2020-07-15';

   $reporte = Stock::
        	select(DB::raw('LOTES.COD_PROD, LINEAS.DESCRIPCION AS CATEGORIA, SUBLINEAS.DESCRIPCION AS SUBCATEGORIA, SUBLINEA_DET.DESCRIPCION AS NOMBRE, PRODUCTOS_AUX.STOCK_MIN'),
        DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = lotes.COD_PROD) AND (l.ID_SUCURSAL = lotes.ID_SUCURSAL))),0) AS STOCK'))
        	->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'LOTES.COD_PROD')
        	->leftjoin('PRODUCTOS_AUX', 'PRODUCTOS_AUX.CODIGO', '=', 'LOTES.COD_PROD')
        	 ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->leftjoin('SUBLINEAS', 'SUBLINEAS.CODIGO', '=', 'PRODUCTOS.SUBLINEA')
            ->leftjoin('SUBLINEA_DET', 'SUBLINEA_DET.CODIGO', '=', 'PRODUCTOS.SUBLINEADET')
        	->leftjoin('LOTES_USER', 'LOTES.ID', '=', 'LOTES_USER.FK_LOTE')
        	->whereRaw('(IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = LOTES.COD_PROD) AND (l.ID_SUCURSAL = LOTES.ID_SUCURSAL))),0)) <= PRODUCTOS_AUX.STOCK_MIN AND (IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = LOTES.COD_PROD) AND (l.ID_SUCURSAL = LOTES.ID_SUCURSAL))),0)) >0')
        ->whereRaw('SUBSTR(LOTES_USER.FECHA, 1,11) = "'.$dia.'"')
        /* ->whereBetween('LOTES_USER.FECHA', ['2020-05-25', '2020-07-10'])*/
        	->where('LOTES.ID_SUCURSAL','=', 9)	
        	->groupBy('LOTES.COD_PROD')
            ->orderBy('LOTES.COD_PROD')
            ->get();
       


      
    
        $marcas_array[]=array('COD_PROD','CATEGORIA','SUBCATEGORIA','NOMBRE','STOCK','STOCK_MINIMO');

       







        foreach ($reporte as $key=>$value ) {

        

               $this->posicion=$this->posicion+1;
           
             if($value->STOCK==0){
             	$value->STOCK='0';
             }
              if($value->STOCK_MIN==0){
              $value->STOCK_MIN='0';
             }

            if($value->NOMBRE==NULL){
                $value->NOMBRE='';
             }
             $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
$drawing->setName('Logo');
$drawing->setDescription('Logo');
        $drawing->setPath('C:\laragon\www\StockWebApp-ultimo\public\images\SinImagen.png');
$drawing->setHeight(36);
 $drawing->setCoordinates('G'.$this->posicion);

       
       
        log::error(["imagen"=>$drawing]);
              $marcas_array[]=array(
                 
                'COD_PROD'=> $value->COD_PROD,
                'CATEGORIA'=> $value->CATEGORIA,
                'SUBCATEGORIA'=> $value->SUBCATEGORIA,
                'NOMBRE'=> $value->NOMBRE,
                'STOCK'=> $value->STOCK,
                'STOCK_MINIMO'=> $value->STOCK_MIN
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
            $event->sheet->getStyle('A1:F1')->applyfromarray($styleArray);
           
             
        }

        ];
       
       
      
    }







    public function columnFormats(): array
    {
         return [
         	 'A' => NumberFormat::FORMAT_NUMBER,
           'E' => NumberFormat::FORMAT_NUMBER,
           'F' => NumberFormat::FORMAT_NUMBER,
           

        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
         
             return 'REPORTE';
         
       
        }
}
