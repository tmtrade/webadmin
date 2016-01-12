<?
/**
 * 国内商标
 *
 * @package	Action
 * @author	Xuni
 * @since	2016-01-8
 */
class internalAction extends AppAction
{
//	public $debug = true;
	
	/**
	 * 国内商标列表
	 * 
	 * @author	Xuni
	 * @since	2016-01-8
	 * @access	public
	 * @return	void
	 */
	public function index()
	{	
		$this->getSetting();
		//参数
		$params = $this->getFormData();
		$page 	= $this->input('page', 'int', '1');

		$result 	= $this->load('internal')->getList($params, $page, $this->rowNum);
		$pager 		= $this->pager($result['total'], $this->rowNum);
        $pageBar 	= empty($result['rows']) ? '' : getPageBar($pager);

		$total 	= $result['total'];
		$list 	= $result['rows'];

		//获取所有联系人
		foreach ($list as $k => $v) {
			$list[$k]['contact'] = $this->load('internal')->getSaleContact($v['id']);
		}

        $this->set("allTotal",$this->load('internal')->countSale());
		$this->set('total', $total);
        $this->set("pageBar",$pageBar);
		$this->set('s', $params);
		$this->set('saleList', $list);
		$this->display();
	}

	//添加商品
	public function create()
	{
		$this->display();
	}


	//添加/编辑 联系人
	public function contact()
	{
		$sId = $this->input('saleId', 'int', 0);
		$cId = $this->input('cId', 'int', 0);
		if ( $cId > 0 ){
			$contact = $this->load('internal')->getSaleContact($sId, $cId);
		}else{
			$contact = array();
		}
		$this->set('cId', $cId);
		$this->set('saleId', $sId);
		$this->set('contact', $contact);
		$this->getSetting();
		$this->display();
	}

	//（操作）添加/编辑 联系人
	public function setContact()
	{
		//参数
		$params = $this->getFormData();
		if ( $params['saleId'] <= 0 ){
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误'));
		}
		if ( $params['source'] <= 0 ){
			$this->returnAjax(array('code'=>2,'msg'=>'请选择来源'));
		}
		if ( $params['name'] == '' ){
			$this->returnAjax(array('code'=>2,'msg'=>'请输入联系人'));
		}
		if ( $params['phone'] == '' ){
			$this->returnAjax(array('code'=>2,'msg'=>'请输入联系人电话'));
		}
		if ( $params['price'] <= 0 ){
			$this->returnAjax(array('code'=>2,'msg'=>'请输入底价'));
		}
		if ( $params['date'] <= 0 ) $params['date'] = time();
		$cId = $params['cId'];
		unset($params['cId']);
		if ( $cId <= 0 ){
			$type = 8;
			$res = $this->load('internal')->addContact($params, $params['saleId']);
			$cId = $res;
		}else{
			$type = 9;
			$res = $this->load('internal')->updateContact($params, $cId);
		}
		if ( $res ){
			$this->load('log')->addSaleLog($params['saleId'], $type, '联系人ID：'.$cId);//记录日志
			$this->returnAjax(array('code'=>1,'msg'=>'成功'));
		}
		$this->returnAjax(array('code'=>2,'msg'=>'操作失败'));
	}

	//添加商品
	public function add()
	{
		$this->display();
	}

	//编辑商品
	public function edit()
	{
		$id 	= $this->input('id', 'int', 0);
		if ( $id <= 0 ){
			MessageBox::halt('参数错误');
		}
		$sale 		= $this->load('internal')->getSaleInfo($id);
		$tminfo 	= $this->load('trademark')->getTminfo($sale['number']);
		$isBlack 	= $this->load('blacklist')->existBlack($sale['number']);
		$log 		= $this->load('log')->getSaleLog($id);

		$this->getSetting();
		$this->set('isBlack', $isBlack);
		$this->set('sale', $sale);
		$this->set('tminfo', $tminfo);
		$this->set('log', $log);
		$this->display();
	}

	//商品下架
	public function doDown()
	{
		$id 	= $this->input('id', 'int', 0);
		$reason = $this->input('reason', 'string', '');
		if ( $id <= 0 || empty($reason) ){
			$this->returnAjax(array('code'=>2));
		}
		$res = $this->load('internal')->saleDown($id, $reason);
		if ( $res ) $this->returnAjax(array('code'=>1));
		$this->returnAjax(array('code'=>2));
	}

	//商品上架
	public function doUp()
	{
		$id 	= $this->input('id', 'int', 0);
		if ( $id <= 0 ){
			$this->returnAjax(array('code'=>2));
		}
		$res = $this->load('internal')->saleUp($id);
		if ( $res ) $this->returnAjax(array('code'=>1));
		$this->returnAjax(array('code'=>2));
	}

	//删除黑名单
	public function outBlack()
	{
		$id 	= $this->input('id', 'int', 0);
		$number = $this->input('number', 'string', '');
		if ( empty($number) || $id <= 0 ) $this->returnAjax(array('code'=>2)); 

		$list 	= array_filter( array_unique( explode(',', $number) ) );
		$res 	= $this->load('blacklist')->deleteBlack($list);
		if ( $res ){
			$this->load('log')->addSaleLog($id, 7);//删除黑名单
			$this->returnAjax(array('code'=>1));
		}
		$this->returnAjax(array('code'=>2));
	}

	//添加黑名单
	public function setBlack()
	{
		$id 	= $this->input('id', 'int', 0);
		$number = $this->input('number', 'string', '');
		$reason = $this->input('reason', 'string', '');
		if ( empty($number) || $id <= 0 ) $this->returnAjax(array('code'=>2)); 

		$list 	= array_filter( array_unique( explode(',', $number) ) );
		$res 	= $this->load('blacklist')->addBlack($list, $reason);
		if ( $res ) {
			$this->load('internal')->saleDown($id, '添加黑名单');//下架商品
			$this->load('log')->addSaleLog($id, 6, $reason);//添加黑名单 
			$this->returnAjax(array('code'=>1));
		}
		$this->returnAjax(array('code'=>2));
	}

	protected function getSetting()
	{
		$saleStatus = C("SALE_STATUS");
		$saleSource = C("SOURCE");
		$saleType 	= C("SALE_TYPE");
		$tmNums 	= C("TM_NUMBER");
		$tmLabel 	= C("TM_LABEL");
		$tmType		= C("TYPES");
		$salePlat 	= C("SALE_PLATFORM");
		$tmPrice 	= C("SEARCH_PRICE");
		$tmClass 	= range(1,45);
		
		$this->set('tmType', $tmType);
		$this->set('tmNums', $tmNums);
		$this->set('tmClass', $tmClass);
		$this->set('tmPrice', $tmPrice);
		$this->set('tmLabel', $tmLabel);
		$this->set('saleType', $saleType);
		$this->set('saleSource', $saleSource);
		$this->set('saleStatus', $saleStatus);
		$this->set('salePlat', $salePlat);
	}
	
}
?>