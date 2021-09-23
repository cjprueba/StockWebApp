<!DOCTYPE html>
<html>
<head>
    <title>BOLETA DE VENTA</title>
    <style type="text/css">
    body{
        font-size: 15px;
        font-family: "Arial";
    }
    table{
        border-collapse: collapse;
    }
    td{
        padding: 1.85px 4px;
        font-size: 11px;
    }
    .tabla1{
        margin-bottom: 3px;
    }
    .tabla2 {
        padding-top: 92px;
        margin-bottom: 0px;
        padding-bottom: 0px;
    }
    .tabla3{
        border: 1px solid #000;
        margin-top: 63px;
    }
    .tabla3 td{
        border: 1px solid #fff;
    }
    .tabla3 .cancelado{
        border-left: 0;
        border-right: 0;
        border-bottom: 0;
        border-top: 1px dotted #000;
        width: 200px;
    }
    .emisor{
        color: red;
    }
    .linea{
        border-bottom: 1px dotted #000;
    }
    .border{
        border: 1px solid #000;
    }
    .fondo{
        background-color: #dfdfdf;
    }
    .fisico{
        color: #fff;
    }
    .fisico td{
        color: #fff;
    }
    .fisico .border{
        border: 1px solid #fff;
    }
    .fisico .tabla3 td{
        border: 1px solid #fff;
    }
    .fisico .linea{
        border-bottom: 1px dotted #fff;
    }
    .fisico .emisor{
        color: #fff;
    }
    .fisico .tabla3 .cancelado{
        border-top: 1px dotted #fff;
    }
    .fisico .text{
        color: #000;
    }
    .fisico .fondo{
        background-color: #fff;
    }

    .tabla2 {
       border-collapse:separate; 
       border-spacing: 0 5px;
    }

    .tablaRazon {
        margin-top: -8px;
        padding-top: 0px;
       border-collapse:separate; 
       border-spacing: -2 -1px;
    }

    .tablaFooter {
        margin-top: 34px;
    }

    .tablaFooter td {
        border: 1px solid #fff;
    }

    .tablaFooter2 td {
        margin-top: -20px;
    }

    @if($tipo=='fisico')
    #logo{
        display: none;
    }
    @endif


</style>
</head>
<body>
    <div class="@if($tipo=='fisico') fisico @endif">
        <table width="100%" class="tabla2" >
            <tr>
                <td width="12%">Señor (es):</td>
                <td width="36%" class="linea" align="left"><span class="text">{{ $fecha }}</span></td>
                <td width="5%">&nbsp;</td>
                <td width="14%">&nbsp;</td>
                <td width="4%" align="left" class="border fondo"><span class="text">{{ $contado_x }}</span></td>
                <td width="7%"></td>
                <td width="9%" align="left"><span class="text">{{ $credito_x }}</span></td>
                <td width="4%" ></td>
            </tr>
        </table>
        <table width="100%" class="tablaRazon">
            <tr>
                <td width="10%">Señor (es):</td>
                <td width="41%" align="left"><span class="text">{{ $cliente }}</span></td>
                <td width="11%">&nbsp;</td>
                <td width="12%"><span class="text">{{ $ruc }}</span></td>
                <td width="13%">&nbsp;</td>
                <td width="7%" ><span class="text"></span></td>
                <td width="4%"></td>
                <td width="2%"></td>
            </tr>
            <tr>
                <td>Dirección:</td>
                <td ><span class="text">{{ $direccion }}</span></td>
                <td>&nbsp;</td>
                <td width="12%"><span class="text">{{ $telefono }}</span></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
               
            </tr>
        </table>
        <table width="100%" class="tabla3">
            @for ($i = 0; $i < 9; $i++)
            @if (array_key_exists($i, $articulos))
            <tr>
                <td width="11%" align="left"><span class="text">{{ $articulos[$i]['cod_prod']}}</span></td>
                <td width="8%" align="center"><span class="text">{{ $articulos[$i]['cantidad'] }}</span></td>
                <td width="32%" align="left"><span class="text">{{ $articulos[$i]['descripcion'] }}</span></td>
                <td width="10%" align="left"><span class="text">{{ $articulos[$i]['precio'] }}</span></td>
                <td width="10%" align="center"><span class="text">{{ $articulos[$i]['exentas'] }}</span></td>
                <td width="13%" align="center"><span class="text">{{ $articulos[$i]['base5'] }}</span></td>
                <td width="16%" align="left"><span class="text">{{ $articulos[$i]['base10'] }}</span></td>
            </tr>
            @else
            <tr>
                <td width="11%" align="left">&nbsp;</td>
                <td width="8%"  align="center">&nbsp;</td>
                <td width="32%" align="left">&nbsp;</td>
                <td width="10%" align="left">&nbsp;</td>
                <td width="10%" align="center">&nbsp;</td>
                <td width="13%" align="center">&nbsp;</td>
                <td width="16%" align="left">&nbsp;</td>
            </tr>
            @endif
            @endfor
        </table>



 

        <table width="100%" class="tablaFooter">
            <tr>
                <td width="13%" align="center"></td>
                <td width="72%" style="font-size: 9px;" align="left"><span class="text">{{ $letra }}</span></td>
                <td width="15%" align="left"><span class="text">{{ $total }}</span></td>
            </tr>
        </table>
        <table width="100%" class="tablaFooter2">
            <tr>
                <td width="23%"></td>
                <td width="15%" align="left"><span class="text">{{ $base5 }}</span></td>
                <td width="21%" align="left"><span class="text">{{ $base10 }}</span></td>
                <td width="41%" align="left"><span class="text">{{ $iva }}</span></td>
            </tr>
        </table>
    </div>
</body>
</html>