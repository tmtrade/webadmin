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
    public function isBlack($number)
    {
        if ( empty($number) ) return false;

        $r['eq'] = array(
            'number' => $number,
            );
        $res = $this->import('black')->find($r);
        if ( empty($res) ) return false;
        return $res['id'];
    }

    public function deleteBlack($number)
    {
        if ( !is_array($number) || empty($number) ) return false;
        $r['in'] = array(
            'number' => $number,
            );
        return $this->import('black')->remove($r);
    }

    //多条执行加入黑名单（有事务）
    public function addBlacklist($number, $memo='加入黑名单')
    {
        if ( !is_array($number) || empty($number) ) return false;
        $this->begin('blacklist');
        foreach ($number as $k => $v) {
            
            $res = $this->setBlack($v, $memo);
            //TODO 记录成功和失败的数据，返回给用户！！！
        }
        return $this->commit('blacklist');      
    }

    //单条执行加入黑名单（无事务）
    public function setBlack($number, $memo='加入黑名单')
    {
        if ( $this->isBlack($number) ) return true;

        $info = $this->load('trademark')->getTminfo($number);
        if ( empty($info) ) return false;

        $black = array(
            'tid'       => $info['tid'],
            'number'    => $number,
            'class'     => implode(',', $info['class']),
            'date'      => time(),
            'memberId'  => $this->userId,
            'memo'      => $memo,
            );
        $res = $this->import('black')->create($black);
        if ( $res ) return true;
        return false;
    }

}
?>