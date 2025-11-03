<?php
/**
 * Debug script - Verificar valores do banner
 * Acesse via: seu-site.com/wp-content/themes/sg-juridico/debug-banner.php
 * Remova este arquivo após usar!
 */

// Carregar WordPress
require_once( __DIR__ . '/../../../wp-load.php' );

// Verificar se o usuário está logado
if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
	die( 'Acesso negado. Faça login como administrador.' );
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Debug - Banner Images</title>
	<style>
		body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
		.container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
		h1 { color: #333; border-bottom: 2px solid #0073aa; padding-bottom: 10px; }
		.info-box { background: #f9f9f9; border-left: 4px solid #0073aa; padding: 15px; margin: 20px 0; }
		.banner-item { border: 2px solid #ddd; margin: 20px 0; padding: 20px; border-radius: 8px; }
		.banner-item h3 { margin-top: 0; color: #0073aa; }
		.banner-image { max-width: 100%; height: auto; border: 1px solid #ddd; border-radius: 4px; margin: 10px 0; }
		.banner-info { background: #f0f0f0; padding: 10px; border-radius: 4px; margin: 10px 0; font-family: monospace; }
		.success { color: #46b450; font-weight: bold; }
		.error { color: #dc3232; font-weight: bold; }
		.warning { color: #ffb900; font-weight: bold; }
		.preview-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0; }
	</style>
</head>
<body>
	<div class="container">
		<h1>🔍 Debug - Banner Images do Carrossel</h1>
		
		<?php
		// Verificar valor da opção
		$banner_images_str = get_option( 'sg_home_banner_images', '' );
		?>
		
		<div class="info-box">
			<h3>📊 Informações da Opção</h3>
			<p><strong>Valor bruto da opção 'sg_home_banner_images':</strong></p>
			<div class="banner-info"><?php echo $banner_images_str ? esc_html( $banner_images_str ) : '<span class="error">VAZIO - Nenhuma imagem configurada</span>'; ?></div>
		</div>
		
		<?php
		// Processar IDs
		$banner_image_ids = array();
		if ( ! empty( $banner_images_str ) ) {
			$ids = explode( ',', $banner_images_str );
			foreach ( $ids as $id ) {
				$id = absint( trim( $id ) );
				if ( $id > 0 ) {
					$image_url = wp_get_attachment_image_url( $id, 'full' );
					if ( $image_url ) {
						$banner_image_ids[] = $id;
					}
				}
			}
		}
		?>
		
		<div class="info-box">
			<h3>📋 Resumo</h3>
			<p><strong>Total de IDs encontrados:</strong> <?php echo count( $banner_image_ids ); ?></p>
			<p><strong>IDs válidos:</strong> <?php echo ! empty( $banner_image_ids ) ? implode( ', ', $banner_image_ids ) : '<span class="error">Nenhum ID válido</span>'; ?></p>
			<?php if ( empty( $banner_image_ids ) ) : ?>
				<p class="warning">⚠️ ATENÇÃO: Nenhuma imagem válida foi encontrada. O carrossel não será exibido!</p>
			<?php else : ?>
				<p class="success">✅ <?php echo count( $banner_image_ids ); ?> imagem(ns) válida(s) encontrada(s). Estas devem aparecer no carrossel.</p>
			<?php endif; ?>
		</div>
		
		<?php if ( ! empty( $banner_image_ids ) ) : ?>
			<h2>🖼️ Imagens que DEVEM aparecer no Carrossel:</h2>
			<div class="preview-grid">
				<?php foreach ( $banner_image_ids as $index => $image_id ) : ?>
					<?php
					$image_url = wp_get_attachment_image_url( $image_id, 'full' );
					$image_thumb = wp_get_attachment_image_url( $image_id, 'medium' );
					$image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
					$image_title = get_the_title( $image_id );
					?>
					<div class="banner-item">
						<h3>Banner <?php echo $index + 1; ?> (ID: <?php echo $image_id; ?>)</h3>
						<?php if ( $image_url ) : ?>
							<img src="<?php echo esc_url( $image_thumb ); ?>" alt="<?php echo esc_attr( $image_alt ?: $image_title ); ?>" class="banner-image" />
							<div class="banner-info">
								<p><strong>URL Completa:</strong><br><?php echo esc_html( $image_url ); ?></p>
								<p><strong>Título:</strong> <?php echo esc_html( $image_title ); ?></p>
								<p><strong>Alt Text:</strong> <?php echo esc_html( $image_alt ?: '(vazio)' ); ?></p>
								<p><strong>Status:</strong> <span class="success">✓ Válido</span></p>
							</div>
						<?php else : ?>
							<p class="error">❌ Imagem não encontrada!</p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<div class="info-box">
				<h3>⚠️ Problema Detectado</h3>
				<p>Nenhuma imagem válida foi encontrada. Possíveis causas:</p>
				<ul>
					<li>A opção 'sg_home_banner_images' está vazia</li>
					<li>Os IDs salvos não correspondem a imagens válidas</li>
					<li>As imagens foram deletadas da biblioteca de mídia</li>
					<li>Os dados não foram salvos corretamente após adicionar os banners no painel</li>
				</ul>
				<p><strong>Solução:</strong></p>
				<ol>
					<li>Vá em <strong>WP Admin → Configurações Gerais - SG Jurídico</strong></li>
					<li>Adicione/verifique os banners nos slots Banner 1, 2 e 3</li>
					<li>Clique em <strong>"Salvar Configurações"</strong> no final da página</li>
					<li>Limpe o cache (LiteSpeed Cache → Purge All)</li>
					<li>Recarregue esta página</li>
				</ol>
			</div>
		<?php endif; ?>
		
		<div class="info-box">
			<h3>🔧 Teste da Função</h3>
			<?php if ( function_exists( 'sg_get_home_banner_images' ) ) : ?>
				<p class="success">✓ Função sg_get_home_banner_images() existe</p>
				<?php 
				$result = sg_get_home_banner_images();
				?>
				<p><strong>Resultado da função:</strong></p>
				<div class="banner-info"><?php var_dump( $result ); ?></div>
			<?php else : ?>
				<p class="error">❌ Função sg_get_home_banner_images() NÃO existe!</p>
			<?php endif; ?>
		</div>
		
		<div class="info-box">
			<h3>📝 Próximos Passos</h3>
			<ol>
				<li>Verifique se os banners acima correspondem ao que você configurou no painel</li>
				<li>Se não corresponder, vá ao painel e salve novamente as configurações</li>
				<li>Limpe o cache completo do WordPress/LiteSpeed</li>
				<li>Limpe o cache do navegador (Ctrl + Shift + R)</li>
				<li>Verifique a home novamente</li>
				<li><strong>Remova este arquivo de debug após resolver o problema</strong></li>
			</ol>
		</div>
	</div>
</body>
</html>

