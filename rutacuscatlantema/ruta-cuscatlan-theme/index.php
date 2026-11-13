<?php get_header(); ?>
<div class="rc-container rc-page-layout">
    <?php if (have_posts()) : ?>
        <header class="rc-page-heading"><span class="rc-eyebrow"><i></i><?php esc_html_e('RUTA CUSCATLÁN', 'ruta-cuscatlan-theme'); ?></span><h1><?php esc_html_e('Historias y lugares', 'ruta-cuscatlan-theme'); ?></h1></header>
        <div class="rc-blog-grid">
            <?php while (have_posts()) : the_post(); ?>
                <article <?php post_class('rc-blog-card'); ?>>
                    <a class="rc-blog-image" href="<?php the_permalink(); ?>"><?php if (has_post_thumbnail()) { the_post_thumbnail('rc-place-card'); } else { echo '<span aria-hidden="true">✦</span>'; } ?></a>
                    <div class="rc-blog-card-body"><span class="rc-blog-date"><?php echo esc_html(get_the_date()); ?></span><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><p><?php echo esc_html(get_the_excerpt()); ?></p><a class="rc-text-link" href="<?php the_permalink(); ?>"><?php esc_html_e('Leer más', 'ruta-cuscatlan-theme'); ?> →</a></div>
                </article>
            <?php endwhile; ?>
        </div>
        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <header class="rc-page-heading"><span class="rc-eyebrow"><i></i><?php esc_html_e('RUTA CUSCATLÁN', 'ruta-cuscatlan-theme'); ?></span><h1><?php esc_html_e('Bienvenido a Ruta Cuscatlán', 'ruta-cuscatlan-theme'); ?></h1><p><?php esc_html_e('Activa el plugin para mostrar el catálogo de destinos y hospedajes.', 'ruta-cuscatlan-theme'); ?></p></header>
    <?php endif; ?>
</div>
<?php get_footer(); ?>
