<?php
/**
 * Single post template.
 *
 * @package Amor_Fraterno
 */

get_header();
?>
<main id="conteudo">
    <?php while (have_posts()) : the_post(); ?>
        <article <?php post_class('single-post'); ?>>
            <header class="single-post-hero">
                <div class="single-post-hero-bg" data-parallax="0.16"></div>
                <div class="single-post-hero-inner" data-aos="fade-up">
                    <?php amor_breadcrumbs(); ?>
                    <a class="single-post-back" href="<?php echo esc_url(amor_posts_url()); ?>">
                        <i data-lucide="arrow-left" aria-hidden="true"></i>
                        Todas as publicações
                    </a>
                    <span class="section-kicker"><?php echo esc_html(get_the_category()[0]->name ?? 'Publicação'); ?></span>
                    <h1><?php the_title(); ?></h1>
                    <?php if (has_excerpt()) : ?><p><?php echo esc_html(get_the_excerpt()); ?></p><?php endif; ?>
                    <div class="single-post-meta" aria-label="Informações da publicação">
                        <span><i data-lucide="calendar-days" aria-hidden="true"></i> <?php echo esc_html(get_the_date()); ?></span>
                        <span><i data-lucide="clock-3" aria-hidden="true"></i> <?php echo esc_html(amor_estimated_reading_time()); ?> min de leitura</span>
                    </div>
                </div>
            </header>

            <div class="single-post-layout">
                <div class="single-post-main">
                    <?php if (has_post_thumbnail()) : ?>
                        <figure class="single-post-cover" data-aos="fade-up">
                            <?php the_post_thumbnail('large', array('loading' => 'eager', 'decoding' => 'async')); ?>
                        </figure>
                    <?php endif; ?>

                    <?php if (is_active_sidebar('single-before')) : ?>
                        <div class="single-plugin-area single-plugin-area-before">
                            <?php dynamic_sidebar('single-before'); ?>
                        </div>
                    <?php endif; ?>
                    <?php do_action('amor_before_single_content'); ?>

                    <div class="single-post-content" data-aos="fade-up">
                        <?php the_content(); ?>
                        <?php
                        wp_link_pages(array(
                            'before' => '<div class="page-links">',
                            'after' => '</div>',
                        ));
                        ?>
                    </div>

                    <?php do_action('amor_after_single_content'); ?>
                    <?php if (is_active_sidebar('single-after')) : ?>
                        <div class="single-plugin-area single-plugin-area-after">
                            <?php dynamic_sidebar('single-after'); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php get_sidebar(); ?>
            </div>

            <section class="single-related section-white" aria-labelledby="related-title">
                <div class="section-heading">
                    <span class="section-kicker">Continue lendo</span>
                    <h2 id="related-title">Publicações relacionadas.</h2>
                </div>
                <div class="post-card-grid">
                    <?php
                    $related = new WP_Query(array(
                        'posts_per_page' => 3,
                        'post__not_in' => array(get_the_ID()),
                        'category__in' => wp_get_post_categories(get_the_ID()),
                        'ignore_sticky_posts' => true,
                    ));

                    while ($related->have_posts()) :
                        $related->the_post();
                        ?>
                        <article class="post-card">
                            <a class="post-card-image" href="<?php the_permalink(); ?>">
                                <?php if (has_post_thumbnail()) { the_post_thumbnail('medium_large', array('loading' => 'lazy', 'decoding' => 'async')); } ?>
                            </a>
                            <div class="post-card-body">
                                <span><?php echo esc_html(get_the_category()[0]->name ?? 'Publicação'); ?></span>
                                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 16)); ?></p>
                            </div>
                        </article>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
            </section>
        </article>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>
