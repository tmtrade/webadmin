<?
/**
 * 队列处理
 *
 * 处理队列方法
 * 
 * @package	Module
 * @author	Xuni
 * @since	2015-06-18
 */
class QueueModule extends AppModule
{
    private $cacheType      = 'redisQc';
    private $queueType      = 'redisQ';
    private $queueName      = 'tradeQueue';

    const SECOND = 5;//秒

    /**
     *  默认队列处理的方法，方便记录日志与管理
     */
    public $methodList = array(
        'offpriceDown'       => '21',//特价下架，回归定价
        );

    /**
     * 创建队列，将需要执行的队列数据丢入队列中
     * @author      Xuni
     * @since       2015-06-19
     * @param       array       队列数据array(
     *                                   'method'    => $method,//方法
     *                                   'data'      => $data,//数据
     *                                   'memo'      => $memo,//备注，可用于测试，方便查找
     *                                   'source'    => $source,//来源于-（1：后台，2：前台web，3：前台seller，4：手机端）
     *                                   );
     * @return      boolean
     */
    public function addQueue($method, $data, $memo='', $source=1)
    {
        $objRq 	= $this->com($this->queueType)->name($this->queueName);
        $objRs  = $this->com($this->cacheType);

        if ( !is_object($objRq) || !is_object($objRs) ) return false;

        //5秒内相同的数据不会被重复加入队列执行
        $uKey   = md5($method.md5($data).$source.$memo);
        if ( $objRs->get($uKey) == true ) return false;
        //判断是否保存成功---有时redis服务器可用但无法保存成功
        $flag   = $objRs->set($uKey, true, self::SECOND);
        if ( $flag === false ) return false;

        $res 	= array(
            'method'    => $method,
            'source'	=> $source,
            'data'      => $data,
            'memo'      => $memo,
            );
        return $objRq->push($res);
    }

    /*
     * 特价下架，回归定价
     *
     */
    public function offpriceDown($data)
    {

    }

    
}
?>