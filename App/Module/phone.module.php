<?
/**
* 联系电话列表
*
* 联系电话列表创建，修改，删除等
*
* @package	Module
* @author	Xuni
* @since	2015-12-30
*/
class PhoneModule extends AppModule
{
    public $models = array(
        'phone'     => 'phone',
    );

    public function getList($params, $page=1, $limit=20)
    {
        $r['page']  = $page;
        $r['limit'] = $limit;
        $r['order'] = array('date'=>'desc');

        $res = $this->import('phone')->findAll($r);
        return $res;
    }

    //商标是否在黑名单中
    public function existPhone($phone)
    {
        if ( empty($phone) ) return false;

        $r['eq'] = array(
            'phone' => $phone
            );
        $res = $this->import('phone')->find($r);
        if ( empty($res) ) return false;
        return $res['id'];
    }

    //添加联系电话
    public function addPhone($phone)
    {
        if ( empty($phone) ) return false;
        if ( $this->existPhone($phone) ) return true;

        $data = array(
            'phone'     => $phone,
            'date'      => time(),
            'memberId'  => $this->userId,
            'memo'      => '操作员：《'.$this->username.'》添加',
            );
        $res = $this->import('phone')->create($data);
        if ( $res ) return $res;
        return false;
    }

    //获取所有电话号码，可以排除某一个
    public function getAllPhone($not='')
    {
        $r['limit'] = 10000;
        $r['col']   = array('phone');
        if ( !empty($not) ) $r['raw'] = " phone != '".$not."' ";
        $res = $this->import('phone')->find($r);

        if ( empty($res) ) return array();
        $list = arrayColumn($res, 'phone');
        return $list;
    }

    //随机获取一个电话号码
    public function getRandPhone($not)
    {
        $list = $this->getAllPhone($not);
        $randKey = array_rand($list, 1);
        return $list[$randKey];
    }

    //编辑联系电话并更新商品新的显示电话
    public function editPhone($old, $phone)
    {
        if ( empty($old) || empty($phone) ) return false;
        if ( !$this->existPhone($old) ) return false;
        if ( $this->existPhone($phone) ) return false;
        
        $this->begin('phone');
        $r['eq'] = array('phone'=>$old);
        $data    = array(
            'phone'     => $phone,
            'updated'   => time(),
            'memo'      => '操作员：《'.$this->username.'》更新',
        );
        $flag1  = $this->import('phone')->modify($data, $r);//更新手机
        $flag2  = $this->load('internal')->setAllViewPhone($old, $phone);//更新商品的显示手机
        if ( !$flag1 || !$flag2 ){
            $this->rollBack('phone');
            return false;
        }
        return $this->commit('phone');
    } 

    //删除联系电话并更新商品新的显示电话
    public function deletePhone($phone, $other)
    {
        if ( empty($other) || empty($phone) ) return false;
        if ( !$this->existPhone($phone) ) return false;
        if ( !$this->existPhone($other) ) return false;

        $this->begin('phone');
        $r['eq']    = array('phone'=>$phone);
        $rl['eq']   = array('phone'=>$other);
        $data       = array(
            'updated'   => time(),
            'memo'      => '操作员：《'.$this->username."》删除 $phone 更新为 $other ",
        );
        $flag1   = $this->import('phone')->remove($r);//更新手机
        $flag2   = $this->import('phone')->modify($data, $rl);//更新手机
        $flag3  = $this->load('internal')->setAllViewPhone($phone, $other);//更新商品的显示手机
        if ( !$flag1 || !$flag2 || !$flag3 ){
            $this->rollBack('phone');
            return false;
        }
        return $this->commit('phone');
    }

}
?>