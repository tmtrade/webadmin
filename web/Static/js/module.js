//±à¼­ÁªÏµÈË
function addLink(moduleId, lId)
{
	if ( moduleId <= 0 || saleId == '' ) return false;
	var url = '?saleId='+saleId+'&cId='+cId;
	layer.open({
		type: 2,
		title: false,
		closeBtn: false,
		area: ['501px', '485px'],
		content: '/module/link/'+url,
	});
}