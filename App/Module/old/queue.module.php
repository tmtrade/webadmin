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
    private $cacheType      = 'redis';
    private $queueType      = 'redisQ';
    private $queueName      = 'tradeQueue';

    const SECOND = 5;//秒

    /**
     *  默认队列处理的方法，方便记录日志与管理
     */
    public $methodList = array(
        'verifyEmail'       => '1',//推送官文
        );

    /**
     * 创建队列，将需要执行的队列数据丢入队列中
     * @author      Xuni
     * @since       2015-06-19
     * @param       array       队列数据array(
     *                                   'method'    => $method,//方法
     *                                   'source'    => $source,//方法
     *                                   'data'      => $data,//数据
     *                                   'memo'      => $memo,//备注，可用于测试，方便查找
     *                                   );
     * @return      boolean
     */
    public function addQueue($method, $data, $source='1', $memo='')
    {
        $objRq 	= $this->com($this->queueType)->name($this->queueName);
        $objRs  = $this->com($this->cacheType);

        //5秒内相同的数据不会被重复加入队列执行
        $uKey   = md5($method.serialize($data).$source.$memo);
        if ( $objRs->get($uKey) == true ) return false;
        $objRs->set($uKey, true, self::SECOND);

        $res 	= array(
            'method'    => $method,
            'source'	=> $source,
            'data'      => $data,
            'memo'      => $memo,
            );
        $objRq->push($res);
        return true;
    }
    
}
?>