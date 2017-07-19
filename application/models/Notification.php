<?PHP

class Notification extends MY_Model {

    var $tablename = "notification";
    var $idfield = "id";


	function my_messages($id, $seen = 0){
		$this->db->select("*");
		$this->db->from("notification");
		$this->db->where("user_id", $id);
		$this->db->where("seen", $seen);
		
		return $this->multiple_results($this->db->get());
	}
	
	function read($id, $userid){
		
		$data = array(
			'seen' => 1,
		);
		
		$this->db->where("id", $id);
		$this->db->where("user_id", $userid);
		$this->db->update($this->tablename, $data);
		return;
		
	}
	
	function send($user_id, $message, $type = "msg", $expires = null){
		
		$data = array(
			'user_id' => $user_id,
			'message' => $message,
			'type'    => $type,
			'expires' => $expires
		);
		
		$this->save($data);
		
	}

}
