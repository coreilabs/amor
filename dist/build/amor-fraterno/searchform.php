<?php
/**
 * Search form.
 *
 * @package Amor_Fraterno
 */
?>
<form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
    <label class="sr-only" for="s">Buscar no site</label>
    <input id="s" type="search" name="s" value="<?php echo esc_attr(get_search_query()); ?>" placeholder="Buscar no site">
    <button type="submit" aria-label="Buscar">
        <i data-lucide="search" aria-hidden="true"></i>
    </button>
</form>
