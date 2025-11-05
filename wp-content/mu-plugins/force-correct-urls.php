<?php
/**
 * Plugin Name: Force Correct URLs
 * Description: Força URLs corretas baseadas no domínio atual da requisição. Resolve problema de URLs redirecionando para localhost em produção.
 * Version: 1.0.0
 * Author: SG Jurídico
 */

// Prevenir acesso direto
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Detecta se estamos em ambiente localhost
 */
function sg_is_localhost() {
    if (php_sapi_name() === 'cli') {
        return true;
    }
    
    if (!isset($_SERVER['HTTP_HOST'])) {
        return true;
    }
    
    $host = $_SERVER['HTTP_HOST'];
    return (
        strpos($host, 'localhost') !== false || 
        strpos($host, '127.0.0.1') !== false ||
        strpos($host, '::1') !== false ||
        $host === 'localhost'
    );
}

/**
 * Obtém a URL base correta baseada no ambiente atual
 */
function sg_get_base_url() {
    $is_local = sg_is_localhost();
    
    // Determina protocolo
    $protocol = 'http';
    if (!$is_local && isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        $protocol = 'https';
    }
    // Verifica headers de proxy reverso
    if (!$is_local && isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        $protocol = 'https';
    }
    
    // Determina host
    $host = 'localhost';
    if (isset($_SERVER['HTTP_HOST'])) {
        $host = $_SERVER['HTTP_HOST'];
    }
    
    // Adiciona caminho em localhost
    $path = '';
    if ($is_local && strpos($host, 'localhost') !== false) {
        $path = '/sg-juridico';
    }
    
    return $protocol . '://' . $host . $path;
}

/**
 * Força home_url() a retornar a URL correta
 */
function sg_force_home_url($url, $path, $orig_scheme, $blog_id) {
    // Se já temos uma override global, usa ela
    if (isset($GLOBALS['wp_home_override'])) {
        return $GLOBALS['wp_home_override'] . ($path ? '/' . ltrim($path, '/') : '');
    }
    
    // Caso contrário, calcula baseado no ambiente atual
    $base_url = sg_get_base_url();
    
    // Se a URL atual contém localhost mas não estamos em localhost, substitui
    if (!sg_is_localhost() && strpos($url, 'localhost') !== false) {
        $current_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        if (!empty($current_host)) {
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
            if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
                $protocol = 'https';
            }
            $new_url = $protocol . '://' . $current_host . ($path ? '/' . ltrim($path, '/') : '');
            return $new_url;
        }
    }
    
    // Retorna URL calculada
    return $base_url . ($path ? '/' . ltrim($path, '/') : '');
}

/**
 * Força site_url() a retornar a URL correta
 */
function sg_force_site_url($url, $path, $scheme, $blog_id) {
    // Se já temos uma override global, usa ela
    if (isset($GLOBALS['wp_siteurl_override'])) {
        return $GLOBALS['wp_siteurl_override'] . ($path ? '/' . ltrim($path, '/') : '');
    }
    
    // Caso contrário, calcula baseado no ambiente atual
    $base_url = sg_get_base_url();
    
    // Se a URL atual contém localhost mas não estamos em localhost, substitui
    if (!sg_is_localhost() && strpos($url, 'localhost') !== false) {
        $current_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        if (!empty($current_host)) {
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
            if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
                $protocol = 'https';
            }
            $new_url = $protocol . '://' . $current_host . ($path ? '/' . ltrim($path, '/') : '');
            return $new_url;
        }
    }
    
    // Retorna URL calculada
    return $base_url . ($path ? '/' . ltrim($path, '/') : '');
}

/**
 * Corrige URLs em opções do WordPress que podem estar como localhost
 */
function sg_fix_option_urls($value, $option) {
    // Apenas corrigir opções críticas de URL
    $url_options = array('home', 'siteurl');
    
    if (!in_array($option, $url_options)) {
        return $value;
    }
    
    // Se estamos em produção e a opção contém localhost, corrigir
    if (!sg_is_localhost() && is_string($value) && strpos($value, 'localhost') !== false) {
        $current_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        if (!empty($current_host)) {
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
            if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
                $protocol = 'https';
            }
            $new_url = $protocol . '://' . $current_host;
            // Remove qualquer caminho localhost e substitui
            $value = preg_replace('#https?://localhost[^/]*/sg-juridico?#', $new_url, $value);
            $value = preg_replace('#https?://localhost[^/]*#', $new_url, $value);
        }
    }
    
    return $value;
}

// Aplicar filtros para forçar URLs corretas
add_filter('home_url', 'sg_force_home_url', 999, 4);
add_filter('site_url', 'sg_force_site_url', 999, 4);
add_filter('option_home', 'sg_fix_option_urls', 999, 2);
add_filter('option_siteurl', 'sg_fix_option_urls', 999, 2);

// Também corrigir URLs em get_permalink e similares
add_filter('post_link', function($permalink, $post) {
    if (!sg_is_localhost() && strpos($permalink, 'localhost') !== false) {
        $current_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        if (!empty($current_host)) {
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
            if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
                $protocol = 'https';
            }
            $base_url = $protocol . '://' . $current_host;
            $permalink = preg_replace('#https?://localhost[^/]*/sg-juridico?#', $base_url, $permalink);
            $permalink = preg_replace('#https?://localhost[^/]*#', $base_url, $permalink);
        }
    }
    return $permalink;
}, 999, 2);

// Corrigir URLs em páginas
add_filter('page_link', function($permalink, $page_id) {
    if (!sg_is_localhost() && strpos($permalink, 'localhost') !== false) {
        $current_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        if (!empty($current_host)) {
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
            if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
                $protocol = 'https';
            }
            $base_url = $protocol . '://' . $current_host;
            $permalink = preg_replace('#https?://localhost[^/]*/sg-juridico?#', $base_url, $permalink);
            $permalink = preg_replace('#https?://localhost[^/]*#', $base_url, $permalink);
        }
    }
    return $permalink;
}, 999, 2);

// Corrigir URLs em termos/categorias
add_filter('term_link', function($termlink, $term, $taxonomy) {
    if (!sg_is_localhost() && strpos($termlink, 'localhost') !== false) {
        $current_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        if (!empty($current_host)) {
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
            if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
                $protocol = 'https';
            }
            $base_url = $protocol . '://' . $current_host;
            $termlink = preg_replace('#https?://localhost[^/]*/sg-juridico?#', $base_url, $termlink);
            $termlink = preg_replace('#https?://localhost[^/]*#', $base_url, $termlink);
        }
    }
    return $termlink;
}, 999, 3);

