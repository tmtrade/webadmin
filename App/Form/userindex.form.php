<?
/**
 * 编辑用户表单验证模型
 *
 * @package	Form
 * @author	void
 * @since	2015-06-18
 */
class UserIndexForm extends AppForm
{
	/**
     * 字段映射(建立表单字段与程序字段或数据表字段的关联)
     */
    protected $map = array(
        'username' 	=> array( 'field' => 'username', 'method' => 'fieldString', ),
        'name'    	=> array( 'field' => 'name', 'method' => 'fieldString', ),
        'isUse'   	=> array( 'field' => 'isUse', 'method' => 'fieldInt', ),
        'roleId'  	=> array( 'field' => 'roleId', 'method' => 'fieldInt', ),
    );

    /**
     * 处理字符串
     * @author	martin
     * @since	2015-07-23
     *
     * @access	public
     * @param	array	$value	字符串
     * @return	string
     */
    public function fieldName($value)
    {
        return trim($value);
    }


    /**
     * 处理字符串
     * @author	martin
     * @since	2015-07-23
     *
     * @access	public
     * @param	array	$value	字符串
     * @return	string
     */
    public function fieldString($value)
    {
        return empty($value) ? '' : htmlspecialchars(trim($value));
    }

    /**
     * 处理数字
     * @author	martin
     * @since	2015-07-24
     *
     * @access	public
     * @param	array	$value	字符串
     * @return	string
     */
    public function fieldInt($value)
    {
        return intval(trim($value));
    }
}
?>