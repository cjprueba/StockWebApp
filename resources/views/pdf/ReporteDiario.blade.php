<!DOCTYPE html>
<html>
    <style type="text/css">
    
    body {
        font-family: "Arial";
        font-size: 11px;
        color: grey;
    }

    .firma{
        font-family: "Arial";
        font-size: 10pt;
        color: #c0c0c0;
        border-top: 1px solid #c0c0c0;
        padding: 5px;
    }

    table, th, td { 
        padding: 5px;
        border-collapse:collapse;
    }
    .border{
        border:1px solid #dbdbdb;
        padding: 1px; 
        -webkit-border-radius: 10px;
        -moz-border-radius: 10px;
        border-radius: 10px;
    }

    .borde-fin{
        border-left: 1px solid #dbdbdb; 
        border-top: 1px solid #dbdbdb;
    }

    .borde-der{
        border-right: 1px solid #dbdbdb;
    }
    .borde-der-top{
        border-right: 1px solid #dbdbdb;
        border-top: 1px solid #dbdbdb;
    }
    .borde-iz-top{
        border-left: 1px solid #dbdbdb;
        border-top: 1px solid #dbdbdb;
    }
    .borde-iz{
        border-left: 1px solid #dbdbdb;
    }

    .borde-inicio{
        border-right: 1px solid #dbdbdb;
        border-bottom: 1px solid #dbdbdb;
    }
    .titulo{
        font-family: "Arial";
        font-size: 14pt; 
        text-align: center;
        color: #c0c0c0;
    }

    .borde-iz-bot{
        border-bottom: 1px solid #dbdbdb;
        border-left: 1px solid #dbdbdb;
    }

</style>

<body>
    @if($sucursal == '9')
        <div align="center"><img src="C:\inetpub\wwwroot\Master\public\storage\imagenes\tiendas\Toku-logo2.3.png" width="350px">
        </div>
    @else
        <div class="titulo"><strong>REPORTE DIARIO</strong></div>
    @endif

    <br><br>
    <div align="center">FECHA: {{$fecha}}</div><br>
    <!-- TABLA -->

    <div width="30%" align="center">EFECTIVO</div>

    <div class="border">
    <table class="items" width="100%">
        <thead>
            <tr>
                <td width="20%" class="borde-inicio" align="center"><b>CAJAS</b></td>
                <td width="20%" class="borde-inicio" align="center"><b>DOLARES</b></td>
                <td width="20%" class="borde-inicio" align="center"><b>GUARANIES</b></td>
                <td width="20%" class="borde-inicio" align="center"><b>REALES</b></td>
                <td width="20%" class="borde-iz-bot" align="center"><b>PESOS</b></td>
            </tr>
        </thead>
        <tbody>
           @for ($i = 0; $i < $c_rows; $i++)
                @if($i <= $c_rows-1)
                <tr>
                    <td width="20%" class="borde-der-top" align="center"><span>{{ $efectivo[$i]['CAJA'] }}</span></td>
                    <td width="20%" class="borde-der-top" align="center"><span>{{ $efectivo[$i]['DOLARES'] }}</span></td>
                    <td width="20%" class="borde-der-top" align="center"><span>{{ $efectivo[$i]['GUARANIES']}}</span></td>
                    <td width="20%" class="borde-der-top" align="center"><span>{{ $efectivo[$i]['REALES'] }}</span></td>
                    <td width="20%" class="borde-der-top" align="center"><span>{{ $efectivo[$i]['PESOS'] }}</span></td>
                </tr>
                @endif
            @endfor 
           <tr>
                    <td width="20%" class="borde-der-top" align="right"><b>TOTALES</b></td>
                    <td width="20%" class="borde-der-top" align="center"><b>{{$totalDles}}</b></td>
                    <td width="20%" class="borde-der-top" align="center"><b>{{$totalGes}}</b></td>
                    <td width="20%" class="borde-der-top" align="center"><b>{{$totalRles}}</b></td>
                    <td width="20%" class="borde-iz-top" align="center"><b>{{$totalPes}}</b></td>
                </tr>
        </tbody>
    </table>
    </div>
    <br>
    <div width="30%" align="center">MEDIOS</div>
    
    <div class="border">
    <table class="items" width="100%">
        <thead>
            <tr>
                <td width="20%" class="borde-inicio" align="center"><b>CAJAS</b></td>
                <td width="20%" class="borde-inicio" align="center"><b>TARJETAS</b></td>
                <td width="20%" class="borde-inicio" align="center"><b>VALES</b></td>
                <td width="20%" class="borde-inicio" align="center"><b>TRANSFERENCIAS</b></td>
                <td width="20%" class="borde-iz-bot" align="center"><b>GIROS</b></td>
            </tr>
        </thead>
        <tbody>
           @for ($i = 0; $i < $c_rows; $i++)
                @if($i <= $c_rows-1)
                <tr>
                    <td width="20%" class="borde-der-top" align="center"><span>{{ $medios[$i]['CAJAS'] }}</span></td>
                    <td width="20%" class="borde-der-top" align="center"><span>{{ $medios[$i]['TARJETAS'] }}</span></td>
                    <td width="20%" class="borde-der-top" align="center"><span>{{ $medios[$i]['VALES']}}</span></td>
                    <td width="20%" class="borde-der-top" align="center"><span>{{ $medios[$i]['TRANSFERENCIAS'] }}</span></td>
                    <td width="20%" class="borde-der-top" align="center"><span>{{ $medios[$i]['GIROS'] }}</span></td>
                </tr>
                @endif
            @endfor 
           <tr>
                    <td width="20%" class="borde-der-top" align="right"><b>TOTALES</b></td>
                    <td width="20%" class="borde-der-top" align="center"><b>{{$totalTjs}}</b></td>
                    <td width="20%" class="borde-der-top" align="center"><b>{{$totalVls}}</b></td>
                    <td width="20%" class="borde-der-top" align="center"><b>{{$totalTrs}}</b></td>
                    <td width="20%" class="borde-iz-top" align="center"><b>{{$totalGrs}}</b></td>
                </tr>
        </tbody>
    </table>
    </div>
    <br>

    <div width="30%" align="center">CHEQUES</div>
    <div class="border">
    <table class="items" width="100%">
        <thead>
            <tr>
                <td width="20%" class="borde-inicio" align="center"><b>CAJAS</b></td>
                <td width="20%" class="borde-inicio" align="center"><b>DOLARES</b></td>
                <td width="20%" class="borde-inicio" align="center"><b>GUARANIES</b></td>
                <td width="20%" class="borde-inicio" align="center"><b>REALES</b></td>
                <td width="20%" class="borde-iz-bot" align="center"><b>PESOS</b></td>
            </tr>
        </thead>
        <tbody>
           @for ($i = 0; $i < $c_rows; $i++)
                @if($i <= $c_rows-1)
                <tr>
                    <td width="20%" class="borde-iz-top" align="center"><span>{{ $cheques[$i]['CAJA'] }}</span></td>
                    <td width="20%" class="borde-iz-top" align="center"><span>{{ $cheques[$i]['DOLARES'] }}</span></td>
                    <td width="20%" class="borde-iz-top" align="center"><span>{{ $cheques[$i]['GUARANIES']}}</span></td>
                    <td width="20%" class="borde-iz-top" align="center"><span>{{ $cheques[$i]['REALES'] }}</span></td>
                    <td width="20%" class="borde-iz-top" align="center"><span>{{ $cheques[$i]['PESOS'] }}</span></td>
                </tr>
                @endif
            @endfor
           <tr>
                    <td width="20%" class="borde-der-top" align="right"><b>TOTALES</b></td>
                    <td width="20%" class="borde-der-top" align="center"><b>{{$totalDls}}</b></td>
                    <td width="20%" class="borde-der-top" align="center"><b>{{$totalGs}}</b></td>
                    <td width="20%" class="borde-der-top" align="center"><b>{{$totalRls}}</b></td>
                    <td width="20%" class="borde-iz-top" align="center"><b>{{$totalPs}}</b></td>
                </tr>
        </tbody>
    </table>
    </div>
    <br>

    <div width="30%" align="center">VENTAS</div>
    <div class="border">
    <table width="100%">
      <tr>
        <th width="25%" align="center" class="borde-inicio"><b>CONTADO</b></th>
        <th width="25%" align="center" class="borde-inicio"><b>CRÉDITO</b></th>
        <th width="25%" align="center" class="borde-inicio"><b>PAGO AL ENTREGAR</b></th>
        <th width="25%" align="center" class="borde-iz-bot"><b>ANULADO</b></th>
      </tr>
      <tr>
        <td width="25%" align="center" class="borde-der"><b>{{$contado}}</b></td>
        <td width="25%" align="center" class="borde-der"><b>{{$creditoV}}</b></td>
        <td width="25%" align="center" class="borde-der"><b>{{$pago}}</b></td>
        <td width="25%" align="center" class="borde-fin"><b>{{$anulado}}</b></td>
      </tr>
    </table>
    </div>
    <br><br>

    <div class="border">
    <table width="100%">
      <tr>
        <th width="50%" align="left" class="borde-inicio"><b>OTROS</b></th>
        <th width="50%" align="right"><b>MONTO</b></th>
      </tr>
      <tr>
        <td width="50%" class="borde-inicio" align="left">DESCUENTO POR PRODUCTO</td>
        <td width="50%" class="borde-fin" align="right">{{$descuentoP}}</td>
      </tr>
      <tr>
          <td width="50%" class="borde-inicio" align="left">RETENCIÓN 30%</td>
          <td width="50%" class="borde-fin" align="right">{{$retencion}}</td>
      </tr>
      <tr>
          <td width="50%" class="borde-inicio" align="left">NOTA DE CRÉDITO</td>
          <td width="50%" class="borde-fin" align="right">{{$credito}}</td>
      </tr>
      <tr>
          <td width="50%" class="borde-inicio" align="left">DESCUENTO GENERAL</td>
          <td width="50%" class="borde-fin" align="right">{{$descuentoG}}</td>
      </tr>
      <tr>
          <td width="50%" class="borde-inicio" align="left">SALIDA DE PRODUCTO</td>
          <td width="50%" class="borde-fin" align="right">{{$salida}}</td>
      </tr>
      <tr>
          <td width="50%" class="borde-inicio" align="left">CUPÓN</td>
          <td width="50%" class="borde-fin" align="right">{{$cupon}}</td>
      </tr>
      <tr>
          <td width="50%" align="left">ABONOS</td>
          <td width="50%" align="right" class="borde-fin">{{$abono}}</td>
      </tr>
    </table>
    </div>
    <br>

    <table width="100%">
        <tr>
            <th width="50%" align="left"><b>TOTAL GENERAL</b></th>
            <th width="50%" align="right"><b>{{$total}}</b></th>
        </tr>

        <tr>
            <th width="50%" align="left"><b>TOTAL VENTAL</b></th>
            <th width="50%" align="right"><b>{{$totalV}}</b></th>
        </tr>
    </table>
  
    <br><br><br>
    <div width="35%" align="center" class="firma"><b>FIRMA RESPONSABLE</b></div>
    
</body>
</html>