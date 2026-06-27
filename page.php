<?php
/**
 * Page template.
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
                    <span class="section-kicker">Página</span>
                    <h1><?php the_title(); ?></h1>
                </div>
            </header>
            <div class="single-post-layout">
                <div class="single-post-main">
                    <?php if (has_post_thumbnail()) : ?>
                        <figure class="single-post-cover" data-aos="fade-up">
                            <?php the_post_thumbnail('large', array('loading' => 'eager', 'decoding' => 'async')); ?>
                        </figure>
                    <?php endif; ?>
                    <div class="single-post-content" data-aos="fade-up">
                        <?php the_content(); ?>
                    </div>
                </div>
                <?php get_sidebar(); ?>
            </div>
        </article>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>
