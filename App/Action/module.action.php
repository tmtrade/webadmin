<?
header("Content-type: text/html; charset=utf-8"); 
/**
 * 首页模块设置
 *
 * @package	Action
 * @author	Xuni
 * @since	2016-01-8
 */
class moduleAction extends AppAction
{
//	public $debug = true;
	
	/**
	 * 首页模块设置列表
	 * 
	 * @author	Jeany
	 * @since	2016-02-17
	 * @access	public
	 * @return	void
	 */
	public function index()
	{	

		//参数
		$params = array();
		//$params = $this->getFormData();
		$page 	= $this->input('page', 'int', '1');
		
		$res 	= $this->load('module')->getList($params, $page, $this->rowNum);

		$total 	= empty($res['total']) ? 0 : $res['total'];
		$list 	= empty($res['rows']) ? array() : $res['rows'];

		$pager 		= $this->pager($total, $this->rowNum);
        $pageBar 	= empty($list) ? '' : getPageBar($pager);
		$this->set('total', $total);
        $this->set("pageBar",$pageBar);
		$this->set('s', $params);
		$this->set('saleList', $result);
		$this->display();
	}
}
?>