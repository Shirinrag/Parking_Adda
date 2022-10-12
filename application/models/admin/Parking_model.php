<?php
	class Parking_model extends CI_Model{ 
	    

	    // Add Place Details on Master Tables
	public function addNewPlaces($data){

			$this->db->insert('master_parking_places', $data);
			$last_id = $this->db->insert_id();
			$data['fk_master_place_id'] = $last_id;
			$slots_counts = $data['applied_slots'];
			unset($data['applied_slots']);
			$data['no_of_slots'] =$slots_counts;
			$this->db->insert('ci_parking_places', $data);
			
			return true;
		}

		public function getAllParkingPlaceData(){
 		$this->db->select('cc.name as country_name,cs.name as state_name,city.name as city_name,tv.name as vendor_name,tv.mobileno as vendor_contact,cpp.*');
		 $this->db->from('ci_parking_places cpp');
		 $this->db->join('tbl_vendor tv','cpp.vendor_id = tv.id','left');
		 $this->db->join('ci_countries cc','cpp.fk_country_id=cc.id','left');
		 $this->db->join('ci_states cs','cpp.fk_state_id=cs.id','left');
		 $this->db->join('ci_cities city','city on cpp.city_id = city.id','left');
		 $this->db->join('tbl_places_stages tps','cpp.id = tps.place_id','left');
		 $this->db->where('cpp.is_deleted',0);
		 $this->db->where('tps.stages_id>=',3);
		$query = $this->db->get();
		 $data = array();
		if($query !== FALSE && $query->num_rows() > 0){
   			 $data = $query->result_array();
			}
		return $data;
	}

	public function change_status()
	{
		$this->db->set('place_status',$this->input->post('status'));
		$this->db->where('id',$this->input->post('id'));
		$this->db->update('ci_parking_places');
	}

	function delete_place($id){	
		$this->db->set('is_deleted',1);
		$this->db->where('id',$id);
		$this->db->update('ci_parking_places');
	}
	function checkDevice()
	{
		 $this->db->select('cpsi.slot_name,cpsi.display_id');
		 $this->db->from('tbl_sensor_list tsl');
		 $this->db->join('ci_parking_slot_info cpsi','tsl.id = cpsi.machine_id','left');
		 $this->db->where('cpsi.is_deleted',0);
		 $this->db->where('device_id',$this->input->post('new_device_id'));
		 $query=$this->db->get();
		return $query->result_array();

	} 
	function checkDeviceExist(){

		$this->db->from('tbl_sensor_list');
		$this->db->where('device_id',$this->input->post('new_device_id'));
		$query = $this->db->get();
        return $query->result_array();
	}
	
	public function UpdatePriceInfo($data){

	
		$this->db->select('*');
		$this->db->from('ci_price_slab');
		$this->db->where('id',$data['unique_id']);
		$this->db->where('is_deleted','0');
		$query = $this->db->get();
		 $datas = array();
		if($query !== FALSE && $query->num_rows() > 0){
   			 $datas = $query->result_array()[0];
			}
		  	if($datas['cost']!= $data['costs']){

		  		$this->db->set('is_deleted',1);
				$this->db->where('id',$data['unique_id']);
				$flag = $this->db->update('ci_price_slab');
				if($flag){
				$id = $data['unique_id'];
				$hrs = $data['hrs'];
				$costs = $data['costs'];
				$place_id = $datas['place_id'];
				$pass = $datas['pass'];
				$save  = array('place_id'=>$place_id,'hrs'=>$hrs,'cost'=>$data['costs'],'pass'=>$pass);
				$status = $this->db->insert('ci_price_slab', $save);
				}
				
		  }
		  return $datas['place_id'];
	}



	}