<?php
	class Vendors_model extends CI_Model{

		public function GetAllVendors(){
			$this->db->select('*');
			$this->db->where('is_deleted',0);
			$this->db->order_by('name');
			return $this->db->get('tbl_vendor')->result_array();
		}

	}
