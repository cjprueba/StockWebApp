<?php

namespace App\Exports;


use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
USE Illuminate\Support\Facades\DB;
use App\Exports\VentasDiarias;
use Illuminate\Support\Facades\Log;
class VentasDiariasExport implements WithMultipleSheets
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
    public function __construct()
    {
        
    }

            public function sheets(): array
    {
          
Log::error(["repeti"=>1]);

          $this->sheets[]= new VentasDiarias();


          return $this->sheets;
}
}
