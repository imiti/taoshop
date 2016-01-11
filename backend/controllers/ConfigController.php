<?php
/**
 * 商城配置控制器
 */
namespace backend\controllers;
use common\component\BackendBaseController;
use common\models\ShopConfig;
use yii\db\Query;
use common\component\UtilD;
use common\models\AdminLog;

class ConfigController extends BackendBaseController
{
    
    public function actionIndex()
    {
        return $this->render('index');
    }
    
    
    public function actionListedit(){
        
        //var_dump(unserialize('a:2:{s:4:"type";a:3:{i:0;s:1:"1";i:1;s:1:"2";i:2;s:0:"";}s:4:"rate";a:3:{i:0;d:1;i:1;d:1.5;i:2;d:0;}}'));exit;
        $group_list = ShopConfig::get_settigs([],[5]);
        return $this->render('listedit',['group_list'=>$group_list]);
    }
    
    
    /*
     * 保存商城配置参数
     */
    public function actionPost(){
        $allow_file_types = ['jpg','jpeg','png','gif','bmp','swf'];
        
        $values = \yii::$app->request->post('value',[]);
        /* 保存变量值 */
        $count = count($values);
        
        $arr = [];
        $sql = "SELECT id,value FROM ".ShopConfig::tableName();
        $res = \yii::$app->db->createCommand($sql)->queryAll();
        
        foreach ($res as $row){
            $arr[$row['id']] = $row['value'];
        }
        
        foreach ($_POST['value'] as $key=>$val){
            //值更改则更新
            if ($arr[$key] != $val){
                $sql = "UPDATE ".ShopConfig::tableName()." SET value='".  trim($val)."' WHERE id=".$key;
                $rs = \yii::$app->db->createCommand($sql)->execute();
            }
        }
        
        /* 处理上传文件 */
        $file_var_list = [];
        $sql = "SELECT * FROM ".ShopConfig::tableName()." WHERE parent_id > 0 AND type='file'";
        $res = \yii::$app->db->createCommand($sql)->queryAll();
        
        foreach ($res as $row){
            $file_var_list[$row['code']]=$row;
        }
        foreach ($_FILES as $code=>$file)
        {
            /* 判断用户是否选择了文件 */
            if ((isset($file['error']) && $file['error'] == 0) || (!isset($file['error']) && $file['tmp_name'] != 'none')){
                
                //检查上传的文件类型是否合法 
                if(!UtilD::check_file_type($file['tmp_name'],$file['name'],$allow_file_types)){
                    UtilD::toJavaScriptAlert('','back');
                    \yii::$app->end();
                }
                else{
                    //取得文件路径
                    if ($code == 'shop_logo'){
                        $ext = array_pop(explode('.', $file['name']));
                        $file_name = \yii::getAlias('@static').DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'logo.'.$ext;
                    }
                    elseif ($code == 'watermark'){
                        $ext = array_pop(explode('.', $file['name']));
                        $file_name = $file_var_list[$code]['store_dir'] . 'watermark.'.$ext;
                        if (file_exists($file_var_list[$code]['value'])){
                            @unlink($file_var_list[$code]['value']);
                        }
                    }
                    elseif ($code == 'wap_logo'){
                        $ext = array_pop(explode('.', $file['name']));
                        $file_name = $file_var_list[$code]['store_dir'].'wap.logo.'.$ext;
                        if (file_exists($file_var_list[$code]['value'])){
                            @unlink($file_var_list[$code]['value']);
                        }
                    }
                    else{
                        $file_name = $file_var_list[$code]['store_dir'].$file['name'];
                               
                    }
                    /* 判断是否上传成功 */
                    if(move_uploaded_file($file['tmp_name'], $file_name)){
                        $sql = "UPDATE ".ShopConfig::tableName()." SET value='". $file_name."' WHERE code='".$code."'";
                        $rs = \yii::$app->db->createCommand($sql)->execute();
                    }
                }
            }
        }
        /* 处理发票类型及税率 */
        if (!empty($_POST['invoice_rate'])){
            foreach ($_POST['invoice_rate'] as $key=>$rate){
                $rate = round(floatval($rate),2);
                if($rate < 0){
                    $rate = 0;
                }
                $_POST['invoice_rate'][$key] = $rate;
            }
            $invoice = [
                'type' => $_POST['invoice_type'],
                'rate' => $_POST['invoice_rate']
            ];
            
            $sql = "UPDATE ".ShopConfig::tableName()." SET value='".  serialize($invoice)."' WHERE code ='invoice_type'";
            \yii::$app->db->createCommand($sql)->execute();
        }
        AdminLog::admin_log('edit', 'shop_config');
        /* 清除缓存 */
        ShopConfig::clearCache();
        return $this->redirect(\yii\helpers\Url::to('/config/listedit'));
    }

}
