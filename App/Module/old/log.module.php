<?
/**
 * 日志模块
 *
 * 记录各项日志
 * 
 * @package	Module
 * @author	Xuni
 * @since	2015-06-17
 */
class LogModule extends AppModule
{
    /**
     * 引用业务模型
     */
    public $models = array(
        'system'	=> 'SystemLog',
        );

    /**
     * 获取用户信息
     * @author      Xuni
     * @since       2015-04-22
     *
     * @access      public
     * @param       array     $params     数据包
     * @return      void
     */
    public function writeLog($params)
    {
        $msg        = empty($params['desc']) ? '' : $params['desc'];
        $memo       = empty($params['memo']) ? '' : $params['memo'];
        $type       = empty($params['type']) ? 3 : $params['type'];
        $action     = empty($params['action']) ? 0 : $params['action'];
        $status     = empty($params['status']) ? 1 : $params['status'];
        $data		= empty($params['data']) ? '' : $params['data'];

        $data 		= is_array($data) ? serialize($data) : $data;
        $memo 		= is_array($memo) ? serialize($memo) : $memo;

        $log = array(
            'type'   	=> $type,
            'action'   	=> $action,
            'status'  	=> $status,
            'data'    	=> $data,
            'desc'    	=> $msg,
            'created'	=> time(),
            'memo'   	=> $memo,
            );

        $this->import('system')->create($log);
    }


}
?>