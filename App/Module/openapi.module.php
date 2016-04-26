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
class OpenApiModule extends AppModule
{
    public $models = array(
        'sale'      => 'sale',
        'tminfo'    => 'saleTminfo',
    );

    public function getRandSale(array $params=array() )
    {
        $class = $params['class'];
        $limit = $params['limit'];
        if ( empty($class) || !is_numeric($class) ) return array();

        $r['eq']    = array('class'=>$class,'status'=>'1');
        $r['limit'] = 50;
        $r['col']   = array('number','class','name','pid','tid','id');
        $r['order'] = array('isTop'=>'desc');

        $list   = array();
        $res    = $this->import('sale')->find($r);
        $list   = $this->doRand($list, $res, $limit);
        foreach ($list as $k => $v) {
            //获取商标图片
            $_url = $this->getViewImg($v['id']);
            $list[$k]['imgUrl']     = $_url;
            $list[$k]['viewUrl']    = SITE_URL.'d-'.$v['tid']."-$class.html";
            unset($list[$k]['id'],$list[$k]['<p></p>id']);
        }
        
        return $list;
    }

    //随机获取数组元素
    protected function doRand($list, $rlist, $num)
    {
        if ( count($rlist) <= $num ) return array_merge($list, $rlist);

        $tmpList    = array();
        $rand       = array_rand($rlist, $num);
        if ($num != 1 && is_array($rand)){
            foreach ($rand as $k => $v) {
                array_push($tmpList, $rlist[$v]);
            }
        }else{
            array_push($tmpList, $rlist[$rand]);
        }
        return array_merge($list, $tmpList);
    }

    //获取详情图片
    public function getViewImg($id)
    {   
        if ( $id <= 0 ) return '';

        $r['eq']    = array('saleId'=>$id);
        $r['col']   = array('embellish','number');
        $data       = $this->import("tminfo")->find($r);
        if( empty($data['embellish']) ){
            if ( empty($data['number']) ) return '';
            $url = $this->load('trademark')->getImg($data['number']);
        }else{
            $url = TRADE_URL.$data['embellish'];
        }
        return $url;
    }
}
?>