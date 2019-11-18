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

class VentasMarca implements WithMultipleSheets
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
  private $final;
  private $sucursal;
   private $vales = array("v", "a", "l", "e", "%");
         use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
 public function __construct($datos)
    {
     
        $this->inicio = date('Y-m-d', strtotime($datos['Inicio']));
        $this->final  =  date('Y-m-d', strtotime($datos['Final']));
        $this->sucursal = $datos['Sucursal'];
    }

            public function sheets(): array
    {


      if ($this->hojas==1){

        $this->ventageneral = DB::connection('retail')->table('VENTASDET')->SELECT(DB::raw('VENTASDET.COD_PROD AS COD_PROD'),
            DB::raw('PRODUCTOS.DESCRIPCION'), 
            DB::raw('SUM(VENTASDET.CANTIDAD) AS CANTIDAD_S'),
            DB::raw('PRODUCTOS.LINEA AS LINEA'), 
            DB::raw('PRODUCTOS.MARCA AS MARCA'), 
           DB::raw('LOTES.COSTO AS PRECOSTO'),
             DB::raw('LOTES.LOTE AS LOTE'),
            DB::raw('(SUM(VENTASDET.CANTIDAD)*LOTES.COSTO) AS PRECOSTO_TOTAL'),
           DB::raw ('SUM(VENTASDET.PRECIO) AS PRECIO_VENTA'),
            DB::raw('VENTASDET.PRECIO_UNIT as PRECIO_UNIT_VENTA'),
            DB::raw('IFNULL(((SUM(VENTASDET.PRECIO))-(SUM(VENTASDET.CANTIDAD) *LOTES.COSTO)),0) AS UTILIDAD'))
         ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
                              ->leftjoin('LOTES',function($join){
             $join->on('VENTASDET.LOTE','=','LOTES.LOTE')
             ->on('VENTASDET.COD_PROD','=','LOTES.COD_PROD')
               ->on('LOTES.ID_SUCURSAL','=','VENTASDET.ID_SUCURSAL');
         })
         ->WHERE([ 
         ['VENTASDET.DESCRIPCION', 'not like', 'DESCUENTO%'],
         ['VENTASDET.ID_SUCURSAL','=',$this->sucursal],
         ['VENTASDET.ANULADO','<>',1 ]])
         ->whereBetween('VENTASDET.FECALTAS', [$this->inicio, $this->final])
         ->GROUPBY('VENTASDET.COD_PROD')
         ->GROUPBY('VENTASDET.LOTE')
         ->GROUPBY('VENTASDET.PRECIO_UNIT')
         ->get()
         ->toArray();


           $this->descuentogeneral= DB::connection('retail')->table('VENTASDET')->SELECT(DB::raw('VENTASDET.CODIGO'),
            DB::raw('SUBSTRING(VENTASDET.DESCRIPCION,11,15) AS PORCENTAJE'),
            DB::raw('VENTASDET.CAJA'),
            DB::raw('VENTASDET.ID_SUCURSAL'),
            DB::raw('VENTASDET.ITEM')
         )
             ->where([['VENTASDET.ID_SUCURSAL','=',$this->sucursal],
            ['VENTASDET.DESCRIPCION', 'like', 'DESCUENTO%'],
               ['VENTASDET.cod_prod', '=','2'],
             ['VENTASDET.ANULADO','<>',1]])
               ->whereBetween('VENTASDET.FECALTAS', [$this->inicio, $this->final])
               ->get();
               foreach ($this->descuentogeneral as $this->descuento ) {
                $this->descuento->PORCENTAJE = str_replace($this->vales, "", $this->descuento->PORCENTAJE);
                    $this->calculo=DB::connection('retail')->table('VENTASDET')->SELECT(DB::raw('VENTASDET.COD_PROD'),
            DB::raw('VENTASDET.PRECIO'),
            DB::raw('VENTASDET.PRECIO_UNIT'),
            DB::raw('VENTASDET.ITEM')
             )
             ->where([
            ['VENTASDET.ID_SUCURSAL','=',$this->sucursal],
            ['VENTASDET.CODIGO', '=', $this->descuento->CODIGO],
            ['VENTASDET.DESCRIPCION', 'not like', 'DESCUENTO%'],
            ['VENTASDET.CAJA', '=',$this->descuento->CAJA],
             ])

           ->get();

                 foreach ($this->calculo as $this->calculos) {

                  if ($this->calculos->ITEM< $this->descuento->ITEM){
                    $key=array_search($this->calculos->COD_PROD, array_column($this->ventageneral, 'COD_PROD'));

                  $this->ventageneral[$key]->PRECIO_VENTA=(int)$this->ventageneral[$key]->PRECIO_VENTA-(((int)$this->calculos->PRECIO*(float)$this->descuento->PORCENTAJE)/100);

                                        # code...
                  }
                  } # code...
               }
         $this->sheets[]=new VentaMarcaPorMes(1,"a<",1,"as",$this->hojas,$this->ventageneral);
        $this->hojas=$this->hojas+1;
      } 
     


          $RESULTS= DB::connection('retail')->table('VENTASDET')->SELECT(DB::raw('PRODUCTOS.MARCA as Marca' ),
            DB::raw('MARCA.DESCRIPCION as DescriM' ),
            DB::raw('PRODUCTOS.LINEA As Lineas' ),
            DB::raw('LINEAS.DESCRIPCION AS descrili')
          )
         ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'VENTASDET.COD_PROD')
          ->leftJoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
           ->leftJoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
         ->WHERE('VENTASDET.ID_SUCURSAL','=',$this->sucursal)
         ->where('VENTASDET.DESCRIPCION', 'not like', 'DESCUENTO%')
        ->whereBetween('VENTASDET.FECALTAS', [$this->inicio, $this->final])
        ->GROUPBY ('PRODUCTOS.MARCA')
        ->GROUPBY ('PRODUCTOS.LINEA')
         ->get() 
         ->toArray();
  $this->sheets[]= new VentasMarcaTotal($this->ventageneral,$RESULTS);

         foreach ($RESULTS as $KEY  => $value) {
          if($value->DescriM==NULL){
            $descrim='Indefinido';
          }else{
            $descrim=$value->DescriM;
          }
          if($value->descrili==NULL){
            $descrili='Indefinido';
          }else{
            $descrili=$value->descrili;
          }
          $this->sheets[]= new VentaMarcaPorMes($value->Marca,$descrim,$value->Lineas,$descrili,$this->hojas,$this->ventageneral);
           
         }
       

         
        //return var_dump($this->sheets);
     return $this->sheets;

      } 
}
