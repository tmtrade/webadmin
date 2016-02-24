<?php

class TopicSetTopicForm extends AppForm
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
		'pic'    => array(
			'field' => 'pic',
			'method' => 'fieldName',
			),
        'banner'    => array(
			'field' => 'banner',
			'method' => 'fieldName',
			),
        'bgImg'    => array(
			'field' => 'bgImg',
			'method' => 'fieldName',
			),
		'bgColor'    => array(
			'field' => 'bgColor',
			'method' => 'fieldName',
			),
		'bgImgShow'    => array(
			'field' => 'bgImgShow',
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