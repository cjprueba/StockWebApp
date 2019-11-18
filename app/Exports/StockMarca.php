<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Ventas_det;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
USE Illuminate\Support\Facades\DB;
class StockMarca implements WithMultipleSheets
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

    	 $RESULTS= DB::connection('retail')->table('PRODUCTOS_AUX')->SELECT(DB::raw('PRODUCTOS.MARCA as Marca' ),
            DB::raw('MARCA.DESCRIPCION as DescriM' ),
              DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRODUCTOS_AUX.CODIGO) AND (l.ID_SUCURSAL = PRODUCTOS_AUX.ID_SUCURSAL))),0) AS STOCK'),
            DB::raw('PRODUCTOS_AUX.CODIGO As COD_PROD' ),
             DB::raw('PRODUCTOS.DESCRIPCION As DESCRIPCION' ),
            DB::raw('PRODUCTOS.LINEA As Lineas' ),
            DB::raw('LINEAS.DESCRIPCION AS descrili')
          )
         ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'PRODUCTOS_AUX.CODIGO')
          ->leftJoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
           ->leftJoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
         ->WHERE('PRODUCTOS_AUX.ID_SUCURSAL','=',$this->sucursal)
         ->WHERE('PRODUCTOS.MARCA','=',8)
        ->orderby('PRODUCTOS.MARCA')
        ->orderby('PRODUCTOS.LINEA')
         ->get() 
         ->toArray();



         
         
           $this->sheets[]= new StockMarcaLinea($RESULTS);
        
          
         
           
         
       

         
        //return var_dump($this->sheets);
     return $this->sheets;
    }
}
