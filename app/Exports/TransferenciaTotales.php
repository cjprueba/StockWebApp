<?php

namespace App\Exports;

use App\Ventas_det;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Facades\Excel as MaatExcel;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Maatwebsite\Excel\Concerns\WithHeadings;
USE Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class TransferenciaTotales implements FromArray, WithTitle,WithEvents,ShouldAutoSize,WithColumnFormatting
{
	private $CODIGO;
    private $ventageneral=[];   
    private $DESCRIPCION;
    private $CODIGOL;
    private $DESCRIPCIONL;
    private $marcas;
      private $general_stock;
   private $general_precio;
   private $general_precio_unit;
   private $general_costo;
   private $general_costo_unit;
   private $general_utilidad; 
   private $general_cantidad;
    private  $venta;
    private $total_utilidad;
    private $total_general;
    private $total_preciounit;
    private $costo;
    private $totalcosto;
    private $cantidadvendida;
    private $stock;
    private $stocktotal;
    public $posicion=1;
    private $stockarray;
    private $stockarrays;
    public $marcas_array=[];
     public $marcas_array_aux=[];
    private $descuentogeneral;
    private $descuentos; 
    private $sucursal;
    /**
    * @return \Illuminate\Support\Collection
    */
     public function __construct( array $ventageneral=[], array $RESULTS=[] ,$sucursal)
    {
        
        $this->ventageneral =$ventageneral ;
      $this->RESULTS =$RESULTS ;
      $this->sucursal=$sucursal;
    }
 public function  array(): array
    {
      
      $marcas_array[]=array('TOTALES',"",'STOCK','RECIBIDO','COSTO','TOTAL_COSTO','PRECIO','TOTAL_PRECIO');
       $this->stockarray = DB::connection('retail')
            ->table('LOTES as l')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'l.COD_PROD')
             ->leftjoin('PRODUCTOS_AUX',function($join){
             $join->on('PRODUCTOS_AUX.CODIGO','=','PRODUCTOS.CODIGO')
               ->on('PRODUCTOS_AUX.ID_SUCURSAL','=','l.ID_SUCURSAL');
          })
            ->select(DB::raw('SUM(l.CANTIDAD) AS CANTIDAD'),
            DB::raw('PRODUCTOS.MARCA'),
            DB::raw('PRODUCTOS.LINEA'))
            ->where('l.ID_SUCURSAL', '=', $this->sucursal)
             ->where('PRODUCTOS_AUX.PROVEEDOR', '=',19)
            ->groupBy('PRODUCTOS_AUX.PROVEEDOR')
            ->get();
            foreach ($this->stockarray as $this->stockarrays) 
            {
                     $this->stocktotal=$this->stocktotal+$this->stockarrays->CANTIDAD;
                      $this->general_stock=$this->general_stock+$this->stocktotal;
            };
            foreach ($this->ventageneral as $key=> $value){
                  if($value->PROVEEDOR==19){
                     $this->total_general=$this->total_general+$value->PRECIO_VENTA;
                     $this->total_preciounit=$this->total_preciounit+$value->PRECIO_UNIT_VENTA;
                     $this->cantidadvendida=$this->cantidadvendida+$value->CANTIDAD_S;
                    $this->costo=$this->costo+$value->PRECOSTO;
                   $this->totalcosto=$this->totalcosto+$value->PRECOSTO_TOTAL;
                  }
            }
            $this->posicion=$this->posicion+1;
                 $marcas_array[]=array(
                'TOTALES'=> "TOKUTOKUYA",
                ''=>"",
                'STOCK'=> $this->stocktotal,
                'CANTIDAD'=> $this->cantidadvendida,
                'COSTO'=> $this->costo,
                'TOTAL_COSTO'=> $this->totalcosto,
                'PRECIO'=> $this->total_preciounit,
                'TOTAL_PRECIO'=> $this->total_general,
              
                );
               
               $this->general_cantidad=$this->general_cantidad+$this->cantidadvendida;
               $this->general_costo=$this->general_costo+$this->totalcosto;
               $this->general_costo_unit=$this->general_costo_unit+$this->costo;
               $this->general_precio=$this->general_precio+$this->total_general;
               $this->general_precio_unit=$this->general_precio_unit+$this->total_preciounit;
               $this->stocktotal=0;
               $this->cantidadvendida=0;
               $this->costo=0;
               $this->totalcosto=0;
               $this->total_preciounit=0;
               $this->total_general=0;

       foreach ($this->RESULTS as $key => $PROVEE) {
        $this->stockarray = DB::connection('retail')
            ->table('LOTES as l')
            ->join('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'l.COD_PROD')
            ->select(DB::raw('SUM(l.CANTIDAD) AS CANTIDAD'),
            DB::raw('PRODUCTOS.MARCA'),
            DB::raw('PRODUCTOS.LINEA'))
            ->where('l.ID_SUCURSAL', '=',4)
             ->where('PRODUCTOS.MARCA', '=',$PROVEE->Marca)
             ->where('PRODUCTOS.LINEA', '=',$PROVEE->Linea)
            ->groupBy('PRODUCTOS.MARCA', 'PRODUCTOS.LINEA')
            ->get();
           foreach ($this->stockarray as $this->stockarrays) 
            {
                     $this->stocktotal=$this->stocktotal+$this->stockarrays->CANTIDAD;
                      $this->general_stock=$this->general_stock+$this->stocktotal;
            };
       	           foreach ($this->ventageneral as $key=> $value) {

            if($value->MARCA==$PROVEE->Marca && $value->LINEA==$PROVEE->Linea ){
           
            
             $this->total_general=$this->total_general+$value->PRECIO_VENTA;
             $this->total_preciounit=$this->total_preciounit+$value->PRECIO_UNIT_VENTA;
             $this->cantidadvendida=$this->cantidadvendida+$value->CANTIDAD_S;
            $this->costo=$this->costo+$value->PRECOSTO;
           $this->totalcosto=$this->totalcosto+$value->PRECOSTO_TOTAL;


            }
            # code...
        }
      
     
        

            $this->posicion=$this->posicion+1;
                 $marcas_array[]=array(
                'TOTALES'=> $PROVEE->DescriM." ".$PROVEE->descrili,
                ''=>"",
                'STOCK'=> $this->stocktotal,
                'CANTIDAD'=> $this->cantidadvendida,
                'COSTO'=> $this->costo,
                'TOTAL_COSTO'=> $this->totalcosto,
                'PRECIO'=> $this->total_preciounit,
                'TOTAL_PRECIO'=> $this->total_general,
              
                );
               
               $this->general_cantidad=$this->general_cantidad+$this->cantidadvendida;
               $this->general_costo=$this->general_costo+$this->totalcosto;
               $this->general_costo_unit=$this->general_costo_unit+$this->costo;
               $this->general_precio=$this->general_precio+$this->total_general;
               $this->general_precio_unit=$this->general_precio_unit+$this->total_preciounit;
               $this->stocktotal=0;
               $this->cantidadvendida=0;
               $this->costo=0;
               $this->totalcosto=0;
               $this->total_preciounit=0;
               $this->total_general=0;
             
       }
            
           $this->posicion=$this->posicion+1;
                 $marcas_array[]=array(
                'TOTALES'=> "GENERAL",
                ''=>"",
                'STOCK'=> $this->general_stock,
                'CANTIDAD'=> $this->general_cantidad,
                'COSTO'=> $this->general_costo_unit,
                'TOTAL_COSTO'=> $this->general_costo,
                'PRECIO'=> $this->general_precio_unit,
                'TOTAL_PRECIO'=> $this->general_precio,
              
                );
          return $marcas_array;
     }


    /**
     * @return array
     */
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
            $event->sheet->getStyle('A1:H1')->applyfromarray($styleArray);
           
            $event->sheet->getStyle('A'.$this->posicion.':H'.$this->posicion)->applyfromarray($styleArray);
            $event->sheet->getStyle('G2:'.'G'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0');
            $event->sheet->getStyle('E2:'.'E'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0');
            $event->sheet->getStyle('F2:'.'F'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0');
            $event->sheet->getStyle('H2:'.'H'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0');
          
        }

        ];
       
      
    }







    public function columnFormats(): array
    {
        return [
           'E' => NumberFormat::FORMAT_NUMBER,
           'F' => NumberFormat::FORMAT_NUMBER,
           'G' => NumberFormat::FORMAT_NUMBER,
           'H' => NumberFormat::FORMAT_NUMBER,
          
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
         
              return "TOTALES"  ;
      
       
        }
}
