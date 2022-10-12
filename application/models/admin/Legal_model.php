<?php
	class Legal_model extends CI_Model{ 
	    

	    // Add Place Details on Master Tables
		public function getLegalData(){

		 $this->db->select('ms.stages as stage_name,ms.id as stage_id,tv.name as vendor_name,cc.name as country_name,cs.name as state_name,city.name as city_name,cpp.*');
		 $this->db->from('ci_parking_places cpp');
		 $this->db->join('tbl_vendor tv','cpp.vendor_id = tv.id','left');
		 $this->db->join('ci_countries cc','cpp.fk_country_id=cc.id','left');
		 $this->db->join('ci_states cs','cpp.fk_state_id=cs.id','left');
		 $this->db->join('ci_cities city','city on cpp.city_id = city.id','left');
		 $this->db->join('tbl_places_stages tps','tps on cpp.id = tps.place_id','left');
		 $this->db->join('master_stages ms','ms on tps.stages_id = ms.id','left');
         $this->db->order_by('cpp.id','desc');

        
		$query = $this->db->get();
		 $data = array();
		if($query !== FALSE && $query->num_rows() > 0){
   			 $data = $query->result_array();
			}
		return $data;
		}

		public function getDataById($id){

		$this->db->select('tv.name as vendor_name,cc.name as country_name,cs.name as state_name,city.name as city_name,cpp.*,cs.prefix,tps.status as phase_status');
		 $this->db->from('ci_parking_places cpp');
		 $this->db->join('tbl_vendor tv','cpp.vendor_id = tv.id','left');
		 $this->db->join('ci_countries cc','cpp.fk_country_id=cc.id','left');
		 $this->db->join('ci_states cs','cpp.fk_state_id=cs.id','left');
		 $this->db->join('ci_cities city','city on cpp.city_id = city.id','left');
		 $this->db->join('tbl_places_stages tps','tps on cpp.id = tps.place_id','left');
		 $this->db->where('cpp.id', $id);

		$query = $this->db->get();
		 $data = array();
		if($query !== FALSE && $query->num_rows() > 0){
   			 $data = $query->result_array();
			}
		return $data;

		}

		function getStageInfoById($id){
		$this->db->select('count(id) as total');
		$this->db->where('is_deleted','1');	
		$this->db->where('place_id',$id);
		$query = $this->db->get('tbl_places_stages');
        return $query->row_array();
    }
    function getStageName($id){

		$stage_id = ($id == 5) ? '5' : ($id+1) ;
    	
		$this->db->select('stages');
		$this->db->where('id',$stage_id);		
		$query = $this->db->get('master_stages');
        return $query->row_array();
    }

    






		public function UpdatePlaces($id,$data){
		$this->db->where('id', $id);
		$this->db->update('ci_parking_places', $data);
		return true;

		}

		public function AddStageInfo($process_data){

		$this->db->insert('tbl_places_stages',$process_data);
		return true;
		}

		public function UpdateStageInfo($id,$data){
		    
		 
		$count = $this->getStageInfoById($id);
		$total_count = $count['total'];
		if($total_count==0){
			$data['place_id']=$id;
			$this->db->insert('tbl_places_stages',$data);
		}else{
		$this->db->where('place_id', $id);
		$this->db->update('tbl_places_stages', $data);
		}
	
		return true;
		}





		



	}