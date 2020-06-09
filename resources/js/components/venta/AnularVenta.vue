
<template>
	
<div class="container">
  <!-- ------------------------------------------------------------------ -->

  <!-- MENSAJE DE ERROR SI NO HAY CONECCION  -->
      
  <mensaje v-bind:mostrar_error="mostrar_error, mensaje"></mensaje>

  <!-- ------------------------------------------------------------------------------------- -->
      
     <div class="offset-md-2 col-8">
    <div class="card mt-3 shadow-sm">
      <h5 class="card-header">Anulacion De Ventas</h5>
      <div class="card-body">
       
        <div class="row" >

           <div class="col-md-6">
             <div class="form-input form-input-inline">
              <ventas-id class="form-input-inline form-input-sm" ref="componente_textbox_Ventas" @codigo='traer_codigo'  :nombre='codigo' :validarVenta='validarVenta' @codigoCaja='traer_CodigoCaja' @fecha='traer_fecha' @hora='traer_hora' @tipo='traer_tipo' @moneda='traer_moneda' @total='traer_total' @vendedor='traer_vendedor' @cliente='traer_cliente' v-model='codigo'></ventas-id>
            </div>    
          </div>            
            <div class="col-md-6">
              <label >Caja:</label>
            <input v-model="caja" type="text" class="form-control form-control-sm"  id="formGroupExampleInput1 "  :tabindex=1 v-bind:class="{ 'is-invalid': validarCaja }" disabled >
            </div>
 

  <hr>
        <div class="col-md-3 mt-3">
          <label for="selectedInicialFecha">Fecha:</label>
                   <input type="text" class="form-control form-control-sm" id="selectedInicialFecha" data-date-format="yyyy-mm-dd" v-model="selectedInicialFecha" v-bind:class="{ 'is-invalid': validarInicialFecha }"/>
                
        </div>
                 
             
          <div class="col-md-3 mt-3">
            <label for="formGroupExampleInput2">Codigo_Ca:</label>
          <input v-model="codigo_ca" type="text" class="form-control form-control-sm"  id="formGroupExampleInput2 " :tabindex=3 v-on:blur="filtrarVenta"  >
          </div>

          <div class="col-md-3 mt-3">
            <label for="formGroupExampleInput2">Hora:</label>
          <input v-model="hora" type="text" class="form-control form-control-sm"  id="formGroupExampleInput2 " :tabindex=3   >
          </div>
          <div class="col-md-3 mt-3">
             <label for="formGroupExampleInput3">Tipo:</label>
          <input v-model="tipo" type="text" class="form-control form-control-sm"  id="formGroupExampleInput3 " :tabindex=4   >
          </div>
         
          <div class="col-md-6 mt-3">
            <label >Cliente:</label>
            <input v-model="cliente" type="text" class="form-control form-control-sm"  id="formGroupExampleInput1 " :tabindex=5    >
          </div>
          <div class="col-md-6 mt-3">
            <label >Vendedor:</label>
            <input v-model="vendedor" type="text" class="form-control form-control-sm"  id="formGroupExampleInput1 " :tabindex=6   >
          </div>
          <div class="col-md-6 mt-3">
            <label >Moneda:</label>
            <input v-model="mon" type="text" class="form-control form-control-sm"  id="formGroupExampleInput1 " :tabindex=7   >
          </div>
            <div class="col-md-6 mt-3">
            <label >Total:</label>
            <input v-model="total" type="text" class="form-control form-control-sm"  id="formGroupExampleInput1 " :tabindex=8   >
          </div>
        


            <div class="col-12">

                 <hr>

            </div>
               
              

             </div>
                    <div  class="col-11 mt-3 text-right">
                     <button v-on:click="anularVenta" type="submit" class="btn btn-danger">Anular(F6)</button>
                  </div>

    </div>
  </div>
</div>
     </div>


</div>

</template>
<script>
	 export default {
      props: ['moneda'],
      data(){
        return {
          menu : 0,
          messageInvalidFecha: '',
          idPermiso:"",
        
          switch_descuento:false,
          validarVenta:false,
          validarDescuento:false,
          descuento:"",
          codigo_ca:"",
          codigo:"",
          total:'',
          existeTela:false,
          selectedFinalFecha: '',
          validarFinalFecha: false,
          selectedInicialFecha: '',
          validarInicialFecha: false,
          guardar:true,
          hora:'',
          tipo:'',
          mon:'',
          validarCaja:'',
          deshabilitarCaja:true,
          radioPermisos:3,
          validarDescripcion:false,
          permisosSelected:[] ,
          mostrar_error:false,
          mensaje:"",
          caja:"",
          vendedor:"",
          ip:window.IP,
          cliente:"",
          mostrarPermisos:false,
          nombreGondola:"",
          validarCheck:false,
          descripcionTela:"",
          deshabilitar:false,
        }
      }, 
      methods: {
          existe(data){

          this.existeTela=data;
           if(data===false){
             this.guardar=true;
             this.descripcionTela="";
           }else{
             this.guardar=false;
           }
          

        },
           traer_descripcion(data){
          
         this.descripcionTela=data;

        },
         traer_hora(data){
         this.hora=data;
        },

         traer_tipo(data){
         this.tipo=data;
        },
          traer_fecha(data){
         this.selectedInicialFecha=data;
        },

        traer_codigo(data){

          this.codigo=data;
           
        },

        traer_CodigoCaja(data){

          this.codigo_ca=data;
           
        },
          traer_cliente(data){

          this.cliente=data;
           
        },
        traer_vendedor(data){

          this.vendedor=data;
           
        },
           traer_moneda(data){

          this.mon=data;
           
        },
          traer_total(data){

          this.total=data;
           
        },
        enviar_id(id){

          this.idPermiso=id;
        },
        limpiar(){
         
          let me =this;
         me.codigo=""
         me.selectedInicialFecha= ""
         me.hora=""
         me.codigo_ca= ""
         me.tipo=""
         me.cliente=""
         me.vendedor=""
         me.total=""
         me.mon=""
        },
        timepicker(){
          let me=this;
           $(function(){
              $('#sandbox-container .input-daterange').datepicker({
                    keyboardNavigation: false,
                forceParse: false, format:"yyyy-mm-dd"
            });
            $("#selectedInicialFecha").datepicker().on(
              "changeDate", () => {me.selectedInicialFecha = $('#selectedInicialFecha').val()}
          );
          $("#selectedFinalFecha").datepicker().on(
              "changeDate", () => {me.selectedFinalFecha = $('#selectedFinalFecha').val()}
          );

          $('table').dataTable();
          });
         },

                filtrarVenta(){


             let me =this;
 
             if(me.selectedInicialFecha===""){
              
              me.validarInicialFecha=true;
              return;
             } else {
              me.validarInicialFecha= false;
             } 

             var data = {
              co_ca:me.codigo_ca,
              fecha:me.selectedInicialFecha,
              codigo:me.codigo,
              caja:me.caja,
             }
              Common.filtrarVentasCommon(data).then(data => {
                        if(data.response===true){
                           me.codigo=data.ventas[0].CODIGO;
                           me.caja=data.ventas[0].CAJA;
                           me.selectedInicialFecha= data.ventas[0].FECHA;
                           me.hora=data.ventas[0].HORA;
                           me.codigo_ca= data.ventas[0].CODIGO_CA;
                           me.tipo=data.ventas[0].TIPO;
                           me.cliente=data.ventas[0].NOMBRE;
                           me.vendedor=data.ventas[0].NOMBRE_E;
                           me.total=data.ventas[0].TOTAL;
                           me.mon=data.ventas[0].MONEDA;

                        }
                      
                           
                    })


        },
               anularVenta(){
   
             let me =this;
             if(me.codigo===""){
               
              me.validarVenta=true;
              return;
             } else {
              me.validarVenta = false;
             
             } 
              if(me.caja===""){
               
              me.validarCaja=true;
              return;
             } else {
              me.validarCaja = false;
             
             }

             var data = {
              codigo:me.codigo,
              caja:me.caja,
             }
             Swal.fire({
          title: 'Estas seguro ?',
          text: "anular la venta " + me.codigo + " !",
          type: 'warning',
          showLoaderOnConfirm: true,
          showCancelButton: true,
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Si, anulalo!',
          cancelButtonText: 'Cancelar',
          preConfirm: () => {
            return  Common.anularVentaCommon(data).then(data => {
              if (!data.response === true) {
                  throw new Error(data.statusText);
                }
              return data;
            }).catch(error => {
                Swal.showValidationMessage(
                  `Request failed: ${error}`
                )
            });
          }
        }).then((result) => {
          if (result.value) {


            Swal.fire(
                  'Anulado !',
                  'Se ha anulado la venta !',
                  'success'
          )
            me.$refs.componente_textbox_Ventas.recargar();
              me.limpiar();
                }
        })

              
            
              
        },
       Accesos(){
        let me =this;
          
         if(me.radioPermisos==="1"){

              me.deshabilitar=true;
              me.permisos.map(function(x) {
                 me.permisosSelected.push(x.id);
              });


         }else{

          if(me.radioPermisos==="2"){
             me.permisosSelected=[];
             me.deshabilitar=true;
          }else{ 

            me.deshabilitar=false;

          }

         }
          
        }
         
       },
        mounted() {
          
          let me=this;
          Common.obtenerIPCommon(function(){
                axios.post('/cajaObtener', {'id': window.IPv}).then(function (response) {
              
              me.caja  =   response.data.caja[0].CAJA;
              me.$refs.componente_textbox_Ventas.datatableMostrar(response.data.caja[0].CAJA);
            })

     });
        


                 

          
        /* Common.obtenerCajaCommon().then(data => {
                        if(data.response===true){
                           me.caja=data.caja[0].CAJA;

                        }
                      
                           
                    })
*/
          
                    $(function(){
              $('#sandbox-container .input-daterange').datepicker({
                    keyboardNavigation: false,
                forceParse: false
            });
            $("#selectedInicialFecha").datepicker().on(
              "changeDate", () => {me.selectedInicialFecha = $('#selectedInicialFecha').val()}
          );

      });
   
    hotkeys('f4', function(event, handler){

  event.preventDefault() 
   me.limpiar();
});
      

        }
    }
</script>