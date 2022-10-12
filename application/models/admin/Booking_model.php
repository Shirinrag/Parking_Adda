<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Booking_model extends CI_Model{


	//--------------------------------------------------------------------
	public function getBookingData(){
	    
	    	$user_type = $this->session->userdata('admin_role');
		    $id = $this->session->userdata('admin_id');
		 
		 
	    	$this->db->select('
			ca.firstname as verifier_name,ci_parking_places.placename,
			tbl_verifier_complaints.id as complaint_id,
			ci_parking_places.place_address,ci_parking_slot_info.slot_no,
			ci_parking_slot_info.slot_name,
			ci_parking_slot_info.display_id,ci_booking.unique_booking_id,
			ci_booking.booking_from_date,ci_booking.booking_from_date,
			ci_booking.booking_to_date,ci_booking.booking_to_date,
			ci_booking.from_time,ci_booking.to_time,ci_booking.cost,ci_booking.booking_type,
			tbl_verifier_complaints.complaint_text,ci_users.firstname,ci_booking.booking_status,ci_booking.paid_status,
			tbl_verifier_complaints.status,tbl_verifier_complaints.actionPerformedByEnforcer as enf_status');
			
			
			
		 $this->db->from('tbl_verifier_complaints');
		 $this->db->join('ci_admin ca','tbl_verifier_complaints.verifier_id=ca.admin_id','left');
		 $this->db->join('ci_parking_places','tbl_verifier_complaints.place_id=ci_parking_places.id','left');
		 $this->db->join('ci_parking_slot_info','tbl_verifier_complaints.slot_id=ci_parking_slot_info.slot_no','left');
		 $this->db->join('ci_booking','tbl_verifier_complaints.booking_id=ci_booking.id','left');
		 $this->db->join('ci_admin','tbl_verifier_complaints.enforcer_id=ci_admin.admin_id','left');
		 $this->db->join('ci_users','ci_booking.user_id=ci_users.id','left');
		 $this->db->order_by('tbl_verifier_complaints.id','desc');
		 if($user_type=='Enforcer'){
		  $this->db->where('tbl_verifier_complaints.enforcer_id',$id);
		}
		 if($this->session->userdata('complaint_type')!='' && $this->session->userdata('complaint_type')!=0){
			$this->db->where('tbl_verifier_complaints.actionPerformedByEnforcer	',$this->session->userdata('complaint_type'));}
			$this->session->unset_userdata('complaint_type');

		 $query = $this->db->get();
		 $data = array();
		if($query !== FALSE && $query->num_rows() > 0){
   			 $data = $query->result_array();
			}
		return $data;
	
	}
	
	public function getAllBookingData()
	{
	    
	        $this->db->select('CONCAT(tu.firstname," ",tu.lastname) as firstname,cb.unique_booking_id,cb.booking_type,cb.booking_status,cb.cost,cb.booking_from_date,cb.booking_to_date,
                            cb.from_time,cb.to_time,cpp.placename,cpp.place_address,cb.paid_status,cpsi.slot_name,cpsi.display_id,cb.booking_status,cbc.check_type,cbc.check_out,tu.mobile_no');
             $this->db->from('ci_booking cb');
             $this->db->join('ci_parking_places cpp','cb.place_id = cpp.id','left');
             $this->db->join('ci_parking_slot_info cpsi','cb.slot_id = cpsi.slot_no','left');
             $this->db->join('ci_users tu','cb.user_id = tu.id','left');
             $this->db->join('ci_booking_check cbc','cb.id = cbc.booking_id','left');
             $this->db->order_by('cb.id','desc');
             
             $where = "cpp.id!='1'";
             $this->db->where($where);
             
             $query = $this->db->get();
		     $data = array();
		        if($query !== FALSE && $query->num_rows() > 0){
   			         $data = $query->result_array();
			        }
		        return $data;
	}
	
	public function getFollowUpBookingData(){

		   $current_date = date("Y-m-d"); 
		   $current_time = date('H:i:s', strtotime('3 minutes')); 
           $minus_time =  date('H:i:s', strtotime('-13 minutes')); 


		    $this->db->select('CONCAT(tu.firstname," ",tu.lastname) as firstname,cb.unique_booking_id,cb.booking_type,cb.booking_status,cb.cost,cb.booking_from_date,cb.booking_to_date,
                            cb.from_time,cb.to_time,cpp.placename,cpp.place_address,cb.paid_status,cpsi.slot_name,cpsi.display_id,cb.booking_status,cbc.check_type,cbc.check_out,mobile_no');
             $this->db->from('ci_booking cb');
             $this->db->join('ci_parking_places cpp','cb.place_id = cpp.id','left');
             $this->db->join('ci_parking_slot_info cpsi','cb.slot_id = cpsi.slot_no','left');
             $this->db->join('ci_users tu','cb.user_id = tu.id','left');
             $this->db->join('ci_booking_check cbc','cb.id = cbc.booking_id','left');
             $where = "cb.booking_from_date = '$current_date'  AND cb.to_time BETWEEN '$minus_time' AND '$current_time' AND cpp.id!='1'";
             $this->db->where($where);

             $this->db->order_by('cb.to_time');
             $query = $this->db->get();
		     $data = array();
		        if($query !== FALSE && $query->num_rows() > 0){
   			         $data = $query->result_array();
			        }
		        return $data;



	}
	
	
	

	
}

?>