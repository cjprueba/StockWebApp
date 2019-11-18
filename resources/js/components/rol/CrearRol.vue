
<template>
	
<div class="container">
	<div class="card mt-3 shadow-sm">
  		<h5 class="card-header">Roles</h5>
  		<div class="card-body">

  			<div class="row">
  			 	
  			    <div class="col-12">
  			     	  <label>Nombre de la etiqueta</label>
	                  <input type="text" name="" class="form-control">
	                 <div class="form-group">
	                      <label for="exampleFormControlTextarea1">Descripcion</label>
	    		          <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
	                  </div>
  			    </div>
	             
	             

                 <div class="col-12">
                 	 <hr>

	                 <h5>Permiso especial</h5>
		 		     <div class="form-check form-check-inline">
		                  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1">
		                  <label class="form-check-label" for="inlineRadio1">Acceso total</label>
		             </div>

		             <div class="form-check form-check-inline">
		                  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2">
		                  <label class="form-check-label" for="inlineRadio2">Ningun acceso</label>
		             </div>

		             <div class="form-check form-check-inline">
		                  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="option3">
		                  <label class="form-check-label" for="inlineRadio3">Personalizado</label>
		             </div>

                 </div>
	 		     

	             
                  <div class="col-12">
                  	 <hr>

	                 <h5>Lista de permisos</h5>

		             <div class="form-check form-check-inline">
		               	   <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
		               	   <label class="form-check-label" for="inlineCheckbox1">Navegar usuarios (Lista y navega todos los usuarios del sistema)</label>
		             </div>
                  </div>

                  <div class="col-12 mt-3 text-right">
                  	 <button type="submit" class="btn btn-primary">Guardar</button>
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
          menu : 0
        }
      }, 
      methods: {
      	llamarRoles(){
              Common.llamarRolesCommon().then(data => {

           				
           			
           		});
      	}

      },
        mounted() {
           this.llamarRoles();
        }
    }
</script>