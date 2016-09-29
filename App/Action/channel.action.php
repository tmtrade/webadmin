<?
/**
 * 频道页管理
 *
 * @package	Action
 * @author	Xuni
 * @since	2016-02-19
 */
class ChannelAction extends AppAction
{
//	public $debug = true;
	
	/**
	 * 首页基本配置
	 * 
	 * @author	Xuni
	 * @since	2016-02-18
	 */
	public function index()
	{
		$id = $this->input('id', 'int','');

		if ( empty($id) ){
			$this->display();
			exit;
		}
		$info = $this->load('channel')->getChannel($id);
        $this->set('info', $info);
		$this->display();
	}

	/**
	 * 上传banner图
	 * 
	 * @author	Xuni
	 * @since	2016-02-22
	 */
	public function addBannber()
	{
		$id 	= $this->input('id', 'int', '');
		if ( empty($id) ) {
			MessageBox::halt('参数错误');
		}
		$info = $this->load('channel')->getChannel($id);
        $this->set('info', $info);
		$this->display('channel/channel.banner.html');
	}

	/**
	 * 创建或编辑Banner图
	 * 
	 * @author	Xuni
	 * @since	2016-02-22
	 */
	public function setBanner()
	{
		$id 	= $this->input('id', 'int', '');
		$banner = $this->input('banner', 'string', '');
		$alt    = $this->input('alt', 'string', '');
		$model 	= $this->input('isBanner', 'int', '2');

		if ( empty($id) ){
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误'));
		}
		if ( empty($banner) ){
			$this->returnAjax(array('code'=>2,'msg'=>'请上传图片'));
		}

		$data = array(
			'banner'  	=> $banner,
			'isBanner'	=> $model,
			'alt'	    => $alt,
		);
        $res = $this->load('channel')->setChannel($data, $id);
        if ( $res ){
            $this->returnAjax(array('code'=>1));
        }
        $this->returnAjax(array('code'=>2,'msg'=>'创建失败'));
	}

	/**
	 * 上传banner图
	 * 
	 * @author	Xuni
	 * @since	2016-02-23
	 */
	public function addAd()
	{
		$id 	= $this->input('id', 'int', '');
		if ( empty($id) ) {
			MessageBox::halt('参数错误');
		}
		$this->set('cId', $id);
		$this->display('channel/channel.ad.html');
	}

	/**
	 * 上传banner图
	 * 
	 * @author	Xuni
	 * @since	2016-02-23
	 */
	public function editAd()
	{
		$id 	= $this->input('id', 'int', '');
		if ( empty($id) ) {
			MessageBox::halt('参数错误');
		}
		$info = $this->load('channel')->getItems($id);
		$this->set('info', $info);
		$this->set('cId', $info['channelId']);
		$this->display('channel/channel.ad.html');
	}

	/**
	 * 创建或编辑广告图
	 * 
	 * @author	Xuni
	 * @since	2016-02-23
	 */
	public function setAd()
	{
		$id 	= $this->input('id', 'int', '');
		$pic 	= $this->input('pic', 'string', '');
		$link 	= $this->input('link', 'string', '');
		$desc 	= $this->input('desc', 'string', '');
		$alt 	= $this->input('alt', 'string', '');
		$cId 	= $this->input('cId', 'int', '');
		if ( $id > 0 ){
			$info 	= $this->load('channel')->getItems($id);
			$cId 	= $info['channelId'];
		}
		if ( empty($cId) ){
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误'));
		}
		if ( empty($pic) ){
			$this->returnAjax(array('code'=>2,'msg'=>'请上传图片'));
		}
		$count = $this->load('channel')->countChannel(1, $cId);
		if ( empty($id) && $count >= 5 ){
			$this->returnAjax(array('code'=>2,'msg'=>'数量已达上限'));
		}
		if ( $id ){
			$data = array(
				'type'       => 1,
				//'channelId'  => $cId,
				'pic'        => $pic,
				'link'       => $link,
				'desc'       => $desc,
				'alt'       => $alt,
			);
	        $res = $this->load('channel')->setItems($data, $id);
		}else{
			$order = $this->load('channel')->getLastOrder($cId, 1);
			$order = $order + rand(2,5);
			$data = array(
				'type'      => 1,
				'channelId' => $cId,
				'pic'       => $pic,
				'link'      => $link,
				'desc'      => $desc,
				'alt'       => $alt,
				'sort'      => $order,
			);
	        $res = $this->load('channel')->addItems($data);
		}
        if ( $res ){
            $this->returnAjax(array('code'=>1));
        }
        $this->returnAjax(array('code'=>2,'msg'=>'创建失败'));
	}
	
	/**
	 * 天天低价模板
	 * 
	 * @author	Xuni
	 * @since	2016-02-23
	 */
	public function addTop()
	{
		$id 	= $this->input('id', 'int', '');
		if ( empty($id) ) {
			MessageBox::halt('参数错误');
		}
		$this->set('cId', $id);
		$this->display('channel/channel.top.html');
	}

	/**
	 * 创建天天低价
	 * 
	 * @author	Xuni
	 * @since	2016-02-23
	 */
	public function setTop()
	{
		$number = $this->input('number', 'string', '');
		$cId 	= $this->input('cId', 'int', '');
        $type 	= $this->input('type', 'int', '');

		if ( empty($cId) ){
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误'));
		}
		if ( empty($number) ){
			$this->returnAjax(array('code'=>2,'msg'=>'请填写商标号'));
		}
		$numbers = array_filter( explode(',', $number) );
		if ( empty($numbers) ){
			$this->returnAjax(array('code'=>2,'msg'=>'请填写正确的商标号'));
		}
		$ishas 	= $this->load('channel')->existSale($numbers);
		$hasnot = array_diff($numbers, $ishas);
		if ( !empty($hasnot) ){
			$this->returnAjax(array('code'=>2,'msg'=>'下列商标未出售或不在销售中：'.implode(',', $hasnot)));
		}

        $res = $this->load('channel')->addTops($numbers, $cId, $type);
        if ( $res ){
            $this->returnAjax(array('code'=>1));
        }
        $this->returnAjax(array('code'=>2,'msg'=>'创建失败'));
	}

	/**
	 * 上传banner图
	 * 
	 * @author	Xuni
	 * @since	2016-02-23
	 */
	public function setUse()
	{
		$id 	= $this->input('id', 'int', '');
		$model 	= $this->input('model', 'int', '');
		$name 	= $this->input('name', 'string', '');

		if ( empty($id) || empty($model) || empty($name) ) {
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误'));
		}
		$data = array(
			"$name" => $model,
		);
		$res = $this->load('channel')->setChannel($data, $id);
        if ( $res ){
            $this->returnAjax(array('code'=>1));
        }
		$this->returnAjax(array('code'=>2,'msg'=>'操作失败'));
	}

	/**
	 * 排序某项设置 (向上向下)
	 * 
	 * @author	Xuni
	 * @since	2016-02-18
	 */
	public function orderChannel()
	{
		$id 	= $this->input('id', 'int', '');
		$updown = $this->input('updown', 'int', '');
		$type 	= $this->input('type', 'int', '');
		$cId 	= $this->input('cId', 'int', '');
		if ( empty($id) || empty($updown) || empty($type) || empty($cId) ){
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误'));
		}
		//向上向下
		$res = $this->load('channel')->orderUpDown($id, $updown, $type, $cId);
		if ( $res ){
            $this->returnAjax(array('code'=>1));
        }
        $this->returnAjax(array('code'=>2,'msg'=>'操作失败'));
	}

	/**
	 * 删除某项设置
	 * 
	 * @author	Xuni
	 * @since	2016-02-18
	 */
	public function delChannel()
	{
		$id = $this->input('id', 'int', '');

		if ( empty($id) ){
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误'));
		}

		$res = $this->load('channel')->delItems($id);
		if ( $res ){
            $this->returnAjax(array('code'=>1));
        }
        $this->returnAjax(array('code'=>2,'msg'=>'删除失败'));
	}

    /**
	 * 修改精品特卖
	 * 
	 * @author	Xuni
	 * @since	2016-02-23
	 */
	public function setGoodsSale()
	{
		$old_number = $this->input('old_number', 'string', '');
		$cId 	= $this->input('cId', 'int', '');
        $type 	= $this->input('type', 'int', '');
        $number 	= $this->input('number', 'int', '');

		if ( empty($cId) ){
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误'));
		}
		if ( empty($number) ){
			$this->returnAjax(array('code'=>2,'msg'=>'请填写商标号'));
		}
		$numbers = array_filter( explode(',', $number) );
		if ( empty($numbers) ){
			$this->returnAjax(array('code'=>2,'msg'=>'请填写正确的商标号'));
		}
		$ishas 	= $this->load('channel')->existSale($numbers);
		$hasnot = array_diff($numbers, $ishas);
		if ( !empty($hasnot) ){
			$this->returnAjax(array('code'=>2,'msg'=>'下列商标未在出售中或销售价格为议价!'.implode(',', $hasnot)));
		}

        $res = $this->load('channel')->updateGoodsSale($old_number,$number, $cId, $type);
        if ( $res ){
            $this->returnAjax(array('code'=>1));
        }
        $this->returnAjax(array('code'=>2,'msg'=>'创建失败'));
	}
}
?>