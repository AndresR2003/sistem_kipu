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
                    ['key' => 'configuracion', 'label' => 'Configuración',            'icon' => 'bi-gear-fill',          'url' => 'configuracion'],
                    ['key' => 'colaboradores', 'label' => 'Colaboradores / Personal', 'icon' => 'bi-person-badge-fill',  'url' => 'colaboradores'],
                    ['key' => 'soporte',       'label' => 'Soporte',                  'icon' => 'bi-question-circle-fill', 'url' => 'soporte'],
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

if (!function_exists('menu_oculto')) {
    /**
     * Claves de las secciones deshabilitadas para los usuarios que no son superadmin.
     */
    function menu_oculto(): array
    {
        try {
            $cfg = model('App\Models\ConfiguracionVisualModel')->Obtener();
        } catch (\Throwable $e) {
            return [];
        }

        $raw = $cfg['menu_disabled'] ?? '[]';
        $lista = is_array($raw) ? $raw : json_decode((string) $raw, true);
        if (!is_array($lista)) {
            return [];
        }

        return array_values(array_intersect($lista, menu_keys()));
    }
}
