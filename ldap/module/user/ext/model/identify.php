<?php
public function identify($account, $password)
{
	if (0 == strcmp('$',substr($account, 0, 1))) {
		return parent::identify(ltrim($account, '$'), $password);
	} else {
		$user = false;
		$record = $this->dao->select('*')->from(TABLE_USER)
            ->where('account')->eq($account)
            ->andWhere('deleted')->eq(0)
            ->fetch();
        if ($record) {
        	$ldap = $this->loadModel('ldap');
        	$ldap_account = $this->config->ldap->uid.'='.$account.','.$this->config->ldap->baseDN;
        	$pass = $ldap->identify($this->config->ldap->host, $ldap_account, $password);
        	if (0 == strcmp('Success', $pass)) {
        		$user = $record;
        		$ip   = $this->server->remote_addr;
						$last = $this->server->request_time;
						$this->dao->update(TABLE_USER)
							// 禅道有多处地方需要验证密码, 所以需要存储密码的 MD5 到禅道里以供验证
							->set('password')->eq(md5($password))
							->set('visits = visits + 1')
							->set('ip')->eq($ip)
							->set('ip')->eq($ip)
							->set('last')->eq($last)
							->where('account')->eq($account)->exec();
						$user->last = date(DT_DATETIME1, $user->last);
						
						/* Create cycle todo in login. */
            $todoList = $this->dao->select('*')->from(TABLE_TODO)->where('cycle')->eq(1)->andWhere('account')->eq($user->account)->fetchAll('id');
            $this->loadModel('todo')->createByCycle($todoList);
        	}
        }		
		return $user;
	}
}