<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\VentaSeccionExport;
use App\Exports\CategoriaSeccionExport;

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

    public function __construct(string $inicio, string $final, array $categorias, int $sucursal, array $sublineas, int $seccion)
    {
        $this->inicio = $inicio;
        $this->final = $final;
        $this->categorias = $categorias;
        $this->sucursal = $sucursal;
        $this->sublineas = $sublineas;
        $this->seccion = $seccion;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new VentaSeccionExport($this->inicio, $this->final, $this->categorias, $this->sucursal, $this->sublineas, $this->seccion);
        $sheets[] = new CategoriaSeccionExport($this->inicio, $this->final, $this->categorias, $this->sucursal, $this->sublineas, $this->seccion);
        $sheets[] = new SubCategoriaSeccionExport($this->inicio, $this->final, $this->categorias, $this->sucursal, $this->sublineas, $this->seccion);
        
        return $sheets;
    }
}
