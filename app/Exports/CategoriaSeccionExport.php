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
use DateTime;

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
    protected $AllSubCategory;
    protected $AllCategory;
    protected $datos2;

    public function __construct($datos)
    {
        $this->categorias = $datos["Categorias"];
        $this->sucursal = $datos["Sucursal"];
        $this->AllCategory=$datos["AllCategory"];
        $this->seccion=$datos["Seccion"];
        $this->proveedores=$datos["SubCategorias"];
        $this->AllSubCategory=$datos["AllSubCategory"];
        $this->inicio = date('Y-m-d', strtotime($datos["Inicio"]));
        $this->final  =  date('Y-m-d', strtotime($datos["Final"]));
    }

    public function  array(): array
    {

        $categorias = Ventas_det::select(
            DB::raw('LINEAS.CODIGO, SUM(CANTIDAD) AS CANTIDAD, LINEAS.DESCRIPCION, 0 AS PORCENTAJE')
        )
        ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
        ->leftJoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
        ->leftjoin('GONDOLA_TIENE_PRODUCTOS',function($join){
             $join->on('GONDOLA_TIENE_PRODUCTOS.GONDOLA_COD_PROD','=','PRODUCTOS.CODIGO')
                  ->on('GONDOLA_TIENE_PRODUCTOS.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL');
        })
        /* ->leftjoin('GONDOLA_TIENE_PRODUCTOS','GONDOLA_TIENE_PRODUCTOS.GONDOLA_COD_PROD','=','VENTASDET.COD_PROD')*/
        ->leftjoin('productos_tiene_seccion','productos_tiene_seccion.COD_PROD','=','VENTASDET.COD_PROD')
        ->leftJoin('gondolas', 'gondolas.ID', '=', 'gondola_tiene_productos.ID_GONDOLA')
        ->Where('VENTASDET.ID_SUCURSAL', '=', $this->sucursal)
        ->Where('VENTASDET.ANULADO', '=',0)
       ->where('PRODUCTOS_TIENE_SECCION.SECCION', '=', $this->seccion)
        ->whereBetween('VENTASDET.FECALTAS', [$this->inicio, $this->final]);
        if($this->AllSubCategory==false){
            $categorias->whereIn('PRODUCTOS.LINEA', $this->categorias);
        }
        $categorias=$categorias->groupBy('PRODUCTOS.LINEA')
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
