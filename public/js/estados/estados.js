$(document).ready(function () {
    var table = $('#tabla').DataTable({
        "initComplete": function (settings, json) {
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
            { targets: 0, width: '30%', className: 'text-left' },
            { targets: 1, width: '30%', className: 'text-left' },
           // { targets: 2, width: '20%', className: 'text-left' },
            { targets: 2, width: '30%', orderable: false, 'searchable': false },
        ],

        language: {
            url: $endpoint_cdn + '/datatables/1.10.12/Spanish_sym.json',
            searchPlaceholder: "Buscar Registro"
        },
    });
});