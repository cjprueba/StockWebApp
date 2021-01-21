<?php

namespace App\Exports;

use App\Ventas_det;
use Maatwebsite\Excel\Concerns\FromCollection;
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

class CategoriaSeccionExport implements FromArray, WithHeadings, ShouldAutoSize, WithTitle, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $inicio;
    protected $final;
    protected $categorias;
    protected $sucursal;
    protected $sublineas;
    protected $seccion;

    public function __construct(string $inicio, string $final, array $categorias, int $sucursal, array $sublineas, int $seccion)
    {
        $this->inicio = $inicio;
        $this->final = $final;
        $this->categorias = $categorias;
        $this->sucursal = $sucursal;
        $this->sublineas = $sublineas;
        $this->seccion = $seccion;
    }

    public function  array(): array
    {

        $categorias = Ventas_det::select(
            DB::raw('LINEAS.CODIGO, SUM(CANTIDAD) AS CANTIDAD, LINEAS.DESCRIPCION, 0 AS PORCENTAJE')
        )
        ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
        ->leftJoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
        ->leftJoin('PRODUCTOS_TIENE_SECCION', 'PRODUCTOS_TIENE_SECCION.COD_PROD', '=', 'VENTASDET.COD_PROD')
        ->Where('VENTASDET.ID_SUCURSAL', '=', $this->sucursal)
        ->whereIn('PRODUCTOS.LINEA', $this->categorias)
        ->where('PRODUCTOS_TIENE_SECCION.SECCION', '=', $this->seccion)
        ->whereBetween('VENTASDET.FECALTAS', [$this->inicio, $this->final])
        ->groupBy('PRODUCTOS.LINEA')
        ->get()
        ->toArray();

        $total_ventas = array_sum(array_column($categorias, 'CANTIDAD'));

        foreach ($categorias as $key => $value) {
            $categorias[$key]['PORCENTAJE'] = round(($value['CANTIDAD'] * 100) / $total_ventas, 2);
        }

        return $categorias;

    }

    public function headings(): array
    {
        return ["CODIGO", "VENTAS", "DESCRIPCION", "PORCENTAJE"];
    }

    public function title(): string
    {
        return 'CATEGORIAS';
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
                $event->sheet->getStyle('A1:D1')->applyfromarray($styleArray);
            }

        ];
    }
}
