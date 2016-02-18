<?php

class IndustryindexForm extends AppForm
{
    /**
     * 字段映射(建立表单字段与程序字段或数据表字段的关联)
     */
	protected $map = array(
		'title'    => array(
			'field' => 'title',
			'method' => 'fieldName',
		),
		'icon'    => array(
			'field' => 'icon',
			'method' => 'fieldName',
		),
		'isUse'    => array(
			'field' => 'isUse',
			'match' => array('int', '1', ''),
		),
		'date'    => array(
			'field' => 'date',
			'match' => array('int', '0', ''),
		),
		'sort'    => array(
			'field' => 'sort',
			'match' => array('int', '0', ''),
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

}