<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
USE Illuminate\Support\Facades\DB;
use App\Exports\StockMinimo;
use Illuminate\Support\Facades\Log;

class StockMinimoExport implements WithMultipleSheets
{
	  use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
        public function __construct()
    {
        
    }
   public function sheets(): array
    {
        //
                  $this->sheets[]= new StockMinimo();


          return $this->sheets;
    }
}
