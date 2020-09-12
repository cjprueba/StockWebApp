<!DOCTYPE html>
<html>
    <!-- <title>BOLETA DE VENTA</title> -->
    <style type="text/css">
    
    table{
        border-collapse: collapse;
        font-size: 12px;
        /*border-bottom: 1px solid #757575;*/
    }
    
    body {
        font-family: "Arial";
        font-size: 12px;
        text-align: justify;
    }

    td { 
        vertical-align: center; 

    }
    span{
        font-size: 10pt;
    }

    .title{
        text-align: center;
        font-size: 12pt; 
        border-bottom: 0.2mm solid #000000;
        border-top: 0.2mm solid #000000;
    }

    .cuerpo{
        text-align: center;
        font-size: 10pt;
        border-bottom: 1px solid #dbdbdb;
    }
    .totals {
        font-size: 12pt;
        text-align: right;
        border-top: 0.2mm solid #000000;
        border-bottom: 0.2mm solid #000000;
    }
    .cost {
        text-align: center;
    }
    .items{
        font-size: 11pt;
    }

    .texto{
        font-size: 11pt;
    }

    .titulo{
        font-size: 14pt; 
        text-align: center;
    }

    .pagina{
        border-top: 1px solid #000000; 
        font-size: 9pt; 
        text-align: center; 
        padding-top: 3mm; 
    }

    .cuerpo2{
        text-align: center;
        font-size: 10pt;
        border-top: 1px solid #dbdbdb;
    }
</style>
<body>
    <htmlpagefooter name="myfooter">
        <div class="pagina">
            Página {PAGENO} de {nb}
        </div>
    </htmlpagefooter>

    <sethtmlpagefooter name="myfooter" value="on" />

    <div class="titulo"><strong>REPORTE VENTAS POR TARJETA</strong></div>
    
    <br><div class="texto"><span>Fecha: {{$intervalo}}</span></div><br>
    <div class="texto"><span>Generado Por: {{$generador}}</span></div><br>
    <div class="texto"><span>Fecha Generada: {{$fecha}}</span></div><br>
  
    <table class="items" width="100%" cellpadding="6">
        <thead>
            <tr>
                <td width="15%" class="title"><b>Ref.</b></td>
                <td width="40%" class="title"><b>Nombre</b></td>
                <td width="20%" class="title"><b>Tarjeta</b></td>
                <td width="15%" class="title"><b>Fecha</b></td>
                <td width="20%" class="title"><b>Total</b></td>
            </tr>
        </thead>
        <tbody>
            <!-- ITEMS HERE -->
            @for ($i = 0; $i < $c_rows; $i++)
                @if($i <= $c_rows-2)
                <tr>
                    <td width="15%" class="cuerpo"><span>{{$i + 1}} </span></td>
                    <td width="40%" align="left" class="cuerpo"><span>{{ $articulos[$i]['NOMBRE'] }}</span></td>
                    <td width="20%" class="cuerpo"><span>{{ $articulos[$i]['TARJETA']}}</span></td>
                    <td width="15%" class="cuerpo"><span>{{ $articulos[$i]['FECHA']}}</span></td>
                    <td width="20%" class="cuerpo"><span>{{ $articulos[$i]['TOTAL'] }}</span></td>
                </tr>
                @else
                <tr>
                    <td width="15%" class="cuerpo2"><span>{{$i + 1}} </span></td>
                    <td width="40%" class="cuerpo2" align="left"><span>{{ $articulos[$i]['NOMBRE'] }}</span></td>
                    <td width="20%" class="cuerpo2"><span>{{ $articulos[$i]['TARJETA']}}</span></td>
                    <td width="15%" class="cuerpo2"><span>{{ $articulos[$i]['FECHA']}}</span></td>
                    <td width="20%" class="cuerpo2"><span>{{ $articulos[$i]['TOTAL'] }}</span></td>
                </tr>
                @endif
                @if($articulos[$i]['SALTO'] == true)
                <tr>
                    <td width="15%">&nbsp;</td>
                    <td width="40%">&nbsp;</td>
                    <td width="20%">&nbsp;</td>
                    <td width="15%">&nbsp;</td>
                    <td width="20%">&nbsp;</td>
                </tr>
                @endif
            @endfor
            <!-- END ITEMS HERE -->
                <tr>
                    <td colspan='4' class="totals"><b><b>TOTAL:</b></td>
                    <td class="totals" align="center"><b>{{$total}}</b></td>
                </tr>
        </tbody>
    </table>
</body>
</html>