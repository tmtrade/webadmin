<?php
//添加或编辑 联系人
class ModulesetclassForm extends AppForm
{
    /**
     * 字段映射(建立表单字段与程序字段或数据表字段的关联)
     */
    protected $map = array(
		'id'    => array(
			'field' => 'id',
			'method' => 'fieldInt',
			),
		'moduleId'    => array(
			'field' => 'moduleId',
			'method' => 'fieldInt',
			),
        'name'    => array(
            'field' => 'name',
            'method' => 'fieldName',
            ),
			
		'numbers'    => array(
            'field' => 'numbers',
            'match' => array('fieldName', '', ''),  
            ), 
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

    public function fieldInt($value)
    {
        return intval($value);
    }

}