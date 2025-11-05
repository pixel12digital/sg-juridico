<?php
/**
 * Plugin Name: Force Correct URLs
 * Description: Força URLs corretas baseadas no domínio atual da requisição. Resolve problema de URLs redirecionando para localhost em produção.
 * Version: 1.1.0
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
    
    // Determina host - SEMPRE usa HTTP_HOST atual
    $host = 'localhost';
    if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
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
 * Substitui URLs de localhost pela URL correta
 */
function sg_replace_localhost_url($url) {
    if (!is_string($url) || empty($url)) {
        return $url;
    }
    
    // Se estamos em produção e a URL contém localhost, substituir
    if (!sg_is_localhost() && strpos($url, 'localhost') !== false) {
        $current_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        if (!empty($current_host)) {
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
            if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
                $protocol = 'https';
            }
            $base_url = $protocol . '://' . $current_host;
            
            // Substituir todas as variações de localhost
            $url = preg_replace('#https?://localhost[^/]*/sg-juridico?#', $base_url, $url);
            $url = preg_replace('#https?://localhost[^/]*#', $base_url, $url);
            $url = preg_replace('#https?://127\.0\.0\.1[^/]*/sg-juridico?#', $base_url, $url);
            $url = preg_replace('#https?://127\.0\.0\.1[^/]*#', $base_url, $url);
        }
    }
    
    return $url;
}

/**
 * Força home_url() a retornar a URL correta
 * PRIORIDADE MÁXIMA para garantir que seja executado depois de tudo
 */
function sg_force_home_url($url, $path, $orig_scheme, $blog_id) {
    // Se já temos uma override global, usa ela
    if (isset($GLOBALS['wp_home_override'])) {
        $base = $GLOBALS['wp_home_override'];
    } else {
        // Calcula baseado no ambiente atual
        $base = sg_get_base_url();
    }
    
    // Se estamos em produção e a URL contém localhost, substituir completamente
    if (!sg_is_localhost()) {
        $url = sg_replace_localhost_url($url);
        
        // Se ainda contém localhost após substituição, usar URL base calculada
        if (strpos($url, 'localhost') !== false) {
            return $base . ($path ? '/' . ltrim($path, '/') : '');
        }
    }
    
    // Se a URL já está correta ou estamos em localhost, retornar como está
    // Mas garantir que use a base correta se necessário
    if (!sg_is_localhost() && strpos($url, 'localhost') === false) {
        return $url;
    }
    
    return $base . ($path ? '/' . ltrim($path, '/') : '');
}

/**
 * Força site_url() a retornar a URL correta
 */
function sg_force_site_url($url, $path, $scheme, $blog_id) {
    // Se já temos uma override global, usa ela
    if (isset($GLOBALS['wp_siteurl_override'])) {
        $base = $GLOBALS['wp_siteurl_override'];
    } else {
        // Calcula baseado no ambiente atual
        $base = sg_get_base_url();
    }
    
    // Se estamos em produção e a URL contém localhost, substituir completamente
    if (!sg_is_localhost()) {
        $url = sg_replace_localhost_url($url);
        
        // Se ainda contém localhost após substituição, usar URL base calculada
        if (strpos($url, 'localhost') !== false) {
            return $base . ($path ? '/' . ltrim($path, '/') : '');
        }
    }
    
    // Se a URL já está correta ou estamos em localhost, retornar como está
    if (!sg_is_localhost() && strpos($url, 'localhost') === false) {
        return $url;
    }
    
    return $base . ($path ? '/' . ltrim($path, '/') : '');
}

/**
 * Corrige URLs em opções do WordPress ANTES de serem carregadas
 * Esta é a chave: interceptar ANTES do WordPress usar
 */
function sg_fix_option_urls($value, $option) {
    // Apenas corrigir opções críticas de URL
    $url_options = array('home', 'siteurl');
    
    if (!in_array($option, $url_options)) {
        return $value;
    }
    
    // Se estamos em produção e a opção contém localhost, corrigir
    if (!sg_is_localhost() && is_string($value) && strpos($value, 'localhost') !== false) {
        return sg_replace_localhost_url($value);
    }
    
    return $value;
}

/**
 * Força atualização das constantes WP_HOME e WP_SITEURL se necessário
 */
function sg_force_constants() {
    if (!sg_is_localhost()) {
        $base_url = sg_get_base_url();
        
        // Se as constantes não estão definidas ou contêm localhost, forçar atualização
        if (!defined('WP_HOME') || strpos(WP_HOME, 'localhost') !== false) {
            // Não podemos remover constantes, mas podemos usar a variável global
            $GLOBALS['wp_home_override'] = $base_url;
        }
        
        if (!defined('WP_SITEURL') || strpos(WP_SITEURL, 'localhost') !== false) {
            $GLOBALS['wp_siteurl_override'] = $base_url;
        }
    }
}

// Executar ANTES de tudo
add_action('plugins_loaded', 'sg_force_constants', 1);
add_action('init', 'sg_force_constants', 1);

// Aplicar filtros para forçar URLs corretas - PRIORIDADE MÁXIMA
add_filter('home_url', 'sg_force_home_url', 99999, 4);
add_filter('site_url', 'sg_force_site_url', 99999, 4);
add_filter('option_home', 'sg_fix_option_urls', 99999, 2);
add_filter('option_siteurl', 'sg_fix_option_urls', 99999, 2);

// Corrigir URLs em get_permalink e similares
add_filter('post_link', function($permalink, $post) {
    return sg_replace_localhost_url($permalink);
}, 99999, 2);

// Corrigir URLs em páginas
add_filter('page_link', function($permalink, $page_id) {
    return sg_replace_localhost_url($permalink);
}, 99999, 2);

// Corrigir URLs em termos/categorias
add_filter('term_link', function($termlink, $term, $taxonomy) {
    return sg_replace_localhost_url($termlink);
}, 99999, 3);

// Corrigir URLs em menus - CRÍTICO para resolver o problema reportado
add_filter('wp_get_nav_menu_items', function($items, $menu, $args) {
    if (!sg_is_localhost() && is_array($items)) {
        foreach ($items as $item) {
            if (isset($item->url) && strpos($item->url, 'localhost') !== false) {
                $item->url = sg_replace_localhost_url($item->url);
            }
        }
    }
    return $items;
}, 99999, 3);

// Corrigir URLs em navegação
add_filter('nav_menu_link_attributes', function($atts, $item, $args) {
    if (!sg_is_localhost() && isset($atts['href']) && strpos($atts['href'], 'localhost') !== false) {
        $atts['href'] = sg_replace_localhost_url($atts['href']);
    }
    return $atts;
}, 99999, 3);

// Corrigir URL do item do menu antes de ser usado
add_filter('wp_setup_nav_menu_item', function($menu_item) {
    if (!sg_is_localhost() && isset($menu_item->url) && strpos($menu_item->url, 'localhost') !== false) {
        $menu_item->url = sg_replace_localhost_url($menu_item->url);
    }
    return $menu_item;
}, 99999, 1);

// Corrigir URLs em redirects
add_filter('wp_redirect', function($location, $status) {
    return sg_replace_localhost_url($location);
}, 99999, 2);

// Corrigir URLs em location header
add_filter('wp_headers', function($headers) {
    if (!sg_is_localhost() && isset($headers['Location'])) {
        $headers['Location'] = sg_replace_localhost_url($headers['Location']);
    }
    return $headers;
}, 99999, 1);

// Corrigir URLs em scripts e styles
add_filter('script_loader_src', function($src, $handle) {
    return sg_replace_localhost_url($src);
}, 99999, 2);

add_filter('style_loader_src', function($src, $handle) {
    return sg_replace_localhost_url($src);
}, 99999, 2);

// Corrigir URLs em attachments
add_filter('wp_get_attachment_url', function($url, $post_id) {
    return sg_replace_localhost_url($url);
}, 99999, 2);

add_filter('attachment_link', function($link, $post_id) {
    return sg_replace_localhost_url($link);
}, 99999, 2);

add_filter('wp_get_attachment_image_url', function($url, $attachment_id, $size, $icon) {
    return sg_replace_localhost_url($url);
}, 99999, 4);

// Corrigir URLs em wp_get_attachment_image_src
add_filter('wp_get_attachment_image_src', function($image, $attachment_id, $size, $icon) {
    if (is_array($image) && !empty($image[0])) {
        $image[0] = sg_replace_localhost_url($image[0]);
    }
    return $image;
}, 99999, 4);

// Limpar cache de transients que podem conter URLs antigas
add_action('init', function() {
    if (!sg_is_localhost()) {
        // Limpar cache relacionado a URLs
        delete_transient('_site_transient_update_core');
        delete_transient('_site_transient_update_themes');
        delete_transient('_site_transient_update_plugins');
    }
}, 99999);


