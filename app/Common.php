<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 */

if (!function_exists('media_url')) {
    /**
     * Devuelve una URL limpia para imágenes o recursos.
     * Si la ruta ya es una URL absoluta (http/https), la retorna directamente.
     * De lo contrario, aplica base_url($path).
     */
    function media_url(?string $path, string $default = 'assets/img/placeholder.jpg'): string
    {
        if (empty($path)) {
            return base_url($default);
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        return base_url($path);
    }
}
