<!DOCTYPE html>
<html>
<head>
    <title>INVOICE EXPORTACION</title>
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
    .tabla2 {
        padding-top: 100px;
        margin-bottom: 0px;
        padding-bottom: 0px;
    }
    .tabla3{
        border: 1px solid #000;
        margin-top: 69px;
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
        margin-top: -6px;
        padding-top: 0px;
       border-collapse:separate; 
       border-spacing: 10px;
    }

    .tablaFooter {
        margin-top: 6px;
    }

    .tablaFooter td {
        border: 1px solid #fff;
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
                <td width="11%"></td>
                <td width="89%" class="linea" align="left"><span class="text">{{ $fecha }}</span></td>
            </tr>
        </table>
        <table width="100%" class="tablaRazon">
            <tr>
                <td width="16%"></td>
                <td width="50%" align="left"><span class="text">{{ $senores }}</span></td>
                <td width="15%">&nbsp;</td>
                <td width="19%"><span class="text">{{ $pais }}</span></td>
            </tr>
            <tr>
                <td width="16%"></td>
                <td width="50%" align="left"><span class="text">{{ $direccion }}</span></td>
                <td width="15%">&nbsp;</td>
                <td width="19%"><span class="text">{{ $ciudad }}</span></td>
            </tr>
        </table>
        <table width="100%" style="margin-top: 2px;">
            <tr>
                <td width="18%"></td>
                <td width="21%" align="left"><span class="text">{{ $telefono }}</span></td>
                <td width="32%"></td>
                <td width="29%" align="left"><span class="text">{{ $condiciones_p }}</span></td>
            </tr>
        </table>
        <table width="100%" class="tabla3">
            @for ($i = 0; $i < 39; $i++)
            @if (array_key_exists($i, $articulos))
            <tr>
                <td width="6%" align="left"><span class="text">{{ $articulos[$i]['item']}}</span></td>
                <td width="15%" align="center"><span class="text">{{ $articulos[$i]['cantidad'] }}</span></td>
                <td width="53%" align="left"><span class="text">{{ $articulos[$i]['descripcion'] }}</span></td>
                <td width="13%" align="center"><span class="text">{{ $articulos[$i]['precio'] }}</span></td>
                <td width="13%" align="right"><span class="text">{{ $articulos[$i]['total'] }}</span></td>

            </tr>
            @else
            <tr>
                <td width="6%" align="left">&nbsp;</td>
                <td width="15%"  align="center">&nbsp;</td>
                <td width="53%" align="left">&nbsp;</td>
                <td width="13%" align="center">&nbsp;</td>
                <td width="13%" align="right">&nbsp;</td>
            </tr>
            @endif
            @endfor
        </table>
 
<br>
        <table width="100%" class="tablaFooter">
            <tr>
                <td width="14%" align="center"></td>
                <td width="68%" align="left"><span class="text">{{ $letra }}</span></td>
                <td width="18%" align="center"><span class="text">{{ $total }}</span></td>
            </tr>
        </table>
    </div>
</body>
</html>