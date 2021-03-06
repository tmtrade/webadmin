<?
header("Content-type: text/html; charset=utf-8");

/**
 * SEO设置
 *
 * @package    Action
 * @author     Far
 * @since      2016年4月6日10:09:46
 */
class seoAction extends AppAction
{
        public function __construct() {
            $seo_list = C('SEO_LIST');
            $this->set('seo_list', $seo_list);
        }
    
        //列表页
        public function index()
        {
                //参数
		$params = array();
		$page   = $this->input('page', 'int', '1');
                $type   = $this->input('type', 'int', '0');
                if($type) $params['type'] = $type;
                $res = $this->load('seo')->getList($params, $page, $this->rowNum);

		$total 	= empty($res['total']) ? 0 : $res['total'];
		$list 	= empty($res['rows']) ? array() : $res['rows'];

		$pager 		= $this->pager($total, $this->rowNum);
                $pageBar 	= empty($list) ? '' : getPageBar($pager);
                $this->set("pageBar",$pageBar);
                $this->set("params",$params);
                $this->set('list', $res['rows']);
                $this->display();

        }
        
        /**
	 * 创建SEO的弹窗及处理
	 * @throws SpringException
	 */
	public function add()
	{
		//get方式渲染页面
		if ( !$this->isPost() ){
			$this->display();
			exit;
		}
		//post方式添加案例
		$type = $this->input('type', 'string', '');
		if ( empty($type) ){
			$this->returnAjax(array('code'=>2,'msg'=>'请选择页面'));
		}
		$data = array(
			'type'     => $type,
			'date'      => time(),
		);
		$res = $this->load('seo')->addSeo($data);
		if ( $res ){
			$this->returnAjax(array('code'=>1,'id'=>$res));
		}
		$this->returnAjax(array('code'=>2,'msg'=>'创建失败'));
	}
        
	//编辑SEO
	public function edit()
	{
		$id = $this->input('id', 'int', 0);
		if ($id <= 0) {
			MessageBox::halt('参数错误');
		}
		$seo = $this->load('seo')->getInfo($id);
		$this->set('seo', $seo);
		$this->display();
	}
        
        /**
	 * 修改SEO
	 * @throws SpringException
	 */
	public function setSeo()
	{
		//参数
		$params = $this->getFormData();
		if ( $params['id'] <= 0 ){
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误'));
		}
		$id = $params['id'];
		unset($params['id']);
		$res = $this->load('seo')->updateSeo($params, $id);
		if ( $res ){
			$this->returnAjax(array('code'=>1,'msg'=>'成功'));
		}
		$this->returnAjax(array('code'=>2,'msg'=>'操作失败'));
	}
        
        /**
	 * 删除SEO
	 * @throws SpringException
	 */
	public function delSeo()
	{
		//获得id
		$id   = $this->input('id', 'int', '0');
		//执行操作
		$res  = $this->load('seo')->delSeo($id);
		//返回结果
		if ( $res ){
			$this->returnAjax(array('code'=>1));
		}
		$this->returnAjax(array('code'=>2,'msg'=>'删除错误'));
	}
        
	//移除标签
	public function removeLabel()
	{
		$id    = $this->input('id', 'int', 0);
		$label = $this->input('label', 'text', '');
		if ($id <= 0) {
			MessageBox::halt('参数错误');
		}
		$res = $this->load('seo')->removeLabel($id, $label);
		if ($res == 0) {
			$this->returnAjax(array('code' => 0, 'msg' => '移除失败。'));
		} else {
			$seo = $this->load('seo')->getInfo($id);
			$this->returnAjax(array('code' => 1, 'msg' => '操作成功。','data'=>$seo['labelArr']));
		}
	}
	//添加标签
	public function addLabel()
	{
		$id    = $this->input('id', 'int', 0);
		$label = $this->input('label', 'text', '');
		if ($id <= 0) {
			MessageBox::halt('参数错误');
		}
		if ($label == '') {
			MessageBox::halt('标签不能为空');
		}
		$res = $this->load('seo')->addLabel($id, $label);
		if ($res == 0) {
			$this->returnAjax(array('code' => 0, 'msg' => '操作失败。'));
		} elseif ($res == 1) {
			$seo = $this->load('seo')->getInfo($id);
			$this->returnAjax(array('code' => 1, 'msg' => '操作成功。','data'=>$seo['labelArr']));
		} elseif ($res == 2) {
			$this->returnAjax(array('code' => 0, 'msg' => '不能添加重复标签。'));
		}
	}
}

?>