<?
/**
 * 角色模块
 *
 * 添加、删除，设置权限
 * 
 * @package	Module
 * @author	Xuni
 * @since	2015-06-17
 */
class RoleModule extends AppModule
{
    /**
     * 引用业务模型
     */
    public $models = array(
        'role'	=> 'role',
        );

    /**
     * 获取角色信息
     * @author  Xuni
     *
     * @param   string  $name   角色名 
     *
     * @return  array
     */
    public function get($name)
    {
        $r['eq'] = array('name'=>$name);

        return $this->import('role')->find($r);
    }

    /**
     * 新增角色
     * @author  Xuni
     *
     * @param   array  $data   角色信息
     *
     * @return  int
     */
    public function addRole($data)
    {
        $info = array(
            'name' => $data['name'],
            'role' => $data['role'],
        );

        return $this->import('role')->create($info);
    }

    /**
     * 获取以角色ID为key名称name为值的数组
     * @author  Xuni
     *
     * @param   int  $isUse   是否可用
     *
     * @return  array
     */
    public function getRoleIdNameList($isUse=0)
    {
        $r['col'] = array('id','name');
        if ( $isUse == 1 ) $r['eq'] = array('isUse'=>1);
        $r['limit'] = 1000;
        $res = $this->import('role')->find($r);
        return arrayColumn($res, 'name', 'id');
    }

    /**
     * 通过ID获取角色信息
     * @author  Xuni
     *
     * @param   int  $id   ID
     *
     * @return  array
     */
    public function getRoleById($id)
    {
        $r['limit'] = 1;
        //$r['col']   = array("roleId");
        $r['eq'] = array("id"=>$id);
        return $this->import('role')->find($r);
    }

    /**
     * 处理单条角色信息
     * @author  Xuni
     *
     * @param   array  $list   角色信息列表
     *
     * @return  array
     */
    private function makeList(array $list)
    {
        if ( empty($list) ) return $list;
        $allAuth    = C('ALL_AUTH');
        $authList   = C('AUTH_LIST');
        $_list      = arrayColumn($allAuth, 'up', 'id');
        foreach ($list as $k => $v) {
            $list[$k]['roleStr'] = '';
            if ( empty($v['role']) ) continue;
            $roleList = explode(',', $v['role']);
            $_tmpList = array();
            foreach ($roleList as $val) {
                $_tmpList[$_list[$val]][] = $allAuth[$val]['label'];
            }
            $_tmpList2 = array();
            foreach ($_tmpList as $key => $value) {
                $_str = $authList[$key]['label'];
                $_tmpList2[] = $_str.'--'.implode(',', $value);
            }
            $list[$k]['roleStr'] = implode('<br />', $_tmpList2);
        }
        return $list;
    }

    /**
     * 获取相关角色信息列表
     * @author  Xuni
     * @since   2015-09-10
     *
     * @access  public
     * @param   array       $where      条件
     * @param   int         $page       页码
     * @param   int         $limit      条数
     * @return  array
     */
    public function getList($where, $page=1, $limit=20)
    {
        $r['limit']     = $limit;
        $r['page']      = $page > 0 ? $page : 1;
        $r['order']     = array("id"=>"asc");
        if ( !empty($where['eq']) ) $r['eq'] = $where['eq'];
        if ( !empty($where['like']) ) $r['like'] = $where['like'];

        $res = $this->import("role")->findAll($r);
        $res['rows'] = $this->makeList($res['rows']);
        return $res;
    }

    /**
     * 设置角色启用、禁用
     * @author  Xuni
     * @since   2015-09-10
     *
     * @access  public
     * @param   int     $id     用户id
     * @param   int     $use    是否启用
     * @return  bool
     */
    public function setRoleUse($id, $use=1)
    {
        if ( $id <= 0 ) return false;

        $r['eq']        = array('id'=>$id);
        $data['isUse']  = $use;

        return $this->modifyRole($data, $r);
    }

    /**
     * 修改角色信息
     * @author  Xuni
     *
     * @param   int     $id      ID
     * @param   array   $role    角色信息
     *
     * @return  bool
     */
    public function setRole($id, array $role)
    {
        if ( empty($id) || empty($role) ) return false;

        $r['eq']        = array('id'=>$id);
        $data['role']   = implode(',', $role);

        return $this->modifyRole($data, $r);
    }

    /**
     * 更新角色信息
     * @author  Xuni
     * @since   2015-09-10
     *
     * @access  public
     * @param   array       $data   信息
     * @param   array       $r      条件
     * @return  bool
     */
    public function modifyRole($data, $r)
    {
        if ( empty($data) || !is_array($data) ) return false;
        return $this->import('role')->modify($data, $r);
    }


}
?>