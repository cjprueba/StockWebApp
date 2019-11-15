// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 								   UTILES
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------


function filtrarCommon(array, codigo) {

			// ------------------------------------------------------------------------

			// FILTRAR ARRAY 

            return array.filter(function(item) {
              return item.CODIGO === codigo;
            })

            // ------------------------------------------------------------------------
}

function darFormatoCommon(valor, candec) {

			// ------------------------------------------------------------------------

            // INICIAR VARIABLES

            let me = this;

            // ------------------------------------------------------------------------

            // REVISAR LA CANTIDAD DE DECIMALES PARA DAR FORMATO A PRECIO

            if (candec === 0) {
                valor = numeral(valor).format('0,0');
            } else {
                valor = numeral(valor).format('0,0.00');
            }  

            return valor;

            // ------------------------------------------------------------------------

}


function quitarComaCommon(valor) {

			// ------------------------------------------------------------------------

            // QUITAR LA COMA DE LOS VALORES

            valor = valor.toString().replace(/,/g,"");
            return valor;

            // ------------------------------------------------------------------------

}

function calcularIVACommon(valor, porcentaje, candec) {

			// ------------------------------------------------------------------------

			//	CALCULAR IVA DE ACUERDO AL PORCENTAJE
			
			if (porcentaje === 10) {
				valor = darFormatoCommon(quitarComaCommon(valor) / 11, candec);
			} else if (porcentaje === '5') {
				valor = darFormatoCommon(quitarComaCommon(valor) / 21, candec);
			}

			// ------------------------------------------------------------------------

			return valor;

			// ------------------------------------------------------------------------

}

function restarCommon(valor_a, valor_b, candec){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES 

			var resta = 0;

			// ------------------------------------------------------------------------

			// QUITAR COMAS 

			valor_a = quitarComaCommon(valor_a);
			valor_b = quitarComaCommon(valor_b);

			// ------------------------------------------------------------------------

			// REALIZAR RESTA 

			resta = valor_a - valor_b;

			// ------------------------------------------------------------------------

			// RETORNAR VALOR

			return darFormatoCommon(resta, candec);

			// ------------------------------------------------------------------------

}

function multiplicarCommon(valor_a, valor_b, candec){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES 

			var multiplicacion = 0;

			// ------------------------------------------------------------------------

			// QUITAR COMAS 

			valor_a = quitarComaCommon(valor_a);
			valor_b = quitarComaCommon(valor_b);

			// ------------------------------------------------------------------------

			// REALIZAR RESTA 

			multiplicacion = valor_a * valor_b;

			// ------------------------------------------------------------------------

			// RETORNAR VALOR

			return darFormatoCommon(multiplicacion, candec);

			// ------------------------------------------------------------------------

}

function ocultarBoton(valor){

			// ------------------------------------------------------------------------

			//	OCULTAR BOTON 

			document.getElementById(''+valor+'').disabled = true;
			//document.getElementById(''+valor+'').style.visibility = 'disabled';

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 								 PRODUCTOS
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function existeProductoDataTableCommon(tabla, codigo){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

        	var valor = false;

        	// ------------------------------------------------------------------------

        	//	REVISAR SI PRODUCTO EXISTE EN DATATABLE 

			tabla.rows().every(function(){
				var data = this.data();
				    if (data['CODIGO'] === codigo) {
				    	valor = true;
				    } 
			});

			// ------------------------------------------------------------------------

			// RETORNAR TRUE SI SE SE ENCONTRO CODIGO IGUAL O FALSE SI NO SE ENCONTRO NADA

			return valor;

			// ------------------------------------------------------------------------

}

function filtrarProductosCommon(codigo){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DIEZ PRIMEROS DATOS DE ACUERDO AL TIPEAR EL TEXTBOX 

			return axios.post('/producto', {'codigo': codigo, 'Opcion': 3}).then(function (response) {
						return response.data.producto;
					});

			// ------------------------------------------------------------------------

}

function codigoInternoCommon(codigo){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;
			var producto = '';

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/producto', {'codigo': codigo, 'Opcion': 4}).then(function (response) {
					if(response.data.producto === 0) {
						return '0';
					} else {
						return response.data.producto[0].CODIGO;
					}
			});

			// ------------------------------------------------------------------------

}


function calcularCotizaciónPrecioCommon(precioProducto, monedaProducto, monedaSistema, dec, tab_unica){


	        // ------------------------------------------------------------------------

			// CONSEGUIR LA COTIZACION DEL PRECIO
				
			return axios.post('/cotizacion', {'precio': precioProducto, 'monedaProducto': monedaProducto, 'monedaSistema': monedaSistema, 'decSistema': dec, 'tab_unica': tab_unica}).then(function (response) {
					return response.data.valor;
			});

			// ------------------------------------------------------------------------

}


function guardarProductoCommon(datos){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/productoGuardar', {'datos': datos}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}


function obtenerProductoCommon(codigo, tipo){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/productoObtener', {'codigo': codigo, 'tipo': tipo}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							  TRANSFERENCIAS
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function guardarTransferenciaCommon(data, cabecera){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/transferenciaG', {'data': data, 'cabecera': cabecera}).then(function (response) {
					console.log(response.data);
					return response.data;
			});

			// ------------------------------------------------------------------------

}

function eliminarTransferenciaCommon(data){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/transferenciaEliminar', {'data': data}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

function enviarTransferenciaCommon(data){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/transferenciaEnviar', {'data': data}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}


function modificarTransferenciaCommon(codigo, data, cabecera){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/transferenciaModificar', {'codigo': codigo, 'data': data, 'cabecera': cabecera}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

function obtenerCabeceraTransferenciaCommon(codigo){

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DATOS DE LA CABECERA DE TRANSFERENCIA
			
			return axios.post('/transferenciaCabecera', {'codigo': codigo}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

function obtenerCuerpoTransferenciaCommon(codigo){

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DATOS DE LA CABECERA DE TRANSFERENCIA
			
			return axios.post('/transferenciaCuerpo', {'codigo': codigo}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

function rechazarTransferenciaCommon(codigo, codigo_origen){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/transferenciaRechazar', {'codigo': codigo, 'codigo_origen': codigo_origen}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

function importarTransferenciaCommon(codigo, codigo_origen){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/transferenciaImportar', {'codigo': codigo, 'codigo_origen': codigo_origen}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							  COTIZACIONES
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function obtenerCotizacionDia(codigo){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DIEZ PRIMEROS DATOS DE ACUERDO AL TIPEAR EL TEXTBOX 

			return axios.get('/cotizacion').then(function (response) {
						return response.data;
					});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							  SUCURSALES
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function obtenerSucursalesCommon(){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DIEZ PRIMEROS DATOS DE ACUERDO AL TIPEAR EL TEXTBOX 

			return axios.get('/sucursal').then(function (response) {
					return response.data.sucursales;
				});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							  INVENTARIOS
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function agregarInventarioCommon(codigo, id, cantidad) {

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/inventarioAgregarEditarProducto', {'codigo': codigo, 'id': id, 'cantidad': cantidad}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}


function guardarInventarioCommon(sucursal, observacion) {

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/inventarioGuardar', {'sucursal': sucursal, 'observacion': observacion}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							     COMPRAS
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function obtenerProductosComprasDetCommon(nro_caja){

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DATOS DE LA CABECERA DE TRANSFERENCIA
			
			return axios.post('/comprasDetProductos', {'nro_caja': nro_caja}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 								 CATEGORIAS
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function obtenerCategoriasCommon(){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DIEZ PRIMEROS DATOS DE ACUERDO AL TIPEAR EL TEXTBOX 

			return axios.get('/categoria').then(function (response) {
					return response.data.categorias;
				});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							 SUB CATEGORIAS
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function obtenerSubCategoriaCommon(){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DIEZ PRIMEROS DATOS DE ACUERDO AL TIPEAR EL TEXTBOX 

			return axios.get('/subCategoria').then(function (response) {
					return response.data.subCategorias;
				});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							 	COLOR
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function obtenerColorCommon(){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DIEZ PRIMEROS DATOS DE ACUERDO AL TIPEAR EL TEXTBOX 

			return axios.get('/color').then(function (response) {
					return response.data.colores;
				});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							 	TELA
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function obtenerTelaCommon(){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DIEZ PRIMEROS DATOS DE ACUERDO AL TIPEAR EL TEXTBOX 

			return axios.get('/tela').then(function (response) {
					return response.data.telas;
				});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							 	TALLE
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function obtenerTalleCommon(){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DIEZ PRIMEROS DATOS DE ACUERDO AL TIPEAR EL TEXTBOX 

			return axios.get('/talle').then(function (response) {
					return response.data.talles;
				});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							 	GENERO
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function obtenerGeneroCommon(){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DIEZ PRIMEROS DATOS DE ACUERDO AL TIPEAR EL TEXTBOX 

			return axios.get('/genero').then(function (response) {
					return response.data.generos;
				});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							 	MARCA
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function obtenerMarcaCommon(){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DIEZ PRIMEROS DATOS DE ACUERDO AL TIPEAR EL TEXTBOX 

			return axios.get('/marca').then(function (response) {
					return response.data.marcas;
				});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							 	PROVEEDORES
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function obtenerProveedorCommon(){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DIEZ PRIMEROS DATOS DE ACUERDO AL TIPEAR EL TEXTBOX 

			return axios.get('/proveedor').then(function (response) {
					return response.data.proveedores;
				});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							 	GONDOLAS
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function obtenerGondolaCommon(){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DIEZ PRIMEROS DATOS DE ACUERDO AL TIPEAR EL TEXTBOX 

			return axios.get('/gondola').then(function (response) {
					return response.data.gondolas;
				});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							 	MONEDAS
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function obtenerMonedaCommon(){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DIEZ PRIMEROS DATOS DE ACUERDO AL TIPEAR EL TEXTBOX 

			return axios.get('/moneda').then(function (response) {
					return response.data.monedas;
			});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							 	CODIGOS
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function generarCodigoInternoCommon(){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GENERAR CODIGO INTERNO

			return axios.get('/productoCodigoInterno').then(function (response) {
					return response.data.codigo_interno;
				});

			// ------------------------------------------------------------------------

}


function generarCodigoCommon(){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GENERAR CODIGO INTERNO

			return axios.get('/productoCodigo').then(function (response) {
					return response.data.codigo;
				});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							EXPORTAR FUNCIONES
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

export {
		obtenerSubCategoriaCommon,
		darFormatoCommon, 
		quitarComaCommon, 
		calcularIVACommon, 
		restarCommon, 
		multiplicarCommon, 
		existeProductoDataTableCommon, 
		filtrarProductosCommon, 
		codigoInternoCommon, 
		calcularCotizaciónPrecioCommon, 
		guardarTransferenciaCommon, 
		obtenerCotizacionDia,
		obtenerCabeceraTransferenciaCommon,
		obtenerCuerpoTransferenciaCommon,
		ocultarBoton,
		modificarTransferenciaCommon,
		rechazarTransferenciaCommon,
		importarTransferenciaCommon,
		eliminarTransferenciaCommon,
		enviarTransferenciaCommon,
		agregarInventarioCommon,
		obtenerSucursalesCommon,
		guardarInventarioCommon,
		obtenerProductosComprasDetCommon,
		obtenerCategoriasCommon,
		obtenerColorCommon,
		obtenerTelaCommon,
		obtenerTalleCommon,
		obtenerGeneroCommon,
		obtenerMarcaCommon,
		obtenerProveedorCommon,
		obtenerGondolaCommon,
		obtenerMonedaCommon,
		generarCodigoInternoCommon,
		generarCodigoCommon,
		filtrarCommon,
		guardarProductoCommon,
		obtenerProductoCommon
		};