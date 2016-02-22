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
class ChannelModule extends AppModule
{
    public $models = array(
        'channel'   => 'channel',
        'items'     => 'channelItems',
    );

    //统计某个设置数据条数
    public function countBasic($type, $isUse=0)
    {
        if ( empty($type) ) return false;

        $r['eq'] = array('type'=>$type);
        if ( !empty($isUse) ){
            $r['eq']['isUse'] = $isUse;
        }
        return $this->import('basic')->count($r);
    }
    //获取某个设置
    public function getChannel($id)
    {
        if ( empty($id) ) return false;

        $r['eq'] = array('id'=>$id);

        $res            = $this->import('channel')->find($r);
        $res['items']   = $this->getItemsList($id);
        return $res;
    }

    //获取所有的设置
    public function getItemsList($channelId)
    {
        if ( empty($id) ) return false;

        $r['eq']    = array('channelId'=>$channelId);
        $r['order'] = array('type'=>'asc');
        $r['limit'] = 1000;

        $res    = $this->import('items')->find($r);
        $list = array(
            '1' => array(),
            '2' => array(),
        );
        if ( empty($res) ) return $list;
        
        foreach ($res as $k => $v) {
            if ( $v['type'] ) $list[$v['type']][$v['sort']] = $v;
            ksort($list[$v['type']]);
        }
        return $list;
    }

    //获取某种类型的最后一个排序值
    public function getLastOrder($channelId, $type)
    {
        $r['eq']    = array('channelId'=>$channelId,'type'=>$type);
        $r['order'] = array('sort'=>'desc');
        $r['col']   = array('sort');
        
        $res    = $this->import('basic')->find($r);
        $order  = empty($res) ? 1 : $res['sort']; 
        return $order;
    }

    //创建基本设置
    public function addChannel($data)
    {
        if ( empty($data) ) return false;
        //创建时间
        $data['date'] = time();
        return $this->import('channel')->create($data);
    }

    //修改基本设置
    public function setChannel($data, $id)
    {
        if ( empty($data) || empty($id) ) return false;

        $r['eq'] = array('id'=>$id);

        return $this->import('channel')->modify($data, $r);
    }

    //创建基本设置
    public function addItems($data)
    {
        if ( empty($data) ) return false;
        //创建时间
        $data['date'] = time();
        return $this->import('items')->create($data);
    }

    //修改基本设置
    public function setItems($data, $id)
    {
        if ( empty($data) || empty($id) ) return false;

        $r['eq'] = array('id'=>$id);

        return $this->import('items')->modify($data, $r);
    }

    //删除基本设置
    public function delItems($id)
    {
        if ( empty($id) ) return false;

        $r['eq'] = array('id'=>$id);

        return $this->import('items')->remove($r);

    }

    //对某类别中某项进行上下排序
    //$updown 1：向上，2：向下
    public function orderUpDown($id, $updown, $type, $channelId)
    {
        if ( empty($id) ) return false;

        $rl['eq']   = array('id'=>$id);
        $rl['col']  = array('sort');
        $res = $this->import('items')->find($rl);
        if ( empty($res) ) return false;

        $order = $res['sort'];

        $r['eq'] = array(
            'type'      => $type,
            'channelId' => $channelId,
            );
        $r['raw']   = $updown == 1 ? " `sort` > $order " : " `sort` < $order ";
        $ord        = $updown == 1 ? 'asc' : 'desc';
        $r['order'] = array('sort'=>$ord);
        $res = $this->import('items')->find($r);
        if ( empty($res) ) return false;

        $changeOrder    = $res['sort'];
        $changeId       = $res['id'];

        $update1    = array('sort'=>$changeOrder);//需要交换的
        $update2    = array('sort'=>$order);//被交换的

        $this->begin('channel');

        $flag1 = $this->setItems($update1, $id);//需要变更的
        $flag2 = $this->setItems($update2, $changeId);//被变更的

        if ( $flag1 && $flag2 ) {
            return $this->commit('channel');
        }
        $this->rollback('channel');
        return false;
    }

}
?>