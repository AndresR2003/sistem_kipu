<?php

if (!function_exists('fecha_es')) {
    /**
     * Formatea una fecha/hora a español.
     * Ejemplos:
     *   fecha_es('now', 'largo')  => "Martes, 11 de agosto de 2026"
     *   fecha_es($ts, 'abreviado') => "11 ago 2026"
     *   fecha_es($ts, 'corto')    => "11/08/2026 14:30"
     */
    function fecha_es(string $fecha = 'now', string $formato = 'largo'): string
    {
        if (!class_exists('IntlDateFormatter')) {
            $ts = strtotime($fecha);
            $trad = [
                'January' => 'enero', 'February' => 'febrero', 'March' => 'marzo',
                'April' => 'abril', 'May' => 'mayo', 'June' => 'junio', 'July' => 'julio',
                'August' => 'agosto', 'September' => 'septiembre', 'October' => 'octubre',
                'November' => 'noviembre', 'December' => 'diciembre',
                'Monday' => 'Lunes', 'Tuesday' => 'Martes', 'Wednesday' => 'Miércoles',
                'Thursday' => 'Jueves', 'Friday' => 'Viernes', 'Saturday' => 'Sábado',
                'Sunday' => 'Domingo',
            ];
            $salida = date('l j F Y', $ts);
            foreach ($trad as $en => $es) {
                $salida = str_replace($en, $es, $salida);
            }
            if ($formato === 'abreviado') {
                $salida = date('j M Y', $ts);
                $abre = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
                $salida = preg_replace_callback('/\b[a-z]{3}\b/i', function ($m) use ($trad, $abre) {
                    return $abre[(int) date('n', strtotime('-' . (int) date('j') . ' days'))] ?? $m[0];
                }, $salida);
            }
            return $salida;
        }

        $ts = $fecha === 'now' ? time() : strtotime($fecha);
        if ($ts === false) {
            $ts = time();
        }

        switch ($formato) {
            case 'abreviado':
                $fmt = new IntlDateFormatter(
                    'es_ES',
                    IntlDateFormatter::MEDIUM,
                    IntlDateFormatter::NONE,
                    null,
                    null,
                    'd MMM y'
                );
                break;
            case 'corto':
                $fmt = new IntlDateFormatter(
                    'es_ES',
                    IntlDateFormatter::SHORT,
                    IntlDateFormatter::SHORT
                );
                break;
            case 'largo':
            default:
                $fmt = new IntlDateFormatter(
                    'es_ES',
                    IntlDateFormatter::LONG,
                    IntlDateFormatter::NONE,
                    null,
                    null,
                    'EEEE, d \'de\' MMMM \'de\' y'
                );
                break;
        }

        return $fmt->format($ts) ?: date('d/m/Y', $ts);
    }
}

if (!function_exists('fecha_publicacion')) {
    /**
     * Devuelve la fecha en formato estandar DD/MM/YYYY.
     */
    function fecha_publicacion(?string $fecha): string
    {
        if (empty($fecha)) {
            return '';
        }
        $ts = strtotime($fecha);
        return $ts === false ? '' : date('d/m/Y', $ts);
    }
}

if (!function_exists('hora_publicacion')) {
    /**
     * Devuelve la hora en formato estandar HH:MM.
     */
    function hora_publicacion(?string $fecha): string
    {
        if (empty($fecha)) {
            return '';
        }
        $ts = strtotime($fecha);
        return $ts === false ? '' : date('H:i', $ts);
    }
}

if (!function_exists('rol_legible')) {
    /**
     * Traduce el codigo de rol de admin_usuarios a una etiqueta legible.
     */
    function rol_legible(?string $rol): string
    {
        $mapa = [
            'superadmin' => 'Administrador',
            'admin'      => 'Administrador',
            'empleado'   => 'Empleado',
            'soporte'    => 'Soporte',
            'vendedor'   => 'Vendedor',
            'tecnico'    => 'Tecnico',
        ];

        return $mapa[$rol] ?? ($rol ? ucfirst($rol) : 'Empleado');
    }
}
