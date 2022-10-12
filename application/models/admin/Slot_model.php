<?php

	class Slot_model extends CI_Model{



	

		public function get_all_slots(){

		
		$this->db->select('cpsi.*,cpp.placename,cpp.place_address,city.name as city_name,
		cc.name country_name,cs.name as state_name');
		$this->db->from('ci_parking_slot_info cpsi');
		$this->db->join('ci_parking_places cpp','cpsi.place_id = cpp.id','left');
		$this->db->join('ci_countries cc','cpp.fk_country_id = cc.id','left');
		$this->db->join('ci_states cs','cpp.fk_state_id = cs.id','left');
		$this->db->join('ci_cities city','cpp.city_id = city.id','left');
	    $query = $this->db->get();
		$data = array();
	   if($query !== FALSE && $query->num_rows() > 0){
			   $data = $query->result_array();
		   }
	   return $data;

	}

	

	public function add_device_id($data){
	   // echo "<pre>";
	   // print_r($data);
	   // die;
	    $data["test_status"] = 1;

	    $query = $this->db->insert("tbl_sensor_list",$data);

	     return true;

	}

	

	public function getDeviceData()

	{

	    

	   $this->db->select('cpp.placename,cpsi.slot_name,cpsi.display_id,tsl.*');

	   $this->db->from('tbl_sensor_list tsl');

	   $this->db->join('ci_parking_slot_info cpsi','tsl.id = cpsi.machine_id','left');

	   $this->db->join('ci_parking_places cpp','cpsi.place_id = cpp.id','left');



	   $query = $this->db->get();

		$data = array();

	   if($query !== FALSE && $query->num_rows() > 0){

			   $data = $query->result_array();

		   }

	   return $data;

	    

	    $query = $this->db->get("tbl_sensor_list");

	    return true;

	}

	public function change_status(){

		

	$this->db->set('test_status',$this->input->post('status'));

	$this->db->where('id',$this->input->post('id'));

	$this->db->update('tbl_sensor_list');

} 



	    

	

	

	

}



?>