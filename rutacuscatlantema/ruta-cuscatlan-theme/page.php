<?php get_header(); ?>
<div class="rc-container rc-page-layout">
    <?php while (have_posts()) : the_post(); ?>
        <header class="rc-page-heading"><span class="rc-eyebrow"><i></i><?php esc_html_e('RUTA CUSCATLÁN', 'ruta-cuscatlan-theme'); ?></span><h1><?php the_title(); ?></h1>
            <?php if (has_excerpt()) : ?><p><?php echo esc_html(get_the_excerpt()); ?></p><?php endif; ?>
        </header>
        <article <?php post_class('rc-page-content'); ?>><?php the_content(); ?></article>
    <?php endwhile; ?>
</div>
<?php get_footer(); ?>
