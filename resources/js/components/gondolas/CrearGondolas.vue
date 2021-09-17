
<template>
	<div class="container mt-3">
    <div v-if="$can('gondola.crear') && $can('gondola') && $can('configuracion')">
    <!-- MENSAJE DE ERROR SI NO HAY CONECCION  -->     
      <mensaje v-bind:mostrar_error="mostrar_error, mensaje"></mensaje>
          <div class="col-7">
            <div class="card shadow border-bottom-primary mb-3">
              <h5 class="text-center card-header">Gondolas</h5>
              <div class="card-body">            
                <div class="row">
                  <div class="col-12">
                    <div class="mb-3">
                      <gondola-nombre ref="componente_textbox_Gondola" @nombre_gondola='enviar_nombre' @existe_gondola='existe' :nombre='nombreGondola' :validarGondola='validarGondola' :rack='rack' @seccion="enviar_seccion" @id='enviar_id' @descripcion='traer_descripcion' @pisos='traer_pisos' @sectores='traer_sectores'></gondola-nombre>
                    </div>

                    <div class="invalid-feedback">{{messageInvalidSeccion}}</div>
                    <div class="mb-3">
                      <label for="exampleFormControlTextarea1" class="form-label">Descripción</label>
                      <input v-model="descripcionTela" type="text" class="form-control form-control-sm" id="exampleFormControlTextarea1" v-bind:class="{ 'is-invalid': validarDescripcion}">
                    </div>


                    <div class="mb-3">
                      <label for="validationTooltip01">Seleccionar Sección</label>
                      <select class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validarSeccion }" v-model="selectedSeccion">
                        <option value="null" selected>Seleccionar</option>
                        <option v-for="seccion in secciones" :value="seccion.ID_SECCION">{{ seccion.DESCRIPCION }}</option>
                      </select>
                    </div>

                <div class="row">
                   <div class="col-md-1"></div>
                      <div v-if="rack==='SI'" class="col-md-4">


                        <label for="validationTooltip01">Seleccione Pisos</label> 
                        <div class="container_checkbox1 rounded">
                                    <div class="ml-3" v-for="piso in pisos">
                                      <div class="custom-control custom-checkbox">
                                        <input  type="checkbox" class="custom-control-input" :disabled="onPiso" 
                                        :value="piso.NRO_PISO" 
                                        :id='"Piso_"+piso.ID' 
                                        v-model="selectedPiso" 
                                        v-on:change="marcar_inferiores_piso"
                                        v-bind:class="{ 'is-invalid': validarPiso }">
                                        <label class="custom-control-label" :for='"Piso_"+piso.ID' >{{piso.DESCRIPCION }}</label>
                                      </div>
                                    </div>
                                </div>
                        <div>
                              <div class="form-text text-danger">{{messageInvalidPiso}}</div>
                          </div>
                        <div  class="custom-control custom-switch mt-3">
                          <input type="checkbox" class="custom-control-input" id="customSwitch1" v-model="onPiso" v-on:change="seleccionarTodoPiso">
                          <label class="custom-control-label" for="customSwitch1" >Seleccionar todos</label>
                        </div>


                    </div>
                      <div class="col-md-2"></div>
                       <div v-if="rack==='SI'" class="col-md-4">


                        <label for="validationTooltip01">Seleccione Sectores</label> 
                        <div class="container_checkbox1 rounded">
                                    <div class="ml-3" v-for="sector in sectores">
                                      <div class="custom-control custom-checkbox">
                                        <input  type="checkbox" class="custom-control-input" :disabled="onSector" 
                                        :value="sector.DESCRIPCION" 
                                        :id='"Sectores_"+sector.ID' 
                                        v-model="selectedSector" 
                                        v-on:change="marcar_inferiores_sector"
                                        v-bind:class="{ 'is-invalid': validarSector }">
                                        <label class="custom-control-label" :for='"Sectores_"+sector.ID' >{{sector.DESCRIPCION }}</label>
                                      </div>
                                    </div>
                                </div>
                        <div>
                              <div class="form-text text-danger">{{messageInvalidSector}}</div>
                          </div>
                        <div  class="custom-control custom-switch mt-3">
                          <input type="checkbox" class="custom-control-input" id="customSwitch2" v-model="onSector" v-on:change="seleccionarTodoSector">
                          <label class="custom-control-label" for="customSwitch2" >Seleccionar todos</label>
                        </div>


                    </div>
                
                </div>
                    




                      <div class="row mt-3">
                        <div class="col" align="left">
                          <button v-on:click="nuevaGondola" type="submit" class="btn btn-primary">Nuevo (F2)</button>
                        </div>
                        <div class="col" align="center">
                          <div v-if='guardar'>
                          <button v-on:click="guardarGondola" type="submit" class="btn btn-success" >Guardar (F3)</button>
                        </div>
                        <div v-else>
                          <button v-on:click="guardarGondola" type="submit" class="btn btn-warning">Actualizar (F3)</button>
                        </div>
                        </div>
                        <div class="col" align="right">
                          <button v-on:click="eliminarGondola" type="submit" class="btn btn-danger">Eliminar (F6)</button>
                        </div>
                             
                      </div>
                  </div>   
              </div>
            </div>
          </div>
        </div>
    </div>
    <div v-else>
      <cuatrocientos-cuatro></cuatrocientos-cuatro>
    </div>
  </div>
</template>
<script>
	 export default {
      props: ['moneda'],
      data(){
        return {
          mayor : 0,
          mayor_sector:0,
          messageInvalidFecha: '',
          idPermiso:"",
          switch_descuento:false,
          validarGondola:false,
          validarDescuento:false,
          descuento:"",
          existeTela:false,
          selectedFinalFecha: '',
          validarFinalFecha: false,
          selectedInicialFecha: '',
          validarInicialFecha: false,
          guardar:true,
          radioPermisos:3,
          validarDescripcion:false,
          permisosSelected:[] ,
          mostrar_error:false,
          mensaje:"",
          mostrarPermisos:false,
          nombreGondola:"",
          validarCheck:false,
          descripcionTela:"",
          deshabilitar:false,
          validarSeccion: false,
          validarPiso:false,
          secciones: [],
          sectores:[],
          pisos:[],
          onPiso:false,
          onSector:false,
          messageInvalidSeccion: '',
          messageInvalidPiso:'',
          validarSector:false,
          messageInvalidSector:'',
          selectedSeccion:"null",
          selectedPiso:[],
          selectedSector:[],
          selectedSectorID:[],
          SelectedPisoID:[],
          rack:'',
          AnteriorPiso:[] ,
          DesmarcadoPiso:[],
          AnteriorSector:[] ,
          DesmarcadoSector:[],
          NuevosPisos:[],
          NuevosSectores:[],
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
        nuevaGondola(){
          let me=this;
        Common.nuevaGondolaCommon().then(data => {
              me.nombreGondola =data.Gondola[0].CODIGO+1;

              me.guardar = true;    
              });
              me.limpiar();
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

          this.nombreGondola=data;
           
        },
        enviar_id(id){
          
          this.idPermiso=id;
        },
        enviar_seccion(data){

          this.selectedSeccion=data;
        },
         traer_pisos(pisos_marcados){
          let me =this;
            me.SelectedPisoID=[];
            me.selectedPiso=[];
           /* me.AnteriorPiso=pisos_marcados;*/
             for (var key in pisos_marcados){
                 me.SelectedPisoID[key]=pisos_marcados[key].ID;
                 me.selectedPiso[key]=pisos_marcados[key].NRO_PISO;
                 me.AnteriorPiso.push({
                      "ID": pisos_marcados[key].ID,
                      "NRO_PISO":pisos_marcados[key].NRO_PISO,
                      "MARCADO":true,
                 });
                

             }
          
           /*console.log(me.AnteriorPiso);*/
           
            
        },
        traer_sectores(sectores_marcados){
          
          let me =this;
            me.selectedSector=[];
            me.selectedSectorID=[];
            me.AnteriorSector=[];
            for (var key in sectores_marcados){
                 me.selectedSectorID[key]=sectores_marcados[key].ID;
                 me.selectedSector[key]=sectores_marcados[key].DESCRIPCION;
                 me.AnteriorSector.push({
                      "ID": sectores_marcados[key].ID,
                      "DESCRIPCION":sectores_marcados[key].DESCRIPCION,
                      "MARCADO":true,
                 });
               
             }
          
          
           
             
        },
        limpiar(){
         
          let me =this;
          me.descripcionTela="";
          me.selectedSeccion="null";
          me.$refs.componente_textbox_Gondola.recargar();
          me.selectedPiso=[];
          me.SelectedPisoID=[];
          me.selectedSector=[];
          me.selectedSectorID=[];
          me.DesmarcadoPiso=[];
          me.DesmarcadoSector=[];
          me.NuevosPisos=[];
          me.NuevosSectores=[];
          me.AnteriorPiso=[];
          me.AnteriorSector=[];
          me.guardar= true;
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
      	guardarGondola(){

             let me =this;
             if(me.nombreGondola===""){
               
              me.validarGondola=true;
              return;
             } else {
              me.validarGondola = false;
             
             } 
             if(me.descripcionTela===""){
              me.validarDescripcion=true;
              return;
             } else {
              me.validarDescripcion = false;
             } 
             if(me.rack==='SI'){
                if(me.selectedPiso.length===0){
                  me.validarPiso=true;
                  me.messageInvalidPiso="Por Favor Seleccione la cantidad de pisos.";
                  return
                }else{
                  me.validarPiso=false;
                  me.messageInvalidPiso="";
                }
                if(me.selectedSector.length===0){
                  me.validarSector=true;
                  me.messageInvalidSector="Por Favor Seleccione la cantidad de sectores.";
                  return
                }else{
                  me.validarSector=false;
                  me.messageInvalidSector="";
                }
                me.verificar_desmarcados_pisos();
                me.verificar_desmarcados_sectores();
                me.verificar_marcados_pisos();
                me.verificar_marcados_sectores();
                
                  var data = {
                  Codigo:me.nombreGondola,
                  Descripcion:me.descripcionTela,
                  SeccionGuardar:me.selectedSeccion,
                  Existe:me.existeTela,
                  Rack:me.rack,
                  Piso:me.SelectedPisoID,
                  Sector:me.selectedSectorID,
                  PisosDesmarcados:me.DesmarcadoPiso,
                  SectoresDesmarcados:me.DesmarcadoSector,
                  PisosNuevos:me.NuevosPisos,
                  SectoresNuevos:me.NuevosSectores
                 }
               }else{
                  var data = {
                    Codigo:me.nombreGondola,
                    Descripcion:me.descripcionTela,
                    SeccionGuardar:me.selectedSeccion,
                    Existe:me.existeTela,
                    Rack:me.rack

                   }
               }

            

              Common.guardarGondolaCommon(data).then(data => {
               if(data.response===true){
                  Swal.fire(
                     'Guardado!',
                     'Se ha guardado correctamente la gondola!!!',
                     'success'
                  )
                  window.location.href='/gond1';
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
              me.nuevaGondola();
              me.$refs.componente_textbox_Gondola.recargar();
      	},
                eliminarGondola(){

             let me =this;
             if(me.nombreGondola===""){
               
              me.validarGondola=true;
              return;
             } else {
              me.validarGondola = false;
             
             } 

             var data = {
              Codigo:me.nombreGondola,
              Existe:me.existeTela,
             }

              Common.eliminarGondolaCommon(data).then(data => {
               if(data.response===true){
                  Swal.fire(
                     'Eliminado!',
                     'Se ha eliminado correctamente la Gondola!!!',
                     'success'
                  )
                  window.location.href='/gond1';
               }else{
                    Swal.fire(
                     'Error!',
                     data.statusText,
                     'warning'
                  )
               }
             
                
              }).catch((err) => {
                console.log(err);
                this.mostrar_error = true;
                this.mensaje = err;
              });
              
              me.nuevaGondola();
              me.$refs.componente_textbox_Gondola.recargar();
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
          
        },
          seleccionarTodoPiso(){

            let me = this;

               me.SelectedPisoID=[];
            if(me.onPiso === true) {
               me.validarPiso=false;
                me.messageInvalidPiso="";
              for (var key in me.pisos){
                   me.SelectedPisoID[key]=me.pisos[key].ID;
                me.selectedPiso[key] = me.pisos[key].NRO_PISO;
              }
          }else{
            me.selectedPiso = [];
            me.SelectedPisoID=[];
          }
          },
          seleccionarTodoSector(){

            let me = this;
             me.selectedSectorID=[];
            if(me.onSector === true) {
               me.validarSector=false;
               me.messageInvalidSector="";
              for (var key in me.sectores){
      
                me.selectedSector[key] = me.sectores[key].DESCRIPCION;
                me.selectedSectorID[key]=me.sectores[key].ID;
              }
          }else{
            me.selectedSector = [];
            me.selectedSectorID=[];
          }
          },
          marcar_inferiores_piso(){
            let me=this;
            me.mayor=0;
            if(me.selectedPiso.length!==0){
                me.validarPiso=false;
                me.messageInvalidPiso="";
                me.selectedPiso.sort();
                if(me.mayor!==null){
                    me.mayor= me.selectedPiso[me.selectedPiso.length-1];
                }else{
                  me.mayor=0;
                }
               
              
                me.selectedPiso=[];
                me.SelectedPisoID=[];
                me.selectedPiso.push(parseInt(me.mayor));
              
              /*  console.log(me.selectedPiso[me.selectedPiso.length-1]);*/
                for (var key in me.pisos){
                  

                    
                    if(me.mayor === me.pisos[key].NRO_PISO){
                      me.SelectedPisoID.push(parseInt(me.pisos[key].ID));

                        for (var i=me.pisos.length-1; i>=0; i--) {

                             
                           if(me.pisos[i]["NRO_PISO"]<me.mayor){
                          
                                 me.selectedPiso.push(parseInt(me.pisos[i]["NRO_PISO"]));
                                 me.SelectedPisoID.push(parseInt(me.pisos[i]["ID"]));

                                 document.getElementById("Piso_"+me.pisos[i]["ID"]).checked = true;
                              
                           }
                        }
                        
                      return;

                    }
                  }
            }
          

          },
          marcar_inferiores_sector(){
            let me=this;
            me.mayor_sector=0;

            if(me.selectedSector.length!==0){
              
               me.validarSector=false;
               me.messageInvalidSector="";
               me.selectedSector.sort();
               
                if(me.mayor_sector!==null){
                    me.mayor_sector= me.selectedSector[me.selectedSector.length-1];

                }else{
                  me.mayor_sector=0;
                }
               
              
                me.selectedSector=[];
                me.selectedSectorID=[];
                me.selectedSector.push(me.mayor_sector);
              
             
                for (var key in me.sectores){
                  

                    
                    if(me.mayor_sector=== me.sectores[key].DESCRIPCION){
                      me.selectedSectorID.push(parseInt(me.sectores[key].ID));

                        for (var i=me.sectores.length-1; i>=0; i--) {

                             
                           if(me.sectores[i]["DESCRIPCION"]<me.mayor_sector){
                          
                                 me.selectedSector.push(me.sectores[i]["DESCRIPCION"]);
                                 me.selectedSectorID.push(parseInt(me.sectores[i]["ID"]));

                                 document.getElementById("Sectores_"+me.sectores[i]["ID"]).checked = true;
                              
                           }
                        }
                       
                      return;

                    }
                  }
            }
          

          },
      verificar_desmarcados_pisos(){
        let me=this;
        var encontrado=false;
        me.DesmarcadoPiso=[];
        //recorrido para encontrar los desmarcados, si existen desmarcados.
        for (var i=0; i<me.AnteriorPiso.length; i++){

          for ( var key in me.SelectedPisoID) { 
            
           
           
              if(me.SelectedPisoID[key]===me.AnteriorPiso[i]["ID"]){
                encontrado=true;
              }
          }
          if(encontrado){
             me.AnteriorPiso[i]["MARCADO"]=true;
          }else{
             me.AnteriorPiso[i]["MARCADO"]=false;
          }
          encontrado=false;
        }
        for (var i=0; i<me.AnteriorPiso.length; i++){
           
            if( me.AnteriorPiso[i]["MARCADO"]===false){
                me.DesmarcadoPiso.push({
                      "ID": me.AnteriorPiso[i]["ID"],
                      "NRO_PISO":me.AnteriorPiso[i]["NRO_PISO"],
                 });
            }
        }
        /*console.log(me.DesmarcadoPiso);*/
      },

      verificar_desmarcados_sectores(){
         let me=this;
        var encontrado=false;
        me.DesmarcadoSector=[];
        //recorrido para encontrar los desmarcados, si existen desmarcados.
        for (var i=0; i<me.AnteriorSector.length; i++){

          for ( var key in me.selectedSectorID) { 
            
           
           
              if(me.selectedSectorID[key]===me.AnteriorSector[i]["ID"]){
                encontrado=true;
              }
          }
          if(encontrado){
             me.AnteriorSector[i]["MARCADO"]=true;
          }else{
             me.AnteriorSector[i]["MARCADO"]=false;
          }
          encontrado=false;
        }
        for (var i=0; i<me.AnteriorSector.length; i++){
           
            if( me.AnteriorSector[i]["MARCADO"]===false){
                me.DesmarcadoSector.push({
                      "ID": me.AnteriorSector[i]["ID"],
                      "DESCRIPCION":me.AnteriorSector[i]["DESCRIPCION"],
                 });
            }
        }
       /* console.log(me.DesmarcadoSector);*/
      },
      verificar_marcados_pisos(){
        let me=this;
        var nuevo=true;
         me.NuevosPisos=[];
         for ( var key in me.SelectedPisoID) { 
             for (var i=0; i<me.AnteriorPiso.length; i++){
                if(me.SelectedPisoID[key]===me.AnteriorPiso[i]["ID"]){
                  nuevo=false;
                }
             }
             if(nuevo){
              me.NuevosPisos.push({
                      "ID": me.SelectedPisoID[key],
                 });
             }
             nuevo=true;
          }
        /*  console.log(me.NuevosPisos);*/
      },
      verificar_marcados_sectores(){
         let me=this;
        var nuevo=true;
         me.NuevosSectores=[];
         for ( var key in me.selectedSectorID) { 
             for (var i=0; i<me.AnteriorSector.length; i++){
                if(me.selectedSectorID[key]===me.AnteriorSector[i]["ID"]){
                  nuevo=false;
                }
             }
             if(nuevo){
              me.NuevosSectores.push({
                      "ID": me.selectedSectorID[key],
                 });
             }
             nuevo=true;
          }
         /* console.log(me.NuevosSectores);*/
      },
      
      BusquedaSeccion(){
        axios.get('busquedas/').then((response) => {
          this.secciones = response.data.seccion;

        });
      }
         
       },
        mounted() {

          let me=this;
           Common.obtenerParametroCommon().then(data => {
              me.rack = data.parametros[0].RACK;
              if(me.rack==='SI'){
                Common.inicioConfiguracionGondola().then(data => {
                    if(data.response){
                      me.pisos=data.pisos;
                      me.sectores=data.sectores;
                    }
                 });
              }
        });
          this.BusquedaSeccion();
          this.nuevaGondola();
      hotkeys('f2', function(event, handler){

        event.preventDefault() 
         me.nuevaGondola();
      });
  hotkeys('f3', function(event, handler){

      event.preventDefault() 
       me.guardarGondola();
    });
    hotkeys('f4', function(event, handler){

      event.preventDefault() 
       me.limpiar();
    });
    hotkeys('f6', function(event, handler){

      event.preventDefault() 
       me.eliminarGondola();
    });

        }
    }
</script>