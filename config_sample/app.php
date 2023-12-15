<?php
return [
    'app' => [
        'dev'                        	=> true, // Estado del desarrollo
        'modulo'                    	=> 0, // Numero del modulo
        'title'                       	=> 'Servicio Medico - Ministerio de Transporte', // Nombre del Modulo,
        'titulo_pantalla'            	=> 'Servicio Médico',
        'endpoint_panel'            	=> 'https://panel-testing.transporte.gob.ar',
        'endpoint_cdn'                	=> 'https://cdn-testing.transporte.gob.ar',
        'ssl_verifypeer'            	=> true,
        'endpoint_ubicaciones'        	=> 'https://ubicaciones-testing.transporte.gob.ar/index.php/',
        'endpoint_sigarhu'            	=> 'https://sigarhu-testing.transporte.gob.ar/api.php',
        'sigarhu_access_token'        	=> 'XXX',
        'id_usuario_sistema'        	=> '999999999', //En caso de operaciones automaticas, se establece un id de usuario que identifique al sistema
        'php_interprete'            	=> '/usr/bin/php74',
        'endpoint_informacion_fecha'	=> 'https://fecha-testing.transporte.gob.ar/index.php/consulta/',
        'endpoint_control_accesos' => 'https://controlaccesos-testing.transporte.gob.ar/api.php',
        'control_accesos_access_token' => 'xxxxx',
        'routers_public'             =>['consultasmedicas'=>['qrCode'] ]//'controlador' => [acciones] ]
    ]
];