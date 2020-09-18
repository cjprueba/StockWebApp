<template>
<div class="container-fluid ">
  <div id="demo" class="carousel slide m-1" data-ride="carousel">
        <!-- El carrusel -->
        <div class="carousel-inner">
            <div align=center><span v-html="imagen.logo"></span></div>
           
            <div v-bind:class="{'carousel-item': true, active: activar(index) }" v-for="(producto, index) in productos">
                <div class="card">
                    <div class="row">
                        <div class="col-md-6 text-center align-self-center border-right">
                            <!-- <img src="./img/product1.jpg" class="img-fluid"> -->
                            <div class="card-img">
                            <span v-html="producto.IMAGEN"></span>
                        	</div>
                        </div>
                        <div class="col-md-6 my-auto">
                            <!-- <div class="offer"><big><b>{{producto.DESCUENTO}}% OFF</b></big></div> -->
                            <div class="row title mb-2">
                                <div class="col-md-12 text-center">
                                    
                                    <h1 class="font-weight-bold text-truncate">{{producto.DESCRIPCION}} </h1>
                                    <h6 class="font-weight-light" >{{producto.CATEGORIA}} </h6>
                                    <h3 class="font-weight-light" >{{producto.CODIGO}} </h3>
                                    <!-- <p>Ofrecemos una variedad de sabores únicos que son particulares de los frijoles y los métodos de fabricación.
                                        La marca proviene del estado de Georgia, el lugar de nacimiento de Coca Cola, en los Estados Unidos.
                                    </p> -->
                                </div>
                            </div>
                            <!-- <div class="row prices">
                                <label class="radio">
                                    <span>
                                        <div class="row">
                                            <big class="centrar texto-chico">Antes</big>
                                        </div>
                                        <div class="row incorrecto">
                                            {{producto.PREC_VENTA}}
                                        </div>
                                    </span>
                                </label> 

                                
                                                      
                            </div> -->
                            <div class="row">
                            	
                            	<div class="col-md-12">
                            		<div class="text-center">
                            			<span class="" v-html="imagen.oferta"></span>
                            		</div>
                            	</div>	

                            	<div class="col-md-12 mt-3">
                            		<hr>
                            		<h1 class="text-center text-danger">
                            			{{producto.DESCUENTO}}% OFF
                            		</h1>
                            		<hr>
                            	</div>	

                            	<div class="col-md-12 text-center mt-3">
                            		<span  class="badge badge-primary">
		                                <p style="font-size:1vw;">ANTES</p><br>
		                                <p style="font-size:3vw;" class="incorrecto">{{producto.PREC_VENTA}}</p>
		                            </span>

		                            <span  class="badge badge-danger">
		                                <p style="font-size:2vw;">AHORA</p><br>
		                                <p style="font-size:7vw;color:yellow">{{producto.PREC_DESCUENTO}}</p>
		                            </span>
                            	</div>

                            		
                            </div>	
                            
                            

                            
                        </div>

                        <div>
                        </div>	
                    </div>
                </div>
            </div>
            
        </div>
  
        <!-- Left and right controls -->
        <a class="carousel-control-prev" href="#demo" data-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </a>
        <a class="carousel-control-next" href="#demo" data-slide="next">
            <span class="carousel-control-next-icon"></span>
        </a>
    </div>
	</div>
</template>
<script>
	export default {
	  props: ['sucursal'],	
      data(){
        return {
          productos: [],
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
	    background: #f0f5d3;
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