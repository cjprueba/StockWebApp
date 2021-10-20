<?php

namespace App\Exports\Reportes\Gondola\Productos;

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
use App\Gondola_tiene_Productos;

class GondolasProductosImage implements FromArray, WithHeadings, WithTitle, WithEvents,WithColumnFormatting 
{
    /**
    * @return \Illuminate\Support\Collection
    */
   	protected $productos;
   	protected $row_number;
    protected $gondola;
    protected $titulo;
    protected $total;
    protected $stock;

   public function __construct($datos)
    {
        $this->titulo = $datos["Titulo"];
        $this->gondola = $datos["Gondola"];
        $this->stock = $datos["Stock"];
       

    }
     public function  array(): array
    {



        $productos = Gondola_tiene_Productos::Select(
            DB::raw('PRODUCTOS_AUX.CODIGO AS COD_PROD, 
                  
                    IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),"0") AS STOCK,
                    PROVEEDORES.NOMBRE AS PROVEEDOR,
                    LINEAS.DESCRIPCION AS CATEGORIA, 
                    PRODUCTOS.DESCRIPCION,  
                    PRODUCTOS_AUX.PREC_VENTA, 
                    PRODUCTOS_AUX.PREMAYORISTA')
        )
        ->leftjoin('PRODUCTOS_AUX','PRODUCTOS_AUX.ID','=','GONDOLA_TIENE_PRODUCTOS.FK_PRODUCTOS_AUX')
       /* ->leftJoin('PRODUCTOS_AUX', function($join){
                            $join->on('PRODUCTOS_AUX.CODIGO', '=', 'lOTES.COD_PROD')
                                 ->on('lOTES.ID_SUCURSAL', '=', 'PRODUCTOS_AUX.ID_SUCURSAL');
                         })*/
        ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
        ->leftjoin('proveedores','proveedores.codigo','=','PRODUCTOS_AUX.PROVEEDOR')
        ->leftjoin('LINEAS','LINEAS.CODIGO','=','PRODUCTOS.LINEA')
      
        ->Where('GONDOLA_TIENE_PRODUCTOS.ID_GONDOLA', '=', $this->gondola);
        

        if ($this->stock === false) {

        	$productos = $productos->whereRaw('(IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0)) <= 0');

        } else {

        	$productos = $productos->whereRaw('(IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0)) > 0');

        }
       
        
        $productos = $productos->get()
        ->toArray();

        $this->total = count($productos);
        $this->productos = $productos;

        return $productos;

    }

    public function headings(): array
    {
        return ["CODIGO", "STOCK","PROVEEDOR","CATEGORIA", "DESCRIPCION", "PRE. V.", "PRE. M."];
        
    }

    public function title(): string
    {
        return $this->titulo;
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

                $event->sheet->getStyle('A1:G1')->applyfromarray($styleArray);
                $event ->sheet->getDelegate()->getColumnDimension('A')->setWidth(15);
                $event ->sheet->getDelegate()->getColumnDimension('C')->setWidth(35);
                $event ->sheet->getDelegate()->getColumnDimension('D')->setWidth(35);
                $event ->sheet->getDelegate()->getColumnDimension('E')->setWidth(100);
                $event ->sheet->getDelegate()->getColumnDimension('F')->setWidth(15);
                $event ->sheet->getDelegate()->getColumnDimension('G')->setWidth(15);

                for( $intRowNumber = 2; $intRowNumber <= $this->total + 1; $intRowNumber++){
                    $event->sheet->getRowDimension($intRowNumber)->setRowHeight(20);
                }
                
               /* foreach ($this->productos as $key => $value) {
                	$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
			        $drawing->setName('Logo');
			        $drawing->setDescription('Logo');

			        $imagen = 'C:/inetpub/wwwroot/Master/storage/app/public/imagenes/productos/'.$value['COD_PROD'].'.jpg';*/
			      /* $imagen = 'C:/laragon/www/StockWebApp/public/storage/imagenes/productos/'.$value['COD_PROD'].'.jpg';*/
			        

			        /*if(!file_exists($imagen)) {
			        	$drawing->setPath('C:/inetpub/wwwroot/Master/public/images/SinImagen.png');
			        } else {
			        	$drawing->setPath($imagen);
			        }

			        $drawing->setHeight(100);
			        $drawing->setCoordinates('B'.$this->row_number);
			        $drawing->setWorksheet($event->sheet->getDelegate());
			        $drawing_array[] = $drawing;

			        $this->row_number +=1;

                }*/
            }

        ];
    }
/*     public function drawings(): array
    {

    	$drawing_array = [];
    	
        


        return $drawing_array;
    }*/
        public function columnFormats(): array
    {
         return [
           'A' => NumberFormat::FORMAT_NUMBER,

        ];
    }

}
