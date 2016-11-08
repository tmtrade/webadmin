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
		'alt1'    => array(
			'field' => 'alt1',
			'method' => 'fieldName',
		),
		'alt2'    => array(
			'field' => 'alt2',
			'method' => 'fieldName',
		),
		'date'    => array(
			'field' => 'date',
			'method' => 'handleTime',
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

	/**
	 * 处理时间
	 * @param $value
	 * @return int
	 */
	public function handleTime($value){
		if($value){
			$value .= ' 8:00';//上海市区与格林威治时间差
			return strtotime($value);
		}
		return 0;
	}
}