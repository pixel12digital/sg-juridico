<?php
/**
 * Script para testar carregamento direto do wp-admin
 * Acesse: https://sgjuridico.com.br/test-wp-admin.php
 * 
 * IMPORTANTE: DELETE após usar!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('max_execution_time', 60);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test WP-Admin Direto</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .error { background: #ffeaea; padding: 15px; margin: 10px 0; border-left: 4px solid #d63638; }
        .success { background: #eafaea; padding: 15px; margin: 10px 0; border-left: 4px solid #00a32a; }
        pre { background: #2d2d2d; color: #f8f8f2; padding: 15px; overflow-x: auto; font-size: 11px; }
    </style>
</head>
<body>
    <h1>Testando Carregamento Direto do WP-Admin</h1>
    
    <?php
    // Carregar WordPress
    require_once('wp-load.php');
    
    echo '<div class="success">✅ WordPress carregado</div>';
    
    // Simular ambiente admin
    define('WP_ADMIN', true);
    $_SERVER['PHP_SELF'] = '/wp-admin/index.php';
    $_SERVER['REQUEST_URI'] = '/wp-admin/index.php';
    
    echo '<div class="success">✅ Ambiente admin simulado</div>';
    
    // Tentar carregar wp-admin/index.php
    echo '<h2>Tentando carregar wp-admin/index.php...</h2>';
    
    try {
        // Capturar output e erros
        ob_start();
        
        // Handler de erro personalizado
        set_error_handler(function($errno, $errstr, $errfile, $errline) {
            echo '<div class="error">❌ Erro PHP capturado:</div>';
            echo '<pre>';
            echo "Erro $errno: $errstr\n";
            echo "Arquivo: $errfile:$errline\n";
            echo '</pre>';
            return false;
        });
        
        // Handler de exceções
        set_exception_handler(function($exception) {
            echo '<div class="error">❌ Exceção capturada:</div>';
            echo '<pre>' . htmlspecialchars($exception->getMessage()) . '</pre>';
            echo '<pre>Arquivo: ' . htmlspecialchars($exception->getFile()) . ':' . $exception->getLine() . '</pre>';
            echo '<pre>' . htmlspecialchars($exception->getTraceAsString()) . '</pre>';
        });
        
        // Tentar incluir o arquivo
        $admin_file = ABSPATH . 'wp-admin/index.php';
        
        if (file_exists($admin_file)) {
            echo '<div class="success">✅ Arquivo existe: ' . htmlspecialchars($admin_file) . '</div>';
            
            // Tentar incluir
            try {
                // Não podemos incluir diretamente porque precisa de autenticação
                // Mas podemos verificar se há algum problema ao carregar as dependências
                
                // Verificar se admin.php carrega
                $admin_bootstrap = ABSPATH . 'wp-admin/admin.php';
                if (file_exists($admin_bootstrap)) {
                    echo '<div class="success">✅ Tentando carregar wp-admin/admin.php...</div>';
                    
                    // Vamos verificar se há algum problema lendo o arquivo
                    $admin_content = file_get_contents($admin_bootstrap);
                    
                    // Verificar se há includes problemáticos
                    if (preg_match_all('/require.*?[\'"]([^\'"]+)[\'"]/', $admin_content, $matches)) {
                        echo '<div class="success">✅ Arquivos incluídos por admin.php:</div>';
                        echo '<ul>';
                        foreach ($matches[1] as $include) {
                            // Resolver caminho relativo
                            $full_path = ABSPATH . 'wp-admin/' . $include;
                            if (file_exists($full_path)) {
                                echo '<li><code>' . htmlspecialchars($include) . '</code> ✅</li>';
                            } else {
                                echo '<li><code>' . htmlspecialchars($include) . '</code> ❌ NÃO ENCONTRADO</li>';
                            }
                        }
                        echo '</ul>';
                    }
                }
                
                // Verificar se há algum problema com plugins sendo carregados
                echo '<h2>Testando carregamento de plugins...</h2>';
                
                if (function_exists('get_option')) {
                    $active_plugins = get_option('active_plugins', array());
                    
                    foreach ($active_plugins as $plugin) {
                        echo '<div>Testando plugin: <code>' . htmlspecialchars($plugin) . '</code>...</div>';
                        
                        $plugin_file = WP_PLUGIN_DIR . '/' . $plugin;
                        
                        if (file_exists($plugin_file)) {
                            // Tentar verificar se o plugin tem algum problema de inicialização
                            $plugin_content = file_get_contents($plugin_file);
                            
                            // Verificar se há chamadas problemáticas no nível do arquivo
                            if (preg_match('/add_action\s*\([^)]*\)\s*;/', $plugin_content, $matches)) {
                                // Verificar se está no nível do arquivo (não dentro de função)
                                $lines = explode("\n", $plugin_content);
                                foreach ($lines as $line_num => $line) {
                                    if (preg_match('/add_action\s*\(/', $line) && !preg_match('/function\s+\w+\s*\(/', implode("\n", array_slice($lines, max(0, $line_num-10), 20)))) {
                                        echo '<div class="error">⚠️ Possível add_action no nível do arquivo na linha ' . ($line_num + 1) . ':</div>';
                                        echo '<pre>' . htmlspecialchars($line) . '</pre>';
                                    }
                                }
                            }
                            
                            // Tentar carregar o plugin
                            try {
                                // Não vamos incluir diretamente, mas podemos verificar sintaxe mais profunda
                                $tokens = token_get_all($plugin_content);
                                $brace_count = 0;
                                $in_function = false;
                                
                                foreach ($tokens as $token) {
                                    if (is_array($token)) {
                                        if ($token[0] === T_FUNCTION) {
                                            $in_function = true;
                                        }
                                    }
                                }
                            } catch (Exception $e) {
                                echo '<div class="error">❌ Erro ao analisar plugin:</div>';
                                echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
                            }
                        }
                    }
                }
                
            } catch (Throwable $e) {
                echo '<div class="error">❌ Erro ao processar:</div>';
                echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
                echo '<pre>Arquivo: ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</pre>';
                echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
            }
        }
        
        $output = ob_get_clean();
        restore_error_handler();
        restore_exception_handler();
        
        if (!empty($output)) {
            echo $output;
        }
        
    } catch (Throwable $e) {
        echo '<div class="error">❌ Erro Fatal:</div>';
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        echo '<pre>Arquivo: ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</pre>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    }
    
    // Verificar memória
    echo '<h2>Informações do Sistema</h2>';
    echo '<div>Memória PHP: ' . ini_get('memory_limit') . '</div>';
    echo '<div>Memória usada: ' . number_format(memory_get_usage() / 1024 / 1024, 2) . ' MB</div>';
    echo '<div>Memória pico: ' . number_format(memory_get_peak_usage() / 1024 / 1024, 2) . ' MB</div>';
    echo '<div>Max execution time: ' . ini_get('max_execution_time') . 's</div>';
    ?>
    
    <hr>
    <p><strong>⚠️ IMPORTANTE:</strong> Delete este arquivo após usar!</p>
</body>
</html>

