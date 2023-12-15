$(document).ready(function () {
	if ($('.historia_clinica').length) {	
		var tabla = $('#tabla').DataTable({
	        language: {
	            url: $endpoint_cdn + '/datatables/1.10.12/Spanish_sym.json',
	            decimal: ',',
	            thousands: '.',
	            infoEmpty: 'No hay datos de Historia Clinica...'
	        },
	        processing: true,
	        serverSide: true,
	        //responsive: true,
	        searchDelay: 1200,

	        ajax: {
	            url: $base_url + '/index.php/Consultasmedicas/ajax_historia_clinica',
	            contentType: "application/json",
	            data: function (d) {
                filtros_dataTable = $.extend({}, d, {
                	dni							: $dni,
                    articulo_filtro  			: $('#articulo_filtro').val(),
                    estado_filtro  				: $('#estado_filtro').val(),
                    interviniente_filtro        : $('#interviniente_filtro').val()
                });
                return filtros_dataTable; 
                } 	
	         
	        },
	        info: true, 
	        bFilter: true,
	        columnDefs: [
		        { targets: 0, width: '5%'}, //numero de consulta
		        { targets: 1, width: '5%'}, //tipo operación
		        { targets: 2, width: '5%'}, //fecha operacion
		        { targets: 3, width: '5'}, //fecha de intervencion
		        { targets: 4, width: '5%'}, //articulo
		        { targets: 5, width: '5%'}, //estado
		        { targets: 6, width: '5%'}, //iterviniente
		        { targets: 7, width: '15%'}, //fecha desde
		        { targets: 8, width: '15%'}, //fecha hasta
		        { targets: 9, width: '20%'}, //regresa a trabajar
		        { targets: 10, width: '10%'}, //fecha nueva revisión
		        { targets: 11, width: '15%'}, //observaciones
		        { targets: 12, width: '15%'}, //adjuntos
	        ],
	        order: [[0,'desc'],[2, 'desc']],
	        columns: [
	            {
	                title: 'Nº Consulta',
	                name:  'numero_consulta',
	                data:  'numero_consulta',
	                className: 'text-left',
	            }, 
	            {
	                title: 'Tipo de Operación',
	                name:  'tipo_operacion',
	                data:  'tipo_operacion',
	                className: 'text-left',
	            },
	            {
	                title: 'Fecha de Operación',
	                name:  'fecha_operacion',
	                data:  'fecha_operacion',
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
	                title: 'Artículo',
	                name:  'articulo',
	                data:  'articulo',
	                className: 'text-left',
	            },
	            {
	                title: 'Estado',
	                name:  'estado',
	                data:  'estado',
	                className: 'text-left',
	            },
	            {
	                title: 'Interviniente',
	                name:  'interviniente',
	                data:  'interviniente',
	                className: 'text-left',
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
	                title: 'Regreso al Trabajo',
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
	                title: 'Fecha Nueva Revisión',
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
    $('#articulo_filtro').on('change', update);

    $('#estado_filtro').on('change', update);

    $('#interviniente_filtro').on('change', update);

    //filtros para el exportador
    $(".accion_exportador").click(function () {
    var form = $('<form/>', {id:'form_ln' , action : $(this).val(), method : 'POST'});
    $(this).append(form);
    form.append($('<input/>', {name: 'search', type: 'hidden', value: $('div.dataTables_filter input').val() }))
        .append($('<input/>', {name: 'campo_sort', type: 'hidden', value: $('#tabla').dataTable().fnSettings().aoColumns[$('#tabla').dataTable().fnSettings().aaSorting[0][0]].name }))
        .append($('<input/>', {name: 'dir', type: 'hidden', value: $('#tabla').dataTable().fnSettings().aaSorting[0][1] }))
        .append($('<input/>', {name: 'rows', type: 'hidden', value: $('#tabla').dataTable().fnSettings().fnRecordsDisplay() }))
        .append($('<input/>', {name: 'articulo', type: 'hidden', value:$('#articulo_filtro').val() }))
        .append($('<input/>', {name: 'estado', type: 'hidden', value:$('#estado_filtro').val() }))
        .append($('<input/>', {name: 'interviniente', type: 'hidden', value:$('#interviniente_filtro').val() }));
     form.submit();
	});

});