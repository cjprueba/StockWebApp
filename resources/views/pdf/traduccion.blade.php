<!DOCTYPE html>
<html>
	@if ($TAMAÑO == 9)
	<style type="text/css">
		p{
            width: 100px;
            margin: 0px 0;
            padding: 0px;
            font: normal 8px arial, helvetica, sans-serif;
        }
        .normal  {
            white-space: normal;width: 	

        p#tamaño  {
            font: normal 12px arial, helvetica, sans-serif;
        }
	</style>
	<body>
		@if ($VALOR_NUTRCIONAL_CHECK == true && $PROPIEDADES_CHECK == true)
		<p class="col-2">
            <p align="center"><font size="9">{{$MARCA}}</font></p>
            <p class="normal">{{$NOMBRE_DEL_PRODUCTO}}</p>
            <p class="normal">{{$PROPIEDADES}}</p>
            <p class="normal">FORMA DE USO: {{$FORMA_DE_USO}}</p>
            <p class="normal">INGREDIENTES: {{$INGREDIENTES}}</p>
            <p class="normal">VALOR NUTRICIONAL: {{$VALOR_NUTRICIONAL}}</p>
            <p class="normal">CONTENIDO: {{$CONTENIDO}}</p>
        </p>
	    @endif
	    @if ($VALOR_NUTRCIONAL_CHECK == false && $PROPIEDADES_CHECK == true)
		<p class="col-2">
            <p align="center"><font size="9">{{$MARCA}}</font></p>
            <p class="normal">{{$NOMBRE_DEL_PRODUCTO}}</p>
            <p class="normal">{{$PROPIEDADES}}</p>
            <p class="normal">FORMA DE USO: {{$FORMA_DE_USO}}</p>
            <p class="normal">INGREDIENTES: {{$INGREDIENTES}}</p>
            <p class="normal">CONTENIDO: {{$CONTENIDO}}</p>
        </p>
	    @endif
	    @if ($VALOR_NUTRCIONAL_CHECK == false && $PROPIEDADES_CHECK == false)
		<p class="col-2">
            <p align="center"><font size="9">{{$MARCA}}</p>
            <p class="normal">{{$NOMBRE_DEL_PRODUCTO}}</p>
            <p class="normal">FORMA DE USO: {{$FORMA_DE_USO}}</p>
            <p class="normal">INGREDIENTES: {{$INGREDIENTES}}</p>
            <p class="normal">CONTENIDO: {{$CONTENIDO}}</p>
        </p>
	    @endif
	</body>
	@endif

	@if ($TAMAÑO == 8)
	<style type="text/css">
		p{
            width: 80px;
            margin: 0px 0;
            padding: 0px;
            font: normal 8px arial, helvetica, sans-serif;
        }
        .normal  {
            white-space: normal;width: 	

        p#tamaño  {
            font: normal 12px arial, helvetica, sans-serif;
        }
	</style>
	<body>
		@if ($VALOR_NUTRCIONAL_CHECK == true && $PROPIEDADES_CHECK == true)
		<p class="col-2">
            <p align="center"><font size="9">{{$MARCA}}</font></p>
            <p class="normal">{{$NOMBRE_DEL_PRODUCTO}}</p>
            <p class="normal">{{$PROPIEDADES}}</p>
            <p class="normal">FORMA DE USO: {{$FORMA_DE_USO}}</p>
            <p class="normal">INGREDIENTES: {{$INGREDIENTES}}</p>
            <p class="normal">VALOR NUTRICIONAL: {{$VALOR_NUTRICIONAL}}</p>
            <p class="normal">CONTENIDO: {{$CONTENIDO}}</p>
        </p>
	    @endif
	    @if ($VALOR_NUTRCIONAL_CHECK == false && $PROPIEDADES_CHECK == true)
		<p class="col-2">
            <p align="center"><font size="9">{{$MARCA}}</font></p>
            <p class="normal">{{$NOMBRE_DEL_PRODUCTO}}</p>
            <p class="normal">{{$PROPIEDADES}}</p>
            <p class="normal">FORMA DE USO: {{$FORMA_DE_USO}}</p>
            <p class="normal">INGREDIENTES: {{$INGREDIENTES}}</p>
            <p class="normal">CONTENIDO: {{$CONTENIDO}}</p>
        </p>
	    @endif
	    @if ($VALOR_NUTRCIONAL_CHECK == false && $PROPIEDADES_CHECK == false)
		<p class="col-2">
            <p align="center"><font size="9">{{$MARCA}}</p>
            <p class="normal">{{$NOMBRE_DEL_PRODUCTO}}</p>
            <p class="normal">FORMA DE USO: {{$FORMA_DE_USO}}</p>
            <p class="normal">INGREDIENTES: {{$INGREDIENTES}}</p>
            <p class="normal">CONTENIDO: {{$CONTENIDO}}</p>
        </p>
	    @endif
	</body>
	@endif

	@if ($TAMAÑO == 7)
		<style type="text/css">
			p{
	            width: 10px;
	            margin: 0px 0;
	            padding: 0px;
	            font: normal 10px arial, helvetica, sans-serif;
	            line-height: 0.10;
	        }
	        .normal  {
	            white-space: normal;
	            line-height: 1;
	            margin-left: 5px;
	        } 

	        p#tamaño  {
	            font: normal 62px arial, helvetica, sans-serif;
	        }
		</style>
		<body>

			<!-- ....................................GRADO DE ALCOHOL..................................... -->


			@if ($ALCO_CHECK == true && $CALORIAS_CHECK == true && $CONTENIDO_CHECK == true)
				<p class="col-2">
		            <p class="normal"><font size="7">{{$NOMBRE}}</font></p>
		            <p class="normal"><font size="6.2">{{$PROPIEDADES}}</p>
		            <p class="normal"><font size="6.2">CONTENIDO: {{$CONTENIDO}}</p>
		            <p class="normal"><font size="6.2">CALORIAS: {{$CALORIAS}}</p>
		            <p class="normal"><font size="6.2">GRADO DE ALCOHOL: {{$GRAD_ALCOH}} %</p>
		        </p>
	        @else
		        @if ($ALCO_CHECK == true && $CALORIAS_CHECK == false && $PROPIEDADES_CHECK == true && $CONTENIDO_CHECK == true)
			        <p class="col-2">
			            <p class="normal"><font size="7">{{$NOMBRE}}</font></p>
			            <p class="normal"><font size="6.2">{{$PROPIEDADES}}</p>
			            <p class="normal"><font size="6.2">CONTENIDO: {{$CONTENIDO}}</p>
			            <p class="normal"><font size="6.2">GRADO DE ALCOHOL: {{$GRAD_ALCOH}} %</p>
			        </p>
		        @endif
		        @if ($ALCO_CHECK == true && $CALORIAS_CHECK == false && $PROPIEDADES_CHECK == false && $CONTENIDO_CHECK == true)
			        <p class="col-2">
			            <p class="normal"><font size="7">{{$NOMBRE}}</font></p>
			            <p class="normal"><font size="6.2">CONTENIDO: {{$CONTENIDO}}</p>
			            <p class="normal"><font size="6.2">GRADO DE ALCOHOL: {{$GRAD_ALCOH}} %</p>
			        </p>
		        @endif
		    @endif

		    <!-- ....................................CONTENIDO/INGREDIENTES/MATERIAL......................................... -->
		    @if ($MATE_CHECK == true && $ALCO_CHECK == false && $CONTENIDO_CHECK == true && $PROPIEDADES_CHECK == false && $INGRE_CHECK == false)
	        	<p class="col-2">
		            <p class="normal"><font size="7">{{$NOMBRE}}</font></p>
		            <p class="normal"><font size="6.2">MATERIAL: {{$MATERIAL}}</font></p>
		            <p class="normal"><font size="6.2">CONTENIDO: {{$CONTENIDO}}</font></p>
		        </p>
	        @endif
	        @if ($INGRE_CHECK == true && $ALCO_CHECK == false && $CONTENIDO_CHECK == true && $PROPIEDADES_CHECK == false && $MATE_CHECK == false)
		        <p class="col-2">
		            <p class="normal"><font size="7">{{$NOMBRE}}</font></p>
		            <p class="normal"><font size="6.2">INGREDIENTES: {{$INGREDIENTES}}</font></p>
		            <p class="normal"><font size="6.2">CONTENIDO: {{$CONTENIDO}}</font></p>
		        </p>
	        @else
		        @if ($INGRE_CHECK == false && $ALCO_CHECK == false && $CONTENIDO_CHECK == true && $PROPIEDADES_CHECK == false && $MATE_CHECK == false)
			        <p class="col-2">
			            <p class="normal"><font size="7">{{$NOMBRE}}</font></p>
			            <p class="normal"><font size="6.2">CONTENIDO: {{$CONTENIDO}}</font></p>
			        </p>
		        @endif
		        @if ($INGRE_CHECK == false && $ALCO_CHECK == false && $CONTENIDO_CHECK == false && $PROPIEDADES_CHECK == false && $MATE_CHECK == false)
			        <p class="col-2">
			            <p class="normal"><font size="7">{{$NOMBRE}}</font></p>
			        </p>
		        @endif
		        @if ($INGRE_CHECK == true && $ALCO_CHECK == false && $CONTENIDO_CHECK == false && $PROPIEDADES_CHECK == false && $MATE_CHECK == false)
			        <p class="col-2">
			            <p class="normal"><font size="7">{{$NOMBRE}}</font></p>
			            <p class="normal"><font size="6.2">INGREDIENTES: {{$INGREDIENTES}}</font></p>
			        </p>
		        @endif
		        @if ($INGRE_CHECK == false && $ALCO_CHECK == false && $CONTENIDO_CHECK == false && $PROPIEDADES_CHECK == false && $MATE_CHECK == true)
			        <p class="col-2">
			            <p class="normal"><font size="7">{{$NOMBRE}}</font></p>
			            <p class="normal"><font size="6.2">MATERIAL: {{$MATERIAL}}</font></p>
			        </p>
			    @endif
		    @endif


		    <!-- ..........................................PROPIEDADES/INGREDIENTES/MATERIAL...................................................... -->
		    @if ($MATE_CHECK == true && $ALCO_CHECK == false && $CONTENIDO_CHECK == true && $PROPIEDADES_CHECK == true && $INGRE_CHECK == false)
	        	<p class="col-2">
		            <p class="normal"><font size="7">{{$NOMBRE}}</font></p>
		            <p class="normal"><font size="6.2">{{$PROPIEDADES}}</font></p>
		            <p class="normal"><font size="6.2">MATERIAL: {{$MATERIAL}}</font></p>
		            <p class="normal"><font size="6.2">CONTENIDO: {{$CONTENIDO}}</font></p>
	            </p>
	        @endif
		    @if ($INGRE_CHECK == true && $ALCO_CHECK == false && $CONTENIDO_CHECK == true && $PROPIEDADES_CHECK == true && $MATE_CHECK == false)
			    <p class="col-2">
		            <p class="normal"><font size="7">{{$NOMBRE}}</font></p>
		            <p class="normal"><font size="6.2">{{$PROPIEDADES}}</font></p>
		            <p class="normal"><font size="6.2">INGREDIENTES: {{$INGREDIENTES}}</font></p>
		            <p class="normal"><font size="6.2">CONTENIDO: {{$CONTENIDO}}</font></p>
		        </p>
		    @else
		    	@if ($INGRE_CHECK == false && $ALCO_CHECK == false && $CONTENIDO_CHECK == true && $PROPIEDADES_CHECK == true && $MATE_CHECK == false)
			        <p class="col-2">
			            <p class="normal"><font size="7">{{$NOMBRE}}</font></p>
			            <p class="normal"><font size="6.2">{{$PROPIEDADES}}</font></p>
			            <p class="normal"><font size="6.2">CONTENIDO: {{$CONTENIDO}}</font></p>
			        </p>
		        @endif
		        @if ($INGRE_CHECK == false && $ALCO_CHECK == false && $CONTENIDO_CHECK == false && $PROPIEDADES_CHECK == true && $MATE_CHECK == false)
			        <p class="col-2">
			            <p class="normal"><font size="7">{{$NOMBRE}}</font></p>
			            <p class="normal"><font size="6.2">{{$PROPIEDADES}}</font></p>
			        </p>
		        @endif
		        @if ($INGRE_CHECK == true && $ALCO_CHECK == false && $CONTENIDO_CHECK == false && $PROPIEDADES_CHECK == true && $MATE_CHECK == false)
			        <p class="col-2">
			            <p class="normal"><font size="7">{{$NOMBRE}}</font></p>
			            <p class="normal"><font size="6.2">{{$PROPIEDADES}}</font></p>
			            <p class="normal"><font size="6.2">INGREDIENTES: {{$INGREDIENTES}}</font></p>
			        </p>
		        @endif
		        @if ($INGRE_CHECK == false && $ALCO_CHECK == false && $CONTENIDO_CHECK == false && $PROPIEDADES_CHECK == true && $MATE_CHECK == true)
			        <p class="col-2">
			            <p class="normal"><font size="7">{{$NOMBRE}}</font></p>
			            <p class="normal"><font size="6.2">{{$PROPIEDADES}}</font></p>
			            <p class="normal"><font size="6.2">MATERIAL: {{$MATERIAL}}</font></p>
			        </p>
		        @endif
		    @endif
		</body>
	@endif

	@if ($TAMAÑO == 6)
	<style type="text/css">
		p{
            width: 80px;
            margin: 0px 0;
            padding: 0px;
            font: normal 3px arial, helvetica, sans-serif;
            line-height: 0.1;
        }
        .normal  {
            white-space: normal;
            line-height: 0.90;
        }
        .diferente  {
            white-space: normal;
            line-height: 0.9;
        } 

        p#tamaño  {
            font: normal 12px arial, helvetica, sans-serif;
        }
	</style>
	<body>  

		<!-- ....................................GRADO DE ALCOHOL..................................... -->

		@if ($ALCO_CHECK == true && $CALORIAS_CHECK == true && $CONTENIDO_CHECK == true)
			<p class="col-2">
	            <p class="diferente"><font size="5">{{$NOMBRE}}</font></p>
	            <p class="diferente"><font size="5.8">{{$PROPIEDADES}}</font></p>
	            <p class="normal"><font size="5.8">CONTENIDO: {{$CONTENIDO}}</font></p>
	            <p class="normal"><font size="5.8">CALORIAS: {{$CALORIAS}}</font></p>
	            <p class="normal"><font size="5.8">GRADO DE ALCOHOL: {{$GRAD_ALCOH}} %</font></p>
	        </p>
        @else
	        @if ($ALCO_CHECK == true && $CALORIAS_CHECK == false && $PROPIEDADES_CHECK == true && $CONTENIDO_CHECK == true) 
		        <p class="col-2">
		            <p class="diferente"><font size="5">{{$NOMBRE}}</font></p>
		            <p class="diferente"><font size="5.8">{{$PROPIEDADES}}</font></p>
		            <p class="normal"><font size="5.8">CONTENIDO: {{$CONTENIDO}}</font></p>
		            <p class="normal"><font size="5.8">GRADO DE ALCOHOL: {{$GRAD_ALCOH}} %</font></p>
		        </p>
	        @endif
	        @if ($ALCO_CHECK == true && $CALORIAS_CHECK == false && $PROPIEDADES_CHECK == false && $CONTENIDO_CHECK == true)
		        <p class="col-2">
		            <p class="diferente"><font size="5">{{$NOMBRE}}</font></p>
		            <p class="normal"><font size="5.8">CONTENIDO: {{$CONTENIDO}}</font></p>
		            <p class="normal"><font size="5.8">GRADO DE ALCOHOL: {{$GRAD_ALCOH}} %</font></p>
		        </p>
	        @endif
	    @endif


	    <!-- ....................................CONTENIDO/INGREDIENTES/MATERIAL......................................... -->
	    @if ($MATE_CHECK == true && $ALCO_CHECK == false && $CONTENIDO_CHECK == true && $PROPIEDADES_CHECK == false && $INGRE_CHECK == false)
        	<p class="col-2">
	            <p class="diferente"><font size="5">{{$NOMBRE}}</font></p>
	            <p class="normal"><font size="5.8">MATERIAL: {{$MATERIAL}}</font></p>
	            <p class="normal"><font size="5.8">CONTENIDO: {{$CONTENIDO}}</font></p>
	        </p>
        @endif
        @if ($INGRE_CHECK == true && $ALCO_CHECK == false && $CONTENIDO_CHECK == true && $PROPIEDADES_CHECK == false && $MATE_CHECK == false)
	        <p class="col-2">
	            <p class="diferente"><font size="5">{{$NOMBRE}}</font></p>
	            <p class="normal"><font size="5.8">INGREDIENTES: {{$INGREDIENTES}}</font></p>
	            <p class="normal"><font size="5.8">CONTENIDO: {{$CONTENIDO}}</font></p>
	        </p>
        @else
	        @if ($INGRE_CHECK == false && $ALCO_CHECK == false && $CONTENIDO_CHECK == true && $PROPIEDADES_CHECK == false && $MATE_CHECK == false)
		        <p class="col-2">
		            <p class="diferente"><font size="5">{{$NOMBRE}}</font></p>
		            <p class="normal"><font size="5.8">CONTENIDO: {{$CONTENIDO}}</font></p>
		        </p>
	        @endif
	        @if ($INGRE_CHECK == false && $ALCO_CHECK == false && $CONTENIDO_CHECK == false && $PROPIEDADES_CHECK == false && $MATE_CHECK == false)
		        <p class="col-2">
		            <p class="diferente"><font size="5">{{$NOMBRE}}</font></p>
		        </p>
	        @endif
	        @if ($INGRE_CHECK == true && $ALCO_CHECK == false && $CONTENIDO_CHECK == false && $PROPIEDADES_CHECK == false && $MATE_CHECK == false)
		        <p class="col-2">
		            <p class="diferente"><font size="5">{{$NOMBRE}}</font></p>
		            <p class="normal"><font size="5.8">INGREDIENTES: {{$INGREDIENTES}}</font></p>
		        </p>
	        @endif
	        @if ($INGRE_CHECK == false && $ALCO_CHECK == false && $CONTENIDO_CHECK == false && $PROPIEDADES_CHECK == false && $MATE_CHECK == true)
		        <p class="col-2">
		            <p class="diferente"><font size="5">{{$NOMBRE}}</font></p>
		            <p class="normal"><font size="5.8">MATERIAL: {{$MATERIAL}}</font></p>
		        </p>
		    @endif
	    @endif

	    <!-- ..........................................PROPIEDADES/INGREDIENTES/MATERIAL...................................................... -->
	    @if ($MATE_CHECK == true && $ALCO_CHECK == false && $CONTENIDO_CHECK == true && $PROPIEDADES_CHECK == true && $INGRE_CHECK == false)
        	<p class="col-2">
	            <p class="diferente"><font size="5">{{$NOMBRE}}</font></p>
	            <p class="diferente"><font size="5.8">{{$PROPIEDADES}}</font></p>
	            <p class="normal"><font size="5.8">MATERIAL: {{$MATERIAL}}</font></p>
	            <p class="normal"><font size="5.8">CONTENIDO: {{$CONTENIDO}}</font></p>
            </p>
        @endif
	    @if ($INGRE_CHECK == true && $ALCO_CHECK == false && $CONTENIDO_CHECK == true && $PROPIEDADES_CHECK == true && $MATE_CHECK == false)
		    <p class="col-2">
	            <p class="diferente"><font size="5">{{$NOMBRE}}</font></p>
	            <p class="diferente"><font size="5.8">{{$PROPIEDADES}}</font></p>
	            <p class="normal"><font size="5.8">INGREDIENTES: {{$INGREDIENTES}}</font></p>
	            <p class="normal"><font size="5.8">CONTENIDO: {{$CONTENIDO}}</font></p>
	        </p>
	    @else
	    	@if ($INGRE_CHECK == false && $ALCO_CHECK == false && $CONTENIDO_CHECK == true && $PROPIEDADES_CHECK == true && $MATE_CHECK == false)
		        <p class="col-2">
		            <p class="diferente"><font size="5">{{$NOMBRE}}</font></p>
		            <p class="diferente"><font size="5.8">{{$PROPIEDADES}}</font></p>
		            <p class="normal"><font size="5.8">CONTENIDO: {{$CONTENIDO}}</font></p>
		        </p>
		    @endif
		    @if ($INGRE_CHECK == false && $ALCO_CHECK == false && $CONTENIDO_CHECK == false && $PROPIEDADES_CHECK == true && $MATE_CHECK == false)
		        <p class="col-2">
		            <p class="diferente"><font size="5">{{$NOMBRE}}</font></p>
		            <p class="diferente"><font size="5.8">{{$PROPIEDADES}}</font></p>
		        </p>
		    @endif
		    @if ($INGRE_CHECK == true && $ALCO_CHECK == false && $CONTENIDO_CHECK == false && $PROPIEDADES_CHECK == true && $MATE_CHECK == false)
		        <p class="col-2">
		            <p class="diferente"><font size="5">{{$NOMBRE}}</font></p>
		            <p class="diferente"><font size="5.8">{{$PROPIEDADES}}</font></p>
		            <p class="normal"><font size="5.8">INGREDIENTES: {{$INGREDIENTES}}</font></p>
		        </p>
		    @endif
		    @if ($INGRE_CHECK == false && $ALCO_CHECK == false && $CONTENIDO_CHECK == false && $PROPIEDADES_CHECK == true && $MATE_CHECK == true)
		        <p class="col-2">
		            <p class="diferente"><font size="5">{{$NOMBRE}}</font></p>
		            <p class="diferente"><font size="5.8">{{$PROPIEDADES}}</font></p>
		            <p class="normal"><font size="5.8">MATERIAL: {{$MATERIAL}}</font></p>
		        </p>
		    @endif
		@endif
	</body>
	@endif
</html>