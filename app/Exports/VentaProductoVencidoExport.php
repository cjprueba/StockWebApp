<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Stock;
use DateTime;

class VentaProductoVencidoExport implements FromArray, WithHeadings, WithTitle, WithEvents, WithDrawings,WithColumnFormatting 
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    protected $total;
    protected $vencidos;
    protected $row_number;
    protected $sucursal;
    protected $stock;
    protected $inicio;
    protected $final;

    public function __construct($datos){
       
        $this->sucursal = $datos["Sucursal"];
        $this->inicio = date('Y-m-d', strtotime($datos["Inicio"]));
        $this->final  =  date('Y-m-d', strtotime($datos["Final"]));

    }

    public function  array(): array{

        $vencidos = Stock::query()->select(
            DB::raw('LOTES.COD_PROD, 
                    0 AS IMAGEN,
                    LOTES.LOTE AS LOTE, 
                    LOTES.CANTIDAD_INICIAL AS CANTIDAD_INICIAL_LOTE,
                    IFNULL(LOTES.CANTIDAD,"0") AS STOCK_LOTE,
	                VENTASDET.PRECIO_UNIT AS PRECIO,
	                SUM(VENTASDET_TIENE_LOTES.CANTIDAD) AS VENDIDO,
                    SUBSTR(LOTES.FECHA_VENC, 1, 11) AS FECHA_VENCIMIENTO,
                    SUBSTR((SELECT MAX(L2.FECALTAS) FROM LOTES AS L2 WHERE L2.ID_SUCURSAL=LOTES.ID_SUCURSAL AND L2.COD_PROD=LOTES.COD_PROD), 1, 11) AS ULTIMA_ENTRADA,
                    PROVEEDORES.NOMBRE AS PROVEEDOR,
                    LINEAS.DESCRIPCION AS CATEGORIA, 
                    PRODUCTOS.DESCRIPCION,  
                    DETALLE_PROD.DESCRIPCION AS DESCRIPCION_LARGA, 
                    PRODUCTOS_AUX.PREMAYORISTA')
        )
        ->leftJoin('PRODUCTOS_AUX', function($join){
                            $join->on('PRODUCTOS_AUX.CODIGO', '=', 'lOTES.COD_PROD')
                                 ->on('lOTES.ID_SUCURSAL', '=', 'PRODUCTOS_AUX.ID_SUCURSAL');
                         })
        ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'LOTES.COD_PROD')
        ->leftjoin('LINEAS','LINEAS.CODIGO','=','PRODUCTOS.LINEA')
        ->leftjoin('PROVEEDORES','PROVEEDORES.CODIGO','=','PRODUCTOS_AUX.PROVEEDOR')
        ->leftjoin('DETALLE_PROD', 'PRODUCTOS.CODIGO', '=', 'DETALLE_PROD.COD_PROD')
        ->leftjoin('VENTASDET_TIENE_LOTES','VENTASDET_TIENE_LOTES.ID_LOTE','=','LOTES.ID')
        ->rightjoin('VENTASDET', function($join){
                    $join->on('VENTASDET.ID', '=', 'VENTASDET_TIENE_LOTES.ID_VENTAS_DET')
                         ->on('VENTASDET.FECALTAS', '>=', 'lOTES.FECHA_VENC');
                })
        ->Where('LOTES.ID_SUCURSAL', '=', $this->sucursal)
        ->whereBetween('LOTES.FECHA_VENC', [$this->inicio, $this->final]);

        $vencidos = $vencidos->whereRaw('(IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = LOTES.COD_PROD) AND (l.ID_SUCURSAL = LOTES.ID_SUCURSAL))),0)) >= 0');

        
        $vencidos = $vencidos
        ->groupBy('LOTES.COD_PROD','LOTES.lOTE')
        ->get()
        ->toArray();

        $this->total = count($vencidos);
        $this->vencidos = $vencidos;

        return $vencidos;

    }
     public function headings(): array
    {
        return ["CODIGO", "IMAGEN","LOTE","CANTIDAD_INICIAL","STOCK_LOTE", "PRE. VENTA", "CANTIDAD_VENDIDA","FECHA_VENCIMIENTO","ULTIMA_ENTRADA","PROVEEDOR","CATEGORIA", "DESCRIPCION", "DESCRIPCION DETALLADA", "PRE. M."];
    }

    public function title(): string
    {
        return 'VENCIMIENTOS';
    }

    public function registerEvents(): array
    {

        $this->row_number = 2;

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

                $event->sheet->getStyle('A1:N1')->applyfromarray($styleArray);
                $event ->sheet->getDelegate()->getColumnDimension('B')->setWidth(15);
                $event ->sheet->getDelegate()->getColumnDimension('A')->setWidth(15);
                $event ->sheet->getDelegate()->getColumnDimension('D')->setWidth(15);
                $event ->sheet->getDelegate()->getColumnDimension('E')->setWidth(15);
                $event ->sheet->getDelegate()->getColumnDimension('F')->setWidth(8);
                $event ->sheet->getDelegate()->getColumnDimension('G')->setWidth(15);
                $event ->sheet->getDelegate()->getColumnDimension('H')->setWidth(15);
                $event ->sheet->getDelegate()->getColumnDimension('I')->setWidth(15);
                $event ->sheet->getDelegate()->getColumnDimension('J')->setWidth(20);
                $event ->sheet->getDelegate()->getColumnDimension('K')->setWidth(30);
                $event ->sheet->getDelegate()->getColumnDimension('L')->setWidth(40);
                $event ->sheet->getDelegate()->getColumnDimension('M')->setWidth(50);
                $event ->sheet->getDelegate()->getColumnDimension('N')->setWidth(8);

                for( $intRowNumber = 2; $intRowNumber <= $this->total + 1; $intRowNumber++){
                    $event->sheet->getRowDimension($intRowNumber)->setRowHeight(80);
                }
                
                foreach ($this->vencidos as $key => $value) {
                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawing->setName('Logo');
                    $drawing->setDescription('Logo');
                    $imagen = 'C:/laragon/www/StockWebApp/public/images/'.$value['COD_PROD'].'.jpg';
                    // $imagen = 'C:/inetpub/wwwroot/Master/storage/app/public/imagenes/productos/'.$value['COD_PROD'].'.jpg';

                    if(!file_exists($imagen)) {
                        $drawing->setPath('C:/laragon/www/StockWebApp/public/images/SinImagen.png');
                        // $drawing->setPath('C:/inetpub/wwwroot/Master/public/images/SinImagen.png');
                    } else {
                        $drawing->setPath($imagen);
                    }

                    $drawing->setHeight(100);
                    $drawing->setCoordinates('B'.$this->row_number);
                    $drawing->setWorksheet($event->sheet->getDelegate());
                    $drawing_array[] = $drawing;

                    $this->row_number +=1;

                }
            }

        ];
    }

    public function drawings(): array{

        $drawing_array = [];

        return $drawing_array;
    }
    
    public function columnFormats(): array{

        return [
           'A' => NumberFormat::FORMAT_NUMBER,

        ];
    }
}
