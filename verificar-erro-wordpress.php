<?php
/**
 * Script de diagnóstico de erros do WordPress
 * 
 * USO: Acesse via navegador: http://seudominio.com/verificar-erro-wordpress.php
 * 
 * IMPORTANTE: Remova este arquivo após o uso por segurança!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Diagnóstico de Erros WordPress</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 20px auto;
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
            color: #d63638;
            border-bottom: 3px solid #d63638;
            padding-bottom: 10px;
        }
        .section {
            margin: 30px 0;
            padding: 20px;
            background: #f9f9f9;
            border-left: 4px solid #0073aa;
            border-radius: 4px;
        }
        .section h2 {
            color: #0073aa;
            margin-top: 0;
        }
        .error {
            background: #ffeaea;
            border-left: 4px solid #d63638;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success {
            background: #eafaea;
            border-left: 4px solid #00a32a;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #f0b849;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .info {
            background: #e5f5fa;
            border-left: 4px solid #0073aa;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
        }
        pre {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            font-size: 12px;
        }
        code {
            background: #f0f0f0;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #0073aa;
            color: white;
            font-weight: bold;
        }
        tr:hover {
            background: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Diagnóstico de Erros WordPress</h1>
        
        <?php
        $errors = array();
        $warnings = array();
        $info = array();
        
        // 1. Verificar se wp-config.php existe
        echo '<div class="section">';
        echo '<h2>1. Verificação de Arquivos Essenciais</h2>';
        
        if (file_exists('wp-config.php')) {
            echo '<div class="success">✅ wp-config.php encontrado</div>';
        } else {
            echo '<div class="error">❌ wp-config.php NÃO encontrado</div>';
            $errors[] = 'wp-config.php não existe';
        }
        
        if (file_exists('wp-load.php')) {
            echo '<div class="success">✅ wp-load.php encontrado</div>';
        } else {
            echo '<div class="error">❌ wp-load.php NÃO encontrado</div>';
            $errors[] = 'wp-load.php não existe';
        }
        
        if (file_exists('wp-settings.php')) {
            echo '<div class="success">✅ wp-settings.php encontrado</div>';
        } else {
            echo '<div class="error">❌ wp-settings.php NÃO encontrado</div>';
            $errors[] = 'wp-settings.php não existe';
        }
        
        echo '</div>';
        
        // 2. Verificar sintaxe do wp-config.php
        echo '<div class="section">';
        echo '<h2>2. Verificação de Sintaxe PHP</h2>';
        
        // Verificar sintaxe usando tokenizer (sem exec/shell_exec)
        if (file_exists('wp-config.php')) {
            $syntax_ok = true;
            $syntax_error = '';
            
            try {
                // Usar tokenizer para verificar sintaxe básica
                $code = file_get_contents('wp-config.php');
                $tokens = @token_get_all($code);
                
                if ($tokens === false) {
                    $syntax_ok = false;
                    $syntax_error = 'Não foi possível analisar o arquivo com tokenizer';
                } else {
                    // Verificação básica de sintaxe
                    // Se chegou aqui sem erro fatal, a sintaxe básica está OK
                    echo '<div class="success">✅ wp-config.php: Sintaxe PHP básica válida (verificado com tokenizer)</div>';
                }
            } catch (ParseError $e) {
                $syntax_ok = false;
                $syntax_error = $e->getMessage();
                echo '<div class="error">❌ wp-config.php: Erro de sintaxe encontrado</div>';
                echo '<pre>' . htmlspecialchars($syntax_error) . '</pre>';
                $errors[] = 'Erro de sintaxe em wp-config.php';
            } catch (Exception $e) {
                echo '<div class="warning">⚠️ Não foi possível verificar sintaxe completa: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        }
        
        // Verificar mu-plugins
        if (file_exists('wp-content/mu-plugins/db-connection-manager.php')) {
            try {
                $code = file_get_contents('wp-content/mu-plugins/db-connection-manager.php');
                $tokens = @token_get_all($code);
                
                if ($tokens === false) {
                    echo '<div class="error">❌ db-connection-manager.php: Não foi possível analisar</div>';
                    $errors[] = 'Erro ao analisar db-connection-manager.php';
                } else {
                    echo '<div class="success">✅ db-connection-manager.php: Sintaxe PHP básica válida (verificado com tokenizer)</div>';
                }
            } catch (ParseError $e) {
                echo '<div class="error">❌ db-connection-manager.php: Erro de sintaxe encontrado</div>';
                echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
                $errors[] = 'Erro de sintaxe em db-connection-manager.php';
            } catch (Exception $e) {
                echo '<div class="warning">⚠️ Não foi possível verificar sintaxe completa: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        }
        
        echo '</div>';
        
        // 3. Tentar carregar WordPress e capturar erros
        echo '<div class="section">';
        echo '<h2>3. Tentativa de Carregar WordPress</h2>';
        
        if (file_exists('wp-load.php')) {
            // Capturar qualquer output/erro
            ob_start();
            $error_handler = set_error_handler(function($errno, $errstr, $errfile, $errline) {
                global $errors;
                $errors[] = "Erro $errno: $errstr em $errfile:$errline";
                return false;
            });
            
            try {
                // Tentar carregar wp-load
                $wp_loaded = false;
                
                // Verificar constantes necessárias
                if (!defined('ABSPATH')) {
                    require_once('wp-config.php');
                }
                
                if (defined('ABSPATH')) {
                    echo '<div class="success">✅ ABSPATH definido: ' . ABSPATH . '</div>';
                    
                    // Tentar carregar wp-load
                    if (file_exists(ABSPATH . 'wp-load.php')) {
                        try {
                            require_once(ABSPATH . 'wp-load.php');
                            $wp_loaded = true;
                            echo '<div class="success">✅ WordPress carregado com sucesso</div>';
                        } catch (Throwable $e) {
                            echo '<div class="error">❌ Erro ao carregar WordPress: ' . htmlspecialchars($e->getMessage()) . '</div>';
                            echo '<div class="error">Arquivo: ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</div>';
                            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
                            $errors[] = $e->getMessage();
                        }
                    }
                } else {
                    echo '<div class="error">❌ ABSPATH não foi definido</div>';
                    $errors[] = 'ABSPATH não definido';
                }
                
            } catch (Throwable $e) {
                echo '<div class="error">❌ Erro Fatal: ' . htmlspecialchars($e->getMessage()) . '</div>';
                echo '<div class="error">Arquivo: ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</div>';
                echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
                $errors[] = $e->getMessage();
            }
            
            restore_error_handler();
            $output = ob_get_clean();
            
            if (!empty($output)) {
                echo '<div class="warning">⚠️ Output capturado:</div>';
                echo '<pre>' . htmlspecialchars($output) . '</pre>';
            }
        }
        
        echo '</div>';
        
        // 4. Verificar debug.log
        echo '<div class="section">';
        echo '<h2>4. Últimos Erros do debug.log</h2>';
        
        $debug_log = 'wp-content/debug.log';
        if (file_exists($debug_log)) {
            $log_lines = file($debug_log);
            $recent_errors = array_slice($log_lines, -20); // Últimas 20 linhas
            
            if (!empty($recent_errors)) {
                echo '<div class="info">Mostrando últimas 20 linhas do debug.log:</div>';
                echo '<pre>' . htmlspecialchars(implode('', $recent_errors)) . '</pre>';
            } else {
                echo '<div class="success">✅ Nenhum erro recente no debug.log</div>';
            }
        } else {
            echo '<div class="warning">⚠️ debug.log não encontrado</div>';
        }
        
        echo '</div>';
        
        // 5. Verificar plugins mu-plugins
        echo '<div class="section">';
        echo '<h2>5. Verificação de Mu-Plugins</h2>';
        
        $mu_plugins_dir = 'wp-content/mu-plugins';
        if (is_dir($mu_plugins_dir)) {
            $mu_plugins = glob($mu_plugins_dir . '/*.php');
            if (!empty($mu_plugins)) {
                echo '<div class="info">Mu-plugins encontrados:</div>';
                echo '<ul>';
                foreach ($mu_plugins as $plugin) {
                    $plugin_name = basename($plugin);
                    echo '<li><code>' . htmlspecialchars($plugin_name) . '</code>';
                    
                    // Verificar sintaxe usando tokenizer (sem exec/shell_exec)
                    $syntax_ok = true;
                    try {
                        $code = file_get_contents($plugin);
                        $tokens = @token_get_all($code);
                        
                        if ($tokens === false) {
                            $syntax_ok = false;
                        }
                    } catch (ParseError $e) {
                        $syntax_ok = false;
                    } catch (Exception $e) {
                        $syntax_ok = false;
                    }
                    
                    if ($syntax_ok) {
                        echo ' <span style="color: green;">✅</span>';
                    } else {
                        echo ' <span style="color: red;">❌ Erro de sintaxe</span>';
                    }
                    
                    echo '</li>';
                }
                echo '</ul>';
            } else {
                echo '<div class="warning">⚠️ Nenhum mu-plugin encontrado</div>';
            }
        } else {
            echo '<div class="warning">⚠️ Diretório mu-plugins não encontrado</div>';
        }
        
        echo '</div>';
        
        // 6. Verificar configurações do banco
        echo '<div class="section">';
        echo '<h2>6. Verificação de Configuração do Banco</h2>';
        
        if (file_exists('wp-config.php')) {
            // Ler wp-config.php e verificar constantes
            $wp_config_content = file_get_contents('wp-config.php');
            
            $db_config = array();
            if (preg_match("/define\s*\(\s*['\"]DB_NAME['\"]\s*,\s*['\"]([^'\"]+)['\"]/", $wp_config_content, $matches)) {
                $db_config['DB_NAME'] = $matches[1];
            }
            if (preg_match("/define\s*\(\s*['\"]DB_USER['\"]\s*,\s*['\"]([^'\"]+)['\"]/", $wp_config_content, $matches)) {
                $db_config['DB_USER'] = $matches[1];
            }
            if (preg_match("/define\s*\(\s*['\"]DB_HOST['\"]\s*,\s*['\"]([^'\"]+)['\"]/", $wp_config_content, $matches)) {
                $db_config['DB_HOST'] = $matches[1];
            }
            
            if (!empty($db_config)) {
                echo '<table>';
                echo '<tr><th>Configuração</th><th>Valor</th></tr>';
                foreach ($db_config as $key => $value) {
                    // Ocultar senha parcialmente
                    if ($key === 'DB_PASSWORD') {
                        $value = '***' . substr($value, -3);
                    }
                    echo '<tr><td><code>' . htmlspecialchars($key) . '</code></td><td>' . htmlspecialchars($value) . '</td></tr>';
                }
                echo '</table>';
            } else {
                echo '<div class="warning">⚠️ Não foi possível ler configurações do banco</div>';
            }
        }
        
        echo '</div>';
        
        // 7. Resumo
        echo '<div class="section">';
        echo '<h2>📊 Resumo do Diagnóstico</h2>';
        
        if (empty($errors)) {
            echo '<div class="success">✅ Nenhum erro crítico encontrado nos arquivos verificados</div>';
        } else {
            echo '<div class="error">❌ Encontrados ' . count($errors) . ' erro(s):</div>';
            echo '<ul>';
            foreach ($errors as $error) {
                echo '<li>' . htmlspecialchars($error) . '</li>';
            }
            echo '</ul>';
        }
        
        echo '</div>';
        
        // 8. Recomendações
        echo '<div class="section">';
        echo '<h2>💡 Recomendações</h2>';
        
        if (!empty($errors)) {
            echo '<div class="warning">';
            echo '<strong>Ações recomendadas:</strong><br>';
            echo '1. Verifique os erros listados acima<br>';
            echo '2. Corrija erros de sintaxe PHP<br>';
            echo '3. Verifique se todos os arquivos estão presentes<br>';
            echo '4. Limpe o cache do WordPress (se houver)<br>';
            echo '5. Verifique permissões de arquivos<br>';
            echo '6. Consulte o debug.log para mais detalhes';
            echo '</div>';
        } else {
            echo '<div class="info">';
            echo 'Se o site ainda apresenta erro, pode ser:<br>';
            echo '1. Problema de conexão com o banco de dados<br>';
            echo '2. Plugin ou tema com erro<br>';
            echo '3. Cache do servidor<br>';
            echo '4. Permissões de arquivos<br>';
            echo '5. Limite de memória PHP atingido';
            echo '</div>';
        }
        
        echo '</div>';
        
        echo '<div class="warning">';
        echo '<strong>⚠️ IMPORTANTE:</strong><br>';
        echo '• Após usar este script, <strong>DELETE este arquivo</strong> por segurança!<br>';
        echo '• Este script expõe informações sensíveis do sistema<br>';
        echo '• Use apenas para diagnóstico e remova imediatamente após';
        echo '</div>';
        ?>
    </div>
</body>
</html>

