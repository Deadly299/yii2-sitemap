<?php

namespace deadly299\sitemap;

/**
 * document-upload module definition class
 */
class Module extends \yii\base\Module
{
    public $sitemapModels = null;
    public $otherLinks = null;
    public $domain = null;

    /**
     * @inheritdoc
     */

    public function init()
    {
        parent::init();

        $this->domain = \Yii::$app->request->baseUrl;
    }
}
