<?php

class Account extends CI_Controller{

    function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->library('form_validation');
        $this->load->helper(array('form', 'url'));
        $this->load->helper('security');
        $this->load->model('maccount');
        $this->load->library('session');
    }
    
    //login 
    //**********************************************************************************************************
    function index(){
        $this->load->view('account/register');
    }

    function login(){
        $config = array(
                    array(
                 'field'=>'username',
                 'label'=>'用户名',
                 'rules'=>'trim|required|xss_clean|callback_username_check'
             ),
             array(
                 'field'=>'password',
                 'label'=>'密码',
                 'rules'=>'trim|required|xss_clean|callback_password_check'
             )
            );
        $this->form_validation->set_rules($config);
 
        $this->_username = $this->input->post('username');                //用户名

        if ($this->form_validation->run() == FALSE){
             // $this->load->view('account/login');
            echo FALSE;
        }
        else {
            //注册session,设定登录状态
            $this->maccount->login($this->_username);
            $user_tmp = $this->maccount->get_by_username($this->_username);
            $data['message'] = $this->session->userdata('username').' You are logged in! Now take a look at the '
                                 .anchor('account/dashboard', 'Dashboard');
            $data['id'] = $user_tmp->id;
            $data['username'] = $this->_username;
            // $this->load->view('account/note', $data);
            echo TRUE;
        }
    }
 
//登录表单验证时自定义的函数
/**
    * 提示用户名是不存在的登录
    * @param string $username
    * @return bool 
    */
    function username_check($username){
        if ($this->maccount->get_by_username($username)){
            return TRUE;
        }
        else {
            $this->form_validation->set_message('username_check', '用户名不存在');
            return FALSE;
        }
    }
    /**
    * 检查用户的密码正确性
    */
    function password_check($password)
    {
        if ($this->maccount->password_check($this->_username, md5($password))){
            return TRUE;
        }
        else {
            $this->form_validation->set_message('password_check', '用户名或密码不正确');
            return FALSE;
        }
    }

    //Sign up
    //***************************************************************************************

    function register()
    {
        $config = array(
                    array(
                 'field'=>'username',
                 'label'=>'用户名',
                 'rules'=>'trim|required|xss_clean|callback_username_exists'
             ),
             array(
                 'field'=>'password',
                 'label'=>'密码',
                 'rules'=>'trim|required|min_length[4]|max_length[12]|xss_clean|matches[passconf]'
             ),
             array(
                    'field'=>'passconf',
                    'label'=>'密码确认',
                    'rules'=>'trim|required|min_length[4]|max_length[12]|xss_clean'
                ),
             array(
                 'field'=>'email',
                 'label'=>'邮箱账号',
                 'rules'=>'trim|required|xss_clean|valid_email|callback_email_exists'
             )
            );
        $this->form_validation->set_rules($config);
        
        if ($this->form_validation->run() == FALSE)
        {
            //$this->load->view('account/register');
            echo FALSE;
        }
        else 
        {
            $username = $this->input->post('username');
            $password = md5($this->input->post('password'));
            $passconf = md5($this->input->post('passconf'));
            $email = $this->input->post('email');
            if ($this->maccount->add_user($username, $password, $email))
            {
                $data['message'] = "The user account has now been created! You can go "
                            .anchor('account/index', 'here').'.';
                $user_tmp = $this->maccount->get_by_username($username);
                $data['id'] = $user_tmp->id;
                $data['username'] = $username;
                //$this->load->view('account/note', $data);
                echo TRUE;
            }
            else 
            {
                $data['message'] = "There was a problem when adding your account. You can register "
                            .anchor('account/register', 'here').' again.';
            }
            
        }        
        }
    /**
    * ======================================
    * 用于注册表单验证的函数
    * 1、username_exists()
    * 2、email_exists()
    * ======================================
    */
    /**
    * 验证用户名是否被占用。
    * 存在返回false, 否者返回true.
    * @param string $username
    * @return boolean
    */
    function username_exists($username)
    {
        if ($this->maccount->get_by_username($username))
        {
            $this->form_validation->set_message('username_exists', '用户名已被占用');
            return FALSE;
        }
        return TRUE;
    }
    function email_exists($email)
    {
        if ($this->maccount->email_exists($email))
        {
            $this->form_validation->set_message('email_exists', '邮箱已被占用');
            return FALSE;
        }
        return TRUE;
    }

    function logout()
    {
        if ($this->maccount->logout() == TRUE)
        {
            //$this->load->view('account/logout');
            echo 1;
        }
        else
        {
            //$this->load->view('account/details');
            echo 0;
        }
    }

    
}