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
		'img_color'    => array(
			'field' => 'img_color',
			'method' => 'fieldName',
		),
		'has_desc'    => array(
			'field' => 'has_desc',
			'method' => 'handleCheck',
		),
		'desc_title'    => array(
			'field' => 'desc_title',
			'method' => 'fieldName',
		),
		'desc_content'    => array(
			'field' => 'desc_content',
			'method' => 'fieldName',
		),
		'pic'    => array(
			'field' => 'pic',
			'method' => 'fieldName',
                        ),
        'type'    => array(
            'field' => 'type',
            'match' => array('int', '1', ''),
            ),
		'banner'    => array(
						'field' => 'banner',
						'method' => 'fieldName',
				),
        'link'    => array(
                        'field' => 'link',
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
		'isUse'    => array(
						'field' => 'isUse',
						'match' => array('int', '1', ''),
				),
		'isMore'    => array(
						'field' => 'isMore',
						'match' => array('int', '2', ''),
				),
		'alt1'    => array(
			'field' => 'alt1',
			'method' => 'fieldName',
		),
		'alt2'    => array(
			'field' => 'alt2',
			'method' => 'fieldName',
		),
		'alt3'    => array(
			'field' => 'alt3',
			'method' => 'fieldName',
		),
        'listPic'    => array(
            'field' => 'listPic',
            'method' => 'fieldName',
                        ),
        'listClass'    => array(
            'field' => 'listClass',
            'method' => 'fieldName',
                        ),
        'alt4'    => array(
            'field' => 'alt4',
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

	/**
	 * 处理多选框
	 * @param $value
	 * @return int
	 */
	public function handleCheck($value){
		return $value=='on'?1:0;
	}

}