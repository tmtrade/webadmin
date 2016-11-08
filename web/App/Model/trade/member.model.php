<?
/**
 * 工作日志
 *
 * 获取工作日志id分页列表、添加工作日志、编辑工作日志、删除工作日志、删除所有工作日志
 * 
 * @package	Model
 * @author	void
 * @since	2015-07-22
 */
class MemberModel extends AppModel
{
	/**
	 * 查询信息(通过用户名来查询)
	 * @author	void
	 * @since	2015-07-22
	 *
	 * @access	public
	 * @param	string	$username	用户名称
	 * @return	bool
	 */
	public function getName($username)
	{
		$r['eq'] = array('username'=>$username);
		
		return $this->find($r);
	}
	
	/**
	 * 添加用户信息
	 * @author	void
	 * @since	2015-07-22
	 *
	 * @access	public
	 * @param	string	$username	用户名称
	 * @return	bool
	 */
	public function add($data)
	{
		return $this->create($data);
	}
}
?>