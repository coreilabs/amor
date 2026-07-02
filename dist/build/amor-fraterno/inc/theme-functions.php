<?php
/**
 * Helpers and Customizer setup for Amor Fraterno.
 *
 * @package Amor_Fraterno
 */

if (!defined('ABSPATH')) {
    exit;
}

function amor_asset($path) {
    return esc_url(get_template_directory_uri() . '/' . ltrim($path, '/'));
}

function amor_get($key, $default = '') {
    return get_theme_mod('amor_' . $key, $default);
}

function amor_text($key, $default = '') {
    return esc_html(amor_get($key, $default));
}

function amor_html($key, $default = '') {
    return wp_kses_post(amor_get($key, $default));
}

function amor_map_embed_default() {
    return '<iframe src="https://www.google.com/maps/embed?pb=!1m23!1m12!1m3!1d20390.41916573777!2d-49.232787948693435!3d-16.831880970264336!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m8!3e6!4m0!4m5!1s0x935d9d70bf81da55%3A0xf7659954fd0bd173!2sCentro%20Terap%C3%AAutico%20Amor%20Fraterno%2C%20R.%20Ang%C3%A9lica%2C%20Qd.30%20-%20Lt.10%20-%20Jardim%20Rosa%20do%20Sul%2C%20Aparecida%20de%20Goi%C3%A2nia%20-%20GO%2C%2074975-255!3m2!1d-16.8415246!2d-49.2559513!5e0!3m2!1spt-BR!2sbr!4v1782659591533!5m2!1spt-BR!2sbr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>';
}

function amor_sanitize_map_embed($value) {
    return wp_kses($value, amor_map_embed_allowed_html());
}

function amor_map_embed_allowed_html() {
    return array(
        'iframe' => array(
            'src' => true,
            'width' => true,
            'height' => true,
            'style' => true,
            'allowfullscreen' => true,
            'loading' => true,
            'referrerpolicy' => true,
            'title' => true,
        ),
    );
}

function amor_map_embed() {
    return wp_kses(amor_get('location_embed', amor_map_embed_default()), amor_map_embed_allowed_html());
}

function amor_phone_digits() {
    return preg_replace('/\D+/', '', amor_get('whatsapp_number', '5562992096062'));
}

function amor_whatsapp_url($message = '') {
    $phone = amor_phone_digits();
    $message = $message ?: amor_get('whatsapp_message', 'Vim pelo site do Centro Terapêutico Amor Fraterno e quero mais informações.');

    return esc_url('https://wa.me/' . $phone . '?text=' . rawurlencode($message));
}

function amor_whatsapp_plain_url($message = '') {
    $phone = amor_phone_digits();
    $message = $message ?: amor_get('whatsapp_message', 'Vim pelo site do Centro Terapêutico Amor Fraterno e quero mais informações.');

    return 'https://wa.me/' . $phone . '?text=' . rawurlencode($message);
}

function amor_tel_url() {
    return esc_url('tel:+' . amor_phone_digits());
}

function amor_image_url($key, $default_path) {
    $value = amor_get($key, '');
    return esc_url($value ?: get_template_directory_uri() . '/' . ltrim($default_path, '/'));
}

function amor_posts_url() {
    $template_pages = get_posts(array(
        'post_type' => 'page',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'meta_key' => '_wp_page_template',
        'meta_value' => 'template-publicacoes.php',
        'fields' => 'ids',
    ));

    if (!empty($template_pages)) {
        return get_permalink((int) $template_pages[0]);
    }

    $posts_page_id = (int) get_option('page_for_posts');

    if ($posts_page_id > 0) {
        return get_permalink($posts_page_id);
    }

    return home_url('/publicacoes/');
}

function amor_is_posts_archive() {
    return (bool) get_query_var('amor_posts_archive');
}

function amor_og_image_size_name() {
    return 'amor-og-image';
}

function amor_get_og_image($post_id = 0) {
    $fallback = array(
        'url' => amor_asset('assets/images/opengraph.png'),
        'width' => 1200,
        'height' => 630,
    );

    $post_id = $post_id ? (int) $post_id : get_queried_object_id();

    if (!$post_id || !is_singular('post') || !has_post_thumbnail($post_id)) {
        return $fallback;
    }

    $thumbnail_id = get_post_thumbnail_id($post_id);
    $image = wp_get_attachment_image_src($thumbnail_id, amor_og_image_size_name());

    if (!$image) {
        $image = wp_get_attachment_image_src($thumbnail_id, 'full');
    }

    if (!$image || empty($image[0])) {
        return $fallback;
    }

    return array(
        'url' => esc_url($image[0]),
        'width' => !empty($image[1]) ? (int) $image[1] : 1200,
        'height' => !empty($image[2]) ? (int) $image[2] : 630,
    );
}

function amor_get_og_description($post_id = 0) {
    $post_id = $post_id ? (int) $post_id : get_queried_object_id();

    if (function_exists('amor_is_family_area_page') && amor_is_family_area_page()) {
        return wp_strip_all_tags(amor_get('family_intro', 'Um espaço reservado para aproximar família e acolhidos, com recados aprovados pela equipe e informações da rotina da casa.'));
    }

    if (is_singular('post') && $post_id) {
        $excerpt = get_the_excerpt($post_id);

        if (!$excerpt) {
            $post = get_post($post_id);
            $excerpt = $post ? wp_trim_words(wp_strip_all_tags(strip_shortcodes($post->post_content)), 28) : '';
        }

        return $excerpt;
    }

    if (is_front_page()) {
        return wp_strip_all_tags(amor_get('hero_text', get_bloginfo('description')));
    }

    if (is_singular() && $post_id) {
        $excerpt = get_the_excerpt($post_id);
        return $excerpt ?: get_bloginfo('description');
    }

    return get_bloginfo('description');
}

function amor_get_og_data() {
    $post_id = get_queried_object_id();
    $is_single_post = is_singular('post') && $post_id;
    $is_family_area = function_exists('amor_is_family_area_page') && amor_is_family_area_page();
    $image = amor_get_og_image($post_id);
    $current_path = isset($GLOBALS['wp']->request) && $GLOBALS['wp']->request ? '/' . $GLOBALS['wp']->request . '/' : '/';

    $data = array(
        'type' => $is_single_post ? 'article' : 'website',
        'title' => wp_get_document_title(),
        'description' => amor_get_og_description($post_id),
        'url' => $is_single_post ? get_permalink($post_id) : home_url($current_path),
        'image' => $image['url'],
        'image_type' => 'image/png',
        'image_width' => $image['width'],
        'image_height' => $image['height'],
        'image_alt' => $is_single_post ? get_the_title($post_id) : get_bloginfo('name'),
        'published_time' => '',
        'modified_time' => '',
        'section' => '',
    );

    if ($is_single_post) {
        $categories = amor_public_post_categories($post_id);

        $data['title'] = get_the_title($post_id) . ' | ' . get_bloginfo('name');
        $data['published_time'] = get_the_date(DATE_W3C, $post_id);
        $data['modified_time'] = get_the_modified_date(DATE_W3C, $post_id);
        $data['section'] = !empty($categories) ? $categories[0]->name : '';
    }

    if ($is_family_area) {
        $data['title'] = 'Área da Família | ' . get_bloginfo('name');
        $data['description'] = amor_get_og_description($post_id);
        $data['url'] = amor_family_area_url();
        $data['image_alt'] = 'Área da Família do ' . get_bloginfo('name');
    }

    return $data;
}

function amor_public_post_categories($post_id = 0) {
    $post_id = $post_id ? (int) $post_id : get_the_ID();
    $categories = $post_id ? get_the_category($post_id) : array();

    if (empty($categories) || is_wp_error($categories)) {
        return array();
    }

    if (!function_exists('amor_family_category_id')) {
        return $categories;
    }

    $family_category_id = amor_family_category_id();

    if (!$family_category_id) {
        return $categories;
    }

    return array_values(array_filter($categories, function ($category) use ($family_category_id) {
        return (int) $category->term_id !== $family_category_id;
    }));
}

function amor_post_primary_category_name($post_id = 0, $fallback = 'Publicação') {
    $categories = amor_public_post_categories($post_id);
    return !empty($categories) ? $categories[0]->name : $fallback;
}

function amor_public_post_category_ids($post_id = 0) {
    return array_map(function ($category) {
        return (int) $category->term_id;
    }, amor_public_post_categories($post_id));
}

function amor_single_share_buttons($post_id = 0) {
    $post_id = $post_id ? (int) $post_id : get_the_ID();
    $url = get_permalink($post_id);
    $title = get_the_title($post_id);
    $encoded_url = rawurlencode($url);
    $encoded_title = rawurlencode($title);

    $links = array(
        'whatsapp' => array(
            'label' => 'Compartilhar no WhatsApp',
            'url' => 'https://wa.me/?text=' . rawurlencode($title . ' ' . $url),
            'icon' => '<path d="M12.04 2a9.86 9.86 0 0 0-8.43 14.96L2.5 21.5l4.65-1.08A9.96 9.96 0 1 0 12.04 2Zm5.8 14.04c-.24.68-1.4 1.3-1.94 1.35-.5.05-1.12.08-1.8-.11-.42-.13-.96-.31-1.65-.61-2.9-1.25-4.8-4.16-4.94-4.35-.15-.2-1.18-1.58-1.18-3.01 0-1.44.74-2.15 1-2.44.27-.3.58-.36.78-.36h.56c.18 0 .43-.07.67.52.25.6.84 2.05.91 2.2.08.15.13.33.03.53-.1.2-.15.33-.3.51-.15.18-.31.4-.44.54-.15.15-.3.31-.13.61.18.3.77 1.27 1.65 2.05 1.13 1.01 2.08 1.33 2.38 1.48.3.15.48.13.66-.08.2-.23.76-.89.96-1.19.2-.3.4-.25.68-.15.28.1 1.79.84 2.1.99.3.15.5.23.58.36.08.13.08.74-.16 1.42Z"/>',
        ),
        'facebook' => array(
            'label' => 'Compartilhar no Facebook',
            'url' => 'https://www.facebook.com/sharer/sharer.php?u=' . $encoded_url,
            'icon' => '<path d="M14.2 8.2V6.6c0-.77.38-1.52 1.6-1.52h.73V2.18S15.86 2 14.5 2c-2.84 0-4.7 1.72-4.7 4.84V8.2H6.65v3.25H9.8V22h3.86V11.45h3.02l.48-3.25H14.2Z"/>',
        ),
        'x' => array(
            'label' => 'Compartilhar no X',
            'url' => 'https://twitter.com/intent/tweet?url=' . $encoded_url . '&text=' . $encoded_title,
            'icon' => '<path d="M13.9 10.47 21.35 2h-1.77l-6.46 7.35L7.95 2H2l7.82 11.13L2 22h1.77l6.84-7.77L16.06 22H22l-8.1-11.53Zm-2.42 2.75-.8-1.11L4.38 3.3H7.1l5.08 7.1.8 1.11 6.6 9.23h-2.72l-5.39-7.52Z"/>',
        ),
        'linkedin' => array(
            'label' => 'Compartilhar no LinkedIn',
            'url' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $encoded_url,
            'icon' => '<path d="M6.94 8.98H3.48V20h3.46V8.98ZM5.2 3.5a2 2 0 1 0 0 4.01 2 2 0 0 0 0-4.01Zm15.3 10.17c0-2.96-1.58-4.34-3.7-4.34-1.7 0-2.46.94-2.88 1.6V8.98h-3.32c.04.94 0 11.02 0 11.02h3.32v-6.15c0-.33.02-.66.12-.9.27-.66.87-1.34 1.88-1.34 1.33 0 1.86 1.01 1.86 2.5V20h3.46v-6.33h-.74Z"/>',
        ),
    );

    echo '<div class="single-share-block" aria-label="Compartilhar publicação">';
    echo '<p>Compartilhe esta publicação em sua rede social.</p>';
    echo '<div class="single-share-strip">';
    foreach ($links as $network => $item) {
        echo '<a class="share-' . esc_attr($network) . '" href="' . esc_url($item['url']) . '" target="_blank" rel="noopener" aria-label="' . esc_attr($item['label']) . '">';
        echo '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">' . $item['icon'] . '</svg>';
        echo '</a>';
    }
    echo '<button class="share-native" type="button" data-native-share data-share-title="' . esc_attr($title) . '" data-share-url="' . esc_url($url) . '" aria-label="Compartilhar em outros aplicativos">';
    echo '<i data-lucide="share-2" aria-hidden="true"></i>';
    echo '</button>';
    echo '</div>';
    echo '<div class="single-share-copy">';
    echo '<label for="single-share-url-' . esc_attr($post_id) . '">Link da publicação</label>';
    echo '<div>';
    echo '<input id="single-share-url-' . esc_attr($post_id) . '" type="url" value="' . esc_url($url) . '" readonly data-share-url-field>';
    echo '<button type="button" data-copy-share-url="' . esc_url($url) . '">Copiar</button>';
    echo '</div>';
    echo '<span class="single-share-copy-alert" data-copy-share-alert role="status" aria-live="polite" hidden>Link Copiado</span>';
    echo '</div>';
    echo '</div>';
}

function amor_single_about_box() {
    if (function_exists('amor_is_family_post') && amor_is_family_post()) {
        return;
    }

    $logo = amor_get('single_about_logo', amor_asset('assets/logos/amor-fraterno-small.webp'));
    $title = amor_get('single_about_title', 'Centro Terapêutico Amor Fraterno');
    $text = amor_get('single_about_text', 'Somos um centro terapêutico dedicado ao acolhimento e tratamento humanizado de dependentes químicos e alcoolistas, com equipe presente, rotina estruturada e cuidado pensado para apoiar famílias em cada etapa do recomeço.');
    $button = amor_get('single_about_button', 'Falar com a equipe');

    ?>
    <aside class="single-about-box" aria-label="Sobre o Centro Terapêutico Amor Fraterno">
        <?php if ($logo) : ?>
            <img src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" decoding="async">
        <?php endif; ?>
        <div>
            <h2><?php echo esc_html($title); ?></h2>
            <p><?php echo wp_kses_post($text); ?></p>
            <a class="btn btn-primary" href="<?php echo esc_url(amor_whatsapp_plain_url()); ?>" target="_blank" rel="noopener">
                <i data-lucide="message-circle" aria-hidden="true"></i>
                <?php echo esc_html($button); ?>
            </a>
        </div>
    </aside>
    <?php
}

function amor_add_control($wp_customize, $section, $id, $label, $default = '', $type = 'text') {
    $setting = 'amor_' . $id;
    $sanitize = 'sanitize_text_field';

    if ('textarea' === $type) {
        $sanitize = 'wp_kses_post';
    } elseif ('embed' === $type) {
        $sanitize = 'amor_sanitize_map_embed';
    } elseif ('url' === $type || 'image' === $type) {
        $sanitize = 'esc_url_raw';
    }

    $wp_customize->add_setting($setting, array(
        'default' => $default,
        'sanitize_callback' => $sanitize,
        'transport' => 'refresh',
    ));

    if ('image' === $type) {
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, $setting, array(
            'label' => $label,
            'section' => $section,
            'settings' => $setting,
        )));
        return;
    }

    $wp_customize->add_control($setting, array(
        'label' => $label,
        'section' => $section,
        'type' => 'embed' === $type ? 'textarea' : $type,
    ));
}

function amor_register_customizer($wp_customize) {
    $wp_customize->add_panel('amor_panel', array(
        'title' => __('Amor Fraterno', 'amor-fraterno'),
        'priority' => 30,
    ));

    $sections = array(
        'amor_global' => 'Dados globais',
        'amor_single_post' => 'Posts: chamada institucional',
        'amor_home_hero' => 'Home: Hero',
        'amor_home_about' => 'Home: Sobre',
        'amor_home_structure' => 'Home: Estrutura',
        'amor_home_care' => 'Home: Tratamento',
        'amor_home_journey' => 'Home: Jornada',
        'amor_home_gallery' => 'Home: Galeria',
        'amor_home_values' => 'Home: Missão e valores',
        'amor_home_posts' => 'Home: Publicações',
        'amor_home_contact' => 'Home: Contato',
    );

    foreach ($sections as $id => $title) {
        $wp_customize->add_section($id, array(
            'title' => $title,
            'panel' => 'amor_panel',
        ));
    }

    amor_add_control($wp_customize, 'amor_global', 'whatsapp_number', 'WhatsApp com DDI', '5562992096062');
    amor_add_control($wp_customize, 'amor_global', 'phone_display', 'Telefone formatado', '(62) 99209-6062');
    amor_add_control($wp_customize, 'amor_global', 'email', 'E-mail institucional', 'centroterapeuticoamorfraterno@gmail.com', 'email');
    amor_add_control($wp_customize, 'amor_global', 'location', 'Localização', 'Aparecida de Goiânia - Goiás');
    amor_add_control($wp_customize, 'amor_global', 'whatsapp_message', 'Mensagem padrão do WhatsApp', 'Vim pelo site do Centro Terapêutico Amor Fraterno e quero mais informações.', 'textarea');
    amor_add_control($wp_customize, 'amor_global', 'ga_id', 'Google Analytics / Ads ID', 'G-HK88EVQ1NM');
    amor_add_control($wp_customize, 'amor_global', 'footer_tagline', 'Frase do rodapé', 'Acolhimento que transforma. Amor que restaura. Esperança que renasce.', 'textarea');
    amor_add_control($wp_customize, 'amor_global', 'location_embed', 'Código embed da localização', amor_map_embed_default(), 'embed');

    amor_add_control($wp_customize, 'amor_single_post', 'single_about_logo', 'Logotipo', amor_asset('assets/logos/amor-fraterno-small.webp'), 'image');
    amor_add_control($wp_customize, 'amor_single_post', 'single_about_title', 'Título', 'Centro Terapêutico Amor Fraterno');
    amor_add_control($wp_customize, 'amor_single_post', 'single_about_text', 'Texto', 'Somos um centro terapêutico dedicado ao acolhimento e tratamento humanizado de dependentes químicos e alcoolistas, com equipe presente, rotina estruturada e cuidado pensado para apoiar famílias em cada etapa do recomeço.', 'textarea');
    amor_add_control($wp_customize, 'amor_single_post', 'single_about_button', 'Texto do botão', 'Falar com a equipe');

    amor_add_control($wp_customize, 'amor_home_hero', 'hero_eyebrow', 'Chamada curta', 'Acolhimento com amor, tratamento com propósito');
    amor_add_control($wp_customize, 'amor_home_hero', 'hero_title', 'Título', 'Centro Terapêutico Amor Fraterno');
    amor_add_control($wp_customize, 'amor_home_hero', 'hero_text', 'Texto', 'Tratamento humanizado para dependentes químicos e alcoólicos, com suporte 24 horas, equipe multidisciplinar e uma estrutura pensada para reconstrução.', 'textarea');
    amor_add_control($wp_customize, 'amor_home_hero', 'hero_primary_label', 'Texto do botão principal', 'Falar no WhatsApp');
    amor_add_control($wp_customize, 'amor_home_hero', 'hero_secondary_label', 'Texto do botão secundário', 'Conhecer a estrutura');
    amor_add_control($wp_customize, 'amor_home_hero', 'hero_panel_text', 'Selo do card', 'Aqui, cada vida importa');
    amor_add_control($wp_customize, 'amor_home_hero', 'hero_panel_image', 'Imagem do card', amor_asset('assets/images/entrada.webp'), 'image');
    foreach (amor_hero_stats_defaults() as $index => $item) {
        $n = $index + 1;
        amor_add_control($wp_customize, 'amor_home_hero', 'hero_stat_' . $n . '_strong', 'Diferencial ' . $n . ': destaque', $item['strong']);
        amor_add_control($wp_customize, 'amor_home_hero', 'hero_stat_' . $n . '_text', 'Diferencial ' . $n . ': texto', $item['text']);
    }

    amor_add_control($wp_customize, 'amor_home_about', 'about_kicker', 'Chamada curta', 'Sobre a clínica');
    amor_add_control($wp_customize, 'amor_home_about', 'about_title', 'Título', 'Recuperação com presença, cuidado e direção.');
    amor_add_control($wp_customize, 'amor_home_about', 'about_text', 'Texto', 'Somos um centro terapêutico destinado à recuperação de dependentes químicos e alcoolistas. Nossa missão é oferecer recuperação de forma humanizada, com diversas áreas terapêuticas e ferramentas para que os acolhidos se mantenham abstinentes, ressignificando suas vivências e suas vidas.', 'textarea');
    amor_add_control($wp_customize, 'amor_home_about', 'about_quote', 'Frase destacada', 'Acolher é o primeiro passo. Transformar vidas é o nosso propósito.', 'textarea');

    amor_add_control($wp_customize, 'amor_home_structure', 'structure_kicker', 'Chamada curta', 'Estrutura completa');
    amor_add_control($wp_customize, 'amor_home_structure', 'structure_title', 'Título', 'Um espaço preparado para um novo recomeço.');
    amor_add_control($wp_customize, 'amor_home_structure', 'structure_text', 'Texto', 'A clínica combina apoio terapêutico, áreas de convivência, acomodações e acompanhamento profissional para acolher com segurança.', 'textarea');
    foreach (amor_features_defaults() as $index => $item) {
        $n = $index + 1;
        amor_add_control($wp_customize, 'amor_home_structure', 'feature_' . $n . '_title', 'Estrutura ' . $n . ': título', $item['title']);
        amor_add_control($wp_customize, 'amor_home_structure', 'feature_' . $n . '_text', 'Estrutura ' . $n . ': texto', $item['text'], 'textarea');
        amor_add_control($wp_customize, 'amor_home_structure', 'feature_' . $n . '_image', 'Estrutura ' . $n . ': imagem', amor_asset($item['image']), 'image');
        amor_add_control($wp_customize, 'amor_home_structure', 'feature_' . $n . '_alt', 'Estrutura ' . $n . ': texto alternativo', $item['alt']);
    }

    amor_add_control($wp_customize, 'amor_home_care', 'care_kicker', 'Chamada curta', 'Atendimento humanizado');
    amor_add_control($wp_customize, 'amor_home_care', 'care_title', 'Título', 'Cada história é única. Cada tratamento também.');
    amor_add_control($wp_customize, 'amor_home_care', 'care_text', 'Texto', 'O atendimento combina acompanhamentos em grupo e individuais, fortalecendo a autonomia e o vínculo dos acolhidos com a própria recuperação.', 'textarea');
    amor_add_control($wp_customize, 'amor_home_care', 'care_image', 'Imagem', amor_asset('assets/images/terapias-interacao.webp'), 'image');
    foreach (amor_care_items_defaults() as $index => $item) {
        $n = $index + 1;
        amor_add_control($wp_customize, 'amor_home_care', 'care_item_' . $n, 'Item de tratamento ' . $n, $item['text']);
    }

    amor_add_control($wp_customize, 'amor_home_journey', 'journey_kicker', 'Chamada curta', 'Jornada de acolhimento');
    amor_add_control($wp_customize, 'amor_home_journey', 'journey_title', 'Título', 'Um caminho cuidado de ponta a ponta.');
    amor_add_control($wp_customize, 'amor_home_journey', 'journey_text', 'Texto', 'Do primeiro contato à rotina terapêutica, a proposta é acolher com clareza, presença e acompanhamento constante.', 'textarea');
    foreach (amor_journey_defaults() as $index => $item) {
        $n = $index + 1;
        amor_add_control($wp_customize, 'amor_home_journey', 'journey_' . $n . '_title', 'Etapa ' . $n . ': título', $item['title']);
        amor_add_control($wp_customize, 'amor_home_journey', 'journey_' . $n . '_text', 'Etapa ' . $n . ': texto', $item['text'], 'textarea');
    }

    amor_add_control($wp_customize, 'amor_home_gallery', 'gallery_kicker', 'Chamada curta', 'Ambientes');
    amor_add_control($wp_customize, 'amor_home_gallery', 'gallery_title', 'Título', 'Estrutura que acolhe e organiza a rotina.');

    $gallery_defaults = amor_gallery_defaults();
    foreach ($gallery_defaults as $index => $item) {
        $n = $index + 1;
        amor_add_control($wp_customize, 'amor_home_gallery', 'gallery_' . $n . '_title', 'Galeria ' . $n . ': título', $item['title']);
        amor_add_control($wp_customize, 'amor_home_gallery', 'gallery_' . $n . '_image', 'Galeria ' . $n . ': imagem', amor_asset($item['image']), 'image');
        amor_add_control($wp_customize, 'amor_home_gallery', 'gallery_' . $n . '_alt', 'Galeria ' . $n . ': texto alternativo', $item['alt']);
    }

    amor_add_control($wp_customize, 'amor_home_values', 'values_kicker', 'Chamada curta', 'Missão, visão e valores');
    amor_add_control($wp_customize, 'amor_home_values', 'values_title', 'Título', 'Princípios que aparecem na rotina, não só no discurso.');
    amor_add_control($wp_customize, 'amor_home_values', 'values_text', 'Texto', 'Acolher com clareza, sustentar a abstinência e reconstruir vínculos são compromissos presentes em cada conversa, atividade terapêutica e decisão de cuidado.', 'textarea');
    amor_add_control($wp_customize, 'amor_home_values', 'mission_text', 'Missão', 'Acolher dependentes químicos e alcoolistas com presença, método e respeito pela história de cada pessoa.', 'textarea');
    amor_add_control($wp_customize, 'amor_home_values', 'vision_text', 'Visão', 'Ser reconhecida por unir estrutura, escuta e direção em cada etapa do recomeço.', 'textarea');
    amor_add_control($wp_customize, 'amor_home_values', 'values_list', 'Valores (um por linha)', "Amor fraternal e escuta sem julgamento\nRespeito à história e aos limites\nRotina terapêutica com equipe presente\nAutonomia, abstinência e família orientada", 'textarea');

    amor_add_control($wp_customize, 'amor_home_posts', 'posts_kicker', 'Chamada curta', 'Últimas Publicações');
    amor_add_control($wp_customize, 'amor_home_posts', 'posts_title', 'Título', 'Conteúdos para orientar famílias e apoiar recomeços.');
    amor_add_control($wp_customize, 'amor_home_posts', 'posts_text', 'Texto', 'Materiais sobre acolhimento, tratamento, família e rotina terapêutica.', 'textarea');

    amor_add_control($wp_customize, 'amor_home_contact', 'contact_kicker', 'Chamada curta', 'Contato');
    amor_add_control($wp_customize, 'amor_home_contact', 'contact_title', 'Título', 'Fale com a Amor Fraterno.');
    amor_add_control($wp_customize, 'amor_home_contact', 'contact_text', 'Texto', 'Envie uma mensagem e receba orientação inicial com acolhimento, clareza e respeito ao momento da família.', 'textarea');
    amor_add_control($wp_customize, 'amor_home_contact', 'contact_card_title', 'Título do destaque', 'O primeiro passo pode começar com uma conversa.');
    amor_add_control($wp_customize, 'amor_home_contact', 'contact_card_text', 'Texto do destaque', 'O botão abre uma mensagem pronta para agilizar o primeiro acolhimento.', 'textarea');
}
add_action('customize_register', 'amor_register_customizer');

function amor_gallery_defaults() {
    return array(
        array('title' => 'Entrada', 'image' => 'assets/images/entrada.webp', 'alt' => 'Entrada do Centro Terapêutico Amor Fraterno'),
        array('title' => 'Lazer', 'image' => 'assets/images/area-lazer.webp', 'alt' => 'Área de lazer com piscina'),
        array('title' => 'Área verde', 'image' => 'assets/images/area-verde-real.webp', 'alt' => 'Área verde com lago ornamental e paisagismo'),
        array('title' => 'Terapias', 'image' => 'assets/images/terapias-interacao.webp', 'alt' => 'Espaço coberto para terapias e convivência'),
        array('title' => 'Reuniões', 'image' => 'assets/images/reunioes.webp', 'alt' => 'Atividade terapêutica em grupo'),
        array('title' => 'Acomodações', 'image' => 'assets/images/acomodacoes.webp', 'alt' => 'Dormitório organizado com camas'),
        array('title' => 'Cozinha', 'image' => 'assets/images/cozinha.webp', 'alt' => 'Cozinha da clínica'),
        array('title' => 'Escritório', 'image' => 'assets/images/escritorio-real.webp', 'alt' => 'Escritório da clínica'),
    );
}

function amor_hero_stats_defaults() {
    return array(
        array('icon' => 'clock-3', 'strong' => '24h', 'text' => 'suporte e coordenação'),
        array('icon' => 'stethoscope', 'strong' => 'Equipe', 'text' => 'psicólogo, médico e terapeuta'),
        array('icon' => 'trees', 'strong' => 'Ambiente', 'text' => 'amplo, seguro e com natureza'),
    );
}

function amor_features_defaults() {
    return array(
        array('icon' => 'users-round', 'title' => 'Terapia de grupo', 'text' => 'Fortalecimento, apoio e reconstrução em conjunto.', 'image' => 'assets/images/reunioes.webp', 'alt' => 'Reunião terapêutica em grupo na área externa'),
        array('icon' => 'house', 'title' => 'Espaço amplo', 'text' => 'Ambiente seguro, tranquilo e em contato com a natureza.', 'image' => 'assets/images/area-verde-real.webp', 'alt' => 'Área verde com paisagismo e fonte'),
        array('icon' => 'bed-double', 'title' => 'Boas acomodações', 'text' => 'Conforto e estrutura para o bem-estar dos acolhidos.', 'image' => 'assets/images/acomodacoes.webp', 'alt' => 'Acomodações com camas organizadas'),
        array('icon' => 'brain', 'title' => 'Profissionais especializados', 'text' => 'Psicólogo, médico, terapeuta e coordenadores.', 'image' => 'assets/images/enfermaria.webp', 'alt' => 'Sala de atendimento e enfermaria'),
        array('icon' => 'clock-3', 'title' => 'Suporte 24 horas', 'text' => 'Equipe preparada para acolher, orientar e guiar.', 'image' => 'assets/images/escritorio-real.webp', 'alt' => 'Escritório administrativo da clínica'),
    );
}

function amor_get_features() {
    $items = array();
    foreach (amor_features_defaults() as $index => $default) {
        $n = $index + 1;
        $items[] = array(
            'icon' => $default['icon'],
            'title' => amor_get('feature_' . $n . '_title', $default['title']),
            'text' => amor_get('feature_' . $n . '_text', $default['text']),
            'image' => amor_get('feature_' . $n . '_image', amor_asset($default['image'])),
            'alt' => amor_get('feature_' . $n . '_alt', $default['alt']),
        );
    }
    return $items;
}

function amor_care_items_defaults() {
    return array(
        array('icon' => 'messages-square', 'text' => 'Atendimentos em grupo'),
        array('icon' => 'user-round-check', 'text' => 'Acompanhamento individual'),
        array('icon' => 'stethoscope', 'text' => 'Rede profissional multidisciplinar'),
    );
}

function amor_get_care_items() {
    $items = array();
    foreach (amor_care_items_defaults() as $index => $default) {
        $items[] = array(
            'icon' => $default['icon'],
            'text' => amor_get('care_item_' . ($index + 1), $default['text']),
        );
    }
    return $items;
}

function amor_journey_defaults() {
    return array(
        array('icon' => 'message-circle', 'n' => '01', 'title' => 'Conversa inicial', 'text' => 'Escuta cuidadosa para entender a história e orientar os próximos passos.'),
        array('icon' => 'clipboard-check', 'n' => '02', 'title' => 'Avaliação', 'text' => 'Equipe multidisciplinar organiza o plano terapêutico de forma individual.'),
        array('icon' => 'calendar-check', 'n' => '03', 'title' => 'Rotina terapêutica', 'text' => 'Atividades em grupo, atendimentos individuais e convivência estruturada.'),
        array('icon' => 'shield-check', 'n' => '04', 'title' => 'Suporte contínuo', 'text' => 'Coordenação presente 24 horas para acolher, orientar e acompanhar.'),
    );
}

function amor_get_journey_items() {
    $items = array();
    foreach (amor_journey_defaults() as $index => $default) {
        $n = $index + 1;
        $items[] = array(
            'icon' => $default['icon'],
            'n' => $default['n'],
            'title' => amor_get('journey_' . $n . '_title', $default['title']),
            'text' => amor_get('journey_' . $n . '_text', $default['text']),
        );
    }
    return $items;
}

function amor_get_gallery_items() {
    $items = array();
    foreach (amor_gallery_defaults() as $index => $default) {
        $n = $index + 1;
        $image = amor_get('gallery_' . $n . '_image', amor_asset($default['image']));
        if (!$image) {
            continue;
        }
        $items[] = array(
            'title' => amor_get('gallery_' . $n . '_title', $default['title']),
            'image' => $image,
            'alt' => amor_get('gallery_' . $n . '_alt', $default['alt']),
        );
    }
    return $items;
}

function amor_breadcrumbs() {
    if (is_front_page()) {
        return;
    }

    echo '<nav class="breadcrumbs" aria-label="Breadcrumbs">';
    echo '<a href="' . esc_url(home_url('/')) . '">Início</a>';

    if (is_single()) {
        $categories = amor_public_post_categories();
        if (!empty($categories)) {
            $category = $categories[0];
            echo '<span>/</span><a href="' . esc_url(get_category_link($category)) . '">' . esc_html($category->name) . '</a>';
        }
        echo '<span>/</span><span>' . esc_html(get_the_title()) . '</span>';
    } elseif (is_page()) {
        echo '<span>/</span><span>' . esc_html(get_the_title()) . '</span>';
    } elseif (is_category()) {
        echo '<span>/</span><span>' . esc_html(single_cat_title('', false)) . '</span>';
    } elseif (is_archive()) {
        echo '<span>/</span><span>' . esc_html(get_the_archive_title()) . '</span>';
    } elseif (is_search()) {
        echo '<span>/</span><span>Busca</span>';
    }

    echo '</nav>';
}

function amor_cookie_banner() {
    ?>
    <div class="cookie-banner" data-cookie-banner hidden role="region" aria-label="Aviso de cookies">
        <div class="cookie-summary">
            <span class="cookie-icon" aria-hidden="true"><i data-lucide="shield-check"></i></span>
            <div>
                <strong>Privacidade e cookies</strong>
                <p>Usamos cookies necessários e, com sua permissão, cookies de análise e publicidade.</p>
                <a href="<?php echo esc_url(home_url('/politica-de-privacidade/#cookies')); ?>">Ler Política de Privacidade</a>
            </div>
        </div>
        <div class="cookie-actions">
            <button class="cookie-button cookie-button-ghost" type="button" data-cookie-customize>Configurar</button>
            <button class="cookie-button cookie-button-secondary" type="button" data-cookie-reject>Rejeitar não essenciais</button>
            <button class="cookie-button cookie-button-primary" type="button" data-cookie-accept>Aceitar todos</button>
        </div>
    </div>
    <div class="cookie-modal" data-cookie-modal hidden role="dialog" aria-modal="true" aria-labelledby="cookie-modal-title">
        <div class="cookie-modal-panel">
            <button class="cookie-modal-close" type="button" data-cookie-close aria-label="Fechar preferências de cookies"><i data-lucide="x" aria-hidden="true"></i></button>
            <span class="section-kicker">Preferências de privacidade</span>
            <h2 id="cookie-modal-title">Escolha como podemos usar cookies.</h2>
            <p>Você pode aceitar todos, rejeitar cookies não essenciais ou permitir apenas as categorias que preferir.</p>
            <div class="cookie-options">
                <label class="cookie-option"><span class="cookie-option-text"><strong>Necessários</strong><span>Mantêm recursos básicos do site, segurança e preferências essenciais. Estão sempre ativos.</span></span><span class="cookie-toggle-control"><input class="cookie-switch-input" type="checkbox" checked disabled><span class="cookie-switch" aria-hidden="true"></span></span></label>
                <label class="cookie-option"><span class="cookie-option-text"><strong>Análise</strong><span>Ajudam a entender visitas, páginas acessadas e desempenho do site por meio de estatísticas.</span></span><span class="cookie-toggle-control"><input class="cookie-switch-input" type="checkbox" data-cookie-toggle="analytics"><span class="cookie-switch" aria-hidden="true"></span></span></label>
                <label class="cookie-option"><span class="cookie-option-text"><strong>Publicidade</strong><span>Permitem medir campanhas, conversões e anúncios personalizados quando usados.</span></span><span class="cookie-toggle-control"><input class="cookie-switch-input" type="checkbox" data-cookie-toggle="marketing"><span class="cookie-switch" aria-hidden="true"></span></span></label>
            </div>
            <div class="cookie-modal-actions">
                <button class="cookie-button cookie-button-secondary" type="button" data-cookie-reject>Rejeitar não essenciais</button>
                <button class="cookie-button cookie-button-ghost" type="button" data-cookie-save>Salvar preferências</button>
                <button class="cookie-button cookie-button-primary" type="button" data-cookie-accept>Aceitar todos</button>
            </div>
        </div>
    </div>
    <?php
}
