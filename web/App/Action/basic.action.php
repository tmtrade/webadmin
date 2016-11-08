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

		$this->set('INDEX_WORD_STRESS', C('INDEX_WORD_STRESS'));
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
		$alt 	= $this->input('alt', 'string', '');
		$model 	= $this->input('model', 'int', '2');
		$id 	= $this->input('id', 'int', '');

		if ( empty($pic) ){
			$this->returnAjax(array('code'=>2,'msg'=>'请上传图片'));
		}
		$count = $this->load('basic')->countBasic(1);
		if ( empty($id) && $count >= 5 ){
			$this->returnAjax(array('code'=>2,'msg'=>'数量已达上限'));
		}
		if ( $id ){
			$data = array(
				'type'       => 1,
				'pic'        => $pic,
				'link'       => $link,
				'alt'        => $alt,
				'other'      => $model,
			);
	        $res = $this->load('basic')->setBasic($data, $id);
		}else{
			$order = $this->load('basic')->getLastOrder(1);
			$order = $order + rand(2,5);
			$data = array(
				'type'      => 1,
				'pic'       => $pic,
				'link'      => $link,
				'alt'       => $alt,
				'sort'      => $order,
				'other'     => $model,
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
		$this->set('INDEX_WORD_STRESS', C('INDEX_WORD_STRESS'));
		$this->display('basic/basic.word.html');
	}

	/**
	 * 编辑搜索词
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

		$this->set('INDEX_WORD_STRESS', C('INDEX_WORD_STRESS'));
		$this->set('id', $id);
		$this->set('info', $info);
		$this->display('basic/basic.word.html');
	}

	/**
	 * 创建或编辑搜索词
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

		if ( empty($title) ){
			$this->returnAjax(array('code'=>2,'msg'=>'请填写标题'));
		}
		$count = $this->load('basic')->countBasic(2);
		if ( empty($id) && $count >= 10 ){
			$this->returnAjax(array('code'=>2,'msg'=>'数量已达上限'));
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
			$order = $order + rand(2,5);
			$data = array(
				'type'    	=> 2,
				'desc'    	=> $title,
				'link'   	=> $link,
				'sort'    	=> $order,
				'other'    	=> $model,
			);
	        $res = $this->load('basic')->addBasic($data);
		}
        if ( $res ){
            $this->returnAjax(array('code'=>1));
        }
        $this->returnAjax(array('code'=>2,'msg'=>'创建失败'));
	}

	/**
	 * 创建滑动广告图
	 * 
	 * @author	Xuni
	 * @since	2016-02-19
	 */
	public function addSlide()
	{
		$this->display('basic/basic.slide.html');
	}

	/**
	 * 编辑滑动广告图
	 * 
	 * @author	Xuni
	 * @since	2016-02-19
	 */
	public function editSlide()
	{
		$id = $this->input('id', 'int', '');

		if ( empty($id) ){
			MessageBox::halt('参数错误');
		}

		$info = $this->load('basic')->getBasic($id);
		$this->set('id', $id);
		$this->set('info', $info);
		$this->display('basic/basic.slide.html');
	}

	/**
	 * 创建或编辑滑动广告图
	 * 
	 * @author	Xuni
	 * @since	2016-02-19
	 */
	public function setSlide()
	{
		$pic 	= $this->input('pic', 'string', '');
		$link 	= $this->input('link', 'string', '');
		$alt 	= $this->input('alt', 'string', '');
		$desc 	= $this->input('desc', 'string', '');
		$text 	= $this->input('text', 'string', '');
		$id 	= $this->input('id', 'int', '');

		if ( empty($pic) ){
			$this->returnAjax(array('code'=>2,'msg'=>'请上传图片'));
		}
		if ( $id ){
			$data = array(
				'type'   	=> 3,
				'pic'		=> $pic,
				'link'  	=> $link,
				'alt'  	    => $alt,
				'desc'  	=> $desc,
				'text'  	=> $text,
			);
	        $res = $this->load('basic')->setBasic($data, $id);
		}else{
			$order = $this->load('basic')->getLastOrder(3);
			$order = $order + rand(2,5);
			$data = array(
				'type'    	=> 3,
				'pic'    	=> $pic,
				'link'   	=> $link,
				'alt'   	=> $alt,
				'sort'    	=> $order,
				'desc'  	=> $desc,
				'text'  	=> $text,
			);
	        $res = $this->load('basic')->addBasic($data);
		}
        if ( $res ){
            $this->returnAjax(array('code'=>1));
        }
        $this->returnAjax(array('code'=>2,'msg'=>'创建失败'));
	}

	/**
	 * 编辑推荐分类 
	 * 
	 * @author	Xuni
	 * @since	2016-02-19
	 */
	public function editClass()
	{
		$id = $this->input('id', 'int', '');

		if ( empty($id) ){
			MessageBox::halt('参数错误');
		}

        $info   = $this->load('basic')->getBasic($id);
        $class  = array_filter( explode(',', $info['desc']) );
        $this->set('allClass', $class);
		$this->set('id', $id);
		$this->set('info', $info);
		$this->display('basic/basic.class.html');
	}

	/**
	 * 创建或编辑推荐分类 
	 * 
	 * @author	Xuni
	 * @since	2016-02-19
	 */
	public function setClass()
	{
		$pic 	= $this->input('pic', 'string', '');
		$alt 	= $this->input('alt', 'string', '');
		$class 	= $this->input('class', 'string', '');
        $name   = $this->input('name', 'string', '');
		$id 	= $this->input('id', 'int', '');

        if ( empty($id) ){
            $this->returnAjax(array('code'=>2,'msg'=>'参数错误'));
        }
		if ( empty($pic) ){
			$this->returnAjax(array('code'=>2,'msg'=>'请上传图片'));
		}
        if ( empty($class) ){
            $this->returnAjax(array('code'=>2,'msg'=>'请选择至少一个分类'));
        }
        if ( empty($name) ){
            $this->returnAjax(array('code'=>2,'msg'=>'请填写分类名称'));
        }

		$data = array(
			'type'   	=> 4,
			'pic'		=> $pic,
			'alt'		=> $alt,
            'link'      => $name,
			'desc'  	=> $class,
		);
        $res = $this->load('basic')->setBasic($data, $id);	
        if ( $res ){
            $this->returnAjax(array('code'=>1));
        }
        $this->returnAjax(array('code'=>2,'msg'=>'创建失败'));
	}

	/**
	 * 创建成功案例
	 *
	 * @author	dower
	 * @since	2016-09-26
	 */
	public function addCase()
	{
		$this->display('basic/basic.case.html');
	}

	/**
	 * 编辑成功案例
	 *
	 * @author	dower
	 * @since	2016-09-26
	 */
	public function editCase()
	{
		$id = $this->input('id', 'int', '');

		if ( empty($id) ){
			MessageBox::halt('参数错误');
		}

		$info = $this->load('basic')->getBasic($id);

		$this->set('id', $id);
		$this->set('info', $info);
		$this->display('basic/basic.case.html');
	}

	/**
	 * 创建或编辑成功案例
	 *
	 * @author	dower
	 * @since	2016-09-26
	 */
	public function setCase()
	{
		$pic 	= $this->input('pic', 'string', '');
		$link 	= $this->input('link', 'string', '');
		$alt 	= $this->input('alt', 'string', '');
		$id 	= $this->input('id', 'int', '');

		if ( empty($pic) ){
			$this->returnAjax(array('code'=>2,'msg'=>'请上传图片'));
		}
		if ( $id ){
			$data = array(
				'type'   	=> 5,
				'pic'		=> $pic,
				'link'  	=> $link,
				'alt'  	    => $alt,
			);
			$res = $this->load('basic')->setBasic($data, $id);
		}else{
			$order = $this->load('basic')->getLastOrder(5);
			$order = $order + rand(2,5);
			$data = array(
				'type'    	=> 5,
				'pic'    	=> $pic,
				'link'   	=> $link,
				'alt'   	=> $alt,
				'sort'    	=> $order,
			);
			$res = $this->load('basic')->addBasic($data);
		}
		if ( $res ){
			$this->returnAjax(array('code'=>1));
		}
		$this->returnAjax(array('code'=>2,'msg'=>'创建失败'));
	}

	
	/**
	 * 排序某项设置 (向上向下)
	 * 
	 * @author	Xuni
	 * @since	2016-02-18
	 */
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
	 * 删除某项设置
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