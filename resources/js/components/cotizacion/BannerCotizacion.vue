<template>
	<div class="container-fluid ">
		<div class="col-12">
			<div class="row ">
				<div class="col">
					<div class="p-3 text-white fixed-top text-center titulo"><strong>CAMBIOS DEL DÍA</strong> </div> 	
				</div>
			</div>

			
			<div class="row no-gutters position-relative ">

				<div class="col-md-2 mb-md-0 p-md-4 text-right">
				    <div style="font-size:125px">
				    	<span class="flag-icon flag-icon-py"></span>
				    </div>
				</div>
				<div class="col-md-2 position-static p-4 pl-md-0 text-left mt-1">
				  	<br>
				    <p style="font-size:1.5vw; "><strong>GUARANÍ</strong></p>
				    <p style="font-size:3.5vw; "><strong>G$ {{cotizacion.guarani}} </strong></p>
				   
				</div>


				<div class="col-md-2 mb-md-0 p-md-4 text-right">
				    <div style="font-size:125px">
				    	<span class="flag-icon flag-icon-br"></span>
				    </div>
				</div>
				<div class="col-md-2 position-static p-4 pl-md-0 text-left mt-1">
				  	<br>
				    <p style="font-size:1.5vw; "><strong>REAL</strong></p>
				    <p style="font-size:3.5vw; "><strong>R$ {{cotizacion.real}} </strong></p>
				</div>


				<div class="col-md-2 mb-md-0 p-md-4 text-right">
				    <div style="font-size:125px">
				    	<span class="flag-icon flag-icon-ar"></span>
				    </div>
				</div>
				<div class="col-md-2 position-static p-4 pl-md-0 text-left mt-1">
				  	<br>
				    <p style="font-size:1.5vw; "><strong>PESO</strong></p>
				    <p style="font-size:3.5vw; "><strong>P$ {{cotizacion.peso}}</strong> </p>
				</div>


			</div>	

			
			<div class="row ">

				<div class="fixed-bottom img-thumbnail" align=center><span v-html="imagen.logo"></span></div>
			</div>
		</div>
		

			
	

	</div>
</template>
<script>
	export default {
	  props: ['sucursal'],	
      data(){
        return {
          productos: [],
          cotizacion: {
          	guarani:'',
          	dolar:'',
          	real:'',
          	peso: ''
          },
          imagen: {
          	oferta: '',
          	logo: ''
          }
        }
      }, 
      methods: {
      	activar(index){
      		if (index === 0) {
      			return true;
      		}
      	}, 
      	obtener(){

      		// ------------------------------------------------------------------------

      		// OBTENER PRODUCTOS 

      		let me = this;
      		Common.obtenerCotizacionBannerCommon(this.sucursal).then(data => {
	        			me.cotizacion.guarani = data.Guaranies;
	        			me.cotizacion.dolar = data.Dolares;
	        			me.cotizacion.real = data.Reales;
	        			me.cotizacion.peso = data.Pesos;
	        			
	        });
	      

	        Common.obtenerProductoOfertaCommon(this.sucursal).then(data => {
	        			me.productos = data.data;
	        			me.imagen.oferta = data.oferta;
	        			me.imagen.logo = data.logo;
	        });

	         // ------------------------------------------------------------------------

      	}
      },
        mounted() {

          // ------------------------------------------------------------------------

          this.obtener();
          let me = this;

          // ------------------------------------------------------------------------

          setInterval( function () {
				me.obtener(); // user paging is not reset on reload
		  }, 1800000 );

          // 1800000

          // ------------------------------------------------------------------------

          $('.carousel').carousel({
			  interval: 1000 * 7
		  });

          // ------------------------------------------------------------------------


        }
    }
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