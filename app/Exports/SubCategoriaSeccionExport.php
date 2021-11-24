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
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;

class SubCategoriaSeccionExport implements FromArray, WithHeadings, ShouldAutoSize, WithTitle, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $categorias;
    protected $sucursal;
    protected $sublineas;
    protected $AllSubCategory;
    protected $AllCategory;
    protected $seccion;
    protected $total_vendido = 0;
    protected $total_venta = 0;
    protected $porcentaje;
    public  $posicion = 1;
    public  $subcategoria_array = [];
    private $AllSecciones;

    public function __construct($datos){
        
        $this->AllCategory = $datos["AllCategory"];
        $this->categorias = $datos["Categorias"];
        $this->sucursal = $datos["Sucursal"];
        $this->AllSubCategory = $datos["AllSubCategory"];
        $this->sublineas = $datos["SubCategorias"];
        $this->seccion = $datos["Seccion"];
        $this->AllSecciones = $datos["AllSecciones"];
    }

    public function  array(): array {

        $user = auth()->user();

        if($this->AllSecciones){
            $subcategoria = DB::connection('retail')->table('TEMP_VENTAS')
                ->select(DB::raw('
                    TEMP_VENTAS.LINEA_CODIGO, 
                    SUM(TEMP_VENTAS.VENDIDO) AS CANTIDAD, 
                    TEMP_VENTAS.SUBCATEGORIA AS DESCRIPCION,
                    SUM(TEMP_VENTAS.PRECIO) AS TOTAL,
                    0 AS PORCENTAJE'))
            ->where('TEMP_VENTAS.ID_SUCURSAL', '=', $this->sucursal)
            ->where('TEMP_VENTAS.USER_ID', '=', $user->id)
            ->groupBy('TEMP_VENTAS.SUBLINEA_CODIGO')
            ->orderBy('TEMP_VENTAS.SUBCATEGORIA')
            ->get()
            ->toArray();
        }else{
             $subcategoria = DB::connection('retail')->table('TEMP_VENTAS')
                ->select(DB::raw('
                    TEMP_VENTAS.LINEA_CODIGO, 
                    SUM(TEMP_VENTAS.VENDIDO) AS CANTIDAD, 
                    TEMP_VENTAS.SUBCATEGORIA AS DESCRIPCION,
                    SUM(TEMP_VENTAS.PRECIO) AS TOTAL,
                    0 AS PORCENTAJE'))
            ->where('TEMP_VENTAS.ID_SUCURSAL', '=', $this->sucursal)
            ->where('TEMP_VENTAS.SECCION_CODIGO', '=', $this->seccion)
            ->where('TEMP_VENTAS.USER_ID', '=', $user->id)
            ->whereIn('temp_ventas.LINEA_CODIGO', $this->categorias)
            ->whereIn('temp_ventas.SUBLINEA_CODIGO', $this->sublineas)
            ->groupBy('TEMP_VENTAS.SUBLINEA_CODIGO')
            ->orderBy('TEMP_VENTAS.SUBCATEGORIA')
            ->get()
            ->toArray();
        }

        $total_ventas = array_sum(array_column($subcategoria, 'CANTIDAD'));

        foreach ($subcategoria as $key => $value) {
            $subcategoria[$key]->PORCENTAJE = round(($value->CANTIDAD * 100) / $total_ventas, 2);

            $this->posicion = $this->posicion + 1;
            $this->total_vendido = $this->total_vendido + $value->CANTIDAD;
            $this->total_venta = $this->total_venta + $value->TOTAL;
            $this->porcentaje = $this->porcentaje + $value->PORCENTAJE;

            $subcategoria_array[] = array(
                'CODIGO' => $value->LINEA_CODIGO,
                'DESCRIPCION' => $value->DESCRIPCION,
                'VENTAS' => $value->CANTIDAD,
                'TOTAL' => $value->TOTAL,
                'PORCENTAJE' => $value->PORCENTAJE
            );
        }

        $subcategoria_array[] = array(
            'CODIGO' => '',
            'DESCRIPCION' => 'TOTALES',
            'VENTAS' =>$this->total_vendido,
            'TOTAL' => $this->total_venta,
            'PORCENTAJE' => $this->porcentaje
        );

        return $subcategoria_array;

    }

    public function headings(): array
    {
        return ["CODIGO", "DESCRIPCION", "VENTAS", "TOTAL", "PORCENTAJE"];
    }

    public function title(): string
    {
        return 'SUBCATEGORIAS';
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
                $event->sheet->getStyle('A1:E1')->applyfromarray($styleArray);
                $this->posicion = $this->posicion + 1;
                $event->sheet->getStyle('A'.$this->posicion.':E'.$this->posicion)->applyfromarray($styleArray);
                $event->sheet->getStyle('C2:'.'C'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0');
                $event->sheet->getStyle('D2:'.'D'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
                $event->sheet->getStyle('E2:'.'E'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            }

        ];
    }
}
