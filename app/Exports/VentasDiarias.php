<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
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
USE Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Facades\Log;
class VentasDiarias implements FromArray, WithTitle,WithEvents,ShouldAutoSize,WithColumnFormatting 
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


    /**
     * @return Builder
     */
        public function __construct()
    {
        
    }
    public function  array(): array
    {
    	$dia = '2020-07-02';
        $precio=100;
        $precio_descontado=0;
        $descuento=0;
        $descuento_general=0;
        $precio_descontado_general=0;
      $precio_descontado_total=0;
      $descuento_real=0;
    $reporte=DB::connection('retail')->table('VENTASDET')
           
            ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->leftjoin('SUBLINEAS', 'SUBLINEAS.CODIGO', '=', 'PRODUCTOS.SUBLINEA')
            ->leftjoin('SUBLINEA_DET', 'SUBLINEA_DET.CODIGO', '=', 'PRODUCTOS.SUBLINEADET')
            ->leftjoin('VENTASDET_DESCUENTO', 'VENTASDET_DESCUENTO.FK_VENTASDET', '=', 'VENTASDET.ID')
            ->leftjoin('VENTASDET_DEVOLUCIONES',function($join){
             $join->on('VENTASDET_DEVOLUCIONES.CODIGO','=','VENTASDET.CODIGO')
             ->on('VENTASDET_DEVOLUCIONES.CAJA','=','VENTASDET.CAJA')
              ->on('VENTASDET_DEVOLUCIONES.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL')
              ->on('VENTASDET_DEVOLUCIONES.COD_PROD','=','VENTASDET.COD_PROD');
         })
          
           ->leftjoin('VENTAS',function($join){
             $join->on('VENTAS.CODIGO','=','VENTASDET.CODIGO')
             ->on('VENTAS.CAJA','=','VENTASDET.CAJA')
               ->on('VENTAS.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL');
         })
             ->leftjoin('VENTAS_DESCUENTO', 'VENTAS_DESCUENTO.FK_VENTAS', '=', 'VENTAS.ID')
            ->select(
            DB::raw('VENTASDET.COD_PROD AS COD_PROD'),
            DB::raw('VENTASDET.CANTIDAD AS VENDIDO'),
            DB::raw('LINEAS.DESCRIPCION AS CATEGORIA'),
            DB::raw('SUBLINEAS.DESCRIPCION AS SUBCATEGORIA'),
            DB::raw('SUBLINEA_DET.DESCRIPCION AS NOMBRE'),
            DB::raw('VENTAS.ID AS ID'),
            DB::raw('IFNULL(VENTAS_DESCUENTO.PORCENTAJE,0) AS PORCENTAJE_GENERAL'),
              DB::raw('IFNULL(VENTASDET_DEVOLUCIONES.CANTIDAD,0) AS CANTIDAD_DEVUELTA'),
            DB::raw('IFNULL(VENTASDET_DESCUENTO.PORCENTAJE,0) AS DESCUENTO_PORCENTAJE'))

        ->Where('VENTASDET.ANULADO','=',0)
       ->Where('VENTASdet.FECALTAS','like',$dia.'%')
        //->whereBetween('VENTASDET.FECALTAS', ['2020-05-05', '2020-06-19'])
        ->Where('VENTASDET.ID_SUCURSAL','=',9) 
         
        ->orderby('VENTASDET.COD_PROD')
                ->get()
                ->toArray();
       
 

      
    
        $marcas_array[]=array('COD_PROD','VENDIDO','CATEGORIA','SUBCATEGORIA','NOMBRE','DESCUENTO_PORCENTAJE');

        Schema::connection('auxi')->create('temp_porcentaje', function (Blueprint $table) {
        $table->increments('id');

        $table->string('COD_PROD');
        $table->integer('VENDIDO');
        $table->string('CATEGORIA');
        $table->string('SUBCATEGORIA');
        $table->string('NOMBRE');
        $table->string('DESCUENTO_PORCENTAJE');
        $table->string('DESCUENTO_PRODUCTO');
        $table->timestamps();
        $table->temporary();
        });







        foreach ($reporte as $key=>$value ) {
            if($value->CANTIDAD_DEVUELTA>0){
                $value->VENDIDO=$value->VENDIDO-$value->CANTIDAD_DEVUELTA;
            }
            if($value->VENDIDO>0){


             if ($value->PORCENTAJE_GENERAL>0 ) {
               $descuento=($precio*$value->DESCUENTO_PORCENTAJE)/100;
               $precio_descontado=$precio-$descuento;
               $descuento_general=($precio_descontado*$value->PORCENTAJE_GENERAL)/100;
               $precio_descontado_general=$precio_descontado-$descuento_general;
               $precio_descontado_total=$descuento+$descuento_general;
               $descuento_real=($precio_descontado_total*100)/$precio;
               $value->DESCUENTO_PORCENTAJE=$descuento_real;
                
             }
               $this->posicion=$this->posicion+1;
             if($value->DESCUENTO_PORCENTAJE==0){
             	$value->DESCUENTO_PORCENTAJE='0';
             }

            if($value->NOMBRE==NULL){
                $value->NOMBRE='';
             }

              	DB::connection('auxi')->table('temp_porcentaje')->insert(['COD_PROD'=>$value->COD_PROD,'VENDIDO'=>$value->VENDIDO,'CATEGORIA'=>$value->CATEGORIA,'SUBCATEGORIA'=>$value->SUBCATEGORIA,'NOMBRE'=>$value->NOMBRE,'DESCUENTO_PORCENTAJE'=>$value->PORCENTAJE_GENERAL,'DESCUENTO_PRODUCTO'=>$value->DESCUENTO_PORCENTAJE]);
              	
}
              
           # code...

        };
       

          $temp=DB::connection('auxi')->table('temp_porcentaje')->select(
            DB::raw('COD_PROD'),
            DB::raw('SUM(VENDIDO) AS VENDIDO'),
            DB::raw('CATEGORIA'),
            DB::raw(' SUBCATEGORIA'),
             DB::raw(' NOMBRE'),
               DB::raw('DESCUENTO_PORCENTAJE'),
            DB::raw('DESCUENTO_PRODUCTO'))
                   ->GROUPBY('COD_PROD','DESCUENTO_PRODUCTO') 
        ->orderby('COD_PROD')
          ->get()
          ->toArray();
          foreach ($temp as $key => $value) {
                    $marcas_array[]=array(
                 
                'COD_PROD'=> $value->COD_PROD,
                'VENIDO'=> $value->VENDIDO,
                'CATEGORIA'=> $value->CATEGORIA,
                'SUBCATEGORIA'=> $value->SUBCATEGORIA,
                'NOMBRE'=> $value->NOMBRE,
                'DESCUENTO_PORCENTAJE'=> $value->DESCUENTO_PRODUCTO,
                

              
              

            );
          }
           log::error(["table"=>$temp]);

    Schema::connection('auxi')->drop('temp_porcentaje');


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
            $event->sheet->getStyle('A1:F1')->applyfromarray($styleArray);
           
             
        }

        ];
       
       
      
    }







    public function columnFormats(): array
    {
         return [
         	 'A' => NumberFormat::FORMAT_NUMBER,
           'B' => NumberFormat::FORMAT_NUMBER,
           

        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
         
             return 'REPORTE';
         
       
        }
}
