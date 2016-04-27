<?php 
use yii\helpers\Url;
?>
<table id="navigator-list"  class="easyui-datagrid" title="友情连接列表" 
				data-options="toolbar: '#btn-navigator',rownumbers:true,fitColumns:true,singleSelect:true,pagination:true,url:'<?= Url::to('/navigator/ajax-get')?>' ">
			<thead>
				<tr>
					<th data-options="field:'name',width:250,align:'center'">名称</th>
					<th data-options="field:'is_show',width:350,align:'center'" formatter="fmtSta">是否显示</th>
					<th data-options="field:'open_new',width:300,align:'center'"  formatter="fmtSta">是否新窗口</th>
					<th data-options="field:'view_order',width:150,align:'center',sortable:true">排序</th>
					<th data-options="field:'type',width:150,align:'center'" formatter="fmt2Cn">位置</th>
				</tr>
			</thead>
</table>
<div id="btn-navigator" style="padding:4px">  
	<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add2" onclick="javascript:location.href='<?=Url::to('/navigator/add')?>'">添加导航</a>
	<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" onclick="updateNav();">修改</a> 
	<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" onclick="javascript:$('#navigator-list').edatagrid('destroyRow')">删除</a>   
</div>
<script type="text/javascript">
//格式化状态
function fmtSta(val, row){
	var img = "no.png";
	if(val==1){
		img = "ok.png";
	}
	return '<span><img src="/images/'+img+'" /></span>';			
}

function updateNav(){
	var row = $('#navigator-list').datagrid('getSelected');
	if (row){  
		window.parent.addTab('修改自定义导航','<?= Url::to('/navigator/edit?id=')?>'+row.id);
	}else{
		$.messager.alert('警告','请先选择要修改的项目','warning');   
	}
}

function fmt2Cn(val,row){
	var rt = Array();
	rt['top'] = '<?=Yii::t('navigator', 'top')?>';
	rt['middle'] = '<?=Yii::t('navigator', 'middle')?>';
	rt['bottom'] = '<?=Yii::t('navigator', 'bottom')?>';
	return rt[val];
}
$('#navigator-list').edatagrid({  
    destroyUrl: '<?=Url::to('/navigator/delete') ?>'  
}); 
</script>