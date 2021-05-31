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

class SalidaProductosTotales implements FromArray, WithHeadings, WithTitle, WithEvents,WithColumnFormatting ,ShouldAutoSize
{
  private $sheets = [];
  private $hojas=1;
  private $inicio;
  private $final;
  private $sucursal;
  private $checkedTipos;
  private $posicion=1;
  private $tipos;
   public function __construct($datos)
    {
     
        $this->inicio = $datos['inicio'];
        $this->final  =  $datos['final'];
        $this->sucursal = $datos['sucursal'];
        $this->tipos=$datos['tipos'];
        $this->checkedTipos=$datos['checkedTipo'];
       
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function  array(): array
    {
        $totales=SalidaProducto::Select(DB::raw("IF(SALIDA_PRODUCTOS.TIPO = 1,
        'AVERIO',
        IF(SALIDA_PRODUCTOS.TIPO = 2,
            'VENCIDOS',
            IF(SALIDA_PRODUCTOS.TIPO = 3,
                'ROBADO',
                IF(SALIDA_PRODUCTOS.TIPO = 4,
                    'MUESTRA',
                    IF(SALIDA_PRODUCTOS.TIPO = 5,
                        'EXTRAVIADO',
                        IF(SALIDA_PRODUCTOS.TIPO = 6,
                            'REGALO',
                            IF(SALIDA_PRODUCTOS.TIPO = 7,
                                'USO INTERNO',
                                'INDEFINIDO'))))))) AS TOTALES,
           0 AS C,
          SUM(SALIDA_PRODUCTOS_DET.CANTIDAD) AS PIEZAS_CON_SALIDA,
          SUM(SALIDA_PRODUCTOS_DET.COSTO_TOTAL) AS COSTO_TOTAL"))
        ->LEFTJOIN('SALIDA_PRODUCTOS_DET','SALIDA_PRODUCTOS_DET.FK_SALIDA_PRODUCTOS','=','SALIDA_PRODUCTOS.ID')
        ->WHERE('SALIDA_PRODUCTOS.ID_SUCURSAL','=',$this->sucursal)
        ->WHERE('SALIDA_PRODUCTOS.ESTADO','=',0)
        ->whereBetween('SALIDA_PRODUCTOS.FECALTAS',[$this->inicio,$this->final]);
        if(!$this->checkedTipos){
        	$totales->whereIn('SALIDA_PRODUCTOS.TIPO',$this->tipos);
        }
        $totales=$totales->GROUPBY('SALIDA_PRODUCTOS.TIPO')->get()->toArray();
        $costo=0;
        $cantidad=0;
        foreach ($totales as $key => $value) {
        	$this->posicion+=1;
        	$costo+=$value["COSTO_TOTAL"];
        	$cantidad+=$value["PIEZAS_CON_SALIDA"];
        	# code...
        }
        $totales[]=array(
        	"TOTALES"=>'',
        	"C"=> 0,
        	"PIEZAS_CON_SALIDA"=>$cantidad,
        	"COSTO_TOTAL"=>$costo
        );
        return $totales;
    }
        public function headings(): array
    {
        return ["TOTALES","","PIEZAS_CON_SALIDA","COSTO_TOTAL"];
    }
        public function title(): string
    {
        return 'TOTALES';
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
            $event->sheet->getStyle('A1:D1')->applyfromarray($styleArray);
            $this->posicion=$this->posicion+1;
            $event->sheet->getStyle('A'.$this->posicion.':D'.$this->posicion)->applyfromarray($styleArray);
            $event->sheet->getStyle('C2:'.'C'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('D2:'.'D'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
             
             
        }

        ];
       
       
      
    }
            public function columnFormats(): array
    {
         return [
           'C' => NumberFormat::FORMAT_NUMBER,
           'D' => NumberFormat::FORMAT_NUMBER,

        ];
    }

}
