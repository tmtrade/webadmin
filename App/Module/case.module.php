<?
/**
* 成功案例
*
* 成功案例的增删改查
*
* @package	Module
* @author	dower
* @since	2016-03-21
*/
class CaseModule extends AppModule
{
    public $models = array(
        'case'		=> 'case',
    );

    /**
     * 得到成功案例的列表数据
     * @param $params
     * @param $page
     * @param int $limit
     * @return array
     */
    public function getList($params, $page, $limit=20)
    {
        $r = array();
        $r['page']  = $page;
        $r['limit'] = $limit;
        $r['order'] = array('sort'=>'asc');
        $res = $this->import('case')->findAll($r);
        return $res;
    }

    /**
     * 得到成功案例信息
     * @param $moduleId
     * @return array
     */
    public function getCaseInfo($id)
    {
        $r = array();
        $r['eq']['id']  = $id;
        $r['limit'] = 1;
        $res = $this->import('case')->find($r);
        return $res;
    }

    /**
     * 得到专题的最好一个排序值
     * @param string $where
     * @return int
     */
    private function getLastSort($where = '')
    {
		if($where){
				 $r['eq']    = $where;
		}
        $r['order'] = array('sort'=>'desc');
        $r['col']   = array('sort');
        
        $res    = $this->import('case')->find($r);
        $sort  = empty($res) ? 0 : $res['sort']; 
        return $sort;
    }

    /**
     * 排序
     * @param $id
     * @param $updown
     * @param string $where
     * @return bool|int
     */
    public function sortUpDown($id, $updown, $where='')
    {
        if ( empty($id) ) return false;
        $rl['eq']   = array('id'=>$id);
        $rl['col']  = array('sort');
        $res = $this->import('case')->find($rl);
        if ( empty($res) ) return false;
        $order = $res['sort'];
        $r['raw']   = $updown == 1 ? " `sort` > $order " : " `sort` < $order ";
        $ord        = $updown == 1 ? 'asc' : 'desc';
        $r['order'] = array('sort'=>$ord);
        if($where){
            $r['eq'] = $where;
        }
        $res = $this->import('case')->find($r);
		
        if ( empty($res) ) return false;

        $changeOrder    = $res['sort'];
        $changeId       = $res['id'];

        $update1    = array('sort'=>$changeOrder);//需要交换的
        $update2    = array('sort'=>$order);//被交换的

		$rO['eq'] = array('id'=>$id);
		$rOC['eq'] = array('id'=>$changeId);
		$flag1 = $this->import('case')->modify($update1, $rO);
		$flag2 = $this->import('case')->modify($update2, $rOC);

        if ( $flag1 && $flag2 ) {
            return 1;
        }
        return false;
    }


    /**
     * 添加成功案例
     * @param $data
     * @return int
     */
    public function addCase($data)
    {    
		$sort = $this->getLastSort();
		$data['sort'] =  $sort + rand(2,5);
        return $this->import('case')->create($data);
    }

    /**
     * 修改成功案例
     * @param $data
     * @param $id
     * @return bool
     */
    public function updateCase($data, $id)
    {
        $r['eq'] = array('id'=>$id);
        return $this->import('case')->modify($data, $r);
    }

    /**
     * 删除成功案例
     * @param $id
     * @return bool
     */
    public function delCase($id)
    {
		$r = array();
        if ( empty($id) ) return false;
        $r['eq'] = array('id'=>$id);
        return $this->import('case')->remove($r);
    }
}
?>