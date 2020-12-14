<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Registro_Maquina;

class Registro_MaquinaController extends Controller{
    

    /*  ---------------------------- CANTIDAD EN SUCURSAL ---------------------------------------- */

    public function ultimoRegistroSucursalRm(Request $request){

        $rm_sucursal = Registro_Maquina::ultimoCodigoSucursal($request->all());
        return response()->json($rm_sucursal);

    }

    /*  ---------------------------- CANTIDAD EN SECTOR ---------------------------------------- */

    public function ultimoRegistroSectorRm(Request $request){

        $rm_sector = Registro_Maquina::ultimoCodigoSector($request->all());
        return response()->json($rm_sector);

    }

    /*  --------------------------------- GUARDAR --------------------------------------------- */

    public function registroMaquinaGuardar(Request $request){
       
        $maquina = Registro_Maquina::guardarMaquina($request->all());
        return response()->json($maquina);
    }
	
	/*  -------------------------- DATATABLE REGISTROS DE MAQUINAS --------------------------- */

    public function registrosDatatable(Request $request){


       // OBTENER TODOS LOS DATOS

        $registros = Registro_Maquina::registrosMaquinaDatatable($request);
        return response()->json($registros);

    }

    /*  --------------------------------- FILTRAR ---------------------------------------- */

    public function registroMaquinaFiltrar(Request $request){

        $registro = Registro_Maquina::filtrarRegistro($request->all());
        return response()->json($registro);
    }

    /*  ---------------------------- NUEVO REGISTRO ---------------------------------------- */

    public function nuevoRegistroMaquina(){

        $registro = Registro_Maquina::ultimoRegistro();
        return response()->json($registro);

    }


    /*  --------------------------------- ELIMINAR ---------------------------------------- */

    public function eliminarRegistroMaquina(Request $request){

        $registro = Registro_Maquina::eliminarMaquina($request->all());
        return response()->json($registro);
        
    }
}

