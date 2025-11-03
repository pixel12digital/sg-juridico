<?php
/**
 * Main template file
 *
 * @package SG_Juridico
 */

get_header();
?>

<main id="main" class="site-main">
	<div class="container">

		<div class="site-main-wrapper">
			<div class="posts-container">
				<?php if ( is_home() || is_front_page() ) : ?>
					<?php
					// Carrossel alimentado pelo painel
					get_template_part( 'template-parts/home', 'carousel' );
					?>
				<?php endif; ?>
				<?php if ( have_posts() ) : ?>
					<?php if ( is_singular() ) : ?>
						<?php
						while ( have_posts() ) :
							the_post();
							get_template_part( 'template-parts/content', get_post_type() );
						endwhile;
						?>
					<?php else : ?>
						<div class="posts-list">
							<?php
							while ( have_posts() ) :
								the_post();
								get_template_part( 'template-parts/content', get_post_type() );
							endwhile;
							?>
						</div>

						<?php
						// Pagination (somente em listas)
						the_posts_pagination(
							array(
								'mid_size'  => 2,
								'prev_text' => sprintf(
									'<span class="nav-prev-text">%s</span>',
									__( '← Anterior', 'sg-juridico' )
								),
								'next_text' => sprintf(
									'<span class="nav-next-text">%s</span>',
									__( 'Próximo →', 'sg-juridico' )
								),
							)
						);
						?>
					<?php endif; ?>
				<?php else : ?>
					<?php get_template_part( 'template-parts/content', 'none' ); ?>
				<?php endif; ?>
			</div>

				<?php get_sidebar(); ?>
		</div>
	</div>
</main>

<?php
get_footer();

