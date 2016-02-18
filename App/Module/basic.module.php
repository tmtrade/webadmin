<?
/**
* 首页基本设置
*
* 首页基本设置所有项目创建，修改，删除等
*
* @package	Module
* @author	Xuni
* @since	2016-02-18
*/
class BasicModule extends AppModule
{
    public $models = array(
        'basic'     => 'indexBasic',
    );

    //获取某个设置
    public function getBasic($id)
    {
        if ( empty($id) ) return false;

        $r['eq'] = array('id'=>$id);
        return $this->import('basic')->find($r);
    }

    //获取所有的设置
    public function getAllSetting()
    {
        $r['in']    = array('type'=>array(1,2,3,4));
        $r['order'] = array('type'=>'asc');
        $r['limit'] = 1000;

        $res    = $this->import('basic')->find($r);
        $list   = $this->groupBy($res);
        return $list;
    }

    //按type分组数据
    public function groupBy($data)
    {
        if ( empty($data) || !is_array($data) ) return $data;
        $list = array();
        foreach ($data as $k => $v) {
            if ( $v['type'] ) $list[$v['type']][$v['order']] = $v;
            ksort($list[$v['type']]);
        }
        return $list;
    }

    //获取某种类型的最后一个排序值
    public function getLastOrder($type)
    {
        $r['eq']    = array('type'=>$type);
        $r['order'] = array('order'=>'desc');
        $r['col']   = array('order');
        
        $res    = $this->import('basic')->find($r);
        $order  = empty($res) ? 1 : $res['order']; 
        return $order;
    }

    //创建基本设置
    public function addBasic($data)
    {
        if ( empty($data) ) return false;
        //创建时间
        $data['date'] = time();
        return $this->import('basic')->create($data);
    }

    //修改基本设置
    public function setBasic($data, $id)
    {
        if ( empty($data) || empty($id) ) return false;

        $r['eq'] = array('id'=>$id);

        return $this->import('basic')->modify($data, $r);
    }

    //删除基本设置
    public function delBasic($id)
    {
        if ( empty($id) ) return false;

        $r['eq'] = array('id'=>$id);

        return $this->import('basic')->remove($r);

    }

    //对某类别中某项进行上下排序
    //$updown 1：向上，2：向下
    public function orderUpDown($id, $updown, $type)
    {
        if ( empty($id) ) return false;

        $rl['eq']   = array('id'=>$id);
        $rl['col']  = array('order');
        $res = $this->import('basic')->find($rl);
        if ( empty($res) ) return false;

        $order = $res['order'];

        $r['eq'] = array(
            'type' => $type,
            );
        $r['raw']   = $updown == 1 ? " `order` > $order " : " `order` < $order ";
        $ord        = $updown == 1 ? 'asc' : 'desc';
        $r['order'] = array('order'=>$ord);
        $res = $this->import('basic')->find($r);
        if ( empty($res) ) return false;

        $changeOrder    = $res['order'];
        $changeId       = $res['id'];

        $update1    = array('order'=>$changeOrder);//需要交换的
        $update2    = array('order'=>$order);//被交换的

        $this->begin('indexBasic');

        $flag1 = $this->setBasic($update1, $id);//需要变更的
        $flag2 = $this->setBasic($update2, $changeId);//被变更的

        if ( $flag1 && $flag2 ) {
            return $this->commit('indexBasic');
        }
        $this->rollback('indexBasic');
        return false;
    }

}
?>