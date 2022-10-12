<?php
	class Offers_model extends CI_Model{
	    
public function get_all_offers(){
		    
		$query = $this->db->get("ci_offers_master");
		$data = array();
		if($query !== FALSE && $query->num_rows() > 0){

			$data = $query->result_array();
		}
		return $data;
	}



public function change_status(){
    
        $this->db->set('is_active',$this->input->post('status'));
		$this->db->where('id',$this->input->post('id'));
		$this->db->update('ci_offers_master');
}

	    
	
	
	
}

?>