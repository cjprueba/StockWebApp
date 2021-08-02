<?php

namespace App\Exports\Reportes\Compra\Entrada;

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


class CompraSeccionProveedor implements FromArray, WithTitle,WithEvents,ShouldAutoSize,WithColumnFormatting
{
    private $total_descuento;
    private $costo;
    private $totalcosto;
    public  $posicion=1;
    private $hojas;
    public  $seccion_proveedor_array=[];
    private $titulo;
    private $seccion_proveedor=[];
    private $porcentaje;
    private $cantidadentrada;
    private $cantidadvendida;
    private $total;
    private $seccion_id;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($datos)
    {
    	
    	$this->seccion_proveedor=$datos["PROVEEDOR_TOTALES"];
    	$this->titulo=$datos["SECCION_NOMBRE"];
    	$this->seccion_id=$datos["SECCION"];
    }
     public function  array(): array{
    	    $seccion_proveedor_array[]=array('TOTALES',"",'ENTRADA','VENDIDO','COSTO','COSTO TOTAL','TOTAL','PORCENTAJE');
	    	foreach ($this->seccion_proveedor as $key => $value) {

	    	if($value["SECCIONES"]==$this->seccion_id ){
	    		$this->posicion=$this->posicion+1;
				$this->cantidadentrada=$this->cantidadentrada+$value["ENTRADA"] ;
				$this->cantidadvendida=$this->cantidadvendida+$value["VENDIDO"] ;
				$this->costo=$this->costo+$value["COSTO"] ;
				$this->total=$this->total+$value["TOTAL"] ;
				$this->totalcosto=$this->totalcosto+$value["COSTO_TOTAL"] ;
				$this->porcentaje=$this->porcentaje+$value["PORCENTAJE"];

				$seccion_proveedor_array[]=array(
				    'TOTALES'=> $value["PROVEEDOR_NOMBRE"] ,
				    ''=>"",
				    'ENTRADA'=> $value["ENTRADA"] ,
				     'VENDIDO'=> $value["VENDIDO"] ,
				    'COSTO'=> $value["COSTO"] ,
				    'COSTO TOTAL'=> $value["COSTO_TOTAL"] ,
				     'TOTAL'=> $value["TOTAL"] ,
				    'PORCENTAJE'=>$value["PORCENTAJE"], 
				    
				 );
	    	}
	    		
	    		# code...
	    	}

		      $seccion_proveedor_array[]=array(
			     'TOTALES'=> 'GENERAL',
			     ''=>"",
			     'ENTRADA'=> $this->cantidadentrada,
			     'VENDIDO'=> $this->cantidadvendida,
			     'COSTO'=> $this->costo,
			     'COSTO TOTAL'=> $this->totalcosto,
			     'TOTAL'=> $this->total,
			     'PORCENTAJE'=> $this->porcentaje,
				 
				);

	            return $seccion_proveedor_array;

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
           

        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        
             return substr($this->titulo, 0,31);
       
         	
         
         
       
    }
}
