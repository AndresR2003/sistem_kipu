<?php

if (!function_exists('menu_secciones')) {
    /**
     * Definicion de las secciones del sidebar agrupadas.
     * Cada item: key, label, icon, url y opcionalmente fijo (no configurable).
     */
    function menu_secciones(): array
    {
        return [
            [
                'titulo' => 'Menu principal',
                'items'  => [
                    ['key' => 'dashboard',    'label' => 'Inicio',        'icon' => 'bi-house-fill',       'url' => 'dashboard', 'fijo' => true],
                    ['key' => 'recordatorio', 'label' => 'Recordatorio',  'icon' => 'bi-bell-fill',        'url' => 'recordatorio'],
                    ['key' => 'marcadores',   'label' => 'Marcadores',    'icon' => 'bi-bookmark-fill',    'url' => 'marcadores'],
                    ['key' => 'borradores',   'label' => 'Borradores',    'icon' => 'bi-pencil-fill',      'url' => 'borradores'],
                ],
            ],
            [
                'titulo' => 'Herramientas',
                'items'  => [
                    ['key' => 'entregas',     'label' => 'Pases de turno',          'icon' => 'bi-arrow-left-right',  'url' => 'entregas'],
                    ['key' => 'noticias',     'label' => 'Noticias',                'icon' => 'bi-newspaper',         'url' => 'noticias'],
                    ['key' => 'ideas',        'label' => 'Ideas',                   'icon' => 'bi-lightbulb-fill',    'url' => 'ideas'],
                    ['key' => 'manual',       'label' => 'Manual',                  'icon' => 'bi-book-fill',         'url' => 'manual'],
                    ['key' => 'tareas',       'label' => 'Tareas',                  'icon' => 'bi-check2-square',     'url' => 'tareas'],
                    ['key' => 'calendario',   'label' => 'Calendario',              'icon' => 'bi-calendar-fill',     'url' => 'calendario'],
                    ['key' => 'reparaciones', 'label' => 'Reparaciones',            'icon' => 'bi-tools',             'url' => 'reparaciones'],
                    ['key' => 'peticiones',   'label' => 'Peticiones de huéspedes', 'icon' => 'bi-chat-dots-fill',    'url' => 'peticiones'],
                ],
            ],
            [
                'titulo' => 'Más',
                'items'  => [
                    ['key' => 'configuracion', 'label' => 'Configuración',            'icon' => 'bi-gear-fill',             'url' => 'configuracion'],
                    ['key' => 'colaboradores', 'label' => 'Colaboradores / Personal', 'icon' => 'bi-person-badge-fill',     'url' => 'colaboradores'],
                    ['key' => 'soporte',       'label' => 'Soporte',                  'icon' => 'bi-question-circle-fill',  'url' => 'soporte'],
                ],
            ],
        ];
    }
}

if (!function_exists('menu_keys')) {
    /**
     * Claves de las secciones configurables (excluye las fijas).
     */
    function menu_keys(): array
    {
        $keys = [];
        foreach (menu_secciones() as $grupo) {
            foreach ($grupo['items'] as $item) {
                if (empty($item['fijo'])) {
                    $keys[] = $item['key'];
                }
            }
        }

        return $keys;
    }
}

if (!function_exists('menu_roles')) {
    /**
     * Roles que pueden ocultarse desde la configuracion del menu.
     * superadmin nunca se oculta, por eso no aparece.
     *
     * @return array<string, string> codigo => etiqueta
     */
    function menu_roles(): array
    {
        return [
            'admin'    => 'Administrador',
            'empleado' => 'Empleado',
            'soporte'  => 'Soporte',
            'vendedor' => 'Vendedor',
            'tecnico'  => 'Tecnico',
        ];
    }
}

if (!function_exists('menu_config')) {
    /**
     * Configuracion de visibilidad del menu normalizada.
     *
     * @return array<string, array{roles: list<string>, usuarios: list<int>}>
     */
    function menu_config(): array
    {
        try {
            $cfg = model('App\Models\ConfiguracionVisualModel')->Obtener();
        } catch (\Throwable $e) {
            return [];
        }

        $raw  = $cfg['menu_disabled'] ?? '[]';
        $data = is_array($raw) ? $raw : json_decode((string) $raw, true);
        if (!is_array($data)) {
            return [];
        }

        $rolesValidos = array_keys(menu_roles());
        $out = [];

        // Formato antiguo: lista plana de claves -> ocultas para todos los no superadmin
        if (array_is_list($data)) {
            foreach ($data as $key) {
                if (in_array($key, menu_keys(), true)) {
                    $out[$key] = ['roles' => $rolesValidos, 'usuarios' => []];
                }
            }
            return $out;
        }

        foreach (menu_keys() as $key) {
            if (!isset($data[$key]) || !is_array($data[$key])) {
                continue;
            }
            $roles = array_values(array_intersect((array) ($data[$key]['roles'] ?? []), $rolesValidos));
            $usuarios = array_values(array_unique(array_filter(
                array_map('intval', (array) ($data[$key]['usuarios'] ?? [])),
                static fn ($id) => $id > 0
            )));

            if ($roles || $usuarios) {
                $out[$key] = ['roles' => $roles, 'usuarios' => $usuarios];
            }
        }

        return $out;
    }
}

if (!function_exists('menu_oculto_para')) {
    /**
     * Claves del menu ocultas para un rol / usuario concreto.
     * El superadmin siempre ve todo.
     */
    function menu_oculto_para(?string $rol = null, ?int $usuarioId = null): array
    {
        if ($rol === null) {
            $rol = session('admin_rol') ?? '';
        }
        if ($rol === 'superadmin') {
            return [];
        }
        if ($usuarioId === null) {
            $usuarioId = (int) (session('admin_id') ?? 0);
        }

        $ocultos = [];
        foreach (menu_config() as $key => $conf) {
            if (in_array($rol, $conf['roles'], true) || in_array($usuarioId, $conf['usuarios'], true)) {
                $ocultos[] = $key;
            }
        }

        return $ocultos;
    }
}
