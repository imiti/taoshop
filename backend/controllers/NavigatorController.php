<?php
namespace backend\controllers;

use common\models\Nav;
use common\component\BackendBaseController;
use yii\helpers\Json;
use yii\filters\VerbFilter;
use common\component\UtilD;
use yii\helpers\Html;
class NavigatorController extends BackendBaseController
{
    public function  behaviors()
    {
        return [
            'verbs' =>[
                'class' => VerbFilter::className(),
                'actions' => [
                    'list' => ['GET'],
                    'ajax-get'=> ['POST','GET'],
                    'add'  => ['GET'],
                    'add-post'=>['POST'],
                    'edit' => ['GET'],
                    'edit-post' => ['POST'],
                    ]
                ]
            ];
    }
    /*
     * 自定义导航列表
     */
    public function actionList(){
        return $this->render('navigator');
    }
    
    public function actionAjaxGet() {
        $page = (int)\Yii::$app->request->post('page',1);
        $pageSize = (int)\Yii::$app->request->post('rows',20);

        $navdb = Nav::getDataByPage($page,$pageSize);
        exit(Json::encode($navdb));
    }
    
    public function actionAdd(){
        $sysmain = Nav::getSysnav();
        return $this->render('navigator_add',['sysmain'=>$sysmain,'act'=>'add-post']);
    }
    
    public function actionAddPost() {
        $item_name = \Yii::$app->request->post('item_name','');
        $item_url  = \Yii::$app->request->post('item_url','');
        $item_ifshow = \Yii::$app->request->post('item_ifshow','');
        $item_opennew = \Yii::$app->request->post('item_opennew','');
        $item_type = \Yii::$app->request->post('item_type','');
        
        if (empty($item_name || empty($item_url))){
            exit(UtilD::handleResult(false, '必要参数不能为空'));
        }
        
        $vieworder = Nav::find()->select("max(view_order)")
                     ->where("type='{$item_type}'")
                     ->scalar();
        $vieworder = (is_null($vieworder)?0:$vieworder)+1;
        $item_vieworder = \Yii::$app->request->post('item_vieworder','');
        $item_vieworder = !empty($item_vieworder)?$item_vieworder:$vieworder;
        $sql = '';
        //如果设置在中部显示 
        if ($item_ifshow == 1 && $item_type == 'middle'){
            $arr = Nav::analyse_uri($item_url); //分析URI
            if ($arr){
                //设置为显示 
                Nav::setShowInNav($arr['type'], $arr['id'], 1);
                $sql = "INSERT INTO ".Nav::tableName()." (name,ctype,cid,is_show,view_order,open_new,url,type) VALUES 
                    ('$item_name','".$arr['type']."','".$arr['id']."','$item_ifshow','$item_vieworder','$item_opennew','$item_url','$item_type')";
            }
        }
        
        if (empty($sql)){
            $sql = "INSERT INTO ".Nav::tableName()." (name,is_show,view_order,open_new,url,type) VALUES ('$item_name','$item_ifshow','$item_vieworder','$item_opennew','$item_url','$item_type')";
        }
        $status = \Yii::$app->db->createCommand($sql)->execute();
        if (!$status){
            exit(UtilD::handleResult(false, '添加失败，请稍后再试'));
        }
        exit(UtilD::handleResult(true, '保存成功'));
    }
    
    /*
     * 编辑自定义导航
     */
    public function actionEdit() {
        $id = (int)\Yii::$app->request->get('id');
        if (!$id){
            $this->system_msg(\Yii::t('common', 'req_null_empty'));
        }
        $row = Nav::find()->where($id)->one();
        $rt['id'] = $row['id'];
        $rt['item_name'] = $row['name'];
        $rt['item_url'] = $row['url'];
        $rt['item_vieworder'] = $row['view_order'];
        $rt['item_ifshow'] = $row['is_show'];
        $rt['item_opennew'] = $row['open_new'];
        $rt['item_type'] = $row['type'];
        
        $sysmain = Nav::getSysnav();
        return $this->render('navigator_add',['rt'=>$rt,'sysmain'=>$sysmain,'act'=>'edit-post']);
    }
    
    /*
     * 编辑提交 
     */
    public function actionEditPost() {
        $id = (int)\Yii::$app->request->post('id');
        $item_name = Html::decode(\Yii::$app->request->post('item_name'));
        $item_url  = Html::decode(\Yii::$app->request->post('item_url'));
        $item_ifshow = \Yii::$app->request->post('item_ifshow');
        $item_opennew = \Yii::$app->request->post('item_opennew');
        $item_type = \Yii::$app->request->post('item_type');
        $item_vieworder = (int)\Yii::$app->request->post('item_vieworder',0);
        
        $row = Nav::find()->select(['ctype','cid','is_show','type'])->where($id)->one();
        $arr = Nav::analyse_uri($item_url);
        
        if ($arr){
            if ($row['ctype'] == $arr['type'] && $row['cid'] == $arr['id']){
                //如果没有修改分类
                if ($item_type != 'middle'){
                    //位置不在中部
                    Nav::setShowInNav($arr['type'], $arr['id'], 0);
                }
            } else {
                //修改了分类
                if ($row['is_show'] == 1 && $row['type'] == 'middle'){
                    Nav::setShowInNav($row['ctype'], $row['cid'], 0);  //设置成不显示
                }
            }
            //分类判断
            if ($item_ifshow != Nav::isShowInNav($arr['type'], $arr['id']) && $item_type == 'middle'){
                Nav::setShowInNav($arr['type'], $arr['id'], $item_ifshow);
            }
            Nav::updateAll(['name'=>$item_name,'ctype'=>$arr['type'],'cid'=>$arr['id'],'is_show'=>$item_ifshow,
                'view_order'=> $item_vieworder,
                'open_new'  => $item_opennew,
                'url'       => $item_url,
                'type'      => $item_type,
            ],'id=:id',[':id'=>$id]);
        }
        else{
            if ($row['ctype'] && $row['cid']){
                Nav::setShowInNav($row['ctype'], $row['cid'], 0);
            }
            Nav::updateAll(['name'=>$item_name,'ctype'=>'','cid'=>'','is_show'=>$item_ifshow,'view_order'=>$item_vieworder,'open_new'=>$item_opennew,'url'=>$item_url,'type'=>$item_type],'id=:id',[':id'=>$id]);
        }
        exit(UtilD::handleResult(true, \Yii::t('common', 'edit_ok')));
    }
    
    
    public function actionDelete() {
        $id = (int)\Yii::$app->request->post('id',0);
        $row = Nav::find()->select('ctype,cid,type')->where($id)->one();
        
        if ($row['type'] == 'middle' && $row['ctype'] && $row['cid']){
            Nav::setShowInNav($row['ctype'], $row['cid'], 0);
        }
        if (!Nav::deleteAll('id=:id',[':id'=>$id])){
            exit(Json::encode(['success'=>false]));
        }
        exit(Json::encode(['success'=>true]));
    }
}

?>