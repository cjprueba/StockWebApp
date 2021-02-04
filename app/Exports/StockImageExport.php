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
use App\Stock;

class StockImageExport implements FromArray, WithHeadings, WithTitle, WithEvents, WithDrawings
{
    /**
    * @return \Illuminate\Support\Collection
    */

   	protected $total;
   	protected $ventas;
   	protected $row_number;
    protected $categorias;
    protected $categoriaSeccion;
    protected $AllCategory;
    protected $AllCategorySeccion;
    protected $sucursal;
    protected $sublineas;
    protected $stock;
    protected $seccion;
    protected $proveedores;
    protected $AllProveedores;
    protected $filtro;

    public function __construct($datos)
    {
        $this->categorias = $datos["Categorias"];
        $this->sucursal = $datos["Sucursal"];
        $this->stock = $datos["Stock"];
        $this->AllCategory=$datos["AllCategory"];
        $this->seccion=$datos["Seccion"];
        $this->proveedores=$datos["Proveedores"];
        $this->AllProveedores=$datos["AllProveedores"];
        $this->filtro=$datos["Filtro"];
        $this->categoriaSeccion=$datos["CategoriaSeccion"];
        $this->AllCategorySeccion=$datos["AllCategorySeccion"];

    }

    public function  array(): array
    {



        $ventas = Stock::query()->select(
            DB::raw('LOTES.COD_PROD, 0 AS IMAGEN, IFNULL(SUM(LOTES.CANTIDAD),"0") AS STOCK,(IFNULL((SELECT sum(l.CANTIDAD_INICIAL) FROM lotes as l WHERE (l.COD_PROD = LOTES.COD_PROD) AND (l.ID_SUCURSAL = LOTES.ID_SUCURSAL) AND l.FECALTAS BETWEEN date_add((select max(l1.FECALTAS) FROM LOTES AS l1 WHERE l1.COD_PROD =l.COD_PROD AND (l1.ID_SUCURSAL = l.ID_SUCURSAL)) , INTERVAL -3 WEEK) AND (select max(l2.FECALTAS) FROM LOTES AS l2 WHERE l2.COD_PROD =l.COD_PROD AND (l2.ID_SUCURSAL = l.ID_SUCURSAL))),0)) as CANTIDAD_INICIAL,MAX(LOTES.FECALTAS) AS ULTIMA_ENTRADA,0 AS CANTIDAD_PEDIDO, PRODUCTOS.DESCRIPCION, DETALLE_PROD.DESCRIPCION AS DESCRIPCION_LARGA, PRODUCTOS_AUX.PREC_VENTA, PRODUCTOS_AUX.PREMAYORISTA')
        )
        ->leftJoin('PRODUCTOS_AUX', function($join){
                            $join->on('PRODUCTOS_AUX.CODIGO', '=', 'lOTES.COD_PROD')
                                 ->on('lOTES.ID_SUCURSAL', '=', 'PRODUCTOS_AUX.ID_SUCURSAL');
                         })
        ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'LOTES.COD_PROD')
        ->leftjoin('GONDOLA_TIENE_PRODUCTOS',function($join){
         $join->on('GONDOLA_TIENE_PRODUCTOS.GONDOLA_COD_PROD','=','LOTES.COD_PROD')
              ->on('GONDOLA_TIENE_PRODUCTOS.ID_SUCURSAL','=','LOTES.ID_SUCURSAL');
        })
        ->leftjoin('GONDOLA_TIENE_SECCION','GONDOLA_TIENE_SECCION.ID_GONDOLA','=','GONDOLA_TIENE_PRODUCTOS.ID_GONDOLA')
        ->leftjoin('SECCIONES','SECCIONES.ID','=','GONDOLA_TIENE_SECCION.ID_SECCION')
        ->leftjoin('DETALLE_PROD', 'PRODUCTOS.CODIGO', '=', 'DETALLE_PROD.COD_PROD')
        ->Where('LOTES.ID_SUCURSAL', '=', $this->sucursal);
        

        if ($this->stock === false) {

        	$ventas = $ventas->whereRaw('(IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = LOTES.COD_PROD) AND (l.ID_SUCURSAL = LOTES.ID_SUCURSAL))),0)) = 0');

        } else {

        	$ventas = $ventas->whereRaw('(IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = LOTES.COD_PROD) AND (l.ID_SUCURSAL = LOTES.ID_SUCURSAL))),0)) >= 0');

        }
        if($this->filtro=="SECCION"){
             $ventas->where('SECCIONES.ID','=', $this->seccion);
             if($this->AllProveedores==false){
                 $ventas->whereIn('PRODUCTOS_AUX.PROVEEDOR',$this->proveedores);
             }
             if($this->AllCategorySeccion==false){
                $ventas->whereIn('PRODUCTOS.LINEA',$this->categoriaSeccion);
             }

         }

        if($this->filtro=="PROVEEDOR"){
            if($this->AllProveedores==false){
                 $ventas->whereIn('PRODUCTOS_AUX.PROVEEDOR',$this->proveedores);
             }
             if($this->AllCategory==false){
                $ventas->whereIn('PRODUCTOS.LINEA',$this->categorias);
             }
        }
        
        $ventas = $ventas->groupBy('LOTES.COD_PROD')
        ->get()
        ->toArray();

        $this->total = count($ventas);
        $this->ventas = $ventas;

        return $ventas;

    }

    public function headings(): array
    {
        return ["CODIGO", "IMAGEN", "STOCK","CANTIDAD_INICIAL","ULTIMA_ENTRADA","CANTIDAD_PEDIDO", "DESCRIPCION", "DESCRIPCION DETALLADA", "PRE. V.", "PRE. M."];
    }

    public function title(): string
    {
        return 'TOP VENTAS';
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

                $event->sheet->getStyle('A1:J1')->applyfromarray($styleArray);
                $event ->sheet->getDelegate()->getColumnDimension('B')->setWidth(15);
                $event ->sheet->getDelegate()->getColumnDimension('A')->setWidth(15);
                $event ->sheet->getDelegate()->getColumnDimension('D')->setWidth(15);
                $event ->sheet->getDelegate()->getColumnDimension('E')->setWidth(25);
                $event ->sheet->getDelegate()->getColumnDimension('F')->setWidth(15);
                $event ->sheet->getDelegate()->getColumnDimension('G')->setWidth(100);
                $event ->sheet->getDelegate()->getColumnDimension('H')->setWidth(100);

                for( $intRowNumber = 2; $intRowNumber <= $this->total + 1; $intRowNumber++){
                    $event->sheet->getRowDimension($intRowNumber)->setRowHeight(80);
                }
                
                foreach ($this->ventas as $key => $value) {
                	$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
			        $drawing->setName('Logo');
			        $drawing->setDescription('Logo');

			        $imagen = 'C:/inetpub/wwwroot/Master/storage/app/public/imagenes/productos/'.$value['COD_PROD'].'.jpg';

			        if(!file_exists($imagen)) {
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
}
