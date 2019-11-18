<?php

namespace App\Exports;


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

class ExportConteo implements FromArray, WithTitle,WithEvents,ShouldAutoSize
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
  private  $igual;
  private  $igualtot;
   private  $Stockm;
  private  $Stockmtot;
   private  $Stockm;
  private  $Stockmtot;
    /**
    * @return \Illuminate\Support\Collection
    */
      public function __construct( array $ventageneral=[], array $RESULTS=[] )
    {
        
        $this->ventageneral =$ventageneral ;
      $this->RESULTS =$RESULTS ;
    }
     public function  array(): array
    {
      
      $marcas_array[]=array('MARCAS',"",'CONTEO MAYOR');
       foreach ($this->RESULTS as $key => $PROVEE) {
       	           foreach ($this->ventageneral as $key=> $value) {

            if($value->Marca==$PROVEE->Marca && $this->ventageneral[$key]->STOCK < $this->ventageneral[$key]->CONTEO ){
           
              $this->igual=$this->igual+1;;
                $this->igualtot=$this->igualtot+1;
            }
           
            # code...
        }
      
     
        

            $this->posicion=$this->posicion+1;
                 $marcas_array[]=array(
                'MARCAS'=> $PROVEE->DescriM,
                ''=>"",
                'CONTEO MAYOR'=> $this->igual,

                );
               
               $this->igual=0;
             
       }
                 $marcas_array[]=array(
                'MARCAS'=>'TOTALES',
                ''=>"",
                'CONTEO MAYOR'=> $this->igualtot,

                );

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
            $event->sheet->getStyle('A'.$this->posicion.':C'.$this->posicion)->applyfromarray($styleArray);
          
        }

        ];
       
      
    }









    /**
     * @return string
     */
    public function title(): string
    {
         
              return "TOTALES"  ;
      
       
        }

}