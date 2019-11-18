<?php

namespace App\Exports;
use app/Vendedores;
use Maatwebsite\Excel\Concerns\FromCollection;

class VendedorPorMarca implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //
    }
            public function mostrar(Request $request)
    {


            $ventas = Vendedores::generarConsulta($request->all());
            return response()->json($ventas);

        //return response()->json([$request->all()]);
    }
}
