<?php

namespace App\Exports\Reportes\Inventario;

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
use App\Inventario;
USE App\ProductosAux;
use Illuminate\Support\Facades\Log;
class InventarioImageGondolaExport implements FromArray, WithHeadings, WithTitle, WithEvents, WithDrawings,WithColumnFormatting
{

	private $ID_INVENTARIO;
	private $ID_GONDOLA;
	private $productos=array();
	private $total;
	private $row_number;
	public function __construct($datos)
    {
    	
        $this->ID_INVENTARIO = $datos["ID_INV"];
        $this->ID_GONDOLA=$datos["ID_GONDOLA"];

    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function  array(): array
    {
    	$user=auth()->user();
        $inventario = ProductosAux::query()->select(
            DB::raw('PRODUCTOS_AUX.CODIGO AS COD_PROD,
		    0 AS IMAGEN,
		    IFNULL((SELECT SUM(l4.CANTIDAD) FROM lotes AS l4  WHERE ((l4.COD_PROD = PRODUCTOS_AUX.CODIGO)  AND (l4.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS STOCK,
		    IFNULL(PROVEEDORES.NOMBRE, "INDEFINIDO") AS PROVEEDOR,
		    IFNULL(LINEAS.DESCRIPCION, "INDEFINIDO") AS CATEGORIA,
		    PRODUCTOS.DESCRIPCION,
		    PRODUCTOS_AUX.PREC_VENTA,
		    PRODUCTOS_AUX.PREMAYORISTA')
        )
      
        ->leftJoin('CONTEO_DET', function($join){
                            $join->on('CONTEO_DET.COD_PROD', '=','PRODUCTOS_AUX.CODIGO')
                            	->on('CONTEO_DET.ID_SUCURSAL', '=','PRODUCTOS_AUX.ID_SUCURSAL')
                            	->where('CONTEO_DET.FK_CONTEO', '=',$this->ID_INVENTARIO);
                         })
        
        ->leftjoin('GONDOLA_TIENE_PRODUCTOS','GONDOLA_TIENE_PRODUCTOS.FK_PRODUCTOS_AUX','=','PRODUCTOS_AUX.ID')
      
        ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
        ->leftjoin('proveedores','proveedores.codigo','=','PRODUCTOS_AUX.PROVEEDOR')
        ->leftjoin('LINEAS','LINEAS.CODIGO','=','PRODUCTOS.LINEA')
        ->Where('GONDOLA_TIENE_PRODUCTOS.ID_GONDOLA', '=', $this->ID_GONDOLA)
        ->where('PRODUCTOS_AUX.ID_SUCURSAL','=',$user->id_sucursal)
        ->whereNull('CONTEO_DET.CONTEO')
        ->whereRaw('IFNULL((SELECT SUM(l4.CANTIDAD) FROM lotes AS l4  WHERE ((l4.COD_PROD = PRODUCTOS_AUX.CODIGO)  AND (l4.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0)>0')
        
        ->get()
        ->toArray();
        log::error(["data"=>$inventario]);

        
        
       
        

        $this->total = count($inventario);
        $this->productos = $inventario;

        return $inventario;
    }
        public function headings(): array
    {
        return ["CODIGO", "IMAGEN", "STOCK","PROVEEDOR","CATEGORIA", "DESCRIPCION", "PRE. V.", "PRE. M."];
    }
        public function title(): string
    {
        return 'Inventario'.$this->ID_INVENTARIO;
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

                $event->sheet->getStyle('A1:H1')->applyfromarray($styleArray);
                $event ->sheet->getDelegate()->getColumnDimension('B')->setWidth(15);
                $event ->sheet->getDelegate()->getColumnDimension('A')->setWidth(15);
                $event ->sheet->getDelegate()->getColumnDimension('D')->setWidth(15);
                $event ->sheet->getDelegate()->getColumnDimension('E')->setWidth(25);
                $event ->sheet->getDelegate()->getColumnDimension('F')->setWidth(50);
                $event ->sheet->getDelegate()->getColumnDimension('G')->setWidth(15);
                $event ->sheet->getDelegate()->getColumnDimension('H')->setWidth(15);

                for( $intRowNumber = 2; $intRowNumber <= $this->total + 1; $intRowNumber++){
                    $event->sheet->getRowDimension($intRowNumber)->setRowHeight(80);
                }
                
                foreach ($this->productos as $key => $value) {
                	$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
			        $drawing->setName($value['COD_PROD']);
			        $drawing->setDescription($value['COD_PROD']);

			       // $imagen = 'C:/laragon/www/StockWebApp/storage/'.$value['COD_PROD'].'.jpg'; 
			           $imagen = 'C:/inetpub/wwwroot/Master/storage/app/public/imagenes/productos/'.$value['COD_PROD'].'.jpg';

			        if(!file_exists($imagen)) {
			        	//$drawing->setPath('C:/laragon/www/StockWebApp/storage/product.png');
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
