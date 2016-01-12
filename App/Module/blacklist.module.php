<?
/**
* 国内商标
*
* 国内商标商品创建，修改，删除等
*
* @package	Module
* @author	Xuni
* @since	2015-12-30
*/
class BlacklistModule extends AppModule
{
    public $models = array(
        'black'     => 'blacklist',
    );

    //商标是否在黑名单中
    public function existBlack($number)
    {
        if ( empty($number) ) return false;

        $r['eq'] = array(
            'number' => $number,
            );
        $res = $this->import('black')->find($r);
        if ( empty($res) ) return false;
        return true;
    }

    public function deleteBlack($number)
    {
        if ( !is_array($number) || empty($number) ) return false;
        $r['in'] = array(
            'number' => $number,
            );
        return $this->import('black')->remove($r);
    }


    public function addBlack($number, $memo='加入黑名单')
    {
        if ( !is_array($number) || empty($number) ) return false;
        $this->begin('blacklist');
        foreach ($number as $k => $v) {
            if ( $this->existBlack($v) ) continue;
            $info = $this->load('trademark')->getTminfo($v);
            if ( empty($info) ){
                $this->rollBack('blacklist');
                return false;
            }

            $black = array(
                'tid'       => $info['tid'],
                'number'    => $v,
                'class'     => implode(',', $info['class']),
                'date'      => time(),
                'memberId'  => $this->userId,
                'memo'      => $memo,
                );
            $res = $this->import('black')->create($black);
            if ( !$res ){
                $this->rollBack('blacklist');
                return false;
            } 
        }
        return $this->commit('blacklist');      
    }

}
?>