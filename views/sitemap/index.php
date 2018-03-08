<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php foreach ($urls as $url) { ?>
        <url>
            <loc><?= htmlspecialchars(yii\helpers\Url::to($url['url'], true)) ?></loc>
            <lastmod><?= $url['lastMod'] ?></lastmod>
            <changefreq><?= $url['frequencyUpdate'] ?></changefreq>
        </url>
    <?php } ?>
</urlset>
