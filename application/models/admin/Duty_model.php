<?php

	class Duty_model extends CI_Model{

	public function getPlaces(){
			$this->db->select('cpp.*');
			$this->db->from('ci_parking_places cpp');
			$this->db->where('cpp.place_status','1');
			$this->db->where('cpp.is_deleted','0');
		    $this->db->order_by('cpp.placename');
			$query = $this->db->get();
		 	$data = array();
			if($query !== FALSE && $query->num_rows() > 0){
   			 $data = $query->result_array();
			}
			return $data;
}


	public function getVerifiers(){
			
			$this->db->select("ca.admin_id,CONCAT(ca.firstname,' ', ca.lastname) as fullname");
			$this->db->from('ci_admin ca');
			$where = '((ca.admin_role_id="3" OR ca.admin_role_id = "11") AND ca.is_active="1") ';
			$this->db->where($where);
		    $this->db->order_by('ca.firstname');
			$query = $this->db->get();
		 	$data = array();
			if($query !== FALSE && $query->num_rows() > 0){
   			 $data = $query->result_array();
			}
			return $data;
}

	public function AllocateDuty($place_id,$verifier_id,$duty_date){

			 $verification= $this->verifyAllocation($place_id,$verifier_id,$duty_date);
			 if(count($verification)<1){
			 	$array_data  = array('verifier_id'=>$verifier_id,'place_id'=>$place_id,'duty_date'=>$duty_date);
		     	$data = $this->db->insert('tbl_verifier_place',$array_data);
			 }else{
			 	$array_data[]  = array('verifier_id'=>$verifier_id,'place_id'=>$place_id,'duty_date'=>$duty_date);
			 	$this->session->set_userdata($array_data);
			 }
			 
	}

	public function verifyAllocation($place_id,$verifier_id,$duty_date){
		$this->db->select("*");
		$this->db->from('tbl_verifier_place');
		$this->db->where('place_id',$place_id);
		$this->db->where('duty_date',$duty_date);
		$this->db->where('verifier_id',$verifier_id);
		$this->db->where('isDeleted',1);
		$query = $this->db->get();
		 	$data = array();
			if($query !== FALSE && $query->num_rows() > 0){
   			 $data = $query->result_array();
			}
		return $data;
	}

	public function get_all(){
			$this->db->select("CONCAT(ca.firstname,' ',ca.lastname) as fullname,ca.mobile_no,cpp.placename,tvp.duty_date,tvp.onCreated,tvp.id as duty_id,tvp.onCreated");
			$this->db->from('tbl_verifier_place tvp');
			$this->db->join('ci_admin ca','tvp.verifier_id = ca.admin_id');
			$this->db->join('ci_parking_places cpp','tvp.place_id = cpp.id');
			$this->db->where('ca.is_active','1');
			$this->db->where('tvp.isDeleted','0');

			 if($this->session->userdata('verifier_id')!='' && $this->session->userdata('verifier_id')!='0'){
			 	$this->db->where('ca.admin_id',$this->session->userdata('verifier_id'));
			 }
			 if($this->session->userdata('date')!=''){
			 	$this->db->where('tvp.duty_date',$this->session->userdata('date'));
			 }
			 if($this->session->userdata('place_id')){
			 	$this->db->where('cpp.id',$this->session->userdata('place_id'));
			 }
		    if($this->session->userdata('filter_keyword')==''){
		    	$this->db->order_by('tvp.id','desc');
		    }
			$query = $this->db->get();
		 	$data = array();
			if($query !== FALSE && $query->num_rows() > 0){
   			 $data = $query->result_array();
			}
			
			return $data;
	}

	function deactiveDuty($id){

		$date = date('Y-m-d H:s:m');
		$this->db->set('isDeleted', '1');
		$this->db->set('updatedDate', $date);
		$this->db->where('id',$id);
		$this->db->update('tbl_verifier_place');

	}

}

?>