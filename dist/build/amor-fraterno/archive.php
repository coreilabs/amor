<?php
/**
 * Archive template.
 *
 * @package Amor_Fraterno
 */

get_header();
?>
<main id="conteudo">
    <section class="blog-hero" aria-labelledby="blog-title">
        <div class="blog-hero-bg" data-parallax="0.18"></div>
        <div class="blog-hero-inner" data-aos="fade-up">
            <?php amor_breadcrumbs(); ?>
            <span class="section-kicker">Publicações</span>
            <h1 id="blog-title">
                <?php
                if (is_search()) {
                    printf('Resultados para: %s', esc_html(get_search_query()));
                } elseif (is_home() || amor_is_posts_archive()) {
                    echo 'Conteúdos para famílias, acolhidos e recomeços.';
                } elseif (is_category()) {
                    single_cat_title();
                } else {
                    echo wp_kses_post(get_the_archive_title());
                }
                ?>
            </h1>
            <?php if (!is_category() && get_the_archive_description()) : ?>
                <p><?php echo wp_kses_post(get_the_archive_description()); ?></p>
            <?php else : ?>
                <p>Orientações sobre acolhimento, tratamento, rotina terapêutica e apoio para famílias.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="blog-archive section-white" aria-label="Todas as publicações">
        <div class="blog-layout">
            <div class="blog-main">
                <div class="blog-toolbar" data-aos="fade-up">
                    <div>
                        <span>Blog Amor Fraterno</span>
                        <strong><?php echo is_search() ? 'Resultado da busca' : 'Todas as publicações'; ?></strong>
                    </div>
                    <a href="<?php echo esc_url(home_url('/#publicacoes')); ?>">
                        <i data-lucide="arrow-left" aria-hidden="true"></i>
                        Voltar para a home
                    </a>
                </div>

                <div class="archive-post-grid">
                    <?php if (have_posts()) : ?>
                        <?php while (have_posts()) : the_post(); ?>
                            <article <?php post_class('archive-post-card'); ?> data-aos="fade-up">
                                <a class="archive-post-image" href="<?php the_permalink(); ?>">
                                    <?php if (has_post_thumbnail()) { the_post_thumbnail('medium_large', array('loading' => 'lazy', 'decoding' => 'async')); } else { echo '<img src="' . amor_asset('assets/images/equipe-atendimento.webp') . '" alt="" loading="lazy" decoding="async">'; } ?>
                                </a>
                                <div class="archive-post-body">
                                    <span><?php echo esc_html(get_the_category()[0]->name ?? 'Publicação'); ?></span>
                                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                    <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 24)); ?></p>
                                    <a class="post-read-more" href="<?php the_permalink(); ?>">Ler publicação</a>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <article class="archive-post-card">
                            <div class="archive-post-body">
                                <h2>Nenhuma publicação encontrada</h2>
                                <p>Novos conteúdos aparecerão aqui assim que forem publicados.</p>
                            </div>
                        </article>
                    <?php endif; ?>
                </div>

                <?php the_posts_pagination(array('prev_text' => 'Anteriores', 'next_text' => 'Próximas')); ?>
            </div>

            <?php get_sidebar(); ?>
        </div>
    </section>
</main>
<?php get_footer(); ?>
