$(document).ready(function () {
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1;
    var yyyy = today.getFullYear();
    if (dd < 10) {
        dd = '0' + dd
    }
    if (mm < 10) {
        mm = '0' + mm
    }
    today = dd + '-' + mm + '-' + yyyy;
    var titulo;
    titulo = $('#nombreArchivoExport').text();
    titulo = titulo + '_' + today;

    var table = $('#tabla').DataTable({
        "initComplete": function(settings, json) {
            $('#preloader').hide();
            $('#tabla').show();
        },
        responsive: {
            details: {
                renderer: function (api, rowIdx, columns) {
                    var data = $.map(columns, function (col, i) {
                        body = "";
                        data = "-";
                        if (col.hidden) {
                            if (col.data != "") { data = col.data; }
                            body = '<div class="col-md-3" style="padding-bottom: 15px;"><b>' + col.title + ':' + '</b></div>' +
                                '<div class="col-md-2">' + data + '</div>';
                        }
                        return body;
                    }).join('');

                    return data ?
                        $('<table/>').append(data) :
                        false;
                }
            }
        },
        columnDefs: [
            { targets: 0, width: '20%', className: 'text-left' },
            { targets: 1, width: '20%', className: 'text-left' },
            { targets: 2, width: '20%', className: 'text-left' },
            { targets: 3,  width: '20%', orderable: false, 'searchable': false  },
        ],

        language: {
            url: $endpoint_cdn + '/datatables/1.10.12/Spanish_sym.json',
            searchPlaceholder: "Buscar Registro"
        },
        buttons: [{
            extend: ['excelHtml5'],
            exportOptions: { "columns": ':not(.Acciones)' },
            className: 'btn-sm',
            text: 'Descargar Excel',
            title: titulo,
            customizeData: function (data) {
                for (var i = 0; i < data.body.length; i++) {
                    for (var j = 0; j < data.body[i].length; j++) {
                        data.body[i][j] = '\u200C' + data.body[i][j];
                    }
                }
            }
        }],
    });

    table.table().on('init', function () {
        table.buttons().container().css('padding-right', '10px').prependTo($('.dataTables_filter', table.table().container()));
    });
});