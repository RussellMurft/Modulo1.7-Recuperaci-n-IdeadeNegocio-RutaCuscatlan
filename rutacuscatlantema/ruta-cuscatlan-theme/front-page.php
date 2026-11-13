<?php
get_header();
?>
<div class="rc-home-wrap">
    <?php if (shortcode_exists('ruta_pagina')) : ?>
        <div class="rc-container rc-plugin-home">
            <?php echo do_shortcode('[ruta_pagina]'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </div>
    <?php else : ?>
        <section class="rc-hero rc-container">
            <div class="rc-hero-copy"><span class="rc-eyebrow"><i></i><?php esc_html_e('EL SALVADOR TE ESPERA', 'ruta-cuscatlan-theme'); ?></span>
                <h1><?php esc_html_e('Hay mucho más por descubrir.', 'ruta-cuscatlan-theme'); ?></h1>
                <p><?php esc_html_e('Explora volcanes, playas, lagos, parques acuáticos, plazas, teatros, pueblos con encanto y experiencias que hacen especial cada rincón de nuestro país.', 'ruta-cuscatlan-theme'); ?></p>
                <a class="rc-button rc-button-gold" href="<?php echo esc_url(rc_theme_get_destination_url()); ?>"><?php esc_html_e('Explorar destinos', 'ruta-cuscatlan-theme'); ?> <span aria-hidden="true">↗</span></a>
            </div>
            <div class="rc-hero-art" aria-hidden="true"><div class="rc-art-sun"></div><div class="rc-art-mountain rc-art-mountain-back"></div><div class="rc-art-mountain rc-art-mountain-front"></div><div class="rc-art-ribbon">DESCUBRE · EXPLORA · VIVE</div></div>
        </section>
        <div class="rc-container rc-plugin-warning"><strong><?php esc_html_e('Activa el plugin Ruta Cuscatlán', 'ruta-cuscatlan-theme'); ?></strong><p><?php esc_html_e('Cuando el plugin esté activo, aquí aparecerán automáticamente los destinos, filtros, fichas y hospedajes.', 'ruta-cuscatlan-theme'); ?></p></div>
    <?php endif; ?>
</div>
<?php get_footer(); ?>
