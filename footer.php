<?php
/**
 * Footer template.
 *
 * @package Amor_Fraterno
 */
?>
<footer class="site-footer">
    <div class="footer-inner">
        <?php if (is_active_sidebar('footer-plugins')) : ?>
            <div class="footer-plugin-area">
                <?php dynamic_sidebar('footer-plugins'); ?>
            </div>
        <?php endif; ?>

        <div class="footer-columns">
            <div class="footer-column footer-about">
                <a class="footer-brand" href="<?php echo esc_url(home_url('/#inicio')); ?>" aria-label="<?php bloginfo('name'); ?>">
                    <img class="footer-brand-symbol" src="<?php echo amor_asset('assets/logos/amor-fraterno-simbolo.webp'); ?>" alt="" aria-hidden="true" loading="lazy" decoding="async">
                    <img class="footer-brand-text" src="<?php echo amor_asset('assets/logos/amor-fraterno-texto.webp'); ?>" alt="<?php bloginfo('name'); ?>" loading="lazy" decoding="async">
                </a>
                <p class="footer-tagline"><?php echo amor_html('footer_tagline', 'Acolhimento que transforma. Amor que restaura. Esperança que renasce.'); ?></p>
            </div>

            <div class="footer-column footer-details">
                <div class="footer-info" aria-label="Informações de contato e localização">
                    <div class="footer-info-item">
                        <i data-lucide="map-pin" aria-hidden="true"></i>
                        <span>Situado em</span>
                        <strong><?php echo amor_text('location', 'Aparecida de Goiânia - Goiás'); ?></strong>
                    </div>
                    <div class="footer-info-item">
                        <span class="footer-copy-mark" aria-hidden="true">&copy;</span>
                        <span>Copyright</span>
                        <strong><?php echo esc_html(date_i18n('Y')); ?> <?php bloginfo('name'); ?></strong>
                    </div>
                </div>

                <div class="footer-legal" aria-label="Links legais">
                    <a href="<?php echo esc_url(home_url('/politica-de-privacidade/')); ?>">Política de Privacidade</a>
                    <button type="button" data-cookie-customize>Preferências de cookies</button>
                    <?php
                    if (has_nav_menu('footer')) {
                        wp_nav_menu(array(
                            'theme_location' => 'footer',
                            'container' => false,
                            'items_wrap' => '%3$s',
                            'fallback_cb' => false,
                        ));
                    }
                    ?>
                </div>
            </div>

            <div class="footer-column footer-location">
                <h2>Localização:</h2>
                <div class="footer-map" aria-label="Mapa da localização">
                    <?php echo amor_map_embed(); ?>
                </div>
            </div>
        </div>
    </div>
</footer>

<a class="floating-whatsapp" href="<?php echo amor_whatsapp_url(); ?>" target="_blank" rel="noopener" aria-label="Chamar Amor Fraterno no WhatsApp">
    <i data-lucide="message-circle" aria-hidden="true"></i>
</a>

<div class="lightbox" data-lightbox hidden role="dialog" aria-modal="true" aria-label="Galeria de imagens">
    <button class="lightbox-close" type="button" data-lightbox-close aria-label="Fechar galeria"><i data-lucide="x" aria-hidden="true"></i></button>
    <button class="lightbox-nav lightbox-prev" type="button" data-lightbox-prev aria-label="Imagem anterior"><i data-lucide="chevron-left" aria-hidden="true"></i></button>
    <figure class="lightbox-stage" data-lightbox-stage>
        <img src="<?php echo amor_asset('assets/images/entrada.webp'); ?>" alt="" data-lightbox-image>
        <figcaption><span data-lightbox-title></span><strong data-lightbox-counter></strong></figcaption>
    </figure>
    <button class="lightbox-nav lightbox-next" type="button" data-lightbox-next aria-label="Próxima imagem"><i data-lucide="chevron-right" aria-hidden="true"></i></button>
</div>

<?php amor_cookie_banner(); ?>
<?php wp_footer(); ?>
</body>
</html>
