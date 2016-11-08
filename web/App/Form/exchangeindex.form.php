<?php

class ExchangeindexForm extends AppForm {

    /**
     * 字段映射(建立表单字段与程序字段或数据表字段的关联)
     */
    protected $map = array(
        'phone' => array(
            'field' => 'phone',
            'method' => 'fieldName',
        ),
         'isUse' => array(
            'field' => 'isUse',
            'method' => 'fieldName',
        ),
        'page' => array(
            'field' => 'page',
            'match' => array('int', '1', ''),
        ),
        'pages' => array(
            'field' => 'pages',
            'match' => array('int', '0', ''),
        ),
        'dateStart' => array(
            'field' => 'dateStart',
            'match' => array('', '', ''),
        ),
        'dateEnd' => array(
            'field' => 'dateEnd',
            'match' => array('', '', ''),
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
