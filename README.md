Yii2-sitemap
==========
Это модуль генерирует sitemap на основе моделей и статических ссылок

Установка
==========

Выполнить команду

```
php composer require deadly299/yii2-sitemap "@dev"
```

Или добавить в composer.json

```
"deadly299/yii2-sitemap": "@dev",
```

И выполнить

```
php composer update
```
Подключение и настройка
---------------------------------

Модуль

```php
    'modules' => [
               'sitemap' => [
                   'class' => 'deadly299\sitemap\Module',
                   //статичские ссылки
                   'otherLinks' => [
                       [
                           'link' => '/controller/action',
                           'updates' => 'never',
                       ],
                       //...
                   ],
                   //генерация ссылок на основе модели
                   'sitemapModels' => [
                       [
                           'class' => 'deadly299\models\Model',
                           //дополнительные условия ($query->andWhere(conditions))
                           'conditions' => ['show' => 'no'],
                           //link зависит от настроек url-manager
                           'link' => 'url',
                           //get параметр
                           'slugItem' => 'slug',
                           'updates' => 'weekly',
                       ],
                      //...
                   ],
               ],
        //...
    ]
```
Компоненты

```php
    'component' => [
        'siteMapBuilder' => [
            'class' => 'deadly299\sitemap\SiteMapBuilder',
        ],
        'cacheFrontend' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => Yii::getAlias('@frontend') . '/runtime/cache'
        ],
    ],
    /...
```

Настраиваем Url-manager(логично в frontend/web)
```php
'urlManager' => [
    //..
    'rules' => [
        'sitemap.xml' => '/sitemap/sitemap/index',
       //...
    ],
    //..
],
```

Использование
==========
sitemap доступен по ссылке project/sitemap.xml(зависит от того как настроен url-manager)

Тому кто будут ставить это модуель
==========
Модуль в разработке, пулл реквестам буду рад и замечаниям.