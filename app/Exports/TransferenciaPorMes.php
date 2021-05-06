<?php

namespace App\Exports;

use App\transferencias;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\FromQuery;
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

class TransferenciaPorMes implements FromArray, WithTitle,WithEvents,ShouldAutoSize,WithColumnFormatting 
{
  private $CODIGO;
    private $ventageneral=[];   
    private $DESCRIPCION;
    private $CODIGOL;
    private $DESCRIPCIONL;
    private $marcas;
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
    public function __construct($datos)
    {

        $this->CODIGO = $datos['CODIGO'];   
        $this->DESCRIPCION  = $datos['DESCRIPCION'];
        $this->CODIGOL = $datos['CODIGOL'];   
        $this->DESCRIPCIONL  = $datos['DESCRIPCIONL'];
        $this->hojas=$datos['HOJAS'];
        $this->ventageneral =$datos['TRANSFERENCIAS'] ;
      
    }

    /**
     * @return Builder
     */
    public function  array(): array
    {
      
      if ($this->hojas==2){
       

        foreach ($this->ventageneral as $key=> $value) {

            if($this->ventageneral[$key]->MARCA==$this->CODIGO and $this->ventageneral[$key]->LINEA==$this->CODIGOL ){

               $this->marcas_array_aux[]= (object) array(
                 
                'COD_PROD'=> $value->COD_PROD,
                 'LOTE'=> $value->LOTE,
                'DESCRIPCION'=> $value->DESCRIPCION,
                'CANTIDAD_S'=> $value->CANTIDAD_S,
                'PRECOSTO'=> $value->PRECOSTO,
               'PRECOSTO_TOTAL'=> $value->PRECOSTO_TOTAL,
               'PRECIO_UNIT_VENTA'=> $value->PRECIO_UNIT_VENTA,
                'PRECIO_VENTA'=> $value->PRECIO_VENTA,
                

            );
            }
            # code...
        }
      
      } else {
        
        $this->marcas_array_aux = (array)$this->ventageneral;
         
      }
        $marcas_array[]=array('COD_PROD','LOTE','DESCRIPCION','STOCK','RECIBIDO','COSTO','TOTAL_COSTO','PRECIO','TOTAL_PRECIO');
        foreach ($this->marcas_array_aux as $this->venta) {
               $this->posicion=$this->posicion+1;
              
               $this->total_general=$this->total_general+$this->venta->PRECIO_VENTA;
               $this->total_preciounit=$this->total_preciounit+$this->venta->PRECIO_UNIT_VENTA;
               $this->cantidadvendida=$this->cantidadvendida+$this->venta->CANTIDAD_S;
              $this->costo=$this->costo+$this->venta->PRECOSTO;
              $this->totalcosto=$this->totalcosto+$this->venta->PRECOSTO_TOTAL;
                
         $this->stockarray=DB::connection('retail')->table('LOTES')->SELECT(DB::raw('SUM(LOTES.CANTIDAD) AS CANTI'))
        ->WHERE('LOTES.COD_PROD','=',$this->venta->COD_PROD)
        ->WHERE('LOTES.ID_SUCURSAL','=',4)
        ->GROUPBY('LOTES.COD_PROD')
        ->orderby('LOTES.cod_prod')
        ->get()
        ->toArray();
           foreach ($this->stockarray as $this->stockarrays) 
            {
                    $this->stock=$this->stockarrays->CANTI;  
                        if ($this->hojas==1){
                            $this->stocktotal=$this->stocktotal+$this->stockarrays->CANTI;
                        }
                    
            };
            $marcas_array[]=array(
                 
                'COD_PROD'=> $this->venta->COD_PROD,
                'LOTE'=> $this->venta->LOTE,
                'DESCRIPCION'=> $this->venta->DESCRIPCION,
                'STOCK'=> $this->stock,
                'CANTIDAD'=> $this->venta->CANTIDAD_S,
                'COSTO'=> $this->venta->PRECOSTO,
                'TOTAL_COSTO'=> $this->venta->PRECOSTO_TOTAL,
               'PRECIO'=> $this->venta->PRECIO_UNIT_VENTA,
                'TOTAL_PRECIO'=> $this->venta->PRECIO_VENTA,
              

            );
           # code...
        };
                 $marcas_array[]=array(
                'COD_PROD'=> "",
                'LOTE'=> "",
                'DESCRIPCION'=>"TOTALES",
                'STOCK'=> $this->stocktotal,
                'CANTIDAD'=> $this->cantidadvendida,
                'COSTO'=> $this->costo,
                'TOTAL_COSTO'=> $this->totalcosto,
                'PRECIO'=> $this->total_preciounit,
                'TOTAL_PRECIO'=> $this->total_general,
             
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
            $event->sheet->getStyle('A1:I1')->applyfromarray($styleArray);
            $this->posicion=$this->posicion+1;
            $event->sheet->getStyle('C'.$this->posicion.':I'.$this->posicion)->applyfromarray($styleArray);
            $event->sheet->getStyle('G2:'.'G'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0');
            $event->sheet->getStyle('E2:'.'E'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0');
            $event->sheet->getStyle('F2:'.'F'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0');
            $event->sheet->getStyle('H2:'.'H'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0');
            $event->sheet->getStyle('I2:'.'I'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0');
             
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
           'I' => NumberFormat::FORMAT_NUMBER,
          
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
          if ($this->hojas==1){
             return 'TRANSFERENCIAS';
          }else{
             return $this->DESCRIPCION." ".$this->DESCRIPCIONL  ;
          }
       
        }
    }
