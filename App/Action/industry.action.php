<?
header("Content-type: text/html; charset=utf-8");

/**
 * 通栏行业
 *
 * @package    Action
 * @author     Alexey
 * @since      2016年2月17日14:26:46
 */
class industryAction extends AppAction
{
	public $debug = true;

	/**
	 * 国内商标列表
	 *
	 * @author    Alexey
	 * @since     2016年2月17日
	 * @access    public
	 * @return    void
	 */
	public function index()
	{
		$this->getSetting();
		//参数
		//$params = $this->getFormData();
		$page    = $this->input('page', 'int', '1');
		$res     = $this->load('industry')->getList($page, $this->rowNum);
		$total   = empty($res['total']) ? 0 : $res['total'];
		$list    = empty($res['rows']) ? array() : $res['rows'];
		$pager   = $this->pager($total, $this->rowNum);
		$pageBar = empty($list) ? '' : getPageBar($pager);

		//$result = array();
		//获取所有联系人
		/*foreach ($list as $k => $v) {
			$result[$k] = $this->load('internal')->getSaleInfo($v['id']);
		}*/
		//$this->set("allTotal", $this->load('internal')->countSale());
		$this->set('total', $total);
		$this->set("pageBar", $pageBar);
		//$this->set('s', $params);
		$this->set('list', $list);
		$this->display();
	}

	/**
	 * 设置排序
	 *
	 * @author    Alexey
	 * @since     2016年2月18日16:37:15
	 *
	 * @access    public
	 * @return    void
	 */
	//
	public function setSort()
	{
		$sort = trim($this->input("s"));
		$t  = trim($this->input("t"));
		$res =  $this->load("industry")->setSort($sort,$t);
        if($res==0){
	        $this->returnAjax(array('code'=>0,'msg'=>'已经无法升降了。'));
        }else{
	        $this->returnAjax(array('code'=>1,'msg'=>'操作成功.'));
        }

	}



	//添加分类
	public function addIndustry()
	{
		$typeName = $this->input('typeName', 'text', '');
		$bzpic    = $this->input('bzpic', 'text', '');
		if ($typeName == "") {
			$this->returnAjax(array('code' => 2, 'msg' => '请填写分类。'));
		}
		if (length($typeName) > 8) {
			$this->returnAjax(array('code' => 2, 'msg' => '分类的名称最多不超过8个中文字符。'));
		}
		if ($bzpic == "") {
			$this->returnAjax(array('code' => 2, 'msg' => '请上传图标。'));
		}
		$data = array(
			'title' => $typeName,
			'icon'  => $bzpic,
			'date'  => time(),
		);
		$res  = $this->load('industry')->addIndustry($data);
		if ($res) {
			$this->returnAjax(array('code' => 1, 'msg' => '操作成功'));
		}
		$this->returnAjax(array('code' => 2, 'msg' => '操作失败'));
	}

	//图片上传
	public function ajaxUploadPic()
	{
		$msg = array(
			'code' => 0,
			'msg'  => '',
			'img'  => '',
		);
		if (empty($_FILES) || empty($_FILES['fileName'])) {
			$msg['msg'] = '请上传图片';
			$this->returnAjax($msg);
		}
		$obj = $this->load('upload')->upload('fileName', 'img', 51200);
		if ($obj->_imgUrl_) {
			$msg['code'] = 1;
			$msg['img']  = $obj->_imgUrl_;
		} else {
			$msg['msg'] = $obj->msg;
		}
		$this->returnAjax($msg);
	}

	protected function getSetting()
	{
		$saleStatus = C("SALE_STATUS");
		$saleSource = C("SOURCE");
		$saleType   = C("SALE_TYPE");
		$tmNums     = C("TM_NUMBER");
		$tmLabel    = C("TM_LABEL");
		$tmType     = C("TYPES");
		$salePlat   = C("SALE_PLATFORM");
		$tmPrice    = C("SEARCH_PRICE");
		$tmClass    = range(1, 45);

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


	//导入数据弹出界面
	public function import()
	{
		$source = C('SOURCE');
		$this->set('source', $source);
		$this->display();
	}


}

?>