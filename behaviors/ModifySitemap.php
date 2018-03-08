<?php

namespace deadly299\sitemap\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class ModifySitemap extends Behavior
{
    private $doReset = true;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_DELETE => 'deleteLink',
            ActiveRecord::EVENT_AFTER_UPDATE=> 'createLink',
            ActiveRecord::EVENT_AFTER_INSERT => 'createLink',
        ];
    }

    public function deleteLink()
    {
        if ($this->doReset) {
            Yii::$app->siteMapBuilder->updateXmlUlr('delete', $this->owner);
            $this->doReset = false;
        }
    }

    public function createLink()
    {
        if ($this->doReset) {
            Yii::$app->siteMapBuilder->updateXmlUlr('update', $this->owner);
            $this->doReset = false;
        }
    }
}