<?php
/**
 * Template Name: Área da Família
 *
 * @package Amor_Fraterno
 */

$family_password_error = amor_family_handle_password_submit();

if (!amor_is_family_area_page() && amor_family_has_access()) {
    wp_safe_redirect(home_url(add_query_arg(array(), $GLOBALS['wp']->request ? '/' . $GLOBALS['wp']->request . '/' : '/')));
    exit;
}

$family_message_status = amor_family_handle_message_submit();

get_header();
?>
<main id="conteudo">
    <?php if (!amor_family_has_access()) : ?>
        <?php echo amor_family_password_form($family_password_error); ?>
    <?php else : ?>
        <section class="family-hero" aria-labelledby="family-title">
            <div class="family-hero-bg" data-parallax="0.14"></div>
            <div class="family-hero-inner" data-aos="fade-up">
                <?php amor_breadcrumbs(); ?>
                <span class="section-kicker">Espaço reservado</span>
                <h1 id="family-title">Área da Família</h1>
                <p><?php echo esc_html(amor_get('family_intro', 'Um espaço reservado para aproximar família e acolhidos, com recados aprovados pela equipe e informações da rotina da casa.')); ?></p>
                <div class="family-hero-actions">
                    <a class="btn btn-primary" href="#novo-recado"><i data-lucide="pen-line" aria-hidden="true"></i> Escrever novo recado</a>
                    <a class="btn btn-secondary" href="#cronograma"><i data-lucide="calendar-days" aria-hidden="true"></i> Ver cronograma</a>
                </div>
            </div>
        </section>

        <section class="family-board section-white" aria-labelledby="family-board-title">
            <div class="section-heading" data-aos="fade-up">
                <span class="section-kicker">Mural de recados</span>
                <h2 id="family-board-title">Mensagens que chegam com carinho.</h2>
                <p>Recados aprovados pela equipe para compartilhar afeto, saudade e incentivo com os internos.</p>
            </div>

            <div class="family-board-layout">
                <div class="family-message-grid">
                    <?php
                    $messages = new WP_Query(array(
                        'post_type' => 'amor_family_message',
                        'post_status' => 'publish',
                        'posts_per_page' => 12,
                        'ignore_sticky_posts' => true,
                    ));

                    if ($messages->have_posts()) :
                        while ($messages->have_posts()) :
                            $messages->the_post();
                            $recipient = get_post_meta(get_the_ID(), '_amor_message_recipient', true);
                            $relative = get_post_meta(get_the_ID(), '_amor_message_relative', true);
                            ?>
                            <article class="family-note" data-aos="fade-up">
                                <?php if (has_post_thumbnail()) : ?>
                                    <button class="family-note-photo" type="button" data-lightbox-src="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'large')); ?>" data-lightbox-caption="<?php echo esc_attr($recipient ? 'Recado para ' . $recipient : get_the_title()); ?>" data-lightbox-group="recados">
                                        <?php the_post_thumbnail('medium_large', array('loading' => 'lazy', 'decoding' => 'async')); ?>
                                    </button>
                                <?php endif; ?>
                                <div class="family-note-body">
                                    <span class="family-note-to">Para <?php echo esc_html($recipient ?: 'um acolhido especial'); ?></span>
                                    <div class="family-note-text"><?php the_content(); ?></div>
                                    <footer>Com carinho, <strong><?php echo esc_html($relative ?: 'familia'); ?></strong></footer>
                                </div>
                            </article>
                            <?php
                        endwhile;
                        wp_reset_postdata();
                    else :
                        ?>
                        <article class="family-empty">
                            <i data-lucide="heart-handshake" aria-hidden="true"></i>
                            <h3>O mural está pronto para receber os primeiros recados.</h3>
                            <p>Assim que a equipe aprovar uma mensagem, ela aparece aqui.</p>
                        </article>
                    <?php endif; ?>
                </div>

                <aside class="family-form-panel" id="novo-recado" data-aos="fade-up">
                    <span class="section-kicker">Novo recado</span>
                    <h2>Enviar uma mensagem</h2>
                    <p><?php echo esc_html(amor_get('family_notice', 'Os recados passam por aprovação manual antes de aparecerem no mural.')); ?></p>

                    <?php if (!empty($family_message_status['message'])) : ?>
                        <div class="family-alert family-alert-<?php echo esc_attr($family_message_status['type']); ?>"><?php echo esc_html($family_message_status['message']); ?></div>
                    <?php endif; ?>

                    <form class="family-message-form" method="post" enctype="multipart/form-data">
                        <?php wp_nonce_field('amor_family_message', 'amor_family_message_nonce'); ?>
                        <input type="hidden" name="amor_family_message_action" value="1">

                        <label for="amor_recipient">Para quem</label>
                        <input id="amor_recipient" name="amor_recipient" type="text" required placeholder="Nome do interno">

                        <label for="amor_relative">Seu nome</label>
                        <input id="amor_relative" name="amor_relative" type="text" required placeholder="Ex.: Mae, pai, irma...">

                        <label for="amor_message">Mensagem</label>
                        <textarea id="amor_message" name="amor_message" rows="6" required placeholder="Escreva seu recado com carinho"></textarea>

                        <label for="amor_photo">Foto</label>
                        <input id="amor_photo" name="amor_photo" type="file" accept="image/*" capture="environment">

                        <button class="btn btn-primary" type="submit"><i data-lucide="send" aria-hidden="true"></i> Enviar para aprovação</button>
                    </form>
                </aside>
            </div>
        </section>

        <section class="family-schedule" id="cronograma" aria-labelledby="family-schedule-title">
            <div class="section-heading" data-aos="fade-up">
                <span class="section-kicker">Cronograma da casa</span>
                <h2 id="family-schedule-title">Rotina compartilhada com clareza.</h2>
            </div>

            <div class="family-schedule-list">
                <?php
                $schedule = new WP_Query(array(
                    'post_type' => 'amor_family_schedule',
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                    'orderby' => array('menu_order' => 'ASC', 'date' => 'DESC'),
                ));

                if ($schedule->have_posts()) :
                    while ($schedule->have_posts()) :
                        $schedule->the_post();
                        $period = get_post_meta(get_the_ID(), '_amor_schedule_period', true);
                        $time = get_post_meta(get_the_ID(), '_amor_schedule_time', true);
                        $audience = get_post_meta(get_the_ID(), '_amor_schedule_audience', true);
                        ?>
                        <article class="family-schedule-item" data-aos="fade-up">
                            <div class="family-schedule-time">
                                <strong><?php echo esc_html($time ?: 'Horário a definir'); ?></strong>
                                <span><?php echo esc_html($period ?: 'Rotina da casa'); ?></span>
                            </div>
                            <div class="family-schedule-content">
                                <h3><?php the_title(); ?></h3>
                                <?php if ($audience) : ?><span><?php echo esc_html($audience); ?></span><?php endif; ?>
                                <div><?php the_content(); ?></div>
                            </div>
                        </article>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    ?>
                    <article class="family-empty family-empty-wide">
                        <i data-lucide="calendar-plus" aria-hidden="true"></i>
                        <h3>Cronograma em configuração.</h3>
                        <p>A equipe pode adicionar atividades no menu Área da Família > Cronograma.</p>
                    </article>
                <?php endif; ?>
            </div>
        </section>

        <section class="family-posts section-white" aria-labelledby="family-posts-title">
            <div class="section-heading" data-aos="fade-up">
                <span class="section-kicker">Publicações exclusivas</span>
                <h2 id="family-posts-title">Conteudos para acompanhar a jornada.</h2>
            </div>
            <div class="post-card-grid">
                <?php
                $family_posts = new WP_Query(array(
                    'post_type' => 'post',
                    'post_status' => 'publish',
                    'posts_per_page' => 3,
                    'category_name' => amor_family_category_slug(),
                    'ignore_sticky_posts' => true,
                ));

                if ($family_posts->have_posts()) :
                    while ($family_posts->have_posts()) :
                        $family_posts->the_post();
                        ?>
                        <article class="post-card" data-aos="fade-up">
                            <a class="post-card-image" href="<?php the_permalink(); ?>">
                                <?php if (has_post_thumbnail()) { the_post_thumbnail('medium_large', array('loading' => 'lazy', 'decoding' => 'async')); } else { echo '<img src="' . esc_url(amor_asset('assets/images/equipe-atendimento.webp')) . '" alt="" loading="lazy" decoding="async">'; } ?>
                            </a>
                            <div class="post-card-body">
                                <span>Área da Família</span>
                                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 16)); ?></p>
                            </div>
                        </article>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    ?>
                    <article class="family-empty family-empty-wide">
                        <i data-lucide="newspaper" aria-hidden="true"></i>
                        <h3>Nenhuma publicação exclusiva por enquanto.</h3>
                        <p>Publique posts na categoria configurada como Area da Familia para eles aparecerem aqui.</p>
                    </article>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>
</main>
<?php get_footer(); ?>
