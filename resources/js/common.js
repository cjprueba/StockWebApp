// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 								   UTILES
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------


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
		enviarTransferenciaCommon
		};