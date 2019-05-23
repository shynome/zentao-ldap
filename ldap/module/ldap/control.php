<?php
/**
 * The control file of user module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv11.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     user
 * @version     $Id: control.php 5005 2013-07-03 08:39:11Z chencongzhi520@gmail.com $
 * @link        http://www.zentao.net
 */
class ldap extends control
{
    public $referer;

    /**
     * Construct 
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->locate(inlink('setting'));
    }

    public function setting() 
    {
        $this->view->title      = $this->lang->ldap->common . $this->lang->colon . $this->lang->ldap->setting;
        $this->view->position[] = html::a(inlink('index'), $this->lang->ldap->common);
        $this->view->position[] = $this->lang->ldap->setting;

        $this->display();
    }

    public function save()
    {
        if (!empty($_POST)) {
            $this->config->ldap->host = $this->post->ldapHost;
            $this->config->ldap->version = $this->post->ldapVersion;
            $this->config->ldap->bindDN = $this->post->ldapBindDN;
            $this->config->ldap->bindPWD = $this->post->ldapPassword;
            $this->config->ldap->baseDN =  $this->post->ldapBaseDN;
            $this->config->ldap->searchFilter = $this->post->ldapFilter;
            $this->config->ldap->uid = $this->post->ldapAttr;
            $this->config->ldap->mail = $this->post->ldapMail;

            // 此处我们把配置写入配置文件
            $ldapConfig = "<?php \n"
                          ."\$config->ldap = new stdclass();\n"
                          ."\$config->ldap->host = '{$this->post->ldapHost}';\n"
                          ."\$config->ldap->version = '{$this->post->ldapVersion}';\n"
                          ."\$config->ldap->bindDN = '{$this->post->ldapBindDN}';\n"
                          ."\$config->ldap->bindPWD = '{$this->post->ldapPassword}';\n"
                          ."\$config->ldap->baseDN = '{$this->post->ldapBaseDN}';\n"
                          ."\$config->ldap->searchFilter = '{$this->post->ldapFilter}';\n"
                          ."\$config->ldap->uid = '{$this->post->ldapAttr}';\n"
                          ."\$config->ldap->mail = '{$this->post->ldapMail}';\n"
                          ."\$config->ldap->name = '{$this->post->ldapName}';\n";

            $file = fopen("config.php", "w") or die("Unable to open file!");
            fwrite($file, $ldapConfig); 
            fclose($file); 

            $this->locate(inlink('setting'));        
        }
    }

    public function test()
    {
        echo $this->ldap->identify($this->post->host, $this->post->dn, $this->post->pwd);
    }

    public function sync()
    {  
        $users = $this->ldap->sync2db($this->config->ldap);
        echo $users;
    }

    public function identify($user, $pwd)
    {
        $ret = false;
        $account = $this->config->ldap->uid.'='.$user.','.$this->config->ldap->baseDN;
        if (0 == strcmp('Success', $this->ldap->identify($this->config->ldap->host, $account, $pwd))) {
            $ret = true;
        }

        echo $ret;
    }
}
