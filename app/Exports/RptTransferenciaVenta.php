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
use DateTime;

class RptTransferenciaVenta implements FromArray, WithTitle,WithEvents,ShouldAutoSize,WithColumnFormatting
{
 private $CODIGO;
    private $ventageneral=[];   
    private $DESCRIPCION;
    private $CODIGOL;
    private $Proveedores;
    private $Sucursales;
    private $Linea;
    private $descri_M;
    private $descri_S;
    private $proveedor;
    private $descri_P;
    private $DESCRIPCIONL;
    private $marcas;
    private  $venta;
    private $inicio;
    private $fin;
    private $user;
    private $total_descuento;
    private $total_utilidad;
    private $total_general;
    private $total_preciounit;
    private $costo;
    private $totalcosto;
    private $cantidadvendida;
    private $stock;
    private $id_sucursal;
    private $agrupar;
    private $stocktotal;
    public $posicion=1;
    private $stockarray;
    private $hojas;
    private $stockarrays;
    public $marcas_array=[];
    public $marcas_array_aux=[];
    private $descuentogeneral;
    private $descuentos;
            public function __construct($datos)
    {
    	
    	 $this->hojas=$datos["HOJAS"];
         $this->id_sucursal=$datos["SUCURSAL"];
         $this->Proveedores=$datos["PROVEEDOR_CODIGO"];
         $this->Sucursales=$datos["SUCURSAL_CODIGO"];
         $this->descri_P=$datos["DESCRI_P"];
         $this->descri_S=$datos["DESCRI_S"];
         $this->agrupar=$datos["AGRUPAR"];
         $this->user=$datos["USER_ID"];
         $this->inicio=$datos["INICIO"];
         $this->fin=$datos["FINAL"];
    }
     public function  array(): array
    {
    	    	
    	 $dia2 = date("Y-m-d");
         $dia1 = new DateTime();
         $dia1=  $dia1->modify('first day of this month');
	    	$dia = '2020-07-02';
	        $precio=100;
	        $descuento_precio=0;
	        $precio_descontado=0;
	        $descuento=0;
	        $descuento_general=0;
	        $precio_descontado_general=0;
	      	$precio_descontado_total=0;
	      	$descuento_real=0;
	      	//SI LA HOJA ES 3 ES POR QUE EXISTEN PRODUCTOS DE TOKUTOKUYA Y POR LA CANTIDAD DE CATEGORIAS Y SUBCATEGORIAS EL EXCEL NO SOPORTA POR ENDE SE LOS ADJUNTA A TODOS EN UNA HOJA LLAMADA "TOKUTOKUYA"
/*	 if($this->hojas==3){
	 	    	  $marcas_array[]=array('COD_PROD','LOTE','STOCK','MARCA','CATEGORIA','SUBCATEGORIA','VENDIDO','PRECIO_UNIT','TOTAL','DESCUENTO','COSTO_UNIT','COSTO_TOTAL','DESCUENTO_PORCENTAJE');
			      $temp=DB::connection('retail')->table('temp_ventas')
	
				 ->select(
				  DB::raw('temp_ventas.COD_PROD AS COD_PROD'),
				  DB::raw('temp_ventas.LOTE AS LOTE'),
				  DB::raw('SUM(temp_ventas.VENDIDO) AS VENDIDO'),
				  DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = temp_ventas.COD_PROD) AND (l.ID_SUCURSAL = temp_ventas.ID_SUCURSAL))),0) AS STOCK'),
				  DB::raw('SUM(temp_ventas.DESCUENTO) AS DESCUENTO'),
				  DB::raw('SUM(COSTO_TOTAL) AS COSTO_TOTAL'),
				  DB::raw('COSTO_UNIT AS COSTO_UNIT'),
				  DB::raw('SUM(temp_ventas.PRECIO) AS TOTAL'),
				  DB::raw('temp_ventas.PRECIO_UNIT AS PRECIO_UNIT'),
				  DB::raw('temp_ventas.CATEGORIA AS CATEGORIA'),
				  DB::raw('temp_ventas.SUBCATEGORIA AS SUBCATEGORIA'),
				  DB::raw('temp_ventas.MARCA AS MARCA'),
				  DB::raw('temp_ventas.DESCUENTO_PORCENTAJE AS DESCUENTO_PORCENTAJE'),
				  DB::raw('temp_ventas.DESCUENTO_PRODUCTO AS DESCUENTO_PRODUCTO'))
				  ->where('temp_ventas.ID_SUCURSAL','=',$this->id_sucursal)
		          ->where('temp_ventas.USER_ID','=',$this->user)
				 
				   ->where('temp_ventas.proveedor','=',19)
				  ->GROUPBY('temp_ventas.COD_PROD','temp_ventas.LOTE','temp_ventas.DESCUENTO_PRODUCTO') 
				  ->orderby('COD_PROD')
				  ->get()
				  ->toArray();
				           
				          	foreach ($temp as $key => $value) 
				          	{
				          	            if($value->TOTAL==0){
									     $value->TOTAL='0';
									    }
									    if($value->PRECIO_UNIT==0){
									      $value->PRECIO_UNIT='0';
									    }
									    if($value->STOCK==0){
									     	$value->STOCK='0';
									    }
									    if($value->DESCUENTO==0){
									     	$value->DESCUENTO='0';
									    }
					          	       $this->posicion=$this->posicion+1;

								       $this->total_general=$this->total_general+$value->TOTAL;
								       $this->total_descuento=$this->total_descuento+$value->DESCUENTO;
								       $this->total_preciounit=$this->total_preciounit+$value->PRECIO_UNIT;
								      $this->cantidadvendida=$this->cantidadvendida+$value->VENDIDO;
								       $this->costo=$this->costo+$value->COSTO_UNIT;
								       $this->totalcosto=$this->totalcosto+$value->COSTO_TOTAL;
				         
					                    $marcas_array[]=array(
					                 
					                'COD_PROD'=> $value->COD_PROD,
					                'LOTE'=> $value->LOTE,
					                'STOCK'=> $value->STOCK,
					                'MARCA'=> $value->MARCA,
					                'CATEGORIA'=> $value->CATEGORIA,
					                'SUBCATEGORIA'=> $value->SUBCATEGORIA,
					                'VENIDO'=> $value->VENDIDO,
					                'PRECIO_UNIT'=>$value->PRECIO_UNIT,
					                'TOTAL'=>$value->TOTAL,
					                'DESCUENTO'=>$value->DESCUENTO,
					                'COSTO_UNIT'=>$value->COSTO_UNIT,
					                'COSTO_TOTAL'=>$value->COSTO_TOTAL,
					                'DESCUENTO_PORCENTAJE'=> $value->DESCUENTO_PRODUCTO
					              );
 
				           }

				             $marcas_array[]=array(
				                'COD_PROD'=> '',
					            'LOTE'=> '',
					            'STOCK'=> '',
					            'MARCA'=> '',
					            'CATEGORIA'=> 'TOTALES',
					            'SUBCATEGORIA'=>'',
					            'VENIDO'=> $this->cantidadvendida,
					            'PRECIO_UNIT'=>$this->total_preciounit,
					            'TOTAL'=> $this->total_general,
					            'DESCUENTO'=> $this->total_descuento,
					            'COSTO_UNIT'=>$this->costo,
					            'COSTO_TOTAL'=>$this->totalcosto,
					            'DESCUENTO_PORCENTAJE'=> ''

	                );
				        return $marcas_array;

	 }*/
	       
	 

	     if($this->hojas==1)
	     {

	      	 	$marcas_array[]=array('COD_PROD','LOTE','STOCK','CATEGORIA','SUBCATEGORIA','MARCA','VENDIDO','PRECIO_UNIT','TOTAL','DESCUENTO','COSTO_UNIT','COSTO_TOTAL','DESCUENTO_PORCENTAJE');

		          $temp=DB::connection('retail')->table('temp_ventas')
		   		 
		           ->select(
		            DB::raw('temp_ventas.COD_PROD AS COD_PROD'),
		            DB::raw('temp_ventas.LOTE AS LOTE'),
		            DB::raw('SUM(temp_ventas.VENDIDO) AS VENDIDO'),
		            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = temp_ventas.COD_PROD) AND (l.ID_SUCURSAL = temp_ventas.ID_SUCURSAL))),0) AS STOCK'),
		            DB::raw('SUM(temp_ventas.DESCUENTO) AS DESCUENTO'),
		            DB::raw('SUM(COSTO_TOTAL) AS COSTO_TOTAL'),
		            DB::raw('COSTO_UNIT AS COSTO_UNIT'),
		            DB::raw('SUM(temp_ventas.PRECIO) AS TOTAL'),
		            DB::raw('temp_ventas.PRECIO_UNIT AS PRECIO_UNIT'),
		            DB::raw('temp_ventas.CATEGORIA AS CATEGORIA'),
		            DB::raw('temp_ventas.SUBCATEGORIA AS SUBCATEGORIA'),
		            DB::raw('temp_ventas.MARCA AS MARCA'),
		            DB::raw('temp_ventas.DESCUENTO_PORCENTAJE AS DESCUENTO_PORCENTAJE'),
		            DB::raw('temp_ventas.DESCUENTO_PRODUCTO AS DESCUENTO_PRODUCTO'))
		          ->where('temp_ventas.ID_SUCURSAL','=',$this->id_sucursal)
		          ->where('temp_ventas.USER_ID','=',$this->user)
		          ->GROUPBY('temp_ventas.COD_PROD','temp_ventas.LOTE','temp_ventas.DESCUENTO_PRODUCTO') 
		          ->orderby('COD_PROD')
		          ->get()
		          ->toArray();

	       
		          foreach ($temp as $key => $value) {
		          	if($value->TOTAL==0){
		          	$value->TOTAL='0';
		          	}
		          	if($value->PRECIO_UNIT==0){
		          		$value->PRECIO_UNIT='0';
		          	}
		          	if($value->STOCK==0){
		          		$value->STOCK='0';
		          	}
		          	if($value->DESCUENTO==0){
		          		$value->DESCUENTO='0';
		          	}
	                $this->posicion=$this->posicion+1;
	               $this->total_general=$this->total_general+$value->TOTAL;
	               $this->total_descuento=$this->total_descuento+$value->DESCUENTO;
	               $this->total_preciounit=$this->total_preciounit+$value->PRECIO_UNIT;
	               $this->cantidadvendida=$this->cantidadvendida+$value->VENDIDO;
	              $this->costo=$this->costo+$value->COSTO_UNIT;
	              $this->totalcosto=$this->totalcosto+$value->COSTO_TOTAL;
		          
		                    $marcas_array[]=array(
		                 
		                'COD_PROD'=> $value->COD_PROD,
		                'LOTE'=> $value->LOTE,
		                'STOCK'=> $value->STOCK,
		                'CATEGORIA'=> $value->CATEGORIA,
		                'SUBCATEGORIA'=> $value->SUBCATEGORIA,
		                'MARCA'=> $value->MARCA,
		                'VENIDO'=> $value->VENDIDO,
		                'PRECIO_UNIT'=>$value->PRECIO_UNIT,
		                'TOTAL'=>$value->TOTAL,
		                'DESCUENTO'=>$value->DESCUENTO,
		                'COSTO_UNIT'=>$value->COSTO_UNIT,
		                'COSTO_TOTAL'=>$value->COSTO_TOTAL,
		                'DESCUENTO_PORCENTAJE'=> $value->DESCUENTO_PRODUCTO
		                
		                

		              
		              

		            );
		          }


		          $marcas_array[]=array(
	                'COD_PROD'=> '',
		            'LOTE'=> '',
		            'STOCK'=> '',
		            'MARCA'=>'',
		            'CATEGORIA'=> '',
		            'SUBCATEGORIA'=> 'TOTALES',     
		            'VENIDO'=> $this->cantidadvendida,
		            'PRECIO_UNIT'=>$this->total_preciounit,
		            'TOTAL'=> $this->total_general,
		            'DESCUENTO'=> $this->total_descuento,
		            'COSTO_UNIT'=>$this->costo,
		            'COSTO_TOTAL'=>$this->totalcosto,
		            'DESCUENTO_PORCENTAJE'=> ''

	                );
		     }else{

				     		 $marcas_array[]=array('COD_PROD','LOTE','STOCK','MARCA','CATEGORIA','SUBCATEGORIA','VENDIDO','PRECIO_UNIT','TOTAL','DESCUENTO','COSTO_UNIT','COSTO_TOTAL','DESCUENTO_PORCENTAJE');
			                $temp=DB::connection('retail')->table('temp_ventas')
				   			
				           ->select(
				            DB::raw('temp_ventas.COD_PROD AS COD_PROD'),
				            DB::raw('temp_ventas.LOTE AS LOTE'),
				            DB::raw('SUM(temp_ventas.VENDIDO) AS VENDIDO'),
				            DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = temp_ventas.COD_PROD) AND (l.ID_SUCURSAL = temp_ventas.ID_SUCURSAL))),0) AS STOCK'),
				            DB::raw('SUM(temp_ventas.DESCUENTO) AS DESCUENTO'),
				            DB::raw('SUM(COSTO_TOTAL) AS COSTO_TOTAL'),
				            DB::raw('COSTO_UNIT AS COSTO_UNIT'),
				            DB::raw('SUM(temp_ventas.PRECIO) AS TOTAL'),
				            DB::raw('temp_ventas.PRECIO_UNIT AS PRECIO_UNIT'),
				            DB::raw('temp_ventas.CATEGORIA AS CATEGORIA'),
				            DB::raw('temp_ventas.SUBCATEGORIA AS SUBCATEGORIA'),
				            DB::raw('temp_ventas.MARCA AS MARCA'),
				            DB::raw('temp_ventas.DESCUENTO_PORCENTAJE AS DESCUENTO_PORCENTAJE'),
				            DB::raw('temp_ventas.DESCUENTO_PRODUCTO AS DESCUENTO_PRODUCTO'))
				            ->where('temp_ventas.ID_SUCURSAL','=',$this->id_sucursal)
		                    ->where('temp_ventas.USER_ID','=',$this->user)
				            ->GROUPBY('temp_ventas.COD_PROD','temp_ventas.LOTE','temp_ventas.DESCUENTO_PRODUCTO') 
				            ->orderby('COD_PROD');
				            if($this->agrupar==0){
				            	 $temp->where('temp_ventas.PROVEEDOR_CODIGO','=',$this->Proveedores);
				           
				        }else{
				        	     $temp->where('temp_ventas.SUCURSAL_ORIGEN','=',$this->Sucursales);
				        }
				        $temp=$temp->get()->toArray();
				           
				          	foreach ($temp as $key => $value) 
				          	{
				          	          		       if($value->TOTAL==0){
											          $value->TOTAL='0';
											         }
											         if($value->PRECIO_UNIT==0){
											           $value->PRECIO_UNIT='0';
											          }
											          if($value->STOCK==0){
											          	$value->STOCK='0';
											          }
											          if($value->DESCUENTO==0){
											          	$value->DESCUENTO='0';
											          }
					          	       $this->posicion=$this->posicion+1;

								       $this->total_general=$this->total_general+$value->TOTAL;
								       $this->total_descuento=$this->total_descuento+$value->DESCUENTO;
								       $this->total_preciounit=$this->total_preciounit+$value->PRECIO_UNIT;
								      $this->cantidadvendida=$this->cantidadvendida+$value->VENDIDO;
								       $this->costo=$this->costo+$value->COSTO_UNIT;
								       $this->totalcosto=$this->totalcosto+$value->COSTO_TOTAL;
				         
					                    $marcas_array[]=array(
					                 
					                'COD_PROD'=> $value->COD_PROD,
					                'LOTE'=> $value->LOTE,
					                'STOCK'=> $value->STOCK,
					                'MARCA'=> $value->MARCA,
					                'CATEGORIA'=> $value->CATEGORIA,
					                'SUBCATEGORIA'=> $value->SUBCATEGORIA,
					                'VENIDO'=> $value->VENDIDO,
					                'PRECIO_UNIT'=>$value->PRECIO_UNIT,
					                'TOTAL'=>$value->TOTAL,
					                'DESCUENTO'=>$value->DESCUENTO,
					                'COSTO_UNIT'=>$value->COSTO_UNIT,
					                'COSTO_TOTAL'=>$value->COSTO_TOTAL,
					                'DESCUENTO_PORCENTAJE'=> $value->DESCUENTO_PRODUCTO
					              );
 
				           }

				             $marcas_array[]=array(
				                'COD_PROD'=> '',
					            'LOTE'=> '',
					            'STOCK'=> '',
					            'MARCA'=> '',
					            'CATEGORIA'=> 'TOTALES',
					            'SUBCATEGORIA'=>'',
					            'VENIDO'=> $this->cantidadvendida,
					            'PRECIO_UNIT'=>$this->total_preciounit,
					            'TOTAL'=> $this->total_general,
					            'DESCUENTO'=> $this->total_descuento,
					            'COSTO_UNIT'=>$this->costo,
					            'COSTO_TOTAL'=>$this->totalcosto,
					            'DESCUENTO_PORCENTAJE'=> ''

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
            $event->sheet->getStyle('A1:M1')->applyfromarray($styleArray);
            $this->posicion=$this->posicion+1;
            $event->sheet->getStyle('E'.$this->posicion.':L'.$this->posicion)->applyfromarray($styleArray);
            $event->sheet->getStyle('G2:'.'G'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('F2:'.'F'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('H2:'.'H'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('I2:'.'I'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('J2:'.'J'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('K2:'.'K'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
            $event->sheet->getStyle('L2:'.'L'.$this->posicion)->getNumberFormat()->setFormatCode('#,##0.00');
             
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
         if($this->hojas==1){
             return 'GENERAL';
         }else{
            if($this->agrupar==0){
            	$titulo=$this->descri_P;
                return substr($titulo,0,31);
            }else{
            	 $titulo=$this->descri_S;
                  return substr($titulo,0,31);
            }
         
         }
         
       
    }
}
