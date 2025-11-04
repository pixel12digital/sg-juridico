<?php
/**
 * Plugin Name: Fix SSL Admin Localhost
 * Description: Força HTTP em localhost para evitar páginas em branco após login
 * Version: 1.0
 * Author: Auto Fix
 */

// CRÍTICO: Garantir que FORCE_SSL_ADMIN seja sempre false em localhost
add_filter('secure_auth_redirect', function($secure) {
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    $is_localhost = (
        strpos($host, 'localhost') !== false || 
        strpos($host, '127.0.0.1') !== false ||
        strpos($host, '::1') !== false ||
        empty($host)
    );
    
    if ($is_localhost) {
        return false; // NUNCA usar HTTPS em localhost
    }
    
    return $secure;
}, 1);

// Também garantir que force_ssl_admin() retorne false
add_filter('force_ssl_admin', function($force) {
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    $is_localhost = (
        strpos($host, 'localhost') !== false || 
        strpos($host, '127.0.0.1') !== false ||
        strpos($host, '::1') !== false ||
        empty($host)
    );
    
    if ($is_localhost) {
        return false;
    }
    
    return $force;
}, 1);

