<?php get_header(); ?>
<div class="rc-container rc-page-layout">
    <?php $term = get_queried_object(); ?>
    <header class="rc-page-heading"><span class="rc-eyebrow"><i></i><?php esc_html_e('EXPLORA POR CATEGORÍA', 'ruta-cuscatlan-theme'); ?></span><h1><?php echo esc_html(single_term_title('', false)); ?></h1>
        <?php if (!empty($term->description)) : ?><p><?php echo esc_html($term->description); ?></p><?php else : ?><p><?php esc_html_e('Encuentra lugares que encajan con esta forma de descubrir El Salvador.', 'ruta-cuscatlan-theme'); ?></p><?php endif; ?>
    </header>
    <?php if (have_posts()) : ?>
        <div class="rc-blog-grid">
            <?php $rc_seen_places = array(); while (have_posts()) : the_post();
                $rc_key = function_exists('ruta_lugar_normalizar_titulo') ? ruta_lugar_normalizar_titulo(get_the_title()) : sanitize_title(remove_accents(get_the_title()));
                if (isset($rc_seen_places[$rc_key])) { continue; }
                $rc_seen_places[$rc_key] = true; ?>
                <article <?php post_class('rc-blog-card'); ?>>
                    <?php $rc_photo = (!has_post_thumbnail() && function_exists('ruta_foto_lugar_markup')) ? ruta_foto_lugar_markup(get_the_ID(), 'card') : ''; ?>
                    <?php if ($rc_photo) : ?><div class="ruta rc-tax-photo"><?php echo $rc_photo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
                    <?php else : ?><a class="rc-blog-image" href="<?php the_permalink(); ?>"><?php if (has_post_thumbnail()) { the_post_thumbnail('rc-place-card'); } else { echo '<span aria-hidden="true">✦</span>'; } ?></a><?php endif; ?>
                    <div class="rc-blog-card-body"><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><p><?php echo esc_html(get_the_excerpt()); ?></p><?php if (function_exists('ruta_badge_abierto')) { echo ruta_badge_abierto(get_the_ID()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php } ?><a class="rc-text-link" href="<?php the_permalink(); ?>"><?php esc_html_e('Explorar este lugar', 'ruta-cuscatlan-theme'); ?> →</a></div>
                </article>
            <?php endwhile; ?>
        </div>
        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <div class="rc-plugin-warning"><strong><?php esc_html_e('Todavía no hay lugares en esta categoría.', 'ruta-cuscatlan-theme'); ?></strong><p><?php esc_html_e('Prueba con otra categoría o vuelve al catálogo general.', 'ruta-cuscatlan-theme'); ?></p><a class="rc-button" href="<?php echo esc_url(rc_theme_get_destination_url()); ?>"><?php esc_html_e('Ver todos los destinos', 'ruta-cuscatlan-theme'); ?> →</a></div>
    <?php endif; ?>
</div>
<?php get_footer(); ?>
