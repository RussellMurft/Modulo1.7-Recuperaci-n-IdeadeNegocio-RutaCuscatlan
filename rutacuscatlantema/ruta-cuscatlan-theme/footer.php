</main>
<footer class="rc-footer">
    <div class="rc-container rc-footer-main">
        <div class="rc-footer-brand">
            <?php echo rc_theme_logo_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <p><?php esc_html_e('Inspiramos a descubrir los paisajes, pueblos, sabores y rincones que hacen único a El Salvador.', 'ruta-cuscatlan-theme'); ?></p>
        </div>
        <div class="rc-footer-links"><h2><?php esc_html_e('Explora', 'ruta-cuscatlan-theme'); ?></h2>
            <a href="<?php echo esc_url(home_url('/explorar/')); ?>"><?php esc_html_e('Destinos turísticos', 'ruta-cuscatlan-theme'); ?></a>
            <a href="<?php echo esc_url(rc_theme_get_stay_url()); ?>"><?php esc_html_e('Alojamiento', 'ruta-cuscatlan-theme'); ?></a>
            <a href="<?php echo esc_url(home_url('/nosotros/')); ?>"><?php esc_html_e('Nosotros', 'ruta-cuscatlan-theme'); ?></a>
            <a href="<?php echo esc_url(rc_theme_get_suggest_url()); ?>"><?php esc_html_e('Sugerir un lugar', 'ruta-cuscatlan-theme'); ?></a>
        </div>
        <div class="rc-footer-social"><?php rc_theme_social_links(); ?></div>
        <div class="rc-footer-note"><span class="rc-footer-eyebrow"><?php esc_html_e('VIAJA CON CURIOSIDAD', 'ruta-cuscatlan-theme'); ?></span><h2><?php esc_html_e('Tu próxima historia empieza aquí.', 'ruta-cuscatlan-theme'); ?></h2><a class="rc-footer-button" href="<?php echo esc_url(rc_theme_get_destination_url()); ?>"><?php esc_html_e('Comenzar a explorar', 'ruta-cuscatlan-theme'); ?> <span aria-hidden="true">→</span></a></div>
    </div>
    <div class="rc-container rc-footer-bottom"><span>© <?php echo esc_html(wp_date('Y')); ?> <?php bloginfo('name'); ?>. <?php esc_html_e('Todos los derechos reservados.', 'ruta-cuscatlan-theme'); ?></span><span><?php esc_html_e('Hecho para descubrir El Salvador', 'ruta-cuscatlan-theme'); ?> <b aria-hidden="true">♥</b></span></div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
