<?php

namespace App\Exports;

use App\Ventas_det;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use DateTime;

class VentaSeccionExport implements FromArray, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{

    use Exportable;

    /**
    * @return \Illuminate\Support\Collection
    */

    protected $categorias;
    protected $sucursal;
    protected $sublineas;
    protected $seccion;
    protected $AllSubCategory;
    protected $AllCategory;
    protected $total_venta = 0;
    protected $total_vendido = 0;
    protected $total_descuento = 0;
    protected $total_precio = 0;
    public  $posicion = 1;
    public  $venta_array = [];
    private $total_cant_descuento = 0;
    private $total_utilidad = 0;
    private $total_costo = 0;
    private $total_costo_unit = 0;
    private $total_costo_restante = 0;
    protected $total_stock = 0;

    public function __construct($datos){

        $this->AllCategory = $datos["AllCategory"];
        $this->categorias = $datos["Categorias"];
        $this->AllSubCategory = $datos["AllSubCategory"];
        $this->sublineas = $datos["SubCategorias"];
        $this->sucursal = $datos["Sucursal"];
        $this->seccion = $datos["Seccion"];
    }

    public function  array(): array {

        $user = auth()->user();

        $ventas = DB::connection('retail')->table('temp_ventas')
            ->select(DB::raw('
                temp_ventas.COD_PROD, 
                temp_ventas.NOMBRE AS DESCRIPCION,
                temp_ventas.GONDOLA_NOMBRE AS GONDOLA, 
                SUM(temp_ventas.VENDIDO) AS VENDIDO, 
                SUM(temp_ventas.PRECIO) AS TOTAL, 
                SUM(temp_ventas.COSTO_TOTAL) AS COSTO_TOTAL,
                AVG(temp_ventas.PRECIO_UNIT) AS PRECIO, 
                AVG(temp_ventas.COSTO_UNIT) AS COSTO, 
                SUM(temp_ventas.DESCUENTO) AS DESCUENTO_TOTAL,
                SUM(temp_ventas.UTILIDAD) AS UTILIDAD'),
                DB::raw('IFNULL((SELECT SUM(T.VENDIDO) FROM TEMP_VENTAS AS T WHERE T.DESCUENTO_PRODUCTO > 0 AND T.COD_PROD = TEMP_VENTAS.COD_PROD AND T.ID_SUCURSAL = TEMP_VENTAS.ID_SUCURSAL AND T.USER_ID = TEMP_VENTAS.USER_ID AND T.SECCION_CODIGO = TEMP_VENTAS.SECCION_CODIGO AND T.LINEA_CODIGO = TEMP_VENTAS.LINEA_CODIGO AND T.SUBLINEA_CODIGO = TEMP_VENTAS.SUBLINEA_CODIGO), "0") AS DESCUENTO'))
        ->where('temp_ventas.ID_SUCURSAL', '=', $this->sucursal)
        ->where('temp_ventas.SECCION_CODIGO', '=', $this->seccion)
        ->where('temp_ventas.USER_ID', '=', $user->id)
        ->whereIn('temp_ventas.LINEA_CODIGO', $this->categorias)
        ->whereIn('temp_ventas.SUBLINEA_CODIGO', $this->sublineas)
        ->groupBy('temp_ventas.COD_PROD')
        ->orderBy('temp_ventas.COD_PROD')
        ->get()
        ->toArray();

        foreach ($ventas as $key => $value) {
            
            $lotes = DB::connection('retail')->table('LOTES')
                ->select(DB::raw('SUM(CANTIDAD) AS STOCK, 
                    SUM(CANTIDAD * COSTO) AS COSTO_RESTANTE'))
            ->where('COD_PROD', '=', $value->COD_PROD)
            ->where('ID_SUCURSAL', '=', $this->sucursal)
            ->get();

            $this->posicion = $this->posicion + 1;
            $this->total_precio = $this->total_precio + $value->PRECIO;
            $this->total_vendido = $this->total_vendido + $value->VENDIDO;
            $this->total_venta = $this->total_venta + $value->TOTAL;
            $this->total_cant_descuento = $this->total_descuento + $value->DESCUENTO;
            $this->total_utilidad = $this->total_utilidad + $value->UTILIDAD;
            $this->total_costo = $this->total_costo + $value->COSTO_TOTAL;
            $this->total_costo_unit = $this->total_costo_unit + $value->COSTO;
            $this->total_descuento = $this->total_descuento + $value->DESCUENTO_TOTAL;
            $this->total_stock = $this->total_stock + $lotes[0]->STOCK;
            $this->total_costo_restante = $this->total_costo_restante + $lotes[0]->COSTO_RESTANTE;

            $venta_array[] = array(

                'CODIGO'=> $value->COD_PROD,
                'DESCRIPCION'=>$value->DESCRIPCION,
                'GONDOLA'=>$value->GONDOLA,
                'VENTAS'=> $value->VENDIDO,
                'PRECIO'=> $value->PRECIO,
                'CANTIDAD DESCUENTO'=> $value->DESCUENTO,
                'TOTAL DESCUENTO' => $value->DESCUENTO_TOTAL,
                'TOTAL'=> $value->TOTAL,
                'COSTO' => $value->COSTO,
                'TOTAL COSTO' => $value->COSTO_TOTAL,
                'UTILIDAD' => $value->UTILIDAD,
                'STOCK'=> $lotes[0]->STOCK,
                'COSTO RESTANTE'=> $lotes[0]->COSTO_RESTANTE
            );

        }

        $venta_array[] = array(
            'CODIGO'=> '',
            'DESCRIPCION'=>'',
            'GONDOLA'=> 'TOTALES',
            'VENTAS'=> $this->total_vendido,
            'PRECIO'=> $this->total_precio,
            'CANTIDAD DESCUENTO'=> $this->total_cant_descuento,
            'TOTAL DESCUENTO' => $this->total_descuento,
            'TOTAL'=> $this->total_venta,
            'COSTO' => $this->total_costo_unit,
            'TOTAL COSTO' => $this->total_costo,
            'UTILIDAD' => $this->total_utilidad,
            'STOCK'=> $this->total_stock,
            'COSTO RESTANTE'=> $this->total_costo_restante
        );

        return $venta_array;

    }

    public function headings(): array
    {
        return ["CODIGO", "DESCRIPCION", "GONDOLA", "VENTAS", "PRECIO", "CANTIDAD DESCUENTO", "TOTAL DESCUENTO", "TOTAL", "COSTO", "COSTO TOTAL", "UTILIDAD", "STOCK", "COSTO RESTANTE"];
    }

    public function title(): string
    {
        return 'GENERAL';
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

            AfterSheet::class => function(AfterSheet $event) use($styleArray)  {
                $event->sheet->getStyle('A1:M1')->applyfromarray($styleArray);
                $this->posicion = $this->posicion + 1;
                $event->sheet->getStyle('A'.$this->posicion.':M'.$this->posicion)->applyfromarray($styleArray);
                $event->sheet->getStyle('A2:'.'A'.$this->posicion)->getNumberFormat()->setFormatCode('#');
                $event->sheet->getStyle('D2:'.'D'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0');
                $event->sheet->getStyle('E2:'.'E'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
                $event->sheet->getStyle('F2:'.'F'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0');
                $event->sheet->getStyle('G2:'.'G'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
                $event->sheet->getStyle('H2:'.'H'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
                $event->sheet->getStyle('I2:'.'I'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
                $event->sheet->getStyle('J2:'.'J'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
                $event->sheet->getStyle('K2:'.'K'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
                $event->sheet->getStyle('L2:'.'L'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0');
                $event->sheet->getStyle('M2:'.'M'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            }

        ];
    }
    
}
