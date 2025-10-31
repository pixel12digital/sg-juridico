<?php
/**
 * Template part for displaying post content
 *
 * @package SG_Juridico
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	// Thumbnail antes do header em listas/arquivos (não em single)
	if ( ! is_singular() ) :
		sg_post_thumbnail();
	endif;
	?>

	<header class="entry-header">
		<?php
		// Ocultar título na página inicial (Home)
		if ( ! is_front_page() ) :
			if ( is_singular() ) :
				the_title( '<h1 class="entry-title">', '</h1>' );
			else :
				the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
			endif;
		endif;

		if ( 'post' === get_post_type() ) :
			?>
			<div class="entry-meta">
				<?php
				sg_posted_on();
				sg_posted_by();
				?>
			</div>
			<?php
		endif;
		?>
	</header>

	<?php
	// Thumbnail depois do header em single posts/pages
	if ( is_singular() ) :
		sg_post_thumbnail();
	endif;
	?>

	<div class="entry-content">
		<?php
		if ( is_singular() ) :
			// Não exibir conteúdo da página inicial (texto "hero" será renderizado pelo banner)
			if ( ! is_front_page() ) :
				the_content();
				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'sg-juridico' ),
						'after'  => '</div>',
					)
				);
			endif;
		else :
			// Em listas, mostrar excerpt
			if ( has_excerpt() ) :
				the_excerpt();
			else :
				// Se não tem excerpt, mostrar conteúdo limitado
				$content = get_the_content();
				$content = wp_strip_all_tags( $content );
				$content = wp_trim_words( $content, 25, '...' );
				echo '<p>' . esc_html( $content ) . '</p>';
			endif;
			
			?>
			<a href="<?php echo esc_url( get_permalink() ); ?>" class="read-more">
				<?php esc_html_e( 'Ler mais', 'sg-juridico' ); ?>
			</a>
			<?php
		endif;
		?>
	</div>

	<?php if ( ! is_singular() || ( is_singular() && 'post' === get_post_type() ) ) : ?>
		<footer class="entry-footer">
			<?php sg_entry_footer(); ?>
		</footer>
	<?php endif; ?>
</article>

