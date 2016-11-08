<?
/**
 * 黑名单列表
 *
 * @package	Action
 * @author	Xuni
 * @since	2016-01-11
 */
class BlacklistAction extends AppAction
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
		$number = $this->input('inputNumber', 'string', '');
		$name 	= $this->input('inputName', 'string', '');

		$list = $this->load('blacklist')->getBlack($number, $name);
		
		$this->set('number', $number);
		$this->set('isHave', $this->load('trademark')->existTm($number));
		$this->set('list', $list);
		$this->display();
	}

	public function add()
	{
		$this->display();
	}

	public function outBlack()
	{
		$number = $this->input('number', 'string', '');
		if ( empty($number) ) $this->returnAjax(array('code'=>2,'msg'=>'参数错误')); 
		if ( $this->load('internal')->existSale($number) ){
			$this->returnAjax(array('code'=>2,'msg'=>'商标已在出售列表中，请到出售列表中操作！')); 
		}
		$res 	= $this->load('blacklist')->outBlack($number);
		if ( $res ) $this->returnAjax(array('code'=>1));
		$this->returnAjax(array('code'=>2));
	}

	public function setBlack()
	{
		$number = $this->input('number', 'string', '');
		if ( empty($number) ) $this->returnAjax(array('code'=>2)); 

		$list 	= array_filter( array_unique( explode(',', $number) ) );
		$hasnot = array();
		foreach ($list as $k => $v) {
			if ( !$this->load('trademark')->existTm($v) ){
				$hasnot[] = $v;
			}
		}
		if ( !empty($hasnot) ){
			$str = implode(',', $hasnot);
			$this->returnAjax(array('code'=>2, 'msg'=>"商标 ($str) 不存在")); 
		}
		$res 	= $this->load('blacklist')->addBlacklist($list);
		if ( $res ) $this->returnAjax(array('code'=>1));
		$this->returnAjax(array('code'=>2));
	}

}
?>