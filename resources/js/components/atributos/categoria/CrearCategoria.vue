
<template>
	
<div class="container">

  <div v-if="$can('categoria.crear') && $can('atributos')">

    <!-- ------------------------------------------------------------------ -->

    <!-- MENSAJE DE ERROR SI NO HAY CONECCION  -->
        
    <mensaje v-bind:mostrar_error="mostrar_error, mensaje"></mensaje>

    <!-- ------------------------------------------------------------------------------------- -->
        
       <div class="offset-md-2 col-6">
      <div class="card mt-3 shadow-sm">
        <h5 class="card-header">Categorias</h5>
        <div class="card-body">
         
          <div class="row">

            <div class="col-12">
              <categoria-nombre ref="componente_textbox_categoria" @nombre_categoria='enviar_nombre' @existe_categoria='existe' :nombre='nombreCategoria' :validarCategoria='validarCategoria' @marcados_traer='marcados_traer' @marcaMarcados_traer='marcadosMarca_traer' @id='enviar_id' @traer_Descripcion='traer_descripcion' @traer_Marca='traer_marca' @traer_Color='traer_color' @traer_Genero='traer_genero' @traer_Talle='traer_talle' @traer_Tela='traer_tela' @traer_Temporada='traer_temporada'></categoria-nombre>
            </div>   
              <div class="col-12">

                   <hr>

                     <div class="form-group">
                          <label for="exampleFormControlTextarea1">Descripcion</label>
                      <textarea v-model="descripcionCategoria" class="form-control" id="exampleFormControlTextarea1" rows="3" v-bind:class="{ 'is-invalid': validarDescripcion }" ></textarea>
                      </div>
              </div>
              <hr>

           <div class="modal fade subcategoria-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalCenterTitle">Categorias: </small></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                      <table id="subcategorias" class="table table-striped table-bordered" style="width:30%"> 

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
                    <div class="modal fade Marca-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalCenterTitle">Categorias: </small></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                      <table id="Marcas" class="table table-striped table-bordered" style="width:30%"> 

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






          <div class=" mt-2 row col-12">
                <div class=" ml-3 form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" id="inlineCheckbox1"  v-model="atribMarca"  value=1>
                  <label class="form-check-label" for="inlineCheckbox1" title="Esta opción si esta activada es para que en Registro de productos puedas seleccionar una marca al momento de Registrar el Producto">MARCA</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" id="inlineCheckbox2" v-model="atribColor" value=1>
                  <label class="form-check-label" for="inlineCheckbox2" title="Esta opción si esta activada es para que en Registro de productos puedas seleccionar un color al momento de Registrar el Producto">COLOR</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" id="inlineCheckbox3"  v-model="atribTalle"  value=1 >
                  <label class="form-check-label" for="inlineCheckbox3" title="Esta opción si esta activada es para que en Registro de productos puedas seleccionar una talla (tamañp) al momento de Registrar el Producto">TALLE</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" id="inlineCheckbox4"  v-model="atribGenero"  value=1 >
                  <label class="form-check-label" for="inlineCheckbox4" title="Esta opción si esta activada es para que en Registro de productos puedas seleccionar un Genero al momento de Registrar el Producto">GENERO</label>
                </div>
                <div class=" form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" id="inlineCheckbox5"  v-model="atribTela"  value=1 >
                  <label class="form-check-label" for="inlineCheckbox5" title="Esta opción si esta activada es para que en Registro de productos puedas seleccionar una Tela al momento de Registrar el Producto">TELA  </label>
                </div>
                
          </div>
          <div class=" mt-1 row col-12">

                <div class=" ml-3 form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="inlineCheckbox6"  v-model="atribTemporada"  value=1 >
                        <label class="form-check-label" for="inlineCheckbox6" title="Esta opción si esta activada es para que en Registro de productos puedas seleccionar una Temporada al momento de Registrar el Producto">TEMPORADA</label>
                </div>
          </div>


         
              <div  class="col-3 mt-3 text-right">
                 <div class="btn-group" role="group">
                        
                          
                        
                     <button id="btnGroupDrop1" type="button" class="btn btn-dark dropdown-toggle" v-bind:class="{ 'text-danger': validarMarcados}"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Asignar
                     </button>
                     <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                       <a class="dropdown-item" href="#" data-toggle="modal" data-target=".subcategoria-modal" v-bind:class="{ 'text-danger': validarSubMarcados }">Subcategorias</a>
                       <a class="dropdown-item" href="#" data-toggle="modal" data-target=".Marca-modal" v-bind:class="{ 'text-danger': validarMarcaMarcados }">Marcas</a>
                     </div>
                  </div>
              </div> 

              <div  class="col-3 mt-3 text-right">
                    <button v-on:click="nuevaCategoria" type="submit" class="btn btn-primary">Nuevo(F2)</button>
              </div>   
                          
              <div v-if='guardar' class="col-3 mt-3 float-center">
                   <button v-on:click="guardarCategoria" type="submit" class="btn btn-primary">Guardar(F3)</button>
              </div>

              <div v-else class="col-3 mt-3 float-center">
                   <button v-on:click="guardarCategoria" type="submit" class="btn btn-warning">Editar(F3)</button>
              </div>
                    

              <div  class="col-3 mt-3 text-right">
                   <button v-on:click="eliminarCategoria" type="submit" class="btn btn-danger">Eliminar(F6)</button>
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
          validarCategoria:false,
          existeCategoria:false,
          guardar:true,
          radioPermisos:3,
          validarDescripcion:false,
          validarSubMarcados:false,
          validarMarcados:false,
          validarMarcaMarcados:false,
          permisosSelected:[] ,
          mostrar_error:false,
          mensaje:"",
          mostrarPermisos:false,
          nombreCategoria:"",
          validarCheck:false,
          descripcionCategoria:"",
          deshabilitar:false,
          marcados: [],
          marcado:[],
          info2:"",
          array_marcados: [],
          nuevoMarcados: [],
          marcadosMarca:[],
          atribMarca:0,
          atribGenero:0,
          atribTalle:0,
          atribColor:0,
          atribTemporada:0,
          atribTela:0
        }
      }, 
      methods: {
          existe(data){

          this.existeCategoria=data;
           if(data===false){
             this.guardar=true;
             this.descripcionCategoria="";
             this.limpiar();

           }else{
             this.guardar=false;
           }
          

        },
        traer_descripcion(data){
         
         this.descripcionCategoria=data;


        },
        traer_color(data){
          
         this.atribColor=data;

        },
        traer_genero(data){
          
         this.atribGenero=data;

        },
        traer_marca(data){
          
         this.atribMarca=data;

        },
        traer_talle(data){
          
         this.atribTalle=data;

        },
        traer_tela(data){
          
         this.atribTela=data;

        },
        traer_temporada(data){
          
         this.atribTemporada=data;

        },
          marcados_traer(data){
          
         //this.marcado=data;

         this.nuevoMarcados = data;
         if(this.nuevoMarcados.length>0){
           this.validarMarcados=false;
           this.validarSubMarcados=false;
         }else{
            this.validarMarcados=true;
            this.validarSubMarcados=true;
         }
        /* console.log(this.nuevoMarcados);*/
         this.Marcar();
        },
          marcadosMarca_traer(data){
          
         //this.marcado=data;

         this.marcadosMarca = data;
         if(this.marcadosMarca.length>0){
           this.validarMarcados=false;
           this.validarMarcaMarcados=false;
         }else{
            this.validarMarcados=true;
            this.validarMarcaMarcados=true;
         }
         /*console.log(this.marcadosMarca);*/
         this.MarcarMarca();
        },
        nuevaCategoria(){
          let me=this;
          me.nombreCategoria='';
          me.nuevoMarcados=[];
          me.marcadosMarca=[];

           me.limpiar();
          $('.call-checkbox').prop('checked', false);
        Common.nuevaCategoriaCommon().then(data => {
              me.nombreCategoria =data.categoria[0].CODIGO+1;
              me.guardar = true;    
            
              });
        
        },
         traer_descuento(data){
         this.descuento=data;
        },


          traer_fechaini(data){
         this.selectedInicialFecha=data;
        },
        traer_fechafin(data){
         this.selectedFinalFecha=data;
        },
        enviar_nombre(data){

          this.nombreCategoria=data;
           
        },
          Marcar(){
            let me=this;
           

             me.nuevoMarcados.map(function (x) {

              $("#"+x).prop('checked', true);
            });
                             
           
        },
          MarcarMarca(){
            let me=this;
           

             me.marcadosMarca.map(function (x) {
           /*  console.log(x);*/
              $("#"+x).prop('checked', true);
            });
                             
           
        },
        enviar_id(id){

          this.idPermiso=id;
        },
        limpiar(){
         
          let me =this;
         
          me.descripcionCategoria="";
          me.atribTemporada=0;
          me.atribTela=0;
          me.atribTalle=0;
          me.atribMarca=0;
          me.atribColor=0;
          me.atribGenero=0;
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
    Eliminar_Array_Marca(element){

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

if (this.marcadosMarca.length > 0) {
  this.marcadosMarca.removeItem(element);
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
              
            
             if(me.nombreCategoria===""){
               
              me.validarCategoria=true;
              return;
             } else {
              me.validarCategoria = false;
             
             } 
             if(me.descripcionCategoria===""){
              
              me.validarDescripcion=true;
              return;
             } else {
              me.validarDescripcion = false;
             } 
             if(me.nuevoMarcados.length<=0){
              me.validarMarcados=true;
              me.validarSubMarcados=true;
              return;
             }else{
              me.validarMarcados=false;
              me.validarSubMarcados=false;
             }
              if(me.marcadosMarca.length<=0){
              me.validarMarcados=true;
              me.validarMarcaMarcados=true;
              return;
             }else{
              me.validarMarcados=false;
              me.validarMarcaMarcados=false;
             }
            


             var data = {
              Codigo:me.nombreCategoria,
              Descripcion:me.descripcionCategoria,
              Existe:me.existeCategoria,
              Marcados:me.nuevoMarcados,
              MarcaMarcados:me.marcadosMarca,
              AtribMarca:me.atribMarca,
              AtribColor:me.atribColor,
              AtribGenero:me.atribGenero,
              AtribTela:me.atribTela,
              AtribTalle:me.atribTalle,
              AtribTemporada:me.atribTemporada
             }
              /*console.log("Marca: "+me.atribMarca);
             console.log("Genero: "+me.atribGenero);
             console.log("Tela: "+me.atribTela);
             console.log("Talle: "+me.atribTalle);
             console.log("Temporada: "+me.atribTemporada);
              console.log("Color: "+me.atribColor);*/

              Common.guardarCategoriaCommon(data).then(data => {
               if(data.response===true){
                  Swal.fire(
                     'Guardado!',
                     'Se ha guardado correctamente la Categoria!!!',
                     'success'
                  )
                    me.nuevaCategoria();
               }else{
                Swal.fire(
                     'Error!!',
                     data.statusText,
                     'warning'
                  )
                  me.nuevaCategoria();
               }
           
           			
           		}).catch((err) => {
                console.log(err);
                this.mostrar_error = true;
                this.mensaje = err;
                
              });
            /*  me.limpiar();*/


      	},
                eliminarCategoria(){

             let me =this;
             if(me.nombreCategoria===""){
               
              me.validarCategoria=true;
              return;
             } else {
              me.validarCategoria = false;
             
             } 

             var data = {
              Codigo:me.nombreCategoria,
              Existe:me.existeCategoria,
             }

              Common.eliminarCategoriaCommon(data).then(data => {
               if(data.response===true){
                  Swal.fire(
                     'Eliminado!',
                     'Se ha eliminado correctamente la Categoria!!!',
                     'success'
                  )
               }else{
                    Swal.fire(
                     'Error!',
                     'Este codigo de Categoria no existe o ya contiene productos registrados!!!',
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
              me.nuevaCategoria();
        }
         
       },
        mounted() {

          let me=this;
          var info = '';
           var info2='';
           var tableSubCategoria = $('#subcategorias').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy":true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                 },
                                 "url": "/subCategoriaDatatable",
                                 "dataType": "json",
                                 "type": "POST"
                               },
                        "columns": [
                            
                            { "data": "CODIGO" },
                            { "data": "DESCRIPCION" },
                            { "data": "FECALTAS" }
                        ],
                        "fnDrawCallback": function( oSettings ) {

                          me.info2 = tableSubCategoria.page.info();  

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
                    tableSubCategoria.columns.adjust().draw();
                     $('#subcategorias').on('click', 'tbody tr', function() {
                

                    // *******************************************************************
                          //me.marcados.map(function (x) {
                    
                   //         me.marcados[x]=me.marcados[x];
                           // $("#"+x).prop('checked', true);
                     //     });
                    info = tableSubCategoria.page.info();
                   
                   //me.marcados = $( tableSubCategoria.$('input[type="checkbox"]').map(function () {
                    //return $(this).prop("checked") ? $(this).closest('input').attr('id') : null;
                    //} ) );

                   $( tableSubCategoria.$('input[type="checkbox"]').map(function () {
                   
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
                  var tableMarcas = $('#Marcas').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "destroy":true,
                        "bAutoWidth": true,
                        "select": true,
                        "ajax":{
                                 "data": {
                                    "_token": $('meta[name="csrf-token"]').attr('content')
                                 },
                                 "url": "/MarcasPorCategoriaDatatable",
                                 "dataType": "json",
                                 "type": "POST"
                               },
                        "columns": [
                            
                            { "data": "CODIGO" },
                            { "data": "DESCRIPCION" },
                            { "data": "FECALTAS" }
                        ],
                        "fnDrawCallback": function( oSettings ) {

                          me.info2 = tableMarcas.page.info();  

                          //console.log(me.array_marcados);
                       //   me.marcados.map(function (x) {
                           
                           // 
                          //});
               
                          //   if(me.array_marcados[me.info2.page]!==undefined){
                           //   me.array_marcados[me.info2.page].map(function (x) {
                             //   $("#"+me.array_marcados[me.info2.page][x]).prop('checked', true);
                              //  });
                            // }
                              
                           me.MarcarMarca();
                            

                          
                        }      
                    }); 

                    tableMarcas.columns.adjust().draw();
                     $('#Marcas').on('click', 'tbody tr', function() {
                

                    // *******************************************************************
                          //me.marcados.map(function (x) {
                    
                   //         me.marcados[x]=me.marcados[x];
                           // $("#"+x).prop('checked', true);
                     //     });
                    info = tableMarcas.page.info();
                   
                   //me.marcados = $( tableSubCategoria.$('input[type="checkbox"]').map(function () {
                    //return $(this).prop("checked") ? $(this).closest('input').attr('id') : null;
                    //} ) );

                   $( tableMarcas.$('input[type="checkbox"]').map(function () {
                   
                                       if  ($(this).prop("checked"))  {
                                         // SI ESTA MARCADO A GREGAR 

                                         if (me.marcadosMarca.includes(($(this).closest('input').attr('id'))) === false) {
                                            me.marcadosMarca.push(($(this).closest('input').attr('id')));
                                         }
                                         
                                       } else {
                                        //alert($(this).closest('input').attr('id'));
                                         me.Eliminar_Array_Marca(($(this).closest('input').attr('id')));
                                         //return null;
                                       };
                                       } ) );
                   
                  //  if(me.marcado.length>0){
                   //   me.marcados.map(function(x){
                    //  me.Eliminar_Array(me.marcados[x]);
                    //});
                   // }

                    //me.array_marcados[info.page] = me.marcados;
                           // $("#"+x).prop('checked', true);
            
                    //rows.map(function)
                   

                    
                    // *******************************************************************

                    // CERRAR EL MODAL
                     
                    


                    // *******************************************************************

                });    
                     
    hotkeys('f2', function(event, handler){

  event.preventDefault() 
   me.nuevaCategoria();
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
   me.eliminarCategoria();
});

        }
    }
</script>