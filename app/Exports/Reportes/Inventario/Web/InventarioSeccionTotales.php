<?php

namespace App\Exports\Reportes\Inventario\Web;

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
class InventarioSeccionTotales implements FromArray, WithTitle,WithEvents,ShouldAutoSize,WithColumnFormatting

{
	private $total_descuento;
    private $costo_sobrante;
    private $costo_perdida;
    private $cantidadsobrante;
    private $porcentaje_cantidad_perdida;
    public  $posicion=1;
    private $hojas;
    public  $seccion_inventario_array=[];
    private $seccion_inventario=[];
    private $porcentaje_cantidad_sobrante;
    private $cantidadperdida;
    private $agrupado=0;
    private $porcentaje_costo_perdida;
    private $porcentaje_costo_sobrante;
    /**
    * @return \Illuminate\Support\Collection
    */
 public function __construct($secciones_inventario)
    {
    	
    	$this->seccion_inventario=$secciones_inventario;
    }
     public function  array(): array{
    	    $seccion_inventario_array[]=array('TOTALES',"",'CANTIDAD PERDIDA','CANTIDAD SOBRANTE','COSTO PERDIDA','COSTO SOBRANTE','% CANTIDAD PERDIDA','% CANTIDAD SOBRANTE','% COSTO PERDIDA','% COSTO SOBRANTE');
	    	foreach ($this->seccion_inventario as $key => $value) {
	    		
	    		$this->posicion=$this->posicion+1;
				$this->cantidadperdida=$this->cantidadperdida+$value["CANTIDAD_PERDIDA"] ;
				$this->cantidadsobrante=$this->cantidadsobrante+$value["CANTIDAD_SOBRANTE"] ;
                $this->costo_perdida=$this->costo_perdida+$value["COSTO_PERDIDA"] ;
				$this->costo_sobrante=$this->costo_sobrante+$value["COSTO_SOBRANTE"] ;
				$this->porcentaje_cantidad_perdida=$this->porcentaje_cantidad_perdida+$value["PORCENTAJE_CANTIDAD_PERDIDA"] ;
				$this->porcentaje_cantidad_sobrante=$this->porcentaje_cantidad_sobrante+$value["PORCENTAJE_CANTIDAD_SOBRANTE"];
				$this->porcentaje_costo_perdida=$this->porcentaje_costo_perdida+$value["PORCENTAJE_COSTO_PERDIDA"] ;
				$this->porcentaje_costo_sobrante=$this->porcentaje_costo_sobrante+$value["PORCENTAJE_COSTO_SOBRANTE"];

				$seccion_inventario_array[]=array(
				    'TOTALES'=> $value["TOTALES"] ,
				    ''=>"",
				    'CANTIDAD_PERDIDA'=> $value["CANTIDAD_PERDIDA"] ,
				    'CANTIDAD_SOBRANTE'=> $value["CANTIDAD_SOBRANTE"] ,
				    'COSTO_PERDIDA'=> $value["COSTO_PERDIDA"] ,
				    'COSTO_SOBRANTE'=> $value["COSTO_SOBRANTE"] ,
				    'PORCENTAJE_CANTIDAD_PERDIDA'=>$value["PORCENTAJE_CANTIDAD_PERDIDA"],
				    'PORCENTAJE_CANTIDAD_SOBRANTE'=>$value["PORCENTAJE_CANTIDAD_SOBRANTE"], 
				    'PORCENTAJE_COSTO_PERDIDA'=>$value["PORCENTAJE_COSTO_PERDIDA"],
				    'PORCENTAJE_COSTO_SOBRANTE'=>$value["PORCENTAJE_COSTO_SOBRANTE"], 
				    
				 );
	    		# code...
	    	}

		      $seccion_inventario_array[]=array(
			     'TOTALES'=> 'GENERAL',
			     ''=>"",
			     'CANTIDAD_PERDIDA'=> $this->cantidadperdida ,
				 'CANTIDAD_SOBRANTE'=> $this->cantidadsobrante ,
				 'COSTO_PERDIDA'=> $this->costo_perdida ,
				 'COSTO_SOBRANTE'=> $this->costo_sobrante ,
				 'PORCENTAJE_CANTIDAD_PERDIDA'=>$this->porcentaje_cantidad_perdida,
				 'PORCENTAJE_CANTIDAD_SOBRANTE'=>$this->porcentaje_cantidad_sobrante, 
				 'PORCENTAJE_COSTO_PERDIDA'=>$this->porcentaje_costo_perdida,
				 'PORCENTAJE_COSTO_SOBRANTE'=>$this->porcentaje_costo_sobrante, 
				 
				);

	            return $seccion_inventario_array;

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
            $event->sheet->getStyle('A1:J1')->applyfromarray($styleArray);
            $this->posicion=$this->posicion+1;
            $event->sheet->getStyle('A'.$this->posicion.':J'.$this->posicion)->applyfromarray($styleArray);
            $event->sheet->getStyle('C2:'.'C'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('D2:'.'D'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('E2:'.'E'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('F2:'.'F'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('G2:'.'G'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('H2:'.'H'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('I2:'.'I'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('J2:'.'J'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
             
             
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
        
             return 'TOTALES';
       
         	
         
         
       
    }
}
