<?php
/**
 * Script para desabilitar todos os plugins temporariamente
 * Acesse: https://sgjuridico.com.br/desabilitar-plugins.php
 * 
 * IMPORTANTE: DELETE após usar!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Desabilitar Plugins</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; }
        .success { background: #eafaea; padding: 15px; margin: 10px 0; border-left: 4px solid #00a32a; }
        .error { background: #ffeaea; padding: 15px; margin: 10px 0; border-left: 4px solid #d63638; }
        .warning { background: #fff3cd; padding: 15px; margin: 10px 0; border-left: 4px solid #f0b849; }
        code { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; }
        button { background: #0073aa; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
        button:hover { background: #005a87; }
        button.danger { background: #d63638; }
        button.danger:hover { background: #b32d2e; }
    </style>
</head>
<body>
    <h1>Gerenciar Plugins</h1>
    
    <?php
    // Carregar WordPress
    require_once('wp-load.php');
    
    if (!function_exists('get_option') || !function_exists('update_option')) {
        echo '<div class="error">❌ WordPress não carregou corretamente</div>';
        exit;
    }
    
    // Verificar se foi solicitada uma ação
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    $plugin = isset($_GET['plugin']) ? $_GET['plugin'] : '';
    
    if ($action === 'disable_all') {
        // Desabilitar todos os plugins
        $active_plugins = get_option('active_plugins', array());
        update_option('active_plugins_backup', $active_plugins);
        update_option('active_plugins', array());
        echo '<div class="success">✅ Todos os plugins foram desabilitados. Backup salvo em active_plugins_backup</div>';
        echo '<div class="warning">⚠️ Teste o site agora. Se funcionar, o problema está em um dos plugins.</div>';
    } elseif ($action === 'restore') {
        // Restaurar plugins
        $backup = get_option('active_plugins_backup', array());
        if (!empty($backup)) {
            update_option('active_plugins', $backup);
            delete_option('active_plugins_backup');
            echo '<div class="success">✅ Plugins restaurados</div>';
        } else {
            echo '<div class="error">❌ Nenhum backup encontrado</div>';
        }
    } elseif ($action === 'disable' && !empty($plugin)) {
        // Desabilitar um plugin específico
        $active_plugins = get_option('active_plugins', array());
        $backup = get_option('active_plugins_backup', array());
        if (empty($backup)) {
            update_option('active_plugins_backup', $active_plugins);
        }
        $active_plugins = array_diff($active_plugins, array($plugin));
        update_option('active_plugins', array_values($active_plugins));
        echo '<div class="success">✅ Plugin desabilitado: <code>' . htmlspecialchars($plugin) . '</code></div>';
    } elseif ($action === 'enable' && !empty($plugin)) {
        // Habilitar um plugin específico
        $active_plugins = get_option('active_plugins', array());
        if (!in_array($plugin, $active_plugins)) {
            $active_plugins[] = $plugin;
            update_option('active_plugins', array_values($active_plugins));
            echo '<div class="success">✅ Plugin habilitado: <code>' . htmlspecialchars($plugin) . '</code></div>';
        }
    }
    
    // Listar plugins ativos
    $active_plugins = get_option('active_plugins', array());
    $backup = get_option('active_plugins_backup', array());
    
    echo '<h2>Plugins Ativos (' . count($active_plugins) . ')</h2>';
    
    if (!empty($active_plugins)) {
        echo '<table style="width: 100%; border-collapse: collapse;">';
        echo '<tr style="background: #f0f0f0;"><th style="padding: 10px; text-align: left;">Plugin</th><th style="padding: 10px;">Ações</th></tr>';
        foreach ($active_plugins as $plugin) {
            echo '<tr style="border-bottom: 1px solid #ddd;">';
            echo '<td style="padding: 10px;"><code>' . htmlspecialchars($plugin) . '</code></td>';
            echo '<td style="padding: 10px;">';
            echo '<a href="?action=disable&plugin=' . urlencode($plugin) . '" style="color: #d63638; text-decoration: none;">Desabilitar</a>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo '<div class="warning">⚠️ Nenhum plugin ativo</div>';
    }
    
    if (!empty($backup)) {
        echo '<h2>Plugins em Backup (' . count($backup) . ')</h2>';
        echo '<div class="warning">⚠️ Há um backup de plugins. Você pode restaurar clicando no botão abaixo.</div>';
    }
    ?>
    
    <div style="margin-top: 30px;">
        <h2>Ações</h2>
        <a href="?action=disable_all"><button class="danger">Desabilitar TODOS os Plugins</button></a>
        <?php if (!empty($backup)): ?>
            <a href="?action=restore"><button>Restaurar Plugins do Backup</button></a>
        <?php endif; ?>
    </div>
    
    <hr>
    <div class="warning">
        <strong>⚠️ IMPORTANTE:</strong><br>
        • Este script desabilita plugins temporariamente<br>
        • Um backup automático é criado antes de desabilitar<br>
        • Teste o site após desabilitar os plugins<br>
        • DELETE este arquivo após usar por segurança!
    </div>
</body>
</html>

