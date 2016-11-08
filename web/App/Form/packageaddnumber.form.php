<?

/**
 * 应用公用表单组件
 *
 * 表单数据收集
 *
 * @package	Form
 * @author	Xuni
 * @since	2015-11-11
 */
class PackageaddnumberForm extends AppForm {

    /**
     * 字段映射(建立表单字段与程序字段或数据表字段的关联)
     */
    protected $map = array(
        'id' => array('field' => 'id', 'method' => 'fieldInt',),
        'title' => array('field' => 'title', 'method' => 'fieldString',),
        'value' => array('field' => 'value', 'method' => 'fieldString',),
        'number' => array('field' => 'number', 'method' => 'fieldName',),
        'price' => array('field' => 'price', 'method' => 'fieldInt',),
        'isAll' => array('field' => 'isAll', 'method' => 'fieldInt',),
        'label' => array('field' => 'label', 'method' => 'fieldString',),
        'isTop' => array('field' => 'isTop', 'method' => 'fieldInt',),
        'desc' => array('field' => 'desc', 'method' => 'fieldName',),
        'viewPhone' => array('field' => 'viewPhone', 'method' => 'fieldString',),
    );

    /**
     * 处理字符串
     * @author	Xuni
     * @since	2015-11-11
     *
     * @access	public
     * @param	array	$value	字符串
     * @return	string
     */
    public function fieldString($value) {
        return empty($value) ? '' : urldecode(htmlspecialchars(trim($value)));
    }

    /**
     * 处理数字
     * @author	Xuni
     * @since	2015-11-11
     *
     * @access	public
     * @param	array	$value	字符串
     * @return	string
     */
    public function fieldInt($value) {
        return intval(trim($value));
    }

    public function fieldName($value) {
        return $value;
    }

}

?>