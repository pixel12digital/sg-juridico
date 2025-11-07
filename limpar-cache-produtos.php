<?php
/**
 * Script para limpar cache e forçar recarregamento dos estilos
 * Acesse: http://localhost/sg-juridico/limpar-cache-produtos.php
 */

// Limpar cache do WordPress
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
    echo "✅ Cache do WordPress limpo<br>";
}

// Limpar transientes
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%'");
echo "✅ Transientes limpos<br>";

// Forçar atualização do CSS (adicionar timestamp)
$theme_dir = get_stylesheet_directory();
$style_file = $theme_dir . '/style.css';
$functions_file = $theme_dir . '/functions.php';

if (file_exists($style_file)) {
    touch($style_file);
    echo "✅ style.css atualizado<br>";
}

if (file_exists($functions_file)) {
    touch($functions_file);
    echo "✅ functions.php atualizado<br>";
}

echo "<br><strong>CACHE LIMPO COM SUCESSO!</strong><br>";
echo "<a href='/produto/proposta-trf-analista-judiciario-area-judiciaria/'>Ver página do produto</a>";

