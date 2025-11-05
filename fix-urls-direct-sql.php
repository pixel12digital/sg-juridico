<?php
/**
 * Script SQL direto para corrigir URLs no banco de dados WordPress
 * 
 * Este script atualiza DIRETAMENTE no banco de dados as opções 'home' e 'siteurl'
 * e todas as URLs de localhost encontradas em posts, postmeta e options.
 * 
 * USO:
 * - Via navegador: Acesse http://seu-dominio.com/sg-juridico/fix-urls-direct-sql.php
 * - IMPORTANTE: Execute APENAS UMA VEZ e depois DELETE este arquivo!
 * 
 * ATENÇÃO: Este script faz alterações diretas no banco de dados.
 * Faça backup antes de executar!
 */

// Carregar WordPress
require_once(__DIR__ . '/wp-load.php');

// Verificar se usuário está logado como admin (se via navegador)
if (php_sapi_name() !== 'cli' && !current_user_can('manage_options')) {
    wp_die('Você não tem permissão para executar este script.');
}

global $wpdb;

// Detectar ambiente e URL correta
function sg_get_production_url() {
    if (php_sapi_name() === 'cli') {
        // Em CLI, pedir ao usuário ou usar variável de ambiente
        return getenv('WP_HOME') ?: 'http://localhost/sg-juridico';
    }
    
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
    $protocol = 'http';
    
    if (strpos($host, 'localhost') === false && strpos($host, '127.0.0.1') === false) {
        // Produção
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            $protocol = 'https';
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            $protocol = 'https';
        }
    }
    
    return $protocol . '://' . $host;
}

$production_url = sg_get_production_url();
$is_localhost = (
    strpos($production_url, 'localhost') !== false || 
    strpos($production_url, '127.0.0.1') !== false
);

// HTML header se for acesso via navegador
if (php_sapi_name() !== 'cli') {
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Corrigir URLs (SQL Direto) - SG Jurídico</title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                max-width: 900px;
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
            .error {
                padding: 15px;
                margin: 10px 0;
                border-left: 4px solid #f44336;
                background: #ffebee;
            }
            code {
                background: #f5f5f5;
                padding: 2px 6px;
                border-radius: 3px;
                font-family: 'Courier New', monospace;
            }
            .btn {
                display: inline-block;
                padding: 10px 20px;
                background: #2196f3;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                margin: 10px 5px;
            }
            .btn:hover {
                background: #1976d2;
            }
            .btn-danger {
                background: #f44336;
            }
            .btn-danger:hover {
                background: #d32f2f;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🔧 Corrigir URLs no Banco de Dados (SQL Direto)</h1>
    <?php
}

function sg_output($message, $type = 'info') {
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
        $class = $type;
        echo "<div class='{$class}'>" . esc_html($message) . "</div>";
    }
}

sg_output("URL de produção detectada: <code>" . $production_url . "</code>", 'info');
sg_output("Ambiente: " . ($is_localhost ? 'Localhost' : 'Produção'), 'info');

if ($is_localhost) {
    sg_output("⚠️  Você está em localhost. Este script atualizará URLs para: " . $production_url, 'warning');
}

// Verificar URLs atuais
$current_home = $wpdb->get_var("SELECT option_value FROM {$wpdb->options} WHERE option_name = 'home'");
$current_siteurl = $wpdb->get_var("SELECT option_value FROM {$wpdb->options} WHERE option_name = 'siteurl'");

sg_output("", 'info');
sg_output("URLs atuais no banco:", 'info');
sg_output("  - home: <code>" . esc_html($current_home) . "</code>", 'info');
sg_output("  - siteurl: <code>" . esc_html($current_siteurl) . "</code>", 'info');

// Se via navegador, mostrar botão de confirmação
if (php_sapi_name() !== 'cli' && !isset($_GET['confirm'])) {
    ?>
    <div class="warning">
        <strong>⚠️ ATENÇÃO:</strong> Este script irá atualizar diretamente no banco de dados:
        <ul>
            <li>Opções 'home' e 'siteurl'</li>
            <li>URLs em post_content</li>
            <li>URLs em postmeta</li>
            <li>URLs em options</li>
        </ul>
        <p><strong>Recomendação:</strong> Faça backup do banco de dados antes de continuar!</p>
    </div>
    <a href="?confirm=1" class="btn btn-danger">⚠️ Confirmar e Executar Atualização</a>
    <a href="javascript:history.back()" class="btn">Cancelar</a>
    <?php
    if (php_sapi_name() !== 'cli') {
        ?>
        </div>
        </body>
        </html>
        <?php
    }
    exit;
}

sg_output("", 'info');
sg_output("Executando atualizações no banco de dados...", 'warning');

// 1. Atualizar opções home e siteurl
$result1 = $wpdb->update(
    $wpdb->options,
    array('option_value' => $production_url),
    array('option_name' => 'home')
);

$result2 = $wpdb->update(
    $wpdb->options,
    array('option_value' => $production_url),
    array('option_name' => 'siteurl')
);

sg_output("Opções atualizadas:", 'success');
sg_output("  - home: " . ($result1 !== false ? '✅ Atualizado' : '❌ Erro'), $result1 !== false ? 'success' : 'error');
sg_output("  - siteurl: " . ($result2 !== false ? '✅ Atualizado' : '❌ Erro'), $result2 !== false ? 'success' : 'error');

// 2. Atualizar URLs em post_content
$patterns = array(
    'http://localhost/sg-juridico' => $production_url,
    'https://localhost/sg-juridico' => $production_url,
    'http://localhost' => $production_url,
    'https://localhost' => $production_url,
    'http://127.0.0.1/sg-juridico' => $production_url,
    'https://127.0.0.1/sg-juridico' => $production_url,
    'http://127.0.0.1' => $production_url,
    'https://127.0.0.1' => $production_url,
);

$total_posts = 0;
foreach ($patterns as $old_url => $new_url) {
    $result = $wpdb->query($wpdb->prepare(
        "UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s) WHERE post_content LIKE %s",
        $old_url,
        $new_url,
        '%' . $wpdb->esc_like($old_url) . '%'
    ));
    if ($result !== false && $result > 0) {
        $total_posts += $result;
    }
}

sg_output("Posts atualizados: " . $total_posts, $total_posts > 0 ? 'success' : 'info');

// 3. Atualizar URLs em postmeta
$total_postmeta = 0;
foreach ($patterns as $old_url => $new_url) {
    $result = $wpdb->query($wpdb->prepare(
        "UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_value LIKE %s",
        $old_url,
        $new_url,
        '%' . $wpdb->esc_like($old_url) . '%'
    ));
    if ($result !== false && $result > 0) {
        $total_postmeta += $result;
    }
}

sg_output("Postmeta atualizados: " . $total_postmeta, $total_postmeta > 0 ? 'success' : 'info');

// 4. Atualizar URLs em options (exceto home e siteurl que já foram atualizados)
$total_options = 0;
foreach ($patterns as $old_url => $new_url) {
    $result = $wpdb->query($wpdb->prepare(
        "UPDATE {$wpdb->options} SET option_value = REPLACE(option_value, %s, %s) WHERE option_name NOT IN ('home', 'siteurl') AND option_value LIKE %s",
        $old_url,
        $new_url,
        '%' . $wpdb->esc_like($old_url) . '%'
    ));
    if ($result !== false && $result > 0) {
        $total_options += $result;
    }
}

sg_output("Outras opções atualizadas: " . $total_options, $total_options > 0 ? 'success' : 'info');

// Verificar URLs finais
$final_home = $wpdb->get_var("SELECT option_value FROM {$wpdb->options} WHERE option_name = 'home'");
$final_siteurl = $wpdb->get_var("SELECT option_value FROM {$wpdb->options} WHERE option_name = 'siteurl'");

sg_output("", 'info');
sg_output("URLs finais no banco:", 'success');
sg_output("  - home: <code>" . esc_html($final_home) . "</code>", 'success');
sg_output("  - siteurl: <code>" . esc_html($final_siteurl) . "</code>", 'success');

sg_output("", 'info');
sg_output("✅ Atualização concluída!", 'success');
sg_output("⚠️  IMPORTANTE: Delete este arquivo agora para segurança!", 'warning');

// Limpar cache do WordPress
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
    sg_output("Cache do WordPress limpo.", 'success');
}

// HTML footer se for acesso via navegador
if (php_sapi_name() !== 'cli') {
    ?>
        </div>
    </body>
    </html>
    <?php
}

