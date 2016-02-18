<?
/**
 * 首页基本配置
 *
 * @package	Action
 * @author	Xuni
 * @since	2016-02-17
 */
class BasicAction extends AppAction
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
		$list = $this->load('basic')->getAllSetting();

        $this->set('list', $list);
		$this->display();
	}

	/**
	 * 创建广告图
	 * 
	 * @author	Xuni
	 * @since	2016-02-18
	 */
	public function addBannber()
	{
		$this->display('basic/basic.banner.html');
	}

	/**
	 * 编辑广告图
	 * 
	 * @author	Xuni
	 * @since	2016-02-18
	 */
	public function editBanner()
	{
		$id = $this->input('id', 'int', '');

		if ( empty($id) ){
			MessageBox::halt('参数错误');
		}

		$info = $this->load('basic')->getBasic($id);

		$this->set('id', $id);
		$this->set('info', $info);
		$this->display('basic/basic.banner.html');
	}

	/**
	 * 创建或编辑广告图
	 * 
	 * @author	Xuni
	 * @since	2016-02-18
	 */
	public function setBanner()
	{
		$pic 	= $this->input('pic', 'string', '');
		$link 	= $this->input('link', 'string', '');
		$model 	= $this->input('model', 'int', '2');
		$id 	= $this->input('id', 'int', '');

		if ( empty($pic) ){
			$this->returnAjax(array('code'=>2,'msg'=>'请上传图片'));
		}
		if ( $id ){
			$data = array(
				'type'       => 1,
				'pic'        => $pic,
				'link'       => $link,
				'other'      => $model,
			);
	        $res = $this->load('basic')->setBasic($data, $id);
		}else{
			$order = $this->load('basic')->getLastOrder(1);
			$order++;
			$data = array(
				'type'       => 1,
				'pic'        => $pic,
				'link'       => $link,
				'order'      => $order,
				'other'      => $model,
			);
	        $res = $this->load('basic')->addBasic($data);
		}
        if ( $res ){
            $this->returnAjax(array('code'=>1));
        }
        $this->returnAjax(array('code'=>2,'msg'=>'创建失败'));
	}

	/**
	 * 创建热门搜索词
	 * 
	 * @author	Xuni
	 * @since	2016-02-18
	 */
	public function addWord()
	{
		$this->display('basic/basic.word.html');
	}

	/**
	 * 编辑广告图
	 * 
	 * @author	Xuni
	 * @since	2016-02-18
	 */
	public function editWord()
	{
		$id = $this->input('id', 'int', '');

		if ( empty($id) ){
			MessageBox::halt('参数错误');
		}

		$info = $this->load('basic')->getBasic($id);

		$this->set('id', $id);
		$this->set('info', $info);
		$this->display('basic/basic.word.html');
	}

	/**
	 * 创建或编辑广告图
	 * 
	 * @author	Xuni
	 * @since	2016-02-18
	 */
	public function setWord()
	{
		$title 	= $this->input('title', 'string', '');
		$link 	= $this->input('link', 'string', '');
		$model 	= $this->input('model', 'int', '0');
		$id 	= $this->input('id', 'int', '');

		if ( empty($pic) ){
			$this->returnAjax(array('code'=>2,'msg'=>'请上传图片'));
		}
		if ( $id ){
			$data = array(
				'type'   	=> 2,
				'desc'		=> $title,
				'link'  	=> $link,
				'other'   	=> $model,
			);
	        $res = $this->load('basic')->setBasic($data, $id);
		}else{
			$order = $this->load('basic')->getLastOrder(2);
			$order++;
			$data = array(
				'type'    	=> 2,
				'desc'    	=> $title,
				'link'   	=> $link,
				'order'    	=> $order,
				'other'    	=> $model,
			);
	        $res = $this->load('basic')->addBasic($data);
		}
        if ( $res ){
            $this->returnAjax(array('code'=>1));
        }
        $this->returnAjax(array('code'=>2,'msg'=>'创建失败'));
	}

	//排序广告图(向上向下)
	public function orderBasic()
	{
		$id 	= $this->input('id', 'int', '');
		$updown = $this->input('updown', 'int', '');
		$type 	= $this->input('type', 'int', '');
		if ( empty($id) || empty($updown) || empty($type) ){
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误'));
		}
		//向上向下
		$res = $this->load('basic')->orderUpDown($id, $updown, $type);
		if ( $res ){
            $this->returnAjax(array('code'=>1));
        }
        $this->returnAjax(array('code'=>2,'msg'=>'操作失败'));
	}

	/**
	 * 创建或编辑广告图
	 * 
	 * @author	Xuni
	 * @since	2016-02-18
	 */
	public function delBasic()
	{
		$id = $this->input('id', 'int', '');

		if ( empty($id) ){
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误'));
		}

		$res = $this->load('basic')->delBasic($id);
		if ( $res ){
            $this->returnAjax(array('code'=>1));
        }
        $this->returnAjax(array('code'=>2,'msg'=>'删除失败'));
	}



}
?>