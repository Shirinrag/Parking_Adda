<?php
class Operations_Model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	
	public function getOperationsData(){

		 $this->db->select('ms.stages as stage_name,ms.id as stage_id,tv.name as vendor_name,cc.name as country_name,cs.name as state_name,city.name as city_name,cpp.*');
		 $this->db->from('ci_parking_places cpp');
		 $this->db->join('tbl_vendor tv','cpp.vendor_id = tv.id','left');
		 $this->db->join('ci_countries cc','cpp.fk_country_id=cc.id','left');
		 $this->db->join('ci_states cs','cpp.fk_state_id=cs.id','left');
		 $this->db->join('ci_cities city','city on cpp.city_id = city.id','left');
		 $this->db->join('tbl_places_stages tps','tps on cpp.id = tps.place_id','left');
		 $this->db->join('master_stages ms','ms on tps.stages_id = ms.id','left');
		 $this->db->where('tps.stages_id>=',2);
         $this->db->order_by('cpp.id','desc');

		$query = $this->db->get();
		 $data = array();
		if($query !== FALSE && $query->num_rows() > 0){
   			 $data = $query->result_array();
			}
		return $data;
		}



		public function addSlabInfo($data){
			$this->db->insert('ci_price_slab', $data);
			return true;

		}

		public function getPriceInfoById($id){

		$this->db->from('ci_price_slab');
		$this->db->where('is_deleted',0);
		$this->db->where('place_id',$id);
		$this->db->order_by('id');
		$query=$this->db->get();
		return $query->result_array();
		}

		public function getPriceInfoByIdType($id,$type){

		$this->db->from('ci_price_slab');
		$this->db->where('is_deleted',0);
		$this->db->where('place_id',$id);
		$this->db->where('pass',$type);
		$this->db->order_by('id');
		$query=$this->db->get();
		return $query->result_array();
		}
        
        public function updateExtentionById($id,$data){

			$this->db->where('id', $id);
			$this->db->update('ci_parking_places', $data);
			return true;
		}
		public function addHourlyPrice($data)
		{
			$result = $this->db->insert('ci_price_slab', $data);
			if($result){
			     $place_status =array('place_status'=>5);
			     $this->db->where('id', $data['place_id']);
			     $place = $this->db->update('ci_parking_places', $place_status);
			     
			  if($place){
			     $stage_data =array('stages_id'=>5);
			     $this->db->where('place_id', $data['place_id']);
			     $stage = $this->db->update('tbl_places_stages', $stage_data);
			    
			}
			
			}
			
			return true;
		}
		public function addDailyPriceInfo($data){
			
			$result  = $this->db->insert('ci_price_slab', $data);
			if($result){
			     $stage_data =array('stages_id'=>5);
			     $this->db->where('place_id', $data['place_id']);
			     $stage = $this->db->update('tbl_places_stages', $stage_data);
			}
			return true;
		}
		public function updatePricingTypeById($id,$data){
		    $this->db->where('id', $id);
			$this->db->update('ci_parking_places', $data);
			return true;
		    
		}
		
		
// 		Added By Raj Namdev (21/03/2022)

	public function getVerifiers($id){

		
		 $this->db->select('ca.admin_id,CONCAT(ca.firstname," ",ca.lastname) as verifier_name,ca.email,ca.mobile_no,tvp.onCreated');
		 $this->db->from('tbl_verifier_place tvp');
		 $this->db->join('ci_admin ca','tvp.verifier_id = ca.admin_id','left');
		 $this->db->where('tvp.place_id',$id);
	   	 $querys = $this->db->get();
		 $datas = array();
		if($querys !== FALSE && $querys->num_rows() > 0){
   			 $datas = $querys->result_array();
			}

			$ids = '';
			$size = count($datas);
			foreach ($datas as $key => $value) {
    		 $ids .= $value['admin_id'].",";
			}

			$ids = $ids."0";
		    $this->db->from('ci_admin');
		    $where = "admin_role_id = '3' AND is_active = '1' AND admin_id  NOT IN($ids)";
		    $this->db->order_by('firstname');
		    $this->db->where($where);
		
		$query = $this->db->get();
		 $data = array();
		if($query !== FALSE && $query->num_rows() > 0){
   			 $data = $query->result_array();
		}

            $fdata['verifiers_list'] = $data;
            $fdata['assigned_verifiers_list'] = $datas;	
            return $fdata;
		}


    public function AddVerifiers($id,$data){
        
        $datas['place_id'] = $id;
        $datas['verifier_id'] = $data;
        $result  = $this->db->insert('tbl_verifier_place', $datas);
        return true;
    }
    
    
     public function AddEnforcers($id,$data){
        
        $datas['place_id'] = $id;
        $datas['enforcer_id'] = $data;
        $result  = $this->db->insert('tbl_enforcer_place', $datas);
        return true;
    }
    
    
    
    
    
        public function getEnforcers($id){
        
		 $this->db->select('ca.admin_id,CONCAT(ca.firstname," ",ca.lastname) as verifier_name,ca.email,ca.mobile_no,tep.createdDate as  onCreated');
		 $this->db->from('tbl_enforcer_place tep');
		 $this->db->join('ci_admin ca','tep.enforcer_id = ca.admin_id','left');
		 $this->db->where('tep.place_id',$id);
	   	 $querys = $this->db->get();
		 $datas = array();
		if($querys !== FALSE && $querys->num_rows() > 0){
   			 $datas = $querys->result_array();
			}


		$size = count($datas);
		$ids = ""; 
		foreach ($datas as $key => $value) {
    		 $ids .= $value['admin_id'].",";
		}

		$ids = $ids."0";
		$this->db->from('ci_admin');
		$where = "admin_role_id = '4' AND is_active = '1' AND admin_id  NOT IN($ids)";
		$this->db->order_by('firstname');
		$this->db->where($where);
		$query = $this->db->get();
		 $data = array();
		if($query !== FALSE && $query->num_rows() > 0){
   			 $data = $query->result_array();
			}


            $fdata['enforcers_list'] = $data;
            $fdata['assigned_enforcer_list'] = $datas;	
            return $fdata;
			
    }


		




	}

