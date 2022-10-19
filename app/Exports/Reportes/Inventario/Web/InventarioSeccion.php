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

class InventarioSeccion implements FromArray, WithTitle,WithEvents,ShouldAutoSize,WithColumnFormatting
{

    public  $posicion=1;
    private $hojas;
    public  $produtos_seccion_array=[];
    private $titulo;
    private $productos=[];
    private $costo_sobrante;
    private $costo_perdida;
 	private $cantidadsobrante;
 	private $porcentaje_cantidad_perdida;
 	public  $seccion_inventario_array=[];
 	private $porcentaje_cantidad_sobrante;
    private $cantidadperdida;
    private $agrupado=0;
    private $porcentaje_costo_perdida;
    private $porcentaje_costo_sobrante;
    private $seccion_id;
    private $gondola_id=0;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($datos)
    {
    	log::error(["datos"=>$datos]);
    	$this->productos=$datos["PRODUCTOS"];
    	$this->titulo=$datos["TITULO"];
    	$this->seccion_id=$datos["SECCION"];
    	$this->agrupado=$datos["AGRUPADO"];
    	$this->gondola_id=$datos["GONDOLA"];
    }
         public function  array(): array{
    	    $produtos_seccion_array[]=array('COD_PROD','CATEGORIA','SUBCATEGORIA','STOCK','CANTIDAD PERDIDA','CANTIDAD SOBRANTE','COSTO PERDIDA', 'COSTO SOBRANTE');

	    	foreach ($this->productos as $key => $value) {
	    		if($this->agrupado==2){
	    			$this->gondola_id=0;
	    			$value["GONDOLA"]=0;
	    		}


	    	if(($value["SECCION_CODIGO"]==$this->seccion_id) && ($value["GONDOLA"]==$this->gondola_id)  ){
	    		$this->posicion=$this->posicion+1;
			    $this->cantidadperdida=$this->cantidadperdida+$value["CANTIDAD_PERDIDA"] ;
				$this->cantidadsobrante=$this->cantidadsobrante+$value["CANTIDAD_SOBRANTE"] ;
                $this->costo_perdida=$this->costo_perdida+$value["COSTO_PERDIDA"] ;
				$this->costo_sobrante=$this->costo_sobrante+$value["COSTO_SOBRANTE"] ;

				$produtos_seccion_array[]=array(
				    'COD_PROD'=> $value["COD_PROD"],
                    'CATEGORIA'=> $value["CATEGORIA"],
                    'SUBCATEGORIA'=> $value["SUBCATEGORIA"],
                    'STOCK'=> $value["STOCK"],
                    'CANTIDAD_PERDIDA'=> $value["CANTIDAD_PERDIDA"],
                    'CANTIDAD_SOBRANTE'=>$value["CANTIDAD_SOBRANTE"],
                    'COSTO_PERDIDA'=>$value["COSTO_PERDIDA"],
                    'COSTO_SOBRANTE'=>$value["COSTO_SOBRANTE"]
				    
				 );
	    	}
	    		
	    		# code...
	    	}

		      $produtos_seccion_array[]=array(
			     'COD_PROD'=> '',
			     'CATEGORIA'=>"",
			     'SUBCATEGORIA'=>"",
			     'STOCK'=>"TOTALES GENERALES",
			    'CANTIDAD_PERDIDA'=> $this->cantidadperdida ,
				'CANTIDAD_SOBRANTE'=> $this->cantidadsobrante ,
				'COSTO_PERDIDA'=> $this->costo_perdida ,
				'COSTO_SOBRANTE'=> $this->costo_sobrante ,
				 
				);

	            return $produtos_seccion_array;

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
            $event->sheet->getStyle('A'.$this->posicion.':I'.$this->posicion)->applyfromarray($styleArray);
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
