<?php
/**
 * Script para corrigir URLs no banco de dados WordPress
 * 
 * Este script atualiza as opções 'home' e 'siteurl' no banco de dados
 * para usar o domínio correto baseado no ambiente atual.
 * 
 * USO:
 * - Via navegador: Acesse http://seu-dominio.com/sg-juridico/fix-urls-database.php
 * - Via WP-CLI: php fix-urls-database.php
 * 
 * IMPORTANTE: Execute este script apenas uma vez após migração ou se as URLs estiverem incorretas.
 * Depois delete este arquivo para segurança.
 */

// Carregar WordPress
require_once(__DIR__ . '/wp-load.php');

// Verificar se usuário está logado como admin (se via navegador)
if (php_sapi_name() !== 'cli' && !current_user_can('manage_options')) {
    wp_die('Você não tem permissão para executar este script.');
}

// Detectar ambiente
function sg_detect_environment() {
    if (php_sapi_name() === 'cli') {
        return array(
            'is_local' => true,
            'protocol' => 'http',
            'host' => 'localhost',
            'path' => '/sg-juridico'
        );
    }
    
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
    $is_local = (
        strpos($host, 'localhost') !== false || 
        strpos($host, '127.0.0.1') !== false ||
        strpos($host, '::1') !== false
    );
    
    $protocol = 'http';
    if (!$is_local && isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        $protocol = 'https';
    }
    if (!$is_local && isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        $protocol = 'https';
    }
    
    $path = '';
    if ($is_local && strpos($host, 'localhost') !== false) {
        $path = '/sg-juridico';
    }
    
    return array(
        'is_local' => $is_local,
        'protocol' => $protocol,
        'host' => $host,
        'path' => $path
    );
}

$env = sg_detect_environment();
$new_url = $env['protocol'] . '://' . $env['host'] . $env['path'];

// HTML header se for acesso via navegador
if (php_sapi_name() !== 'cli') {
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Corrigir URLs - SG Jurídico</title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                max-width: 800px;
                margin: 50px auto;
                padding: 20px;
                background: #f5f5f5;
            }
            .container {
                background: white;
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            h1 {
                color: #333;
                margin-top: 0;
            }
            .info {
                padding: 15px;
                margin: 10px 0;
                border-left: 4px solid #2196f3;
                background: #e3f2fd;
            }
            .success {
                padding: 15px;
                margin: 10px 0;
                border-left: 4px solid #4caf50;
                background: #e8f5e9;
            }
            .warning {
                padding: 15px;
                margin: 10px 0;
                border-left: 4px solid #ff9800;
                background: #fff3e0;
            }
            code {
                background: #f5f5f5;
                padding: 2px 6px;
                border-radius: 3px;
                font-family: 'Courier New', monospace;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🔧 Corrigir URLs no Banco de Dados</h1>
    <?php
}

function sg_output($message, $type = 'info') {
    global $env;
    
    $icons = array(
        'success' => '✅',
        'error' => '❌',
        'warning' => '⚠️',
        'info' => 'ℹ️'
    );
    
    $icon = $icons[$type] ?? '';
    $message = $icon . ' ' . $message;
    
    if (php_sapi_name() === 'cli') {
        echo $message . "\n";
    } else {
        $class = $type === 'success' ? 'success' : ($type === 'warning' ? 'warning' : 'info');
        echo "<div class='{$class}'>" . esc_html($message) . "</div>";
    }
}

sg_output("Ambiente detectado:", 'info');
sg_output("  - Local: " . ($env['is_local'] ? 'Sim' : 'Não"), 'info');
sg_output("  - Protocolo: " . $env['protocol'], 'info');
sg_output("  - Host: " . $env['host'], 'info');
sg_output("  - Nova URL: " . $new_url, 'info');

// Obter URLs atuais
$current_home = get_option('home');
$current_siteurl = get_option('siteurl');

sg_output("", 'info');
sg_output("URLs atuais no banco de dados:", 'info');
sg_output("  - home: " . $current_home, 'info');
sg_output("  - siteurl: " . $current_siteurl, 'info');

// Verificar se precisa atualizar
$needs_update = false;
if ($current_home !== $new_url || $current_siteurl !== $new_url) {
    $needs_update = true;
}

if (!$needs_update) {
    sg_output("", 'info');
    sg_output("As URLs já estão corretas! Nenhuma atualização necessária.", 'success');
} else {
    sg_output("", 'info');
    sg_output("Atualizando URLs no banco de dados...", 'warning');
    
    // Atualizar opções
    $updated_home = update_option('home', $new_url);
    $updated_siteurl = update_option('siteurl', $new_url);
    
    if ($updated_home && $updated_siteurl) {
        sg_output("URLs atualizadas com sucesso!", 'success');
        sg_output("  - home: " . get_option('home'), 'success');
        sg_output("  - siteurl: " . get_option('siteurl'), 'success');
    } else {
        sg_output("Erro ao atualizar URLs. Verifique permissões do banco de dados.", 'error');
    }
    
    // Também atualizar URLs em serialized data
    global $wpdb;
    
    // Atualizar URLs em post_content (pode conter links hardcoded)
    $wpdb->query($wpdb->prepare(
        "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s) WHERE post_content LIKE %s",
        'http://localhost/sg-juridico',
        $new_url,
        '%http://localhost/sg-juridico%'
    ));
    
    $wpdb->query($wpdb->prepare(
        "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s) WHERE post_content LIKE %s",
        'https://localhost/sg-juridico',
        $new_url,
        '%https://localhost/sg-juridico%'
    ));
    
    // Atualizar URLs em postmeta (pode conter URLs serializadas)
    $wpdb->query($wpdb->prepare(
        "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_value LIKE %s",
        'http://localhost/sg-juridico',
        $new_url,
        '%http://localhost/sg-juridico%'
    ));
    
    $wpdb->query($wpdb->prepare(
        "UPDATE {$wpdb->options} SET option_value = REPLACE(option_value, %s, %s) WHERE option_value LIKE %s",
        'http://localhost/sg-juridico',
        $new_url,
        '%http://localhost/sg-juridico%'
    ));
    
    sg_output("URLs em conteúdo e metadados também foram atualizadas.", 'success');
}

sg_output("", 'info');
sg_output("⚠️  IMPORTANTE: Delete este arquivo após usar!", 'warning');

// HTML footer se for acesso via navegador
if (php_sapi_name() !== 'cli') {
    ?>
        </div>
    </body>
    </html>
    <?php
}

