<?php

namespace App\Exports\Reportes;

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

class VentaSeccionProveedorExport implements FromArray, WithTitle, WithEvents, ShouldAutoSize, WithColumnFormatting {

    private $total_descuento = 0;
    private $total_utilidad = 0;
    private $total_general = 0;
    private $total_preciounit = 0;
    private $costo = 0;
    private $totalcosto = 0;
    private $cantidadvendida = 0;
    public  $posicion = 1;
    public  $proveedor_array = [];
    private $seccion;
    private $descripcion;
    private $sucursal;
    private $descri_s;
    private $descri_g;
    private $total_costo_restante = 0;
    protected $total_stock = 0;
    private $AllSecciones;

    /**
    * @return \Illuminate\Support\Collection
    */

    public function __construct($datos){

        $this->sucursal = $datos['Sucursal'];
        $this->descri_s = $datos['Descripcion'];
    	$this->seccion = $datos['Seccion'];
        $this->AllSecciones = $datos['AllSecciones'];
    }

    public function  array(): array{

		$user = auth()->user();

        $proveedor_array[] = array('PROVEEDORES','VENDIDO','DESCUENTO','COSTO PROMEDIO','PRECIO PROMEDIO','COSTO VENTA', 'TOTAL VENTA', 'UTILIDAD', "STOCK", "COSTO RESTANTE");

    	$TOTAL = DB::connection('retail')->table('TEMP_VENTAS')->select(
            DB::raw('SUM(TEMP_VENTAS.VENDIDO) AS VENDIDO'),
            DB::raw('TEMP_VENTAS.PROVEEDOR_NOMBRE AS DESCRI_G'),
            DB::raw('TEMP_VENTAS.SECCION AS SECCION'),
            DB::raw('IFNULL(SUM(TEMP_VENTAS.DESCUENTO), "0") AS DESCUENTO'),
            DB::raw('SUM(COSTO_TOTAL) AS COSTO_TOTAL'),
            DB::raw('AVG(COSTO_UNIT) AS COSTO_UNIT'),
            DB::raw('SUM(TEMP_VENTAS.PRECIO) AS TOTAL'),
            DB::raw('AVG(TEMP_VENTAS.PRECIO_UNIT) AS PRECIO_UNIT'),
            DB::raw('IFNULL(SUM(TEMP_VENTAS.UTILIDAD), "0") AS UTILIDAD'),
            DB::raw('TEMP_VENTAS.PROVEEDOR'),
            DB::raw('TEMP_VENTAS.SECCION_CODIGO'),
            DB::raw('TEMP_VENTAS.SECCION AS SECCION'))
        ->where('TEMP_VENTAS.USER_ID','=',$user->id)
        ->where('TEMP_VENTAS.ID_SUCURSAL','=', $this->sucursal)
        ->where('TEMP_VENTAS.CREDITO_COBRADO','=', 0)
        ->groupBy('TEMP_VENTAS.SECCION_CODIGO', 'TEMP_VENTAS.PROVEEDOR')
        ->orderBy('TEMP_VENTAS.SECCION')
        ->orderBy('TEMP_VENTAS.PROVEEDOR_NOMBRE')
        ->get()
        ->toArray();

       	foreach ($TOTAL as $key => $value) {

            $lotes = DB::connection('retail')->table('LOTES')
                ->leftjoin('PRODUCTOS_TIENE_SECCION', function($join){
                    $join->on('PRODUCTOS_TIENE_SECCION.COD_PROD','=','LOTES.COD_PROD')
                        ->on('PRODUCTOS_TIENE_SECCION.ID_SUCURSAL','=','LOTES.ID_SUCURSAL');
                    })
                ->select(DB::raw('SUM(LOTES.CANTIDAD) AS STOCK, 
                    SUM(LOTES.CANTIDAD * LOTES.COSTO) AS COSTO_RESTANTE'))
            ->where('LOTES.FK_PROVEEDOR', '=', $value->PROVEEDOR)
            ->where('LOTES.ID_SUCURSAL', '=', $this->sucursal)
            ->where('PRODUCTOS_TIENE_SECCION.SECCION', '=', $value->SECCION_CODIGO)
            ->get();

           	$this->posicion = $this->posicion + 1;
            $this->total_general = $this->total_general + $value->TOTAL;
            $this->total_descuento = $this->total_descuento + $value->DESCUENTO;
            $this->total_preciounit = $this->total_preciounit + $value->PRECIO_UNIT;
            $this->cantidadvendida = $this->cantidadvendida + $value->VENDIDO;
            $this->costo = $this->costo + $value->COSTO_UNIT;
            $this->totalcosto = $this->totalcosto + $value->COSTO_TOTAL;
            $this->total_utilidad = $this->total_utilidad + $value->UTILIDAD;
            $this->total_stock = $this->total_stock + $lotes[0]->STOCK;
            $this->total_costo_restante = $this->total_costo_restante + $lotes[0]->COSTO_RESTANTE;

            if($this->AllSecciones){
                $this->descripcion = $value->SECCION.', '.$value->DESCRI_G;
            }else{
                $this->descripcion = $value->DESCRI_G;
            }


            $proveedor_array[]=array(
                'PROVEEDORES'=> $this->descripcion,
                'VENDIDO'=> $value->VENDIDO,
                'DESCUENTO'=>$value->DESCUENTO,
                'COSTO PROMEDIO'=> $value->COSTO_UNIT,
                'PRECIO PROMEDIO'=> $value->PRECIO_UNIT,
                'COSTO TOTAL'=> $value->COSTO_TOTAL,
                'TOTAL VENTA'=> $value->TOTAL,
                'UTILIDAD' => $value->UTILIDAD,
                'STOCK'=> $lotes[0]->STOCK,
                'COSTO RESTANTE'=> $lotes[0]->COSTO_RESTANTE
            );
        }
					
        $proveedor_array[] = array(
	        'PROVEEDORES'=> 'TOTALES',
	        'VENDIDO'=> $this->cantidadvendida,
	        'DESCUENTO'=>$this->total_descuento,
	        'COSTO PROMEDIO'=> $this->costo,
	        'PRECIO PROMEDIO'=> $this->total_preciounit,
	        'COSTO TOTAL'=> $this->totalcosto,
	        'TOTAL VENTA' => $this->total_general,
            'UTILIDAD' => $this->total_utilidad,
            'STOCK'=> $this->total_stock,
            'COSTO RESTANTE'=> $this->total_costo_restante
        );

        return $proveedor_array;
    }

    public function registerEvents(): array{

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
	            $event->sheet->getStyle('A1:J1')->applyfromarray($styleArray);
	            $this->posicion = $this->posicion + 1;
	            $event->sheet->getStyle('A'.$this->posicion.':J'.$this->posicion)->applyfromarray($styleArray);
	            $event->sheet->getStyle('B2:'.'B'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0');
	            $event->sheet->getStyle('C2:'.'C'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
	            $event->sheet->getStyle('D2:'.'D'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
	            $event->sheet->getStyle('E2:'.'E'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
	            $event->sheet->getStyle('F2:'.'F'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
	            $event->sheet->getStyle('G2:'.'G'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
                $event->sheet->getStyle('H2:'.'H'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
                $event->sheet->getStyle('I2:'.'I'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0');
                $event->sheet->getStyle('J2:'.'J'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
	        }
        ];
    }

    public function columnFormats(): array{

        return [
           'A' => NumberFormat::FORMAT_NUMBER,
 		   'M' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    /**
     * @return string
     */

    public function title(): string{

        return substr($this->descri_s, 0,30);
   
    }
}
