/*
 *通栏添加、编辑js
 */
//添加分类
function addindustry()
{
	var data    = $("#addForm").serialize();
	if ( data == "" ){
		layer.msg('请填写添加内容');
		return false;
	}
	$.ajax({
		type : 'post',
		url  : '/industry/addIndustry/',
		data : data,
		dataType : 'json',
		success : function (data){
			if (data.code==1){
				layer.msg('操作成功！', {
					time: 1000 //2秒关闭（如果不配置，默认是3秒）
				}, function(){
                    parent.location.reload();
                    //window.location.reload(true);
				});
			}else{
				layer.msg(data.msg, {
					time: 1000 //2秒关闭（如果不配置，默认是3秒）
				});
			}
		},
		error : function (data){
			layer.msg('操作失败，请检查您输入的内容是否正确后重新尝试。', {
				time: 2000 //2秒关闭（如果不配置，默认是3秒）
			});
		}
	});
}
// t=1 降、t=2升。
function setSort(t,s){
    $.ajax({
        type : 'post',
        url  : '/industry/setSort/',
        data : 's='+s+'&t='+t,
        dataType : 'json',
        success : function (data){
            if (data.code==1){
                layer.msg('操作成功！', {
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
}
