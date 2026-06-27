<?php
/**
 * Blog sidebar.
 *
 * @package Amor_Fraterno
 */
?>
<aside class="blog-sidebar single-post-sidebar" aria-label="Sidebar do blog" data-aos="fade-left">
    <?php if (is_active_sidebar('single-sidebar-top')) : ?>
        <?php dynamic_sidebar('single-sidebar-top'); ?>
    <?php endif; ?>

    <div class="sidebar-widget sidebar-search">
        <h2>Buscar</h2>
        <?php get_search_form(); ?>
    </div>

    <?php if (is_active_sidebar('sidebar-blog')) : ?>
        <?php dynamic_sidebar('sidebar-blog'); ?>
    <?php else : ?>
        <div class="sidebar-widget">
            <h2>Categorias</h2>
            <ul class="sidebar-list">
                <?php wp_list_categories(array('title_li' => '', 'show_count' => false)); ?>
            </ul>
        </div>
        <div class="sidebar-widget">
            <h2>Recentes</h2>
            <div class="sidebar-recent-posts">
                <?php
                $recent = new WP_Query(array('posts_per_page' => 3, 'ignore_sticky_posts' => true));
                while ($recent->have_posts()) :
                    $recent->the_post();
                    ?>
                    <a href="<?php the_permalink(); ?>">
                        <?php if (has_post_thumbnail()) { the_post_thumbnail('thumbnail', array('loading' => 'lazy', 'decoding' => 'async')); } ?>
                        <span><?php the_title(); ?></span>
                    </a>
                    <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="sidebar-widget sidebar-cta">
        <i data-lucide="message-circle" aria-hidden="true"></i>
        <h2>Precisa de orientação?</h2>
        <p>Converse com a equipe da Amor Fraterno pelo WhatsApp.</p>
        <a class="btn btn-primary" href="<?php echo amor_whatsapp_url(); ?>" target="_blank" rel="noopener">Falar no WhatsApp</a>
    </div>

    <?php if (is_active_sidebar('single-sidebar-bottom')) : ?>
        <?php dynamic_sidebar('single-sidebar-bottom'); ?>
    <?php endif; ?>
</aside>
