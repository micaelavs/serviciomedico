<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
require_once __DIR__ . "/constantes.php";
require_once BASE_PATH . '/vendor/autoload.php';

define('APP_VERSION','1.0.0');

$config	= FMT\Configuracion::instancia();
$config->cargar(BASE_PATH . '/config');

if(!defined('PHP_INTERPRETE')){
    define('PHP_INTERPRETE', \FMT\Helper\Arr::get($config['app'], 'php_interprete', 'php74'));
}

\FMT\Logger::init(empty($_SESSION['iu']) ? '1' : $_SESSION['iu'], $config['logs']['modulo'], $config['logs']['end_point_event'], $config['logs']['end_point_debug'], $config['logs']['debug']);

\FMT\Usuarios::init($config['app']['modulo'], $config['app']['endpoint_panel'].'/api.php', ['CURLOPT_SSL_VERIFYPEER' => \FMT\Helper\Arr::get($config['app'], 'ssl_verifypeer', true)]);

FMT\Mailer::init($config['email']['app_mailer'], $config['app']['ssl_verifypeer']);

\FMT\Ubicaciones::init($config['app']['endpoint_ubicaciones'], ['CURLOPT_SSL_VERIFYPEER' => \FMT\Helper\Arr::get($config['app'], 'ssl_verifypeer', true)]);

\App\Modelo\SigarhuApi::init($config['app']['endpoint_sigarhu'],['CURLOPT_SSL_VERIFYPEER' => $config['app']['ssl_verifypeer']]);

\App\Modelo\SigarhuApi::setToken($config['app']['modulo'], \FMT\Helper\Arr::get($config['app'],'sigarhu_access_token'));

\App\Modelo\ControlAccesosApi::init($config['app']['endpoint_control_accesos'],['CURLOPT_SSL_VERIFYPEER' => $config['app']['ssl_verifypeer']]);

\App\Modelo\ControlAccesosApi::setToken($config['app']['modulo'], \FMT\Helper\Arr::get($config['app'],'control_accesos_access_token'));

\FMT\Informacion_fecha::init($config['app']['endpoint_informacion_fecha'], ['CURLOPT_SSL_VERIFYPEER' => \FMT\Helper\Arr::get($config['app'], 'ssl_verifypeer', true)]);

