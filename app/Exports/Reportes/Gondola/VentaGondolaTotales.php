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

class VentaGondolaTotales implements  FromArray, WithTitle,WithEvents,ShouldAutoSize,WithColumnFormatting
{
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
    private $servicioDelivery;
    private $mayoristaContado;
    private $mayoristaCredito;
    private $inicio;
    private $final;
    private $sucursal;
    private $utilidad_total;

    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($datos)
    {
    	$this->servicioDelivery=$datos['servicioDelivery'];
    	$this->mayoristaContado=$datos['mayoristaContado'];
    	$this->mayoristaCredito=$datos['mayoristaCredito'];
    	$this->sucursal=$datos['sucursal'];
    	$this->inicio=$datos['inicio'];
    	$this->final=$datos['final'];
    }
    public function  array(): array{
    	  $user = auth()->user();
    	  $marcas_array[]=array('TOTALES',"",'VENDIDO','DESCUENTO','COSTO','COSTO TOTAL','PRECIO','TOTAL','UTILIDAD','UTILIDAD_PORCENTAJE','VENTA_PORCENTAJE');
         $temp=DB::connection('retail')->table('temp_ventas')
	   	
	           ->select(
	            DB::raw('temp_ventas.SECCION_CODIGO AS SECCION'),
	            DB::raw('IFNULL(temp_ventas.SECCION,"INDEFINIDO") as DESCRI_S'))
		          ->WHERE('TEMP_VENTAS.VENDEDOR','=',1)
		          ->where('TEMP_VENTAS.CREDITO_COBRADO','=',0)
		          ->where('TEMP_VENTAS.USER_ID','=',$user->id)
		          ->where('TEMP_VENTAS.ID_SUCURSAL','=',$this->sucursal)
		          ->GROUPBY('temp_ventas.SECCION_CODIGO') 
		          ->get()
		          ->toArray();
	          

	            $TOTALG=DB::connection('retail')->table('temp_ventas')
			           ->select(
			            DB::raw('SUM(temp_ventas.VENDIDO) AS VENDIDO'),
			            DB::raw('SUM(temp_ventas.DESCUENTO) AS DESCUENTO'),
			            DB::raw('SUM(COSTO_TOTAL) AS COSTO_TOTAL'),
			            DB::raw('SUM(COSTO_UNIT) AS COSTO_UNIT'),
			            DB::raw('SUM(temp_ventas.PRECIO) AS TOTAL'),
			             DB::raw('SUM(temp_ventas.UTILIDAD) AS UTILIDAD'),
			            DB::raw('SUM(temp_ventas.PRECIO_UNIT) AS PRECIO_UNIT'))
			           ->where('TEMP_VENTAS.USER_ID','=',$user->id)
		               ->where('TEMP_VENTAS.ID_SUCURSAL','=',$this->sucursal)
			           ->get()
			           ->toArray();

			    $ser=DB::connection('retail')->table('ventasdet_servicios') 
			   		 ->leftjoin('VENTAS',function($join){
		             $join->on('VENTAS.CODIGO','=','ventasdet_servicios.CODIGO')
		             ->on('VENTAS.CAJA','=','ventasdet_servicios.CAJA')
		             ->on('VENTAS.ID_SUCURSAL','=','ventasdet_servicios.ID_SUCURSAL');
		             })
			   		 ->leftjoin('VENTAS_ANULADO', 'VENTAS_ANULADO.FK_VENTA', '=', 'VENTAS.ID')
			         ->select(DB::raw('SUM(ventasdet_servicios.PRECIO) AS PRECIO_SERVICIO,
			            	sum(ventasdet_servicios.CANTIDAD) AS VENDIDO,
			            	sum(ventasdet_servicios.PRECIO_UNIT) AS PRECIO_UNIT'))
			           
		             ->Where('VENTAS_ANULADO.ANULADO','<>',1)
		             ->Where('VENTAS.ID_SUCURSAL','=',$this->sucursal)
		             ->whereBetween('VENTAS.FECALTAS', [$this->inicio, $this->final])
			         ->get()
			         ->toArray();

			         if(count($ser)>0){
			         	$total_porcentaje=$TOTALG[0]->TOTAL+$ser[0]->PRECIO_SERVICIO;
			         }else{
			         	$total_porcentaje=$TOTALG[0]->TOTAL;
			         }

	            foreach ($temp as $key => $value) {
					 
	            	 $TOTAL=DB::connection('retail')->table('temp_ventas')
			           ->select(
			            DB::raw('SUM(temp_ventas.VENDIDO) AS VENDIDO'),
			            DB::raw('SUM(temp_ventas.DESCUENTO) AS DESCUENTO'),
			            DB::raw('SUM(COSTO_TOTAL) AS COSTO_TOTAL'),
			            DB::raw('SUM(COSTO_UNIT) AS COSTO_UNIT'),
			            DB::raw('SUM(temp_ventas.PRECIO) AS TOTAL'),
			            DB::raw('SUM(temp_ventas.UTILIDAD) AS UTILIDAD'),
			            DB::raw('SUM(temp_ventas.PRECIO_UNIT) AS PRECIO_UNIT'))
				         ->where('temp_ventas.SECCION_CODIGO','=',$value->SECCION)
	                     ->WHERE('TEMP_VENTAS.VENDEDOR','=',1)
	                     ->where('TEMP_VENTAS.CREDITO_COBRADO','=',0)
                         ->where('TEMP_VENTAS.USER_ID','=',$user->id)
		                 ->where('TEMP_VENTAS.ID_SUCURSAL','=',$this->sucursal)
			          ->get()
			          ->toArray();
						$this->posicion=$this->posicion+1;
			            $this->total_general=$this->total_general+$TOTAL[0]->TOTAL;
			            $this->total_descuento=$this->total_descuento+$TOTAL[0]->DESCUENTO;
			            $this->total_preciounit=$this->total_preciounit+$TOTAL[0]->PRECIO_UNIT;
			            $this->cantidadvendida=$this->cantidadvendida+$TOTAL[0]->VENDIDO;
			            $this->costo=$this->costo+$TOTAL[0]->COSTO_UNIT;
			            $this->totalcosto=$this->totalcosto+$TOTAL[0]->COSTO_TOTAL;
			             $this->utilidad_total=$this->utilidad_total+$TOTAL[0]->UTILIDAD;

			            $marcas_array[]=array(
			                'TOTALES'=> $value->DESCRI_S,
			                ''=>"",
			                'VENDIDO'=> $TOTAL[0]->VENDIDO,
			                'DESCUENTO'=>$TOTAL[0]->DESCUENTO,
			                'COSTO'=> $TOTAL[0]->COSTO_UNIT,
			                'COSTO TOTAL'=> $TOTAL[0]->COSTO_TOTAL,
			                'PRECIO'=> $TOTAL[0]->PRECIO_UNIT,
			                'TOTAL'=> $TOTAL[0]->TOTAL,
			                'UTILIDAD'=>$TOTAL[0]->UTILIDAD,
			                'UTILIDAD_PORCENTAJE'=>($TOTAL[0]->UTILIDAD/$TOTAL[0]->TOTAL)*100,  
			                'VENTA_PORCENTAJE'=>(100*$TOTAL[0]->TOTAL)/$total_porcentaje,
			                
			             );
	            	# code...
	            }
	           
	            	if($this->mayoristaContado){
		            		$TOTAL=DB::connection('retail')->table('temp_ventas')
	                       ->select(
	                        DB::raw('SUM(temp_ventas.VENDIDO) AS VENDIDO'),
	                        DB::raw('SUM(temp_ventas.DESCUENTO) AS DESCUENTO'),
	                        DB::raw('SUM(COSTO_TOTAL) AS COSTO_TOTAL'),
	                        DB::raw('SUM(COSTO_UNIT) AS COSTO_UNIT'),
	                        DB::raw('SUM(temp_ventas.PRECIO) AS TOTAL'),
	                         DB::raw('SUM(temp_ventas.UTILIDAD) AS UTILIDAD'),
	                        DB::raw('SUM(temp_ventas.PRECIO_UNIT) AS PRECIO_UNIT'))
	                      ->WHERE('TEMP_VENTAS.VENDEDOR','<>',1)
	                      ->where('TEMP_VENTAS.CREDITO_COBRADO','=',0)
	                      ->where('TEMP_VENTAS.USER_ID','=',$user->id)
			              ->where('TEMP_VENTAS.ID_SUCURSAL','=',$this->sucursal)
	                      ->get()
	                      ->toArray();

	                       $marcas_array[]=array(
	                            'TOTALES'=> "MAYORISTA AL CONTADO",
	                            ''=>"",
	                            'VENDIDO'=> $TOTAL[0]->VENDIDO,
	                            'DESCUENTO'=>$TOTAL[0]->DESCUENTO,
	                            'COSTO'=> $TOTAL[0]->COSTO_UNIT,
	                            'COSTO TOTAL'=> $TOTAL[0]->COSTO_TOTAL,
	                            'PRECIO'=> $TOTAL[0]->PRECIO_UNIT,
	                            'TOTAL'=> $TOTAL[0]->TOTAL,  
	                            'UTILIDAD'=>$TOTAL[0]->UTILIDAD,
	                            'UTILIDAD_PORCENTAJE'=>($TOTAL[0]->UTILIDAD/$TOTAL[0]->TOTAL)*100,
	                            'VENTA_PORCENTAJE'=>(100*$TOTAL[0]->TOTAL)/$total_porcentaje,
	                           
	                         );
	                          $this->posicion=$this->posicion+1;

					            $this->total_general=$this->total_general+$TOTAL[0]->TOTAL;
					            $this->total_descuento=$this->total_descuento+$TOTAL[0]->DESCUENTO;
					            $this->total_preciounit=$this->total_preciounit+$TOTAL[0]->PRECIO_UNIT;
					            $this->cantidadvendida=$this->cantidadvendida+$TOTAL[0]->VENDIDO;
					            $this->costo=$this->costo+$TOTAL[0]->COSTO_UNIT;
					            $this->totalcosto=$this->totalcosto+$TOTAL[0]->COSTO_TOTAL;
					            $this->utilidad_total=$this->utilidad_total+$TOTAL[0]->UTILIDAD;
	            	}
	            		            	if($this->mayoristaContado){
		            		$TOTAL=DB::connection('retail')->table('temp_ventas')
	                       ->select(
	                        DB::raw('SUM(temp_ventas.VENDIDO) AS VENDIDO'),
	                        DB::raw('SUM(temp_ventas.DESCUENTO) AS DESCUENTO'),
	                        DB::raw('SUM(COSTO_TOTAL) AS COSTO_TOTAL'),
	                        DB::raw('SUM(COSTO_UNIT) AS COSTO_UNIT'),
	                        DB::raw('SUM(temp_ventas.PRECIO) AS TOTAL'),
	                         DB::raw('SUM(temp_ventas.UTILIDAD) AS UTILIDAD'),
	                        DB::raw('SUM(temp_ventas.PRECIO_UNIT) AS PRECIO_UNIT'))
	                      ->where('TEMP_VENTAS.CREDITO_COBRADO','=',1)
	                      ->where('TEMP_VENTAS.USER_ID','=',$user->id)
			              ->where('TEMP_VENTAS.ID_SUCURSAL','=',$this->sucursal)
	                      ->get()
	                      ->toArray();

	                       $marcas_array[]=array(
	                            'TOTALES'=> "MAYORISTA A CREDITO COBRADO",
	                            ''=>"",
	                            'VENDIDO'=> $TOTAL[0]->VENDIDO,
	                            'DESCUENTO'=>$TOTAL[0]->DESCUENTO,
	                            'COSTO'=> $TOTAL[0]->COSTO_UNIT,
	                            'COSTO TOTAL'=> $TOTAL[0]->COSTO_TOTAL,
	                            'PRECIO'=> $TOTAL[0]->PRECIO_UNIT,
	                            'TOTAL'=> $TOTAL[0]->TOTAL,
	                            'UTILIDAD'=>$TOTAL[0]->UTILIDAD ,
	                            'UTILIDAD_PORCENTAJE'=>($TOTAL[0]->UTILIDAD/$TOTAL[0]->TOTAL)*100, 
	                            'VENTA_PORCENTAJE'=>(100*$TOTAL[0]->TOTAL)/$total_porcentaje,
	                            
	                         );
	                          $this->posicion=$this->posicion+1;
	                            $this->total_general=$this->total_general+$TOTAL[0]->TOTAL;
					            $this->total_descuento=$this->total_descuento+$TOTAL[0]->DESCUENTO;
					            $this->total_preciounit=$this->total_preciounit+$TOTAL[0]->PRECIO_UNIT;
					            $this->cantidadvendida=$this->cantidadvendida+$TOTAL[0]->VENDIDO;
					            $this->costo=$this->costo+$TOTAL[0]->COSTO_UNIT;
					            $this->totalcosto=$this->totalcosto+$TOTAL[0]->COSTO_TOTAL;
					              $this->utilidad_total=$this->utilidad_total+$TOTAL[0]->UTILIDAD;
	            	}
                     
	        
	            	if($this->servicioDelivery){
	            	      if(count($ser)>0){
				             $this->posicion=$this->posicion+1;
			                 $this->total_general=$this->total_general+$ser[0]->PRECIO_SERVICIO;
			                 $this->total_preciounit=$this->total_preciounit+$ser[0]->PRECIO_UNIT;
			                 $this->cantidadvendida=$this->cantidadvendida+$ser[0]->VENDIDO;
			              
				               $marcas_array[]=array(
				                'TOTALES'=> 'SERVICIO DE DELIVERY',
				                ''=>"",
				                'VENDIDO'=> $ser[0]->VENDIDO,
				                'DESCUENTO'=>'0',
				                'COSTO'=> '0',
				                'COSTO TOTAL'=> '0',
				                'PRECIO'=> $ser[0]->PRECIO_UNIT,
				                'TOTAL'=> $ser[0]->PRECIO_SERVICIO,
				                'UTILIDAD'=>0,
			                	'UTILIDAD_PORCENTAJE'=>0,
			                	'VENTA_PORCENTAJE'=>(100*$ser[0]->PRECIO_SERVICIO)/$total_porcentaje,
			                	
			                	);
				           }

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
				                'UTILIDAD'=> $this->utilidad_total,
			                
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
            $event->sheet->getStyle('A1:K1')->applyfromarray($styleArray);
            $this->posicion=$this->posicion+1;
            $event->sheet->getStyle('A'.$this->posicion.':I'.$this->posicion)->applyfromarray($styleArray);
            $event->sheet->getStyle('C2:'.'C'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('D2:'.'D'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('E2:'.'E'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('F2:'.'F'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('G2:'.'G'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('H2:'.'H'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('I2:'.'I'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('J2:'.'J'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('K2:'.'K'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
             
             
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
