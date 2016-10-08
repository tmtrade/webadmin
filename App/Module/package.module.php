<?
/**
 * 商品报价单
 * 
 * 查询、创建
 *
 * @package	Module
 * @author	dower
 * @since	2016-09-13
 */
class PackageModule extends AppModule
{

    /**
     * 引用业务模型
     */
    public $models = array(
        'package'   => 'package',
        'packageitems'      => 'packageitems',
    );

    /**
     * 得到报价单分页数据
     * @param $page
     * @param int $limit
     * @return array
     */
    public function getList($params, $page, $limit=20)
    {
        $r = array();
        $r['page']  = $page;
        $r['limit'] = $limit;
        $r['col'] = array('id','username','title','created');
        $r['order'] = array('created'=>'desc');
        $res = $this->import('package')->findAll($r);
        $items = $this->getPackageItemCount();
        
        
        //处理数据
        if($res['rows']){
            foreach($res['rows'] as &$item){
                $item['count'] = $items[$item['id']];
            }
        }
        return $res;
    }
    
    /**
     * 得到打包的信息
     * @param $id
     * @return string
     */
    public function getPackageInfo($id){
        $r = array();
        $r['eq']['id'] = $id;
        $res = $this->import('package')->find($r);
        return $res;
    }

    /**
     * 得到打包商标信息
     * @param $id
     * @return string
     */
    public function getPackageItemByPid($pid){
        $r = array();
        $r['eq'] = array('pkgId'=>$pid);
        $r['limit'] = 45;
        $r['col'] = array('pkgId','number');
        $r['order'] = array('sort'=>'asc');
        $rst =  $this->import('packageitems')->findAll($r);
        return $rst;
    }
    
    /**
     * 得到打包商标信息
     * @param $id
     * @return string
     */
    public function getPackageItemInfo($pid,$number){
        $r = array();
        $r['eq'] = array('pkgId'=>$pid,'number'=>$number);
        $rst =  $this->import('packageitems')->find($r);
        return $rst;
    }
    
    /**
     * 添加页面提交的打包内容
     * @param type $data
     * @param type $num 限制个数
     * @return 
     */
    public function insertPackage($data,$num, $pkgId=0){
        if(count($data['number'])>$num) return array('code'=>1,'msg'=>'提交数大于'.$num);
        
        $this->begin('package');
        if($pkgId==0){
            $tmp = array(
                'username'          => $this->username,
                'title'             => $data['title'],
                'value'             => $data['value'],
                'price'             => $data['price'],
                'isAll'             => $data['isAll'],
                'label'             => $data['label'],
                'isTop'             => $data['isTop'],
                'desc'              => $data['desc'],
                'viewPhone'         => $data['viewPhone'],
                'created'           => time(),
            );
            $pkgId = $this->addPackage($tmp);
            if(!$pkgId) return array('code'=>1,'msg'=>'打包数据添加失败');
        }else{
            //edit
            $tmp = array(
                'title'             => $data['title'],
                'value'             => $data['value'],
                'price'             => $data['price'],
                'isAll'             => $data['isAll'],
                'label'             => $data['label'],
                'isTop'             => $data['isTop'],
                'desc'              => $data['desc'],
                'viewPhone'         => $data['viewPhone'],
            );
            $this->editPackage($tmp, $pkgId);
            //删除打包数据
            $this->delPackageItems($pkgId);
        }
        foreach($data['number'] as $k=>$v){
            //商品
            $item = array(
                'pkgId'         => $pkgId,
                'number'        => $v,
                'sort'          => ($k+1),
                'created'       => time(),
            );
            $this->begin('packageitems');
            $rst = $this->addPackageItems($item);
            
            //添加标签属性
            $sale = $this->load('internal')->updateOffpriceLabel($v, 4, 1);
            
            if($rst && $sale){
                    $this->commit('packageitems');
            }else{
                $this->rollBack('packageitems');
                $this->rollBack('package');
                return array('code'=>1,'msg'=>"写入数据库失败");
            }
        }
        $this->commit('package');
        return array('code'=>$pkgId);
    }
    
      /**
     * 删除商品单
     * @param $id
     * @param $uid
     * @return bool
     */
    public function delete($id){
        //删除报价单详情
        $this->begin('package');
        $res = $this->delPackageItems($id);//删除打包里面的数据
        if($res){
            //删除报价单表
            $rst = $this->import('package')->remove(array('eq'=>array('id'=>$id)));
            if($rst){
                return $this->commit('package');
            }
        }
        $this->rollBack('package');
        return false;
    }
    /**
     * 添加商品单数据
     * @param array $data
     */
    public function addPackage($data){
        $res = $this->import('package')->create($data);
        return $res;
    }
    
    /**
     * 添加商品单商标数据
     * @param array $data
     */
    public function addPackageItems($data){
        $res = $this->import('packageitems')->create($data);
        return $res;
    }
    
    //添加子分类 edit
	public function editPackage($data, $id)
	{
		$rc['eq'] = array('id' => $id);
		$res      = $this->import('package')->modify($data, $rc);
		return $res;
	}

	//添加子分类 del  对应 items
	public function delPackageItems($pkgId)
	{
        //去除多类转让标签属性
        $rows_number = $this->getPackageItemByPid($pkgId);
        $items = $rows_number['rows'];
        foreach($items as $v){
            $this->load('internal')->updateOffpriceLabel($v['number'], 4, 2);
        }
            
		$rc['eq'] = array('pkgId' => $pkgId);
		$res      = $this->import('packageitems')->remove($rc);
		return $res;
	}
    
    //获取打包里面的商标数量
    public function getPackageItemCount(){
        $r = array();
        $r['col'] = array('pkgId','count(pkgId) as count');
        $r['group'] = array('pkgId' => 'asc');
        $r['limit'] = "10000";
        $res =  $this->import('packageitems')->find($r);
        $arr = array();
        foreach($res as $v){
            $arr[$v['pkgId']] = $v['count'];
        }
        return $arr;
    }
    
    /**
     * 得到报价单内商标类商标数量
     * @param $id
     * @return int
     */
    public function getNumberCount($number){
        $r = array();
        $r['eq'] = array('number'=>$number);
        return $this->import('packageitems')->count($r);
    }
}
?>