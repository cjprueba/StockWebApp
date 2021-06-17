
<template>
	
<div class="container">

  <div v-if="$can('user.rol') && $can('user') && $can('configuracion')"> 

    <!-- ------------------------------------------------------------------ -->

    <!-- MENSAJE DE ERROR SI NO HAY CONECCION  -->
        
    <mensaje v-bind:mostrar_error="mostrar_error, mensaje"></mensaje>

    <!-- ------------------------------------------------------------------------------------- -->
  	<div class="card mt-3 shadow-sm">
    		<h5 class="card-header">Roles</h5>
    		<div class="card-body">
           
    			<div class="row">

    			 	<div class="col-12">
              <rol-nombre @nombre_rol='enviar_nombre' @existe_rol='existe' :nombre='nombreRol' :validarRol='validarRol' @id='enviar_id' @permisos='traer_permisos' @roles='traer_descripcion'></rol-nombre>
            </div>   

    			    <div class="col-12">

                   <hr>

    			     	
  	                 <div class="form-group">
  	                      <label for="exampleFormControlTextarea1">Descripcion</label>
  	    		          <textarea v-model="descripcionRol" class="form-control" id="exampleFormControlTextarea1" rows="3" v-bind:class="{ 'is-invalid': validarDescripcion }" ></textarea>
  	                  </div>
    			    </div>
  	             
  	             

                   <div class="col-12">
                   	 <hr>

  	                 <h5>Permiso especial</h5>
  		 		     <div class="form-check form-check-inline">
  		                  <input v-model="radioPermisos"  v-on:change="Accesos" class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="1">
  		                  <label class="form-check-label" for="inlineRadio1">Acceso total</label>
  		             </div>

  		             <div class="form-check form-check-inline">
  		                  <input v-model="radioPermisos" v-on:change="Accesos" class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="2">
  		                  <label class="form-check-label" for="inlineRadio2">Ningun acceso</label>
  		             </div>

  		             <div class="form-check form-check-inline">
  		                  <input  v-model="radioPermisos"  v-on:change="Accesos" class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="3">
  		                  <label class="form-check-label" for="inlineRadio3">Personalizado</label>
  		             </div>

                   </div>

                    <div class="col-12">
                      <hr>

                      <h5>Lista de permisos</h5> 



    		              <ul>
                          <li v-if="mostrarPermisos" v-for="permiso in permisos" class="form-check mt-4"> 
                            <input class="form-check-input " type="checkbox" :value="permiso.IDP" :id='permiso.IDP' v-model="permisosSelected" :disabled="deshabilitar" v-bind:class="{ 'is-invalid': validarCheck }">
                            <label class="form-check-label font-weight-bold" :for="permiso.IDP">{{permiso.DESCRIPCION}}</label>


                          
                           
                              <ul>  
                                <li  v-if="(permisoH.IDP==permiso.IDP)"  v-for="permisoH in permisosHijo" class="form-check ml-2 mt-2 ">
                                  <input class="form-check-input" type="checkbox" :value="permisoH.IDH" :id='permisoH.IDH' v-model="permisosSelected" :disabled="deshabilitar" v-bind:class="{ 'is-invalid': validarCheck }">
                                  <label class="form-check-label" :for="permisoH.IDH">{{permisoH.DESCRIPCION}}</label>


                                
                                    <ul>  
                                      <li v-if="(permisoN.IDH==permisoH.IDH && permisoH.IDP==permiso.IDP)"  v-for="permisoN in permisosNieto" class="form-check ml-2 ">
                                        <input class="form-check-input" type="checkbox" :value="permisoN.IDN" :id='permisoN.IDN' v-model="permisosSelected" :disabled="deshabilitar" v-bind:class="{ 'is-invalid': validarCheck }">
                                        <label class="form-check-label font-weight-light" :for="permisoN.IDN">{{permisoN.DESCRIPCION}}</label>
                                      </li>
                                    </ul>
                                  
                              
                                </li>
                              </ul>
                           
                          
                          </li>
                        </ul>

                    </div>




                    <div v-if='guardar' class="col-12 mt-3 text-right">
                    	 <button v-on:click="guardarRol" type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                     <div v-else class="col-12 mt-3 text-right">
                       <button v-on:click="guardarRol" type="submit" class="btn btn-warning">Actualizar</button>
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
          menu : 0,
          permisos:[],
          permisosHijo:[],
          permisosNieto:[],
          validarRol:false,
          radioPermisos:3,
          validarDescripcion:false,
          permisosSelected:[] ,
          mostrar_error:false,
          mensaje:"",
          mostrarRol:false,
          nombreRol:"",
          validarCheck:false,
          descripcionRol:"",
          guardar:true,
          nombreUsuario:"",
          existeRol:false,
          idRol:"",
          deshabilitar:false,
        }
      }, 
      methods: {
        traer_permisos(data){

           this.permisosSelected=data;
           console.log(this.permisosSelected);
          

        },
        existe(data){
          this.existeRol=data;
           if(data===false){
             this.guardar=true;
             this.permisosSelected=[];
             this.descripcionRol="";
             this.radioPermisos=3;
           }else{
             this.guardar=false;
           }
          

        },
        traer_descripcion(data){
          
         this.descripcionRol=data.description;

        },
        enviar_nombre(data){

          this.nombreRol=data;

        },
        enviar_id(id){

          this.idRol=id;
        },
      	llamarRoles(){
      		let me =this;
          return new Promise((resolve, reject)=>{
            setTimeout(()=>{
              Common.llamarPermisoCommon().then(data => {
              me.permisos=data.permisos;
              me.permisosHijo=data.permisosHijo;
              me.permisosNieto=data.permisosNieto;
              me.mostrarPermisos= true;
              
              console.log(me.permisosNieto);
              resolve();
                
              });
            },4000)

          });

      	},
      	guardarRol(){
             let me =this;

             if(me.nombreRol===""){
           
              me.validarRol=true;
              return;
             } else {
              me.validarRol = false;
             
             } 
             if(me.descripcionRol===""){
              me.validarDescripcion=true;
              return;
             } else {
              me.validarDescripcion = false;
             } 
             if(me.permisosSelected.length === 0){
              me.validarCheck=true;
               return;
             }else{
              me.validarCheck=false;
             }	 
             
              Common.guardarRolCommon(me.nombreRol,me.descripcionRol,me.permisosSelected,me.existeRol,me.idRol).then(data => {
               if(data.response===true){
                  Swal.fire(
                     'Guardado!',
                     'Se ha guardado correctamente el rol !',
                     'success'
                  )
               }
           
           			
           		}).catch((err) => {
                console.log(err);
                this.mostrar_error = true;
                this.mensaje = err;
              });
      	},
        Accesos(){
          let me =this;
            
          if(me.radioPermisos==="1"){

                me.deshabilitar=true;
                me.permisos.map(function(x) {
                   me.permisosSelected.push(x.IDP);

                });

                me.permisosHijo.map(function(x) {
                  if (me.permisosSelected.includes(x.IDH) === false) { 

                    //Guarda en el Array
                     me.permisosSelected.push(x.IDH);
                  }
                  

                });

                me.permisosNieto.map(function(x) {
                  if (me.permisosSelected.includes(x.IDN) === false) { 

                    //Guarda en el Array
                     me.permisosSelected.push(x.IDN);
                  }
                });

          }else{

            if(me.radioPermisos==="2"){
               me.permisosSelected=[];
               me.deshabilitar=true;
            }else{ 
              me.permisosSelected=[];
              me.deshabilitar=false;

            }

          }  
        }
         
       },
      async mounted() {
        await this.llamarRoles();
        let me=this;
        //Recorre todos los checksbox del Vue
        $('input[type="checkbox"]').change(function(e) {
         

          //Guarda en checked las casillas checkeadas
          var checked = $(this).prop("checked"),
              container = $(this).parent(),
              siblings = container.siblings();

          //checkea todos los hijos del checkbox o simplemente individualmente
          container.find('input[type="checkbox"]').prop({
            indeterminate: false,
            checked: checked
          });

          //Recorre todos los checkbox para poder incluir o eliminiar de nuestro Array de seleccion
          container.find('input[type="checkbox"]').each(function() {

            //si está chekeado 
          if  ($(this).prop("checked"))  {
             // Si no existe en nuestro Array de seleccion
            if (me.permisosSelected.includes(parseInt($(this).closest('input').attr('id'))) === false) { 

              //Guarda en el Array
              me.permisosSelected.push(parseInt($(this).closest('input').attr('id')));
            }

          }else{
              //si no está chekeado
              //RECORREMOS NUESTRO ARRAY DE SELECCION
              for (var i=0; i<me.permisosSelected.length; i++) { 
                //Si exixste en nuestro Array
                if(me.permisosSelected[i]===parseInt($(this).closest('input').attr('id'))){

                   //Eliminar de nuestro Array
                   me.permisosSelected.splice(i, 1); 
                           i--;
                }
              }
            } 
        });


        function checkSiblings(el) {
            var parent = el.parent().parent(),
            all = true;
            console.log(el.siblings());
            el.siblings().each(function() {
              let returnValue = all = ($(this).children('input[type="checkbox"]').prop("checked") === checked);
              // console.log(returnValue);

              return returnValue;
            });
            
            //Proceso para seleccionar todo los checkbox sin incluir en nuestro Array
            console.log(all);
            if (all && checked) {
              // console.log("1ra parte");
              console.log("parte 1");
              parent.children('input[type="checkbox"]').prop({
                indeterminate: false,
                checked: checked
              });
              checkSiblings(parent);
            }else if (all && !checked) {
              //Proceo de desmarcación de los checkboxs individuales o con padres o con hijos
              console.log("parte 2");
              parent.children('input[type="checkbox"]').prop("checked", checked);
               
              parent.children('input[type="checkbox"]').prop("indeterminate", (parent.find('input[type="checkbox"]:checked').length > 0));


              //Recorrer todos los checkboxs  
              parent.children('input[type="checkbox"]').each(function(){

                //Si indeterminado es falso
               // console.log($(this).prop("indeterminate"));
                if  (!$(this).prop("indeterminate")) {

                    
                    //Recorremos el array de selección
                  for (var i=0; i<me.permisosSelected.length; i++) { 
                  
                    //Si exixste en nuestro Array
                    if(me.permisosSelected[i]===parseInt($(this).closest('input').attr('id'))){

                       //Eliminar de nuestro Array
                       me.permisosSelected.splice(i, 1); 
                               i--;
                    }
                  }
                }
              });
              checkSiblings(parent);
            }else{
              console.log("parte 3");
              
                //Proceso de marcación de checkboxs padres Indeterminados
              el.parents("li").children('input[type="checkbox"]').prop({
                  indeterminate: true,
                  checked: false

              });

              //Recorridos de todoa los checkboxs
              el.parents("li").children('input[type="checkbox"]').each(function(){

                //Si indeterminado es verdadero
                if  ($(this).prop("indeterminate"))  {
                        
                        //Si no exixste en nuestro Array
                  if (me.permisosSelected.includes(parseInt($(this).closest('input').attr('id'))) === false) { 

                    //Guarda en el Array
                    me.permisosSelected.push(parseInt($(this).closest('input').attr('id')));
                  }
                }else{

                //si no está chekeado
                  //RECORREMOS NUESTRO ARRAY DE SELECCION
                  for (var i=0; i<me.permisosSelected.length; i++) {

                    ///Si exixste en nuestro Array
                    if(me.permisosSelected[i]===parseInt($(this).closest('input').attr('id'))){

                       //Eliminar de nuestro Array
                       me.permisosSelected.splice(i, 1); 
                               i--;
                    }
                  }
                }
              });
            }
          }
          checkSiblings(container);

        }); 
      }
    }
</script>