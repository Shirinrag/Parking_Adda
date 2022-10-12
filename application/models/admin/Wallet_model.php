<?php
	class Wallet_model extends CI_Model{

    public function getWalletInfoById($id){
	    
	    $this->db->from('ci_wallet_history');
		$this->db->where('booking_id',$id);
		$query=$this->db->get();
		return $query->result_array();

		
	    
	}
	
// 	*************** Show Data in Wallet system for super admin *****************************

   public function todays_earning(){
       
          $date = new DateTime("now");
          $curr_date = $date->format('Y-m-d ');
		  $this->db->select('*');
		  $this->db->where('onUpdated',$curr_date);
		  return $this->db->get('ci_wallet_user')->result_array();
	}
	
//  ************** Show Total Users in Wallet System for super admin ********************	
	public function total_users(){
       
          $this->db->select('*');
		  return $this->db->get('ci_wallet_user')->result_array();
	}
	
//  ************** Show Refundable Amount in wallet system for super admin ******************
    public function total_refundable(){
       
          $this->db->select('*');
          $this->db->where('payment_type',3);
		  return $this->db->get('ci_wallet_history')->result_array();
	}

//  ************** Show User Data in wallet system for super admin ******************
    public function table_data(){
       
          $this->db->select('*');
          $this->db->from('ci_wallet_user');
          $this->db->join('ci_users', 'ci_wallet_user.user_id=ci_users.id');
           $this->db->where('ci_users.is_active',1);
          return $this->db->get()->result_array();


    }


}