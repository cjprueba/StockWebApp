<template>
	<div class="container-fluid " align="center">
		<div class="col-12" align="center">
			<div class="row ">
				<div class="col">
					<div class="p-3 text-white fixed-top text-center titulo"><strong>PAGINA EN MANTENIMIENTO</strong></div> 	
				</div>
			</div>
			<div class="row text-center">
				<div class="col-3">
				</div>

				<div class="card col-6 text-center" style="width: 35rem; align-content: center;">
				  <div class="card-body">
				    <h1 class="card-title"><strong>¡LAMENTAMOS LAS MOLESTIAS!</strong></h1>
				    <h2 class="card-text">Este espacio se encuentra en mantenimiento.</h2>
				    <h2 class="card-text">Pronto estara disponible nuevamente.</h2>
				    <br>
				    <br>
				    <meta name="viewport" content="width=device-width, initial-scale=1">
					<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
				    <h4 class="card-text">Para estar a tanto de las novedades de la tienda, pueden seguirnos en nuestras redes sociales.</h4>
				    <br>
				    <a href="https://instagram.com/tokutokuya_py" class="stretched-link col-3 texto-chico"><i class="fa fa-instagram" style="font-size:30px;color:#82bb39"></i></a>
				    <a>&nbsp;&nbsp;&nbsp;</a>
				    <a href="https://www.facebook.com/tokutokuya.py" class="stretched-link col-3 texto-chico"><i class="fa fa-facebook" style="font-size:30px;color:#82bb39"></i></a>

				  </div>
				</div>
			</div>	
			<!-- <div class="row ">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
				<div class="fixed-bottom img-thumbnail">
					<div class="row" align="center">
						<div class="col-2 mt-5" align=right style="font-size:25px">
							<i class="fa fa-facebook" style="font-size:25px;color:#82bb39"></i>
							<i class="fa fa-instagram ml-1 " style="font-size:30px;color:#82bb39"></i>
							
							<label class="ml-2">tokutokuya_py</label>
						</div>
					</div>	
				</div>
			</div> -->
		</div>
	</div>
</template>
<script>
	
</script>
<style scoped>
	*{
	    margin: 0;
	    padding: 0;
	    box-sizing: border-box;
	}
	.container-fluid {
	    width: 100%;
	    min-height: 100vh;
	    display: flex;
	    justify-content: center;
	    align-items: center;
	    background: #ffffff;
	    font-size: 0.8rem;
	}
	.col-12, .col-md-9{
	    padding-left: 0px !important;
	    padding-right: 0px !important;
	}
	.col-12, .col-md-9 .h1{
	    font-size: 14px;
	}
	.card{
	    width: 100%;    
	    padding: 4em;
	}
	.row{
	    margin: 0;
	}
	.info{
	    padding: 6vh 0vh;
	    border: 1px solid #0b932d;
	    border-radius: 5px;
	    padding-left: 12%;
	    box-shadow: -1px 5px 8px rgba(14,161,207,0.4) !important;
	    overflow: hidden;
	}
	.prices{
	    margin: 0;
	    display: flex;
	}
	label.radio span{
	    padding: 1vh 10vh;
	    display: inline-block;
	    margin-left: 3vh;
	    border: 1px solid grey;
	    border-radius: 5px;
	    font-size: 23px;
	}
	.centrar{
	    margin: auto;
	}
	
	label.radion span{
	    padding: 1vh 10vh;
	    display: inline-block;
	    margin-left: 3vh;
	    background: #fffb16;
	    border: 1px solid rgb(252, 255, 48);
	    border-radius: 5px;
	    color: red;
	    box-shadow: -1px 5px 8px rgba(28, 29, 24, 0.4) !important;
	    font-size: 24px;
	}
	.carousel-control-prev-icon{
	    visibility: hidden;
	}
	.img-thumbnail{
		background: #f7f7f7;
		padding: 15px;
	}
	.carousel-control-next-icon{
	    visibility: hidden;
	}
	.incorrecto {
	    text-decoration:line-through;
	    color: white;
	}
	.texto-chico{
	    font-size: 20px;
	}
	.titulo{
	    font-size: 70px ;
	    font-family: Verdana;
	    background: #82bb39;
	}
	.color{
		background: #f7f7f7;
	}
	.carousel-item{
	    border: 1px;
	    border-radius: 5px;
	}
	.offer{
	    display: block;
	    color: #ffffff;
	    text-align: center;
	    font-size: 23px;
	    padding: 1px 0;
	    position: absolute;
	    top: 35px;
	    z-index: 100;
	    left: -43px;
	    width: 200px;
	    height: 40px;
	    box-shadow: 0 1px 7px #666;
	    -ms-transform: rotate(-45deg);
	    -moz-transform: rotate(-45deg);
	    -o-transform: rotate(-45deg);
	    -webkit-transform: rotate(-45deg);
	    transform: rotate(-45deg);
	    background: -moz-linear-gradient(top, #ff4040 0%, #ff4040 1%, #ff4040 100%);
	    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #ff4040), color-stop(1%, #ff4040), color-stop(100%, #ff4040));
	   
	}

	.border-right.border-md {
	  border-right-width:0!important;
	}
	    
	@media (min-width: 768px) {
	  .border-right.border-md {
	    border-right-width:1px!important;
	  }
	}
	
</style>