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

function sumarCommon(valor_a, valor_b, candec){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES 

			var suma = 0;

			// ------------------------------------------------------------------------

			// QUITAR COMAS 

			valor_a = quitarComaCommon(valor_a);
			valor_b = quitarComaCommon(valor_b);

			// ------------------------------------------------------------------------

			// REALIZAR SUMA 

			suma = parseFloat(valor_a) + parseFloat(valor_b);

			// ------------------------------------------------------------------------

			// RETORNAR VALOR

			return darFormatoCommon(suma, candec);

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

function dividirCommon(valor_a, valor_b, candec){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES 

			var division = 0;

			// ------------------------------------------------------------------------

			// QUITAR COMAS 

			valor_a = quitarComaCommon(valor_a);
			valor_b = quitarComaCommon(valor_b);

			// ------------------------------------------------------------------------

			// REALIZAR RESTA 

			division = valor_a / valor_b;

			// ------------------------------------------------------------------------

			// RETORNAR VALOR

			return darFormatoCommon(division, candec);

			// ------------------------------------------------------------------------

}

function saldoCommon(valor_a, valor_b, candec){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			var resta = 0;

			// ------------------------------------------------------------------------

			// RESTAR 

			resta = restarCommon(valor_a, valor_b, candec);

			// ------------------------------------------------------------------------

			// QUITAR COMA 

			resta = quitarComaCommon(resta);

			// ------------------------------------------------------------------------

			if (resta > 0) {
				return darFormatoCommon(resta, candec);
			} else {
				return darFormatoCommon(0, candec);
			}

			// ------------------------------------------------------------------------

}

function ocultarBoton(valor){

			// ------------------------------------------------------------------------

			//	OCULTAR BOTON 

			document.getElementById(''+valor+'').disabled = true;
			//document.getElementById(''+valor+'').style.visibility = 'disabled';

			// ------------------------------------------------------------------------

}

function formatDateCommon(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) 
        month = '0' + month;
    if (day.length < 2) 
        day = '0' + day;

    return [year, month, day].join('-');
}

function formulaCommon(formula, a, b, candec, moneda_principal, moneda_valor){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES 

			let me = this;
			var valor = 0;

			// ------------------------------------------------------------------------

			if (moneda_principal === moneda_valor) {
				valor = darFormatoCommon(a, candec);
			} else if (formula === '*') {
				valor = me.multiplicarCommon(a, b, candec);
			} else if (formula === '/') {
				valor = me.dividirCommon(a, b, candec);
			}

			// ------------------------------------------------------------------------

			return valor;

			// ------------------------------------------------------------------------

}

function calcularCotizacionRestaCommon(a, b, candec, moneda_principal, moneda_valor, cotizacion) {

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES 

			let me = this;
			var valor = 0; 

			if (moneda_principal === moneda_valor) {
				console.log("entre");
				console.log(a+' '+b+' '+candec);
				valor = me.restarCommon(a, b, candec);
				console.log(valor);
			} 

			return valor;

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 								 PRODUCTOS
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function existeProductoDataTableCommon(tabla, codigo, tipo_respuesta){

			// ------------------------------------------------------------------------

			// TIPO_RESPUESTO

			// REVISAR SI EXISTE VALORES REPETIDOS EN TABLA TRANSFERENCIAS 
            // LA OPCION 1 ES PARA DEVOLVER SOLO TRUE O FALSE SI EXISTE O NO
            // LA OPCION 2 ES PARA DEVOLVER MAS DATOS DEL PRODUCTO 

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

        	var valor = { 'respuesta': false };

        	// ------------------------------------------------------------------------

        	//	REVISAR SI PRODUCTO EXISTE EN DATATABLE 

			tabla.rows().every(function(){
				var data = this.data();
				    if (data['CODIGO'] === codigo) {
				    	if (tipo_respuesta === 1) {
				    		valor = { 'respuesta': true };
				    	} else if (tipo_respuesta === 2) {
				    		// ESTA SECCION PERTENECE A TRANSFERENCIA
				    		valor =  {
				    			'respuesta': true,
				    			'cantidad': data['CANTIDAD'],
				    			'precio': data['PRECIO'],
				    			'iva': data['IVA'],
				    			'stock': data['STOCK'],
				    			'row': tabla.row( this )
				    		};
				    	} else if (tipo_respuesta === 3) {
				    		// ESTA SECCION PERTENECE A COMPRA
				    		valor = {
				    			'respuesta': true,
				    			'cantidad': data['CANTIDAD'],
				    			'costo': data['COSTO'],
				    			'row': tabla.row( this )
				    		}
				    	}
				    	
				    } 
			});

			// ------------------------------------------------------------------------

			// RETORNAR TRUE SI SE SE ENCONTRO CODIGO IGUAL O FALSE SI NO SE ENCONTRO NADA

			return valor;

			// ------------------------------------------------------------------------

}


function existeProductoLoteDataTableCommon(tabla, codigo, lote, tipo_respuesta){

			// ------------------------------------------------------------------------

			// TIPO_RESPUESTO

			// REVISAR SI EXISTE VALORES REPETIDOS EN TABLA TRANSFERENCIAS 
            // LA OPCION 1 ES PARA DEVOLVER SOLO TRUE O FALSE SI EXISTE O NO
            // LA OPCION 2 ES PARA DEVOLVER MAS DATOS DEL PRODUCTO 

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

        	var valor = { 'respuesta': false };

        	// ------------------------------------------------------------------------

        	//	REVISAR SI PRODUCTO EXISTE EN DATATABLE 

			tabla.rows().every(function(){
				var data = this.data();
				    if (data['CODIGO'] === codigo && data['LOTE'] === lote) {
				    	if (tipo_respuesta === 1) {
				    		valor = { 'respuesta': true };
				    	} else if (tipo_respuesta === 2) {
				    		// ESTA SECCION PERTENECE A TRANSFERENCIA
				    		valor =  {
				    			'respuesta': true,
				    			'cantidad': data['CANTIDAD'],
				    			'precio': data['PRECIO'],
				    			'iva': data['IVA'],
				    			'stock': data['STOCK'],
				    			'row': tabla.row( this )
				    		};
				    	} else if (tipo_respuesta === 3) {
				    		// ESTA SECCION PERTENECE A COMPRA
				    		valor = {
				    			'respuesta': true,
				    			'cantidad': data['CANTIDAD'],
				    			'costo': data['COSTO'],
				    			'row': tabla.row( this )
				    		}
				    	}
				    	
				    } 
			});

			// ------------------------------------------------------------------------

			// RETORNAR TRUE SI SE SE ENCONTRO CODIGO IGUAL O FALSE SI NO SE ENCONTRO NADA

			return valor;

			// ------------------------------------------------------------------------

}

function cantidadSuperadaCommon(a, b){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

        	var valor = false;

        	// ------------------------------------------------------------------------

        	//	REVISAR SI PRODUCTO EXISTE EN DATATABLE 

			if (parseFloat(a) > parseFloat(b)) {
	            var valor = true;	
	       	} 

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
function filtrarUsuariosCommon(codigo){

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


function obtenerProductoDetalleCommon(codigo){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DIEZ PRIMEROS DATOS DE ACUERDO AL TIPEAR EL TEXTBOX 

			return axios.post('/productoDetalle', {'codigo': codigo}).then(function (response) {
						return response.data;
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

function obtenerProveedoresProductoCommon(codigo){

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DATOS DE LA CABECERA DE TRANSFERENCIA
			
			return axios.post('/productoConProveedor', {'codigo': codigo}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

function obtenerTransferenciaProductoCommon(codigo){

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DATOS DE LA CABECERA DE TRANSFERENCIA
			
			return axios.post('/producto/transferencia', {'codigo': codigo}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

function eliminarProductoCommon(codigo) {

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DATOS DE LA CABECERA DE TRANSFERENCIA
			
			return axios.post('/producto/eliminar', {'codigo': codigo}).then(function (response) {
				return response.data;
			});

			// ------------------------------------------------------------------------

}

function obtenerProductoCompraCommon(codigo, moneda){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/productoCompra', {'codigo': codigo, 'moneda': moneda}).then(function (response) {
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


function generarPdfFacturaTransferenciaCommon(data){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios({url: 'pdf-generar-factura', method: 'post', responseType: 'arraybuffer', data: {'codigo': data}}).then( 
				(response) => {
					const url = window.URL.createObjectURL(new Blob([response.data], {type: 'application/pdf'}));
					const link = document.createElement('a');
					link.href = url;
					//DESCARGAR
					// link.setAttribute('download', 'file.pdf');
					// document.body.appendChild(link);
					link.target = '_blank'
					link.click();
				},
				(error) => { return error }
			);

			// ------------------------------------------------------------------------

}

function generarRptPdfTransferenciaCommon(codigo, codigo_origen){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios({url: 'pdf-generar-transferencia', method: 'post', responseType: 'blob', data: {'codigo': codigo, 'codigo_origen': codigo_origen}}).then(function (response) {
					const url = window.URL.createObjectURL(new Blob([response.data]));
					const link = document.createElement('a');
					link.href = url;
					link.target = '_blank'
					link.click();
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

function obtenerCabeceraTransferenciaCommon(codigo, codigo_origen){

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DATOS DE LA CABECERA DE TRANSFERENCIA
			
			return axios.post('/transferenciaCabecera', {'codigo': codigo, 'codigo_origen': codigo_origen}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

function obtenerCuerpoTransferenciaCommon(codigo, codigo_origen){

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DATOS DE LA CABECERA DE TRANSFERENCIA
			
			return axios.post('/transferenciaCuerpo', {'codigo': codigo, 'codigo_origen': codigo_origen}).then(function (response) {
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

function guardarModificarCompraCommon(data){

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DATOS DE LA CABECERA DE TRANSFERENCIA
			
			return axios.post('/compra/guardar-modificar', {'data': data}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

function obtenerCodigoCompraCommon(){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DIEZ PRIMEROS DATOS DE ACUERDO AL TIPEAR EL TEXTBOX 

			return axios.get('/compra/codigo').then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------

}

function eliminarCompraCommon(data){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// ELIMINAR LA COMPRA, ENVIAR OPCION 1 PARA ELIMINAR EL REGISTRO DE LA TABLA COMPRA - 2 PARA ELIMINAR SOLO COMPRASDET
			
			return axios.post('/compra/eliminar', {'data': data, 'opcion': 1}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

function generarRptPdfCompraCommon(codigo){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios({url: 'compra/pdf', method: 'post', responseType: 'blob', data: {'codigo': codigo}}).then(function (response) {
					const url = window.URL.createObjectURL(new Blob([response.data]));
					const link = document.createElement('a');
					link.href = url;
					link.target = '_blank'
					link.click();
			});

			// ------------------------------------------------------------------------

}

function obtenerCabeceraCompraCommon(codigo){

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DATOS DE LA CABECERA DE COMPRA
			
			return axios.post('/compra/cabecera', {'codigo': codigo}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

function obtenerCuerpoCompraCommon(codigo){

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DATOS DE LA CABECERA DE COMPRA
			
			return axios.post('/compra/cuerpo', {'codigo': codigo}).then(function (response) {
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

function obtenerSubCategoriaCommon(categoria){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DIEZ PRIMEROS DATOS DE ACUERDO AL TIPEAR EL TEXTBOX 

			return axios.post('/subCategoria', {categoria: categoria}).then(function (response) {
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

function obtenerMarcaCommon(categoria){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DIEZ PRIMEROS DATOS DE ACUERDO AL TIPEAR EL TEXTBOX 

			return axios.post('/marca', {categoria: categoria}).then(function (response) {
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

function guardarPagoProveedorCommon(data) {

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DIEZ PRIMEROS DATOS DE ACUERDO AL TIPEAR EL TEXTBOX 

			return axios.post('/proveedor/pago', {data: data}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}


function guardarDevolucionProveedorCommon(data) {

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DIEZ PRIMEROS DATOS DE ACUERDO AL TIPEAR EL TEXTBOX 

			return axios.post('/proveedor/devolucion', {data: data}).then(function (response) {
					return response.data;
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

function obtenerGondolasProductoCommon(codigo){

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DATOS DE LA CABECERA DE TRANSFERENCIA
			
			return axios.post('/gondola/producto', {'codigo': codigo}).then(function (response) {
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
// 							 	ROLES
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function llamarRolesCommon(){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// LLAMAR ROL

			return axios.get('/rolTraer').then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------

}

function traerRolUsuarioCommon(id){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// LLAMAR ROL
			return axios.post('/rolUsuarioTraer', {'id': id}).then(function (response) {
					return response.data;
			});


			// ------------------------------------------------------------------------

}

function llamarPermisoCommon(){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// LLAMAR ROL

			return axios.get('/permisoTraer').then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------

}
function filtrarRolesCommon(id){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// LLAMAR ROL
			return axios.post('/rolFiltrar', {'id': id}).then(function (response) {
					return response.data;
			});


			// ------------------------------------------------------------------------

}
function filtrarPermisosCommon(id){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// LLAMAR ROL
			return axios.post('/permisoFiltrar', {'id': id}).then(function (response) {
					return response.data;
			});

}
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							      LOTES
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function obtenerLotesConCantidadCommon(codigo){

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DATOS DE LA CABECERA DE TRANSFERENCIA
			
			return axios.post('/lotesConCantidad', {'codigo': codigo}).then(function (response) {
					return response.data.lote;
			});

			// ------------------------------------------------------------------------

}
function filtrarMarcasCommon(id){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// LLAMAR ROL
			return axios.post('/marcaFiltrar', {'id': id}).then(function (response) {
					return response.data;
			});


			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							      ROLES
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function guardarRolCommon(nombre,descripcion,permisos,existe,id){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// LLAMAR ROL

			return axios.post('/rolGuardar', {'nombre':nombre,'descripcion':descripcion,'permisos':permisos,'existe':existe,'id':id}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------

}
function asignarRolCommon(id,roles){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// URL ASIGNAR ROL

			return axios.post('/rolAsignar', {'id':id,'roles':roles}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------

}
function asignarPermisoCommon(id,permisos){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// URL ASIGNAR ROL

			return axios.post('/permisoAsignar', {'id':id,'permisos':permisos}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							 	PERMISOS
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
function guardarPermisoCommon(nombre,descripcion,existe,id){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/PermisoGuardar', {'nombre':nombre,'descripcion':descripcion,'existe':existe,'id':id}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function guardarMarcaCommon(data){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/marcaGuardar', {'data':data}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}

function eliminarMarcaCommon(data){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/marcaEliminar', {'data':data}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function guardarUsuarioCommon(nombre,email,contraseña){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/usuarioGuardar', {'nombre':nombre,'email':email,'contraseña':contraseña	}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function nuevaMarcaCommon(){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.get('/nuevaMarca').then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function filtrarColoresCommon(id){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// LLAMAR ROL
			return axios.post('/colorFiltrar', {'id': id}).then(function (response) {
					return response.data;
			});


			// ------------------------------------------------------------------------

}
function nuevoColorCommon(){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.get('/nuevoColor').then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function guardarColorCommon(data){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/colorGuardar', {'data':data}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function eliminarColorCommon(data){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/colorEliminar', {'data':data}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function filtrarTallesCommon(id){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// LLAMAR ROL
			return axios.post('/talleFiltrar', {'id': id}).then(function (response) {
					return response.data;
			});


			// ------------------------------------------------------------------------

}
function nuevoTalleCommon(){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.get('/nuevoTalle').then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function guardarTalleCommon(data){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/talleGuardar', {'data':data}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function eliminarTalleCommon(data){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/talleEliminar', {'data':data}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function filtrarTelasCommon(id){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// LLAMAR TELAS
			return axios.post('/telaFiltrar', {'id': id}).then(function (response) {
					return response.data;
			});


			// ------------------------------------------------------------------------

}
function guardarTelaCommon(data){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/telaGuardar', {'data':data}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function nuevaTelaCommon(){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.get('/nuevaTela').then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}

function eliminarTelaCommon(data){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/telaEliminar', {'data':data}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function filtrarCategoriasCommon(id){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// LLAMAR TELAS
			return axios.post('/categoriaFiltrar', {'id': id}).then(function (response) {
					return response.data;
			});


			// ------------------------------------------------------------------------

}
function nuevaCategoriaCommon(){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.get('/nuevaCategoria').then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function guardarCategoriaCommon(data){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/categoriaGuardar', {'data':data}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function filtrarSubCategoriasCommon(id){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// LLAMAR TELAS
			return axios.post('/subCategoriaFiltrar', {'id': id}).then(function (response) {
					return response.data;
			});


			// ------------------------------------------------------------------------

}
function nuevaSubCategoriaCommon(){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.get('/nuevaSubCategoria').then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function guardarSubCategoriaCommon(data){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/subCategoriaGuardar', {'data':data}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function eliminarSubCategoriaCommon(data){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/subCategoriaEliminar', {'data':data}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function eliminarCategoriaCommon(data){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/CategoriaEliminar', {'data':data}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function filtrarGondolasCommon(id){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// LLAMAR TELAS
			return axios.post('/gondolasFiltrar', {'id': id}).then(function (response) {
					return response.data;
			});


			// ------------------------------------------------------------------------

}
function nuevaGondolaCommon(){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.get('/nuevaGondola').then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function guardarGondolaCommon(data){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/gondolaGuardar', {'data':data}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function eliminarGondolaCommon(data){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/gondolaEliminar', {'data':data}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							 	CUENTAS
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function datosNotaCuentaCommon(codigo){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/deuda/datosNota', {'codigo': codigo}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							 PAGOS PROVEEDOR
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function datosPagoUnicoCommon(id){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/pagos_prov/pagoUnico', {'id': id}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							    FORMAS PAGO
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function cotizacionyMonedaFormaPagoCommon() {

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// OBTENER COTIZACION DE COMPRA

			return axios.get('/cotizacion/compra-dia').then(function (response) {
					return response.data;
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
		filtrarUsuariosCommon,
		filtrarRolesCommon, 
		filtrarPermisosCommon, 
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
		obtenerProductoCommon,
		llamarRolesCommon,
		llamarPermisoCommon,
		guardarRolCommon,
		asignarRolCommon,
		guardarMarcaCommon,
		asignarPermisoCommon,
		traerRolUsuarioCommon,
		guardarPermisoCommon,
		guardarUsuarioCommon,
		cantidadSuperadaCommon,
		obtenerProductoDetalleCommon,
		obtenerLotesConCantidadCommon,
		obtenerProveedoresProductoCommon,
		obtenerTransferenciaProductoCommon,
		obtenerGondolasProductoCommon,
		eliminarProductoCommon,
		generarPdfFacturaTransferenciaCommon,
		generarRptPdfTransferenciaCommon,
		nuevaMarcaCommon,
		filtrarMarcasCommon, 
		eliminarMarcaCommon,
		filtrarColoresCommon,
		guardarColorCommon,
		eliminarColorCommon,
		nuevoColorCommon,
		filtrarTallesCommon,
		guardarTalleCommon,
		eliminarTalleCommon,
		nuevoTalleCommon,
		filtrarTelasCommon,
		guardarTelaCommon,
		eliminarTelaCommon,
		nuevaTelaCommon,
		filtrarCategoriasCommon,
		nuevaCategoriaCommon,
		guardarCategoriaCommon,
		eliminarCategoriaCommon,
		filtrarSubCategoriasCommon,
		nuevaSubCategoriaCommon, 
		guardarSubCategoriaCommon,
		eliminarSubCategoriaCommon,
		filtrarGondolasCommon,
		nuevaGondolaCommon,
		guardarGondolaCommon,
		eliminarGondolaCommon,
		obtenerProductoCompraCommon,
		guardarModificarCompraCommon,
		dividirCommon,
		sumarCommon,
		obtenerCodigoCompraCommon,
		eliminarCompraCommon,
		generarRptPdfCompraCommon,
		obtenerCabeceraCompraCommon,
		obtenerCuerpoCompraCommon,
		formatDateCommon,
		datosNotaCuentaCommon,
		cotizacionyMonedaFormaPagoCommon,
		formulaCommon,
		calcularCotizacionRestaCommon,
		saldoCommon,
		guardarPagoProveedorCommon,
		datosPagoUnicoCommon,
		guardarDevolucionProveedorCommon,
		existeProductoLoteDataTableCommon
		};