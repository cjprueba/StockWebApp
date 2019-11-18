<?php
namespace App\Exports;

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

class VentasProveedorTotal implements  FromArray, WithTitle,WithEvents,ShouldAutoSize,WithColumnFormatting
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
    * @return \Illuminate\Support\Collection
    */
       public function __construct( array $ventageneral=[], array $RESULTS=[] )
    {
        
        $this->ventageneral =$ventageneral ;
      $this->RESULTS =$RESULTS ;
    }
      public function  array(): array
    {
      
      $marcas_array[]=array('TOTALES',"",'STOCK','VENDIDO','COSTO','TOTAL_COSTO','PRECIO','TOTAL_PRECIO','UTILIDAD');
       foreach ($this->RESULTS as $key => $PROVEE) {
       	           foreach ($this->ventageneral as $key=> $value) {

            if($value->PROVEEDOR==$PROVEE->CODIGO ){

              $this->total_utilidad=$this->total_utilidad+$value->UTILIDAD;  
               $this->total_general=$this->total_general+$value->PRECIO_VENTA;
               $this->total_preciounit=$this->total_preciounit+$value->PRECIO_UNIT_VENTA;
               $this->cantidadvendida=$this->cantidadvendida+$value->CANTIDAD_S;
              $this->costo=$this->costo+$value->PRECOSTO;
              $this->totalcosto=$this->totalcosto+$value->PRECOSTO_TOTAL;
         $this->stockarray=DB::connection('retail')->table('LOTES')->SELECT(DB::raw('SUM(LOTES.CANTIDAD) AS CANTI'))
        ->WHERE('LOTES.COD_PROD','=',$value->COD_PROD)
        ->WHERE('LOTES.ID_SUCURSAL','=',4)
        ->GROUPBY('LOTES.COD_PROD')
        ->orderby('LOTES.cod_prod')
        ->get()
        ->toArray();
           foreach ($this->stockarray as $this->stockarrays) 
            {
                    $this->stock=$this->stockarrays->CANTI;  

                    
            };

            }
            # code...
        }
      
     
        

            $this->posicion=$this->posicion+1;
                 $marcas_array[]=array(
                'TOTALES'=> $PROVEE->DescriM,
                ''=>"",
                'STOCK'=> $this->stocktotal,
                'CANTIDAD'=> $this->cantidadvendida,
                'COSTO'=> $this->costo,
                'TOTAL_COSTO'=> $this->totalcosto,
                'PRECIO'=> $this->total_preciounit,
                'TOTAL_PRECIO'=> $this->total_general,
                'UTILIDAD'=>  $this->total_utilidad,
                );
               
               $this->stocktotal=0;
               $this->cantidadvendida=0;
               $this->costo=0;
               $this->totalcosto=0;
               $this->total_preciounit=0;
               $this->total_general=0;
               $this->total_utilidad=0;
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
            $event->sheet->getStyle('A1:i1')->applyfromarray($styleArray);
            $this->posicion=$this->posicion+1;
            $event->sheet->getStyle('B'.$this->posicion.':I'.$this->posicion)->applyfromarray($styleArray);
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
         
             return 'TOTALES';
      
       
        }
}



