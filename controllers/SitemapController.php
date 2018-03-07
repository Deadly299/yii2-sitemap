<?php

namespace deadly299\sitemap\controllers;

use dvizh\filter\models\Filter;
use dvizh\shop\models\Product;
use yii\web\Controller;
use yii\db\Query;
use Yii;

class SitemapController extends Controller
{
    public $urls = [];

    public function actionIndex()
    {
        $module = $this->getModule();
        $siteMapModels = $module->sitemapModels;
        $xmlSiteMap = Yii::$app->cache->get('siteMap');

        if ($xmlSiteMap) {

            foreach ($siteMapModels as $key => $siteMapModel) {

                $model = $siteMapModel['class'];
                $models = $model::find();

                if (isset($siteMapModel['conditions']))
                    $models->andWhere($siteMapModel['conditions']);


                $models = $models->asArray()->all();

                foreach ($models as $item) {
                    $this->urls[] = [
                        Yii::$app->urlManager->createUrl(['/'. $siteMapModel['link'] .'/' . $item['slug']]),
                        $siteMapModel['updates'],
                    ];
                }
            }

            if($module->otherLinks) {
                foreach ($module->otherLinks as $link) {
                    $this->urls[] = [
                        Yii::$app->urlManager->createUrl(['/'. $link['link'] .'/' . $link['slug']]),
                        $link['updates'],
                    ];
                }
            }

            Yii::$app->cache->set('siteMap', $this->urls, 3600*12);
        }

        echo $xmlSiteMap = $this->renderPartial('index', ['urls' => $xmlSiteMap]);
    }

    public function getModule()
    {
        return Yii::$app->getModule('sitemap');
    }
}