<!DOCTYPE html>
<html>
<head>
    <!-- <title>BOLETA DE VENTA</title> -->
    <style type="text/css">
    
    table{
        border-collapse: collapse;
        font-family: "Malgun Gothic";
        font-size: 12px;
    }
    
    body {
        font-size: 12px;
        text-align: justify;
    }

    p { 
        margin: 0pt; 
    }

    td { 
        vertical-align: center; 

    }
    span{
        font-size: 10pt;
    }
    table thead td {
        text-align: center;
        font-size: 10;
        /*font-variant: small-caps;*/
    }
    .items td.subs {
        text-align: right;
        border-top: 0.1mm solid #dbdbdb;
    }
    .items td.totals {
        text-align: right;
        border-top: 0.2mm solid #000000;
        border-bottom: 0.2mm solid #000000;
    }
    .items td.cost {
        text-align: right;
    }

</style>
</head>
<body>
   <htmlpageheader name="myheader">
        <table width="100%"><tr>
            <td width="50%" align="center">
                <img src="C:\laragon\www\StockWebApp\resources\imagenes\LOGO.png" width="200px">
                <br /><br />
            </td>
        </tr></table>
    </htmlpageheader>
    <htmlpagefooter name="myfooter">
        <div style="border-top: 1px solid #000000; font-size: 9pt; text-align: center; padding-top: 3mm; ">
            Página {PAGENO} de {nb}
        </div>
    </htmlpagefooter>

    <sethtmlpagefooter name="myfooter" value="on" />
    <sethtmlpageheader name="myheader" value="on" show-this-page="1" />

    <table width="100%"><tr>
        <td width="68%" align="left">
            <span style="font-size: 14pt; color: #000000;"><b>FACTURA:</b></span>
            <br /><br /><span class="text">{{ $cliente }}</span>
            <br /> <span class="text">{{ $ciudad }}</span>
            <br /> <span class="text">{{ $codigoPostal }}</span>
            <br /><span class="text">{{ $direccion }}</span>
            <br /><span class="text">{{ $direccion_2 }}</span>
            <br /> <span class="text">{{ $telefono }}</span>
            <br /><span class="text">{{ $email }}</span>
        </td>
        <td width="32%" align="left">
            <br /><span class="text">Fecha de Factura: {{$dia}}</span>
            <br /><span class="text">Número de Factura: {{$factura}}</span>
            <br /><span class="text">Número de Orden: {{$codigo}}</span>
            <br /><span class="text">R.U.C/C.I: {{$ruc}}</span>
            <br /><span class="text">Fecha de Orden: {{ $fecha}}</span>
            <br /><span class="text">Método de Pago: {{$metodo}}.</span>
        </td>
    </tr></table>
    <br />
    <table class="items" width="100%" style="border-bottom: 0.2px solid #dbdbdb; font-size: 10pt;" cellpadding="3">
        <thead>
            <tr>
                <td width="50%" aling="left" style="font-size: 11pt; background-color: #000000; color: #ffffff"><b>Productos</b></td>
                <td width="10%" style="font-size: 11pt; background-color: #000000; color: #ffffff"><b>Cantidad</b></td>
                <td width="20%" style="font-size: 11pt; background-color: #000000; color: #ffffff"><b>Precio</b></td>
                <td width="20%" style="font-size: 11pt; background-color: #000000; color: #ffffff"><b>Total</b></td>
            </tr>
        </thead>
        <tbody>

        <!-- ITEMS HERE -->

        @for ($i = 0; $i < 10; $i++)
            @if (array_key_exists($i, $articulos))
            <tr>
                <td width="50%" align="left">
                    <span class="text">{{ $articulos[$i]['descripcion'] }} </span>
                    <span class="text" style="font-size: 9pt; color: #414141"><br/>{{ $articulos[$i]['cod_prod']}}<br/>Peso: {{ $articulos[$i]['peso']}}</span>
                </td>
                <td width="15%" align="center"><span class="text">{{ $articulos[$i]['cantidad'] }}</span></td>
                <td width="15%" align="center"><span class="text">{{ $articulos[$i]['precio'] }}</span></td>
                <td width="20%" align="right"><span class="text">{{ $articulos[$i]['total'] }}</span></td>
            </tr>
             
            @endif

        @endfor

        <!-- END ITEMS HERE -->

                <tr>
                    <td class="subs" colspan="3" rowspan="4"><br/></td>
                    <td class="subs">Subtotal: {{$subtotal}}</td>
                </tr>
                <tr>
                    <td class="subs">Envío: {{$envio}}</td>
                </tr>
                <tr>
                    <td class="totals"><b>TOTAL:  {{$total}}</b></td>
                    <td class="totals cost"><b></b></td>
                </tr>
                
        
        </tbody>
    </table>
</body>
</html>