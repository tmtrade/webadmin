<?
/**
 * 队列进程管理
 *
 * 对默认队列进程数进行管控，可根据服务器性能设置总进程数量。
 * 注意：在程序意外中止时，会有小于2秒的暂时停止。
 *
 * @package     ConsoleAction
 * @author      Xuni
 * @since       2015-06-15
 */
class CronAction extends QueueCommonAction
{
    private $cacheType      = 'redisQc';//缓存类型
    private $cacheName      = 'tradeCronCache';//进程的缓存标识，在本文件中判断重复会出现多进程！！！
    private $queueModule    = '/cronjob/%s/';//队列执行组件

    function __construct()
    {
        //可以在超时后执行，destruct无法执行。
        //register_shutdown_function(array(&$this,'destroy'));
        $this->cmd = sprintf("%s %s%s ",PHPPath, CmdDir, "/cmd.php");
        //$this->cacheName = $this->encodeCacheName($this->cacheName);
    }

    private function taskList()
    {
        $list = array(
            array(
                'type' => 'once',//设置时间执行一次
                'time' => '15:00',//24小时制时间如：08:30（表示早上8点半）
                'func' => 'test',
                'name' => 'test1500',//设置执行文件唯一标识（每个task执行名称需不同）
                ),
            array(
            	'type' => 'many',//设置间隔时间执行
            	'time' => 600,//格式为秒
            	'func' => 'test',
            	'name' => 'test600',//设置执行文件唯一标识（每个task执行名称需不同）
            	),
            );

        return $list;
    }

    public function run()
    {
        set_time_limit(0);
        $objRs = $this->com($this->cacheType);//获取缓存资源
        //$cache = $objRs->get($this->cacheName);//获取队列管理管理进程标识

        if ( !is_object($objRs) ) exit('cron cache error');
        //$cache && exit('task running');//处理中时，不再处理
        //$objRs->set($this->cacheName, true, 2);//设置60秒为效，保证此方法只执行一次。
        $list = $this->taskList();
        foreach ($list as $k => $v) {
            $res = $this->doRun($v);
        }
        $objRs->remove($this->cacheName);
        exit('task finished');
    }

    private function doRun($params)
    {
        $type = $params['type'];
        $time = $params['time'];
        $func = $params['func'];
        $name = $params['name'];

        if ( $type == 'once' ) return $this->doOnce($time, $func, $name, $params);
        if ( $type == 'many' ) return $this->doMany($time, $func, $name, $params);
        return false;
    }

    private function doOnce($time, $func, $name, $params)
    {
        $objRs = $this->com($this->cacheType);//获取缓存资源
        $cache = $objRs->get($name);//获取进程标识
        if ( $cache ) return false;        
        if ( $time != date('H:i') ) return false;//是否为当前设置时间
        $objRs->set($name, true, 62);//设置60秒为效，保证此方法只执行一次。
        //执行相关文件
        $res = $this->doQueue($func);
        $log = array(
            'type'      => '5',
            'action'    => '44',
            'data'      => $params,
            'status'    => $res==true?1:2,
            'desc'      => $name,
            'memo'      => $time,
        );
        //$this->load('log')->writeLog($log);
        return $res;
    }

    private function doMany($time, $func, $name, $params)
    {
        $objRs = $this->com($this->cacheType);//获取缓存资源
        $cache = $objRs->get($name);//获取进程标识
        if (  $time <= 0 || $cache ) return false;
        $objRs->set($name, true, $time);//设置60秒为效，保证每间隔多少时间执行一次。
        //执行相关文件
        $res = $this->doQueue($func);
        $log = array(
            'type'      => '5',
            'action'    => '45',
            'data'      => $params,
            'status'    => $res==true?1:2,
            'desc'      => $name,
            'memo'      => $time,
        );
        //$this->load('log')->writeLog($log);
        return $res;
    }
    
    /**
     * 执行文件
     *
     * @access public
     * @param  array    $queue       队列数据
     * @return bool
     */
    private function doQueue($func)
    {
        $resouce    = array(array('pipe','r'));
        $param      = sprintf($this->queueModule, $func);
        $cmd        = $this->cmd.$param;
        $thread     = proc_open($cmd, $resouce, $tmp);
        $res        = is_resource($thread) ? true : false;
        return $res;
    }

}
?>