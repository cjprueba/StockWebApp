<template>
	
	<div class="clearfix borderbox" id="page"><!-- column -->

	    <div class="browser_width colelem" id="u162-bw">
	    	<div class="clearfix" id="u162"><!-- group -->
	     		<div class="clip_frame grpelem" id="u245" data-sizePolicy="fluidWidthHeight" data-pintopage="page_fixedCenter"><!-- image -->
	      			<!-- <img class="block" id="u245_img" :src="logo" alt="" data-heightwidthratio="0.1323943661971831" data-image-width="355" data-image-height="47"/> -->
	     		</div>
	    	</div>
	    </div>

	    <div class="clearfix colelem" align="center" id="u264-7"><!-- content -->
	    	<p>{{codigoCa}} </p>
	    </div>



	    <div v-for="encabezados in encabezado">
	    	<!-- <div class="col 12">	 -->
	    		<div class="rounded-corners clearfix colelem" id="u287-3">
	    		
	    				<div class="clearfix grpelem" id="u267-5"><!-- content -->
		    				<p>RACK: {{encabezados.RACK}} </p>
						</div>
						<div class="clearfix grpelem" id="u267-5B"><!-- content -->
		    				<p>SECTOR: {{encabezados.SECTOR}} </p>
						</div>
	    				<div class="clearfix grpelem" id="u270-5"><!-- content -->
				    		<p>PISO: {{encabezados.PISO}} </p>
						</div>
	    		</div>
	    		<hr>
	    	<!-- </div> -->				
	    </div>
	    	
	    <hr>
	    <div  v-for="producto in productos">
	    	
	    	<div class="card mt-3 shadow-sm mt-20" id="u287-4">

    			<div class="card-body" id="u287-4">

				    <div class="clearfix colelem" align="center" id="u264-4">
				    	
				    	<div v-if="producto.STOCK_LOTE>0" >
				    		<p>{{producto.DESCRIPCION}} </p>
				    	</div>
				    	<div v-else>
				    		<font-awesome-icon id="uColorR" icon="exclamation-triangle" />
				    		<p>{{producto.DESCRIPCION}} </p>
				    	</div>
				    </div>
				    <div class="clearfix colelem" align="center"  id="u264-5">
				    	<p>COD: {{producto.CODIGO}} </p>
				    </div>

				    <div class="clip_frame colelem" id="u255" data-sizePolicy="fluidWidthHeight" data-pintopage="page_fixedCenter"><!-- image -->
				   		<span class="block" id="u255_img" data-heightwidthratio="1" data-image-width="241" data-image-height="241"  v-html="producto.IMAGEN"></span>
				    	<!-- <img class="block" id="u255_img" :src="producto" alt="" data-heightwidthratio="1" data-image-width="241" data-image-height="241"/> -->
				    </div>

				    <div class="rounded-corners clearfix colelem" id="u287"><!-- group -->
				    	<div class="clearfix grpelem" id="u267-4"><!-- content -->
				    		<p>Lote: </p>
				    	</div>
				    	<div class="clearfix grpelem" id="u270-4"><!-- content -->
				     		<p>{{producto.LOTE}} </p>
				    	</div>
				    </div>

				    <div class="rounded-corners clearfix colelem" id="u287"><!-- group -->
				    	<div class="clearfix grpelem" id="u267-4"><!-- content -->
				    		<p>Stock Inicial: </p>
				    	</div>
				    	<div class="clearfix grpelem" id="u270-4"><!-- content -->
				     		<p>{{producto.STOCK_INICIAL}} </p>
				    	</div>
				    </div>

				    <div class="rounded-corners clearfix colelem" id="u287"><!-- group -->
				    	<div class="clearfix grpelem" id="u267-4"><!-- content -->
				      		<p>Stock Lote: </p>
				    	</div>
				    	<div v-if="producto.STOCK_LOTE>0" class="clearfix grpelem" id="u270-4"><!-- content -->
				     		<p>{{producto.STOCK_LOTE}}</p>
				    	</div>
				    	<div v-else class="clearfix grpelem" id="u270-4B">
				    		<p>{{producto.STOCK_LOTE}}</p>
				    	</div>
				    </div>
				   
				   <div class="rounded-corners clearfix colelem" id="u287"><!-- group -->
				    	<div class="clearfix grpelem" id="u267-4"><!-- content -->
				    		<p>Costo: </p>
				    	</div>
				    	<div class="clearfix grpelem" id="u270-4"><!-- content -->
				     		<p>{{producto.COSTO}} </p>
				    	</div>
				    </div>

				    <div class="rounded-corners clearfix colelem" id="u287"><!-- group -->
				    	<div class="clearfix grpelem" id="u267-4"><!-- content -->
				    		<p>Vencimiento: </p>
				    	</div>
				    	<div class="clearfix grpelem" id="u270-4"><!-- content -->
				     		<p>{{producto.FECHA_VENC}} </p>
				    	</div>
				    </div>
				    <div class="verticalspacer" data-offset-top="732" data-content-above-spacer="732" data-content-below-spacer="0" data-sizePolicy="fixed" data-pintopage="page_fixedLeft"></div>
				
				</div>
				
			</div>
	    </div>
	</div>

</template>
<script>
	export default {
	  props: ['sucursal', 'codigo_ca', 'tipo_busqueda'],	
      data(){
        return {
          productos: [],
          encabezado: [],
          codigoCa: '',
          color: '',
          logo: require('./../../../imagenes/logo-tokutokuya.png'),
          imagen: {
          	oferta: '',
          	logo: ''
          }
        }
      }, 
      methods: {
      	activar(index){
      		if (index === 0) {
      			return true;
      		}
      	}, 
      	obtener(){

      		// ------------------------------------------------------------------------

      		// OBTENER PRODUCTOS 

      		let me = this;
      			
      			Common.obtenerCompraCajaQRCommon({sucursal: me.sucursal, codigo_ca: me.codigo_ca, tipo_busqueda: me.tipo_busqueda}).then(data => {
	        	
	        		me.codigoCa = me.codigo_ca;
	        		me.productos = data.data;
	        		me.encabezado = data.encabezado;
	        			// me.productos = data.data;
	        			// me.imagen.oferta = data.oferta;
	        			// me.imagen.logo = data.logo;
	        	});

      		
	        

	         // ------------------------------------------------------------------------

      	}
      },
        mounted() {

          // ------------------------------------------------------------------------
	
          this.obtener();
          let me = this;

          // ------------------------------------------------------------------------

          

          // ------------------------------------------------------------------------


        }
    }
</script>
<style scoped>
html{min-height:100%;min-width:100%;-ms-text-size-adjust:none;}body,div,dl,dt,dd,ul,ol,li,nav,h1,h2,h3,h4,h5,h6,pre,code,form,fieldset,legend,input,button,textarea,p,blockquote,th,td,a{margin:0px;padding:0px;border-width:0px;border-style:solid;border-color:transparent;-webkit-transform-origin:left top;-ms-transform-origin:left top;-o-transform-origin:left top;transform-origin:left top;background-repeat:no-repeat;}button.submit-btn{-moz-box-sizing:content-box;-webkit-box-sizing:content-box;box-sizing:content-box;}.transition{-webkit-transition-property:background-image,background-position,background-color,border-color,border-radius,color,font-size,font-style,font-weight,letter-spacing,line-height,text-align,box-shadow,text-shadow,opacity;transition-property:background-image,background-position,background-color,border-color,border-radius,color,font-size,font-style,font-weight,letter-spacing,line-height,text-align,box-shadow,text-shadow,opacity;}.transition *{-webkit-transition:inherit;transition:inherit;}table{border-collapse:collapse;border-spacing:0px;}fieldset,img{border:0px;border-style:solid;-webkit-transform-origin:left top;-ms-transform-origin:left top;-o-transform-origin:left top;transform-origin:left top;}address,caption,cite,code,dfn,em,strong,th,var,optgroup{font-style:inherit;font-weight:inherit;}del,ins{text-decoration:none;}li{list-style:none;}caption,th{text-align:left;}h1,h2,h3,h4,h5,h6{font-size:100%;font-weight:inherit;}input,button,textarea,select,optgroup,option{font-family:inherit;font-size:inherit;font-style:inherit;font-weight:inherit;}.form-grp input,.form-grp textarea{-webkit-appearance:none;-webkit-border-radius:0;}body{font-family:Arial, Helvetica Neue, Helvetica, sans-serif;text-align:left;font-size:14px;line-height:17px;word-wrap:break-word;text-rendering:optimizeLegibility;-moz-font-feature-settings:'liga';-ms-font-feature-settings:'liga';-webkit-font-feature-settings:'liga';font-feature-settings:'liga';}a:link{color:#0000FF;text-decoration:underline;}a:visited{color:#800080;text-decoration:underline;}a:hover{color:#0000FF;text-decoration:underline;}a:active{color:#EE0000;text-decoration:underline;}a.nontext{color:black;text-decoration:none;font-style:normal;font-weight:normal;}.normal_text{color:#000000;direction:ltr;font-family:Arial, Helvetica Neue, Helvetica, sans-serif;font-size:14px;font-style:normal;font-weight:normal;letter-spacing:0px;line-height:17px;text-align:left;text-decoration:none;text-indent:0px;text-transform:none;vertical-align:0px;padding:0px;}.list0 li:before{position:absolute;right:100%;letter-spacing:0px;text-decoration:none;font-weight:normal;font-style:normal;}.rtl-list li:before{right:auto;left:100%;}.nls-None > li:before,.nls-None .list3 > li:before,.nls-None .list6 > li:before{margin-right:6px;content:'•';}.nls-None .list1 > li:before,.nls-None .list4 > li:before,.nls-None .list7 > li:before{margin-right:6px;content:'○';}.nls-None,.nls-None .list1,.nls-None .list2,.nls-None .list3,.nls-None .list4,.nls-None .list5,.nls-None .list6,.nls-None .list7,.nls-None .list8{padding-left:34px;}.nls-None.rtl-list,.nls-None .list1.rtl-list,.nls-None .list2.rtl-list,.nls-None .list3.rtl-list,.nls-None .list4.rtl-list,.nls-None .list5.rtl-list,.nls-None .list6.rtl-list,.nls-None .list7.rtl-list,.nls-None .list8.rtl-list{padding-left:0px;padding-right:34px;}.nls-None .list2 > li:before,.nls-None .list5 > li:before,.nls-None .list8 > li:before{margin-right:6px;content:'-';}.nls-None.rtl-list > li:before,.nls-None .list1.rtl-list > li:before,.nls-None .list2.rtl-list > li:before,.nls-None .list3.rtl-list > li:before,.nls-None .list4.rtl-list > li:before,.nls-None .list5.rtl-list > li:before,.nls-None .list6.rtl-list > li:before,.nls-None .list7.rtl-list > li:before,.nls-None .list8.rtl-list > li:before{margin-right:0px;margin-left:6px;}.TabbedPanelsTab{white-space:nowrap;}.MenuBar .MenuBarView,.MenuBar .SubMenuView{display:block;list-style:none;}.MenuBar .SubMenu{display:none;position:absolute;}.NoWrap{white-space:nowrap;word-wrap:normal;}.rootelem{margin-left:auto;margin-right:auto;}.colelem{display:inline;float:left;clear:both;}.clearfix:after{content:"\0020";visibility:hidden;display:block;height:0px;clear:both;}*:first-child+html .clearfix{zoom:1;}.clip_frame{overflow:hidden;}.popup_anchor{position:relative;width:0px;height:0px;}.allow_click_through *{pointer-events:auto;}.popup_element{z-index:100000;}.svg{display:block;vertical-align:top;}span.wrap{content:'';clear:left;display:block;}span.actAsInlineDiv{display:inline-block;}.position_content,.excludeFromNormalFlow{float:left;}.preload_images{position:absolute;overflow:hidden;left:-9999px;top:-9999px;height:1px;width:1px;}.preload{height:1px;width:1px;}.animateStates{-webkit-transition:0.3s ease-in-out;-moz-transition:0.3s ease-in-out;-o-transition:0.3s ease-in-out;transition:0.3s ease-in-out;}[data-whatinput="mouse"] *:focus,[data-whatinput="touch"] *:focus,input:focus,textarea:focus{outline:none;}textarea{resize:none;overflow:auto;}.allow_click_through,.fld-prompt{pointer-events:none;}.wrapped-input{position:absolute;top:0px;left:0px;background:transparent;border:none;}.submit-btn{z-index:50000;cursor:pointer;}.anchor_item{width:22px;height:18px;}.MenuBar .SubMenuVisible,.MenuBarVertical .SubMenuVisible,.MenuBar .SubMenu .SubMenuVisible,.popup_element.Active,span.actAsPara,.actAsDiv,a.nonblock.nontext,img.block{display:block;}.widget_invisible,.js .invi,.js .mse_pre_init{visibility:hidden;}.ose_ei{visibility:hidden;z-index:0;}.no_vert_scroll{overflow-y:hidden;}.always_vert_scroll{overflow-y:scroll;}.always_horz_scroll{overflow-x:scroll;}.fullscreen{overflow:hidden;left:0px;top:0px;position:fixed;height:100%;width:100%;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;-ms-box-sizing:border-box;box-sizing:border-box;}.fullwidth{position:absolute;}.borderbox{-moz-box-sizing:border-box;-webkit-box-sizing:border-box;-ms-box-sizing:border-box;box-sizing:border-box;}.scroll_wrapper{position:absolute;overflow:auto;left:0px;right:0px;top:0px;bottom:0px;padding-top:0px;padding-bottom:0px;margin-top:0px;margin-bottom:0px;}.browser_width > *{position:absolute;left:0px;right:0px;}.grpelem,.accordion_wrapper{display:inline;float:left;}.fld-checkbox input[type=checkbox],.fld-radiobutton input[type=radio]{position:absolute;overflow:hidden;clip:rect(0px, 0px, 0px, 0px);height:1px;width:1px;margin:-1px;padding:0px;border:0px;}.fld-checkbox input[type=checkbox] + label,.fld-radiobutton input[type=radio] + label{display:inline-block;background-repeat:no-repeat;cursor:pointer;float:left;width:100%;height:100%;}.pointer_cursor,.fld-recaptcha-mode,.fld-recaptcha-refresh,.fld-recaptcha-help{cursor:pointer;}p,h1,h2,h3,h4,h5,h6,ol,ul,span.actAsPara{max-height:1000000px;}.superscript{vertical-align:super;font-size:66%;line-height:0px;}.subscript{vertical-align:sub;font-size:66%;line-height:0px;}.horizontalSlideShow{-ms-touch-action:pan-y;touch-action:pan-y;}.verticalSlideShow{-ms-touch-action:pan-x;touch-action:pan-x;}.colelem100,.verticalspacer{clear:both;}.list0 li,.MenuBar .MenuItemContainer,.SlideShowContentPanel .fullscreen img,.css_verticalspacer .verticalspacer{position:relative;}.popup_element.Inactive,.js .disn,.js .an_invi,.hidden,.breakpoint{display:none;}#muse_css_mq{position:absolute;display:none;background-color:#FFFFFE;}.fluid_height_spacer{width:0.01px;}.muse_check_css{display:none;position:fixed;}@media screen and (-webkit-min-device-pixel-ratio:0){body{text-rendering:auto;}}

.version.index{color:#0000EF;background-color:#59C0AF;}#page{z-index:1;min-height:689.5789473684213px;background-image:none;border-width:0px;border-color:#000000;background-color:transparent;padding-bottom:0px;width:100%;}#u162{z-index:4;min-height:62px;background-color:#3B83BD;padding-bottom:11px;}#u245{z-index:5;background-color:transparent;position:relative;margin-right:-10000px;margin-top:15px;width:88.75%;margin-left:5.63%;left:-1px;}.js body{visibility:hidden;}.js body.initialized{visibility:visible;}#u162-bw{z-index:4;min-height:62px;}
#u264-4{z-index:9;background-color:transparent;color:#2F2F2F;font-size:18px;line-height:22px;font-weight:bold;margin-top:24px;position:center;width:89%;margin-left:5%;}

#u264-7{z-index:9;background-color:transparent;color:#2F2F2F;font-size:24px;line-height:22px;font-weight:bold;margin-top:24px;position:center;width:89%;margin-left:5%;}

#u255{z-index:7;background-color:transparent;margin-top:32px;position:relative;width:60.25%;margin-left:19.88%;left:-1px;}#u245_img,#u255_img{width:100%;}#u290{z-index:2;background-color:#F0F0F0;border-radius:5px;padding-bottom:7px;margin-top:32px;position:relative;width:88.75%;margin-left:5.63%;left:0px;}



#u267-4{z-index:13;background-color:transparent;font-size:15px;color:#404040;line-height:24px;position:relative;margin-right:-10000px;margin-top:8px;width:31.84%;left:3.1%;}


#u270-4{z-index:17;background-color:transparent;color:#000000;font-size:18px;line-height:24px;font-weight:bold;position:relative;margin-right:-10000px;margin-top:8px;width:45.36%;left:35.57%;}
#u270-4B{z-index:17;background-color:transparent;color:#FF0800;font-size:18px;line-height:24px;font-weight:bold;position:relative;margin-right:-10000px;margin-top:8px;width:45.36%;left:35.57%;}



#u267-5{z-index:13;background-color:transparent;color:#000000;font-weight:bold;font-size:18px;line-height:24px;position:relative;margin-right:-10000px;margin-top:8px;width:31.84%;margin-left:5%}

#u267-5B{z-index:13;background-color:transparent;color:#000000;font-weight:bold;font-size:18px;line-height:24px;position:relative;margin-right:-10000px;margin-top:8px;width:31.84%;margin-left:38%}


#u270-5{z-index:17;background-color:transparent;color:#000000;font-weight:bold;font-size:18px;line-height:24px;position:relative;margin-right:-10000px;margin-top:8px;width:31.84%;margin-left:70%;left:3.1%;}

#uColorR{
	color:#FF0800;
}




#u287{z-index:3;background-color:#F0F0F0;border-radius:5px;padding-bottom:7px;margin-top:6px;position:relative;width:100%;left:0px;}

#u273-4{z-index:21;background-color:transparent;font-size:20px;color:#404040;line-height:24px;position:relative;margin-right:-10000px;margin-top:8px;width:31.84%;left:3.1%;}#u276-4{z-index:25;background-color:transparent;color:#000000;font-size:20px;line-height:24px;font-weight:bold;position:relative;margin-right:-10000px;margin-top:8px;width:45.36%;left:40.57%;}#u282-4{z-index:42;background-color:transparent;color:#4B7429;font-size:20px;text-align:center;line-height:24px;font-weight:bold;margin-top:25px;position:relative;width:36%;margin-left:32%;left:0px;}#u279-13{z-index:29;min-height:134px;background-color:transparent;margin-top:10px;position:relative;width:88.75%;margin-left:5%;}#u279-2,#u279-4,#u279-6,#u279-8,#u279-10{font-size:12px;line-height:18px;}.css_verticalspacer .verticalspacer{height:calc(100vh - 732px);}#muse_css_mq,.html{background-color:#FFFFFF;}body{position:relative;min-width:320px;}
    
    #u264-5{z-index:9;background-color:transparent;color:#2F2F2F;font-size:18px;line-height:15px;font-weight:bold;margin-top:3px;position:relative;width:89%;margin-left:5%;}

    #u287-2{z-index:3;background-color:#FAFAFA;border-radius:1px;padding-bottom:7px;margin-top:1px;position:relative;width:92.50%;margin-left:3.63%;left:0px;}
    #u287-4{z-index:3;background-color:#FAFAFA;border-radius:1px;padding-bottom:7px;margin-top:1px;position:relative;width:92.50%;margin-left:3.63%;left:0px;}
    #u287-4B{z-index:3;background-color:#FEE1E1;border-radius:1px;padding-bottom:7px;margin-top:1px;position:relative;width:92.50%;margin-left:3.63%;left:0px;}
    


    #u287-3{z-index:1;background-color:transparent;border-radius:5px;padding-bottom:4px;margin-top:6px;position:relative;width:100%;left:0px;}

    #u264-6{z-index:9;background-color:transparent;color:#2F2F2F;font-size:18px;line-height:22px;font-weight:bold;margin-top:24px;position:center;width:89%;margin-left:50%;}
	
</style>