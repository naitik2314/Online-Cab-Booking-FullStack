<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Googlelogin extends MY_Controller {

   
    function __construct()
    {
        parent::__construct();
       
        $this->load->helper('string');
        
    }

    /**
     * Googlelogin
     *
     *
     * @return boolean
    **/ 
	public function index()
	{
        
		if($this->session->userdata('login') == true){
			redirect(SITEURL);
		}
		
		if (isset($_GET['code'])) {
			
			$this->googleplus->getAuthenticate();
            
			$this->session->set_userdata('login',true);
            
			$this->session->set_userdata('user_profile',$this->googleplus->getUserInfo());
            
            $userProfile=array();
            
            $userProfile = $this->googleplus->getUserInfo();
            
          
            
            $data=array();
            
            $data['oauth_provider'] = 'googleplus';
            $data['name']           = $userProfile['given_name'].$userProfile['family_name'];
            $data['oauth_uid']      = $userProfile['id'];
            $data['first_name']     = $userProfile['given_name'];
            $data['last_name']      = $userProfile['family_name'];
            $data['email']          = $userProfile['email'];
            
            
            $this->db->select('id');
            $this->db->from($this->db->dbprefix(TBL_USERS));
            $this->db->where(array('email'=>$data['email']));
            $prevQuery = $this->db->get();

            $prevCheck = $prevQuery->num_rows();
        
            
            if($prevCheck > 0)
            {
                $prevResult = $prevQuery->row_array();
                $userID = $prevResult['id'];
            }
            else
            {
                $username 	= $data['name'];
                $password 	= random_string('alnum', 5);
                $email 		= $data['email']; 
                
                $additional_data = array(
                        'first_name' 			=> $data['first_name'],
                        'last_name'  			=> $data['last_name'],
                        'username'				=> $data['first_name'].' '.$data['last_name'],
                        'phone'  			    => '',
                        'date_of_registration'  	=> date('Y-m-d')
                        );
                $group = array(2);
                $registered_by = $data['oauth_provider'];
                
                $userID = $this->ion_auth->register($username, $password, $email, $additional_data,$group,$registered_by);
                   
            }
            
          
             // Check user data insert or update status
            if (!empty($userID)) {
                
                $password   = random_string('alnum', 5);

                if ($this->ion_auth->login($data['email'], $password, 1, true)) {
                 
                    $this->prepare_flashmessage('Loggedin Successfully', 0);
                    
                    redirect(SITEURL, REFRESH);
             
                } else {
                    
                    $this->session->sess_destroy();
                    $this->googleplus->revokeToken();
                    $this->prepare_flashmessage($this->ion_auth->errors(), 1);
                    
                    redirect(SITEURL); 

                }
            } 
            else 
            {
                $this->prepare_flashmessage("Unable to login", 1);
                $this->session->sess_destroy();
                $this->googleplus->revokeToken();
                redirect(SITEURL);
            }
        
            
			redirect(SITEURL);
			
		} 
			
		redirect(SITEURL);
		
	}
	
	public function profile(){
		
		if($this->session->userdata('login') != true){
			redirect('');
		}
		
		$contents['user_profile'] = $this->session->userdata('user_profile');
		$this->load->view('profile',$contents);
		
	}
	
	public function logout(){
		
		$this->session->sess_destroy();
		$this->googleplus->revokeToken();
		redirect('');
		
	}
	
}
