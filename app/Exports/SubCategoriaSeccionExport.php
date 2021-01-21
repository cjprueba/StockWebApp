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

        $subcategoria = Ventas_det::query()->select(
            DB::raw('SUBLINEAS.CODIGO, SUM(CANTIDAD) AS CANTIDAD, SUBLINEAS.DESCRIPCION, 0 as PORCENTAJE')
        )
        ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
        ->leftJoin('SUBLINEAS', 'SUBLINEAS.CODIGO', '=', 'PRODUCTOS.SUBLINEA')
        ->leftJoin('PRODUCTOS_TIENE_SECCION', 'PRODUCTOS_TIENE_SECCION.COD_PROD', '=', 'VENTASDET.COD_PROD')
        ->Where('VENTASDET.ID_SUCURSAL', '=', $this->sucursal)
        ->whereIn('PRODUCTOS.SUBLINEA', $this->sublineas)
        ->where('PRODUCTOS_TIENE_SECCION.SECCION', '=', $this->seccion)
        ->whereBetween('VENTASDET.FECALTAS', [$this->inicio, $this->final])
        ->groupBy('PRODUCTOS.SUBLINEA')
        ->get()
        ->toArray();

        $total_ventas = array_sum(array_column($subcategoria, 'CANTIDAD'));

        foreach ($subcategoria as $key => $value) {
            $subcategoria[$key]['PORCENTAJE'] = round(($value['CANTIDAD'] * 100) / $total_ventas, 2);
        }

        return $subcategoria;

    }

    public function headings(): array
    {
        return ["CODIGO", "VENTAS", "DESCRIPCION", "PORCENTAJE"];
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
                $event->sheet->getStyle('A1:D1')->applyfromarray($styleArray);
            }

        ];
    }
}
