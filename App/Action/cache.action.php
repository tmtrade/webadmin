<?
/**
 * 缓存管理
 *
 * @package	Action
 * @author	Xuni
 * @since	2016-02-26
 */
class CacheAction extends AppAction
{
//	public $debug = true;
	
	/**
	 * 缓存管理
	 * 
	 * @author	Xuni
	 * @since	2016-02-26
	 */
	public function index()
	{	

		//首页缓存
		$indexSize = $this->com('qcache')->select(5)->size();

		//entity类型缓存（列表，详情页等）
		$otherSize = $this->com('qcache')->select(6)->size();

		$this->set('indexSize', $indexSize);
		$this->set('otherSize', $otherSize);
		$this->display();
	}

	public function clear()
	{
		$cid = $this->input('cid', 'int', -1);
		//redis默认15个数据库，如redis配置文件修改，这里也要修改
		if ( $cid < 0 || !in_array($cid, range(0,14)) ){
			$this->returnAjax(array('code'=>2,'msg'=>'参数错误'));
		}

		//清除缓存（注意：清除前要关闭缓存配置，否则不会执行到这里）
		$this->com('qcache')->select($cid)->clear();
		$this->returnAjax(array('code'=>1));
	}


}
?>