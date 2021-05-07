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
use App\Temp_venta;
use DateTime;

class rptTopArticulos implements FromArray, WithTitle,WithEvents,ShouldAutoSize,WithColumnFormatting
{
    private $marca;
  private $descuentogeneral;
  private $descuento;
  private $calculo;
  private $calculos;
  private $ventageneral;
  private $linea=[];
  private $nullsheets;
  private $sheets = [];
  private $marcas=[];
  private $hojas=1;
  private $inicio;
  private $final;
  private $checkedProveedor;
   private $checkedTipo;
  private $insert;
  private $checkedSucursal;
  private $sucursal;
  private $agrupar;
 private $top;
  private $stock;
  private $seccion;
    public $posicion=1;
            public function __construct($datos)
    {
    	        $this->inicio = date('Y-m-d', strtotime($datos['inicio']));
        $this->final  =  date('Y-m-d', strtotime($datos['final']));
        $this->sucursal = $datos['sucursal'];
        $this->checkedProveedor= $datos['checkedProveedor'];
        $this->Proveedores=$datos['proveedores'];
        $this->checkedTipo=$datos['checkedTipo'];
        $this->stock=$datos["stock"];
        $this->seccion=$datos['seccion'];
        $this->insert=$datos['insert'];
        $this->top=$datos['top'];

    }
          public function  array(): array
    { 
    	  $user = auth()->user();
    	  Temp_venta::where('USER_ID', $user->id)->WHERE('ID_SUCURSAL','=',$this->sucursal)->delete();
    	
        $reporte=DB::connection('retail')->table('VENTASDET')
            ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
            ->leftjoin('PRODUCTOS_AUX',function($join){
            $join->on('PRODUCTOS_AUX.CODIGO','=','VENTASDET.COD_PROD')
             ->on('PRODUCTOS_AUX.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL');
            })
               
            ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
            ->leftjoin('VENTASDET_TIENE_LOTES', 'VENTASDET_TIENE_LOTES.ID_VENTAS_DET', '=', 'VENTASDET.ID')
            ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'PRODUCTOS_AUX.PROVEEDOR')
            ->leftjoin('SUBLINEAS', 'SUBLINEAS.CODIGO', '=', 'PRODUCTOS.SUBLINEA')
            ->leftjoin('LOTES', 'LOTES.ID', '=', 'VENTASDET_TIENE_LOTES.ID_LOTE')
            ->leftjoin('SUBLINEA_DET', 'SUBLINEA_DET.CODIGO', '=', 'PRODUCTOS.SUBLINEADET')
            ->leftjoin('VENTASDET_DESCUENTO', 'VENTASDET_DESCUENTO.FK_VENTASDET', '=', 'VENTASDET.ID')
            ->leftjoin('GONDOLA_TIENE_PRODUCTOS',function($join){
             $join->on('GONDOLA_TIENE_PRODUCTOS.GONDOLA_COD_PROD','=','VENTASDET.COD_PROD')
               ->on('GONDOLA_TIENE_PRODUCTOS.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL');
             })
            ->leftjoin('GONDOLAS','GONDOLAS.ID','=','GONDOLA_TIENE_PRODUCTOS.ID_GONDOLA')
            ->leftjoin('GONDOLA_TIENE_SECCION','GONDOLA_TIENE_SECCION.ID_GONDOLA','=','GONDOLAS.ID')
            ->leftjoin('SECCIONES','SECCIONES.ID','=','GONDOLA_TIENE_SECCION.ID_SECCION')
            ->leftjoin('VENTAS',function($join){
             $join->on('VENTAS.CODIGO','=','VENTASDET.CODIGO')
                ->on('VENTAS.CAJA','=','VENTASDET.CAJA')
                ->on('VENTAS.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL');
              })
            ->leftjoin('VENTAS_DESCUENTO', 'VENTAS_DESCUENTO.FK_VENTAS', '=', 'VENTAS.ID')
            ->select(
            DB::raw(
             'VENTASDET.COD_PROD AS COD_PROD,
             VENTASDET.CODIGO,
             VENTASDET_TIENE_LOTES.CANTIDAD AS VENDIDO,
             LOTES.LOTE AS LOTE,
             LOTES.COSTO AS COSTO_UNIT,
             (VENTASDET_TIENE_LOTES.CANTIDAD*LOTES.COSTO) AS COSTO_TOTAL,
             LINEAS.DESCRIPCION AS CATEGORIA,
             SUBLINEAS.DESCRIPCION AS SUBCATEGORIA,
             SUBLINEA_DET.DESCRIPCION AS NOMBRE,
             PRODUCTOS_AUX.PROVEEDOR AS PROVEEDOR,
             PROVEEDORES.NOMBRE AS PROVEEDOR_NOMBRE,
             SECCIONES.DESCRIPCION AS SECCION,
             VENTAS.ID AS ID,
             VENTAS.VENDEDOR,
             (VENTASDET.PRECIO_UNIT*VENTASDET_TIENE_LOTES.CANTIDAD) AS PRECIO,
             VENTASDET.PRECIO_UNIT AS PRECIO_UNIT'),
            DB::raw('IFNULL(VENTASDET_DESCUENTO.TOTAL,0) AS DESCUENTO'),
            DB::raw('IFNULL(SECCIONES.ID,0) AS SECCION_CODIGO'),
            DB::raw('IFNULL(VENTAS_DESCUENTO.PORCENTAJE,0) AS PORCENTAJE_GENERAL'),
            DB::raw('IFNULL(LINEAS.CODIGO,0) AS LINEA_CODIGO'),
            DB::raw('IFNULL(SUBLINEAS.CODIGO,0) AS SUBLINEA_CODIGO'),
            DB::raw('IFNULL(VENTASDET_DESCUENTO.PORCENTAJE,0) AS DESCUENTO_PORCENTAJE'))
        ->Where('VENTASDET.ANULADO','<>',1)
        ->Where('VENTAS.TIPO','<>','CR')
        ->whereBetween('VENTASDET.FECALTAS', [$this->inicio, $this->final])
        ->Where('VENTASDET.ID_SUCURSAL','=',$this->sucursal);
        if($this->seccion<>"null"){
             $reporte->where('SECCIONES.ID','=', $this->seccion);
        }
        if(!$this->checkedProveedor &&  $this->seccion=="null"){
             $reporte->whereIn('PRODUCTOS_AUX.PROVEEDOR',$this->Proveedores);
        }
        if($this->stock){
             $reporte->where('LOTES.CANTIDAD','>',0);
        }
        $reporte=$reporte->orderby('VENTASDET.COD_PROD')->get()->toArray();
     
        $precio=100;
        $descuento_precio=0;
        $precio_descontado=0;
        $costo=0;
        $total_dev=0;
        $total_des=0;
        $descuento=0;
        $descuento_general=0;
        $precio_descontado_general=0;
        $precio_descontado_total=0;
        $descuento_real=0;
        $venta_in = array();

        foreach ($reporte as $key=>$value ) {


            if($value->VENDIDO>0){


                if ($value->PORCENTAJE_GENERAL>0 ) {
                    $descuento_precio=round((($value->PRECIO*$value->PORCENTAJE_GENERAL)/100),2);
                    $total_des=$total_des+$descuento_precio;
                    $value->PRECIO=(round($value->PRECIO-$descuento_precio,2));
                    $value->DESCUENTO=($value->DESCUENTO+round($descuento_precio,2));
                    $value->PRECIO_UNIT=round((($value->PRECIO_UNIT*$value->PORCENTAJE_GENERAL)/100),2);

                    $descuento=($precio*$value->DESCUENTO_PORCENTAJE)/100;
                    $precio_descontado=$precio-$descuento;
                    $descuento_general=($precio_descontado*$value->PORCENTAJE_GENERAL)/100;
                    $precio_descontado_general=$precio_descontado-$descuento_general;
                    $precio_descontado_total=$descuento+$descuento_general;
                    $descuento_real=($precio_descontado_total*100)/$precio;
                    $value->DESCUENTO_PORCENTAJE=$descuento_real;
                                
                }
                               
                if($value->DESCUENTO_PORCENTAJE==0){
                    $value->DESCUENTO_PORCENTAJE='0';
                }
                if($value->NOMBRE==NULL){
                    $value->NOMBRE='';
                }
                
                if($value->SECCION==NULL){
                    $value->SECCION='INDEFINIDO';
                }

                                       
                $nestedData['COD_PROD'] = $value->COD_PROD;
                $nestedData['VENDIDO'] =$value->VENDIDO;
                $nestedData['CATEGORIA'] =$value->CATEGORIA;
                $nestedData['SUBCATEGORIA']=$value->SUBCATEGORIA;
                $nestedData['NOMBRE']=$value->NOMBRE;
                $nestedData['DESCUENTO_PORCENTAJE']=$value->PORCENTAJE_GENERAL;
                $nestedData['DESCUENTO_PRODUCTO']=$value->DESCUENTO_PORCENTAJE;
                $nestedData['PRECIO']= $value->PRECIO;
                $nestedData['PRECIO_UNIT']= $value->PRECIO_UNIT;
                $nestedData['COSTO_UNIT']= $value->COSTO_UNIT;
                $nestedData['COSTO_TOTAL']= $value->COSTO_TOTAL;
                $nestedData['UTILIDAD']= $value->PRECIO-$value->COSTO_TOTAL;
                $nestedData['DESCUENTO']= $value->DESCUENTO;
                $nestedData['LINEA_CODIGO']=$value->LINEA_CODIGO;
                $nestedData['SUBLINEA_CODIGO']= $value->SUBLINEA_CODIGO;
                $nestedData['PROVEEDOR']= $value->PROVEEDOR;
                $nestedData['PROVEEDOR_NOMBRE']= $value->PROVEEDOR_NOMBRE;
                $nestedData['SECCION']= $value->SECCION;
                $nestedData['SECCION_CODIGO']= $value->SECCION_CODIGO;
                $nestedData['LOTE']= $value->LOTE;
                $nestedData['USER_ID']= $user->id;
                $nestedData['ID_SUCURSAL']= $this->sucursal;
                $venta_in[]=$nestedData;
                                
            }
                      

        }
       
              
        foreach (array_chunk($venta_in,1000) as $t) {
            DB::connection('retail')->table('temp_ventas')->insert($t);
        }
        $venta_nc = array();
        $reporte=DB::connection('retail')->table('NOTA_CREDITO_DET')  
         ->leftjoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'NOTA_CREDITO_DET.CODIGO_PROD')
         ->leftjoin('PRODUCTOS_AUX',function($join){
          $join->on('PRODUCTOS_AUX.CODIGO','=','NOTA_CREDITO_DET.CODIGO_PROD')
             ->on('PRODUCTOS_AUX.ID_SUCURSAL','=','NOTA_CREDITO_DET.ID_SUCURSAL');
         }) 
         ->leftjoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
         ->leftjoin('VENTASDET', 'VENTASDET.ID', '=', 'NOTA_CREDITO_DET.FK_VENTASDET')
         ->leftjoin('nota_credito_tiene_lote', 'nota_credito_tiene_lote.FK_VENTA_DET', '=', 'VENTASDET.ID')
         ->leftjoin('PROVEEDORES', 'PROVEEDORES.CODIGO', '=', 'PRODUCTOS_AUX.PROVEEDOR')
         ->leftjoin('SUBLINEAS', 'SUBLINEAS.CODIGO', '=', 'PRODUCTOS.SUBLINEA')
         ->leftjoin('LOTES', 'LOTES.ID', '=', 'nota_credito_tiene_lote.ID_LOTE')
         ->leftjoin('SUBLINEA_DET', 'SUBLINEA_DET.CODIGO', '=', 'PRODUCTOS.SUBLINEADET')
         ->leftjoin('NOTA_CREDITO', 'NOTA_CREDITO.ID', '=', 'NOTA_CREDITO_DET.FK_NOTA_CREDITO')
         ->leftjoin('GONDOLA_TIENE_PRODUCTOS',function($join){
         $join->on('GONDOLA_TIENE_PRODUCTOS.GONDOLA_COD_PROD','=','VENTASDET.COD_PROD')
             ->on('GONDOLA_TIENE_PRODUCTOS.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL');
         })
         ->leftjoin('VENTAS',function($join){
         $join->on('VENTAS.CODIGO','=','VENTASDET.CODIGO')
         ->on('VENTAS.CAJA','=','VENTASDET.CAJA')
           ->on('VENTAS.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL');
         })
         ->leftjoin('GONDOLAS','GONDOLAS.ID','=','GONDOLA_TIENE_PRODUCTOS.ID_GONDOLA')
         ->leftjoin('GONDOLA_TIENE_SECCION','GONDOLA_TIENE_SECCION.ID_GONDOLA','=','GONDOLAS.ID')
         ->leftjoin('SECCIONES','SECCIONES.ID','=','GONDOLA_TIENE_SECCION.ID_SECCION')

         ->select(
            DB::raw(
             'NOTA_CREDITO_DET.CODIGO_PROD AS COD_PROD,
             VENTASDET.CODIGO,
             nota_credito_tiene_lote.CANTIDAD AS VENDIDO,
             LOTES.LOTE AS LOTE,
             LOTES.COSTO AS COSTO_UNIT,
             (nota_credito_tiene_lote.CANTIDAD*LOTES.COSTO) AS COSTO_TOTAL,
             LINEAS.DESCRIPCION AS CATEGORIA,
             SUBLINEAS.DESCRIPCION AS SUBCATEGORIA,
             SUBLINEA_DET.DESCRIPCION AS NOMBRE,
             PRODUCTOS_AUX.PROVEEDOR AS PROVEEDOR,
             PROVEEDORES.NOMBRE AS PROVEEDOR_NOMBRE,
             SECCIONES.DESCRIPCION AS SECCION,
             VENTAS.VENDEDOR,
             (nota_credito_tiene_lote.CANTIDAD*NOTA_CREDITO_DET.PRECIO) AS PRECIO,
             NOTA_CREDITO_DET.PRECIO AS PRECIO_UNIT'),
            DB::raw('IFNULL(LINEAS.CODIGO,0) AS LINEA_CODIGO'),
            DB::raw('IFNULL(SECCIONES.ID,0) AS SECCION_CODIGO'),
            DB::raw('IFNULL(SUBLINEAS.CODIGO,0) AS SUBLINEA_CODIGO'))
        ->Where('VENTASDET.ANULADO','<>',1)
        ->Where('NOTA_CREDITO.PROCESADO','=',1)
        ->Where('VENTAS.TIPO','<>','CR')
        ->whereBetween('NOTA_CREDITO_DET.FECALTAS', [$this->inicio , $this->final])
        ->Where('NOTA_CREDITO_DET.ID_SUCURSAL','=',$this->sucursal);
       if($this->seccion<>"null"){
             $reporte->where('SECCIONES.ID','=', $this->seccion);
        }
        if(!$this->checkedProveedor &&  $this->seccion=="null"){
             $reporte->whereIn('PRODUCTOS_AUX.PROVEEDOR',$this->Proveedores);
        }
        if($this->stock){
             $reporte->where('LOTES.CANTIDAD','>',0);
        }
        $reporte=$reporte->orderby('VENTASDET.COD_PROD')->get()->toArray();
        foreach ($reporte as $key => $value) {

                            
             if($value->NOMBRE==NULL){
                 $value->NOMBRE='';
              }

              if($value->SECCION==NULL){
                 $value->SECCION='INDEFINIDO';
              }
             $nestedDataNC['COD_PROD'] = $value->COD_PROD;
             $nestedDataNC['VENDIDO'] =-$value->VENDIDO;
             $nestedDataNC['CATEGORIA'] =$value->CATEGORIA;
             $nestedDataNC['SUBCATEGORIA']=$value->SUBCATEGORIA;
             $nestedDataNC['NOMBRE']='DEVOLUCION NC:'.$value->NOMBRE;
             $nestedDataNC['DESCUENTO_PORCENTAJE']=0;
             $nestedDataNC['DESCUENTO_PRODUCTO']=0;
             $nestedDataNC['PRECIO']= $value->PRECIO*-1;
             $nestedDataNC['PRECIO_UNIT']= $value->PRECIO_UNIT*-1;
             $nestedDataNC['COSTO_UNIT']= $value->COSTO_UNIT*-1;
             $nestedDataNC['COSTO_TOTAL']= $value->COSTO_TOTAL*-1;

             if($value->COSTO_TOTAL>$value->PRECIO){
                 $nestedDataNC['UTILIDAD']= $value->COSTO_TOTAL*-1 + $value->PRECIO ;
             }elseif ($value->PRECIO>$value->COSTO_TOTAL) {
                 $nestedDataNC['UTILIDAD']= ($value->PRECIO*-1) + $value->COSTO_TOTAL;
             }else{
                 $nestedDataNC['COSTO_TOTAL']= ($value->PRECIO*-1) + $value->COSTO_TOTAL;
             }
             $nestedDataNC['DESCUENTO']= 0;
             $nestedDataNC['LINEA_CODIGO']=$value->LINEA_CODIGO;
             $nestedDataNC['SUBLINEA_CODIGO']= $value->SUBLINEA_CODIGO;
             $nestedDataNC['PROVEEDOR']= $value->PROVEEDOR;
             $nestedDataNC['PROVEEDOR_NOMBRE']= $value->PROVEEDOR_NOMBRE;
             $nestedDataNC['SECCION']= $value->SECCION;
             $nestedDataNC['SECCION_CODIGO']= $value->SECCION_CODIGO;
             $nestedDataNC['LOTE']= $value->LOTE;
             $nestedData['USER_ID']= $user->id;
                $nestedData['ID_SUCURSAL']= $this->sucursal;
             $venta_nc[]=$nestedDataNC;
        
         }
        foreach (array_chunk($venta_nc,1000) as $t) {
             DB::connection('retail')->table('temp_ventas')->insert($t);
        }
        $temp=DB::connection('retail')->table('temp_ventas')
        ->leftjoin('LOTES',function($join){
        $join->on('LOTES.COD_PROD','=','temp_ventas.COD_PROD')
             ->on('LOTES.ID_SUCURSAL','=','temp_ventas.ID_SUCURSAL');
        })
        ->leftjoin('productos','productos.CODIGO','=','temp_ventas.COD_PROD')
        ->select(
           DB::raw(
             'temp_ventas.COD_PROD,
             PRODUCTOS.DESCRIPCION AS DESCRIPCION,
             SUM(temp_ventas.VENDIDO) AS CANTIDAD,
             IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = temp_ventas.COD_PROD) AND (l.ID_SUCURSAL = temp_ventas.ID_SUCURSAL))),0) AS STOCK,
             (SUM(temp_ventas.PRECIO)/SUM(temp_ventas.VENDIDO)) AS PRECIO_UNIT,
             SUM(temp_ventas.PRECIO) AS TOTAL,
             (SUM(temp_ventas.COSTO_TOTAL)/SUM(temp_ventas.VENDIDO)) AS COSTO_UNIT,
             SUM(temp_ventas.COSTO_TOTAL),
             SUM(temp_ventas.UTILIDAD) AS UTILIDADES,
             SUM(temp_ventas.DESCUENTO) AS DESCUENTO,
             temp_ventas.CATEGORIA,
             temp_ventas.SUBCATEGORIA,
             temp_ventas.NOMBRE,
             temp_ventas.SECCION,
             temp_ventas.PROVEEDOR_NOMBRE'))
        ->WHERE('temp_ventas.USER_ID','=',$user->id)
        ->WHERE('temp_ventas.ID_SUCURSAL','=',$this->sucursal)    
        ->GROUPBY('temp_ventas.COD_PROD'); 
        if($this->checkedTipo=="false"){
        	
             $temp->orderby('UTILIDADES','DESC');
        }else{
        	
             $temp->orderby('CANTIDAD','DESC');
        }
        
        $temp=$temp->limit($this->top)
        ->get()
        ->toArray();
      /* var_dump($temp);*/



        $marcas_array[]=array('COD_PROD','DESCRIPCION','CATEGORIA','VENDIDO','STOCK','TOTAL','DESCUENTO','UTILIDAD');
        foreach ($temp as $key => $value) {
        	 $this->posicion=$this->posicion+1;

             $marcas_array[]=array(
		                 
		                'COD_PROD'=>$value->COD_PROD,
		                'DESCRIPCION'=> $value->DESCRIPCION,
		                'CATEGORIA'=> $value->CATEGORIA,
		                'VENDIDO'=> $value->CANTIDAD,
		                'STOCK'=>$value->STOCK,
		                'TOTAL'=> $value->TOTAL,
		                'DESCUENTO'=> $value->DESCUENTO,
		                'UTILIDAD'=>$value->UTILIDADES
		            );
        	
        }

        return $marcas_array;
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
            $event->sheet->getStyle('A1:H1')->applyfromarray($styleArray);
            $this->posicion=$this->posicion+1;
           
            $event->sheet->getStyle('D2:'.'D'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('E2:'.'E'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('F2:'.'F'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('H2:'.'H'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
           
        }

        ];
       
       
      
    }







    public function columnFormats(): array
    {
         return [
           'A' => NumberFormat::FORMAT_NUMBER,
           
 		   'M' => NumberFormat::FORMAT_NUMBER,
           

        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
         return 'TOP';
    }
         
       
    
}
