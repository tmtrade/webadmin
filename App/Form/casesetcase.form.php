<?php

class CaseSetcaseForm extends AppForm
{
    /**
     * 字段映射(建立表单字段与程序字段或数据表字段的关联)
     */
    protected $map = array(
		'id'    => array(
			'field' => 'id',
			'match' => array('int', '0', ''),
			),
		'title'    => array(
			'field' => 'title',
			'method' => 'fieldName',
			),
		'name'    => array(
			'field' => 'name',
			'method' => 'fieldName',
                        ),
		'goods'    => array(
						'field' => 'goods',
						'method' => 'fieldName',
				),
		'adviser'    => array(
						'field' => 'adviser',
						'method' => 'fieldName',
				),
		'buyer'    => array(
			'field' => 'buyer',
			'method' => 'fieldName',
			),
		'desc'    => array(
			'field' => 'desc',
			'method' => 'fieldName',
		),
		'pic1'    => array(
			'field' => 'pic1',
			'method' => 'fieldName',
		),
		'pic2'    => array(
			'field' => 'pic2',
			'method' => 'fieldName',
		),
		'date'    => array(
			'field' => 'date',
			'match' => array('int', '1', ''),
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