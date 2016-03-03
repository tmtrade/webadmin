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
	//public $debug = true;

	/**
	 * 通栏行业
	 *
	 * @author    Alexey
	 * @since     2016年2月17日
	 * @access    public
	 * @return    void
	 */
	public function index()
	{
		$this->getSetting();
		$page    = $this->input('page', 'int', '1');
		$res     = $this->load('industry')->getList($page, $this->rowNum);
		$total   = empty($res['total']) ? 0 : $res['total'];
		$list    = empty($res['rows']) ? array() : $res['rows'];
		$pager   = $this->pager($total, $this->rowNum);
		$pageBar = empty($list) ? '' : getPageBar($pager);
		$this->set('total', $total);
		$this->set("pageBar", $pageBar);
		$this->set('list', $list);
		$this->display();
	}

	/**
	 * 删除
	 *
	 * @author    Alexey
	 * @since     2016年2月18日16:37:15
	 *
	 * @access    public
	 * @return    void
	 */
	public function del()
	{
		$id = trim($this->input("id"));
		if ($id == "") {
			$this->returnAjax(array('code' => 0, 'msg' => '参数错误！'));
		}
		$res = $this->load("industry")->delAll($id);
		if ($res == 0) {
			$this->returnAjax(array('code' => 0, 'msg' => '删除失败。'));
		} else {
			$this->returnAjax(array('code' => 1, 'msg' => '操作成功.'));
		}
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
	public function setSort()
	{
		$sort = trim($this->input("s"));
		$t    = trim($this->input("t"));
		$res  = $this->load("industry")->setSort($sort, $t);
		if ($res == 0) {
			$this->returnAjax(array('code' => 0, 'msg' => '已经无法升降了。'));
		} else {
			$this->returnAjax(array('code' => 1, 'msg' => '操作成功.'));
		}

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
	public function setPicSort()
	{
		$sort = trim($this->input("s"));
		$t    = trim($this->input("t"));
		$industryId    = trim($this->input("i"));
		$res  = $this->load("industry")->setPicSort($sort, $t,$industryId);
		if ($res == 0) {
			$this->returnAjax(array('code' => 0, 'msg' => '已经无法升降了。'));
		} else {
			$this->returnAjax(array('code' => 1, 'msg' => '操作成功.'));
		}

	}

	/**
	 * 设置排序 分类
	 *
	 * @author    Alexey
	 * @since     2016年2月18日16:37:15
	 *
	 * @access    public
	 * @return    void
	 */
	//
	public function setItemsSort()
	{
		$sort       = trim($this->input("s"));
		$t          = trim($this->input("t"));
		$industryId = trim($this->input("i"));
		$res        = $this->load("industry")->setItemsSort($sort, $t, $industryId);
		if ($res == 0) {
			$this->returnAjax(array('code' => 0, 'msg' => '已经无法升降了。'));
		} else {
			$this->returnAjax(array('code' => 1, 'msg' => '操作成功.'));
		}

	}

	//添加主分类
	public function create()
	{
		$this->display();
	}

	//添加子分类
	public function ztype()
	{
		$id         = $this->input('id', 'int', '0');
		$industryId = $this->input('industryId', 'int', '0');
		$cinfo      = "";
		if ($id != 0) {
			$cinfo = $this->load('industryclass')->getInfoOne($id);
		}
		$this->set('industryId', $industryId);
		$this->set('cinfo', $cinfo);
		$this->display();
	}

	//添加图片
	public function indusPic()
	{
		$id         = $this->input('id', 'int', '0');
		$industryId = $this->input('industryId', 'int', '0');
		$pinfo      = "";
		if ($id != 0) {
			//图片
			$pinfo = $this->load('industrypic')->getInfoOne($id);
		}
		$this->set('industryId', $industryId);
		$this->set('pinfo', $pinfo);
		$this->display();
	}


	//添加分类
	public function addIndustry()
	{
		$typeName = $this->input('typeName', 'text', '');
		$bzpic    = $this->input('bzpic', 'text', '');
		$id       = $this->input('id', 'text', '');
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

		if ($id == "") {
			$res = $this->load('industry')->addIndustry($data);
		} else {
			$res = $this->load('industry')->editIndustry($data, $id);
		}

		if ($res) {
			$this->returnAjax(array('code' => 1, 'msg' => '操作成功'));
		}
		$this->returnAjax(array('code' => 2, 'msg' => '操作失败'));
	}

	//添加分类图片
	public function addIndustryPic()
	{
		$link       = $this->input('link', 'text', '');
		$bzpic      = $this->input('bzpic', 'text', '');
		$industryId = $this->input('industryId', 'int', '0');
		$id         = $this->input('id', 'int', '0');
		if ($industryId == "0") {
			$this->returnAjax(array('code' => 2, 'msg' => '参数错误，非法操作！'));
		}
		if ($bzpic == "") {
			$this->returnAjax(array('code' => 2, 'msg' => '请上传广告图。'));
		}
		if ($link == "") {
			$this->returnAjax(array('code' => 2, 'msg' => '请填写链接。'));
		}
		$data = array(
			'industryId' => $industryId,
			'link'       => $link,
			'pic'        => $bzpic,
			'date'       => time(),
		);
		if ($id == "0") {
			$res = $this->load('industry')->addIndustryPic($data);
		} else {
			$res = $this->load('industry')->editIndustryPic($data, $id);
		}
		if ($res) {
			$this->returnAjax(array('code' => 1, 'msg' => '操作成功'));
		}
		$this->returnAjax(array('code' => 2, 'msg' => '操作失败'));
	}

	//删除分类广告图
	public function delIndustryPic()
	{
		$id = $this->input('pid', 'int', 0);
		if ($id <= 0) {
			$this->returnAjax(array('code' => 2, 'msg' => '参数错误'));
		}
		$res = $this->load('industryPic')->del($id);
		if ($res) {
			$this->returnAjax(array('code' => 1, 'msg' => '删除成功'));
		} else {
			$this->returnAjax(array('code' => 2, 'msg' => '删除失败'));
		}
	}

	//删除子分类
	public function delIndustryItems()
	{
		$id = $this->input('iid', 'int', 0);
		if ($id <= 0) {
			$this->returnAjax(array('code' => 2, 'msg' => '参数错误'));
		}
		$res1 = $this->load('industryClass')->del($id);
		$res2 = $this->load('industryClassItems')->del($id); //对应classId
		if ($res1 && $res2) {
			$this->returnAjax(array('code' => 1, 'msg' => '删除成功'));
		} else {
			$this->returnAjax(array('code' => 2, 'msg' => '删除失败'));
		}
	}

	//添加子分类
	public function addZtype()
	{
		$id         = $this->input('id', 'int', '0');
		$industryId = $this->input('industryId', 'text', '');
		$ztypeName  = $this->input('ztypeName', 'text', '');
		$ztypeLink  = $this->input('ztypeLink', 'text', '');
		$tname      = $this->input('tname', 'text', '');
		$tlink      = $this->input('tlink', 'text', '');
		$isopen     = $this->input('isopen', 'text', '');
		$isshow     = $this->input('isshow', 'text', '');
		if (empty($ztypeName)) {
			$this->returnAjax(array('code' => 2, 'msg' => '请填写分类。'));
		}
		if (empty($ztypeLink)) {
			$this->returnAjax(array('code' => 2, 'msg' => '请填写分类链接。'));
		}
		if ($tname[0] == "") {
			$this->returnAjax(array('code' => 2, 'msg' => '请至少填写一列分类名称。'));
		}
		if ($tlink[0] == "") {
			$this->returnAjax(array('code' => 2, 'msg' => '请至少填写一列链接。'));
		}
		$zType = array(
			'industryId' => $industryId,
			'name'       => $ztypeName,
			'link'       => $ztypeLink,
			'date'       => time(),
		);
		if ($id == 0) {
			//add
			$resc = $this->load('industry')->addIndustryClass($zType);
		} else {
			//edit
			$this->load('industry')->editIndustryClass($zType, $id);
			$this->load('industry')->delIndustryClassItems($id);
			$resc = $id;
		}
		foreach ($tname as $k => $val) {
			if ($val != "") {
				$zItems = array(
					'classId' => $resc,
					'name'    => $val,
					'link'    => $tlink[$k],
					'open'    => $isopen[$k],
					'show'    => $isshow[$k],
					'date'    => time(),
				);
				//print_r($zItems);
				$res = $this->load('industry')->addIndustryClassItems($zItems);
			}
		}
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

	//编辑分类
	public function edit()
	{
		$id = $this->input('id', 'int', 0);
		if ($id <= 0) {
			MessageBox::halt('参数错误');
		}
		$indus = $this->load('industry')->getInfo($id);
		//子分类和对应内容信息
		$cinfo = $this->load('industryClass')->getInfo($id);
		//图片
		$pinfo = $this->load('industryPic')->getInfo($id);
		$this->set('pinfo', $pinfo);
		$this->set('cinfo', $cinfo);
		$this->set('indus', $indus);
		$this->display();
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
}

?>