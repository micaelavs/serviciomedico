<?php
	use \App\Modelo\AppRoles;

    /** @var  $vista */

	$menu		= new \FMT\Menu();
	$config		= FMT\Configuracion::instancia();
	if($config['app']['dev']) {
		$menu->activar_dev();
	}

/* 	$opcion1	= $menu->agregar_opcion('EJEMPLO');
	if (AppRoles::puede('Ejemplos', 'index')) {
		$opcion1->agregar_titulo('Titulo Ejemplo', \FMT\Opcion::COLUMNA1);
		$opcion1->agregar_link('Ejemplo', \App\Helper\Vista::get_url('index.php') . '/ejemplos/index', \FMT\Opcion::COLUMNA1);
	} else {
		$opcion1->agregar_titulo('Titulo Ejemplo Sin Permiso', \FMT\Opcion::COLUMNA1);
		$opcion1->agregar_link('Ejemplo Sin Permiso', \App\Helper\Vista::get_url('index.php').'/ejemplos/index', \FMT\Opcion::COLUMNA1);
	} */

	$opcion2	= $menu->agregar_opcion('Gestion');

	if (AppRoles::puede('Usuarios', 'index')) {
		$opcion2->agregar_titulo('Administración del sistema', \FMT\Opcion::COLUMNA1);
		$opcion2->agregar_link('Usuarios', \App\Helper\Vista::get_url('index.php/usuarios/index'), \FMT\Opcion::COLUMNA1);
	}

	if (AppRoles::puede('Estados', 'index')) {
		$opcion2->agregar_titulo('Estados', \FMT\Opcion::COLUMNA1);
		$opcion2->agregar_link('Estados', \App\Helper\Vista::get_url('index.php/estados/index'), \FMT\Opcion::COLUMNA1);
	}

	if (AppRoles::puede('Medicos', 'index')) {
		$opcion2->agregar_titulo('Médicos', \FMT\Opcion::COLUMNA1);
		$opcion2->agregar_link('Médicos', \App\Helper\Vista::get_url('index.php/medicos/index'), \FMT\Opcion::COLUMNA1);
        $opcion2->agregar_link('Resumen Invervenciones Empleados', \App\Helper\Vista::get_url('index.php/consultasmedicas/resumenempleado'), \FMT\Opcion::COLUMNA1);

	}

	if(AppRoles::puede('Enfermeras','index')) {
		$opcion2->agregar_titulo('Enfermeras', \FMT\Opcion::COLUMNA1);
		$opcion2->agregar_link('Enfermeras', \App\Helper\Vista::get_url('index.php/enfermeras/index'), \FMT\Opcion::COLUMNA1);
	}

	if(AppRoles::puede('Articulos','index')) {
		$opcion2->agregar_titulo('Artículos', \FMT\Opcion::COLUMNA1);
		$opcion2->agregar_link('Artículos', \App\Helper\Vista::get_url('index.php/articulos/index'), \FMT\Opcion::COLUMNA1);
	}
	if(AppRoles::puede('Consultasmedicas','index')) {
		$opcion2->agregar_titulo('Consultas Médicas', \FMT\Opcion::COLUMNA1);
		$opcion2->agregar_link('Consultas Médicas', \App\Helper\Vista::get_url('index.php/consultasmedicas/index'), \FMT\Opcion::COLUMNA1);
	}

	if(AppRoles::puede('Consultasmedicas','log_cambios')) {
		$opcion2->agregar_titulo('Historial de cambios', \FMT\Opcion::COLUMNA1);
		$opcion2->agregar_link('Historial de Cambios', \App\Helper\Vista::get_url('index.php/consultasmedicas/log_cambios'), \FMT\Opcion::COLUMNA1);
	}

	//----------------------------------------/
	//----------------------------------------/

	if(AppRoles::puede('Manuales','index')) {
		$menu->agregar_manual(\App\Helper\Vista::get_url('index.php/Manuales/index'));
	}
	$menu->agregar_salir($config['app']['endpoint_panel'].'/logout.php');
	$vars['CABECERA'] = "{$menu}";
	$vista->add_to_var('vars', $vars);
	return true;
