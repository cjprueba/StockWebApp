<?php

namespace App\Exports\Reportes\Gondola;

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


class VentaGondola implements FromArray, WithTitle,WithEvents,ShouldAutoSize,WithColumnFormatting 
{

    private $DESCRIPCIONL;
    private $total_descuento;
    private $total_utilidad;
    private $total_general;
    private $total_preciounit;
    private $costo;
    private $totalcosto;
    private $cantidadvendida;
    public  $posicion=1;
    private $hojas;
    public  $marcas_array=[];
    public  $marcas_array_aux=[];
    private $seccion;
    private $sucursal;
    private $descri_s;
    private $gondola;
    private $descri_g;
    private $inicio;
    private $final;
    private $total_porcentaje;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($datos,$seccion,$descri_s,$total_porcentajes)
    {
        $this->seccion=$seccion;
        $this->descri_s=$descri_s;
        $this->sucursal=$datos['sucursal'];
        $this->inicio=$datos['inicio'];
    	$this->final=$datos['final'];
    	$this->total_porcentaje=$total_porcentajes;

    }
    public function  array(): array
    {
    			$user = auth()->user();
	            $marcas_array[]=array('TOTALES',"",'VENDIDO','DESCUENTO','COSTO','COSTO TOTAL','PRECIO','TOTAL','PORCENTAJE');

	            	 $TOTAL=DB::connection('retail')->table('temp_ventas')
			           ->select(
			            DB::raw('SUM(temp_ventas.VENDIDO) AS VENDIDO'),
			            DB::raw('GONDOLA_NOMBRE AS DESCRI_G'),
			            DB::raw('SUM(temp_ventas.DESCUENTO) AS DESCUENTO'),
			            DB::raw('SUM(COSTO_TOTAL) AS COSTO_TOTAL'),
			            DB::raw('SUM(COSTO_UNIT) AS COSTO_UNIT'),
			            DB::raw('SUM(temp_ventas.PRECIO) AS TOTAL'),
			            DB::raw('SUM(temp_ventas.PRECIO_UNIT) AS PRECIO_UNIT'))
			            ->where('USER_ID','=',$user->id)
			           ->where('ID_SUCURSAL','=',$this->sucursal)
			          ->where('temp_ventas.SECCION_CODIGO','=',$this->seccion)
                      ->WHERE('temp_ventas.VENDEDOR','=',1)
                      ->where('temp_ventas.CREDITO_COBRADO','=',0)
			          ->GROUPBY('temp_ventas.GONDOLA')
			          ->get()
			          ->toArray();
                       foreach ($TOTAL as $key => $value) {
                       	$this->posicion=$this->posicion+1;
			            $this->total_general=$this->total_general+$value->TOTAL;
			            $this->total_descuento=$this->total_descuento+$value->DESCUENTO;
			            $this->total_preciounit=$this->total_preciounit+$value->PRECIO_UNIT;
			            $this->cantidadvendida=$this->cantidadvendida+$value->VENDIDO;
			            $this->costo=$this->costo+$value->COSTO_UNIT;
			            $this->totalcosto=$this->totalcosto+$value->COSTO_TOTAL;
                        
			            $marcas_array[]=array(
			                'TOTALES'=> $value->DESCRI_G,
			                ''=>"",
			                'VENDIDO'=> $value->VENDIDO,
			                'DESCUENTO'=>$value->DESCUENTO,
			                'COSTO'=> $value->COSTO_UNIT,
			                'COSTO TOTAL'=> $value->COSTO_TOTAL,
			                'PRECIO'=> $value->PRECIO_UNIT,
			                'TOTAL'=> $value->TOTAL,  
			                'PORCENTAJE'=>(100*$value->TOTAL)/$this->total_porcentaje,
			             );
                       	# code...
                       }
						
	            	# code...
	      



	        

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
            $event->sheet->getStyle('A1:I1')->applyfromarray($styleArray);
            $this->posicion=$this->posicion+1;
            $event->sheet->getStyle('A'.$this->posicion.':H'.$this->posicion)->applyfromarray($styleArray);
            $event->sheet->getStyle('C2:'.'C'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('D2:'.'D'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('E2:'.'E'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('F2:'.'F'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('G2:'.'G'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('H2:'.'H'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('I2:'.'I'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
             
             
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
        
             return substr($this->descri_s, 1,30);
       
         	
         
         
       
     }
}
