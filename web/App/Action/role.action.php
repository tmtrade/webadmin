<?php
class RoleAction extends AppAction
{
	public function index()
	{
		$page 	= $this->input("page","int");
		$name 	= $this->input("name","string");

        $where['like'] = array('name'=>$name);

        $data 	 	= $this->load("role")->getList($where, $page, $this->rowNum);
        $pager   	= $this->pager($data['total'], $this->rowNum);
        $pageBar 	= empty($data['rows']) ? '' : getPageBar($pager);

        $this->set("pageBar",$pageBar);
        $this->set("page",$page);
        $this->set("data",$data);
        $this->set("name",$name);
        $this->set("where", http_build_query(array('name'=>$name)));
		$this->display();
	}

	public function error()
	{
		$this->display();
	}

	public function setUse()
	{
		$id  = $this->input("id","int");
		$use = $this->input("use","int");

		$_use = ($use == 1) ? 2 : 1;
		$res  = $this->load('role')->setRoleUse($id, $_use);
		if ( $res ) exit('1');
		exit('2');
	}

	public function edit()
	{
		$id  		= $this->input("id","int");
		$authList 	= C('AUTH_LIST');
		$info 		= $this->load("role")->getRoleById($id);
		if ( !empty($info['role']) )
			$roleList 	= explode(',', $info['role']);
		else
			$roleList 	= array();

		$this->set("id", $id);
		$this->set("name", $info['name']);
		$this->set("role", $roleList);
		$this->set("authList", $authList);
		$this->display();
	}

	public function setRole()
	{
		$id  	= $this->input("id","int");
		$role 	= $this->input("role","array");
		$name 	= $this->input("name","string");
		if ( empty($id) || empty($role) ) exit('2');
		if ( empty($name) ) exit('3');
		sort($role);
		$res  = $this->load('role')->setRole($id, $role, $name);
		if ( $res ) exit('1');
		exit('0');		
	}

	public function add()
	{
		$authList 	= C('AUTH_LIST');

		$this->set("authList",$authList);
		$this->display();
	}

	public function addRole()
	{
		$name  	= $this->input("name","string");
		$role 	= $this->input("role","array");

		if (empty($name) || empty($role)) exit('2');

		$user = $this->load('role')->get($name);
		if ( $user ) exit('3');
		
		sort($role);
		$info = array(
			'name' 		=> $name,
			'role' 		=> implode(',', $role),
			'isUse' 	=> 1,
			);
		$res = $this->load('role')->addRole($info);
		if ( $res ) exit('1');
		exit('4');
	}
}
?>