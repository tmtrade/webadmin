<?php

class PatentindexForm extends AppForm {

    /**
     * 字段映射(建立表单字段与程序字段或数据表字段的关联)
     */
    protected $map = array(
        'tmName' => array(
            'field' => 'tmName',
            'method' => 'fieldName',
        ),
         'type' => array(
            'field' => 'type',
            'method' => 'fieldName',
        ),
        'saleStatus' => array(
            'field' => 'saleStatus',
            'method' => 'fieldName',
        ),
        'tmPrice' => array(
            'field' => 'tmPrice',
            'match' => array('int', '0', ''),
        ),
        'isConfer' => array(
            'field' => 'isConfer',
            'match' => array('int', '0', ''),
        ),
        'tmClass' => array(
            'field' => 'tmClass',
            'method' => 'fieldName',
        ),
        'page' => array(
            'field' => 'page',
            'match' => array('int', '1', ''),
        ),
        'dateStart' => array(
            'field' => 'dateStart',
            'match' => array('', '', ''),
        ),
        'dateEnd' => array(
            'field' => 'dateEnd',
            'match' => array('', '', ''),
        ),
        'saleType' => array(
            'field' => 'saleType',
            'method' => 'fieldName',
        ),
        'saleSource' => array(
            'field' => 'saleSource',
            'match' => array('int', '', ''),
        ),
        'tmNumber' => array(
            'field' => 'tmNumber',
            'match' => array('fieldName', '', ''),
        ),
        'tmType' => array(
            'field' => 'tmType',
            'match' => array('int', '', ''),
        ),
        'offprice' => array(
            'field' => 'offprice',
            'match' => array('int', '', ''),
        ),
        'isTop' => array(
            'field' => 'isTop',
            'match' => array('int', '', ''),
        ),
        'pid' => array(
            'field' => 'pid',
            'match' => array('int', '', ''),
        ),
        'applyDate' => array(
            'field' => 'applyDate',
            'match' => array('', '', ''),
        ),
        'listSort' => array(
            'field' => 'listSort',
            'match' => array('int', '', ''),
        ),
    );

    /**
     * 处理字符串
     * @author	Far
     * @since	2016-04-27
     *
     * @access	public
     * @param	array	$value	字符串
     * @return	string
     */
    public function fieldName($value) {
        return trim($value);
    }

}
