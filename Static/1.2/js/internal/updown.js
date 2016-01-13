/*
 *商品上下架js
 */
function doUpDown(status, id)
{
	if(status != 'up' && status != 'down'){
		return false;
	}
	if ( status == 'down' ){
		layer.prompt({
			formType: 2,
			value: '',
			maxlength: 100,
			title: '请输入下架原因'
		}, function(value, index, elem){
			if ( $.trim(value) == ''){
				layer.tips('请输入下架原因',elem);
				return false;
			}else{
				_doDown(id, value);
			}
		});
	}else if (  status == 'up' ){
		layer.confirm('确认要上架吗？请确认相关数据已审核成功！', {
			btn: ['确认','取消'] //按钮
		}, function(elem){
			_doUp(id);
		});
	}else{
		return false;
	}
}
function _doDown(id, reason)
{
	$.ajax({
		type : 'post',
		url  : '/internal/doDown/',
		data : {'id':id,'reason':reason},
		dataType : 'json',
		success : function (data){
			if (data.code==1){
				layer.msg('操作成功！', {
					time: 1000 //2秒关闭（如果不配置，默认是3秒）
				}, function(){
					window.location.reload();
				});
			}else{
				layer.msg('操作失败，请重试。', {
					time: 1000 //2秒关闭（如果不配置，默认是3秒）
				}, function(){
					doUpDown('down', id);
				});
			}
		},
		error : function (data){
			layer.msg('操作失败，请稍后重试。', {
				time: 2000 //2秒关闭（如果不配置，默认是3秒）
			});
		}
	});
}
function _doUp(id)
{
	$.ajax({
		type : 'post',
		url  : '/internal/doUp/',
		data : {'id':id},
		dataType : 'json',
		success : function (data){
			if (data.code==1){
				layer.msg('操作成功！', {
					time: 1000 //2秒关闭（如果不配置，默认是3秒）
				}, function(){
					window.location.reload();
				});
			}else{
				var msg = data.msg == undefined ? '操作失败，请重试' : data.msg;
				layer.msg(msg, {
					time: 3000 //2秒关闭（如果不配置，默认是3秒）
				});
			}
		},
		error : function (data){
			layer.msg('操作失败，请稍后重试。', {
				time: 2000 //2秒关闭（如果不配置，默认是3秒）
			});
		}
	});
}
