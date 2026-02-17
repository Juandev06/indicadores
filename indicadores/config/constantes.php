<?php
return [
    // menu
    'menu' => [
        [
            'tipo' => 'menuitem',
            'nombre' => 'Dashboard',
            'permiso' => 'dashboard',
            'url' => 'dashboard',
            'ico' => 'ico-dashboard',
        ],
        [
            'tipo' => 'menuitem',
            'nombre' => 'Análisis indicadores',
            'permiso' => 'indicador_analisis',
            'url' => 'analysis',
            'ico' => 'ico-chart-line',
        ],
        [
            'tipo' => 'submenu',
            'nombre' => 'Variables',
            'permiso' => 'variables',
            'url' => '',
            'ico' => 'ico-flow',
            'items' => [
                [
                    'tipo' => 'menuitem',
                    'nombre' => 'Ingresar',
                    'permiso' => 'variable_ingresar',
                    'url' => 'setvariable',
                    'ico' => '',
                ],
                [
                    'tipo' => 'menuitem',
                    'nombre' => 'Habilitar',
                    'permiso' => 'variable_habilitar',
                    'url' => 'enablevariable',
                    'ico' => '',
                ],
            ]
        ],
        [
            'tipo' => 'menuitem',
            'nombre' => 'Metas',
            'permiso' => 'metas',
            'url' => 'goals',
            'ico' => 'ico-flag-goal',
        ],
        [
            'tipo' => 'submenu',
            'nombre' => 'Configuración',
            'permiso' => 'config',
            'url' => '',
            'ico' => 'ico-cog',
            'items' => [
                [
                    'tipo' => 'menuitem',
                    'nombre' => 'Variables',
                    'permiso' => 'config_variables',
                    'url' => 'variables',
                    'ico' => '',
                ],
                [
                    'tipo' => 'menuitem',
                    'nombre' => 'Indicadores',
                    'permiso' => 'config_indicadores',
                    'url' => 'indicators',
                    'ico' => '',
                ],
                [
                    'tipo' => 'menuitem',
                    'nombre' => 'Áreas',
                    'permiso' => 'areas',
                    'url' => 'areas',
                    'ico' => '',
                ],
                [
                    'tipo' => 'menuitem',
                    'nombre' => 'Categorías',
                    'permiso' => 'categorias',
                    'url' => 'categories',
                    'ico' => '',
                ],
                [
                    'tipo' => 'menuitem',
                    'nombre' => 'Subcategorías',
                    'permiso' => 'subcategorias',
                    'url' => 'subcategories',
                    'ico' => '',
                ],
            ]
        ],
        [
            'tipo' => 'submenu',
            'nombre' => 'Seguridad',
            'permiso' => 'seguridad',
            'url' => '',
            'ico' => 'ico-shield',
            'items' => [
                [
                    'tipo' => 'menuitem',
                    'nombre' => 'Usuarios',
                    'permiso' => 'usuarios',
                    'url' => 'users',
                    'ico' => '',
                ],
                [
                    'tipo' => 'menuitem',
                    'nombre' => 'Roles',
                    'permiso' => 'roles',
                    'url' => 'profiles',
                    'ico' => '',
                ],
                [
                    'tipo' => 'menuitem',
                    'nombre' => 'Permisos',
                    'permiso' => 'permisos',
                    'url' => 'permissions',
                    'ico' => '',
                ],
            ]
        ],
    ],
    // meses: id, nombre
    'meses' => [
        1 =>  'enero',
        2 =>  'febrero',
        3 =>  'marzo',
        4 =>  'abril',
        5 =>  'mayo',
        6 =>  'junio',
        7 =>  'julio',
        8 =>  'agosto',
        9 =>  'septiembre',
        10 => 'octubre',
        11 => 'noviembre',
        12 => 'diciembre',
    ],
    // tipos de graficos para dashboard
    'chartTypes' => [
        'line' => 'Linea',
        'bar' => 'Barras',
        'pie' => 'Torta',
        'doughnut' => 'Dona',
    ],
    // paleta de colores para graficos
    'chartColors' => [
        '#4fc1e9',
        '#a0d468',
        '#e8ce4d',
        '#ec87c0',
        '#fc6e51',
        '#8067b7',
        '#656d78',
        '#48cfad',
        '#C98B70',
        '#B83B5E',
        '#07749b',
        '#5caf03',
        '#927c0b',
        '#ac0b66',
        '#db310f',
        '#5426b6',
        '#3d4653',
        '#f83737',
        '#035468',
        '#48802e',
    ],
];
