<?php

namespace App\Exports;


use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
USE Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
  private $consignacion;
         use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
     public function __construct($datos)
    {
        $this->inicio = date('Y-m-d', strtotime($datos['Inicio']));
        $this->final  =  date('Y-m-d', strtotime($datos['Final']));
        $this->sucursal = $datos['SucursalDestino'];
        $this->consignacion=$datos['Consignacion'];
    }
            public function sheets(): array
    {


if ($this->hojas==1){

        $this->transferenciageneral= DB::connection('retail')->table('TRANSFERENCIAS as T')->SELECT(
            DB::raw('TRANSFERENCIAS_DET.CODIGO_PROD AS COD_PROD,
              PRODUCTOS.DESCRIPCION,
              IFNULL(PRODUCTOS.MARCA,0) AS MARCA,
              IFNULL(PRODUCTOS.LINEA,0) AS LINEA,
              SUM(TRANSFERENCIAS_DET.CANTIDAD) AS CANTIDAD_S,
              LOTES.COSTO AS PRECOSTO,
              LOTES.LOTE AS LOTE,
              (SUM(TRANSFERENCIAS_DET.CANTIDAD)*LOTES.COSTO) AS PRECOSTO_TOTAL,
              SUM(TRANSFERENCIAS_DET.TOTAL*T.CAMBIO) AS PRECIO_VENTA,
              TRANSFERENCIAS_DET.PRECIO*T.CAMBIO as PRECIO_UNIT_VENTA,
              PRODUCTOS_AUX.PROVEEDOR as PROVEEDOR'))
            ->leftjoin('TRANSFERENCIAS_DET','TRANSFERENCIAS_DET.FK_TRANSFERENCIA','T.ID')
         /* ->leftjoin('TRANSFERENCIAS AS T',function($join){
          $join->on('T.CODIGO','=','TRANSFERENCIAS_DET.CODIGO')
               ->on('TRANSFERENCIAS_DET.ID_SUCURSAL','=','T.ID_SUCURSAL');
               })*/

          ->leftjoin('LOTE_TIENE_TRANSFERENCIADET','LOTE_TIENE_TRANSFERENCIADET.ID_TRANSFERENCIA_DET','=','TRANSFERENCIAS_DET.ID')
          ->leftjoin('LOTES','LOTES.ID','=','LOTE_TIENE_TRANSFERENCIADET.ID_LOTE')
          ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'TRANSFERENCIAS_DET.CODIGO_PROD')
          ->leftjoin('PRODUCTOS_AUX',function($join){
          $join->on('PRODUCTOS_AUX.CODIGO','=','TRANSFERENCIAS_DET.CODIGO_PROD')
               ->on('PRODUCTOS_AUX.ID_SUCURSAL','=','T.SUCURSAL_DESTINO');
               })
          ->WHERE([
          ['LOTES.ID_SUCURSAL','=',$this->sucursal],
          ['T.SUCURSAL_DESTINO','=',$this->sucursal],
          ['T.ESTATUS','=',2 ]])
         ->whereBetween('T.FECMODIF',  [$this->inicio, $this->final])
         ->GROUPBY('TRANSFERENCIAS_DET.CODIGO_PROD')
         ->GROUPBY('TRANSFERENCIAS_DET.PRECIO')
         ->GROUPBY('TRANSFERENCIAS_DET.LOTE')
         ->GROUPBY('T.CAMBIO');
         if($this->consignacion===true){
          $this->transferenciageneral=$this->transferenciageneral->WHERE('T.CONSIGNACION','=',1);
         }
         $this->transferenciageneral=$this->transferenciageneral->get()
         ->toArray();
      

           

       $data=array(
                 
                'CODIGO'=> 1,
                'DESCRIPCION'=> "Ale",
                'CODIGOL'=> 1,
                'DESCRIPCIONL'=> "ale2",
                'HOJAS'=>$this->hojas,
                'SUCURSAL'=> $this->sucursal,
                'CONSIGNACION'=>$this->consignacion,
                'TRANSFERENCIAS'=>$this->transferenciageneral,

            );



        $this->sheets[] = new TransferenciaPorMes($data,0);
       $this->hojas=$this->hojas+1;
      }
       $RESULTS=DB::connection('retail')->TABLE('TRANSFERENCIAS')->SELECT(DB::raw('PRODUCTOS.MARCA as Marca' ),
            DB::raw('IFNULL(MARCA.DESCRIPCION,"INDEFINIDO") as DescriM' ),
            DB::raw('IFNULL(PRODUCTOS.LINEA,0) As Linea' ),
            DB::raw('TRANSFERENCIAS_DET.CODIGO_PROD' ),
            DB::raw('IFNULL(LINEAS.DESCRIPCION,"INDEFINIDO") AS descrili')
          )
         ->leftjoin('TRANSFERENCIAS_DET','TRANSFERENCIAS_DET.FK_TRANSFERENCIA','=','TRANSFERENCIAS.ID')
         ->leftJoin('PRODUCTOS', 'PRODUCTOS.CODIGO', '=', 'TRANSFERENCIAS_DET.CODIGO_PROD')
         ->leftjoin('PRODUCTOS_AUX',function($join){
          $join->on('PRODUCTOS_AUX.CODIGO','=','PRODUCTOS.CODIGO')
               ->on('PRODUCTOS_AUX.ID_SUCURSAL','=','TRANSFERENCIAS.SUCURSAL_DESTINO');
          })
         ->leftJoin('MARCA', 'MARCA.CODIGO', '=', 'PRODUCTOS.MARCA')
         ->leftJoin('LINEAS', 'LINEAS.CODIGO', '=', 'PRODUCTOS.LINEA')
         /*->leftjoin('TRANSFERENCIAS',function($join){
          $join->on('TRANSFERENCIAS.CODIGO','=','TRANSFERENCIAS_DET.CODIGO')
               ->on('TRANSFERENCIAS_DET.ID_SUCURSAL','=','TRANSFERENCIAS.ID_SUCURSAL');
          })*/
        ->WHERE('TRANSFERENCIAS.SUCURSAL_DESTINO','=',$this->sucursal)
        ->WHERE('TRANSFERENCIAS.ESTATUS','=',2)->WHERE('PRODUCTOS_AUX.PROVEEDOR','<>',19)
        ->whereBetween('TRANSFERENCIAS.FECMODIF', [$this->inicio, $this->final])
        ->GROUPBY ('PRODUCTOS.MARCA')
        ->GROUPBY ('PRODUCTOS.LINEA')->ORDERBY('MARCA.DESCRIPCION')->ORDERBY('LINEAS.DESCRIPCION');
        if($this->consignacion===true){
          $RESULTS=$RESULTS->where('TRANSFERENCIAS.CONSIGNACION','=',1);
        }
         $RESULTS=$RESULTS->get()
         ->toArray();
          $data=array(
                 
                'CODIGO'=> 0,
                'DESCRIPCION'=> '',
                'CODIGOL'=> 0,
                'DESCRIPCIONL'=> '',
                'HOJAS'=>$this->hojas,
                'SUCURSAL'=> $this->sucursal,
                'TRANSFERENCIAS'=>$this->transferenciageneral,
                'CONSIGNACION'=>$this->consignacion

            );
       $this->sheets[]= new TransferenciaTotales($this->transferenciageneral,$RESULTS,$this->sucursal);
       $this->sheets[]= new TransferenciaPorMes($data,1);
         foreach ($RESULTS as $KEY  => $value) {
          if($value->DescriM==NULL){
            $descrim='INDEFINIDO';
          }else{
            $descrim=$value->DescriM;
          }
          if($value->descrili==NULL){
            $descrili='INDEFINIDO';
          }else{
            $descrili=$value->descrili;
          }

          $data=array(
                 
                'CODIGO'=> $value->Marca,
                'DESCRIPCION'=> $descrim,
                'CODIGOL'=> $value->Linea,
                'DESCRIPCIONL'=> $descrili,
                'HOJAS'=>$this->hojas,
                'SUCURSAL'=> $this->sucursal,
                'TRANSFERENCIAS'=>$this->transferenciageneral,
                'CONSIGNACION'=>$this->consignacion

            );
        

          $this->sheets[]= new TransferenciaPorMes($data,0);


}
return $this->sheets;
}
}

