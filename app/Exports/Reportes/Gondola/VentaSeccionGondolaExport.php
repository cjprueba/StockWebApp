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

class VentaSeccionGondolaExport implements FromArray, WithTitle, WithEvents, ShouldAutoSize, WithColumnFormatting {

    private $total_descuento;
    private $total_utilidad;
    private $total_general;
    private $total_preciounit;
    private $costo;
    private $totalcosto;
    private $cantidadvendida;
    public  $posicion = 1;
    public  $gondola_array = [];
    private $seccion;
    private $sucursal;
    private $descri_s;
    private $gondola;
    private $descri_g;

    /**
    * @return \Illuminate\Support\Collection
    */

    public function __construct($datos) {

        $this->sucursal = $datos['Sucursal'];
        $this->seccion = $datos['Seccion'];
        $this->descri_s = $datos['Descripcion'];
    }

    public function  array(): array {

        $user = auth()->user();

        $gondola_array[] = array('GONDOLAS','VENDIDO','DESCUENTO','COSTO PROMEDIO','PRECIO PROMEDIO','COSTO TOTAL','TOTAL VENTA','UTILIDAD');

        $TOTAL = DB::connection('retail')->table('TEMP_VENTAS')->select(
            DB::raw('SUM(TEMP_VENTAS.VENDIDO) AS VENDIDO'),
            DB::raw('GONDOLA_NOMBRE AS DESCRI_G'),
            DB::raw('SUM(TEMP_VENTAS.DESCUENTO) AS DESCUENTO'),
            DB::raw('SUM(COSTO_TOTAL) AS COSTO_TOTAL'),
            DB::raw('AVG(COSTO_UNIT) AS COSTO_UNIT'),
            DB::raw('SUM(TEMP_VENTAS.PRECIO) AS TOTAL'),
            DB::raw('AVG(TEMP_VENTAS.PRECIO_UNIT) AS PRECIO_UNIT'),
            DB::raw('SUM(TEMP_VENTAS.UTILIDAD) AS UTILIDAD'))
        ->where('TEMP_VENTAS.USER_ID', '=', $user->id)
        ->where('TEMP_VENTAS.ID_SUCURSAL', '=', $this->sucursal)
        ->where('TEMP_VENTAS.SECCION_CODIGO','=', $this->seccion)
        ->where('TEMP_VENTAS.CREDITO_COBRADO', '=', 0)
        ->groupBy('TEMP_VENTAS.GONDOLA', 'TEMP_VENTAS.SECCION_CODIGO')
        ->get()
        ->toArray();

        foreach ($TOTAL as $key => $value) {

            $this->posicion = $this->posicion + 1;
            $this->total_general = $this->total_general + $value->TOTAL;
            $this->total_descuento = $this->total_descuento + $value->DESCUENTO;
            $this->total_preciounit = $this->total_preciounit + $value->PRECIO_UNIT;
            $this->cantidadvendida = $this->cantidadvendida + $value->VENDIDO;
            $this->costo = $this->costo + $value->COSTO_UNIT;
            $this->totalcosto = $this->totalcosto + $value->COSTO_TOTAL;
            $this->total_utilidad = $this->total_utilidad + $value->UTILIDAD;
            
            $gondola_array[] = array(
                'GONDOLAS'=> $value->DESCRI_G,
                'VENDIDO'=> $value->VENDIDO,
                'DESCUENTO'=>$value->DESCUENTO,
                'COSTO PROMEDIO'=> $value->COSTO_UNIT,
                'PRECIO PROMEDIO'=> $value->PRECIO_UNIT,
                'COSTO TOTAL'=> $value->COSTO_TOTAL,
                'TOTAL VENTA'=> $value->TOTAL,
                'UTILIDAD'=> $value->UTILIDAD
            );
        }

        $gondola_array[] = array(
            'GONDOLAS'=> 'TOTALES',
            'VENDIDO'=> $this->cantidadvendida,
            'DESCUENTO'=>$this->total_descuento,
            'COSTO PROMEDIO'=> $this->costo,
            'PRECIO PROMEDIO'=> $this->total_preciounit,
            'COSTO TOTAL'=> $this->totalcosto,
            'TOTAL VENTA'=> $this->total_general,
            'UTILIDAD'=> $this->total_utilidad
        );

        return $gondola_array;
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
                $event->sheet->getStyle('A1:H1')->applyfromarray($styleArray);
                $this->posicion = $this->posicion + 1;
                $event->sheet->getStyle('A'.$this->posicion.':H'.$this->posicion)->applyfromarray($styleArray);
                $event->sheet->getStyle('B2:'.'B'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0');
                $event->sheet->getStyle('C2:'.'C'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
                $event->sheet->getStyle('D2:'.'D'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
                $event->sheet->getStyle('E2:'.'E'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
                $event->sheet->getStyle('F2:'.'F'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
                $event->sheet->getStyle('G2:'.'G'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
                $event->sheet->getStyle('H2:'.'H'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            }
        ];
    }

    public function columnFormats(): array {
         return [
           'A' => NumberFormat::FORMAT_NUMBER,
           'M' => NumberFormat::FORMAT_NUMBER
        ];
    }

    /**
     * @return string
     */
    public function title(): string {
        
        return substr($this->descri_s, 0,30);
    }
}