<?php
namespace backend\controllers;

use common\component\BackendBaseController;
use common\models\ShopConfig;
use yii\filters\VerbFilter;
use common\component\UtilD;
use common\component\GoogleSitemap;
use common\component\GoogleSitemapItem;
class SitemapController extends BackendBaseController
{
    public function  behaviors()
    {
        return [
            'verbs' =>[
                'class' => VerbFilter::className(),
                'actions' => [
                'list' => ['GET'],
                        'index'  => ['GET'],
                        'save-post'=>['POST'],
                        ]
                    ]
                ];
    }
    
    public function actionIndex() {
        $config = unserialize(ShopConfig::loadFieldByCode('sitemap'));
        return $this->render('sitemap',[
            'config'=>$config,
            'arr_changefreq'=>[1,0.9,0.8,0.7,0.6,0.5,0.4,0.3,0.2,0.1],
        ]);
    }
    
    public function actionSavePost() {
        $today = date('Y-m-d');
        $domain = UtilD::getSiteDomain();
        
        $sm =& new GoogleSitemap();
        $smi =& new GoogleSitemapItem($domain, $today, $_POST['homepage_changefreq'], $_POST['homepage_priority']);
        $sm->add_item($smi);
        
        $config = [
            'homepage_changefreq' => $_POST['homepage_changefreq'],
            'homepage_priority' => $_POST['homepage_priority'],
            'category_changefreq' => $_POST['category_changefreq'],
            'category_priority' => $_POST['category_priority'],
            'content_changefreq' => $_POST['content_changefreq'],
            'content_priority' => $_POST['content_priority'],
        ];
        $status = ShopConfig::updateConfigSitemap($config,$sm,$domain,$today);
        if (!$status){
            exit(UtilD::handleResult(false, \Yii::t('common', 'attradd_error')));
        }
        exit(UtilD::handleResult(true, \Yii::t('common', 'attradd_succed')));
    }
}

?>