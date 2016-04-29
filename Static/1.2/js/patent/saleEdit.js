/*
 *商品编辑js
 */

//编辑包装信息
function setBzxx()
{
	var patentId    = $("#patentId").val();
	var data        = $("#bzxxForm").serialize();
	var phone 	= $("#viewPhone").children('option:selected').val();
	if ( phone == 0 ){
		layer.msg('请选择一个联系号码');
		return false;
	}
	$.ajax({
		type : 'post',
		url  : '/patent/setEmbellish/',
		data : data+'&patentId='+patentId,
		dataType : 'json',
		success : function (data){
			if (data.code==1){
				layer.msg('操作成功！', {
					time: 1000 //2秒关闭（如果不配置，默认是3秒）
				}, function(){
					window.location.reload();
				});
			}else{
				layer.msg('操作失败，请确认数据是否正确。', {
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

//编辑价格信息
function setPrice()
{
	var isSale      = $("#isSale").is(':checked');
	var isLicense   = $("#isLicense").is(':checked');
	var isOffprice  = $("#isOffprice").is(':checked');
	var patentId      = $("#patentId").val();
	if ( !isSale && !isLicense ){
		layer.msg('至少选择 出售与许可 中一项');
		return false;
	}
	if ( isSale ){
		var type = $("#priceType").children('option:selected').val();
		if ( type == 1 ){//定价
			if ( $("#price").val() <= 0 ){//必须输入销售价格
				layer.msg('请输入销售价格');
				return false;
			}
			if ( isOffprice ){//选择特价
				if ( $("#salePrice").val() <= 0  ){
					layer.msg('请输入特价价格');
					return false;
				}
				var radio = $('input:radio[name="priceDate"]:checked').val();
				if ( radio == null ){
					layer.msg('请选择一种特价方式');
					return false;
				}else if ( radio == 1 && $("#salePriceDate").val() == '' ){
					layer.msg('请选择特价限时时间');
					return false;
				}
			}
		}
	}
	var data = $("#priceForm").serialize();
	$.ajax({
		type : 'post',
		url  : '/patent/setPrice/',
		data : data+'&patentId='+patentId,
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

//编辑备注
function setMemo()
{
	var patentId  = $("#patentId").val();
	var memo    = $("#memo").val();
	if ( $.trim(memo) == '' ){
		layer.msg('请输入备注信息');
		return false;
	}
	$.ajax({
		type : 'post',
		url  : '/patent/setMemo/',
		data : {'patentId':patentId, 'memo':memo},
		dataType : 'json',
		success : function (data){
			if (data.code==1){
				layer.msg('操作成功！', {
					time: 1000 //2秒关闭（如果不配置，默认是3秒）
				}, function(){
					window.location.reload();
				});
			}else{
				layer.msg('操作失败，请确认数据是否正确。', {
					time: 1000 //2秒关闭（如果不配置，默认是3秒）
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

//编辑联系人
function setContact(patentId, cId)
{
	if ( patentId <= 0 || patentId == '' ) return false;
	var url = '?patentId='+patentId+'&cId='+cId;
	layer.open({
		type: 2,
        title: false,
        scrollbar: false,
		closeBtn: false,
		area: ['501px', '485px'],

		content: '/patent/contact/'+url,
	});
}

//删除联系人
function delContact(patentId, cId, nums)
{
	if ( nums <= 1 ){
		layer.msg('只有一个联系人时，不能删除！', {
			time: 3000 //2秒关闭（如果不配置，默认是3秒）
		});
		return false;
	}	
	if ( patentId <= 0 || patentId == '' ) return false;
	if ( cId <= 0 || cId == '' ) return false;

	layer.confirm('确认要删除此联系人吗？<br><span class="red">注意：联系人必须至少保留一个。<br>如商品为上架状态，要保留一个审核过的联系人！</span>', {
		btn: ['删了','算了'] //按钮
	}, function(){
		$.ajax({
			type : 'post',
			url  : '/patent/delContact/',
			data : {'id':cId,'patentId':patentId},
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

//审核联系人
function setVerify(patentId, id)
{
	if ( $.trim(patentId) == '' || $.trim(id) == '' ){
		layer.msg('参数错误', {
			time: 1000 //2秒关闭（如果不配置，默认是3秒）
		});
		return false;
	}
	layer.confirm('请确认相关信息已正确！<br> <span class="red">注意：通过审核会自动上架商品。</span>', {
		btn: ['通过','再想想'] //按钮
	}, function(){
		$.ajax({
			type : 'post',
			url  : '/patent/setVerify/',
			data : {'id':id,'patentId':patentId},
			dataType : 'json',
			success : function (data){
				if (data.code==1){
					layer.msg('操作成功！', {
						time: 1000 //2秒关闭（如果不配置，默认是3秒）
					}, function(){
						window.location.reload();
					});
				}else{
					layer.msg('操作失败，请确认数据是否正确。', {
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
	});
}

//驳回联系人
function delVerify(patentId, id, nums)
{
	if ( nums <= 1 ){
		layer.msg('只有一个联系人时，请直接删除此商品即可！', {
			time: 3000 //2秒关闭（如果不配置，默认是3秒）
		});
		return false;
	}
	if ( $.trim(patentId) == '' || $.trim(id) == '' ){
		layer.msg('参数错误', {
			time: 1000 //2秒关闭（如果不配置，默认是3秒）
		});
		return false;
	}
	layer.prompt({
		formType: 2,
		scrollbar: false,
		value: '',
		maxlength: 100,
		title: '请输入驳回联系人的原因'
	}, function(value, index, elem){
		if ( $.trim(value) == ''){
			layer.tips('请输入原因',elem);
			return false;
		}else{
			$.ajax({
				type : 'post',
				url  : '/patent/delVerify/',
				data : {'id':id,'patentId':patentId,'reason':value},
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
						layer.tips(msg,elem);
					}
				},
				error : function (data){
					layer.msg('操作失败，请检查您输入的内容是否正确后重新尝试。', {
						time: 2000 //2秒关闭（如果不配置，默认是3秒）
					});
				}
			});
		}
	});
}