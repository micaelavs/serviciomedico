$(document).ready(function () {

	let const_apto_si = $opciones_apto['apto_si'];
	let const_apto_no = $opciones_apto['apto_no'];
	let rol_medico 	= $roles['medico'];
	let rol_enfermera = $roles['enfermera'];
	let rrhh 		= $roles['rrhh'];
	let admin 		= $roles['administrador'];
	let rol_actual 	= $rol_actual;
	
	//boton apto siempre inhabilitado, sólo se habilita cuando completen la fecha apto, dni este correcto, este apto y nombre y apellido completo
	$(function(){
		let flagMod = $("#flag").val();
		let fechaApto = $('#fecha_apto').val();
		let dni = $('#dni').val();
		let apellidoNombre = $('#apellido_nombre').val();
		let apto = $('#apto').val();
		if(flagMod){
			if((fechaApto.length !=0) && (dni.length !=0) && (apellidoNombre.length !=0) && (apto == const_apto_si) && (rol_actual == rol_medico || rol_actual== rol_enfermera) ){
				$("#boton_apto").removeAttr('disabled');
				$('#boton_apto').prop('href',$base_url+'/consultasmedicas/aptoMedico/'+dni);
			}else{
				$("#boton_apto").attr('disabled','disabled');
				$('#boton_apto').prop('href', '');
			}
		}

	 });	
	
	//botón comprobante, siempre deshabilitado, sòlo se habilita cuando completen, fecha intervencion, artículo, interviniente, fecha desde, fecha hasta
	$(function(){
			let flagMod = $("#flag").val(); //viene con 0 si estas en el alta
			//let fechaIntervencion = $('#fecha_intervencion').val();
			//let articulo = $('#articulo').val();
			//let interviniente = $('#interviniente').val();
			//let fechaDesde = $('#fecha_desde').val();
			//let fechaHasta = $('#fecha_hasta').val();
			if(flagMod ==1 && (rol_actual == rol_medico || rol_actual== rol_enfermera)){ //si estas en la modificación remueve el attr OK PODES IMPRIMIR COMPROBANTE DE CONSULTA
				//if((articulo.length != 0) && (interviniente.length != 0) &&  (interviniente.length != 0) && (fechaDesde.length !=0 ) && (fechaHasta.length !=0 ) && (fechaIntervencion.length !=0)){
					$("#boton_comprobante").removeAttr('disabled');
				}else{
					$("#boton_comprobante").attr('disabled','disabled');
				}
			//}	
	 });	
	//Botón resumen de eventos siempre deshabilitado, solo habilita cuando escribe dni. --> ALTA
	$(function(){
		$('#dni').keyup(function() { 
			if((this.value).length == 8) {
				$("#boton_resumen_eventos").removeAttr('disabled');
			}else if((this.value).length==0){
				$("#boton_resumen_eventos").attr('disabled','disabled');
			}	
				
		});

	 });	

	//Botón resumen de eventos siempre deshabilitado, solo habilita cuando escribe dni completo. --> MODIFICACION YA VIENE CARGADO
	$(function(){
		if($('#dni').length){
			let dni = $('#dni').val();
			if(dni.length == 8) {
				$("#boton_resumen_eventos").removeAttr('disabled');
			}else{
				$("#boton_resumen_eventos").attr('disabled','disabled');
			}	
		}
	 });	

	//Botón historia clínica siempre deshabilitado, solo habilita cuando escribe dni y está en los roles pertinentes. --> ALTA
	$(function(){
		$('#dni').keyup(function() { 
			if(((this.value).length == 8) && (rol_actual == rol_medico || rol_actual== rol_enfermera)){
				$("#boton_historia_clinica").removeAttr('disabled');
			}else if((this.value).length==0){
				$("#boton_historia_clinica").attr('disabled','disabled');
			}	
				
		});

	 });	

	//Botón historia clínica siempre deshabilitado, solo habilita cuando escribe dni completo y está en el rol permitido. --> MODIFICACION YA VIENE CARGADO
	$(function(){
		if($('#dni').length){
			let dni = $('#dni').val();
			if((dni.length == 8) && (rol_actual == rol_medico || rol_actual== rol_enfermera)){
				$("#boton_historia_clinica").removeAttr('disabled');
			}else{
				$("#boton_historia_clinica").attr('disabled','disabled');
			}	
		}
	 });	

	function calcularEdad(fecha) {
            nuevafecha = fecha.split('/').reverse().join('/');
            let hoy = new Date();
            let cumpleanos = new Date(nuevafecha);
            let edad = hoy.getFullYear() - cumpleanos.getFullYear();
            let m = hoy.getMonth() - cumpleanos.getMonth();

            if (m < 0 || (m === 0 && hoy.getDate() < cumpleanos.getDate())) {
                edad--;
            }
        	return edad;
    	}
    //para que se calcule la edad si no viene la fecha nacimiento del servidor
	$(function(){
        	$('#fecha_nacimiento').datetimepicker({
        		format: 'DD/MM/YYYY'
				}).on("dp.change", function (e) {
            	let edad = calcularEdad(this.value);
            	$('#edad').text(edad);
        });
    });

    //alta consulta
	if($('.alta_consulta_medica').length){
		 $(".fecha_nacimiento").datetimepicker({
				format: 'DD/MM/YYYY',
				maxDate: 'now'
			});

		$("#fecha_nacimiento").datetimepicker({
				format: 'DD/MM/YYYY',
				maxDate: 'now'
			});

		$(".fecha_apto").datetimepicker({
				format: 'DD/MM/YYYY',
				//minDate: 'now'
			});

		$("#fecha_apto").datetimepicker({
				format: 'DD/MM/YYYY',
				//minDate: 'now'
			});

		$(".fecha_intervencion").datetimepicker({
				format: 'DD/MM/YYYY HH:mm',
				
			});

		$("#fecha_intervencion").datetimepicker({
				format: 'DD/MM/YYYY HH:mm',
				
			});

		$(".fecha_desde").datetimepicker({
				format: 'DD/MM/YYYY',
				//minDate: 'now'
			});

		$("#fecha_desde").datetimepicker({
				format: 'DD/MM/YYYY',
				//minDate: 'now'
			});

		$(".fecha_hasta").datetimepicker({
				format: 'DD/MM/YYYY',
				//minDate: 'now'
			});

		$("#fecha_hasta").datetimepicker({
				format: 'DD/MM/YYYY',
				//minDate: 'now'
			});

		$(".fecha_regreso").datetimepicker({
				format: 'DD/MM/YYYY',
				//minDate: 'now'
			});

		$("#fecha_regreso").datetimepicker({
				format: 'DD/MM/YYYY',
				//minDate: 'now'
			});

		$(".fecha_nueva_revision").datetimepicker({
				format: 'DD/MM/YYYY',
				//minDate: 'now'
			});

		$("#fecha_nueva_revision").datetimepicker({
				format: 'DD/MM/YYYY',
				//minDate: 'now'
			});


	/*agregar o quitar items de documento adjunto y setea cada campo con su respectivo id y name iterativo*/
		(function Adjuntos(){
	/** @var object $secciones_ids - Los "id" contenedores (padres) de cada seccion, y su respectivo titulo para los botones Agregar/Eliminar */
		$secciones_ids	= {
			
			'#documentos_div' 				: 'Documento',
		};

		$.each($secciones_ids, function(_id, _titulo){
			$(document).on('click', _id + ' .btn-add', function(e){
				e.preventDefault();
				var controlForm		= $(this).parents().parents('.combo-input');
				var	currentEntry	= $(this).parents('.entry:first');
				currentEntry	= currentEntry.clone();

				/**
				 * Setea nuevos "id" a los <input> y su respectivo "for" a los <label>
				 * usando como variable el atributo "data-new-id"
				*/
				
				if(_id == '#documentos_div'){
					var new_id	= parseInt(currentEntry.attr('data-new-id')) + 1;
					currentEntry.attr('data-new-id', new_id);

					controlForm.parents().parents().find('label[for="documento0"]')
						.attr('for', 'documento' + new_id)

					controlForm.parents().parents().find('input#documento0')
						.attr('id', 'documento' + new_id)
						.attr('name', 'documento[new][' + new_id + '][doc]')
				}
				/*Le coloco el file input y su formato al campo, cuando se generen dinamicamente acá*/
						$("#documento" + new_id).fileinput({
							language: 'es',
					      	browseLabel: 'Seleccione archivo',
					      	showRemove: false,
					      	showUpload: false,
					      	previewFileIcon: '<i class="glyphicon glyphicon-eye"></i>',
					      	previewFileIconClass: 'file-icon-4x',
						});
						/**fin file input*/
						
				var newEntry		= $(currentEntry).appendTo(controlForm);
				newEntry.find('.form-control').val('');
				controlForm.find('.entry:not(:last) .btn-add')
					.removeClass('btn-add').addClass('btn-remove')
					.removeClass('btn-info').addClass('btn-default')
					.attr('title', 'Eliminar ' + _titulo)
					.html('<span class="glyphicon glyphicon-minus"></span>');

			 	var block = $('[data-new-id="'+new_id+'"]');
				block.find('input#documento').attr('name', 'documento[new][' + new_id + '][doc]');	

			}).on('click', _id + ' .btn-remove', function(e){
				$(this).parents('.entry:first').remove();
				e.preventDefault();
				return false;
			});
		});
	})();

	}

	if($('#dni').length){
		$('#dni').keyup(function() { //cuando borren el dni, se borran los campos
	        	if(this.value == ''){
	        		$("#apellido_nombre").val('');
	        		$("#cuit").val('');
	                $("#fecha_nacimiento").val('');
	                $("#edad").text('');
	                $("#modalidad_vinculacion").val('');
	                $("#fecha_apto").val('');
	                $("#tipo").text('');
	                $("#cud").text('');
	                $("#fecha_vencimiento").text('');
	                $("#observacion_discapacidad").text('');
	                $("#calle").text('');
	                $("#numero").text('');
	                $("#piso").text('');
	                $("#dpto").text('');
	                $("#provincia").text('');
	                $("#localidad").text('');
	                $("#codigo_postal").text('');
	                $("#tipo_tel_1").text('');
	                $("#numero_tel_1").text('');
	                $("#tipo_tel_2").text('');
	                $("#numero_tel_2").text('');
	                $("#obra_social").text('');	
	                $('#mensaje').css("display","none");		
	                $("#mensaje").text('');
					$('#alerta').css("display","none");		
	               	$("#alerta").text('');
	               	$('#aviso').css("display","none");		
	               	$("#aviso").text('');
	           		$('#errores_validar').empty();
	           		$("#consultasmedicas").removeAttr('disabled');
	           		//se refrescan los select también
	           		$.ajax({
						url: $base_url+"/Consultasmedicas/actualizarApto",
					    data: {
					    
						},
	   				method: "POST"
					})
					.done(function (data) {
						if(typeof data.data != 'undefined'){
							addOptionsMulti(data.data, '#apto',data.data.nombre);
						}

					})
					.fail(function(data){
						addOptionsMulti([], '#apto');
					});

					$.ajax({
						url: $base_url+"/Consultasmedicas/actualizarGrupo",
					    data: {
					    
						},
	   				method: "POST"
					})
					.done(function (data) {
						if(typeof data.data != 'undefined'){
							addOptionsMulti(data.data, '#grupo',data.data.nombre);
						}

					})
					.fail(function(data){
						addOptionsMulti([], '#grupo');
					});

					$.ajax({
						url: $base_url+"/Consultasmedicas/actualizarTipoApto",
					    data: {
					    
						},
	   				method: "POST"
					})
					.done(function (data) {
						if(typeof data.data != 'undefined'){
							addOptionsMulti(data.data, '#tipo_apto',data.data.nombre);
						}

					})
					.fail(function(data){
						addOptionsMulti([], '#tipo_apto');
					});


	        	}	
	    });   
	}   

	if($('#dni').length){
		$('#dni').typeahead({
	        onSelect: function (item) {

	        },
	        ajax: {
	            url: $base_url+"/Consultasmedicas/buscarAgente",
	            displayField: 'full_name',
	            valueField: 'dep',
	            triggerLength: 8, //ahora se busca por dni, ej: 32318670
	            method: "post",
	            loadingClass: "loading-circle",
	            preDispatch: function (query) {
	            	//escribo el href para el botón historia clínica y resumen de eventos
	            	$('#boton_historia_clinica').prop('href',$base_url+'/consultasmedicas/historia_clinica/'+query);
	            	$('#boton_resumen_eventos').prop('href',$base_url+'/consultasmedicas/resumen_eventos/'+query);
	                return {
	                    dni: query,
	                }
	            },
	            preProcess: function (data) {
	            	let apellidoNombre ='-';
	            	let cuit = '-';
	                let fechaNac = '-';
					let edad = '-';
					let modalidadVic = '-';
					let disTipo = '-';
					let disCud = '-';
					let disFechaVenc = '-';
					let disObserv = '-';
					let domCalle = '-';
					let domPiso = '-';
					let domNumero = '-';
					let domDpto = '-';
					let domPcia = '-';
					let domLocalidad = '-';
					let domCP = '-';
					let telTipo1 = '-'
					let telNum1 = '-';
					let telTipo2 = '-'
					let telNum2 = '-';
					let oSocial = '-';
					let apto = '-';
					let tipo_apto = '-';
					let grupo = '-';
					let fechaApto = '-';

	                if (data.data[0].mensaje_error != undefined) { //mensaje de error, si no está en sigarhu
	                	$('#aviso').css("display","block");		
	                	$("#aviso").text(data.data[0].mensaje_error); 
	                	$("#apellido_nombre").val('');
	                	$("#cuit").val('');
	                	$("#fecha_nacimiento").val('');
	                	$("#edad").text('');
	                	$("#modalidad_vinculacion").val('');
	                	$("#tipo").text('');
	                	$("#cud").text('');
	                	$("#fecha_vencimiento").text('');
	                	$("#observacion_discapacidad").text('');
	                	$("#calle").text('');
	                	$("#numero").text('');
	                	$("#piso").text('');
	                	$("#dpto").text('');
	                	$("#provincia").text('');
	                	$("#localidad").text('');
	                	$("#codigo_postal").text('');
	                	$("#tipo_tel_1").text('');
	                	$("#numero_tel_1").text('');
	                	$("#tipo_tel_2").text('');
	                	$("#numero_tel_2").text('');
	                	$("#obra_social").text('');
	                	//si los datos no vienen de sigarhu
	                	$("input:hidden[name=id_agente]").val('');
	                	
	                	$('#mensaje').css("display","none");		
	                	$("#mensaje").text('');

	                	$('#alerta').css("display","none");		
	                	$("#alerta").text('');
	                    //return false;
	                   	if(data.data[1]!= undefined){
		                    if(data.data[1].datos_persona != undefined){ //pero si la persona si está en servicio médico
		                    	apellidoNombre = data.data[1].datos_persona.apellido_nombre;
		                    	cuit = data.data[1].datos_persona.cuit;
		                    	fechaNac = data.data[1].datos_persona.fecha_nacimiento;
		                    	apto = data.data[1].datos_persona.apto;
		                    	tipo_apto = data.data[1].datos_persona.tipo_apto;
		                    	grupo = data.data[1].datos_persona.grupo_sanguineo;
		                    	modalidadVic = data.data[1].datos_persona.modalidad_vinculacion;
		                    	fechaApto = data.data[1].datos_persona.fecha_apto;
		                    	$("#apellido_nombre").val(apellidoNombre);
		                    	$("#cuit").val(cuit);
		             
		                    	if(fechaNac!=null){
		                    		let laFecha = new Date(fechaNac.date);
		                			let nuevaFecha = moment(laFecha,'YYYY-MM-DD HH:II').format('DD-MM-YYYY');
		                			$("#fecha_nacimiento").val(nuevaFecha);
		                		
		                			if(nuevaFecha.length != 0){ 
		                				if(nuevaFecha.length != 0 && nuevaFecha.includes('-')){
											nuevaFecha = nuevaFecha.replace(/\-/g, '/');
											let edad = calcularEdad(nuevaFecha);
	            							$('#edad').text(edad);
									}
		                			
		                			}
		                    	}

		                		$("#modalidad_vinculacion").val(modalidadVic);
		                		$("#apto").val(apto);
		                		$("#tipo_apto").val(tipo_apto);
		                		$("#grupo").val(grupo);
		                		
		                		let laFecha2 = new Date(fechaApto.date);
		                		let nuevaFecha2 = moment(laFecha2,'YYYY-MM-DD HH:II').format('DD-MM-YYYY'); 
		                		$("#fecha_apto").val(nuevaFecha2);

		                		//imprimir apto: cuit apellido y nombre y fecha de apto completo habilito el botón imprimir apto
		                		let fApto =$("#fecha_apto").val();
								let dni = $('#dni').val();
								let valor_apto = $('#apto').val();
								if((dni.length != 0 ) && (apellidoNombre.length != 0 ) && (fApto.length != 0 ) && (valor_apto == const_apto_si)){
									$("#boton_apto").removeAttr('disabled');
									$('#boton_apto').prop('href',$base_url+'/consultasmedicas/aptoMedico/'+dni); 
								}


		                    }
	                	}

	                }else{ //vienen los datos del agente
	                	$('#aviso').css("display","none");		
	                	$("#aviso").text(''); 
	                
	                	cuit = data.data[0].cuit;
	                	apellidoNombre = data.data[2].apellido_nombre;
	                	fechaNac = data.data[3].fecha_nacimiento;
	                	edad = data.data[4].edad;
	                	modalidadVic = data.data[5].modalidad_vinculacion;
						disTipo = data.data[6].datos_discapacidad.discapacidad;
						disCud = data.data[6].datos_discapacidad.cud;
						disFechaVenc = data.data[6].datos_discapacidad.fecha_vencimiento;
						disObserv = data.data[6].datos_discapacidad.observaciones;
						domCalle = data.data[7].domicilio.calle;
						domPiso = data.data[7].domicilio.piso;
						domNumero = data.data[7].domicilio.numero;
						domDpto = data.data[7].domicilio.depto;
						domPcia = data.data[7].domicilio.pcia;
						domLocalidad = data.data[7].domicilio.localidad;
						domCP = data.data[7].domicilio.cod_postal;
						telefonos = data.data[8];
						if(telefonos.length == 1){//si viene un solo tel
							telTipo1 = data.data[8][0].tipo_telefono;
							telNum1 = data.data[8][0].telefono;
							}else if(telefonos.length == 2){//si vienen los dos tel, fijo y cel
								telTipo2 = data.data[8][1].tipo_telefono;
								telNum2 = data.data[8][1].telefono;
								telTipo1 = data.data[8][0].tipo_telefono;
								telNum1 = data.data[8][0].telefono;
							}
						oSocial =  data.data[9].obra_social;
	                	$("#apellido_nombre").val(apellidoNombre);
	                	$("#cuit").val(cuit);
	                	$("#fecha_nacimiento").val(fechaNac);
	                	$("#edad").text(edad);
	                	$("#modalidad_vinculacion").val(modalidadVic);
	                	$("#tipo").text(disTipo);
	                	$("#cud").text(disCud);
	                	$("#fecha_vencimiento").text(disFechaVenc);
	                	$("#observacion_discapacidad").text(disObserv);
	                	$("#calle").text(domCalle);
	                	$("#numero").text(domNumero);
	                	$("#piso").text(domPiso);
	                	$("#dpto").text(domDpto);
	                	$("#provincia").text(domPcia);
	                	$("#localidad").text(domLocalidad);
	                	$("#codigo_postal").text(domCP);
	                	$("#tipo_tel_1").text(telTipo1);
	                	$("#numero_tel_1").text(telNum1);
	                	$("#tipo_tel_2").text(telTipo2);
	                	$("#numero_tel_2").text(telNum2);
	                	$("#obra_social").text(oSocial);
	                	//esto hay que mostrarlo solo si se da la persona de alta (la persona se la da de alta por única vez)
	                	if(data.data[11].mensaje_alta != undefined){
	                		$('#mensaje').css("display","block");		
	                		$("#mensaje").text(data.data[11].mensaje_alta); 
	                	}

	                	if(data.data[11].mensaje_aviso_alta != undefined){
	                		$('#alerta').css("display","block");		
	                		$("#alerta").text(data.data[11].mensaje_aviso_alta); 
	                	}
	                		if(data.data[11].mensaje_error_alta != undefined){
	                		$('#alerta').css("display","block");		
	                		$("#alerta").text(data.data[11].mensaje_error_alta); 
	                	}

                		if(data.data[12].datos_persona != undefined){ //si esta en servicio médico escribo los datos
                			$("#apto").val(data.data[12].datos_persona.apto); 
                			$("#tipo_apto").val(data.data[12].datos_persona.tipo_apto); 
                			$("#grupo").val(data.data[12].datos_persona.grupo_sanguineo); 
                			let laFecha = data.data[12].datos_persona.fecha_apto;
                			
                			if(laFecha!=null){
                				let laFecha2 = new Date(laFecha.date);
	                			let nuevaFecha = moment(laFecha2,'YYYY-MM-DD HH:II').format('DD-MM-YYYY'); 
	                			$("#fecha_apto").val(nuevaFecha);
                			}
	                		
	                		//Imprimir apto: dni, apellido y nombre, fecha de apto completo, apto sí habilito el botón imprimir apto
	                		let dni = $("#dni").val();
	                		let fApto = $("#fecha_apto").val();
	                		let valor_apto = $('#apto').val();
	                		if((dni.length != 0 ) && (apellidoNombre.length != 0 ) && (fApto.length != 0 ) && (valor_apto == const_apto_si)){						
								$("#boton_apto").removeAttr('disabled');
								$('#boton_apto').prop('href',$base_url+'/consultasmedicas/aptoMedico/'+dni);
                			}
	                	}
	                	
	                	//cargo un hidden con id del agente
	                	$("input:hidden[name=id_agente]").val(data.data[10].id_agente);
	                	
	                }

	               
	            }

		        
	        }
	      
	    });
	}

	//cuando los campos esten todos completos y no venga el agente (persona) de sigarhu tendría que dar de alta
	//cuando cambie la fecha desde de consulta médica debería ahí recién dar el alta, porque fecha desde es un campo obligatorio
	$('#fecha_desde').datetimepicker({
	    format: 'DD/MM/YYYY'
	    }).on("dp.change", function (e) {
		
		let dni = $('#dni').val();

		/*function isCuitValid(cuit){
			const regexCuit = /^(20|23|27|24|30|33|34)([0-9]{9}|[0-9]{8}[0-9]{1})$/g;
			return regexCuit.test(cuit);
		}*/

		function isDniValid(dni){ //preg_match('/[0-9]{8}/', $dni);
			const regexDni = /^[0-9]{8}$/;
			return regexDni.test(dni);
		}	

		if(!isDniValid(dni)){
		    $("#aviso").text(''); 
			$('#aviso').css("display","block");		
            $("#aviso").text('El formato del DNI ingresado es incorrecto, por favor verifique.'); 
            $("#consultasmedicas").attr('disabled','disabled');
		}else{
			$('#aviso').css("display","none");		
            $("#aviso").text(''); 
            $("#consultasmedicas").removeAttr('disabled');
        
        	//si el dni es valido:    
        	let cuit = $('#cuit').val();
	        let apellido_nombre = $('#apellido_nombre').val();
			let id_agente = $("input:hidden[name=id_agente]").val();
			let fecha_nac = $('#fecha_nacimiento').val();
			let apto = $('select#apto').val();
			let tipo_apto = $('select#tipo_apto').val();
			let grupo = $('select#grupo').val();
			let modalidad_vinculacion = $('#modalidad_vinculacion').val();
			let fecha_apto = $('#fecha_apto').val();

			if(fecha_nac.length != 0 && fecha_nac.includes('-')){
				fecha_nac = fecha_nac.replace(/\-/g, '/');
			}
			
			if(fecha_apto.length != 0 && fecha_apto.includes('-')){
				fecha_apto = fecha_apto.replace(/\-/g, '/');
			}

			if((dni.length == 8) && (apellido_nombre.length > 5) && (id_agente.length == 0)){ //el agente de sigarhu no existe
				$.ajax({
					url: $base_url+"/Consultasmedicas/persona_alta",
				    data: {
				    cuit: cuit,
				    dni: dni,
				    apellido_nombre: apellido_nombre,
				    fecha_nac: fecha_nac,
				    apto: apto,
				    tipo_apto: tipo_apto,
				    grupo: grupo,
				    modalidad_vinculacion: modalidad_vinculacion,
				    fecha_apto: fecha_apto 
					},
				    method: "POST"
				})
				.done(function (data) {
					if(data.data[0].errores != undefined){
						for (let i = 0; i <= data.data[0].errores.length - 1; i++) {
							$('#errores_validar').append('<div style="display: block;" class="alert alert-danger fade in alert-dismissable">'+data.data[0].errores[i]+'</div>');
						
						}

					}

					if(data.data[0].error_existe != undefined){//aviso que ya existe y se actualizaron los datos
						$('#alerta').css("display","block");		
		        		$("#alerta").text(data.data[0].error_existe); 
					}

					if(data.data[0].mensaje != undefined){
						$('#mensaje').css("display","block");		
		        		$("#mensaje").text(data.data[0].mensaje); 
					}	
					
				

				})
				.fail(function(data){
					
				});

			}else if((dni.length == 8) && (apellido_nombre.length > 5) && (id_agente.length != 0)){ //el agente de sigarhu existe
				$.ajax({
					url: $base_url+"/Consultasmedicas/persona_actualizar",
				    data: {
				    dni: dni,
				    cuit: cuit,
				    apellido_nombre: apellido_nombre,
				    fecha_nac: fecha_nac,
				    apto: apto,
				    tipo_apto: tipo_apto,
				    grupo: grupo,
				    modalidad_vinculacion: modalidad_vinculacion,
				    fecha_apto: fecha_apto, 
				    id_sigarhu: id_agente
					},
				    method: "POST"
				})
				.done(function (data) {
				
				if(data.data[0].mensaje != undefined){
		        	$('#mensaje').css("display","block");		
		        	$("#mensaje").text(data.data[0].mensaje); 
		        }

		        if(data.data[0].error != undefined){
		        	$('#aviso').css("display","block");		
		        	$("#aviso").text(data.data[0].error); 
		        }

		        if(data.data[0].errores != undefined){
						for (let i = 0; i <= data.data[0].errores.length - 1; i++) {
							$('#errores_validar').append('<div style="display: block;" class="alert alert-danger fade in alert-dismissable">'+data.data[0].errores[i]+'</div>');
						
						}

					}

				})
				.fail(function(data){
					
				});
			
			}

		} 

			
	});
	//se compenta ya que impresion del comprobante solo en la modificación se podrá imprimir    
	//si estoy en el form
	//para el boton imprimir comprobante: cuando cambie la fecha hasta, el ultimo campo obligatorio para imprimir comprobante y estén completos artículo, estado, interviniente, fecha Desde, fecha hasta se habilita    
	if($('.alta_consulta_medica').length){
	/*$('#fecha_hasta').datetimepicker({
	    format: 'DD/MM/YYYY'
	    }).on("dp.change", function (e) {
	    	let fechaIntervencion = $('#fecha_intervencion').val();
			let articulo = $('#articulo').val();
			let interviniente = $('#interviniente').val();
			let fechaDesde = $('#fecha_desde').val();
			if((articulo.length != 0) && (interviniente.length != 0) &&  (interviniente.length != 0) && (fechaDesde.length !=0 ) && (fechaIntervencion.length !=0)){
				$("#boton_comprobante").removeAttr('disabled');
			}
	    
	    });*/

	//para el botón imprimir apto: cuando cambie la fecha apto (o este completa) y estén completos los campos dni, apellido y nombre.
	$('#fecha_apto').datetimepicker({
	    format: 'DD/MM/YYYY'
	    }).on("dp.change", function (e) {
		    let fechaApto = $('#fecha_apto').val();
			let dni = $('#dni').val();
			let apellido_nombre = $('#apellido_nombre').val();
			let valor_apto = $('#apto').val();
		    //el dni ya lo validó el sistema antes, en su momento cuando lo tipean
			if((dni.length != 0 ) && (apellido_nombre.length != 0) && (valor_apto == const_apto_si)){
				$("#boton_apto").removeAttr('disabled');
				$('#boton_apto').prop('href',$base_url+'/consultasmedicas/aptoMedico/'+dni);
			}
	    
	    });
	    //cuando seleccione apto que traiga la opción correspondiente Apto: no, Tipo_apto: No apto // Apto: sí, Tipo_apto: Las demás opciones
	    $('select#apto').on('change', function($e){
			let id_apto;
			if($('select#apto').val()==""){
				id_apto = 0;
			}else{
					id_apto = $('select#apto').val();
					$.ajax({
					url: $base_url+"/Consultasmedicas/traer_tipo_apto_segun_apto",
					data: {
						id_apto
					},
					method: "POST"
					})
					.done(function (data) {
						addOptionsMulti(data.data, '#tipo_apto',data.data.nombre);
  					})
  					.fail(function(data){
    					addOptionsMulti([], '#tipo_apto');
					});
		
			}
		

		});
	
	}


	//para el listado ajax de las consultas médicas
	var filtros_dataTable = null;	
	if ($('.consultas_listado').length) { 
		var tabla = $('#tabla').DataTable({
	        language: {
	            url: $endpoint_cdn + '/datatables/1.10.12/Spanish_sym.json',
	            decimal: ',',
	            thousands: '.',
	            infoEmpty: 'No hay datos de Consultas...'
	        },
	        processing: true,
	        serverSide: true,
	        //responsive: true,
	        searchDelay: 1200,

	        ajax: {
	            url: $base_url + '/index.php/Consultasmedicas/ajax_consultas_medicas',
	            contentType: "application/json",
	            data: function (d) {
                filtros_dataTable = $.extend({}, d, {
                    dni_filtro            		: $('#dni_filtro').val(),
                    fecha_intervencion_filtro	: $('#fecha_intervencion_filtro').val(),
                    articulo_filtro  			: $('#articulo_filtro').val(),
                    estado_filtro  				: $('#estado_filtro').val(),
                    fecha_desde_filtro  		: $('#fecha_desde_filtro').val(),
                   	fecha_hasta_filtro  		: $('#fecha_hasta_filtro').val()
                });
                return filtros_dataTable; 
                } 	
	        },
	        info: true, 
	        bFilter: true,
	        columnDefs: [
	        	{ targets: 0, width: '5%'}, //id
		        { targets: 1, width: '10%'}, //estado
		        { targets: 2, width: '10%'}, //fecha
		        { targets: 3, width: '10%'}, //dni
		        { targets: 4, width: '10%'}, //cuit
		        { targets: 5, width: '10%'}, //nombre y apellido
				{ targets: 6, width: '10%'}, //interviniente
				{ targets: 7, width: '10%'}, //articulo
				{ targets: 8, width: '8%'}, //fecha_desde
				{ targets: 9, width: '8%'}, //fecha_hasta
				{ targets: 10, width: '5%' } //acciones
	        ],
	        order: [[2,'desc']],
	        columns: [
	        	{
	                title: 'Nº consulta',
	                name:  'id',
	                data:  'id',
	                className: 'text-left'
	            },
	            {
	                title: 'Estado',
	                name:  'estado',
	                data:  'estado',
	                className: 'text-left'
	            },
	            {
	                title: 'Fecha de Intervención',
	                name:  'fecha_intervencion',
	                data:  'fecha_intervencion',
	                className: 'text-left',
	                  render: function (data, type, row) {
						if(data == null){
						}else{
							rta = moment(data,'DD/MM/YYYY HH:mm').format('DD/MM/YYYY HH:mm'); 
						} 	
						
						rta = rta+' hs';
						return rta;
					}
	            },
	            {
	                title: 'DNI',
	                name:  'dni',
	                data:  'dni',
	                className: 'text-left'
	            },
	            {
	                title: 'CUIT',
	                name:  'cuit',
	                data:  'cuit',
	                className: 'text-left'
	            },
	            {
	                title: 'Apellido y Nombre',
	                name: 'apellido_nombre',
	                data: 'apellido_nombre',
	                className: 'text-left'
	            },
	            {
	                title: 'Médico / Enfermera interviniente',
	                name: 'nombre_interviniente',
	                data: 'nombre_interviniente',
	                className: 'text-left'
	            },
	            {
	                title: 'Artículo',
	                name: 'articulo',
	                data: 'articulo',
	                className: 'text-left'
	            },
	            {
	                title: 'Fecha Desde',
	                name:  'fecha_desde',
	                data:  'fecha_desde',
	                className: 'text-left',
	                  render: function (data, type, row) {
						if(data == null){
						}else{
							rta = moment(data,'DD/MM/YYYY').format('DD/MM/YYYY'); 
						} 	
						return rta;
					}
	            },
	            {
	                title: 'Fecha Hasta',
	                name:  'fecha_hasta',
	                data:  'fecha_hasta',
	                className: 'text-left',
	                  render: function (data, type, row) {
						if(data == null){
						}else{
							rta = moment(data,'DD/MM/YYYY').format('DD/MM/YYYY'); 
						} 	
						return rta;
					}
	            },
	            {
	                title: 'Acciones',
	                data: 'acciones',
	                name: 'acciones',
	                className: 'text-center',
	                orderable: false,
	                render: function (data, type, row) {
	                    var $html = '';
	                    $html += '<div class="btn-group btn-group-sm">';
	                    $html += ' <a href="' + $base_url + '/index.php/consultasmedicas/modificacion/' + row.id + '" data-user="" data-toggle="tooltip" data-placement="top" title="Modificar consulta médica" target="_self"><i class="fa fa-pencil"></i></a>&nbsp;';
	                    $html += ' <a href="' + $base_url + '/index.php/consultasmedicas/baja/' + row.id + '" class="borrar" data-user="" data-toggle="tooltip" data-placement="top" title="Baja consulta médica" target="_self"><i class="fa fa-trash"></i></a>';
	                    $html += '</div>';
	                    return $html;
	                }
	            },
	        ]
	    });

	}

	if($('.consultas_listado').length){

		$(".fecha_intervencion_filtro").datetimepicker({
				format: 'DD/MM/YYYY'
			});
		$("#fecha_intervencion_filtro").datetimepicker({
			format: 'DD/MM/YYYY'
		});

		$(".fecha_desde_filtro").datetimepicker({
				format: 'DD/MM/YYYY'
			});
		$("#fecha_desde_filtro").datetimepicker({
			format: 'DD/MM/YYYY'
		});

		$(".fecha_hasta_filtro").datetimepicker({
			format: 'DD/MM/YYYY'
		});
		$("#fecha_hasta_filtro").datetimepicker({
			format: 'DD/MM/YYYY'
		});
	

		/** Consulta al servidor los datos y redibuja la tabla
	     * @return {Void}
	    */
	    function update() {
	        tabla.draw();
	    }

	    /**
	     * Acciones para los filtros, actualizar vista
	    */
	    $('#articulo_filtro').on('change', update);

	    $('#estado_filtro').on('change', update);

	     $('#dni_filtro').keyup(function() {
	        if (this.value.length >= 8) { 
	            update();
	        }else if(this.value == ''){
	        	update();
	        }
	    });

	    $('#fecha_intervencion_filtro,.fecha_intervencion_filtro').datetimepicker({
	        format: 'DD/MM/YYYY'
	    }).on("dp.change", function (e) {
	        update();
	        $('#fecha_intervencion_filtro').keyup(function() { //cuando borran lo que esta en el datepicker se actualiza la tabla
	        	if(this.value == ''){
	        		update();
	       		}
	    	});
	    });

	    $('#fecha_desde_filtro,.fecha_desde_filtro').datetimepicker({
	        format: 'DD/MM/YYYY'
	    }).on("dp.change", function (e) {
	        update();
	        $('#fecha_desde_filtro').keyup(function() { //cuando borran lo que esta en el datepicker se actualiza la tabla
	        	if(this.value == ''){
	        		update();
	       		}
	    	});
	    });

	    $('#fecha_hasta_filtro,.fecha_hasta_filtro').datetimepicker({
	        format: 'DD/MM/YYYY'
	    }).on("dp.change", function (e) {
	        update();
	        $('#fecha_hasta_filtro').keyup(function() { //cuando borran lo que esta en el datepicker se actualiza la tabla
	        	if(this.value == ''){
	        		update();
	       		}
	    	});
	    });

	    //filtros para el exportador
	    $(".accion_exportador").click(function () {
	    var form = $('<form/>', {id:'form_ln' , action : $(this).val(), method : 'POST'});
	    $(this).append(form);
	    form.append($('<input/>', {name: 'search', type: 'hidden', value: $('div.dataTables_filter input').val() }))
	        .append($('<input/>', {name: 'campo_sort', type: 'hidden', value: $('#tabla').dataTable().fnSettings().aoColumns[$('#tabla').dataTable().fnSettings().aaSorting[0][0]].name }))
	        .append($('<input/>', {name: 'dir', type: 'hidden', value: $('#tabla').dataTable().fnSettings().aaSorting[0][1] }))
	        .append($('<input/>', {name: 'rows', type: 'hidden', value: $('#tabla').dataTable().fnSettings().fnRecordsDisplay() }))
	        .append($('<input/>', {name: 'dni', type: 'hidden', value:$('#dni_filtro').val() }))
	        .append($('<input/>', {name: 'fecha_intervencion', type: 'hidden', value:$('#fecha_intervencion_filtro').val() }))
	        .append($('<input/>', {name: 'fecha_desde', type: 'hidden', value:$('#fecha_desde_filtro').val() }))
	        .append($('<input/>', {name: 'fecha_hasta', type: 'hidden', value:$('#fecha_hasta_filtro').val() }))
	        .append($('<input/>', {name: 'articulo', type: 'hidden', value:$('#articulo_filtro').val() }))
	        .append($('<input/>', {name: 'estado', type: 'hidden', value:$('#estado_filtro').val() }));
	     form.submit();
		});
	}

	//para el listado del historial de archivos adjuntOS
	//le paso el id_consulta desde la vista ver_historial
	if ($('.historial').length) {		
		var tabla = $('#tabla').DataTable({
	        language: {
	            url: $endpoint_cdn + '/datatables/1.10.12/Spanish_sym.json',
	            decimal: ',',
	            thousands: '.',
	            infoEmpty: 'No hay datos de Empresas...'
	        },
	        processing: true,
	        serverSide: true,
	        //responsive: true,
	        searchDelay: 1200,

	        ajax: {
	            url: $base_url + '/index.php/Consultasmedicas/ajax_archivos_adjuntos',
	            contentType: "application/json",
	            data: {
		    	id_consulta: $id_consulta,
	     
                } 	
	        },
	        info: true, 
	        bFilter: true,
	        columnDefs: [
		        { targets: 0, width: '15%'}, //nombre
		        { targets: 1, width: '5%'}, //fecha de operacón
				{ targets: 2, width: '5%' } //acciones
	        ],
	        order: [[1,'desc']],
	        columns: [
	            {
	                title: 'Nombre',
	                name:  'nombre',
	                data:  'nombre',
	                className: 'text-left',
	            }, 
	            {
	                title: 'Fecha de Operación',
	                name:  'fecha_alta_operacion',
	                data:  'fecha_alta_operacion',
	                className: 'text-left',
	                  render: function (data, type, row) {
						if(data == null){
						}else{
							rta = moment(data,'DD/MM/YYYY HH:II').format('DD/MM/YYYY'); 
						} 	
						return rta;
					}
	            },
	            {
	                title: 'Acciones',
	                data: 'acciones',
	                name: 'acciones',
	                className: 'text-center',
	                orderable: false,
	                render: function (data, type, row) {
	                    var $html = '';
	                    $html += '<div class="btn-group btn-group-sm">';
	                    $html += ' <a href="' + $base_url + '/index.php/consultasmedicas/ver_adjunto/' + row.id + '" data-user="" data-toggle="tooltip" data-placement="top" title="Ver Archivo" target="_blank"><i class="fa fa-eye"></i></a>&nbsp;';
	                    $html += ' <a href="' + $base_url + '/index.php/consultasmedicas/baja_adjunto/' + row.id + '" class="borrar" data-user="" data-toggle="tooltip" data-placement="top" title="Eliminar Archivo" target="_self"><i class="fa fa-trash"></i></a>';
	                    $html += '</div>';
	                    return $html;
	                }
	            },
	        ]
	    });

	}

	if($('.alta_consulta_medica').length){
		//Cuando carga, si ya se envió correo, se bloquean los campos de la intervención médica, ya no se podrán modificar.
		if($flag_enviado){
			$("#fecha_intervencion").attr('disabled','disabled');	
			$("#articulo").attr('disabled','disabled');	
			$("#estado").attr('disabled','disabled');	
			$("#interviniente").attr('disabled','disabled');	
			$("#fecha_desde").attr('disabled','disabled');	
			$("#fecha_hasta").attr('disabled','disabled');	
			$("#fecha_regreso").attr('disabled','disabled');	
			$("#fecha_nueva_revision").attr('disabled','disabled');	
			$("#medico_tratante").attr('disabled','disabled');	
			$("#telefono_medico_tratante").attr('disabled','disabled');	
			$("#observacion_intervencion").attr('disabled','disabled');	
			$("#consultasmedicas").attr('disabled','disabled');	
			$("#documento0").attr('disabled','disabled');
			$("#boton_comprobante").html('<a href="#" style="color: #fff; id="boton_comprobante"><i class="fa fa-print" aria-hidden="true"></i> IMPRIMIR COMPROBANTE</a>');	
		}

		
		$("#boton_comprobante").click(function (e) {	
			e.preventDefault();
			btn = $(this)
			$.redirect(btn.attr("href"), {},"GET","_blank");

			$.ajax({
				url: $base_url+"/Consultasmedicas/enviarComprobante",
				data: {
				    dni_paciente: $dni_paciente,
				    id_consulta: $id_consulta
					},
				method: "POST",
				beforeSend: function(){
					Swal.fire({
						title: 'Aguarde un momento',
						html: 'Generando comprobante',// add html attribute if you want or remove
						allowOutsideClick: false,
						onBeforeOpen: () => {
							Swal.showLoading()
						},
					});
				},
				success: function (data){
					Swal.close();
					if(typeof data.data != 'undefined'){
						//si se envió OK
						if(typeof data.data.envio_ok != 'undefined'){ 
							$("#fecha_intervencion").attr('disabled','disabled');	
							$("#articulo").attr('disabled','disabled');	
							$("#estado").attr('disabled','disabled');	
							$("#interviniente").attr('disabled','disabled');	
							$("#fecha_desde").attr('disabled','disabled');	
							$("#fecha_hasta").attr('disabled','disabled');	
							$("#fecha_regreso").attr('disabled','disabled');	
							$("#fecha_nueva_revision").attr('disabled','disabled');	
							$("#medico_tratante").attr('disabled','disabled');	
							$("#telefono_medico_tratante").attr('disabled','disabled');	
							$("#observacion_intervencion").attr('disabled','disabled');	
							$("#consultasmedicas").attr('disabled','disabled');	
							$("#documento0").attr('disabled','disabled');	
							//$("#boton_comprobante").text('IMPRIMIR COMPROBANTE');
							$("#boton_comprobante").html('<a href="" disabled style="color:#fff; id="boton_comprobante"><i class="fa fa-print" aria-hidden="true"></i> IMPRIMIR COMPROBANTE</a>');
							$('#mensaje2').css("display","block");		
							$("#mensaje2").text(data.data.envio_ok[0]); 
							//mostrar mensaje envio OK
						}
						//si ya esta enviado... bloquear los campos de la consulta médica y cambiar texto al botón
						if(typeof data.data.respuesta != 'undefined'){
							$("#fecha_intervencion").attr('disabled','disabled');	
							$("#articulo").attr('disabled','disabled');	
							$("#estado").attr('disabled','disabled');	
							$("#interviniente").attr('disabled','disabled');	
							$("#fecha_desde").attr('disabled','disabled');	
							$("#fecha_hasta").attr('disabled','disabled');	
							$("#fecha_regreso").attr('disabled','disabled');	
							$("#fecha_nueva_revision").attr('disabled','disabled');	
							$("#medico_tratante").attr('disabled','disabled');	
							$("#telefono_medico_tratante").attr('disabled','disabled');	
							$("#observacion_intervencion").attr('disabled','disabled');	
							$("#consultasmedicas").attr('disabled','disabled');	
							$("#documento0").attr('disabled','disabled');	
							//$("#boton_comprobante").text('IMPRIMIR COMPROBANTE');
							$("#boton_comprobante").html('<a href="" disabled style="color:#fff; id="boton_comprobante"><i class="fa fa-print" aria-hidden="true"></i> IMPRIMIR COMPROBANTE</a>');
							$('#mensaje2').css("display","none");		
							$("#mensaje2").text(""); 
						}
	
						//Al volver a hacer click dice que solo se pude descargar el doc, el mail se envía una vez
						if(typeof data.data.error_2 != 'undefined'){
							$("#fecha_intervencion").attr('disabled','disabled');	
							$("#articulo").attr('disabled','disabled');	
							$("#estado").attr('disabled','disabled');	
							$("#interviniente").attr('disabled','disabled');	
							$("#fecha_desde").attr('disabled','disabled');	
							$("#fecha_hasta").attr('disabled','disabled');	
							$("#fecha_regreso").attr('disabled','disabled');	
							$("#fecha_nueva_revision").attr('disabled','disabled');	
							$("#medico_tratante").attr('disabled','disabled');	
							$("#telefono_medico_tratante").attr('disabled','disabled');	
							$("#observacion_intervencion").attr('disabled','disabled');	
							$("#consultasmedicas").attr('disabled','disabled');	
							$("#documento0").attr('disabled','disabled');	
							//$("#boton_comprobante").text('IMPRIMIR COMPROBANTE');
							$("#boton_comprobante").html('<a href="" disabled style="color:#fff; id="boton_comprobante"><i class="fa fa-print" aria-hidden="true"></i> IMPRIMIR COMPROBANTE</a>');
							$('#mensaje2').css("display","none");		
							$("#mensaje2").text(""); 
							$('#alerta2').css("display","block");		
							$("#alerta2").text(data.data.error_2[0]); 
	
						}
						if(typeof data.data.error_3 != 'undefined'){ //No existe RCA para el empleado porque no pertenece al min
							$("#fecha_intervencion").attr('disabled','disabled');	
							$("#articulo").attr('disabled','disabled');	
							$("#estado").attr('disabled','disabled');	
							$("#interviniente").attr('disabled','disabled');	
							$("#fecha_desde").attr('disabled','disabled');	
							$("#fecha_hasta").attr('disabled','disabled');	
							$("#fecha_regreso").attr('disabled','disabled');	
							$("#fecha_nueva_revision").attr('disabled','disabled');	
							$("#medico_tratante").attr('disabled','disabled');	
							$("#telefono_medico_tratante").attr('disabled','disabled');	
							$("#observacion_intervencion").attr('disabled','disabled');	
							$("#consultasmedicas").attr('disabled','disabled');	
							$("#documento0").attr('disabled','disabled');	
							//$("#boton_comprobante").text('IMPRIMIR COMPROBANTE');
							$("#boton_comprobante").html('<a href="" disabled style="color:#fff; id="boton_comprobante"><i class="fa fa-print" aria-hidden="true"></i> IMPRIMIR COMPROBANTE</a>');
							$('#mensaje2').css("display","none");		
							$("#mensaje2").text(""); 
							$('#aviso2').css("display","none");		
							$("#aviso2").text(""); 
							$('#alerta2').css("display","block");		
							$("#alerta2").text(data.data.error_3);
	
						}
						//de no enviarse el correo por algun motivo, aparecerá el mensaje de error pero no se bloquea nada
						if(typeof data.data.error != 'undefined'){ 
							$('#mensaje2').css("display","none");		
							$("#mensaje2").text(""); 
							$('#alerta2').css("display","none");		
							$("#alerta2").text(""); 
							$('#aviso2').css("display","block");		
							$("#aviso2").text(data.data.error[0]); 
	
	
						}
						//de no encontrar el correo del rca, porque no tiene o porque el rca no tiene dependencia por ej. 
						//se envía el mail a rrhh y se bloquean los campos
						if(typeof data.data.error_4 != 'undefined'){ 
							$("#fecha_intervencion").attr('disabled','disabled');	
							$("#articulo").attr('disabled','disabled');	
							$("#estado").attr('disabled','disabled');	
							$("#interviniente").attr('disabled','disabled');	
							$("#fecha_desde").attr('disabled','disabled');	
							$("#fecha_hasta").attr('disabled','disabled');	
							$("#fecha_regreso").attr('disabled','disabled');	
							$("#fecha_nueva_revision").attr('disabled','disabled');	
							$("#medico_tratante").attr('disabled','disabled');	
							$("#telefono_medico_tratante").attr('disabled','disabled');	
							$("#observacion_intervencion").attr('disabled','disabled');	
							$("#consultasmedicas").attr('disabled','disabled');	
							$("#documento0").attr('disabled','disabled');	
							//$("#boton_comprobante").text('IMPRIMIR COMPROBANTE');
							$("#boton_comprobante").html('<a href="" disabled style="color:#fff; id="boton_comprobante"><i class="fa fa-print" aria-hidden="true"></i> IMPRIMIR COMPROBANTE</a>');
							$('#mensaje2').css("display","none");		
							$("#mensaje2").text(""); 
							$('#aviso2').css("display","none");		
							$("#aviso2").text(""); 
							$('#alerta2').css("display","block");		
							$("#alerta2").text(data.data.error_4[0]); 
							
	
						}

			
					}else{
						Swal.fire({
							icon: 'Error',
							title: 'Lo sentimos',
							html: 'Ocurrió un error en el envío del comprobante.',
							confirmButtonText: 'Ok'
						});
					}
				}
			})
			
		});


	}	
});


