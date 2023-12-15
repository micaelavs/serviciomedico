<?php
namespace App\Modelo;
use FMT\Roles;
use FMT\Usuarios;

class AppRoles extends Roles {
    const PADRE_BASE			= 0;
    const ROL_ADMINISTRACION    = 1;
    const ROL_MEDICO            = 2;
    const ROL_ENFERMERA         = 3;
    const ROL_RRHH              = 4;



    static $rol;
    static $permisos	= [
        self::PADRE_BASE => [
            'nombre'	=> 'Padre',
            'inicio'	=> ['control' => 'error','accion' => 'index'],
            'atributos' => [
                'campos' => [],
            ],
            'permisos'	=> [
                'Error' => [
                    'index'				=> true
                ],
                'Ejemplos' => [
                    'index'			=> true,
                ],
                  'Consultasmedicas'=> [
                        'qrCode' => true
                ]        
            ]
        ],

        self::ROL_ADMINISTRACION => [
            'nombre'	=> 'Administrador del sistema',
            'padre'		=> self::PADRE_BASE,
            'inicio'	=> ['control' => 'Usuarios','accion' => 'index'],
            'roles_permitidos' => [
                self::ROL_ADMINISTRACION,
                self::ROL_ENFERMERA,
                self::ROL_MEDICO,
                self::ROL_RRHH
            ],
            'atributos' => [
                'campos' => [],
            ],
            'permisos'	=> [
                'Usuarios'=> [
                    'index'        => true,
                    'alta'         => true,
                    'modificar'    => true,
                    'baja'         => true,
                ],
                'Estados' => [
                    'index'        => true,
                    'alta'         => true,
                    'modificacion' => true,
                    'baja'         => true,
                    'activar'      => true,
                ],
                'Enfermeras'=> [
                    'index'         =>true,
                    'alta'          =>true,
                    'modificacion'  =>true,
                    'baja'          =>true,
                    'reactivar'=>true,
                ],
                'Articulos'=> [
                    'index'         =>true,
                    'alta'          =>true,
                    'modificacion'  =>true,
                    'baja'          =>true,
                    'activar'       =>true
                ],
                'Medicos' => [
                    'index'           => true,
                    'alta'            => true,
                    'modificacion'    => true,
                    'baja'            => true,
                    'reactivar'       => true
                ],
                'Consultasmedicas'=>[
                    'resumenempleado'=>true,
                    'get_resumen_consultas_medicas_empleados'=>true,
                    'busqueda_avanzada_consultasmedicas'=>true,
                    'ajax_log_cambios' => true,
                    'log_cambios' => true,
                    'log_cambios_exportar_excel' => true
                ]
            ]
        ],
            self::ROL_MEDICO => [
                'nombre'    => 'Medico',
                'padre'        => self::PADRE_BASE,
                'inicio'    => ['control' => 'Consultasmedicas', 'accion' => 'index'],
                'roles_permitidos' => [
                    self::ROL_MEDICO,
                ],
                'atributos' => [
                    'campos' => [],
                ],
                'permisos' => [
                    'Consultasmedicas'=> [
                        'index'        => true,
                        'alta'         => true,
                        'modificacion' => true,
                        'baja'         => true,
                        'buscarAgente' => true,
                        'ajax_consultas_medicas' => true,
                        'exportar_excel' => true,
                        'persona_alta'   => true,
                        'persona_actualizar' => true,
                        'actualizarApto'     => true,
                        'traer_tipo_apto_segun_apto'  => true,
                        'actualizarTipoApto' => true,
                        'actualizarGrupo' => true,
                        'ver_historial' => true,
                        'ajax_archivos_adjuntos' => true,
                        'ver_adjunto' => true,
                        'baja_adjunto'=> true,
                        'historia_clinica' => true,
                        'ajax_historia_clinica' => true,
                        'exportar_historia_clinica_excel' => true,   
                        'aptoMedico'   => true,
                        'comprobanteMedico' => true,   
                        'resumen_eventos'=>true,
                        'ajax_resumen_eventos'=>true,
                        'exportar_excel_resumen_eventos'=>true,
                        'qrCode' => true,
                        'enviarComprobante' => true
                    ],
                ]
            ],
            self::ROL_ENFERMERA => [
                'nombre'    => 'Enfermera',
                'padre'        => self::PADRE_BASE,
                'inicio'    => ['control' => 'Consultasmedicas', 'accion' => 'index'],
                'roles_permitidos' => [
                    self::ROL_ENFERMERA,
                ],
                'atributos' => [
                    'campos' => [],
                ],
                'permisos' => [
                    'Consultasmedicas'=> [
                        'index'        => true,
                        'alta'         => true,
                        'modificacion' => true,
                        'baja'         => true,
                        'buscarAgente' => true,
                        'ajax_consultas_medicas' => true,
                        'exportar_excel' => true,
                        'persona_alta'   => true,
                        'persona_actualizar' => true,
                        'actualizarApto'     => true,
                        'traer_tipo_apto_segun_apto' => true,
                        'actualizarTipoApto' => true,
                        'actualizarGrupo' => true, 
                        'ver_historial' => true,
                        'ajax_archivos_adjuntos' => true,
                        'ver_adjunto' => true,
                        'baja_adjunto'=> true,
                        'historia_clinica' => true,
                        'ajax_historia_clinica' => true,
                        'exportar_historia_clinica_excel' => true,    
                        'aptoMedico'   => true,
                        'comprobanteMedico' => true,   
                        'resumen_eventos' => true,
                        'ajax_resumen_eventos' => true,
                        'exportar_excel_resumen_eventos' => true,
                        'qrCode' => true,
                        'enviarComprobante' => true
                    ],
                ]
            ],

        self::ROL_RRHH => [
            'nombre'	=> 'RRHH',
            'padre'		=> self::PADRE_BASE,
            'inicio'	=> ['control' => 'Consultasmedicas','accion' => 'index'],
            'roles_permitidos' => [
                self::ROL_RRHH,
            ],
            'atributos' => [
                'campos' => [],
            ],
            'permisos' => [
                    'Consultasmedicas'=> [
                        'index'        => true,
                        'alta'         => true,
                        'modificacion' => true,
                        'baja'         => true,
                        'buscarAgente' => true,
                        'ajax_consultas_medicas' => true,
                        'exportar_excel' => true,
                        'persona_alta'   => true,
                        'persona_actualizar' => true,
                        'actualizarApto' => true,
                        'traer_tipo_apto_segun_apto'  => true,
                        'actualizarTipoApto' => true,
                        'actualizarGrupo' => true,
                        'resumenempleado'=>true,
                        'get_resumen_consultas_medicas_empleados'=>true,
                        'busqueda_avanzada_consultasmedicas'=>true,
                        'resumen_eventos' => true,
                        'ajax_resumen_eventos' => true,
                        'exportar_excel_resumen_eventos' => true,
                        'ver_historial' => true,
                        'ajax_archivos_adjuntos' => true,
                    ],    
            ]
        ],
    ];

	public static function sin_permisos($accion){
		$vista = include (VISTAS_PATH.'/widgets/acceso_denegado_accion.php');
		return $vista;
	}

    public static function obtener_rol() {
    	return static::$rol;
    }

	public static function obtener_inicio() {
    	static::$rol= Usuarios::$usuarioLogueado['permiso'];
		static::$rol= (is_null(static::$rol))? self::PADRE_BASE : static::$rol ;
    	$inicio		= static::$permisos[static::$rol]['inicio'];
    	return $inicio;
    }

    public static function obtener_nombre_rol() {
    	$nombre	= static::$permisos[static::$rol]['nombre'];
    	return $nombre;
    }

 	public static function obtener_manual() {
    	$manual	= static::$permisos[static::$rol]['manual'];
    	return $manual;
    }

 	public static function obtener_atributos_visibles() {
		$atributo_visible	= static::$permisos[static::$rol]['atributos_visibles'];
		return $atributo_visible;
    }

    public static function obtener_atributos_select() {
		$atributos_select	= static::$permisos[static::$rol]['atributos_select'];
		return $atributos_select;
    }


/**
 * @param string $cont			- Controlador
 * @param string $accion		- Accion que se aplica sobre el atributo
 * @param string $atributo		- Tipo de atributo
 * @param string $id_atributo	- Indice del atributo
*/
    public static function puede_atributo($cont, $accion, $atributo, $id_atributo) {
		$flag = true;
		$rol = static::$rol;
	    while ($flag) {
		    if (isset(static::$permisos[$rol]['atributos'][$atributo][$id_atributo])) {
		        if(isset(static::$permisos[$rol]['atributos'][$atributo][$id_atributo][$cont][$accion])) {
		            $puede = static::$permisos[$rol]['atributos'][$atributo][$id_atributo][$cont][$accion];
		            $flag = false;
		        }
		    }

		    if ($flag && isset(static::$permisos[$rol]['padre'])) {
                $rol = static::$permisos[$rol]['padre'];
            } else {
                $flag = false;
            }
        }
	    if (!isset($puede)) {
	        $puede = static::puede($cont, $accion);
	    }
	    return $puede;
	}

    public static function puede($cont, $accion) {
		$rol	=  Usuarios::$usuarioLogueado['permiso'];
		if($rol) {
			$puede	= parent::puede($cont, $accion);
		} else {
			$rol	= self::PADRE_BASE;
			$puede	= false;
            if (isset(static::$permisos[$rol]['permisos'][$cont][$accion])) {
                $puede	= static::$permisos[$rol]['permisos'][$cont][$accion];
			}
		}
		return $puede;
	}

/**
 * Se usa para consultar si un usuario logueado tiene permisos sobre el rol de otro.
 *
 * @param int $rol_externo El rol de un usuario distinto al logueado
 * @return boolean
*/
	public static function tiene_permiso_sobre($rol_externo=null){
		return in_array($rol_externo, (array)static::$permisos[static::$rol]['roles_permitidos']);
	}

	public static function obtener_listado() {
		$roles_permitidos	= static::$permisos[static::$rol]['roles_permitidos'];
		$permisos			= static::$permisos;
		foreach ($permisos as $key => $permiso) {
			if(!in_array( $key, $roles_permitidos )){
				unset($permisos[$key]);
			}
		}

		return $permisos;
	}

    public static function obtener_motivos_cambio_estado_noeditables() {
		$flag = true;
		$rol = static::$rol;
		$atributos = [];
	    while ($flag) {
	    	if (isset(static::$permisos[$rol]['motivos_cambio_estado_noeditables'])) {
				$atributos	= static::$permisos[$rol]['motivos_cambio_estado_noeditables'];
				$flag = false;
			}
		    if ($flag && isset(static::$permisos[$rol]['padre'])) {
                $rol = static::$permisos[$rol]['padre'];
            } else {
                $flag = false;
            }
		}
        return $atributos;
	}

    public static function obtener_pedidos_especificos_noeditables() {
		$flag = true;
		$rol = static::$rol;
		$atributos = [];
	    while ($flag) {
	    	if (isset(static::$permisos[$rol]['pedidos_especificos_noeditables'])) {
				$atributos	= static::$permisos[$rol]['pedidos_especificos_noeditables'];
				$flag = false;
			}
		    if ($flag && isset(static::$permisos[$rol]['padre'])) {
                $rol = static::$permisos[$rol]['padre'];
            } else {
                $flag = false;
            }
		}
        return $atributos;
	}

    public static function obtener_lista_roles_permitidos() {
		$flag = true;
		$rol = static::$rol;
		$roles = [];
	   	if (isset(static::$permisos[$rol]['roles_permitidos'])) {
			foreach( static::$permisos[$rol]['roles_permitidos'] as $id) {
				$roles[$id] = static::$permisos[$id]['nombre'];
			}
		}
        return $roles;
	}

}
