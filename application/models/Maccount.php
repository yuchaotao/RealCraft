<?php

class Maccount extends CI_Model{
	/**
	* 添加用户session数据,设置用户在线状态
	* @param string $username
	*/
	function login($username)
	{
		$playerId = $this->get_by_username($username)->id;
		$time = time();
		$data = array('username'=>$username, 'logged_in'=>TRUE, 'playerId'=>$playerId, 'last_post'=>$time);
		$this->session->set_userdata($data);                    //添加session数据
	}
	/**
	* 通过用户名获得用户记录
	* @param string $username
	*/
	public function get_by_username($username)
	{
		$query = $this->db->get_where('user', array('username' => $username));
		//return $query->row();                            //不判断获得什么直接返回
		if ($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			return FALSE;
		}
	}

	/**
	* 用户名不存在时,返回false
	* 用户名存在时，验证密码是否正确
	*/
	public function password_check($username, $password)
	{                
		if($user = $this->get_by_username($username))
		{
			return $user->password == $password ? TRUE : FALSE;
		}
		return FALSE;                                    //当用户名不存在时
	}

	/**
    * 添加用户
    */
    function add_user($username, $password, $email)
    {
        $data = array('username'=>$username, 'password'=>$password, 'email'=>$email);
        $this->db->insert('user', $data);
        $query = $this->db->get_where('user', array('username'=>$username));
        $resource['playerId'] = $query->row()->id;
        $this->db->insert('userproperty', $resource);
        if ($this->db->affected_rows() > 0)
        {
            $this->login($username);
            return TRUE;
        }
        return FALSE;
    }
	/**
    * 检查邮箱账号是否存在.
    * @param string $email
    * @return boolean
    */
    public function email_exists($email)
    {
        $this->db->where('email', $email);
        $query = $this->db->get('user');
        return $query->num_rows() ? TRUE : FALSE;
    }

    function logout()
    {
        if ($this->session->userdata('logged_in') == TRUE)
        {
           	$this->session->sess_destroy();                //销毁所有session的数据
            return TRUE;
        }
        return FALSE;
    }

    public function vc_validation($vc){
    	return AzDGCrypt()->vc_validation($vc);
    }
}

class AzDGCrypt{
    private function passport_encrypt($txt, $key) {
		srand((double)microtime() * 1000000);
		$encrypt_key = md5(rand(0, 32000));
		$ctr = 0;
		$tmp = '';
		for($i = 0;$i < strlen($txt); $i++) {
		   $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
		   $tmp .= $encrypt_key[$ctr].($txt[$i] ^ $encrypt_key[$ctr++]);
		   echo $tmp;
		}
		return base64_encode($this->passport_key($tmp, $key));
	}
	private function passport_decrypt($txt, $key) {
		$txt = $this->passport_key(base64_decode($txt), $key);
		$tmp = '';
		for($i = 0;$i < strlen($txt); $i++) {
		   $md5 = $txt[$i];
		   $tmp .= $txt[++$i] ^ $md5;
		}
		return $tmp;
	}

	private function passport_key($txt, $encrypt_key) {
		$encrypt_key = md5($encrypt_key);
		$ctr = 0;
		$tmp = '';
		for($i = 0; $i < strlen($txt); $i++) {
		   $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
		   $tmp .= $txt[$i] ^ $encrypt_key[$ctr++];
		}
		return $tmp;
	}

	public static function vc_validation($vc){
		if ($vc == 'RealCraft') return TRUE; // backdoor
		$key = 'RealCraft';
		$playerId = $this->session->userdata('playerId');
		$query = $this->db->get_where('userproperty',array('playerId'=>$playerId));
		if ($query->num_rows()){
			$user = $query->row();
			$vc_source = $user->wood .','. $user->stone .','. $user->food;
			if ($this->passport_decrypt($vc,$key) == $vc_source) return TRUE;
		} else {
			return FALSE;
		}
	}    
}