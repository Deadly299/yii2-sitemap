<?php

namespace deadly299\sitemap\behaviors;

use Yii;
use yii\base\Behavior;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;
use deadly299\attachdocument\models\AttachDocument;

class ModifySitemap extends Behavior
{
    private $doReset = true;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_DELETE => 'deleteLink',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'createLink',
        ];
    }

    public function deleteLink()
    {
        $this->bust('delete');
    }

    public function createLink()
    {
        $this->bust('create');
    }

    public function bust($event)
    {
        if ($this->doReset) {
            $model = $this->owner;
            $module = $this->getModule();
            $xmlSitemap = $this->getSiteMap();
            $sitemapModels = $module->sitemapModels;

            foreach ($sitemapModels as $sitemapModel) {
                if ($model::className() == $sitemapModel['class']) {
                    $slug = $sitemapModel['slugItem'];
                    if ($event == 'delete') {
                        foreach ($xmlSitemap as $key => $item) {
                            $url = $sitemapModel['link'] . '/' . $model->slug;
                            if ($item[$key] == '/' . $url) {
                                ArrayHelper::remove($xmlSitemap, $key);
                                $this->setSiteMap($xmlSitemap);
                                break;
                            }
                        }
                    } else {

                        $xmlSitemap[] = [
                            $sitemapModel['link'] . '/' . $model->$slug,
                            $sitemapModel['updates'],
                        ];

                        $this->setSiteMap($xmlSitemap);

                    }

                    $this->doReset = false;

                }
            }
        }
    }

    public function getModule()
    {
        return Yii::$app->getModule('sitemap');
    }

    public function getSiteMap()
    {
       return Yii::$app->cache->get('sitemap');
    }

    public function setSiteMap($data)
    {
        Yii::$app->cache->set('sitemap', $data, 3600 * 12);
    }
}