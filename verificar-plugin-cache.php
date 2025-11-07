<?php
/**
 * Script para verificar status do plugin de cache
 * Acesse: http://localhost/sg-juridico/verificar-plugin-cache.php
 */

// Carregar WordPress
require_once(__DIR__ . '/wp-load.php');

// Verificar se usuário está logado e é admin
if (!is_user_logged_in() || !current_user_can('manage_options')) {
    die('Você precisa estar logado como administrador para ver esta página.');
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação de Plugin de Cache</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            max-width: 1200px;
            margin: 40px auto;
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
            color: #23282d;
            border-bottom: 2px solid #0073aa;
            padding-bottom: 10px;
        }
        .status {
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
            border-left: 4px solid;
        }
        .status.active {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        .status.inactive {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        .status.info {
            background: #d1ecf1;
            border-color: #17a2b8;
            color: #0c5460;
        }
        .status.warning {
            background: #fff3cd;
            border-color: #ffc107;
            color: #856404;
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
            background: #f8f9fa;
            font-weight: 600;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #0073aa;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 10px 5px 10px 0;
        }
        .btn:hover {
            background: #005a87;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Verificação de Plugin de Cache</h1>
        
        <?php
        // Verificar usuário atual
        $current_user = wp_get_current_user();
        ?>
        
        <div class="status info">
            <strong>Usuário atual:</strong> <?php echo esc_html($current_user->user_login); ?> 
            (<?php echo esc_html(implode(', ', $current_user->roles)); ?>)
        </div>
        
        <?php
        // Verificar permissões
        $can_manage_options = current_user_can('manage_options');
        ?>
        
        <div class="status <?php echo $can_manage_options ? 'active' : 'inactive'; ?>">
            <strong>Permissão de Administrador:</strong> 
            <?php echo $can_manage_options ? '✅ SIM - Você pode ver o menu Plugins' : '❌ NÃO - O menu Plugins está oculto para você'; ?>
        </div>
        
        <?php
        // Verificar plugins instalados
        $all_plugins = get_plugins();
        $active_plugins = get_option('active_plugins', array());
        
        // Procurar plugins de cache
        $cache_plugins = array();
        foreach ($all_plugins as $plugin_file => $plugin_data) {
            $plugin_name = strtolower($plugin_data['Name']);
            if (strpos($plugin_name, 'cache') !== false || 
                strpos($plugin_name, 'litespeed') !== false ||
                strpos($plugin_name, 'w3 total') !== false ||
                strpos($plugin_name, 'wp super') !== false ||
                strpos($plugin_name, 'wp rocket') !== false) {
                $cache_plugins[$plugin_file] = array(
                    'name' => $plugin_data['Name'],
                    'active' => in_array($plugin_file, $active_plugins),
                    'file' => $plugin_file
                );
            }
        }
        ?>
        
        <h2>📦 Plugins de Cache Encontrados</h2>
        
        <?php if (empty($cache_plugins)): ?>
            <div class="status warning">
                ⚠️ Nenhum plugin de cache encontrado instalado.
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Plugin</th>
                        <th>Status</th>
                        <th>Arquivo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cache_plugins as $plugin_file => $plugin_info): ?>
                        <tr>
                            <td><strong><?php echo esc_html($plugin_info['name']); ?></strong></td>
                            <td>
                                <div class="status <?php echo $plugin_info['active'] ? 'active' : 'inactive'; ?>">
                                    <?php echo $plugin_info['active'] ? '✅ ATIVO' : '❌ INATIVO'; ?>
                                </div>
                            </td>
                            <td><code><?php echo esc_html($plugin_file); ?></code></td>
                            <td>
                                <?php if (!$plugin_info['active']): ?>
                                    <a href="<?php echo admin_url('plugins.php'); ?>" class="btn">Ativar Plugin</a>
                                <?php else: ?>
                                    <a href="<?php echo admin_url('admin.php?page=litespeed'); ?>" class="btn">Abrir Configurações</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <h2>🔧 Verificações Adicionais</h2>
        
        <?php
        // Verificar se menu Plugins está oculto
        global $menu;
        $plugins_menu_exists = false;
        if (isset($menu)) {
            foreach ($menu as $menu_item) {
                if (isset($menu_item[2]) && $menu_item[2] === 'plugins.php') {
                    $plugins_menu_exists = true;
                    break;
                }
            }
        }
        ?>
        
        <div class="status <?php echo $plugins_menu_exists ? 'active' : 'warning'; ?>">
            <strong>Menu "Plugins" visível:</strong> 
            <?php echo $plugins_menu_exists ? '✅ SIM' : '⚠️ NÃO - Pode estar oculto por código no tema'; ?>
        </div>
        
        <?php
        // Verificar código que pode estar ocultando menus
        $functions_file = get_template_directory() . '/functions.php';
        if (file_exists($functions_file)) {
            $functions_content = file_get_contents($functions_file);
            if (strpos($functions_content, 'remove_menu_page') !== false) {
                ?>
                <div class="status warning">
                    ⚠️ <strong>Encontrado código que remove menus:</strong> O arquivo <code>functions.php</code> contém código que pode estar ocultando menus do admin.
                </div>
                <?php
            }
        }
        ?>
        
        <h2>💡 Soluções</h2>
        
        <div class="status info">
            <h3>Se o plugin não está aparecendo:</h3>
            <ol>
                <li><strong>Verifique se você é administrador:</strong> Apenas administradores podem ver o menu "Plugins"</li>
                <li><strong>Verifique se o plugin está ativo:</strong> Vá em <a href="<?php echo admin_url('plugins.php'); ?>">Plugins → Plugins Instalados</a></li>
                <li><strong>Se o LiteSpeed Cache está ativo:</strong> O menu deve aparecer como "LiteSpeed Cache" na barra lateral do admin</li>
                <li><strong>Limpe o cache do navegador:</strong> Pressione Ctrl+F5 para recarregar a página</li>
            </ol>
        </div>
        
        <div style="margin-top: 30px;">
            <a href="<?php echo admin_url(); ?>" class="btn">Voltar ao Admin</a>
            <a href="<?php echo admin_url('plugins.php'); ?>" class="btn">Ver Todos os Plugins</a>
        </div>
    </div>
</body>
</html>







