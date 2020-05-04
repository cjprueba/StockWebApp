<template>
	<div>

    <section id="container" class="container">
    <h3>The user's camera</h3>
    <p>If your platform supports the <strong>getUserMedia</strong> API call, you
        can try the real-time locating and decoding features.
        Simply allow the page to access your web-cam and point it to a barcode.</p>
    <p>The various options available allow you to adjust the decoding
        process to your needs (Type of barcode, resolution, ...)</p>
    <div class="controls">
        <fieldset class="input-group">
            <button class="stop">Stop</button>
        </fieldset>
        <fieldset class="reader-config-group">
        <!--    <label>
                <span>Barcode-Type</span>
                <select name="decoder_readers">
                    <option value="code_128" selected="selected">Code 128</option>
                    <option value="code_39">Code 39</option>
                    <option value="code_39_vin">Code 39 VIN</option>
                    <option value="ean">EAN</option>
                    <option value="ean_extended">EAN-extended</option>
                    <option value="ean_8">EAN-8</option>
                    <option value="upc">UPC</option>
                    <option value="upc_e">UPC-E</option>
                    <option value="codabar">Codabar</option>
                    <option value="i2of5">I2of5</option>
                </select>
            </label>
            <label>
                <span>Resolution (long side)</span>
                <select name="input-stream_constraints">
                    <option value="320x240">320px</option>
                    <option selected="selected" value="640x480">640px</option>
                    <option value="800x600">800px</option>
                    <option value="1280x720">1280px</option>
                    <option value="1600x960">1600px</option>
                    <option value="1920x1080">1920px</option>
                </select>
            </label>
            <label>
                <span>Patch-Size</span>
                <select name="locator_patch-size">
                    <option value="x-small">x-small</option>
                    <option value="small">small</option>
                    <option selected="selected" value="medium">medium</option>
                    <option value="large">large</option>
                    <option value="x-large">x-large</option>
                </select>
            </label>
            <label>
                <span>Half-Sample</span>
                <input type="checkbox" checked="checked" name="locator_half-sample" />
            </label>
            <label>
                <span>Workers</span>
                <select name="numOfWorkers">
                    <option value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option selected="selected" value="4">4</option>
                    <option value="8">8</option>
                </select>
            </label>-->
            <label>
                <span>Camera</span>
                <select name="input-stream_constraints" id="deviceSelection">
                </select>
            </label>
        </fieldset>
    </div>
    <div id="result_strip">
        <ul class="thumbnails"></ul>
    </div>
    <div id="interactive" class="viewport"></div>
</section>

		<!-- ------------------------------------------------------------------------ -->

		<!-- BOTON CAMARA -->

		<button class="btn btn-primary mt-3" data-toggle="modal" data-target=".camara-modal" v-on:click="mostrarCamara"><font-awesome-icon icon="camera" /></button>

		<!-- ------------------------------------------------------------------------ -->

		<!-- CAMARA -->

	    <div class="modal fade camara-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
	            <div class="modal-content">
	                <div class="modal-header">
	                  <h5 class="modal-title" id="exampleModalCenterTitle">Apunte la cámara hacia el código</small></h5>
	                </div>
	                <div class="modal-body">
	                    <div class="text-center">
	                      <div id="camera">
	                      </div>
	                    </div> 
	                </div>
	                <div class="modal-footer">
	                    <button type="button" class="btn btn-secondary" v-on:click="detenerCamara" data-dismiss="modal">Cerrar</button>
	                </div>
	            </div>
	        </div>
	    </div> 
	    
	    <!-- ------------------------------------------------------------------------ -->


	</div>	
</template>
<script>
	export default {
      data(){
        return {
          	codigo: ''
        }
      }, 
      methods: {
        mostrarCamara(){

          Quagga.init({
            inputStream : {
              name : "Live",
              type : "LiveStream",
              target: document.querySelector('#camera')    // Or '#yourElement' (optional)
            },
            decoder : {
              readers : ["code_128_reader",
                          "ean_reader",
                          "ean_8_reader",
                          "code_39_reader",
                          "code_39_vin_reader",
                          "codabar_reader",
                          "upc_reader",
                          "upc_e_reader",
                          "i2of5_reader"
              ], debug: {
                  drawBoundingBox: true,
                  showFrequency: true,
                  drawScanline: true,
                  showPattern: true
              }
            }
          }, function(err) {
              if (err) {
                  console.log(err);
                  return
              }
              console.log("Initialization finished. Ready to start");
              Quagga.start();
          });

        },
        detenerCamara(){

          // ------------------------------------------------------------------------

          // DETENER

          Quagga.stop()

          // ------------------------------------------------------------------------

        }
      },
        mounted() {
        	
          
        	// ------------------------------------------------------------------------

        	// INICIAR VARIABLES 

        	let me = this;

        	// ------------------------------------------------------------------------

	        // Quagga.onDetected(function (data){
	        //    me.$emit('codigo_camara', data.codeResult.code); 
	        //    $('.camara-modal').modal('hide');
	        //    me.detenerCamara();
	        // });

	        // // ------------------------------------------------------------------------
          
         //  Quagga.onProcessed(function (result){
         //     var drawingCtx = Quagga.canvas.ctx.overlay, drawingCanvas = Quagga.canvas.dom.overlay;

         //      if (result) {
         //        if (result.boxes) {
         //          drawingCtx.clearRect(0, 0, parseInt(drawingCanvas.getAttribute("width")), parseInt(drawingCanvas.getAttribute("height")));
         //          result.boxes.filter(function (box) {
         //            return box !== result.box;
         //          }).forEach(function (box) {
         //            Quagga.ImageDebug.drawPath(box, {x: 0, y: 1}, drawingCtx, {color: "green", lineWidth: 2});
         //          });
         //        }

         //        if (result.box) {
         //          Quagga.ImageDebug.drawPath(result.box, {x: 0, y: 1}, drawingCtx, {color: "#00F", lineWidth: 2});
         //        }

         //        if (result.codeResult && result.codeResult.code) {
         //          Quagga.ImageDebug.drawPath(result.line, {x: 'x', y: 'y'}, drawingCtx, {color: 'red', lineWidth: 3});
         //        }
         //      }
         //  });

          // ------------------------------------------------------------------------

          /**Quagga initialiser starts here*/

$(function() {
    var value;
    var App = {
        init : function() {
            Quagga.init(this.state, function(err) {
                if (err) {
                    console.log(err);
                    return;
                }
                App.attachListeners();
                Quagga.start();
            });
        },
        initCameraSelection: function(){
            var streamLabel = Quagga.CameraAccess.getActiveStreamLabel();

            return Quagga.CameraAccess.enumerateVideoDevices()
            .then(function(devices) {
                function pruneText(text) {
                    return text.length > 30 ? text.substr(0, 30) : text;
                }
                var $deviceSelection = document.getElementById("deviceSelection");
                while ($deviceSelection.firstChild) {
                    $deviceSelection.removeChild($deviceSelection.firstChild);
                }
                devices.forEach(function(device) {
                    var $option = document.createElement("option");
                    $option.value = device.deviceId || device.id;
                    $option.appendChild(document.createTextNode(pruneText(device.label || device.deviceId || device.id)));
                    $option.selected = streamLabel === device.label;
                    $deviceSelection.appendChild($option);
                });
            });
        },
            querySelectedReaders: function() {
        return Array.prototype.slice.call(document.querySelectorAll('.readers input[type=checkbox]'))
            .filter(function(element) {
                return !!element.checked;
            })
            .map(function(element) {
                return element.getAttribute("name");
            });
    },
        attachListeners: function() {
            var self = this;

            self.initCameraSelection();
            $(".controls").on("click", "button.stop", function(e) {
                e.preventDefault();
                Quagga.stop();
            });

            $(".controls .reader-config-group").on("change", "input, select", function(e) {
                e.preventDefault();
                var $target = $(e.target);
                   // value = $target.attr("type") === "checkbox" ? $target.prop("checked") : $target.val(),
                   value =  $target.attr("type") === "checkbox" ? this.querySelectedReaders() : $target.val();
                  var  name = $target.attr("name"),
                    state = self._convertNameToState(name);

                console.log("Value of "+ state + " changed to " + value);
                self.setState(state, value);
            });
        },
        _accessByPath: function(obj, path, val) {
            var parts = path.split('.'),
                depth = parts.length,
                setter = (typeof val !== "undefined") ? true : false;

            return parts.reduce(function(o, key, i) {
                if (setter && (i + 1) === depth) {
                    if (typeof o[key] === "object" && typeof val === "object") {
                        Object.assign(o[key], val);
                    } else {
                        o[key] = val;
                    }
                }
                return key in o ? o[key] : {};
            }, obj);
        },
        _convertNameToState: function(name) {
            return name.replace("_", ".").split("-").reduce(function(result, value) {
                return result + value.charAt(0).toUpperCase() + value.substring(1);
            });
        },
        detachListeners: function() {
            $(".controls").off("click", "button.stop");
            $(".controls .reader-config-group").off("change", "input, select");
        },
        setState: function(path, value) {
            var self = this;

            if (typeof self._accessByPath(self.inputMapper, path) === "function") {
                value = self._accessByPath(self.inputMapper, path)(value);
            }

            self._accessByPath(self.state, path, value);

            console.log(JSON.stringify(self.state));
            App.detachListeners();
            Quagga.stop();
            App.init();
        },
        inputMapper: {
            inputStream: {
                constraints: function(value){
                    if (/^(\d+)x(\d+)$/.test(value)) {
                        var values = value.split('x');
                        return {
                            width: {min: parseInt(values[0])},
                            height: {min: parseInt(values[1])}
                        };
                    }
                    return {
                        deviceId: value
                    };
                }
            },
            numOfWorkers: function(value) {
                return parseInt(value);
            },
            decoder: {
                readers: function(value) {
                    if (value === 'ean_extended') {
                        return [{
                            format: "ean_reader",
                            config: {
                                supplements: [
                                    'ean_5_reader', 'ean_2_reader'
                                ]
                            }
                        }];
                    }
                    console.log("value before format :"+value);
                    return [{
                        format: value + "_reader",
                        config: {}
                    }];
                }
            }
        },
        state: {
            inputStream: {
                type : "LiveStream",
                constraints: {
                    width: {min: 640},
                    height: {min: 480},
                    aspectRatio: {min: 1, max: 100},
                    facingMode: "environment" // or user
                }
            },
            locator: {
                patchSize: "large",
                halfSample: true
            },
            numOfWorkers: 4,
            decoder: {
                readers : ["code_128_reader",
                          "ean_reader",
                          "ean_8_reader",
                          "code_39_reader",
                          "code_39_vin_reader",
                          "codabar_reader",
                          "upc_reader",
                          "upc_e_reader",
                          "i2of5_reader"]
            },
            locate: true,
            multiple:true
        },
        lastResult : null
    };
    
                   //value =  App.querySelectedReaders() ;
    App.init();

    Quagga.onProcessed(function(result) {
        var drawingCtx = Quagga.canvas.ctx.overlay,
            drawingCanvas = Quagga.canvas.dom.overlay;

        if (result) {
            if (result.boxes) {
                drawingCtx.clearRect(0, 0, parseInt(drawingCanvas.getAttribute("width")), parseInt(drawingCanvas.getAttribute("height")));
                result.boxes.filter(function (box) {
                    return box !== result.box;
                }).forEach(function (box) {
                    Quagga.ImageDebug.drawPath(box, {x: 0, y: 1}, drawingCtx, {color: "green", lineWidth: 2});
                });
            }

            if (result.box) {
                Quagga.ImageDebug.drawPath(result.box, {x: 0, y: 1}, drawingCtx, {color: "#00F", lineWidth: 2});
            }

            if (result.codeResult && result.codeResult.code) {
                Quagga.ImageDebug.drawPath(result.line, {x: 'x', y: 'y'}, drawingCtx, {color: 'red', lineWidth: 3});
            }
        }
    });

    Quagga.onDetected(function(result) {
        var code = result.codeResult.code;
        if (App.lastResult !== code) {
            App.lastResult = code;
            var $node = null, canvas = Quagga.canvas.dom.image;

            $node = $('<li><div class="thumbnail"><div class="imgWrapper"><img /></div><div class="caption"><h4 class="code"></h4></div></div></li>');
            $node.find("img").attr("src", canvas.toDataURL());
            $node.find("h4.code").html(code);
            $("#result_strip ul.thumbnails").prepend($node);
        }
    });
});
        }
    }
</script>
<style>
  /*#camera video, canvas {
    width: 100%;
    height: auto;
  }

  #camera video.drawingBuffer, canvas.drawingBuffer {
    display: none;
  }*/

  @charset "UTF-8";

.collapsable-source pre {
    font-size: small;
}

.input-field {
    display: flex;
    align-items: center;
    width: 260px;
}

.input-field label {
    flex: 0 0 auto;
    padding-right: 0.5rem;
}

.input-field input {
    flex: 1 1 auto;
    height: 20px;
}

.input-field button {
    flex: 0 0 auto;
    height: 28px;
    font-size: 20px;
    width: 40px;
}

.icon-barcode {
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center center;
    background-image: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPjxzdmcgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiI+PHBhdGggZD0iTTAgNGg0djIwaC00ek02IDRoMnYyMGgtMnpNMTAgNGgydjIwaC0yek0xNiA0aDJ2MjBoLTJ6TTI0IDRoMnYyMGgtMnpNMzAgNGgydjIwaC0yek0yMCA0aDF2MjBoLTF6TTE0IDRoMXYyMGgtMXpNMjcgNGgxdjIwaC0xek0wIDI2aDJ2MmgtMnpNNiAyNmgydjJoLTJ6TTEwIDI2aDJ2MmgtMnpNMjAgMjZoMnYyaC0yek0zMCAyNmgydjJoLTJ6TTI0IDI2aDR2MmgtNHpNMTQgMjZoNHYyaC00eiI+PC9wYXRoPjwvc3ZnPg==);
}

.overlay {
    overflow: hidden;
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    width: 100%;
    background-color: rgba(0, 0, 0, 0.3);
}

.overlay__content {
    top: 50%;
    position: absolute;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 90%;
    max-height: 90%;
    max-width: 800px;
}

.overlay__close {
    position: absolute;
    right: 0;
    padding: 0.5rem;
    width: 2rem;
    height: 2rem;
    line-height: 2rem;
    text-align: center;
    background-color: white;
    cursor: pointer;
    border: 3px solid black;
    font-size: 1.5rem;
    margin: -1rem;
    border-radius: 2rem;
    z-index: 100;
    box-sizing: content-box;
}

.overlay__content video {
    width: 100%;
    height: 100%;
}

.overlay__content canvas {
    position: absolute;
    width: 100%;
    height: 100%;
    left: 0;
    top: 0;
}

#interactive.viewport {
    position: relative;
}

#interactive.viewport > canvas, #interactive.viewport > video {
    max-width: 100%;
    width: 100%;
}

canvas.drawing, canvas.drawingBuffer {
    position: absolute;
    left: 0;
    top: 0;
}

/* line 16, ../sass/_viewport.scss */
.controls fieldset {
  border: none;
}
/* line 19, ../sass/_viewport.scss */
.controls .input-group {
  float: left;
}
/* line 21, ../sass/_viewport.scss */
.controls .input-group input, .controls .input-group button {
  display: block;
}
/* line 25, ../sass/_viewport.scss */
.controls .reader-config-group {
  float: right;
}
/* line 28, ../sass/_viewport.scss */
.controls .reader-config-group label {
  display: block;
}
/* line 30, ../sass/_viewport.scss */
.controls .reader-config-group label span {
  width: 11rem;
  display: inline-block;
  text-align: right;
}
/* line 37, ../sass/_viewport.scss */
.controls:after {
  content: '';
  display: block;
  clear: both;
}

/* line 22, ../sass/_viewport.scss */
#result_strip {
  margin: 10px 0;
  border-top: 1px solid #EEE;
  border-bottom: 1px solid #EEE;
  padding: 10px 0;
}
/* line 28, ../sass/_viewport.scss */
#result_strip ul.thumbnails {
  padding: 0;
  margin: 0;
  list-style-type: none;
  width: auto;
  overflow-x: auto;
  overflow-y: hidden;
  white-space: nowrap;
}
/* line 37, ../sass/_viewport.scss */
#result_strip ul.thumbnails > li {
  display: inline-block;
  vertical-align: middle;
  width: 160px;
}
/* line 41, ../sass/_viewport.scss */
#result_strip ul.thumbnails > li .thumbnail {
  padding: 5px;
  margin: 4px;
  border: 1px dashed #CCC;
}
/* line 46, ../sass/_viewport.scss */
#result_strip ul.thumbnails > li .thumbnail img {
  max-width: 140px;
}
/* line 49, ../sass/_viewport.scss */
#result_strip ul.thumbnails > li .thumbnail .caption {
  white-space: normal;
}
/* line 51, ../sass/_viewport.scss */
#result_strip ul.thumbnails > li .thumbnail .caption h4 {
  text-align: center;
  word-wrap: break-word;
  height: 40px;
  margin: 0px;
}
/* line 61, ../sass/_viewport.scss */
#result_strip ul.thumbnails:after {
  content: "";
  display: table;
  clear: both;
}

@media (max-width: 603px) {
  /* line 2, ../sass/phone/_core.scss */
  #container {
    margin: 10px auto;
    -moz-box-shadow: none;
    -webkit-box-shadow: none;
    box-shadow: none;
  }
}
@media (max-width: 603px) {
  /* line 5, ../sass/phone/_viewport.scss */
  #interactive.viewport {
    width: 100%;
    height: auto;
    overflow: hidden;
  }

  /* line 20, ../sass/phone/_viewport.scss */
  #result_strip {
    margin-top: 5px;
    padding-top: 5px;
  }

  #result_strip ul.thumbnails {
    width: 100%;
    height: auto;
  }

  /* line 24, ../sass/phone/_viewport.scss */
  #result_strip ul.thumbnails > li {
    width: 150px;
  }
  /* line 27, ../sass/phone/_viewport.scss */
  #result_strip ul.thumbnails > li .thumbnail .imgWrapper {
    width: 130px;
    height: 130px;
    overflow: hidden;
  }
  /* line 31, ../sass/phone/_viewport.scss */
  #result_strip ul.thumbnails > li .thumbnail .imgWrapper img {
    margin-top: -25px;
    width: 130px;
    height: 180px;
  }
}
</style>