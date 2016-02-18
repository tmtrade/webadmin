<?php

class InternalindexForm extends AppForm
{
    /**
     * 字段映射(建立表单字段与程序字段或数据表字段的关联)
     */
    protected $map = array(
        'tmNums'    => array(
				'field' => 'tmNums',
				'method' => 'fieldName', 
				),
		'tmName'    => array(
			'field' => 'tmName',
			'method' => 'fieldName',
			),
		'saleStatus'    => array(
			'field' => 'saleStatus',
			'method' => 'fieldName',
			),
		'startPrice'    => array(
			'field' => 'startPrice',
			'match' => array('int', '0', ''),
			),
        'endPrice'    => array(
            'field' => 'endPrice',
            'match' => array('int', '0', ''),
            ),
        'isConfer'    => array(
            'field' => 'isConfer',
            'match' => array('int', '0', ''),
            ),
		'tmType'    => array(
			'field' => 'tmType',
			'method' => 'fieldName',
			),
		'tmClass'    => array(
			'field' => 'tmClass',
			'method' => 'fieldName',
			),
		'page'    => array(
			'field' => 'page',
			'match' => array('int', '1', ''),
			),
		'dateStart'    => array(
			'field' => 'dateStart',
			'match' => array('', '', ''),
			),
		'dateEnd'    => array(
			'field' => 'dateEnd',
			'match' => array('', '', ''),
			),
		'saleType'    => array(
			'field' => 'saleType',
			'method' => 'fieldName',
			),
		'tmGroup'    => array(
			'field' => 'tmGroup',
			'method' => 'fieldName',
			),
		'saleSource'    => array(
			'field' => 'saleSource',
			'match' => array('int', '', ''),
			),
		'tmNumber'    => array(
			'field' => 'tmNumber',
			'match' => array('fieldName', '', ''),
			),
		'tmLabel'    => array(
			'field' => 'tmLabel',
			'method' => 'fieldName',
			),
		'salePlat'    => array(
			'field' => 'salePlat',
			'method' => 'fieldName',
			),
        'offprice'    => array(
            'field' => 'offprice',
            'match' => array('int', '', ''),
            ),
		'isTop'    => array(
			'field' => 'isTop',
			'match' => array('int', '', ''),
			),
        'isVerify'    => array(
            'field' => 'isVerify',
            'match' => array('int', '', ''),
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