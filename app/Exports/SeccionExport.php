<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\VentaSeccionExport;
use App\Exports\CategoriaSeccionExport;
use DateTime;


class SeccionExport implements WithMultipleSheets
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $inicio;
    protected $final;
    protected $categorias;
    protected $sucursal;
    protected $sublineas;
    protected $seccion;
    protected $AllSubCategory;
    protected $AllCategory;
    protected $datos2;

    public function __construct($datos)
    {
        $this->datos2=$datos;

        
    }

    /**
     * @return array
    */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new VentaSeccionExport($this->datos2);
        $sheets[] = new CategoriaSeccionExport($this->datos2);
        $sheets[] = new SubCategoriaSeccionExport($this->datos2);
        
        return $sheets;
    }
}
