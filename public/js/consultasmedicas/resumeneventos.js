$(document).ready(function () {
//para el listado del historial de archivos adjuntOS
//le paso el dni desde la vista resumen_eventos
    if ($('.eventos').length) {
        var tabla = $('#tabla').DataTable({
            language: {
                url: $endpoint_cdn + '/datatables/1.10.12/Spanish_sym.json',
                decimal: ',',
                thousands: '.',
                infoEmpty: 'No hay datos del Empleado...'
            },
            processing: true,
            serverSide: true,
            //responsive: true,
            searchDelay: 1200,

            ajax: {
                url: $base_url + '/index.php/Consultasmedicas/ajax_resumen_eventos',
                contentType: "application/json",
                data: {
                    dni: $dni,
                }
            },      
            info: true,
            bFilter: true,
            columnDefs: [
                { targets: 0, width: '20%', className: 'text-left' },
                { targets: 1, width: '20%', className: 'text-left' },
                { targets: 2, width: '20%', className: 'text-left' },
                { targets: 3, width: '15%', className: 'text-left' },
                { targets: 4, width: '15%', className: 'text-left' },
            ],
            order: [[1, 'desc']],
            columns: [
                {
                    title: 'Articulo',
                    name: 'articulo',
                    data: 'articulo',
                },
                {
                    title: 'Cantidad de dias segun ley',
                    name: 'cantidad_dias_norma',
                    data: 'cantidad_dias_norma',
                },
                {
                    title: 'Frecuencia segun norma',
                    name: 'frecuencia_segun_norma',
                    data: 'periodo_norma',
                },
                {
                    title: 'Tomados',
                    name: 'dias_tomados',
                    data: 'dias_tomados',
                },
                {
                    title: 'Alerta',
                    name: 'alerta',
                    data: 'flag_alerta',
                    render: function (data, type, row) {
                    var alerta = '';
                        if (data==1) {
                            alerta = '<span class="label label-success">Correcta</span>'
                        } else if (data==0) {
                            alerta = '<span class="label label-danger">Incorrecta</span>';
                        }
                        return alerta;
                    }
                },
            ]
        });

        //filtros para el exportador
        $(".accion_exportador").click(function () {
        var form = $('<form/>', {id:'form_ln' , action : $(this).val(), method : 'POST'});
        $(this).append(form);
        form.append($('<input/>', {name: 'search', type: 'hidden', value: $('div.dataTables_filter input').val() }))
            .append($('<input/>', {name: 'campo_sort', type: 'hidden', value: $('#tabla').dataTable().fnSettings().aoColumns[$('#tabla').dataTable().fnSettings().aaSorting[0][0]].name }))
            .append($('<input/>', {name: 'dir', type: 'hidden', value: $('#tabla').dataTable().fnSettings().aaSorting[0][1] }))
            .append($('<input/>', {name: 'rows', type: 'hidden', value: $('#tabla').dataTable().fnSettings().fnRecordsDisplay() }))
         form.submit();
        });
    }
});
