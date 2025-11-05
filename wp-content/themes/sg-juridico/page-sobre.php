<?php
/**
 * Template Name: Página Sobre
 *
 * @package SG_Juridico
 */

get_header();
?>

<style>
/* CSS inline para remover espaços em branco excessivos */
body.page-template-page-sobre #main.site-main,
body.page.sobre #main.site-main {
	padding-bottom: 0 !important;
	min-height: auto !important;
}
body.page-template-page-sobre .entry-content,
body.page.sobre .entry-content,
body.page-template-page-sobre article.page,
body.page.sobre article.page {
	padding-bottom: 0 !important;
	margin-bottom: 0 !important;
}
body.page-template-page-sobre .site-main .container,
body.page.sobre .site-main .container,
body.page-template-page-sobre .site-main-wrapper,
body.page.sobre .site-main-wrapper {
	min-height: auto !important;
	height: auto !important;
}
/* Remover sidebar na página sobre */
body.page-template-page-sobre #secondary.widget-area,
body.page.sobre #secondary.widget-area {
	display: none !important;
}
body.page-template-page-sobre .site-main-wrapper,
body.page.sobre .site-main-wrapper {
	flex-direction: column !important;
}
body.page-template-page-sobre .posts-container,
body.page.sobre .posts-container {
	width: 100% !important;
	max-width: 100% !important;
}
/* Garantir que o conteúdo ocupe toda a largura */
body.page-template-page-sobre #primary.content-area,
body.page.sobre #primary.content-area {
	width: 100% !important;
	max-width: 100% !important;
}
body.page-template-page-sobre .entry-content .container,
body.page.sobre .entry-content .container {
	max-width: 1200px;
	margin: 0 auto;
	padding: 0 20px;
}
</style>

<div id="primary" class="content-area">
	<main id="main" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post();
			?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="entry-header">
					<div class="container">
						<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
					</div>
				</header><!-- .entry-header -->

				<div class="entry-content">
					<div class="container">
						<?php
						$content = get_the_content();
						
						if ( empty( trim( $content ) ) ) :
							// Se não há conteúdo, mostrar conteúdo padrão
							?>
							<div class="about-content">
								<h2>Sobre o SG Jurídico</h2>
								<p>O SG Jurídico é uma plataforma especializada em cursos preparatórios para concursos públicos na área jurídica, com foco em magistratura, ministério público, delegado, procurador e demais cargos da área jurídica.</p>

								<h3>Nossa Missão</h3>
								<p>Oferecer materiais didáticos de alta qualidade e metodologia eficaz para auxiliar candidatos a alcançarem seus objetivos de aprovação em concursos públicos.</p>

								<h3>Nossos Cursos</h3>
								<p>Trabalhamos com cursos especializados em:</p>
								<ul>
									<li>Método SG para diversos tribunais</li>
									<li>Lei Seca</li>
									<li>Análise verticalizada de editais</li>
									<li>Súmulas sistematizadas</li>
									<li>Jurisprudência comentada</li>
								</ul>

								<h3>Nossa Equipe</h3>
								<p>Contamos com professores especializados e material atualizado constantemente, garantindo que nossos alunos tenham acesso às melhores informações para os concursos.</p>
							</div>
							<?php
						else :
							the_content();

							wp_link_pages(
								array(
									'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'sg-juridico' ),
									'after'  => '</div>',
								)
							);
						endif;
						?>
					</div>
				</div><!-- .entry-content -->
			</article><!-- #post-<?php the_ID(); ?> -->

			<?php
			// Se há comentários e estão abertos ou há pelo menos um comentário, carregar o template de comentários.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile; // End of the loop.
		?>

	</main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();

