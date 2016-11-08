<?php
class UserAction extends AppAction
{
	public function index()
	{
		$page 	= $this->input("page","int");
        $search = $this->getFormData();
        $_tmp 	= $search;
        $where  = array(
        	'eq' 	=> array(),
        	'like'	=> array(),
        	);
        foreach ($search as $k => $v) {
        	if ($v == ''){
        		unset($_tmp[$k]);
        		continue;
        	}
        	if ($k == 'username' || $k == 'name'){
        		$where['like'] = array_merge($where['like'], array($k=>$v));
        	}else{
        		$where['eq'] = array_merge($where['eq'], array($k=>$v));
        	}
        }
        $data 	 	= $this->load("member")->getList($where, $page, $this->rowNum);
        $pager   	= $this->pager($data['total'], $this->rowNum);
        $pageBar 	= empty($data['rows']) ? '' : getPageBar($pager);
        $roleList 	= $this->load("role")->getRoleIdNameList();

        $this->set("pageBar",$pageBar);
        $this->set("page",$page);
        $this->set("data",$data);
        $this->set("search",$search);
        $this->set("where", http_build_query($search));
        $this->set("role",$roleList);
		$this->display();
	}

	public function setUse()
	{
		$id  = $this->input("id","int");
		$use = $this->input("use","int");

		$_use = ($use == 1) ? 2 : 1;
		$res  = $this->load('member')->setMemberUse($id, $_use);
		if ( $res ) exit('1');
		exit('2');
	}

	public function role()
	{
		$id  		= $this->input("id","int");
		$roleList 	= $this->load("role")->getRoleIdNameList(1);
		$info 		= $this->load("member")->getMemberById($id);

		$this->set("id",$id);
		$this->set("roleId",$info['roleId']);
		$this->set("role",$roleList);
		$this->display();
	}

	public function add()
	{
		$roleList 	= $this->load("role")->getRoleIdNameList(1);

		$this->set("role",$roleList);
		$this->display();
	}

	public function addUser()
	{
		$uname 	= $this->input("username","string");
		$role 	= $this->input("roleId","int");

		if (empty($uname) || $role <= 0) exit('2');

		$user = $this->load('member')->get($uname);
		if ( $user ) exit('3');
		
		$info = array(
			'username' 	=> $uname,
			'roleId' 	=> $role,
			'isUse' 	=> 1,
			);
		$res = $this->load('member')->addMember($info);
		if ( $res ) exit('1');
		exit('4');
	}

	public function setRole()
	{
		$id 	= $this->input("id","int");
		$role 	= $this->input("roleId","int");
		$info 	= $this->load("member")->getMemberById($id);
		if ($info['roleId'] > 0 && $info['roleId'] == $role) exit('1');
		$res  = $this->load('member')->setMemberRole($id, $role);
		if ( $res ) exit('1');
		exit('2');
	}

	public function checkCrmName()
	{
		$uname 	= $this->input("name","string");
		if ( empty($uname) ) exit(2);
		$res 	= $this->load('member')->checkCrmName($uname);
		if ( $res ) exit('1');
		exit('3');
	}
}
?>