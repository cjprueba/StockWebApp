<template>
	<!-- ******************************************************************* -->

		<!-- MODAL DETALLE PRODUCTO -->
		<div class="container-fluid">
				<div class="modal fade modal-detalle" tabindex="-1" role="dialog"  aria-hidden="true">
				  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
				    <div class="modal-content">
				      <div class="modal-header">
				        <h5 class="modal-title" id="exampleModalCenterTitle"><small>Detalle</small></h5>
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				          <span aria-hidden="true">&times;</span>
				        </button>
				      </div>
				      <div class="modal-body">
				        	<div class="row">
			                    <div class="col-md-4">
			                        <div class="profile-img">
			                            <img :src="producto.IMAGEN" alt="" class="img-thumbnail"/>
			                        </div>
			                        <div class="row">
			                        	<div class="col-md-12">
					                        <div class="profile-work">
					                            <p>PRECIOS</p>
					                            <span class="detalle"><strong>Venta:</strong> <span class="float-right">{{producto.PREC_VENTA}} </span></span><br/>
					                            <span class="detalle"><strong>Mayorista:</strong> <span class="float-right">{{producto.PREMAYORISTA}}</span></span><br/>
					                            <span class="detalle"><strong>VIP:</strong> <span class="float-right">{{producto.PREVIP}} </span></span><br/>
					                            <span class="detalle"><strong>Costo:</strong> <span class="float-right">{{producto.PRECOSTO}} </span></span>
					                            <p>FECHAS</p>
					                            <span class="detalle"><strong>Creación:</strong> <span class="float-right">{{producto.FECALTAS}} </span></span><br/>
					                            <span class="detalle"><strong>Modificación:</strong> <span class="float-right">{{producto.FECMODIF}} </span></span><br/>
					                            <span class="detalle"><strong>Ult. Venta:</strong> <span class="float-right">{{producto.FECHULT_V}}</span></span><br/>
					                            <span class="detalle"><strong>Ult. Compra:</strong> <span class="float-right">{{producto.FECHULT_C}}</span></span>
					                            <p>STOCK</p>
					                            <span class="detalle"><strong>Minimo:</strong> <span class="float-right">{{producto.STOCK_MIN}} </span></span><br/>
					                            <span class="detalle"><strong>Máximo:</strong> <span class="float-right">2,000</span></span><br/>
					                        </div>
			                    		</div>
			                        </div>	
			                    </div>
			                    <div class="col-md-8">
			                        <div class="profile-head">
			                                    <h5>
			                                        {{codigo}}
			                                    </h5>
			                                    <h6>
			                                        {{producto.DESCRIPCION}}
			                                    </h6>
			                                    <p class="proile-rating">STOCK : <span>{{producto.STOCK}}</span></p>
			                            <ul class="nav nav-tabs" id="myTab" role="tablist">
			                                <li class="nav-item">
			                                    <a class="nav-link active" id="informacion-tab" data-toggle="tab" href="#informacion" role="tab" aria-controls="informacion" aria-selected="true">Información</a>
			                                </li>
			                                <li class="nav-item">
			                                    <a class="nav-link" id="lotes-tab" data-toggle="tab" href="#lotes" role="tab" aria-controls="lotes" aria-selected="false" v-on:click="obtenerLotes">Lotes</a>
			                                </li>
			                                <li class="nav-item">
			                                    <a class="nav-link" id="gondola-tab" data-toggle="tab" href="#gondola" role="tab" aria-controls="gondola" aria-selected="false" v-on:click="obtenerGondolas">Góndolas</a>
			                                </li>
			                                <li class="nav-item">
			                                    <a class="nav-link" id="proveedores-tab" data-toggle="tab" href="#proveedores" role="tab" aria-controls="proveedor" aria-selected="false" v-on:click="obtenerProveedores">Compras</a>
			                                </li>
			                                <li class="nav-item">
			                                    <a class="nav-link" id="gondola-tab" data-toggle="tab" href="#gondola" role="tab" aria-controls="gondola" aria-selected="false">Depósitos</a>
			                                </li>
			                                <li class="nav-item">
			                                    <a class="nav-link" id="transferencias-tab" data-toggle="tab" href="#transferencias" role="tab" aria-controls="transferencias" aria-selected="false" v-on:click="obtenerTransferenciaProductos">Transferencias</a>
			                                </li>
			                            </ul>
			                            <div class="row">
			                            	<div class="col-md-12">
						                        <div class="tab-content informacion-tab" id="myTabContent">
						                            <div class="tab-pane fade show active" id="informacion" role="tabpanel" aria-labelledby="informacion-tab">
						                                        <div class="row">
						                                            <div class="col-md-6">
						                                                <label>Código Interno</label>
						                                            </div>
						                                            <div class="col-md-6">
						                                                <p>{{producto.CODIGO_INTERNO}}</p>
						                                            </div>
						                                        </div>
						                                        <div class="row">
						                                            <div class="col-md-6">
						                                                <label>Categoría</label>
						                                            </div>
						                                            <div class="col-md-6">
						                                                <p>{{producto.CATEGORIA}}</p>
						                                            </div>
						                                        </div>
						                                        <div class="row">
						                                            <div class="col-md-6">
						                                                <label>Sub Categoría</label>
						                                            </div>
						                                            <div class="col-md-6">
						                                                <p>{{producto.SUBCATEGORIA}}</p>
						                                            </div>
						                                        </div>
						                                        <div class="row">
						                                            <div class="col-md-6">
						                                                <label>Color</label>
						                                            </div>
						                                            <div class="col-md-6">
						                                                <p>{{producto.COLOR}}</p>
						                                            </div>
						                                        </div>
						                                        <div class="row">
						                                            <div class="col-md-6">
						                                                <label>IVA</label>
						                                            </div>
						                                            <div class="col-md-6">
						                                                <p>{{producto.IMPUESTO}}</p>
						                                            </div>
						                                        </div>
						                                        <div class="row">
						                                            <div class="col-md-6">
						                                                <label>Cantidad Mayorista</label>
						                                            </div>
						                                            <div class="col-md-6">
						                                                <p>{{producto.CANT_MAYORISTA}}</p>
						                                            </div>
						                                        </div>
						                                        <div class="row">
						                                            <div class="col-md-6">
						                                                <label>Unidad de Medida</label>
						                                            </div>
						                                            <div class="col-md-6">
						                                                <p>{{producto.PRESENTACION}}</p>
						                                            </div>
						                                        </div>
						                                        <div class="row">
						                                            <div class="col-md-6">
						                                                <label>Moneda</label>
						                                            </div>
						                                            <div class="col-md-6">
						                                                <p>{{producto.MONEDA}}</p>
						                                            </div>
						                                        </div>
						                                        <div class="row">
						                                            <div class="col-md-6">
						                                                <label>Descuento</label>
						                                            </div>
						                                            <div class="col-md-6">
						                                                <p>{{producto.DESCUENTO}}</p>
						                                            </div>
						                                        </div>
						                                        <div class="row">
								                                    <div class="col-md-12">
								                                        <label>Observación</label><br/>
								                                        <p>{{producto.OBSERVACION}}</p>
								                                    </div>
								                                </div>
						                            </div>
						                            <div class="tab-pane fade" id="lotes" role="tabpanel" aria-labelledby="lotes-tab">
						                                        <table class="table">
																  <thead>
																    <tr>
																      <th scope="col">#</th>
																      <th scope="col">Lote</th>
																      <th scope="col">Inicial</th>
																      <th scope="col">Existente</th>
																      <th scope="col">Costo</th>
																      <th scope="col">Creación</th>
																    </tr>
																  </thead>
																  <tbody>
																    <tr v-for="(lote, index) in lotes" class="cuerpoTabla">
																      <th scope="row">{{index + 1}}</th>	
																      <th>{{lote.LOTE}}</th>
																      <td>{{lote.CANTIDAD_INICIAL}}</td>
																      <td>{{lote.CANTIDAD}}</td>
																      <td>{{lote.COSTO}}</td>
																      <td>{{lote.FECALTAS}}</td>
																    </tr>
																  </tbody>
																</table>
						                            </div>
						                            <div class="tab-pane fade" id="gondola" role="tabpanel" aria-labelledby="gondola-tab">
						                                        <table class="table table-borderless">
																  <thead>
																    <tr>
																      <th scope="col">#</th>
																      <th scope="col">Código</th>
																      <th scope="col">Descripción</th>
																      <th scope="col">Asignacion</th>
																    </tr>
																  </thead>
																  <tbody>
																    <tr v-for="(gondola, index) in gondolas" class="cuerpoTabla">
																      <th scope="row">{{index + 1}}</th>	
																      <th>{{gondola.CODIGO}}</th>
																      <td>{{gondola.DESCRIPCION}}</td>
																      <td>{{gondola.FECHA}}</td>
																    </tr>
																  </tbody>
																</table>
						                            </div>
						                            <div class="tab-pane fade" id="proveedores" role="tabpanel" aria-labelledby="proveedores-tab">
						                                        <table class="table">
																  <thead>
																    <tr>
																      <th scope="col">#</th>
																      <th scope="col">Proveedor</th>
																      <th scope="col">Cantidad</th>
																      <th scope="col">Guaranies</th>
																      <th scope="col">Dolares</th>
																      <th scope="col">Reales</th>
																      <th scope="col">Pesos</th>
																      <th scope="col">Ult. Compra</th>
																    </tr>
																  </thead>
																  <tbody>
																    <tr v-for="proveedor in proveedores" class="cuerpoTabla">
																      <th scope="row">{{proveedor.C}}</th>	
																      <th>{{proveedor.NOMBRE}}</th>
																      <td>{{proveedor.CANTIDAD}}</td>
																      <td>{{proveedor.GUARANIES}}</td>
																      <td>{{proveedor.DOLARES}}</td>
																      <td>{{proveedor.PESOS}}</td>
																      <td>{{proveedor.REALES}}</td>
																      <td>{{proveedor.FECALTAS}}</td>
																    </tr>
																  </tbody>
																</table>
						                            </div>
						                            <div class="tab-pane fade" id="proveedores" role="tabpanel" aria-labelledby="proveedores-tab">
						                                        <table class="table">
																  <thead>
																    <tr>
																      <th scope="col">#</th>
																      <th scope="col">Proveedor</th>
																      <th scope="col">Cantidad</th>
																      <th scope="col">Guaranies</th>
																      <th scope="col">Dolares</th>
																      <th scope="col">Reales</th>
																      <th scope="col">Pesos</th>
																      <th scope="col">Ult. Compra</th>
																    </tr>
																  </thead>
																  <tbody>
																    <tr v-for="proveedor in proveedores" class="cuerpoTabla">
																      <th scope="row">{{proveedor.C}}</th>	
																      <th>{{proveedor.NOMBRE}}</th>
																      <td>{{proveedor.CANTIDAD}}</td>
																      <td>{{proveedor.GUARANIES}}</td>
																      <td>{{proveedor.DOLARES}}</td>
																      <td>{{proveedor.PESOS}}</td>
																      <td>{{proveedor.REALES}}</td>
																      <td>{{proveedor.FECALTAS}}</td>
																    </tr>
																  </tbody>
																</table>
						                            </div>
						                            <div class="tab-pane fade" id="transferencias" role="tabpanel" aria-labelledby="transferencias-tab">
						                            	<div class="card">
														  <div class="card-body">
														    <h5 class="card-title">Recibidos</h5>
														    <h6 class="card-subtitle mb-10 text-muted">Transferencias importadas de otras sucursales</h6>
														    <table class="table mt-4">
																  <thead>
																    <tr>
																      <th scope="col">#</th>
																      <th scope="col">Sucursal</th>
																      <th scope="col">Transferencias</th>
																      <th scope="col">Cantidad</th>
																      <th scope="col">Guaranies</th>
																      <th scope="col">Dolares</th>
																      <th scope="col">Reales</th>
																      <th scope="col">Pesos</th>
																    </tr>
																  </thead>
																  <tbody>
																    <tr v-for="importado in importados" class="cuerpoTabla">
																      <th scope="row">{{importado.C}}</th>	
																      <th>{{importado.DESCRIPCION}}</th>
																      <td>{{importado.CANTIDAD_TRANSFERENCIA}}</td>
																      <td>{{importado.CANTIDAD}}</td>
																      <td>{{importado.GUARANIES}}</td>
																      <td>{{importado.DOLARES}}</td>
																      <td>{{importado.REALES}}</td>
																      <td>{{importado.PESOS}}</td>
																    </tr>
																  </tbody>
															</table>
														  </div>
														</div>
						                                
						                                <div class="card mt-4 mb-4">
														  <div class="card-body">
														    <h5 class="card-title">Enviados</h5>
														    <h6 class="card-subtitle mb-10 text-muted">Transferencias enviadas a otras sucursales</h6>
														    <table class="table mt-4">
																  <thead>
																    <tr>
																      <th scope="col">#</th>
																      <th scope="col">Sucursal</th>
																      <th scope="col">Transferencias</th>
																      <th scope="col">Cantidad</th>
																      <th scope="col">Guaranies</th>
																      <th scope="col">Dolares</th>
																      <th scope="col">Reales</th>
																      <th scope="col">Pesos</th>
																    </tr>
																  </thead>
																  <tbody>
																    <tr v-for="enviada in enviadas" class="cuerpoTabla">
																      <th scope="row">{{}}</th>	
																      <th>{{enviada.DESCRIPCION}}</th>
																      <td>{{enviada.CANTIDAD_TRANSFERENCIA}}</td>
																      <td>{{enviada.CANTIDAD}}</td>
																      <td>{{enviada.GUARANIES}}</td>
																      <td>{{enviada.DOLARES}}</td>
																      <td>{{enviada.REALES}}</td>
																      <td>{{enviada.PESOS}}</td>
																    </tr>
																  </tbody>
															</table>
														  </div>
														</div>

						                            </div>
						                        </div>
						                    </div>
			                            </div>	
			                        </div>
			                    </div>
			                </div>
			                <div class="row">
			                    
			                    
			                </div>     
				      </div>
				      <div class="modal-footer">
				        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
				      </div>
				    </div>
				  </div>
				</div>	
		</div>
		<!-- ******************************************************************* -->	
</template>
<script>
	export default {
	  props: ["codigo"],	
      data(){
        return {
          	producto: {
          		DESCRIPCION: '',
          		STOCK: '',
          		IMAGEN: '',
          		PREC_VENTA: '',
          		PREMAYORISTA: '',
          		PREVIP: '',
          		PRECOSTO: '',
          		FECALTAS: '',
                FECMODIF: '',
                FECHULT_C: '',
                FECHULT_V: '',
                CATEGORIA: '',
                SUBCATEGORIA: '',
                COLOR: '',
                IMPUESTO: '',
                PRESENTACION: '',
                MONEDA: '',
                DESCUENTO: '',
                OBSERVACION: '',
                STOCK_MIN: '',
                CANT_MAYORISTA: '' 
          	},
          	lotes: {
          		LOTE: '', 
          		CANTIDAD_INICIAL: '', 
          		CANTIDAD: '', 
          		COSTO: '', 
          		FECALTAS: ''
          	},
          	proveedores: {
          		CANTIDAD: '',
          		PROVEEDOR: '',
          		NOMBRE: '',
          		FECALTAS: ''
          	},
          	importados: {
          		DESCRIPCION: '',
          		CANTIDAD_TRANSFERENCIA: '',
          		CANTIDAD: ''
          	},
          	gondolas: {
          		CODIGO: '',
          		DESCRIPCION: ''
          	},
          	enviadas: {

          	}      
         }
      },
      watch: { 
        codigo: function(newVal, oldVal) { 
            this.obtener_datos(newVal);
        }
      }, 
      methods: {
      	mostrar(){

      		// ------------------------------------------------------------------------

      		// MOSTRAR MODAL

      		$('.modal-detalle').modal('show');

      		// ------------------------------------------------------------------------

      		// FIJAR TAB LOTE

      		$('#informacion-tab').trigger('click');

      		// ------------------------------------------------------------------------

      	},
      	obtener_datos(valor){

      		// ------------------------------------------------------------------------

      		let me = this;

      		// ------------------------------------------------------------------------

      		// LLAMAR DATOS 

      		Common.obtenerProductoDetalleCommon(valor).then(data => {
           		me.producto = data.producto;
           		me.producto.IMAGEN = data.imagen; 
           	}).catch((err) => {
           		
           	});

      		// ------------------------------------------------------------------------

      	},
      	obtenerGondolas(){

      		// ------------------------------------------------------------------------

      		let me = this;

      		// ------------------------------------------------------------------------

      		// LLAMAR DATOS 

      		Common.obtenerGondolasProductoCommon(me.codigo).then(data => {
           		me.gondolas = data;
           	}).catch((err) => {
           		
           	});

      		// ------------------------------------------------------------------------

      	},
      	obtenerLotes(){

      		// ------------------------------------------------------------------------

      		let me = this;

      		// ------------------------------------------------------------------------

      		// LLAMAR DATOS 

      		Common.obtenerLotesConCantidadCommon(me.codigo).then(data => {
           		me.lotes = data;
           	}).catch((err) => {
           		
           	});

      		// ------------------------------------------------------------------------
      	},
      	obtenerProveedores(){

      		// ------------------------------------------------------------------------

      		let me = this;

      		// ------------------------------------------------------------------------

      		// LLAMAR DATOS 

      		Common.obtenerProveedoresProductoCommon(me.codigo).then(data => {
           		me.proveedores = data;
           	}).catch((err) => {
           		
           	});

      		// ------------------------------------------------------------------------
      	},
      	obtenerTransferenciaProductos(){

      		// ------------------------------------------------------------------------

      		let me = this;

      		// ------------------------------------------------------------------------

      		// LLAMAR DATOS 

      		Common.obtenerTransferenciaProductoCommon(me.codigo).then(data => {
           		me.importados = data.importados;
           		me.enviadas = data.enviadas;
           	}).catch((err) => {
           		
           	});

      		// ------------------------------------------------------------------------

      	}
      },
        mounted() {
        	
        	// ------------------------------------------------------------------------

        	// ABRIR EL MODAL

            

            // ------------------------------------------------------------------------   
        }
    }
</script>
<style>
.emp-profile{
    padding: 3%;
    margin-top: 3%;
    margin-bottom: 3%;
    border-radius: 0.5rem;
    background: #fff;
}
.profile-img{
    text-align: center;
}
.profile-img img{
    width: 70%;
    height: 100%;
}
.profile-img .file {
    position: relative;
    overflow: hidden;
    margin-top: -20%;
    width: 70%;
    border: none;
    border-radius: 0;
    font-size: 15px;
    background: #212529b8;
}
.profile-img .file input {
    position: absolute;
    opacity: 0;
    right: 0;
    top: 0;
}
.profile-head h5{
    color: #333;
}
.profile-head h6{
    color: #0062cc;
}
.producto-edit-btn{
    border: none;
    border-radius: 1.5rem;
    width: 70%;
    padding: 2%;
    font-weight: 600;
    color: #6c757d;
    cursor: pointer;
}
.proile-rating{
    font-size: 12px;
    color: #818182;
    margin-top: 5%;
}
.proile-rating span{
    color: #495057;
    font-size: 15px;
    font-weight: 600;
}
.profile-head .nav-tabs{
    margin-bottom:5%;
}
.profile-head .nav-tabs .nav-link{
    font-weight:600;
    border: none;
}
.profile-head .nav-tabs .nav-link.active{
    border: none;
    border-bottom:2px solid #0062cc;
}
.profile-work{
    padding: 14%;
    margin-top: -15%;
}
.profile-work p{
    font-size: 12px;
    color: #818182;
    font-weight: 600;
    margin-top: 10%;
}
.profile-work .detalle {
    text-decoration: none;
    color: #495057;
    font-weight: 600;
    font-size: 14px;
}
.profile-work ul{
    list-style: none;
}
.informacion-tab label{
    font-weight: 600;
}
.informacion-tab p{
    font-weight: 600;
    color: #0062cc;
}
.cuerpoTabla {
	font-size: 12px;
}
.card-subtitle {
	font-size: 12px;
}
</style>