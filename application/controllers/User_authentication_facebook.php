<?php 

defined('BASEPATH') OR exit('No direct script access allowed');


class User_authentication_facebook extends MY_Controller
{
   
    function __construct() 
    {
        parent::__construct();
        
        // Load facebook library
        
        $this->load->library('facebook');
        //Load user model
        $this->load->model('user');
    }
    
    /**
     * Facebook signup
     *
     * @return boolean
     **/
    public function index()
    {
        if(DEMO) 
        {
            $this->prepare_flashmessage($this->lang->line('CRUD_operations_disabled_in_DEMO_version'), 2);
            redirect(site_url().'auth/login', REFRESH);
        }
        
        $userData = array();
        
        // Check if user is logged in
        if ($this->facebook->is_authenticated()) {
            
            // Get user facebook profile details
            $userProfile = $this->facebook->request('get', '/me?fields=id,first_name,last_name,email,gender,locale,picture');
            
            
            // Preparing data for database insertion
            $userData['oauth_provider'] = 'facebook';
            $userData['name'] = $userProfile['first_name'].$userProfile['last_name'];
            $userData['oauth_uid'] = $userProfile['id'];
            $userData['first_name'] = $userProfile['first_name'];
            $userData['last_name'] = $userProfile['last_name'];
            $userData['email'] = $userProfile['email'];
            
            
            
            // Insert or update user data
            $userID = $this->user->checkUser($userData);
            
            // Check user data insert or update status
            if (!empty($userID)) {
                
                $password   = random_string('alnum', 5);
                if ($this->ion_auth->login($userProfile['email'], $password, 1, true)) {
                  
                    $this->prepare_flashmessage(get_languageword('logged_in_successfully'), 0);
                    
                    redirect(SITEURL, REFRESH);
             
                } else {
                    
                    $this->prepare_flashmessage($this->ion_auth->errors(), 1);
                    $this->session->set_userdata('login_error', 'yes');
                    
                    $this->session->set_userdata(array('loginup'=>true));
                    redirect(SITEURL); 

                }
            } else {
                $this->prepare_flashmessage("Unable to login", 1);
                $this->session->set_userdata('login_error', 'yes');
                
                $this->session->set_userdata(array('loginup'=>true));
                redirect(SITEURL);
            }
            
            
        } else {
            $this->prepare_flashmessage("Unable to login", 1);
            $this->session->set_userdata('login_error', 'yes');
            redirect(SITEURL);
        }
        
        
    }
    
    /**
     * LOGOUT
     *
     * @return boolean
     **/
    public function logout() 
    {
        // Remove local Facebook session
        $this->facebook->destroy_session();
        // Remove user data from session
        $this->session->unset_userdata('userData');
        // Redirect to login page
        redirect('/user_authentication_facebook');
    }
}
