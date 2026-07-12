<?php include MASIR_GHALEB . 'sarsafhe.php'; ?>

<div class="homepage-content">
    <?php
    $builder_html = $GLOBALS['khane_builder_html'] ?? '';
    if ($builder_html !== '') {
        echo $builder_html;
    } else {
        echo $page_data['content'] ?? '';
    }
    ?>
</div>

<?php include MASIR_GHALEB . 'panevis.php'; ?>