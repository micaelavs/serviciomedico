$(document).ready(function () {


    let $collapseFiltros = $('#collapseFiltros');
    let $collapseFiltrosCaret = $("#collapseFiltros_caret");

    
    $collapseFiltros.on('hide.bs.collapse', function () {
        $collapseFiltrosCaret.removeClass('fa-caret-down').addClass('fa-caret-right');
        $('#filtros_resumen_empleado').hide();
        limpiar_campos_filtro();
    });

    $collapseFiltros.on('show.bs.collapse', function () {
        $collapseFiltrosCaret.removeClass('fa-caret-right').addClass('fa-caret-down')
        $('#filtros_resumen_empleado').show();
        limpiar_campos_filtro();
    });



    let today = new Date();
    let dd = today.getDate();
    let mm = today.getMonth() + 1;
    let yyyy = today.getFullYear();
    if (dd < 10) {
        dd = '0' + dd
    }
    if (mm < 10) {
        mm = '0' + mm
    }
    today = dd + '-' + mm + '-' + yyyy;


    let columnas = '';
    let colDefs = '';

    columnas = [
        { title: 'DNI',  name: 'dni',},
        { title: 'Cuit', name: 'cuit',},
        { title: 'Apellido y Nombre', name: 'apellido_nombre',},
        { title: 'Articulo', name: 'articulo',},
        { title: 'Cantidad de dias segun ley', name: 'cantidad_dias_ley',},
        { title: 'Frecuencia segun norma', name: 'frecuencia_segun_norma',},
        { title: 'Tomados', name: 'tomados',},
        { title: 'Periodo', name: 'periodo',},
        { title: 'Alerta', name: 'alerta',},

    ]

    colDefs = [
        { targets: 0, width: '10%', className: 'text-left' },
        { targets: 1, width: '10%', className: 'text-left' },
        { targets: 2, width: '20%', className: 'text-left' },
        { targets: 3, width: '10%', className: 'text-left' },
        { targets: 4, width: '15%', className: 'text-left' },
        { targets: 5, width: '15%', className: 'text-left' },
        { targets: 6, width: '10%', className: 'text-left' },
        { targets: 7, width: '10%', className: 'text-left' },
        { targets: 8, width: '10%', className: 'text-left' },
    ]


    let _tabla_resumen_empleado = $('#tabla_resumen_empleado').DataTable({
        "initComplete": function(settings, json) {
            $('#preloader_resumen_empleado').hide();
            $('#tabla_resumen_empleado').show();
        },
        buttons: [{
            extend: ['excelHtml5'],
            exportOptions: { "columns": ':not(.Acciones)' },
            className: 'btn-sm',
            text: 'Descargar Excel',
            title: 'reporte_resumen_empleado' + '_' + today,
            customizeData: function (data) {
                for (let i = 0; i < data.body.length; i++) {
                    for (let j = 0; j < data.body[i].length; j++) {
                        data.body[i][j] = '\u200C' + data.body[i][j];
                    }
                }
            }
        }],
        language: {
            url: $endpoint_cdn + '/datatables/1.10.12/Spanish_sym.json',
            searchPlaceholder: "Buscar Registro"
        },
        columns: columnas,
        columnDefs:  colDefs,
        bFilter: true,
        order: [[1, 'desc']],
    });

    _tabla_resumen_empleado.table().on('init', function () {
        _tabla_resumen_empleado.buttons().container()
            .css('padding-right', '10px')
            .css('padding-top', '8px')
            .prependTo($('.dataTables_filter', _tabla_resumen_empleado.table().container()));
    });

    $.fn.dataTable.moment('DD/MM/YYYY');


    limpiar_datatable_reporte_nota_gde();
    get_resumen_consultas_medicas_empleados();



    function limpiar_datatable_reporte_nota_gde() {
        if (typeof _tabla_resumen_empleado !== 'undefined') {
            _tabla_resumen_empleado.clear().draw();
        }
    }


    function set_row_tabla_resumen_empleado(values){
        let dni=values.dni ? values.dni:'';
        let cuit=values.cuit ? values.cuit:'';
        let nombre_apellido=values.nombre_apellido ? values.nombre_apellido:'';
        let articulo=values.nombre_articulos ? values.nombre_articulos : '';
        let cantidad_dias_segun_ley=values.cantidad_dias_norma ? values.cantidad_dias_norma :'';
        let periodo_norma=(values.periodo_norma == 4) ? 'Anual' : (values.periodo_norma==3) ? 'Semestral' : (values.periodo_norma==5) ? 'Bianual':'';
        let dias_tomados=values.dias_tomados ? values.dias_tomados :'';
        let periodo=values.periodo ? values.periodo :'';
        let alerta='';

                if(Number(dias_tomados) <= Number(cantidad_dias_segun_ley)){
                    alerta='<span class="label label-success">Correcta</span>'
                }else{
                    alerta='<span class="label label-danger">Incorrecta</span>';
                }

            let rowNode = _tabla_resumen_empleado.row.add([
                dni,
                cuit,
                nombre_apellido,
                articulo,
                cantidad_dias_segun_ley,
                periodo_norma,
                dias_tomados,
                periodo,
                alerta
            ]).draw().node();


    }

    function  get_resumen_consultas_medicas_empleados(){

        $.ajax({
            url: base_url + '/index.php/consultasmedicas/get_resumen_consultas_medicas_empleados',
            data: {
            },
            method: "GET",
            async:false,
        }).success(function (data) {
                
            if(data.status !=204){

                $.each(data.resumen_empleados, function (key, value) {
                    set_row_tabla_resumen_empleado(value);
                });

            }
        });
    }



    $('#filtrar_resumen_empleado').on('click',function (){
        let dni=$('#dni').val();
        let apellido_nombre=$('#apellido_nombre').val();


        if(dni!='' || apellido_nombre !='' ){
            $('#mensaje_filtro').hide();
            $.ajax({
                url: base_url + "/index.php/consultasmedicas/busqueda_avanzada_consultasmedicas",
                type: "POST",
                data:{
                    dni:dni,
                    apellido_nombre:apellido_nombre,
                },
                dataType:"json",
                beforeSend: function(){
                    $('#preloader_resumen_empleado').show();
                },
                success: function(data){
                    
                    $('#preloader_resumen_empleado').hide();
                    if(data.status !=204){
                        limpiar_datatable_reporte_nota_gde();
                        $.each(data.resumen_empleados, function (i,values) {
                            set_row_tabla_resumen_empleado(values);
                        });
                    }else{
                        limpiar_datatable_reporte_nota_gde();
                    }
                },
                error: function(e){
                },
            });
        }else{

            limpiar_datatable_reporte_nota_gde();
            get_resumen_consultas_medicas_empleados();
            $('#mensaje_filtro').show();

        }

    });


    function limpiar_campos_filtro(){
        $('#dni').val('');
        $('#apellido_nombre').change();
        $('#mensaje_filtro').hide();
    }



});