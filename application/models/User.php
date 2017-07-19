<?PHP

class User extends MY_Model {
	
	var $tablename = "user";
	var $idfield   = "id";

	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
		$this->load->database();
    }
	
	function email_in_use($email, User_Object $user = null){
		$this->db->select("*");
		$this->db->from("user");
		$this->db->where("email", $email);
		
        $result = $this->db->get();
		$result = $this->single_result($result);
		
		if (!$result){
			return false;
		} else {
			if ($user && $result->id == $user->id){
				return false;
				
			} else {
				return true;
			}
			
		}

	}
    
    function user_by_email($email){
    
        $this->db->select("*");
        $this->db->from("user");
        $this->db->where("email", $email);
        
        $res = $this->db->get();
        
        return $this->single_result($res);
    }
    
    function check_reset_code($code){
        $this->db->select("*");
        $this->db->from("user");
        $this->db->where("reset_code", $code);
        $this->db->where("DATEDIFF(reset_stamp,NOW()) < 14");
        
        $res = $this->db->get();
        
        return $this->single_result($res);
    }
	
	function hash_password($password){
		$salt = $this->config->item('encryption_key');
		return "hash:sha1:".sha1($salt.$password);
		
	}
	
	function create_user($data){
		$data['password'] = $this->hash_password($data['password']);
		$data['created_stamp'] = date ("Y-m-d H:i:s");
		return $this->save($data, "user");
		
	}
	
	function check_login($email, $password){
		
		$hashed = $this->hash_password($password);
	
		$this->db->select("*");
		$this->db->from("user");
		$this->db->where("email", $email);
		$this->db->where("password", $hashed);
		
		$result = $this->db->get();
		
		if ($result->num_rows() == 0){
			return false;
		} else {
			$user = $result->row();
			
	        $data = array(
	            'reset_code' => NULL
	        );
	    
	        $this->db->where("id", $user->id);
        	$this->db->update($this->tablename, $data);
			
			return $user;
		}
	}
    
    function change_password($userid, $newpassword){
        $password = $this->hash_password($newpassword);
        
        $data = array(
            'password'   => $password,
            'reset_code' => NULL
        );
    
        $this->db->where("id", $userid);
        
        $this->db->update($this->tablename, $data);
        
    }
	
	function verify_email($userid, $code){
		$this->db->select("*");
		$this->db->from("user");
        $this->db->where("validate_code", $code);
        $this->db->where("validated_stamp is null");
		$this->db->where("id", $userid);
		
		$row = $this->db->get()->row();
		if($row){
			$data = array('validated_stamp' => date("Y-m-d h:i:s"), 'validate_code' => null);
			$this->db->where("id", $userid);
			$this->db->update("user", $data);
			return true;
		} else {
			return false;
		}
	}
	
}


class User_Object extends MY_Object {
	
	function view_url(){
		return sprintf("/player/%s", md5($this->email));
	}
	
	function edit_url(){
		return $this->view_url()."/edit";
	}
	
    function logged_in(){
        return true;
    }
}

class Anonymous_User extends User_Object {
	/*  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `pronoun` varchar(15) COLLATE utf8_bin DEFAULT NULL,
  `avatar_id` int(11) DEFAULT NULL,
  `validated_stamp` datetime DEFAULT NULL,
  `validate_code` varchar(127) COLLATE utf8_bin DEFAULT NULL,
  `created_stamp` datetime DEFAULT NULL,
  `updated_stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `reset_code` varchar(31) null,
  `reset_stamp` DATETIME null,
  `is_admin` tinyint(1) DEFAULT '0',*/
    
	function __construct($data = false)
    {
        // Call the Model constructor
        parent::__construct();
        
        $this->set_data(array(
            "id" => false,
            "name" => "",
            "email" => "", 
            "pronoun" => "THEY",
            "avatar_id" => false,
            "is_admin" => false
        ));
    }
    
    function logged_in(){
        return false;
    }
    
}

class Authed_User extends User_Object {
	
    
    function logged_in(){
        return true;
    }
}
