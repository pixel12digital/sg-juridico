<?php
/**
 * Plugin Name: Disable Heartbeat API
 * Description: Desabilita completamente o Heartbeat API para reduzir conexões ao banco
 * Version: 1.0.0
 */

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

// Só ativa em localhost
$is_local = isset($_SERVER['HTTP_HOST']) && 
           (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
            strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false);

// Aguardar WordPress estar pronto antes de usar hooks
if ($is_local && function_exists('add_action')) {
    // Desabilita Heartbeat API completamente - CRÍTICO para reduzir conexões
    // Heartbeat faz requisições AJAX a cada 15-30 segundos, gerando muitas conexões
    add_action('init', function() {
        if (function_exists('wp_deregister_script')) {
            wp_deregister_script('heartbeat');
        }
    }, 1);
    
    // Remove também do admin
    add_action('admin_init', function() {
        if (function_exists('wp_deregister_script')) {
            wp_deregister_script('heartbeat');
        }
    }, 1);
    
    // Remove do editor
    add_action('admin_enqueue_scripts', function() {
        if (function_exists('wp_deregister_script')) {
            wp_deregister_script('heartbeat');
        }
    }, 1);
    
    // Remove meta tags desnecessárias (reduz queries)
    add_action('init', function() {
        if (function_exists('remove_action')) {
            remove_action('wp_head', 'wp_generator');
            remove_action('wp_head', 'wlwmanifest_link');
            remove_action('wp_head', 'rsd_link');
        }
    }, 1);
}
