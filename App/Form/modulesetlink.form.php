<?php
//添加或编辑 联系人
class ModulesetlinkForm extends AppForm
{
    /**
     * 字段映射(建立表单字段与程序字段或数据表字段的关联)
     */
    protected $map = array(
		'lId'    => array(
			'field' => 'lId',
			'method' => 'fieldInt',
			),
		'moduleId'    => array(
			'field' => 'moduleId',
			'method' => 'fieldInt',
			),
        'show'    => array(
            'field' => 'show',
            'method' => 'fieldInt',
            ),
		'title'    => array(
			'field' => 'title',
			'method' => 'fieldName',
			),
		'link'    => array(
			'field' => 'link',
			'method' => 'fieldName',  
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