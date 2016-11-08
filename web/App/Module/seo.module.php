<?

/**
 * SEO设置
 *
 * SEO创建，修改，删除等
 *
 * @package    Module
 * @author     Far
 * @since      2016年4月6日10:10:07
 */
class SeoModule extends AppModule
{
    
    public $models = array(
            'seo'            => 'seo',
    );
    
    //index 列表
    public function getList($params, $page, $limit=20)
    {
            $r['eq']    = $params;
            $r['page']  = $page;
            $r['limit'] = $limit;
            $r['order'] = array('date' => 'desc');
            $res        = $this->import('seo')->findAll($r);
            return $res;
    }
    
    //info 单条
    public function getInfo($id)
    {
            $r['eq']    = array(
                    'id' => $id
            );
            $r['limit'] = 1;
            $res        = $this->import('seo')->find($r);
            if (!empty($res['label'])) {
                    $res['labelArr'] = explode(",", $res['label']);
            }
            return $res;
    }
    
    //info 单条
    public function getInfoByType($type,$vid="")
    {
            $r['eq']    = array(
                    'type' => $type
            );
            if(!empty($vid)){
                $r['eq']['vid'] = $vid;
            }
            $r['limit'] = 1;
            $res        = $this->import('seo')->find($r);
            return $res;
    }
        
    /**
     * 添加一条数据
     * @param $data
     * @return int
     */
    public function addSeo($data)
    {    
        return $this->import('seo')->create($data);
    }
        
    /**
     * 修改SEO
     * @param $data
     * @param $id
     * @return bool
     */
    public function updateSeo($data, $id)
    {
        $r['eq'] = array('id'=>$id);
        return $this->import('seo')->modify($data, $r);
    }
    
    //详情页面设置SEO
    public function viewSetSeo($sid,$data,$type)
    {
            if ( empty($sid) ){
                if(!empty($data['seotitle']) || !empty($data['keyword']) || !empty($data['description'])){
                    $params = array(
                        'type'          => $type,
                        'vid'           => $data['vid'],
                        'title'         => $data['seotitle'],
                        'keyword'       => $data['keyword'],
                        'description'   => $data['description'],
                        'isUse'         => $data['isUse'],
                        'date'          => time(),
                    );
                    $res = $this->addSeo($params);
                }else{
                     return array('code'=>1,'msg'=>'成功');
                }
            }else{
                $params = array(
                    'title'         => $data['seotitle'],
                    'keyword'       => $data['keyword'],
                    'description'   => $data['description'],
                    'isUse'         => $data['isUse'],
            );
                $res = $this->updateSeo($params, $sid);
            }
            if ( $res ){
                return array('code'=>1,'msg'=>'成功');
            }
            return array('code'=>2,'msg'=>'创建失败啦');
    }
    /**
     * 删除SEO
     * @param $id
     * @return bool
     */
    public function delSeo($id)
    {
		$r = array();
        if ( empty($id) ) return false;
        $r['eq'] = array('id'=>$id);
        return $this->import('seo')->remove($r);
    }
    
    //移除标签
    public function removeLabel($id, $label)
    {
            $mres       = 0;
            $r['eq']    = array(
                    'id' => $id
            );
            $r['limit'] = 1;
            $res        = $this->import('seo')->find($r);
            if (!empty($res['label'])) {
                    $labelArr 	= explode(",", $res['label']);
                    $_key 		= array_search($label, $labelArr);
                    if ($_key !== false) unset($labelArr[$_key]);
                    $data['label'] = implode(",", $labelArr);
                    $rm['eq']      = array('id' => $res['id']);
                    $mres          = $this->import('seo')->modify($data, $rm);
            }
            return $mres;
    }
       
    
    //添加标签
    public function addLabel($id, $label)
    {
            $mres       = 0;
            $r['eq']    = array(
                    'id' => $id
            );
            $r['limit'] = 1;
            $res        = $this->import('seo')->find($r);
            if (!empty($res['label'])) {
                    $res['labelArr'] = explode(",", $res['label']);
                    if (in_array($label, $res['labelArr'])) {
                            $mres = 2; //有重复的了。
                    } else {
                            $res['labelArr'][] = $label;
                            $data['label']     = implode(",", $res['labelArr']);
                            $rm['eq']          = array('id' => $res['id']);
                            $this->import('seo')->modify($data, $rm);
                            $mres = 1;
                    }
            }else{
                    $data['label']     = $label;
                    $rm['eq']          = array('id' => $id);
                    $this->import('seo')->modify($data, $rm);
                    $mres = 1;
            }
            return $mres;
    }

    /**
     * 直接执行sql
     *
     * @author    martin
     * @since     2015/10/21
     *
     * @access    public
     *
     * @param     string $dbName 字符串
     *
     * @return    void
     */
    public function fetchAll($dbName, $sql)
    {
            static $db = null;
            if ($db == null) {
                    $db = new DbQuery($dbName);
            }
            return $db->fetchAll($sql);
    }

}
?>