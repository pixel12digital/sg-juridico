<?php
/**
 * Template Name: Página Contato
 *
 * @package SG_Juridico
 */

get_header();
?>

<style>
/* CSS inline para garantir que os espaços sejam removidos */
body.page-template-page-contato #main.site-main,
body.page.contato #main.site-main,
body.page-template-page-contato #primary,
body.page.contato #primary {
	padding-bottom: 0 !important;
	margin-bottom: 0 !important;
	min-height: auto !important;
}
body.page-template-page-contato .entry-content,
body.page.contato .entry-content,
body.page-template-page-contato article.page,
body.page.contato article.page {
	padding-top: 0 !important;
	padding-bottom: 0 !important;
	margin-bottom: 0 !important;
}
body.page-template-page-contato .site-main .container,
body.page.contato .site-main .container,
body.page-template-page-contato .site-main-wrapper,
body.page.contato .site-main-wrapper {
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
				<div class="entry-content">
					<div class="container contact-container">
						<div class="contact-page-wrapper">
							<?php
							$content = get_the_content();
							$has_content = ! empty( trim( $content ) );
							?>
							
							<!-- Seção Entre em Contato - Ocupa 3 colunas -->
							<div class="contact-section-full">
								<div class="contact-content">
									<h2>Entre em Contato</h2>
									<p>Tem dúvidas sobre nossos cursos? Estamos prontos para ajudar!</p>

									<div class="contact-info">
										<h3>Informações de Contato</h3>
										<div class="contact-items-grid">
											<div class="contact-item">
												<strong>E-mail:</strong>
												<p>contato@sgjuridico.com.br</p>
											</div>
											<div class="contact-item">
												<strong>WhatsApp:</strong>
												<p>+55 (00) 00000-0000</p>
											</div>
											<div class="contact-item">
												<strong>Horário de Atendimento:</strong>
												<p>Segunda a Sexta: 9h às 18h<br>Sábado: 9h às 13h</p>
											</div>
										</div>
									</div>

									<!-- Formulário de Contato -->
									<div class="contact-form-section">
										<h3>Envie sua Mensagem</h3>
										<form id="contact-form" class="contact-form" method="post" action="">
											<?php wp_nonce_field( 'sg_contact_form', 'sg_contact_nonce' ); ?>
											
											<div class="form-row">
												<div class="form-group">
													<label for="contact-name">Nome <span class="required">*</span></label>
													<input type="text" id="contact-name" name="contact_name" required aria-required="true">
												</div>
												<div class="form-group">
													<label for="contact-email">E-mail <span class="required">*</span></label>
													<input type="email" id="contact-email" name="contact_email" required aria-required="true">
												</div>
											</div>
											
											<div class="form-row">
												<div class="form-group">
													<label for="contact-phone">Telefone</label>
													<input type="tel" id="contact-phone" name="contact_phone" placeholder="(00) 00000-0000">
												</div>
												<div class="form-group">
													<label for="contact-subject">Assunto <span class="required">*</span></label>
													<select id="contact-subject" name="contact_subject" required aria-required="true">
														<option value="">Selecione um assunto</option>
														<option value="duvida">Dúvida sobre cursos</option>
														<option value="suporte">Suporte técnico</option>
														<option value="parceria">Parcerias</option>
														<option value="outro">Outro</option>
													</select>
												</div>
											</div>
											
											<div class="form-group">
												<label for="contact-message">Mensagem <span class="required">*</span></label>
												<textarea id="contact-message" name="contact_message" rows="5" required aria-required="true"></textarea>
											</div>
											
											<div class="form-group form-submit">
												<button type="submit" class="contact-submit-btn">
													<span class="btn-text">Enviar Mensagem</span>
													<span class="btn-loading" style="display: none;">Enviando...</span>
												</button>
											</div>
											
											<div class="form-message" id="form-message" style="display: none;"></div>
										</form>
									</div>

									<h3>Redes Sociais</h3>
									<div class="social-links">
										<a href="#" target="_blank" rel="noopener" class="social-icon" aria-label="Facebook">
											<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" fill="currentColor"/>
											</svg>
										</a>
										<a href="<?php echo esc_url( sg_get_company_info( 'instagram' ) ); ?>" target="_blank" rel="noopener" class="social-icon" aria-label="Instagram">
											<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" fill="currentColor"/>
											</svg>
										</a>
										<a href="#" target="_blank" rel="noopener" class="social-icon" aria-label="YouTube">
											<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" fill="currentColor"/>
											</svg>
										</a>
									</div>
								</div>
							</div>
							
							<!-- Conteúdo da página (políticas, etc.) - Organizado em 3 colunas -->
							<?php if ( $has_content ) : ?>
								<div class="contact-page-content">
									<?php
									// Filtrar conteúdo duplicado - remover seção "Fale Conosco" que duplica informações já exibidas acima
									$filtered_content = $content;
									
									// Remover seção "Fale Conosco" completa (título + conteúdo até próximo h2/h3 ou fim)
									// Esta seção duplica informações já mostradas na seção "Entre em Contato"
									$filtered_content = preg_replace(
										'/<h2[^>]*>.*?Fale\s+Conosco.*?<\/h2>\s*(?:<p>.*?<\/p>\s*)*(?:.*?contato@sgjuridico\.com\.br.*?|.*?\(61\)\s*92000-7184.*?|.*?\+55.*?).*?(?=<h2|<h3|<\/div>|$)/is',
										'',
										$filtered_content
									);
									
									// Remover parágrafos isolados que contenham apenas informações de contato duplicadas
									$filtered_content = preg_replace(
										'/<p[^>]*>\s*(?:.*?(?:contato@sgjuridico\.com\.br|\(61\)\s*92000-7184|\+55\s*\(\d{2}\)\s*\d{4,5}-?\d{4}).*?)\s*<\/p>/is',
										'',
										$filtered_content
									);
									
									echo apply_filters( 'the_content', $filtered_content );
									?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div><!-- .entry-content -->
			</article><!-- #post-<?php the_ID(); ?> -->

		<?php endwhile; // End of the loop. ?>

	</main><!-- #main -->
</div><!-- #primary -->

<?php
get_sidebar();
get_footer();

