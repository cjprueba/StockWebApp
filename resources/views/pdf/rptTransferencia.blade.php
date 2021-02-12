<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>{{ $nombre }}</title>
    <style type="text/css">
      .clearfix:after {
        content: "";
        display: table;
        clear: both;
      }

      a {
        color: #5D6975;
        text-decoration: underline;
      }

      body {
        position: relative;
        width: 21cm;  
        height: 29.7cm; 
        margin: 0 auto; 
        color: #001028;
        background: #FFFFFF; 
        font-family: Arial, sans-serif; 
        font-size: 12px; 
        font-family: Arial;
      }

      header {
        padding: 10px 0;
        margin-bottom: 30px;
      }

      #logo {
        text-align: center;
        margin-bottom: 10px;
      }

      #logo img {
        width: 90px;
      }

      h1 {
        border-top: 1px solid  #5D6975;
        border-bottom: 1px solid  #5D6975;
        color: #5D6975;
        font-size: 2.4em;
        line-height: 1.4em;
        font-weight: normal;
        text-align: center;
        margin: 0 0 20px 0;
        background: url(./../storage/app/public/imagenes/dimension.png);
      }

      #project {
        float: left;
      }

      #project span {
        color: #5D6975;
        text-align: right;
        width: 52px;
        margin-right: 10px;
        display: inline-block;
        font-size: 0.8em;
      }

      #company {
        float: right;
        text-align: right;
      }

      #project div,
      #company div {
        white-space: nowrap;        
      }

      table {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        margin-bottom: 20px;
      }

      table tr:nth-child(2n-1) td {
        background: #F5F5F5;
      }

      table th,
      table td {
        text-align: center;
      }

      table th {
        padding: 5px 20px;
        color: #5D6975;
        border-bottom: 1px solid #C1CED9;
        white-space: nowrap;        
        font-weight: normal;
      }

      table .service,
      table .desc {
        text-align: left;
      }

      table td {
        padding: 5px;
        text-align: right;
      }

      table td.service,
      table td.desc {
        vertical-align: top;
      }

      table td.unit,
      table td.qty,
      table td.total {
        font-size: 1.2em;
      }

      table td.grand {
        border-top: 1px solid #5D6975;;
      }

      #notices .notice {
        color: #5D6975;
        font-size: 1.2em;
      }

      footer {
        color: #5D6975;
        width: 100%;
        height: 30px;
        position: absolute;
        bottom: 0;
        border-top: 1px solid #C1CED9;
        padding: 8px 0;
        text-align: center;
      }
    </style>
  </head>
  <body>
    <header class="clearfix">
      <div id="logo">
         <span v-html= "{{ $logo }}"></span>
        
      </div>
      <h1>TRANSFERENCIA {{ $codigo }}</h1>
      <div id="company" class="clearfix">
        <div>{{ $sucursal }}</div>
        <div>{{ $direccion }}</div>
      </div>
      <div id="project">
        <div><span>ORIGEN</span> {{ $origen }}</div>
        <div><span>DESTINO</span> {{ $destino }}</div>
        <div><span>ENVIA</span> {{ $envia }}</div>
        <div><span>TRANSPORTA</span> {{ $transporta }}</div>
        <div><span>RECIBE</span> {{ $recibe }}</div>
        <div><span>FECHA</span> {{ $fecha }}</div>
      </div>
    </header>
    <main>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>CODIGO</th>
            <th class="desc">DESCRIPCION</th>
            <th>PRECIO</th>
            <th>CANT.</th>
            <th>TOTAL</th>
          </tr>
        </thead>
        <tbody>
          @for ($i = 0; $i < $c; $i++)
            <tr>
                <td class="service"><span class="text">{{ $i + 1 }}</span></td>
                <td class="service"><span class="text">{{ $articulos[$i]['cod_prod'] }}</span></td>
                <td class="desc"><span class="text">{{ $articulos[$i]['descripcion'] }}</span></td>
                <td class="total"><span class="text">{{ $articulos[$i]['precio'] }}</span></td>
                <td class="qty"><span class="text">{{ $articulos[$i]['cantidad'] }}</span></td>
                <td class="total"><span class="text">{{ $articulos[$i]['total'] }}</span></td>
            </tr>
          @endfor
          <tr>
            <td colspan="4" class="grand total">TOTALES</td>
            <td class="grand total">{{ $cantidad }}</td>
            <td class="grand total">{{ $total }}</td>
          </tr>
        </tbody>
      </table>
      <!-- <div id="notices">
        <div>NOTICE:</div>
        <div class="notice">A finance charge of 1.5% will be made on unpaid balances after 30 days.</div>
      </div> -->
    </main>
    <footer>
      La nota es creada para solo uso interno.
    </footer>
  </body>
</html>