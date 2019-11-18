<?php

namespace App\Exports;

use App\Ventas_det;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
USE Illuminate\Support\Facades\DB;
class Inventario implements WithMultipleSheets
{
 private $marca;
  private $descuentogeneral;
  private $descuento;
  private $calculo;
  private $calculos;
  private $ventageneral;
  private $linea;
  private $nullsheets;
  private $sheets = [];
  private $hojas=1;
  private $inicio;
 private $modo;
 private $comparacion;
  private $sucursal;
           use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
  public function __construct($datos)
    {
        $this->sucursal = $datos['Sucursal'];
        $this->modo=$datos['Modo'];
    }
            public function sheets(): array
    {
    	if($this->modo==1){
          $this->comparacion='=';
    	}else{
         if($this->modo==2){
         	$this->comparacion='<';
         }else{
         	$this->comparacion='>';
         }
    	}

    	 $this->ventageneral= DB::connection('retail')->table('lista_inventario')->SELECT(DB::raw('lista_inventario.MARCA as Marca' ),
            DB::raw('lista_inventario.CODIGO As COD_PROD' ),
             DB::raw('PRODUCTOS.DESCRIPCION As DESCRIPCION' ),
              DB::raw('lista_inventario.CANTIDAD As CONTEO' ),
               DB::raw('lista_inventario.STOCK As STOCK' )
          )
         ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'lista_inventario.CODIGO')

         ->WHERE('lista_inventario.ID_SUCURSAL','=',4)

         ->get() 
         ->toArray();
   		  	
if($this->modo==4){
$RESULTS= DB::connection('retail')->table('lista_inventario')->SELECT(DB::raw('PRODUCTOS.MARCA as Marca' ),
            DB::raw('MARCA.DESCRIPCION as DescriM' )
          )
         ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'lista_inventario.CODIGO')
          ->leftJoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
         ->WHERE('lista_inventario.ID_SUCURSAL','=',4)
        ->GROUPBY ('PRODUCTOS.MARCA')
    ->orderby('lista_inventario.MARCA')
         ->get() 
         ->toArray();
}else{
	$RESULTS= DB::connection('retail')->table('lista_inventario')->SELECT(DB::raw('PRODUCTOS.MARCA as Marca' ),
            DB::raw('MARCA.DESCRIPCION as DescriM' )
          )
         ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'lista_inventario.CODIGO')
          ->leftJoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
         ->WHERE('lista_inventario.ID_SUCURSAL','=',4)
           ->whereRaw('lista_inventario.STOCK'.$this->comparacion.'lista_inventario.CANTIDAD')
        ->GROUPBY ('PRODUCTOS.MARCA')
    ->orderby('lista_inventario.MARCA')
         ->get() 
         ->toArray();
}

 
$this->sheets[]= new InventarioMarca(1,'PRODUCTOS',$this->ventageneral,$this->hojas,$this->modo);
$this->hojas=$this->hojas+1;
if($this->modo==1){
	$this->sheets[]= new ExportIgual($this->ventageneral,$RESULTS);
}else{
	if($this->modo==2){
$this->sheets[]= new ExportConteo($this->ventageneral,$RESULTS);
	}else{
		if($this->modo==3){
			$this->sheets[]= new ExportStock($this->ventageneral,$RESULTS);
		}else{
			$this->sheets[]= new InventarioTotal($this->ventageneral,$RESULTS);
		}
	}
}
          foreach ($RESULTS as $KEY  => $value) {
          if($value->DescriM==NULL){
            $descrim='Indefinido';
          }else{
            $descrim=$value->DescriM;
          }
          $this->sheets[]= new InventarioMarca($value->Marca,$descrim,$this->ventageneral,$this->hojas,$this->modo,$RESULTS);
           
         }
       
         
          // $this->sheets[]= new StockMarcaLinea($RESULTS);
        
          
         
           
         
       

         
        //return var_dump($this->sheets);
     return $this->sheets;
    }

}
