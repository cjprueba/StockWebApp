<?php

namespace App\Exports\Reportes;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProveedorSeccionExport implements FromArray, WithTitle, WithEvents, WithColumnFormatting, ShouldAutoSize
{
    private $costo;
    private $total;
    private $cantidadvendida;
    private $totalcosto;
    public  $posicion = 1;
    public  $seccion_proveedor_array=[];
    private $seccion_proveedor = [];
    private $ganancia;
    private $cantidadentrada;
    private $stock_actual;
    private $transferencia;
    private $total_transferencia;
    private $total_venta_costo;
    private $utilidad;
    private $total_utilidad;
    private $total_costo_sobrante;

    /**
    * @return \Illuminate\Support\Collection
    */

    public function __construct($datos){
    	// var_dump($datos);
    	$this->seccion_proveedor = $datos["proveedores"];
    	$this->titulo = $datos["proveedores"][0]["SECCION"];
    }
    
    public function  array(): array{

	    $seccion_proveedor_array[] = array('PROVEEDORES', 'ENTRADA','VENDIDO', 'TRANSFERENCIA', 'COSTO PROMEDIO','COSTO TOTAL','TOTAL VENTA', 'COSTO VENTA', 'TOTAL TRANSFERENCIA', 'STOCK ACTUAL', 'UTILIDAD', 'COSTO SOBRANTE');

    	foreach ($this->seccion_proveedor as $key => $value) {

    		$this->posicion = $this->posicion + 1;
			$this->cantidadentrada = $this->cantidadentrada + $value["ENTRADA"];
			$this->cantidadvendida = $this->cantidadvendida + $value["VENDIDO"];
			$this->costo = $this->costo + $value["COSTO_PROMEDIO"];
			$this->totalcosto = $this->totalcosto + $value["COSTO_TOTAL"];
			$this->total = $this->total + $value["TOTAL_VENTA"];
			$this->total_venta_costo = $this->total_venta_costo + $value["VENTA_COSTO"];
			$this->stock_actual = $this->stock_actual + $value["STOCK_ACTUAL"];
			$this->transferencia = $this->transferencia + $value["TRANSFERENCIA"];
			$this->total_transferencia = $this->total_transferencia + $value["TOTAL_TRANSFERENCIA"];
			$this->utilidad = $value["TOTAL_VENTA"] - $value["VENTA_COSTO"];
			$this->total_utilidad = $this->total_utilidad + $this->utilidad;
			$this->total_costo_sobrante = $this->total_costo_sobrante + $value["COSTO_SOBRANTE"];

			if($value["VENDIDO"] == 0){
				$value["VENDIDO"] = "0";
				$value["TOTAL_VENTA"] = "0";
				$this->utilidad = "0";
				$value["VENTA_COSTO"] = "0";
			}

			if($value["TRANSFERENCIA"] == 0){
			    $value["TRANSFERENCIA"] = "0";
			    $value['TOTAL TRANSFERENCIA'] = "0" ;
			}

			if($value["STOCK_ACTUAL"] == 0){
				$value["STOCK_ACTUAL"] = "0";
				$value["COSTO_SOBRANTE"] = "0";
			}

			$seccion_proveedor_array[] = array(
			    'PROVEEDOR'=> $value["PROVEEDOR"],
			    'ENTRADA'=> $value["ENTRADA"],
			    'VENDIDO'=> $value["VENDIDO"],
			    'TRANSFERENCIA'=> $value["TRANSFERENCIA"],
			    'COSTO PROMEDIO'=> $value["COSTO_PROMEDIO"],
			    'COSTO TOTAL'=> $value["COSTO_TOTAL"],
			    'TOTAL VENTA'=> $value["TOTAL_VENTA"],
			    'VENTA COSTO'=> $value["VENTA_COSTO"],
			    'TOTAL TRANSFERENCIA'=> $value["TOTAL_TRANSFERENCIA"],
			    'STOCK ACTUAL' => $value["STOCK_ACTUAL"], 
			    'UTILIDAD' =>  $this->utilidad,
			    'COSTO SOBRANTE' => $value["COSTO_SOBRANTE"]
			);
    	}

    	if($this->transferencia == 0){

    		$this->transferencia = "0";
    		$this->total_transferencia = "0";
    	}
	    $seccion_proveedor_array[] = array(
		    'PROVEEDOR'=> 'TOTALES',
		    'ENTRADA'=> $this->cantidadentrada,
		    'VENDIDO'=> $this->cantidadvendida,
		    'TRANSFERENCIA' => $this->transferencia,
		    'COSTO PROMEDIO'=> $this->costo,
		    'COSTO TOTAL'=> $this->totalcosto,
		    'TOTAL VENTA'=> $this->total,
		    'VENTA COSTO' => $this->total_venta_costo,
		    'TOTAL TRANSFERENCIA' => $this->total_transferencia,
		    'STOCK ACTUAL'=> $this->stock_actual,
		    'UTILIDAD'=> $this->total_utilidad,
		    'COSTO SOBRANTE' => $this->total_costo_sobrante
		);

        return $seccion_proveedor_array;
    }

    public function registerEvents(): array {
 
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

         	AfterSheet::class => function(AfterSheet $event) use($styleArray)  {

	            $event->sheet->getStyle('A1:L1')->applyfromarray($styleArray);
	            $this->posicion = $this->posicion + 1;
	            $event->sheet->getStyle('A'.$this->posicion.':L'.$this->posicion)->applyfromarray($styleArray);
	            $event->sheet->getStyle('B2:'.'B'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0');
	            $event->sheet->getStyle('C2:'.'C'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0');
	            $event->sheet->getStyle('D2:'.'D'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0');
	            $event->sheet->getStyle('E2:'.'E'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
	            $event->sheet->getStyle('F2:'.'F'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
	            $event->sheet->getStyle('G2:'.'G'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00'); 
	            $event->sheet->getStyle('H2:'.'H'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');  
	            $event->sheet->getStyle('I2:'.'I'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00'); 
	            $event->sheet->getStyle('J2:'.'J'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0'); 
	            $event->sheet->getStyle('K2:'.'K'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');   
	            $event->sheet->getStyle('L2:'.'L'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00'); 
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(30);
             
        	}
        ];
    }

    public function columnFormats(): array {
        return [
           'A' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    /**
     * @return string
     */

    public function title(): string {
        
        return substr($this->titulo, 0,31);
    }

}
