<?php

class SeoSetseoForm extends AppForm
{
    /**
     * 字段映射(建立表单字段与程序字段或数据表字段的关联)
     */
    protected $map = array(
		'id'    => array(
			'field' => 'id',
			'match' => array('int', '0', ''),
			),
		'type'    => array(
			'field' => 'type',
			'match' => array('int', '0', '请选择页面'),
			),
		'title'    => array(
			'field' => 'title',
			'method' => 'fieldName',
                        ),
		'keyword'    => array(
                        'field' => 'keyword',
                        'method' => 'fieldName',
				),
		'description'    => array(
                        'field' => 'description',
                        'method' => 'fieldName',
				),
                'isUse'    => array(
			'field' => 'isUse',
			'match' => array('int', '1', ''),
			),
    );


	/**
	 * 处理字符串
	 * @param $value
	 * @return string
	 */
    public function fieldName($value)
    {
        return trim($value);
    }

}