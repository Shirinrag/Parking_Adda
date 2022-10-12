<?php
	class Sensor_model extends CI_Model{

	
		public function getSensorLogsById($id){
        
        $this->db->select('created_date');	
		$this->db->from('mpc_sensor');
		$this->db->where('device_id',$id);
		$this->db->order_by('id', 'desc');
		$this->db->limit(1);  
		$query=$this->db->get();
		return $query->row_array();

	}
	}