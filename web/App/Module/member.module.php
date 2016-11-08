<?
/**
 * 用户接口
 *
 * 获取用户信息、获取用户分页列表、添加用户、
 * 修改密码、重置密码、编辑用户资料、删除用户
 * 
 * @package	Module
 * @author	void
 * @since	2015-06-11
 */
class MemberModule extends AppModule
{

	/**
	 * 引用业务模型
	 */
	public $models = array(
		'member'	=> 'Member',
		'role'		=> 'role',
	);

	/**
	 * 获取用户信息
	 * @author	void
	 * @since	2015-06-11
	 *
	 * @access	public
	 * @param	int		$username  用户名称
	 * @return	array
	 */
	public function get($username)
	{
		$user = $this->import('member')->getName($username);

		return !empty($user) ? $user : '' ;
	}
	
	/**
	 * 验证用户信息
	 * @author	void
	 * @since	2015-06-11
	 *
	 * @access	public
	 * @param	int		$username  用户名称
	 * @return	array
	 */
	public function check($username)
	{
		$user = $this->import('member')->getName($username);

		return !empty($user) ? $user['id'] : '' ;
	}


    /**
     * 验证CRM用户名
     * @author  Xuni
     *
     * @access  public
     * @param   string     $username  用户名称
     * @return  bool
     */
    public function checkCrmName($username)
    {
        $res = $this->importBi('crmpassport')->checkCrmName($username);
        return $res['error']==1 ? false : true ;
    }
	
	/**
	 * 添加用户信息
	 * @author	void
	 * @since	2015-06-11
	 *
	 * @access	public
	 * @param	array		$param  用户名称
	 * @return	array
	 */
	public function addMember($param)
	{
		$data = array(
			'username' => $param['username'],
			'name'     => empty($param['name'])?'':$param['name'],
			'staffId'  => empty($param['staffId'])?'':$param['staffId'],
			'roleId'   => empty($param['roleId'])?'2':$param['roleId'],
			'isUse'    => empty($param['isUse'])?'2':$param['isUse'],
		);
		return $this->import('member')->add($data);
	}
	
    /**
     * 获取用户信息
     * @author	martin
     * @since	2015-07-22
     *
     * @access	public
     * @param	int		$attacheId  用户id
     * @return	array
     */
    public function getName($attacheId)
    {
        $r['limit'] = 1;
        $r['col']   = array("name");
        $r['eq'] = array("id"=>$attacheId, 'isUse' => 1);
        $user = $this->import('member')->find($r);

        return empty($user) ?  '' :  $user['name'] ;
    }

    /**
     * 获取所有用户信息
     * @author	martin
     * @since	2015-07-22
     *
     * @access	public
     * @param	int		$attacheId  用户id
     * @return	array
     */
    public function allMember()
    {
        $r['limit']     = 100000;
        $r['page']      = 1;
        $r['order']     = array("id"=>"asc");
        $r['eq']        = array('isUse' => 1, 'roleId' => 2);
        return $this->import("member")->find($r);
    }

    /**
     * 通过ID获取用户信息
     * @author	Xuni
     * @since	2015-09-09
     *
     * @access	public
     * @param	int		$id  用户id
     * @return	array
     */
    public function getMemberById($id)
    {
    	$r['limit'] = 1;
        //$r['col']   = array("roleId");
        $r['eq']	= array("id"=>$id);
        return $this->import('member')->find($r);
    }

    /**
     * 获取相关用户信息列表
     * @author	Xuni
     * @since	2015-09-09
     *
     * @access	public
     * @param	array		$where  	条件
     * @param	int			$page  		页码
     * @param	int			$limit  	条数
     * @return	array
     */
    public function getList($where, $page=1, $limit=20)
    {
    	$r['limit']     = $limit;
        $r['page']      = $page > 0 ? $page : 1;
        $r['order']     = array("id"=>"asc");
        if ( !empty($where['eq']) ) $r['eq'] = $where['eq'];
        if ( !empty($where['like']) ) $r['like'] = $where['like'];

        return $this->import("member")->findAll($r);
    }

    /**
     * 设置用户账号
     * @author	Xuni
     * @since	2015-09-09
     *
     * @access	public
     * @param	array		$params  用户信息
     * @return	bool
     */
    public function setMember($params)
    {
    	if ( empty($params) || !is_array($params) ) return false;

    	$r1['eq'] 	= array('username'=>$params['username']);
    	$r1['col']	= array('id','username','name','staffId');
    	$member = $this->import('member')->find($r1);

    	if ( empty($member) ) return $this->addMember($params);
    	if ( $this->isEqual($member, $params) ) return $member['id'];

    	$r['eq'] = array('id'=>$member['id']);
    	$data = array(
    		'username' 	=> $params['username'],
    		'name' 		=> $params['name'],
    		'staffId' 	=> $params['staffId'],
    		);

    	$res = $this->modifyMember($data, $r);
    	if ( $res ) return $member['id'];
    	return false;
    }

    /**
     * 设置用户启用、禁用
     * @author	Xuni
     * @since	2015-09-09
     *
     * @access	public
     * @param	int		$id  	用户id
     * @param	int		$use  	是否启用
     * @return	bool
     */
    public function setMemberUse($id, $use=1)
    {
    	if ( $id <= 0 ) return false;

    	$r['eq'] 		= array('id'=>$id);
    	$data['isUse'] 	= $use;

    	return $this->modifyMember($data, $r);
    }

    /**
     * 设置用户角色
     * @author	Xuni
     * @since	2015-09-09
     *
     * @access	public
     * @param	int		$id  	用户id
     * @param	int		$role  	角色ID
     * @return	bool
     */
    public function setMemberRole($id, $role)
    {
    	if ( $id <= 0 || $role <= 0 ) return false;
    	$r['eq'] 		= array('id'=>$id);
    	$data['roleId']	= $role;

    	return $this->modifyMember($data, $r);
    }

    /**
     * 更新用户信息
     * @author	Xuni
     * @since	2015-09-09
     *
     * @access	public
     * @param	array		$data  	信息
     * @param	array		$r  	条件
     * @return	bool
     */
    public function modifyMember($data, $r)
    {
    	if ( empty($data) || !is_array($data) ) return false;
    	return $this->import('member')->modify($data, $r);
    }

    private function isEqual(array $self, array $other)
    {
    	foreach ($self as $k => $v) {
    		if ( $k == 'id' ) continue;
    		if ( $v != $other[$k] ){
    			return false;
    		}
    	}
    	return true;
    }

    /**
     * 获取第三顾问列表
     * @author	martin
     * @since	2015/11/5
     *
     * @access	public
     * @return	array
     */
	public function otherMember(){
		$r['eq']['name']	= '第三顾问';
		$r['limit']			= 1;
		$role				= $this->import("role")->find($r);
		$roleId				= empty($role) ? 0 : $role['id'];
		$q['eq']			= array("roleId"=>$roleId,'isUse'=>1);
		$q['limit']			= 1000;
		$q['col']			= array("id","username",'name');
		$member				= $this->import("member")->findAll($q);
		return $member['rows'];
	}
}
?>