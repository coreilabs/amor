<?php
/**
 * Family area features.
 *
 * @package Amor_Fraterno
 */

if (!defined('ABSPATH')) {
    exit;
}

function amor_family_area_slug() {
    return 'area-da-familia';
}

function amor_family_area_url() {
    $page = get_page_by_path(amor_family_area_slug());
    return $page ? get_permalink($page) : home_url('/' . amor_family_area_slug() . '/');
}

function amor_family_password() {
    return trim((string) amor_get('family_password', 'familiaamor2026'));
}

function amor_family_category_slug() {
    return sanitize_title(amor_get('family_category_slug', 'area-da-familia'));
}

function amor_family_access_token($password) {
    return hash_hmac('sha256', (string) $password, wp_salt('auth'));
}

function amor_family_has_access() {
    $password = amor_family_password();

    if ('' === $password) {
        return true;
    }

    $cookie = isset($_COOKIE['amor_family_access']) ? sanitize_text_field(wp_unslash($_COOKIE['amor_family_access'])) : '';
    return $cookie && hash_equals(amor_family_access_token($password), $cookie);
}

function amor_family_grant_access() {
    $token = amor_family_access_token(amor_family_password());
    setcookie('amor_family_access', $token, time() + WEEK_IN_SECONDS, COOKIEPATH ?: '/', COOKIE_DOMAIN, is_ssl(), true);
    $_COOKIE['amor_family_access'] = $token;
}

function amor_family_handle_password_submit() {
    if (empty($_POST['amor_family_password_action'])) {
        return '';
    }

    if (!isset($_POST['amor_family_password_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['amor_family_password_nonce'])), 'amor_family_password')) {
        return 'Nao foi possivel validar a tentativa. Atualize a pagina e tente novamente.';
    }

    $password = isset($_POST['amor_family_password']) ? (string) wp_unslash($_POST['amor_family_password']) : '';

    if (hash_equals(amor_family_password(), $password)) {
        amor_family_grant_access();
        return '';
    }

    return 'Senha incorreta. Confira a senha atual informada pela equipe.';
}

function amor_is_family_area_page() {
    if (!is_page()) {
        return false;
    }

    $page = get_queried_object();
    return $page && (amor_family_area_slug() === $page->post_name || 'template-area-da-familia.php' === get_page_template_slug($page));
}

function amor_is_family_category_context() {
    $slug = amor_family_category_slug();

    if (!$slug) {
        return false;
    }

    if (is_category($slug)) {
        return true;
    }

    return is_singular('post') && has_category($slug, get_queried_object_id());
}

function amor_family_category_id() {
    $term = get_category_by_slug(amor_family_category_slug());
    return $term ? (int) $term->term_id : 0;
}

function amor_family_exclude_private_posts($query) {
    if (is_admin()) {
        return;
    }

    $category_id = amor_family_category_id();

    if (!$category_id) {
        return;
    }

    $post_type = $query->get('post_type');
    $category_name = (string) $query->get('category_name');

    if ('post' !== $post_type && '' !== $post_type) {
        return;
    }

    if ($category_name === amor_family_category_slug() || $query->is_category(amor_family_category_slug())) {
        return;
    }

    $category__in = (array) $query->get('category__in');
    if (in_array($category_id, array_map('intval', $category__in), true)) {
        return;
    }

    $excluded = array_map('intval', (array) $query->get('category__not_in'));
    $excluded[] = $category_id;
    $query->set('category__not_in', array_values(array_unique($excluded)));
}
add_action('pre_get_posts', 'amor_family_exclude_private_posts', 20);

function amor_family_maybe_gate_content() {
    if ((amor_is_family_area_page() || amor_is_family_category_context()) && !amor_family_has_access()) {
        status_header(200);
    }
}
add_action('template_redirect', 'amor_family_maybe_gate_content');

function amor_register_family_content_types() {
    register_post_type('amor_family_message', array(
        'labels' => array(
            'name' => 'Área da Família',
            'singular_name' => 'Recado da família',
            'add_new_item' => 'Adicionar recado',
            'edit_item' => 'Aprovar ou editar recado',
            'menu_name' => 'Área da Família',
            'all_items' => 'Mural de recados',
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-heart',
        'supports' => array('title', 'editor', 'thumbnail'),
        'capability_type' => 'post',
        'map_meta_cap' => true,
    ));

    register_post_type('amor_family_schedule', array(
        'labels' => array(
            'name' => 'Cronograma',
            'singular_name' => 'Item do cronograma',
            'add_new_item' => 'Adicionar atividade',
            'edit_item' => 'Editar atividade',
            'menu_name' => 'Cronograma',
            'all_items' => 'Cronograma',
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => 'edit.php?post_type=amor_family_message',
        'supports' => array('title', 'editor', 'page-attributes'),
        'capability_type' => 'post',
        'map_meta_cap' => true,
    ));
}
add_action('init', 'amor_register_family_content_types');

function amor_family_ensure_page_and_category() {
    $page = get_page_by_path(amor_family_area_slug());

    if (!$page) {
        $page_id = wp_insert_post(array(
            'post_title' => 'Área da Família',
            'post_name' => amor_family_area_slug(),
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_content' => '',
        ));
    } else {
        $page_id = (int) $page->ID;
    }

    if (!empty($page_id) && !is_wp_error($page_id)) {
        wp_update_post(array(
            'ID' => $page_id,
            'post_title' => 'Área da Família',
        ));
        update_post_meta($page_id, '_wp_page_template', 'template-area-da-familia.php');
    }

    if (!term_exists(amor_family_category_slug(), 'category')) {
        wp_insert_term('Área da Família', 'category', array('slug' => amor_family_category_slug()));
    }
}
add_action('after_switch_theme', 'amor_family_ensure_page_and_category');
add_action('admin_init', 'amor_family_ensure_page_and_category');
add_action('init', 'amor_family_ensure_page_and_category');

function amor_family_customize($wp_customize) {
    $wp_customize->add_section('amor_family_area', array(
        'title' => 'Área da Família',
        'panel' => 'amor_panel',
    ));

    amor_add_control($wp_customize, 'amor_family_area', 'family_password', 'Senha de acesso', 'familiaamor2026', 'text');
    amor_add_control($wp_customize, 'amor_family_area', 'family_category_slug', 'Slug da categoria exclusiva', 'area-da-familia', 'text');
    amor_add_control($wp_customize, 'amor_family_area', 'family_intro', 'Texto de abertura', 'Um espaço reservado para aproximar família e acolhidos, com recados aprovados pela equipe e informações da rotina da casa.', 'textarea');
    amor_add_control($wp_customize, 'amor_family_area', 'family_notice', 'Aviso do formulário', 'Os recados passam por aprovação manual antes de aparecerem no mural.', 'textarea');
}
add_action('customize_register', 'amor_family_customize', 20);

function amor_family_schedule_meta_box() {
    add_meta_box('amor_family_schedule_details', 'Detalhes do cronograma', 'amor_family_schedule_meta_box_html', 'amor_family_schedule', 'normal', 'high');
}
add_action('add_meta_boxes', 'amor_family_schedule_meta_box');

function amor_family_schedule_meta_box_html($post) {
    wp_nonce_field('amor_family_schedule_save', 'amor_family_schedule_nonce');
    $period = get_post_meta($post->ID, '_amor_schedule_period', true);
    $time = get_post_meta($post->ID, '_amor_schedule_time', true);
    $audience = get_post_meta($post->ID, '_amor_schedule_audience', true);
    ?>
    <p>
        <label for="amor_schedule_period"><strong>Dia ou período</strong></label><br>
        <input class="widefat" id="amor_schedule_period" name="amor_schedule_period" type="text" value="<?php echo esc_attr($period); ?>" placeholder="Ex.: Segunda-feira">
    </p>
    <p>
        <label for="amor_schedule_time"><strong>Horário</strong></label><br>
        <input class="widefat" id="amor_schedule_time" name="amor_schedule_time" type="text" value="<?php echo esc_attr($time); ?>" placeholder="Ex.: 09h às 10h30">
    </p>
    <p>
        <label for="amor_schedule_audience"><strong>Público ou observação</strong></label><br>
        <input class="widefat" id="amor_schedule_audience" name="amor_schedule_audience" type="text" value="<?php echo esc_attr($audience); ?>" placeholder="Ex.: Internos, familiares, equipe">
    </p>
    <?php
}

function amor_family_save_schedule_meta($post_id) {
    if (!isset($_POST['amor_family_schedule_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['amor_family_schedule_nonce'])), 'amor_family_schedule_save')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    foreach (array('period', 'time', 'audience') as $key) {
        $field = 'amor_schedule_' . $key;
        $value = isset($_POST[$field]) ? sanitize_text_field(wp_unslash($_POST[$field])) : '';
        update_post_meta($post_id, '_amor_schedule_' . $key, $value);
    }
}
add_action('save_post_amor_family_schedule', 'amor_family_save_schedule_meta');

function amor_family_message_columns($columns) {
    $columns['amor_recipient'] = 'Para quem';
    $columns['amor_relative'] = 'Familiar';
    return $columns;
}
add_filter('manage_amor_family_message_posts_columns', 'amor_family_message_columns');

function amor_family_message_column_content($column, $post_id) {
    if ('amor_recipient' === $column) {
        echo esc_html(get_post_meta($post_id, '_amor_message_recipient', true));
    }

    if ('amor_relative' === $column) {
        echo esc_html(get_post_meta($post_id, '_amor_message_relative', true));
    }
}
add_action('manage_amor_family_message_posts_custom_column', 'amor_family_message_column_content', 10, 2);

function amor_family_schedule_columns($columns) {
    $columns['amor_period'] = 'Dia/período';
    $columns['amor_time'] = 'Horário';
    return $columns;
}
add_filter('manage_amor_family_schedule_posts_columns', 'amor_family_schedule_columns');

function amor_family_schedule_column_content($column, $post_id) {
    if ('amor_period' === $column) {
        echo esc_html(get_post_meta($post_id, '_amor_schedule_period', true));
    }

    if ('amor_time' === $column) {
        echo esc_html(get_post_meta($post_id, '_amor_schedule_time', true));
    }
}
add_action('manage_amor_family_schedule_posts_custom_column', 'amor_family_schedule_column_content', 10, 2);

function amor_family_handle_message_submit() {
    if (empty($_POST['amor_family_message_action'])) {
        return array('type' => '', 'message' => '');
    }

    if (!amor_family_has_access()) {
        return array('type' => 'error', 'message' => 'Acesse com a senha da familia antes de enviar um recado.');
    }

    if (!isset($_POST['amor_family_message_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['amor_family_message_nonce'])), 'amor_family_message')) {
        return array('type' => 'error', 'message' => 'Nao foi possivel validar o envio. Atualize a pagina e tente novamente.');
    }

    $recipient = isset($_POST['amor_recipient']) ? sanitize_text_field(wp_unslash($_POST['amor_recipient'])) : '';
    $relative = isset($_POST['amor_relative']) ? sanitize_text_field(wp_unslash($_POST['amor_relative'])) : '';
    $message = isset($_POST['amor_message']) ? wp_kses_post(wp_unslash($_POST['amor_message'])) : '';

    if (!$recipient || !$relative || !$message) {
        return array('type' => 'error', 'message' => 'Preencha para quem, seu nome e a mensagem.');
    }

    $photo_size = !empty($_FILES['amor_photo']['size']) ? (int) $_FILES['amor_photo']['size'] : 0;
    $submission_fingerprint = hash('sha256', implode('|', array(
        strtolower($recipient),
        strtolower($relative),
        wp_strip_all_tags($message),
        $photo_size,
        isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '',
    )));
    $submission_key = 'amor_family_message_' . $submission_fingerprint;

    if (get_transient($submission_key)) {
        return array('type' => 'success', 'message' => 'Recado recebido. Ele ja esta aguardando aprovacao da equipe.');
    }

    set_transient($submission_key, 1, 10 * MINUTE_IN_SECONDS);

    $post_id = wp_insert_post(array(
        'post_type' => 'amor_family_message',
        'post_title' => sprintf('Recado para %s', $recipient),
        'post_content' => $message,
        'post_status' => 'pending',
    ));

    if (is_wp_error($post_id) || !$post_id) {
        delete_transient($submission_key);
        return array('type' => 'error', 'message' => 'Nao foi possivel salvar o recado agora. Tente novamente em instantes.');
    }

    update_post_meta($post_id, '_amor_message_recipient', $recipient);
    update_post_meta($post_id, '_amor_message_relative', $relative);

    if (!empty($_FILES['amor_photo']['name'])) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $attachment_id = media_handle_upload('amor_photo', $post_id);

        if (!is_wp_error($attachment_id)) {
            set_post_thumbnail($post_id, $attachment_id);
        }
    }

    return array('type' => 'success', 'message' => 'Recado enviado para aprovação. Assim que a equipe aprovar, ele aparece no mural.');
}

function amor_family_password_form($error = '') {
    ob_start();
    ?>
    <section class="family-lock" aria-labelledby="family-lock-title">
        <div class="family-lock-panel">
            <span class="section-kicker">Área reservada</span>
            <h1 id="family-lock-title">Área da Família</h1>
            <p>Digite a senha atual informada pela equipe para acessar recados, cronograma e publicações exclusivas.</p>
            <?php if ($error) : ?><div class="family-alert family-alert-error"><?php echo esc_html($error); ?></div><?php endif; ?>
            <form class="family-lock-form" method="post">
                <?php wp_nonce_field('amor_family_password', 'amor_family_password_nonce'); ?>
                <input type="hidden" name="amor_family_password_action" value="1">
                <label for="amor_family_password">Senha de acesso</label>
                <div class="family-password-row">
                    <input id="amor_family_password" name="amor_family_password" type="password" autocomplete="current-password" required>
                    <button class="btn btn-primary" type="submit"><i data-lucide="unlock" aria-hidden="true"></i> Entrar</button>
                </div>
            </form>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

function amor_family_gate_single_content($template) {
    if (!amor_is_family_category_context() || amor_family_has_access()) {
        return $template;
    }

    return locate_template('template-area-da-familia.php') ?: $template;
}
add_filter('template_include', 'amor_family_gate_single_content', 99);
