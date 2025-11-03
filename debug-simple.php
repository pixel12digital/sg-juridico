<?php
/**
 * Script de debug simples para capturar erro exato
 * Acesse: https://sgjuridico.com.br/debug-simple.php
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
    <title>Debug WordPress</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .error { background: #ffeaea; padding: 15px; margin: 10px 0; border-left: 4px solid #d63638; }
        .success { background: #eafaea; padding: 15px; margin: 10px 0; border-left: 4px solid #00a32a; }
        pre { background: #2d2d2d; color: #f8f8f2; padding: 15px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Debug WordPress - Erro Exato</h1>
    
    <?php
    echo '<div class="success">✅ PHP está funcionando</div>';
    echo '<div class="success">✅ Arquivo debug-simple.php está sendo executado</div>';
    
    // Verificar se wp-config.php existe
    if (file_exists('wp-config.php')) {
        echo '<div class="success">✅ wp-config.php encontrado</div>';
        
        // Tentar carregar wp-config.php
        try {
            require_once('wp-config.php');
            echo '<div class="success">✅ wp-config.php carregado</div>';
        } catch (Throwable $e) {
            echo '<div class="error">❌ Erro ao carregar wp-config.php:</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            echo '<pre>Arquivo: ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</pre>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
            exit;
        }
    } else {
        echo '<div class="error">❌ wp-config.php NÃO encontrado</div>';
        exit;
    }
    
    // Verificar se ABSPATH foi definido
    if (defined('ABSPATH')) {
        echo '<div class="success">✅ ABSPATH definido: ' . ABSPATH . '</div>';
    } else {
        echo '<div class="error">❌ ABSPATH não foi definido</div>';
        exit;
    }
    
    // Verificar se wp-settings.php existe
    if (file_exists(ABSPATH . 'wp-settings.php')) {
        echo '<div class="success">✅ wp-settings.php encontrado</div>';
        
        // Tentar carregar wp-settings.php
        try {
            // Capturar qualquer output
            ob_start();
            
            // Definir handler de erro personalizado
            set_error_handler(function($errno, $errstr, $errfile, $errline) {
                echo '<div class="error">❌ Erro PHP:</div>';
                echo '<pre>' . htmlspecialchars("$errno: $errstr em $errfile:$errline") . '</pre>';
                return false;
            });
            
            // Tentar carregar
            require_once(ABSPATH . 'wp-settings.php');
            
            $output = ob_get_clean();
            restore_error_handler();
            
            if (!empty($output)) {
                echo '<div class="error">❌ Output capturado durante carregamento:</div>';
                echo '<pre>' . htmlspecialchars($output) . '</pre>';
            } else {
                echo '<div class="success">✅ wp-settings.php carregado sem erros visíveis</div>';
            }
            
        } catch (ParseError $e) {
            echo '<div class="error">❌ Erro de Sintaxe (ParseError):</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            echo '<pre>Arquivo: ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</pre>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        } catch (Error $e) {
            echo '<div class="error">❌ Erro Fatal (Error):</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            echo '<pre>Arquivo: ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</pre>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        } catch (Exception $e) {
            echo '<div class="error">❌ Exceção:</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            echo '<pre>Arquivo: ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</pre>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        } catch (Throwable $e) {
            echo '<div class="error">❌ Erro (Throwable):</div>';
            echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
            echo '<pre>Arquivo: ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</pre>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        }
    } else {
        echo '<div class="error">❌ wp-settings.php NÃO encontrado em: ' . ABSPATH . 'wp-settings.php</div>';
    }
    
    // Verificar se WordPress carregou
    if (function_exists('wp_get_current_user')) {
        echo '<div class="success">✅ WordPress carregado com sucesso!</div>';
    } else {
        echo '<div class="error">❌ WordPress NÃO carregou completamente</div>';
    }
    ?>
    
    <hr>
    <p><strong>⚠️ IMPORTANTE:</strong> Delete este arquivo após usar!</p>
</body>
</html>

