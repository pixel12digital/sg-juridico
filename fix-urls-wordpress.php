<?php
/**
 * Script para corrigir URLs absolutas no WordPress
 * 
 * Este script atualiza as URLs no banco de dados para usar URLs relativas/dinâmicas
 * Funciona tanto em ambiente local quanto em produção
 * 
 * IMPORTANTE: Delete este arquivo após usar!
 */

require_once 'wp-load.php';

// Detecta o ambiente atual
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$current_url = $protocol . '://' . $host;

echo "<h2>Corrigir URLs do WordPress</h2>";
echo "<pre>";
echo "URL Atual Detectada: {$current_url}\n";
echo "\n" . str_repeat("=", 60) . "\n\n";

// Verifica as URLs atuais no banco
$home_url = get_option('home');
$site_url = get_option('siteurl');

echo "URLs atuais no banco:\n";
echo "  Home URL: {$home_url}\n";
echo "  Site URL: {$site_url}\n";
echo "\n";

// Pergunta se deve atualizar (automatizado)
$should_update = true;

if ($should_update) {
    // Atualiza as URLs principais
    update_option('home', $current_url);
    update_option('siteurl', $current_url);
    
    echo "✓ URLs atualizadas para: {$current_url}\n";
    
    // Também atualiza posts com URLs antigas
    global $wpdb;
    
    // Busca posts com URLs antigas
    $old_url = 'sgjuridico.com.br';
    $results = $wpdb->get_results($wpdb->prepare("
        SELECT ID, post_title 
        FROM {$wpdb->posts} 
        WHERE post_content LIKE %s 
        LIMIT 100
    ", '%' . $old_url . '%'));
    
    if (!empty($results)) {
        echo "\nEncontrados " . count($results) . " posts com URLs antigas\n";
        
        foreach ($results as $post) {
            // Atualiza o conteúdo do post
            $post_content = get_post_field('post_content', $post->ID);
            $new_content = str_replace($old_url, $host, $post_content);
            
            if ($new_content !== $post_content) {
                wp_update_post([
                    'ID' => $post->ID,
                    'post_content' => $new_content
                ]);
                echo "  ✓ Atualizado: {$post->post_title}\n";
            }
        }
    }
    
    // Limpa cache
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
    
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "\n✓✓✓ CORREÇÃO CONCLUÍDA! ✓✓✓\n";
    echo "\nAs URLs foram atualizadas para o ambiente atual.\n";
    echo "IMPORTANTE: Delete este arquivo após a correção!\n";
} else {
    echo "Atualização cancelada.\n";
}

echo "</pre>";
?>

