//设置链接
function setLink(moduleId, lId)
{
	if ( moduleId <= 0 || moduleId == '' ) return false;
	var url = '?moduleId='+moduleId+'&lId='+lId;
	layer.open({
		type: 2,
		title: false,
		closeBtn: false,
		area: ['640px', '400px'],
		content: '/module/link/'+url,
	});
}

//删除链接
function delLink(moduleId, lId)
{	
	if ( moduleId <= 0 || moduleId == '' ) return false;
	if ( lId <= 0 || lId == '' ) return false;

	layer.confirm('确认要删除此推广链接吗？<br>', {
		btn: ['删了','算了'] //按钮
	}, function(){
		$.ajax({
			type : 'post',
			url  : '/module/delLink/',
			data : {'id':lId,'moduleId':moduleId},
			dataType : 'json',
			success : function (data){
				if (data.code==1){
					layer.msg('操作成功', {
						time: 1000 //2秒关闭（如果不配置，默认是3秒）
					}, function(){
						window.location.reload();
					});
				}else{
					var msg = data.msg == undefined ? '操作失败，请确认数据是否正确。' : data.msg;
					layer.msg(msg, {
						time: 2000 //2秒关闭（如果不配置，默认是3秒）
					});
				}
			},
			error : function (data){
				layer.msg('操作失败，请检查您输入的内容是否正确后重新尝试', {
					time: 2000 //2秒关闭（如果不配置，默认是3秒）
				});
			}
		});
	});
}

//设置广告图
function setPic(moduleId, id)
{
	if ( moduleId <= 0 || moduleId == '' ) return false;
	var url = '?moduleId='+moduleId+'&id='+id;
	layer.open({
		type: 2,
		title: false,
		closeBtn: false,
		area: ['640px', '400px'],
		content: '/module/pic/'+url,
	});
}


//删除链接
function delPic(moduleId, id)
{	
	if ( moduleId <= 0 || moduleId == '' ) return false;
	if ( id <= 0 || id == '' ) return false;

	layer.confirm('确认要删除此推广链接吗？<br>', {
		btn: ['删了','算了'] //按钮
	}, function(){
		$.ajax({
			type : 'post',
			url  : '/module/delPic/',
			data : {'id':id,'moduleId':moduleId},
			dataType : 'json',
			success : function (data){
				if (data.code==1){
					layer.msg('操作成功', {
						time: 1000 //2秒关闭（如果不配置，默认是3秒）
					}, function(){
						window.location.reload();
					});
				}else{
					var msg = data.msg == undefined ? '操作失败，请确认数据是否正确。' : data.msg;
					layer.msg(msg, {
						time: 2000 //2秒关闭（如果不配置，默认是3秒）
					});
				}
			},
			error : function (data){
				layer.msg('操作失败，请检查您输入的内容是否正确后重新尝试', {
					time: 2000 //2秒关闭（如果不配置，默认是3秒）
				});
			}
		});
	});
}

