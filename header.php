<?php
/**
 * Header template.
 *
 * @package Amor_Fraterno
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#conteudo">Pular para o conteúdo</a>

<header class="site-header" data-header>
    <a class="brand" href="<?php echo esc_url(home_url('/#inicio')); ?>" aria-label="<?php bloginfo('name'); ?>">
        <img class="brand-symbol" src="<?php echo amor_asset('assets/logos/amor-fraterno-simbolo.webp'); ?>" alt="" aria-hidden="true">
        <?php if (has_custom_logo()) : ?>
            <?php the_custom_logo(); ?>
        <?php else : ?>
            <img class="brand-text" src="<?php echo amor_asset('assets/logos/amor-fraterno-texto.webp'); ?>" alt="<?php bloginfo('name'); ?>">
        <?php endif; ?>
    </a>

    <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="menu-principal" data-nav-toggle>
        <i data-lucide="menu" aria-hidden="true"></i>
        <span class="sr-only">Abrir menu</span>
    </button>

    <nav class="main-nav" id="menu-principal" data-nav>
        <?php
        wp_nav_menu(array(
            'theme_location' => 'primary',
            'container' => false,
            'menu_class' => 'menu',
            'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
            'fallback_cb' => 'amor_primary_menu_fallback',
            'depth' => 1,
        ));
        ?>
    </nav>

    <a class="header-cta" href="<?php echo amor_whatsapp_url(); ?>" target="_blank" rel="noopener">
        <i data-lucide="message-circle" aria-hidden="true"></i>
        WhatsApp
    </a>
</header>
