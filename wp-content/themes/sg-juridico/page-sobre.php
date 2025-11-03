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
						the_content();

						wp_link_pages(
							array(
								'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'sg-juridico' ),
								'after'  => '</div>',
							)
						);
						?>

						<?php if ( ! have_posts() ) : ?>
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
						<?php endif; ?>
					</div>
				</div><!-- .entry-content -->

				<?php if ( get_edit_post_link() ) : ?>
					<footer class="entry-footer">
						<div class="container">
							<?php
							edit_post_link(
								sprintf(
									wp_kses(
										/* translators: %s: Name of current post. Only visible to screen readers */
										__( 'Edit <span class="screen-reader-text">%s</span>', 'sg-juridico' ),
										array(
											'span' => array(
												'class' => array(),
											),
										)
									),
									get_the_title()
								),
								'<span class="edit-link">',
								'</span>'
							);
							?>
						</div>
					</footer><!-- .entry-footer -->
				<?php endif; ?>
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
get_sidebar();
get_footer();

