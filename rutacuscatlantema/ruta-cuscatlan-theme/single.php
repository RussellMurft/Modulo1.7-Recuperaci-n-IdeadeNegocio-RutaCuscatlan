<?php get_header(); ?>
<div class="rc-container rc-page-layout"><div class="rc-page-content">
<?php while (have_posts()) : the_post(); ?>
    <article <?php post_class('rc-standard-article'); ?>>
        <header class="rc-page-heading"><span class="rc-eyebrow"><i></i><?php echo esc_html(get_post_type_object(get_post_type())->labels->singular_name ?? __('Artículo', 'ruta-cuscatlan-theme')); ?></span><h1><?php the_title(); ?></h1><p><?php echo esc_html(get_the_date()); ?></p></header>
        <?php if (has_post_thumbnail()) : ?><figure class="rc-standard-cover"><?php the_post_thumbnail('large'); ?></figure><?php endif; ?>
        <div class="rc-prose"><?php the_content(); ?></div>
    </article>
<?php endwhile; ?>
</div></div>
<?php get_footer(); ?>
