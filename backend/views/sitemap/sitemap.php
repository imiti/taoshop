<?php 

use yii\helpers\Html;
use yii;
use yii\helpers\Url;
?>
<p class="easyui-note"><?=Yii::t('sitemap', 'sitemaps_note')?></p>
<div class="main-layout">
    <form id="sitemap-fm" method="post">
    <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>" />
    <table width="100%" cellspacing="1" cellpadding="3">
        <tr>
            <td class="label"><?=Yii::t('sitemap', 'homepage_changefreq') ?>:</td>
            <td>
                <?=Html::dropDownList('homepage_priority',$config['homepage_priority'],$arr_changefreq,['class'=>'easyui-combobox','style'=>'width:80px'])?>
                <?=Html::dropDownList('homepage_changefreq',$config['homepage_changefreq'],Yii::t('sitemap', 'priority'),['class'=>'easyui-combobox','style'=>'width:100px'])?>
            </td>
        </tr>
        <tr>
            <td class="label"><?=Yii::t('sitemap', 'category_changefreq') ?></td>
            <td>
                <?=Html::dropDownList('category_priority',$config['category_priority'],$arr_changefreq,['class'=>'easyui-combobox','style'=>'width:80px']) ?>
                <?=Html::dropDownList('category_changefreq',$config['category_changefreq'],Yii::t('sitemap', 'priority'),['class'=>'easyui-combobox','style'=>'width:100px'])?>
            </td>
        </tr>
        <tr>
            <td class="label"><?=Yii::t('sitemap', 'content_changefreq') ?></td>
            <td>
                <?=Html::dropDownList('content_priority',$config['content_priority'],$arr_changefreq,['class'=>'easyui-combobox','style'=>'width:80px']) ?>
                <?=Html::dropDownList('content_changefreq',$config['content_changefreq'],Yii::t('sitemap', 'priority'),['class'=>'easyui-combobox','style'=>'width:100px'])?>
            </td>
        </tr>
        <tr style="height: 50px;">
            <td>&nbsp;
            </td>
            <td>
                <div style="text-align:left;padding:5px;line-height:50px">
                    	    	<a href="javascript:void(0)" class="easyui-linkbutton" onclick="submitForm()" data-options="iconCls:'icon-save'">&nbsp;&nbsp;<?=Yii::t('common', 'button_submit')?>&nbsp;&nbsp; </a>
                    	    	<a href="javascript:void(0)" class="easyui-linkbutton" onclick="clearForm()" data-options="iconCls:'icon-undo'"> &nbsp;&nbsp;<?=Yii::t('common', 'button_reset')?> &nbsp;&nbsp;</a>
                 </div>
            </td>
        </tr>
    </table>
    </form>
</div>
<script type="text/javascript">
function submitForm(){
	$('#sitemap-fm').form('submit',{
		url: '<?=Url::to('/sitemap/save-post')?>',  
		onSubmit: function(){
			if(!$(this).form('validate')) {
				return false;
			};
            return true;
		},  
		success: function(result){  
			var result = eval('('+result+')');  
			if (result.key){  
				window.self.location = '<?=Url::to('/sitemap/index')?>';
			} else {  
				$.messager.show({  
					title: '错误提示',  
					msg: result.keyMain
				});
			}  
		} 
	});
}
function clearForm(){
	$('#sitemap-fm').form('clear');
}
</script>