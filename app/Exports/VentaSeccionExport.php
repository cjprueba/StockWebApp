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

    protected $inicio;
    protected $final;
    protected $categorias;
    protected $sucursal;
    protected $sublineas;
    protected $seccion;
    protected $AllSubCategory;
    protected $AllCategory;


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

        $ventas = Ventas_det::query()->select(
            DB::raw('VENTASDET.COD_PROD, SUM(CANTIDAD) AS CANTIDAD, SUM(VENTASDET.PRECIO) AS TOTAL, VENTASDET.DESCRIPCION, PRODUCTOS_AUX.PREC_VENTA, 0 as 10OFF, 0 as T, 0 as C, 0 as R, GONDOLAS.DESCRIPCION AS GONDOLA'),
            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = VENTASDET.COD_PROD) AND (l.ID_SUCURSAL = VENTASDET.ID_SUCURSAL))),"0") AS STOCK')
        )
        ->leftJoin('PRODUCTOS_AUX', function($join){
                            $join->on('PRODUCTOS_AUX.CODIGO', '=', 'VENTASDET.COD_PROD')
                                 ->on('VENTASDET.ID_SUCURSAL', '=', 'PRODUCTOS_AUX.ID_SUCURSAL');
                         })
        
        ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
        ->leftjoin('GONDOLA_TIENE_PRODUCTOS',function($join){
             $join->on('GONDOLA_TIENE_PRODUCTOS.GONDOLA_COD_PROD','=','PRODUCTOS.CODIGO')
                  ->on('GONDOLA_TIENE_PRODUCTOS.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL');
        })
        /*->leftjoin('GONDOLA_TIENE_PRODUCTOS','GONDOLA_TIENE_PRODUCTOS.GONDOLA_COD_PROD','=','VENTASDET.COD_PROD')*/
        ->leftjoin('productos_tiene_seccion','productos_tiene_seccion.COD_PROD','=','VENTASDET.COD_PROD')
        ->leftJoin('gondolas', 'gondolas.ID', '=', 'gondola_tiene_productos.ID_GONDOLA')
        ->Where('VENTASDET.ID_SUCURSAL', '=', $this->sucursal)
        ->where('PRODUCTOS_TIENE_SECCION.SECCION', '=', $this->seccion)
        ->where('VENTASDET.ANULADO', '=', 0)
        ->whereBetween('VENTASDET.FECALTAS', [$this->inicio, $this->final]);
        if($this->AllSubCategory==false){
            $ventas->whereIn('PRODUCTOS.LINEA', $this->categorias);
        }
        if($this->AllSubCategory==false){
            $ventas->whereIn('PRODUCTOS.SUBLINEA', $this->sublineas);
        }
        
        
        $ventas=$ventas->groupBy('VENTASDET.COD_PROD')
        ->get()
        ->toArray();

        foreach ($ventas as $key => $value) {

            $producto = Ventas_det::select(
                DB::raw('SUM(CANTIDAD) AS CANTIDAD, PORCENTAJE')
            )
            ->leftJoin('VENTASDET_DESCUENTO', 'VENTASDET_DESCUENTO.FK_VENTASDET', '=', 'VENTASDET.ID')
            ->Where('VENTASDET.ID_SUCURSAL', '=', $this->sucursal)
            ->Where('VENTASDET.COD_PROD', '=', $value['COD_PROD'])
            ->whereBetween('VENTASDET.FECALTAS', [$this->inicio, $this->final])
            ->groupBy('VENTASDET_DESCUENTO.PORCENTAJE')
            ->get()
            ->toArray();

            foreach ($producto as $key_descuento => $value) {

                if ( $value['PORCENTAJE']===10  ) {
                    $ventas[$key]['10OFF'] = $value['CANTIDAD'];
                } else {
                    $ventas[$key]['10OFF'] = '0';
                }

                if ($value['PORCENTAJE'] === 30) {
                    $ventas[$key]['T'] = $value['CANTIDAD'];
                } else {
                    $ventas[$key]['T'] = '0';
                }

                if ($value['PORCENTAJE'] === 50) {
                    $ventas[$key]['C'] = $value['CANTIDAD'];
                } else {
                    $ventas[$key]['C'] = '0';
                }

                if ($value['PORCENTAJE'] === 100) {
                    $ventas[$key]['R'] = $value['CANTIDAD'];
                } else {
                    $ventas[$key]['R'] = '0';
                }
            }

        }

        return $ventas;

    }

    public function headings(): array
    {
        return ["CODIGO", "VENTAS", "TOTAL", "DESCRIPCION", "PRECIO", "10% OFF", "30% OFF", "50% OFF", "100% OFF", "GONDOLA", "STOCK"];
    }

    public function title(): string
    {
        return 'TOP VENTAS';
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
                $event->sheet->getStyle('A1:K1')->applyfromarray($styleArray);
                // $event ->sheet->getDelegate()->getColumnDimension('A')->setWidth(500);
                // $event->sheet->getRowDimension(1)->setRowHeight(500);
            }

        ];
    }
    
}
