<?php

namespace App\Exports;


use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
USE Illuminate\Support\Facades\DB;
class TransferenciaGeneral implements WithMultipleSheets
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
        $this->sucursal = $datos['SucursalDestino'];
    }
            public function sheets(): array
    {


if ($this->hojas==1){

        $this->transferenciageneral= DB::connection('retail')->table('TRANSFERENCIAS_DET')->SELECT(DB::raw('TRANSFERENCIAS_DET.CODIGO_PROD AS COD_PROD'),
            DB::raw('PRODUCTOS.DESCRIPCION'),
             DB::raw('PRODUCTOS.MARCA'),
              DB::raw('PRODUCTOS.LINEA'),
             
            DB::raw('SUM(TRANSFERENCIAS_DET.CANTIDAD) AS CANTIDAD_S'),
        DB::raw('LOTES.COSTO AS PRECOSTO'),
             DB::raw('LOTES.LOTE AS LOTE'),
            DB::raw('(SUM(TRANSFERENCIAS_DET.CANTIDAD)*LOTES.COSTO) AS PRECOSTO_TOTAL'),
            DB::raw ('SUM(TRANSFERENCIAS_DET.TOTAL*T.CAMBIO) AS PRECIO_VENTA'),
            DB::raw('TRANSFERENCIAS_DET.PRECIO*T.CAMBIO as PRECIO_UNIT_VENTA'))

         
                ->leftjoin('TRANSFERENCIAS AS T',function($join){
          $join->on('T.CODIGO','=','TRANSFERENCIAS_DET.CODIGO')
               ->on('TRANSFERENCIAS_DET.ID_SUCURSAL','=','T.ID_SUCURSAL');
               })
            ->leftjoin('LOTE_TIENE_TRANSFERENCIADET','LOTE_TIENE_TRANSFERENCIADET.ID_TRANSFERENCIA_DET','=','TRANSFERENCIAS_DET.ID')
            ->leftjoin('LOTES','LOTES.ID','=','LOTE_TIENE_TRANSFERENCIADET.ID_LOTE')
         ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'TRANSFERENCIAS_DET.CODIGO_PROD')
         ->leftJoin('PRODUCTOS_AUX', 'PRODUCTOS_AUX.CODIGO', '=', 'TRANSFERENCIAS_DET.CODIGO_PROD')
         ->WHERE([['PRODUCTOS_AUX.ID_SUCURSAL','=',$this->sucursal],
          ['LOTES.ID_SUCURSAL','=',$this->sucursal],
         ['T.SUCURSAL_DESTINO','=',$this->sucursal],
         ['T.ESTATUS','=',2 ]])
         ->whereBetween('T.FECMODIF',  [$this->inicio, $this->final])
         ->GROUPBY('TRANSFERENCIAS_DET.CODIGO_PROD')
         ->GROUPBY('TRANSFERENCIAS_DET.PRECIO')
         ->GROUPBY('TRANSFERENCIAS_DET.LOTE')
         ->GROUPBY('PRODUCTOS_AUX.PRECOSTO')
         ->GROUPBY('T.CAMBIO')
         ->get()
         ->toArray();
      

           

$this->info[0]=array(
                 
                'CODIGO'=> 1,
                'DESCRIPCION'=> "Ale",
                'CODIGOL'=> 1,
                'DESCRIPCIONL'=> "ale2",
                'SUCURSAL'=> $this->sucursal,

            );



        $this->sheets[] = new TransferenciaPorMes(1,"a<",1,"as",$this->hojas,$this->transferenciageneral);
       $this->hojas=$this->hojas+1;
      }
       $RESULTS=DB::connection('retail')->TABLE('TRANSFERENCIAS_DET')->SELECT(DB::raw('PRODUCTOS.MARCA as Marca' ),
            DB::raw('MARCA.DESCRIPCION as DescriM' ),
            DB::raw('PRODUCTOS.LINEA As Linea' ),
            DB::raw('TRANSFERENCIAS_DET.CODIGO_PROD' ),
            DB::raw('LINEAS.DESCRIPCION AS descrili')
          )
         ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'TRANSFERENCIAS_DET.CODIGO_PROD')
         ->leftJoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
         ->leftJoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
         ->leftjoin('TRANSFERENCIAS',function($join){
          $join->on('TRANSFERENCIAS.CODIGO','=','TRANSFERENCIAS_DET.CODIGO')
               ->on('TRANSFERENCIAS_DET.ID_SUCURSAL','=','TRANSFERENCIAS.ID_SUCURSAL');
          })
        ->WHERE('TRANSFERENCIAS.SUCURSAL_DESTINO','=',$this->sucursal)
        ->whereBetween('TRANSFERENCIAS.FECMODIF', [$this->inicio, $this->final])
        ->GROUPBY ('PRODUCTOS.MARCA')
        ->GROUPBY ('PRODUCTOS.LINEA')
         ->get()
         ->toArray();

      $this->sheets[]= new TransferenciaTotales($this->transferenciageneral,$RESULTS);
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

          $this->sheets[]= new TransferenciaPorMes($value->Marca,$descrim,$value->Linea,$descrili,$this->hojas,$this->transferenciageneral);


}
return $this->sheets;
}
}

