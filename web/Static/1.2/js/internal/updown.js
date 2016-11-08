/*
 *商品上下架js
 */
//商品下架
function doDown(id)
{
	if ( $.trim(id) == '' ){
		layer.msg('参数错误', {
			time: 2000 //2秒关闭（如果不配置，默认是3秒）
		});
	}
	layer.prompt({
		formType: 2,
		scrollbar: false,
		value: '',
		maxlength: 100,
		title: '请输入下架原因'
	}, function(value, index, elem){
		if ( $.trim(value) == ''){
			layer.tips('请输入下架原因',elem);
			return false;
		}else{
			$.ajax({
				type : 'post',
				url  : '/internal/doDown/',
				data : {'id':id,'reason':value},
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
							time: 1000 //2秒关闭（如果不配置，默认是3秒）
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
	});
}
//商品上架
function doUp(id)
{
	layer.confirm('确认要上架吗？请确认相关数据已审核成功！', {
		btn: ['确认','取消'], //按钮
		scrollbar: false,
	}, function(elem){
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
	});
}

