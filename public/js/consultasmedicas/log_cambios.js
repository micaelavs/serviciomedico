$(document).ready(function () {
	if ($('.log_listado').length) {	
		var tabla = $('#tabla').DataTable({
	        language: {
	            url: $endpoint_cdn + '/datatables/1.10.12/Spanish_sym.json',
	            decimal: ',',
	            thousands: '.',
	            infoEmpty: 'No hay datos de Historial de cambios...'
	        },
	        scrollX: true,
	        processing: true,
	        serverSide: true,
	        responsive: true,
	        searchDelay: 1200,

	        ajax: {
	            url: $base_url + '/index.php/Consultasmedicas/ajax_log_cambios',
	            contentType: "application/json",
	            data: function (d) {
                filtros_dataTable = $.extend({}, d, {
                    dni_filtro  			: $('#dni_filtro').val(),
                    usuario_filtro  		: $('#usuario_filtro').val()
                });
                return filtros_dataTable; 
                } 	
	         
	        },
	        info: true, 
	        bFilter: true,
	        columnDefs: [
		        { targets: 0, width: '5%',responsivePriority:1}, 
		        { targets: 1, width: '5%',responsivePriority:2}, 
		        { targets: 2, width: '20%',responsivePriority:3},
		        { targets: 3, width: '5',responsivePriority:4}, 	
		        { targets: 4, width: '5%',responsivePriority:5},
		        { targets: 5, width: '5%',responsivePriority:6}, 
		        { targets: 6, width: '5%',responsivePriority:6}, 
		        { targets: 7, width: '20%',responsivePriority:7}, 
		        { targets: 8, width: '10%',responsivePriority:8}, 
		        { targets: 9, width: '10%',responsivePriority:9},
		        { targets: 10, width: '10%',responsivePriority:10}, 
		        { targets: 11, width: '10%',responsivePriority:11}, 
		        { targets: 12, width: '10%',responsivePriority:11}, 
		        { targets: 13, width: '10%',responsivePriority:11}, 
		        { targets: 14, width: '10%',responsivePriority:11}, 
		        { targets: 15, width: '10%',responsivePriority:11}, 
		        { targets: 16, width: '15%',responsivePriority:11}, 
		        { targets: 17, width: '15%',responsivePriority:11}, 
		        { targets: 18, width: '15%',responsivePriority:11}, 
		        { targets: 19, width: '15%',responsivePriority:11}, 
	        ],
	        order: [[0,'desc']],
	        columns: [
	           	{
	                title: 'Fecha de Operación',
	                name:  'fecha_operacion',
	                data:  'fecha_operacion',
	                className: 'text-left', 
	            },
	            {
	                title: 'Usuario',
	                name:  'nombre_usuario',
	                data:  'nombre_usuario',
	                className: 'text-left',
	            }, 
	            {
	                title: 'Apellido y Nombre Usuario',
	                name:  'apellido_nombre_usu',
	                data:  'apellido_nombre_usu',
	                className: 'text-left',
	            },
	            {
	                title: 'Operación',
	                name:  'tipo_operacion',
	                data:  'tipo_operacion',
	                className: 'text-left',
	            },
	            {
	                title: 'Consulta',
	                name:  'numero_consulta',
	                data:  'numero_consulta',
	                className: 'text-left',
	            },
	            {
	                title: 'DNI',
	                name:  'dni',
	                data:  'dni',
	                className: 'text-left',
	            },
	            {
	                title: 'CUIT',
	                name:  'cuit',
	                data:  'cuit',
	                className: 'text-left',
	            },
	            {
	                title: 'Apellido y Nombre Agente',
	                name:  'apellido_nombre_pers',
	                data:  'apellido_nombre_pers',
	                className: 'text-left',
	            },
	            {
	                title: 'Estado',
	                name:  'estado',
	                data:  'estado',
	                className: 'text-left',
	            },
	            {
	                title: 'Artículo',
	                name:  'articulo',
	                data:  'articulo',
	                className: 'text-left',
	            },
	            {
	                title: 'Interviniente',
	                name:  'interviniente',
	                data:  'interviniente',
	                className: 'text-left',
	            },
	            {
	                title: 'Fecha Intervención',
	                name:  'fecha_intervencion',
	                data:  'fecha_intervencion',
	                className: 'text-left',
	                  render: function (data, type, row) {
						if(data == null){
						}else{
							rta = moment(data,'DD/MM/YYYY HH:mm').format('DD/MM/YYYY HH:mm'); 
							rta = rta+' hs';
						} 	
					
						return rta;
					}
	            },
	            {
	                title: 'Fecha Desde',
	                name:  'fecha_desde',
	                data:  'fecha_desde',
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
	                title: 'Fecha Hasta',
	                name:  'fecha_hasta',
	                data:  'fecha_hasta',
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
	                title: 'Regresa Trabajo',
	                name:  'fecha_regreso_trabajo',
	                data:  'fecha_regreso_trabajo',
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
	                title: 'Nueva Revisión',
	                name:  'fecha_nueva_revision',
	                data:  'fecha_nueva_revision',
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
	                title: 'Observaciones',
	                name:  'observacion',
	                data:  'observacion',
	                className: 'text-left',
	            },
	            {
	                title: 'Medico Tratante',
	                name:  'medico_tratante',
	                data:  'medico_tratante',
	                className: 'text-left',
	            },
	            {
	                title: 'Telefono Contacto',
	                name:  'contacto_tratante',
	                data:  'contacto_tratante',
	                className: 'text-left',
	            },
	            {
	                title: 'Adjuntos',
	                name:  'doc_adjuntos',
	                data:  'doc_adjuntos',
	                className: 'text-left',
	            },
	           
	        ]
	    });

	}


	/** Consulta al servidor los datos y redibuja la tabla
     * @return {Void}
    */
    function update() {
        tabla.draw();
    }

    /**
     * Acciones para los filtros, actualizar vista
    */
    $('#dni_filtro').on('change', update);

    $('#usuario_filtro').on('change', update);

    //filtros para el exportador
    $(".accion_exportador").click(function () {
    var form = $('<form/>', {id:'form_ln' , action : $(this).val(), method : 'POST'});
    $(this).append(form);
    form.append($('<input/>', {name: 'search', type: 'hidden', value: $('div.dataTables_filter input').val() }))
        .append($('<input/>', {name: 'campo_sort', type: 'hidden', value: $('#tabla').dataTable().fnSettings().aoColumns[$('#tabla').dataTable().fnSettings().aaSorting[0][0]].name }))
        .append($('<input/>', {name: 'dir', type: 'hidden', value: $('#tabla').dataTable().fnSettings().aaSorting[0][1] }))
        .append($('<input/>', {name: 'rows', type: 'hidden', value: $('#tabla').dataTable().fnSettings().fnRecordsDisplay() }))
        .append($('<input/>', {name: 'dni', type: 'hidden', value:$('#dni_filtro').val() }))
        .append($('<input/>', {name: 'usuario', type: 'hidden', value:$('#usuario_filtro').val() }))
     form.submit();
	});

});