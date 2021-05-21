
<template>
	
<div class="container">

  <div v-if="$can('subcategoria.crear') && $can('atributos')">

    <!-- ------------------------------------------------------------------ -->

    <!-- MENSAJE DE ERROR SI NO HAY CONECCION  -->
        
    <mensaje v-bind:mostrar_error="mostrar_error, mensaje"></mensaje>

    <!-- ------------------------------------------------------------------------------------- -->
        
       <div class="offset-md-2 col-6">
      <div class="card mt-3 shadow-sm">
        <h5 class="card-header">SubCategorias</h5>
        <div class="card-body">
         
          <div class="row">

            <div class="col-12">
              <subcategoria-nombre ref="componente_textbox_categoria" @nombre_Subcategoria='enviar_nombre' @existe_Subcategoria='existe' :nombre='nombreSubCategoria' :validarSubCategoria='validarSubCategoria' @marcados_traer='marcados_traer'  @id='enviar_id' @descripcion='traer_descripcion'></subcategoria-nombre>
            </div>   
              <div class="col-12">

                   <hr>

                     <div class="form-group">
                          <label for="exampleFormControlTextarea1">Descripcion</label>
                      <textarea v-model="descripcionSubCategoria" class="form-control" id="exampleFormControlTextarea1" rows="3" v-bind:class="{ 'is-invalid': validarDescripcion }" ></textarea>
                      </div>
              </div>
              <hr>

           <div class="modal fade categoria-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalCenterTitle">Categorias: </small></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                      <table id="categorias" class="table table-striped table-bordered" style="width:30%"> 

                                <thead>

                                 <tr>
                                   <th>CODIGO</th>
                                   <th>DESCRIPCION</th>
                                   <th>FECHA CREACION</th>
                                 </tr>
                                </thead>
                         <tbody>
                            <tr>
                              <td></td>
                              <td v-on:click="() => alert('hola')"></td>
                            </tr>
                         </tbody>
                    </table>    
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                          </div>
                        </div>
                      </div>
                    </div>  









                  <hr>
  <div  class="col-3 mt-3 text-right">
                  <div class="btn-group" role="group">
                 
                   
                 
      <button id="btnGroupDrop1" type="button" class="btn btn-dark dropdown-toggle" v-bind:class="{ 'text-danger': validarMarcados}"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
       Asignar
      </button>
      <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
        <a class="dropdown-item" href="#" data-toggle="modal" data-target=".categoria-modal" v-bind:class="{ 'text-danger': validarCatMarcados }">Categorias</a>
      </div>
    </div>
         </div>           <div  class="col-3 mt-3 text-right">
                       <button v-on:click="nuevaSubCategoria" type="submit" class="btn btn-primary">Nuevo(F2)</button>
                    </div>
                     
                      
                    <div v-if='guardar' class="col-3 mt-3 float-center">
                       <button v-on:click="guardarCategoria" type="submit" class="btn btn-primary">Guardar(F3)</button>
                    </div>
                     <div v-else class="col-3 mt-3 float-center">
                       <button v-on:click="guardarCategoria" type="submit" class="btn btn-warning">Editar(F3)</button>
                    </div>
                

                      <div  class="col-3 mt-3 text-right">
                       <button v-on:click="eliminarSubCategoria" type="submit" class="btn btn-danger">Eliminar(F6)</button>
                    </div>
                     




      </div>
     </div>
   </div>
  </div>

  </div>

  <!-- ------------------------------------------------------------------------ -->

  <div v-else>
    <cuatrocientos-cuatro></cuatrocientos-cuatro>
  </div>

  <!-- ------------------------------------------------------------------------ -->
  
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
          validarSubCategoria:false,
          existeSubCategoria:false,
          guardar:true,
          radioPermisos:3,
          validarDescripcion:false,
          validarCatMarcados:false,
          validarMarcados:false,
          validarMarcaMarcados:false,
          permisosSelected:[] ,
          mostrar_error:false,
          mensaje:"",
          mostrarPermisos:false,
          nombreSubCategoria:"",
          validarCheck:false,
          descripcionSubCategoria:"",
          deshabilitar:false,
          marcados: [],
          marcado:[],
          info2:"",
          array_marcados: [],
          nuevoMarcados: [],
          marcadosMarca:[]
        }
      }, 
      methods: {
          existe(data){

          this.existeSubCategoria=data;
           if(data===false){
             this.guardar=true;
             this.descripcionSubCategoria="";

           }else{
             this.guardar=false;
           }
          

        },
           traer_descripcion(data){
          
         this.descripcionSubCategoria=data;

        },
          marcados_traer(data){
          
         //this.marcado=data;

         this.nuevoMarcados = data;
         if(this.nuevoMarcados.length>0){
           this.validarMarcados=false;
           this.validarCatMarcados=false;
         }else{
            this.validarMarcados=true;
            this.validarCatMarcados=true;
         }
         console.log(this.nuevoMarcados);
         this.Marcar();
        },
        nuevaSubCategoria(){
          let me=this;
          me.nuevoMarcados=[];
          $('.call-checkbox').prop('checked', false);
        Common.nuevaSubCategoriaCommon().then(data => {
              me.nombreSubCategoria =data.subCategoria[0].CODIGO+1;
              me.guardar = true;    
              });
         me.limpiar();
        },

        enviar_nombre(data){

          this.nombreSubCategoria=data;
        },
          Marcar(){
            let me=this;
           

             me.nuevoMarcados.map(function (x) {

              $("#"+x).prop('checked', true);
            });
                             
           
        },

        enviar_id(id){

          this.idPermiso=id;
        },
        limpiar(){
         
          let me =this;
          me.nombreSubCategoria="";
          me.descripcionSubCategoria="";
        },

        Eliminar_Array(element){

    Array.prototype.removeItem = function (a) {
    for (var i = 0; i < this.length; i++) {
  if (this[i] == a) {
   for (var i2 = i; i2 < this.length - 1; i2++) {
    this[i2] = this[i2 + 1];
   }
   this.length = this.length - 1;
   return;
  }
 }
};

if (this.nuevoMarcados.length > 0) {
  this.nuevoMarcados.removeItem(element);
}
//console.log(this.marcado); //

         
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
      	guardarCategoria(){
              //console.log("este es nuevo marcados");
              //console.log(this.nuevoMarcados);
              //return;
             let me =this;
             if(me.nombreSubCategoria===""){
               
              me.validarSubCategoria=true;
              return;
             } else {
              me.validarSubCategoria = false;
             
             } 
             if(me.descripcionSubCategoria===""){
              
              me.validarDescripcion=true;
              return;
             } else {
              me.validarDescripcion = false;
             } 
             if(me.nuevoMarcados.length<=0){
              me.validarMarcados=true;
              me.validarCatMarcados=true;
              return;
             }else{
              me.validarMarcados=false;
              me.validarCatMarcados=false;
             }

             var data = {
              Codigo:me.nombreSubCategoria,
              Descripcion:me.descripcionSubCategoria,
              Existe:me.existeSubCategoria,
              Marcados:me.nuevoMarcados,
             }

              Common.guardarSubCategoriaCommon(data).then(data => {
               if(data.response===true){
                  Swal.fire(
                     'Guardado!',
                     'Se ha guardado correctamente la SubCategoria!!!',
                     'success'
                  )
               }else{
                Swal.fire(
                     'Error!!',
                     data.statusText,
                     'warning'
                  )
               }
           
           			
           		}).catch((err) => {
                console.log(err);
                this.mostrar_error = true;
                this.mensaje = err;
              });
              me.limpiar();
      	},
                eliminarSubCategoria(){

             let me =this;
             if(me.nombreSubCategoria===""){
               
              me.validarSubCategoria=true;
              return;
             } else {
              me.validarSubCategoria = false;
             
             } 

             var data = {
              Codigo:me.nombreSubCategoria,
              Existe:me.existeSubCategoria,
             }

              Common.eliminarSubCategoriaCommon(data).then(data => {
               if(data.response===true){
                  Swal.fire(
                     'Eliminado!',
                     'Se ha eliminado correctamente la SubCategoria!!!',
                     'success'
                  )
               }else{
                    Swal.fire(
                     'Error!',
                     'Este codigo de SubCategoria no existe o ya contiene productos registrados!!!',
                     'warning'
                  )
               }
        
             me.$refs.componente_textbox_categoria.recargar();
                
              }).catch((err) => {
                console.log(err);
                this.mostrar_error = true;
                this.mensaje = err;
              });
              
              me.limpiar();
              me.nuevaSubCategoria();
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
          var info = '';
           var info2='';
           var tableCategoria = $('#categorias').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy":true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                 },
                                 "url": "/CategoriasPorSubCategoriasDatatable",
                                 "dataType": "json",
                                 "type": "POST"
                               },
                        "columns": [
                            
                            { "data": "CODIGO" },
                            { "data": "DESCRIPCION" },
                            { "data": "FECALTAS" }
                        ],
                        "fnDrawCallback": function( oSettings ) {

                          me.info2 = tableCategoria.page.info();  

                          //console.log(me.array_marcados);
                       //   me.marcados.map(function (x) {
                           
                           // 
                          //});
               
                          //   if(me.array_marcados[me.info2.page]!==undefined){
                           //   me.array_marcados[me.info2.page].map(function (x) {
                             //   $("#"+me.array_marcados[me.info2.page][x]).prop('checked', true);
                              //  });
                            // }
                              
                           me.Marcar();
                            

                          
                        }      
                    });
                    tableCategoria.columns.adjust().draw();
                     $('#categorias').on('click', 'tbody tr', function() {
                

                    // *******************************************************************
                          //me.marcados.map(function (x) {
                    
                   //         me.marcados[x]=me.marcados[x];
                           // $("#"+x).prop('checked', true);
                     //     });
                    info = tableCategoria.page.info();
                   
                   //me.marcados = $( tableCategoria.$('input[type="checkbox"]').map(function () {
                    //return $(this).prop("checked") ? $(this).closest('input').attr('id') : null;
                    //} ) );

                   $( tableCategoria.$('input[type="checkbox"]').map(function () {
                   
                                       if  ($(this).prop("checked"))  {
                                         // SI ESTA MARCADO A GREGAR 

                                         if (me.nuevoMarcados.includes(parseInt($(this).closest('input').attr('id'))) === false) {
                                            me.nuevoMarcados.push(parseInt($(this).closest('input').attr('id')));
                                         }
                                         
                                       } else {
                                        //alert($(this).closest('input').attr('id'));
                                         me.Eliminar_Array(parseInt($(this).closest('input').attr('id')));
                                         //return null;
                                       };
                                       } ) );
                   
//                    if(me.marcado.length>0){
  //                    me.marcados.map(function(x){
    //                  me.Eliminar_Array(me.marcados[x]);
      //              });
        //            }

          //          me.array_marcados[info.page] = me.marcados;
                           // $("#"+x).prop('checked', true);
            
                    //rows.map(function)
                   

                    
                    // *******************************************************************

                    // CERRAR EL MODAL
                     
                    


                    // *******************************************************************

                });
                
                     
    hotkeys('f2', function(event, handler){

  event.preventDefault() 
   me.nuevaSubCategoria();
});
  hotkeys('f3', function(event, handler){

  event.preventDefault() 
   me.guardarCategoria();
});
    hotkeys('f4', function(event, handler){

  event.preventDefault() 
   me.limpiar();
});
        hotkeys('f6', function(event, handler){

  event.preventDefault() 
   me.eliminarSubCategoria();
});

        }
    }
</script>