<?php
/**
 * Script para corrigir plugins desativados por erro de arquivo não encontrado
 * 
 * Este script verifica quais plugins estão registrados como ativos no banco de dados
 * e verifica se os arquivos realmente existem. Se existirem, reativa os plugins.
 * 
 * USO:
 * - Via navegador: Acesse http://seu-dominio.com/sg-juridico/fix-plugins.php
 * - Via WP-CLI: php fix-plugins.php
 * 
 * IMPORTANTE: Execute este script apenas uma vez após migração ou se os plugins estiverem com erro.
 * Depois delete este arquivo para segurança.
 */

// Carregar WordPress
require_once(__DIR__ . '/wp-load.php');

// Verificar se usuário está logado como admin (se via navegador)
if (php_sapi_name() !== 'cli' && !current_user_can('manage_options')) {
    wp_die('Você não tem permissão para executar este script.');
}

// HTML header se for acesso via navegador
if (php_sapi_name() !== 'cli') {
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Corrigir Plugins - SG Jurídico</title>
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
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
            }
            th, td {
                padding: 12px;
                text-align: left;
                border-bottom: 1px solid #ddd;
            }
            th {
                background: #f5f5f5;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🔧 Corrigir Plugins Desativados</h1>
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

sg_output("Verificando plugins...", 'info');

// Obter lista de plugins ativos do banco de dados
$active_plugins = get_option('active_plugins', array());

if (empty($active_plugins)) {
    sg_output("Nenhum plugin ativo encontrado no banco de dados.", 'warning');
} else {
    sg_output("Encontrados " . count($active_plugins) . " plugins ativos no banco de dados.", 'info');
}

// Verificar cada plugin
$plugins_to_fix = array();
$plugins_ok = array();
$plugins_missing = array();

if (php_sapi_name() !== 'cli') {
    echo "<table>";
    echo "<tr><th>Plugin</th><th>Caminho Esperado</th><th>Status</th><th>Ação</th></tr>";
}

foreach ($active_plugins as $plugin_file) {
    // Normalizar caminho do plugin (remover barras invertidas do Windows)
    $plugin_file_normalized = str_replace('\\', '/', $plugin_file);
    
    // Tentar diferentes caminhos possíveis
    $plugin_paths = array(
        WP_PLUGIN_DIR . '/' . $plugin_file_normalized,
        WP_PLUGIN_DIR . '/' . $plugin_file,
        ABSPATH . 'wp-content/plugins/' . $plugin_file_normalized,
        ABSPATH . 'wp-content/plugins/' . $plugin_file,
    );
    
    $plugin_exists = false;
    $found_path = '';
    
    foreach ($plugin_paths as $path) {
        // Normalizar caminho para comparação
        $normalized_path = str_replace('\\', '/', $path);
        if (file_exists($normalized_path)) {
            $plugin_exists = true;
            $found_path = $normalized_path;
            break;
        }
    }
    
    if ($plugin_exists) {
        $plugins_ok[] = $plugin_file;
        if (php_sapi_name() !== 'cli') {
            echo "<tr><td><code>" . esc_html($plugin_file) . "</code></td>";
            echo "<td><code>" . esc_html(str_replace(ABSPATH, '', $found_path)) . "</code></td>";
            echo "<td style='color: green;'>✅ Arquivo existe</td>";
            echo "<td>-</td></tr>";
        } else {
            sg_output("✅ {$plugin_file} - Arquivo existe em: " . str_replace(ABSPATH, '', $found_path), 'success');
        }
    } else {
        $plugins_missing[] = $plugin_file;
        
        if (php_sapi_name() !== 'cli') {
            echo "<tr><td><code>" . esc_html($plugin_file) . "</code></td>";
            echo "<td><code>" . esc_html(WP_PLUGIN_DIR . '/' . $plugin_file) . "</code></td>";
            echo "<td style='color: red;'>❌ Arquivo não encontrado</td>";
            echo "<td style='color: orange;'>Será removido</td>";
            echo "</tr>";
        } else {
            sg_output("❌ {$plugin_file} - Arquivo não encontrado", 'error');
            sg_output("  Tentado: " . WP_PLUGIN_DIR . '/' . $plugin_file, 'error');
        }
    }
}

if (php_sapi_name() !== 'cli') {
    echo "</table>";
}

sg_output("", 'info');
sg_output("Resumo:", 'info');
sg_output("  - Plugins OK: " . count($plugins_ok), 'success');
sg_output("  - Plugins com problema: " . count($plugins_missing), count($plugins_missing) > 0 ? 'error' : 'success');

if (!empty($plugins_missing)) {
    sg_output("", 'info');
    sg_output("Verificando se os plugins realmente não existem ou se é um problema de caminho...", 'warning');
    
    // Tentar encontrar os plugins mesmo que não estejam no caminho esperado
    $plugins_found = array();
    $plugins_not_found = array();
    
    foreach ($plugins_missing as $plugin_file) {
        $plugin_name = basename(dirname($plugin_file));
        $plugin_main_file = basename($plugin_file);
        
        // Verificar se o diretório do plugin existe
        $plugin_dir = WP_PLUGIN_DIR . '/' . $plugin_name;
        if (is_dir($plugin_dir)) {
            // Verificar se o arquivo principal existe dentro do diretório
            $possible_files = array(
                $plugin_dir . '/' . $plugin_main_file,
                $plugin_dir . '/' . $plugin_name . '.php',
            );
            
            $found = false;
            foreach ($possible_files as $file) {
                if (file_exists($file)) {
                    $plugins_found[] = array(
                        'old' => $plugin_file,
                        'new' => $plugin_name . '/' . basename($file),
                        'path' => $file
                    );
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $plugins_not_found[] = $plugin_file;
            }
        } else {
            $plugins_not_found[] = $plugin_file;
        }
    }
    
    // Se encontramos plugins com caminhos diferentes, corrigir
    if (!empty($plugins_found)) {
        sg_output("Encontrados " . count($plugins_found) . " plugins com caminhos incorretos. Corrigindo...", 'warning');
        
        $updated_plugins = $active_plugins;
        
        foreach ($plugins_found as $plugin_info) {
            // Remover o caminho antigo e adicionar o novo
            $updated_plugins = array_diff($updated_plugins, array($plugin_info['old']));
            if (!in_array($plugin_info['new'], $updated_plugins)) {
                $updated_plugins[] = $plugin_info['new'];
            }
            
            sg_output("  Corrigido: " . $plugin_info['old'] . " → " . $plugin_info['new'], 'success');
        }
        
        update_option('active_plugins', array_values($updated_plugins));
        sg_output("Caminhos dos plugins corrigidos!", 'success');
    }
    
    // Remover plugins que realmente não existem
    if (!empty($plugins_not_found)) {
        sg_output("Removendo " . count($plugins_not_found) . " plugins que realmente não existem...", 'warning');
        
        $updated_plugins = get_option('active_plugins', array());
        $updated_plugins = array_diff($updated_plugins, $plugins_not_found);
        update_option('active_plugins', array_values($updated_plugins));
        
        sg_output("Plugins removidos da lista de ativos.", 'success');
    }
    
    // Limpar cache
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
    
    // Limpar transients relacionados a plugins
    global $wpdb;
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%' AND option_name LIKE '%plugin%'");
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_site_transient_%' AND option_name LIKE '%plugin%'");
    
    sg_output("", 'info');
    sg_output("✅ Correção concluída! Recarregue a página de plugins no WordPress admin.", 'success');
} else {
    sg_output("", 'info');
    sg_output("✅ Todos os plugins estão OK! Nenhuma correção necessária.", 'success');
    
    // Mesmo assim, limpar cache para garantir
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
}

sg_output("", 'info');
sg_output("⚠️  IMPORTANTE: Delete este arquivo após usar!", 'warning');

// Mostrar informações de debug
sg_output("", 'info');
sg_output("Informações de debug:", 'info');
sg_output("  - WP_PLUGIN_DIR: <code>" . WP_PLUGIN_DIR . "</code>", 'info');
sg_output("  - WP_PLUGIN_URL: <code>" . (defined('WP_PLUGIN_URL') ? WP_PLUGIN_URL : 'Não definido') . "</code>", 'info');
sg_output("  - ABSPATH: <code>" . ABSPATH . "</code>", 'info');

// HTML footer se for acesso via navegador
if (php_sapi_name() !== 'cli') {
    ?>
        </div>
    </body>
    </html>
    <?php
}

