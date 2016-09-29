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
        'sale'      => 'sale',

    );

    //统计某个设置数据条数
    public function countChannel($type, $channelId)
    {
        if ( empty($type) ) return false;

        $r['eq'] = array('channelId'=>$channelId,'type'=>$type);

        return $this->import('items')->count($r);
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

    //获取某个设置
    public function getItems($id)
    {
        if ( empty($id) ) return false;

        $r['eq']    = array('id'=>$id);
        $res        = $this->import('items')->find($r);
        return $res;
    }

    //获取所有的设置
    public function getItemsList($channelId)
    {
        if ( empty($channelId) ) return false;

        $r['eq']    = array('channelId'=>$channelId);
        $r['order'] = array('type'=>'asc','sort'=>'asc');
        $r['limit'] = 1000;

        $res    = $this->import('items')->find($r);
        $list = array(
            '1' => array(),
            '2' => array(),
            '3' => array(),
            '4' => array(),
            
        );
        if ( empty($res) ) return $list;
        
        foreach ($res as $k => $v) {
            if ( $v['type'] ) $list[$v['type']][$v['sort']] = $v;
        }
        return $list;
    }

    //获取某种类型的最后一个排序值
    public function getLastOrder($channelId, $type)
    {
        $r['eq']    = array('channelId'=>$channelId,'type'=>$type);
        $r['order'] = array('sort'=>'desc');
        $r['col']   = array('sort');
        
        $res    = $this->import('items')->find($r);
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

    //判断商标号是否为真的商标
    // public function existNumber($number)
    // {
    //     if ( empty($number) || !is_array($number) ) return array();

    //     $r['in']    = array('id'=>$number);
    //     $r['limit'] = 10000;
    //     $r['col']   = array('id');
    //     $res = $this->load('trademark')->findTm($r);
    //     $ids = array_filter( arrayColumn($res, 'id') );
    //     return $ids;
    // }

    //判断商标号是否为真的商标
    public function existSale($number)
    {
        if ( empty($number) || !is_array($number) ) return array();

        $r['in']    = array('number'=>$number);
        $r['eq']    = array('status'=>1,'priceType'=>1);
        $r['limit'] = 10000;
        $r['col']   = array('number');
        $res = $this->import('sale')->find($r);
        $ids = array_filter( arrayColumn($res, 'number') );
        return $ids;
    }

    //添加天天低价
    public function addTops($ids, $cid, $type)
    {
        if ( empty($ids) || !is_array($ids) ) return false;
        $order = $this->getLastOrder($cid, 2);
        $this->begin('channel');
        foreach ($ids as $number) {
            if ( $this->existTop($number, $cid, $type) ) continue;
            
            $info   = $this->load('trademark')->getInfo($number, array('`trademark` as `name`,`class`'));
            $order  = $order + rand(2,5);
            $data   = array(
                'channelId' => $cid,
                'type'      => $type,
                'pic'       => $number,
                'link'      => $info['name'],
                'desc'      => $info['class'],
                'sort'      => $order,
                'date'      => time(),
            );
            $res = $this->addItems($data);
            if ( !$res ){
                $this->rollback('channel');
                return false;
            }
        }
        return $this->commit('channel');
    }

    //判断置顶商品是否已存在
    public function existTop($number, $cid, $type)
    {
        if ( empty($number) || empty($cid) ) return false;

        $r['eq'] = array(
            'channelId' => $cid,
            'type'      => $type,
            'pic'       => $number,
        );
        $num = $this->import('items')->count($r);
        return $num > 0 ? true : false;
    }
    
    //添加天天低价
    public function updateGoodsSale($old_number,$number, $cid, $type)
    {
        if ( empty($number)) return false;
        if ( $this->existTop($number, $cid, $type) ) return false;
            
            $info   = $this->load('trademark')->getInfo($number, array('`trademark` as `name`,`class`'));
            $data   = array(
                'pic'       => $number,
                'link'      => $info['name'],
                'desc'      => $info['class'],
            );
        $r['eq'] = array('channelId'=>$cid,'type'=>$type,'pic'=>$old_number);

        return $this->import('items')->modify($data, $r);
    }

}
?>