<?php

namespace App\Exports;
use app/Clientes
use Maatwebsite\Excel\Concerns\FromCollection;

class ClientesPorMarca implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
        public function mostrar(Request $request)
    {


            $ventas = Vendedores::generarConsulta($request->all());
            return response()->json($ventas);

        //return response()->json([$request->all()]);
    }
}
