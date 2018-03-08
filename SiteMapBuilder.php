<?php

namespace deadly299\sitemap;

use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;

class siteMapBuilder extends Component
{
    const EVENT_DELETE = 'delete';
    const EVENT_UPDATE = 'update';
    private $urls = null;

    public function getModule()
    {
        return Yii::$app->getModule('sitemap');
    }

    //builds methods
    public function buildUrlViaModel($data)
    {
        foreach ($data as $siteMapModel) {

            $className = $siteMapModel['class'];
            $query = $className::find();

            if (isset($siteMapModel['conditions'])) {
                $query->andWhere($siteMapModel['conditions']);
            }

            $models = $query->asArray()->all();

            foreach ($models as $item) {
                $url = Yii::$app->urlManager->createUrl(['/' . $siteMapModel['link'] . '/' . $item['slug']]);
                $frequency = $siteMapModel['updates'];

                $this->addUrl($url, $frequency);
            }
        }
    }

    public function buildUrlOtherLink($data)
    {
        if ($data) {
            foreach ($data as $link) {
                $url = $this->getModule()->domain . $link['link'];
                $frequency = $link['updates'];

                $this->addUrl($url, $frequency);
            }
        }
    }

    public function buildSiteMap()
    {
        $module = $this->getModule();
        $this->buildUrlViaModel($module->sitemapModels);
        $this->buildUrlOtherLink($module->otherLinks);
        $this->setXmlInCache();

        return $this->urls;
    }

    public function addUrl($url, $frequency)
    {
        $this->urls[] = [
            'url' => $url,
            'frequencyUpdate' => $frequency,
            'lastMod' => date(DATE_W3C),
        ];
    }

    //behavior methods
    public function updateXmlUlr($event, $modelOwner)
    {
        $this->urls = $this->getXmlOutOfCache();
        $modelSettings = $this->findSiteMapSetting($modelOwner);
        if(!$modelSettings) {
            return false;
        }
        $urlKey = $this->findUrlKey($modelSettings, $modelOwner);

        if ($event == self::EVENT_DELETE) {
            $this->deleteUrl($urlKey);
        }
        if ($event == self::EVENT_UPDATE) {
            if ($urlKey) {
                $this->updateUrl($modelOwner, $urlKey, $modelSettings);
            } else {
                $this->insertUrl($modelOwner, $modelSettings);
            }
        }
    }

    private function findSiteMapSetting($model)
    {
        $module = $this->getModule();
        $shortClassNameOwnerModel = $this->getShortClass($model::className());

        foreach ($module->sitemapModels as $key => $siteMapModel) {
            if ($this->getShortClass($siteMapModel['class']) === $shortClassNameOwnerModel) {
                return $siteMapModel;
            }
        }

        return false;
    }

    private function findUrlKey($modelSetting, $modelOwner)
    {
        $url = $url = $this->getUrlOutSettings($modelSetting, $modelOwner);

        foreach ($this->urls as $key => $xmlItem) {

            if ($xmlItem['url'] === $url) {

                return $key;
            }
        }

        return false;
    }

    public function deleteUrl($urlKey)
    {
        $siteMapUrls = $this->getXmlOutOfCache();
        ArrayHelper::remove($siteMapUrls, $urlKey);
        $this->urls = $siteMapUrls;
        $this->setXmlInCache();
    }

    public function updateUrl($modelOwner, $urlKey, $modelSetting)
    {
        $url = $this->getUrlOutSettings($modelSetting, $modelOwner);
        $this->urls[$urlKey]['url'] = $url;
        $this->urls[$urlKey]['lastMod'] = date(DATE_W3C);

        $this->setXmlInCache();
    }

    public function insertUrl($modelOwner, $modelSetting)
    {
        $url = $this->getUrlOutSettings($modelSetting, $modelOwner);
        $this->addUrl($url, $modelSetting['updates']);
        $this->setXmlInCache();
    }

    public function getUrlOutSettings($modelSetting, $modelOwner)
    {
        $slugItem = $modelSetting['slugItem'];
        $url = Yii::$app->urlManager->createUrl(['/' . $modelSetting['link'] . '/' . $modelOwner->$slugItem]);
        $url = str_replace('/admin', '', $url);

        return $url;
    }

    //cache methods
    public function hasXmlInCache()
    {
        $empty = true;
        if (!Yii::$app->cacheFrontend->get('sitemap')) {
            $empty = false;
        }

        return $empty;
    }

    public function setXmlInCache()
    {
        Yii::$app->cacheFrontend->set('sitemap', $this->urls, 3600 * 12);
    }

    public function getXmlOutOfCache()
    {
        return Yii::$app->cacheFrontend->get('sitemap');
    }

    public function getShortClass($className)
    {
        if (preg_match('@\\\\([\w]+)$@', $className, $matches)) {
            $className = $matches[1];
        }

        return $className;
    }
}