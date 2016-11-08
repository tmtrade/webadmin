<?
/**
 * 联系电话列表
 *
 * @package	Action
 * @author	Xuni
 * @since	2016-01-11
 */
class PhoneAction extends AppAction
{
//	public $debug = true;
	
	/**
	 * 联系电话列表
	 * 
	 * @author	Xuni
	 * @since	2016-01-8
	 * @access	public
	 * @return	void
	 */
	public function index()
	{	
		$page = $this->input('page', 'int', 1);

		$res = $this->load('phone')->getList(array(), $page, $this->rowNum);
		
		$list 	= empty($res['rows']) ? array() : $res['rows'];
		$total 	= empty($res['total']) ? 0 : $res['total'];

		$pager 		= $this->pager($total, $this->rowNum);
    	$pageBar 	= empty($list) ? '' : getPageBar($pager);

       	$this->set("pageBar",$pageBar);
		$this->set('list', $list);
		$this->set('total', $total);
		$this->display();
	}

	//创建电话
	public function add()
	{
		$this->display();
	}

	//编辑电话
	public function edit()
	{
		$phone = $this->input('no', 'string', '');

		$this->set('phone', $phone);
		$this->display();
	}

	//删除电话
	public function delete()
	{
		$phone = $this->input('no', 'string', '');

		$list = $this->load('phone')->getAllPhone($phone);
		$this->set('list', $list);
		$this->set('phone', $phone);
		$this->display();
	}

	public function doAdd()
	{
		$phone 	= $this->input('phone', 'string', '');

		if ( empty($phone) ) $this->returnAjax(array('code'=>2,'msg'=>'号码不能为空'));
		if ( isCheck($phone) != 2 ) {
			$this->returnAjax(array('code'=>2,'msg'=>'号码格式不正确'));
		}

		if ( $this->load('phone')->existPhone($phone) ){
			$this->returnAjax(array('code'=>2,'msg'=>'号码已经存在'));
		}
		$res = $this->load('phone')->addPhone($phone);
		if ( $res ) $this->returnAjax(array('code'=>1,'msg'=>'创建成功'));
		$this->returnAjax(array('code'=>2,'msg'=>'创建失败'));
	}

	public function doEdit()
	{
		$old 	= $this->input('old', 'string', '');
		$phone 	= $this->input('phone', 'string', '');

		if ( empty($old) ) $this->returnAjax(array('code'=>2,'msg'=>'参数错误'));
		if ( empty($phone) ) $this->returnAjax(array('code'=>2,'msg'=>'号码不能为空'));	
		if ( $phone == $old ) $this->returnAjax(array('code'=>2,'msg'=>'新旧号码不能相同'));	
		if ( isCheck($phone) != 2 ) {
			$this->returnAjax(array('code'=>2,'msg'=>'号码格式不正确'));
		}
		if ( $this->load('phone')->existPhone($phone) ){
			$this->returnAjax(array('code'=>2,'msg'=>'号码已经存在'));
		}

		$res = $this->load('phone')->editPhone($old, $phone);
		if ( $res ) $this->returnAjax(array('code'=>1,'msg'=>'更新成功'));
		$this->returnAjax(array('code'=>2,'msg'=>'更新失败'));
	}

	public function doDelete()
	{
		$old 	= $this->input('old', 'string', '');
		$phone 	= $this->input('phone', 'string', '');
		$rand 	= $this->input('rand', 'int', 0);

		if ( empty($old) ) $this->returnAjax(array('code'=>2,'msg'=>'参数错误'));
		if ( !$rand && $phone == '' ){
			$this->returnAjax(array('code'=>2,'msg'=>'请选择新号码'));
		}
		if ( $rand ){
			$other = $this->load('phone')->getRandPhone($old);
		}else{
			$other = $phone;
		}
		$res = $this->load('phone')->deletePhone($old, $other);
		if ( $res ) $this->returnAjax(array('code'=>1,'msg'=>'删除并更新成功'));
		$this->returnAjax(array('code'=>2,'msg'=>'操作失败'));
	}


}
?>