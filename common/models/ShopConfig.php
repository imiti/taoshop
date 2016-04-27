<?php

namespace common\models;

use Yii;
use common\component\UtilD;
use common\component\ActiveRecordD;
use common\component\GoogleSitemapItem;
/**
 * This is the model class for table "{{%shop_config}}".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $code
 * @property string $type
 * @property string $store_range
 * @property string $store_dir
 * @property string $value
 * @property integer $sort_order
 * @property string $notice
 */
class ShopConfig extends ActiveRecordD
{
    const CACHE_KEY = 'shopconfig_key';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop_config}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'sort_order'], 'integer'],
            [['code', 'type', 'value'], 'required'],
            [['code'], 'string', 'max' => 32],
            [['type'], 'string', 'max' => 10],
            [['store_range', 'store_dir','notice'], 'string', 'max' => 255],
            [['value'], 'string', 'max' => 1024]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'parent_id' => Yii::t('app', 'Parent ID'),
            'code' => Yii::t('app', 'Code'),
            'type' => Yii::t('app', 'Type'),
            'store_range' => Yii::t('app', 'Store Range'),
            'store_dir' => Yii::t('app', 'Store Dir'),
            'value' => Yii::t('app', 'Value'),
            'sort_order' => Yii::t('app', 'Sort Order'),
            'notice' => \yii::t('app', 'notice'),
        ];
    }
    
    
    public static function getAllConfigData(){
        return static::findAll();
    }
    
    /**
    * 获得设置信息
    *
    * @param   array   $groups     需要获得的设置组
    * @param   array   $excludes   不需要获得的设置组
    *
    * @return  array
    */
    public static function get_settigs($groups=null,$excludes=null){
        $config_groups = $excludes_groups = '';
        if (!empty($groups)){
            foreach ($groups as $key=>$val){
                $config_groups .= " AND (id='$val' OR parent_id='$val')";
            }
        }
        
        if (!empty($excludes)){
            foreach ($excludes as $key=>$val){
                $excludes_groups .= " AND (parent_id<>'$val' AND id<>'$val')";
            }
        }
        
        $sql = "SELECT * FROM ".self::tableName()." WHERE type <> 'hidden' $config_groups $excludes_groups ORDER BY parent_id,sort_order,id";
        $item_list = self::findBySql($sql)->all();
        
        /* 整理数据 */
        $group_list = [];
        foreach ($item_list as $val){
            $item = $val->getAttributes();
            $pid = $item['parent_id'];
            $item['name'] = \yii::t("config", $item['code']);
            
            if ($item['code'] == 'sms_shop_mobile'){
                $item['url'] = 1;
            }
            if ($pid == 0){
                if ($item['type'] == 'group'){
                    $group_list[$item['id']] = $item;
                }
            }
            else{
                if (isset($group_list[$pid])){
                    if ($item['store_range']){
                        $item['store_options'] = explode(',', $item['store_range']);
                        foreach ($item['store_options'] as $k=>$v){
                            $item['display_options'][$k] = \yii::t('config', $item['code'].'_'.$v);
                        }
                    }
                    $group_list[$pid]['vars'][] = $item;
                }
            }
        }
        return $group_list;
    }
    
    
    /*
     * 清除缓存
     */
    public static function clearCache(){
        $key = md5(self::CACHE_KEY."wholeVariable");
        UtilD::setCache(__CLASS__, $key, false,-1);
    }
    /**
     * 从缓存中获取一条记录，无则从DB找查找
     * @param string $code
     * @return mixed
     */
    public static function loadRowData($code){
        $key = md5(self::CACHE_KEY."_code_".$code);
        $data = UtilD::getCache(__CLASS__, $key);
        if (!$data){
            $data = static::find()->where("code='{$code}'")->one();
            if (is_null($data)) return false;
            UtilD::setCache(__CLASS__, $key, $data);
        }
        return $data;
    }
    
    /**
     * 从缓存中获取字段值
     * @param string $code
     * @param string $field
     * @return mixed
     */
    public static function loadFieldByCode($code,$field='value') {
       $data = self::loadRowData($code);
       if (!$data) return '';
       return isset($data[$field])?$data[$field]:'';
    }
    
    /**
     * 更新配置文件的sitemap
     * @param string $config
     */
    public static function updateConfigSitemap($config,$sm,$domain,$today){
        if (!static::updateAll(['value'=>serialize($config)],"code='sitemap'")){
            return false;
        }
        $res = Category::find()->select(['id','cat_name'])->orderBy('parent_id')->column();
        foreach ($res as $row){
            $smi =& new GoogleSitemapItem($domain.UtilD::build_uri('category',[$row['id']],$row['cat_name']),$today,
                $config['category_changefreq'],$config['category_priority']
                );
            $sm->add_item($smi);
        }
        $res = ArticleCat::find()->select(['id','cat_name'])->where(['cat_type'=>1])->column();
        foreach ($res as $row){
            $smi =& new GoogleSitemapItem($domain.UtilD::build_uri('article_cat', ['acid'=>$row['id']],$row['cat_name']),$today,$config['category_changefreq'],$config['category_priority']);
            $sm->add_item($smi); 
        }
        $res = Goods::find()->select(['id','goods_name'])->where('is_delete=0')->column();
        foreach ($res as $row){
            $smi =& new GoogleSitemapItem($domain . UtilD::build_uri('goods', ['gid'=>$row['id']],$row['goods_name']),$today,$config['content_changefreq'],$config['content_priority']);
            $sm->add_item($smi);
        }
        //文章
        $res = Article::find()->select(['id','title','file_url','open_type'])->where('is_open=1')->column();
        foreach ($res as $row){
            $article_url = $row['open_type'] != 1 ? UtilD::build_uri('article', ['aid'=>$row['id']],$row['title']) : trim($row['file_url']);
            $smi =& new GoogleSitemapItem($domain . $article_url,$today,$config['content_changefreq'],$config['content_priority']);
            $sm->add_item($smi);
        }
        $sm_file = 'sitemaps.xml';
        if ($sm->build($sm_file)){
            return true;
        }
        else{
            
        }
    }
}
