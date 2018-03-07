<?php
echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
?>
<?= header('Content-Type: application/xml') ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php foreach ($urls as $url) { ?>
        <url>
            <loc><?= htmlspecialchars(yii\helpers\Url::to($url[0], true)) ?></loc>
            <lastmod><?= date(DATE_W3C) ?></lastmod>
            <priority><?= $url[1] ?></priority>
        </url>
    <?php } ?>
</urlset>
