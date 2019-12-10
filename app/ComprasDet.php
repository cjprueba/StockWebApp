<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Common;
use App\Parametro;

class ComprasDet extends Model
{
    protected $connection = 'retail';
    protected $table = 'comprasdet';
     public static function id_cd($cod_prod, $lote)
    {

    	/*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */

    	// CONSULTAR COMPRAS DET 

    	$comprasdet = DB::connection('retail')
        ->table('COMPRASDET')
        ->select(DB::raw('ID'))
        ->where('COD_PROD', '=', $cod_prod)
        ->where('LOTE', '=', $lote)
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        if (count($comprasdet) > 0) {
            return $comprasdet[0]->ID;
        } else {
            return 0;
        }

        /*  --------------------------------------------------------------------------------- */
    }


    public static function productos_nro_caja($data)
    {
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // INICIAR VARIABLES

        $codigo = $data['nro_caja'];

        /*  --------------------------------------------------------------------------------- */

        // OBTENER PARAMETROS 

        $parametro = Parametro::mostrarParametro();
        
        $monedaSistema = $parametro["parametros"][0]->MONEDA;
        $candecSistem = $parametro["parametros"][0]->CANDEC;

        if ($parametro["parametros"][0]->TAB_UNICA === "SI") {
            $tab_unica = true;
        } else {
            $tab_unica = false;
        }

        /*  --------------------------------------------------------------------------------- */

        $comprasdet = DB::connection('retail')
        ->table('COMPRASDET')
        ->select(DB::raw(
                        'COMPRAS.MONEDA,
                        COMPRASDET.ITEM, 
                        COMPRASDET.COD_PROD AS CODIGO_PROD, 
                        COMPRASDET.DESCRIPCION, 
                        COMPRASDET.CANTIDAD, 
                        COMPRASDET.PREC_VENTA AS PRECIO,
                        0 AS IVA,
                        0 AS IVA_PORCENTAJE,
                        (COMPRASDET.CANTIDAD * COMPRASDET.PREC_VENTA) AS TOTAL'
                    ),
                DB::raw('IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = COMPRASDET.COD_PROD) AND (l.ID_SUCURSAL = COMPRASDET.ID_SUCURSAL))),0) AS STOCK'))
        ->leftJoin('COMPRAS', function($join){
                                $join->on('COMPRAS.CODIGO', '=', 'COMPRASDET.CODIGO')
                                     ->on('COMPRAS.ID_SUCURSAL', '=', 'COMPRASDET.ID_SUCURSAL');
                            })
        ->where('COMPRASDET.ID_SUCURSAL','=', $user->id_sucursal)
        ->where('COMPRAS.NRO_FACTURA','=', $codigo)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        foreach ($comprasdet as $key => $value) {

            /*  --------------------------------------------------------------------------------- */

            // COTIZACION 

            if ($monedaSistema !== $value->MONEDA) {
                $precio = Cotizacion::CALMONED(["monedaSistema" => $monedaSistema, "monedaProducto" => $value->MONEDA, "precio" => $value->PRECIO, "decSistema" => $candecSistem, "tab_unica" => $tab_unica]);

                $comprasdet[$key]->PRECIO = Common::quitar_coma($precio["valor"], $candecSistem);

                $comprasdet[$key]->TOTAL = (float)$comprasdet[$key]->PRECIO * (float)$comprasdet[$key]->CANTIDAD;
                $value->MONEDA = $monedaSistema;
            }
            

            /*  --------------------------------------------------------------------------------- */

            // BUSCAR IVA PRODUCTO

            $producto = DB::connection('retail')
            ->table('PRODUCTOS')
            ->select(DB::raw('IMPUESTO'))
            ->where('CODIGO', '=', $value->CODIGO_PROD)
            ->get();

            /*  --------------------------------------------------------------------------------- */

            // CALCULAR IVA 

            $comprasdet[$key]->IVA_PORCENTAJE = $producto[0]->IMPUESTO;

            if ($comprasdet[$key]->IVA_PORCENTAJE === 10){
                $comprasdet[$key]->IVA = Common::precio_candec_sin_letra($comprasdet[$key]->TOTAL / 11, $value->MONEDA);
            } else if ($comprasdet[$key]->IVA_PORCENTAJE === 5) {
                $comprasdet[$key]->IVA = Common::precio_candec_sin_letra($comprasdet[$key]->TOTAL / 21, $value->MONEDA);
            }

            /*  --------------------------------------------------------------------------------- */

            // DAR FORMATO A LOS MONTOS

            $comprasdet[$key]->PRECIO = Common::precio_candec_sin_letra($value->PRECIO, $value->MONEDA);
            $comprasdet[$key]->TOTAL = Common::precio_candec_sin_letra($comprasdet[$key]->TOTAL, $value->MONEDA);
            $comprasdet[$key]->CANTIDAD = Common::precio_candec_sin_letra($value->CANTIDAD, 1);

            /*  --------------------------------------------------------------------------------- */

        }

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        if (count($comprasdet) > 0) {
            return ['response' => true, 'datos' => $comprasdet];
        } else {
            return ['response' => false];
        }

        /*  --------------------------------------------------------------------------------- */

    }
}
