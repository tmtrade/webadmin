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
        'class'     => 'tmclass',
    );

    /**
     * 获取出售商标的包装图片
     * 
     * @author  Xuni
     * @since   2016-07-04
     *
     * @return  array
     */
    public function getSaleImg($number)
    {
        $list = array_filter(explode(',', $number));
        if ( empty($list) ) return array();
        $data = array();
        foreach ($list as $v) {
            $data[$v] = $this->saleImg($v);
        }
        return $data;
    }

    //获取单条商标包装图片
    protected function saleImg($number)
    {
        $saleId = $this->load('internal')->existsale($number);

        if ( $saleId ) {
            return $this->getViewImg($saleId);
        }

        return $this->load('trademark')->getImg($data['number']);
    }

    /**
     * 获取随机的出售信息
     *
     * 通过分类，获取不同分类的商标出售信息
     * 
     * @author  Xuni
     * @since   2016-03-08
     *
     * @return  array
     */
    public function getRandSale(array $params=array() )
    {
        $class = $params['class'];
        $limit = $params['limit'];
        if ( empty($class) || !is_numeric($class) ) return array();

        $code   = rand(0,9);
        $time   = 600;//10分钟
        $name   = 'getRandomTm_'.$class.'_'.$limit;
        $random = $this->com('redisHtml')->get($name);
        //return count($random);
        if ( !empty($random) && is_array($random) ){            
            if ( !empty($random[$code]) ) return $random[$code];
        }

        $r['eq']    = array('class'=>$class,'status'=>'1');
        $r['limit'] = 50;
        $r['col']   = array('number','class','name','pid','tid','id');
        $r['order'] = array('isTop'=>'desc');

        $list   = array();
        $res    = $this->import('sale')->find($r);
        $list   = $this->doRand($list, $res, $limit);
        if ( empty($list) ) return array();
        $titles = $this->com('redisHtml')->get('ClassGroupTitle');
        if ( is_array($titles) ){
            list($_class, $_group) = $titles;
        }else{
            $titles = $this->getClassGroup(0,0);
            list($_class, $_group) = $titles;
            $this->com('redisHtml')->set('ClassGroupTitle', $titles, $time);
        }
        
        foreach ($list as $k => $v) {
            //获取商标图片
            $_url = $this->getViewImg($v['id']);
            $list[$k]['imgUrl']     = $_url;
            $list[$k]['className']  = $_class[$class] ? $_class[$class] : '';
            $list[$k]['viewUrl']    = SITE_URL.'d-'.$v['tid']."-$class.html";
            unset($list[$k]['id'],$list[$k]['pid']);
        }
        $random[$code] = $list;
        $this->com('redisHtml')->set($name, $random, $time);
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

    /**
     * 获取商标分类与群组相关标题
     *
     * 获取商标分类与群组相关标题
     * 
     * @author  Xuni
     * @since   2016-03-08
     *
     * @return  array
     */
    public function getClassGroup($class=0, $group=1)
    {
        if ( $class == 0 && $group != 1 ){
            $r['eq'] = array('parent'=>0);
        }elseif ( $class != 0 && $group == 1 ){
            //$r['eq'] = array('parent'=>$class);
            $r['raw'] = " (`parent` = '$class' OR `number` = '$class') ";
        }elseif ( $class != 0 ){
            $r['eq'] = array('number'=>$class);
        }
        //$r['order'] = array('parent'=>'asc','number'=>'asc');
        $r['limit'] = 1000;

        $_class = $_group = array();
        $res    = $this->import('class')->find($r);
        if ( empty($res) ) return array();

        foreach ($res as $k => $v) {
            if ( $v['parent'] == '0' ){
                $_class[$v['number']] = empty($v['title']) ? $v['name'] : $v['title'];
            }elseif ( $v['parent'] != 0 ){
                $_group[$v['parent']][$v['number']] = empty($v['title']) ? $v['name'] : $v['title'];
            }
        }
        ksort($_class);
        return array($_class, $_group);
    }
}
?>