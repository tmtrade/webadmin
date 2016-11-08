
//小选项分类切换
$('#industrycategory').change(function(){
	var category = $(this).val();
	var options = "<option value='' >请选择小类</option>";
	if(category){
		options += getSmallCate(category);
	}
	$('#industrycategorySmall').html(options);
});

//精品图片
$('#isgood').change(function(){
	var file = $("#goodpictd") 
	if($(this).val() == 2){
		file.show();
		file.next().show();
	}else{
		$('#goodpic').val('');
		file.hide();
		file.next().hide();
	}
})

//提交循环
function foreachObj(obj,thistype){
	var thisstr = '';
	obj.each(function(){
		if(this.checked){
			if(thistype == 1){
				thisstr += this.value+",";
			}else{
				thisstr = this.value;
			}
		}
	})
	return thisstr;
}


//复选框，单独一个选中
function checkOneShow(obj){
	var thissbstr = '';
	obj.bind('change',function(){
		var  thissbstr = $(this).val();
		obj.each(function(){
			if($(this).val() != thissbstr){
				$(this).prop('checked',false);
			}
		})
	})
}

var 
	pregTelel   = /^((0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$/,
	pregPhone = /^1[3|4|5|7|8]\d{9}$/,
	phone 	  = $('#phone'),
	number 	  = $('#number'),
	price 	  = $('#price'),
	contact   = $('#contact'),
	source 	  = $('#source'),
	type      = $('#type'),
	classes   = $('#classes');
	goods 	  = $('#goods'),
	proposer  = $('#proposer'),
	validEnd  = $('#validEnd'),
	area      = $('#area'),
	oldarea   = $('#oldarea').val(),
	oldnumber = $('#oldnumber').val(),
	oldclass  = $('#oldclass').val(),

	
area.bind('change',function(){
	if(this.value == 2){
		//国外的显示
		if(this.value == oldarea){
			$('#number').val(oldnumber);
			$('#classes').val(oldclass);
			$("[active='chinadata']").html('');
			$('.detail_imgurl').css('display',"inline");
			//编辑的时候切换到原本就是国外的要出现原始数据
			if(number.val() != '' && classes.val() != '') {
				gettrademarkC();
			}
		}else{
			$('#number').val('');
			$('#classes').val('');
			$('.country').find('input').val('');
			$('.country').find('textarea').val('');
			$('.detail_imgurl').css('display',"none");
			$('#imgurl').val('');
		}

		$('.china').hide();
		$('.country').show();
		$('.img_file').show();
		
	}else if(this.value == 1){
		//国内的显示
		if(this.value == oldarea){
			$('#number').val(oldnumber);
			$('#classes').val(oldclass);
			$('.detail_imgurl').css('display',"inline");
		}else{
			$('#number').val('');
			$('#classes').val('');
			$('.detail_imgurl').css('display',"none");
			$("[active='chinadata']").html('');
			$('#imgurl').val('');
		}

		$('.china').show();
		$('.country').hide();
		$('.img_file').hide();
	}
})
	
	
	//验证提交		
	function formSubmit(obj){

		if(!contact.val()){
			contact.focus();
			alert('请填写联系人');
			return false;
		}
		if(!phone.val()){
			phone.focus();
			alert('请填写联系电话');
			return false;
		 }
		
		if(!source.val()){
			source.focus();
			alert('请选择来源渠道');
			return false;
		}
		
		if(!type.val()){
			type.focus();
			alert('请选择认证方式');
			return false;
		}
		
		if(!number.val()){
			number.focus();
			alert('请填写商标号');
			return false;
		}
		
		if(!classes.val()){
			classes.focus();
			alert('请选择商标分类');
			return false;
		}
		
		if(!price.val()){
			price.focus();
			alert('请填写底价');
			return false;
		}
		if(area.val() == 2){
			if(!$('#name').val()){
				$('#name').focus();
				alert('请填写商标名称');
				return false;
			}
			if(!goods.val()){
				goods.focus();
				alert('请填写商品');
				return false;
			}
			if(!proposer.val()){
				proposer.focus();
				alert('请填写申请人');
				return false;
			}
			
			if(!validEnd.val()){
				validEnd.focus();
				alert('请填写商标有效期');
				return false;
			}

			if(!document.getElementById("img_file").value && !document.getElementById("imgurl").value){
				alert('请上传商标图片');
				return false;
			}
		}
	}
	
	//验证提交		
	function formSubmitEdit2(){
		
		if(!price.val()){
			price.focus();
			alert('请填写底价');
			return false;
		}

		if(industrycategory > 0 && !industrycategorySmall.val()){
			alert('请选择小分类');
			return false;
		}
		
		if(document.getElementById("isgood").value == '2'){
			if(!document.getElementById("goodpic").value && !document.getElementById("goodpicOld").value){
				alert('请上传精品图片');
				return false;
			}
		}
	}
	
	
	
	contact.bind('keyup',function(){
		if(contact.val().length > 13){
			contact.val('');
			alert('联系人不能超过13个字符');
		}
	})
	
	price.bind('keyup',function(){
		if(price.val().length > 13){
			price.val('');
			alert('底价不能超过13位数');
		}
	})
	
	price.bind('blur',function(){
		var salePrice = '';
		if(price.val()){
			var salePrice = getSalePrice(price.val());	
		}
		$('#guideprice').val(salePrice);
	})
	
	$('#salePrice').bind('keyup',function(){
		if($(this).val().length > 13){
			$(this).val('');
			alert('特价价格不能超过13位数');
		}
	})
	
	$('#salePrice').bind('blur',function(){
		if($(this).val() === "0"){
			$(this).val('');
			alert('特价价格必须大于0');
		}
	})
	
	$('#guideprice').bind('keyup',function(){
		if($(this).val().length > 13){
			$(this).val('');
			alert('指导价格不能超过13位数');
		}
	})
	
	$('#valueAnalysis').bind('keyup',function(){
		if($(this).val().length > 100){
			$(this).val('');
			alert('不能输入超过100个字');
		}
	})
		
	//验证商标号只能输入数字字母字符串
	number.bind('keyup',function(){
		var thisvalue  = $(this).val();
		var regCountry = /[\u4E00-\u9FA5]/i;

		//var regChina   = new RegExp("^[0-9]+$","gi");
		// if(area.val() == 1){
			// if(thisvalue.replace(regChina,"").length != 0){
				// var msg = '只能输入数字';
				// $(this).val('');
				// $(this).focus();
				// alert(msg);
			// }
		// }else{
			if(regCountry.test(thisvalue)){
				var msg = '只能输入数字字母和符号';
				$(this).val('');
				$(this).focus();
				alert(msg);
				return false;
			}
		//}
		
	})
	
	
	$("#name").bind("blur",function(){
		name = $(this).val()
		gettypesbyname(name,'saletype',1);
		gettypesbyname(name,'salelength',2);
	})
	
	
	/**通过商标号。获取商标
	**type 1商标类型。2商标字数。
	**item = 控制器
	**/
	function  gettypesbyname(name,item,type){
		var content = {name:name,thistype:type};
		$.ajax({url:"/sale/getcheckdata/",data:content,anysn:false,success: function(value){
			getStringToArr(value,$("[active='"+item+"']"),0);
		}});
	}
	
	classes.bind("blur",function(){
		gettypesbyclass(classes.val(),'platform');
	})
	
	/**通过商标号。获取商标
	**type 1商标类型。2商标字数。
	**item = 控制器
	**/
	function  gettypesbyclass(classes,item){
		content = {classes:classes}
		$.ajax({url:"/sale/getplatform/",data:content,success: function(value){
			var obj = eval('(' + value + ')');
			getStringToArr(obj,$("[active='"+item+"']"),1);
		}});
	}
	
	
	//国内联动获取商标信息
	function  gettrademark(){
		content = {classes:classes.val(),number:number.val(),area:area.val()}
		$.ajax({url:"/sale/trademark/",data:content,success: function(value){
			
			var obj = eval('(' + value + ')');
			if(obj == ''){
				number.val('');
				classes.val('');
				alert('商标号或者分类不正确');
				number.focus();
				return false;
			} 
			if(obj['status'] == '0'){
				number.val('');
				classes.val('');
				alert(obj['statusValue']+'的商标不可出售');
				number.focus();
				return false;
			}
			$.each(obj,function(item,value){				
				if(item == 'imgurl'){
					$('.detail_imgurl').attr('src',value);
					$('.detail_imgurl').show();
					$('#imgurl').attr('name','detail_loadimgurl');
					$('#imgurl').val(value);
				}else if(item == 'saletype' || item == 'salelength'){
					getStringToArr(value,$("[active='"+item+"']"),0);
				}else if(item == 'platform'){
					getStringToArr(value,$("[active='"+item+"']"),1);
				}else{
					$('#'+item).val(value);
					$('.formchina'+item).html(value)
				}
			})
		}});
	}
	
	
	//国外联动获取商标信息
	function  gettrademarkC(){
		var id = $('#hideid').val();
		content = {number:number.val(),id:id}
		$.ajax({url:"/sale/trademarkC/",data:content,async:false,success: function(value){
			var obj = eval('(' + value + ')');
			$.each(obj,function(item,value){				
				if(item == 'imgurl'){
					$('.detail_imgurl').attr('src',value);
					$('.detail_imgurl').show();
					$('#imgurl').val(value);
				}else if(item == 'saletype' || item == 'salelength'){
					getStringToArr(value,$("[active='"+item+"']"),0);
				}else if(item == 'platform'){
					getStringToArr(value,$("[active='"+item+"']"),1);
				}else{
					$('#'+item).val(value);
					$('.formchina'+item).html(value)
				}
			})
		}});
	}
	
	//拆分用分号链接的字符串成数组。然后赋值给对应数组
	function getStringToArr(str,obejct,type){
		$.each(obejct,function(indexO,item){
			$(this).prop("checked",false);	
		})
			
		if(type == 1){
			var strArr= new Array(); //定义一数组 
			strArr=str.split(","); //字符分割 
			$.each(strArr,function(index,item){
				$.each(obejct,function(indexO,itemO){
					if(item == $(this).val()){
						$(this).prop("checked",true);
					}
				})
			})
		}else{
			$.each(obejct,function(indexO,item){
				if(str == $(this).val()){
					$(this).prop("checked",true);
				}
			})
		}
			
	}
	
	//指导价格 浮动率
	function getSalePrice($price){
		var str = '';
		$.ajax({url:"/sale/getSalePrice/",data:{price:$price},async:false,success: function(value){
			str = value;
		}});
		return str;
	}
	