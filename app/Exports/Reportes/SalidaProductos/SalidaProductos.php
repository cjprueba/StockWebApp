<?php

namespace App\Exports\Reportes\SalidaProductos;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\SalidaProducto;

class SalidaProductos implements FromArray, WithHeadings, WithTitle, WithEvents,WithColumnFormatting ,ShouldAutoSize
{
  private $sheets = [];
  private $hojas=1;
  private $inicio;
  private $tipo=0;
  private $final;
  private $sucursal;
  private $checkedTipos;
  private $posicion=1;
  private $tipos;
   public function __construct($datos,$tipo,$descripcion_tipo)
    {
     
        $this->inicio = $datos['inicio'];
        $this->final  =  $datos['final'];
        $this->sucursal = $datos['sucursal'];
        $this->descripcion_tipo=$descripcion_tipo;
        $this->tipo=$tipo;
       
    }
        /**
    * @return \Illuminate\Support\Collection
    */
            public function  array(): array
    {
        $totales=SalidaProducto::Select(DB::raw("SALIDA_PRODUCTOS_DET.COD_PROD AS COD_PROD,
        	LOTES.LOTE AS LOTE,
        	PRODUCTOS.DESCRIPCION AS DESCRIPCION,
        	IFNULL(LINEAS.DESCRIPCION,'INDEFINIDO') AS CATEGORIA,
        	IFNULL(SUBLINEAS.DESCRIPCION,'INDEFINIDO') AS SUBCATEGORIA,
        	IFNULL(SUBLINEA_DET.DESCRIPCION,'INDEFINIDO') AS NOMBRE,
        	IFNULL(UPPER(SALIDA_PRODUCTOS.OBSERVACION),'NO POSEE' ) AS COMENTARIO,
        	SUM(SALIDA_PRODUCTOS_DET.CANTIDAD) AS CANTIDAD,
        	LOTES.COSTO AS COSTO_UNITARIO,
        	SUM(SALIDA_PRODUCTOS_DET.COSTO_TOTAL) AS COSTO_TOTAL"))
        ->LEFTJOIN('SALIDA_PRODUCTOS_DET','SALIDA_PRODUCTOS_DET.FK_SALIDA_PRODUCTOS','=','SALIDA_PRODUCTOS.ID')
        ->LEFTJOIN('LOTES','LOTES.ID','=','SALIDA_PRODUCTOS_DET.FK_ID_LOTE')
        ->LEFTJOIN('PRODUCTOS','PRODUCTOS.CODIGO','=','LOTES.COD_PROD')
        ->LEFTJOIN('LINEAS','LINEAS.CODIGO','=','PRODUCTOS.LINEA')
        ->LEFTJOIN('SUBLINEAS','SUBLINEAS.CODIGO','=','PRODUCTOS.LINEA')
        ->LEFTJOIN('SUBLINEA_DET','SUBLINEA_DET.CODIGO','=','PRODUCTOS.SUBLINEADET')
        ->WHERE('SALIDA_PRODUCTOS.ID_SUCURSAL','=',$this->sucursal)
        ->WHERE('SALIDA_PRODUCTOS.ESTADO','=',0)
        ->WHERE('SALIDA_PRODUCTOS.TIPO','=',$this->tipo)
        ->whereBetween('SALIDA_PRODUCTOS.FECALTAS',[$this->inicio,$this->final])->GROUPBY('SALIDA_PRODUCTOS_DET.COD_PROD',"SALIDA_PRODUCTOS_DET.FK_ID_LOTE")
        ->ORDERBY('SALIDA_PRODUCTOS.OBSERVACION')
        ->get()
        ->toArray();
        $costo=0;
        $costo_unit=0;
        $cantidad=0;
        foreach ($totales as $key => $value) {
        	$this->posicion+=1;
        	$costo+=$value["COSTO_TOTAL"];
        	$costo_unit+=$value["COSTO_UNITARIO"];
        	$cantidad+=$value["CANTIDAD"];
        	
        }
        $totales[]=array(
        	"COD_PROD"=>"",
        	"LOTE"=> 0,
        	"DESCRIPCION"=>"TOTALES",
        	"CATEGORIA"=>"",
        	"SUBCATEGORIA"=>"",
        	"NOMBRE"=>"",
        	"COMENTARIO"=>"",
        	"CANTIDAD"=>$cantidad,
        	"COSTO_UNITARIO"=>$costo_unit,
        	"COSTO_TOTAL"=>$costo
        );
        return $totales;
    }
            public function headings(): array
    {
        return ["COD_PROD","LOTE","DESCRIPCION","CATEGORIA","SUBCATEGORIA","NOMBRE","COMENTARIO","CANTIDAD","COSTO_UNITARIO","COSTO_TOTAL"];
    }
            public function title(): string
    {
        return $this->descripcion_tipo;
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
            // Handle by a closure.
         AfterSheet::class => function(AfterSheet $event) use($styleArray)  {
            $event->sheet->getStyle('A1:J1')->applyfromarray($styleArray);
            $this->posicion=$this->posicion+1;
            $event->sheet->getStyle('A'.$this->posicion.':J'.$this->posicion)->applyfromarray($styleArray);
            $event->sheet->getStyle('J2:'.'J'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('H2:'.'H'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
             $event->sheet->getStyle('I2:'.'I'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
             
             
        }

        ];
       
       
      
    }
            public function columnFormats(): array
    {
         return [
         	'A' => NumberFormat::FORMAT_NUMBER,
           'G' => NumberFormat::FORMAT_NUMBER,
           'H' => NumberFormat::FORMAT_NUMBER,
           'I' => NumberFormat::FORMAT_NUMBER,

        ];
    }
}
