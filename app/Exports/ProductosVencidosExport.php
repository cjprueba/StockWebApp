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
class ProductosVencidosExport implements FromArray, WithHeadings, WithTitle, WithEvents, WithDrawings,WithColumnFormatting 
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

    public function __construct($datos)
    {
       
        $this->sucursal = $datos["Sucursal"];
        /*$this->stock = $datos["Stock"];*/
        $this->inicio = date('Y-m-d', strtotime($datos["Inicio"]));
        $this->final  =  date('Y-m-d', strtotime($datos["Final"]));

    }
    public function  array(): array
    {



        $vencidos = Stock::query()->select(
            DB::raw('LOTES.COD_PROD, 
                    0 AS IMAGEN,
                    LOTES.LOTE AS LOTE, 
                    IFNULL(LOTES.CANTIDAD,"0") AS STOCK_LOTE,
                    LOTES.CANTIDAD_INICIAL AS CANTIDAD_INICIAL_LOTE,
                    PROVEEDORES.NOMBRE AS PROVEEDOR,
                    LINEAS.DESCRIPCION AS CATEGORIA, 
                    PRODUCTOS.DESCRIPCION, 
                    DETALLE_PROD.DESCRIPCION AS DESCRIPCION_LARGA, 
                    PRODUCTOS_AUX.PREC_VENTA, 
                    LOTES.COSTO AS COSTO')
        )
        ->leftJoin('PRODUCTOS_AUX', function($join){
                            $join->on('PRODUCTOS_AUX.CODIGO', '=', 'lOTES.COD_PROD')
                                 ->on('lOTES.ID_SUCURSAL', '=', 'PRODUCTOS_AUX.ID_SUCURSAL');
                         })
        ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'LOTES.COD_PROD')
        ->leftjoin('proveedores','proveedores.codigo','=','PRODUCTOS_AUX.PROVEEDOR')
        ->leftjoin('LINEAS','LINEAS.CODIGO','=','PRODUCTOS.LINEA')
        ->leftjoin('DETALLE_PROD', 'PRODUCTOS.CODIGO', '=', 'DETALLE_PROD.COD_PROD')
        ->Where('LOTES.ID_SUCURSAL', '=', $this->sucursal)
        ->WHERE('PRODUCTOS_AUX.PROVEEDOR','=',19)->orderBy('LINEAS.DESCRIPCION','ASC')->orderBy('LOTES.COD_PROD');
       /* ->whereBetween('LOTES.FECHA_VENC', [$this->inicio, $this->final]);*/

/*        if ($this->stock === false) {

        	$vencidos = $vencidos->whereRaw('(IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = LOTES.COD_PROD) AND (l.ID_SUCURSAL = LOTES.ID_SUCURSAL))),0)) = 0');

        } else {*/

        	/*$vencidos = $vencidos->whereRaw('(IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = LOTES.COD_PROD) AND (l.ID_SUCURSAL = LOTES.ID_SUCURSAL))),0)) >= 0');*/

/*        }*/
        
        $vencidos = $vencidos
        ->get()
        ->toArray();

        $this->total = count($vencidos);
        $this->vencidos = $vencidos;

        return $vencidos;

    }
     public function headings(): array
    {
        return ["CODIGO", "IMAGEN","LOTE","STOCK_LOTE","CANTIDAD_INICIAL_LOTE","PROVEEDOR","CATEGORIA", "DESCRIPCION", "DESCRIPCION DETALLADA", "PRE. V.", "COSTO"];
    }

    public function title(): string
    {
        return 'LOTES_COSTOS';
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

                $event->sheet->getStyle('A1:M1')->applyfromarray($styleArray);
                $event ->sheet->getDelegate()->getColumnDimension('B')->setWidth(15);
                $event ->sheet->getDelegate()->getColumnDimension('A')->setWidth(15);
                $event ->sheet->getDelegate()->getColumnDimension('E')->setWidth(15);
                $event ->sheet->getDelegate()->getColumnDimension('F')->setWidth(25);
                $event ->sheet->getDelegate()->getColumnDimension('G')->setWidth(25);
                $event ->sheet->getDelegate()->getColumnDimension('H')->setWidth(35);
                $event ->sheet->getDelegate()->getColumnDimension('I')->setWidth(35);
                $event ->sheet->getDelegate()->getColumnDimension('J')->setWidth(50);
                $event ->sheet->getDelegate()->getColumnDimension('K')->setWidth(50);

                for( $intRowNumber = 2; $intRowNumber <= $this->total + 1; $intRowNumber++){
                    $event->sheet->getRowDimension($intRowNumber)->setRowHeight(80);
                }
                
                foreach ($this->vencidos as $key => $value) {
                	$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
			        $drawing->setName('Logo');
			        $drawing->setDescription('Logo');
			       /* $imagen = 'C:/laragon/www/StockWebApp-ultimo/public/images/'.$value['COD_PROD'].'.jpg';*/
			        $imagen = 'C:/inetpub/wwwroot/Master/storage/app/public/imagenes/productos/'.$value['COD_PROD'].'.jpg';

			        if(!file_exists($imagen)) {
			        	/*$drawing->setPath('C:/laragon/www/StockWebApp-ultimo/public/images/SinImagen.png');*/
			        	$drawing->setPath('C:/inetpub/wwwroot/Master/public/images/SinImagen.png');
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

    public function drawings(): array
    {

    	$drawing_array = [];
    	
        

     //    for( $intRowNumber = 1; $intRowNumber <= 30; $intRowNumber++){
     //    	$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
	    //     $drawing->setName('Logo');
	    //     $drawing->setDescription('Logo');
	    //     $drawing->setPath('C:\laragon\www\StockWebApp\public\images\SinImagen.png');
	    //     $drawing->setHeight(30);
	    //     $drawing->setCoordinates('B'.$intRowNumber);
	    //     $drawing_array[] = $drawing;
	    // }   
     //   /*  $drawing->ShouldAutoSize(true);*/

        return $drawing_array;
    }
        public function columnFormats(): array
    {
         return [
           'A' => NumberFormat::FORMAT_NUMBER,

        ];
    }
}
