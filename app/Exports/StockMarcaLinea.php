<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Ventas_det;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
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
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
class StockMarcaLinea implements  FromArray, WithTitle,WithEvents,ShouldAutoSize,WithColumnFormatting
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
      public function __construct( array $ventageneral=[] )
    {
       
        $this->ventageneral =$ventageneral ;
      
    }
        public function  array(): array
    {

    	 foreach ($this->ventageneral as $key=> $value) {

           

               $this->marcas_array_aux[]= (object) array(
                 
                'COD_PROD'=> $value->COD_PROD,
                'DESCRIPCION'=> $value->DESCRIPCION,
 'STOCK'=> $value->STOCK,

            );
           

            }
             $marcas_array[]=array('COD_PROD','DESCRIPCION','STOCK');
        foreach ($this->marcas_array_aux as $this->venta) {
                $this->posicion=$this->posicion+1;
                
             $marcas_array[]=array(
                 
                'COD_PROD'=> $this->venta->COD_PROD,
                'DESCRIPCION'=> $this->venta->DESCRIPCION,
                'STOCK'=> $this->venta->STOCK,

            );
             
    }

  return $marcas_array;
}
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
            $event->sheet->getStyle('A1:C1')->applyfromarray($styleArray);
            $this->posicion=$this->posicion+1;
            $event->sheet->getStyle('B'.$this->posicion.':I'.$this->posicion)->applyfromarray($styleArray);
           
        }

        ];
       
      
    }
     public function columnFormats(): array
    {
        return [
           
        ];
    }
     public function title(): string
    {
     
             return $this->DESCRIPCION." ".$this->DESCRIPCIONL  ;
          
       
        }
}