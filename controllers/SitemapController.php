<?php

namespace deadly299\sitemap\controllers;

use Yii;
use yii\web\Response;
use yii\web\Controller;

class SitemapController extends Controller
{
    public function actionIndex()
    {
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        $response->getHeaders()->set('Content-Type', 'application/xml; charset=utf-8');
        $siteMapBuilder = Yii::$app->siteMapBuilder;

        if ($siteMapBuilder->hasXmlInCache()) {
            $urls = $siteMapBuilder->getXmlOutOfCache();
        } else {
            $urls = $siteMapBuilder->buildSiteMap();
        }

        return $this->renderPartial('index', ['urls' => $urls]);
    }
}