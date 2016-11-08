<?
/**
 * 添加用户表单验证模型
 *
 * @package	Form
 * @author	void
 * @since	2015-06-18
 */
class UserAddForm extends AppForm
{
	/**
	 * 字段映射(建立表单字段与程序字段或数据表字段的关联)
	 */
	protected $map = array(
		'account'    => array(
			'field' => 'account',
			'match' => array('require', '', '登陆账户不能为空'), 
			),
		'password'    => array(
			'field' => 'password',
			'match' => array('require', '', '登陆密码不能为空'), 
			),
		'name'    => array(
			'field' => 'name',
			'match' => array('require', '', '用户姓名不能为空'), 
			),
		'teamId'    => array(
			'field' => 'teamId',
			'match' => array('int', '', '行政组不能为空'), 
			),
		'positionId'=> array(
			'field' => 'positionId',
			'match' => array('require', '', '用户职位不能为空'), 
			),
		);
}
?>