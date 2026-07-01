<?php
/**
 * Amor Fraterno theme bootstrap.
 *
 * @package Amor_Fraterno
 */

if (!defined('ABSPATH')) {
    exit;
}

require get_template_directory() . '/inc/theme-functions.php';
require get_template_directory() . '/inc/family-area.php';

function amor_setup() {
    load_theme_textdomain('amor-fraterno', get_template_directory() . '/languages');

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_image_size(amor_og_image_size_name(), 1200, 630, true);
    add_theme_support('automatic-feed-links');
    add_theme_support('custom-logo', array(
        'height' => 120,
        'width' => 360,
        'flex-height' => true,
        'flex-width' => true,
    ));
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script'));
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');

    register_nav_menus(array(
        'primary' => __('Menu principal', 'amor-fraterno'),
        'footer' => __('Menu do rodapé', 'amor-fraterno'),
    ));
}
add_action('after_setup_theme', 'amor_setup');

function amor_default_primary_menu_items() {
    return array(
        array('title' => 'Início', 'url' => home_url('/#inicio')),
        array('title' => 'Sobre', 'url' => home_url('/#sobre')),
        array('title' => 'Estrutura', 'url' => home_url('/#estrutura')),
        array('title' => 'Tratamento', 'url' => home_url('/#tratamento')),
        array('title' => 'Ambientes', 'url' => home_url('/#galeria')),
        array('title' => 'Publicações', 'url' => amor_posts_url()),
        array('title' => 'Área da Família', 'url' => amor_family_area_url()),
        array('title' => 'Contato', 'url' => home_url('/#contato')),
    );
}

function amor_ensure_primary_menu() {
    $locations = get_nav_menu_locations();

    if (!empty($locations['primary'])) {
        amor_update_posts_menu_item_url((int) $locations['primary']);
        return;
    }

    $menu_name = 'Menu principal Amor Fraterno';
    $menu = wp_get_nav_menu_object($menu_name);

    if (!$menu) {
        $menu_id = wp_create_nav_menu($menu_name);

        if (is_wp_error($menu_id)) {
            return;
        }

        foreach (amor_default_primary_menu_items() as $item) {
            wp_update_nav_menu_item($menu_id, 0, array(
                'menu-item-title' => $item['title'],
                'menu-item-url' => $item['url'],
                'menu-item-status' => 'publish',
                'menu-item-type' => 'custom',
            ));
        }
    } else {
        $menu_id = (int) $menu->term_id;
    }

    amor_update_posts_menu_item_url($menu_id);

    $locations['primary'] = $menu_id;
    set_theme_mod('nav_menu_locations', $locations);
}
add_action('after_switch_theme', 'amor_ensure_primary_menu');
add_action('init', 'amor_ensure_primary_menu');
add_action('admin_init', 'amor_ensure_primary_menu');

function amor_ensure_privacy_page() {
    $slug = 'politica-de-privacidade';
    $page = get_page_by_path($slug);

    if (!$page) {
        $page_id = wp_insert_post(array(
            'post_title' => 'Política de Privacidade',
            'post_name' => $slug,
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_content' => '',
        ));

        if (is_wp_error($page_id) || !$page_id) {
            return;
        }
    } else {
        $page_id = (int) $page->ID;

        if ('publish' !== get_post_status($page_id)) {
            wp_update_post(array(
                'ID' => $page_id,
                'post_status' => 'publish',
            ));
        }
    }

    update_post_meta($page_id, '_wp_page_template', 'page-politica-de-privacidade.php');
    update_option('wp_page_for_privacy_policy', $page_id);
}
add_action('after_switch_theme', 'amor_ensure_privacy_page');
add_action('init', 'amor_ensure_privacy_page');
add_action('admin_init', 'amor_ensure_privacy_page');

function amor_primary_menu_fallback() {
    echo '<ul class="menu amor-menu-fallback">';
    foreach (amor_default_primary_menu_items() as $item) {
        echo '<li class="menu-item"><a href="' . esc_url($item['url']) . '">' . esc_html($item['title']) . '</a></li>';
    }
    echo '</ul>';
}

function amor_primary_menu() {
    echo '<ul id="menu-principal-amor-fraterno" class="menu">';

    foreach (amor_default_primary_menu_items() as $item) {
        $is_current = amor_is_primary_menu_item_current($item);
        $classes = array('menu-item');

        if ($is_current) {
            $classes[] = 'current-menu-item';
        }

        echo '<li class="' . esc_attr(implode(' ', $classes)) . '">';
        echo '<a href="' . esc_url($item['url']) . '"' . ($is_current ? ' aria-current="page"' : '') . '>' . esc_html($item['title']) . '</a>';
        echo '</li>';
    }

    echo '</ul>';
}

function amor_is_primary_menu_item_current($item) {
    $title = isset($item['title']) ? $item['title'] : '';
    $request_path = isset($_SERVER['REQUEST_URI']) ? (string) parse_url(wp_unslash($_SERVER['REQUEST_URI']), PHP_URL_PATH) : '';

    if ('Início' === $title) {
        return is_front_page();
    }

    if ('Publicações' === $title) {
        return is_home()
            || amor_is_posts_archive()
            || is_singular('post')
            || is_archive()
            || is_search()
            || (bool) preg_match('#^/(novidades|publicacoes|category|tag|author)(/|$)#', $request_path);
    }

    if ('Área da Família' === $title) {
        return amor_is_family_area_page()
            || is_singular('amor_family_message')
            || is_singular('amor_family_schedule')
            || (bool) preg_match('#^/(area-da-familia)(/|$)#', $request_path);
    }

    return false;
}

function amor_update_posts_menu_item_url($menu_id) {
    $items = wp_get_nav_menu_items($menu_id);

    if (empty($items) || is_wp_error($items)) {
        return;
    }

    foreach ($items as $item) {
        if ('Publicações' !== $item->title || false === strpos($item->url, 'post_type=post')) {
            continue;
        }

        wp_update_nav_menu_item($menu_id, $item->ID, array(
            'menu-item-title' => $item->title,
            'menu-item-url' => amor_posts_url(),
            'menu-item-status' => 'publish',
            'menu-item-type' => 'custom',
        ));
    }
}

function amor_widgets_init() {
    $sidebars = array(
        'sidebar-blog' => array('name' => 'Sidebar do blog', 'description' => 'Widgets e plugins exibidos em arquivos e posts.'),
        'single-before' => array('name' => 'Antes do conteúdo do post', 'description' => 'Área para plugins antes da publicação.'),
        'single-after' => array('name' => 'Depois do conteúdo do post', 'description' => 'Área para plugins depois da publicação.'),
        'single-sidebar-top' => array('name' => 'Sidebar do post: topo', 'description' => 'Área para plugins no topo da sidebar do post.'),
        'single-sidebar-bottom' => array('name' => 'Sidebar do post: rodapé', 'description' => 'Área para plugins no final da sidebar do post.'),
        'footer-plugins' => array('name' => 'Rodapé: plugins', 'description' => 'Área global para scripts, selos, formulários ou plugins no rodapé.'),
    );

    foreach ($sidebars as $id => $sidebar) {
        register_sidebar(array(
            'id' => $id,
            'name' => $sidebar['name'],
            'description' => $sidebar['description'],
            'before_widget' => '<div id="%1$s" class="sidebar-widget widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h2>',
            'after_title' => '</h2>',
        ));
    }
}
add_action('widgets_init', 'amor_widgets_init');

function amor_scripts() {
    $style_path = get_stylesheet_directory() . '/style.css';
    $style_version = file_exists($style_path) ? (string) filemtime($style_path) : wp_get_theme()->get('Version');
    $script_path = get_template_directory() . '/script.js';
    $script_version = file_exists($script_path) ? (string) filemtime($script_path) : wp_get_theme()->get('Version');

    wp_enqueue_style('amor-fonts', 'https://fonts.googleapis.com/css2?family=Ephesis&family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@700&display=swap', array(), null);
    wp_enqueue_style('aos', 'https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css', array(), '2.3.4');
    wp_enqueue_style('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11');
    wp_enqueue_style('amor-style', get_stylesheet_uri(), array('amor-fonts', 'aos', 'swiper'), $style_version);

    wp_enqueue_script('aos', 'https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js', array(), '2.3.4', true);
    wp_enqueue_script('swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11', true);
    wp_enqueue_script('lucide', 'https://unpkg.com/lucide@latest/dist/umd/lucide.min.js', array(), null, true);
    wp_enqueue_script('amor-script', get_template_directory_uri() . '/script.js', array('aos', 'swiper', 'lucide'), $script_version, true);
}
add_action('wp_enqueue_scripts', 'amor_scripts');

function amor_resource_hints($urls, $relation_type) {
    if ('preconnect' === $relation_type) {
        $urls[] = 'https://fonts.googleapis.com';
        $urls[] = array('href' => 'https://fonts.gstatic.com', 'crossorigin' => 'anonymous');
    }
    return $urls;
}
add_filter('wp_resource_hints', 'amor_resource_hints', 10, 2);

function amor_register_posts_archive_route() {
    add_rewrite_rule('^publicacoes/page/([0-9]+)/?$', 'index.php?amor_posts_archive=1&paged=$matches[1]', 'top');
    add_rewrite_rule('^publicacoes/?$', 'index.php?amor_posts_archive=1', 'top');

    if ('1.0' !== get_option('amor_posts_archive_route_version')) {
        flush_rewrite_rules(false);
        update_option('amor_posts_archive_route_version', '1.0');
    }
}
add_action('init', 'amor_register_posts_archive_route');

function amor_posts_archive_query_vars($vars) {
    $vars[] = 'amor_posts_archive';
    return $vars;
}
add_filter('query_vars', 'amor_posts_archive_query_vars');

function amor_prepare_posts_archive_query($query) {
    if (is_admin() || !$query->is_main_query() || !$query->get('amor_posts_archive')) {
        return;
    }

    $query->set('post_type', 'post');
    $query->set('post_status', 'publish');
    $query->set('posts_per_page', (int) get_option('posts_per_page'));
}
add_action('pre_get_posts', 'amor_prepare_posts_archive_query');

function amor_posts_archive_template($template) {
    if (!get_query_var('amor_posts_archive')) {
        return $template;
    }

    $archive_template = locate_template('archive.php');
    return $archive_template ?: $template;
}
add_filter('template_include', 'amor_posts_archive_template');

function amor_head_meta() {
    $ga_id = trim(amor_get('ga_id', 'G-HK88EVQ1NM'));
    $og = amor_get_og_data();
    ?>
    <meta name="theme-color" content="#005d75">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:site_name" content="<?php bloginfo('name'); ?>">
    <meta property="og:type" content="<?php echo esc_attr($og['type']); ?>">
    <meta property="og:title" content="<?php echo esc_attr($og['title']); ?>">
    <meta property="og:description" content="<?php echo esc_attr($og['description']); ?>">
    <meta property="og:url" content="<?php echo esc_url($og['url']); ?>">
    <meta property="og:image" content="<?php echo esc_url($og['image']); ?>">
    <meta property="og:image:secure_url" content="<?php echo esc_url($og['image']); ?>">
    <meta property="og:image:width" content="<?php echo esc_attr($og['image_width']); ?>">
    <meta property="og:image:height" content="<?php echo esc_attr($og['image_height']); ?>">
    <meta property="og:image:alt" content="<?php echo esc_attr($og['image_alt']); ?>">
    <?php if ('article' === $og['type']) : ?>
        <meta property="article:published_time" content="<?php echo esc_attr($og['published_time']); ?>">
        <meta property="article:modified_time" content="<?php echo esc_attr($og['modified_time']); ?>">
        <?php if ($og['section']) : ?>
            <meta property="article:section" content="<?php echo esc_attr($og['section']); ?>">
        <?php endif; ?>
    <?php endif; ?>
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo esc_attr($og['title']); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr($og['description']); ?>">
    <meta name="twitter:image" content="<?php echo esc_url($og['image']); ?>">
    <link rel="shortcut icon" href="<?php echo amor_asset('assets/images/favicon.ico'); ?>">
    <link rel="icon" type="image/png" sizes="512x512" href="<?php echo amor_asset('assets/images/favicon.png'); ?>">
    <link rel="apple-touch-icon" href="<?php echo amor_asset('assets/images/favicon.png'); ?>">
    <?php if ($ga_id) : ?>
        <script>
            window.dataLayer = window.dataLayer || [];
            window.gtag = window.gtag || function gtag(){window.dataLayer.push(arguments);};
            const savedCookieConsent = (() => {
                try { return JSON.parse(localStorage.getItem("amorFraternoCookieConsent")); } catch (error) { return null; }
            })();
            window.gtag("consent", "default", {
                ad_storage: savedCookieConsent?.marketing ? "granted" : "denied",
                analytics_storage: savedCookieConsent?.analytics ? "granted" : "denied",
                ad_user_data: savedCookieConsent?.marketing ? "granted" : "denied",
                ad_personalization: savedCookieConsent?.marketing ? "granted" : "denied",
                functionality_storage: "granted",
                security_storage: "granted",
                wait_for_update: 500
            });
        </script>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($ga_id); ?>"></script>
        <script>
            window.gtag("js", new Date());
            window.gtag("config", "<?php echo esc_js($ga_id); ?>");
        </script>
    <?php endif;
}
add_action('wp_head', 'amor_head_meta', 1);

function amor_estimated_reading_time() {
    $words = str_word_count(wp_strip_all_tags(get_the_content()));
    return max(1, (int) ceil($words / 220));
}
