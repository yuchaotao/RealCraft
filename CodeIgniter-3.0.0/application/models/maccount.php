<?php


class Maccount extends CI_Model{
	/**
	* 添加用户session数据,设置用户在线状态
	* @param string $username
	*/
	function login($username)
	{
		$data = array('username'=>$username, 'logged_in'=>TRUE);
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
        if ($this->logged_in() === TRUE)
        {
           	$this->session->sess_destroy();                //销毁所有session的数据
            return TRUE;
        }
        return FALSE;
    }
}