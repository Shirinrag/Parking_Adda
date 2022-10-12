<?php
	class Dashboard_model extends CI_Model{

		public function get_all_users(){
			return $this->db->count_all('ci_users');
		}
		public function get_active_users(){
			$this->db->where('is_active', 1);
			return $this->db->count_all_results('ci_users');
		}
		public function get_deactive_users(){
			$this->db->where('is_active', 0);
			return $this->db->count_all_results('ci_users');
		}
		
		public function getActiveUsers(){
			$this->db->select('COUNT(id) as total_users');
			$this->db->from('ci_users');
			$this->db->where('is_admin',0);
			$query=$this->db->get();
			return  $query->row_array();
		}

		public function getBookingsData(){
			$this->db->select('COUNT(id) as total_completed_booking');
			$this->db->from('ci_booking');
			$this->db->where('is_deleted','0');
			$this->db->where('booking_status','1');
			$query=$this->db->get();
			return $query->row_array();
		}

		public function getActivePlaces(){

			$this->db->select('COUNT(id) as total_active_places');
			$this->db->from('ci_parking_places');
			$this->db->where('place_status','1');
			$this->db->where('is_deleted','0');
			$query=$this->db->get();
			return $query->row_array();
		}

		public function getUsersType(){

			$this->db->select('COUNT(id) as users_type');
			$this->db->from('ci_users');
			$this->db->where('is_admin',0);
			$query=$this->db->get();
		    return $query->row_array();
		}
		
		public function getPlaceData(){

		
			$this->db->select('SUM(cost) as total_earning,cpp.placename,COUNT(cb.id) as total_bookings');
			$this->db->from('ci_booking as cb');
			$this->db->join('ci_parking_places as cpp','cb.place_id = cpp.id','left');
			$this->db->where('cb.booking_from_date',date('Y-m-d'));
			$this->db->where('cb.is_deleted','0');
			$this->db->where('cb.booking_status!=','3');
			$this->db->group_by('cpp.placename');
			$this->db->order_by('total_earning','DESC');
			$query=$this->db->get();
		    $data = array();
			if($query !== FALSE && $query->num_rows() > 0){
   			 $data = $query->result_array();
			}

			return $data;

		}
		
		public function getPieChartData(){


			$this->db->select('COUNT(id) as total, booking_status');
			$this->db->from('ci_booking');
			$this->db->where('is_deleted','0');
			$this->db->where('booking_status!=','0');
			$this->db->where('booking_status!=','3');
			$this->db->where('is_deleted','0');
			$this->db->group_by('booking_status');
			$this->db->order_by('2');
			$query=$this->db->get();
		    $data = array();
			if($query !== FALSE && $query->num_rows() > 0){
   			 $data = $query->result_array();
			}

			return $data;

		}




		
	}

?>
