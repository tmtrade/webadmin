<?
/**
 * 公共console类
 *
 * 公共console继承类
 *
 * @package      Action
 * @author       Xuni
 * @since       2015-04-17
 */
class QueueCommonAction extends ConsoleAction
{
    
    public function encodeCacheName($name)
    {
        //$res = md5($name);
        $res = $name;
        return $res;
    }

    /**
     * 解密队列数据
     *
     * @param   string      $data   队列加密数据
     * @return  array
     */
    public function decodeQueueData($data)
    {
        return unserialize($data);
    }

    /**
     * 加密队列数据
     *
     * @param   array   $data   队列数据
     * @return  string
     */
    public function encodeQueueData($data)
    {
        return serialize($data);
    }

}
?>