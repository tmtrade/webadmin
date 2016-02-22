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
	 * 创建或编辑广告图
	 * 
	 * @author	Xuni
	 * @since	2016-02-22
	 */
	public function setBanner()
	{
		$id 	= $this->input('id', 'int', '');
		$banner = $this->input('banner', 'string', '');
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
		);
        $res = $this->load('channel')->setChannel($data, $id);
        if ( $res ){
            $this->returnAjax(array('code'=>1));
        }
        $this->returnAjax(array('code'=>2,'msg'=>'创建失败'));
	}

	


}
?>