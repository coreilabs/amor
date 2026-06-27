<?php
/**
 * Front page template.
 *
 * @package Amor_Fraterno
 */

get_header();
?>

<main id="conteudo">
    <section class="hero" id="inicio" aria-labelledby="hero-title">
        <div class="hero-bg" data-parallax="0.26"></div>
        <div class="hero-overlay"></div>
        <div class="hero-inner">
            <div class="hero-copy" data-aos="fade-up">
                <span class="eyebrow"><?php echo amor_text('hero_eyebrow', 'Acolhimento com amor, tratamento com propósito'); ?></span>
                <h1 id="hero-title"><?php echo amor_text('hero_title', 'Centro Terapêutico Amor Fraterno'); ?></h1>
                <p><?php echo amor_html('hero_text', 'Tratamento humanizado para dependentes químicos e alcoólicos, com suporte 24 horas, equipe multidisciplinar e uma estrutura pensada para reconstrução.'); ?></p>
                <div class="hero-actions">
                    <a class="btn btn-primary" href="<?php echo amor_whatsapp_url(); ?>" target="_blank" rel="noopener">
                        <i data-lucide="phone-call" aria-hidden="true"></i>
                        <?php echo amor_text('hero_primary_label', 'Falar no WhatsApp'); ?>
                    </a>
                    <a class="btn btn-ghost" href="#estrutura">
                        <i data-lucide="arrow-down" aria-hidden="true"></i>
                        <?php echo amor_text('hero_secondary_label', 'Conhecer a estrutura'); ?>
                    </a>
                </div>
            </div>

            <aside class="hero-panel" data-aos="fade-left" data-aos-delay="150" aria-label="Resumo do atendimento">
                <div class="panel-image">
                    <img src="<?php echo amor_image_url('hero_panel_image', 'assets/images/entrada.webp'); ?>" alt="Fachada do Centro Terapêutico Amor Fraterno" fetchpriority="high" decoding="async">
                </div>
                <div class="life-badge">
                    <i data-lucide="heart" aria-hidden="true"></i>
                    <span><?php echo amor_text('hero_panel_text', 'Aqui, cada vida importa'); ?></span>
                </div>
            </aside>
        </div>

        <div class="hero-strip" aria-label="Diferenciais">
            <?php foreach (amor_hero_stats_defaults() as $index => $stat) : ?>
                <div>
                    <i data-lucide="<?php echo esc_attr($stat['icon']); ?>" aria-hidden="true"></i>
                    <strong><?php echo amor_text('hero_stat_' . ($index + 1) . '_strong', $stat['strong']); ?></strong>
                    <span><?php echo amor_text('hero_stat_' . ($index + 1) . '_text', $stat['text']); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="intro section-white" id="sobre">
        <div class="section-grid">
            <div class="intro-logo" data-aos="zoom-in">
                <img src="<?php echo amor_asset('assets/logos/amor-fraterno-small.webp'); ?>" alt="Logotipo Amor Fraterno" loading="lazy" decoding="async">
            </div>
            <div data-aos="fade-up">
                <span class="section-kicker"><?php echo amor_text('about_kicker', 'Sobre a clínica'); ?></span>
                <h2><?php echo amor_text('about_title', 'Recuperação com presença, cuidado e direção.'); ?></h2>
                <p><?php echo amor_html('about_text', 'Somos um centro terapêutico destinado à recuperação de dependentes químicos e alcoolistas. Nossa missão é oferecer recuperação de forma humanizada, com diversas áreas terapêuticas e ferramentas para que os acolhidos se mantenham abstinentes, ressignificando suas vivências e suas vidas.'); ?></p>
                <div class="quote-line" data-aos="fade-up" data-aos-delay="120">
                    <i data-lucide="quote" aria-hidden="true"></i>
                    <span><?php echo amor_html('about_quote', 'Acolher é o primeiro passo. Transformar vidas é o nosso propósito.'); ?></span>
                </div>
            </div>
        </div>
    </section>

    <section class="features section-photo" id="estrutura">
        <div class="features-bg" data-parallax="0.22"></div>
        <div class="section-heading" data-aos="fade-up">
            <span class="section-kicker"><?php echo amor_text('structure_kicker', 'Estrutura completa'); ?></span>
            <h2><?php echo amor_text('structure_title', 'Um espaço preparado para um novo recomeço.'); ?></h2>
            <p><?php echo amor_html('structure_text', 'A clínica combina apoio terapêutico, áreas de convivência, acomodações e acompanhamento profissional para acolher com segurança.'); ?></p>
        </div>
        <div class="feature-grid">
            <?php foreach (amor_get_features() as $feature) : ?>
                <article class="feature-card" data-aos="fade-up">
                    <button class="feature-image-button" type="button" data-lightbox-group="estrutura" data-lightbox-src="<?php echo esc_url($feature['image']); ?>" data-lightbox-caption="<?php echo esc_attr($feature['title']); ?>">
                        <img src="<?php echo esc_url($feature['image']); ?>" alt="<?php echo esc_attr($feature['alt']); ?>" loading="lazy" decoding="async">
                        <span class="sr-only">Ampliar imagem de <?php echo esc_html($feature['title']); ?></span>
                    </button>
                    <div>
                        <i data-lucide="<?php echo esc_attr($feature['icon']); ?>" aria-hidden="true"></i>
                        <h3><?php echo esc_html($feature['title']); ?></h3>
                        <p><?php echo esc_html($feature['text']); ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="care-section" id="tratamento">
        <div class="care-media" data-aos="fade-right">
            <img src="<?php echo amor_image_url('care_image', 'assets/images/terapias-interacao.webp'); ?>" alt="Espaço para terapias e interação" loading="lazy" decoding="async">
        </div>
        <div class="care-copy" data-aos="fade-left">
            <span class="section-kicker"><?php echo amor_text('care_kicker', 'Atendimento humanizado'); ?></span>
            <h2><?php echo amor_text('care_title', 'Cada história é única. Cada tratamento também.'); ?></h2>
            <p><?php echo amor_html('care_text', 'O atendimento combina acompanhamentos em grupo e individuais, fortalecendo a autonomia e o vínculo dos acolhidos com a própria recuperação.'); ?></p>
            <div class="care-list">
                <?php foreach (amor_get_care_items() as $item) : ?>
                    <div><i data-lucide="<?php echo esc_attr($item['icon']); ?>" aria-hidden="true"></i><span><?php echo esc_html($item['text']); ?></span></div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="care-path" id="jornada" aria-labelledby="path-title">
        <div class="path-bg" data-parallax="0.24"></div>
        <div class="path-overlay"></div>
        <div class="path-inner">
            <div class="path-heading" data-aos="fade-up">
                <span class="section-kicker"><?php echo amor_text('journey_kicker', 'Jornada de acolhimento'); ?></span>
                <h2 id="path-title"><?php echo amor_text('journey_title', 'Um caminho cuidado de ponta a ponta.'); ?></h2>
                <p><?php echo amor_html('journey_text', 'Do primeiro contato à rotina terapêutica, a proposta é acolher com clareza, presença e acompanhamento constante.'); ?></p>
            </div>
            <div class="path-grid" aria-label="Etapas do acolhimento">
                <?php foreach (amor_get_journey_items() as $step) : ?>
                    <article class="path-card" data-aos="fade-up">
                        <i data-lucide="<?php echo esc_attr($step['icon']); ?>" aria-hidden="true"></i>
                        <span><?php echo esc_html($step['n']); ?></span>
                        <h3><?php echo esc_html($step['title']); ?></h3>
                        <p><?php echo esc_html($step['text']); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="gallery" id="galeria">
        <div class="section-heading" data-aos="fade-up">
            <span class="section-kicker"><?php echo amor_text('gallery_kicker', 'Ambientes'); ?></span>
            <h2><?php echo amor_text('gallery_title', 'Estrutura que acolhe e organiza a rotina.'); ?></h2>
        </div>
        <div class="swiper gallery-swiper" data-aos="fade-up" aria-label="Galeria de ambientes">
            <div class="swiper-wrapper">
                <?php foreach (amor_get_gallery_items() as $index => $item) : ?>
                    <button class="swiper-slide gallery-item" type="button" data-gallery-index="<?php echo esc_attr($index); ?>" data-gallery-src="<?php echo esc_url($item['image']); ?>" data-gallery-title="<?php echo esc_attr($item['title']); ?>">
                        <img src="<?php echo esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['alt']); ?>" loading="lazy" decoding="async">
                        <span><?php echo esc_html($item['title']); ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
            <div class="gallery-controls">
                <button class="swiper-button-prev-custom" type="button" aria-label="Imagem anterior"><i data-lucide="chevron-left" aria-hidden="true"></i></button>
                <div class="swiper-pagination"></div>
                <button class="swiper-button-next-custom" type="button" aria-label="Próxima imagem"><i data-lucide="chevron-right" aria-hidden="true"></i></button>
            </div>
        </div>
    </section>

    <section class="values values-photo" id="valores">
        <div class="values-bg" data-parallax="0.2"></div>
        <div class="section-heading" data-aos="fade-up">
            <span class="section-kicker"><?php echo amor_text('values_kicker', 'Missão, visão e valores'); ?></span>
            <h2><?php echo amor_text('values_title', 'Princípios que aparecem na rotina, não só no discurso.'); ?></h2>
            <p><?php echo amor_html('values_text', 'Acolher com clareza, sustentar a abstinência e reconstruir vínculos são compromissos presentes em cada conversa, atividade terapêutica e decisão de cuidado.'); ?></p>
        </div>
        <div class="values-experience">
            <aside class="values-manifesto" data-aos="fade-right">
                <span class="values-map-label">Mapa do cuidado</span>
                <h3>Da escuta inicial à autonomia possível, cada etapa precisa ter presença.</h3>
                <p>A Amor Fraterno trabalha para transformar acolhimento em rotina: conversa clara, vínculo terapêutico, equipe presente e uma estrutura que ajuda o acolhido a reencontrar direção.</p>
            </aside>
            <div class="values-grid" aria-label="Missão, visão e valores da Amor Fraterno">
                <article class="value-panel value-panel-featured" data-aos="fade-up">
                    <span class="value-label">01</span><div class="value-icon"><i data-lucide="target" aria-hidden="true"></i></div>
                    <h3>Missão</h3><strong><?php echo amor_html('mission_text', 'Acolher dependentes químicos e alcoolistas com presença, método e respeito pela história de cada pessoa.'); ?></strong>
                </article>
                <article class="value-panel" data-aos="fade-up" data-aos-delay="100">
                    <span class="value-label">02</span><div class="value-icon"><i data-lucide="telescope" aria-hidden="true"></i></div>
                    <h3>Visão</h3><strong><?php echo amor_html('vision_text', 'Ser reconhecida por unir estrutura, escuta e direção em cada etapa do recomeço.'); ?></strong>
                </article>
                <article class="value-panel value-panel-wide" data-aos="fade-up" data-aos-delay="200">
                    <span class="value-label">03</span><div class="value-icon"><i data-lucide="sparkles" aria-hidden="true"></i></div>
                    <h3>Valores</h3><strong>Valores que dão forma ao cuidado diário.</strong>
                    <ul class="value-tags" aria-label="Valores da Amor Fraterno">
                        <?php foreach (array_filter(array_map('trim', explode("\n", amor_get('values_list', "Amor fraternal e escuta sem julgamento\nRespeito à história e aos limites\nRotina terapêutica com equipe presente\nAutonomia, abstinência e família orientada")))) as $value) : ?>
                            <li><?php echo esc_html($value); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </article>
            </div>
        </div>
    </section>

    <section class="latest-posts section-white" id="publicacoes" aria-labelledby="latest-posts-title">
        <div class="section-heading" data-aos="fade-up">
            <span class="section-kicker"><?php echo amor_text('posts_kicker', 'Últimas Publicações'); ?></span>
            <h2 id="latest-posts-title"><?php echo amor_text('posts_title', 'Conteúdos para orientar famílias e apoiar recomeços.'); ?></h2>
            <p><?php echo amor_html('posts_text', 'Materiais sobre acolhimento, tratamento, família e rotina terapêutica.'); ?></p>
        </div>
        <div class="post-card-grid" aria-label="Publicações recentes">
            <?php
            $latest = new WP_Query(array('posts_per_page' => 6, 'ignore_sticky_posts' => true));
            if ($latest->have_posts()) :
                while ($latest->have_posts()) :
                    $latest->the_post();
                    ?>
                    <article class="post-card" data-aos="fade-up">
                        <a class="post-card-image" href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) { the_post_thumbnail('medium_large', array('loading' => 'lazy', 'decoding' => 'async')); } else { echo '<img src="' . amor_asset('assets/images/equipe-atendimento.webp') . '" alt="" loading="lazy" decoding="async">'; } ?>
                        </a>
                        <div class="post-card-body">
                            <span><?php echo esc_html(get_the_category()[0]->name ?? 'Publicação'); ?></span>
                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 18)); ?></p>
                        </div>
                    </article>
                    <?php
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
    </section>

    <section class="contact" id="contato">
        <div class="contact-bg" data-parallax="0.18"></div>
        <div class="contact-inner">
            <div class="contact-copy" data-aos="fade-up">
                <span class="section-kicker"><?php echo amor_text('contact_kicker', 'Contato'); ?></span>
                <h2><?php echo amor_text('contact_title', 'Fale com a Amor Fraterno.'); ?></h2>
                <p><?php echo amor_html('contact_text', 'Envie uma mensagem e receba orientação inicial com acolhimento, clareza e respeito ao momento da família.'); ?></p>
            </div>
            <div class="contact-layout">
                <article class="contact-highlight" data-aos="zoom-in">
                    <div class="whatsapp-orbit" aria-hidden="true"><i data-lucide="message-circle"></i></div>
                    <span>WhatsApp principal</span>
                    <h3><?php echo amor_text('contact_card_title', 'O primeiro passo pode começar com uma conversa.'); ?></h3>
                    <p><?php echo amor_html('contact_card_text', 'O botão abre uma mensagem pronta para agilizar o primeiro acolhimento.'); ?></p>
                    <div class="contact-actions">
                        <a class="btn btn-whatsapp" href="<?php echo amor_whatsapp_url(); ?>" target="_blank" rel="noopener"><i data-lucide="send" aria-hidden="true"></i>Chamar no WhatsApp</a>
                        <a class="contact-phone" href="<?php echo amor_tel_url(); ?>"><i data-lucide="phone" aria-hidden="true"></i><?php echo amor_text('phone_display', '(62) 99209-6062'); ?></a>
                    </div>
                </article>
                <div class="contact-grid" aria-label="Canais e informações de contato">
                    <a class="contact-card contact-card-whatsapp" href="<?php echo amor_whatsapp_url(); ?>" target="_blank" rel="noopener"><i data-lucide="message-circle" aria-hidden="true"></i><span>WhatsApp</span><strong><?php echo amor_text('phone_display', '(62) 99209-6062'); ?></strong><em>Conversar no WhatsApp</em></a>
                    <a class="contact-card contact-card-email" href="mailto:<?php echo esc_attr(amor_get('email', 'centroterapeuticoamorfraterno@gmail.com')); ?>"><i data-lucide="mail" aria-hidden="true"></i><span>E-mail institucional</span><strong><?php echo amor_text('email', 'centroterapeuticoamorfraterno@gmail.com'); ?></strong><em>Enviar e-mail</em></a>
                    <div class="contact-card contact-card-info"><i data-lucide="map-pin" aria-hidden="true"></i><span>Localização</span><strong><?php echo amor_text('location', 'Aparecida de Goiânia - Goiás'); ?></strong><em>Informações na triagem</em></div>
                    <div class="contact-card contact-card-info"><i data-lucide="heart-handshake" aria-hidden="true"></i><span>Acolhimento</span><strong>Orientação para famílias e responsáveis</strong><em>Escuta humanizada</em></div>
                </div>
            </div>
        </div>
    </section>
</main>

<div class="lightbox" data-lightbox hidden role="dialog" aria-modal="true" aria-label="Galeria de imagens">
    <button class="lightbox-close" type="button" data-lightbox-close aria-label="Fechar galeria"><i data-lucide="x" aria-hidden="true"></i></button>
    <button class="lightbox-nav lightbox-prev" type="button" data-lightbox-prev aria-label="Imagem anterior"><i data-lucide="chevron-left" aria-hidden="true"></i></button>
    <figure class="lightbox-stage" data-lightbox-stage>
        <img src="<?php echo amor_asset('assets/images/entrada.webp'); ?>" alt="" data-lightbox-image>
        <figcaption><span data-lightbox-title></span><strong data-lightbox-counter></strong></figcaption>
    </figure>
    <button class="lightbox-nav lightbox-next" type="button" data-lightbox-next aria-label="Próxima imagem"><i data-lucide="chevron-right" aria-hidden="true"></i></button>
</div>

<?php get_footer(); ?>
