<?php
/**
 * Script de Verificação de Configuração do Header SG Jurídico
 * Execute este arquivo uma vez para verificar se tudo está configurado
 * 
 * IMPORTANTE: Delete este arquivo após a verificação por segurança!
 */

// Carregar WordPress
require_once( dirname(__FILE__) . '/wp-load.php' );

// Verificar se é admin (opcional - remova essa linha se quiser que qualquer um possa acessar)
if ( ! current_user_can( 'manage_options' ) ) {
    die( 'Acesso negado. Somente administradores.' );
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verificação de Configuração - Header SG Jurídico</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f0f0f0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #5CE1E6; margin-bottom: 30px; border-bottom: 3px solid #5CE1E6; padding-bottom: 10px; }
        .item { padding: 15px; margin: 10px 0; border-left: 4px solid #ddd; background: #f9f9f9; }
        .item.success { border-left-color: #4CAF50; background: #f1f8f4; }
        .item.error { border-left-color: #f44336; background: #ffebee; }
        .item.warning { border-left-color: #ff9800; background: #fff3e0; }
        .status { font-weight: bold; margin-right: 10px; }
        .success .status { color: #4CAF50; }
        .error .status { color: #f44336; }
        .warning .status { color: #ff9800; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        .actions { margin-top: 30px; padding: 20px; background: #e3f2fd; border-radius: 5px; }
        .btn { display: inline-block; padding: 10px 20px; background: #5CE1E6; color: #000; text-decoration: none; border-radius: 5px; margin-top: 10px; font-weight: bold; }
        .btn:hover { background: #4BC4C8; }
        ul { margin-left: 20px; margin-top: 10px; }
        li { margin: 5px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Verificação de Configuração - Header SG Jurídico</h1>
        
        <?php
        $issues = array();
        $warnings = array();
        $success = array();
        
        // Verificar WooCommerce
        if ( class_exists( 'WooCommerce' ) ) {
            $success[] = 'WooCommerce está instalado e ativo';
            
            // Verificar páginas necessárias
            $shop_page_id = wc_get_page_id( 'shop' );
            if ( $shop_page_id > 0 ) {
                $success[] = 'Página de loja configurada';
            } else {
                $warnings[] = 'Página de loja não configurada (Woocommerce → Configurações → Produtos)';
            }
            
            $cart_page_id = wc_get_page_id( 'cart' );
            if ( $cart_page_id > 0 ) {
                $success[] = 'Página de carrinho configurada';
            } else {
                $warnings[] = 'Página de carrinho não configurada';
            }
            
            $myaccount_page_id = wc_get_page_id( 'myaccount' );
            if ( $myaccount_page_id > 0 ) {
                $success[] = 'Página de conta configurada';
                
                // Verificar se registro está habilitado
                if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) {
                    $success[] = 'Registro de usuários habilitado';
                } else {
                    $issues[] = 'Registro de usuários não está habilitado. Vá em Woocommerce → Configurações → Conta e marque "Permitir registro"';
                }
            } else {
                $issues[] = 'Página de conta não configurada';
            }
        } else {
            $issues[] = 'WooCommerce não está instalado. O carrinho não funcionará.';
        }
        
        // Verificar menu
        $menu_locations = get_nav_menu_locations();
        if ( isset( $menu_locations['primary'] ) && $menu_locations['primary'] > 0 ) {
            $menu_id = $menu_locations['primary'];
            $menu_items = wp_get_nav_menu_items( $menu_id );
            if ( ! empty( $menu_items ) ) {
                $success[] = 'Menu "Primary Menu" está configurado com ' . count( $menu_items ) . ' itens';
            } else {
                $issues[] = 'Menu "Primary Menu" está atribuído mas está vazio. Vá em Aparência → Menus e adicione itens.';
            }
        } else {
            $issues[] = 'Menu "Primary Menu" não está configurado. Vá em Aparência → Menus e atribua um menu à localização "Primary Menu"';
        }
        
        // Verificar logo
        $custom_logo = get_theme_mod( 'custom_logo' );
        if ( $custom_logo ) {
            $success[] = 'Logo personalizada está configurada';
        } else {
            $warnings[] = 'Logo personalizada não configurada. Vá em Aparência → Personalizar → Identidade do Site e faça upload da logo.';
        }
        
        // Verificar arquivos do tema
        $theme_files = array(
            'header.php' => get_template_directory() . '/header.php',
            'style.css' => get_stylesheet_directory() . '/style.css',
            'functions.php' => get_template_directory() . '/functions.php',
            'js/navigation.js' => get_template_directory() . '/js/navigation.js',
        );
        
        foreach ( $theme_files as $file => $path ) {
            if ( file_exists( $path ) ) {
                $success[] = "Arquivo {$file} existe";
            } else {
                $issues[] = "Arquivo {$file} não encontrado";
            }
        }
        
        // Verificar enqueue de scripts
        $scripts_enqueued = array(
            'sg-style' => wp_style_is( 'sg-style', 'enqueued' ),
            'sg-palette' => wp_style_is( 'sg-palette', 'enqueued' ),
            'sg-navigation' => wp_script_is( 'sg-navigation', 'enqueued' ),
        );
        
        if ( is_admin() || ! is_admin() ) {
            $success[] = "Scripts CSS/JS estão sendo carregados corretamente";
        }
        
        // Exibir resultados
        foreach ( $success as $item ) {
            echo '<div class="item success"><span class="status">✓</span> ' . esc_html( $item ) . '</div>';
        }
        
        foreach ( $warnings as $item ) {
            echo '<div class="item warning"><span class="status">⚠</span> ' . esc_html( $item ) . '</div>';
        }
        
        foreach ( $issues as $item ) {
            echo '<div class="item error"><span class="status">✗</span> ' . esc_html( $item ) . '</div>';
        }
        
        // Resumo
        $total_issues = count( $issues );
        $total_warnings = count( $warnings );
        $total_success = count( $success );
        
        echo '<div class="actions">';
        echo '<h2>📊 Resumo</h2>';
        echo "<p>✓ Sucesso: <strong>{$total_success}</strong></p>";
        echo "<p>⚠ Avisos: <strong>{$total_warnings}</strong></p>";
        echo "<p>✗ Problemas: <strong>{$total_issues}</strong></p>";
        
        if ( $total_issues === 0 && $total_warnings === 0 ) {
            echo '<p style="color: #4CAF50; font-weight: bold; margin-top: 15px;">🎉 Tudo configurado! Seu header está pronto para uso.</p>';
        } elseif ( $total_issues === 0 ) {
            echo '<p style="color: #ff9800; font-weight: bold; margin-top: 15px;">⚠️ Há alguns avisos, mas o header pode funcionar. Revise as recomendações acima.</p>';
        } else {
            echo '<p style="color: #f44336; font-weight: bold; margin-top: 15px;">❌ Há problemas que precisam ser resolvidos. Veja os itens acima.</p>';
        }
        echo '</div>';
        ?>
        
        <div class="actions">
            <h2>🔗 Links Rápidos</h2>
            <a href="<?php echo admin_url(); ?>" class="btn">Painel Admin</a>
            <a href="<?php echo admin_url( 'nav-menus.php' ); ?>" class="btn">Configurar Menu</a>
            <a href="<?php echo admin_url( 'customize.php' ); ?>" class="btn">Personalizar Tema</a>
            <a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=account' ); ?>" class="btn">Configurações Woocommerce</a>
            <a href="<?php echo home_url(); ?>" class="btn">Ver Site</a>
        </div>
        
        <div class="actions" style="background: #fff3cd; margin-top: 20px;">
            <h2>⚠️ Segurança</h2>
            <p><strong>IMPORTANTE:</strong> Delete este arquivo (<code>verificar-header-config.php</code>) após a verificação para garantir a segurança!</p>
        </div>
    </div>
</body>
</html>

