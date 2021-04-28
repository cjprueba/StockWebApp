<!DOCTYPE html>
<html>
<head>
    <title>NOTA DE CREDITO</title>
    <style type="text/css">
    body{
        font-size: 12px;
        font-family: "Arial";
    }
    table{
        border-collapse: collapse;
    }
    td{
        padding: 2.5px 4.5px;
        font-size: 8px;
    }
    .h1{
        font-size: 21px;
        font-weight: bold;
    }
    .h2{
        font-size: 18px;
        font-weight: bold;
    }
    .tabla1{
        margin-bottom: 3px;
    }
    .tabla2 {
        padding-top: 19px;
        margin-bottom: 0px;
        padding-bottom: 0px;
    }
    .tabla3{
        border: 1px solid #fff;
        margin-top: 45px;
    }
    .tabla3 td{
        border: 1px solid #fff;
    }
    .tabla3 .cancelado{
        border-left: 0;
        border-right: 0;
        border-bottom: 0;
        border-top: 1px dotted #fff;
        width: 200px;
    }
    .tabla4 {
        padding-top: 15px;
        margin-bottom: 0px;
        padding-bottom: 0px;
    }
    .emisor{
        color: red;
    }
    .linea{
        border-bottom: 1px dotted #fff;
    }
    .border{
        border: 1px solid #fff;
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
       border-collapse:collapse; 
       border-spacing: -2 -1px;
    }

    .tablaFooter {
        margin-top: 18px;
    }

    .tablaFooter td {
        border: 1px solid #fff;
    }

    .tablaFooter2 td {
        border: 1px solid #fff;
        margin-bottom:: 15px;
    }
    .tabla4 {
        padding-top: 18px;
        margin-bottom: 0px;
        padding-bottom: 0px;
    }
    .tabla4 {
       border-collapse:separate; 
       border-spacing: 0 5px;
    }
    .tabla5 {
        padding-top: 14px;
        margin-bottom: 0px;
        padding-bottom: 0px;
    }
    .tabla5 {
       border-collapse:separate; 
       border-spacing: 0 5px;
    }
    .tablaRazon2 {
        margin-top: -8px;
        padding-top: 0px;
       border-collapse:separate; 
       border-spacing: -2 -1px;
    }
    .tablaFooter3 {
        margin-top: 19px;
        padding-top: 5px;
    }

    .tablaFooter3 td {
        border: 1px solid #fff;
    }

    .copia {
        margin-top: 85px;
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
                <td width="16%">Señor (es):</td>
                <td width="32%" class="linea" align="left"><span class="text">{{ $fecha }}</span></td>
                <td width="5%">&nbsp;</td>
                <td width="21%">&nbsp;</td>
                <td width="2%" >&nbsp;</td>
                <td width="4%" align="left" ><span class="text">{{ $ruc }}</span></td>
                <td width="4%"></td>
                <td width="3%" align="left"><span class="text"></span></td>
                <td width="13%" ></td>
            </tr>
        </table>
        <table width="100%" class="tablaRazon">
            <tr>
                <td width="10%">Señor (es):</td>
                <td width="45%" align="left"><span class="text">{{ $cliente }}</span></td>
                <td width="11%">&nbsp;</td>
                <td width="9%"><span class="text"></span></td>
                <td width="6%">&nbsp;</td>
                <td width="5%" ><span class="text"></span></td>
                <td width="7%"></td>
                <td width="7%"></td>
            </tr>
           <tr>
                <td width="10%">Dirección:</td>
                <td width="38%" align="left"><span class="text">{{ $direccion }}</span></td>
                <td width="18%">&nbsp;</td>
                <td width="9%">&nbsp;</td>
                <td width="6%" align="left" ><span class="text">{{ $telefono }}</span></td>
                <td width="5%">&nbsp;</td>
                <td width="7%">&nbsp;</td>
                <td width="7%">&nbsp;</td>
            </tr>
            <tr>
                <td width="10%">Numero:</td>
                <td width="38%">&nbsp;</td>
                <td width="18%">&nbsp;</td>
                <td width="9%">&nbsp;</td>
                <td width="6%" align="left"><span class="text">{{ $numero_factura }}</span></td>
                <td width="5%">&nbsp;</td>
                <td width="7%">&nbsp;</td>
                <td width="7%">&nbsp;</td>
            </tr>

        </table>
        <table width="100%" class="tabla3">
            @for ($i = 0; $i < 8; $i++)
            @if (array_key_exists($i, $articulos))
            <tr>
                <td width="9%" align="left"><span class="text">{{ $articulos[$i]['cod_prod']}}</span></td>
                <td width="9%" align="center"><span class="text">{{ $articulos[$i]['cantidad'] }}</span></td>
                <td width="29%" align="left"><span class="text">{{ $articulos[$i]['descripcion'] }}</span></td>
                <td width="12%" align="center"><span class="text">{{ $articulos[$i]['precio'] }}</span></td>
                <td width="13%" align="center"><span class="text">{{ $articulos[$i]['exentas'] }}</span></td>
                <td width="14%" align="center"><span class="text">{{ $articulos[$i]['base5'] }}</span></td>
                <td width="13%" align="center"><span class="text">{{ $articulos[$i]['base10'] }}</span></td>
            </tr>
            @else
            <tr>
                <td width="9%">&nbsp;</td>
                <td width="9%">&nbsp;</td>
                <td width="29%">&nbsp;</td>
                <td width="12%">&nbsp;</td>
                <td width="13%">&nbsp;</td>
                <td width="14%" align="left">&nbsp;</td>
                <td width="13%" align="left">&nbsp;</td>
            </tr>
            @endif
            @endfor
        </table>
        <table width="100%" class="tablaFooter">
            <tr>
                <td width="0%" align="center"></td>
                <td width="80%" align="left"><span class="text">{{ $letra }}</span></td>
                <td width="20%" align="left"><span class="text">{{ $total }}</span></td>
            </tr>
        </table>
        <table width="100%" class="tablaFooter2">
            <tr>
                <td width="20%" align="center"></td>
                <td width="16%" align="left"><span class="text">{{ $base5 }}</span></td>
                <td width="19%" align="left"><span class="text">{{ $base10 }}</span></td>
                <td width="19%" align="left"><span class="text">{{ $iva }}</span></td>
                <td width="23%" align="right"><span class="text"></span></td>
            </tr>
        </table>
    </div>

    <div class="@if($tipo=='fisico') fisico @endif copia">
        <table width="100%" class="tabla4" >
            <tr>
                <td width="16%">Señor (es):</td>
                <td width="32%" class="linea" align="left"><span class="text">{{ $fecha }}</span></td>
                <td width="5%">&nbsp;</td>
                <td width="21%">&nbsp;</td>
                <td width="4%" align="left" ><span class="text">{{ $ruc }}</span></td>
                <td width="4%"></td>
                <td width="3%" align="left"><span class="text"></span></td>
                <td width="7%" ></td>
            </tr>
        </table>
        <table width="100%" class="tablaRazon2">
            <tr>
                <td width="10%">Señor (es):</td>
                <td width="45%" align="left"><span class="text">{{ $cliente }}</span></td>
                <td width="11%">&nbsp;</td>
                <td width="9%"><span class="text"></span></td>
                <td width="6%">&nbsp;</td>
                <td width="5%" ><span class="text"></span></td>
                <td width="7%"></td>
                <td width="7%"></td>
            </tr>
           <tr>
                <td width="10%">Dirección:</td>
                <td width="38%" align="left"><span class="text">{{ $direccion }}</span></td>
                <td width="18%">&nbsp;</td>
                <td width="9%">&nbsp;</td>
                <td width="6%" align="left" ><span class="text">{{ $telefono }}</span></td>
                <td width="5%">&nbsp;</td>
                <td width="7%">&nbsp;</td>
                <td width="7%">&nbsp;</td>
            </tr>
            <tr>
                <td width="10%">Numero:</td>
                <td width="38%">&nbsp;</td>
                <td width="18%">&nbsp;</td>
                <td width="9%">&nbsp;</td>
                <td width="6%" align="left"><span class="text">{{ $numero_factura }}</span></td>
                <td width="5%">&nbsp;</td>
                <td width="7%">&nbsp;</td>
                <td width="7%">&nbsp;</td>
            </tr>
        </table>
        <table width="100%" class="tabla3">
            @for ($i = 0; $i < 8; $i++)
            @if (array_key_exists($i, $articulos))
            <tr>
                <td width="9%" align="left"><span class="text">{{ $articulos[$i]['cod_prod']}}</span></td>
                <td width="9%" align="center"><span class="text">{{ $articulos[$i]['cantidad'] }}</span></td>
                <td width="29%" align="left"><span class="text">{{ $articulos[$i]['descripcion'] }}</span></td>
                <td width="12%" align="center"><span class="text">{{ $articulos[$i]['precio'] }}</span></td>
                <td width="13%" align="center"><span class="text">{{ $articulos[$i]['exentas'] }}</span></td>
                <td width="14%" align="center"><span class="text">{{ $articulos[$i]['base5'] }}</span></td>
                <td width="13%" align="center"><span class="text">{{ $articulos[$i]['base10'] }}</span></td>
            </tr>
            @else
            <tr>
                <td width="9%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
                <td width="29%">&nbsp;</td>
                <td width="12%">&nbsp;</td>
                <td width="13%">&nbsp;</td>
                <td width="15%" align="left">&nbsp;</td>
                <td width="13%" align="left">&nbsp;</td>
            </tr>
            @endif
            @endfor
        </table>
        <table width="100%" class="tablaFooter3">
            <tr>
                <td width="0%" align="center"></td>
                <td width="80%" align="left"><span class="text">{{ $letra }}</span></td>
                <td width="20%" align="left"><span class="text">{{ $total }}</span></td>
            </tr>
        </table>
        <table width="100%" class="tablaFooter2">
            <tr>
                <td width="20%" align="center"></td>
                <td width="16%" align="left"><span class="text">{{ $base5 }}</span></td>
                <td width="19%" align="left"><span class="text">{{ $base10 }}</span></td>
                <td width="19%" align="left"><span class="text">{{ $iva }}</span></td>
                <td width="23%" align="right"><span class="text"></span></td>
            </tr>
        </table>
    </div>

    <div class="@if($tipo=='fisico') fisico @endif copia">
        <table width="100%" class="tabla5" >
            <tr>
                <td width="16%">Señor (es):</td>
                <td width="32%" class="linea" align="left"><span class="text">{{ $fecha }}</span></td>
                <td width="5%">&nbsp;</td>
                <td width="21%">&nbsp;</td>
                <td width="4%" align="left" ><span class="text">{{ $ruc }}</span></td>
                <td width="4%"></td>
                <td width="3%" align="left"><span class="text"></span></td>
                <td width="7%" ></td>
            </tr>
        </table>
        <table width="100%" class="tablaRazon2">
            <tr>
                <td width="10%">Señor (es):</td>
                <td width="45%" align="left"><span class="text">{{ $cliente }}</span></td>
                <td width="11%">&nbsp;</td>
                <td width="9%"><span class="text"></span></td>
                <td width="6%">&nbsp;</td>
                <td width="5%" ><span class="text"></span></td>
                <td width="7%"></td>
                <td width="7%"></td>
            </tr>
           <tr>
                <td width="10%">Dirección:</td>
                <td width="38%" align="left"><span class="text">{{ $direccion }}</span></td>
                <td width="18%">&nbsp;</td>
                <td width="9%">&nbsp;</td>
                <td width="6%" align="left" ><span class="text">{{ $telefono }}</span></td>
                <td width="5%">&nbsp;</td>
                <td width="7%">&nbsp;</td>
                <td width="7%">&nbsp;</td>
            </tr>
            <tr>
                <td width="10%">Numero:</td>
                <td width="38%">&nbsp;</td>
                <td width="18%">&nbsp;</td>
                <td width="9%">&nbsp;</td>
                <td width="6%" align="left"><span class="text">{{ $numero_factura }}</span></td>
                <td width="5%">&nbsp;</td>
                <td width="7%">&nbsp;</td>
                <td width="7%">&nbsp;</td>
            </tr>
        </table>
        <table width="100%" class="tabla3">
            @for ($i = 0; $i < 8; $i++)
            @if (array_key_exists($i, $articulos))
            <tr>
                <td width="9%" align="left"><span class="text">{{ $articulos[$i]['cod_prod']}}</span></td>
                <td width="9%" align="center"><span class="text">{{ $articulos[$i]['cantidad'] }}</span></td>
                <td width="29%" align="left"><span class="text">{{ $articulos[$i]['descripcion'] }}</span></td>
                <td width="12%" align="center"><span class="text">{{ $articulos[$i]['precio'] }}</span></td>
                <td width="13%" align="center"><span class="text">{{ $articulos[$i]['exentas'] }}</span></td>
                <td width="14%" align="center"><span class="text">{{ $articulos[$i]['base5'] }}</span></td>
                <td width="13%" align="center"><span class="text">{{ $articulos[$i]['base10'] }}</span></td>
            </tr>
            @else
            <tr>
                <td width="9%">&nbsp;</td>
                <td width="9%">&nbsp;</td>
                <td width="29%">&nbsp;</td>
                <td width="12%">&nbsp;</td>
                <td width="13%">&nbsp;</td>
                <td width="14%" align="left">&nbsp;</td>
                <td width="13%" align="left">&nbsp;</td>
            </tr>
            @endif
            @endfor
        </table>
        <table width="100%" class="tablaFooter3">
            <tr>
                <td width="0%" align="center"></td>
                <td width="80%" align="left"><span class="text">{{ $letra }}</span></td>
                <td width="20%" align="left"><span class="text">{{ $total }}</span></td>
            </tr>
        </table>
        <table width="100%" class="tablaFooter2">
            <tr>
                <td width="20%" align="center"></td>
                <td width="16%" align="left"><span class="text">{{ $base5 }}</span></td>
                <td width="19%" align="left"><span class="text">{{ $base10 }}</span></td>
                <td width="19%" align="left"><span class="text">{{ $iva }}</span></td>
                <td width="23%" align="right"><span class="text"></span></td>
            </tr>
        </table>
    </div>
</body>
</html>