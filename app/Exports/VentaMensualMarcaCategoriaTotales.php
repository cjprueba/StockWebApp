<?php

namespace App\Exports;

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
class VentaMensualMarcaCategoriaTotales implements  FromArray, WithTitle,WithEvents,ShouldAutoSize,WithColumnFormatting 
{
	private $CODIGO;
    private $ventageneral=[];   
    private $DESCRIPCION;
    private $CODIGOL;
    private $proveedor;
    private $descri_p;
    private $DESCRIPCIONL;
    private $marcas;
    private  $venta;
    private $total_descuento;
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
     private $hojas;
    private $stockarrays;
    public $marcas_array=[];
     public $marcas_array_aux=[];
    private $descuentogeneral;
    private $descuentos;
   /**
    * @return \Illuminate\Support\Collection
    */
            public function __construct()
    {

    }
          public function  array(): array
    {
    	$promedio=0;
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
	            $marcas_array[]=array('TOTALES',"",'VENDIDO','DESCUENTO','COSTO','COSTO TOTAL','PRECIO','TOTAL');
	            foreach ($temp as $key => $value) {
	            	 $TOTAL=DB::connection('retail')->table('temp_ventas')
			   			
			           ->select(
			           
		
			            DB::raw('SUM(temp_ventas.VENDIDO) AS VENDIDO'),
			         
			            DB::raw('SUM(temp_ventas.DESCUENTO) AS DESCUENTO'),
			            DB::raw('SUM(COSTO_TOTAL) AS COSTO_TOTAL'),
			            DB::raw('SUM(COSTO_UNIT) AS COSTO_UNIT'),
			            DB::raw('SUM(temp_ventas.PRECIO) AS TOTAL'),
			            DB::raw('SUM(temp_ventas.PRECIO_UNIT) AS PRECIO_UNIT'))
			         
			            ->where('temp_ventas.proveedor','<>',19)
			             ->where('temp_ventas.MARCAS_CODIGO','=',$value->MARCA)
			              ->where('temp_ventas.Linea_Codigo','=',$value->LINEA)
			          ->get()
			          ->toArray();
			           $this->posicion=$this->posicion+1;
                  $this->total_general=$this->total_general+$TOTAL[0]->TOTAL;
               $this->total_descuento=$this->total_descuento+$TOTAL[0]->DESCUENTO;
               $this->total_preciounit=$this->total_preciounit+$TOTAL[0]->PRECIO_UNIT;
               $this->cantidadvendida=$this->cantidadvendida+$TOTAL[0]->VENDIDO;
              $this->costo=$this->costo+$TOTAL[0]->COSTO_UNIT;
              $this->totalcosto=$this->totalcosto+$TOTAL[0]->COSTO_TOTAL;
                  $marcas_array[]=array(
                'TOTALES'=> $value->DESCRI_M." ".$value->DESCRI_L,
                ''=>"",
                'VENDIDO'=> $TOTAL[0]->VENDIDO,
                'DESCUENTO'=>$TOTAL[0]->DESCUENTO,
                'COSTO'=> $TOTAL[0]->COSTO_UNIT,
                'COSTO TOTAL'=> $TOTAL[0]->COSTO_TOTAL,
                'PRECIO'=> $TOTAL[0]->PRECIO_UNIT,
                'TOTAL'=> $TOTAL[0]->TOTAL,
                
                );
	            	# code...
	            }



	             $marcas_array[]=array(
                'TOTALES'=> 'GENERAL',
                ''=>"",
                'VENDIDO'=> $this->cantidadvendida,
                'DESCUENTO'=>$this->total_descuento,
                'COSTO'=> $this->costo,
                'COSTO TOTAL'=> $this->totalcosto,
                'PRECIO'=> $this->total_preciounit,
                'TOTAL'=> $this->total_general,
                
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
            $event->sheet->getStyle('A1:H1')->applyfromarray($styleArray);
            $this->posicion=$this->posicion+1;
          $event->sheet->getStyle('A'.$this->posicion.':H'.$this->posicion)->applyfromarray($styleArray);
            $event->sheet->getStyle('C2:'.'C'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('D2:'.'D'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('E2:'.'E'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('F2:'.'F'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('G2:'.'G'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
              $event->sheet->getStyle('H2:'.'H'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
             
             
        }

        ];
       
       
      
    }







    public function columnFormats(): array
    {
         return [
           'A' => NumberFormat::FORMAT_NUMBER,
           
 		   'M' => NumberFormat::FORMAT_NUMBER,
           

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
