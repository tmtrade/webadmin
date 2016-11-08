<?
/**
 * 对外发送信息组件
 *
 * 发送邮件、短信
 * 
 * @package	Module
 * @author	Xuni
 * @since	2015-11-05
 */
class OutmsgModule extends AppModule
{

	/**
     * 发送邮件
     * 
	 * @author  Xuni
     * @since   2015-11-05
     *
     * @access  public
     * @param   string  $email      收件人邮箱
     * @param   string  $title      邮件标题
     * @param   string  $content    邮件内容
     * @param   string  $name       收件人名称[没有则为空]
     * @param   string  $from       签名
	 */
    public function sendEmail($email, $title, $content, $name = '', $from='一只蝉')
    {
    	$res = $this->importBi('Message')->sendEmail($email, $title, $content, $name, $from);
        return $res;
    }

    /**
     * 发送短信
     * 
     * @author  Xuni
     * @since   2015-11-05
     *
     * @access  public
     * @param   string  $mobile     收件人手机号
     * @param   string  $content    邮件内容
     * 
     * @return  array
     */
    public function sendMsg($mobile, $content)
    {
        $res = $this->importBi('Message')->sendMsg($mobile, $content);
        return $res;
    }
	
}
?>