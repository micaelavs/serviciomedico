<?php

//datos para enviar por smtp
return [
    'email'=>[
        'debug'            => false,
        'insecure'        => false,
        'host'            => '',
        'port'            => '',
        'user'            => '',
        'pass'            => '',
        'from'          => 'notificacionesis@transporte.gob.ar',
        'name'          => 'Notificaciones Servicio Médico',
        'SMTPAutoTLS'    => true,
        'SMTPAuth'        => true ,
        'app_mailer'    => 'https://qa-mailer.dev.transporte.gob.ar/endpoint.php',
        'email_rrhh'                => 'example@transporte.gob.ar' //'asistencias@transporte.gob.ar'
    ]

];