<?php
//添加或编辑 联系人
class PatentsetcontactForm extends AppForm
{
    /**
     * 字段映射(建立表单字段与程序字段或数据表字段的关联)
     */
    protected $map = array(
		'cId'    => array(
			'field' => 'cId',
			'method' => 'fieldInt',
			),
		'patentId'    => array(
			'field' => 'patentId',
			'method' => 'fieldInt',
			),
		'date'    => array(
			'field' => 'date',
			'method' => 'fieldName',
			),
        'source'    => array(
            'field' => 'source',
            'method' => 'fieldInt',
            ),
        'isVerify'    => array(
            'field' => 'isVerify',
            'match' => array('int', 1,''),
            ),
        'saleType'    => array(
            'field' => 'saleType',
            'method' => 'fieldInt',
            ),
		'name'    => array(
			'field' => 'name',
			'method' => 'fieldName',
			),
		'phone'    => array(
			'field' => 'phone',
			'method' => 'fieldName',  
			),
		'price'    => array(
			'field' => 'price',
			'method' => 'fieldInt',  
			),
		'advisor'    => array(
			'field' => 'advisor',
			'method' => 'fieldName',  
			),
		'department'    => array(
			'field' => 'department',
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