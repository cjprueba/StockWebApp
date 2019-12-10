<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
USE Illuminate\Support\Facades\DB;
use App\ComprasDet;
class Compras implements WithMultipleSheets
{
  private $marca;
  private $descuentogeneral;
  private $descuento;
  private $calculo;
  private $calculos;
  private $ventageneral;
  private $info=[];
  private $linea;
  private $nullsheets;
  private $sheets = [];
  private $hojas=1;
  private $inicio;
  private $final;
  private $sucursal;
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
    $this->transferenciageneral= ComprasDet::SELECT(DB::raw('COMPRASDET.COD_PROD AS COD_PROD'),
    DB::raw('PRODUCTOS.DESCRIPCION'),
    DB::raw('PRODUCTOS.MARCA AS Marca'),
    DB::raw('SUM(COMPRASDET.CANTIDAD) AS CANTIDAD_S'),      
    DB::raw('PRODUCTOS_AUX.FECHULT_C'))
    
    ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'COMPRASDET.COD_PROD')
    ->leftJoin('PRODUCTOS_AUX', 'PRODUCTOS_AUX.CODIGO', '=', 'COMPRASDET.COD_PROD')
    ->WHERE([['PRODUCTOS_AUX.ID_SUCURSAL','=',$this->sucursal]])
    ->whereBetween('COMPRASDET.FECALTAS',  [$this->inicio, $this->final])
    ->GROUPBY('COMPRASDET.COD_PROD')
    ->get()
    ->toArray();
    $this->sheets[] = new ComprasMarca(1,"a<",$this->hojas,$this->transferenciageneral);
    $this->hojas=$this->hojas+1;
   }
   // $RESULTS= ComprasDet::SELECT(DB::raw('PRODUCTOS.MARCA as Marca' ),
    //DB::raw('MARCA.DESCRIPCION as DescriM' ),
    //DB::raw('COMPRASDET.COD_PROD' ))
    //->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'COMPRASDET.COD_PROD')
    //->leftJoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
    //->WHERE('COMPRASDET.ID_SUCURSAL','=',$this->sucursal)
    //->whereBetween('COMPRASDET.FECALTAS', [$this->inicio, $this->final])
    //->GROUPBY ('PRODUCTOS.MARCA')
    //->GROUPBY ('PRODUCTOS.LINEA')
    //->get()
    //->toArray();
    //foreach ($RESULTS as $KEY  => $value) {
     //if($value["DescriM"]==NULL){
       //  $descrim='Indefinido';
        //}else{
         //$descrim=$value["DescriM"];
       // }

     //&$this->sheets[]= new ComprasMarca($value["Marca"],$descrim,$this->hojas,$this->transferenciageneral);
    //}
   return $this->sheets;
  }
}
