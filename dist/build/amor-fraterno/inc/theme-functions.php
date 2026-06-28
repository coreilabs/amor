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
    $image = amor_get_og_image($post_id);
    $current_path = isset($GLOBALS['wp']->request) && $GLOBALS['wp']->request ? '/' . $GLOBALS['wp']->request . '/' : '/';

    $data = array(
        'type' => $is_single_post ? 'article' : 'website',
        'title' => wp_get_document_title(),
        'description' => amor_get_og_description($post_id),
        'url' => $is_single_post ? get_permalink($post_id) : home_url($current_path),
        'image' => $image['url'],
        'image_width' => $image['width'],
        'image_height' => $image['height'],
        'image_alt' => $is_single_post ? get_the_title($post_id) : get_bloginfo('name'),
        'published_time' => '',
        'modified_time' => '',
        'section' => '',
    );

    if ($is_single_post) {
        $categories = get_the_category($post_id);

        $data['title'] = get_the_title($post_id) . ' | ' . get_bloginfo('name');
        $data['published_time'] = get_the_date(DATE_W3C, $post_id);
        $data['modified_time'] = get_the_modified_date(DATE_W3C, $post_id);
        $data['section'] = !empty($categories) ? $categories[0]->name : '';
    }

    return $data;
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
        array('title' => 'Piscina', 'image' => 'assets/images/piscina.webp', 'alt' => 'Piscina e área externa'),
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
        $categories = get_the_category();
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
