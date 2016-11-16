<?
/**
* 商标相关信息
*
* 查询商标基础信息、图片、状态等
*
* @package	Module
* @author	Xuni
* @since	2015-10-22
*/
class TmModule extends AppModule
{
    public $models = array(
        'img'		=> 'imgurl',
        'tm'		=> 'trademark',
        'second'	=> 'secondStatus',
        'proposer'  => 'proposer',
        'new'       => 'proposerNew',
        //'third'		=> 'statusnew',
        'tmdata'    => 'tmdata',
    );

    public function setAll($data)
    {
        if ( empty($data) ) return false;

        $this->begin('trademark');

        $flag1 = $this->setInfo($data['info']);
        $flag2 = $this->setProposer($data['proposer']);
        $flag3 = $this->setProposerNew($data['proposerNew']);
        $flag4 = $this->setSecond($data['second']);
        $flag5 = $this->setImage($data['image']);

        if ( !$flag1 || !$flag2 || !$flag3 || !$flag4 || !$flag5 ){
            $this->rollback('trademark');
            return false;
        }
        return $this->commit('trademark');
    }

    public function setInfo($data)
    {
        if ( empty($data) || !is_array(current($data)) ) return false;

        foreach ($data as $k => $val) {
            $id     = $val['auto'];
            $info   = $this->import('tm')->get($id);
            if ( empty($info) ){
                $flag = $this->import('tm')->create($val);
            }else{
                continue;
                unset($val['auto']);
                $flag = $this->import('tm')->modify($val, $id);
            }
            if ( !$flag ) return false;
        }
        return true;
    }

    public function setProposer($data)
    {
        if ( empty($data) ) return false;

        $id     = $data['id'];
        $info   = $this->import('proposer')->get($id);
        if ( empty($info) ){
            return $this->import('proposer')->create($data);
        }
        return true;
        unset($data['id']);
        return $this->import('proposer')->modify($data, $id);
    }

    public function setProposerNew($data)
    {
        if ( empty($data) ) return false;

        $id     = $data['id'];
        $info   = $this->import('new')->get($id);
        if ( empty($info) ){
            return $this->import('new')->create($data);
        }
        return true;
        unset($data['id']);
        return $this->import('new')->modify($data, $id);
    }

    public function setSecond($data)
    {
        if ( empty($data) || !is_array(current($data)) ) return false;

        foreach ($data as $k => $val) {
            $id     = $val['status_id'];
            $info   = $this->import('second')->get($id);
            if ( empty($info) ){
                $flag = $this->import('second')->create($val);
            }else{
                continue;
                unset($val['status_id']);
                $flag = $this->import('second')->modify($val, $id);
            }
            if ( !$flag ) return false;
        }
        return true;
    }

    public function setImage($data)
    {
        if ( empty($data) ) return false;

        $id     = $data['auto'];
        $info   = $this->import('img')->get($id);
        if ( empty($info) ){
            return $this->import('img')->create($data);
        }
        return true;
        unset($data['auto']);
        return $this->import('img')->modify($data, $id);
    }

    public function setTmData($number, $data)
    {
        $data = array(
            'number'    => $number,
            'data'      => serialize($data),
        );
        return $this->import('tmdata')->create($data);
    }

    public function existTmData($number)
    {
        if ( empty($number) ) return false;

        $r['eq']    = array('number'=>$number);
        $info       = $this->import('tmdata')->find($r);
        if ( empty($info) ) return array();
        return $info;
    }

}
?>