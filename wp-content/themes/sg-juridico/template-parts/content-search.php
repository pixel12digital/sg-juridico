<?php
/**
 * Template part for displaying results in search pages
 *
 * @package SG_Juridico
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

		<?php if ( 'post' === get_post_type() ) : ?>
		<div class="entry-meta">
			<?php
			sg_posted_on();
			sg_posted_by();
			?>
		</div>
		<?php endif; ?>
	</header>

	<?php sg_post_thumbnail(); ?>

	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div>

	<footer class="entry-footer">
		<?php if ( in_array( get_post_type(), array( 'sg_eventos', 'etn', 'tribe_events' ) ) ) : ?>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'etn' ) ?: home_url( '/eventos' ) ); ?>" class="btn-view-all-events" style="display: inline-block; padding: 10px 25px; background-color: #0ea5e9; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">
				<?php esc_html_e( 'Ver todos os concursos', 'sg-juridico' ); ?>
			</a>
		<?php else : ?>
			<a href="<?php the_permalink(); ?>" class="btn-read-more" style="display: inline-block; padding: 10px 25px; background-color: #0ea5e9; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">
				<?php esc_html_e( 'Ler mais', 'sg-juridico' ); ?>
			</a>
		<?php endif; ?>
	</footer>
</article>

