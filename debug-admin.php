<?php
/**
 * Script para testar acesso ao wp-admin
 * Acesse: https://sgjuridico.com.br/debug-admin.php
 * 
 * IMPORTANTE: DELETE após usar!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Debug WP-Admin</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .error { background: #ffeaea; padding: 15px; margin: 10px 0; border-left: 4px solid #d63638; }
        .success { background: #eafaea; padding: 15px; margin: 10px 0; border-left: 4px solid #00a32a; }
        .warning { background: #fff3cd; padding: 15px; margin: 10px 0; border-left: 4px solid #f0b849; }
        pre { background: #2d2d2d; color: #f8f8f2; padding: 15px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Debug WP-Admin - Testando Acesso</h1>
    
    <?php
    // Carregar WordPress
    require_once('wp-load.php');
    
    echo '<div class="success">✅ WordPress carregado</div>';
    
    // Verificar se estamos no admin
    if (is_admin()) {
        echo '<div class="success">✅ Estamos no contexto admin</div>';
    } else {
        echo '<div class="warning">⚠️ NÃO estamos no contexto admin</div>';
    }
    
    // Tentar carregar wp-admin
    echo '<h2>Testando carregamento do wp-admin...</h2>';
    
    // Simular acesso ao wp-admin/index.php
    $_SERVER['REQUEST_URI'] = '/wp-admin/index.php';
    $_SERVER['SCRIPT_NAME'] = '/wp-admin/index.php';
    
    try {
        // Capturar output
        ob_start();
        
        // Tentar carregar o admin
        if (file_exists(ABSPATH . 'wp-admin/index.php')) {
            echo '<div class="success">✅ wp-admin/index.php existe</div>';
            
            // Tentar incluir o arquivo
            try {
                // Não podemos simplesmente incluir porque precisa de autenticação
                // Mas podemos verificar se há erros de sintaxe
                $admin_code = file_get_contents(ABSPATH . 'wp-admin/index.php');
                $tokens = @token_get_all($admin_code);
                
                if ($tokens === false) {
                    echo '<div class="error">❌ Erro ao analisar wp-admin/index.php</div>';
                } else {
                    echo '<div class="success">✅ wp-admin/index.php tem sintaxe válida</div>';
                }
            } catch (ParseError $e) {
                echo '<div class="error">❌ Erro de sintaxe em wp-admin/index.php:</div>';
                echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            }
        } else {
            echo '<div class="error">❌ wp-admin/index.php NÃO existe</div>';
        }
        
        $output = ob_get_clean();
        if (!empty($output)) {
            echo '<div class="warning">⚠️ Output capturado:</div>';
            echo '<pre>' . htmlspecialchars($output) . '</pre>';
        }
        
    } catch (Throwable $e) {
        echo '<div class="error">❌ Erro ao processar wp-admin:</div>';
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        echo '<pre>Arquivo: ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</pre>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    }
    
    // Verificar plugins ativos
    echo '<h2>Verificando Plugins Ativos...</h2>';
    
    if (function_exists('get_option')) {
        $active_plugins = get_option('active_plugins', array());
        
        if (!empty($active_plugins)) {
            echo '<div class="warning">⚠️ Plugins ativos encontrados: ' . count($active_plugins) . '</div>';
            echo '<ul>';
            foreach ($active_plugins as $plugin) {
                echo '<li><code>' . htmlspecialchars($plugin) . '</code>';
                
                // Verificar se o arquivo existe
                $plugin_file = WP_PLUGIN_DIR . '/' . $plugin;
                if (file_exists($plugin_file)) {
                    echo ' ✅';
                    
                    // Verificar sintaxe
                    try {
                        $code = file_get_contents($plugin_file);
                        $tokens = @token_get_all($code);
                        if ($tokens === false) {
                            echo ' ❌ Erro de sintaxe';
                        }
                    } catch (ParseError $e) {
                        echo ' ❌ Erro: ' . htmlspecialchars($e->getMessage());
                    }
                } else {
                    echo ' ❌ Arquivo não encontrado';
                }
                echo '</li>';
            }
            echo '</ul>';
        } else {
            echo '<div class="success">✅ Nenhum plugin ativo</div>';
        }
    }
    
    // Verificar tema ativo
    echo '<h2>Verificando Tema Ativo...</h2>';
    
    if (function_exists('get_option')) {
        $theme = get_option('stylesheet', '');
        if (!empty($theme)) {
            echo '<div class="warning">⚠️ Tema ativo: <code>' . htmlspecialchars($theme) . '</code></div>';
            
            $theme_dir = get_theme_root() . '/' . $theme;
            if (is_dir($theme_dir)) {
                echo '<div class="success">✅ Diretório do tema existe: ' . htmlspecialchars($theme_dir) . '</div>';
                
                // Verificar functions.php
                $functions_file = $theme_dir . '/functions.php';
                if (file_exists($functions_file)) {
                    echo '<div class="success">✅ functions.php existe</div>';
                    
                    // Verificar sintaxe
                    try {
                        $code = file_get_contents($functions_file);
                        $tokens = @token_get_all($code);
                        if ($tokens === false) {
                            echo '<div class="error">❌ Erro ao analisar functions.php</div>';
                        } else {
                            echo '<div class="success">✅ functions.php tem sintaxe válida</div>';
                        }
                    } catch (ParseError $e) {
                        echo '<div class="error">❌ Erro de sintaxe em functions.php:</div>';
                        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
                    }
                }
            }
        }
    }
    
    // Tentar simular o erro crítico
    echo '<h2>Simulando Acesso ao WP-Admin...</h2>';
    
    try {
        // Definir variáveis como se estivéssemos no admin
        if (!defined('WP_ADMIN')) {
            define('WP_ADMIN', true);
        }
        
        // Tentar carregar o admin bootstrap
        if (file_exists(ABSPATH . 'wp-admin/admin.php')) {
            echo '<div class="success">✅ wp-admin/admin.php existe</div>';
            
            // Não podemos incluir diretamente porque precisa de autenticação
            // Mas podemos verificar sintaxe
            try {
                $admin_code = file_get_contents(ABSPATH . 'wp-admin/admin.php');
                $tokens = @token_get_all($admin_code);
                
                if ($tokens === false) {
                    echo '<div class="error">❌ Erro ao analisar wp-admin/admin.php</div>';
                } else {
                    echo '<div class="success">✅ wp-admin/admin.php tem sintaxe válida</div>';
                }
            } catch (ParseError $e) {
                echo '<div class="error">❌ Erro de sintaxe em wp-admin/admin.php:</div>';
                echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            }
        }
        
    } catch (Throwable $e) {
        echo '<div class="error">❌ Erro ao simular acesso:</div>';
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        echo '<pre>Arquivo: ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</pre>';
    }
    ?>
    
    <hr>
    <p><strong>⚠️ IMPORTANTE:</strong> Delete este arquivo após usar!</p>
</body>
</html>

