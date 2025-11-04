<?php
/**
 * Plugin Name: Fix WooCommerce Admin Notices Type Error
 * Description: Corrige erro de tipo no WooCommerce Admin Notices
 * Version: 1.0
 * Author: Auto Fix
 */

// CRÍTICO: Corrigir opção corrompida antes do WooCommerce carregar
add_action('plugins_loaded', function() {
    // Verificar se a opção existe e está corrompida
    $wc_notices = get_option('woocommerce_admin_notices');
    
    if ($wc_notices !== false && !is_array($wc_notices)) {
        // Opção corrompida - deletar e recriar como array vazio
        delete_option('woocommerce_admin_notices');
        update_option('woocommerce_admin_notices', array());
    }
}, 1); // Prioridade alta para executar antes do WooCommerce

// Também corrigir durante init caso ainda esteja corrompida
add_action('init', function() {
    $wc_notices = get_option('woocommerce_admin_notices');
    
    if ($wc_notices !== false && !is_array($wc_notices)) {
        delete_option('woocommerce_admin_notices');
        update_option('woocommerce_admin_notices', array());
    }
}, 1);

// Filter para garantir que sempre retorne array
add_filter('option_woocommerce_admin_notices', function($value) {
    if (!is_array($value)) {
        return array();
    }
    return $value;
}, 1);

