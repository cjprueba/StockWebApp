// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							    FILTRAR CLIENTE
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function filtrarClienteCommon(data){
	
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/clienteFiltrar', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							    GUARDAR CLIENTE
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function guardarClienteCommon(data){
	
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/clienteGuardar', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							    DATOS CLIENTE
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function obtenerCreditoClienteCommon(data){
	
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/cliente/credito', {'data': data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							    ELIMINAR CLIENTE
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function eliminarClienteCommon(data){
	
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/clienteEliminar', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}
function clienteNuevoCommon(){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.get('/nuevoCliente').then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 								   UTILES
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function filtrarTransporteCommon(id){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// LLAMAR TELAS
			return axios.post('/transporteFiltrar', {'id': id}).then(function (response) {
					return response.data;
			});


			// ------------------------------------------------------------------------

}
function nuevoTransporteCommon(){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// NUEVO TRANSPORTE

			return axios.get('/nuevoTransporte').then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function guardarTransporteCommon(data){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/transporteGuardar', {'data':data}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function eliminarTransporteCommon(data){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/transporteEliminar', {'data':data}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}

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
				/*console.log(valor);
				console.log(porcentaje);
				console.log(candec);*/
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

function descuentoCommon(valor_a, valor_b, candec){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES 

			var valor = 0;

			// ------------------------------------------------------------------------

			valor = dividirCommon(multiplicarCommon(valor_a, valor_b, candec), 100, candec);

			// ------------------------------------------------------------------------

			// RETORNAR VALOR

			return darFormatoCommon(valor, candec);

			// ------------------------------------------------------------------------

}

function sumarCommon(valor_a, valor_b, candec){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES 

			var suma = 0;

			// ------------------------------------------------------------------------

			// QUITAR COMAS 
			if(valor_a===undefined){
				valor_a = 0;
			}else{
				valor_a = quitarComaCommon(valor_a);
			}

			if(valor_b===undefined){
				valor_b = 0;
			}else{
				valor_b = quitarComaCommon(valor_b);
			}
			/*valor_a = quitarComaCommon(valor_a);
			valor_b = quitarComaCommon(valor_b);*/

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

			// if (moneda_valor === 1) {
			// 	alert(a);
			// 	alert(b);
			// }
			
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
				    if (data['CODIGO'] === codigo && data['TIPO'] !== 3) {
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

function mayoristaCommon(codigo_real, tabla, limite_mayorista, precio_mayorista, cantidad, decimal, rowClass) {

				// ------------------------------------------------------------------------

				var precio_total = 0;

	        	// ------------------------------------------------------------------------

	        	// CONTAR LA CANTIDAD DE PRODUCTOS CON CODIGO REAL

	        	tabla.rows().every(function(){

					var data = this.data();
				    if (data['CODIGO_REAL'] === codigo_real && codigo_real !== '' && codigo_real !== 0 && codigo_real !== '0' && codigo_real !== null && codigo_real !== 'null' && (data['DESCUENTO'] === 0 || data['DESCUENTO'] === 0.00)) {
				    	cantidad = parseInt(data['CANTIDAD']) + parseInt(cantidad);
				    }

				});

	        	// ------------------------------------------------------------------------

	        	// APLICAR VALIDAR MAYORISTA 

				if ((cantidad >= limite_mayorista) && precio_mayorista !== '' && precio_mayorista !== null) {

					// ------------------------------------------------------------------------

					// EMPEZAR A ACTUALIZAR PRECIO MAYORISTA DE TODOS LOS PRODUCTOS 

					tabla.rows().every(function(){
						var data = this.data();
					    if (data['CODIGO_REAL'] === codigo_real && (data['DESCUENTO'] === 0 || data['DESCUENTO'] === 0.00) && data['PREMAYORISTA'] !== "0" && data['PREMAYORISTA'] !== "0.00" && codigo_real !== null && codigo_real !== 'null' && codigo_real !== 0 && codigo_real !== '0') {

					    	// ------------------------------------------------------------------------

					    	// PRECIO 

					    	tabla.cell(tabla.row( this ), 8).data(data['PREMAYORISTA']).draw();

					    	// ------------------------------------------------------------------------

				            //  QUITAR COMA DE PRECIO

				            precio_total = Common.multiplicarCommon(data['CANTIDAD'], data['PREMAYORISTA'], decimal);
				         
				            // ------------------------------------------------------------------------

				            //  DAR FORMATO AL RESULTADO FINAL PARA MOSTRAR EN DATATABLE 

				            precio_total = Common.darFormatoCommon(precio_total, decimal);

				            // ------------------------------------------------------------------------

				            // PRECIO TOTAL

				            tabla.cell(tabla.row( this ), 9).data(precio_total).draw();

				            // ------------------------------------------------------------------------

				            // CALCULAR IVA

				            tabla.cell(tabla.row( this ), 7).data(Common.calcularIVACommon(precio_total, data['IVA'], decimal)).draw();
			            	
			            	// ------------------------------------------------------------------------

			            	// EDITAR FILA COLOR 

				            tabla.row( this ).draw()
				            .nodes()
			    			.to$()
			    			.addClass(rowClass);

			            	// ------------------------------------------------------------------------

					    }
					});

					// ------------------------------------------------------------------------

					return true;

					// ------------------------------------------------------------------------

				} else {
					return false;
				}

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

function inicioProductosViewNewCommon(datos){

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DATOS DE LA CABECERA DE TRANSFERENCIA
			
			return axios.post('/pedido/inicio_mostrar_new', {'datos': datos}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

function mostrarProductosViewNewCommon(datos){

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DATOS DE LA CABECERA DE TRANSFERENCIA
			
			return axios.post('/producto/mostrar_new', {'datos': datos}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

function darDeBajaProductoCommon(codigo){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// DAR DE BAJA DEL PRODUCTO

			return axios.post('/producto/baja', {'codigo': codigo}).then(function (response) {
						return response.data;
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

function nuevoProveedorCommon(){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.get('/nuevoProveedor').then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function filtrarProveedorCommon(id){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// LLAMAR TELAS
			return axios.post('/proveedorFiltrar', {'id': id}).then(function (response) {
					return response.data;
			});


			// ------------------------------------------------------------------------

}
function guardarProveedorCommon(data){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/proveedorGuardar', {'data':data}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function eliminarProveedorCommon(data){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/proveedorEliminar', {'data':data}).then(function (response) {
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


function obtenerProductoPOSCommon(codigo, moneda,cantidad,cantidad_existente){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/producto/POS', {'codigo': codigo, 'moneda': moneda,'cantidad':cantidad,'cantidad_existente':cantidad_existente}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

function obtenerServicioPOSCommon(codigo){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/servicio/POS', {'codigo': codigo}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

function existeProductoCommon(codigo){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DIEZ PRIMEROS DATOS DE ACUERDO AL TIPEAR EL TEXTBOX 

			return axios.post('/producto/existe', {'codigo': codigo}).then(function (response) {
						return response.data;
					});

			// ------------------------------------------------------------------------

}

function mostrarProductosCatalogoCommon(datos){

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DATOS DE LA CABECERA DE TRANSFERENCIA
			
			return axios.post('/producto/catalogo', {'datos': datos}).then(function (response) {
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
			
			return axios({url: 'pdf-generar-factura-transferencia', method: 'post', responseType: 'arraybuffer', data: {'codigo': data, 'codigo_origen': 0}}).then( 
				(response) => {

					// var base64data = '';

					// const url = window.URL.createObjectURL(new Blob([response.data]));
					// var reader = new FileReader();
					//  reader.readAsDataURL(new Blob([response.data])); 
					//  reader.onloadend = function() {
					//      base64data = reader.result;
					//  }

					 return response.data;
					// return var blobToBase64 = function(blob, callback) {
					//     var reader = new FileReader();
					//     reader.onload = function() {
					//         var dataUrl = reader.result;
					//         var base64 = dataUrl.split(',')[1];
					//         callback(base64);
					//     };
					//     reader.readAsDataURL(blob);
					// };
				},
				(error) => { return error }
			);

			// ------------------------------------------------------------------------

}

// function generarPdfFacturaTransferenciaCommon(data){

// 			// ------------------------------------------------------------------------

// 			// INICIAR VARIABLES

// 			let me = this;

// 			// ------------------------------------------------------------------------

// 			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
// 			return axios({url: 'pdf-generar-factura-transferencia', method: 'post', responseType: 'arraybuffer', data: {'codigo': data, 'codigo_origen': 0}}).then( 
// 				(response) => {
// 					const url = window.URL.createObjectURL(new Blob([response.data], {type: 'application/pdf'}));
// 					const link = document.createElement('a');
// 					link.href = url;
// 					//DESCARGAR
// 					// link.setAttribute('download', 'file.pdf');
// 					// document.body.appendChild(link);
// 					link.target = '_blank'
// 					link.click();
// 				},
// 				(error) => { return error }
// 			);

// 			// ------------------------------------------------------------------------

// }

function generarPdfFacturaVentaVisualizarCommon(codigo, caja,id_venta){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios({url: 'venta/factura', method: 'post', responseType: 'arraybuffer', data: {'codigo': codigo, 'caja': caja,'id_venta':id_venta}}).then( 
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

function generarPdfTicketVentaVisualizarCommon(codigo, caja,id_venta){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios({url: 'venta/ticket', method: 'post', responseType: 'arraybuffer', data: {'codigo': codigo, 'caja': caja,'id_venta':id_venta}}).then( 
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

function generarPdfFacturaVentaCommon(codigo, caja, id_venta){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios({url: 'venta/factura', method: 'post', responseType: 'arraybuffer', data: {'codigo': codigo, 'caja': caja, 'id_venta': id_venta}}).then( 
				(response) => {

					// var base64data = '';

					// const url = window.URL.createObjectURL(new Blob([response.data]));
					// var reader = new FileReader();
					//  reader.readAsDataURL(new Blob([response.data])); 
					//  reader.onloadend = function() {
					//      base64data = reader.result;
					//  }

					 return response.data;
					// return var blobToBase64 = function(blob, callback) {
					//     var reader = new FileReader();
					//     reader.onload = function() {
					//         var dataUrl = reader.result;
					//         var base64 = dataUrl.split(',')[1];
					//         callback(base64);
					//     };
					//     reader.readAsDataURL(blob);
					// };
				},
				(error) => { return error }
			);

			// ------------------------------------------------------------------------

}

function generarPdfTicketVentaCommon(codigo, caja, id_venta){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios({url: 'venta/ticket', method: 'post', responseType: 'arraybuffer', data: {'codigo': codigo, 'caja': caja, 'id_venta': id_venta}}).then( 
				(response) => {

					// var base64data = '';

					// const url = window.URL.createObjectURL(new Blob([response.data]));
					// var reader = new FileReader();
					//  reader.readAsDataURL(new Blob([response.data])); 
					//  reader.onloadend = function() {
					//      base64data = reader.result;
					//  }

					 return response.data;
					// return var blobToBase64 = function(blob, callback) {
					//     var reader = new FileReader();
					//     reader.onload = function() {
					//         var dataUrl = reader.result;
					//         var base64 = dataUrl.split(',')[1];
					//         callback(base64);
					//     };
					//     reader.readAsDataURL(blob);
					// };
				},
				(error) => { return error }
			);

			// ------------------------------------------------------------------------

}

function generarPdfResumenCajaVentaCommon(caja){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios({url: 'venta/resumen', method: 'post', responseType: 'arraybuffer', data: {'caja': caja}}).then( 
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

function generarPdfTicketVentaTestCommon(){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios({url: 'venta/ticket', method: 'post', responseType: 'blob'}).then(function (response) {
					const url = window.URL.createObjectURL(new Blob([response.data]));
					const link = document.createElement('a');
					link.href = url;
					link.target = '_blank'
					link.click();
			});

			// ------------------------------------------------------------------------

}

function filtrarVentasCommon(id){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// LLAMAR TELAS
			return axios.post('/ventaFiltrar', {'id': id}).then(function (response) {
					return response.data;
			});


			// ------------------------------------------------------------------------

}

function anularVentaCommon(data){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/ventaAnular', {'data':data}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}



async function obtenerIPCommon(_callback) {

	// INICIAR VARIABLES

    window.RTCPeerConnection = window.RTCPeerConnection || window.mozRTCPeerConnection || window.webkitRTCPeerConnection;   //compatibility for firefox and chrome
    var pc = new RTCPeerConnection({iceServers:[]}), noop = function(){};      
    pc.createDataChannel("");    //create a bogus data channel
    pc.createOffer(pc.setLocalDescription.bind(pc), noop);    // create offer and set local description
 	var ip=true;

 		

    	   pc.onicecandidate=  function (ice){  //listen for candidate events

	 			try {

			        if(!ice || !ice.candidate || !ice.candidate.candidate)  return;
			        window.IPv = /([0-9]{1,3}(\.[0-9]{1,3}){3}|[a-f0-9]{1,4}(:[a-f0-9]{1,4}){7})/.exec(ice.candidate.candidate)[1];
			        console.log(window.IPv);
		           	ip=false;	
			        _callback();
		    		
	        	} catch(error){
			    	window.IPv = false;
			    	 _callback();
			    }

           };     
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

function importarTransferenciaCommon(data){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/transferenciaImportar', {'data': data}).then(function (response) {
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

function obtenerCotizacionDia(){

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
// 							    GUARDAR COTIZACION
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function guardarCotizacionCommon(data){

	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/cotizacionGuardar', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}

function guardarNuevaCotizacionCommon(data){

	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/cotizacion/guardar', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							    FILTRAR COTIZACION
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function filtrarCotizacionCommon(data){

	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/cotizacionFiltrar', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							    ELIMINAR COTIZACION
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function eliminarCotizacionCommon(data){

	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/eliminarCotizacion', {'data':data}).then(function (response) {
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

function sucursalNuevaCommon(){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.get('/nuevaSucursal').then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}

function cambiarSucursalCommon(data){
	// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/cambiarSucursal', {'data':data}).then(function (response) {
					return response.data;
				});

}

function obtenerSucursalesInventarioCommon(){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DIEZ PRIMEROS DATOS DE ACUERDO AL TIPEAR EL TEXTBOX 

			return axios.get('/sucursalInventario').then(function (response) {
					return response.data.sucursales;
				});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							    FILTRAR SUCURSAL
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function filtrarSucursalCommon(data){

	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/sucursalFiltrar', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}

function filtrarSucursalUserCommon(data){

	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/sucursalFiltrarUser', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							    GUARDAR SUCURSAL
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function guardarSucursalCommon(data){

	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/sucursalGuardar', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}


// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							    ELIMINAR SUCURSAL
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function eliminarSucursalCommon(data){

	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/sucursalEliminar', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							    GUARDAR NOTA DE REMISION
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function guardarRemisionCommon(productos, data){
	
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/remisionGuardar', {'productos':productos, 'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							    FILTRAR NOTA DE REMISION
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function filtrarRemisionCommon(data){
	
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/remisionFiltrar', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							    ELIMINAR NOTA DE REMISION
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function eliminarNotaRemCommon(data){
	
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/remisionEliminar', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							    CODIGO NOTA DE REMISION
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function nuevaNotaCommon(){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.get('/nuevaNota').then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							    PDF TICKET
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function generarPdfRemisionCommon(data){

	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO

	return axios({url: 'pdf-generar-remision', method: 'post', responseType: 'arraybuffer', data: {'codigo': data}}).then(
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

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							    CUERPO NOTA DE REMISION
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function obtenerCuerpoRemisionCommon(codigo){

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DATOS DE LA CABECERA 
			
			return axios.post('/remisionCuerpo', {'codigo': codigo}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							    MODIFICAR NOTA DE REMISION
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function modificarRemisionCommon(productos, data){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/remisionModificar', {'productos':productos, 'data':data}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 					TABLA DE PRODUCTOS NOTA DE REMISION
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function existeProductoRemisionDataTableCommon(tabla, codigo, tipo_respuesta){

			// ------------------------------------------------------------------------

			// TIPO_RESPUESTO

			// REVISAR SI EXISTE VALORES REPETIDOS EN TABLA PRODUCTOS REMISION
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
				    			'descuento' : data['DESCUENTO'],
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
function existeProductoImprimirEtiquetaDataTableCommon(tabla, codigo, tipo_respuesta){

			// ------------------------------------------------------------------------

			// TIPO_RESPUESTO

			// REVISAR SI EXISTE VALORES REPETIDOS EN TABLA PRODUCTOS REMISION
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
function existeProductoImprimirEtiquetaQrDataTableCommon(tabla, codigo, tipo_respuesta,lote){

			// ------------------------------------------------------------------------

			// TIPO_RESPUESTO

			// REVISAR SI EXISTE VALORES REPETIDOS EN TABLA PRODUCTOS REMISION
            // LA OPCION 1 ES PARA DEVOLVER SOLO TRUE O FALSE SI EXISTE O NO
            // LA OPCION 2 ES PARA DEVOLVER MAS DATOS DEL PRODUCTO 

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

        	var valor = { 'respuesta': false };

        	// ------------------------------------------------------------------------

        	//	REVISAR SI PRODUCTO EXISTE EN DATATABLE 

			tabla.rows().every(function(){
				var data = this.data();
				    if (data['CODIGO'] === codigo && data['LOTE']===lote ) {
				    	if (tipo_respuesta === 1) {
				    		valor = { 'respuesta': true };
				    	} else if (tipo_respuesta === 2) {
				    		// ESTA SECCION PERTENECE A TRANSFERENCIA
				    		valor =  {
				    			'respuesta': true,
				    			'cantidad': data['CANTIDAD'],
				    			'precio': data['PRECIO'],
				    			
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
function existeProductoImprimirDataTableCommon(tabla, codigo, tipo_respuesta){

			// ------------------------------------------------------------------------

			// TIPO_RESPUESTO

			// REVISAR SI EXISTE VALORES REPETIDOS EN TABLA PRODUCTOS REMISION
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
				    			'mayorista': data['PRECIO_MAYORISTA'],
				    			'stock': data['STOCK'],
				    			
				    			'row': tabla.row( this )
				    		};
				    	} else if (tipo_respuesta === 3) {
				    		// ESTA SECCION PERTENECE A COMPRA
				    		valor = {
				    			'respuesta': true,
				    			'cantidad': data['CANTIDAD'],
				    			
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

function guardarInventarioCommon(data) {

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/inventarioGuardar', {'data': data}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

function editarComentarioProductoInventarioCommon(id, codigo, comentario) {

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/inventario/comentario', {'id': id, 'codigo': codigo, 'comentario': comentario}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}


function eliminarProductoInventarioCommon(id, codigo) {

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/inventario/eliminar/producto', {'id': id, 'codigo': codigo}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

function generarRptPdfInventarioCommon(codigo, tipo){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios({url: '/inventario/reporte', method: 'post', responseType: 'blob', data: {'codigo': codigo, 'tipo': tipo}}).then(function (response) {
					const url = window.URL.createObjectURL(new Blob([response.data]));
					const link = document.createElement('a');
					link.href = url;
					link.target = '_blank'
					link.click();
			});

			// ------------------------------------------------------------------------

}

function procesarInventarioCommon(id, codigo) {

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/inventario/procesar', {'id': id}).then(function (response) {
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

function obtenerSubCategoriaDetalleCommon(){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DIEZ PRIMEROS DATOS DE ACUERDO AL TIPEAR EL TEXTBOX 

			return axios.post('/subCategoriaDetalle').then(function (response) {
					return response.data;
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

function obtenerMarcaCategoriaCommon(categoria, marca){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DIEZ PRIMEROS DATOS DE ACUERDO AL TIPEAR EL TEXTBOX 

			return axios.post('/marca/categoria/seleccion', {categoria: categoria, marca: marca}).then(function (response) {
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

function guardarVentaCommon(data) {

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR LA LA VENTA DEL POS 

			return axios.post('/venta/guardar', {data: data}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}

function guardarPagoPECommon(data) {

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR LA LA VENTA DEL POS 

			return axios.post('/venta/pago/pe', {data: data}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}

function guardarPagoCreditoCommon(data) {

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR LA LA VENTA DEL POS 

			return axios.post('/venta/pago/credito', {data: data}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}

function numeracionVentaCommon(data) {

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR LA LA VENTA DEL POS 

			return axios.post('/venta/numeracion', {caja: data}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}


function inicioVentaCommon() {

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR LA LA VENTA DEL POS 

			return axios.post('/venta/inicio').then(function (response) {
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
					return response.data;
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
// 							      UBICACION
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function obtenerUbicacionCommon(codigo){

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DATOS DE LA UBICACION
			
			return axios.post('/producto/ubicacion', {'codigo': codigo}).then(function (response) {
					return response.data.ubicacion;
			});

			// ------------------------------------------------------------------------

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
function guardarUsuarioCommon(nombre,email,contraseña,btn_guardar,idUsuario, mostrarSucursales, sucursalesSelected){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/usuarioGuardar', {'nombre':nombre,'email':email,'contraseña':contraseña, 'btn_guardar':btn_guardar,'idUsuario':idUsuario, 'mostrarSucursales':mostrarSucursales, 'sucursalesSelected':sucursalesSelected}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function obtenerSucursalCommon(){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DIEZ PRIMEROS DATOS DE ACUERDO AL TIPEAR EL TEXTBOX 

			return axios.get('/obtenerUsuarioSucursales').then(function (response) {
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

function filtrarNombresCommon(id){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// LLAMAR ROL
			return axios.post('/subCategoriaDetalle/filtrar', {'id': id}).then(function (response) {
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

function nuevoNombreCommon(){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.get('/subCategoriaDetalle/nuevo').then(function (response) {
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

function guardarNombreCommon(data){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR NOMBRE

			return axios.post('/subCategoriaDetalle/guardar', {'data': data}).then(function (response) {
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

function eliminarNombreCommon(data){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/subCategoriaDetalle/eliminar', {'data':data}).then(function (response) {
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
function filtrarGondolasCommon(id,rack){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// LLAMAR TELAS
			return axios.post('/gondolasFiltrar', {'id': id, 'rack': rack}).then(function (response) {
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
// ------------------------------------------------------------------------
// 							 CONTAINER
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function filtrarContainerCommon(id){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// LLAMAR TELAS
			return axios.post('/containerFiltrar', {'id': id}).then(function (response) {
					return response.data;
			});


			// ------------------------------------------------------------------------

}

function nuevoContainerCommon(){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// NUEVO TRANSPORTE

			return axios.get('/nuevoContainer').then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function guardarContainerCommon(data){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/containerGuardar', {'data':data}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function eliminarContainerCommon(data){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/containerEliminar', {'data':data}).then(function (response) {
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

// function cotizacionyMonedaFormaPagoCommon(data) {

// 			// ------------------------------------------------------------------------

// 			// INICIAR VARIABLES

// 			let me = this;

// 			// ------------------------------------------------------------------------

// 			// OBTENER COTIZACION DE COMPRA

// 			return axios.get('/cotizacion/compra-dia', {fk_venta: data.fk_venta}).then(function (response) {
// 					return response.data;
// 				});

// 			// ------------------------------------------------------------------------
// }

function cotizacionyMonedaFormaPagoCommon(data) {

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

function generarPdfQrProductoCommon(datos){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios({url: 'qrcode', method: 'post', responseType: 'arraybuffer',data: {'data': datos }}).then( 
				(response) => {

					// var base64data = '';

					// const url = window.URL.createObjectURL(new Blob([response.data]));
					// var reader = new FileReader();
					//  reader.readAsDataURL(new Blob([response.data])); 
					//  reader.onloadend = function() {
					//      base64data = reader.result;
					//  }

					 return response.data;
					// return var blobToBase64 = function(blob, callback) {
					//     var reader = new FileReader();
					//     reader.onload = function() {
					//         var dataUrl = reader.result;
					//         var base64 = dataUrl.split(',')[1];
					//         callback(base64);
					//     };
					//     reader.readAsDataURL(blob);
					// };
				},
				(error) => { return error }
			);
/*return axios({url: 'qrcode', method: 'post', responseType: 'arraybuffer', data:  {'data': datos }}).then( 
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
			);*/
			// ------------------------------------------------------------------------

}
function generarPdfBarcodeProductoCommon(datos, tamaño, codigo, precio, proveedor, moneda, textura,cotizacion, gondola, impresion, stock, switch_desc, rotulo){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios({url: '/barcode', method: 'post', responseType: 'arraybuffer',data: {'data': datos, 'tamaño': tamaño, 'codigo':codigo, 'precio':precio, 'proveedor': proveedor, 'tipomoneda':moneda, 'tipotextura':textura,'cotizacion':cotizacion, 'seleccion_gondola':gondola, 'seleccionImpresion':impresion, 'tipoStock':stock, 'switch_desc':switch_desc, 'rotulo':rotulo}}).then( 
				(response) => {

					// var base64data = '';

					// const url = window.URL.createObjectURL(new Blob([response.data]));
					// var reader = new FileReader();
					//  reader.readAsDataURL(new Blob([response.data])); 
					//  reader.onloadend = function() {
					//      base64data = reader.result;
					//  }

					 return response.data;
					// return var blobToBase64 = function(blob, callback) {
					//     var reader = new FileReader();
					//     reader.onload = function() {
					//         var dataUrl = reader.result;
					//         var base64 = dataUrl.split(',')[1];
					//         callback(base64);
					//     };
					//     reader.readAsDataURL(blob);
					// };
				},
				(error) => { return error }
			);
// return axios({url: 'barcode', method: 'post', responseType: 'arraybuffer', data:  {'data': datos, 'tamaño': tamaño, 'codigo':codigo, 'precio':precio, 'proveedor': proveedor, 'tipomoneda':moneda, 'tipotextura':textura,'cotizacion':cotizacion, 'seleccion_gondola':gondola, 'seleccionImpresion':impresion, 'tipoStock':stock, 'switch_desc':switch_desc, 'rotulo':rotulo}}).then( 
// 				(response) => {
// 					const url = window.URL.createObjectURL(new Blob([response.data], {type: 'application/pdf'}));
// 					const link = document.createElement('a');
// 					link.href = url;
// 					//DESCARGAR
// 					// link.setAttribute('download', 'file.pdf');
// 					// document.body.appendChild(link);
// 					link.target = '_blank'
// 					link.click();
// 				},
// 				(error) => { return error }
// 			);
			// ------------------------------------------------------------------------

}
function generarPdfBarinternoProductoCommon(datos){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			// return axios({url: '/barinterno', method: 'post', responseType: 'arraybuffer',data: {'data': datos }}).then( 
			// 	(response) => {

			// 		// var base64data = '';

			// 		// const url = window.URL.createObjectURL(new Blob([response.data]));
			// 		// var reader = new FileReader();
			// 		//  reader.readAsDataURL(new Blob([response.data])); 
			// 		//  reader.onloadend = function() {
			// 		//      base64data = reader.result;
			// 		//  }

			// 		 return response.data;
			// 		// return var blobToBase64 = function(blob, callback) {
			// 		//     var reader = new FileReader();
			// 		//     reader.onload = function() {
			// 		//         var dataUrl = reader.result;
			// 		//         var base64 = dataUrl.split(',')[1];
			// 		//         callback(base64);
			// 		//     };
			// 		//     reader.readAsDataURL(blob);
			// 		// };
			// 	},
			// 	(error) => { return error }
			// );
return axios({url: 'barinterno', method: 'post', responseType: 'arraybuffer', data:  {'data': datos }}).then( 
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
function obtenerCuponCommon(){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.get('/obtenerCupon').then(function (response) {
				console.log(response.data);
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function guardarCuponCommon(data){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES


			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR CUPON

			return axios.post('/cuponGuardar', {'data':data}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function conseguirDatosCuponCommon(data){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/conseguirCupon', {'data':data}).then(function (response) {
				
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function modificarCuponCommon(data){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES


			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR CUPON

			return axios.post('/cuponModificar', {'data':data}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function obtenerParametroCommon(){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.get('/parametro').then(function (response) {
				// console.log(response.data);
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function deshabilitarCuponCommon(data){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/cuponDeshabilitar', {'data': data}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}
function habilitarCuponCommon(data){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/cuponHabilitar', {'data': data}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}
function generarReporteVentaCommon(data){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/reporte_ventas', {'data': data}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}
function encontrarfotoCommon(){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.get('/encontrarFoto').then(function (response) {
				
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function importarProductoCommon(codigo, tipo){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/productoImportar', {'codigo': codigo}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}



// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							    ORDEN FACTURA
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function generarPdfFacturaOrdenCommon(data){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO
			
			return axios({url: 'pdf-generar-factura', method: 'post', responseType: 'arraybuffer', data: {'data': data}}).then(
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

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							    ORDEN CABECERA
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function obtenerCabeceraOrdenCommon(codigo){

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DATOS DE LA CABECERA 
			
			return axios.post('/ordenCabecera', {'codigo': codigo}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							    DIRECCION DE ORDEN
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function generarPdfDireccionOrdenCommon(data){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO
			
			return axios({url: 'pdf-generar-direccion', method: 'post', responseType: 'arraybuffer', data: {'data': data}}).then(
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

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							    ENVIAR ORDEN
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function enviarOrdenCommon(data){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO 
			
			return axios.post('/ordenEnviar', {'data': data}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 						CABECERA ORDEN PENDIENTE
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function obtenerCabeceraOrdenPendienteCommon(codigo){

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DATOS DE LA CABECERA 
			
			return axios.post('/ordenPendienteCabecera', {'codigo': codigo}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							 FACTURA ORDEN PENDIENTE
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function generarPdfFacturaOrdenPendienteCommon(data){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO
			
			return axios({url: 'pdf-generar-factura-pendiente', method: 'post', responseType: 'arraybuffer', data: {'data': data}}).then(
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

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							 DIRECCION ORDEN PENDIENTE
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function generarPdfDireccionOrdenPendienteCommon(data){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO
			
			return axios({url: 'pdf-generar-direccion-pendiente', method: 'post', responseType: 'arraybuffer', data: {'data': data}}).then(
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

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							    PDF VALE REPORTE
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function generarReporteValeCommon(data){

	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// CONSEGUIR EL CODIGO

	return axios({url: 'pdf-generar-rptVale', method: 'post', responseType: 'arraybuffer', data: {'data': data}}).then(function (response){
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

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------


function reporteVentaTransferenciaCommon(data){

	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// CONSEGUIR EL CODIGO

	return axios({url: 'pdf-rptTransferencia', method: 'post', responseType: 'arraybuffer', data: {'data': data}}).then(function (response){
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

function reporteDeliveryCommon(data){
	// CONSEGUIR EL CODIGO

	return axios({url: 'pdf-rptDelivery', method: 'post', responseType: 'arraybuffer', data: {'data': data}}).then(function (response){
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

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 						   PDF DELIVERY
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
function busquedaCajeroCommon(data){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/busquedaCajero',{'data':data}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function reporteCajeroVentaCommon(data){
	// CONSEGUIR EL CODIGO

	return axios({url: 'pdf-rptCajero', method: 'post', responseType: 'arraybuffer', data: {'data': data}}).then(function (response){
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

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 						   PDF DELIVERY
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------


function agregarProductoPedidoCommon(data){
	
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/pedido/producto/agregar', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}


function mostrarProductosPedidoCommon(data){
	
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/pedido/producto/obtener', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}

function confirmarPedidoCommon(data){
	
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/pedido/confirmar', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}

function cambiarCantidadPedidoCommon(data){
	
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/pedido/cambiar/cantidad', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}

function eliminarProductoPedidoCommon(data){
  // GUARDAR PERMISO

	return axios.post('/pedido/producto/eliminar', {'data':data}).then(function (response) {
		return response.data;
	});

	// ------------------------------------------------------------------------
}	

function cambiarEstatusPedidoCommon(codigo, estatus){
	
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// GUARDAR PERMISO

	return axios.post('/pedido/cambiar/estatus', {'codigo': codigo, 'estatus': estatus}).then(function (response) {
		return response.data;
	});

	// ------------------------------------------------------------------------
}


function generarRptPdfPedidoCommon(data, moneda){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios({url: '/pedido/reporte', method: 'post', responseType: 'arraybuffer', data: {'data':data, 'moneda': moneda}}).then( 
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

function generarRptPdfVentaCommon(data, caja, moneda,id_venta){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios({url: '/venta/reporte/unico', method: 'post', responseType: 'arraybuffer', data: {'codigo':data, 'caja': caja, 'moneda': moneda,'id_venta':id_venta}}).then( 
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

function arreglar_costo(){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.get('/arreglar').then(function (response) {
				console.log(response.data);
					return response.data;
				});

			// ------------------------------------------------------------------------
}

function obtenerProductoOfertaCommon(sucursal){
	
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// GUARDAR PERMISO

	return axios.post('api/producto/ofertas', {'sucursal': sucursal}).then(function (response) {
		return response.data;
	});

	// ------------------------------------------------------------------------
}
function devolverTransferenciaCommon(codigo_tr,marcado){
	
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// GUARDAR PERMISO

	return axios.post('devolver_transferencia', { 'codigos':codigo_tr,'marcados':marcado}).then(function (response) {
		return response.data;
	});

	// ------------------------------------------------------------------------
}
function marcarTodoTransferenciaCommon(codigo_tr){
	
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// GUARDAR PERMISO

	return axios.post('marcar_transferencia_devolucion', { 'codigos':codigo_tr}).then(function (response) {
		return response.data;
	});

	// ------------------------------------------------------------------------
}
function enviarDevTransferenciaCommon(data){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/devTransferenciaEnviar', {'data': data}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}
function eliminarDevTransferenciaCommon(data){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/devTransferenciaEliminar', {'data': data}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

function importarDevTransferenciaCommon(codigo){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/DevtransferenciaImportar', {'codigo': codigo}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}
function rechazarDevTransferenciaCommon(codigo){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/devTransferenciaRechazar', {'codigo': codigo}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}




function inicioCatalogoCommon(datos){

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DATOS DE LA CABECERA DE TRANSFERENCIA
			
			return axios.post('/pedido/inicio_catalogo', {'datos': datos}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}



// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 						   PDF VENTA TARJETA
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function reporteVentaTarjetaCommon(data){
	
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// CONSEGUIR EL CODIGO

	return axios({url: 'pdf-rptTarjeta', method: 'post', responseType: 'arraybuffer', data: {'data': data}}).then(function (response){
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
function aplicarCuponCommon(datos){

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DATOS DE LA CABECERA DE TRANSFERENCIA
			
			return axios.post('/cupon/aplicar', {'datos': datos}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 						   PDF VENTA VENDEDOR
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function reporteVentaVendedorCommon(data){
	
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// CONSEGUIR EL CODIGO

	return axios({url: 'pdf-rptVendedor', method: 'post', responseType: 'arraybuffer', data: {'data': data}}).then(function (response){
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

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 						    MOVIMIENTOS POR PRODUCTO
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function obtenerMovimientosProductosCommon(codigo){

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DATOS DE LOS MOVIMIENTOS
			
			return axios.post('/producto/movimiento', {'codigo': codigo}).then(function (response) {
					return response.data.movimientos;
			});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 				   REPORTE TRANSFERENCIA POR CONSIGNACION
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function generarReporteTransferenciaConsignacionCommon(data){

	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
	return axios.post('/reporte-transferencia', {'data': data}).then(function (response) {
			return response.data;
		});

	// ------------------------------------------------------------------------

}

function generarCuerpoNotaCreditoCommon(datos){

			// ------------------------------------------------------------------------

			// GUARDAR EL CUERPO DE LA NOTA DE CREDITO 
			
			return axios.post('/nota/credito/generar_cuerpo', {'datos': datos}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

function guardarNotaCreditoCommon(datos){

			// ------------------------------------------------------------------------

			// GUARDAR LA NOTA DE CREDITO
			
			return axios.post('/nota/credito/guardar', {'datos': datos}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

function generarPdfNotaCreditoCommon(id){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios({url: 'nota/credito/generar/pdf', method: 'post', responseType: 'arraybuffer', data: {'id': id}}).then( 
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

function notaCreditoClienteCreditoCommon(datos){

			// ------------------------------------------------------------------------

			// GUARDAR LA NOTA DE CREDITO
			
			return axios.post('/venta/nota/credito/asingar/credito/cliente', {'datos': datos}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

function obtenerAutorizacion(data){
	
			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// OBTENER PERMISO

			return axios.post('/obtener/autorizacion', {'codigo':data}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------
}

function guardarSalidaProductoCommon(data) {

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DIEZ PRIMEROS DATOS DE ACUERDO AL TIPEAR EL TEXTBOX 

			return axios.post('/salida/producto', {data: data}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------
}

function obtenerAvisoDiaCommon(){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.get('/aviso/obtener').then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------

}

function confirmarAvisoDiaCommon(codigo){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/aviso/confirmar', {codigo: codigo}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------

}

function devolverSalidaCommon(codigo){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/salida/devolver', {codigo: codigo}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 						 GUARDAR MOVIMIENTO CAJA
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function guardarMovimientoCajaCommon(data){
	
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/movimiento/caja/guardar', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							    PDF REPORTE DIARIO
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function generarReporteDiarioCommon(data){

	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// CONSEGUIR EL CODIGO

	return axios({url: 'pdf-generar-rptDiario', method: 'post', responseType: 'arraybuffer', data: {'data': data}}).then(function (response){
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

function obtenerProductoQRCommon(data){
	
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// GUARDAR PERMISO

	return axios.post('api/producto/qr', {'data': data}).then(function (response) {
		return response.data;
	});

	// ------------------------------------------------------------------------
}


function generarRptPdfSalidaCommon(id){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios({url: '/salida/reporte', method: 'post', responseType: 'blob', data: {'id': id}}).then(function (response) {
					const url = window.URL.createObjectURL(new Blob([response.data]));
					const link = document.createElement('a');
					link.href = url;
					link.target = '_blank'
					link.click();
			});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							FILTRAR SUCURSALES RM
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function filtrarSucursalesRmCommon(data){
	
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// GUARDAR PERMISO

	return axios.post('/sucursalRmFiltrar', {'data':data}).then(function (response) {
		return response.data;
	});

	// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							 FILTRAR SECTORES RM
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function filtrarSectoresRmCommon(data){
	
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// GUARDAR PERMISO

	return axios.post('/sectorRmFiltrar', {'data':data}).then(function (response) {
		return response.data;
	});

	// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 						 GUARDAR SUCURSAL RM
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function guardarSucursalRmCommon(data){
	
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// GUARDAR PERMISO

	return axios.post('/sucursalRmGuardar', {'data':data}).then(function (response) {
		return response.data;
	});

	// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 						 GUARDAR SECTOR RM
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function guardarSectorRmCommon(data){
	
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// GUARDAR PERMISO

	return axios.post('/sectorRmGuardar', {'data':data}).then(function (response) {
		return response.data;
	});

	// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							  ELIMINAR SUCURSAL RM
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function eliminarSucursalesRmCommon(data){
	
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	return axios.post('/sucursalEliminarRM', {'data':data}).then(function (response) {
		return response.data;
	});

	// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							 ELIMINAR SECTOR RM
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function eliminarSectoresRmCommon(data){
	
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	return axios.post('/sectorEliminarRM', {'data':data}).then(function (response) {
		return response.data;
	});

	// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							 NUEVO ID SECTOR RM
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function nuevoSectorRmCommon(){
	
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// GUARDAR PERMISO

	return axios.get('/nuevoSectorRm').then(function (response) {
		return response.data;
	});

	// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							 NUEVA ID SUCURSAL RM
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function nuevaSucursalRmCommon(){
	
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// GUARDAR PERMISO

	return axios.get('/nuevaSucursalRm').then(function (response) {
		return response.data;
	});

	// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 						 ULTIMO REGISTRO DE MAQUINA SUCURSAL
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function obtenerRegistroSucursalRmCommon(data){
	
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	return axios.post('/ultimaSucursalRm', {'data':data}).then(function (response) {
		return response.data;
	});

	// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 						 ULTIMO REGISTRO DE MAQUINA SECTOR
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function obtenerRegistroSectorRmCommon(data){
	
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	return axios.post('/ultimoSectorRm', {'data':data}).then(function (response) {
		return response.data;
	});

	// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 						 GUARDAR REGISTRO DE MAQUINAS
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function guardarMaquinaRmCommon(data){
	
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// GUARDAR PERMISO

	return axios.post('/guardarRegistroM', {'data':data}).then(function (response) {
		return response.data;
	});

	// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							 NUEVO REGISTRO DE MAQUINAS
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function obtenerUltimoRegistroCommon(){
	
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// GUARDAR PERMISO

	return axios.get('/nuevoRegistroM').then(function (response) {
		return response.data;
	});

	// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							 FILTRAR SECTORES RM
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function filtrarMaquinasRegistradasCommon(data){
	
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// GUARDAR PERMISO

	return axios.post('/filtrarRm', {'data':data}).then(function (response) {
		return response.data;
	});

	// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							 ELIMINAR REGISTRO DE MAQUINA
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function eliminarRegistroMaquinaCommon(data){
	
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	return axios.post('/registroMaEliminar', {'data':data}).then(function (response) {
		return response.data;
	});

	// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							    FILTRAR CODIGOS
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function obtenerBarcodeCommon(codigo){

	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// GUARDAR PERMISO

	return axios.post('/barcodeFiltrar', {'codigo':codigo}).then(function (response) {
		return response.data;
	});

	// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 					GENERAR REPORTE DE VENTAS POR SECCION
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function generarReporteVentaSeccionCommon(data){

	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// CONSEGUIR LOS DATOS
			
	return axios.post('/reporte_ventas_seccion', {'data': data}).then(function (response) {
			return response.data;
	});

	// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 				TABLA DE PRODUCTOS FORMULARIO CAMBIAR PRECIO
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function productoExisteDataTableCommon(tabla, codigo, tipo_respuesta){

			// ------------------------------------------------------------------------

			// TIPO_RESPUESTO

			// REVISAR SI EXISTE VALORES REPETIDOS EN TABLA PRODUCTOS REMISION
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
				    		
				    		valor =  {
				    			'respuesta': true,
				    			'precio_venta': data['PRECIO'],
				    			'precio_mayorista': data['PREMAYORISTA'],
				    			'precio_venta_nuevo': data['NUEVOPRECIO'],
				    			'precio_mayorista_nuevo': data['NUEVOPREMAYORISTA'],
				    			'row': tabla.row( this )
				    		};
				    	} 
				    	
				    } 
			});

			// ------------------------------------------------------------------------

			// RETORNAR TRUE SI SE SE ENCONTRO CODIGO IGUAL O FALSE SI NO SE ENCONTRO NADA

			return valor;

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 						GUARDAR PRESTAMO DE PRODUCTO
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function guardarPrestamoProductoCommon(data){
	
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/prestamoProductoGuardar', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 						DEVOLVER PRODUCTOS DE PRESTAMO
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function devolverPrestamoCommon(codigo){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/prestamo/devolver', {codigo: codigo}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							    GUARDAR COMBO
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function guardarComboCommon(data){
	
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/comboGuardar', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							    FILTRAR COMBO
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function filtrarComboCommon(data){
	
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/comboFiltrar', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							    CUERPO COMBO
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function obtenerCuerpoComboCommon(codigo){

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DATOS DE LA CABECERA 
			
			return axios.post('/remisionCuerpo', {'codigo': codigo}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

function nuevoEmpleadoCommon(){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.get('/nuevoEmpleado').then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function filtrarEmpleadoCommon(data){
	
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/empleadoFiltrar', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}
function guardarEmpleadosCommon(data){
	
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/guardarEmpleado', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}

function eliminarEmpleadosCommon(data){
	
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// GUARDAR PERMISO

	return axios.post('/eliminarEmpleado', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}


function filtrarSeccionCommon(data){
	
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/seccionFiltrar', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}

function nuevaSeccionCommon(){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.get('/nuevaSeccion').then(function (response) {
					return response.data;
			});
}

function guardarSeccionCommon(data){
	
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/guardarSeccion', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}

function eliminarSeccionCommon(data){
	
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// GUARDAR PERMISO

	return axios.post('/eliminarSeccion', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}


function cancelarNotaCreditoCommon(id){

			// ------------------------------------------------------------------------

			// GUARDAR EL CUERPO DE LA NOTA DE CREDITO 
			
			return axios.post('/nota/credito/cancelar', {'id': id}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							      INVENTARIO
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function obtenerInventarioCommon(codigo){

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DATOS DE INVENTARIO
			
			return axios.post('/producto/inventario', {'codigo': codigo}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

function  seleccionarValorCommon(valor_a, valor_b, candec){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES 

			var seleccion = 0;
			console.log(valor_a);console.log(valor_b);

			// ------------------------------------------------------------------------

			// QUITAR COMAS 
			if(valor_a===undefined){
				valor_a = 0;
			}else{
				valor_a = quitarComaCommon(valor_a);
			}

			if(valor_b===undefined){
				valor_b = 0;
			}else{
				valor_b = quitarComaCommon(valor_b);
			}
			
			if(parseFloat(valor_a)>0){
				seleccion=parseFloat(valor_a);
			}else{
				seleccion=parseFloat(valor_b);
			}
			console.log(seleccion);
			
			// ------------------------------------------------------------------------

			// REALIZAR SUMA 

			/*suma = parseFloat(valor_a) + parseFloat(valor_b);*/

			// ------------------------------------------------------------------------

			// RETORNAR VALOR

			return darFormatoCommon(seleccion, candec);

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							ASIGNAR/MODIFICAR CAJA
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
function  asignarCajaCommon(data){

// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/asignarCaja', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							COMPROBAR SI EXISTE CAJA
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------


function existeCajaCommon(data){

// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/existeCaja', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------

}


function quitarCajaCommon(data){
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// GUARDAR PERMISO

	return axios.post('/quitarCaja', {'data':data}).then(function (response) {
					return response.data;
				});

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							PANEL VENDEDORES
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function obtenerRendimientoVendedoresCommon(sucursal){
	
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// GUARDAR PERMISO

	return axios.post('api/vendedor/rendimiento', {'sucursal': sucursal}).then(function (response) {
		return response.data;
	});

	// ------------------------------------------------------------------------
}
function generarReporteVentaProveedorCommon(data){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/reporte_ventas_Proveedor', {'data': data}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}
function generarReporteVentaGondolaCommon(data){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/reporte_ventas_Gondola', {'data': data}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}
function generarReporteSalidaProductosCommon(data){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/reporte_salida_productos', {'data': data}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 						GUARDAR DESCUENTOS POR LOTE
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function guardarLoteDescuentoCommon(data) {

	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// GUARDAR REGISTROS 

	return axios.post('/descuentoLoteProducto', {data: data}).then(function (response) {
		return response.data;
	});

}
function existeProductoConDescuentoDataTableCommon(tabla, codigo, tipo_respuesta,descuento,tipo_descuento){

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
				
				if(data['CODIGO'] === codigo && data['TIPO'] !== 3 && tipo_descuento!==7 && data['TIPO_DESCUENTO']!==7){
					
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
				    			'iva': data['IVA'],
				    			'row': tabla.row( this )
				    		}
				    	} else if (tipo_respuesta === 3) {
				    		// DEVUELVE LOS DATOS CON EL ROW ESPECIFICO DEL PRODUCTO CON EL DESCUENTO 
				    		valor =  {
				    			'respuesta': true,
				    			'cantidad': data['CANTIDAD'],
				    			'precio': data['PRECIO'],
				    			'iva': data['IVA'],
				    			'stock': data['STOCK'],
				    			'row': tabla.row( this )
				    		};
				    	}
				}else if (data['CODIGO'] === codigo && data['TIPO'] !== 3 && data['DESCUENTO']===descuento &&  tipo_descuento===7 && data['TIPO_DESCUENTO']===7) {

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
				    			'iva': data['IVA'],
				    			'row': tabla.row( this )
				    		}
				    	} else if (tipo_respuesta === 3) {
				    		// DEVUELVE LOS DATOS CON EL ROW ESPECIFICO DEL PRODUCTO CON EL DESCUENTO 
				    		valor =  {
				    			'respuesta': true,
				    			'cantidad': data['CANTIDAD'],
				    			'precio': data['PRECIO'],
				    			'iva': data['IVA'],
				    			'stock': data['STOCK'],
				    			'row': tabla.row( this )
				    		};
				    	}
				    	
				    } 
			});

			// ------------------------------------------------------------------------

			// RETORNAR TRUE SI SE SE ENCONTRO CODIGO IGUAL O FALSE SI NO SE ENCONTRO NADA

			return valor;

			// ------------------------------------------------------------------------

}
function existenProductosDataTableCommon(tabla, codigo, tipo_respuesta){

			// ------------------------------------------------------------------------

			// TIPO_RESPUESTO

			// REVISAR SI EXISTE VALORES REPETIDOS EN TABLA TRANSFERENCIAS 
            // LA OPCION 1 ES PARA DEVOLVER SOLO TRUE O FALSE SI EXISTE O NO
            // LA OPCION 2 ES PARA DEVOLVER MAS DATOS DEL PRODUCTO 

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES
			return new Promise((resolve,reject)=>{
				setTimeout(()=>{
					var valor = { 'respuesta': false };
        			var cantidad=0;

		        	// ------------------------------------------------------------------------

		        	//	REVISAR SI PRODUCTO EXISTE EN DATATABLE 

					tabla.rows().every(function(){
						var data = this.data();
						    if (data['CODIGO'] === codigo && data['TIPO'] !== 3) {
						    	if (tipo_respuesta === 1) {
						    		valor = { 'respuesta': true };
						    	} else if (tipo_respuesta === 2) {
						    		// ESTA SECCION PERTENECE A TRANSFERENCIA
						    		cantidad=cantidad+data['CANTIDAD'];
						    		valor =  {
						    			'respuesta': true,
						    			'cantidad': cantidad,
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

					resolve(valor);

			// ------------------------------------------------------------------------
				},100)
			});

        	

}
function cotizacionyMonedaDeVentaFormaPagoCommon(data) {

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// OBTENER COTIZACION DE COMPRA
			
			return axios.post('/cotizacion/compra-venta',{fk_venta: data}).then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}
function obtenerCompraCajaQRCommon(data){
	
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// GUARDAR PERMISO

	return axios.post('api/compra/caja/qr', {'data': data}).then(function (response) {
		return response.data;
	});

	// ------------------------------------------------------------------------
}

function guardarNroPisoCommon(data){
	
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/guardarPiso', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}
function eliminarNroPisoCommon(data){
	
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// GUARDAR PERMISO

	return axios.post('/eliminarPiso', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}
function nuevoNroPisoCommon(){
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.get('/nuevoPiso').then(function (response) {
					return response.data;
			});
}
function inicioConfiguracionGondola() {

			// ------------------------------------------------------------------------


			// INICIAR VARIABLES

			let me = this;

			// OBTENER COTIZACION DE COMPRA
			
			return axios.get('/configuracion/gondola').then(function (response) {
					return response.data;
				});

			// ------------------------------------------------------------------------
}


// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							  MODIFICAR UBICACION DE TRANFERENCIA
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function modificarUbicacionTransferenciaCommon(data){

			// ------------------------------------------------------------------------

			// ------------------------------------------------------------------------

      return axios.post('/transferenciaModificarUbicacion', {'data': data}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}
function filtrarPisoCommon(data){
			// GUARDAR PERMISO

			return axios.post('/pisoFiltrar', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}
function nuevoSectorCommon(){
	// ------------------------------------------------------------------------


			// INICIAR VARIABLES

			let me = this;
  return axios.get('/nuevoSector').then(function (response) {
					return response.data;
			});
}

			// ------------------------------------------------------------------------


			// CONSEGUIR RESPUESTA
			
			

// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// 							  MODIFICAR UBICACION DE COMPRA
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------

function modificarUbicacionCompraCommon(data){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR RESPUESTA
			
			return axios.post('/compraModificarUbicacion', {'data': data}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

			
}
function filtrarSectorCommon(data){
	
	// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// GUARDAR PERMISO

			return axios.post('/sectorFiltrar', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}
function guardarsectorCommon(data){
			// GUARDAR PERMISO

			return axios.post('/guardarSector', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}
function eliminarsectorCommon(data){
	
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// GUARDAR PERMISO

	return axios.post('/eliminarSector', {'data':data}).then(function (response) {
					return response.data;
				});

	// ------------------------------------------------------------------------
}
function generarRptPdfCajaCompraQrCommon(datos){

			// ------------------------------------------------------------------------


			// INICIAR VARIABLES

			let me = this;
			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			/*return axios({url: 'PdfQrCajaCompra', method: 'post', responseType: 'arraybuffer',data: {'data': datos }}).then( 
				(response) => {

					// var base64data = '';

					// const url = window.URL.createObjectURL(new Blob([response.data]));
					// var reader = new FileReader();
					//  reader.readAsDataURL(new Blob([response.data])); 
					//  reader.onloadend = function() {
					//      base64data = reader.result;
					//  }

					 return response.data;
					// return var blobToBase64 = function(blob, callback) {
					//     var reader = new FileReader();
					//     reader.onload = function() {
					//         var dataUrl = reader.result;
					//         var base64 = dataUrl.split(',')[1];
					//         callback(base64);
					//     };
					//     reader.readAsDataURL(blob);
					// };
				},
				(error) => { return error }
			);*/
return axios({url: 'PdfQrCajaCompra', method: 'post', responseType: 'arraybuffer', data:  {'data': datos }}).then( 
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
function obtenerTransferenciaCajaQRCommon(data){
	
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// GUARDAR PERMISO

	return axios.post('api/transferencia/caja/qr', {'data': data}).then(function (response) {
		return response.data;
	});

	// ------------------------------------------------------------------------
}
function obtenerNumeroCajaCommon(codigo,origen){
	
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// GUARDAR PERMISO

	return axios.post('transferencia/obtener/caja/numero', {'codigo': codigo,'origen':origen}).then(function (response) {
		return response.data;
	});

	// ------------------------------------------------------------------------
}
function generarRptPdfCajaTransferenciaQrCommon(datos){

			// ------------------------------------------------------------------------


			// INICIAR VARIABLES

			let me = this;
			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			/*return axios({url: 'PdfQrCajaCompra', method: 'post', responseType: 'arraybuffer',data: {'data': datos }}).then( 
				(response) => {

					// var base64data = '';

					// const url = window.URL.createObjectURL(new Blob([response.data]));
					// var reader = new FileReader();
					//  reader.readAsDataURL(new Blob([response.data])); 
					//  reader.onloadend = function() {
					//      base64data = reader.result;
					//  }

					 return response.data;
					// return var blobToBase64 = function(blob, callback) {
					//     var reader = new FileReader();
					//     reader.onload = function() {
					//         var dataUrl = reader.result;
					//         var base64 = dataUrl.split(',')[1];
					//         callback(base64);
					//     };
					//     reader.readAsDataURL(blob);
					// };
				},
				(error) => { return error }
			);*/
return axios({url: 'PdfQrCajaTransferencia', method: 'post', responseType: 'arraybuffer', data:  {'data': datos }}).then( 
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
function generarReporteCompraEntradaSeccionCommon(data){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/reporte_entrada_compra_seccion', {'data': data}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}
function generarReporteCompraVentaSeccionCommon(data){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/reporte_venta_compra_seccion', {'data': data}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}
function generarRptPdfInventarioGondolaCommon(codigo){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios({url: 'inventarioGondola/pdf', method: 'post', responseType: 'blob', 'data': data}  ).then(function (response) {
					const url = window.URL.createObjectURL(new Blob([response.data]));
					const link = document.createElement('a');
					link.href = url;
					link.target = '_blank'
					link.click();
			});

			// ------------------------------------------------------------------------

}

function generarReporteInventarioSeccionCommon(data){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR EL CODIGO DEL PRODUCTO MEDIANTE EL CODIGO INTERNO
			
			return axios.post('/reporte_inventario_seccion', {'data': data}).then(function (response) {
					return response.data;
			});

			// ------------------------------------------------------------------------

}

function obtenerCotizacionBannerCommon(sucursal){
	
	// ------------------------------------------------------------------------

	// INICIAR VARIABLES

	let me = this;

	// ------------------------------------------------------------------------

	// GUARDAR PERMISO

	return axios.post('cotizacionBanner', {'sucursal': sucursal}).then(function (response) {
		return response.data;
	});

	// ------------------------------------------------------------------------
}
function obtenerGondolasEncargadaSeccionCommon(){

			// ------------------------------------------------------------------------

			// INICIAR VARIABLES

			let me = this;

			// ------------------------------------------------------------------------

			// CONSEGUIR LOS DIEZ PRIMEROS DATOS DE ACUERDO AL TIPEAR EL TEXTBOX 

			return axios.get('/gondola/encargada/seccion').then(function (response) {
					return response.data.gondolas;
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
		existeProductoLoteDataTableCommon,
		darDeBajaProductoCommon,
		obtenerSubCategoriaDetalleCommon,
		obtenerProductoPOSCommon,
		guardarVentaCommon,
		numeracionVentaCommon,
		descuentoCommon,
		inicioVentaCommon,
		generarPdfFacturaVentaCommon,
		filtrarNombresCommon,
		nuevoNombreCommon,
		guardarNombreCommon,
		eliminarNombreCommon,
		obtenerUbicacionCommon,
		obtenerMarcaCategoriaCommon,
		existeProductoCommon,
		generarPdfTicketVentaCommon,
		generarPdfTicketVentaTestCommon,
		mayoristaCommon,
		generarPdfFacturaVentaVisualizarCommon,
		generarPdfTicketVentaVisualizarCommon,
		filtrarClienteCommon,
		guardarClienteCommon,
		eliminarClienteCommon,
		clienteNuevoCommon,
		generarPdfResumenCajaVentaCommon,
		obtenerServicioPOSCommon,
		guardarCotizacionCommon,
		filtrarCotizacionCommon,
		eliminarCotizacionCommon,
		anularVentaCommon,
		obtenerIPCommon,
		filtrarVentasCommon,
		filtrarTransporteCommon,
		nuevoTransporteCommon,
		guardarTransporteCommon,
		eliminarTransporteCommon,
		filtrarContainerCommon,
		nuevoContainerCommon,
		guardarContainerCommon,
		eliminarContainerCommon,
		filtrarSucursalCommon,
		guardarSucursalCommon,
		eliminarSucursalCommon,
		guardarRemisionCommon,
		filtrarRemisionCommon,
		eliminarNotaRemCommon,
		sucursalNuevaCommon,
		nuevaNotaCommon,
		generarPdfRemisionCommon,
		obtenerCuerpoRemisionCommon,
		modificarRemisionCommon,
		existeProductoRemisionDataTableCommon,
		nuevoProveedorCommon,
		filtrarProveedorCommon,
		guardarProveedorCommon,
		eliminarProveedorCommon,
		generarPdfFacturaOrdenCommon,
		obtenerCabeceraOrdenCommon,
		generarPdfDireccionOrdenCommon,
		enviarOrdenCommon,
		obtenerCabeceraOrdenPendienteCommon,
		generarPdfFacturaOrdenPendienteCommon,
		generarPdfDireccionOrdenPendienteCommon,
		generarPdfQrProductoCommon,
		existeProductoImprimirEtiquetaDataTableCommon,
		existeProductoImprimirEtiquetaQrDataTableCommon,
		generarPdfBarcodeProductoCommon,
		generarPdfBarinternoProductoCommon,
		obtenerCuponCommon,
		guardarCuponCommon,
		conseguirDatosCuponCommon,
		modificarCuponCommon,
		obtenerParametroCommon,
		deshabilitarCuponCommon,
	  	habilitarCuponCommon,
	  	generarReporteVentaCommon,
	  	encontrarfotoCommon,
	  	importarProductoCommon,
		obtenerCreditoClienteCommon,
		editarComentarioProductoInventarioCommon,
		eliminarProductoInventarioCommon,
		generarRptPdfInventarioCommon,
		procesarInventarioCommon,
		generarReporteValeCommon,
		reporteVentaTransferenciaCommon,
		reporteDeliveryCommon,
		mostrarProductosViewNewCommon,
		inicioProductosViewNewCommon,
		agregarProductoPedidoCommon,
		mostrarProductosPedidoCommon,
		confirmarPedidoCommon,
		cambiarCantidadPedidoCommon,
		eliminarProductoPedidoCommon,
		cambiarEstatusPedidoCommon,
		generarRptPdfPedidoCommon,
		reporteVentaVendedorCommon,
		obtenerProductoOfertaCommon,
		devolverTransferenciaCommon,
		marcarTodoTransferenciaCommon,
		enviarDevTransferenciaCommon,
		eliminarDevTransferenciaCommon,
		importarDevTransferenciaCommon,
		rechazarDevTransferenciaCommon, 
		mostrarProductosCatalogoCommon,
		inicioCatalogoCommon,
		guardarPagoPECommon,
		guardarPagoCreditoCommon,
		generarRptPdfVentaCommon,
		reporteVentaTarjetaCommon,
		obtenerMovimientosProductosCommon,
		generarReporteTransferenciaConsignacionCommon,
		generarCuerpoNotaCreditoCommon,
		guardarNotaCreditoCommon,
		generarPdfNotaCreditoCommon,
		aplicarCuponCommon,
		notaCreditoClienteCreditoCommon,
		obtenerAutorizacion,
		guardarSalidaProductoCommon,
		obtenerAvisoDiaCommon,
		confirmarAvisoDiaCommon,
		guardarMovimientoCajaCommon,
		generarReporteDiarioCommon,
		obtenerProductoQRCommon,
		generarRptPdfSalidaCommon,
		filtrarSucursalesRmCommon,
		filtrarSectoresRmCommon,
		guardarSucursalRmCommon,
		guardarSectorRmCommon,
		eliminarSucursalesRmCommon,
		eliminarSectoresRmCommon,
		nuevoSectorRmCommon,
		nuevaSucursalRmCommon,
		obtenerRegistroSucursalRmCommon,
		obtenerRegistroSectorRmCommon,
		guardarMaquinaRmCommon,
		obtenerUltimoRegistroCommon,
		filtrarMaquinasRegistradasCommon,
		eliminarRegistroMaquinaCommon,
		guardarNuevaCotizacionCommon,
		obtenerBarcodeCommon,
		generarReporteVentaSeccionCommon,
		devolverSalidaCommon,
		productoExisteDataTableCommon,
		guardarPrestamoProductoCommon,
		devolverPrestamoCommon,
		guardarComboCommon,
		filtrarComboCommon,
		obtenerCuerpoComboCommon,
		nuevoEmpleadoCommon,
		filtrarEmpleadoCommon,
		guardarEmpleadosCommon,
		eliminarEmpleadosCommon,
		nuevaSeccionCommon,
		guardarSeccionCommon,
		eliminarSeccionCommon,
		filtrarSeccionCommon,
		obtenerSucursalCommon,
		cambiarSucursalCommon,
		cancelarNotaCreditoCommon,
		obtenerInventarioCommon,
		seleccionarValorCommon,
		existeCajaCommon,
		asignarCajaCommon,
		quitarCajaCommon,
		obtenerRendimientoVendedoresCommon,
		generarReporteVentaProveedorCommon,
		generarReporteVentaGondolaCommon,
		generarReporteSalidaProductosCommon,
		guardarLoteDescuentoCommon,
		existeProductoConDescuentoDataTableCommon,
		existenProductosDataTableCommon,
		cotizacionyMonedaDeVentaFormaPagoCommon,
		obtenerCompraCajaQRCommon,
		inicioConfiguracionGondola,
		modificarUbicacionTransferenciaCommon,
		modificarUbicacionCompraCommon,
		guardarNroPisoCommon,
		eliminarNroPisoCommon,
		nuevoNroPisoCommon,
		filtrarPisoCommon,
		nuevoSectorCommon,
		filtrarSectorCommon,
		guardarsectorCommon,
		eliminarsectorCommon,
		generarRptPdfCajaCompraQrCommon,
		obtenerTransferenciaCajaQRCommon,
		obtenerNumeroCajaCommon,
		generarRptPdfCajaTransferenciaQrCommon,
		generarReporteCompraEntradaSeccionCommon,
		generarReporteCompraVentaSeccionCommon,
		generarRptPdfInventarioGondolaCommon,
		obtenerSucursalesInventarioCommon,
		generarReporteInventarioSeccionCommon,
		obtenerCotizacionBannerCommon,
		obtenerGondolasEncargadaSeccionCommon,
		reporteCajeroVentaCommon,
		busquedaCajeroCommon,
		filtrarSucursalUserCommon



		};
