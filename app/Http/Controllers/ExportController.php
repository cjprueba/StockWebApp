<?php

namespace App\Http\Controllers;
use App\Exports\VentasMarca;
use App\Exports\RptVentaMarcaExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ExportController extends Controller
{

	public function __construct(){
        $this->middleware('auth');
    }

    public function mostrar(Request $request)
    {
        return Excel::download(new VentasMarca($request->all()), 'ventasMarca.xlsx');
    }
     public function descargarMarcaCategoria(Request $request)
    {
        return Excel::download(new RptVentaMarcaExport($request->all()), 'ventasMarcaCategoria.xlsx');
    }
         public function descargarTransferenciaVentas(Request $request)
    {
        return Excel::download(new RptTransferenciaVentaExport($request->all()), 'TransferenciasVentasConsignacion.xlsx');
    }
}
