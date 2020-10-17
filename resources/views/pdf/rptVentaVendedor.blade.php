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
        font-size: 11pt; 
        border-bottom: 0.2mm solid #000000;
        border-top: 0.2mm solid #000000;
    }

    .cuerpo{
        text-align: center;
        font-size: 10pt;
        border-bottom: 1px solid #dbdbdb;
    }
    .totals {

        font-size: 11pt;
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

    <div class="titulo"><strong>REPORTE VENTAS POR VENDEDOR</strong></div>
    
    <br><div class="texto"><span>Fecha: {{$intervalo}}</span></div><br>
    <div class="texto"><span>Generado Por: {{$generador}}</span></div><br>
    <div class="texto"><span>Fecha Generada: {{$fecha}}</span></div><br>

  <!-- TABLA -->
    <table class="items" width="100%" cellpadding="6">
        <thead>
            <tr>
                <td width="6%" class="title"><b>Ref</b></td>
                <td width="10%" class="title"><b>Codigo</b></td>
                <td width="30%" class="title"><b>Cliente</b></td>
                <td width="12%" class="title"><b>Fecha</b></td>
                <td width="7%" class="title"><b>Tipo</b></td>
                <td width="20%" class="title"><b>Vendedor</b></td>
                <td width="11%" class="title"><b>IVA</b></td>
                <td width="12%" class="title"><b>SubTotal</b></td>
                <td width="15%" class="title"><b>Total</b></td>
                @if($tipo === 'CR')
                <td width="15%" class="title"><b>Pago</b></td>
                @endif

            </tr>
        </thead>
        <tbody>
            <!-- ITEMS HERE -->
            @for ($i = 0; $i < $c_rows; $i++)
                @if($i <= $c_rows-2)
                <tr>
                    <td width="6%" class="cuerpo"><span>{{$i + 1}} </span></td>
                    <td width="9%" class="cuerpo"><span>{{ $articulos[$i]['CODIGO'] }}</span></td>
                    <td width="30%" align="left" class="cuerpo"><span>{{ $articulos[$i]['NOMBRE']}}</span></td>

                    <td width="12%" class="cuerpo"><span>{{ $articulos[$i]['FECHA'] }}</span></td>
                    <td width="7%" class="cuerpo"><span>{{ $articulos[$i]['TIPO'] }}</span></td>
                    <td width="20%" class="cuerpo"><span>{{ $articulos[$i]['VENDEDOR']}}</span></td>
                    <td width="11%" class="cuerpo"><span>{{ $articulos[$i]['IVA'] }}</span></td>
                    <td width="12%" class="cuerpo"><span>{{ $articulos[$i]['SUBTOTAL']}}</span></td>
                    <td width="15%" class="cuerpo"><span>{{ $articulos[$i]['TOTAL'] }}</span></td>
                    @if($tipo === 'CR')
                    <td width="15%" class="cuerpo"><span>{{ $articulos[$i]['SALDO'] }}</span></td>
                    @endif
                </tr>
                @else
                <tr>
                    <td width="6%" class="cuerpo2"><span>{{$i + 1}} </span></td>
                    <td width="9%" class="cuerpo2"><span>{{ $articulos[$i]['CODIGO'] }}</span></td>
                    <td width="30%" align="left" class="cuerpo2"><span>{{ $articulos[$i]['NOMBRE']}}</span></td>
                    <td width="12%" class="cuerpo2"><span>{{ $articulos[$i]['FECHA'] }}</span></td>
                    <td width="7%" class="cuerpo2"><span>{{ $articulos[$i]['TIPO'] }}</span></td>
                    <td width="20%" class="cuerpo2"><span>{{ $articulos[$i]['VENDEDOR']}}</span></td>
                    <td width="11%" class="cuerpo2"><span>{{ $articulos[$i]['IVA'] }}</span></td>
                    <td width="12%" class="cuerpo2"><span>{{ $articulos[$i]['SUBTOTAL']}}</span></td>
                    <td width="15%" class="cuerpo2"><span>{{ $articulos[$i]['TOTAL'] }}</span></td>
                    @if($tipo === 'CR')
                    <td width="15%" class="cuerpo2"><span>{{ $articulos[$i]['SALDO'] }}</span></td>
                    @endif
                </tr>
                @endif
                @if($articulos[$i]['SALTO'] == true)
                <tr>
                    <td width="6%">&nbsp;</td>
                    <td width="9%">&nbsp;</td>
                    <td width="30%">&nbsp;</td>
                    <td width="12%">&nbsp;</td>
                    <td width="7%">&nbsp;</td>
                    <td width="20%">&nbsp;</td>
                    <td width="11%">&nbsp;</td>
                    <td width="12%">&nbsp;</td>
                    <td width="15%">&nbsp;</td>
                    @if($tipo === 'CR')
                    <td width="15%">&nbsp;</td>
                    @endif
                </tr>
                @endif
            @endfor
            <!-- END ITEMS HERE -->
                <tr>
                    <td class="totals"></td>
                    <td class="totals"></td>
                    <td class="totals"></td>
                    <td colspan='3' class="totals"><b><b>TOTALES:</b></td>
                    <td class="totals" align="center"><b>{{$iva}}</b></td>
                    <td class="totals" align="center"><b>{{$subtotal}}</b></td>
                    <td class="totals" align="center"><b>{{$total}}</b></td>
                    @if($tipo === 'CR')
                    <td class="totals"></td>
                    @endif
                </tr>
        </tbody>
    </table>
</body>
</html>