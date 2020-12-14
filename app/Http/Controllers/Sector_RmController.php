<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sector_Rm;

class Sector_RmController extends Controller{
    
    /*  --------------------------------- DATATABLE--------------------------------------- */

    public function sectorRmDatatable(Request $request){

        $rm_sector = Sector_Rm::sectorRmDatatable($request);
        return response()->json($rm_sector);

    }

    /*  --------------------------------- FILTRAR ---------------------------------------- */

    public function filtrarSectorRm(Request $request){

        $rm_sector = Sector_Rm::filtrarSectorRM($request->all());
        return response()->json($rm_sector);
    }

    /*  --------------------------------- GUARDAR ---------------------------------------- */

    public function guardarSectorRm(Request $request){
       
        $rm_sector = Sector_Rm::guardarSectorRM($request->all());
        return response()->json($rm_sector);
    }

    /*  --------------------------------- ELIMINAR ---------------------------------------- */

    public function eliminarSectorRm(Request $request){

        $rm_sector = Sector_Rm::eliminarSectorRM($request->all());
        return response()->json($rm_sector);
        
    }

    /*  ---------------------------- NUEVO SECTOR ---------------------------------------- */

    public function sectorNuevoRm(){

        $rm_sector = Sector_Rm::nuevoSectorRM();
        return response()->json($rm_sector);

    }

}
