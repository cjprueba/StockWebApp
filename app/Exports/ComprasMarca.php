<?php

namespace App\Exports;
use App\ComprasDet;
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
class ComprasMarca implements  FromArray, WithTitle,WithEvents,ShouldAutoSize,WithColumnFormatting
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
    public function __construct(int $CODIGO, string $DESCRIPCION,int $hojas, array $ventageneral=[] )
    {
        $this->CODIGO = $CODIGO;   
        $this->DESCRIPCION  = $DESCRIPCION;
        $this->hojas=$hojas;
        $this->ventageneral =$ventageneral ;
      
    }
    /**
     * @return Builder
     */
        public function  array(): array
    {
    	 if ($this->hojas==2){
       

        foreach ($this->ventageneral as $key=> $value) {
          
            if($value['Marca']==$this->CODIGO  ){

               $this->marcas_array_aux[]=  array(
                 
                'COD_PROD'=> $value['COD_PROD'],
                'DESCRIPCION'=> $value['DESCRIPCION'],
                'CANTIDAD_S'=> $value['CANTIDAD_S'],
                'FECHULT_C'=>  $value['FECHULT_C'],

            );
            }
            # code...
        }
      
      } else {
        
        $this->marcas_array_aux = (array)$this->ventageneral;
         
      }
      $marcas_array[]=array('COD_PROD','DESCRIPCION','CANTIDAD','FECHULT_C');
        foreach ($this->marcas_array_aux as $this->venta) {
               $this->posicion=$this->posicion+1;
               
                
            $marcas_array[]=array(
                 
                'COD_PROD'=> $this->venta['COD_PROD'],
                'DESCRIPCION'=> $this->venta['DESCRIPCION'],
                'CANTIDAD'=> $this->venta['CANTIDAD_S'],
                'FECHULT_C'=> $this->venta['FECHULT_C'],

            );
           # code...
        };
         $marcas_array[]=array(
                'COD_PROD'=> "",
                'DESCRIPCION'=>"TOTALES",
                'CANTIDAD'=> $this->cantidadvendida,
                'FECHULT_C'=> "",
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
            $event->sheet->getStyle('A1:D1')->applyfromarray($styleArray);
            $this->posicion=$this->posicion+1;
            $event->sheet->getStyle('B'.$this->posicion.':D'.$this->posicion)->applyfromarray($styleArray);
            $event->sheet->getStyle('G2:'.'G'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0');
            $event->sheet->getStyle('E2:'.'E'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0');
            $event->sheet->getStyle('F2:'.'F'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0');
            $event->sheet->getStyle('H2:'.'H'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0');
            $event->sheet->getStyle('I2:'.'I'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0');
             
        }

        ];
       
       
      
    }







    public function columnFormats(): array
    {
         return [
           'E' => NumberFormat::FORMAT_NUMBER,
           'F' => NumberFormat::FORMAT_NUMBER,
           'G' => NumberFormat::FORMAT_NUMBER,
           'H' => NumberFormat::FORMAT_NUMBER,
           'I' => NumberFormat::FORMAT_NUMBER,
          
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
          if ($this->hojas==1){
             return 'COMPRAS';
          }else{
             return $this->DESCRIPCION  ;
          }
       
        }



}