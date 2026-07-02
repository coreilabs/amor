<?php
/**
 * 404 template.
 *
 * @package Amor_Fraterno
 */

get_header();
?>
<main id="conteudo">
    <section class="blog-hero" aria-labelledby="not-found-title">
        <div class="blog-hero-bg" data-parallax="0.18"></div>
        <div class="blog-hero-inner" data-aos="fade-up">
            <?php amor_breadcrumbs(); ?>
            <span class="section-kicker">Página não encontrada</span>
            <h1 id="not-found-title">Este conteúdo não está disponível.</h1>
            <p>A página pode ter sido removida, estar protegida ou não existir neste endereço.</p>
        </div>
    </section>

    <section class="blog-archive section-white" aria-label="Próximos passos">
        <div class="section-heading">
            <span class="section-kicker">Amor Fraterno</span>
            <h2>Continue navegando pelo site.</h2>
            <p>Você pode voltar para a página inicial ou acessar as publicações abertas do blog.</p>
            <div class="hero-actions not-found-actions">
                <a class="btn btn-primary" href="<?php echo esc_url(home_url('/')); ?>">Ir para o início</a>
                <a class="btn btn-secondary" href="<?php echo esc_url(amor_posts_url()); ?>">Ver publicações</a>
            </div>
        </div>
    </section>
</main>
<?php get_footer(); ?>
