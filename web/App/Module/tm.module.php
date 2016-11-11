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
    );

    public function setAll($data)
    {
        if ( empty($data) ) return false;

        $this->begin('trademark');

        $flag1 = $this->setInfo($data['info']);
        $flag2 = $this->setInfo($data['proposer']);
        $flag3 = $this->setInfo($data['proposerNew']);
        $flag4 = $this->setInfo($data['second']);
        $flag5 = $this->setInfo($data['image']);

        if ( !$flag1 || !$flag2 || !$flag3 || !$flag4 || !$flag5 ){
            $this->rollback('trademark');
            return false;
        }
        return $this->commit('trademark');
    }

    public function setInfo($data)
    {
        if ( empty($data) ) return false;

        $id     = $data['auto'];
        $info   = $this->import('tm')->get($id);
        if ( empty($info) ){
            return $this->import('tm')->create($data);
        }
        unset($data['auto']);
        return $this->import('tm')->modify($data, $id);
    }

    public function setProposer($data)
    {
        if ( empty($data) ) return false;

        $id     = $data['id'];
        $info   = $this->import('proposer')->get($id);
        if ( empty($info) ){
            return $this->import('proposer')->create($data);
        }
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
        unset($data['id']);
        return $this->import('new')->modify($data, $id);
    }

    public function setSecond($data)
    {
        if ( empty($data) ) return false;

        $id     = $data['status_id'];
        $info   = $this->import('second')->get($id);
        if ( empty($info) ){
            return $this->import('second')->create($data);
        }
        unset($data['status_id']);
        return $this->import('second')->modify($data, $id);
    }

    public function setImage($data)
    {
        if ( empty($data) ) return false;

        $id     = $data['auto'];
        $info   = $this->import('img')->get($id);
        if ( empty($info) ){
            return $this->import('img')->create($data);
        }
        unset($data['auto']);
        return $this->import('img')->modify($data, $id);
    }

}
?>