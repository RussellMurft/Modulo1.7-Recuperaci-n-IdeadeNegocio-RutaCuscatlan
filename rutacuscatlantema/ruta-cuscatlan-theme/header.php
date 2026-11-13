<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="rc-skip-link" href="#contenido"><?php esc_html_e('Saltar al contenido', 'ruta-cuscatlan-theme'); ?></a>
<div class="rc-topline"><div class="rc-container rc-topline-inner"><span><span class="rc-live-dot"></span><?php esc_html_e('Una guía para descubrir El Salvador', 'ruta-cuscatlan-theme'); ?></span><span class="rc-topline-right">14 departamentos <i aria-hidden="true">·</i> 6 categorías <i aria-hidden="true">·</i> Experiencias locales</span></div></div>
<header class="rc-header">
    <div class="rc-container rc-header-inner">
        <div class="rc-brand"><?php echo rc_theme_logo_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
        <button class="rc-menu-toggle" type="button" aria-expanded="false" aria-controls="rc-primary-nav"><span></span><span></span><span></span><b class="screen-reader-text"><?php esc_html_e('Abrir menú', 'ruta-cuscatlan-theme'); ?></b></button>
        <nav id="rc-primary-nav" class="rc-primary-nav" aria-label="<?php esc_attr_e('Navegación principal', 'ruta-cuscatlan-theme'); ?>">
            <?php rc_theme_primary_menu(); ?>
            <a class="rc-header-cta" href="<?php echo esc_url(rc_theme_get_suggest_url()); ?>"><?php esc_html_e('Comparte un lugar', 'ruta-cuscatlan-theme'); ?><span aria-hidden="true">↗</span></a>
        </nav>
    </div>
</header>
<main id="contenido" class="rc-main">
