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
		
	}

	public function outBlack()
	{
		$number = $this->input('number', 'string', '');
		if ( empty($number) ) $this->returnAjax(array('code'=>2)); 

		$list 	= array_filter( array_unique( explode(',', $number) ) );
		$res 	= $this->load('blacklist')->deleteBlack($list);
		if ( $res ) $this->returnAjax(array('code'=>1));
		$this->returnAjax(array('code'=>2));
	}

	public function setBlack()
	{
		$number = $this->input('number', 'string', '');
		if ( empty($number) ) $this->returnAjax(array('code'=>2)); 

		$list 	= array_filter( array_unique( explode(',', $number) ) );
		$res 	= $this->load('blacklist')->addBlack($list);
		if ( $res ) $this->returnAjax(array('code'=>1));
		$this->returnAjax(array('code'=>2));
	}

}
?>