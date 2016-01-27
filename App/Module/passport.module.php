<?
/**
 * 用户账号登录、验证、修改
 *
 * 用户账号登录、验证、修改
 * 
 * @package	Module
 * @author	Xuni
 * @since	2015-11-05
 */
class PassportModule extends AppModule
{
    private $codeTime   = 600;//手机验证码有效时间(秒)
    private $mvName     = 'mvCode'; //手机验证码名称
    private $mbNo       = 'mbNo'; //验证时的手机号

    /**
     * 获取账号信息
     * 
     * @author  Xuni
     * @since   2015-11-05
     *
     * @access  public
     * @param   string  $email      收件人邮箱
     * @param   string  $title      邮件标题
     *
     * @return  array   $data       用户信息
     */
    public function get($account, $cateId=2)
    {
        $res = $this->importBi('passport')->get($account, $cateId);
        if ( isset($res['code']) && $res['code'] == 1 ){
            return empty($res['data']) ? array() : (array)$res['data'];
        }
        return false;
    }

    /**
     * 通过用户ID获取账号信息
     * 
     * @author  Xuni
     * @since   2015-11-05
     *
     * @access  public
     * @param   array   $userIds      用户ID数组
     *
     * @return  array   $data       用户信息
     */
    public function getListByIds($userIds)
    {
        $res = $this->importBi('passport')->getListByIds($userIds);
        if ( isset($res['code']) && $res['code'] == 1 ){
            return empty($res['data']) ? array() : (array)$res['data'];
        }
        return false;
    }

    /**
     * 根据账号ID 添加并激活手机号
     * 
     * @author  Xuni
     * @since   2015-11-05
     *
     * @access  public
     * @param   int     $mobile     手机号
     * @param   int     $userId     用户ID
     *
     * @return  bool    成功/失败     
     */
    public function changeMoblie($userId, $mobile)
    {
        $res = $this->importBi('passport')->changeMobile($userId, $mobile);
        if ( isset($res['code']) && $res['code'] == 1 ){
            return true;
        }
        return false;
    }
	
    /**
     * 账户登录
     * 
     * @author  Xuni
     * @since   2015-11-05
     *
     * @access  public
     * @param   string  $account    登录账户
     * @param   string  $password   登录密码
     * @param   int     $cateId     账户标识(1邮件、2手机、3用户名)
     *
     * @return  int     $data       用户信息
     */
    public function login($account, $password,  $cateId=1)
    {
        $ip     = getClientIp();
        $res    = $this->importBi('passport')->login($account, $cateId, $password, $ip);
        return $res;
    }

    /**
     * 检查账户是否存在
     * 
     * @author  Xuni
     * @since   2015-11-14
     *
     * @access  public
     * @param   string  $account    登录账户
     * @param   int     $cateId     账户标识(1邮件、2手机、3用户名)
     * @return  int     返回userId(0不存在、大于0存在)
     */
    public function exist($account, $cateId=1)
    {
        $res    = $this->importBi('passport')->exist($account, $cateId);
        return (isset($res['code'])) ? $res : false;
    }


    /**
     * 账户注册
     *
     * @author  Xuni
     * @since   2015-11-14
     *
     * @access  public
     * @param   string  $account    登录账户
     * @param   string  $password   登录密码
     * @param   int     $cateId     账户标识(1邮件、2手机、3用户名)
     * 
     * @return  int     返回userId (0失败或异常、大于0成功)
     */
    public function register($account, $password, $cateId=1)
    {
        $ip     = getClientIp();
        $res    = $this->importBi('passport')->register($account, $password, $cateId, $ip);
        return  (isset($res['code']) && $res['code'] == 1) ? $res['data']['id'] : false;
    }

    /**
     * 注册手机账号
     *
     * 通过手机号进行注册并发送随机的8位密码
     * 
     * @author  Xuni
     * @since   2015-11-14
     *
     * @return  bool
     */
    public function RegTempUser($mobile, $sid, $length=8)
    {
        if ( $length <= 0 || $length > 20 ){
            $length = 8;
        }
        $pass   = randCode($length);//生成8位随机密码
        if ( isCheck($mobile) != 2 ) return false;

        //判断是否在临时数据库中存在
        $user = $this->load('temp')->isExist($account);
        if ( empty($user) ){
            $userId = $this->load('temp')->saveInfo($mobile, $pass, $sid);
            if ( !$userId ) return false;
        }else{
            $pass = $user['password'];
        }

        $msgTemp = C('MSG_TEMPLATE');
        $msg = sprintf($msgTemp['register'], $pass);
        return $this->load('outmsg')->sendMsg($mobile, $msg);
    }

    /**
     * 注册手机账号
     *
     * 通过手机号进行注册并发送随机的8位密码
     * 
     * @author  Xuni
     * @since   2015-11-14
     *
     * @return  bool
     */
    public function autoRegMoblie($mobile, $length=8)
    {
        if ( $length <= 0 || $length > 20 ){
            $length = 8;
        }
        $pass   = randCode($length);//生成8位随机密码
        if ( isCheck($mobile) != 2 ) return false;
        $userId = $this->register($mobile, $pass, 2);
        if ( !$userId ) return false;

        $msgTemp = C('MSG_TEMPLATE');
        $msg = sprintf($msgTemp['register'], $pass);
        $res = $this->load('outmsg')->sendMsg($mobile, $msg);
        if (isset($res['code']) && $res['code'] == 1){
            return $userId;
        }
        return false;
    }

    /**
     * 设置用户登录信息
     * 
     * @author  Xuni
     * @since   2015-11-06
     *
     * @access  public
     * @param   array  $data      用户信息
     *
     * @return  null
     */
    public function setLogin($data)
    {
        $user = array(
            'userId'    => $data['id'],
            'username'  => $data['username'],
            'nickname'  => $data['nickname'],
            'email'     => $data['email'],
            'mobile'    => $data['mobile'],
            'isEmail'   => $data['isEmail'],
            'isMobile'  => $data['isMobile'],
            );

        $uName = C('PUBLIC_USER');
        $uTime = C('PUBLIC_USER_TIME');
        $info  = serialize($user);
        Session::set($uName, $info, $uTime);
    }

    /**
     * 清除验证码记录
     * 
     * @author  Xuni
     * @since   2015-11-06
     *
     * @access  public
     * @return  null
     */
    public function unsetMsgCode()
    {
        $prefix = C('COOKIE_PREFIX');
        $cname  = $prefix.$this->mvName;
        $mbNo   = $prefix.$this->mbNo;

        Session::remove($mbNo);//登录成功清除临时值
        Session::remove($cname);//登录成功清除临时值
		
    }

}
?>