<?
/**
 * 群组
 *
 * 分类群组
 * 
 * @package	Module
 * @author	void
 * @since	2015-09-10
 */
class GroupModule extends AppModule
{
	
	/**
	 * 引用业务模型
	 */
	public $models = array(
		'group'			=> 'Group',
    );
	
	/**
	 * 获取工作日志分页列表
	 * @author	Jeany
	 * @since	2015-07-23
	 *
	 * @access	public
	 * @param	array	
	 * @p		int
	 * @num		int
	 * @return	array
	 */
	public function getGroups($class)
	{
        $r['limit'] = 10000;
        $r['order'] = array('id' => 'asc');
		$r['in']    = array('class' => explode(',',$class));	
		
        $data = $this->import('group')->findAll($r);
		return $data;
	}
	
	

}
?>